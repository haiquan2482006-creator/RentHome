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

        // Nếu chưa có acceptedContactId, tìm hợp đồng của bài đăng này
        if (!$acceptedContactId) {
            $contract = Contract::where(function($q) use ($targetPostIds) {
                $q->whereIn('post_id', $targetPostIds)
                  ->orWhereIn('room_id', $targetPostIds);
            })->where('status', '!=', 'cancelled')->latest()->first();

            if ($contract && !empty($contract->contact_id)) {
                $acceptedContactId = (string)$contract->contact_id;
            } else {
                $signedContact = Contact::where(function ($q) use ($targetPostIds) {
                    $q->whereIn('post_id', $targetPostIds);
                })->where(function($q) {
                    $q->where('status', 'đã ký hợp đồng')
                      ->orWhereNotNull('contract_id');
                })->first();

                if ($signedContact) {
                    $acceptedContactId = (string)($signedContact->_id ?? $signedContact->id);
                }
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

        Contact::create([
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

        return back()->with('success', 'Đã gửi thông tin liên hệ và lịch hẹn thành công! Chủ nhà sẽ sớm phản hồi bạn.');
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem thông báo khách hàng liên hệ.');
        }

        $userId = Auth::id();

        // Lấy danh sách các bài đăng (Post) của user đang đăng nhập (Auth::id()) mà có liên hệ
        $posts = Post::with(['contacts' => function($query) {
                $query->latest();
            }])
            ->where('user_id', $userId)
            ->get();

        // Tự động quét và từ chối các yêu cầu khác nếu bài đăng đã có hợp đồng
        foreach ($posts as $post) {
            $postId = (string)($post->_id ?? $post->id);
            $contract = Contract::where(function($q) use ($postId) {
                $q->where('post_id', $postId)->orWhere('room_id', $postId);
            })->where('status', '!=', 'cancelled')->latest()->first();

            $isRented = ($post->status === 'đã cho thuê' || !empty($post->is_rented) || $contract !== null);

            if ($isRented) {
                $acceptedContactId = $contract ? $contract->contact_id : null;
                self::autoRejectOtherContactsForPost($postId, $acceptedContactId);
            }
        }

        // Tải lại danh sách contacts mới nhất sau khi tự động quét
        $posts->load(['contacts' => function($query) {
            $query->latest();
        }]);

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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem lịch sử liên hệ.');
        }

        // Lấy danh sách các liên hệ do user hiện tại gửi
        $myContacts = Contact::with('post')
            ->where('sender_id', Auth::id())
            ->latest()
            ->get();

        return view('lich-su-lien-he', compact('myContacts'));
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

        if (!$contact->post || ($contact->post->user_id ?? $contact->owner_id) != Auth::id()) {
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
