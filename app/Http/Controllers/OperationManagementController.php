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

            // LOGIC TRẠNG THÁI: Tôn trọng trạng thái chủ nhà bấm chọn trực tiếp bên ngoài
            if ($contract->payment_status === 'paid' || $hasPaidInvoice) {
                $status = 'paid'; // Đã thanh toán -> Chấm xanh
                $statusLabel = 'Đã thu tiền';
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
        $userId = Auth::id();

        // 1. Lấy danh sách hợp đồng hoạt động (ưu tiên của chủ nhà hiện tại)
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

        // Fallback nếu chủ nhà chưa có hợp đồng hoặc admin/test
        if ($contracts->isEmpty()) {
            $contracts = Contract::where('status', '!=', 'cancelled')->latest()->get();
        }

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

            // Thông tin ngân hàng của chủ nhà từ hồ sơ người dùng
            $bankUser = $owner ?: $user;
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

            $tenantNameDisplay = ($contract->tenant_name ?: 'Khách thuê') . ($contract->tenant_phone ? ' (SĐT: ' . $contract->tenant_phone . ')' : '');

            $item = [
                'contractId'    => $contractId,
                'contractCode'  => $contractCode,
                'roomName'      => $postTitle . ' (' . $contractCode . ')',
                'postTitle'     => $postTitle,
                'tenantName'    => $tenantNameDisplay,
                'tenantRawName' => $contract->tenant_name ?: 'Khách thuê',
                'tenantPhone'   => $contract->tenant_phone ?: '',
                'rent'          => (float)($contract->rent_price ?? ($post->price ?? 0)),
                'elecOld'       => $elecOld,
                'elecNew'       => $elecOld, // Khởi tạo bằng số cũ
                'elecRate'      => $elecRate,
                'waterOld'      => $waterOld,
                'waterNew'      => $waterOld, // Khởi tạo bằng số cũ
                'waterRate'     => $waterRate,
                'wifi'          => (float)($contract->internet_price ?? 0),
                'garbage'       => (float)($contract->garbage_price ?? 0),
                'parking'       => (float)($contract->parking_price ?? 0),
                'billingPeriod' => $currentMonthStr,
                'dueDate'       => $dueDate,
                'bankName'      => $bankName,
                'bankAccount'   => $bankAccount,
                'bankHolder'    => $bankHolder,
                'memo'          => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $contractCode)) . ' THANG ' . $now->format('m/Y'),
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

        $invoice = Invoice::create([
            'contract_id'        => $contractIdStr,
            'room_id'            => (string)($contract->room_id ?? $contract->post_id ?? ''),
            'post_id'            => (string)($contract->post_id ?? ''),
            'owner_id'           => (string)($contract->owner_id ?? Auth::id() ?? ''),
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
            'deposit'            => 0, // Tiền cọc chỉ thu 1 lần ở tháng đầu, các tháng sau = 0
            'status'             => $request->input('payment_status', 'unpaid'),
            'payment_status'     => $request->input('payment_status', 'unpaid'),
            'payment_date'       => $request->input('payment_status') === 'paid' ? $now->toDateTimeString() : null,
            'note'               => $request->input('note', ''),
        ]);

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

        // 1. Cập nhật trạng thái trên hợp đồng
        $contract->payment_status = $status;
        if ($status === 'paid') {
            $contract->last_paid_period = $currentMonthStr;
            $contract->last_paid_date = $now->toDateTimeString();
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
            $inv->save();
        }

        return response()->json([
            'success'      => true,
            'message'      => ($status === 'paid') ? 'Đã đánh dấu ĐÃ THU TIỀN thành công!' : 'Đã chuyển thành CHƯA THU TIỀN!',
            'status'       => $status,
            'status_label' => ($status === 'paid') ? 'Đã thu tiền' : 'Chưa thu tiền',
            'room_code'    => $contract->contract_code,
        ]);
    }
}
