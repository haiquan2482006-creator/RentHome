<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý & Kiểm Duyệt Tin Tức BĐS - RentHome</title>

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
            gap: 10px;
            flex-wrap: wrap;
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
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover {
            background: #059669;
        }

        /* ================= 3 KPI CARDS ================= */
        .kpi-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: 0.2s;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        }

        .kpi-val {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
        }

        .kpi-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .kpi-icon.blue { background: var(--primary-light); color: var(--primary); }
        .kpi-icon.amber { background: var(--warning-light); color: #d97706; }
        .kpi-icon.emerald { background: var(--success-light); color: var(--success); }

        /* ================= FILTER & CONTENT PANEL ================= */
        .card-panel {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
            overflow: hidden;
        }

        .filter-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .tabs-box {
            display: flex;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
        }

        .tab-btn {
            border: none;
            background: transparent;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .tab-counter {
            font-size: 11px;
            padding: 1px 6px;
            border-radius: 12px;
            background: #e2e8f0;
            color: #475569;
        }

        .tab-btn.active .tab-counter {
            background: var(--primary-light);
            color: var(--primary);
        }

        .search-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .input-search, .select-category {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            font-size: 13px;
            outline: none;
            background: #f8fafc;
            color: var(--text-main);
        }

        .input-search {
            min-width: 240px;
        }

        .input-search:focus, .select-category:focus {
            border-color: var(--primary);
            background: #fff;
        }

        /* ================= NEWS LIST ================= */
        .news-list {
            display: flex;
            flex-direction: column;
            divide-y: 1px solid #f1f5f9;
        }

        .news-item {
            padding: 18px 22px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            border-bottom: 1px solid #f1f5f9;
            transition: 0.15s;
        }

        .news-item:hover {
            background: #f8fafc;
        }

        .news-thumb-box {
            width: 140px;
            height: 95px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
            background: #e2e8f0;
        }

        .news-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.3s;
        }

        .news-item:hover .news-thumb {
            transform: scale(1.05);
        }

        .news-featured-tag {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #f59e0b;
            color: #fff;
            font-size: 9.5px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .news-content {
            flex: 1;
            min-width: 0;
        }

        .news-meta-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }

        .source-pill {
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .source-vnexpress { background: #fee2e2; color: #b91c1c; }
        .source-cafef { background: #fef3c7; color: #b45309; }
        .source-dantri { background: #d1fae5; color: #047857; }
        .source-batdongsan { background: #e0f2fe; color: #0369a1; }
        .source-other { background: #f1f5f9; color: #475569; }

        .category-pill {
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        .news-time {
            font-size: 11.5px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .news-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .news-title a {
            color: inherit;
            text-decoration: none;
            transition: 0.15s;
        }

        .news-title a:hover {
            color: var(--primary);
        }

        .news-summary {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-source-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-top: 6px;
        }

        .news-source-link:hover {
            text-decoration: underline;
        }

        .news-status-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
            flex-shrink: 0;
            min-width: 140px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending { background: var(--warning-light); color: #b45309; }
        .status-approved { background: var(--success-light); color: #047857; }
        .status-rejected { background: var(--danger-light); color: #b91c1c; }

        .btn-act-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-sm-act {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: 0.2s;
        }

        .btn-edit { background: #f1f5f9; color: #334155; }
        .btn-edit:hover { background: #e2e8f0; color: #0f172a; }

        .btn-approve { background: var(--success-light); color: #047857; }
        .btn-approve:hover { background: var(--success); color: #fff; }

        .btn-delete { background: var(--danger-light); color: #b91c1c; }
        .btn-delete:hover { background: var(--danger); color: #fff; }

        .empty-box {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-box i {
            font-size: 42px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .empty-box h3 {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }

        /* ================= MODAL ================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box-large {
            background: #fff;
            width: 100%;
            max-width: 820px;
            border-radius: 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 16.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-body {
            padding: 22px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 14px 22px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13.5px;
            outline: none;
            background: #fff;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .preview-card-box {
            background: #0f172a;
            border-radius: 14px;
            padding: 16px;
            color: #fff;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .preview-img-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.45;
        }

        .preview-badge-row {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .preview-content-box {
            position: relative;
            z-index: 2;
        }

        .preview-title {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            line-height: 1.35;
        }

        .preview-summary {
            font-size: 12.5px;
            color: #cbd5e1;
            margin-top: 4px;
            line-height: 1.4;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .kpi-grid-3 { grid-template-columns: 1fr; }
            .form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

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
                    <span class="nav-badge badge-warning-pill">{{ $pendingPostsBadge ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill">{{ $pendingComplaints ?? 0 }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/qltin-tuc') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Quản lý tin tức</span>
                    </span>
                    <span class="nav-badge" style="background: rgba(255,255,255,0.25); color: #fff;" id="sidebarNewsPendingBadge">0</span>
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
                <div class="avatar-circle">KD</div>
                <div class="user-meta">
                    <h4>{{ auth()->user()->account_name ?? 'Kiểm Duyệt Viên' }}</h4>
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
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-newspaper"></i>
                    Quản Lý & Kiểm Duyệt Tin Tức BĐS (RSS & Moderation)
                </h1>
                <p>Tự động thu thập tin thị trường từ RSS các báo lớn (VnExpress, CafeF, Dân Trí) với <strong>hạn mức tối đa 10 tin/ngày</strong>, kiểm duyệt và lựa chọn bài xuất bản lên Trang chủ.</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-outline" onclick="loadNews()">
                    <i class="fa-solid fa-rotate"></i> Làm mới
                </button>
                <button type="button" class="btn btn-primary" id="btnFetchRss" onclick="fetchRssNews()">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Kéo tin RSS mới nhất
                </button>
                <button type="button" class="btn btn-success" onclick="openAddModal()">
                    <i class="fa-solid fa-plus"></i> Thêm tin thủ công
                </button>
            </div>
        </div>

        <!-- 3 THẺ KPI THỐNG KÊ -->
        <section class="kpi-grid-3">
            <div class="kpi-card" onclick="switchTab('pending')">
                <div>
                    <div class="kpi-val" id="kpiPending" style="color: #d97706;">0</div>
                    <div class="kpi-label">Tin Chờ Duyệt (Pending)</div>
                </div>
                <div class="kpi-icon amber">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="switchTab('approved')">
                <div>
                    <div class="kpi-val" id="kpiApproved" style="color: var(--success);">0</div>
                    <div class="kpi-label">Đã Lên Trang Chủ (Approved)</div>
                </div>
                <div class="kpi-icon emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="switchTab('all')">
                <div>
                    <div class="kpi-val" id="kpiTotal">0</div>
                    <div class="kpi-label">Tổng Tin Tức Trong DB</div>
                </div>
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
            </div>
        </section>

        <!-- BỘ LỌC VÀ DANH SÁCH BÀI BÁO -->
        <section class="card-panel">
            <div class="filter-header">
                <div class="tabs-box" id="newsTabs">
                    <button type="button" class="tab-btn active" data-status="pending" onclick="switchTab('pending')">
                        Chờ phê duyệt <span class="tab-counter" id="tabCountPending">0</span>
                    </button>
                    <button type="button" class="tab-btn" data-status="approved" onclick="switchTab('approved')">
                        Đã lên Trang chủ <span class="tab-counter" id="tabCountApproved">0</span>
                    </button>
                    <button type="button" class="tab-btn" data-status="all" onclick="switchTab('all')">
                        Tất cả <span class="tab-counter" id="tabCountAll">0</span>
                    </button>
                </div>

                <div class="search-controls">
                    <input type="text" id="searchInput" class="input-search" placeholder="Tìm theo tiêu đề, nguồn báo, từ khóa..." oninput="handleSearch()">
                    <select id="categoryFilter" class="select-category" onchange="handleSearch()">
                        <option value="">Tất cả danh mục</option>
                        <option value="Tin thị trường">Tin thị trường</option>
                        <option value="Cẩm nang pháp lý">Cẩm nang pháp lý</option>
                        <option value="Xu hướng sống">Xu hướng sống</option>
                        <option value="Phân tích giá">Phân tích giá</option>
                    </select>
                </div>
            </div>

            <!-- CONTAINER DANH SÁCH TIN -->
            <div class="news-list" id="newsListContainer">
                <div class="empty-box">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <h3>Đang tải danh sách bài báo...</h3>
                </div>
            </div>
        </section>
    </main>

    <!-- ================= MODAL XEM TRƯỚC & SỬA BÀI ================= -->
    <div class="modal-overlay" id="editModal" onclick="if(event.target === this) closeEditModal()">
        <div class="modal-box-large">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i>
                    Xem Trước & Hiệu Chỉnh Bài Báo
                </h3>
                <button type="button" class="logout-btn" onclick="closeEditModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="editArticleId">

                <div class="form-group">
                    <label>Tiêu đề bài báo *</label>
                    <input type="text" id="editTitle" class="form-control" oninput="updateLivePreview()">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Nguồn báo *</label>
                        <input type="text" id="editSourceName" class="form-control" oninput="updateLivePreview()">
                    </div>
                    <div class="form-group">
                        <label>Danh mục tin tức *</label>
                        <select id="editCategory" class="form-control" onchange="updateLivePreview()">
                            <option value="Tin thị trường">Tin thị trường</option>
                            <option value="Cẩm nang pháp lý">Cẩm nang pháp lý</option>
                            <option value="Xu hướng sống">Xu hướng sống</option>
                            <option value="Phân tích giá">Phân tích giá</option>
                            <option value="Dự án nổi bật">Dự án nổi bật</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Đường link gốc bài viết</label>
                        <input type="text" id="editSourceUrl" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Link hình ảnh đại diện (URL)</label>
                        <input type="text" id="editImageUrl" class="form-control" oninput="updateLivePreview()">
                    </div>
                </div>

                <div class="form-group">
                    <label>Đoạn tóm tắt nội dung (Summary) *</label>
                    <textarea id="editSummary" rows="3" class="form-control" oninput="updateLivePreview()"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 6px;">
                    <label class="checkbox-label">
                        <input type="checkbox" id="editIsFeatured" onchange="updateLivePreview()">
                        <span><strong style="color: #d97706;">⭐ Đặt làm Bài viết Nổi bật</strong> (Chiếm khung lớn bên trái trang chủ)</span>
                    </label>
                </div>

                <!-- LIVE PREVIEW CARD -->
                <div style="margin-top: 14px;">
                    <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                        Xem trước hiển thị trên Trang chủ:
                    </label>
                    <div class="preview-card-box">
                        <img id="pvImage" src="" class="preview-img-bg" alt="preview">
                        <div class="preview-badge-row">
                            <span id="pvFeaturedTag" class="news-featured-tag" style="position: static; display: none;">
                                <i class="fa-solid fa-star"></i> BÀI VIẾT NỔI BẬT
                            </span>
                            <span id="pvSource" class="source-pill source-vnexpress">VnExpress</span>
                            <span id="pvCategory" class="category-pill" style="background: rgba(255,255,255,0.2); color: #fff;">Tin thị trường</span>
                        </div>
                        <div class="preview-content-box">
                            <h4 id="pvTitle" class="preview-title">Tiêu đề bài báo</h4>
                            <p id="pvSummary" class="preview-summary">Tóm tắt nội dung bài viết...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="submitEditNews()">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu chỉnh sửa
                </button>
                <button type="button" class="btn btn-success" id="btnApproveInModal" onclick="submitApproveFromModal()">
                    <i class="fa-solid fa-circle-check"></i> Duyệt lên Trang chủ
                </button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL THÊM TIN THỦ CÔNG ================= -->
    <div class="modal-overlay" id="addModal" onclick="if(event.target === this) closeAddModal()">
        <div class="modal-box-large">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-plus-circle" style="color: var(--success);"></i>
                    Thêm Tin Tức Bất Động Sản Thủ Công
                </h3>
                <button type="button" class="logout-btn" onclick="closeAddModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Tiêu đề bài báo *</label>
                    <input type="text" id="addTitle" class="form-control" placeholder="Nhập tiêu đề tin tức nổi bật...">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Nguồn bài viết</label>
                        <input type="text" id="addSourceName" class="form-control" value="RentHome News" placeholder="VD: VnExpress, CafeF, RentHome...">
                    </div>
                    <div class="form-group">
                        <label>Danh mục tin *</label>
                        <select id="addCategory" class="form-control">
                            <option value="Tin thị trường">Tin thị trường</option>
                            <option value="Cẩm nang pháp lý">Cẩm nang pháp lý</option>
                            <option value="Xu hướng sống">Xu hướng sống</option>
                            <option value="Phân tích giá">Phân tích giá</option>
                            <option value="Dự án nổi bật">Dự án nổi bật</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Link bài viết gốc</label>
                        <input type="text" id="addSourceUrl" class="form-control" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Link ảnh đại diện (URL)</label>
                        <input type="text" id="addImageUrl" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tóm tắt bài viết *</label>
                    <textarea id="addSummary" rows="3" class="form-control" placeholder="Mô tả ngắn gọn nội dung bài viết..."></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="addIsFeatured">
                        <span><strong style="color: #d97706;">⭐ Đặt làm Bài viết Nổi bật</strong> (Khung lớn bên trái trang chủ)</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAddModal()">Hủy</button>
                <button type="button" class="btn btn-success" onclick="submitCreateNews()">
                    <i class="fa-solid fa-check"></i> Tạo bài viết
                </button>
            </div>
        </div>
    </div>

    <!-- ================= JAVASCRIPT LOGIC ================= -->
    <script>
        let currentTab = 'pending';
        let allArticles = [];

        let visibleLimit = 20;

        document.addEventListener('DOMContentLoaded', () => {
            loadNews();

            // Tự động quét và kéo tin mới ngầm mỗi 15 phút khi kiểm duyệt viên đang mở tab
            setInterval(silentAutoFetchNews, 15 * 60 * 1000);
        });

        // 1. Chuyển Tab
        function switchTab(status) {
            currentTab = status;
            visibleLimit = 20;
            document.querySelectorAll('#newsTabs .tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.status === status);
            });
            renderNewsList();
        }

        // Tự động kéo tin RSS ngầm không gián đoạn thao tác người dùng
        async function silentAutoFetchNews() {
            try {
                const res = await fetch('/api/kiem-duyet/news/fetch-rss', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success && data.new_count > 0) {
                    showAutoToast(`🔔 Đã tự động cập nhật thêm ${data.new_count} tin mới từ RSS!`);
                    loadNews();
                }
            } catch (e) {}
        }

        function showAutoToast(msg) {
            let toast = document.getElementById('autoFetchToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'autoFetchToast';
                toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0f172a;color:#fff;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:700;z-index:9999;box-shadow:0 10px 25px rgba(0,0,0,0.3);border:1px solid #38bdf8;display:flex;align-items:center;gap:8px;transition:all 0.3s;';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fa-solid fa-bell" style="color: #38bdf8;"></i> <span>${msg}</span>`;
            toast.style.display = 'flex';
            setTimeout(() => { if (toast) toast.style.display = 'none'; }, 5000);
        }

        // 2. Tải danh sách tin tức từ API
        async function loadNews() {
            const container = document.getElementById('newsListContainer');
            container.innerHTML = `
                <div class="empty-box">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <h3>Đang tải danh sách bài báo...</h3>
                </div>
            `;

            try {
                const res = await fetch('/api/kiem-duyet/news');
                const data = await res.json();
                if (data.success) {
                    allArticles = data.articles || [];
                    updateStats(data.stats);
                    renderNewsList();
                } else {
                    container.innerHTML = `<div class="empty-box"><p style="color: var(--danger);">${data.message || 'Lỗi tải dữ liệu!'}</p></div>`;
                }
            } catch (e) {
                console.error(e);
                container.innerHTML = `<div class="empty-box"><p style="color: var(--danger);">Lỗi kết nối máy chủ khi lấy tin tức!</p></div>`;
            }
        }

        // 3. Cập nhật số liệu KPI & badge
        function updateStats(stats) {
            if (!stats) return;
            document.getElementById('kpiTotal').innerText = stats.total || 0;
            document.getElementById('kpiPending').innerText = stats.pending || 0;
            document.getElementById('kpiApproved').innerText = stats.approved || 0;

            document.getElementById('tabCountPending').innerText = stats.pending || 0;
            document.getElementById('tabCountApproved').innerText = stats.approved || 0;
            document.getElementById('tabCountAll').innerText = stats.total || 0;

            const badge = document.getElementById('sidebarNewsPendingBadge');
            if (badge) badge.innerText = stats.pending || 0;
        }

        // 4. Render danh sách bài báo (Có phân trang xem thêm)
        function renderNewsList() {
            const container = document.getElementById('newsListContainer');
            const keyword = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const category = document.getElementById('categoryFilter')?.value || '';

            const filtered = allArticles.filter(item => {
                const matchTab = (currentTab === 'all') || (item.status === currentTab);
                const text = `${item.title} ${item.summary} ${item.source_name} ${item.category}`.toLowerCase();
                const matchKeyword = !keyword || text.includes(keyword);
                const matchCategory = !category || (item.category === category);
                return matchTab && matchKeyword && matchCategory;
            });

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="empty-box">
                        <i class="fa-solid fa-newspaper"></i>
                        <h3>Không có bài báo nào phù hợp</h3>
                        <p style="font-size: 13px;">Hãy thử bấm "Kéo tin RSS mới nhất" hoặc đổi bộ lọc tìm kiếm.</p>
                    </div>
                `;
                return;
            }

            const itemsToShow = filtered.slice(0, visibleLimit);

            let html = itemsToShow.map(item => {
                const id = item._id || item.id;
                const isApproved = item.status === 'approved';
                const isFeatured = item.is_featured;

                // Màu nguồn báo
                let sourceClass = 'source-other';
                const sName = (item.source_name || '').toLowerCase();
                if (sName.includes('vnexpress')) sourceClass = 'source-vnexpress';
                else if (sName.includes('cafef')) sourceClass = 'source-cafef';
                else if (sName.includes('dân trí') || sName.includes('dan tri')) sourceClass = 'source-dantri';
                else if (sName.includes('batdongsan')) sourceClass = 'source-batdongsan';

                const imgUrl = item.image_url || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80';
                const dateStr = item.published_at ? new Date(item.published_at).toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'}) : 'Vừa xong';

                return `
                    <div class="news-item" data-id="${id}">
                        <!-- Ảnh thumbnail -->
                        <div class="news-thumb-box">
                            <img src="${imgUrl}" class="news-thumb" alt="${escapeHtml(item.title)}" onerror="this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80'">
                            ${isFeatured ? `<span class="news-featured-tag"><i class="fa-solid fa-star"></i> Nổi bật</span>` : ''}
                        </div>

                        <!-- Nội dung tin -->
                        <div class="news-content">
                            <div class="news-meta-top">
                                <span class="source-pill ${sourceClass}">${item.source_name || 'Báo chí'}</span>
                                <span class="category-pill">${item.category || 'Tin tức'}</span>
                                <span class="news-time"><i class="fa-regular fa-clock"></i> ${dateStr}</span>
                            </div>

                            <h3 class="news-title">
                                <a href="${item.source_url || '#'}" target="_blank" rel="noopener noreferrer">${item.title}</a>
                            </h3>

                            <p class="news-summary">${item.summary || ''}</p>

                            <a href="${item.source_url || '#'}" target="_blank" rel="noopener noreferrer" class="news-source-link">
                                Đọc tin gốc <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                            </a>
                        </div>

                        <!-- Trạng thái & Action buttons -->
                        <div class="news-status-col">
                            <span class="status-pill ${isApproved ? 'status-approved' : 'status-pending'}">
                                ${isApproved ? '<i class="fa-solid fa-circle-check"></i> Đã duyệt Home' : '<i class="fa-solid fa-clock"></i> Chờ duyệt'}
                            </span>

                            <div class="btn-act-group">
                                <button type="button" class="btn-sm-act btn-edit" title="Sửa & Xem trước" onclick="openEditModal('${id}')">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </button>
                                ${!isApproved ? `
                                    <button type="button" class="btn-sm-act btn-approve" title="Duyệt lên Trang chủ" onclick="approveNews('${id}')">
                                        <i class="fa-solid fa-check"></i> Duyệt
                                    </button>
                                ` : ''}
                                <button type="button" class="btn-sm-act btn-delete" title="Xóa bài báo" onclick="deleteNews('${id}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Nút Xem thêm nếu danh sách còn nhiều bài
            if (filtered.length > visibleLimit) {
                const remaining = filtered.length - visibleLimit;
                html += `
                    <div style="padding: 18px; text-align: center; background: #f8fafc; border-top: 1px solid var(--border-color);">
                        <button type="button" class="btn btn-outline" onclick="loadMoreNews()">
                            <i class="fa-solid fa-angles-down"></i> Xem thêm 20 bài tiếp theo (Còn lại ${remaining} bài)
                        </button>
                    </div>
                `;
            }

            container.innerHTML = html;
        }

        function loadMoreNews() {
            visibleLimit += 20;
            renderNewsList();
        }

        function handleSearch() {
            visibleLimit = 20;
            renderNewsList();
        }

        // 5. Kéo tin RSS mới
        async function fetchRssNews() {
            const btn = document.getElementById('btnFetchRss');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang cào RSS...';

            try {
                const res = await fetch('/api/kiem-duyet/news/fetch-rss', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Kéo tin RSS thành công!');
                    loadNews();
                } else {
                    alert(data.message || 'Lỗi khi kéo tin RSS!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối máy chủ!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // 6. Phê duyệt bài báo
        async function approveNews(id) {
            if (!confirm('Xác nhận phê duyệt bài báo này hiển thị công khai lên Trang chủ?')) return;

            try {
                const res = await fetch(`/api/kiem-duyet/news/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã duyệt bài thành công!');
                    loadNews();
                } else {
                    alert(data.message || 'Lỗi khi duyệt bài!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối máy chủ!');
            }
        }

        // 7. Xóa bài báo
        async function deleteNews(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa bài báo này khỏi hệ thống?')) return;

            try {
                const res = await fetch(`/api/kiem-duyet/news/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã xóa bài báo!');
                    loadNews();
                } else {
                    alert(data.message || 'Lỗi khi xóa bài!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối máy chủ!');
            }
        }

        // 8. Modal Sửa & Xem trước
        function openEditModal(id) {
            const item = allArticles.find(a => (a._id || a.id) === id);
            if (!item) return;

            document.getElementById('editArticleId').value = id;
            document.getElementById('editTitle').value = item.title || '';
            document.getElementById('editSourceName').value = item.source_name || '';
            document.getElementById('editCategory').value = item.category || 'Tin thị trường';
            document.getElementById('editSourceUrl').value = item.source_url || '';
            document.getElementById('editImageUrl').value = item.image_url || '';
            document.getElementById('editSummary').value = item.summary || '';
            document.getElementById('editIsFeatured').checked = !!item.is_featured;

            const btnApprove = document.getElementById('btnApproveInModal');
            if (btnApprove) {
                btnApprove.style.display = (item.status === 'approved') ? 'none' : 'inline-flex';
            }

            updateLivePreview();
            document.getElementById('editModal').classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        function updateLivePreview() {
            const title = document.getElementById('editTitle').value || 'Tiêu đề bài viết';
            const source = document.getElementById('editSourceName').value || 'Báo chí';
            const category = document.getElementById('editCategory').value || 'Tin tức';
            const summary = document.getElementById('editSummary').value || 'Nội dung tóm tắt...';
            const img = document.getElementById('editImageUrl').value || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80';
            const isFeatured = document.getElementById('editIsFeatured').checked;

            document.getElementById('pvTitle').innerText = title;
            document.getElementById('pvSource').innerText = source;
            document.getElementById('pvCategory').innerText = category;
            document.getElementById('pvSummary').innerText = summary;
            document.getElementById('pvImage').src = img;
            document.getElementById('pvFeaturedTag').style.display = isFeatured ? 'inline-flex' : 'none';
        }

        async function submitEditNews() {
            const id = document.getElementById('editArticleId').value;
            const payload = {
                title: document.getElementById('editTitle').value.trim(),
                source_name: document.getElementById('editSourceName').value.trim(),
                category: document.getElementById('editCategory').value,
                source_url: document.getElementById('editSourceUrl').value.trim(),
                image_url: document.getElementById('editImageUrl').value.trim(),
                summary: document.getElementById('editSummary').value.trim(),
                is_featured: document.getElementById('editIsFeatured').checked
            };

            if (!payload.title || !payload.summary) {
                alert('Vui lòng nhập đầy đủ Tiêu đề và Tóm tắt!');
                return;
            }

            try {
                const res = await fetch(`/api/kiem-duyet/news/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã lưu chỉnh sửa!');
                    closeEditModal();
                    loadNews();
                } else {
                    alert(data.message || 'Lỗi khi lưu bài!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối máy chủ!');
            }
        }

        async function submitApproveFromModal() {
            const id = document.getElementById('editArticleId').value;
            await submitEditNews();
            await approveNews(id);
        }

        // 9. Modal Thêm tin thủ công
        function openAddModal() {
            document.getElementById('addTitle').value = '';
            document.getElementById('addSourceName').value = 'RentHome News';
            document.getElementById('addCategory').value = 'Tin thị trường';
            document.getElementById('addSourceUrl').value = '';
            document.getElementById('addImageUrl').value = '';
            document.getElementById('addSummary').value = '';
            document.getElementById('addIsFeatured').checked = false;
            document.getElementById('addModal').classList.add('show');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('show');
        }

        async function submitCreateNews() {
            const payload = {
                title: document.getElementById('addTitle').value.trim(),
                source_name: document.getElementById('addSourceName').value.trim() || 'RentHome News',
                category: document.getElementById('addCategory').value,
                source_url: document.getElementById('addSourceUrl').value.trim() || window.location.origin,
                image_url: document.getElementById('addImageUrl').value.trim() || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
                summary: document.getElementById('addSummary').value.trim(),
                is_featured: document.getElementById('addIsFeatured').checked
            };

            if (!payload.title || !payload.summary) {
                alert('Vui lòng nhập Tiêu đề và Tóm tắt bài viết!');
                return;
            }

            try {
                const res = await fetch('/api/kiem-duyet/news', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã thêm bài viết thành công!');
                    closeAddModal();
                    loadNews();
                } else {
                    alert(data.message || 'Lỗi khi thêm bài viết!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối máy chủ!');
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>
