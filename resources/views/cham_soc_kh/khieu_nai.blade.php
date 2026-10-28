<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Khiếu Nại - RentHome CSKH</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

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

        .page-title-box h1 i {
            color: var(--warning);
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

        /* ================= 3 SIMPLE KPI CARDS ================= */
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

        .kpi-icon.amber { background: var(--warning-light); color: #d97706; }
        .kpi-icon.emerald { background: var(--success-light); color: var(--success); }
        .kpi-icon.blue { background: var(--primary-light); color: var(--primary); }

        /* ================= BƯỚC 1: CATEGORY TABS (TAB PHÂN LOẠI) ================= */
        .category-tabs-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            background: var(--bg-card);
            padding: 8px 12px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
            flex-wrap: wrap;
        }

        .category-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .category-tab-btn i {
            font-size: 14px;
        }

        .category-tab-btn:hover {
            color: var(--primary);
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .category-tab-btn.active {
            background: #e0f2fe;
            color: #0284c7;
            font-weight: 700;
            border-color: #bae6fd;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.12);
        }

        .category-tab-btn.active i {
            color: #0284c7;
        }

        .category-tab-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #64748b;
            transition: 0.2s;
        }

        .category-tab-btn.active .category-tab-badge {
            background: #0284c7;
            color: #ffffff;
        }

        /* ================= FILTER & TABLE CARD ================= */
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
        }

        .tab-btn.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .search-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .input-search, .select-reason {
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

        .input-search:focus, .select-reason:focus {
            border-color: var(--primary);
            background: #fff;
        }

        /* ================= TABLE ================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .complaint-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .complaint-table th {
            background: #f8fafc;
            padding: 13px 18px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid var(--border-color);
        }

        .complaint-table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            vertical-align: middle;
        }

        .complaint-table tr:hover td {
            background-color: #f8fafc;
        }

        .reason-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--warning-light);
            color: #b45309;
            margin-bottom: 4px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending {
            background: var(--warning-light);
            color: #b45309;
        }

        .status-resolved {
            background: var(--success-light);
            color: #047857;
        }

        .btn-sm-act {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }

        .btn-handle {
            background: var(--primary-light);
            color: var(--primary);
        }

        .btn-handle:hover {
            background: var(--primary);
            color: #fff;
        }

        .empty-box {
            padding: 52px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-box i {
            font-size: 38px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .empty-box h3 {
            font-size: 15.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ================= SIMPLE MODAL ================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
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

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 14px 20px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .summary-info-box {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13.5px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        @media (max-width: 992px) {
            .kpi-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    @php
        $rawComplaints = $complaints ?? [];
        $complaintList = collect($rawComplaints);
        $items = ($rawComplaints instanceof \Illuminate\Pagination\LengthAwarePaginator)
            ? collect($rawComplaints->items())
            : $complaintList;

        $statsArr = $stats ?? [];
        $pendingComplaints = $statsArr['pending_complaints'] ?? ($complaintCount ?? $items->filter(fn($c) => ($c->status ?? 'pending') !== 'resolved')->count());
        $resolvedComplaints = $statsArr['resolved_complaints'] ?? $items->filter(fn($c) => ($c->status ?? '') === 'resolved')->count();
        $totalComplaints = $statsArr['total_complaints'] ?? ($pendingComplaints + $resolvedComplaints);
    @endphp

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
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Quản lý Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill" id="sidebarComplaintBadge">{{ $pendingComplaints }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.notifications') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                    @php
                        $cskhUnreadCount = \App\Models\Notification::where(function ($q) {
                            $q->where('role', 'cskh')
                              ->orWhere('role', 'cham_soc_kh')
                              ->orWhere('role', 'chamsockh');
                        })->where('is_read', false)->count();
                    @endphp
                    @if($cskhUnreadCount > 0)
                        <span class="nav-badge badge-warning-pill" id="sidebarNotifBadge">{{ $cskhUnreadCount }}</span>
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
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Quản Lý Khiếu Nại
                </h1>
                <p>Tiếp nhận, kiểm tra đối soát và xử lý các phản ánh vi phạm, khiếu nại từ khách hàng thuê nhà</p>
            </div>
            <button type="button" class="btn btn-outline" onclick="window.location.reload()">
                <i class="fa-solid fa-rotate"></i> Làm mới
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- 3 THẺ THỐNG KÊ ĐƠN GIẢN -->
        <section class="kpi-grid-3">
            <div class="kpi-card" onclick="switchTab('pending')">
                <div>
                    <div class="kpi-val" id="kpiPending">{{ $pendingComplaints }}</div>
                    <div class="kpi-label">Đơn Chờ Xử Lý</div>
                </div>
                <div class="kpi-icon amber">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="switchTab('resolved')">
                <div>
                    <div class="kpi-val" id="kpiResolved">{{ $resolvedComplaints }}</div>
                    <div class="kpi-label">Đã Giải Quyết Xong</div>
                </div>
                <div class="kpi-icon emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="kpi-card" onclick="switchTab('all')">
                <div>
                    <div class="kpi-val">{{ $totalComplaints }}</div>
                    <div class="kpi-label">Tổng Đơn Khiếu Nại</div>
                </div>
                <div class="kpi-icon blue">
                    <i class="fa-solid fa-inbox"></i>
                </div>
            </div>
        </section>

        <!-- BẢNG DANH SÁCH TICKET KHIẾU NẠI -->
        <section class="card-panel">
            <div class="filter-header">
                <div class="tabs-box" id="complaintTabs">
                    <button type="button" class="tab-btn active" data-status="all" onclick="switchTab('all')">Tất cả</button>
                    <button type="button" class="tab-btn" data-status="pending" onclick="switchTab('pending')">Chờ xử lý</button>
                    <button type="button" class="tab-btn" data-status="resolved" onclick="switchTab('resolved')">Đã giải quyết</button>
                </div>

                <div class="search-controls">
                    <input type="text" id="searchInput" class="input-search" placeholder="Tìm người khiếu nại, SĐT, bài đăng, mã khiếu nại..." oninput="filterComplaints()">
                    <select id="reasonFilter" class="select-reason" onchange="filterComplaints()">
                        <option value="">Tất cả lý do khiếu nại</option>
                        <option value="sai giá">Giá thực tế khác giá niêm yết</option>
                        <option value="hình ảnh">Hình ảnh / Thông tin sai thực tế</option>
                        <option value="thái độ">Thái độ nhân viên phụ trách</option>
                        <option value="đã thuê">Phòng đã cho thuê nhưng chưa gỡ</option>
                    </select>
                </div>
            </div>

            <!-- DANH SÁCH KHIẾU NẠI DẠNG KHUNG THẺ (CARD GRID) -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-4" id="complaintGrid">
                    @forelse($tickets as $ticket)
                        @php
                            $cId = (string)($ticket->_id ?? $ticket->id);
                            $ticketCode = $ticket->complaint_code ?? ('#KN-' . strtoupper(substr($cId, -5)));
                            if (!str_starts_with($ticketCode, '#')) {
                                $ticketCode = '#' . $ticketCode;
                            }
                            $isResolved = strtolower($ticket->status ?? 'pending') === 'resolved';
                            $isEscalated = !empty($ticket->is_escalated) || ($ticket->status ?? '') === 'escalated';
                            $statusKey = $isResolved ? 'resolved' : ($isEscalated ? 'escalated' : 'pending');

                            $senderName = $ticket->name ?? ($ticket->sender_name ?? ($ticket->customer_name ?? ($ticket->user->account_name ?? ($ticket->user->username ?? 'Khách thuê / Người dùng'))));
                            $senderPhone = $ticket->phone ?? ($ticket->sender_phone ?? ($ticket->user->phone ?? '---'));
                            $senderEmail = $ticket->email ?? ($ticket->user->email ?? null);

                            $post = $ticket->post;
                            $postId = (string)($ticket->post_id ?? ($ticket->target_id ?? ($post ? ($post->_id ?? $post->id) : '')));
                            $postTitle = $post ? ($post->title ?? 'Bài đăng trên hệ thống') : ($ticket->post_title ?? 'Bài đăng trên hệ thống');
                            $buildingName = $ticket->building->name ?? ($ticket->building_name ?? ($post->building->name ?? null));
                            $postUrl = !empty($postId) ? url('/chitietbaidang/' . $postId) : null;

                            $staffName = $ticket->staff->account_name ?? ($ticket->staff_name ?? ($post->user->account_name ?? null));

                            $reason = $ticket->reason ?? ($ticket->subject ?? 'Phản ánh thông tin');
                            $content = $ticket->content ?? ($ticket->description ?? ($ticket->message ?? 'Không có mô tả chi tiết.'));

                            // Xử lý danh sách ảnh đính kèm (hỗ trợ cả mảng images và image đơn lẻ)
                            $rawImages = $ticket->images;
                            if (!is_array($rawImages)) {
                                $rawImages = !empty($rawImages) ? [$rawImages] : (!empty($ticket->image) ? [$ticket->image] : []);
                            }
                            $imagesList = [];
                            foreach ($rawImages as $imgItem) {
                                if (empty($imgItem)) continue;
                                $imgStr = (string)$imgItem;
                                if (str_starts_with($imgStr, 'http://') || str_starts_with($imgStr, 'https://') || str_starts_with($imgStr, 'data:')) {
                                    $imagesList[] = $imgStr;
                                } elseif (str_starts_with($imgStr, '/storage/') || str_starts_with($imgStr, 'storage/')) {
                                    $imagesList[] = asset(ltrim($imgStr, '/'));
                                } else {
                                    $imagesList[] = asset('storage/' . ltrim($imgStr, '/'));
                                }
                            }
                            $hasImages = count($imagesList) > 0;
                            $firstImage = $hasImages ? $imagesList[0] : null;
                            $moreCount = count($imagesList) > 1 ? (count($imagesList) - 1) : 0;
                        @endphp

                        <!-- Thẻ Khiếu Nại (Card Layout) -->
                        <div class="complaint-card bg-white rounded-xl shadow border border-slate-200 flex flex-col overflow-hidden transition-all duration-200 hover:shadow-md hover:border-slate-300"
                             data-id="{{ $cId }}" 
                             data-status="{{ $statusKey }}">
                            
                            <!-- 1. KHU VỰC HÌNH ẢNH (TOP) -->
                            @if($hasImages)
                                <div class="relative h-48 w-full bg-slate-100 overflow-hidden cursor-pointer group" 
                                     onclick="zoomEvidenceImage('{{ $firstImage }}')" 
                                     title="Nhấn để xem ảnh phóng to">
                                    <img src="{{ $firstImage }}" alt="Bằng chứng khiếu nại" class="h-48 w-full object-cover rounded-t-xl group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/25 transition-colors"></div>
                                    @if($moreCount > 0)
                                        <span class="absolute bottom-2.5 right-2.5 bg-slate-900/80 backdrop-blur-xs text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-md">
                                            <i class="fa-regular fa-images text-[11px]"></i> +{{ $moreCount }} ảnh
                                        </span>
                                    @endif
                                    <span class="absolute top-2.5 left-2.5 bg-slate-900/70 backdrop-blur-xs text-white text-[11px] font-mono font-bold px-2 py-0.5 rounded shadow-xs">
                                        {{ $ticketCode }}
                                    </span>
                                </div>
                            @else
                                <div class="h-48 w-full bg-slate-50 border-b border-slate-100 rounded-t-xl flex flex-col items-center justify-center text-slate-400 gap-2 relative">
                                    <span class="absolute top-2.5 left-2.5 bg-slate-200/80 text-slate-600 text-[11px] font-mono font-bold px-2 py-0.5 rounded">
                                        {{ $ticketCode }}
                                    </span>
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300">
                                        <i class="fa-regular fa-image text-2xl"></i>
                                    </div>
                                    <span class="text-xs font-medium text-slate-400">Không có hình ảnh đính kèm</span>
                                </div>
                            @endif

                            <!-- 2. KHU VỰC TRẠNG THÁI & BÀI ĐĂNG (HEADER) -->
                            <div class="p-4 pb-2">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    @if(($ticket->is_escalated == true || $isEscalated) && $ticket->status == 'resolved')
                                        <span class="status-pill bg-teal-100 text-teal-800 border border-teal-200 font-bold" id="badge_{{ $cId }}">
                                            👑 Admin đã giải quyết
                                        </span>
                                    @elseif(($ticket->is_escalated == true || $isEscalated) && ($ticket->status == 'pending' || $ticket->status == 'escalated' || $ticket->status != 'resolved'))
                                        <span class="status-pill bg-rose-100 text-rose-800 border border-rose-200" id="badge_{{ $cId }}">
                                            📌 Đang chờ Admin
                                        </span>
                                    @elseif($isResolved)
                                        <span class="status-pill status-resolved" id="badge_{{ $cId }}">
                                            <i class="fa-solid fa-circle-check"></i> Đã giải quyết
                                        </span>
                                    @else
                                        <span class="status-pill status-pending" id="badge_{{ $cId }}">
                                            <i class="fa-solid fa-clock"></i> Chờ xử lý
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                        <i class="fa-regular fa-clock text-[11px]"></i>
                                        {{ !empty($ticket->created_at) ? \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') : 'Mới gửi' }}
                                    </span>
                                </div>

                                <div>
                                    @if(!empty($postTitle) || !empty($buildingName))
                                        <a href="{{ $postUrl ?? 'javascript:void(0)' }}" 
                                           {{ !empty($postUrl) ? 'target="_blank"' : '' }} 
                                           class="font-bold text-blue-600 hover:text-blue-800 text-sm sm:text-base flex items-center gap-1.5 group line-clamp-1 transition-colors" 
                                           title="{{ $postTitle ?? ($buildingName ?? 'Bài đăng') }}">
                                            <i class="fa-solid fa-house text-blue-500 text-xs shrink-0 group-hover:scale-110 transition-transform"></i>
                                            <span class="truncate">{{ $postTitle ?? ($buildingName ?? 'Bài đăng bị khiếu nại') }}</span>
                                            @if(!empty($postUrl))
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"></i>
                                            @endif
                                        </a>
                                        @if(!empty($buildingName) && !empty($postTitle))
                                            <div class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-building text-[10px] text-slate-400"></i>
                                                <span>{{ $buildingName }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="font-bold text-slate-700 text-sm flex items-center gap-1.5">
                                            <i class="fa-solid fa-shield text-slate-400 text-xs"></i>
                                            <span>Khiếu nại bài đăng hệ thống</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. KHU VỰC NỘI DUNG (BODY) -->
                            <div class="px-4 py-2 space-y-2.5">
                                <div class="flex items-center justify-between text-xs text-slate-600 pt-1 border-t border-slate-100">
                                    <div class="flex items-center gap-1.5 font-bold text-slate-800 truncate" title="{{ $senderName }}">
                                        <i class="fa-regular fa-user text-slate-400 text-xs shrink-0"></i>
                                        <span class="truncate">{{ $senderName }}</span>
                                    </div>
                                    <div class="text-slate-500 font-medium shrink-0 flex items-center gap-1">
                                        <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                        <span>{{ $senderPhone }}</span>
                                    </div>
                                </div>

                                <div>
                                    <span class="reason-tag" style="margin-bottom: 0;">
                                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 10px; margin-right: 3px;"></i>
                                        {{ $reason }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-lg text-sm text-gray-700 line-clamp-3 leading-relaxed border border-slate-100" title="{{ $content }}">
                                    {{ $content }}
                                </div>
                            </div>

                            <!-- 4. KHU VỰC THAO TÁC (FOOTER) -->
                            <div class="mt-auto border-t border-slate-100 p-4 pt-3 bg-slate-50/50">
                                @if(($ticket->is_escalated == true || $isEscalated) && $ticket->status == 'resolved')
                                    @php
                                        $verdictMsg = $ticket->reply_message ?? ($ticket->resolution_note ?? 'Ban Quản Trị RentHome đã trực tiếp thụ lý, giải quyết và đóng hồ sơ khiếu nại.');
                                        $resolvedTime = !empty($ticket->resolved_at) ? \Carbon\Carbon::parse($ticket->resolved_at)->format('H:i - d/m/Y') : (!empty($ticket->updated_at) ? \Carbon\Carbon::parse($ticket->updated_at)->format('H:i - d/m/Y') : 'Mới đây');
                                    @endphp
                                    <button type="button" 
                                            onclick="openAdminVerdictModal('{{ $cId }}', @js($ticketCode), @js($verdictMsg), @js($resolvedTime))"
                                            class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 hover:text-blue-600 shadow-2xs transition-all active:scale-[0.98] cursor-pointer">
                                        <i class="fa-regular fa-file-lines text-blue-600"></i>
                                        <span>📄 Xem Biên Bản Xử Lý</span>
                                    </button>
                                @elseif(($ticket->is_escalated == true || $isEscalated) && $ticket->status != 'resolved')
                                    <div class="w-full text-center py-2 bg-gray-200 text-gray-500 rounded font-medium cursor-not-allowed select-none">
                                        🔒 Đang chờ Admin giải quyết
                                    </div>
                                @else
                                    <button type="button" 
                                            onclick="openHandleModal('{{ $cId }}', 'complaint')"
                                            class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-all active:scale-[0.98] {{ $isResolved ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-500 hover:bg-amber-600 text-white' }}">
                                        <i class="fa-solid {{ $isResolved ? 'fa-circle-check' : 'fa-file-signature' }}"></i>
                                        <span>Xử lý & Phản hồi</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                    @empty
                        <div class="col-span-full py-16 text-center text-slate-400">
                            <div class="empty-box">
                                <i class="fa-solid fa-shield-heart text-4xl text-slate-300 mb-3"></i>
                                <h3 class="text-base font-bold text-slate-700">Không tìm thấy đơn khiếu nại nào</h3>
                                <p class="text-xs text-slate-500 mt-1">Không có khiếu nại nào phù hợp với bộ lọc hiện tại.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
        </section>
    </main>

    @php
        if (empty($ticket) || empty($ticket->id)) {
            $ticket = $tickets->first() ?? (object)['id' => '0', '_id' => '0'];
        }
    @endphp

    <!-- ================= MODAL XỬ LÝ & PHẢN HỒI KHIẾU NẠI (ADMINISTRATIVE MODAL) ================= -->
    <div id="handleTicketModalBackdrop" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] transition-opacity duration-200 opacity-0 pointer-events-none flex items-center justify-center p-3 sm:p-6"
         onclick="if(event.target === this) closeHandleModal()">
        
        <div id="handleTicketModalContainer" 
             class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-200 flex flex-col max-h-[92vh]">
            
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div id="hdModalIcon" class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-bold shadow-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-slate-800 text-base" id="hdModalTitle">Chi Tiết & Phản Hồi Khiếu Nại</h3>
                            <span id="hdTicketCode" class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">#TK</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5" id="hdTicketSubtitle">Tiếp nhận, kiểm tra đối soát và xử lý khiếu nại của khách hàng</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span id="hdStatusBadge" class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs">
                        <!-- Chờ xử lý hoặc Đã giải quyết -->
                    </span>
                    <button type="button" 
                            onclick="closeHandleModal()" 
                            class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Body Modal (Scrollable) -->
            <div class="p-4 sm:p-6 overflow-y-auto text-slate-700 flex-1" id="hdModalBody">
                
                <!-- Loading State -->
                <div id="hdLoadingSpinner" class="py-16 flex flex-col items-center justify-center text-slate-400 space-y-3">
                    <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                    <span class="text-xs font-semibold">Đang tải thông tin chi tiết hồ sơ...</span>
                </div>

                <!-- Main Content (2 Columns Grid) -->
                <div id="hdContentWrapper" class="hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
                        
                        <!-- ================= CỘT TRÁI: THÔNG TIN GỐC (READ-ONLY) ================= -->
                        <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200/90 space-y-4">
                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/80">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-lines text-blue-600"></i>
                                    Thông tin khiếu nại gốc
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400 bg-white px-2 py-0.5 rounded border border-slate-200">Chỉ xem</span>
                            </div>

                            <!-- 1. Bài đăng vi phạm & Nhân viên sale phụ trách -->
                            <div id="hdPostBox" class="bg-white p-3.5 rounded-xl border border-blue-200/80 shadow-2xs space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[10px] font-bold text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fa-solid fa-house-chimney text-blue-600"></i> Bài đăng bị khiếu nại
                                        </div>
                                        <div class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 mt-0.5" id="hdPostTitle">
                                            --
                                        </div>
                                    </div>
                                    <a id="hdPostLink" href="#" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shrink-0 inline-flex items-center gap-1 shadow-xs transition active:scale-95">
                                        <span>Xem bài</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </div>
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs" id="hdStaffRow">
                                    <span class="text-slate-500 flex items-center gap-1">
                                        <i class="fa-solid fa-user-tie text-slate-400"></i> Nhân viên phụ trách:
                                    </span>
                                    <strong class="text-slate-800 font-semibold" id="hdStaffName">--</strong>
                                </div>
                            </div>

                            <!-- 2. Thông tin Người khiếu nại & Thời gian -->
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                        <i class="fa-solid fa-user text-slate-400"></i> Khách hàng:
                                    </span>
                                    <strong class="text-slate-800 font-bold" id="hdSenderName">--</strong>
                                </div>
                                <div class="flex items-center justify-between" id="hdSenderPhone">
                                    <span class="text-slate-500 flex items-center gap-1.5">
                                        <i class="fa-solid fa-phone text-slate-400 text-[11px]"></i> Số điện thoại:
                                    </span>
                                    <span class="text-slate-800 font-medium">--</span>
                                </div>
                                <div class="flex items-center justify-between" id="hdSenderEmail">
                                    <span class="text-slate-500 flex items-center gap-1.5">
                                        <i class="fa-regular fa-envelope text-slate-400 text-[11px]"></i> Email:
                                    </span>
                                    <span class="text-slate-800 font-medium">--</span>
                                </div>
                                <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 text-[11px] text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-slate-400"></i> Thời gian gửi:
                                    </span>
                                    <span id="hdCreatedAt" class="font-medium text-slate-700">--</span>
                                </div>
                            </div>

                            <!-- 3. Lý do khiếu nại & Lời khách hàng đã gõ -->
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-exclamation text-amber-500"></i> Lý do khiếu nại:
                                    </span>
                                    <span id="hdSubjectTag" class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <!-- Subject -->
                                    </span>
                                </div>

                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-2">
                                    Lời khách hàng khiếu nại:
                                </label>
                                <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-wrap bg-slate-50 p-3 rounded-lg border border-slate-200/70 select-text" id="hdMessageText">
                                    <!-- Customer message -->
                                </div>
                            </div>

                            <!-- 4. Hình ảnh bằng chứng (Dạng Grid thumbnail nhỏ w-24 h-24) -->
                            <div id="hdImagesContainer" class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs hidden space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-regular fa-images text-blue-600"></i>
                                        Hình ảnh bằng chứng (<span id="hdImagesCount">0</span>)
                                    </span>
                                    <span class="text-[11px] text-slate-400 italic">Nhấn vào ảnh để phóng to</span>
                                </div>
                                <div id="hdImagesGrid" class="flex flex-wrap gap-2.5 pt-1">
                                    <!-- Small thumbnails rendered here -->
                                </div>
                            </div>
                        </div>

                        <!-- ================= CỘT PHẢI: KHU VỰC XỬ LÝ (ACTIONS) ================= -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/80">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-gavel text-emerald-600"></i>
                                    Khu vực xử lý phản hồi
                                </span>
                            </div>

                            <!-- TRƯỜNG HỢP 1: ĐÃ GIẢI QUYẾT XONG (READ-ONLY FEEDBACK) -->
                            <div id="hdResolvedView" class="p-5 rounded-2xl bg-emerald-50/80 border border-emerald-200 space-y-3 hidden">
                                <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                                    <span>Khiếu nại này đã được giải quyết</span>
                                </div>
                                <div class="text-xs text-emerald-700" id="hdResolvedTime"></div>
                                <div class="mt-2">
                                    <span class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider block mb-1">
                                        Chỉ thị / Yêu cầu xử lý đã gửi Người đăng bài:
                                    </span>
                                    <div class="text-xs sm:text-sm text-slate-800 bg-white p-3.5 rounded-xl border border-emerald-200 whitespace-pre-wrap leading-relaxed shadow-2xs" id="hdResolvedMessageText"></div>
                                </div>
                                <div class="text-[11px] text-emerald-700 flex items-center gap-1.5 bg-emerald-100/60 p-2.5 rounded-lg border border-emerald-200">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Hồ sơ đã giải quyết hoàn tất. Khách hàng chỉ nhận thông báo trạng thái; chỉ thị trên đã gửi đến người đăng bài.</span>
                                </div>
                            </div>

                            <!-- TRƯỜNG HỢP 2: CHƯA GIẢI QUYẾT -> 2 LỰA CHỌN THAO TÁC (TABS) -->
                            <div id="hdPendingForm" class="space-y-4">
                                <!-- Thanh chọn 2 lựa chọn xử lý -->
                                <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl border border-slate-200 gap-1">
                                    <button type="button" 
                                            id="btnTabResolve" 
                                            onclick="switchActionTab('resolve')" 
                                            class="py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all bg-white text-emerald-700 shadow-xs border border-emerald-200">
                                        <i class="fa-solid fa-paper-plane text-emerald-600"></i>
                                        <span>1. Trực tiếp giải quyết</span>
                                    </button>
                                    <button type="button" 
                                            id="btnTabEscalate" 
                                            onclick="switchActionTab('escalate')" 
                                            class="py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-rose-600 hover:bg-slate-50">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                                        <span>2. Leo thang lên Admin</span>
                                    </button>
                                </div>

                                <!-- LỰA CHỌN 1: TRỰC TIẾP GIẢI QUYẾT (MẶC ĐỊNH) -->
                                <div id="actionPanelResolve" class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-200/90 shadow-2xs space-y-4">
                                    <form id="cskhResolveForm" method="POST" action="{{ route('cskh.complaints.resolve', $ticket->id) }}">
                                        @csrf
                                        <div class="space-y-4">
                                            <div>
                                                <label for="hdReplyInput" class="block text-xs font-bold text-slate-800 flex items-center justify-between mb-1.5">
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="fa-solid fa-bullhorn text-emerald-600"></i>
                                                        Chỉ thị / Yêu cầu xử lý gửi đến Người đăng bài: <span class="text-rose-500">*</span>
                                                    </span>
                                                    <span class="text-[11px] font-normal text-slate-400" id="hdCharCount">0 ký tự</span>
                                                </label>
                                                <textarea id="hdReplyInput" 
                                                          name="reply_message"
                                                          rows="6" 
                                                          class="w-full resize-none border border-slate-300 rounded-xl p-3.5 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-all placeholder-slate-400 leading-relaxed" 
                                                          placeholder="Nhập chỉ thị điều hành, yêu cầu đính chính thông tin bài đăng hoặc biện pháp xử lý gửi trực tiếp đến nhân viên quản lý bài đăng..."></textarea>
                                            </div>

                                            <div class="flex items-start gap-2 text-[11px] text-slate-600 bg-emerald-50/60 p-3 rounded-xl border border-emerald-200/60">
                                                <i class="fa-solid fa-circle-info text-emerald-600 mt-0.5 shrink-0"></i>
                                                <span>Khách hàng báo cáo sẽ không đọc được nội dung này. Hệ thống chỉ gửi thông báo trạng thái 'Đã giải quyết' cho khách.</span>
                                            </div>

                                            <div class="space-y-2.5 pt-1">
                                                <button type="button" 
                                                        id="btnSubmitResolution" 
                                                        onclick="submitTicketResolution()" 
                                                        class="w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]">
                                                    <i class="fa-solid fa-paper-plane shrink-0"></i>
                                                    <span class="whitespace-nowrap">Gửi phản hồi & Đóng Ticket</span>
                                                </button>
                                                <button type="submit" 
                                                        formaction="{{ route('cskh.ticket.reject', $ticket->id) }}" 
                                                        class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-300 hover:border-rose-300 font-bold text-xs sm:text-sm shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]">
                                                    <i class="fa-solid fa-ban text-rose-500 shrink-0"></i>
                                                    <span class="whitespace-nowrap">⛔ Bác bỏ khiếu nại (Báo cáo sai)</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- LỰA CHỌN 2: LEO THANG ĐẶC QUYỀN (TRÌNH ADMIN) -->
                                <div id="actionPanelEscalate" class="bg-rose-50/60 rounded-2xl p-4 sm:p-5 border border-rose-200 shadow-2xs space-y-4 hidden">
                                    <div class="flex items-center gap-2 text-rose-700 font-bold text-xs uppercase tracking-wider">
                                        <i class="fa-solid fa-shield-halved text-rose-600"></i>
                                        <span>Chuyển tiếp hồ sơ lên Admin cấp cao</span>
                                    </div>

                                    <div>
                                        <label for="hdEscalateNoteInput" class="block text-xs font-bold text-slate-800 flex items-center justify-between mb-1.5">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-lock text-rose-600"></i>
                                                Ghi chú nội bộ cho Admin (Khách hàng không thấy): <span class="text-rose-500">*</span>
                                            </span>
                                            <span class="text-[11px] font-normal text-rose-400" id="hdEscalateCharCount">0 ký tự</span>
                                        </label>
                                        <textarea id="hdEscalateNoteInput" 
                                                  rows="5" 
                                                  class="w-full resize-none border border-rose-300 rounded-xl p-3.5 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all placeholder-rose-400 bg-white leading-relaxed" 
                                                  placeholder="Ví dụ: Đã gọi điện xác minh, chủ nhà lừa đảo tiền cọc, yêu cầu Admin xóa bài đăng vi phạm và khóa tài khoản người đăng..."></textarea>
                                    </div>

                                    <div class="flex items-start gap-2 text-[11px] text-rose-700 bg-white/80 p-3 rounded-xl border border-rose-200">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5 shrink-0"></i>
                                        <span>Sau khi trình lên Admin, thẻ khiếu nại này sẽ được chuyển sang trạng thái <b>📌 Đang chờ Admin</b> và CSKH sẽ bị khóa thao tác xử lý đối với thẻ này.</span>
                                    </div>

                                    <button type="button" 
                                            id="btnEscalateModal" 
                                            onclick="submitTicketEscalation()" 
                                            class="w-full py-3 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white border border-rose-700 font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.98]">
                                        <span>⚠️ Trình lên Admin giải quyết</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end shrink-0">
                <button type="button" 
                        onclick="closeHandleModal()" 
                        class="px-5 py-2 rounded-xl border border-slate-300 hover:bg-slate-200/70 text-slate-700 font-semibold text-xs sm:text-sm transition-all cursor-pointer">
                    Đóng
                </button>
            </div>

        </div>
    </div>

    <!-- ================= MODAL XEM BIÊN BẢN XỬ LÝ (ADMIN VERDICT MODAL - READ ONLY) ================= -->
    <div id="adminVerdictModalBackdrop" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9999] transition-opacity duration-200 opacity-0 pointer-events-none flex items-center justify-center p-3 sm:p-6"
         onclick="if(event.target === this) closeAdminVerdictModal()">
        
        <div id="adminVerdictModalContainer" 
             class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-200 flex flex-col max-h-[90vh]">
            
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-lg font-bold shadow-xs">
                        👑
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-slate-800 text-base">Biên Bản Xử Lý Khiếu Nại</h3>
                            <span id="verdictTicketCode" class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-teal-50 text-teal-800 border border-teal-200">#TK</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Phán quyết chính thức từ Ban Quản Trị hệ thống</p>
                    </div>
                </div>

                <button type="button" 
                        onclick="closeAdminVerdictModal()" 
                        class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body Modal (Read-only) -->
            <div class="p-6 overflow-y-auto space-y-4 text-slate-700 flex-1">
                
                <!-- Status & Time Badge Card -->
                <div class="bg-teal-50/70 rounded-xl p-3.5 border border-teal-200 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="status-pill bg-teal-100 text-teal-800 border border-teal-200 text-xs font-bold">
                            👑 Admin đã giải quyết
                        </span>
                    </div>
                    <div class="text-xs text-slate-600 font-medium flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-teal-600"></i>
                        <span>Thời gian: <strong id="verdictResolvedAt" class="text-slate-800 font-bold">--</strong></span>
                    </div>
                </div>

                <!-- Admin Reply Message Box (Read-only) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-gavel text-teal-600"></i>
                        Nội dung phản hồi / Biên bản từ Admin:
                    </label>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-wrap font-normal max-h-64 overflow-y-auto select-text" id="verdictReplyMessage">
                        <!-- Admin's reply_message text -->
                    </div>
                </div>

                <!-- Readonly Notice -->
                <div class="flex items-center gap-2 text-[11px] text-slate-400 bg-slate-50 p-2.5 rounded-lg border border-slate-200/60">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                    <span>Biên bản này ở chế độ chỉ xem (Read-only) và không thể chỉnh sửa.</span>
                </div>

            </div>

            <!-- Footer Modal (Only Close button, NO edit/send buttons) -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end shrink-0">
                <button type="button" 
                        onclick="closeAdminVerdictModal()" 
                        class="px-5 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm transition-all cursor-pointer shadow-2xs">
                    Đóng
                </button>
            </div>

        </div>
    </div>

    <!-- Lightbox Zoom Modal cho hình ảnh bằng chứng -->
    <div id="evidenceImageModal" 
         class="fixed inset-0 z-[10001] bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none"
         onclick="closeEvidenceImage()">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">
            <button type="button" 
                    onclick="closeEvidenceImage()" 
                    class="absolute -top-10 right-0 text-white hover:text-rose-400 text-sm font-bold flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i> Đóng
            </button>
            <img id="evidenceZoomImg" src="" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl border border-white/20" alt="Hình ảnh phóng to">
        </div>
    </div>

    <script>
        let currentTab = '{{ request("status", "all") }}';
        let currentChatTicketId = null;

        document.addEventListener('DOMContentLoaded', () => {
            if (currentTab !== 'all') {
                document.querySelectorAll('#complaintTabs .tab-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.status === currentTab);
                });
            }

            // Theo dõi độ dài ký tự ô nhập liệu phản hồi khách hàng
            const replyInput = document.getElementById('hdReplyInput');
            if (replyInput) {
                replyInput.addEventListener('input', () => {
                    const count = replyInput.value.length;
                    const counter = document.getElementById('hdCharCount');
                    if (counter) counter.innerText = `${count} ký tự`;
                });
            }

            // Theo dõi độ dài ký tự ô nhập ghi chú leo thang Admin
            const escInput = document.getElementById('hdEscalateNoteInput');
            if (escInput) {
                escInput.addEventListener('input', () => {
                    const count = escInput.value.length;
                    const counter = document.getElementById('hdEscalateCharCount');
                    if (counter) counter.innerText = `${count} ký tự`;
                });
            }
        });

        function switchTab(status) {
            currentTab = status;
            document.querySelectorAll('#complaintTabs .tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.status === status);
            });
            filterComplaints();
        }

        function filterComplaints() {
            const keyword = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
            const reasonVal = (document.getElementById('reasonFilter')?.value || '').toLowerCase().trim();

            document.querySelectorAll('.complaint-row, .complaint-card').forEach(item => {
                const itemStatus = item.getAttribute('data-status');
                const text = item.innerText.toLowerCase();

                const matchTab = (currentTab === 'all') || (itemStatus === currentTab);
                const matchKeyword = !keyword || text.includes(keyword);
                const matchReason = !reasonVal || text.includes(reasonVal);

                item.style.display = (matchTab && matchKeyword && matchReason) ? '' : 'none';
            });
        }

        // ========================================================
        // LOGIC MODAL XỬ LÝ & PHẢN HỒI KHIẾU NẠI (ADMINISTRATIVE)
        // ========================================================
        let currentTicketId = null;
        let currentTicketType = 'complaint';

        function switchActionTab(tab) {
            const panelResolve = document.getElementById('actionPanelResolve');
            const panelEscalate = document.getElementById('actionPanelEscalate');
            const btnResolve = document.getElementById('btnTabResolve');
            const btnEscalate = document.getElementById('btnTabEscalate');

            if (!panelResolve || !panelEscalate || !btnResolve || !btnEscalate) return;

            if (tab === 'resolve') {
                panelResolve.classList.remove('hidden');
                panelEscalate.classList.add('hidden');
                btnResolve.className = 'py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all bg-white text-emerald-700 shadow-xs border border-emerald-200';
                btnEscalate.className = 'py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-rose-600 hover:bg-slate-50';
                setTimeout(() => document.getElementById('hdReplyInput')?.focus(), 150);
            } else {
                panelResolve.classList.add('hidden');
                panelEscalate.classList.remove('hidden');
                btnResolve.className = 'py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-emerald-700 hover:bg-slate-50';
                btnEscalate.className = 'py-2.5 px-3 rounded-lg font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 transition-all bg-rose-600 text-white shadow-xs border border-rose-600';
                setTimeout(() => document.getElementById('hdEscalateNoteInput')?.focus(), 150);
            }
        }

        async function openHandleModal(id, type = 'complaint') {
            currentTicketId = id;
            currentTicketType = type;

            const backdrop = document.getElementById('handleTicketModalBackdrop');
            const container = document.getElementById('handleTicketModalContainer');

            // Reset form nhập phản hồi
            const replyInput = document.getElementById('hdReplyInput');
            if (replyInput) replyInput.value = '';
            const charCount = document.getElementById('hdCharCount');
            if (charCount) charCount.innerText = '0 ký tự';

            // Reset form leo thang Admin
            const escInput = document.getElementById('hdEscalateNoteInput');
            if (escInput) escInput.value = '';
            const escCharCount = document.getElementById('hdEscalateCharCount');
            if (escCharCount) escCharCount.innerText = '0 ký tự';

            // Mặc định về Tab 1: Trực tiếp giải quyết
            switchActionTab('resolve');

            // Cập nhật action của form và formaction của nút reject theo ticket id hiện tại
            const formCskh = document.getElementById('cskhResolveForm');
            if (formCskh) {
                formCskh.action = `/cskh/complaints/${id}/resolve`;
            }
            const btnRejectCskh = formCskh?.querySelector('button[formaction]');
            if (btnRejectCskh) {
                btnRejectCskh.setAttribute('formaction', `/cskh/tickets/${id}/reject`);
            }

            // Mở Modal căn giữa với animation mượt
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');

            // Hiển thị trạng thái loading
            document.getElementById('hdLoadingSpinner').classList.remove('hidden');
            document.getElementById('hdContentWrapper').classList.add('hidden');

            try {
                const res = await fetch(`/api/cskh/tickets/${id}/chat`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    alert(data.message || 'Không thể tải chi tiết ticket!');
                    closeHandleModal();
                    return;
                }

                renderHandleModal(data.ticket, data.replies || []);
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối khi tải chi tiết hồ sơ.');
                closeHandleModal();
            } finally {
                document.getElementById('hdLoadingSpinner').classList.add('hidden');
                document.getElementById('hdContentWrapper').classList.remove('hidden');
            }
        }

        function closeHandleModal() {
            const backdrop = document.getElementById('handleTicketModalBackdrop');
            const container = document.getElementById('handleTicketModalContainer');
            if (backdrop && container) {
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                container.classList.remove('scale-100');
                container.classList.add('scale-95');
            }
            currentTicketId = null;
        }

        function renderHandleModal(ticket, replies) {
            const isResolved = (ticket.status === 'resolved');
            const isEscalated = (ticket.is_escalated === true || ticket.status === 'escalated');

            // Cập nhật Header Icon & Tiêu đề
            const iconEl = document.getElementById('hdModalIcon');
            const titleEl = document.getElementById('hdModalTitle');
            const subtitleEl = document.getElementById('hdTicketSubtitle');

            iconEl.className = 'w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-bold shadow-xs';
            iconEl.innerHTML = '<i class="fa-solid fa-shield-halved"></i>';
            titleEl.innerText = 'Chi Tiết & Phản Hồi Khiếu Nại';
            subtitleEl.innerText = 'Tiếp nhận, kiểm tra đối soát và xử lý khiếu nại của khách hàng';

            // Mã phiếu & Badge trạng thái
            document.getElementById('hdTicketCode').innerText = ticket.code || '#TK';
            const statusBadge = document.getElementById('hdStatusBadge');
            if (isEscalated && isResolved) {
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-teal-100 text-teal-800 border border-teal-200';
                statusBadge.innerHTML = '👑 Admin đã giải quyết';
            } else if (isResolved) {
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 border border-emerald-200';
                statusBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đã giải quyết';
            } else if (isEscalated) {
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-rose-100 text-rose-800 border border-rose-200';
                statusBadge.innerHTML = '📌 Đang chờ Admin';
            } else if (ticket.status === 'processing') {
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-sky-100 text-sky-800 border border-sky-200';
                statusBadge.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý';
            } else {
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 border border-amber-200';
                statusBadge.innerHTML = '<i class="fa-solid fa-clock"></i> Chờ xử lý';
            }

            // Thông tin người gửi
            document.getElementById('hdSenderName').innerText = ticket.sender_name || 'Khách hàng';

            const phoneEl = document.getElementById('hdSenderPhone');
            if (ticket.sender_phone && ticket.sender_phone !== '---') {
                phoneEl.style.display = 'flex';
                phoneEl.querySelector('span').innerText = ticket.sender_phone;
            } else {
                phoneEl.style.display = 'none';
            }

            const emailEl = document.getElementById('hdSenderEmail');
            if (ticket.sender_email) {
                emailEl.style.display = 'flex';
                emailEl.querySelector('span').innerText = ticket.sender_email;
            } else {
                emailEl.style.display = 'none';
            }

            document.getElementById('hdCreatedAt').innerText = ticket.created_at_time || 'Mới gửi';

            const staffRow = document.getElementById('hdStaffRow');
            if (ticket.staff_name) {
                staffRow.style.display = 'flex';
                document.getElementById('hdStaffName').innerText = ticket.staff_name;
            } else {
                staffRow.style.display = 'none';
            }

            // Bài đăng liên quan / bị khiếu nại
            const postBox = document.getElementById('hdPostBox');
            if (ticket.post_title) {
                postBox.classList.remove('hidden');
                document.getElementById('hdPostTitle').innerText = ticket.post_title;
                const postLink = document.getElementById('hdPostLink');
                if (ticket.post_url) {
                    postLink.href = ticket.post_url;
                    postLink.classList.remove('hidden');
                } else {
                    postLink.classList.add('hidden');
                }
            } else {
                postBox.classList.add('hidden');
            }

            // Lý do / Chủ đề & Lời nhắn
            document.getElementById('hdSubjectTag').innerText = ticket.subject || 'Khiếu nại bài đăng';
            document.getElementById('hdMessageText').innerText = ticket.initial_message || 'Không có nội dung mô tả.';

            // Hình ảnh bằng chứng (Thu nhỏ dạng thumbnail w-24 h-24 object-cover)
            const imagesContainer = document.getElementById('hdImagesContainer');
            const imagesGrid = document.getElementById('hdImagesGrid');
            const imagesList = Array.isArray(ticket.images) && ticket.images.length > 0 
                ? ticket.images 
                : (ticket.initial_image ? [ticket.initial_image] : []);

            if (imagesList.length > 0) {
                imagesContainer.classList.remove('hidden');
                document.getElementById('hdImagesCount').innerText = imagesList.length;
                imagesGrid.innerHTML = imagesList.map((imgUrl, idx) => `
                    <div class="relative w-24 h-24 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 group cursor-pointer shadow-2xs hover:shadow-md transition-all shrink-0" 
                         onclick="zoomEvidenceImage('${imgUrl}')" 
                         title="Nhấn để phóng to ảnh ${idx + 1}">
                        <img src="${imgUrl}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Bằng chứng ${idx + 1}">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs">
                            <i class="fa-solid fa-magnifying-glass-plus text-base"></i>
                        </div>
                    </div>
                `).join('');
            } else {
                imagesContainer.classList.add('hidden');
                imagesGrid.innerHTML = '';
            }

            // Phần Phản hồi & Xử lý (Administrative, không chat)
            const resolvedView = document.getElementById('hdResolvedView');
            const pendingForm = document.getElementById('hdPendingForm');

            let replyContent = ticket.reply_message || '';
            if (!replyContent && Array.isArray(replies) && replies.length > 0) {
                const cskhReplies = replies.filter(r => r.is_cskh);
                if (cskhReplies.length > 0) {
                    replyContent = cskhReplies[cskhReplies.length - 1].message;
                }
            }

            if (isResolved) {
                resolvedView.classList.remove('hidden');
                pendingForm.classList.add('hidden');

                document.getElementById('hdResolvedTime').innerText = ticket.resolved_at_time ? `Thời gian giải quyết: ${ticket.resolved_at_time}` : '';
                document.getElementById('hdResolvedMessageText').innerText = replyContent || 'Đã ghi nhận, phản hồi và giải quyết thành công.';
            } else {
                resolvedView.classList.add('hidden');
                pendingForm.classList.remove('hidden');

                const replyInput = document.getElementById('hdReplyInput');
                if (replyInput) {
                    replyInput.value = '';
                    document.getElementById('hdCharCount').innerText = '0 ký tự';
                    setTimeout(() => replyInput.focus(), 250);
                }
            }
        }

        async function submitTicketResolution() {
            if (!currentTicketId) return;

            const replyInput = document.getElementById('hdReplyInput');
            const message = replyInput.value.trim();

            if (!message) {
                alert('Vui lòng nhập chỉ thị / yêu cầu xử lý gửi đến Người đăng bài trước khi hoàn tất giải quyết!');
                replyInput.focus();
                return;
            }

            const submitBtn = document.getElementById('btnSubmitResolution');
            const origHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang gửi...';
            }

            try {
                const res = await fetch(`/api/cskh/tickets/${currentTicketId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: message,
                        action: 'reply_and_close'
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    alert(data.message || 'Lỗi khi gửi phản hồi giải quyết ticket!');
                    return;
                }

                // Cập nhật dòng hoặc thẻ trên giao diện hiện tại mà không cần reload
                const ticketId = currentTicketId;
                const targetItem = document.querySelector(`[data-id="${ticketId}"]`);
                if (targetItem) {
                    targetItem.setAttribute('data-status', 'resolved');
                    const cardBtn = targetItem.querySelector('button');
                    if (cardBtn && targetItem.classList.contains('complaint-card')) {
                        cardBtn.className = 'w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-all active:scale-[0.98] bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200';
                        cardBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Xử lý & Phản hồi</span>';
                    }
                }
                const badge = document.getElementById(`badge_${ticketId}`);
                if (badge) {
                    badge.className = 'status-pill status-resolved';
                    badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đã giải quyết';
                }

                closeHandleModal();
                showFloatingToast(data.message || 'Đã gửi phản hồi và giải quyết khiếu nại thành công!');

            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối khi gửi phản hồi giải quyết!');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                }
            }
        }

        async function submitTicketEscalation() {
            if (!currentTicketId) return;

            const escInput = document.getElementById('hdEscalateNoteInput');
            const cleanNote = (escInput ? escInput.value : '').trim();
            if (!cleanNote) {
                alert('Vui lòng nhập ghi chú nội bộ cho Admin trước khi trình giải quyết!');
                if (escInput) escInput.focus();
                return;
            }

            const btn = document.getElementById('btnEscalateModal');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang trình Admin...';
            }

            try {
                const res = await fetch(`/api/cskh/tickets/${currentTicketId}/escalate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        admin_note: cleanNote,
                        cskh_note: cleanNote,
                        reason: cleanNote
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showFloatingToast(data.message || 'Đã trình hồ sơ lên Admin giải quyết thành công!');

                    const ticketId = currentTicketId;
                    const targetItem = document.querySelector(`[data-id="${ticketId}"]`);
                    if (targetItem) {
                        targetItem.setAttribute('data-status', 'escalated');
                        // Nếu đang ở tab 'pending' (Chờ xử lý), ẩn ngay khỏi màn hình
                        if (currentTab === 'pending') {
                            targetItem.style.display = 'none';
                        } else {
                            // Cập nhật nhãn trạng thái trên thẻ
                            const badge = document.getElementById(`badge_${ticketId}`);
                            if (badge) {
                                badge.className = 'status-pill bg-rose-100 text-rose-800 border border-rose-200';
                                badge.innerHTML = '📌 Đang chờ Admin';
                            }
                            // Khóa nút thao tác ở Footer của thẻ
                            const footerArea = targetItem.querySelector('.mt-auto');
                            if (footerArea) {
                                footerArea.innerHTML = '<div class="w-full text-center py-2 bg-gray-200 text-gray-500 rounded font-medium cursor-not-allowed select-none">🔒 Đang chờ Admin giải quyết</div>';
                            }
                        }
                    }

                    // Giảm số đếm đơn chờ xử lý
                    const kpiPending = document.getElementById('kpiPending');
                    if (kpiPending) {
                        const val = parseInt(kpiPending.innerText) || 0;
                        if (val > 0) kpiPending.innerText = val - 1;
                    }
                    const sidebarBadge = document.getElementById('sidebarComplaintBadge');
                    if (sidebarBadge) {
                        const val = parseInt(sidebarBadge.innerText) || 0;
                        if (val > 0) sidebarBadge.innerText = val - 1;
                    }

                    closeHandleModal();
                } else {
                    alert(data.message || 'Không thể trình ticket lên Admin!');
                }
            } catch (e) {
                console.error(e);
                alert('Lỗi kết nối khi gửi yêu cầu trình Admin.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        function escalateCurrentTicket() {
            submitTicketEscalation();
        }

        function zoomEvidenceImage(url) {
            const modal = document.getElementById('evidenceImageModal');
            const img = document.getElementById('evidenceZoomImg');
            if (modal && img) {
                img.src = url;
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
            }
        }

        function closeEvidenceImage() {
            const modal = document.getElementById('evidenceImageModal');
            if (modal) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        function showFloatingToast(msg) {
            let toast = document.getElementById('handleFloatingToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'handleFloatingToast';
                toast.className = 'fixed top-6 right-6 z-[10001] bg-slate-900 text-white px-5 py-3 rounded-xl shadow-2xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 transition-all duration-300 opacity-0 transform translate-y-[-10px] pointer-events-none';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400 text-base"></i> <span>${msg}</span>`;
            toast.classList.remove('opacity-0', 'translate-y-[-10px]');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
            }, 3500);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }

        // ========================================================
        // LOGIC MODAL XEM BIÊN BẢN XỬ LÝ (ADMIN VERDICT - READ ONLY)
        // ========================================================
        function openAdminVerdictModal(ticketId, ticketCode, replyMessage, resolvedAt) {
            const backdrop = document.getElementById('adminVerdictModalBackdrop');
            const container = document.getElementById('adminVerdictModalContainer');
            
            document.getElementById('verdictTicketCode').innerText = ticketCode || '#TK';
            document.getElementById('verdictResolvedAt').innerText = resolvedAt || 'Mới đây';
            
            const messageEl = document.getElementById('verdictReplyMessage');
            messageEl.innerText = replyMessage || 'Admin đã tiếp nhận, giải quyết và đóng hồ sơ khiếu nại thành công.';

            // Nếu nội dung trống hoặc mẫu, chủ động fetch chi tiết mới nhất từ API
            if (!replyMessage || replyMessage === 'Ban Quản Trị RentHome đã trực tiếp thụ lý, giải quyết và đóng hồ sơ khiếu nại.') {
                fetch(`/api/cskh/tickets/${ticketId}/chat`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.ticket) {
                        if (data.ticket.reply_message) {
                            messageEl.innerText = data.ticket.reply_message;
                        }
                        if (data.ticket.resolved_at_time) {
                            document.getElementById('verdictResolvedAt').innerText = data.ticket.resolved_at_time;
                        }
                    }
                })
                .catch(err => console.error(err));
            }

            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }

        function closeAdminVerdictModal() {
            const backdrop = document.getElementById('adminVerdictModalBackdrop');
            const container = document.getElementById('adminVerdictModalContainer');
            if (backdrop && container) {
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                container.classList.remove('scale-100');
                container.classList.add('scale-95');
            }
        }

        // Lắng nghe phím ESC để đóng các Modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAdminVerdictModal();
                closeHandleModal();
                closeEvidenceImage();
            }
        });

        // Tương thích ngược (Backward Compatibility)
        function openChatPanel(id, type) {
            openHandleModal(id, type);
        }
        function closeChatPanel() {
            closeHandleModal();
        }
        function openResolveModal(id, sender, post, staff, reason, content, type = 'complaint') {
            openHandleModal(id, type);
        }
        function closeResolveModal() {
            closeHandleModal();
        }
    </script>
</body>
</html>
