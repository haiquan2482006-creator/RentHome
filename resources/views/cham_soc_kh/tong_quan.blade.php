<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tổng Quan Chăm Sóc Khách Hàng - RentHome CSKH</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.modern-notification')

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
            width: 44px;
            height: 44px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
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

        .page-title-box p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
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
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* ================= KPI CARDS ================= */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            transition: all 0.25s ease;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .kpi-icon.blue { background: var(--primary-light); color: var(--primary); }
        .kpi-icon.amber { background: var(--warning-light); color: #d97706; }
        .kpi-icon.purple { background: var(--purple-light); color: var(--purple); }
        .kpi-icon.emerald { background: var(--success-light); color: var(--success); }

        .kpi-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .kpi-tag.pending { background: #fef3c7; color: #b45309; }
        .kpi-tag.success { background: #d1fae5; color: #047857; }
        .kpi-tag.purple { background: #ede9fe; color: #6d28d9; }
        .kpi-tag.neutral { background: #f1f5f9; color: #475569; }

        .kpi-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .kpi-footer {
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            border-top: 1px dashed #f1f5f9;
            padding-top: 10px;
        }

        /* ================= CHARTS SECTION ================= */
        .charts-grid {
            display: grid;
            grid-template-columns: 1.85fr 1.15fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .card-panel {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 22px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .panel-title {
            font-size: 15.5px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-subtitle {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .chart-box {
            position: relative;
            height: 290px;
            width: 100%;
        }

        /* ================= INSIGHTS GRID ================= */
        .insights-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .reason-item {
            margin-bottom: 14px;
        }

        .reason-header {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .progress-bar-bg {
            height: 8px;
            border-radius: 4px;
            background: #f1f5f9;
            overflow: hidden;
            display: flex;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.8s ease-in-out;
        }

        .sla-card {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sla-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .sla-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .sla-badge {
            font-size: 14px;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 8px;
        }

        /* ================= TABLE ================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .overview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .overview-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .overview-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .overview-table tr:hover td {
            background: #fafbfc;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-escalated { background: #ede9fe; color: #6d28d9; }
        .badge-resolved { background: #d1fae5; color: #047857; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }

        @media (max-width: 1200px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-grid, .insights-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-wrapper { margin-left: 0; padding: 16px; max-width: 100%; }
            .kpi-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR CHĂM SÓC KHÁCH HÀNG (CSKH) ================= -->
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo">
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">CSKH</span></h2>
                <p>Trung tâm Chăm sóc khách hàng</p>
            </div>
        </div>

        <div class="nav-section-title">Chăm Sóc Khách Hàng</div>
        <ul class="nav-list">
            <li>
                <a href="{{ url('/cskh/tong-quan') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tổng quan</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Quản lý Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill" id="sidebarComplaintBadge">{{ $pendingCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.notifications') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                    @php
                        $cskhCurUserId = (string)(auth()->id() ?? '');
                        $cskhUnreadBadge = isset($unreadCount) ? (int)$unreadCount : ($cskhCurUserId ? \App\Models\Notification::where('user_id', $cskhCurUserId)->where('is_read', false)->count() : 0);
                    @endphp
                    @if($cskhUnreadBadge > 0)
                        <span class="nav-badge badge-warning-pill" id="sidebarNotifBadge">{{ $cskhUnreadBadge }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.history') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lịch sử hoạt động</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.profile') }}" class="nav-link {{ request()->routeIs('cskh.profile') ? 'active' : '' }}">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Hồ sơ cá nhân</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <a href="{{ route('cskh.profile') }}" class="user-footer-left hover:opacity-85 transition-opacity" title="👤 Hồ sơ cá nhân">
                <div class="avatar-circle">
                    @if(!empty($currentUser->avatar))
                        <img src="{{ $currentUser->display_avatar }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
                    @else
                        CSKH
                    @endif
                </div>
                <div class="user-meta">
                    <h4>{{ $currentUser->display_name ?: ($currentUser->account_name ?? 'Chăm Sóc Khách Hàng') }}</h4>
                    <p>{{ $currentUser->email ?? 'chamsockh@gmail.com' }}</p>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('cskh.profile') }}" class="logout-btn" title="👤 Hồ sơ cá nhân">
                    <i class="fa-solid fa-user-pen text-xs"></i>
                </a>
                <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        <!-- Header -->
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-chart-line text-sky-600"></i>
                    Tổng Quan Chăm Sóc Khách Hàng & Khiếu Nại
                </h1>
                <p>Theo dõi tình hình tiếp nhận khiếu nại, hiệu suất giải quyết & phản ánh chất lượng phòng thuê</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 shadow-sm flex items-center gap-2">
                    <i class="fa-regular fa-calendar-check text-sky-600"></i>
                    <span>Hôm nay: {{ now()->format('d/m/Y') }}</span>
                </div>
                <a href="{{ url('/cskh/tong-quan') }}" class="btn btn-outline" title="Tải lại số liệu">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Làm mới</span>
                </a>
                <a href="{{ url('/cskh/khieu-nai') }}" class="btn btn-primary">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Xử lý khiếu nại</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        <!-- 1. KPI CARDS SECTION -->
        <section class="kpi-grid">
            <!-- KPI 1: Tổng số khiếu nại -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon blue">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <span class="kpi-tag neutral">Tất cả dữ liệu</span>
                </div>
                <div>
                    <div class="kpi-title">Tổng khiếu nại ghi nhận</div>
                    <div class="kpi-value text-sky-700">{{ number_format($totalComplaints) }}</div>
                </div>
                <div class="kpi-footer">
                    <i class="fa-solid fa-circle-info text-sky-500"></i>
                    <span>Phản ánh từ khách thuê RentHome</span>
                </div>
            </div>

            <!-- KPI 2: Chờ xử lý Cấp 1 (CSKH) -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon amber">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="kpi-tag pending">Cần xử lý ngay</span>
                </div>
                <div>
                    <div class="kpi-title">Chờ CSKH xử lý (Cấp 1)</div>
                    <div class="kpi-value text-amber-600">{{ number_format($pendingCount) }}</div>
                </div>
                <div class="kpi-footer">
                    <i class="fa-solid fa-headset text-amber-500"></i>
                    <span>Đang chờ liên hệ & hòa giải</span>
                </div>
            </div>

            <!-- KPI 3: Đã chuyển tiếp Cấp 2 (Admin) -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon purple">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="kpi-tag purple">Admin thẩm định</span>
                </div>
                <div>
                    <div class="kpi-title">Trình Ban Quản Trị (Cấp 2)</div>
                    <div class="kpi-value text-purple-700">{{ number_format($escalatedCount) }}</div>
                </div>
                <div class="kpi-footer">
                    <i class="fa-solid fa-gavel text-purple-500"></i>
                    <span>Khiếu nại phức tạp / Bồi hoàn</span>
                </div>
            </div>

            <!-- KPI 4: Đã xử lý & Tỷ lệ giải quyết -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon emerald">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span class="kpi-tag success">{{ $resolutionRate }}% Hoàn tất</span>
                </div>
                <div>
                    <div class="kpi-title">Đã giải quyết thành công</div>
                    <div class="kpi-value text-emerald-600">{{ number_format($cskhResolvedCount ?? 0) }}</div>
                </div>
                <div class="kpi-footer">
                    <i class="fa-solid fa-shield-check text-emerald-500"></i>
                    <span>{{ $rejectedCount }} hồ sơ từ chối / bác bỏ</span>
                </div>
            </div>
        </section>

        <!-- 2. BIỂU ĐỒ TRỰC QUAN (CHARTS SECTION) -->
        <section class="charts-grid">
            <!-- Biểu đồ 1: Xu hướng tiếp nhận & giải quyết 7 ngày qua -->
            <div class="card-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">
                            <i class="fa-solid fa-chart-column text-sky-600"></i>
                            Xu hướng tiếp nhận & giải quyết khiếu nại (7 ngày gần nhất)
                        </h2>
                        <p class="panel-subtitle">So sánh số lượng hồ sơ tiếp nhận mới và số lượng đã xử lý hoàn tất</p>
                    </div>
                    <span class="px-2.5 py-1 bg-sky-50 text-sky-700 rounded-lg text-xs font-semibold">
                        Theo ngày
                    </span>
                </div>
                <div class="chart-box">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Biểu đồ 2: Doughnut Tỷ lệ trạng thái hồ sơ -->
            <div class="card-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">
                            <i class="fa-solid fa-chart-pie text-sky-600"></i>
                            Phân bổ trạng thái xử lý
                        </h2>
                        <p class="panel-subtitle">Tỷ lệ các giai đoạn giải quyết</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold">
                        Tổng thể
                    </span>
                </div>
                <div class="chart-box">
                    <canvas id="statusDoughnutChart"></canvas>
                </div>
            </div>
        </section>

        <!-- 3. DANH SÁCH KHIẾU NẠI MỚI NHẤT GẦN ĐÂY -->
        <section class="card-panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">
                        <i class="fa-solid fa-bell-concierge text-sky-600"></i>
                        Hồ sơ khiếu nại mới nhận gần đây
                    </h2>
                    <p class="panel-subtitle">Danh sách các yêu cầu cần kiểm tra hoặc đang phản hồi khách thuê</p>
                </div>
                <a href="{{ url('/cskh/khieu-nai') }}" class="btn btn-outline text-xs py-1.5 px-3">
                    <span>Xem tất cả danh sách</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="overview-table">
                    <thead>
                        <tr>
                            <th>MÃ HỒ SƠ</th>
                            <th>NGƯỜI KHIẾU NẠI</th>
                            <th>BÀI ĐĂNG PHẢN ÁNH</th>
                            <th>LÝ DO CHÍNH</th>
                            <th>NGÀY GỬI</th>
                            <th>TRẠNG THÁI</th>
                            <th class="text-right">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                            @php
                                $status = $ticket->status ?? 'pending';
                                $isEscalated = $ticket->is_escalated ?? false;
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-bold text-sky-700">#{{ $ticket->ticket_code ?? ('TK-' . substr((string)($ticket->_id ?? $ticket->id), -6)) }}</span>
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-800">{{ $ticket->sender_name ?? $ticket->customer_name ?? 'Khách thuê' }}</div>
                                    <div class="text-xs text-slate-400">{{ $ticket->sender_phone ?? $ticket->sender_email ?? 'Chưa rõ thông tin' }}</div>
                                </td>
                                <td style="max-width: 260px;">
                                    @if($ticket->post)
                                        <div class="font-semibold text-slate-800 line-clamp-1" title="{{ $ticket->post->title ?? '' }}">
                                            {{ $ticket->post->title ?? 'Bài đăng ID #' . $ticket->post_id }}
                                        </div>
                                        <div class="text-xs text-slate-400 line-clamp-1">
                                            {{ $ticket->post->district ?? '' }}{{ $ticket->post->city ? ', ' . $ticket->post->city : '' }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">ID: {{ $ticket->post_id ?? 'Phản ánh chung' }}</span>
                                    @endif
                                </td>
                                <td style="max-width: 220px;">
                                    <span class="line-clamp-1 font-medium text-slate-700" title="{{ $ticket->reason ?? $ticket->message ?? '' }}">
                                        {{ $ticket->reason ?: ($ticket->message ?: 'Phản ánh bài đăng') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-500 whitespace-nowrap">
                                        {{ $ticket->created_at ? $ticket->created_at->format('H:i - d/m/Y') : 'Vừa xong' }}
                                    </span>
                                </td>
                                <td>
                                    @if(in_array($status, ['resolved', 'da_giai_quyet', 'closed']))
                                        <span class="badge-status badge-resolved">
                                            <i class="fa-solid fa-check"></i> Đã giải quyết
                                        </span>
                                    @elseif($isEscalated || $status === 'escalated')
                                        <span class="badge-status badge-escalated">
                                            <i class="fa-solid fa-shield-halved"></i> Đã trình Admin
                                        </span>
                                    @elseif(in_array($status, ['rejected', 'tu_choi', 'cancelled']))
                                        <span class="badge-status badge-rejected">
                                            <i class="fa-solid fa-ban"></i> Đã bác bỏ
                                        </span>
                                    @else
                                        <span class="badge-status badge-pending">
                                            <i class="fa-solid fa-clock"></i> Chờ CSKH
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ url('/cskh/khieu-nai?ticket_id=' . ($ticket->_id ?? $ticket->id)) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white rounded-lg text-xs font-bold transition-all">
                                        <span>Chi tiết</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-slate-400">
                                    <i class="fa-solid fa-circle-check text-4xl text-emerald-400 mb-2 block"></i>
                                    Hiện chưa có hồ sơ khiếu nại mới nào. Tất cả đã được xử lý hoàn tất!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- ================= CHART INITIALIZATION SCRIPT ================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Dữ liệu từ Controller truyền sang
            const chartDays = {!! json_encode($chartDays) !!};
            const incomingData = {!! json_encode($incomingData) !!};
            const resolvedData = {!! json_encode($resolvedData) !!};

            const pendingCount = {{ (int)$pendingCount }};
            const escalatedCount = {{ (int)$escalatedCount }};
            const cskhResolvedCount = {{ (int)($cskhResolvedCount ?? 0) }};
            const resolvedCount = {{ (int)$resolvedCount }};
            const rejectedCount = {{ (int)$rejectedCount }};

            // 2. Cấu hình Biểu đồ Xu hướng (Combo Bar + Line)
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                new Chart(trendCtx.getContext('2d'), {
                    data: {
                        labels: chartDays,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Khiếu nại tiếp nhận',
                                data: incomingData,
                                backgroundColor: 'rgba(2, 132, 199, 0.75)',
                                borderColor: '#0284c7',
                                borderWidth: 1.5,
                                borderRadius: 6,
                                maxBarThickness: 32,
                                order: 2
                            },
                            {
                                type: 'line',
                                label: 'Đã xử lý / Đóng đơn',
                                data: resolvedData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4.5,
                                pointHoverRadius: 6,
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 12,
                                        weight: '600'
                                    },
                                    padding: 16
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: "'Inter', sans-serif", size: 13, weight: 'bold' },
                                bodyFont: { family: "'Inter', sans-serif", size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { family: "'Inter', sans-serif", size: 11.5, weight: '500' },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    font: { family: "'Inter', sans-serif", size: 11.5 },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }

            // 3. Cấu hình Biểu đồ Doughnut Trạng thái
            const statusCtx = document.getElementById('statusDoughnutChart');
            if (statusCtx) {
                // Chỉ giữ lại 2 trạng thái: Trình Admin & CSKH xử lý
                const totalForChart = escalatedCount + cskhResolvedCount;
                const statusData = totalForChart > 0
                    ? [escalatedCount, cskhResolvedCount]
                    : [0, 1];
                const bgColors = totalForChart > 0
                    ? ['#8b5cf6', '#10b981']
                    : ['#e2e8f0'];
                const labels = totalForChart > 0
                    ? ['Trình Admin', 'CSKH xử lý']
                    : ['Chưa có hồ sơ'];

                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: bgColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11.5,
                                        weight: '600'
                                    },
                                    padding: 12
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: "'Inter', sans-serif", size: 13, weight: 'bold' },
                                bodyFont: { family: "'Inter', sans-serif", size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return ` ${label}: ${value} đơn (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

