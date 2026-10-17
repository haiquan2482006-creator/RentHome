<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vận Hành Tòa Nhà (Admin): {{ $building->name ?? 'Tòa Nhà' }} - RentHome Quản Trị</title>

    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #0284c7;
            --brand-primary-hover: #0369a1;
            --brand-light: #f0f9ff;
            --brand-subtle: #e0f2fe;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-emerald: #059669;
            --accent-emerald-bg: #ecfdf5;
            --accent-red: #dc2626;
            --accent-red-bg: #fef2f2;
            --accent-amber: #d97706;
            --accent-amber-bg: #fffbeb;
            --accent-blue: #3b82f6;
            --accent-blue-bg: #eff6ff;
            --accent-purple: #7c3aed;
            --accent-purple-bg: #f5f3ff;
            --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px 36px 60px;
            min-height: 100vh;
            background-color: var(--bg-main);
            max-width: calc(100vw - var(--sidebar-width));
            width: calc(100% - var(--sidebar-width));
        }

        /* Breadcrumb Bar */
        .breadcrumb-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 10px;
            background-color: #ffffff;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .btn-back-link:hover {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background-color: var(--brand-light);
            transform: translateX(-2px);
        }

        .breadcrumb-trail {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb-trail a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb-trail a:hover {
            color: var(--brand-primary);
        }

        .breadcrumb-trail span.current {
            color: var(--brand-primary);
            font-weight: 700;
        }

        /* Page Top Header */
        .page-header-card {
            background-color: var(--bg-card);
            border-radius: 18px;
            border: 1px solid var(--border-color);
            padding: 22px 28px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .page-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .building-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            border: 1px solid #bae6fd;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.12);
        }

        .page-header-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .page-header-title span.highlight {
            color: var(--brand-primary);
        }

        .type-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .scale-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-act-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-act-create {
            background-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-act-create:hover {
            background-color: var(--brand-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }

        .btn-act-refresh {
            background-color: #ffffff;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-act-refresh:hover {
            background-color: #f8fafc;
            color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        /* ========================================================================= */
        /* KHỐI 1 - HÀNG 3 THẺ THỐNG KÊ "TỶ LỆ LẤP ĐẦY" (GRID 3 CỘT)                */
        /* ========================================================================= */
        .stats-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .rm-stat-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: var(--transition);
        }

        .rm-stat-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
            border-color: #cbd5e1;
        }

        .rm-stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
        }

        .rm-stat-card.stat-scale .rm-stat-icon {
            background-color: #f1f5f9;
            color: #475569;
        }

        .rm-stat-card.stat-empty .rm-stat-icon {
            background-color: #ecfdf5;
            color: #10b981;
        }

        .rm-stat-card.stat-rented .rm-stat-icon {
            background-color: #eff6ff;
            color: #3b82f6;
        }

        .rm-stat-label {
            font-size: 0.775rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .rm-stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
        }

        .rm-stat-card.stat-empty .rm-stat-value { color: #059669; }
        .rm-stat-card.stat-rented .rm-stat-value { color: #2563eb; }

        .rm-stat-sub {
            font-size: 0.775rem;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ========================================================================= */
        /* KHỐI 2 - BẢNG KIỂM SOÁT "TIẾN ĐỘ NHẬP LIỆU" & "DÒNG TIỀN" (GRID 2 CỘT)  */
        /* ========================================================================= */
        .controls-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .rm-control-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 22px 24px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
        }

        .rm-control-card:hover {
            box-shadow: var(--shadow-card);
        }

        .rm-card-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .rm-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .rm-progress-label {
            color: var(--text-secondary);
            font-weight: 600;
        }

        .rm-progress-count {
            font-weight: 800;
            color: var(--text-primary);
        }

        .rm-progress-bar-bg {
            width: 100%;
            height: 10px;
            background-color: #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .rm-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #0284c7, #38bdf8);
            border-radius: 6px;
            transition: width 0.5s ease-out;
        }

        .rm-alert-notice {
            font-size: 0.775rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rm-alert-warning {
            color: #dc2626;
        }

        .rm-alert-success {
            color: #059669;
        }

        .rm-status-badges-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            padding-top: 12px;
            border-top: 1px dashed var(--border-color);
        }

        .rm-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.74rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .rm-badge-green {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .rm-badge-amber {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .rm-badge-red {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .rm-badge-blue {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .rm-revenue-amount {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--brand-primary);
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .rm-revenue-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 16px;
            font-style: italic;
        }

        /* ========================================================================= */
        /* KHỐI 3 - BỘ LỌC & DANH SÁCH CHI TIẾT CÁC PHÒNG CON (LỚP RUỘT)            */
        /* ========================================================================= */
        .content-card-main {
            background-color: var(--bg-card);
            border-radius: 18px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        .content-card-topbar {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background-color: #ffffff;
        }

        .content-card-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-card-title span.count-pill {
            font-size: 0.775rem;
            font-weight: 700;
            padding: 3px 10px;
            background-color: var(--brand-light);
            color: var(--brand-primary);
            border-radius: 12px;
            border: 1px solid var(--brand-subtle);
        }


        /* Filter Controls */
        .filter-controls-row {
            padding: 16px 24px;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .filter-search-box {
            position: relative;
            flex: 1;
            min-width: 260px;
            max-width: 380px;
        }

        .filter-search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .filter-search-input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 0.85rem;
            outline: none;
            background-color: #ffffff;
            color: var(--text-primary);
            transition: var(--transition);
        }

        .filter-search-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .filter-dropdowns {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 9px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 0.825rem;
            font-weight: 600;
            outline: none;
            background-color: #ffffff;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* Data Table (6 Cột) */
        .table-wrap {
            overflow-x: auto;
            width: 100%;
        }

        .rm-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
            table-layout: fixed;
        }

        .rm-data-table thead th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-size: 0.725rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 13px 12px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            white-space: nowrap;
        }

        .rm-data-table thead th:first-child,
        .rm-data-table tbody td:first-child {
            padding-left: 20px;
        }

        .rm-data-table thead th:last-child,
        .rm-data-table tbody td:last-child {
            padding-right: 20px;
        }

        .rm-data-table tbody tr {
            transition: var(--transition);
        }

        .rm-data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .rm-data-table tbody td {
            padding: 13px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-primary);
            vertical-align: middle;
        }

        .rm-data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Room Item Cell */
        .rm-room-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .rm-room-info {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .rm-room-thumb {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .rm-room-code {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--text-primary);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rm-room-title {
            font-size: 0.775rem;
            color: var(--text-secondary);
            margin-top: 2px;
            max-width: 100%;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rm-room-date {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rm-price-text {
            font-weight: 800;
            font-size: 0.9rem;
            color: #0284c7;
            white-space: nowrap;
        }

        .btn-act-eye {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background-color: #f0f9ff;
            color: var(--brand-primary);
            border: 1px solid #bae6fd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-act-eye:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            transform: scale(1.08);
            box-shadow: 0 4px 8px rgba(2, 132, 199, 0.25);
        }


        .no-data-hint {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
            font-size: 0.9rem;
            grid-column: 1 / -1;
        }

        .no-data-hint i {
            font-size: 2.2rem;
            margin-bottom: 10px;
            display: block;
            color: #cbd5e1;
        }

        /* Responsive Styles */
        @media (max-width: 1100px) {
            .stats-grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .rm-data-table {
                min-width: 820px;
            }
        }

        @media (max-width: 900px) {
            .admin-main {
                margin-left: 0;
                width: 100%;
                max-width: 100vw;
                padding: 20px 16px;
            }

            .controls-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .stats-grid-3 {
                grid-template-columns: 1fr;
            }

            .page-header-card {
                padding: 18px;
            }

            .filter-search-box {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin chung của hệ thống RentHome -->
    @include('partials.header-admin')

    <main class="admin-main">
        <!-- 1. Thanh điều hướng & Quay lại -->
        <div class="breadcrumb-bar">
            <a href="{{ url('/qltoa_nha') }}" class="btn-back-link">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Quản Lý Tòa Nhà
            </a>

            <div class="breadcrumb-trail">
                <a href="{{ url('/Admin') }}">Trang chủ</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <a href="{{ url('/qltoa_nha') }}">Quản lý cơ sở</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span class="current">{{ $building->name ?? 'Chi tiết tòa nhà' }}</span>
            </div>
        </div>

        <!-- 2. Header của Trang (Thanh tiêu đề trên cùng) -->
        <div class="page-header-card">
            <div class="page-header-left">
                <div class="building-hero-icon">
                    <i class="fa-solid fa-building-circle-check"></i>
                </div>
                <div>
                    <h1 class="page-header-title">
                        Chi Tiết Vận Hành (Admin): <span class="highlight">{{ $building->name }}</span>
                        <span class="type-badge-pill">
                            <i class="fa-solid fa-tag"></i> {{ $typeText }}
                        </span>
                        <span class="scale-badge-pill">
                            <i class="fa-solid fa-layer-group"></i> {{ $stats->total_scale }} Phòng
                        </span>
                    </h1>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KHỐI 1 - HÀNG 3 THẺ THỐNG KÊ "TỶ LỆ LẤP ĐẦY" (GRID 3 CỘT)                -->
        <!-- ========================================================================= -->
        <div class="stats-grid-3">
            <!-- Card 1 (Tổng quy mô) -->
            <div class="rm-stat-card stat-scale">
                <div class="rm-stat-icon">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <div class="rm-stat-label">Tổng Quy Mô</div>
                    <div class="rm-stat-value">{{ $stats->total_scale }} phòng</div>
                    <div class="rm-stat-sub">Admin đã khai báo</div>
                </div>
            </div>

            <!-- Card 2 (Đang trống) -->
            <div class="rm-stat-card stat-empty">
                <div class="rm-stat-icon">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="rm-stat-label">Phòng Đang Trống</div>
                    <div class="rm-stat-value">{{ $stats->empty_rooms }} phòng</div>
                    <div class="rm-stat-sub">Sẵn sàng cho thuê</div>
                </div>
            </div>

            <!-- Card 3 (Đã cho thuê) -->
            <div class="rm-stat-card stat-rented">
                <div class="rm-stat-icon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <div class="rm-stat-label">Đang Cho Thuê</div>
                    <div class="rm-stat-value">{{ $stats->rented_rooms }} phòng</div>
                    <div class="rm-stat-sub">Đang có khách ở</div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KHỐI 2 - BẢNG KIỂM SOÁT "TIẾN ĐỘ NHẬP LIỆU" & "DÒNG TIỀN" (GRID 2 CỘT)  -->
        <!-- ========================================================================= -->
        <div class="controls-grid-2">
            <!-- Cột trái (Khung Tiến Độ & Kiểm Duyệt của Nhân Viên) -->
            <div class="rm-control-card">
                <div>
                    <div class="rm-card-title">
                        <i class="fa-solid fa-bars-progress" style="color: var(--brand-primary);"></i>
                        Tiến độ khởi tạo dữ liệu (Lớp Ruột)
                    </div>

                    <div class="rm-progress-header">
                        <span class="rm-progress-label">Tỷ lệ hoàn thiện:</span>
                        <span class="rm-progress-count">
                            Đã tạo: {{ $stats->created_rooms }} / {{ $stats->total_scale }} phòng ({{ $stats->progress_percent }}%)
                        </span>
                    </div>

                    <div class="rm-progress-bar-bg">
                        <div class="rm-progress-bar-fill" style="width: {{ $stats->progress_percent }}%;"></div>
                    </div>

                    @if($stats->missing_rooms > 0)
                        <div class="rm-alert-notice rm-alert-warning">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Nhân viên phụ trách còn thiếu {{ $stats->missing_rooms }} phòng chưa đưa lên hệ thống.
                        </div>
                    @else
                        <div class="rm-alert-notice rm-alert-success">
                            <i class="fa-solid fa-circle-check"></i>
                            Đã hoàn thiện khởi tạo đầy đủ 100% quy mô phòng.
                        </div>
                    @endif
                </div>

                <div>
                    <div style="font-size: 0.775rem; color: var(--text-muted); font-weight: 600; margin-bottom: 8px;">
                        Trạng thái bài đăng của các phòng:
                    </div>
                    <div class="rm-status-badges-row">
                        <span class="rm-badge rm-badge-green">
                            <i class="fa-solid fa-circle-check"></i> Đang hiển thị web: <strong>{{ $stats->posts_active }}</strong>
                        </span>
                        <span class="rm-badge rm-badge-amber">
                            <i class="fa-solid fa-clock"></i> Chờ kiểm duyệt: <strong>{{ $stats->posts_pending }}</strong>
                        </span>
                        <span class="rm-badge rm-badge-red">
                            <i class="fa-solid fa-ban"></i> Bị từ chối: <strong>{{ $stats->posts_rejected }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cột phải (Khung Báo Cáo Tài Chính & Hợp Đồng Tóm Tắt) -->
            <div class="rm-control-card">
                <div>
                    <div class="rm-card-title">
                        <i class="fa-solid fa-hand-holding-dollar" style="color: #059669;"></i>
                        Báo cáo dòng tiền dự kiến / tháng
                    </div>

                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">
                        Tổng doanh thu đang khai thác:
                    </div>
                    <div class="rm-revenue-amount">{{ $stats->monthly_revenue_text }}</div>
                    <div class="rm-revenue-sub">
                        (Cộng tổng tiền thuê của các phòng đang có khách ở)
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.775rem; color: var(--text-muted); font-weight: 600; margin-bottom: 8px;">
                        Tình trạng thu tiền phòng tháng này:
                    </div>
                    <div class="rm-status-badges-row">
                        <span class="rm-badge rm-badge-green">
                            <i class="fa-solid fa-circle-check"></i> Đã thu đủ: <strong>{{ $stats->paid_rooms }} phòng</strong>
                        </span>
                        <span class="rm-badge rm-badge-red">
                            <i class="fa-solid fa-circle-exclamation"></i> Chưa đóng / Còn nợ: <strong>{{ $stats->unpaid_rooms }} phòng</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KHỐI 3 - BỘ LỌC & DANH SÁCH CHI TIẾT CÁC PHÒNG / CĂN CON (LỚP RUỘT)      -->
        <!-- ========================================================================= -->
        <div class="content-card-main">
            <!-- Topbar tiêu đề -->
            <div class="content-card-topbar">
                <div class="content-card-title">
                    <i class="fa-solid fa-door-closed" style="color: var(--brand-primary);"></i>
                    Danh Sách Chi Tiết Các Phòng / Căn Con (Lớp Ruột)
                </div>
            </div>

            <!-- Thanh công cụ lọc (Filter Bar) -->
            <div class="filter-controls-row">
                <div class="filter-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="rmInputSearch" class="filter-search-input" placeholder="Tìm theo số phòng, tên bài đăng..." oninput="filterRooms()">
                </div>

                <div class="filter-dropdowns">
                    <select id="rmSelectRoomStatus" class="filter-select" onchange="filterRooms()">
                        <option value="all">Tất cả trạng thái phòng</option>
                        <option value="empty">Phòng đang trống</option>
                        <option value="rented">Đang cho thuê</option>
                    </select>

                    <select id="rmSelectPostStatus" class="filter-select" onchange="filterRooms()">
                        <option value="all">Tất cả bài đăng</option>
                        <option value="active">Đang hiển thị web</option>
                        <option value="pending">Chờ kiểm duyệt</option>
                        <option value="rejected">Bị từ chối</option>
                    </select>

                    <button type="button" class="btn-act-action btn-act-refresh" onclick="resetFilters()" style="padding: 9px 14px;">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Xóa lọc
                    </button>
                </div>
            </div>

            <!-- 1. BẢNG DỮ LIỆU SẮC NÉT (6 CỘT) -->
            <div class="table-wrap" id="tableViewContainer">
                <table class="rm-data-table">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Phòng / Căn</th>
                            <th style="width: 18%;">Giá Niêm Yết</th>
                            <th style="width: 18%;">Trạng Thái Phòng</th>
                            <th style="width: 16%;">Tình Trạng Thu Tiền</th>
                            <th style="width: 14%;">Trạng Thái Tin Đăng</th>
                            <th style="width: 6%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        @forelse($rooms as $room)
                            <tr class="room-row-item" 
                                data-search="{{ strtolower(($room->room_number ?? '') . ' ' . ($room->title ?? '')) }}"
                                data-room-status="{{ $room->room_status ?? 'empty' }}"
                                data-post-status="{{ $room->post_status ?? 'active' }}">
                                
                                <!-- 1. Phòng / Căn: Thumbnail + Tên/Mã phòng + Ngày tạo -->
                                <td>
                                    <div class="rm-room-cell">
                                        <img src="{{ $room->thumbnail }}" alt="{{ $room->room_number }}" class="rm-room-thumb" onerror="this.src='https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=150'">
                                        <div class="rm-room-info">
                                            <div class="rm-room-code">{{ $room->room_number }}</div>
                                            <div class="rm-room-title" title="{{ $room->title }}">{{ $room->title }}</div>
                                            <div class="rm-room-date">
                                                <i class="fa-regular fa-calendar-plus"></i> Khởi tạo: {{ $room->created_at }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Giá niêm yết -->
                                <td>
                                    <div class="rm-price-text">{{ $room->price_formatted }}</div>
                                </td>

                                <!-- 4. Trạng thái phòng -->
                                <td>
                                    @if(($room->room_status ?? '') === 'rented')
                                        <span class="rm-badge rm-badge-blue">
                                            <i class="fa-solid fa-user-check"></i> Đang cho thuê
                                        </span>
                                    @else
                                        <span class="rm-badge rm-badge-green">
                                            <i class="fa-solid fa-door-open"></i> Đang trống
                                        </span>
                                    @endif
                                </td>

                                <!-- 5. Tình trạng thu tiền -->
                                <td>
                                    @if(($room->payment_status ?? '') === 'paid')
                                        <span class="rm-badge rm-badge-green">
                                            <i class="fa-solid fa-check-double"></i> Đã thu tiền
                                        </span>
                                    @elseif(($room->payment_status ?? '') === 'unpaid')
                                        <span class="rm-badge rm-badge-red">
                                            <i class="fa-solid fa-circle-exclamation"></i> Chưa đóng
                                        </span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem; padding-left: 12px;">--</span>
                                    @endif
                                </td>

                                <!-- 6. Trạng thái tin đăng -->
                                <td>
                                    @if(($room->post_status ?? '') === 'active')
                                        <span class="rm-badge rm-badge-green">
                                            <i class="fa-solid fa-globe"></i> Đang hiển thị
                                        </span>
                                    @elseif(($room->post_status ?? '') === 'pending')
                                        <span class="rm-badge rm-badge-amber">
                                            <i class="fa-solid fa-clock"></i> Chờ duyệt
                                        </span>
                                    @elseif(($room->post_status ?? '') === 'rejected')
                                        <span class="rm-badge rm-badge-red">
                                            <i class="fa-solid fa-ban"></i> Từ chối
                                        </span>
                                    @else
                                        <span class="rm-badge rm-badge-green">
                                            <i class="fa-solid fa-globe"></i> Đang hiển thị
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Thao tác -->
                                <td style="text-align: center;">
                                    <a href="{{ $room->detail_url }}" target="_blank" class="btn-act-eye" title="Xem chi tiết bài đăng">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="no-data-hint">
                                    <i class="fa-solid fa-folder-open"></i>
                                    Chưa có phòng nào được tạo bên trong tòa nhà này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Hint khi tìm kiếm không ra kết quả -->
            <div id="noMatchHint" class="no-data-hint" style="display: none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                Không tìm thấy phòng nào phù hợp với bộ lọc tìm kiếm.
            </div>
        </div>
    </main>

    <!-- JavaScript Logic xử lý lọc & hiển thị -->
    <script>
        // Xóa lưu trữ view mode cũ nếu có để luôn hiển thị dạng bảng
        try {
            localStorage.removeItem('renthome_room_view_mode');
        } catch(e) {}

        // Bộ lọc tìm kiếm kết hợp cho bảng
        function filterRooms() {
            const query = (document.getElementById('rmInputSearch').value || '').toLowerCase().trim();
            const roomStatusFilter = document.getElementById('rmSelectRoomStatus').value;
            const postStatusFilter = document.getElementById('rmSelectPostStatus').value;

            // Lọc các dòng trong bảng
            const tableRows = document.querySelectorAll('#roomsTableBody .room-row-item');
            let visibleTableCount = 0;

            tableRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const rStatus = row.getAttribute('data-room-status') || '';
                const pStatus = row.getAttribute('data-post-status') || '';

                const matchQuery = !query || searchData.includes(query);
                const matchRoomStatus = (roomStatusFilter === 'all') || (rStatus === roomStatusFilter);
                const matchPostStatus = (postStatusFilter === 'all') || (pStatus === postStatusFilter);

                if (matchQuery && matchRoomStatus && matchPostStatus) {
                    row.style.display = '';
                    visibleTableCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Hiển thị thông báo khi không có kết quả phù hợp
            const noMatch = document.getElementById('noMatchHint');
            if (noMatch) {
                noMatch.style.display = (tableRows.length > 0 && visibleTableCount === 0) ? 'block' : 'none';
            }
        }

        // Xóa tất cả bộ lọc
        function resetFilters() {
            document.getElementById('rmInputSearch').value = '';
            document.getElementById('rmSelectRoomStatus').value = 'all';
            document.getElementById('rmSelectPostStatus').value = 'all';
            filterRooms();
        }
    </script>
</body>
</html>
