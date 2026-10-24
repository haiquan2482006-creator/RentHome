<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thông Báo - Admin RentHome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --primary-blue: #0284c7;
            --primary-hover: #0369a1;
            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --transition: all 0.25s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Đồng bộ class .admin-main co giãn theo thanh điều hướng bên trái */
        .admin-main {
            padding: 28px 32px;
            min-height: 100vh;
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: margin-left 0.25s ease, width 0.25s ease;
        }

        /* Khi sidebar thu gọn bằng class phổ biến */
        body.sidebar-collapsed .admin-main,
        .sidebar.collapsed ~ .admin-main,
        .admin-sidebar.collapsed ~ .admin-main {
            margin-left: 96px;
            width: calc(100% - 96px);
        }

        @media (max-width: 768px) {
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 20px 16px;
            }
        }

        /* --- Header trang --- */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .page-title p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            color: #fff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: #fff;
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        .icon-blue { background: #e0f2fe; color: #0284c7; }
        .icon-red { background: #fee2e2; color: #ef4444; }
        .icon-green { background: #dcfce7; color: #16a34a; }
        .icon-purple { background: #f3e8ff; color: #9333ea; }

        /* --- Bộ lọc & Tìm kiếm --- */
        .filter-bar {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .tabs-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-secondary);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn:hover {
            background: #f1f5f9;
            color: var(--text-primary);
        }

        .tab-btn.active {
            background: #e0f2fe;
            color: var(--primary-blue);
        }

        .search-box {
            position: relative;
            min-width: 280px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 14px;
        }

        .search-box input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 13.5px;
            outline: none;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* --- Danh sách Thông báo --- */
        .notification-container {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            gap: 16px;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item:hover {
            background-color: #f8fafc;
        }

        .notif-item.unread {
            background-color: #f0f9ff;
            border-left: 4px solid var(--primary-blue);
        }

        .notif-left {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            flex: 1;
        }

        .notif-avatar {
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

        .notif-content {
            flex: 1;
        }

        .notif-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }

        .notif-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .badge-urgent { background: #fee2e2; color: #dc2626; }
        .badge-staff { background: #e0f2fe; color: #0284c7; }
        .badge-moderator { background: #f3e8ff; color: #7e22ce; }
        .badge-sent { background: #dcfce7; color: #15803d; }

        .notif-desc {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .notif-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 12px;
            color: #94a3b8;
        }

        .notif-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .notif-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #fff;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .action-icon-btn:hover {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
            background: #f0f9ff;
        }

        .action-icon-btn.delete:hover {
            color: #ef4444;
            border-color: #ef4444;
            background: #fef2f2;
        }

        /* --- Modal Tạo Thông Báo --- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 560px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideUp 0.25s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 14px;
            outline: none;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--text-secondary);
            display: none;
        }
    </style>
</head>
<body>

    {{-- Thanh điều hướng bên trái của Admin --}}
    @include('partials.header-admin')

    <main class="admin-main" id="adminMain">
        <!-- Header -->
        <div class="page-header">
            <div class="page-title">
                <h1>Trung Tâm Thông Báo Hệ Thống</h1>
                <p>Theo dõi cảnh báo vận hành và phát thông báo đến Kiểm duyệt viên, Tài khoản con & Khách thuê</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline" onclick="markAllAsRead()">
                    <i class="fa-solid fa-check-double"></i> Đánh dấu đã đọc tất cả
                </button>
            </div>
        </div>

        <!-- Thanh công cụ lọc & tìm kiếm -->
        <div class="filter-bar">
            <div class="tabs-group">
                <button class="tab-btn active" onclick="filterNotif('all', this)">
                    Tất cả
                </button>
                <button class="tab-btn" onclick="filterNotif('unread', this)">
                    <i class="fa-solid fa-circle" style="font-size: 7px; color: #0284c7;"></i> Chưa đọc
                </button>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="searchNotif()" placeholder="Tìm tiêu đề, nội dung thông báo...">
            </div>
        </div>

        <!-- Danh sách thông báo -->
        <div class="notification-container" id="notifList">

            @forelse($notifications ?? [] as $notif)
                @php
                    $notifId = (string)($notif->_id ?? $notif->id);
                    $isUnread = empty($notif->is_read);
                    $nType = $notif->type ?? 'info';
                    $iconClass = match($nType) {
                        'urgent', 'danger', 'error' => 'icon-red',
                        'success' => 'icon-green',
                        'warning' => 'icon-purple',
                        default => 'icon-blue'
                    };
                    $iconFa = match($nType) {
                        'urgent', 'danger', 'error' => 'fa-solid fa-shield-halved',
                        'success' => 'fa-solid fa-paper-plane',
                        'warning' => 'fa-solid fa-clipboard-check',
                        default => 'fa-solid fa-building-user'
                    };
                    $badgeClass = match($nType) {
                        'urgent', 'danger', 'error' => 'badge-urgent',
                        'success' => 'badge-sent',
                        'warning' => 'badge-moderator',
                        default => 'badge-staff'
                    };
                    $badgeText = match($nType) {
                        'urgent', 'danger', 'error' => 'Khẩn cấp',
                        'success' => 'Đã phát',
                        'warning' => 'Kiểm duyệt',
                        default => 'Hệ thống'
                    };
                @endphp
                <div class="notif-item {{ $isUnread ? 'unread' : '' }}" data-id="{{ $notifId }}">
                    <div class="notif-left">
                        <div class="notif-avatar {{ $iconClass }}">
                            <i class="{{ $iconFa }}"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-header">
                                <span class="notif-title">{{ $notif->title }}</span>
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </div>
                            <p class="notif-desc">
                                {!! nl2br(e($notif->message)) !!}
                            </p>
                            <div class="notif-meta">
                                <span><i class="fa-regular fa-clock"></i> {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Mới đây' }}</span>
                                <span><i class="fa-solid fa-user-shield"></i> Ban Quản Trị RentHome</span>
                            </div>
                        </div>
                    </div>
                    <div class="notif-actions">
                        @if($isUnread)
                            <button class="action-icon-btn" title="Đánh dấu đã đọc" onclick="markRead(this, '{{ $notifId }}')">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endif
                        <button class="action-icon-btn delete" title="Xóa thông báo" onclick="removeNotif(this, '{{ $notifId }}')">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state" id="emptyState" style="display: block;">
                    <i class="fa-regular fa-bell-slash" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                    <p>Hiện chưa có thông báo nào trong hệ thống.</p>
                </div>
            @endforelse

            <div class="empty-state" id="emptyStateFiltered" style="display: none;">
                <i class="fa-regular fa-bell-slash" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                <p>Không tìm thấy thông báo nào phù hợp với bộ lọc.</p>
            </div>
        </div>
    </main>

    <script>
        // Tự động nhận diện kích thước thanh điều hướng và co giãn nội dung .admin-main
        (function initSidebarSync() {
            const mainEl = document.getElementById('adminMain');

            function findSidebarElement() {
                const selectors = ['.admin-sidebar', '.sidebar', 'aside', '#sidebar', '#adminSidebar', 'nav'];
                for (const sel of selectors) {
                    const el = document.querySelector(sel);
                    if (el && el.getBoundingClientRect().height > 200) return el;
                }
                // Tìm phần tử fixed bên trái màn hình từ header-admin
                for (const el of document.body.querySelectorAll('*')) {
                    if (el === mainEl || mainEl.contains(el)) continue;
                    const style = window.getComputedStyle(el);
                    if (style.position === 'fixed' && parseFloat(style.left) <= 40) {
                        const rect = el.getBoundingClientRect();
                        if (rect.width > 40 && rect.height > 200) return el;
                    }
                }
                return null;
            }

            const sidebarEl = findSidebarElement();

            function syncMargin() {
                if (!mainEl || !sidebarEl) return;
                if (window.innerWidth <= 768) {
                    mainEl.style.marginLeft = '0px';
                    mainEl.style.width = '100%';
                    return;
                }
                const rect = sidebarEl.getBoundingClientRect();
                if (rect.width > 0) {
                    const offset = Math.round(rect.right);
                    mainEl.style.transition = 'none';
                    mainEl.style.marginLeft = offset + 'px';
                    mainEl.style.width = `calc(100% - ${offset}px)`;
                }
            }

            if (sidebarEl) {
                syncMargin();
                // Lắng nghe mọi thay đổi kích thước (khi bấm nút thu gọn/mở rộng)
                if (window.ResizeObserver) {
                    new ResizeObserver(syncMargin).observe(sidebarEl);
                }
                sidebarEl.addEventListener('transitionrun', () => {
                    const start = performance.now();
                    function step(now) {
                        syncMargin();
                        if (now - start < 400) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                });
                window.addEventListener('resize', syncMargin);
            }
        })();

        async function markRead(btn, id) {
            const item = btn.closest('.notif-item');
            if (item) {
                item.classList.remove('unread');
                btn.remove();
            }
            if (id) {
                try {
                    await fetch(`/api/admin/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            }
        }

        async function markAllAsRead() {
            document.querySelectorAll('.notif-item.unread').forEach(item => {
                item.classList.remove('unread');
                const checkBtn = item.querySelector('.action-icon-btn:not(.delete)');
                if (checkBtn) checkBtn.remove();
            });
            try {
                await fetch('/api/admin/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {
                console.error(e);
            }
        }

        async function removeNotif(btn, id) {
            if (!confirm('Bạn có chắc chắn muốn xóa thông báo này?')) return;
            const item = btn.closest('.notif-item');
            if (item) {
                item.remove();
            }
            checkEmptyState();
            if (id) {
                try {
                    await fetch(`/api/admin/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            }
        }

        function checkEmptyState() {
            const items = document.querySelectorAll('.notif-item');
            const emptyEl = document.getElementById('emptyState');
            if (emptyEl) {
                emptyEl.style.display = items.length === 0 ? 'block' : 'none';
            }
        }

        function filterNotif(category, tabBtn) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            tabBtn.classList.add('active');

            const items = document.querySelectorAll('.notif-item');
            let visibleCount = 0;

            items.forEach(item => {
                if (category === 'all') {
                    item.style.display = 'flex';
                    visibleCount++;
                } else if (category === 'unread') {
                    if (item.classList.contains('unread')) {
                        item.style.display = 'flex';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            const emptyFiltered = document.getElementById('emptyStateFiltered');
            if (emptyFiltered) {
                emptyFiltered.style.display = (items.length > 0 && visibleCount === 0) ? 'block' : 'none';
            }
        }

        function searchNotif() {
            const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.notif-item');
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                if (!keyword || text.includes(keyword)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            const emptyFiltered = document.getElementById('emptyStateFiltered');
            if (emptyFiltered) {
                emptyFiltered.style.display = (items.length > 0 && visibleCount === 0) ? 'block' : 'none';
            }
        }
    </script>
</body>
</html>