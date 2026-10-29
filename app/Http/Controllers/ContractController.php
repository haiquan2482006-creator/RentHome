<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Building;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Lưu hợp đồng thuê mới và tách biệt 2 luồng: Khách online (từ Liên hệ) vs Khách vãng lai (từ QL Vận hành)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để tạo hợp đồng.'], 401);
            }
            return redirect()->guest(route('login'))->with('error', 'Vui lòng đăng nhập để tạo hợp đồng.');
        }

        $request->validate([
            'room_id'         => 'required',
            'tenant_name'     => 'required|string|max:255',
            'tenant_phone'    => 'required|string|max:20',
            'rent_price'      => 'required',
            'deposit'         => 'required',
            'start_date'      => 'required',
            'duration_months' => 'required',
        ], [
            'room_id.required'         => 'Vui lòng chọn phòng / bài đăng cho thuê.',
            'tenant_name.required'     => 'Vui lòng nhập họ và tên khách thuê.',
            'tenant_phone.required'    => 'Vui lòng nhập số điện thoại khách thuê.',
            'rent_price.required'      => 'Vui lòng nhập giá thuê phòng.',
            'deposit.required'         => 'Vui lòng nhập số tiền cọc.',
            'start_date.required'      => 'Vui lòng chọn ngày bắt đầu hợp đồng.',
            'duration_months.required' => 'Vui lòng nhập thời hạn hợp đồng.',
        ]);

        // Hàm tiện ích chuẩn hóa chuỗi số tiền có dấu chấm/phẩy thành số nguyên/thực
        $cleanNumber = function ($value) {
            if ($value === null || $value === '') return 0;
            if (is_numeric($value)) return (float)$value;
            $clean = preg_replace('/[^\d]/', '', (string)$value);
            return $clean ? (float)$clean : 0;
        };

        // Bắt đầu Transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            $roomId = $request->input('room_id') ?? $request->input('post_id');
            $post = Post::find($roomId);

            // Tìm tenant_id nếu người thuê đã có tài khoản trên hệ thống
            $tenantUser = null;
            $contact = null;
            $rawContactId = $request->input('contact_id') ?: $request->input('contact_select');
            if ($rawContactId) {
                $contact = Contact::find($rawContactId);
                if ($contact && !empty($contact->sender_id)) {
                    $tenantUser = User::find($contact->sender_id);
                }
            }
            if (!$tenantUser && $request->filled('tenant_account')) {
                $tenantUser = User::where('username', $request->input('tenant_account'))->first();
            }
            if (!$tenantUser && $request->filled('tenant_phone')) {
                $tenantUser = User::where('phone', $request->input('tenant_phone'))->first();
            }
            if (!$tenantUser && $request->filled('tenant_email')) {
                $tenantUser = User::where('email', $request->input('tenant_email'))->first();
            }

            // 1. Tạo bản ghi Hợp đồng mới (cho phép contact_id nullable)
            $contractCode = 'HD-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -4));
            $currentUser = Auth::user();
            $enterpriseUser = User::getEnterpriseUser();

            // Nếu người tạo là tài khoản con (nhân viên), gán owner_id về Doanh nghiệp/Admin để dùng chung toàn công ty
            $ownerId = ($currentUser && !empty($currentUser->parent_id))
                ? (string)$currentUser->parent_id
                : ($enterpriseUser ? (string)($enterpriseUser->_id ?? $enterpriseUser->id) : (string)Auth::id());

            $ownerPhone = $enterpriseUser && !empty($enterpriseUser->phone) 
                ? $enterpriseUser->phone 
                : ($currentUser ? $currentUser->phone : null);

            // Xử lý upload hồ sơ đính kèm (CCCD & Bản Scan Hợp đồng)
            $cccdFiles = [];
            if ($request->hasFile('cccd_images')) {
                foreach ($request->file('cccd_images') as $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = public_path('uploads/contracts/cccd');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        $file->move($destinationPath, $filename);
                        $cccdFiles[] = '/uploads/contracts/cccd/' . $filename;
                    }
                }
            }

            $docFiles = [];
            if ($request->hasFile('contract_documents')) {
                foreach ($request->file('contract_documents') as $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $destinationPath = public_path('uploads/contracts/docs');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        $file->move($destinationPath, $filename);
                        $docFiles[] = '/uploads/contracts/docs/' . $filename;
                    }
                }
            }

            // Kiểm tra cấu hình Tòa nhà / Phòng có thu phí dịch vụ hay không
            $building = null;
            if ($post) {
                $building = $post->building;
                if (!$building && !empty($post->building_id) && class_exists(Building::class)) {
                    $building = Building::find($post->building_id);
                }
            }

            $isNoServiceFee = false;
            if ($building) {
                $hasServiceExplicit = isset($building->has_service_pricing) && $building->has_service_pricing !== null
                    ? (bool)$building->has_service_pricing
                    : (isset($building->has_service_fee) && $building->has_service_fee !== null ? (bool)$building->has_service_fee : null);

                $bElec = (float)($building->electricity_price ?? 0);
                $bWater = (float)($building->water_price ?? 0);
                $bService = (float)($building->service_fee ?? 0);

                // Nếu cơ sở tắt tính phí dịch vụ, hoặc giá điện = 0/null, hoặc toàn bộ đơn giá điện nước dịch vụ = 0
                if ($hasServiceExplicit === false || $bElec <= 0 || ($bElec <= 0 && $bWater <= 0 && $bService <= 0)) {
                    $isNoServiceFee = true;
                }
            } else {
                $reqElec = $cleanNumber($request->input('electricity_price'));
                if ($reqElec <= 0) {
                    $isNoServiceFee = true;
                }
            }

            $rawRentPrice = $cleanNumber($request->input('rent_price'));
            $rawDeposit = $cleanNumber($request->input('deposit'));
            $internetPrice = $isNoServiceFee ? 0 : $cleanNumber($request->input('internet_price'));
            $garbagePrice = $isNoServiceFee ? 0 : $cleanNumber($request->input('service_fee', $request->input('garbage_price')));
            $parkingPrice = $isNoServiceFee ? 0 : $cleanNumber($request->input('parking_fee'));
            $rawMonthlyService = $isNoServiceFee ? 0 : ($internetPrice + $garbagePrice + $parkingPrice);

            $startDateStr = $request->input('start_date', date('Y-m-d'));
            $startDate = Carbon::parse($startDateStr);
            $startDay = (int)$startDate->day;

            $paymentCycle = (int)($request->input('payment_cycle', 1) ?: 1);
            if (!in_array($paymentCycle, [1, 3, 6, 12])) {
                $paymentCycle = 1;
            }

            $serviceNote = $isNoServiceFee
                ? ' (Cơ sở này không thu phí dịch vụ tập trung. Khách thuê tự thanh toán điện/nước theo đồng hồ riêng).'
                : ' (Điện nước ghi nhận chỉ số đầu: ' . (int)$request->input('electricity_start', 0) . ' số điện, ' . (int)$request->input('water_start', 0) . ' khối nước; thành tiền = 0 VNĐ).';

            // Xử lý tính toán Lẻ ngày (Pro-rated) theo chu kỳ thu tiền nhà:
            if ($startDay === 1) {
                $isProrated = false;
                $stayDays = 30;
                $rentPriceFirstMonth = (float)($rawRentPrice * $paymentCycle);
                $serviceFirstMonth = $rawMonthlyService;
                $nextRentDate = $startDate->copy()->addMonths($paymentCycle)->toDateString();
                $periodDescription = 'Kỳ đầu tiên (' . ($paymentCycle > 1 ? $paymentCycle . ' tháng: ' . $startDate->format('m/Y') . ' - ' . $startDate->copy()->addMonths($paymentCycle - 1)->format('m/Y') : 'Trọn tháng ' . $startDate->format('m/Y')) . ')';
                $noteDescription = 'Hóa đơn thanh toán lần đầu: Tiền cọc & Tiền phòng kỳ đầu (' . $paymentCycle . ' tháng) khi nhận nhà.' . $serviceNote;
            } else {
                $isProrated = true;
                $stayDays = max(1, 30 - $startDay + 1);
                $rentPriceFirstMonth = (float)round(($rawRentPrice / 30) * $stayDays) + (float)($rawRentPrice * max(0, $paymentCycle - 1));
                $serviceFirstMonth = (float)round(($rawMonthlyService / 30) * $stayDays);
                $nextRentDate = $startDate->copy()->startOfMonth()->addMonths($paymentCycle)->toDateString();
                $periodDescription = 'Kỳ đầu tiên (Lẻ ' . $stayDays . ' ngày: ' . $startDate->format('d/m') . ' - ' . $startDate->copy()->endOfMonth()->format('d/m/Y') . ($paymentCycle > 1 ? ' + ' . ($paymentCycle - 1) . ' tháng tiếp theo' : '') . ')';
                $noteDescription = 'Hóa đơn thanh toán lần đầu: Tiền cọc & Tiền phòng kỳ đầu (' . $paymentCycle . ' tháng, gồm lẻ ' . $stayDays . ' ngày của tháng ' . $startDate->format('m/Y') . ').' . $serviceNote;
            }

            $contractData = [
                'contract_code'          => $contractCode,
                'contact_id'             => $contact ? (string)($contact->_id ?? $contact->id) : null,
                'post_id'                => (string)$roomId,
                'room_id'                => (string)$roomId,
                'owner_id'               => (string)$ownerId,
                'created_by'             => (string)Auth::id(),
                'staff_id'               => (string)Auth::id(),
                'owner_phone'            => $ownerPhone,
                'tenant_id'              => $tenantUser ? (string)($tenantUser->id ?? $tenantUser->_id) : ($contact ? (string)$contact->sender_id : null),
                'tenant_name'            => $request->input('tenant_name'),
                'tenant_phone'           => $request->input('tenant_phone'),
                'tenant_email'           => $request->input('tenant_email'),
                'tenant_id_card'         => $request->input('tenant_id_card'),
                'tenant_account'         => $request->input('tenant_account') ?: ($tenantUser ? $tenantUser->username : null),
                'tenant_message'         => $request->input('tenant_message'),
                'rent_price'             => $rawRentPrice,
                'deposit'                => $rawDeposit,
                'electricity_price'      => $cleanNumber($request->input('electricity_price')),
                'electricity_start'      => (int)$request->input('electricity_start', 0),
                'water_calculation_type' => $request->input('water_calculation_type', 'khoi'),
                'water_price_block'      => $cleanNumber($request->input('water_price_block')),
                'water_start'            => (int)$request->input('water_start', 0),
                'water_price_person'     => $cleanNumber($request->input('water_price_person')),
                'water_people_count'     => (int)$request->input('water_people_count', 1),
                'internet_price'         => $internetPrice,
                'garbage_price'          => $garbagePrice,
                'service_fee'            => $garbagePrice,
                'parking_fee'            => $parkingPrice,
                'license_plates'         => $request->input('license_plates'),
                'cccd_images'            => $cccdFiles,
                'contract_documents'     => $docFiles,
                'payment_cycle'          => $paymentCycle,
                'next_rent_date'         => $nextRentDate,
                'billing_date'           => $request->input('billing_date', 'Mùng 5'),
                'payment_limit_days'     => $request->input('payment_limit_days', '5 ngày'),
                'late_penalty_fee'       => $request->input('late_penalty_fee', '20.000 VNĐ / ngày'),
                'start_date'             => $startDateStr,
                'duration_months'        => (int)$request->input('duration_months', 12),
                'end_date'               => $request->input('end_date_display'),
                'status'                 => 'pending_deposit', // Chờ thanh toán cọc & hóa đơn lần đầu để kích hoạt chính thức
                'payment_status'         => 'unpaid',
            ];

            // BƯỚC 2: Rào chắn bảo mật ép kiểu (Force override) các trường liên quan về null hoặc 0
            if ($isNoServiceFee) {
                $contractData['electricity_start'] = null;
                $contractData['water_start'] = null;
                $contractData['electricity_price'] = 0;
                $contractData['water_price'] = 0;
                $contractData['water_price_block'] = 0;
                $contractData['water_price_person'] = 0;
                $contractData['water_people_count'] = 0;
                $contractData['internet_price'] = 0;
                $contractData['garbage_price'] = 0;
                $contractData['service_fee'] = 0;
                $contractData['parking_fee'] = 0;
            }

            $contract = Contract::create($contractData);

            // 1.1 Tự động tạo Hóa đơn #1 (Tiền cọc + Tiền phòng kỳ đầu lẻ ngày hoặc nguyên tháng + Điện nước 0 VNĐ)
            $depositAmount = $rawDeposit;
            $electricityAmount = 0; // Tháng đầu tiên: chỉ số đầu, thành tiền = 0 VNĐ
            $waterAmount = 0;       // Tháng đầu tiên: chỉ số đầu, thành tiền = 0 VNĐ
            $totalFirstMonth = $depositAmount + $rentPriceFirstMonth + $electricityAmount + $waterAmount + $serviceFirstMonth;

            $firstInvoiceCode = 'INV-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -4));
            $invoice = Invoice::create([
                'contract_id'        => (string)($contract->_id ?? $contract->id),
                'room_id'            => (string)$roomId,
                'post_id'            => (string)$roomId,
                'owner_id'           => (string)$ownerId,
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
                'deposit'            => $depositAmount,
                'electricity_amount' => $electricityAmount,
                'electricity_old'    => $isNoServiceFee ? null : (float)($contract->electricity_start ?? 0),
                'electricity_new'    => $isNoServiceFee ? null : (float)($contract->electricity_start ?? 0),
                'water_amount'       => $waterAmount,
                'water_old'          => $isNoServiceFee ? null : (float)($contract->water_start ?? 0),
                'water_new'          => $isNoServiceFee ? null : (float)($contract->water_start ?? 0),
                'service_amount'     => $serviceFirstMonth,
                'total_amount'       => $totalFirstMonth,
                'status'             => 'unpaid',
                'payment_status'     => 'unpaid',
                'type'               => 'first_month',
                'rent_cycle_applied' => true, // Đã tính sẵn chu kỳ đầu vào next_rent_date khi tạo hợp đồng, chặn nhảy đúp chu kỳ
                'note'               => $noteDescription,
            ]);

            // 2. Cập nhật trạng thái của phòng thành 'đã cho thuê' và tự động gỡ khỏi Slider Nổi Bật
            if ($post) {
                $post->status = 'đã cho thuê';
                $post->is_rented = true;
                $post->is_featured = false;
                $post->featured_at = null;
                $post->save();
            }

            // 3. Logic xử lý yêu cầu được chọn tạo hợp đồng:
            if ($contact) {
                $contact->status = 'đã ký hợp đồng';
                $contact->contract_id = (string)($contract->_id ?? $contract->id);
                $contact->save();
            }

            // Gửi thông báo đến khách thuê nếu có tài khoản liên kết
            $recipientId = $contact ? ($contact->sender_id ?? ($tenantUser ? ($tenantUser->id ?? $tenantUser->_id) : null)) : ($tenantUser ? ($tenantUser->id ?? $tenantUser->_id) : null);
            if ($recipientId) {
                Notification::create([
                    'user_id'  => (string)$recipientId,
                    'post_id'  => (string)$roomId,
                    'title'    => 'Bạn đã nhận được Hợp đồng thuê nhà & Hóa đơn đóng tiền cọc đầu tháng!',
                    'message'  => 'Chủ nhà đã tạo hợp đồng thuê và xuất hóa đơn tiền cọc & kỳ đầu tiên (' . $periodDescription . ') cho bài đăng "' . ($post->title ?? 'phòng trọ') . '". Bạn có thể xem hóa đơn và bấm "Thanh toán ngay" để kích hoạt hợp đồng và nhận chìa khóa phòng.',
                    'type'     => 'success',
                    'is_read'  => false,
                    'category' => 'contract',
                ]);
            }

            // 4. Tự động từ chối tất cả các yêu cầu khác gửi đến bài đăng đó và gửi thông báo
            $acceptedContactId = $contact ? (string)($contact->_id ?? $contact->id) : null;
            ContactController::autoRejectOtherContactsForPost($roomId, $acceptedContactId);

            // Hoàn tất Transaction
            DB::commit();

            // Redirect thẳng sang trang Chi tiết Hóa đơn #1 vừa tạo
            return redirect()->route('qlvan_hanh.invoice_detail', ['code' => $firstInvoiceCode])
                ->with('success', 'Tạo hợp đồng thành công! Hóa đơn thanh toán lần đầu (Hóa đơn #1) đã được tạo.');
        } catch (\Throwable $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage())->withInput();
        }
    }
}
