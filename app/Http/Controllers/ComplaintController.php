<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Complaint;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminActivity;

class ComplaintController extends Controller
{
    /**
     * Xử lý giải quyết khiếu nại Cấp 1 (Dành cho Kiểm duyệt viên / Quản trị viên)
     * Endpoint: POST /api/kiem-duyet/complaints/{id}/resolve
     * Payload JSON: { "action": "remind_staff|hide_post|dismiss", "note": "..." }
     */
    public function resolve(Request $request, $id)
    {
        // 1. Kiểm tra quyền của người dùng (Kiểm duyệt viên hoặc Quản trị viên)
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác kiểm duyệt khiếu nại này.'
            ], 403);
        }

        // 2. Validate dữ liệu đầu vào
        $request->validate([
            'action' => 'required|string|in:remind_staff,hide_post,dismiss',
            'note'   => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');
        $note = trim((string)$request->input('note', ''));
        $adminId = (string)($currentUser->_id ?? $currentUser->id);

        // 3. Tìm hồ sơ khiếu nại trong database
        $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ khiếu nại trên hệ thống.'
            ], 404);
        }

        // Tìm Bài đăng (Post) liên quan (qua quan hệ post hoặc post_id/target_id)
        $post = null;
        if (method_exists($complaint, 'post') && $complaint->post) {
            $post = $complaint->post;
        } elseif (!empty($complaint->post_id)) {
            $post = Post::where('_id', $complaint->post_id)->first() ?: Post::find($complaint->post_id);
        } elseif (!empty($complaint->target_id)) {
            $post = Post::where('_id', $complaint->target_id)->first() ?: Post::find($complaint->target_id);
        }

        // Tìm Nhân viên phụ trách (Staff)
        $staff = null;
        if (method_exists($complaint, 'staff') && $complaint->staff) {
            $staff = $complaint->staff;
        } elseif (!empty($complaint->staff_id)) {
            $staff = User::where('_id', $complaint->staff_id)->first() ?: User::find($complaint->staff_id);
        } elseif ($post) {
            $staffId = $post->staff_id ?? $post->user_id ?? $post->author_id;
            if ($staffId) {
                $staff = User::where('_id', $staffId)->first() ?: User::find($staffId);
            }
        }

        // Tìm Người gửi khiếu nại (Sender)
        $sender = null;
        if (method_exists($complaint, 'sender') && $complaint->sender) {
            $sender = $complaint->sender;
        } elseif (method_exists($complaint, 'user') && $complaint->user) {
            $sender = $complaint->user;
        } elseif (!empty($complaint->user_id)) {
            $sender = User::where('_id', $complaint->user_id)->first() ?: User::find($complaint->user_id);
        }

        $postTitle = $post ? ($post->title ?? 'Bài đăng phòng') : ($complaint->post_title ?? 'Bài đăng');
        $complaintCode = $complaint->complaint_code ?? ('KN-' . substr((string)($complaint->_id ?? $complaint->id), -6));

        // 4. Sử dụng DB::transaction để đảm bảo an toàn & toàn vẹn dữ liệu
        try {
            DB::transaction(function () use ($complaint, $post, $staff, $sender, $action, $note, $adminId, $postTitle, $complaintCode) {
                $now = now();

                switch ($action) {
                    // ========================================================
                    // NHÁNH 1: Nhắc nhở nhân viên (remind_staff)
                    // ========================================================
                    case 'remind_staff':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'remind_staff';
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Gửi Notification trong hệ thống cho Nhân viên phụ trách (Staff)
                        $staffId = $staff ? (string)($staff->_id ?? $staff->id) : ($post ? (string)($post->staff_id ?? $post->user_id) : null);
                        if ($staffId) {
                            $reminderMessage = "Bài đăng [{$postTitle}] nhận được phản ánh khiếu nại từ khách hàng. " .
                                               ($note ? "Ghi chú kiểm duyệt: {$note}. " : "") .
                                               "Vui lòng kiểm tra và cập nhật lại thông tin bài đăng.";

                            Notification::create([
                                'user_id'    => $staffId,
                                'role'       => 'nhanvien',
                                'category'   => 'complaint_remind',
                                'type'       => 'warning',
                                'title'      => 'Yêu cầu kiểm tra & cập nhật lại bài đăng',
                                'message'    => $reminderMessage,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => $post ? url('/chitietbaidang/' . ($post->_id ?? $post->id)) : url('/Overview?tab=posts'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 3. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'warning',
                            'complaint',
                            'Nhắc nhở nhân viên sửa bài vi phạm',
                            "Kiểm duyệt viên đã tiếp nhận khiếu nại #{$complaintCode} và gửi thông báo yêu cầu nhân viên phụ trách kiểm tra, sửa bài [{$postTitle}]. Ghi chú: {$note}",
                            $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'remind_staff',
                                'post_id'      => $post ? (string)($post->_id ?? $post->id) : null,
                                'staff_id'     => $staffId,
                                'note'         => $note,
                            ]
                        );
                        break;

                    // ========================================================
                    // NHÁNH 2: Gỡ bài vi phạm (hide_post)
                    // ========================================================
                    case 'hide_post':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'hide_post';
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Cập nhật Bài đăng (Post) liên quan sang 'rejected' và ẩn khỏi Home
                        if ($post) {
                            $post->status = 'rejected';
                            $post->reject_reason = 'Kiểm duyệt viên đã gỡ bài do khiếu nại: ' . ($note ?: 'Nội dung vi phạm tiêu chuẩn đăng tin');
                            $post->rejected_at = $now;
                            $post->rejected_by = $adminId;
                            $post->save();
                        }

                        // 3. Gửi Notification cảnh báo vi phạm cho Nhân viên phụ trách (Staff)
                        $staffId = $staff ? (string)($staff->_id ?? $staff->id) : ($post ? (string)($post->staff_id ?? $post->user_id) : null);
                        if ($staffId) {
                            $takedownMessage = "Bài đăng [{$postTitle}] đã bị gỡ khỏi Trang chủ và chuyển sang trạng thái Từ chối (Rejected) do vi phạm quy chuẩn. " .
                                               ($note ? "Lý do kiểm duyệt: {$note}" : "");

                            Notification::create([
                                'user_id'    => $staffId,
                                'role'       => 'nhanvien',
                                'category'   => 'post_rejected',
                                'type'       => 'error',
                                'title'      => 'Bài đăng đã bị gỡ do vi phạm',
                                'message'    => $takedownMessage,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => url('/Overview?tab=rejected'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 4. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'reject',
                            'complaint',
                            'Gỡ bài vi phạm do khiếu nại',
                            "Kiểm duyệt viên đã xử lý khiếu nại #{$complaintCode}: Gỡ bài đăng [{$postTitle}], chuyển trạng thái sang [rejected]. Lý do: {$note}",
                            $post ?? $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'hide_post',
                                'post_id'      => $post ? (string)($post->_id ?? $post->id) : null,
                                'staff_id'     => $staffId,
                                'note'         => $note,
                            ]
                        );
                        break;

                    // ========================================================
                    // NHÁNH 3: Bác đơn khiếu nại (dismiss)
                    // ========================================================
                    case 'dismiss':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'dismiss';
                        $complaint->is_valid = false; // Ghi nhận đơn không chính xác / sai sự thật
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Gửi Notification phản hồi lại cho Người gửi khiếu nại (Sender)
                        $senderId = $sender ? (string)($sender->_id ?? $sender->id) : (string)($complaint->user_id ?? '');
                        if ($senderId) {
                            $dismissMessage = "Đơn khiếu nại mã [{$complaintCode}] của bạn đã được BQT thẩm định. " .
                                              "Sau khi đối soát thông tin thực tế, hệ thống xác nhận bài đăng không có dấu hiệu vi phạm theo nội dung phản ánh. " .
                                              ($note ? "Ghi chú phản hồi: {$note}" : "Cảm ơn bạn đã đồng hành cùng RentHome.");

                            Notification::create([
                                'user_id'    => $senderId,
                                'role'       => 'khachhang',
                                'category'   => 'complaint_dismissed',
                                'type'       => 'info',
                                'title'      => 'Thông báo kết quả thẩm định khiếu nại',
                                'message'    => $dismissMessage,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => url('/thong-bao'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 3. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'dismiss',
                            'complaint',
                            'Bác đơn khiếu nại sai sự thật',
                            "Kiểm duyệt viên đã bác đơn khiếu nại #{$complaintCode} do phản ánh không chính xác / sai sự thật. Ghi chú: {$note}",
                            $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'dismiss',
                                'sender_id'    => $senderId,
                                'note'         => $note,
                            ]
                        );
                        break;
                }
            });

            $actionMessages = [
                'remind_staff' => 'Đã xử lý khiếu nại: Gửi nhắc nhở yêu cầu nhân viên sửa bài thành công!',
                'hide_post'    => 'Đã xử lý khiếu nại: Gỡ bài vi phạm (chuyển sang Rejected & ẩn khỏi Home) thành công!',
                'dismiss'      => 'Đã xử lý khiếu nại: Bác đơn (ghi nhận đơn sai sự thật) & phản hồi người gửi thành công!'
            ];

            return response()->json([
                'success' => true,
                'message' => $actionMessages[$action] ?? 'Đã lưu kết quả xử lý khiếu nại thành công!',
                'complaint' => $complaint
            ]);

        } catch (\Throwable $e) {
            Log::error("Lỗi khi xử lý khiếu nại ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý khiếu nại: ' . $e->getMessage()
            ], 500);
        }
    }
}
