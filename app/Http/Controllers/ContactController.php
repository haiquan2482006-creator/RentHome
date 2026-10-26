<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContactController extends Controller
{
    /**
     * Ẩn tất cả các yêu cầu liên hệ liên quan đến một hợp đồng khi hợp đồng bị hủy, thanh lý hoặc có yêu cầu trả phòng
     */
    public static function hideContactsForContract($contract)
    {
        if (!$contract) return;

        $contractId = (string)($contract->_id ?? $contract->id);
        $contactId = !empty($contract->contact_id) ? (string)$contract->contact_id : null;
        $postIds = array_values(array_filter([(string)($contract->post_id ?? ''), (string)($contract->room_id ?? '')]));
        $tenantId = (string)($contract->tenant_id ?? '');
        $tenantPhone = (string)($contract->tenant_phone ?? '');
        $tenantEmail = (string)($contract->tenant_email ?? '');

        // 1. Tìm các contact theo contract_id hoặc _id
        $contactsQuery = Contact::where(function($q) use ($contractId, $contactId) {
            $q->where('contract_id', $contractId);
            if ($contactId) {
                $q->orWhere('_id', $contactId)
                  ->orWhere('id', $contactId);
                try {
                    $q->orWhere('_id', new \MongoDB\BSON\ObjectId($contactId));
                } catch (\Throwable $e) {}
            }
        });

        $contacts = $contactsQuery->get();

        // 2. Tìm thêm các contact theo post_id và thông tin người thuê nếu có
        if (!empty($postIds) && ($tenantId || $tenantPhone || $tenantEmail)) {
            $postContacts = Contact::where(function($q) use ($postIds) {
                $q->whereIn('post_id', $postIds);
                foreach ($postIds as $pid) {
                    try {
                        $q->orWhere('post_id', new \MongoDB\BSON\ObjectId($pid));
                    } catch (\Throwable $e) {}
                }
            })->where(function($cond) use ($tenantId, $tenantPhone, $tenantEmail) {
                $hasCond = false;
                if ($tenantId) {
                    $cond->where('sender_id', $tenantId);
                    $hasCond = true;
                }
                if ($tenantPhone) {
                    if ($hasCond) $cond->orWhere('phone', $tenantPhone);
                    else { $cond->where('phone', $tenantPhone); $hasCond = true; }
                }
                if ($tenantEmail) {
                    if ($hasCond) $cond->orWhere('email', $tenantEmail);
                    else { $cond->where('email', $tenantEmail); $hasCond = true; }
                }
            })->get();

            $contacts = $contacts->merge($postContacts)->unique(function($c) {
                return (string)($c->_id ?? $c->id);
            });
        }

        foreach ($contacts as $c) {
            $c->is_hidden = true;
            $c->status = 'liquidated';
            $c->save();
        }
    }

    /**
     * Tự động từ chối tất cả các yêu cầu liên hệ khác của bài đăng khi bài đăng đã có hợp đồng
     */
    public static function autoRejectOtherContactsForPost($postId, $acceptedContactId = null)
    {
        if (!$postId) return;

        $post = Post::find($postId);
        $targetPostIds = array_unique(array_filter([
            (string)$postId,
            $post ? (string)($post->_id ?? $post->id) : null,
            $post && !empty($post->id) ? (string)$post->id : null,
        ]));

        // Nếu chưa có acceptedContactId, tìm hợp đồng còn hiệu lực của bài đăng này
        if (!$acceptedContactId) {
            $contract = Contract::where(function($q) use ($targetPostIds) {
                $q->whereIn('post_id', $targetPostIds)
                  ->orWhereIn('room_id', $targetPostIds);
            })->whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy', 'liquidated', 'expired'])
              ->whereNull('checkout_request')
              ->latest()->first();

            // Nếu không có hợp đồng active, không tự động từ chối yêu cầu nào
            if (!$contract) {
                return;
            }

            if (!empty($contract->contact_id)) {
                $acceptedContactId = (string)$contract->contact_id;
            }
        }

        // Tìm tất cả các yêu cầu liên hệ thuộc bài đăng này
        $contacts = Contact::where(function ($q) use ($targetPostIds) {
            $q->whereIn('post_id', $targetPostIds);
            foreach ($targetPostIds as $pid) {
                try {
                    $q->orWhere('post_id', new \MongoDB\BSON\ObjectId($pid));
                } catch (\Throwable $e) {}
            }
        })->get();

        if ($post && method_exists($post, 'contacts')) {
            $postContacts = $post->contacts()->get();
            if ($postContacts->isNotEmpty()) {
                $contacts = $contacts->merge($postContacts)->unique(function ($c) {
                    return (string)($c->_id ?? $c->id);
                });
            }
        }

        $notifiedUserIds = [];

        foreach ($contacts as $contact) {
            $cId = (string)($contact->_id ?? $contact->id);

            // Bỏ qua yêu cầu đã tạo hợp đồng
            if ($acceptedContactId && $cId === (string)$acceptedContactId) {
                continue;
            }

            // Xác định tài khoản gửi yêu cầu để gửi thông báo từ chối
            $rejectedUserId = $contact->sender_id;
            if (!$rejectedUserId && !empty($contact->email)) {
                $uByEmail = User::where('email', $contact->email)->first();
                if ($uByEmail) {
                    $rejectedUserId = (string)($uByEmail->_id ?? $uByEmail->id);
                }
            }
            if (!$rejectedUserId && !empty($contact->phone)) {
                $uByPhone = User::where('phone', $contact->phone)->first();
                if ($uByPhone) {
                    $rejectedUserId = (string)($uByPhone->_id ?? $uByPhone->id);
                }
            }

            if ($rejectedUserId && !in_array((string)$rejectedUserId, $notifiedUserIds)) {
                $notifiedUserIds[] = (string)$rejectedUserId;

                // Kiểm tra xem đã gửi thông báo từ chối cho post này chưa
                $alreadyNotified = Notification::where('user_id', (string)$rejectedUserId)
                    ->where('post_id', (string)$postId)
                    ->where('message', 'like', '%đã có người thuê nhà%')
                    ->exists();

                if (!$alreadyNotified) {
                    Notification::create([
                        'user_id'  => (string)$rejectedUserId,
                        'post_id'  => (string)$postId,
                        'title'    => 'Yêu cầu liên hệ đã bị từ chối',
                        'message'  => 'yêu cầu của bạn đã bị từ chối do đã có người thuê nhà ',
                        'type'     => 'error',
                        'is_read'  => false,
                        'category' => 'contact_status',
                    ]);
                }
            }

            // Xóa bỏ hoàn toàn yêu cầu này khỏi cơ sở dữ liệu contacts và giao diện
            $contact->delete();
        }
    }

    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'appointment_date' => 'required|date|after_or_equal:now',
            'message' => 'required',
        ], [
            'appointment_date.after_or_equal' => 'Lịch hẹn không hợp lệ. Vui lòng chọn thời gian từ hiện tại trở đi.',
        ]);

        $formattedDate = Carbon::parse($request->appointment_date)->format('H:i - d/m/Y');
        $note = "Lịch hẹn: " . $formattedDate;

        $contact = Contact::create([
            'post_id' => (string) $post->_id,
            'owner_id' => (string) ($post->user_id ?? $post->user->_id),
            'sender_id' => Auth::check() ? Auth::id() : null,
            'sender_name' => $request->fullname,
            'phone' => $request->phone,
            'email' => $request->email,
            'appointment_date' => $request->appointment_date,
            'note' => $note,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        // Tạo thông báo tự động gửi về quả chuông cho tài khoản đăng bài
        $ownerId = (string) ($post->user_id ?? ($post->user->_id ?? $post->author_id));
        if ($ownerId) {
            Notification::create([
                'user_id'    => $ownerId,
                'role'       => 'owner',
                'category'   => 'contact_received',
                'type'       => 'info',
                'title'      => 'Có yêu cầu liên hệ / đặt lịch hẹn mới',
                'message'    => "Khách hàng {$request->fullname} ({$request->phone}) vừa gửi yêu cầu liên hệ/đặt lịch xem bài đăng \"{$post->title}\".",
                'post_id'    => (string) $post->_id,
                'action_url' => url('/thong-bao-lien-he'),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Nếu là tài khoản nhân viên con, gửi thông báo kèm cho tài khoản doanh nghiệp mẹ
        if (!empty($post->parent_id) && (string)$post->parent_id !== $ownerId) {
            Notification::create([
                'user_id'    => (string)$post->parent_id,
                'role'       => 'doanhnghiep',
                'category'   => 'contact_received',
                'type'       => 'info',
                'title'      => 'Yêu cầu liên hệ mới cho bài đăng nhân viên',
                'message'    => "Khách hàng {$request->fullname} ({$request->phone}) vừa gửi yêu cầu liên hệ cho bài đăng \"{$post->title}\" của nhân viên.",
                'post_id'    => (string) $post->_id,
                'action_url' => url('/thong-bao-lien-he'),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Đã gửi thông tin liên hệ và lịch hẹn thành công! Chủ nhà sẽ sớm phản hồi bạn.');
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem thông báo khách hàng liên hệ.');
        }

        $user = Auth::user();
        $userId = Auth::id();

        // Truy xuất các bài đăng có liên hệ dựa trên phân quyền doanh nghiệp & tài khoản con
        $postsQuery = Post::query();

        if ($user->isSubAccount() && !empty($user->assigned_buildings) && !in_array('all', $user->assigned_buildings, true)) {
            $assignedIds = array_map('strval', (array)$user->assigned_buildings);
            $postsQuery->where(function($q) use ($assignedIds, $userId) {
                $q->whereIn('building_id', $assignedIds)
                  ->orWhere('user_id', (string)$userId);
            });
        } elseif (!$user->isAdmin() && !$user->isEnterprise() && !$user->isSubAccount()) {
            $postsQuery->where('user_id', (string)$userId);
        }

        $posts = $postsQuery->get();

        // Quét và ẩn các contact của những hợp đồng đã hủy, thanh lý hoặc có yêu cầu trả phòng
        foreach ($posts as $post) {
            $postId = (string)($post->_id ?? $post->id);
            $targetPostIds = array_unique(array_filter([
                $postId,
                !empty($post->id) ? (string)$post->id : null
            ]));

            // Tìm các hợp đồng đã hủy / thanh lý / có yêu cầu trả phòng của bài đăng này
            $inactiveContracts = Contract::where(function($q) use ($targetPostIds) {
                $q->whereIn('post_id', $targetPostIds)
                  ->orWhereIn('room_id', $targetPostIds);
            })->where(function($q) {
                $q->whereIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy', 'liquidated', 'expired'])
                  ->orWhereNotNull('checkout_request');
            })->get();

            foreach ($inactiveContracts as $ic) {
                self::hideContactsForContract($ic);
            }

            // Kiểm tra xem bài đăng có hợp đồng nào đang active (và không có yêu cầu trả phòng) không
            $activeContract = Contract::where(function($q) use ($targetPostIds) {
                $q->whereIn('post_id', $targetPostIds)
                  ->orWhereIn('room_id', $targetPostIds);
            })->whereNotIn('status', ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy', 'liquidated', 'expired'])
              ->whereNull('checkout_request')
              ->latest()->first();

            $isRented = ($activeContract !== null || ($post->status === 'đã cho thuê' && !empty($post->is_rented)));

            if ($isRented && $activeContract) {
                $acceptedContactId = $activeContract->contact_id;
                self::autoRejectOtherContactsForPost($postId, $acceptedContactId);
            }
        }

        // Tải danh sách contacts, chỉ lấy các contact chưa bị ẩn và không ở trạng thái liquidated/cancelled
        $posts->load(['contacts' => function($query) {
            $query->where(function($q) {
                $q->whereNull('is_hidden')->orWhere('is_hidden', '!=', true);
            })->whereNotIn('status', ['liquidated', 'cancelled', 'terminated', 'hidden'])->latest();
        }]);

        // Lọc kỹ thêm trên collection để loại trừ triệt để bất kỳ contact nào liên quan đến hợp đồng đã hủy / yêu cầu trả phòng
        foreach ($posts as $post) {
            $filtered = $post->contacts->filter(function($contact) {
                if (!empty($contact->is_hidden) || in_array($contact->status, ['liquidated', 'cancelled', 'terminated', 'hidden'])) {
                    return false;
                }

                // Nếu contact có contract_id, kiểm tra trạng thái hợp đồng
                if (!empty($contact->contract_id)) {
                    $c = Contract::where('_id', $contact->contract_id)->orWhere('id', $contact->contract_id)->first();
                    if (!$c || in_array($c->status, ['cancelled', 'terminated', 'da_thanh_ly', 'da_huy', 'liquidated', 'expired']) || !empty($c->checkout_request)) {
                        $contact->is_hidden = true;
                        $contact->status = 'liquidated';
                        $contact->save();
                        return false;
                    }
                }

                return true;
            });

            $post->setRelation('contacts', $filtered);
        }

        $groupedContacts = $posts->filter(function ($post) {
            return $post->contacts && $post->contacts->count() > 0;
        });

        // Sắp xếp các bài đăng sao cho bài đăng có liên hệ mới nhất nằm ở trên cùng.
        $groupedContacts = $groupedContacts->sortByDesc(function ($post) {
            return $post->contacts->first() ? $post->contacts->first()->created_at : null;
        });

        $totalContacts = 0;
        $pendingContacts = 0;

        foreach ($groupedContacts as $post) {
            $totalContacts += $post->contacts->count();
            $pendingContacts += $post->contacts->where('status', 'pending')->count();
        }

        return view('thongbaolienhe', compact('groupedContacts', 'totalContacts', 'pendingContacts'));
    }

    public function myRequests()
    {
        return redirect()->route('contact.index');
    }
    public function revoke($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->sender_id != Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền thực hiện hành động này.'], 403);
        }

        if ($contact->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Chỉ có thể thu hồi yêu cầu đang ở trạng thái chờ phản hồi.'], 400);
        }

        $contact->forceDelete();

        return response()->json(['success' => true, 'message' => 'Đã thu hồi yêu cầu thành công']);
    }

    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::with('post')->findOrFail($id);
        $user = Auth::user();

        $canUpdate = false;
        if ($user->isAdmin() || $user->isEnterprise()) {
            $canUpdate = true;
        } elseif ($user->isSubAccount()) {
            if ($contact->post && (!empty($contact->post->building_id) ? $user->canManageBuilding($contact->post->building_id) : true)) {
                $canUpdate = true;
            } elseif ($contact->post && $contact->post->user_id == $user->id) {
                $canUpdate = true;
            }
        } elseif (($contact->post->user_id ?? $contact->owner_id) == Auth::id()) {
            $canUpdate = true;
        }

        if (!$canUpdate) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,communicating,approved,rejected,closed'
        ]);

        $contact->status = $request->status;
        $contact->save();

        // Gửi thông báo cho người thuê
        if ($contact->sender_id) {
            $renter = \App\Models\User::find($contact->sender_id);
            if ($renter) {
                $statusMap = [
                    'communicating' => 'yêu cầu của bạn đã được chủ nhà đồng ý , hãy chờ liên hệ',
                    'approved' => 'Yêu cầu liên hệ của bạn đã được duyệt',
                    'rejected' => 'Yêu cầu liên hệ của bạn đã bị từ chối',
                    'closed' => 'Yêu cầu liên hệ của bạn đã bị đóng',
                ];
                
                if (isset($statusMap[$request->status])) {
                    if ($request->status === 'communicating') {
                        $title = 'Yêu cầu của bạn đã được chủ nhà đồng ý';
                        $message = 'yêu cầu của bạn đã được chủ nhà đồng ý , hãy chờ liên hệ';
                    } else {
                        $title = 'Cập nhật trạng thái liên hệ';
                        $message = $statusMap[$request->status] . ' về bài đăng "' . ($contact->post->title ?? 'Bất động sản') . '".';
                    }
                    $actionUrl = url('/lich-su-lien-he');

                    \App\Models\Notification::create([
                        'user_id' => $renter->_id ?? $renter->id,
                        'post_id' => $contact->post_id ?? null,
                        'title' => $title,
                        'message' => $message,
                        'type' => in_array($request->status, ['communicating', 'approved']) ? 'success' : ($request->status === 'rejected' ? 'error' : 'info'),
                        'is_read' => false,
                        'category' => 'contact_status'
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    }
}
