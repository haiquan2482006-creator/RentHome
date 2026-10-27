<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FrontendController extends Controller
{


    /**
     * Tiếp nhận và xử lý báo cáo / khiếu nại bài đăng (BƯỚC 3)
     * Dữ liệu được lưu thẳng vào collection support_tickets với type = 'khieu_nai'
     * Route: POST /bao-cao-tin
     */
    public function submitKhieuNai(Request $request)
    {
        // 1. Validate form: Nội dung không được để trống, mảng images phải là file ảnh
        $validated = $request->validate([
            'post_id'  => 'required',
            'staff_id' => 'nullable',
            'noidung'  => 'required|string|max:5000',
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'noidung.required' => 'Vui lòng nhập nội dung chi tiết phản ánh bài đăng.',
            'images.*.image'   => 'Tất cả tệp đính kèm phải là định dạng hình ảnh.',
            'images.*.max'     => 'Kích thước mỗi ảnh không được vượt quá 5MB.',
        ]);

        try {
            // 2. Xử lý lưu ảnh (nếu có): Lưu các file vào thư mục storage/app/public/complaints
            $imagePaths = [];
            if ($request->hasFile('images')) {
                if (!Storage::disk('public')->exists('complaints')) {
                    Storage::disk('public')->makeDirectory('complaints');
                }

                foreach ($request->file('images') as $file) {
                    if ($file->isValid()) {
                        $storedPath = $file->store('complaints', 'public');
                        $imagePaths[] = 'storage/' . $storedPath;
                    }
                }
            }

            // Fallback lấy staff_id từ bài đăng nếu phía client truyền rỗng
            $staffId = $request->staff_id;
            if (empty($staffId) && !empty($request->post_id)) {
                $post = Post::find($request->post_id);
                if ($post) {
                    $staffId = $post->staff_id ?? ($post->user_id ?? null);
                }
            }

            // Lấy thông tin người gửi nếu đã đăng nhập
            $user = Auth::user();
            $userId = $user ? (string)($user->_id ?? $user->id) : null;
            $contentMsg = trim((string)$request->noidung);
            $postId = (string)$request->post_id;

            // Chống gửi trùng lặp nhiều lần (Duplicate submission trong vòng 10 giây)
            $recentTicket = SupportTicket::where('post_id', $postId)
                ->where('content', $contentMsg)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->when($userId, function ($q, $uId) {
                    return $q->where('user_id', $uId);
                })
                ->first();

            if ($recentTicket) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success'     => true,
                        'message'     => 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.',
                        'ticket_code' => $recentTicket->complaint_code ?? $recentTicket->ticket_code,
                    ]);
                }
                return redirect()->back()->with('success', 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.');
            }

            $ticketCode = '#KN-' . strtoupper(substr(uniqid(), -5));

            // 3. Lưu vào Database (Collection support_tickets)
            $ticket = SupportTicket::create([
                'type'           => 'khieu_nai',
                'complaint_code' => $ticketCode,
                'ticket_code'    => $ticketCode,
                'post_id'        => $request->post_id,
                'target_id'      => $request->post_id,
                'target_type'    => 'post',
                'staff_id'       => $staffId,
                'message'        => $contentMsg,
                'content'        => $contentMsg,
                'reason'         => $request->input('reason', 'Phản ánh bài đăng'),
                'images'         => $imagePaths,
                'status'         => 'pending',
                'user_id'        => $userId,
                'sender_name'    => $user ? ($user->account_name ?? ($user->username ?? ($user->name ?? 'Người dùng'))) : 'Khách vãng lai',
                'sender_phone'   => $user ? ($user->phone ?? null) : null,
                'sender_email'   => $user ? ($user->email ?? null) : null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Bắn thông báo về kênh CSKH
            $reportedPost = Post::find($request->post_id);
            $reportedPostTitle = $reportedPost ? $reportedPost->title : 'Bài đăng';
            $senderDisplayName = $user ? ($user->account_name ?? ($user->username ?? ($user->name ?? 'Khách thuê'))) : 'Khách vãng lai';
            \App\Http\Controllers\CSKHController::notifyCskh(
                'new_complaint',
                'Khiếu nại mới từ khách thuê',
                "Khách hàng [{$senderDisplayName}] vừa gửi khiếu nại {$ticketCode} về bài đăng \"{$reportedPostTitle}\". Vui lòng tiếp nhận và xử lý.",
                (string)($ticket->_id ?? $ticket->id),
                (string)$request->post_id,
                'warning'
            );

            // 4. Return response: Hỗ trợ cả AJAX JSON và Redirect Back
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.',
                    'ticket_code' => $ticketCode,
                ]);
            }

            return redirect()->back()->with('success', 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi lưu khiếu nại bài đăng: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra trong quá trình gửi phản ánh. Vui lòng thử lại sau.'
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình gửi phản ánh. Vui lòng thử lại sau.');
        }
    }
}

