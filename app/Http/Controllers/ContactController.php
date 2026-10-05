<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContactController extends Controller
{
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
                    'communicating' => 'Chủ nhà đang trao đổi với bạn',
                    'approved' => 'Yêu cầu liên hệ của bạn đã được duyệt',
                    'rejected' => 'Yêu cầu liên hệ của bạn đã bị từ chối',
                    'closed' => 'Yêu cầu liên hệ của bạn đã bị đóng',
                ];
                
                if (isset($statusMap[$request->status])) {
                    $title = 'Cập nhật trạng thái liên hệ';
                    $message = $statusMap[$request->status] . ' về bài đăng "' . ($contact->post->title ?? 'Bất động sản') . '".';
                    $actionUrl = url('/lich-su-lien-he');
                    $icon = 'fa-solid fa-clipboard-check text-brand-600';

                    $renter->notify(new \App\Notifications\ContactStatusChanged($title, $message, $actionUrl, $icon));
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    }
}
