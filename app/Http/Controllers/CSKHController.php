<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\Complaint;
use App\Models\Post;

/**
 * CSKHController - Controller chuyên trách cho Kênh Chăm Sóc Khách Hàng (CSKH)
 * Kế thừa toàn bộ logic xử lý Khiếu nại, Liên hệ, Chat Slide-over và Notifications từ ComplaintController.
 */
class CSKHController extends ComplaintController
{
    /**
     * Helper gửi thông báo trực tiếp tới kênh CSKH
     */
    public static function notifyCskh($category, $title, $message, $targetId = null, $postId = null, $type = 'info')
    {
        $actionUrl = $targetId ? url('/cskh/khieu-nai?ticket_id=' . $targetId) : url('/cskh/khieu-nai');

        return Notification::create([
            'user_id'    => null,
            'role'       => 'cskh',
            'category'   => $category,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'target_id'  => $targetId ? (string)$targetId : null,
            'post_id'    => $postId ? (string)$postId : null,
            'action_url' => $actionUrl,
            'is_read'    => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Màn hình Trung tâm Thông báo CSKH
     * Route: GET /cskh/thong-bao
     */
    public function notifications(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isCskh() && !$currentUser->isAdmin() && $currentUser->role !== 'admin')) {
            return redirect('/login')->with('error', 'Chỉ tài khoản Chăm Sóc Khách Hàng mới có quyền truy cập trang Thông báo CSKH.');
        }

        $userId = (string)($currentUser->_id ?? $currentUser->id);

        // Tự động đồng bộ các khiếu nại hiện có thành thông báo nếu chưa có thông báo nào trong hệ thống
        $this->ensureInitialNotificationsExist();

        // Truy vấn tất cả thông báo thuộc kênh CSKH
        $baseQuery = Notification::where(function ($q) use ($userId) {
            $q->where('role', 'cskh')
              ->orWhere('role', 'cham_soc_kh')
              ->orWhere('role', 'chamsockh')
              ->orWhere('user_id', $userId)
              ->orWhereIn('category', [
                  'new_complaint',
                  'admin_verdict',
                  'admin_rejected',
                  'admin_sanction',
                  'customer_reply',
                  'complaint_escalated'
              ]);
        });

        // Thống kê số lượng theo KPI
        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->where('is_read', false)->count();
        $newComplaintCount = (clone $baseQuery)->where('category', 'new_complaint')->count();
        $adminVerdictCount = (clone $baseQuery)->whereIn('category', ['admin_verdict', 'admin_rejected', 'admin_sanction'])->count();
        $customerReplyCount = (clone $baseQuery)->where('category', 'customer_reply')->count();

        // Lọc theo Tab
        $tab = $request->get('tab', 'all');
        $query = clone $baseQuery;
        if ($tab === 'unread') {
            $query->where('is_read', false);
        } elseif ($tab === 'new_complaint') {
            $query->where('category', 'new_complaint');
        } elseif ($tab === 'admin_verdict') {
            $query->whereIn('category', ['admin_verdict', 'admin_rejected', 'admin_sanction']);
        } elseif ($tab === 'customer_reply') {
            $query->where('category', 'customer_reply');
        }

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $kw = trim($request->get('keyword'));
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('message', 'like', "%{$kw}%");
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Thống kê hồ sơ khiếu nại chờ xử lý để hiển thị badge trên sidebar
        $pendingComplaints = SupportTicket::where('type', 'khieu_nai')
            ->where('status', '!=', 'resolved')
            ->where(function ($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->count();

        return view('cham_soc_kh.thong_bao', compact(
            'currentUser',
            'notifications',
            'totalCount',
            'unreadCount',
            'newComplaintCount',
            'adminVerdictCount',
            'customerReplyCount',
            'pendingComplaints',
            'tab'
        ));
    }

    /**
     * Đảm bảo nếu chưa có thông báo nào thì tự động tạo từ các khiếu nại hiện có
     */
    private function ensureInitialNotificationsExist()
    {
        $count = Notification::where('role', 'cskh')->count();
        if ($count === 0) {
            $tickets = SupportTicket::where('type', 'khieu_nai')->latest()->take(6)->get();
            foreach ($tickets as $t) {
                $postId = $t->post_id ?? ($t->target_id ?? null);
                $post = $t->post ?: ($postId ? Post::find($postId) : null);
                $postTitle = $post ? $post->title : 'Bài đăng';
                $senderName = $t->sender_name ?? 'Khách thuê';
                $code = $t->formatted_ticket_code;
                $tId = (string)($t->_id ?? $t->id);

                if ($t->status === 'resolved') {
                    self::notifyCskh(
                        'admin_verdict',
                        'Admin đã phán quyết hồ sơ ' . $code,
                        "Hồ sơ khiếu nại {$code} (bài đăng [{$postTitle}]) đã được Admin hoàn tất xử lý và ban hành chỉ thị điều hành.",
                        $tId,
                        $postId,
                        'success'
                    );
                } elseif (!empty($t->is_escalated)) {
                    self::notifyCskh(
                        'complaint_escalated',
                        'Hồ sơ đã được leo thang lên Admin',
                        "Hồ sơ khiếu nại {$code} đối với tin [{$postTitle}] đã được chuyển lên Ban Quản Trị Cấp 2.",
                        $tId,
                        $postId,
                        'info'
                    );
                } else {
                    self::notifyCskh(
                        'new_complaint',
                        'Khiếu nại mới từ khách thuê',
                        "Khách hàng [{$senderName}] vừa gửi khiếu nại {$code} về bài đăng \"{$postTitle}\". Vui lòng tiếp nhận và đối soát.",
                        $tId,
                        $postId,
                        'warning'
                    );
                }
            }
        }
    }

    /**
     * API: Đánh dấu 1 thông báo là đã đọc
     * Route: POST /cskh/notifications/{id}/read
     */
    public function markNotificationAsRead($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $notif = Notification::where('_id', $id)->first() ?: Notification::find($id);
        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
        }

        $notif->is_read = true;
        $notif->read_at = now();
        $notif->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo là đã đọc.'
        ]);
    }

