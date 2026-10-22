<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Invoice;
use App\Models\Notification;
use Carbon\Carbon;

class OperationManagementController extends Controller
{
    /**
     * Hiển thị danh sách phòng & tính toán trạng thái thanh toán tự động theo thời gian
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang quản lý vận hành.');
        }

        if ($user->isIndividual()) {
            return redirect()->route('nha-dang-thue')->with('info', 'Trang Quản lý vận hành dành cho Ban Quản Trị và Quản lý tòa nhà.');
        }

        // 1. Truy xuất danh sách các hợp đồng đang active của doanh nghiệp chung
        $contractsQuery = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly']);

        // Nếu là tài khoản con và có phân quyền theo tòa nhà chỉ định
        if ($user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
            $assignedIds = array_map('strval', (array)$user->assigned_buildings);
            $postIds = Post::whereIn('building_id', $assignedIds)->pluck('_id')->map(fn($id) => (string)$id)->toArray();
            $contractsQuery->where(function ($q) use ($postIds, $user) {
                $q->whereIn('post_id', $postIds)
                  ->orWhere('owner_id', (string)$user->id)
                  ->orWhere('created_by', (string)$user->id);
            });
        }

        $contracts = $contractsQuery->latest()->get();

        $now = Carbon::now();
        $today = $now->day;
        $currentMonthStr = $now->format('m/Y'); // Ví dụ: '10/2026'
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Lấy thông tin tài khoản ngân hàng của Công ty / Doanh nghiệp RentHome để tạo mã VietQR
        $enterpriseUser = \App\Models\User::getEnterpriseUser();
        $bankUser = ($enterpriseUser && !empty($enterpriseUser->bank_account)) ? $enterpriseUser : Auth::user();
        if (!$bankUser || empty($bankUser->bank_account)) {
            $fallbackBankUser = \App\Models\User::whereNotNull('bank_account')->where('bank_account', '!=', '')->first();
            if ($fallbackBankUser) {
                $bankUser = $fallbackBankUser;
            }
        }

        $bankName = !empty($bankUser->bank_name) ? $bankUser->bank_name : 'VietBank';
        $bankAccount = !empty($bankUser->bank_account) ? $bankUser->bank_account : '00939399884747';
        $rawHolder = (!empty($bankUser->bank_owner)) 
            ? $bankUser->bank_owner 
            : (!empty($bankUser->bank_account_holder) 
                ? $bankUser->bank_account_holder 
                : (!empty($bankUser->account_name) 
                    ? $bankUser->account_name 
                    : (!empty($bankUser->name) 
                        ? $bankUser->name 
                        : 'NGUYEN THI VAN ANH')));
        $bankHolder = mb_strtoupper($rawHolder, 'UTF-8');

        // 2. Duyệt qua từng hợp đồng và tính toán trạng thái thanh toán ảo theo logic thời gian
        $rooms = $contracts->map(function ($contract) use ($now, $today, $currentMonth, $currentYear, $currentMonthStr, $bankName, $bankAccount, $bankHolder) {
            $contractId = (string)($contract->_id ?? $contract->id);
            $post = $contract->post;
            if (!$post && !empty($contract->post_id)) {
                $post = Post::find($contract->post_id);
            }
            if (!$post && !empty($contract->room_id)) {
                $post = Post::find($contract->room_id);
            }
            $postTitle = $post ? $post->title : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ');
            $roomCode = $contract->contract_code ?: ('PH-' . substr($contractId, -4));

            // Lấy ngày chốt hóa đơn hàng tháng (bill_date hoặc billing_date)
            // Ví dụ: lưu '5' hoặc 'Mùng 5' -> lấy số nguyên 5
            $rawBillDate = $contract->bill_date ?? $contract->billing_date ?? 5;
            $billDate = (int) preg_replace('/[^0-9]/', '', (string)$rawBillDate);
            if ($billDate <= 0 || $billDate > 31) {
                $billDate = 5; // Mặc định ngày 5 nếu chưa thiết lập
            }

            // Lấy hóa đơn mới nhất của hợp đồng này (nếu có)
            $latestInvoice = Invoice::where(function ($q) use ($contractId, $contract) {
                    $q->where('contract_id', $contractId);
                    if (!empty($contract->contract_code)) {
                        $q->orWhere('contract_id', $contract->contract_code)
                          ->orWhere('contract_code', $contract->contract_code);
                    }
                })
                ->latest()
                ->first();

            // Kiểm tra xem tháng hiện tại đã có hóa đơn nào trong bảng invoices được thanh toán chưa
            $hasPaidInvoice = Invoice::where(function ($q) use ($contractId, $contract) {
                    $q->where('contract_id', $contractId);
                    if (!empty($contract->contract_code)) {
                        $q->orWhere('contract_id', $contract->contract_code)
                          ->orWhere('contract_code', $contract->contract_code);
                    }
                })
                ->where(function ($q) use ($now, $currentMonth, $currentYear, $currentMonthStr) {
                    $q->where('billing_month', $currentMonthStr)
                      ->orWhere('month', $currentMonth)
                      ->orWhere('billing_period', 'like', "%{$currentMonthStr}%")
                      ->orWhereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                })
                ->where(function ($q) {
                    $q->where('status', 'paid')
                      ->orWhere('payment_status', 'paid');
                })
                ->exists();

            // LOGIC TRẠNG THÁI: Tôn trọng trạng thái chuyển khoản chờ duyệt hoặc thanh toán
            $isPendingApproval = ($latestInvoice && ($latestInvoice->status === 'pending_approval' || $latestInvoice->payment_status === 'pending_approval'));
            if ($isPendingApproval) {
                $status = 'pending_approval'; // Khách đã chuyển khoản, chờ Admin kế toán duyệt
                $statusLabel = 'Khách Đã CK - Chờ Duyệt';
                $isDue = false;
            } elseif ($contract->payment_status === 'paid' || $hasPaidInvoice) {
                $status = 'paid'; // Đã thanh toán -> Chấm xanh
                $statusLabel = ($latestInvoice && $latestInvoice->payment_method === 'bank_transfer') 
                    ? 'Đã CK (Đã duyệt)' 
                    : (($latestInvoice && $latestInvoice->payment_method === 'cash') ? 'Đã thu tiền mặt' : 'Đã thu tiền');
                $isDue = false;
            } elseif ($contract->payment_status === 'unpaid') {
                $status = 'unpaid'; // Chưa thanh toán -> Chấm đỏ
                $statusLabel = 'Chưa thu tiền';
                $isDue = true;
            } elseif ($today < $billDate) {
                $status = 'paid'; // Chưa đến ngày chốt -> Chấm xanh
                $statusLabel = 'Chưa đến kỳ thu';
                $isDue = false;
            } else {
                $status = 'unpaid'; // Đến hạn mà chưa thu -> Chấm đỏ
                $statusLabel = 'Chưa thu tiền';
                $isDue = true;

                // Tự động kích hoạt luồng thông báo thời gian thực khi đến hạn
                $this->triggerDueNotification($contract, $billDate, $currentMonthStr, $roomCode, $postTitle);
            }

            $contractStart = $contract->start_date ? date('d/m/Y', strtotime($contract->start_date)) : '01/01/2026';
            $contractEnd = $contract->end_date ?: ($contract->start_date ? date('d/m/Y', strtotime($contract->start_date . ' + ' . ($contract->duration_months ?? 12) . ' months')) : '01/01/2027');

            // Tính toán chi tiết dữ liệu thực tế của hóa đơn để tránh lệch dữ liệu với popup xem hóa đơn
            $invRentPrice = $latestInvoice && isset($latestInvoice->rent_price) ? (float)$latestInvoice->rent_price : (float)($contract->rent_price ?? ($post->price ?? 0));
            $invDeposit = $latestInvoice && isset($latestInvoice->deposit) ? (float)$latestInvoice->deposit : 0;

            // Điện
            $elecRate = (float)($contract->electricity_price ?? 3500);
            $elecOld = $latestInvoice && isset($latestInvoice->electricity_old) ? (float)$latestInvoice->electricity_old : (float)($contract->electricity_start ?? 0);
            $elecNew = $latestInvoice && isset($latestInvoice->electricity_new) ? (float)$latestInvoice->electricity_new : $elecOld;
            $elecUsage = max(0, $elecNew - $elecOld);
            $elecAmount = $latestInvoice && isset($latestInvoice->electricity_amount) ? (float)$latestInvoice->electricity_amount : ($elecUsage * $elecRate);

            // Nước
            $waterRate = (float)($contract->water_price_block ?? 15000);
            $waterOld = $latestInvoice && isset($latestInvoice->water_old) ? (float)$latestInvoice->water_old : (float)($contract->water_start ?? 0);
            $waterNew = $latestInvoice && isset($latestInvoice->water_new) ? (float)$latestInvoice->water_new : $waterOld;
            $waterUsage = max(0, $waterNew - $waterOld);
            $waterAmount = $latestInvoice && isset($latestInvoice->water_amount) ? (float)$latestInvoice->water_amount : ($waterUsage * $waterRate);

            // Dịch vụ cố định (Wifi, rác, xe...)
            $wifiPrice = (float)($contract->internet_price ?? 0);
            $garbagePrice = (float)($contract->garbage_price ?? 0);
            $parkingPrice = (float)($contract->parking_price ?? 0);
            $defaultService = $wifiPrice + $garbagePrice + $parkingPrice;
            $serviceAmount = $latestInvoice && isset($latestInvoice->service_amount) ? (float)$latestInvoice->service_amount : $defaultService;

            // Tổng hóa đơn thực tế
            if ($latestInvoice && isset($latestInvoice->total_amount) && (float)$latestInvoice->total_amount > 0) {
                $invTotal = (float)$latestInvoice->total_amount;
            } else {
                $invTotal = $invRentPrice + $elecAmount + $waterAmount + $serviceAmount + $invDeposit;
            }

            // Mã hóa đơn chuẩn (chuẩn hóa không bị trùng lặp HD-HD-)
            $cleanInvCode = $latestInvoice && !empty($latestInvoice->invoice_code) ? $latestInvoice->invoice_code : ($contract->contract_code ?: $roomCode);
            if (!$cleanInvCode) {
                $cleanInvCode = $roomCode;
            }
            // Loại bỏ tiền tố lặp thừa
            $cleanInvCode = preg_replace('/^(HD-)+/i', 'HD-', $cleanInvCode);

            $invPeriod = $latestInvoice && !empty($latestInvoice->billing_period) ? $latestInvoice->billing_period : ('Tháng ' . $currentMonthStr);
            $invMonth = $latestInvoice && !empty($latestInvoice->billing_month) ? $latestInvoice->billing_month : $currentMonthStr;

            $invoiceDetails = [
                'invoice_code'    => $cleanInvCode,
                'contract_code'   => $contract->contract_code ?: $roomCode,
                'room_code'       => $roomCode,
                'room_name'       => $postTitle,
                'tenant_name'     => $contract->tenant_name ?: 'Khách thuê',
                'tenant_phone'    => $contract->tenant_phone ?: '',
                'billing_period'  => $invPeriod,
                'billing_month'   => $invMonth,
                'status'          => $status,
                'status_label'    => $statusLabel,
                'invoice_status'  => $latestInvoice ? $latestInvoice->status : $status,
                'payment_method'  => $latestInvoice ? $latestInvoice->payment_method : null,
                'proof_image'     => $latestInvoice ? $latestInvoice->proof_image : null,
                'is_remitted_to_admin' => $latestInvoice ? $latestInvoice->is_remitted_to_admin : null,
                'rent_price'      => $invRentPrice,
                'deposit'         => $invDeposit,
                'elec_old'        => $elecOld,
                'elec_new'        => $elecNew,
                'elec_usage'      => $elecUsage,
                'elec_rate'       => $elecRate,
                'elec_amount'     => $elecAmount,
                'water_old'       => $waterOld,
                'water_new'       => $waterNew,
                'water_usage'     => $waterUsage,
                'water_rate'      => $waterRate,
                'water_amount'    => $waterAmount,
                'wifi_price'      => $wifiPrice,
                'garbage_price'   => $garbagePrice,
                'parking_price'   => $parkingPrice,
                'service_amount'  => $serviceAmount,
                'total_amount'    => $invTotal,
                'bank_name'       => $bankName,
                'bank_account'    => $bankAccount,
                'bank_holder'     => $bankHolder,
                'memo'            => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanInvCode)) . ' ' . $now->format('mY'),
            ];

            $contractDetails = [
                'room_code'              => $roomCode,
                'contract_code'          => $contract->contract_code ?: $roomCode,
                'name'                   => $postTitle,
                'tenant_name'            => $contract->tenant_name ?: 'Khách thuê',
                'tenant_phone'           => $contract->tenant_phone ?: 'Chưa cập nhật',
                'tenant_id_card'         => $contract->tenant_id_card ?: 'Chưa cập nhật',
                'price'                  => (float)($contract->rent_price ?? ($post->price ?? 0)),
                'deposit'                => (float)($contract->deposit ?? 0),
                'contract_start'         => $contractStart,
                'contract_end'           => $contractEnd,
                'duration_months'        => (int)($contract->duration_months ?? 12),
                'electricity_price'      => (float)($contract->electricity_price ?? 0),
                'electricity_start'      => (int)($contract->electricity_start ?? 0),
                'water_calculation_type' => $contract->water_calculation_type ?: 'khoi',
                'water_price_block'      => (float)($contract->water_price_block ?? 0),
                'water_start'            => (int)($contract->water_start ?? 0),
                'water_price_person'     => (float)($contract->water_price_person ?? 0),
                'water_people_count'     => (int)($contract->water_people_count ?? 1),
                'internet_price'         => (float)($contract->internet_price ?? 0),
                'garbage_price'          => (float)($contract->garbage_price ?? 0),
                'payment_cycle'          => $contract->payment_cycle ?: '1 tháng',
                'bill_date'              => $billDate,
                'billing_date'           => $contract->billing_date ?: ('Mùng ' . $billDate),
                'payment_limit_days'     => $contract->payment_limit_days ?: '5 ngày',
                'late_penalty_fee'       => $contract->late_penalty_fee ?: '20.000 VNĐ / ngày',
                'address'                => $post ? ($post->address ?? trim(($post->ward ?? '') . ', ' . ($post->district ?? '') . ', ' . ($post->province ?? ''), ', ')) : 'Số 18 Trần Phú, Phường Mộ Lao, Quận Hà Đông, TP. Hà Nội',
                'checkout_request'       => $contract->checkout_request ?? [],
                'has_checkout_request'   => !empty($contract->checkout_request) && (
                    (is_array($contract->checkout_request) && ($contract->checkout_request['status'] ?? '') === 'pending') ||
                    (is_object($contract->checkout_request) && ($contract->checkout_request->status ?? '') === 'pending')
                ),
            ];

            $checkoutReq = $contract->checkout_request ?? [];
            $hasCheckoutReq = !empty($checkoutReq) && (
                (is_array($checkoutReq) && ($checkoutReq['status'] ?? '') === 'pending') ||
                (is_object($checkoutReq) && ($checkoutReq->status ?? '') === 'pending')
            );

            return (object)[
                'id'                     => $contractId,
                'room_code'              => $roomCode,
                'name'                   => $postTitle,
                'room_name'              => $postTitle,
                'tenant_name'            => $contract->tenant_name ?: 'Khách thuê',
                'tenant_phone'           => $contract->tenant_phone ?: 'Chưa cập nhật',
                'tenant_id_card'         => $contract->tenant_id_card ?: 'Chưa cập nhật',
                'price'                  => (float)($contract->rent_price ?? ($post->price ?? 0)),
                'deposit'                => (float)($contract->deposit ?? 0),
                'bill_date'              => $billDate,
                'status'                 => $status, // Biến ảo: 'paid', 'unpaid', hoặc 'pending_approval'
                'status_label'           => $statusLabel,
                'is_due'                 => $isDue, // true nếu đến hạn và chưa thu
                'payment_method'         => $latestInvoice ? $latestInvoice->payment_method : null,
                'proof_image'            => $latestInvoice ? $latestInvoice->proof_image : null,
                'debt_period'            => 'Kỳ ' . $currentMonthStr . ' (Hạn ' . $billDate . '/' . $currentMonth . ')',
                'debt_amount'            => $invTotal,
                'paid_period'            => 'Kỳ ' . $currentMonthStr,
                'contract_code'          => $contract->contract_code ?: $roomCode,
                'contract_start'         => $contractStart,
                'contract_end'           => $contractEnd,
                'duration_months'        => (int)($contract->duration_months ?? 12),
                'electricity_price'      => (float)($contract->electricity_price ?? 0),
                'electricity_start'      => (int)($contract->electricity_start ?? 0),
                'water_calculation_type' => $contract->water_calculation_type ?: 'khoi',
                'water_price_block'      => (float)($contract->water_price_block ?? 0),
                'water_start'            => (int)($contract->water_start ?? 0),
                'water_price_person'     => (float)($contract->water_price_person ?? 0),
                'water_people_count'     => (int)($contract->water_people_count ?? 1),
                'internet_price'         => (float)($contract->internet_price ?? 0),
                'garbage_price'          => (float)($contract->garbage_price ?? 0),
                'payment_cycle'          => $contract->payment_cycle ?: '1 tháng',
                'payment_limit_days'     => $contract->payment_limit_days ?: '5 ngày',
                'late_penalty_fee'       => $contract->late_penalty_fee ?: '20.000 VNĐ / ngày',
                'contract_status'        => $contract->status ?? 'active',
                'checkout_request'       => $checkoutReq,
                'has_checkout_request'   => $hasCheckoutReq,
                'contract'               => $contract,
                'contract_details'       => $contractDetails,
                'invoice_code'           => $cleanInvCode,
                'invoice_total'          => $invTotal,
                'invoice_details'        => $invoiceDetails,
                'post'                   => $post,
                'image'                  => $post ? $post->display_image : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
            ];
        });

        // 3. Thống kê số lượng phòng
        $paidCount = $rooms->where('status', 'paid')->count();
        $unpaidCount = $rooms->where('status', 'unpaid')->count();
        $pendingApprovalCount = $rooms->where('status', 'pending_approval')->count();
        $dueRooms = $rooms->where('status', 'unpaid')->values();

        $roomsQuery = Post::where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })->where('status', '!=', 'đã cho thuê')->where(function($q) {
            $q->whereNull('is_rented')->orWhere('is_rented', false);
        });

        if ($user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
            $assignedIds = array_map('strval', (array)$user->assigned_buildings);
            $roomsQuery->whereIn('building_id', $assignedIds);
        }

        $availableRooms = $roomsQuery->get();

        return view('qlvan_hanh.Room_list', compact('rooms', 'paidCount', 'unpaidCount', 'pendingApprovalCount', 'availableRooms', 'dueRooms'));
    }

    /**
     * API kiểm tra thông báo thời gian thực khi đến hạn đóng tiền
     */
    public function checkRealtimeDue(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->day;
        $currentMonthStr = $now->format('m/Y');

        $contractsQuery = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly']);
        if ($user && $user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
            $assignedIds = array_map('strval', (array)$user->assigned_buildings);
            $postIds = Post::whereIn('building_id', $assignedIds)->pluck('_id')->map(fn($id) => (string)$id)->toArray();
            $contractsQuery->where(function($q) use ($postIds, $user) {
                $q->whereIn('post_id', $postIds)
                  ->orWhere('owner_id', (string)$user->id)
                  ->orWhere('created_by', (string)$user->id);
            });
        }
        $contracts = $contractsQuery->get();

        $dueList = [];

        foreach ($contracts as $contract) {
            $rawBillDate = $contract->bill_date ?? $contract->billing_date ?? 5;
            $billDate = (int) preg_replace('/[^0-9]/', '', (string)$rawBillDate);
            if ($billDate <= 0 || $billDate > 31) $billDate = 5;

            if ($today >= $billDate) {
                $contractId = (string)($contract->_id ?? $contract->id);
                $isPaid = Invoice::where('contract_id', $contractId)
                    ->where(function ($q) use ($now, $currentMonthStr) {
                        $q->where('billing_month', $currentMonthStr)
                          ->orWhere('month', $now->month)
                          ->orWhere('billing_period', 'like', "%{$currentMonthStr}%");
                    })
                    ->where(function ($q) {
                        $q->where('status', 'paid')->orWhere('payment_status', 'paid');
                    })
                    ->exists();

                if (!$isPaid) {
                    $dueList[] = [
                        'contract_id'  => $contractId,
                        'room_code'    => $contract->contract_code ?: ('PH-' . substr($contractId, -4)),
                        'tenant_name'  => $contract->tenant_name,
                        'tenant_phone' => $contract->tenant_phone,
                        'bill_date'    => $billDate,
                        'price'        => (float)($contract->rent_price ?? 0),
                        'period'       => $currentMonthStr,
                    ];
                }
            }
        }

        return response()->json([
            'success'   => true,
            'today'     => $today,
            'period'    => $currentMonthStr,
            'due_count' => count($dueList),
            'due_rooms' => $dueList,
        ]);
    }

