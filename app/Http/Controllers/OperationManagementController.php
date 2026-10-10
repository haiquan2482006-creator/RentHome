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
        $userId = Auth::id();

        // 1. Truy xuất danh sách các hợp đồng đang active
        $contractsQuery = Contract::query();
        if ($userId) {
            $userPostIds = Post::where('user_id', (string)$userId)
                ->pluck('_id')
                ->map(fn($id) => (string)$id)
                ->toArray();

            $contractsQuery->where(function ($q) use ($userId, $userPostIds) {
                $q->where('owner_id', (string)$userId)
                  ->orWhereIn('post_id', $userPostIds);
            });
        }

        $contracts = $contractsQuery->where('status', '!=', 'cancelled')->latest()->get();

        // Fallback nếu đang test/admin hoặc hợp đồng chưa gán owner_id
        if ($contracts->isEmpty() && (!$userId || ($user && in_array($user->role ?? '', ['admin'])))) {
            $contracts = Contract::where('status', '!=', 'cancelled')->latest()->get();
        }

        $now = Carbon::now();
        $today = $now->day;
        $currentMonthStr = $now->format('m/Y'); // Ví dụ: '10/2026'
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // 2. Duyệt qua từng hợp đồng và tính toán trạng thái thanh toán ảo theo logic thời gian
        $rooms = $contracts->map(function ($contract) use ($now, $today, $currentMonth, $currentYear, $currentMonthStr) {
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

            // LOGIC THỜI GIAN:
            // TH1: Chưa đến hạn chốt của tháng này (now()->day < bill_date)
            if ($today < $billDate) {
                $status = 'paid'; // Gán thuộc tính ảo là 'paid' (Chấm xanh)
                $statusLabel = 'Chưa đến kỳ thu';
                $isDue = false;
            } 
            // TH2: Đến hạn chốt hoặc đã quá hạn (now()->day >= bill_date)
            else {
                // Kiểm tra xem tháng hiện tại đã có hóa đơn nào trong bảng invoices được thanh toán chưa
                $hasPaidInvoice = Invoice::where('contract_id', $contractId)
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

                // Fallback nếu hợp đồng đã thanh toán trực tiếp cho kỳ này
                if (!$hasPaidInvoice && ($contract->payment_status === 'paid' && ($contract->last_paid_period === $currentMonthStr))) {
                    $hasPaidInvoice = true;
                }

                if ($hasPaidInvoice) {
                    $status = 'paid'; // Đã thanh toán -> Chấm xanh
                    $statusLabel = 'Đã thu tiền';
                    $isDue = false;
                } else {
                    $status = 'unpaid'; // Chưa thanh toán -> Chấm đỏ
                    $statusLabel = 'Chưa thu tiền';
                    $isDue = true;

                    // Tự động kích hoạt luồng thông báo thời gian thực khi đến hạn
                    $this->triggerDueNotification($contract, $billDate, $currentMonthStr, $roomCode, $postTitle);
                }
            }

            $contractStart = $contract->start_date ? date('d/m/Y', strtotime($contract->start_date)) : '01/01/2026';
            $contractEnd = $contract->end_date ?: ($contract->start_date ? date('d/m/Y', strtotime($contract->start_date . ' + ' . ($contract->duration_months ?? 12) . ' months')) : '01/01/2027');

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
            ];

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
                'status'                 => $status, // Biến ảo: 'paid' hoặc 'unpaid'
                'status_label'           => $statusLabel, // 'Chưa đến kỳ thu', 'Chưa thu tiền', 'Đã thu tiền'
                'is_due'                 => $isDue, // true nếu đến hạn và chưa thu
                'debt_period'            => 'Kỳ ' . $currentMonthStr . ' (Hạn ' . $billDate . '/' . $currentMonth . ')',
                'debt_amount'            => (float)($contract->rent_price ?? 0),
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
                'contract_details'       => $contractDetails,
                'invoice_code'           => 'HD-' . $roomCode,
                'invoice_total'          => (float)($contract->rent_price ?? 0),
                'post'                   => $post,
                'image'                  => $post ? $post->display_image : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
            ];
        });

        // 3. Thống kê số lượng phòng
        $paidCount = $rooms->where('status', 'paid')->count();
        $unpaidCount = $rooms->where('status', 'unpaid')->count();
        $dueRooms = $rooms->where('status', 'unpaid')->values();

        $availableRooms = Post::where(function ($q) {
            $q->whereNull('is_deleted')->orWhere('is_deleted', false);
        })->where('status', '!=', 'đã cho thuê')->get();

        return view('qlvan_hanh.Room_list', compact('rooms', 'paidCount', 'unpaidCount', 'availableRooms', 'dueRooms'));
    }

    /**
     * API kiểm tra thông báo thời gian thực khi đến hạn đóng tiền
     */
    public function checkRealtimeDue(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $today = $now->day;
        $currentMonthStr = $now->format('m/Y');

        $contractsQuery = Contract::where('status', '!=', 'cancelled');
        if ($userId) {
            $contractsQuery->where('owner_id', (string)$userId);
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
}
