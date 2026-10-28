<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Room;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminActivity;
use App\Models\RefundRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    /**
     * YÊU CẦU 2: Trang "Duyệt chuyển khoản" (Xử lý Luồng 1)
     * Lấy danh sách toàn bộ các Hóa đơn có trạng thái payment_method = 'bank_transfer' 
     * và status = 'pending_approval' (Chờ duyệt).
     */
    public function bankTransfers(Request $request)
    {
        $tab = $request->query('tab', 'pending'); // 'pending', 'approved', 'all'
        $search = trim($request->query('search', ''));

        // Query cơ sở cho các hóa đơn chuyển khoản ngân hàng
        $query = Invoice::where('payment_method', 'bank_transfer');

        if ($tab === 'pending') {
            $query->where(function ($q) {
                $q->where('status', 'pending_approval')
                  ->orWhere('payment_status', 'pending_approval');
            });
        } elseif ($tab === 'approved') {
            $query->where(function ($q) {
                $q->where('status', 'paid')
                  ->orWhere('payment_status', 'paid');
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'like', "%{$search}%")
                  ->orWhere('tenant_name', 'like', "%{$search}%")
                  ->orWhere('contract_id', 'like', "%{$search}%");
            });
        }

        $invoices = $query->latest('transfer_reported_at')->latest()->get();

        // Nạp thông tin phòng/hợp đồng cho từng hóa đơn
        foreach ($invoices as $inv) {
            if (!$inv->post && !empty($inv->post_id)) {
                $inv->post = Post::find($inv->post_id);
            }
            if (!$inv->contract && !empty($inv->contract_id)) {
                $inv->contract = Contract::find($inv->contract_id) ?: Contract::where('contract_code', $inv->contract_id)->first();
            }
            if (!$inv->post && $inv->contract && !empty($inv->contract->post_id)) {
                $inv->post = Post::find($inv->contract->post_id);
            }

            // Chuẩn hóa URL ảnh biên lai để đảm bảo hiển thị đúng trên mọi môi trường
            if (!empty($inv->proof_image)) {
                $p = trim($inv->proof_image);
                $inv->proof_image_url = (str_starts_with($p, 'http://') || str_starts_with($p, 'https://'))
                    ? $p
                    : asset(ltrim($p, '/'));
            } else {
                $inv->proof_image_url = null;
            }
        }

        // Thống kê nhanh
        $pendingTransfers = Invoice::where('payment_method', 'bank_transfer')
            ->where(function ($q) {
                $q->where('status', 'pending_approval')
                  ->orWhere('payment_status', 'pending_approval');
            })->get();

        $pendingCount = $pendingTransfers->count();
        $pendingAmount = $pendingTransfers->sum(fn($i) => (float)($i->total_amount ?? 0));

        // Thống kê đã duyệt hôm nay
        $today = Carbon::today();
        $approvedToday = Invoice::where('payment_method', 'bank_transfer')
            ->where(function ($q) {
                $q->where('status', 'paid')
                  ->orWhere('payment_status', 'paid');
            })
            ->where(function ($q) use ($today) {
                $q->where('admin_approved_at', '>=', $today)
                  ->orWhere('updated_at', '>=', $today);
            })->get();

        $approvedTodayCount = $approvedToday->count();
        $approvedTodayAmount = $approvedToday->sum(fn($i) => (float)($i->total_amount ?? 0));

        return view('Admin.finance.bank_transfers', compact(
            'invoices',
            'tab',
            'search',
            'pendingCount',
            'pendingAmount',
            'approvedTodayCount',
            'approvedTodayAmount'
        ));
    }

    /**
     * Action: Nút màu xanh "Đã nhận tiền (Duyệt)"
     * Cập nhật hóa đơn sang trạng thái paid & kích hoạt hợp đồng
     */
    public function approveBankTransfer(Request $request, $id)
    {
        $cleanId = trim($id);
        $invoice = Invoice::where('_id', $cleanId)
            ->orWhere('id', $cleanId)
            ->orWhere('invoice_code', $cleanId)
            ->first();

        if (!$invoice) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn cần duyệt!'], 404);
            }
            return back()->with('error', 'Không tìm thấy hóa đơn cần duyệt!');
        }

        $now = Carbon::now();
        $adminUser = Auth::user();

        // 1. Cập nhật hóa đơn sang trạng thái paid
        $invoice->status = 'paid';
        $invoice->payment_status = 'paid';
        $invoice->payment_date = $now->format('d/m/Y H:i');
        $invoice->admin_approved_at = $now->toDateTimeString();
        $invoice->admin_approved_by = (string)Auth::id();
        $invoice->is_remitted_to_admin = true; // Chuyển khoản ngân hàng trực tiếp vào tài khoản công ty
        $invoice->save();

        \App\Services\InvoiceService::handleInvoicePaid($invoice);

        // 2. Kích hoạt hợp đồng nếu đang ở trạng thái chờ
        $contractId = (string)($invoice->contract_id ?? '');
        $contract = null;
        if ($contractId) {
            $contract = Contract::where('_id', $contractId)
                ->orWhere('id', $contractId)
                ->orWhere('contract_code', $contractId)
                ->first();
        }

        if ($contract) {
            $isLiquidationInvoice = (
                $invoice->type === 'final_settlement' || 
                $invoice->invoice_type === 'liquidation' || 
                str_starts_with((string)$invoice->invoice_code, 'INV-LIQ-')
            );

            if ($isLiquidationInvoice || $contract->status === 'pending_liquidation') {
                $liqController = new \App\Http\Controllers\ContractLiquidationController();
                $liqController->executeTermination(
                    $contract,
                    Auth::user(),
                    "Kế toán đã duyệt thanh toán hóa đơn thanh lý {$invoice->invoice_code}."
                );

                $msg = 'Đã duyệt thanh toán thành công cho hóa đơn thanh lý ' . $invoice->invoice_code . '! Hợp đồng đã hoàn tất thanh lý (terminated) và phòng đã chuyển về trạng thái Trống (available).';

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success'         => true,
                        'message'         => $msg,
                        'invoice_code'    => $invoice->invoice_code,
                        'total_amount'    => $invoice->total_amount,
                        'approved_at'     => $invoice->admin_approved_at,
                        'contract_status' => 'terminated',
                    ]);
                }
                return back()->with('success', $msg);
            }

            $contract->payment_status = 'paid';
            $contract->last_paid_period = $invoice->billing_month ?: $now->format('m/Y');
            $contract->last_paid_date = $now->toDateTimeString();
            if (in_array($contract->status, ['pending_deposit', 'pending_payment', 'pending', null, ''])) {
                $contract->status = 'active'; // Kích hoạt chính thức
            }
            $contract->save();

            // Gửi thông báo cho khách thuê
            if (!empty($contract->tenant_id)) {
                Notification::create([
                    'user_id'  => (string)$contract->tenant_id,
                    'post_id'  => (string)($contract->post_id ?? ''),
                    'title'    => 'Biên lai chuyển khoản đã được Kế toán duyệt!',
                    'message'  => 'Hóa đơn ' . $invoice->invoice_code . ' số tiền ' . number_format($invoice->total_amount) . ' đ đã được duyệt thành công. Hợp đồng của bạn đã được Kích hoạt.',
                    'type'     => 'success',
                    'is_read'  => false,
                    'category' => 'finance',
                ]);
            }
        }

        // 3. Ghi log hoạt động Admin nếu có
        try {
            if (class_exists(AdminActivity::class)) {
                AdminActivity::create([
                    'user_id'     => (string)Auth::id(),
                    'action'      => 'approve_bank_transfer',
                    'description' => 'Admin ' . ($adminUser->account_name ?? 'Admin') . ' đã duyệt chuyển khoản hóa đơn #' . $invoice->invoice_code . ' (' . number_format($invoice->total_amount) . ' đ)',
                    'target_id'   => (string)($invoice->_id ?? $invoice->id),
                    'target_type' => 'invoice',
                ]);
            }
        } catch (\Exception $e) {
            // Không làm gián đoạn luồng chính
        }

        $msg = 'Đã duyệt thanh toán thành công cho hóa đơn ' . $invoice->invoice_code . '! Hợp đồng đã được kích hoạt.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => $msg,
                'invoice_code' => $invoice->invoice_code,
                'total_amount' => $invoice->total_amount,
                'approved_at'  => $invoice->admin_approved_at,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Action phụ: Từ chối biên lai chuyển khoản (yêu cầu kiểm tra lại hoặc up lại bill)
     */
    public function rejectBankTransfer(Request $request, $id)
    {
        $cleanId = trim($id);
        $invoice = Invoice::where('_id', $cleanId)
            ->orWhere('id', $cleanId)
            ->orWhere('invoice_code', $cleanId)
            ->first();

        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn!'], 404);
        }

        $reason = $request->input('reason', 'Ảnh biên lai mờ hoặc chưa khớp với biến động số dư ngân hàng.');

        $invoice->status = 'unpaid';
        $invoice->payment_status = 'unpaid';
        $invoice->rejection_reason = $reason;
        $invoice->save();

        if (!empty($invoice->tenant_id)) {
            Notification::create([
                'user_id'  => (string)$invoice->tenant_id,
                'post_id'  => (string)($invoice->post_id ?? ''),
                'title'    => 'Biên lai chuyển khoản chưa được duyệt',
                'message'  => 'Hóa đơn ' . $invoice->invoice_code . ' bị từ chối duyệt: ' . $reason . '. Vui lòng kiểm tra lại hoặc liên hệ quản lý.',
                'type'     => 'error',
                'is_read'  => false,
                'category' => 'finance',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối biên lai chuyển khoản hóa đơn ' . $invoice->invoice_code,
        ]);
    }

    /**
     * YÊU CẦU 3: Trang "Công nợ nhân viên" (Xử lý Luồng 2)
     * Truy vấn gom nhóm (Group by) theo Nhân viên: Lấy danh sách các nhân viên đang có is_remitted_to_admin = false,
     * tính TỔNG SỐ TIỀN mặt họ đang giữ.
     */
    public function staffDebts(Request $request)
    {
        // 1. Lấy toàn bộ các hóa đơn đã thu tiền mặt nhưng CHƯA nộp lại cho công ty
        $unremittedInvoices = Invoice::where(function ($q) {
                $q->where('payment_method', 'cash')
                  ->orWhereNotNull('collected_by');
            })
            ->where(function ($q) {
                $q->where('status', 'paid')
                  ->orWhere('payment_status', 'paid');
            })
            ->where(function ($q) {
                $q->where('is_remitted_to_admin', false)
                  ->orWhere('is_remitted_to_admin', 0)
                  ->orWhereNull('is_remitted_to_admin');
            })
            ->latest()
            ->get();

        // 2. Gom nhóm theo nhân viên thu tiền (collected_by)
        $groupedByStaff = $unremittedInvoices->groupBy(function ($inv) {
            return (string)($inv->collected_by ?: ($inv->created_by ?: ($inv->staff_id ?: 'unknown')));
        });

        $staffDebtsList = collect();

        foreach ($groupedByStaff as $staffId => $items) {
            if ($staffId === 'unknown' || empty($staffId)) {
                continue;
            }

            $staffUser = User::find($staffId);
            $staffName = $staffUser 
                ? ($staffUser->account_name ?? ($staffUser->name ?? ($staffUser->username ?? 'Nhân viên #' . substr($staffId, -4))))
                : 'Nhân viên #' . substr($staffId, -4);

            $staffRole = $staffUser->role ?? 'nhansu';
            $staffEmail = $staffUser->email ?? 'Chưa có email';
            $staffPhone = $staffUser->phone ?? 'Chưa có SĐT';
            $staffAvatar = $staffUser->avatar ?? null;

            // Nạp thông tin phòng cho các hóa đơn
            foreach ($items as $inv) {
                if (!$inv->post && !empty($inv->post_id)) {
                    $inv->post = Post::find($inv->post_id);
                }
                if (!$inv->contract && !empty($inv->contract_id)) {
                    $inv->contract = Contract::find($inv->contract_id) ?: Contract::where('contract_code', $inv->contract_id)->first();
                }
            }

            $totalAmount = $items->sum(fn($i) => (float)($i->total_amount ?? 0));
            $invoiceCount = $items->count();
            $latestCollected = $items->sortByDesc('payment_date')->first();

            $staffDebtsList->push((object)[
                'staff_id'         => $staffId,
                'staff_user'       => $staffUser,
                'staff_name'       => $staffName,
                'staff_email'      => $staffEmail,
                'staff_phone'      => $staffPhone,
                'staff_role'       => $staffRole,
                'staff_avatar'     => $staffAvatar,
                'invoice_count'    => $invoiceCount,
                'total_amount'     => $totalAmount,
                'latest_date'      => $latestCollected ? ($latestCollected->payment_date ?: ($latestCollected->created_at ? $latestCollected->created_at->format('d/m/Y H:i') : '')) : '',
                'invoices'         => $items,
            ]);
        }

        // Sắp xếp người giữ nhiều tiền nhất lên đầu
        $staffDebtsList = $staffDebtsList->sortByDesc('total_amount')->values();

        // Thống kê tổng quan đầu trang
        $totalSystemDebt = $staffDebtsList->sum('total_amount');
        $totalStaffHolding = $staffDebtsList->count();
        $totalUnremittedInvoices = $unremittedInvoices->count();

        // Lịch sử các đợt đã nộp quỹ gần đây
        $remittedInvoices = Invoice::where('payment_method', 'cash')
            ->where('is_remitted_to_admin', true)
            ->latest('remitted_at')
            ->limit(20)
            ->get();

        $totalRemittedAmount = Invoice::where('payment_method', 'cash')
            ->where('is_remitted_to_admin', true)
            ->get()
            ->sum(fn($i) => (float)($i->total_amount ?? 0));

        return view('Admin.finance.staff_debts', compact(
            'staffDebtsList',
            'totalSystemDebt',
            'totalStaffHolding',
            'totalUnremittedInvoices',
            'remittedInvoices',
            'totalRemittedAmount'
        ));
    }

    /**
     * API xem chi tiết danh sách hóa đơn mà một nhân sự cụ thể đã thu tiền
     */
    public function staffInvoices(Request $request, $staffId)
    {
        $invoices = Invoice::where(function ($q) use ($staffId) {
                $q->where('collected_by', $staffId)
                  ->orWhere('created_by', $staffId);
            })
            ->where('payment_method', 'cash')
            ->where(function ($q) {
                $q->where('status', 'paid')
                  ->orWhere('payment_status', 'paid');
            })
            ->where(function ($q) {
                $q->where('is_remitted_to_admin', false)
                  ->orWhere('is_remitted_to_admin', 0)
                  ->orWhereNull('is_remitted_to_admin');
            })
            ->latest()
            ->get();

        $staffUser = User::find($staffId);
        $staffName = $staffUser ? ($staffUser->account_name ?? ($staffUser->username ?? 'Nhân viên')) : 'Nhân viên #' . substr($staffId, -4);

        $data = $invoices->map(function ($inv) {
            $post = Post::find($inv->post_id);
            return [
                'id'             => (string)($inv->_id ?? $inv->id),
                'invoice_code'   => $inv->invoice_code,
                'room_title'     => $post ? $post->title : ($inv->tenant_name ? 'Phòng của ' . $inv->tenant_name : 'Phòng thuê'),
                'tenant_name'    => $inv->tenant_name ?: 'Khách thuê',
                'billing_period' => $inv->billing_period ?: ($inv->billing_month ? 'Tháng ' . $inv->billing_month : 'Định kỳ'),
                'total_amount'   => (float)($inv->total_amount ?? 0),
                'total_formatted'=> number_format($inv->total_amount) . ' đ',
                'payment_date'   => $inv->payment_date ?: ($inv->created_at ? $inv->created_at->format('d/m/Y H:i') : ''),
                'view_url'       => url('qlvan_hanh/hoa_don/' . $inv->invoice_code),
            ];
        });

        return response()->json([
            'success'      => true,
            'staff_name'   => $staffName,
            'staff_id'     => $staffId,
            'total_count'  => $data->count(),
            'total_amount' => $data->sum('total_amount'),
            'invoices'     => $data,
        ]);
    }

    /**
     * Nút Action quan trọng: "Xác nhận nhân viên đã nộp tiền (Xóa nợ)"
     * Cập nhật toàn bộ các hóa đơn của nhân viên đó thành is_remitted_to_admin = true.
     * Tổng công nợ của nhân viên đó lập tức về 0.
     */
    public function remitStaffDebt(Request $request, $staffId)
    {
        $cleanStaffId = trim($staffId);

        // Lấy tất cả hóa đơn tiền mặt chưa nộp của nhân viên này
        $invoices = Invoice::where(function ($q) use ($cleanStaffId) {
                $q->where('collected_by', $cleanStaffId)
                  ->orWhere('created_by', $cleanStaffId);
            })
            ->where(function ($q) {
                $q->where('status', 'paid')
                  ->orWhere('payment_status', 'paid');
            })
            ->where(function ($q) {
                $q->where('is_remitted_to_admin', false)
                  ->orWhere('is_remitted_to_admin', 0)
                  ->orWhereNull('is_remitted_to_admin');
            })
            ->get();

        if ($invoices->isEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhân viên này hiện không có hóa đơn tiền mặt nào cần thanh toán nộp quỹ!',
                ], 400);
            }
            return back()->with('info', 'Nhân viên này hiện không có công nợ nào cần nộp!');
        }

        $now = Carbon::now();
        $adminId = (string)Auth::id();
        $totalRemitted = $invoices->sum(fn($i) => (float)($i->total_amount ?? 0));
        $count = $invoices->count();

        // Cập nhật toàn bộ hóa đơn thành ĐÃ NỘP VỀ CÔNG TY
        foreach ($invoices as $inv) {
            $inv->is_remitted_to_admin = true;
            $inv->remitted_at = $now->toDateTimeString();
            $inv->remitted_confirmed_by = $adminId;
            $inv->save();
        }

        // Gửi thông báo đến nhân viên
        $staffUser = User::find($cleanStaffId);
        $staffName = $staffUser ? ($staffUser->account_name ?? ($staffUser->username ?? 'Nhân viên')) : 'Nhân viên';

        Notification::create([
            'user_id'  => $cleanStaffId,
            'title'    => 'Xác nhận nộp tiền mặt thành công (Xóa nợ)',
            'message'  => 'Ban Quản trị đã xác nhận thu đủ số tiền ' . number_format($totalRemitted) . ' VNĐ (' . $count . ' hóa đơn). Công nợ của bạn đã được xóa hoàn tất về 0 VNĐ.',
            'type'     => 'success',
            'is_read'  => false,
            'category' => 'finance',
        ]);

        // Ghi log hoạt động Admin
        try {
            if (class_exists(AdminActivity::class)) {
                AdminActivity::create([
                    'user_id'     => $adminId,
                    'action'      => 'remit_staff_cash',
                    'description' => 'Admin xác nhận nhân sự ' . $staffName . ' đã nộp đủ ' . number_format($totalRemitted) . ' VNĐ tiền mặt (' . $count . ' hóa đơn)',
                    'target_id'   => $cleanStaffId,
                    'target_type' => 'user',
                ]);
            }
        } catch (\Exception $e) {
            // Không làm gián đoạn
        }

        $successMsg = 'Đã xác nhận nhân sự ' . $staffName . ' nộp đủ ' . number_format($totalRemitted) . ' VNĐ tiền mặt vào quỹ! Công nợ của nhân sự này đã về 0 VNĐ.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'message'          => $successMsg,
                'staff_id'         => $cleanStaffId,
                'remitted_count'   => $count,
                'remitted_amount'  => $totalRemitted,
                'remaining_debt'   => 0,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Endpoint cho khách thuê / người dùng báo đã chuyển khoản kèm upload ảnh bill
     */
    public function reportTransfer(Request $request, $code)
    {
        $cleanCode = trim($code);
        $invoice = Invoice::where('invoice_code', $cleanCode)
            ->orWhere('_id', $cleanCode)
            ->orWhere('id', $cleanCode)
            ->first();

        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hóa đơn!'], 404);
        }

        $proofUrl = null;

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $filename = 'proof_' . $cleanCode . '_' . time() . '.' . $file->getClientOriginalExtension();

            // 1. Lưu trực tiếp vào public/uploads/proofs để web server luôn đọc được ngay
            $publicDir = public_path('uploads/proofs');
            if (!file_exists($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            $file->move($publicDir, $filename);

            // 2. Đồng thời sao lưu vào storage/app/public/uploads/proofs để hỗ trợ disk storage
            $storagePublicDir = storage_path('app/public/uploads/proofs');
            if (!file_exists($storagePublicDir)) {
                @mkdir($storagePublicDir, 0755, true);
            }
            @copy($publicDir . DIRECTORY_SEPARATOR . $filename, $storagePublicDir . DIRECTORY_SEPARATOR . $filename);

            $proofUrl = '/uploads/proofs/' . $filename;
        } elseif ($request->filled('proof_url')) {
            $proofUrl = $request->input('proof_url');
        } else {
            // Nếu không up ảnh, dùng ảnh mặc định biên lai minh họa
            $proofUrl = 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&auto=format&fit=crop&q=80';
        }

        $invoice->payment_method = 'bank_transfer';
        $invoice->status = 'pending_approval';
        $invoice->payment_status = 'pending_approval';
        $invoice->proof_image = $proofUrl;
        $invoice->transfer_reported_at = Carbon::now()->toDateTimeString();
        $invoice->save();

        // Gửi thông báo cho Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'  => (string)($admin->_id ?? $admin->id),
                'post_id'  => (string)($invoice->post_id ?? ''),
                'title'    => 'Có chuyển khoản mới cần duyệt!',
                'message'  => 'Khách thuê ' . ($invoice->tenant_name ?: 'Khách') . ' vừa báo chuyển khoản hóa đơn ' . $invoice->invoice_code . ' (' . number_format($invoice->total_amount) . ' đ).',
                'type'     => 'info',
                'is_read'  => false,
                'category' => 'finance',
            ]);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Đã gửi thông báo chuyển khoản thành công! Kế toán sẽ kiểm tra và xác nhận hóa đơn của bạn trong ít phút.',
            'invoice_code' => $invoice->invoice_code,
            'status'       => 'pending_approval',
            'proof_image'  => $proofUrl,
        ]);
    }

    /**
     * YÊU CẦU 3: Trang "Duyệt Hoàn Cọc" (Kịch bản B: Deposit Refund)
     * Thống kê các yêu cầu hoàn cọc và danh sách hợp đồng có trạng thái pending_refund
     */
    public function refundRequests(Request $request)
    {
        $tab = $request->query('tab', 'pending'); // 'pending', 'completed', 'all'
        $search = trim($request->query('search', ''));

        // Query cơ sở
        $query = Contract::query();

        if ($tab === 'pending') {
            $query->where('status', 'pending_refund');
        } elseif ($tab === 'completed') {
            $query->where('status', 'terminated')
                  ->where(function($q) {
                      $q->whereNotNull('refund_request_id')
                        ->orWhere('liquidation_details.scenario', 'B');
                  });
        } else {
            // all
            $query->where(function($q) {
                $q->where('status', 'pending_refund')
                  ->orWhere(function($sub) {
                      $sub->where('status', 'terminated')
                          ->where(function($s2) {
                              $s2->whereNotNull('refund_request_id')
                                 ->orWhere('liquidation_details.scenario', 'B');
                          });
                  });
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_code', 'like', "%{$search}%")
                  ->orWhere('tenant_name', 'like', "%{$search}%")
                  ->orWhere('tenant_phone', 'like', "%{$search}%")
                  ->orWhere('checkout_request.bank_account', 'like', "%{$search}%")
                  ->orWhere('checkout_request.bank_name', 'like', "%{$search}%");
            });
        }

        $contracts = $query->latest('updated_at')->latest()->get();

        // Gắn dữ liệu chi tiết cho từng hợp đồng
        foreach ($contracts as $c) {
            $cIdStr = (string)($c->_id ?? $c->id);

            // 1. Thông tin BĐS / Phòng
            if (!$c->post && !empty($c->post_id)) {
                $c->post = Post::find($c->post_id);
            }
            if (!$c->post && !empty($c->room_id)) {
                $c->post = Post::find($c->room_id);
            }

            // Tên phòng / Tòa nhà
            $roomName = 'Phòng';
            if ($c->post && !empty($c->post->title)) {
                $roomName = $c->post->title;
            } elseif (!empty($c->room_id)) {
                $roomName = 'Phòng ' . $c->room_id;
            }
            $c->display_room_name = $roomName;

            // 2. Bản ghi RefundRequest tương ứng
            $refReq = null;
            if (!empty($c->refund_request_id)) {
                $refReq = RefundRequest::find($c->refund_request_id);
            }
            if (!$refReq) {
                $refReq = RefundRequest::where('contract_id', $cIdStr)->first();
            }
            $c->refund_request = $refReq;

            // 3. Số tiền cần hoàn (VNĐ) lấy từ đối soát / refund_request
            $liqDetails = (array)($c->liquidation_details ?? []);
            $refundAmount = 0;
            if ($refReq && !empty($refReq->refund_amount)) {
                $refundAmount = (float)$refReq->refund_amount;
            } elseif (isset($liqDetails['net_amount'])) {
                $refundAmount = abs((float)$liqDetails['net_amount']);
            } elseif (isset($liqDetails['refund_amount'])) {
                $refundAmount = (float)$liqDetails['refund_amount'];
            }
            $c->calc_refund_amount = $refundAmount;

            // 4. Thông tin nhận tiền của khách (Tên NH, STK, Tên chủ TK)
            $checkoutReq = (array)($c->checkout_request ?? []);
            
            $bankName = $refReq->tenant_bank_name 
                ?? ($checkoutReq['bank_name'] ?? ($liqDetails['refund_bank_name'] ?? 'Chưa cập nhật'));
            
            $bankAccount = $refReq->tenant_bank_account 
                ?? ($checkoutReq['bank_account'] ?? ($liqDetails['refund_bank_account'] ?? 'Chưa cập nhật'));
            
            $bankHolder = $refReq->tenant_bank_holder 
                ?? ($checkoutReq['bank_holder'] ?? ($liqDetails['refund_bank_holder'] ?? ($c->tenant_name ?? 'Chưa cập nhật')));

            $c->bank_name = $bankName;
            $c->bank_account = $bankAccount;
            $c->bank_holder = $bankHolder;
        }

        // Thống kê Thẻ KPI
        $allPendingContracts = Contract::where('status', 'pending_refund')->get();
        $totalPendingCount = $allPendingContracts->count();
        
        $totalPendingAmount = 0;
        foreach ($allPendingContracts as $pc) {
            $pcLiq = (array)($pc->liquidation_details ?? []);
            $refAmount = 0;
            if (!empty($pc->refund_request_id)) {
                $rf = RefundRequest::find($pc->refund_request_id);
                if ($rf && !empty($rf->refund_amount)) {
                    $refAmount = (float)$rf->refund_amount;
                }
            }
            if ($refAmount <= 0) {
                if (isset($pcLiq['net_amount'])) {
                    $refAmount = abs((float)$pcLiq['net_amount']);
                } elseif (isset($pcLiq['refund_amount'])) {
                    $refAmount = (float)$pcLiq['refund_amount'];
                }
            }
            $totalPendingAmount += $refAmount;
        }

        // Đã hoàn tất hôm nay / tổng cộng
        $completedRefunds = RefundRequest::whereIn('status', ['completed', 'refunded'])->get();
        $totalCompletedCount = $completedRefunds->count();
        $totalCompletedAmount = $completedRefunds->sum(fn($r) => (float)($r->refund_amount ?? 0));

        return view('Admin.tai_chinh.duyet_hoan_coc', compact(
            'contracts',
            'tab',
            'search',
            'totalPendingCount',
            'totalPendingAmount',
            'totalCompletedCount',
            'totalCompletedAmount'
        ));
    }

    /**
     * Action: Kế toán bấm "Xác nhận đã chuyển khoản" (Kịch bản B)
     * Thực hiện đồng thời:
     * 1. Cập nhật bản ghi hoàn tiền (RefundRequest) thành completed.
     * 2. Chuyển trạng thái hợp đồng (contracts) thành terminated.
     * 3. Tự động giải phóng phòng (rooms & posts) về available / active.
     * 4. Gửi thông báo thành công cho Khách thuê.
     */
    public function confirmRefund(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập hệ thống!'], 401);
            }
            return back()->with('error', 'Vui lòng đăng nhập hệ thống!');
        }

        $cleanId = trim($id);
        $contract = Contract::where('_id', $cleanId)
            ->orWhere('id', $cleanId)
            ->orWhere('contract_code', $cleanId)
            ->first();

        if (!$contract) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng yêu cầu hoàn cọc!'], 404);
            }
            return back()->with('error', 'Không tìm thấy hợp đồng yêu cầu hoàn cọc!');
        }

        if ($contract->status !== 'pending_refund' && $contract->status !== 'terminated') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Hợp đồng không ở trạng thái chờ hoàn cọc (pending_refund)!'], 400);
            }
            return back()->with('error', 'Hợp đồng không ở trạng thái chờ hoàn cọc!');
        }

        $now = Carbon::now();
        $contractIdStr = (string)($contract->_id ?? $contract->id);

        try {
            // 1. Cập nhật bản ghi RefundRequest thành completed
            $refundRequest = null;
            if (!empty($contract->refund_request_id)) {
                $refundRequest = RefundRequest::find($contract->refund_request_id);
            }
            if (!$refundRequest) {
                $refundRequest = RefundRequest::where('contract_id', $contractIdStr)->first();
            }

            $paymentRef = trim($request->input('payment_reference', ''));
            if ($refundRequest) {
                $refundRequest->status = 'completed'; // Yêu cầu: Cập nhật thành completed
                $refundRequest->refunded_at = $now;
                $refundRequest->refunded_by = (string)($user->_id ?? $user->id);
                if (!empty($paymentRef)) {
                    $refundRequest->payment_reference = $paymentRef;
                }
                $refundRequest->save();
            } else {
                // Tạo mới nếu chưa có
                $liqDetails = (array)($contract->liquidation_details ?? []);
                $checkoutReq = (array)($contract->checkout_request ?? []);
                $refAmt = abs((float)($liqDetails['net_amount'] ?? 0));
                $refundRequest = RefundRequest::create([
                    'contract_id'         => $contractIdStr,
                    'contract_code'       => $contract->contract_code,
                    'room_id'             => (string)($contract->room_id ?? $contract->post_id),
                    'post_id'             => (string)($contract->post_id ?? $contract->room_id),
                    'owner_id'            => (string)($contract->owner_id ?? $user->id),
                    'tenant_id'           => $contract->tenant_id,
                    'tenant_name'         => $contract->tenant_name,
                    'tenant_phone'        => $contract->tenant_phone,
                    'tenant_bank_account' => $checkoutReq['bank_account'] ?? ($liqDetails['refund_bank_account'] ?? ''),
                    'tenant_bank_name'    => $checkoutReq['bank_name'] ?? ($liqDetails['refund_bank_name'] ?? ''),
                    'tenant_bank_holder'  => $checkoutReq['bank_holder'] ?? ($liqDetails['refund_bank_holder'] ?? $contract->tenant_name),
                    'refund_amount'       => $refAmt,
                    'status'              => 'completed',
                    'refunded_at'         => $now,
                    'refunded_by'         => (string)($user->_id ?? $user->id),
                    'payment_reference'   => $paymentRef,
                    'created_by'          => (string)($user->_id ?? $user->id),
                ]);
            }

            // 2. Chuyển trạng thái hợp đồng (collection contracts) thành terminated
            $contract->status = 'terminated';
            $contract->terminated_at = $now;
            $contract->terminated_by = (string)($user->_id ?? $user->id);
            if (!empty($paymentRef)) {
                $contract->refund_payment_reference = $paymentRef;
            }

            if (!empty($contract->checkout_request)) {
                $checkoutReq = (array)$contract->checkout_request;
                $checkoutReq['status'] = 'completed';
                $checkoutReq['completed_at'] = $now->toDateTimeString();
                $contract->checkout_request = $checkoutReq;
            }
            $contract->save();

            // 3. Tự động giải phóng phòng (collection rooms và posts) về trạng thái available / active
            $targetIds = array_filter(array_unique([
                (string)($contract->post_id ?? ''),
                (string)($contract->room_id ?? '')
            ]));

            $primaryPost = null;
            foreach ($targetIds as $targetId) {
                $post = Post::where('_id', $targetId)->orWhere('id', $targetId)->first();
                if ($post) {
                    if (!$primaryPost) {
                        $primaryPost = $post;
                    }
                    $post->status = 'active';
                    $post->is_rented = false;
                    $post->rental_status = 'available';
                    $post->current_contract_id = null;
                    $post->current_tenant_id = null;
                    $post->save();
                }

                Post::where('_id', $targetId)
                    ->orWhere('id', $targetId)
                    ->update([
                        'status'              => 'active',
                        'is_rented'           => false,
                        'rental_status'       => 'available',
                        'current_contract_id' => null,
                        'current_tenant_id'   => null,
                    ]);

                $roomNumber = $post ? $post->title : $targetId;
                Room::updateOrCreate(
                    ['room_number' => $roomNumber],
                    [
                        'status'              => 'available',
                        'current_contract_id' => null,
                        'current_tenant_id'   => null,
                        'building_id'         => $post ? ($post->building_id ?? null) : null,
                        'post_id'             => $targetId,
                    ]
                );

                Room::where('_id', $targetId)
                    ->orWhere('id', $targetId)
                    ->orWhere('post_id', $targetId)
                    ->update([
                        'status'              => 'available',
                        'current_contract_id' => null,
                        'current_tenant_id'   => null,
                    ]);
            }

            // Ẩn hoàn toàn tất cả các yêu cầu liên hệ của hợp đồng này khỏi trang khách hàng liên hệ
            \App\Http\Controllers\ContactController::hideContactsForContract($contract);

            // 4. Gửi thông báo thành công cho Khách thuê
            $liqDetails = (array)($contract->liquidation_details ?? []);
            $refAmt = $refundRequest ? (float)$refundRequest->refund_amount : abs((float)($liqDetails['net_amount'] ?? 0));
            $formattedAmt = number_format($refAmt) . ' VNĐ';

            if (!empty($contract->tenant_id)) {
                Notification::create([
                    'user_id'    => (string)$contract->tenant_id,
                    'title'      => 'Kế toán đã hoàn tất chuyển khoản hoàn tiền cọc',
                    'message'    => "RentHome đã hoàn tất chuyển khoản số tiền cọc {$formattedAmt} cho Hợp đồng {$contract->contract_code} vào tài khoản ngân hàng của bạn. Hợp đồng chính thức hoàn tất thanh lý!",
                    'type'       => 'success',
                    'is_read'    => false,
                    'category'   => 'contract_refund',
                    'action_url' => url('/nhadangthue'),
                    'created_at' => $now,
                ]);
            }

            // Ghi log hoạt động AdminActivity
            $actorName = $user->account_name ?: ($user->username ?: 'Kế toán Admin');
            $roomLabel = $primaryPost ? $primaryPost->title : ($contract->room_id ?? 'Phòng');
            AdminActivity::log(
                'refund_completed',
                'contract',
                'Xác nhận chuyển khoản hoàn cọc & Thanh lý hợp đồng',
                "{$actorName} đã xác nhận chuyển khoản hoàn tiền cọc ({$formattedAmt}) cho khách thuê {$contract->tenant_name} (HĐ: {$contract->contract_code}). Phòng {$roomLabel} đã được giải phóng về trạng thái [available] sẵn sàng cho thuê mới.",
                $contract,
                [
                    'contract_id'       => $contractIdStr,
                    'refund_amount'     => $refAmt,
                    'payment_reference' => $paymentRef
                ]
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Đã xác nhận chuyển khoản {$formattedAmt} thành công! Hợp đồng {$contract->contract_code} đã hoàn tất thanh lý và phòng {$roomLabel} đã được giải phóng về trạng thái còn trống.",
                    'contract_code' => $contract->contract_code,
                    'status' => 'terminated',
                ]);
            }

            return back()->with('success', "Đã xác nhận chuyển khoản hoàn cọc thành công cho hợp đồng {$contract->contract_code}!");

        } catch (\Throwable $e) {
            Log::error('Lỗi khi Kế toán xác nhận hoàn cọc: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi hệ thống khi xác nhận hoàn cọc: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