    /**
     * Helper tạo thông báo tự động (chống trùng lặp trong cùng một kỳ)
     */
    private function triggerDueNotification($contract, $billDate, $monthYearStr, $roomCode, $roomName)
    {
        $ownerId = $contract->owner_id ?? null;
        $tenantId = $contract->tenant_id ?? null;
        $amount = number_format($contract->rent_price ?? 0);

        // 1. Gửi thông báo cho Chủ Nhà nếu chưa gửi kỳ này
        if (!empty($ownerId)) {
            $notifiedOwner = Notification::where('user_id', (string)$ownerId)
                ->where('category', 'billing_due')
                ->where('message', 'like', "%{$roomCode}%")
                ->where('message', 'like', "%kỳ {$monthYearStr}%")
                ->exists();

            if (!$notifiedOwner) {
                Notification::create([
                    'user_id'  => (string)$ownerId,
                    'post_id'  => (string)($contract->post_id ?? ''),
                    'title'    => "Đến hạn thu tiền phòng {$roomCode} (Kỳ {$monthYearStr})",
                    'message'  => "Phòng {$roomCode} ({$roomName}) có ngày chốt hóa đơn mùng {$billDate} đã đến hạn thu tiền kỳ {$monthYearStr}. Số tiền cần thu: {$amount}đ.",
                    'type'     => 'warning',
                    'is_read'  => false,
                    'category' => 'billing_due',
                ]);
            }
        }

        // 2. Gửi thông báo cho Khách Thuê nếu có tài khoản liên kết
        if (!empty($tenantId)) {
            $notifiedTenant = Notification::where('user_id', (string)$tenantId)
                ->where('category', 'billing_due')
                ->where('message', 'like', "%kỳ {$monthYearStr}%")
                ->exists();

            if (!$notifiedTenant) {
                Notification::create([
                    'user_id'  => (string)$tenantId,
                    'post_id'  => (string)($contract->post_id ?? ''),
                    'title'    => "Thông báo đến hạn đóng tiền phòng {$roomCode}",
                    'message'  => "Tiền thuê phòng {$roomName} kỳ {$monthYearStr} đã đến hạn thanh toán vào ngày {$billDate}. Số tiền: {$amount}đ. Vui lòng thanh toán sớm.",
                    'type'     => 'warning',
                    'is_read'  => false,
                    'category' => 'billing_due',
                ]);
            }
        }
    }