    /**
     * API: Đánh dấu tất cả thông báo CSKH là đã đọc
     * Route: POST /cskh/notifications/mark-all-read
     */
    public function markAllNotificationsAsRead()
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $userId = (string)($currentUser->_id ?? $currentUser->id);

        Notification::where(function ($q) use ($userId) {
            $q->where('role', 'cskh')
              ->orWhere('role', 'cham_soc_kh')
              ->orWhere('role', 'chamsockh')
              ->orWhere('user_id', $userId)
              ->orWhereIn('category', [
                  'new_complaint',
                  'admin_verdict',
                  'admin_rejected',
                  'admin_sanction',
                  'customer_reply',
                  'complaint_escalated'
              ]);
        })->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc.'
        ]);
    }

    /**
     * API: Xóa 1 thông báo
     * Route: DELETE /cskh/notifications/{id}
     */
    public function deleteNotification($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $notif = Notification::where('_id', $id)->first() ?: Notification::find($id);
        if (!$notif) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo'], 404);
        }

        $notif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo thành công!'
        ]);
    }

    /**
     * API: Xóa toàn bộ thông báo đã đọc
     * Route: DELETE /cskh/notifications/clear-read
     */
    public function clearReadNotifications()
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $userId = (string)($currentUser->_id ?? $currentUser->id);

        Notification::where(function ($q) use ($userId) {
            $q->where('role', 'cskh')
              ->orWhere('role', 'cham_soc_kh')
              ->orWhere('role', 'chamsockh')
              ->orWhere('user_id', $userId)
              ->orWhereIn('category', [
                  'new_complaint',
                  'admin_verdict',
                  'admin_rejected',
                  'admin_sanction',
                  'customer_reply',
                  'complaint_escalated'
              ]);
        })->where('is_read', true)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã dọn sạch toàn bộ thông báo đã đọc.'
        ]);
    }

    /**
     * API: Đếm số lượng thông báo chưa đọc phục vụ badge real-time
     * Route: GET /api/cskh/notifications/unread-count
     */
    public function getUnreadCount()
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['success' => false, 'count' => 0], 401);
        }

        $userId = (string)($currentUser->_id ?? $currentUser->id);

        $unreadCount = Notification::where(function ($q) use ($userId) {
            $q->where('role', 'cskh')
              ->orWhere('role', 'cham_soc_kh')
              ->orWhere('role', 'chamsockh')
              ->orWhere('user_id', $userId)
              ->orWhereIn('category', [
                  'new_complaint',
                  'admin_verdict',
                  'admin_rejected',
                  'admin_sanction',
                  'customer_reply',
                  'complaint_escalated'
              ]);
        })->where('is_read', false)->count();

        $pendingComplaints = SupportTicket::where('type', 'khieu_nai')
            ->where('status', '!=', 'resolved')
            ->where(function ($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->count();

        return response()->json([
            'success' => true,
            'unread_notifications' => $unreadCount,
            'pending_complaints' => $pendingComplaints
        ]);
    }
}
