<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\Complaint;
use App\Models\Post;
use App\Models\AdminActivity;

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
     * Màn hình Tổng quan CSKH (Biểu đồ khiếu nại & Thống kê KPI)
     * Route: GET /cskh/tong-quan
     */
    public function overview(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isCskh() && !$currentUser->isAdmin() && $currentUser->role !== 'admin' && !$currentUser->isModerator())) {
            return redirect('/login')->with('error', 'Chỉ tài khoản Chăm Sóc Khách Hàng mới có quyền truy cập trang Tổng quan CSKH.');
        }

        // Tự động đồng bộ các thông báo ban đầu nếu chưa có
        $this->ensureInitialNotificationsExist();

        // 1. Thống kê KPI tổng thể
        $totalComplaints = SupportTicket::where('type', 'khieu_nai')->count() + Complaint::count();

        // Đơn chờ xử lý cấp 1 (Pending tại CSKH)
        $pendingCount = SupportTicket::where('type', 'khieu_nai')
            ->whereNotIn('status', ['resolved', 'rejected', 'da_giai_quyet', 'tu_choi'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count()
            + Complaint::whereNotIn('status', ['resolved', 'rejected', 'da_giai_quyet', 'tu_choi'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count();

        // Đơn đã chuyển tiếp lên Admin cấp 2 (Trình Admin / Đẩy lên Admin)
        $escalatedCount = SupportTicket::where('type', 'khieu_nai')
            ->where(function($q) {
                $q->where('is_escalated', true)->orWhere('status', 'escalated');
            })
            ->count()
            + Complaint::where(function($q) {
                $q->where('is_escalated', true)->orWhere('status', 'escalated');
            })
            ->count();

        // Đơn do CSKH tự giải quyết (Không leo thang lên Admin)
        $cskhResolvedCount = SupportTicket::where('type', 'khieu_nai')
            ->whereIn('status', ['resolved', 'da_giai_quyet', 'closed'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count()
            + Complaint::whereIn('status', ['resolved', 'da_giai_quyet', 'closed'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count();

        // Đơn đã giải quyết thành công (Tổng thể cả CSKH & Admin)
        $resolvedCount = SupportTicket::where('type', 'khieu_nai')
            ->whereIn('status', ['resolved', 'da_giai_quyet', 'closed'])
            ->count()
            + Complaint::whereIn('status', ['resolved', 'da_giai_quyet', 'closed'])
            ->count();

        // Đơn đã bác bỏ / từ chối (Rejected)
        $rejectedCount = SupportTicket::where('type', 'khieu_nai')
            ->whereIn('status', ['rejected', 'tu_choi', 'cancelled'])
            ->count()
            + Complaint::whereIn('status', ['rejected', 'tu_choi', 'cancelled'])
            ->count();

        // Tỷ lệ giải quyết
        $resolutionRate = $totalComplaints > 0 ? round(($resolvedCount / $totalComplaints) * 100, 1) : 100;

        // 2. Dữ liệu biểu đồ 7 ngày gần nhất
        $chartDays = [];
        $incomingData = [];
        $resolvedData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();
            $dayLabel = $date->format('d/m');

            $chartDays[] = $dayLabel;

            $inCount = SupportTicket::where('type', 'khieu_nai')
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->count()
                + Complaint::whereBetween('created_at', [$dateStart, $dateEnd])->count();

            $resCount = SupportTicket::where('type', 'khieu_nai')
                ->whereBetween('resolved_at', [$dateStart, $dateEnd])
                ->count()
                + Complaint::whereBetween('updated_at', [$dateStart, $dateEnd])
                    ->whereIn('status', ['resolved', 'da_giai_quyet'])
                    ->count();

            $incomingData[] = $inCount;
            $resolvedData[] = $resCount;
        }

        // 3. Phân loại theo lý do vi phạm phổ biến
        $allTickets = SupportTicket::where('type', 'khieu_nai')->get(['reason', 'message', 'content', 'status']);
        $allComplaints = Complaint::all(['reason', 'content', 'status']);

        $reasonCategories = [
            'sai_lech' => ['label' => 'Sai lệch giá / hình ảnh', 'count' => 0, 'color' => '#f59e0b'],
            'da_thue' => ['label' => 'Đã cho thuê nhưng vẫn đăng', 'count' => 0, 'color' => '#0284c7'],
            'khong_lien_he' => ['label' => 'Không liên hệ được chủ phòng', 'count' => 0, 'color' => '#8b5cf6'],
            'lua_dao' => ['label' => 'Dấu hiệu lừa đảo / Đặt cọc', 'count' => 0, 'color' => '#ef4444'],
            'phan_anh_khac' => ['label' => 'Phản ánh bài đăng / Khác', 'count' => 0, 'color' => '#10b981'],
        ];

        foreach ($allTickets->concat($allComplaints) as $item) {
            $r = mb_strtolower(($item->reason ?? '') . ' ' . ($item->message ?? '') . ' ' . ($item->content ?? ''));
            if (str_contains($r, 'giá') || str_contains($r, 'ảnh') || str_contains($r, 'hình') || str_contains($r, 'sai lệch')) {
                $reasonCategories['sai_lech']['count']++;
            } elseif (str_contains($r, 'đã cho thuê') || str_contains($r, 'hết phòng') || str_contains($r, 'trùng')) {
                $reasonCategories['da_thue']['count']++;
            } elseif (str_contains($r, 'không liên') || str_contains($r, 'không nghe') || str_contains($r, 'thuê bao')) {
                $reasonCategories['khong_lien_he']['count']++;
            } elseif (str_contains($r, 'lừa') || str_contains($r, 'cọc') || str_contains($r, 'tiền')) {
                $reasonCategories['lua_dao']['count']++;
            } else {
                $reasonCategories['phan_anh_khac']['count']++;
            }
        }

        // 4. Danh sách khiếu nại mới nhất / khẩn cấp
        $recentTickets = SupportTicket::where('type', 'khieu_nai')
            ->with(['post', 'staff'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 5. Số thông báo chưa đọc cho Badge Sidebar
        $userId = (string)($currentUser->_id ?? $currentUser->id);
        $unreadCount = Notification::where(function ($q) use ($userId) {
            $q->where('role', 'cskh')
              ->orWhere('role', 'cham_soc_kh')
              ->orWhere('role', 'chamsockh')
              ->orWhere('user_id', $userId);
        })->where('is_read', false)->count();

        return view('cham_soc_kh.tong_quan', compact(
            'currentUser',
            'totalComplaints',
            'pendingCount',
            'escalatedCount',
            'cskhResolvedCount',
            'resolvedCount',
            'rejectedCount',
            'resolutionRate',
            'chartDays',
            'incomingData',
            'resolvedData',
            'reasonCategories',
            'recentTickets',
            'unreadCount'
        ));
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

    /**
     * Màn hình Lịch sử hoạt động của tài khoản Chăm Sóc Khách Hàng
     * Route: GET /cskh/lich-su (name: cskh.history)
     */
    public function history(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isCskh() && !$currentUser->isAdmin() && $currentUser->role !== 'admin' && !$currentUser->isModerator())) {
            return redirect('/login')->with('error', 'Chỉ tài khoản Chăm Sóc Khách Hàng mới có quyền truy cập trang Lịch sử hoạt động.');
        }

        $userId = (string)($currentUser->_id ?? $currentUser->id);

        // Đảm bảo các hoạt động cơ bản được đồng bộ
        $this->ensureInitialActivitiesExist($currentUser);

        // 1. Base Query: Tìm các hoạt động của CSKH hoặc liên quan đến khiếu nại / liên hệ
        $query = AdminActivity::where(function($q) use ($userId) {
            $q->where('admin_id', $userId)
              ->orWhere('category', 'cskh')
              ->orWhereIn('category', ['complaint', 'support_ticket', 'contact'])
              ->orWhere('admin_name', 'LIKE', '%Chăm Sóc%')
              ->orWhere('admin_name', 'LIKE', '%CSKH%');
        });

        // 2. Bộ lọc tìm kiếm từ khóa
        $search = trim((string)$request->input('search', ''));
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('target_name', 'LIKE', "%{$search}%")
                  ->orWhere('target_id', 'LIKE', "%{$search}%")
                  ->orWhere('admin_name', 'LIKE', "%{$search}%");
            });
        }

        // 3. Bộ lọc loại hành động
        $actionType = $request->input('action_type', 'all');
        if ($actionType && $actionType !== 'all') {
            if ($actionType === 'escalate') {
                $query->whereIn('action_type', ['escalate', 'escalate_ticket']);
            } elseif ($actionType === 'resolve') {
                $query->whereIn('action_type', ['resolve', 'resolve_contact', 'approve']);
            } elseif ($actionType === 'reply') {
                $query->whereIn('action_type', ['reply', 'message']);
            } elseif ($actionType === 'warning') {
                $query->whereIn('action_type', ['warning', 'remind_staff']);
            } elseif ($actionType === 'reject') {
                $query->whereIn('action_type', ['reject', 'reject_complaint', 'dismiss']);
            } else {
                $query->where('action_type', $actionType);
            }
        }

        // 4. Bộ lọc thời gian
        $period = $request->input('period', 'all');
        if ($period === 'today') {
            $query->where('created_at', '>=', now()->startOfDay());
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', now()->subDays(7)->startOfDay());
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', now()->subDays(30)->startOfDay());
        }

        // 5. Thống kê nhanh cho các thẻ KPI trên trang Lịch sử
        $statsBase = AdminActivity::where(function($q) use ($userId) {
            $q->where('admin_id', $userId)
              ->orWhere('category', 'cskh')
              ->orWhereIn('category', ['complaint', 'support_ticket', 'contact'])
              ->orWhere('admin_name', 'LIKE', '%Chăm Sóc%')
              ->orWhere('admin_name', 'LIKE', '%CSKH%');
        });

        $totalActivities = (clone $statsBase)->count();
        $escalatedCount = (clone $statsBase)->whereIn('action_type', ['escalate', 'escalate_ticket'])->count();
        $resolvedCount = (clone $statsBase)->whereIn('action_type', ['resolve', 'resolve_contact', 'approve'])->count();
        $repliedCount = (clone $statsBase)->whereIn('action_type', ['reply', 'message'])->count();

        // 6. Phân trang dữ liệu
        $activities = $query->orderBy('created_at', 'desc')->paginate(12)->appends($request->all());

        // 7. Số liệu Badge cho Sidebar
        $pendingCount = SupportTicket::where('type', 'khieu_nai')
            ->whereNotIn('status', ['resolved', 'rejected', 'da_giai_quyet', 'tu_choi'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count()
            + Complaint::whereNotIn('status', ['resolved', 'rejected', 'da_giai_quyet', 'tu_choi'])
            ->where(function($q) {
                $q->whereNull('is_escalated')->orWhere('is_escalated', false);
            })
            ->where('status', '!=', 'escalated')
            ->count();

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

        return view('cham_soc_kh.lich_su', compact(
            'currentUser',
            'activities',
            'totalActivities',
            'escalatedCount',
            'resolvedCount',
            'repliedCount',
            'pendingCount',
            'unreadCount',
            'search',
            'actionType',
            'period'
        ));
    }

    /**
     * Tự động khởi tạo dữ liệu hoạt động CSKH ban đầu nếu chưa có
     */
    protected function ensureInitialActivitiesExist($currentUser)
    {
        $hasCskhLogs = AdminActivity::where('category', 'cskh')->orWhere('action_type', 'escalate')->exists();
        if ($hasCskhLogs) {
            return;
        }

        $cskhName = $currentUser->account_name ?: ($currentUser->username ?: 'Chăm Sóc Khách Hàng');
        $cskhEmail = $currentUser->email ?: 'chamsockh@gmail.com';
        $cskhId = (string)($currentUser->_id ?? $currentUser->id);

        $tickets = SupportTicket::where('type', 'khieu_nai')->get();
        foreach ($tickets as $t) {
            $code = $t->ticket_code ?? ('#KN-' . strtoupper(substr((string)$t->_id, -5)));
            if ($t->is_escalated) {
                AdminActivity::create([
                    'admin_id'    => $cskhId,
                    'admin_name'  => $cskhName,
                    'admin_email' => $cskhEmail,
                    'action_type' => 'escalate',
                    'category'    => 'complaint',
                    'title'       => 'Trình hồ sơ khiếu nại lên Ban Quản Trị (Cấp 2)',
                    'description' => "CSKH đã xác minh và chuyển tiếp hồ sơ {$code} lên Ban Quản Trị Cấp 2 xử lý. Lý do: {$t->subject} - Khách thuê phản ánh cần thẩm tra đối soát.",
                    'target_id'   => (string)$t->_id,
                    'target_name' => $code,
                    'ip_address'  => request()->ip() ?: '127.0.0.1',
                    'user_agent'  => 'Cốc Cốc trên Windows',
                    'status'      => 'warning',
                    'metadata'    => ['code' => $code, 'type' => 'khieu_nai'],
                    'created_at'  => $t->created_at ?? now(),
                    'updated_at'  => $t->created_at ?? now(),
                ]);
            }
        }
    }
}