    /**
     * Hiển thị giao diện Tạo Hóa Đơn Hàng Tháng với dữ liệu thực tế từ cơ sở dữ liệu
     */
    public function createInvoice(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }
        if ($user->isIndividual()) {
            return redirect()->route('nha-dang-thue');
        }

        // 1. Lấy danh sách hợp đồng hoạt động của doanh nghiệp
        $contractsQuery = Contract::whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly']);
        if ($user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
            $assignedIds = array_map('strval', (array)$user->assigned_buildings);
            $postIds = Post::whereIn('building_id', $assignedIds)->pluck('_id')->map(fn($id) => (string)$id)->toArray();
            $contractsQuery->where(function ($q) use ($postIds, $user) {
                $q->whereIn('post_id', $postIds)
                  ->orWhere('owner_id', (string)$user->id)
                  ->orWhere('created_by', (string)$user->id);
            });
        }

        $contracts = $contractsQuery->latest()->get();

        $now = Carbon::now();
        $currentMonthStr = 'Tháng ' . $now->format('m/Y');

        $roomsDataMap = [];
        $contractsList = [];

        foreach ($contracts as $contract) {
            $contractId = (string)($contract->_id ?? $contract->id);
            $post = $contract->post;
            if (!$post && !empty($contract->post_id)) {
                $post = Post::find($contract->post_id);
            }
            if (!$post && !empty($contract->room_id)) {
                $post = Post::find($contract->room_id);
            }

            $owner = $contract->owner;
            if (!$owner && !empty($contract->owner_id)) {
                $owner = \App\Models\User::find($contract->owner_id);
            }
            if (!$owner && $user) {
                $owner = $user;
            }

            $postTitle = $post ? $post->title : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ');
            $contractCode = $contract->contract_code ?: ('PH-' . substr($contractId, -4));

            // Lấy hóa đơn mới nhất của hợp đồng để lấy chỉ số điện/nước đã chốt kỳ trước
            $lastInvoice = Invoice::where('contract_id', $contractId)
                ->where('status', '!=', 'cancelled')
                ->latest()
                ->first();

            // Số điện cũ
            $elecOld = 0;
            if ($lastInvoice && isset($lastInvoice->electricity_new) && is_numeric($lastInvoice->electricity_new)) {
                $elecOld = (float)$lastInvoice->electricity_new;
            } elseif (isset($contract->electricity_start) && is_numeric($contract->electricity_start)) {
                $elecOld = (float)$contract->electricity_start;
            }

            // Số nước cũ
            $waterOld = 0;
            if ($lastInvoice && isset($lastInvoice->water_new) && is_numeric($lastInvoice->water_new)) {
                $waterOld = (float)$lastInvoice->water_new;
            } elseif (isset($contract->water_start) && is_numeric($contract->water_start)) {
                $waterOld = (float)$contract->water_start;
            }

            // Đơn giá điện & nước
            $elecRate = (float)($contract->electricity_price ?? 3500);
            $waterRate = (float)($contract->water_price_block ?? $contract->water_price_person ?? 15000);

            // Ngày chốt & Hạn thanh toán
            $rawBillDate = $contract->bill_date ?? $contract->billing_date ?? 5;
            $billDateNum = (int) preg_replace('/[^0-9]/', '', (string)$rawBillDate);
            if ($billDateNum <= 0 || $billDateNum > 31) {
                $billDateNum = 5;
            }
            $dueDate = $now->copy()->day(min($billDateNum, $now->daysInMonth))->format('Y-m-d');

            // Thông tin ngân hàng của Doanh nghiệp RentHome cố định cho mã VietQR
            $enterpriseUser = \App\Models\User::getEnterpriseUser();
            $bankUser = ($enterpriseUser && !empty($enterpriseUser->bank_account)) 
                ? $enterpriseUser 
                : (($owner && !empty($owner->bank_account)) ? $owner : Auth::user());
            if (!$bankUser || empty($bankUser->bank_account)) {
                $fallbackBankUser = \App\Models\User::whereNotNull('bank_account')->where('bank_account', '!=', '')->first();
                if ($fallbackBankUser) {
                    $bankUser = $fallbackBankUser;
                }
            }

            $bankName = !empty($bankUser->bank_name) ? $bankUser->bank_name : 'VietBank';
            $bankAccount = !empty($bankUser->bank_account) ? $bankUser->bank_account : '00939399884747';
            
            // Trường lưu tên chủ tài khoản trong hồ sơ là bank_owner
            $rawHolder = (!empty($bankUser->bank_owner)) 
                ? $bankUser->bank_owner 
                : (!empty($bankUser->bank_account_holder) 
                    ? $bankUser->bank_account_holder 
                    : (!empty($bankUser->account_name) 
                        ? $bankUser->account_name 
                        : (!empty($bankUser->name) 
                            ? $bankUser->name 
                            : 'NGUYEN THI VAN ANH')));

            $bankHolder = mb_strtoupper($rawHolder, 'UTF-8');

            // Thông tin tiền cọc hợp đồng & kiểm tra tháng đầu tiên
            $contractDeposit = (float)($contract->deposit ?? 0);

            // Kiểm tra xem hợp đồng này đã có hóa đơn nào chưa:
            // Nếu invoices->count() == 0 thì đây là hóa đơn đầu tiên (tháng đầu mới ký)
            $invoicesCount = Invoice::where(function ($q) use ($contractId, $contract) {
                    $q->where('contract_id', $contractId);
                    if (!empty($contract->contract_code)) {
                        $q->orWhere('contract_id', $contract->contract_code)
                          ->orWhere('contract_code', $contract->contract_code);
                    }
                })
                ->where('status', '!=', 'cancelled')
                ->count();

            $isFirstMonth = ($invoicesCount === 0);
            $defaultDeposit = ($isFirstMonth && $contractDeposit > 0) ? $contractDeposit : 0;

            $startMonthYear = !empty($contract->start_date)
                ? (date('m/Y', strtotime($contract->start_date)) ?: $now->format('m/Y'))
                : ($contract->created_at ? Carbon::parse($contract->created_at)->format('m/Y') : $now->format('m/Y'));

            $tenantNameDisplay = ($contract->tenant_name ?: 'Khách thuê') . ($contract->tenant_phone ? ' (SĐT: ' . $contract->tenant_phone . ')' : '');

            $item = [
                'contractId'      => $contractId,
                'contractCode'    => $contractCode,
                'roomName'        => $postTitle . ' (' . $contractCode . ')',
                'postTitle'       => $postTitle,
                'tenantName'      => $tenantNameDisplay,
                'tenantRawName'   => $contract->tenant_name ?: 'Khách thuê',
                'tenantPhone'     => $contract->tenant_phone ?: '',
                'rent'            => (float)($contract->rent_price ?? ($post->price ?? 0)),
                'contractDeposit' => $contractDeposit,
                'deposit'         => $defaultDeposit,
                'isFirstMonth'    => $isFirstMonth,
                'startMonth'      => $startMonthYear,
                'elecOld'         => $elecOld,
                'elecNew'         => $elecOld, // Khởi tạo bằng số cũ
                'elecRate'        => $elecRate,
                'waterOld'        => $waterOld,
                'waterNew'        => $waterOld, // Khởi tạo bằng số cũ
                'waterRate'       => $waterRate,
                'wifi'            => (float)($contract->internet_price ?? 0),
                'garbage'         => (float)($contract->garbage_price ?? 0),
                'parking'         => (float)($contract->parking_price ?? 0),
                'billingPeriod'   => $currentMonthStr,
                'dueDate'         => $dueDate,
                'bankName'        => $bankName,
                'bankAccount'     => $bankAccount,
                'bankHolder'      => $bankHolder,
                'memo'            => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $contractCode)) . ' THANG ' . $now->format('m/Y'),
            ];

            // Lưu vào dictionary với nhiều key để tra cứu linh hoạt
            $roomsDataMap[$contractCode] = $item;
            $roomsDataMap[$contractId] = $item;
            $roomsDataMap[str_replace('-', ' ', $contractCode)] = $item;

            $contractsList[] = (object)[
                'id'    => $contractId,
                'code'  => $contractCode,
                'title' => $postTitle . ' – ' . $contractCode . ' (' . ($contract->tenant_name ?: 'Khách thuê') . ')',
                'data'  => $item,
            ];
        }

        // 2. Nhận diện phòng được chọn qua URL query param (?room=... hoặc ?contract=...)
        $roomQuery = $request->query('room') ?? $request->query('contract') ?? $request->query('contract_id');
        $selectedKey = null;

        if ($roomQuery) {
            $cleanQuery = trim($roomQuery);
            $normalizedQuery = str_replace(' ', '-', $cleanQuery);

            foreach ($contractsList as $c) {
                if ($c->code === $cleanQuery || $c->code === $normalizedQuery || $c->id === $cleanQuery) {
                    $selectedKey = $c->code;
                    break;
                }
                if (strcasecmp($c->code, $cleanQuery) === 0 || strcasecmp($c->code, $normalizedQuery) === 0) {
                    $selectedKey = $c->code;
                    break;
                }
            }
        }

        if (!$selectedKey && count($contractsList) > 0) {
            $selectedKey = $contractsList[0]->code;
        }

        $selectedData = ($selectedKey && isset($roomsDataMap[$selectedKey])) ? $roomsDataMap[$selectedKey] : null;

        return view('qlvan_hanh.tao_hoa_don', [
            'contracts'    => $contractsList,
            'roomsDataMap' => $roomsDataMap,
            'selectedKey'  => $selectedKey,
            'selectedData' => $selectedData,
        ]);
    }

    /**
     * Lưu và phát hành hóa đơn thật vào cơ sở dữ liệu MongoDB
     */
    public function storeInvoice(Request $request)
    {
        $contractId = $request->input('contract_id');
        $contractCode = $request->input('contract_code');

        $contract = null;
        if ($contractId) {
            $contract = Contract::find($contractId);
        }
        if (!$contract && $contractCode) {
            $cleanCode = str_replace(' ', '-', trim($contractCode));
            $contract = Contract::where('contract_code', $cleanCode)
                ->orWhere('contract_code', $contractCode)
                ->first();
        }

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu hợp đồng tương ứng!',
            ], 404);
        }

        $contractIdStr = (string)($contract->_id ?? $contract->id);
        $period = $request->input('billing_period', 'Tháng ' . Carbon::now()->format('m/Y'));
        $now = Carbon::now();

        // Tạo mã hóa đơn INV-YYYYMM-XXXX
        $invoiceCode = 'INV-' . $now->format('Ym') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

        $enterpriseUser = \App\Models\User::getEnterpriseUser();
        $ownerId = $contract->owner_id ?: ($enterpriseUser ? (string)($enterpriseUser->_id ?? $enterpriseUser->id) : (string)Auth::id());

        $invoice = Invoice::create([
            'contract_id'        => $contractIdStr,
            'room_id'            => (string)($contract->room_id ?? $contract->post_id ?? ''),
            'post_id'            => (string)($contract->post_id ?? ''),
            'owner_id'           => (string)$ownerId,
            'created_by'         => (string)Auth::id(),
            'staff_id'           => (string)Auth::id(),
            'tenant_id'          => (string)($contract->tenant_id ?? ''),
            'tenant_name'        => $contract->tenant_name ?? $request->input('tenant_name', 'Khách thuê'),
            'invoice_code'       => $invoiceCode,
            'billing_month'      => $now->format('m/Y'),
            'billing_period'     => $period,
            'month'              => $now->month,
            'year'               => $now->year,
            'rent_price'         => (float)$request->input('rent_price', 0),
            'electricity_old'    => (float)$request->input('electricity_old', 0),
            'electricity_new'    => (float)$request->input('electricity_new', 0),
            'electricity_amount' => (float)$request->input('electricity_amount', 0),
            'water_old'          => (float)$request->input('water_old', 0),
            'water_new'          => (float)$request->input('water_new', 0),
            'water_amount'       => (float)$request->input('water_amount', 0),
            'service_amount'     => (float)$request->input('service_amount', 0),
            'total_amount'       => (float)$request->input('total_amount', 0),
            'deposit'            => (float)$request->input('deposit', 0),
            'status'             => $request->input('payment_status', 'unpaid'),
            'payment_status'     => $request->input('payment_status', 'unpaid'),
            'payment_date'       => $request->input('payment_status') === 'paid' ? $now->toDateTimeString() : null,
            'note'               => $request->input('note', ''),
        ]);

        if ($request->input('payment_status') === 'paid') {
            $contract->payment_status = 'paid';
            $contract->last_paid_period = $now->format('m/Y');
            $contract->last_paid_date = $now->toDateTimeString();
            $contract->save();
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Đã tạo và phát hành hóa đơn thành công!',
            'invoice_code' => $invoiceCode,
            'invoice'      => $invoice,
        ]);
    }

    /**
     * Cập nhật trạng thái thu tiền (Đã thu tiền / Chưa thu tiền) trực tiếp từ bên ngoài danh sách phòng
     */
    public function updatePaymentStatus(Request $request)
    {
        $contractId = $request->input('contract_id');
        $status = $request->input('status', 'paid'); // 'paid' hoặc 'unpaid'

        $contract = Contract::find($contractId);
        if (!$contract) {
            $contract = Contract::where('contract_code', $contractId)->first();
        }

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu hợp đồng!',
            ], 404);
        }

        $now = Carbon::now();
        $currentMonthStr = $now->format('m/Y');
        $contractIdStr = (string)($contract->_id ?? $contract->id);

        // KHÓA AN TOÀN: Nếu khách đã chuyển khoản và đang ở trạng thái pending_approval -> Không cho phép thu tiền mặt
        if ($status === 'paid') {
            $hasPendingApproval = Invoice::where(function ($q) use ($contractIdStr, $contract) {
                    $q->where('contract_id', $contractIdStr);
                    if (!empty($contract->contract_code)) {
                        $q->orWhere('contract_id', $contract->contract_code)
                          ->orWhere('contract_code', $contract->contract_code);
                    }
                })
                ->where(function ($q) {
                    $q->where('status', 'pending_approval')
                      ->orWhere('payment_status', 'pending_approval');
                })
                ->exists();

            if ($hasPendingApproval) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hóa đơn này khách đã chuyển khoản và đang chờ Kế toán duyệt. Không thể thao tác thu tiền mặt!'
                ], 400);
            }
        }

        // 1. Cập nhật trạng thái trên hợp đồng
        $contract->payment_status = $status;
        if ($status === 'paid') {
            $contract->last_paid_period = $currentMonthStr;
            $contract->last_paid_date = $now->toDateTimeString();
            // Nếu hợp đồng đang chờ đóng cọc/kỳ đầu -> Tự động kích hoạt (Active) hợp đồng chính thức
            if ($contract->status === 'pending_deposit' || $contract->status === 'pending_payment' || $contract->status === 'pending') {
                $contract->status = 'active';
            }
        } else {
            if ($contract->last_paid_period === $currentMonthStr) {
                $contract->last_paid_period = null;
            }
        }
        $contract->save();

        // 2. Cập nhật trạng thái hóa đơn của kỳ hiện tại (nếu có)
        $invoices = Invoice::where(function ($q) use ($contractIdStr, $contract) {
                $q->where('contract_id', $contractIdStr);
                if (!empty($contract->contract_code)) {
                    $q->orWhere('contract_id', $contract->contract_code)
                      ->orWhere('contract_code', $contract->contract_code);
                }
            })
            ->where(function ($q) use ($now, $currentMonthStr) {
                $q->where('billing_month', $currentMonthStr)
                  ->orWhere('month', $now->month)
                  ->orWhere('billing_period', 'like', "%{$currentMonthStr}%")
                  ->orWhereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            })
            ->get();

        foreach ($invoices as $inv) {
            $inv->status = $status;
            $inv->payment_status = $status;
            $inv->payment_date = ($status === 'paid') ? $now->toDateTimeString() : null;
            if ($status === 'paid') {
                $method = $request->input('payment_method', 'cash');
                $inv->payment_method = $method;
                if ($method === 'cash') {
                    $inv->collected_by = (string)Auth::id();
                    $inv->is_remitted_to_admin = false;
                } else {
                    $inv->is_remitted_to_admin = true;
                }
            } else {
                $inv->payment_method = null;
                $inv->collected_by = null;
                $inv->is_remitted_to_admin = false;
            }
            $inv->save();
        }

        return response()->json([
            'success'         => true,
            'message'         => ($status === 'paid') ? 'Đã đánh dấu ĐÃ THU TIỀN thành công! Hợp đồng đã được kích hoạt chính thức.' : 'Đã chuyển thành CHƯA THU TIỀN!',
            'status'          => $status,
            'status_label'    => ($status === 'paid') ? 'Đã thu tiền' : 'Chưa thu tiền',
            'contract_status' => $contract->status,
            'room_code'       => $contract->contract_code,
        ]);
    }

    /**
     * Hiển thị trang Chi tiết Hóa đơn chuyên nghiệp (Đặc biệt cho Hóa đơn #1 thanh toán nhận nhà)
     */
    public function showInvoiceDetail($code)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem chi tiết hóa đơn.');
        }

        $cleanCode = trim($code);
        $cleanHyphen = str_replace(' ', '-', $cleanCode);

        // 1. Tìm trực tiếp theo invoice_code hoặc _id/id
        $invoice = Invoice::where('invoice_code', $cleanCode)
            ->orWhere('invoice_code', $cleanHyphen)
            ->orWhere('_id', $cleanCode)
            ->orWhere('id', $cleanCode)
            ->first();

        // 2. Nếu chưa thấy, tìm theo mã hợp đồng (contract_code) hoặc contract_id / room_id
        if (!$invoice) {
            $contract = Contract::where('contract_code', $cleanCode)
                ->orWhere('contract_code', $cleanHyphen)
                ->orWhere('_id', $cleanCode)
                ->orWhere('id', $cleanCode)
                ->orWhere('post_id', $cleanCode)
                ->orWhere('room_id', $cleanCode)
                ->first();

            if ($contract) {
                $cId = (string)($contract->_id ?? $contract->id);
                $invoice = Invoice::where('contract_id', $cId)
                    ->orWhere('contract_id', $contract->contract_code)
                    ->latest()
                    ->first();

                // Nếu hợp đồng chưa có bất kỳ hóa đơn nào trong DB -> Tự động sinh Hóa đơn #1
                if (!$invoice) {
                    $firstInvoiceCode = 'INV-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -4));
                    $startDate = !empty($contract->start_date) ? \Carbon\Carbon::parse($contract->start_date) : now();
                    $startDay = (int)$startDate->day;
                    $rawRentPrice = (float)($contract->rent_price ?? 0);
                    $rawDeposit = (float)($contract->deposit ?? 0);
                    $rawMonthlyService = (float)($contract->internet_price ?? 0) + (float)($contract->service_fee ?? ($contract->garbage_price ?? 0)) + (float)($contract->parking_fee ?? 0);

                    if ($startDay === 1) {
                        $rentPriceFirstMonth = $rawRentPrice;
                        $serviceFirstMonth = $rawMonthlyService;
                        $periodDescription = 'Kỳ đầu tiên (Trọn tháng ' . $startDate->format('m/Y') . ')';
                        $noteDescription = 'Hóa đơn thanh toán lần đầu: Tiền cọc & Trọn tháng ' . $startDate->format('m/Y') . ' tiền phòng khi nhận nhà. (Điện nước ghi nhận chỉ số đầu; thành tiền = 0 VNĐ).';
                    } else {
                        $stayDays = max(1, 30 - $startDay + 1);
                        $rentPriceFirstMonth = (float)round(($rawRentPrice / 30) * $stayDays);
                        $serviceFirstMonth = (float)round(($rawMonthlyService / 30) * $stayDays);
                        $periodDescription = 'Kỳ đầu tiên (Lẻ ' . $stayDays . ' ngày: ' . $startDate->format('d/m') . ' - ' . $startDate->copy()->endOfMonth()->format('d/m/Y') . ')';
                        $noteDescription = 'Hóa đơn thanh toán lần đầu: Tiền cọc & Tiền phòng lẻ ' . $stayDays . ' ngày của tháng ' . $startDate->format('m/Y') . '. (Điện nước ghi nhận chỉ số đầu; thành tiền = 0 VNĐ).';
                    }

                    $invoice = Invoice::create([
                        'contract_id'        => $cId,
                        'room_id'            => (string)($contract->room_id ?? $contract->post_id ?? ''),
                        'post_id'            => (string)($contract->post_id ?? ''),
                        'owner_id'           => (string)($contract->owner_id ?? ''),
                        'created_by'         => (string)Auth::id(),
                        'staff_id'           => (string)Auth::id(),
                        'tenant_id'          => $contract->tenant_id,
                        'tenant_name'        => $contract->tenant_name,
                        'invoice_code'       => $firstInvoiceCode,
                        'billing_month'      => $startDate->format('m/Y'),
                        'billing_period'     => $periodDescription,
                        'month'              => (int)$startDate->format('m'),
                        'year'               => (int)$startDate->format('Y'),
                        'rent_price'         => $rentPriceFirstMonth,
                        'deposit'            => $rawDeposit,
                        'electricity_amount' => 0,
                        'electricity_old'    => (float)($contract->electricity_start ?? 0),
                        'electricity_new'    => (float)($contract->electricity_start ?? 0),
                        'water_amount'       => 0,
                        'water_old'          => (float)($contract->water_start ?? 0),
                        'water_new'          => (float)($contract->water_start ?? 0),
                        'service_amount'     => $serviceFirstMonth,
                        'total_amount'       => $rawDeposit + $rentPriceFirstMonth + $serviceFirstMonth,
                        'status'             => 'unpaid',
                        'payment_status'     => 'unpaid',
                        'note'               => $noteDescription,
                    ]);
                }
            }
        }

        if (!$invoice) {
            return redirect()->route('qlvan_hanh.room_list')->with('error', 'Không tìm thấy hóa đơn tương ứng với ' . $code);
        }

        $contract = $invoice->contract;
        if (!$contract && !empty($invoice->contract_id)) {
            $contract = Contract::find($invoice->contract_id) ?: Contract::where('contract_code', $invoice->contract_id)->first();
        }

        $post = $invoice->post;
        if (!$post && !empty($invoice->post_id)) {
            $post = Post::find($invoice->post_id);
        }
        if (!$post && $contract && !empty($contract->post_id)) {
            $post = Post::find($contract->post_id);
        }

        // Lấy thông tin tài khoản ngân hàng của Công ty / Doanh nghiệp RentHome để tạo mã VietQR
        $enterpriseUser = \App\Models\User::getEnterpriseUser();
        $bankUser = ($enterpriseUser && !empty($enterpriseUser->bank_account)) 
            ? $enterpriseUser 
            : ((!empty($contract->owner_id) ? \App\Models\User::find($contract->owner_id) : null) ?: Auth::user());
        if (!$bankUser || empty($bankUser->bank_account)) {
            $fallbackBankUser = \App\Models\User::whereNotNull('bank_account')->where('bank_account', '!=', '')->first();
            if ($fallbackBankUser) $bankUser = $fallbackBankUser;
        }

        $bankName = !empty($bankUser->bank_name) ? $bankUser->bank_name : 'VietBank';
        $bankAccount = !empty($bankUser->bank_account) ? $bankUser->bank_account : '00939399884747';
        $rawHolder = (!empty($bankUser->bank_owner)) 
            ? $bankUser->bank_owner 
            : (!empty($bankUser->bank_account_holder) 
                ? $bankUser->bank_account_holder 
                : (!empty($bankUser->account_name) 
                    ? $bankUser->account_name 
                    : (!empty($bankUser->name) 
                        ? $bankUser->name 
                        : 'NGUYEN THI VAN ANH')));
        $bankHolder = mb_strtoupper($rawHolder, 'UTF-8');

        $memo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $invoice->invoice_code));
        $totalAmount = (float)($invoice->total_amount ?? 0);
        $qrUrl = "https://img.vietqr.io/image/{$bankName}-{$bankAccount}-compact2.png?amount={$totalAmount}&addInfo=" . urlencode($memo) . "&accountName=" . urlencode($bankHolder);

        // Kiểm tra xem đây có phải là Hóa đơn #1 (Kỳ đầu tiên - Cọc & Nhận nhà) hay không
        $isFirstInvoice = (float)($invoice->deposit ?? 0) > 0 || str_contains($invoice->billing_period ?? '', 'Kỳ đầu') || str_contains($invoice->note ?? '', 'lần đầu');

        return view('qlvan_hanh.chi_tiet_hoa_don', compact(
            'invoice',
            'contract',
            'post',
            'bankName',
            'bankAccount',
            'bankHolder',
            'memo',
            'qrUrl',
            'isFirstInvoice'
        ));
    }
}
