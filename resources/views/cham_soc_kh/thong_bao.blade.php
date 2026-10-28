<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trung Tâm Thông Báo - RentHome CSKH</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            --bg-body: #f8fafc;
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

        .swal2-container {
            z-index: 999999 !important;
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
            background: #0284c7;
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
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
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .brand-info p {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            padding: 8px 12px;
            margin-bottom: 6px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link-left i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .nav-link.active {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .nav-link.active i {
            color: #ffffff;
        }

        .nav-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .badge-danger-pill {
            background: var(--danger-light);
            color: var(--danger);
        }

        .badge-warning-pill {
            background: #fef08a;
            color: #854d0e;
        }

        .nav-link.active .badge-danger-pill,
        .nav-link.active .badge-warning-pill {
            background: #ffffff;
            color: #0284c7;
        }

        .user-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
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
            font-size: 12px;
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
            max-width: calc(100vw - var(--sidebar-width));
            min-height: 100vh;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-outline {
            background: #ffffff;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }


        /* Card animation */
        .notif-card {
            transition: all 0.2s ease;
        }

        .notif-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR CHĂM SÓC KHÁCH HÀNG (CSKH) ================= -->
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">CSKH</span></h2>
                <p>Trung tâm Chăm sóc khách hàng</p>
            </div>
        </div>

        <div class="nav-section-title">Chăm Sóc Khách Hàng</div>
        <ul class="nav-list">
            <li>
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Quản lý Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill" id="sidebarComplaintBadge">{{ $pendingComplaints ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.notifications') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="nav-badge badge-warning-pill" id="sidebarNotifBadge">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-footer-left">
                <div class="avatar-circle">CSKH</div>
                <div class="user-meta">
                    <h4>{{ auth()->user()->account_name ?? 'Chăm Sóc Khách Hàng' }}</h4>
                    <p>{{ auth()->user()->email ?? 'chamsockh@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        
        <!-- Header Page -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-black shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            Trung Tâm Thông Báo CSKH
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span id="headerNotifBadge" class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-rose-500 text-white">
                                    {{ $unreadCount }} mới
                                </span>
                            @endif
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Tiếp nhận thông báo khiếu nại mới từ khách thuê, tiến độ phán quyết Admin và cập nhật hệ thống.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" onclick="markAllAsRead()" class="btn btn-outline text-sky-700 bg-sky-50 border-sky-200 hover:bg-sky-100" title="Đánh dấu tất cả là đã đọc">
                    <i class="fa-solid fa-check-double text-sky-600"></i>
                    <span>Đọc tất cả</span>
                </button>

                <button type="button" onclick="clearReadNotifications()" class="btn btn-outline text-rose-600 bg-rose-50 border-rose-200 hover:bg-rose-100" title="Dọn sạch các thông báo đã đọc">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>Xóa đã đọc</span>
                </button>

                <button type="button" class="btn btn-outline" onclick="window.location.reload()" title="Tải lại dữ liệu">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Làm mới</span>
                </button>
            </div>
        </div>


        <!-- DANH SÁCH THÔNG BÁO -->
        <div class="space-y-3" id="cskh-notif-container">
            @forelse($notifications as $n)
                @php
                    $isUnread = !$n->is_read;
                    $notifId = (string)($n->_id ?? $n->id);
                    $category = strtolower($n->category ?? '');
                    $type = strtolower($n->type ?? '');

                    // Phân loại Icon, Màu sắc và Nhãn hiển thị
                    $iconClass = 'fa-solid fa-bell';
                    $iconBg = 'bg-sky-100 text-sky-600 border border-sky-200';
                    $badgeBg = 'bg-sky-50 text-sky-700 border border-sky-200';
                    $badgeText = 'Hệ thống';

                    if ($category === 'new_complaint' || str_contains($category, 'complaint_created')) {
                        $iconClass = 'fa-solid fa-bullhorn';
                        $iconBg = 'bg-amber-100 text-amber-700 border border-amber-200';
                        $badgeBg = 'bg-amber-50 text-amber-800 border border-amber-200';
                        $badgeText = 'Khiếu nại mới';
                    } elseif ($category === 'admin_verdict' || str_contains($category, 'resolved')) {
                        $iconClass = 'fa-solid fa-scale-balanced';
                        $iconBg = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                        $badgeBg = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                        $badgeText = 'Phán quyết Admin';
                    } elseif ($category === 'admin_rejected') {
                        $iconClass = 'fa-solid fa-ban';
                        $iconBg = 'bg-slate-100 text-slate-700 border border-slate-200';
                        $badgeBg = 'bg-slate-100 text-slate-700 border border-slate-300';
                        $badgeText = 'Bác bỏ khiếu nại';
                    } elseif ($category === 'admin_sanction' || $type === 'danger' || $type === 'error') {
                        $iconClass = 'fa-solid fa-triangle-exclamation';
                        $iconBg = 'bg-rose-100 text-rose-700 border border-rose-200';
                        $badgeBg = 'bg-rose-50 text-rose-700 border border-rose-200';
                        $badgeText = 'Chế tài Admin';
                    } elseif ($category === 'customer_reply') {
                        $iconClass = 'fa-solid fa-comments';
                        $iconBg = 'bg-blue-100 text-blue-700 border border-blue-200';
                        $badgeBg = 'bg-blue-50 text-blue-700 border border-blue-200';
                        $badgeText = 'Phản hồi từ Khách';
                    } elseif ($category === 'complaint_escalated') {
                        $iconClass = 'fa-solid fa-shield-halved';
                        $iconBg = 'bg-purple-100 text-purple-700 border border-purple-200';
                        $badgeBg = 'bg-purple-50 text-purple-700 border border-purple-200';
                        $badgeText = 'Leo thang Admin';
                    }

                    // Tìm bài đăng liên kết (nếu có)
                    $attachedPost = null;
                    if (!empty($n->post_id)) {
                        $attachedPost = \App\Models\Post::where('_id', $n->post_id)->first() ?: \App\Models\Post::find($n->post_id);
                    } elseif (!empty($n->target_id)) {
                        $t = \App\Models\SupportTicket::find($n->target_id) ?: \App\Models\Complaint::find($n->target_id);
                        if ($t && !empty($t->post_id)) {
                            $attachedPost = \App\Models\Post::where('_id', $t->post_id)->first() ?: \App\Models\Post::find($t->post_id);
                        }
                    }
                @endphp

                <div id="cskh-notif-{{ $notifId }}" 
                     class="notif-card bg-white rounded-2xl p-4 sm:p-5 border {{ $isUnread ? 'border-sky-300/80 bg-sky-50/20 ring-1 ring-sky-300/40' : 'border-slate-200' }}">
                    <div class="flex items-start gap-3.5 sm:gap-4">
                        <!-- Icon Thông báo -->
                        <div class="w-11 h-11 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0 text-lg shadow-2xs mt-0.5">
                            <i class="{{ $iconClass }}"></i>
                        </div>

                        <!-- Nội dung Thông báo -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 mb-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-extrabold uppercase tracking-wider {{ $badgeBg }}">
                                        {{ $badgeText }}
                                    </span>
                                    @if($isUnread)
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Mới
                                        </span>
                                    @endif
                                </div>

                                <!-- Thao tác nhanh bên phải -->
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($isUnread)
                                        <button type="button" 
                                                onclick="markNotifRead('{{ $notifId }}')" 
                                                title="Đánh dấu đã đọc" 
                                                class="w-8 h-8 rounded-lg text-slate-400 hover:text-sky-600 hover:bg-sky-50 flex items-center justify-center transition text-sm cursor-pointer">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                    <button type="button" 
                                            onclick="deleteNotif('{{ $notifId }}')" 
                                            title="Xóa thông báo này" 
                                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition text-sm cursor-pointer">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tiêu đề -->
                            <h3 class="text-sm sm:text-base leading-snug {{ $isUnread ? 'font-black text-slate-900' : 'font-bold text-slate-700' }}">
                                {{ $n->title }}
                            </h3>

                            <!-- Nội dung chi tiết -->
                            <p class="text-xs sm:text-sm mt-1.5 leading-relaxed break-words {{ $isUnread ? 'font-medium text-slate-700' : 'text-slate-500' }}">
                                {{ $n->message }}
                            </p>

                            <!-- Thẻ xem trước bài đăng (Nếu có bài đăng đính kèm) -->
                            @if($attachedPost)
                                <div class="mt-3 p-3 rounded-xl bg-slate-50 hover:bg-sky-50/40 border border-slate-200/80 hover:border-sky-300 transition-all group max-w-xl">
                                    <a href="{{ url('/chitietbaidang/' . ($attachedPost->_id ?? $attachedPost->id)) }}" target="_blank" class="flex items-center gap-3">
                                        @php
                                            $thumb = $attachedPost->avatar ?? ($attachedPost->hinh_anh[0] ?? ($attachedPost->images[0] ?? null));
                                        @endphp
                                        @if($thumb)
                                            <img src="{{ $thumb }}" alt="{{ $attachedPost->title }}" class="w-12 h-12 rounded-lg object-cover shrink-0 border border-slate-200 shadow-2xs group-hover:scale-105 transition-transform">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 bg-sky-100/80 px-1.5 py-0.5 rounded">Bài đăng bị khiếu nại</span>
                                                @if(!empty($attachedPost->gia_thue ?? $attachedPost->price))
                                                    <span class="text-xs font-black text-emerald-600 ml-auto">
                                                        {{ is_numeric($attachedPost->gia_thue ?? $attachedPost->price) ? number_format($attachedPost->gia_thue ?? $attachedPost->price) . ' đ/tháng' : ($attachedPost->gia_thue ?? $attachedPost->price) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-sky-700 truncate mt-0.5">
                                                {{ $attachedPost->title }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                                <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                                                <span>{{ $attachedPost->dia_chi ?? ($attachedPost->address ?? 'RentHome') }}</span>
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-slate-400 group-hover:text-sky-600 pl-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <!-- Dòng chân thẻ: Thời gian & Nút Hành Động -->
                            <div class="flex flex-wrap items-center justify-between gap-3 mt-3.5 pt-2.5 border-t border-slate-100 text-xs text-slate-400">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <i class="fa-regular fa-clock text-[11px] text-slate-400"></i>
                                        {{ \Carbon\Carbon::parse($n->created_at)->format('H:i - d/m/Y') }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ \Carbon\Carbon::parse($n->created_at)->locale('vi')->diffForHumans() }}</span>
                                </div>

                                <!-- Nút dẫn tới màn hình xử lý Khiếu nại -->
                                @php
                                    $actionUrl = $n->action_url ?? url('/cskh/khieu-nai');
                                @endphp
                                <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 font-bold text-sky-600 hover:text-sky-700 hover:underline">
                                    <span>Xử lý hồ sơ ngay</span>
                                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Khung trống khi không có thông báo -->
                <div class="text-center py-16 px-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="w-16 h-16 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                        <i class="fa-solid fa-bell-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Không có thông báo nào</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Hiện tại chưa có sự kiện hoặc khiếu nại mới nào trong hệ thống.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        @if($notifications->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $notifications->links() }}
            </div>
        @endif

    </main>

    <!-- JavaScript Xử Lý AJAX Thông Báo CSKH -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Đánh dấu 1 thông báo là đã đọc
        async function markNotifRead(id) {
            try {
                const res = await fetch(`/cskh/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const card = document.getElementById(`cskh-notif-${id}`);
                    if (card) {
                        card.classList.remove('border-sky-300/80', 'bg-sky-50/20', 'ring-1', 'ring-sky-300/40');
                        card.classList.add('border-slate-200');
                        const newBadge = card.querySelector('.animate-pulse');
                        if (newBadge) newBadge.remove();
                        const checkBtn = card.querySelector('button[onclick*="markNotifRead"]');
                        if (checkBtn) checkBtn.remove();
                        const title = card.querySelector('h3');
                        if (title) {
                            title.classList.remove('font-black', 'text-slate-900');
                            title.classList.add('font-bold', 'text-slate-700');
                        }
                        const desc = card.querySelector('p.break-words');
                        if (desc) {
                            desc.classList.remove('font-medium', 'text-slate-700');
                            desc.classList.add('text-slate-500');
                        }
                    }
                    updateSidebarBadge();
                }
            } catch (e) {
                console.error(e);
            }
        }

        // Đánh dấu tất cả là đã đọc (Thực hiện ngay lập tức, không popup xác nhận & không popup hoàn tất)
        async function markAllAsRead() {
            // Cập nhật ngay toàn bộ giao diện thành đã đọc
            const cards = document.querySelectorAll('#cskh-notif-container .notif-card');
            cards.forEach(card => {
                card.classList.remove('border-sky-300/80', 'bg-sky-50/20', 'ring-1', 'ring-sky-300/40');
                card.classList.add('border-slate-200');

                const pulseBadge = card.querySelector('.animate-pulse');
                if (pulseBadge) pulseBadge.remove();

                const checkBtn = card.querySelector('button[onclick*="markNotifRead"]');
                if (checkBtn) checkBtn.remove();

                const title = card.querySelector('h3');
                if (title) {
                    title.classList.remove('font-black', 'text-slate-900');
                    title.classList.add('font-bold', 'text-slate-700');
                }

                const desc = card.querySelector('p.break-words');
                if (desc) {
                    desc.classList.remove('font-medium', 'text-slate-700');
                    desc.classList.add('text-slate-500');
                }
            });

            // Xóa badge số lượng thông báo mới ở Header và Sidebar
            const headerBadge = document.getElementById('headerNotifBadge');
            if (headerBadge) headerBadge.remove();

            const sidebarBadge = document.getElementById('sidebarNotifBadge');
            if (sidebarBadge) sidebarBadge.style.display = 'none';

            // Gửi request API nền đánh dấu tất cả đã đọc vào CSDL
            try {
                await fetch(`/cskh/notifications/mark-all-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                updateSidebarBadge();
            } catch (e) {
                console.error('Lỗi khi đánh dấu tất cả đã đọc:', e);
            }
        }

        // Xóa 1 thông báo
        async function deleteNotif(id) {
            const confirm = await Swal.fire({
                title: 'Xóa thông báo này?',
                text: 'Dữ liệu thông báo này sẽ bị xóa vĩnh viễn khỏi danh sách.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xác nhận xóa',
                cancelButtonText: 'Hủy'
            });

            if (confirm.isConfirmed) {
                try {
                    const res = await fetch(`/cskh/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const card = document.getElementById(`cskh-notif-${id}`);
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(-10px)';
                            setTimeout(() => card.remove(), 250);
                        }
                        updateSidebarBadge();
                    }
                } catch (e) {
                    Swal.fire('Lỗi', 'Không thể xóa thông báo.', 'error');
                }
            }
        }

        // Xóa tất cả thông báo đã đọc
        async function clearReadNotifications() {
            const confirm = await Swal.fire({
                title: 'Dọn sạch thông báo đã đọc?',
                text: 'Tất cả các thông báo cũ mà bạn đã đọc sẽ bị xóa khỏi cơ sở dữ liệu.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý dọn sạch',
                cancelButtonText: 'Hủy'
            });

            if (confirm.isConfirmed) {
                Swal.showLoading();
                try {
                    const res = await fetch(`/cskh/notifications/clear-read`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            title: 'Đã dọn sạch!',
                            text: data.message,
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (e) {
                    Swal.fire('Lỗi', 'Không thể hoàn tất thao tác.', 'error');
                }
            }
        }

        // Cập nhật số lượng unread trên Sidebar & Header
        async function updateSidebarBadge() {
            try {
                const res = await fetch('/cskh/api/notifications/unread-count');
                const data = await res.json();
                if (data.success) {
                    const badge = document.getElementById('sidebarNotifBadge');
                    if (badge) {
                        if (data.unread_notifications > 0) {
                            badge.textContent = data.unread_notifications;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }

                    const headerBadge = document.getElementById('headerNotifBadge');
                    if (headerBadge) {
                        if (data.unread_notifications > 0) {
                            headerBadge.textContent = `${data.unread_notifications} mới`;
                            headerBadge.style.display = 'inline-flex';
                        } else {
                            headerBadge.remove();
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }

        // Lặp định kỳ 30 giây để cập nhật số lượng thông báo mới
        setInterval(updateSidebarBadge, 30000);
    </script>
</body>
</html>
