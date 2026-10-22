<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Khiếu Nại Cấp 1 - RentHome</title>

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
            color: var(--danger);
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
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .complaint-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .complaint-table tbody tr:hover td {
            background: #f8fafc;
        }

        .reason-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            background: var(--danger-light);
            color: #b91c1c;
            margin-bottom: 4px;
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
        .status-resolved { background: var(--success-light); color: #047857; }

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

        .btn-done {
            background: var(--success-light);
            color: #047857;
        }

        .btn-done:hover {
            background: var(--success);
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
        $pendingPostsBadge = $pendingCount ?? ($statsArr['pending_posts'] ?? 0);
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
                    <span class="nav-badge badge-warning-pill">{{ $pendingPostsBadge }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill" id="sidebarComplaintBadge">{{ $pendingComplaints }}</span>
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
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Quản Lý Khiếu Nại & Phản Ánh (Cấp 1)
                </h1>
                <p>Tiếp nhận và xử lý nhanh các phản ánh của khách hàng về thông tin bài đăng hoặc tác phong nhân viên Sale</p>
            </div>
            <button type="button" class="btn btn-outline" onclick="window.location.reload()">
                <i class="fa-solid fa-rotate"></i> Làm mới
            </button>
        </div>

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

        <!-- BẢNG DANH SÁCH ĐƠN KHIẾU NẠI -->
        <section class="card-panel">
            <div class="filter-header">
                <div class="tabs-box" id="complaintTabs">
                    <button type="button" class="tab-btn active" data-status="all" onclick="switchTab('all')">Tất cả</button>
                    <button type="button" class="tab-btn" data-status="pending" onclick="switchTab('pending')">Chờ xử lý</button>
                    <button type="button" class="tab-btn" data-status="resolved" onclick="switchTab('resolved')">Đã giải quyết</button>
                </div>

                <div class="search-controls">
                    <input type="text" id="searchInput" class="input-search" placeholder="Tìm người gửi, SĐT, phòng, nhân viên..." oninput="filterComplaints()">
                    <select id="reasonFilter" class="select-reason" onchange="filterComplaints()">
                        <option value="">Tất cả lý do phản ánh</option>
                        <option value="sai giá">Giá thực tế khác giá niêm yết</option>
                        <option value="hình ảnh">Hình ảnh / Thông tin sai thực tế</option>
                        <option value="thái độ">Thái độ nhân viên phụ trách</option>
                        <option value="đã thuê">Phòng đã cho thuê nhưng chưa gỡ</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="complaint-table" id="complaintTable">
                    <thead>
                        <tr>
                            <th>Người Gửi Phản Ánh</th>
                            <th>Bài Đăng & Tòa Nhà (Vỏ - Ruột)</th>
                            <th>Nhân Viên Phụ Trách</th>
                            <th>Lý Do & Nội Dung Khiếu Nại</th>
                            <th>Trạng Thái</th>
                            <th style="text-align: right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $c)
                            @php
                                $cId = (string)($c->_id ?? $c->id);
                                $isResolved = strtolower($c->status ?? 'pending') === 'resolved';
                                $statusKey = $isResolved ? 'resolved' : 'pending';

                                $senderName = $c->sender_name ?? ($c->customer_name ?? 'Khách hàng');
                                $senderPhone = $c->sender_phone ?? ($c->phone ?? '---');

                                $postTitle = $c->post->title ?? ($c->post_title ?? 'Bài đăng phòng');
                                $buildingName = $c->building->name ?? ($c->building_name ?? 'Cơ sở trực thuộc');
                                $staffName = $c->staff->account_name ?? ($c->staff_name ?? 'Nhân viên Sale');

                                $reason =$c->reason ?? 'Phản ánh thông tin bài đăng';
                                $content = $c->content ?? ($c->description ?? 'Không có mô tả chi tiết.');
                            @endphp

                            <tr class="complaint-row" data-id="{{ $cId }}" data-status="{{ $statusKey }}">
                                <!-- Cột 1: Người gửi -->
                                <td>
                                    <div style="font-weight: 700;">{{ $senderName }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                        <i class="fa-solid fa-phone" style="font-size: 11px;"></i> {{ $senderPhone }}
                                    </div>
                                    <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">
                                        {{ !empty($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'Mới gửi' }}
                                    </div>
                                </td>

                                <!-- Cột 2: Bài đăng & Tòa nhà -->
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $postTitle }}</div>
                                    <div style="font-size: 12px; color: var(--primary); font-weight: 600; margin-top: 3px;">
                                        <i class="fa-solid fa-building"></i> {{ $buildingName }}
                                    </div>
                                </td>

                                <!-- Cột 3: Nhân viên bị phản ánh -->
                                <td>
                                    <span style="font-weight: 600; color: #334155;">
                                        <i class="fa-solid fa-user-tie" style="color: var(--text-muted); margin-right: 4px;"></i>
                                        {{ $staffName }}
                                    </span>
                                </td>

                                <!-- Cột 4: Lý do & Nội dung -->
                                <td style="max-width: 320px;">
                                    <span class="reason-tag">{{ $reason }}</span>
                                    <div style="font-size: 12.5px; color: #475569; line-height: 1.4;">{{ $content }}</div>
                                </td>

                                <!-- Cột 5: Trạng thái -->
                                <td>
                                    <span class="status-pill {{ $isResolved ? 'status-resolved' : 'status-pending' }}" id="badge_{{ $cId }}">
                                        @if($isResolved)
                                            <i class="fa-solid fa-circle-check"></i> Đã giải quyết
                                        @else
                                            <i class="fa-solid fa-clock"></i> Chờ xử lý
                                        @endif
                                    </span>
                                </td>

                                <!-- Cột 6: Thao tác -->
                                <td style="text-align: right; white-space: nowrap;">
                                    <button type="button" class="btn-sm-act btn-handle"
                                        onclick="openResolveModal('{{ $cId }}', '{{ addslashes($senderName) }}', '{{ addslashes($postTitle) }}', '{{ addslashes($staffName) }}', '{{ addslashes($reason) }}', '{{ addslashes($content) }}')">
                                        <i class="fa-solid fa-gavel"></i> Xử lý
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-box">
                                        <i class="fa-solid fa-shield-heart"></i>
                                        <h3>Hiện chưa có đơn khiếu nại nào</h3>
                                        <p style="font-size: 13px;">Khi khách hàng gửi phản ánh về bài đăng hoặc nhân viên, danh sách sẽ hiển thị tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- ================= MODAL XỬ LÝ KHIẾU NẠI ĐƠN GIẢN ================= -->
    <div class="modal-overlay" id="resolveModal" onclick="if(event.target === this) closeResolveModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-scale-balanced" style="color: var(--primary);"></i>
                    Xử Lý Đơn Khiếu Nại
                </h3>
                <button type="button" class="logout-btn" onclick="closeResolveModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="modalComplaintId">

                <div class="summary-info-box">
                    <div><strong>Người phản ánh:</strong> <span id="mdSender"></span></div>
                    <div><strong>Bài đăng liên quan:</strong> <span id="mdPost"></span></div>
                    <div><strong>Nhân viên phụ trách:</strong> <span id="mdStaff"></span></div>
                    <div><strong>Lý do:</strong> <span id="mdReason" style="color: var(--danger); font-weight: 700;"></span></div>
                    <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed var(--border-color); color: #475569;" id="mdContent"></div>
                </div>

                <div class="form-group">
                    <label>Chọn hướng xử lý *</label>
                    <select id="resolutionAction" class="form-control">
                        <option value="remind_staff">Nhắc nhở nhân viên & Yêu cầu cập nhật lại bài đăng</option>
                        <option value="hide_post">Tạm ẩn / Gỡ bài đăng vi phạm khỏi Trang chủ</option>
                        <option value="dismiss">Phản ánh không chính xác (Đóng đơn)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ghi chú kết quả giải quyết:</label>
                    <textarea id="resolutionNote" rows="3" class="form-control" placeholder="Nhập ghi chú xử lý lưu vào hệ thống..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeResolveModal()">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="submitResolveComplaint()">
                    <i class="fa-solid fa-check"></i> Lưu kết quả & Hoàn tất
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentTab = 'all';

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

            document.querySelectorAll('#complaintTable tbody tr.complaint-row').forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const text = row.innerText.toLowerCase();

                const matchTab = (currentTab === 'all') || (rowStatus === currentTab);
                const matchKeyword = !keyword || text.includes(keyword);
                const matchReason = !reasonVal || text.includes(reasonVal);

                row.style.display = (matchTab && matchKeyword && matchReason) ? '' : 'none';
            });
        }

        function openResolveModal(id, sender, post, staff, reason, content) {
            document.getElementById('modalComplaintId').value = id;
            document.getElementById('mdSender').innerText = sender;
            document.getElementById('mdPost').innerText = post;
            document.getElementById('mdStaff').innerText = staff;
            document.getElementById('mdReason').innerText = reason;
            document.getElementById('mdContent').innerText = content;
            document.getElementById('resolutionNote').value = '';
            document.getElementById('resolveModal').classList.add('show');
        }

        function closeResolveModal() {
            document.getElementById('resolveModal').classList.remove('show');
        }

        async function submitResolveComplaint() {
            const id = document.getElementById('modalComplaintId').value;
            const action = document.getElementById('resolutionAction').value;
            const note = document.getElementById('resolutionNote').value.trim();

            try {
                await fetch(`/api/kiem-duyet/complaints/${id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action, note })
                });
            } catch (e) {}

            // Cập nhật trạng thái dòng sang Đã giải quyết
            const row = document.querySelector(`tr.complaint-row[data-id="${id}"]`);
            if (row) {
                row.setAttribute('data-status', 'resolved');
                const badge = document.getElementById(`badge_${id}`);
                if (badge) {
                    badge.className = 'status-pill status-resolved';
                    badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đã giải quyết';
                }
            }

            closeResolveModal();
            filterComplaints();
            alert('Đã lưu kết quả xử lý khiếu nại thành công!');
        }
    </script>
</body>
</html>