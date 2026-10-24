<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trung Tâm Thông Báo - Kiểm Duyệt Viên RentHome</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --bg-body: #f4f7fb;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            padding: 20px 16px;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 18px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: #0f172a;
            color: #38bdf8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .brand-info h2 {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-badge {
            font-size: 10px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .brand-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .nav-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
        }

        .nav-link.active .nav-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .badge-warning-pill { background: var(--warning-light); color: #b45309; }
        .badge-danger-pill { background: var(--danger-light); color: #b91c1c; }
        .badge-primary-pill { background: var(--primary-light); color: var(--primary); }

        .user-footer {
            margin-top: auto;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-footer-left {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-meta h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta p {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
            padding: 6px;
            border-radius: 8px;
            transition: 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: var(--danger-light);
        }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px 32px;
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100vw - var(--sidebar-width));
            min-height: 100vh;
            box-sizing: border-box;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title-box h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title-box h1 i {
            color: var(--primary);
        }

        .page-title-box p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-outline {
            background: #fff;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        /* ================= KPI CARDS ================= */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            cursor: pointer;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .kpi-val {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .kpi-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .kpi-icon.blue { background: var(--primary-light); color: var(--primary); }
        .kpi-icon.amber { background: var(--warning-light); color: #b45309; }
        .kpi-icon.red { background: var(--danger-light); color: var(--danger); }

        /* ================= FILTER TOOLBAR ================= */
        .filter-toolbar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
        }

        .tab-btn {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--text-main);
            background: #f1f5f9;
        }

        .tab-btn.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .search-box {
            position: relative;
            min-width: 240px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .search-input {
            width: 100%;
            padding: 8px 14px 8px 34px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            font-size: 13px;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* ================= NOTIFICATION LIST ================= */
        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .notif-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.25s ease;
            position: relative;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .notif-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .notif-card.unread {
            background: #ffffff;
            border-left: 4px solid var(--primary);
        }

        .notif-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .notif-icon-box.post { background: var(--primary-light); color: var(--primary); }
        .notif-icon-box.complaint { background: var(--danger-light); color: var(--danger); }
        .notif-icon-box.system { background: var(--purple-light); color: var(--purple); }

        .notif-content {
            flex: 1;
            min-width: 0;
        }

        .notif-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .notif-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .notif-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text-main);
        }

        .notif-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .notif-badge.post { background: #e0f2fe; color: #0284c7; }
        .notif-badge.complaint { background: #fee2e2; color: #dc2626; }
        .notif-badge.system { background: #ede9fe; color: #7c3aed; }

        .notif-time {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .notif-message {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .notif-meta-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px dashed #f1f5f9;
        }

        .notif-sender {
            font-size: 12.5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .notif-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .btn-act {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-act-primary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .btn-act-primary:hover {
            background: var(--primary);
            color: #fff;
        }

        .btn-act-light {
            background: #f8fafc;
            border-color: var(--border-color);
            color: #64748b;
        }

        .btn-act-light:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            display: inline-block;
            margin-left: 4px;
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 48px 24px;
            text-align: center;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            background: #f1f5f9;
            color: #94a3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 16px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .empty-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            max-width: 420px;
            margin: 0 auto;
        }

        /* Toast notification */
        .toast-msg {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .toast-msg.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    @php
        $userObj = Auth::user();
        $uId = (string)($userObj->_id ?? $userObj->id ?? '');

        // Số lượng huy hiệu thực tế cho Sidebar
        $pendingBadge = $pendingPostsBadge ?? \App\Models\Post::where('status', 'pending')->count();
        $complaintBadge = $pendingComplaints ?? \App\Models\Complaint::where('status', 'pending')->count();

        // Tự động truy vấn MongoDB nếu controller chưa truyền vào (đảm bảo luôn có dữ liệu thật 100%)
        if (!isset($pendingPosts)) {
            $pendingPosts = \App\Models\Post::where('status', 'pending')->with(['user', 'building'])->orderBy('created_at', 'desc')->get();
        }
        if (!isset($pendingComplaintsList)) {
            $pendingComplaintsList = \App\Models\Complaint::where('status', 'pending')->with('user')->orderBy('created_at', 'desc')->get();
        }
        if (!isset($dbNotifications)) {
            $dbNotifications = \App\Models\Notification::where(function($q) use ($uId) {
                $q->where('role', 'kiemduyet')
                  ->orWhere('user_id', $uId)
                  ->orWhere('category', 'moderator')
                  ->orWhere('category', 'moderation');
            })->where('type', '!=', 'dismissed')->orderBy('created_at', 'desc')->get();
        }
        if (!isset($readPostIds) || !isset($dismissedPostIds)) {
            $readMarkers = \App\Models\Notification::where('user_id', $uId)
                ->whereIn('category', ['post_moderation_read', 'complaint_read', 'post_dismissed', 'complaint_dismissed'])
                ->get();
            $dismissedPostIds = $dismissedPostIds ?? $readMarkers->where('category', 'post_dismissed')->pluck('post_id')->filter()->map('strval')->toArray();
            $dismissedComplaintIds = $dismissedComplaintIds ?? $readMarkers->where('category', 'complaint_dismissed')->pluck('action_url')->filter()->map('strval')->toArray();
            $readPostIds = $readPostIds ?? $readMarkers->where('category', 'post_moderation_read')->pluck('post_id')->filter()->map('strval')->toArray();
            $readComplaintIds = $readComplaintIds ?? $readMarkers->where('category', 'complaint_read')->pluck('action_url')->filter()->map('strval')->toArray();
        }

        // Chuẩn bị danh sách thông báo thực tế tổng hợp từ MongoDB
        $notifList = [];

        // 1. Thêm các bài đăng chờ duyệt thực tế từ collection posts
        if (isset($pendingPosts) && count($pendingPosts) > 0) {
            foreach($pendingPosts as $post) {
                $pId = (string)($post->_id ?? $post->id);
                if (in_array($pId, $dismissedPostIds ?? [])) continue;
                $isRead = in_array($pId, $readPostIds ?? []);
                $posterName = $post->user ? ($post->user->account_name ?? ($post->user->username ?? 'Chủ phòng')) : 'Người dùng';
                $buildingName = $post->building ? ($post->building->name ?? '') : '';
                $pPrice = !empty($post->price) ? number_format($post->price) . ' đ/tháng' : 'Thỏa thuận';
                
                $notifList[] = [
                    'id'          => 'post_' . $pId,
                    'type'        => 'post',
                    'title'       => 'Tin đăng mới cần kiểm duyệt',
                    'badge'       => 'Tin mới gửi',
                    'badge_class' => 'post',
                    'sender'      => $posterName,
                    'message'     => 'Bài đăng "' . ($post->title ?? 'Phòng cho thuê') . '"' . ($buildingName ? ' thuộc tòa nhà ' . $buildingName : '') . ' (Giá: ' . $pPrice . ') đang chờ bạn duyệt nội dung.',
                    'time'        => $post->created_at ? $post->created_at->format('d/m/Y H:i') : 'Mới đây',
                    'is_unread'   => !$isRead,
                    'action_url'  => url('/kiem-duyet/qlkiem-duyet?post_id=' . $pId),
                    'action_text' => 'Duyệt bài ngay',
                ];
            }
        }

        // 2. Thêm danh sách đơn khiếu nại chờ xử lý thực tế từ collection complaints
        if (isset($pendingComplaintsList) && count($pendingComplaintsList) > 0) {
            foreach($pendingComplaintsList as $comp) {
                $cId = (string)($comp->_id ?? $comp->id);
                if (in_array($cId, $dismissedComplaintIds ?? [])) continue;
                $isRead = in_array($cId, $readComplaintIds ?? []);
                $complainantName = $comp->user ? ($comp->user->account_name ?? ($comp->user->username ?? 'Khách hàng')) : 'Người dùng';
                $code = $comp->complaint_code ?: ('KN-' . substr($cId, -6));
                
                $notifList[] = [
                    'id'          => 'complaint_' . $cId,
                    'type'        => 'complaint',
                    'title'       => 'Khiếu nại mới cần xử lý #' . $code,
                    'badge'       => 'Khiếu nại',
                    'badge_class' => 'complaint',
                    'sender'      => $complainantName,
                    'message'     => $comp->content ?: 'Khách hàng vừa gửi đơn khiếu nại/phản ánh về bài đăng hoặc vi phạm quy định.',
                    'time'        => $comp->created_at ? $comp->created_at->format('d/m/Y H:i') : 'Mới đây',
                    'is_unread'   => !$isRead,
                    'action_url'  => url('/kiem-duyet/khieu-nai?id=' . $cId),
                    'action_text' => 'Xem khiếu nại',
                ];
            }
        }

        // 3. Thêm thông báo từ collection notifications (dành cho kiểm duyệt viên)
        if (isset($dbNotifications) && count($dbNotifications) > 0) {
            foreach($dbNotifications as $dbN) {
                if ($dbN->type === 'dismissed') continue;
                $nId = (string)($dbN->_id ?? $dbN->id);
                $cat = strtolower($dbN->category ?? 'system');
                $type = 'system';
                $badgeClass = 'system';
                $badgeText = 'Hệ thống';

                if (str_contains($cat, 'complaint') || str_contains($cat, 'khieu_nai')) {
                    $type = 'complaint';
                    $badgeClass = 'complaint';
                    $badgeText = 'Khiếu nại';
                } elseif (str_contains($cat, 'post') || str_contains($cat, 'bai_dang')) {
                    $type = 'post';
                    $badgeClass = 'post';
                    $badgeText = 'Tin đăng';
                }

                $notifList[] = [
                    'id'          => 'db_' . $nId,
                    'type'        => $type,
                    'title'       => $dbN->title ?: 'Thông báo hệ thống',
                    'badge'       => $badgeText,
                    'badge_class' => $badgeClass,
                    'sender'      => 'Ban Quản Trị RentHome',
                    'message'     => $dbN->message ?: 'Bạn có thông báo mới trong quy trình kiểm duyệt.',
                    'time'        => $dbN->created_at ? $dbN->created_at->format('d/m/Y H:i') : 'Mới đây',
                    'is_unread'   => empty($dbN->is_read),
                    'action_url'  => $dbN->action_url ?: url('/kiem-duyet/qlkiem-duyet'),
                    'action_text' => 'Xem chi tiết',
                ];
            }
        }

        $totalNotifs = count($notifList);
        $unreadCount = count(array_filter($notifList, fn($n) => !empty($n['is_unread'])));
        $postNotifCount = count(array_filter($notifList, fn($n) => $n['type'] === 'post'));
        $complaintNotifCount = count(array_filter($notifList, fn($n) => $n['type'] === 'complaint'));
    @endphp

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">Kiểm Duyệt</span></h2>
                <p>Hệ thống kiểm duyệt nội bộ</p>
            </div>
        </div>

        <div class="nav-section-title">Nghiệp Vụ Kiểm Duyệt</div>
        <ul class="nav-list">
            <li>
                <a href="{{ url('/kiem-duyet/tong-quan') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tổng quan</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/qlkiem-duyet') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Quản lý kiểm duyệt</span>
                    </span>
                    @if($pendingBadge > 0)
                        <span class="nav-badge badge-warning-pill">{{ $pendingBadge }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    @if($complaintBadge > 0)
                        <span class="nav-badge badge-danger-pill">{{ $complaintBadge }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/qltin-tuc') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Quản lý tin tức</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/lich-su') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lịch sử duyệt</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/thong-bao') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                    <span class="nav-badge badge-primary-pill" id="sidebarNotifBadge" style="{{ $unreadCount === 0 ? 'display: none;' : '' }}">{{ $unreadCount }}</span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-footer-left">
                <div class="avatar-circle">
                    {{ strtoupper(mb_substr($userObj->account_name ?? 'KD', 0, 2)) }}
                </div>
                <div class="user-meta">
                    <h4>{{ $userObj->account_name ?? 'Kiểm Duyệt Viên' }}</h4>
                    <p>{{ $userObj->email ?? 'kiemduyet2006@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-bell"></i>
                    Trung Tâm Thông Báo Kiểm Duyệt
                </h1>
                <p>Tiếp nhận thông báo tin đăng mới gửi duyệt, phản ánh của người dùng và cập nhật hệ thống.</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-outline" onclick="markAllAsRead()">
                    <i class="fa-solid fa-check-double"></i> Đánh dấu đã đọc tất cả
                </button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                    <i class="fa-solid fa-rotate"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- 4 Thẻ KPI Tóm Tắt Đơn Giản -->
        <section class="kpi-grid">
            <div class="kpi-card" onclick="filterByTab('all')">
                <div>
                    <div class="kpi-val" id="kpiTotal">{{ $totalNotifs }}</div>
                    <div class="kpi-label">Tổng thông báo</div>
                </div>
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="filterByTab('unread')">
                <div>
                    <div class="kpi-val" style="color: #b45309;" id="kpiUnread">{{ $unreadCount }}</div>
                    <div class="kpi-label">Chưa đọc</div>
                </div>
                <div class="kpi-icon amber">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="filterByTab('post')">
                <div>
                    <div class="kpi-val" style="color: var(--primary);" id="kpiPosts">{{ $postNotifCount }}</div>
                    <div class="kpi-label">Tin đăng mới gửi</div>
                </div>
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="filterByTab('complaint')">
                <div>
                    <div class="kpi-val" style="color: var(--danger);" id="kpiComplaints">{{ $complaintNotifCount }}</div>
                    <div class="kpi-label">Khiếu nại / Cảnh báo</div>
                </div>
                <div class="kpi-icon red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </section>

        <!-- Thanh Lọc Tab & Ô Tìm Kiếm Nhanh -->
        <div class="filter-toolbar">
            <div class="filter-tabs" id="filterTabs">
                <button type="button" class="tab-btn active" onclick="switchFilterTab('all', this)">
                    Tất cả (<span id="tabCountTotal">{{ $totalNotifs }}</span>)
                </button>
                <button type="button" class="tab-btn" onclick="switchFilterTab('unread', this)">
                    Chưa đọc (<span id="tabCountUnread">{{ $unreadCount }}</span>)
                </button>
                <button type="button" class="tab-btn" onclick="switchFilterTab('post', this)">
                    Tin đăng mới (<span id="tabCountPost">{{ $postNotifCount }}</span>)
                </button>
                <button type="button" class="tab-btn" onclick="switchFilterTab('complaint', this)">
                    Khiếu nại (<span id="tabCountComplaint">{{ $complaintNotifCount }}</span>)
                </button>
                <button type="button" class="tab-btn" onclick="switchFilterTab('system', this)">
                    Hệ thống
                </button>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Tìm theo tiêu đề, người gửi..." oninput="handleSearch()">
            </div>
        </div>

        <!-- Danh Sách Các Thông Báo -->
        <div class="notification-list" id="notificationList">
            @forelse($notifList as $item)
                @php
                    $isUnread = !empty($item['is_unread']);
                    $typeClass = $item['type'] ?? 'system';
                    $icon = 'fa-circle-info';
                    if ($typeClass === 'post') $icon = 'fa-file-circle-plus';
                    elseif ($typeClass === 'complaint') $icon = 'fa-triangle-exclamation';
                    elseif ($typeClass === 'warning') $icon = 'fa-shield-halved';
                @endphp

                <div class="notif-card {{ $isUnread ? 'unread' : '' }}" 
                     data-id="{{ $item['id'] }}" 
                     data-type="{{ $typeClass }}" 
                     data-unread="{{ $isUnread ? '1' : '0' }}"
                     data-search="{{ mb_strtolower($item['title'] . ' ' . $item['message'] . ' ' . $item['sender']) }}"
                     id="notif-{{ $item['id'] }}">
                    
                    <div class="notif-icon-box {{ $typeClass }}">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>

                    <div class="notif-content">
                        <div class="notif-top">
                            <div class="notif-title-row">
                                <span class="notif-badge {{ $item['badge_class'] ?? 'system' }}">{{ $item['badge'] ?? 'Thông báo' }}</span>
                                <span class="notif-title">{{ $item['title'] }}</span>
                                @if($isUnread)
                                    <span class="unread-dot" title="Chưa đọc"></span>
                                @endif
                            </div>
                            <div class="notif-time">
                                <i class="fa-regular fa-clock"></i>
                                <span>{{ $item['time'] }}</span>
                            </div>
                        </div>

                        <p class="notif-message">
                            {{ $item['message'] }}
                        </p>

                        <div class="notif-meta-bar">
                            <div class="notif-sender">
                                <i class="fa-regular fa-user"></i>
                                <span>Gửi bởi: <b>{{ $item['sender'] }}</b></span>
                            </div>

                            <div class="notif-actions">
                                @if(!empty($item['action_url']))
                                    <a href="{{ $item['action_url'] }}" class="btn-act btn-act-primary">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        <span>{{ $item['action_text'] ?? 'Xem chi tiết' }}</span>
                                    </a>
                                @endif

                                @if($isUnread)
                                    <button type="button" class="btn-act btn-act-light btn-mark-read" onclick="toggleRead('{{ $item['id'] }}', this)" title="Đánh dấu đã đọc">
                                        <i class="fa-solid fa-check"></i> Đã đọc
                                    </button>
                                @endif

                                <button type="button" class="btn-act btn-act-light" onclick="deleteNotif('{{ $item['id'] }}')" title="Xóa thông báo">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <div class="empty-title">Không có thông báo nào</div>
                    <div class="empty-desc">Hệ thống kiểm duyệt hiện chưa có thông báo mới nào. Mọi tin đăng chờ duyệt và khiếu nại mới từ người dùng sẽ tự động xuất hiện tại đây.</div>
                </div>
            @endforelse

            <!-- Placeholder khi tìm kiếm không ra kết quả -->
            <div class="empty-state" id="noSearchResults" style="display: none;">
                <div class="empty-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="empty-title">Không tìm thấy thông báo phù hợp</div>
                <div class="empty-desc">Vui lòng thử tìm kiếm bằng từ khóa hoặc bộ lọc danh mục khác.</div>
            </div>
        </div>
    </main>

    <!-- Toast Thông Báo Nhẹ Nhàng -->
    <div id="toastMsg" class="toast-msg">
        <i class="fa-solid fa-circle-check" style="color: #4ade80;"></i>
        <span id="toastText">Thao tác thành công!</span>
    </div>

    <script>
        let currentTab = 'all';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function switchFilterTab(tabName, btnElem) {
            currentTab = tabName;
            document.querySelectorAll('#filterTabs .tab-btn').forEach(b => b.classList.remove('active'));
            if (btnElem) {
                btnElem.classList.add('active');
            }
            applyFilters();
        }

        function filterByTab(tabName) {
            const tabs = document.querySelectorAll('#filterTabs .tab-btn');
            tabs.forEach(tab => {
                const txt = tab.innerText.toLowerCase();
                if ((tabName === 'all' && txt.includes('tất cả')) ||
                   (tabName === 'unread' && txt.includes('chưa đọc')) ||
                   (tabName === 'post' && txt.includes('tin đăng')) ||
                   (tabName === 'complaint' && txt.includes('khiếu nại'))) {
                    switchFilterTab(tabName, tab);
                }
            });
        }

        function handleSearch() {
            applyFilters();
        }

        function applyFilters() {
            const searchKeyword = (document.getElementById('searchInput')?.value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.notif-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const type = card.getAttribute('data-type');
                const isUnread = card.getAttribute('data-unread') === '1';
                const searchData = card.getAttribute('data-search') || '';

                let matchTab = false;
                if (currentTab === 'all') {
                    matchTab = true;
                } else if (currentTab === 'unread') {
                    matchTab = isUnread;
                } else if (currentTab === type) {
                    matchTab = true;
                }

                const matchKeyword = !searchKeyword || searchData.includes(searchKeyword);

                if (matchTab && matchKeyword) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noResult = document.getElementById('noSearchResults');
            if (noResult) {
                noResult.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
            }
        }

        // 1. Đánh dấu 1 thông báo là đã đọc (Lưu thật vào MongoDB)
        function toggleRead(id, btn) {
            const card = document.getElementById(`notif-${id}`);
            if (card) {
                card.classList.remove('unread');
                card.setAttribute('data-unread', '0');
                const dot = card.querySelector('.unread-dot');
                if (dot) dot.remove();
                if (btn) btn.remove();

                updateUnreadBadgeCounter();
                showToast('Đang cập nhật trạng thái...');

                fetch(`/api/moderator/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Đã đánh dấu thông báo là đã đọc');
                    }
                })
                .catch(err => {
                    console.error('Lỗi đánh dấu đã đọc:', err);
                });
            }
        }

        // 2. Đánh dấu tất cả thông báo là đã đọc (Lưu thật vào MongoDB)
        function markAllAsRead() {
            const unreadCards = document.querySelectorAll('.notif-card.unread');
            if (unreadCards.length === 0) {
                showToast('Tất cả thông báo đều đã được đọc!');
                return;
            }

            unreadCards.forEach(card => {
                card.classList.remove('unread');
                card.setAttribute('data-unread', '0');
                const dot = card.querySelector('.unread-dot');
                if (dot) dot.remove();
                const readBtn = card.querySelector('.btn-mark-read');
                if (readBtn) readBtn.remove();
            });

            updateUnreadBadgeCounter();
            showToast('Đang lưu trạng thái...');

            fetch('/api/moderator/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Đã đánh dấu tất cả thông báo là đã đọc!');
                }
            })
            .catch(err => {
                console.error('Lỗi đánh dấu tất cả đã đọc:', err);
            });

            applyFilters();
        }

        // 3. Xóa thông báo (Xóa thật trong MongoDB)
        function deleteNotif(id) {
            const card = document.getElementById(`notif-${id}`);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    updateCounters();
                }, 200);

                fetch(`/api/moderator/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Đã xóa thông báo khỏi danh sách');
                    }
                })
                .catch(err => {
                    console.error('Lỗi xóa thông báo:', err);
                });
            }
        }

        function updateUnreadBadgeCounter() {
            const unreadCount = document.querySelectorAll('.notif-card[data-unread="1"]').length;
            
            const kpiUnread = document.getElementById('kpiUnread');
            if (kpiUnread) kpiUnread.innerText = unreadCount;

            const tabCountUnread = document.getElementById('tabCountUnread');
            if (tabCountUnread) tabCountUnread.innerText = unreadCount;

            const sidebarBadge = document.getElementById('sidebarNotifBadge');
            if (sidebarBadge) {
                sidebarBadge.innerText = unreadCount;
                sidebarBadge.style.display = unreadCount === 0 ? 'none' : 'inline-block';
            }
        }

        function updateCounters() {
            const total = document.querySelectorAll('.notif-card').length;
            const kpiTotal = document.getElementById('kpiTotal');
            if (kpiTotal) kpiTotal.innerText = total;

            const tabCountTotal = document.getElementById('tabCountTotal');
            if (tabCountTotal) tabCountTotal.innerText = total;

            const postCount = document.querySelectorAll('.notif-card[data-type="post"]').length;
            const kpiPosts = document.getElementById('kpiPosts');
            if (kpiPosts) kpiPosts.innerText = postCount;
            const tabCountPost = document.getElementById('tabCountPost');
            if (tabCountPost) tabCountPost.innerText = postCount;

            const compCount = document.querySelectorAll('.notif-card[data-type="complaint"]').length;
            const kpiComplaints = document.getElementById('kpiComplaints');
            if (kpiComplaints) kpiComplaints.innerText = compCount;
            const tabCountComplaint = document.getElementById('tabCountComplaint');
            if (tabCountComplaint) tabCountComplaint.innerText = compCount;

            updateUnreadBadgeCounter();
            applyFilters();
        }

        function showToast(message) {
            const toast = document.getElementById('toastMsg');
            const text = document.getElementById('toastText');
            if (toast && text) {
                text.innerText = message;
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 2200);
            }
        }
    </script>
</body>
</html>
