<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use App\Models\RefundRequest;
use App\Models\AdminActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractLiquidationController extends Controller
{
    /**
     * Hiển thị trang Quản lý Thanh lý & Chốt công nợ Hợp đồng
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('login'));
        }

        $userId = (string)($user->_id ?? $user->id);
        $query = Contract::with(['post', 'tenant', 'owner']);

        // Phân quyền theo nhân sự / doanh nghiệp nếu là tài khoản con
        if ($user->isSubAccount() || $user->isNhansu() || $user->isStaff() || in_array($user->role ?? '', ['nhansu', 'staff', 'sub_account'])) {
            $assignedBuildings = (array) ($user->assigned_buildings ?? []);
            if (!empty($assignedBuildings) && !in_array('all', $assignedBuildings, true) && !in_array('*', $assignedBuildings, true)) {
                $assignedIds = array_map('strval', $assignedBuildings);
                $postIds = Post::whereIn('building_id', $assignedIds)->pluck('_id')->map(fn($id) => (string)$id)->toArray();
                $query->where(function ($q) use ($postIds, $assignedIds, $userId) {
                    $q->whereIn('post_id', $postIds)
                      ->orWhereIn('room_id', $postIds)
                      ->orWhereIn('building_id', $assignedIds)
                      ->orWhere('owner_id', $userId)
                      ->orWhere('created_by', $userId);
                });
            }
        } elseif (!$user->isAdmin() && !$user->isEnterprise()) {
            // Nếu là khách thuê thông thường, chỉ xem hợp đồng của chính mình
            $query->where(function ($q) use ($userId, $user) {
                $q->where('tenant_id', $userId)
                  ->orWhere('tenant_account', $user->username)
                  ->orWhere('tenant_email', $user->email);
            });
        }

        // Lọc theo trạng thái
        $status = $request->input('status', 'all');
        if ($status === 'active') {
            $query->whereIn('status', ['active', 'signed']);
        } elseif ($status === 'pending_liquidation') {
            $query->where('status', 'pending_liquidation');
        } elseif ($status === 'pending_refund') {
            $query->where('status', 'pending_refund');
        } elseif ($status === 'terminated') {
            $query->where('status', 'terminated');
        }

        // Tìm kiếm theo mã hợp đồng, tên khách, số điện thoại
        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('contract_code', 'like', "%{$s}%")
                  ->orWhere('tenant_name', 'like', "%{$s}%")
                  ->orWhere('tenant_phone', 'like', "%{$s}%");
            });
        }

        $contracts = $query->orderBy('updated_at', 'desc')->get();

        // Thống kê nhanh theo phạm vi tài khoản hiện tại
        $baseQuery = Contract::query();
        if ($user->isSubAccount() || $user->isNhansu() || $user->isStaff() || in_array($user->role ?? '', ['nhansu', 'staff', 'sub_account'])) {
            $assignedBuildings = (array) ($user->assigned_buildings ?? []);
            if (!empty($assignedBuildings) && !in_array('all', $assignedBuildings, true) && !in_array('*', $assignedBuildings, true)) {
                $assignedIds = array_map('strval', $assignedBuildings);
                $postIds = Post::whereIn('building_id', $assignedIds)->pluck('_id')->map(fn($id) => (string)$id)->toArray();
                $baseQuery->where(function ($q) use ($postIds, $assignedIds, $userId) {
                    $q->whereIn('post_id', $postIds)
                      ->orWhereIn('room_id', $postIds)
                      ->orWhereIn('building_id', $assignedIds)
                      ->orWhere('owner_id', $userId)
                      ->orWhere('created_by', $userId);
                });
            }
        } elseif (!$user->isAdmin() && !$user->isEnterprise()) {
            $baseQuery->where(function ($q) use ($userId, $user) {
                $q->where('tenant_id', $userId)
                  ->orWhere('tenant_account', $user->username)
                  ->orWhere('tenant_email', $user->email);
            });
        }

        $stats = [
            'total'               => (clone $baseQuery)->count(),
            'active'              => (clone $baseQuery)->whereIn('status', ['active', 'signed'])->count(),
            'pending_liquidation' => (clone $baseQuery)->where('status', 'pending_liquidation')->count(),
            'pending_refund'      => (clone $baseQuery)->where('status', 'pending_refund')->count(),
            'terminated'          => (clone $baseQuery)->where('status', 'terminated')->count(),
        ];

        // Lấy contract_id cụ thể nếu được truyền qua query params để tự mở modal
        $selectedContractId = $request->input('contract_id', '');
        $selectedRoom = trim($request->input('room', ''));
        if ($selectedRoom && !$selectedContractId) {
            $matchedContract = Contract::where('room_id', $selectedRoom)
                ->orWhere('contract_code', $selectedRoom)
                ->orWhere('post_id', $selectedRoom)
                ->first();
            if ($matchedContract) {
                $selectedContractId = (string)($matchedContract->_id ?? $matchedContract->id);
            }
        }

        return view('qlvan_hanh.thanh_ly_cong_no', compact('contracts', 'stats', 'selectedContractId'));
    }

    /**
     * BƯỚC 1: Khách thuê gửi Yêu cầu trả phòng
     * Route: POST /api/contracts/{id}/request-checkout
     */
    public function requestCheckout(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện thao tác này!'], 401);
        }

        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng thuê!'], 404);
        }

        $userId = (string)($user->_id ?? $user->id);
        // Kiểm tra quyền: phải là khách thuê của HĐ hoặc nhân sự/admin
        $isTenant = ($contract->tenant_id === $userId || $contract->tenant_account === $user->username || $contract->tenant_email === $user->email);
        $isStaffOrAdmin = ($user->isAdmin() || $user->isEnterprise() || $user->isSubAccount());

        if (!$isTenant && !$isStaffOrAdmin) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền gửi yêu cầu trả phòng cho hợp đồng này!'], 403);
        }

        $request->validate([
            'expected_checkout_date' => 'required|date',
            'reason'                 => 'nullable|string|max:500',
            'bank_account'           => 'nullable|string|max:50',
            'bank_name'              => 'nullable|string|max:100',
            'bank_holder'            => 'nullable|string|max:100',
        ], [
            'expected_checkout_date.required' => 'Vui lòng chọn ngày dự kiến trả phòng!',
            'expected_checkout_date.date'     => 'Ngày dự kiến trả phòng không hợp lệ!',
        ]);

        $expectedDate = $request->input('expected_checkout_date');
        $reason = $request->input('reason') ?: 'Khách thuê yêu cầu thanh lý hợp đồng & trả phòng.';

        $checkoutRequestData = [
            'requested_at'           => now(),
            'expected_checkout_date' => $expectedDate,
            'reason'                 => $reason,
            'bank_account'           => $request->input('bank_account', ''),
            'bank_name'              => $request->input('bank_name', ''),
            'bank_holder'            => $request->input('bank_holder', $contract->tenant_name),
            'status'                 => 'pending',
            'requested_by'           => $userId,
        ];

        $contract->checkout_request = $checkoutRequestData;
        $contract->save();

        // Ẩn tất cả các yêu cầu liên hệ của tài khoản yêu cầu trả phòng / hủy hợp đồng này
        \App\Http\Controllers\ContactController::hideContactsForContract($contract);

        // Gửi Notification đến Quản lý / Nhân sự vận hành và Ban Quản Trị Admin
        $post = Post::find($contract->post_id ?? $contract->room_id);
        $roomTitle = $post ? $post->title : ($contract->room_id ? 'Phòng ' . $contract->room_id : 'Phòng trọ');

        // Gửi cho Owner / Enterprise
        if (!empty($contract->owner_id)) {
            Notification::create([
                'user_id'    => (string)$contract->owner_id,
                'role'       => 'enterprise',
                'title'      => 'Yêu cầu trả phòng mới: ' . $contract->contract_code,
                'message'    => "Khách thuê {$contract->tenant_name} ({$roomTitle}) đã gửi yêu cầu trả phòng vào ngày {$expectedDate}. Lý do: {$reason}.",
                'type'       => 'warning',
                'is_read'    => false,
                'category'   => 'contract_liquidation',
                'action_url' => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . ($contract->_id ?? $contract->id)),
                'created_at' => now(),
            ]);
        }

        // Gửi cho Admin
        $admins = User::where('role', 'admin')->get(['_id', 'id']);
        foreach ($admins as $adm) {
            Notification::create([
                'user_id'    => (string)($adm->_id ?? $adm->id),
                'role'       => 'admin',
                'title'      => 'Yêu cầu trả phòng: ' . $contract->contract_code,
                'message'    => "Khách hàng {$contract->tenant_name} gửi yêu cầu trả phòng {$roomTitle} ngày {$expectedDate}.",
                'type'       => 'warning',
                'is_read'    => false,
                'category'   => 'contract_liquidation',
                'action_url' => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . ($contract->_id ?? $contract->id)),
                'created_at' => now(),
            ]);
        }

        // Gửi cho Nhân sự vận hành (Staff / Nhân sự phụ trách tòa nhà)
        $postBuildingId = $post ? (string)($post->building_id ?? '') : '';
        $staffUsers = User::where(function ($q) {
            $q->whereIn('role', ['nhansu', 'nhan su', 'nhan_su', 'sub_account', 'staff'])
              ->orWhere('staff_role', 'like', '%nhân sự%')
              ->orWhere('staff_role', 'like', '%nhan su%');
        })->get();

        foreach ($staffUsers as $staff) {
            $staffId = (string)($staff->_id ?? $staff->id);
            // Kiểm tra phân quyền tòa nhà hoặc quyền quản lý chung
            if ($staff->canManageBuilding($postBuildingId)) {
                Notification::create([
                    'user_id'    => $staffId,
                    'role'       => 'staff',
                    'title'      => 'Yêu cầu trả phòng: ' . $contract->contract_code,
                    'message'    => "Khách thuê {$contract->tenant_name} ({$roomTitle}) đã gửi yêu cầu trả phòng vào ngày {$expectedDate}. Vui lòng mở Phiếu Quyết Toán để chốt công nợ.",
                    'type'       => 'warning',
                    'is_read'    => false,
                    'category'   => 'contract_liquidation',
                    'action_url' => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . ($contract->_id ?? $contract->id)),
                    'created_at' => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Gửi yêu cầu trả phòng thành công! Nhân viên vận hành sẽ liên hệ kiểm tra tài sản phòng và thực hiện chốt công nợ.',
            'data'    => $checkoutRequestData,
        ]);
    }

    /**
     * BƯỚC 2 (API): Lấy thông tin Hợp đồng để đổ vào Form Phiếu Quyết Toán (Modal)
     * Route: GET /api/contracts/{id}/liquidation-data
     */
    public function getLiquidationData($id)
    {
        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu hợp đồng!'], 404);
        }

        $post = Post::find($contract->post_id ?? $contract->room_id);
        $room = Room::where('_id', $contract->room_id)->orWhere('id', $contract->room_id)->first();

        // Lấy hóa đơn gần đây nhất để lấy chỉ số điện nước kỳ trước
        $contractIdStr = (string)($contract->_id ?? $contract->id);
        $lastInvoice = Invoice::where('contract_id', $contractIdStr)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->first();

        // Chỉ số điện cũ
        $elecOld = $lastInvoice ? (float)($lastInvoice->electricity_new ?? $contract->electricity_start) : (float)($contract->electricity_start ?? 0);
        // Chỉ số nước cũ
        $waterOld = $lastInvoice ? (float)($lastInvoice->water_new ?? $contract->water_start) : (float)($contract->water_start ?? 0);

        // Số tiền cọc: chuẩn hóa an toàn từ deposit hoặc deposit_amount
        $depositAmount = $contract->deposit_safe;

        // Ngày trả phòng mặc định
        $checkoutReq = $contract->checkout_request ?? [];
        $defaultCheckoutDate = !empty($checkoutReq['expected_checkout_date']) 
            ? Carbon::parse($checkoutReq['expected_checkout_date'])->format('Y-m-d')
            : date('Y-m-d');

        $data = [
            'id'                     => (string)($contract->_id ?? $contract->id),
            'contract_code'          => $contract->contract_code,
            'room_id'                => $contract->room_id ?? ($contract->post_id ?? ''),
            'room_name'              => $post ? $post->title : ($room ? 'Phòng ' . $room->room_number : 'Phòng trọ RentHome'),
            'room_address'           => $post ? implode(', ', array_filter([$post->address, $post->ward, $post->district, $post->province])) : '---',
            'tenant_name'            => $contract->tenant_name,
            'tenant_phone'           => $contract->tenant_phone,
            'tenant_email'           => $contract->tenant_email,
            'tenant_account'         => $contract->tenant_account,
            'start_date'             => $contract->start_date,
            'end_date'               => $contract->end_date,
            'default_checkout_date'  => $defaultCheckoutDate,
            'rent_price'             => (float)($contract->rent_price ?? 0),
            'deposit_amount'         => $depositAmount, // Tiền cọc đối trọng từ contracts
            'electricity_price'      => (float)($contract->electricity_price ?? 3500),
            'electricity_old'        => $elecOld,
            'water_calculation_type' => $contract->water_calculation_type ?? 'khoi',
            'water_price_block'      => (float)($contract->water_price_block ?? 25000),
            'water_price_person'     => (float)($contract->water_price_person ?? 100000),
            'water_people_count'     => (int)($contract->water_people_count ?? 1),
            'water_old'              => $waterOld,
            'internet_price'         => (float)($contract->internet_price ?? 0),
            'garbage_price'          => (float)($contract->garbage_price ?? ($contract->service_fee ?? 0)),
            'parking_fee'            => (float)($contract->parking_fee ?? 0),
            'payment_cycle'          => (int)($contract->payment_cycle ?: 1),
            'next_rent_date'         => !empty($contract->next_rent_date) ? Carbon::parse($contract->next_rent_date)->format('Y-m-d') : null,
            'status'                 => $contract->status,
            'checkout_request'       => $checkoutReq,
            'liquidation_details'    => $contract->liquidation_details ?? null,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * BƯỚC 2: Tính toán Realtime Phiếu Quyết Toán qua AJAX
     * Route: POST /api/contracts/{id}/calculate-liquidation
     */
    public function calculateRealtime(Request $request, $id)
    {
        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        $calc = $this->computeSettlementData($contract, $request->all());

        return response()->json([
            'success' => true,
            'data'    => $calc,
        ]);
    }

    /**
     * BƯỚC 3: Xử lý Logic Rẽ nhánh (Backend Controller)
     * Khi Nhân sự submit Form Phiếu Quyết Toán
     * Route: POST /api/contracts/{id}/settle-liquidation
     */
    public function settleLiquidation(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        $request->validate([
            'checkout_date'   => 'required|date',
            'electricity_new' => 'required|numeric',
            'water_new'       => 'nullable|numeric',
            'damage_fee'      => 'nullable|numeric|min:0',
            'damage_reason'   => 'nullable|string|max:1000',
        ], [
            'checkout_date.required'   => 'Vui lòng chọn ngày trả phòng thực tế!',
            'electricity_new.required' => 'Vui lòng nhập chỉ số điện chốt cuối!',
        ]);

        DB::beginTransaction();
        try {
            // Tính toán toàn bộ công nợ
            $calc = $this->computeSettlementData($contract, $request->all());

            $totalDebt     = $calc['total_debt'];     // Tổng các khoản khách phải trả
            $depositAmount = $calc['deposit_amount']; // Tiền cọc đối trọng từ contracts
            $netAmount     = $calc['net_amount'];     // Tổng nợ - Tiền cọc
            $scenario      = $calc['scenario'];       // 'A' (Khách nợ thêm) hoặc 'B' (Hoàn cọc) hoặc 'C' (Hòa)

            $checkoutDate = $request->input('checkout_date');
            $damageFee    = (float)$request->input('damage_fee', 0);
            $damageReason = $request->input('damage_reason', '');
            $note         = $request->input('note', '');

            $post = Post::find($contract->post_id ?? $contract->room_id);
            $roomName = $post ? $post->title : ('Phòng ' . ($contract->room_id ?? 'RentHome'));

            // Lưu chi tiết quyết toán vào contract
            $liquidationDetails = [
                'checkout_date'          => $checkoutDate,
                'stay_days'              => $calc['stay_days'],
                'is_prorated'            => $calc['is_prorated'],
                'prorated_rent'          => $calc['prorated_rent'],
                'electricity_old'        => $calc['electricity_old'],
                'electricity_new'        => $calc['electricity_new'],
                'electricity_usage'      => $calc['electricity_usage'],
                'electricity_amount'     => $calc['electricity_amount'],
                'water_old'              => $calc['water_old'],
                'water_new'              => $calc['water_new'],
                'water_usage'            => $calc['water_usage'],
                'water_amount'           => $calc['water_amount'],
                'fixed_services_amount'  => $calc['fixed_services_amount'],
                'damage_fee'             => $damageFee,
                'damage_reason'          => $damageReason,
                'total_debt'             => $totalDebt,
                'deposit_amount'         => $depositAmount,
                'net_amount'             => $netAmount,
                'scenario'               => $scenario,
                'note'                   => $note,
                'settled_at'             => now(),
                'settled_by'             => (string)($user->_id ?? $user->id),
                'settled_by_name'        => $user->account_name ?: ($user->username ?: 'Nhân sự vận hành'),
            ];

            $contract->liquidation_details = $liquidationDetails;
            $contract->liquidation_scenario = $scenario;

            // =========================================================================
            // KỊCH BẢN A: Khách nợ NHIỀU HƠN Cọc (net_amount > 0)
            // =========================================================================
            if ($scenario === 'A') {
                $contract->status = 'pending_liquidation';

                // Tạo Hóa đơn cuối cùng (Final Invoice) vào collection 'invoices' (status: unpaid)
                $invoiceCode = 'INV-LIQ-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -4));
                $billingMonth = Carbon::parse($checkoutDate)->format('m/Y') . ' (Thanh lý)';

                $invoiceNote = "Hóa đơn chốt công nợ thanh lý hợp đồng {$contract->contract_code}. ";
                $invoiceNote .= "Tổng nợ phát sinh: " . number_format($totalDebt) . "đ (Tiền phòng lẻ: " . number_format($calc['prorated_rent']) . "đ, Điện nước: " . number_format($calc['electricity_amount'] + $calc['water_amount']) . "đ, Bồi thường tài sản: " . number_format($damageFee) . "đ). ";
                $invoiceNote .= "Khấu trừ toàn bộ tiền cọc: -" . number_format($depositAmount) . "đ. ";
                if (!empty($calc['unused_rent_refund']) && $calc['unused_rent_refund'] > 0) {
                    $invoiceNote .= "Khấu trừ hoàn trả tiền phòng đóng trước chưa ở: -" . number_format($calc['unused_rent_refund']) . "đ. ";
                }
                $invoiceNote .= "Số tiền khách cần thanh toán thêm: " . number_format($netAmount) . " VNĐ.";
                if ($damageReason) {
                    $invoiceNote .= " Ghi chú bồi thường: {$damageReason}.";
                }

                $finalInvoice = Invoice::create([
                    'contract_id'        => (string)($contract->_id ?? $contract->id),
                    'room_id'            => (string)($contract->room_id ?? $contract->post_id),
                    'post_id'            => (string)($contract->post_id ?? $contract->room_id),
                    'owner_id'           => (string)($contract->owner_id ?? $user->id),
                    'created_by'         => (string)($user->_id ?? $user->id),
                    'staff_id'           => (string)($user->_id ?? $user->id),
                    'tenant_id'          => $contract->tenant_id,
                    'tenant_name'        => $contract->tenant_name,
                    'invoice_code'       => $invoiceCode,
                    'billing_month'      => $billingMonth,
                    'billing_period'     => 'Quyết toán thanh lý ngày ' . Carbon::parse($checkoutDate)->format('d/m/Y'),
                    'month'              => (int)Carbon::parse($checkoutDate)->format('m'),
                    'year'               => (int)Carbon::parse($checkoutDate)->format('Y'),
                    'rent_price'         => $calc['prorated_rent'],
                    'deposit'            => -$depositAmount, // Âm để thể hiện khấu trừ cọc
                    'deposit_deducted'   => $depositAmount,
                    'electricity_amount' => $calc['electricity_amount'],
                    'electricity_old'    => $calc['electricity_old'],
                    'electricity_new'    => $calc['electricity_new'],
                    'water_amount'       => $calc['water_amount'],
                    'water_old'          => $calc['water_old'],
                    'water_new'          => $calc['water_new'],
                    'service_amount'     => $calc['fixed_services_amount'],
                    'damage_fee'         => $damageFee,
                    'total_amount'       => $netAmount, // Số tiền khách nợ THÊM
                    'status'             => 'unpaid',
                    'payment_status'     => 'unpaid',
                    'type'               => 'final_settlement',
                    'invoice_type'       => 'liquidation',
                    'note'               => $invoiceNote,
                ]);

                $contract->final_invoice_id = (string)($finalInvoice->_id ?? $finalInvoice->id);
                $contract->save();

                // Gửi Notification cho khách thuê
                if (!empty($contract->tenant_id)) {
                    Notification::create([
                        'user_id'    => (string)$contract->tenant_id,
                        'title'      => 'Phiếu quyết toán thanh lý Hợp đồng: Cần thanh toán bổ sung',
                        'message'    => "Hợp đồng {$contract->contract_code} đã được chốt công nợ. Sau khi cấn trừ tiền cọc, bạn cần thanh toán thêm " . number_format($netAmount) . " VNĐ để hoàn tất thủ tục bàn giao.",
                        'type'       => 'error',
                        'is_read'    => false,
                        'category'   => 'contract_liquidation',
                        'action_url' => url('/qlvan_hanh/hoa_don/' . $invoiceCode),
                        'created_at' => now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success'      => true,
                    'scenario'     => 'A',
                    'net_amount'   => $netAmount,
                    'formatted'    => number_format($netAmount) . ' VNĐ',
                    'invoice_code' => $invoiceCode,
                    'invoice_id'   => (string)($finalInvoice->_id ?? $finalInvoice->id),
                    'invoice_url'  => url('/qlvan_hanh/hoa_don/' . $invoiceCode),
                    'message'      => 'Khách cần thanh toán thêm: ' . number_format($netAmount) . ' VNĐ. Đã tạo hóa đơn thu tiền.',
                ]);

            // =========================================================================
            // KỊCH BẢN B: Cọc NHIỀU HƠN Nợ (net_amount < 0 -> hoàn trả cọc)
            // =========================================================================
            } elseif ($scenario === 'B') {
                $contract->status = 'pending_refund';

                $refundAmount = abs($netAmount); // Số tiền hoàn lại cho khách

                $bankAccount = $request->input('refund_bank_account', $contract->checkout_request['bank_account'] ?? ($contract->tenant_account ?? ''));
                $bankName    = $request->input('refund_bank_name', $contract->checkout_request['bank_name'] ?? '');
                $bankHolder  = $request->input('refund_bank_holder', $contract->checkout_request['bank_holder'] ?? ($contract->tenant_name ?? ''));

                // Tạo bản ghi Yêu cầu Hoàn tiền (Refund Request) đẩy lên Kế toán / Admin
                $refundRequest = RefundRequest::create([
                    'contract_id'         => (string)($contract->_id ?? $contract->id),
                    'contract_code'       => $contract->contract_code,
                    'room_id'             => (string)($contract->room_id ?? $contract->post_id),
                    'post_id'             => (string)($contract->post_id ?? $contract->room_id),
                    'owner_id'            => (string)($contract->owner_id ?? $user->id),
                    'tenant_id'           => $contract->tenant_id,
                    'tenant_name'         => $contract->tenant_name,
                    'tenant_phone'        => $contract->tenant_phone,
                    'tenant_bank_account' => $bankAccount,
                    'tenant_bank_name'    => $bankName,
                    'tenant_bank_holder'  => $bankHolder,
                    'total_debt'          => $totalDebt,
                    'deposit'             => $depositAmount,
                    'unused_rent_refund'  => $calc['unused_rent_refund'] ?? 0,
                    'refund_amount'       => $refundAmount,
                    'damage_fee'          => $damageFee,
                    'damage_reason'       => $damageReason,
                    'liquidation_note'    => $note,
                    'status'              => 'pending', // Chờ kế toán duyệt & chuyển khoản
                    'created_by'          => (string)($user->_id ?? $user->id),
                ]);

                $contract->refund_request_id = (string)($refundRequest->_id ?? $refundRequest->id);
                $contract->save();

                // Gửi Notification cho Kế toán & Ban Quản Trị
                $financeStaff = User::whereIn('role', ['admin', 'ketoan', 'finance', 'doanhnghiep'])->get(['_id', 'id']);
                foreach ($financeStaff as $fs) {
                    Notification::create([
                        'user_id'    => (string)($fs->_id ?? $fs->id),
                        'role'       => 'finance',
                        'title'      => 'Yêu cầu hoàn trả cọc thanh lý: ' . $contract->contract_code,
                        'message'    => "Hợp đồng {$contract->contract_code} ({$contract->tenant_name}) đã chốt thanh lý. Cần hoàn trả khách: " . number_format($refundAmount) . " VNĐ. Đang chờ Kế toán xác nhận chuyển khoản.",
                        'type'       => 'warning',
                        'is_read'    => false,
                        'category'   => 'contract_refund',
                        'action_url' => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . ($contract->_id ?? $contract->id)),
                        'created_at' => now(),
                    ]);
                }

                // Gửi Notification cho Khách thuê
                if (!empty($contract->tenant_id)) {
                    Notification::create([
                        'user_id'    => (string)$contract->tenant_id,
                        'title'      => 'Phiếu quyết toán thanh lý Hợp đồng: Đang xử lý hoàn tiền cọc',
                        'message'    => "Hợp đồng {$contract->contract_code} đã được chốt quyết toán. Công ty sẽ hoàn trả lại số tiền cọc dư là " . number_format($refundAmount) . " VNĐ vào tài khoản ngân hàng của bạn trong vòng 1-3 ngày làm việc.",
                        'type'       => 'success',
                        'is_read'    => false,
                        'category'   => 'contract_liquidation',
                        'action_url' => url('/qlvan_hanh/thanh_ly_cong_no?contract_id=' . ($contract->_id ?? $contract->id)),
                        'created_at' => now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success'       => true,
                    'scenario'      => 'B',
                    'refund_amount' => $refundAmount,
                    'formatted'     => number_format($refundAmount) . ' VNĐ',
                    'refund_id'     => (string)($refundRequest->_id ?? $refundRequest->id),
                    'message'       => 'Công ty cần hoàn trả khách: ' . number_format($refundAmount) . ' VNĐ. Đã gửi yêu cầu lên Kế toán.',
                ]);

            // =========================================================================
            // KỊCH BẢN C: Cân bằng (net_amount == 0 -> Tổng nợ đúng bằng tiền cọc)
            // =========================================================================
            } else {
                $contract->save();

                // Tự động hoàn tất luôn
                $this->executeTermination($contract, $user, 'Quyết toán hòa nợ: Tiền nợ đúng bằng tiền cọc.');

                DB::commit();

                return response()->json([
                    'success'    => true,
                    'scenario'   => 'C',
                    'net_amount' => 0,
                    'formatted'  => '0 VNĐ',
                    'message'    => 'Hai bên đã đối soát cân bằng (Tổng nợ = Tiền cọc). Hợp đồng đã được thanh lý hoàn tất và phòng đã sẵn sàng cho thuê lại!',
                ]);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi chốt quyết toán thanh lý HĐ: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi xử lý quyết toán: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BƯỚC 4: Kế toán bấm "Xác nhận đã hoàn tiền" (Kịch bản B)
     * Route: POST /api/contracts/{id}/confirm-refund
     */
    public function confirmRefund(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        if ($contract->status !== 'pending_refund') {
            return response()->json(['success' => false, 'message' => 'Hợp đồng không ở trạng thái chờ hoàn tiền cọc!'], 400);
        }

        DB::beginTransaction();
        try {
            // Cập nhật bản ghi RefundRequest
            $refundRequest = RefundRequest::where('contract_id', (string)($contract->_id ?? $contract->id))->first();
            if ($refundRequest) {
                $refundRequest->status = 'completed';
                $refundRequest->refunded_at = now();
                $refundRequest->refunded_by = (string)($user->_id ?? $user->id);
                $refundRequest->payment_reference = $request->input('payment_reference', '');
                $refundRequest->save();
            }

            // Thực thi BƯỚC 4: Tự động hóa sau khi hoàn tất
            $this->executeTermination($contract, $user, 'Kế toán đã xác nhận chuyển khoản hoàn trả tiền cọc cho khách.');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận hoàn tiền cọc thành công! Hợp đồng chính thức thanh lý (terminated) và phòng đã chuyển về trạng thái sẵn sàng cho thuê (available).',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận hoàn tiền: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BƯỚC 4: Thu ngân / Kế toán xác nhận Hóa đơn cuối cùng đã thanh toán (Kịch bản A)
     * Route: POST /api/contracts/{id}/confirm-final-paid
     */
    public function confirmFinalInvoicePaid(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        $contract = Contract::where('_id', $id)->orWhere('id', $id)->first();
        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hợp đồng!'], 404);
        }

        DB::beginTransaction();
        try {
            // Đánh dấu Hóa đơn cuối cùng đã paid
            $finalInvoice = null;
            if (!empty($contract->final_invoice_id)) {
                $finalInvoice = Invoice::find($contract->final_invoice_id);
            }
            if (!$finalInvoice) {
                $finalInvoice = Invoice::where('contract_id', (string)($contract->_id ?? $contract->id))
                    ->whereIn('type', ['final_settlement', 'liquidation'])
                    ->first();
            }

            if ($finalInvoice) {
                $finalInvoice->status = 'paid';
                $finalInvoice->payment_status = 'paid';
                $finalInvoice->payment_date = now()->toDateTimeString();
                $finalInvoice->payment_method = $request->input('payment_method', 'cash');
                $finalInvoice->collected_by = (string)($user->_id ?? $user->id);
                $finalInvoice->save();
            }

            // Thực thi BƯỚC 4: Tự động hóa sau khi hoàn tất
            $this->executeTermination($contract, $user, 'Khách hàng đã thanh toán đầy đủ hóa đơn chốt công nợ thanh lý.');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận thu tiền thành công! Hợp đồng đã hoàn tất thanh lý (terminated) và phòng đã chuyển sang trạng thái trống (available).',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi xác nhận thu tiền: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * BƯỚC 4 (CORE): Hàm thực thi tự động hóa sau khi hoàn tất thanh toán / hoàn tiền
     * Cập nhật document trong contracts: status = 'terminated', terminated_at = now().
     * Cập nhật document trong rooms: status = 'available' để phòng sẵn sàng cho thuê tiếp.
     * Cập nhật document trong posts: status = 'active', is_rented = false.
     */
    public function executeTermination(Contract $contract, $actorUser = null, string $reason = '')
    {
        $actorId = $actorUser ? (string)($actorUser->_id ?? $actorUser->id) : (string)Auth::id();
        $actorName = $actorUser ? ($actorUser->account_name ?: ($actorUser->username ?: 'Hệ thống')) : 'Hệ thống';

        // 1. Cập nhật contracts: status = 'terminated', terminated_at = now()
        $contract->status = 'terminated';
        $contract->terminated_at = now();
        $contract->terminated_by = $actorId;

        // Cập nhật yêu cầu trả phòng (checkout_request) thành completed
        if (!empty($contract->checkout_request)) {
            $checkoutReq = (array)$contract->checkout_request;
            $checkoutReq['status'] = 'completed';
            $checkoutReq['completed_at'] = now()->toDateTimeString();
            $contract->checkout_request = $checkoutReq;
        }
        $contract->save();

        // 2. Cập nhật rooms & posts: status = 'available' để sẵn sàng cho thuê tiếp
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
        $roomId = !empty($targetIds) ? implode(', ', $targetIds) : ($contract->room_id ?? 'RentHome');

        // 3. Ghi log hoạt động AdminActivity
        AdminActivity::log(
            'terminate',
            'contract',
            'Hoàn tất thanh lý hợp đồng & Mở phòng trống',
            "{$actorName} đã hoàn tất thanh lý hợp đồng {$contract->contract_code} (Khách thuê: {$contract->tenant_name}). Phòng {$roomId} đã được cập nhật sang trạng thái [available] sẵn sàng cho thuê tiếp. Lý do: {$reason}",
            $contract,
            ['contract_id' => (string)($contract->_id ?? $contract->id), 'room_id' => $roomId]
        );

        // 4. Gửi thông báo hoàn tất cho khách thuê
        if (!empty($contract->tenant_id)) {
            Notification::create([
                'user_id'    => (string)$contract->tenant_id,
                'title'      => 'Hợp đồng thuê đã hoàn tất thanh lý',
                'message'    => "Hợp đồng {$contract->contract_code} đã hoàn tất thanh lý biên bản bàn giao. RentHome xin cảm ơn bạn đã đồng hành và kính chúc bạn luôn may mắn, thành công!",
                'type'       => 'success',
                'is_read'    => false,
                'category'   => 'contract_completed',
                'created_at' => now(),
            ]);
        }

        return true;
    }

    /**
     * Tiện ích: Tính toán chi tiết các khoản phát sinh tháng cuối
     */
    private function computeSettlementData(Contract $contract, array $input): array
    {
        $rentPrice = (float)($contract->rent_price ?? 0);
        $depositAmount = $contract->deposit_safe;

        // Ngày trả phòng
        $checkoutDateStr = $input['checkout_date'] ?? date('Y-m-d');
        $checkoutDate = Carbon::parse($checkoutDateStr);

        // Số ngày ở lẻ: Người dùng có thể chọn chế độ lẻ ngày hoặc cả tháng
        $isProrated = isset($input['is_prorated']) ? (bool)$input['is_prorated'] : true;
        $stayDays = isset($input['stay_days']) ? max(1, (int)$input['stay_days']) : (int)$checkoutDate->day;

        // 1. Tiền phòng tháng cuối & Tiền phòng đóng trước chưa ở (Prepaid Cycle):
        // Kiểm tra nếu ngày trả phòng diễn ra trước ngày đến hạn thu tiền tiếp theo (next_rent_date):
        // Khách đã đóng tiền trước theo chu kỳ và chưa ở hết.
        $nextRentDate = !empty($contract->next_rent_date) ? Carbon::parse($contract->next_rent_date) : null;
        $isPrepaidCycle = false;
        $unusedRentRefund = 0;
        $unusedFullMonths = 0;
        $unusedDays = 0;

        if ($nextRentDate && $checkoutDate->startOfDay()->lessThan($nextRentDate->startOfDay())) {
            $isPrepaidCycle = true;
            // Vì khách đã thanh toán tiền phòng trước đó bao gồm cả tháng trả phòng, khách KHÔNG nợ tiền phòng tháng cuối:
            $proratedRent = 0;

            // a. Số ngày thừa chưa ở của tháng trả phòng
            $checkoutDay = (int)$checkoutDate->day;
            $unusedDays = max(0, 30 - $checkoutDay);
            $unusedDaysAmount = (float)round(($rentPrice / 30) * $unusedDays);

            // b. Số tháng trọn vẹn chưa ở tiếp theo trước next_rent_date
            $startOfNextMonth = $checkoutDate->copy()->addMonth()->startOfMonth();
            $nextRentMonth = $nextRentDate->copy()->startOfMonth();
            if ($startOfNextMonth->lessThan($nextRentMonth)) {
                $unusedFullMonths = (int)$startOfNextMonth->diffInMonths($nextRentMonth);
            }
            $unusedMonthsAmount = (float)($unusedFullMonths * $rentPrice);

            $unusedRentRefund = $unusedDaysAmount + $unusedMonthsAmount;
        } else {
            // Không có tiền phòng đóng trước hoặc đã ở quá hạn: tính tiền phòng lẻ của tháng cuối
            if ($isProrated) {
                $proratedRent = (float)round(($rentPrice / 30) * $stayDays);
            } else {
                $proratedRent = $rentPrice;
                $stayDays = 30;
            }
        }

        // 2. Điện nước:
        // Đơn giá
        $elecPrice = (float)($contract->electricity_price ?? 3500);
        $elecOld   = isset($input['electricity_old']) ? (float)$input['electricity_old'] : (float)($contract->electricity_start ?? 0);
        $elecNew   = isset($input['electricity_new']) ? (float)$input['electricity_new'] : $elecOld;
        $elecUsage = max(0, $elecNew - $elecOld);
        $elecCost  = (float)round($elecUsage * $elecPrice);

        // Nước
        $waterCalcType = $contract->water_calculation_type ?? 'khoi';
        $waterOld      = isset($input['water_old']) ? (float)$input['water_old'] : (float)($contract->water_start ?? 0);
        $waterNew      = isset($input['water_new']) ? (float)$input['water_new'] : $waterOld;
        $waterUsage    = 0;
        $waterCost     = 0;

        if ($waterCalcType === 'khoi') {
            $waterPrice = (float)($contract->water_price_block ?? 25000);
            $waterUsage = max(0, $waterNew - $waterOld);
            $waterCost  = (float)round($waterUsage * $waterPrice);
        } else {
            // Theo người
            $waterPrice = (float)($contract->water_price_person ?? 100000);
            $peopleCount = (int)($contract->water_people_count ?? 1);
            $waterUsage = $peopleCount;
            $waterCost = (float)round($waterPrice * $peopleCount);
        }

        // Dịch vụ cố định tháng cuối (mạng, rác, xe...)
        $internet = (float)($contract->internet_price ?? 0);
        $garbage  = (float)($contract->garbage_price ?? ($contract->service_fee ?? 0));
        $parking  = (float)($contract->parking_fee ?? 0);
        $fixedServicesTotal = $internet + $garbage + $parking;

        // Nếu tính lẻ ngày, có thể nhân theo tỉ lệ lẻ ngày
        $servicesCost = $isProrated ? (float)round(($fixedServicesTotal / 30) * $stayDays) : $fixedServicesTotal;

        // 3. Phí bồi thường hư hỏng (Damage Fee)
        $damageFee = isset($input['damage_fee']) ? max(0, (float)$input['damage_fee']) : 0;

        // TỔNG NỢ PHÁT SINH = Tiền phòng lẻ + Điện + Nước + Dịch vụ + Bồi thường
        $totalDebt = $proratedRent + $elecCost + $waterCost + $servicesCost + $damageFee;

        // TỔNG KHOẢN ĐỐI TRỪ/HOÀN LẠI = Tiền cọc + Tiền phòng đóng trước chưa ở
        $totalCredits = $depositAmount + $unusedRentRefund;

        // CHÊNH LỆCH = Tổng nợ - Tổng hoàn (Cọc + Tiền phòng trả trước)
        $netAmount = $totalDebt - $totalCredits;

        // Rẽ nhánh:
        // Kịch bản A: Tổng nợ > Tổng hoàn -> Khách phải đóng thêm
        // Kịch bản B: Tổng hoàn > Tổng nợ -> Công ty hoàn tiền cho khách
        // Kịch bản C: Tổng nợ == Tổng hoàn
        if ($netAmount > 0) {
            $scenario = 'A';
        } elseif ($netAmount < 0) {
            $scenario = 'B';
        } else {
            $scenario = 'C';
        }

        return [
            'checkout_date'         => $checkoutDate->format('Y-m-d'),
            'stay_days'             => $stayDays,
            'is_prorated'           => $isProrated,
            'rent_price'            => $rentPrice,
            'prorated_rent'         => $proratedRent,
            'electricity_price'     => $elecPrice,
            'electricity_old'       => $elecOld,
            'electricity_new'       => $elecNew,
            'electricity_usage'     => $elecUsage,
            'electricity_amount'    => $elecCost,
            'water_calc_type'       => $waterCalcType,
            'water_old'             => $waterOld,
            'water_new'             => $waterNew,
            'water_usage'           => $waterUsage,
            'water_amount'          => $waterCost,
            'fixed_services_total'  => $fixedServicesTotal,
            'fixed_services_amount' => $servicesCost,
            'damage_fee'            => $damageFee,
            'total_debt'            => $totalDebt,
            'deposit_amount'        => $depositAmount,
            'is_prepaid_cycle'      => $isPrepaidCycle,
            'unused_rent_refund'    => $unusedRentRefund,
            'unused_full_months'    => $unusedFullMonths,
            'unused_days'           => $unusedDays,
            'total_credits'         => $totalCredits,
            'net_amount'            => $netAmount,
            'refund_amount'         => ($netAmount < 0) ? abs($netAmount) : 0,
            'scenario'              => $scenario,
        ];
    }
}
