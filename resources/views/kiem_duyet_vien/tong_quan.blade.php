<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tổng Quan Kiểm Duyệt - RentHome</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .badge-warning-pill {
            background: var(--warning-light);
            color: #b45309;
        }

        .badge-danger-pill {
            background: var(--danger-light);
            color: #b91c1c;
        }

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
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
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
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* ================= 4 KPI CARDS ================= */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .kpi-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .kpi-icon.warning { background: var(--warning-light); color: #d97706; }
        .kpi-icon.danger { background: var(--danger-light); color: var(--danger); }
        .kpi-icon.success { background: var(--success-light); color: var(--success); }
        .kpi-icon.purple { background: var(--purple-light); color: var(--purple); }

        .kpi-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .kpi-footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .kpi-tag {
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11.5px;
        }

        .kpi-tag.urgent { background: #fff7ed; color: #c2410c; }
        .kpi-tag.alert { background: #fef2f2; color: #dc2626; }
        .kpi-tag.good { background: #ecfdf5; color: #059669; }
        .kpi-tag.info { background: #f5f3ff; color: #6d28d9; }

        /* ================= CHARTS SECTION ================= */
        .charts-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .card-panel {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 20px 22px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            color: var(--primary);
        }

        .chart-container {
            position: relative;
            height: 270px;
            width: 100%;
        }

        .doughnut-legend {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 12.5px;
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #334155;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* ================= BOTTOM TABLES & LISTS ================= */
        .workspace-grid {
            display: grid;
            grid-template-columns: 2.1fr 1fr;
            gap: 20px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-input, .select-filter {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            background: #f8fafc;
            color: var(--text-main);
        }

        .search-input:focus, .select-filter:focus {
            border-color: var(--primary);
            background: #fff;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table th {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 0.4px;
        }

        .custom-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background: #f8fafc;
        }

        .post-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .post-thumb {
            width: 56px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .post-title {
            font-weight: 700;
            color: var(--text-main);
            font-size: 13.5px;
            margin-bottom: 3px;
            display: block;
        }

        .post-sub {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: var(--primary-light);
            color: var(--primary);
        }

        .price-text {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }

        .action-btns {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: 0.2s;
        }

        .btn-view { background: #f1f5f9; color: #475569; }
        .btn-view:hover { background: #e2e8f0; color: #0f172a; }

        .btn-approve { background: var(--success-light); color: #059669; }
        .btn-approve:hover { background: var(--success); color: #fff; }

        .btn-reject { background: var(--danger-light); color: var(--danger); }
        .btn-reject:hover { background: var(--danger); color: #fff; }

        /* ================= COMPLAINT LIST ================= */
        .complaint-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .complaint-item {
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: #fcfdfe;
            transition: 0.2s;
        }

        .complaint-item:hover {
            border-color: #cbd5e1;
            background: #fff;
        }

        .complaint-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .priority-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .priority-high { background: var(--danger-light); color: #b91c1c; }
        .priority-medium { background: var(--warning-light); color: #b45309; }

        .complaint-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .complaint-desc {
            font-size: 12.5px;
            color: var(--text-muted);
            line-height: 1.45;
            margin-bottom: 10px;
        }

        .complaint-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            color: #64748b;
            padding-top: 8px;
            border-top: 1px dashed var(--border-color);
        }

        /* ================= MODAL ================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 560px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            overflow: hidden;
            animation: modalFadeIn 0.2s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-body {
            padding: 20px 22px;
        }

        .modal-footer {
            padding: 14px 22px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13.5px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        @media (max-width: 1200px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-grid, .workspace-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    @php
        // Dữ liệu mặc định dự phòng nếu Controller chưa truyền biến sang
        $pendingCount   = $stats['pending_posts'] ?? 14;
        $complaintCount = $stats['pending_complaints'] ?? 5;
        $approvedCount  = $stats['approved_posts'] ?? 128;
        $rejectedCount  = $stats['rejected_posts'] ?? 9;
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
                <a href="{{ url('/kiem-duyet/tong-quan') }}" class="nav-link active">
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
                    <span class="nav-badge badge-warning-pill" id="sidebarPendingBadge">{{ $pendingCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill">{{ $complaintCount }}</span>
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
                <a href="{{ url('/kiem-duyet/thong-bao') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-footer-left">
                @php
                    $uName = auth()->user()->account_name ?? auth()->user()->username ?? 'Kiểm Duyệt Viên';
                    $uInitials = strtoupper(mb_substr($uName, 0, 2, 'UTF-8'));
                @endphp
                <div class="avatar-circle">{{ $uInitials ?: 'KD' }}</div>
                <div class="user-meta">
                    <h4>{{ $uName }}</h4>
                    <p>{{ auth()->user()->email ?? 'kiemduyet2006@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        <!-- HEADER -->
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-gauge-high"></i>
                    Tổng Quan Kiểm Duyệt & Chất Lượng Nội Dung
                </h1>
                <p>Rà soát giá niêm yết, phê duyệt bài đăng từ nhân viên Sale và xử lý phản ánh/khiếu nại cấp 1</p>
            </div>
        </div>

        <!-- 1. HÀNG 4 THẺ THỐNG KÊ (KPI CARDS) -->
        <section class="kpi-grid">
            <!-- Card 1: Bài đăng chờ phê duyệt -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div>
                        <div class="kpi-value" id="kpiPendingCount">{{ $pendingCount }}</div>
                        <div class="kpi-label">Bài Đăng Chờ Phê Duyệt</div>
                    </div>
                    <div class="kpi-icon warning">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="kpi-footer">
                    <span class="kpi-tag urgent"><i class="fa-solid fa-fire"></i> Cần duyệt giá & nội dung</span>
                    <a href="#quickApprovalTable" style="color: var(--primary); text-decoration: none; font-weight: 600;">Xem ngay →</a>
                </div>
            </div>

            <!-- Card 2: Đơn khiếu nại cấp 1 -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div>
                        <div class="kpi-value">{{ $complaintCount }}</div>
                        <div class="kpi-label">Đơn Khiếu Nại Cấp 1</div>
                    </div>
                    <div class="kpi-icon danger">
                        <i class="fa-solid fa-scale-unbalanced"></i>
                    </div>
                </div>
                <div class="kpi-footer">
                    <span class="kpi-tag alert">2 đơn mức độ khẩn cấp</span>
                    <a href="{{ url('/kiem-duyet/khieu-nai') }}" style="color: var(--danger); text-decoration: none; font-weight: 600;">Xử lý →</a>
                </div>
            </div>

            <!-- Card 3: Bài đã phê duyệt -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div>
                        <div class="kpi-value" id="kpiApprovedCount">{{ $approvedCount }}</div>
                        <div class="kpi-label">Bài Đã Duyệt (Đang Hiển Thị)</div>
                    </div>
                    <div class="kpi-icon success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="kpi-footer">
                    <span class="kpi-tag good"><i class="fa-solid fa-arrow-trend-up"></i> +18 bài trong hôm nay</span>
                    <span style="color: var(--text-muted);">Tỷ lệ duyệt: 89%</span>
                </div>
            </div>

            <!-- Card 4: Bài từ chối & Tin tức -->
            <div class="kpi-card">
                <div class="kpi-top">
                    <div>
                        <div class="kpi-value" id="kpiRejectedCount">{{ $rejectedCount }}</div>
                        <div class="kpi-label">Bài Bị Từ Chối / Yêu Cầu Sửa</div>
                    </div>
                    <div class="kpi-icon purple">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                </div>
                <div class="kpi-footer">
                    <span class="kpi-tag info">Sai giá / Thiếu ảnh thực tế</span>
                    <a href="{{ url('/kiem-duyet/lich-su') }}" style="color: var(--purple); text-decoration: none; font-weight: 600;">Lịch sử →</a>
                </div>
            </div>
        </section>

        <!-- 2. KHU VỰC BIỂU ĐỒ TRỰC QUAN -->
        <section class="charts-grid">
            <!-- Biểu đồ 1: Lưu lượng kiểm duyệt 7 ngày gần nhất -->
            <div class="card-panel">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-column"></i>
                        Thống Kê Năng Suất Kiểm Duyệt & Khiếu Nại (7 Ngày Qua)
                    </div>
                    <select class="select-filter" onchange="updateBarChartRange(this.value)">
                        <option value="7">7 ngày gần nhất</option>
                        <option value="30">30 ngày gần nhất</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="moderationFlowChart"></canvas>
                </div>
            </div>

            <!-- Biểu đồ 2: Cơ cấu trạng thái bài đăng -->
            <div class="card-panel">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-pie"></i>
                        Tỷ Lệ Trạng Thái Bài Đăng
                    </div>
                </div>
                <div class="chart-container" style="height: 195px;">
                    <canvas id="statusRatioChart"></canvas>
                </div>
                <div class="doughnut-legend">
                    <div class="legend-item">
                        <span class="legend-left"><span class="dot" style="background:#10b981;"></span> Đã duyệt</span>
                        <strong>{{ $approvedCount }}</strong>
                    </div>
                    <div class="legend-item">
                        <span class="legend-left"><span class="dot" style="background:#f59e0b;"></span> Chờ duyệt</span>
                        <strong id="legendPending">{{ $pendingCount }}</strong>
                    </div>
                    <div class="legend-item">
                        <span class="legend-left"><span class="dot" style="background:#ef4444;"></span> Từ chối</span>
                        <strong id="legendRejected">{{ $rejectedCount }}</strong>
                    </div>
                    <div class="legend-item">
                        <span class="legend-left"><span class="dot" style="background:#8b5cf6;"></span> Khiếu nại</span>
                        <strong>{{ $complaintCount }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. BẢNG DUYỆT NHANH BÀI ĐĂNG & DANH SÁCH KHIẾU NẠI CẤP 1 -->
        <section class="workspace-grid" id="quickApprovalTable">
            <!-- Bảng trái: Danh sách bài đăng chờ phê duyệt nhanh -->
            <div class="card-panel">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-list-check"></i>
                        Danh Sách Bài Đăng Chờ Phê Duyệt Nhanh
                    </div>
                    <div class="filter-group">
                        <input type="text" id="quickSearchInput" class="search-input" placeholder="Tìm tên phòng, tòa nhà, NV..." onkeyup="filterPendingTable()">
                        <select id="typeFilterSelect" class="select-filter" onchange="filterPendingTable()">
                            <option value="">Tất cả loại hình</option>
                            <option value="Chung cư mini">Chung cư mini / Trọ</option>
                            <option value="Nhà riêng lẻ">Nhà ở / Nhà riêng lẻ</option>
                            <option value="Cụm liền kề">Cụm nhà liền kề / Dự án</option>
                            <option value="Đất nền">Đất phân lô / Đất nền</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table" id="pendingPostsTable">
                        <thead>
                            <tr>
                                <th>Tài sản / Cơ sở (Lớp Vỏ)</th>
                                <th>Loại hình</th>
                                <th>Nhân viên đăng</th>
                                <th>Giá</th>
                                <th>Thời gian</th>
                                <th style="text-align: right;">Thao tác duyệt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPosts ?? [] as $post)
                                @php
                                    $postId = (string)($post->_id ?? $post->id);
                                    $bName = $post->building->name ?? ($post->address ?? 'Cơ sở trực thuộc');
                                    $staffName = $post->user->account_name ?? ($post->user->username ?? 'Nhân viên');
                                    $priceText = number_format((float)($post->price ?? 0), 0, ',', '.') . ' đ/' . ($post->price_unit ?? 'tháng');
                                    $imgSrc = $post->display_image ?: ($post->thumbnail ?: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=200&q=80');
                                @endphp
                                <tr data-id="{{ $postId }}" data-type="{{ $post->property_type ?? 'Căn hộ' }}">
                                    <td>
                                        <div class="post-cell">
                                            <img src="{{ $imgSrc }}" class="post-thumb" alt="Room" onerror="this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=200&q=80'">
                                            <div>
                                                <span class="post-title">{{ $post->title }}</span>
                                                <span class="post-sub"><i class="fa-solid fa-building"></i> {{ $bName }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="type-badge">{{ $post->property_type ?? 'Phòng / Căn' }}</span></td>
                                    <td>
                                        <strong>{{ $staffName }}</strong>
                                        <div style="font-size:11.5px; color:var(--text-muted);">Tài khoản đăng</div>
                                    </td>
                                    <td>
                                        <div class="price-text">{{ $priceText }}</div>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 12.5px;">{{ $post->created_at ? $post->created_at->diffForHumans() : 'Mới đây' }}</td>
                                    <td>
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <button class="btn-icon btn-view" title="Xem chi tiết bài đăng"
                                                onclick="openPreviewModal('{{ addslashes($post->title) }}', '{{ addslashes($bName) }}', '{{ addslashes($staffName) }}', '{{ $priceText }}', '{{ addslashes(\Illuminate\Support\Str::limit($post->description ?? '', 150)) }}')">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-approve" title="Phê duyệt ngay" onclick="approvePostQuick(this, '{{ addslashes($post->title) }}')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button class="btn-icon btn-reject" title="Từ chối bài đăng" onclick="openRejectModal(this, '{{ addslashes($post->title) }}')">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                                        <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                        <span style="font-weight: 600;">Hiện không có bài đăng nào đang chờ kiểm duyệt.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 14px; text-align: center;">
                    <a href="{{ url('/kiem-duyet/qlkiem-duyet') }}" style="font-size: 13px; font-weight: 600; color: var(--primary); text-decoration: none;">
                        Xem toàn bộ danh sách bài đăng chờ duyệt ({{ $pendingCount ?? 0 }} bài) <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Cột phải: Đơn khiếu nại cấp 1 mới nhất -->
            <div class="card-panel">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-bell-concierge"></i>
                        Khiếu Nại Cấp 1 Cần Xử Lý
                    </div>
                    <a href="{{ url('/kiem-duyet/khieu-nai') }}" style="font-size: 12.5px; color: var(--primary); text-decoration: none; font-weight: 600;">Xem tất cả</a>
                </div>

                <div class="complaint-list">
                    <div style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                        <i class="fa-solid fa-circle-check" style="font-size: 2.2rem; color: #10b981; margin-bottom: 10px; display: block;"></i>
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main); display: block; margin-bottom: 4px;">Không có khiếu nại nào</span>
                        <span style="font-size: 12px; color: var(--text-muted);">Toàn bộ khiếu nại của khách thuê và tài khoản đã được giải quyết hoặc chưa phát sinh.</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ================= MODAL 1: XEM NHANH CHI TIẾT BÀI ĐĂNG ================= -->
    <div class="modal-overlay" id="previewModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class="fa-solid fa-magnifying-glass-dollar" style="color: var(--primary);"></i> Kiểm Tra Nhanh Nội Dung & Giá Niêm Yết</h3>
                <button class="logout-btn" onclick="closeModal('previewModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tiêu đề bài đăng (Lớp Ruột):</label>
                    <div id="pvTitle" style="font-weight: 700; font-size: 15px; color: var(--text-main);"></div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <span style="font-size: 11.5px; color: var(--text-muted);">Thuộc Tòa Nhà / Cơ Sở:</span>
                        <div id="pvBuilding" style="font-weight: 700; font-size: 13px; margin-top: 3px;"></div>
                    </div>
                    <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <span style="font-size: 11.5px; color: var(--text-muted);">Nhân viên khởi tạo:</span>
                        <div id="pvStaff" style="font-weight: 700; font-size: 13px; margin-top: 3px;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mức giá nhân viên đề xuất:</label>
                    <div id="pvPrice" style="font-size: 18px; font-weight: 800; color: var(--primary);"></div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Mô tả chi tiết & Tiện ích phòng:</label>
                    <div id="pvDesc" style="background: #f8fafc; padding: 12px; border-radius: 8px; font-size: 13.5px; color: #334155; line-height: 1.5;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('previewModal')">Đóng</button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL 2: NHẬP LÝ DO TỪ CHỐI BÀI ĐĂNG ================= -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class="fa-solid fa-ban" style="color: var(--danger);"></i> Từ Chối Phê Duyệt Bài Đăng</h3>
                <button class="logout-btn" onclick="closeModal('rejectModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                    Bạn đang từ chối bài đăng: <strong id="rejectPostName" style="color: var(--text-main);"></strong>
                </p>
                <div class="form-group">
                    <label>Lý do từ chối phổ biến *</label>
                    <select id="rejectReasonSelect" class="form-control">
                        <option value="Giá đề xuất chưa hợp lý so với mặt bằng chung của tòa nhà">Giá đề xuất chưa hợp lý so với mặt bằng chung của tòa nhà</option>
                        <option value="Hình ảnh mờ, thiếu ảnh thực tế hoặc nghi ngờ ảnh mạng">Hình ảnh mờ, thiếu ảnh thực tế hoặc nghi ngờ ảnh mạng</option>
                        <option value="Thông tin diện tích / tiện ích mô tả chưa đầy đủ">Thông tin diện tích / tiện ích mô tả chưa đầy đủ</option>
                        <option value="Sai địa chỉ hoặc chọn nhầm cơ sở Tòa nhà (Lớp Vỏ)">Sai địa chỉ hoặc chọn nhầm cơ sở Tòa nhà (Lớp Vỏ)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ghi chú chỉnh sửa gửi cho Nhân viên Sale:</label>
                    <textarea id="rejectNoteInput" rows="3" class="form-control" placeholder="Nhập yêu cầu cụ thể để nhân viên sửa lại bài..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('rejectModal')">Hủy bỏ</button>
                <button class="btn btn-primary" style="background: var(--danger);" onclick="confirmRejectPost()">
                    <i class="fa-solid fa-paper-plane"></i> Xác nhận từ chối
                </button>
            </div>
        </div>
    </div>

    <!-- ================= JAVASCRIPT LOGIC & CHARTS ================= -->
    <script>
        // 1. Khởi tạo Biểu đồ Lưu lượng Kiểm duyệt 7 ngày (Bar + Line Chart)
        const ctxFlow = document.getElementById('moderationFlowChart').getContext('2d');
        const moderationFlowChart = new Chart(ctxFlow, {
            type: 'bar',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [
                    {
                        label: 'Đã phê duyệt',
                        data: [15, 22, 18, 25, 20, 16, 12],
                        backgroundColor: '#0284c7',
                        borderRadius: 6,
                        barPercentage: 0.55
                    },
                    {
                        label: 'Bị từ chối (Sai giá/Ảnh)',
                        data: [2, 3, 1, 4, 2, 1, 1],
                        backgroundColor: '#f59e0b',
                        borderRadius: 6,
                        barPercentage: 0.55
                    },
                    {
                        label: 'Khiếu nại phát sinh',
                        type: 'line',
                        data: [1, 0, 2, 1, 3, 1, 1],
                        borderColor: '#ef4444',
                        backgroundColor: '#ef4444',
                        borderWidth: 2.5,
                        tension: 0.35,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8, font: { size: 12, weight: '600' } }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Khởi tạo Biểu đồ Tròn Tỷ lệ trạng thái (Doughnut Chart)
        const ctxRatio = document.getElementById('statusRatioChart').getContext('2d');
        const statusRatioChart = new Chart(ctxRatio, {
            type: 'doughnut',
            data: {
                labels: ['Đã duyệt', 'Chờ phê duyệt', 'Bị từ chối', 'Đơn khiếu nại'],
                datasets: [{
                    data: [{{ $approvedCount }}, {{ $pendingCount }}, {{ $rejectedCount }}, {{ $complaintCount }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 3. Đổi dữ liệu biểu đồ theo mốc 7 ngày / 30 ngày
        function updateBarChartRange(days) {
            if (days === '30') {
                moderationFlowChart.data.labels = ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'];
                moderationFlowChart.data.datasets[0].data = [68, 82, 74, 91];
                moderationFlowChart.data.datasets[1].data = [8, 11, 6, 9];
                moderationFlowChart.data.datasets[2].data = [4, 6, 3, 5];
            } else {
                moderationFlowChart.data.labels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
                moderationFlowChart.data.datasets[0].data = [15, 22, 18, 25, 20, 16, 12];
                moderationFlowChart.data.datasets[1].data = [2, 3, 1, 4, 2, 1, 1];
                moderationFlowChart.data.datasets[2].data = [1, 0, 2, 1, 3, 1, 1];
            }
            moderationFlowChart.update();
        }

        // 4. Lọc nhanh bảng bài đăng chờ phê duyệt
        function filterPendingTable() {
            const keyword = document.getElementById('quickSearchInput').value.toLowerCase();
            const typeVal = document.getElementById('typeFilterSelect').value;
            const rows = document.querySelectorAll('#pendingPostsTable tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowType = row.getAttribute('data-type');
                const matchKeyword = text.includes(keyword);
                const matchType = !typeVal || rowType === typeVal;
                row.style.display = (matchKeyword && matchType) ? '' : 'none';
            });
        }

        // 5. Xem nhanh bài đăng
        function openPreviewModal(title, building, staff, price, desc) {
            document.getElementById('pvTitle').innerText = title;
            document.getElementById('pvBuilding').innerText = building;
            document.getElementById('pvStaff').innerText = staff;
            document.getElementById('pvPrice').innerText = price;
            document.getElementById('pvDesc').innerText = desc;
            document.getElementById('previewModal').classList.add('show');
        }

        // 6. Phê duyệt nhanh bài đăng trên bảng
        function approvePostQuick(btn, title) {
            if (!confirm('Xác nhận PHÊ DUYỆT bài đăng "' + title + '" và cho phép hiển thị công khai lên trang chủ?')) return;
            const row = btn.closest('tr');
            row.style.transition = '0.3s';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                decrementPendingCounter(true);
            }, 300);
        }

        // 7. Mở Modal Từ chối bài đăng
        let currentRejectRow = null;
        function openRejectModal(btn, title) {
            currentRejectRow = btn.closest('tr');
            document.getElementById('rejectPostName').innerText = title;
            document.getElementById('rejectNoteInput').value = '';
            document.getElementById('rejectModal').classList.add('show');
        }

        function confirmRejectPost() {
            if (currentRejectRow) {
                currentRejectRow.style.transition = '0.3s';
                currentRejectRow.style.opacity = '0';
                setTimeout(() => {
                    currentRejectRow.remove();
                    decrementPendingCounter(false);
                }, 300);
            }
            closeModal('rejectModal');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Cập nhật lại số đếm trên các thẻ KPI & Biểu đồ khi duyệt/từ chối
        function decrementPendingCounter(isApproved) {
            const pendingEl = document.getElementById('kpiPendingCount');
            const sidebarBadge = document.getElementById('sidebarPendingBadge');
            const legendPending = document.getElementById('legendPending');

            let currentPending = parseInt(pendingEl.innerText) || 0;
            if (currentPending > 0) {
                currentPending--;
                pendingEl.innerText = currentPending;
                sidebarBadge.innerText = currentPending;
                legendPending.innerText = currentPending;
            }

            if (isApproved) {
                const approvedEl = document.getElementById('kpiApprovedCount');
                approvedEl.innerText = (parseInt(approvedEl.innerText) || 0) + 1;
                statusRatioChart.data.datasets[0].data[0]++;
            } else {
                const rejectedEl = document.getElementById('kpiRejectedCount');
                const legendRejected = document.getElementById('legendRejected');
                let newRej = (parseInt(rejectedEl.innerText) || 0) + 1;
                rejectedEl.innerText = newRej;
                legendRejected.innerText = newRej;
                statusRatioChart.data.datasets[0].data[2]++;
            }
            statusRatioChart.data.datasets[0].data[1] = currentPending;
            statusRatioChart.update();
        }
    </script>
</body>
</html>