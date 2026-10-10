<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Lưu hợp đồng thuê mới và tách biệt 2 luồng: Khách online (từ Liên hệ) vs Khách vãng lai (từ QL Vận hành)
     */
    public function store(Request $request)
    {
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
            $ownerUser = Auth::user();
            $ownerId = Auth::id() ? (string)Auth::id() : ($post->user_id ?? null);

            $contractData = [
                'contract_code'          => $contractCode,
                'contact_id'             => $contact ? (string)($contact->_id ?? $contact->id) : null,
                'post_id'                => (string)$roomId,
                'room_id'                => (string)$roomId,
                'owner_id'               => (string)$ownerId,
                'owner_phone'            => $ownerUser ? $ownerUser->phone : null,
                'tenant_id'              => $tenantUser ? (string)($tenantUser->id ?? $tenantUser->_id) : ($contact ? (string)$contact->sender_id : null),
                'tenant_name'            => $request->input('tenant_name'),
                'tenant_phone'           => $request->input('tenant_phone'),
                'tenant_email'           => $request->input('tenant_email'),
                'tenant_id_card'         => $request->input('tenant_id_card'),
                'tenant_account'         => $request->input('tenant_account') ?: ($tenantUser ? $tenantUser->username : null),
                'tenant_message'         => $request->input('tenant_message'),
                'rent_price'             => $cleanNumber($request->input('rent_price')),
                'deposit'                => $cleanNumber($request->input('deposit')),
                'electricity_price'      => $cleanNumber($request->input('electricity_price')),
                'electricity_start'      => (int)$request->input('electricity_start', 0),
                'water_calculation_type' => $request->input('water_calculation_type', 'khoi'),
                'water_price_block'      => $cleanNumber($request->input('water_price_block')),
                'water_start'            => (int)$request->input('water_start', 0),
                'water_price_person'     => $cleanNumber($request->input('water_price_person')),
                'water_people_count'     => (int)$request->input('water_people_count', 1),
                'internet_price'         => $cleanNumber($request->input('internet_price')),
                'garbage_price'          => $cleanNumber($request->input('garbage_price')),
                'payment_cycle'          => $request->input('payment_cycle', '1 tháng'),
                'billing_date'           => $request->input('billing_date', 'Mùng 5'),
                'payment_limit_days'     => $request->input('payment_limit_days', '5 ngày'),
                'late_penalty_fee'       => $request->input('late_penalty_fee', '20.000 VNĐ / ngày'),
                'start_date'             => $request->input('start_date'),
                'duration_months'        => (int)$request->input('duration_months', 12),
                'end_date'               => $request->input('end_date_display'),
                'status'                 => 'active', // Trạng thái hợp đồng đang hoạt động
                'payment_status'         => 'unpaid',
            ];

            $contract = Contract::create($contractData);

            // 2. Cập nhật trạng thái của phòng thành 'đã cho thuê'
            if ($post) {
                $post->status = 'đã cho thuê';
                $post->is_rented = true;
                $post->save();
            }

            // 3. Logic xử lý yêu cầu được chọn tạo hợp đồng:
            if ($contact) {
                $contact->status = 'approved';
                $contact->contract_id = (string)($contract->_id ?? $contract->id);
                $contact->save();

                // Gửi thông báo đến khách thuê được duyệt nếu có tài khoản liên kết
                $recipientId = $contact->sender_id ?? ($tenantUser ? ($tenantUser->id ?? $tenantUser->_id) : null);
                if ($recipientId) {
                    Notification::create([
                        'user_id'  => (string)$recipientId,
                        'post_id'  => (string)$roomId,
                        'title'    => 'Hợp đồng thuê nhà đã được ký kết!',
                        'message'  => 'Chủ nhà đã tạo hợp đồng thuê thành công cho bài đăng "' . ($post->title ?? 'phòng trọ') . '". Bạn có thể xem chi tiết tại mục Nhà đang thuê.',
                        'type'     => 'success',
                        'is_read'  => false,
                        'category' => 'contract',
                    ]);
                }
            }

            // 4. Tự động từ chối tất cả các yêu cầu khác gửi đến bài đăng đó và gửi thông báo
            $acceptedContactId = $contact ? (string)($contact->_id ?? $contact->id) : null;
            ContactController::autoRejectOtherContactsForPost($roomId, $acceptedContactId);

            // Hoàn tất Transaction
            DB::commit();

            return redirect()->route('qlvan_hanh.room_list')->with('success', 'Tạo hợp đồng thành công!');
        } catch (\Throwable $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra khi tạo hợp đồng: ' . $e->getMessage())->withInput();
        }
    }
}
