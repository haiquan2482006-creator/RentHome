<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thanh Lý Hợp Đồng & Chốt Công Nợ - RentHome Enterprise</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --brand-primary: #0284c7;
            --brand-primary-hover: #0369a1;
            --brand-subtle: #e0f2fe;
            --accent-emerald: #059669;
            --accent-emerald-bg: #ecfdf5;
            --accent-amber: #d97706;
            --accent-amber-bg: #fffbeb;
            --accent-red: #dc2626;
            --accent-red-bg: #fef2f2;
            --accent-purple: #7c3aed;
            --accent-purple-bg: #f5f3ff;
            --accent-rose: #e11d48;
            --accent-rose-bg: #fff1f2;
            --surface-card: #ffffff;
            --surface-ground: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--surface-ground);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Top Bar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: var(--shadow-sm);
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 10px;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .brand-title span {
            color: var(--accent-emerald);
        }

        .brand-sub {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--brand-primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 2px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-secondary);
        }

        .btn-nav:hover {
            background-color: #f1f5f9;
            color: var(--text-primary);
        }

        .btn-nav-primary {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
        }

        .btn-nav-primary:hover {
            background-color: var(--brand-primary-hover);
            color: #ffffff;
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            width: 100%;
            flex: 1;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #0b132a 0%, #1e293b 100%);
            border-radius: var(--radius-xl);
            padding: 30px;
            color: #ffffff;
            box-shadow: var(--shadow-lg);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background-color: rgba(225, 29, 72, 0.2);
            border: 1px solid rgba(225, 29, 72, 0.35);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #f43f5e;
            margin-bottom: 10px;
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .hero-desc {
            font-size: 0.875rem;
            color: #cbd5e1;
            max-width: 700px;
            line-height: 1.5;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-hero-checkout {
            background-color: #38bdf8;
            color: #0c4a6e;
        }
        .btn-hero-checkout:hover {
            background-color: #7dd3fc;
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .kpi-val {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 0.775rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Content Card & Table */
        .content-card {
            background: #ffffff;
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-toolbar {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            background-color: #ffffff;
        }

        .toolbar-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-box-input {
            position: relative;
            min-width: 260px;
        }

        .search-box-input i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .search-box-input input {
            width: 100%;
            padding: 9px 14px 9px 36px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 0.85rem;
            outline: none;
            transition: var(--transition);
        }

        .search-box-input input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .select-status {
            padding: 9px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            outline: none;
            cursor: pointer;
            background-color: #ffffff;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        .contracts-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        .contracts-table th {
            padding: 14px 18px;
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .contracts-table td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .contracts-table tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.725rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-active { background-color: #ecfdf5; color: #047857; }
        .badge-pending-liq { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-pending-refund { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-terminated { background-color: #f1f5f9; color: #64748b; }
        .badge-req-checkout { background-color: #fffbeb; color: #b45309; }

        /* Action Buttons */
        .btn-table-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-act-settle {
            background-color: var(--brand-primary);
            color: #ffffff;
        }
        .btn-act-settle:hover {
            background-color: var(--brand-primary-hover);
        }

        .btn-act-refund {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-act-refund:hover {
            background-color: #047857;
        }

        .status-waiting-refund {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 9999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1px solid #bfdbfe;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(29, 78, 216, 0.05);
        }

        .btn-act-pay {
            background-color: #dc2626;
            color: #ffffff;
        }
        .btn-act-pay:hover {
            background-color: #b91c1c;
        }

        .btn-act-view {
            background-color: #f1f5f9;
            color: var(--text-secondary);
        }
        .btn-act-view:hover {
            background-color: #e2e8f0;
            color: var(--text-primary);
        }

        /* ========================================================= */
        /* MODAL PHIẾU QUYẾT TOÁN THANH LÝ HỢP ĐỒNG (BƯỚC 2 & BƯỚC 3) */
        /* ========================================================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: #ffffff;
            width: 100%;
            max-width: 1100px;
            max-height: 92vh;
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: modalFadeIn 0.25s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.97) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 20px 28px;
            background: linear-gradient(135deg, #0b132a 0%, #1e293b 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .modal-header-meta h3 {
            font-size: 1.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header-meta p {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        .btn-modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #f43f5e;
        }

        .modal-body {
            padding: 24px 28px;
            overflow-y: auto;
            flex: 1;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
        }

        @media (max-width: 900px) {
            .modal-body {
                grid-template-columns: 1fr;
            }
        }

        /* Form sections */
        .form-section {
            background: #f8fafc;
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            border: 1px solid var(--border-color);
            margin-bottom: 16px;
        }

        .form-section-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-label {
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .form-input {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            outline: none;
            transition: var(--transition);
        }

        .form-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        }

        .form-input[readonly] {
            background-color: #f1f5f9;
            color: var(--text-secondary);
        }

        /* Live KPI Summary Side Panel */
        .live-summary-card {
            background: #ffffff;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .summary-header {
            border-bottom: 2px dashed var(--border-color);
            padding-bottom: 12px;
        }

        .summary-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.825rem;
            color: var(--text-secondary);
            padding: 4px 0;
        }

        .summary-line strong {
            color: var(--text-primary);
            font-weight: 700;
        }

        .summary-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 4px 0;
        }

        /* Dynamic Scenario Cards */
        .scenario-box {
            border-radius: var(--radius-md);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: var(--transition);
        }

        .scenario-box-A {
            background-color: var(--accent-red-bg);
            border: 2px solid #f87171;
            color: #991b1b;
        }

        .scenario-box-B {
            background-color: var(--accent-emerald-bg);
            border: 2px solid #34d399;
            color: #065f46;
        }

        .scenario-box-C {
            background-color: #f8fafc;
            border: 2px solid #cbd5e1;
            color: #475569;
        }

        .scenario-title {
            font-size: 0.775rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .scenario-amount {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .scenario-desc {
            font-size: 0.775rem;
            line-height: 1.4;
            opacity: 0.9;
        }

        .modal-footer {
            padding: 16px 28px;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-modal {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-modal-cancel {
            background-color: #e2e8f0;
            color: var(--text-secondary);
        }
        .btn-modal-cancel:hover {
            background-color: #cbd5e1;
        }

        .btn-modal-submit {
            background-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        .btn-modal-submit:hover {
            background-color: var(--brand-primary-hover);
        }

        /* Print styles */
        @media print {
            .no-print, .top-navbar, .hero-actions, .filter-controls, .btn-table-action {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .modal-overlay {
                position: static !important;
                background: none !important;
                display: block !important;
                padding: 0 !important;
            }
            .modal-container {
                max-width: 100% !important;
                max-height: none !important;
                box-shadow: none !important;
                border: 1px solid #000000 !important;
            }
        }
    </style>
</head>

<body>

    <!-- TOP NAVBAR -->
    <header class="top-navbar no-print">
        <div class="navbar-container">
            <a href="/" class="brand-logo">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" onerror="this.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=80&q=80'">
                <div>
                    <div class="brand-title">Rent<span>Home</span></div>
                    <div class="brand-sub">Hệ Thống Quản Lý Doanh Nghiệp (MongoDB)</div>
                </div>
            </a>

            <div class="nav-actions">
                <a href="{{ url('qlvan_hanh/Room_list') }}" class="btn-nav">
                    <i class="fa-solid fa-door-open"></i>
                    <span>Danh Sách Phòng Vận Hành</span>
                </a>
                @php
                    $isStaffUser = Auth::user() && (Auth::user()->isSubAccount() || Auth::user()->isStaff() || in_array(Auth::user()->role ?? '', ['sub_account', 'staff', 'nhansu']));
                @endphp
                <a href="{{ $isStaffUser ? url('/Overview') : (Auth::user() && Auth::user()->role === 'admin' ? url('/Admin') : url('/Overview')) }}" class="btn-nav btn-nav-primary">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>{{ $isStaffUser ? 'Trang Quản Lý' : (Auth::user() && Auth::user()->role === 'admin' ? 'Trang Tổng Quan' : 'Tổng Quan') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN BODY -->
    <main class="main-container">

        <!-- HERO BANNER -->
        <section class="hero-banner">
            <div>
                <div class="hero-tag">
                    <i class="fa-solid fa-calculator"></i> Quy Trình Thanh Lý & Chốt Công Nợ Doanh Nghiệp
                </div>
                <h1 class="hero-title">Thanh Lý Hợp Đồng & Chốt Công Nợ Realtime</h1>
                <p class="hero-desc">
                    Hệ thống tự động hóa 4 bước đối soát công nợ cuối kỳ: Tính tiền phòng lẻ ngày, chốt chỉ số điện nước, phạt bồi thường tài sản cấn trừ tiền cọc; rẽ nhánh Kịch bản A (Khách nợ thêm -> tạo Hóa đơn cuối) hoặc Kịch bản B (Hoàn cọc -> đẩy Kế toán), tự động giải phóng phòng trống (available) khi hoàn tất.
                </p>
            </div>

            <div class="hero-actions no-print">
                <button type="button" class="btn-hero btn-hero-checkout" onclick="openRequestCheckoutModal()">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Gửi Yêu Cầu Trả Phòng (Bước 1)</span>
                </button>
            </div>
        </section>

        <!-- KPI STATS CARDS -->
        <section class="kpi-grid no-print">
            <div class="kpi-card">
                <div class="kpi-icon" style="background-color: var(--brand-subtle); color: var(--brand-primary);">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <div>
                    <div class="kpi-val">{{ number_format($stats['total'] ?? 0) }}</div>
                    <div class="kpi-label">Tổng hợp đồng quản lý</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background-color: var(--accent-emerald-bg); color: var(--accent-emerald);">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="kpi-val" style="color: var(--accent-emerald);">{{ number_format($stats['active'] ?? 0) }}</div>
                    <div class="kpi-label">Đang hiệu lực (active)</div>
                </div>
            </div>

            <div class="kpi-card" style="border-left: 4px solid var(--accent-red);">
                <div class="kpi-icon" style="background-color: var(--accent-red-bg); color: var(--accent-red);">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="kpi-val" style="color: var(--accent-red);">{{ number_format($stats['pending_liquidation'] ?? 0) }}</div>
                    <div class="kpi-label">Kịch bản A (Chờ thu nợ)</div>
                </div>
            </div>

            <div class="kpi-card" style="border-left: 4px solid var(--brand-primary);">
                <div class="kpi-icon" style="background-color: #eff6ff; color: #1d4ed8;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <div class="kpi-val" style="color: #1d4ed8;">{{ number_format($stats['pending_refund'] ?? 0) }}</div>
                    <div class="kpi-label">Kịch bản B (Chờ hoàn cọc)</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background-color: #f1f5f9; color: #64748b;">
                    <i class="fa-solid fa-check-double"></i>
                </div>
                <div>
                    <div class="kpi-val" style="color: #64748b;">{{ number_format($stats['terminated'] ?? 0) }}</div>
                    <div class="kpi-label">Đã thanh lý (terminated)</div>
                </div>
            </div>
        </section>

        <!-- CONTRACTS TABLE CARD -->
        <section class="content-card">
            <div class="card-toolbar no-print">
                <div class="toolbar-title">
                    <i class="fa-solid fa-list-check" style="color: var(--brand-primary);"></i>
                    <span>Danh Sách Hợp Đồng Cần Thanh Lý & Quyết Toán</span>
                    <span style="font-size: 0.8rem; padding: 2px 8px; border-radius: 12px; background: #f1f5f9; color: #475569;">
                        {{ count($contracts) }} bản ghi
                    </span>
                </div>

                <div class="filter-controls">
                    <div class="search-box-input">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="filterSearchInput" placeholder="Tìm mã HĐ, tên khách, SĐT..." oninput="handleTableFilter()">
                    </div>

                    <select id="filterStatusSelect" class="select-status" onchange="handleTableFilter()">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="active">Đang hiệu lực (active)</option>
                        <option value="pending_liquidation">Kịch bản A (Chờ thu nợ)</option>
                        <option value="pending_refund">Kịch bản B (Chờ hoàn cọc)</option>
                        <option value="terminated">Đã thanh lý xong</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="contracts-table" id="liquidationContractsTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Mã Hợp Đồng</th>
                            <th>Khách Thuê</th>
                            <th>Phòng / Tòa Nhà</th>
                            <th>Giá Thuê & Tiền Cọc</th>
                            <th>Thời Hạn HĐ</th>
                            <th>Trạng Thái Quyết Toán</th>
                            <th style="text-align: right;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody id="contractsTableBody">
                        @forelse($contracts as $index => $c)
                            @php
                                $cId = (string)($c->_id ?? $c->id);
                                $post = $c->post;
                                $roomTitle = $post ? $post->title : ($c->room_id ? 'Phòng ' . $c->room_id : 'Phòng trọ RentHome');
                                $roomAddr = $post ? implode(', ', array_filter([$post->address, $post->district, $post->province])) : '---';
                                $rent = (float)($c->rent_price ?? 0);
                                $deposit = $c->deposit_safe;
                                $status = $c->status ?? 'active';
                                $hasCheckoutReq = !empty($c->checkout_request) && ($c->checkout_request['status'] ?? '') === 'pending';
                            @endphp
                            <tr class="contract-row" 
                                id="row-contract-{{ $cId }}"
                                data-id="{{ $cId }}"
                                data-code="{{ strtolower($c->contract_code) }}"
                                data-tenant="{{ mb_strtolower($c->tenant_name ?? '') }}"
                                data-phone="{{ $c->tenant_phone ?? '' }}"
                                data-status="{{ $status }}">
                                <td style="color: var(--text-muted); font-weight: 700;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight: 800; color: var(--brand-primary);">{{ $c->contract_code }}</div>
                                    @if($hasCheckoutReq)
                                        <div style="font-size: 0.7rem; color: #d97706; font-weight: 700; margin-top: 2px;">
                                            <i class="fa-solid fa-bell"></i> Có yêu cầu trả: {{ $c->checkout_request['expected_checkout_date'] ?? '' }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-primary);">{{ $c->tenant_name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-phone" style="font-size: 0.7rem;"></i> {{ $c->tenant_phone ?: '---' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $roomTitle }}">
                                        {{ $roomTitle }}
                                    </div>
                                    <div style="font-size: 0.725rem; color: var(--text-muted); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $roomAddr }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: var(--accent-emerald);">{{ number_format($rent) }} đ/tháng</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">
                                        Cọc: <span style="color: var(--brand-primary); font-weight: 700;">{{ number_format($deposit) }} đ</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.78rem; font-weight: 600;">
                                        {{ $c->start_date }} &rarr; {{ $c->end_date }}
                                    </div>
                                </td>
                                <td>
                                    @if($status === 'active' || $status === 'signed')
                                        <span class="status-badge badge-active">
                                            <i class="fa-solid fa-circle-check"></i> Đang hiệu lực
                                        </span>
                                    @elseif($status === 'pending_liquidation')
                                        <span class="status-badge badge-pending-liq" title="Khách nợ thêm: Chờ thanh toán hóa đơn chốt">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Kịch bản A: Chờ thu nợ
                                        </span>
                                    @elseif($status === 'pending_refund')
                                        <span class="status-badge badge-pending-refund" title="Cọc dư: Chờ Kế toán hoàn trả lại khách">
                                            <i class="fa-solid fa-clock-rotate-left"></i> Kịch bản B: Chờ hoàn cọc
                                        </span>
                                    @elseif($status === 'terminated')
                                        <span class="status-badge badge-terminated">
                                            <i class="fa-solid fa-check-double"></i> Đã thanh lý
                                        </span>
                                    @else
                                        <span class="status-badge" style="background:#f1f5f9; color:#475569;">{{ $status }}</span>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <!-- Nút Bước 2: Thanh lý hợp đồng / Chốt công nợ -->
                                        @if($status !== 'terminated')
                                            <button type="button" 
                                                    class="btn-table-action btn-act-settle" 
                                                    onclick="openLiquidationModal('{{ $cId }}')"
                                                    title="Mở Phiếu Quyết Toán Chốt Công Nợ (Bước 2 & 3)">
                                                <i class="fa-solid fa-calculator"></i>
                                                <span>{{ $status === 'active' ? 'Thanh lý hợp đồng' : 'Quyết toán lại' }}</span>
                                            </button>
                                        @endif

                                        <!-- Nếu đang ở Kịch bản B: Nhân sự chỉ nhìn thấy text trạng thái chờ Kế toán hoàn tiền -->
                                        @if($status === 'pending_refund')
                                            <span class="status-waiting-refund" title="Hợp đồng đang chờ Kế toán / Admin chuyển khoản hoàn trả tiền cọc cho khách">
                                                ⏳ Đang chờ Kế toán chuyển khoản hoàn tiền
                                            </span>
                                        @endif

                                        <!-- Nếu đang ở Kịch bản A: Nút xác nhận đã thu đủ nợ (Bước 4) -->
                                        @if($status === 'pending_liquidation')
                                            <button type="button" 
                                                    class="btn-table-action btn-act-pay" 
                                                    onclick="confirmFinalPaidDirect('{{ $cId }}', '{{ addslashes($c->tenant_name) }}', {{ $c->liquidation_details['net_amount'] ?? 0 }})"
                                                    title="Xác nhận khách đã thanh toán hóa đơn nợ cuối cùng">
                                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                                <span>Xác nhận thu nợ</span>
                                            </button>
                                        @endif

                                        <!-- Nút xem chi tiết / In biên bản -->
                                        @if(!empty($c->liquidation_details))
                                            <button type="button" 
                                                    class="btn-table-action btn-act-view" 
                                                    onclick="openLiquidationModal('{{ $cId }}', true)"
                                                    title="Xem lại biên bản quyết toán đã chốt">
                                                <i class="fa-solid fa-file-invoice"></i>
                                                <span>Biên bản</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                    Hiện tại chưa có dữ liệu hợp đồng nào cần thanh lý.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- ========================================================================================= -->
    <!-- MODAL BƯỚC 2 & BƯỚC 3: FORM PHIẾU QUYẾT TOÁN THANH LÝ HỢP ĐỒNG (AJAX REALTIME CALCULATION) -->
    <!-- ========================================================================================= -->
    <div class="modal-overlay" id="liquidationModalOverlay" onclick="handleModalBackdropClick(event)">
        <div class="modal-container" id="liquidationModalContainer">
            
            <!-- Header Modal -->
            <div class="modal-header">
                <div class="modal-header-meta">
                    <h3>
                        <i class="fa-solid fa-file-invoice-dollar" style="color: #38bdf8;"></i>
                        <span>Phiếu Quyết Toán & Chốt Công Nợ Hợp Đồng</span>
                    </h3>
                    <p id="modalContractSubtitle">
                        HĐ: <strong id="modalContractCode" style="color: #ffffff;">---</strong> &bull; 
                        Khách: <span id="modalTenantName">---</span> &bull; 
                        Phòng: <span id="modalRoomTitle">---</span>
                    </p>
                </div>
                <button type="button" class="btn-modal-close" onclick="closeLiquidationModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body Modal (Grid: Cột trái Nhập liệu | Cột phải Live KPI Đối trọng) -->
            <div class="modal-body">
                
                <!-- CỘT TRÁI: FORM NHẬP LIỆU (BƯỚC 2) -->
                <div>
                    <!-- 1. Thông tin hợp đồng & Tiền cọc đối trọng -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fa-solid fa-building-circle-check" style="color: var(--brand-primary);"></i>
                            1. Thông Tin Hợp Đồng & Tiền Cọc Đối Trọng
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Giá thuê phòng (VNĐ/tháng):</label>
                                <input type="text" id="calc_rent_price_display" class="form-input" readonly value="0 VNĐ">
                                <input type="hidden" id="calc_rent_price" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: var(--accent-purple);">
                                    <i class="fa-solid fa-shield"></i> Tiền cọc đối trọng (contracts):
                                </label>
                                <input type="text" id="calc_deposit_display" class="form-input" readonly style="font-weight: 800; color: var(--accent-purple);" value="0 VNĐ">
                                <input type="hidden" id="calc_deposit" value="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Thời hạn thuê ban đầu:</label>
                                <input type="text" id="calc_contract_period" class="form-input" readonly value="-- &rarr; --">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color: var(--accent-rose);">
                                    <i class="fa-solid fa-calendar-day"></i> Ngày trả phòng thực tế (*):
                                </label>
                                <input type="date" id="calc_checkout_date" class="form-input" onchange="recalculateSettlement()" style="border: 2px solid #fda4af; font-weight: 800;">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Tiền phòng lẻ tháng cuối -->
                    <div class="form-section">
                        <div class="form-section-title" style="justify-content: space-between;">
                            <span>
                                <i class="fa-solid fa-door-open" style="color: var(--accent-emerald);"></i>
                                2. Tiền Phòng Lẻ Tháng Cuối
                            </span>
                            <label style="font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; text-transform: none; color: var(--text-secondary);">
                                <input type="checkbox" id="calc_is_prorated" checked onchange="toggleProratedMode()">
                                <span>Tính lẻ ngày theo thực tế</span>
                            </label>
                        </div>

                        <div class="form-row" id="proratedInputRow">
                            <div class="form-group">
                                <label class="form-label">Số ngày ở thực tế tháng cuối:</label>
                                <input type="number" id="calc_stay_days" min="1" max="31" class="form-input" oninput="recalculateSettlement()" value="15">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Công thức tính lẻ ngày:</label>
                                <input type="text" id="calc_prorated_formula" class="form-input" readonly style="font-size: 0.775rem; color: var(--text-muted);" value="(Giá phòng / 30) x ngày">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">Thành tiền phòng tháng cuối:</span>
                            <span id="calc_prorated_rent_text" style="font-size: 1rem; font-weight: 800; color: var(--accent-emerald);">0 VNĐ</span>
                        </div>
                    </div>

                    <!-- 3. Điện & Nước chốt cuối -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fa-solid fa-bolt" style="color: var(--accent-amber);"></i>
                            3. Điện & Nước Chốt Cuối
                        </div>

                        <!-- Điện -->
                        <div style="background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-size: 0.775rem; font-weight: 800; color: var(--accent-amber);"><i class="fa-solid fa-bolt"></i> TIỀN ĐIỆN</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Đơn giá: <strong id="calc_elec_price_label">3.500</strong> đ/số</span>
                            </div>
                            <div class="form-row" style="margin-bottom: 4px;">
                                <div class="form-group">
                                    <label class="form-label">Chỉ số cũ (tháng trước):</label>
                                    <input type="number" id="calc_elec_old" class="form-input" readonly value="0">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="color: var(--accent-amber);">Chỉ số chốt cuối (*):</label>
                                    <input type="number" id="calc_elec_new" class="form-input" oninput="recalculateSettlement()" value="0" style="border-color: #fcd34d;">
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.775rem; color: var(--text-secondary); padding-top: 4px;">
                                <span>Tiêu thụ: <strong id="calc_elec_usage">0</strong> số</span>
                                <span>Thành tiền điện: <strong id="calc_elec_amount_text" style="color: var(--accent-amber);">0 VNĐ</strong></span>
                            </div>
                        </div>

                        <!-- Nước -->
                        <div style="background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-size: 0.775rem; font-weight: 800; color: #0284c7;"><i class="fa-solid fa-droplet"></i> TIỀN NƯỚC</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);" id="calc_water_type_label">Đơn giá: 25.000 đ/khối</span>
                            </div>
                            <div class="form-row" id="waterMeterRow" style="margin-bottom: 4px;">
                                <div class="form-group">
                                    <label class="form-label">Chỉ số nước cũ:</label>
                                    <input type="number" id="calc_water_old" class="form-input" readonly value="0">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="color: #0284c7;">Chỉ số nước mới chốt:</label>
                                    <input type="number" id="calc_water_new" class="form-input" oninput="recalculateSettlement()" value="0" style="border-color: #7dd3fc;">
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.775rem; color: var(--text-secondary); padding-top: 4px;">
                                <span id="calc_water_usage_label">Tiêu thụ: <strong id="calc_water_usage">0</strong> khối</span>
                                <span>Thành tiền nước: <strong id="calc_water_amount_text" style="color: #0284c7;">0 VNĐ</strong></span>
                            </div>
                        </div>

                        <!-- Dịch vụ cố định -->
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: var(--text-secondary); padding-top: 4px;">
                            <span>Phí dịch vụ cố định tháng cuối (mạng, rác, xe):</span>
                            <strong id="calc_services_text">0 VNĐ</strong>
                        </div>
                    </div>

                    <!-- 4. Phí bồi thường hư hỏng (Damage Fee) -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-red);"></i>
                            4. Phí Bồi Thường Hư Hỏng Tài Sản & Phạt (Damage Fee)
                        </div>

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Số tiền phạt / bồi thường tài sản (VNĐ):</label>
                            <div style="position: relative;">
                                <input type="number" id="calc_damage_fee" min="0" step="10000" class="form-input" oninput="recalculateSettlement()" value="0" placeholder="0" style="font-size: 1rem; font-weight: 800; color: var(--accent-red);">
                                <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">VNĐ</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Ghi chú chi tiết lý do bồi thường / khấu trừ:</label>
                            <textarea id="calc_damage_reason" rows="2" class="form-input" placeholder="Ví dụ: Vỡ gương nhà tắm phòng 302, trầy xước tủ lạnh, mất chìa khóa cổng..."></textarea>
                        </div>
                    </div>

                    <!-- Ghi chú chung -->
                    <div class="form-group">
                        <label class="form-label">Ghi chú biên bản thanh lý hợp đồng:</label>
                        <textarea id="calc_general_note" rows="2" class="form-input" placeholder="Ghi chú đối soát thêm giữa chủ nhà và khách thuê..."></textarea>
                    </div>
                </div>

                <!-- CỘT PHẢI: BẢNG TỔNG HỢP REALTIME & KỊCH BẢN RẼ NHÁNH (BƯỚC 3) -->
                <div>
                    <div class="live-summary-card">
                        <div class="summary-header">
                            <div class="summary-title">
                                <i class="fa-solid fa-scale-balanced" style="color: var(--brand-primary);"></i>
                                <span>Bảng Đối Trọng Công Nợ Realtime</span>
                            </div>
                            <p style="font-size: 0.725rem; color: var(--text-muted); margin-top: 2px;">
                                Dữ liệu tự động cập nhật ngay khi bạn thay đổi các chỉ số bên trái
                            </p>
                        </div>

                        <!-- Breakdown -->
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <div class="summary-line">
                                <span>Tiền phòng lẻ tháng cuối:</span>
                                <strong id="sum_prorated_rent">+ 0 đ</strong>
                            </div>
                            <div class="summary-line">
                                <span>Tiền điện chốt cuối:</span>
                                <strong id="sum_electricity">+ 0 đ</strong>
                            </div>
                            <div class="summary-line">
                                <span>Tiền nước chốt cuối:</span>
                                <strong id="sum_water">+ 0 đ</strong>
                            </div>
                            <div class="summary-line">
                                <span>Dịch vụ cố định tháng cuối:</span>
                                <strong id="sum_services">+ 0 đ</strong>
                            </div>
                            <div class="summary-line">
                                <span>Phí bồi thường tài sản:</span>
                                <strong id="sum_damage" style="color: var(--accent-red);">+ 0 đ</strong>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-line" style="font-size: 0.9rem;">
                                <strong style="color: var(--text-primary); text-transform: uppercase;">Tổng nợ khách phải trả:</strong>
                                <strong id="sum_total_debt" style="font-size: 1.05rem; color: var(--text-primary);">0 đ</strong>
                            </div>

                            <div class="summary-line" style="font-size: 0.85rem; color: var(--accent-purple);">
                                <span>Khấu trừ tiền cọc (contracts):</span>
                                <strong id="sum_deposit">- 0 đ</strong>
                            </div>

                            <div class="summary-line" id="row_sum_unused_rent" style="display: none; font-size: 0.85rem; color: #059669;">
                                <span><i class="fa-solid fa-arrow-rotate-left"></i> Hoàn tiền phòng đóng trước chưa ở:</span>
                                <strong id="sum_unused_rent">- 0 đ</strong>
                            </div>
                        </div>

                        <!-- HỘP RẼ NHÁNH KỊCH BẢN DYNAMIC -->
                        <div id="scenarioPreviewBox" class="scenario-box scenario-box-C">
                            <div class="scenario-title" id="scenarioTitle">
                                <i class="fa-solid fa-scale-balanced"></i>
                                <span>CÂN BẰNG CÔNG NỢ</span>
                            </div>
                            <div class="scenario-amount" id="scenarioAmount">0 VNĐ</div>
                            <div class="scenario-desc" id="scenarioDesc">
                                Hai bên đã hòa nợ, không bên nào phải thanh toán thêm.
                            </div>
                        </div>

                        <!-- Thông tin tài khoản nhận lại tiền hoàn của khách (hiện khi ở Kịch bản B) -->
                        <div id="refundBankInfoSection" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 12px;">
                            <div style="font-size: 0.75rem; font-weight: 800; color: #065f46; text-transform: uppercase; margin-bottom: 6px;">
                                <i class="fa-solid fa-building-columns"></i> Thông Tin Nhận Hoàn Tiền Của Khách:
                            </div>
                            <div class="form-group" style="margin-bottom: 6px;">
                                <label style="font-size: 0.7rem; font-weight: 700; color: #047857;">Số tài khoản ngân hàng:</label>
                                <input type="text" id="calc_refund_bank_account" class="form-input" placeholder="Ví dụ: 0988888888" style="background:#ffffff;">
                            </div>
                            <div class="form-row" style="margin-bottom: 0;">
                                <div class="form-group">
                                    <label style="font-size: 0.7rem; font-weight: 700; color: #047857;">Ngân hàng:</label>
                                    <input type="text" id="calc_refund_bank_name" class="form-input" placeholder="VD: MB Bank, Vietcombank..." style="background:#ffffff;">
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 0.7rem; font-weight: 700; color: #047857;">Chủ tài khoản:</label>
                                    <input type="text" id="calc_refund_bank_holder" class="form-input" placeholder="Tên chủ TK" style="background:#ffffff;">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <button type="button" 
                                id="btnSubmitSettlement" 
                                class="btn-modal btn-modal-submit" 
                                onclick="submitLiquidationForm()" 
                                style="width: 100%; justify-content: center; padding: 14px; font-size: 0.95rem;">
                            <i class="fa-solid fa-check-to-slot"></i>
                            <span>Xác Nhận Quyết Toán & Chốt Công Nợ</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="modal-footer no-print">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeLiquidationModal()">
                    <i class="fa-solid fa-xmark"></i> Đóng
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- MODAL BƯỚC 1: KHÁCH THUÊ GỬI YÊU CẦU TRẢ PHÒNG                                            -->
    <!-- ========================================================================================= -->
    <div class="modal-overlay" id="requestCheckoutModalOverlay" onclick="handleCheckoutModalBackdrop(event)">
        <div class="modal-container" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-header-meta">
                    <h3>
                        <i class="fa-solid fa-paper-plane" style="color: #38bdf8;"></i>
                        <span>Gửi Yêu Cầu Trả Phòng & Thanh Lý Hợp Đồng</span>
                    </h3>
                    <p>Hệ thống sẽ tạo Notification thông báo ngay đến Quản lý vận hành & Admin</p>
                </div>
                <button type="button" class="btn-modal-close" onclick="closeRequestCheckoutModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div style="padding: 24px; overflow-y: auto;">
                <form id="requestCheckoutForm" onsubmit="submitRequestCheckout(event)">
                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label">Chọn Hợp đồng thuê cần trả phòng (*):</label>
                        <select id="checkout_contract_id" class="form-input" required>
                            <option value="">-- Chọn hợp đồng --</option>
                            @foreach($contracts as $c)
                                @if($c->status !== 'terminated')
                                    <option value="{{ (string)($c->_id ?? $c->id) }}">
                                        {{ $c->contract_code }} - {{ $c->tenant_name }} ({{ $c->post ? $c->post->title : ($c->room_id ? 'Phòng '.$c->room_id : 'Phòng') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label" style="color: var(--accent-rose);">Ngày dự kiến trả phòng & bàn giao chìa khóa (*):</label>
                        <input type="date" id="checkout_expected_date" class="form-input" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label class="form-label">Lý do trả phòng:</label>
                        <textarea id="checkout_reason" rows="3" class="form-input" placeholder="Ví dụ: Chuyển công tác, hết nhu cầu thuê, về quê..."></textarea>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                        <div style="font-size: 0.775rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                            <i class="fa-solid fa-building-columns" style="color: var(--brand-primary);"></i> Thông tin tài khoản nhận lại tiền cọc (nếu cọc dư):
                        </div>
                        <div class="form-group" style="margin-bottom: 8px;">
                            <input type="text" id="checkout_bank_account" class="form-input" placeholder="Số tài khoản ngân hàng">
                        </div>
                        <div class="form-row" style="margin-bottom: 0;">
                            <input type="text" id="checkout_bank_name" class="form-input" placeholder="Tên ngân hàng (VD: Vietcombank)">
                            <input type="text" id="checkout_bank_holder" class="form-input" placeholder="Tên chủ tài khoản">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn-modal btn-modal-cancel" onclick="closeRequestCheckoutModal()">Hủy</button>
                        <button type="submit" class="btn-modal btn-modal-submit" style="background-color: #0284c7;">
                            <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Trả Phòng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- LOGIC JAVASCRIPT & AJAX REALTIME                                                         -->
    <!-- ========================================================================================= -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentContractData = null;
        let currentSettlementCalculation = null;

        // Auto open contract if passed in query param
        const autoSelectedContractId = "{{ $selectedContractId ?? '' }}";
        document.addEventListener('DOMContentLoaded', function() {
            if (autoSelectedContractId) {
                openLiquidationModal(autoSelectedContractId);
            }
        });

        /**
         * Lọc bảng danh sách hợp đồng
         */
        function handleTableFilter() {
            const query = document.getElementById('filterSearchInput').value.toLowerCase().trim();
            const statusFilter = document.getElementById('filterStatusSelect').value;
            const rows = document.querySelectorAll('.contract-row');

            rows.forEach(row => {
                const code = row.getAttribute('data-code') || '';
                const tenant = row.getAttribute('data-tenant') || '';
                const phone = row.getAttribute('data-phone') || '';
                const status = row.getAttribute('data-status') || '';

                const matchSearch = (!query) || code.includes(query) || tenant.includes(query) || phone.includes(query);
                let matchStatus = true;
                if (statusFilter !== 'all') {
                    if (statusFilter === 'active') {
                        matchStatus = (status === 'active' || status === 'signed');
                    } else {
                        matchStatus = (status === statusFilter);
                    }
                }

                row.style.display = (matchSearch && matchStatus) ? '' : 'none';
            });
        }

        /**
         * BƯỚC 1: Mở Modal Gửi Yêu Cầu Trả Phòng
         */
        function openRequestCheckoutModal(prefillContractId = '') {
            if (prefillContractId) {
                document.getElementById('checkout_contract_id').value = prefillContractId;
            }
            document.getElementById('requestCheckoutModalOverlay').classList.add('active');
        }

        function closeRequestCheckoutModal() {
            document.getElementById('requestCheckoutModalOverlay').classList.remove('active');
        }

        function handleCheckoutModalBackdrop(e) {
            if (e.target.id === 'requestCheckoutModalOverlay') {
                closeRequestCheckoutModal();
            }
        }

        async function submitRequestCheckout(e) {
            e.preventDefault();
            const contractId = document.getElementById('checkout_contract_id').value;
            if (!contractId) {
                Swal.fire('Lỗi', 'Vui lòng chọn hợp đồng cần trả phòng!', 'warning');
                return;
            }

            const payload = {
                expected_checkout_date: document.getElementById('checkout_expected_date').value,
                reason: document.getElementById('checkout_reason').value,
                bank_account: document.getElementById('checkout_bank_account').value,
                bank_name: document.getElementById('checkout_bank_name').value,
                bank_holder: document.getElementById('checkout_bank_holder').value,
            };

            Swal.fire({
                title: 'Đang gửi yêu cầu...',
                text: 'Hệ thống đang tạo thông báo gửi đến Quản lý vận hành & Admin...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`/api/contracts/${contractId}/request-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (!data.success) {
                    throw new Error(data.message || 'Không thể gửi yêu cầu trả phòng.');
                }

                closeRequestCheckoutModal();

                Swal.fire({
                    icon: 'success',
                    title: 'Gửi yêu cầu thành công!',
                    text: data.message,
                    confirmButtonColor: '#0284c7'
                }).then(() => {
                    location.reload();
                });

            } catch (err) {
                Swal.fire('Thất bại', err.message, 'error');
            }
        }

        /**
         * BƯỚC 2: Mở Modal Phiếu Quyết Toán (Lấy dữ liệu từ MongoDB qua AJAX)
         */
        async function openLiquidationModal(contractId, viewOnly = false) {
            Swal.fire({
                title: 'Đang tải dữ liệu quyết toán...',
                text: 'Truy xuất thông tin hợp đồng, tiền cọc & chỉ số điện nước...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`/api/contracts/${contractId}/liquidation-data`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const result = await res.json();

                if (!result.success) {
                    throw new Error(result.message || 'Không tìm thấy dữ liệu hợp đồng.');
                }

                Swal.close();
                currentContractData = result.data;

                // Populate Form Fields
                populateLiquidationForm(currentContractData, viewOnly);

                // Hiển thị Modal
                document.getElementById('liquidationModalOverlay').classList.add('active');

                // Tính toán Realtime ngay lập tức
                recalculateSettlement();

            } catch (err) {
                Swal.fire('Lỗi', err.message, 'error');
            }
        }

        function closeLiquidationModal() {
            document.getElementById('liquidationModalOverlay').classList.remove('active');
            currentContractData = null;
            currentSettlementCalculation = null;
        }

        function handleModalBackdropClick(e) {
            if (e.target.id === 'liquidationModalOverlay') {
                closeLiquidationModal();
            }
        }

        /**
         * Đổ dữ liệu vào Modal Phiếu Quyết Toán
         */
        function populateLiquidationForm(d, viewOnly = false) {
            document.getElementById('modalContractCode').innerText = d.contract_code;
            document.getElementById('modalTenantName').innerText = d.tenant_name + (d.tenant_phone ? ` (${d.tenant_phone})` : '');
            document.getElementById('modalRoomTitle').innerText = d.room_name;

            // Tiền phòng & Tiền cọc đối trọng từ contracts
            document.getElementById('calc_rent_price').value = d.rent_price;
            document.getElementById('calc_rent_price_display').value = formatMoney(d.rent_price) + ' VNĐ/tháng';

            document.getElementById('calc_deposit').value = d.deposit_amount;
            document.getElementById('calc_deposit_display').value = formatMoney(d.deposit_amount) + ' VNĐ';

            document.getElementById('calc_contract_period').value = `${d.start_date} → ${d.end_date}`;

            // Ngày trả phòng
            const checkoutInput = document.getElementById('calc_checkout_date');
            checkoutInput.value = d.default_checkout_date;

            // Điện
            document.getElementById('calc_elec_old').value = d.electricity_old;
            document.getElementById('calc_elec_new').value = Math.max(d.electricity_old, d.electricity_old + 15); // Mặc định gợi ý số mới
            document.getElementById('calc_elec_price_label').innerText = formatMoney(d.electricity_price);

            // Nước
            const waterMeterRow = document.getElementById('waterMeterRow');
            if (d.water_calculation_type === 'khoi') {
                waterMeterRow.style.display = 'grid';
                document.getElementById('calc_water_old').value = d.water_old;
                document.getElementById('calc_water_new').value = Math.max(d.water_old, d.water_old + 3);
                document.getElementById('calc_water_type_label').innerText = `Đơn giá: ${formatMoney(d.water_price_block)} đ/khối`;
            } else {
                waterMeterRow.style.display = 'none';
                document.getElementById('calc_water_old').value = 0;
                document.getElementById('calc_water_new').value = 0;
                document.getElementById('calc_water_type_label').innerText = `Theo người: ${d.water_people_count} người x ${formatMoney(d.water_price_person)} đ`;
            }

            // Ngày ở lẻ
            const checkoutDateObj = new Date(d.default_checkout_date);
            const currentDay = checkoutDateObj.getDate() || 15;
            document.getElementById('calc_stay_days').value = currentDay;

            // Damage Fee
            document.getElementById('calc_damage_fee').value = 0;
            document.getElementById('calc_damage_reason').value = '';

            // Nếu đã từng quyết toán thì điền lại dữ liệu cũ
            if (d.liquidation_details) {
                const liq = d.liquidation_details;
                checkoutInput.value = liq.checkout_date || d.default_checkout_date;
                document.getElementById('calc_stay_days').value = liq.stay_days || currentDay;
                document.getElementById('calc_elec_new').value = liq.electricity_new || d.electricity_old;
                document.getElementById('calc_water_new').value = liq.water_new || d.water_old;
                document.getElementById('calc_damage_fee').value = liq.damage_fee || 0;
                document.getElementById('calc_damage_reason').value = liq.damage_reason || '';
                document.getElementById('calc_general_note').value = liq.note || '';
            }

            // Tài khoản ngân hàng nhận hoàn tiền
            const req = d.checkout_request || {};
            document.getElementById('calc_refund_bank_account').value = req.bank_account || (d.tenant_account || '');
            document.getElementById('calc_refund_bank_name').value = req.bank_name || '';
            document.getElementById('calc_refund_bank_holder').value = req.bank_holder || d.tenant_name;
        }

        function toggleProratedMode() {
            const isProrated = document.getElementById('calc_is_prorated').checked;
            document.getElementById('proratedInputRow').style.opacity = isProrated ? '1' : '0.5';
            document.getElementById('calc_stay_days').disabled = !isProrated;
            recalculateSettlement();
        }

        /**
         * BƯỚC 2: TÍNH TOÁN REALTIME CÔNG NỢ & RẼ NHÁNH KỊCH BẢN A / B
         */
        function recalculateSettlement() {
            if (!currentContractData) return;

            const d = currentContractData;
            const rentPrice = parseFloat(d.rent_price) || 0;
            const depositAmount = parseFloat(d.deposit_amount) || 0;

            const isProrated = document.getElementById('calc_is_prorated').checked;
            let stayDays = parseInt(document.getElementById('calc_stay_days').value) || 1;
            stayDays = Math.max(1, Math.min(31, stayDays));

            // 1. Tiền phòng lẻ hoặc Hoàn tiền phòng đóng trước chưa ở
            let proratedRent = 0;
            let unusedRentRefund = 0;
            let isPrepaidCycle = false;

            const checkoutDateVal = document.getElementById('calc_checkout_date').value;
            const checkoutDateObj = checkoutDateVal ? new Date(checkoutDateVal) : new Date();
            const nextRentDateStr = d.next_rent_date;

            if (nextRentDateStr) {
                const nextRentDateObj = new Date(nextRentDateStr);
                if (checkoutDateObj < nextRentDateObj) {
                    isPrepaidCycle = true;
                    proratedRent = 0;

                    // Ngày thừa trong tháng trả phòng
                    const cDay = checkoutDateObj.getDate() || 1;
                    const unusedDays = Math.max(0, 30 - cDay);
                    const unusedDaysAmt = Math.round((rentPrice / 30) * unusedDays);

                    // Tháng nguyên tiếp theo trước next_rent_date
                    const mDiff = (nextRentDateObj.getFullYear() - checkoutDateObj.getFullYear()) * 12 + (nextRentDateObj.getMonth() - checkoutDateObj.getMonth()) - 1;
                    const unusedMonths = Math.max(0, mDiff);
                    const unusedMonthsAmt = unusedMonths * rentPrice;

                    unusedRentRefund = unusedDaysAmt + unusedMonthsAmt;
                    document.getElementById('calc_prorated_formula').value = `Đã đóng trước đến ${nextRentDateStr} (Không nợ tiền phòng)`;
                }
            }

            if (!isPrepaidCycle) {
                if (isProrated) {
                    proratedRent = Math.round((rentPrice / 30) * stayDays);
                    document.getElementById('calc_prorated_formula').value = `(${formatMoney(rentPrice)} / 30) x ${stayDays} ngày`;
                } else {
                    proratedRent = rentPrice;
                    stayDays = 30;
                    document.getElementById('calc_prorated_formula').value = `Trọn cả tháng (${formatMoney(rentPrice)} đ)`;
                }
            }
            document.getElementById('calc_prorated_rent_text').innerText = formatMoney(proratedRent) + ' VNĐ';

            // 2. Điện
            const elecOld = parseFloat(document.getElementById('calc_elec_old').value) || 0;
            const elecNew = parseFloat(document.getElementById('calc_elec_new').value) || elecOld;
            const elecPrice = parseFloat(d.electricity_price) || 3500;
            const elecUsage = Math.max(0, elecNew - elecOld);
            const elecAmount = Math.round(elecUsage * elecPrice);

            document.getElementById('calc_elec_usage').innerText = elecUsage;
            document.getElementById('calc_elec_amount_text').innerText = formatMoney(elecAmount) + ' VNĐ';

            // 3. Nước
            let waterAmount = 0;
            let waterUsage = 0;
            if (d.water_calculation_type === 'khoi') {
                const waterOld = parseFloat(document.getElementById('calc_water_old').value) || 0;
                const waterNew = parseFloat(document.getElementById('calc_water_new').value) || waterOld;
                const waterPrice = parseFloat(d.water_price_block) || 25000;
                waterUsage = Math.max(0, waterNew - waterOld);
                waterAmount = Math.round(waterUsage * waterPrice);
                document.getElementById('calc_water_usage').innerText = waterUsage;
            } else {
                const waterPrice = parseFloat(d.water_price_person) || 100000;
                const count = parseInt(d.water_people_count) || 1;
                waterUsage = count;
                waterAmount = Math.round(waterPrice * count);
                document.getElementById('calc_water_usage').innerText = `${count} người`;
            }
            document.getElementById('calc_water_amount_text').innerText = formatMoney(waterAmount) + ' VNĐ';

            // 4. Dịch vụ cố định tháng cuối
            const internet = parseFloat(d.internet_price) || 0;
            const garbage = parseFloat(d.garbage_price) || 0;
            const parking = parseFloat(d.parking_fee) || 0;
            const fixedServicesTotal = internet + garbage + parking;
            const servicesAmount = isProrated ? Math.round((fixedServicesTotal / 30) * stayDays) : fixedServicesTotal;
            document.getElementById('calc_services_text').innerText = formatMoney(servicesAmount) + ' VNĐ';

            // 5. Phí bồi thường hư hỏng (Damage Fee)
            const damageFee = Math.max(0, parseFloat(document.getElementById('calc_damage_fee').value) || 0);

            // ==========================================
            // TỔNG NỢ KHÁCH PHẢI TRẢ = Phòng + Điện + Nước + Dịch vụ + Bồi thường
            // ==========================================
            const totalDebt = proratedRent + elecAmount + waterAmount + servicesAmount + damageFee;

            // TỔNG ĐỐI TRỪ/HOÀN = Cọc + Tiền phòng đóng trước chưa ở
            const totalCredits = depositAmount + unusedRentRefund;

            // CHÊNH LỆCH = Tổng nợ - Tổng đối trừ
            const netAmount = totalDebt - totalCredits;

            // Cập nhật Cột phải Live Summary
            document.getElementById('sum_prorated_rent').innerText = '+ ' + formatMoney(proratedRent) + ' đ';
            document.getElementById('sum_electricity').innerText = '+ ' + formatMoney(elecAmount) + ' đ';
            document.getElementById('sum_water').innerText = '+ ' + formatMoney(waterAmount) + ' đ';
            document.getElementById('sum_services').innerText = '+ ' + formatMoney(servicesAmount) + ' đ';
            document.getElementById('sum_damage').innerText = '+ ' + formatMoney(damageFee) + ' đ';
            document.getElementById('sum_total_debt').innerText = formatMoney(totalDebt) + ' VNĐ';
            document.getElementById('sum_deposit').innerText = '- ' + formatMoney(depositAmount) + ' VNĐ';

            const unusedRow = document.getElementById('row_sum_unused_rent');
            if (unusedRow) {
                if (unusedRentRefund > 0) {
                    unusedRow.style.display = 'flex';
                    document.getElementById('sum_unused_rent').innerText = '- ' + formatMoney(unusedRentRefund) + ' VNĐ';
                } else {
                    unusedRow.style.display = 'none';
                }
            }

            // Lưu cache tính toán hiện tại
            currentSettlementCalculation = {
                prorated_rent: proratedRent,
                stay_days: stayDays,
                is_prorated: isProrated,
                electricity_old: elecOld,
                electricity_new: elecNew,
                electricity_usage: elecUsage,
                electricity_amount: elecAmount,
                water_old: parseFloat(document.getElementById('calc_water_old').value) || 0,
                water_new: parseFloat(document.getElementById('calc_water_new').value) || 0,
                water_usage: waterUsage,
                water_amount: waterAmount,
                fixed_services_amount: servicesAmount,
                damage_fee: damageFee,
                damage_reason: document.getElementById('calc_damage_reason').value,
                total_debt: totalDebt,
                deposit_amount: depositAmount,
                net_amount: netAmount,
            };

            // RẼ NHÁNH KỊCH BẢN TRỰC QUAN REALTIME
            renderScenarioPreview(netAmount);
        }

        /**
         * Render Hộp Kịch bản Realtime (Đỏ nếu Nợ > Cọc, Xanh nếu Cọc > Nợ)
         */
        function renderScenarioPreview(netAmount) {
            const previewBox = document.getElementById('scenarioPreviewBox');
            const title = document.getElementById('scenarioTitle');
            const amount = document.getElementById('scenarioAmount');
            const desc = document.getElementById('scenarioDesc');
            const bankSection = document.getElementById('refundBankInfoSection');

            // KỊCH BẢN A: Khách nợ NHIỀU HƠN Cọc (netAmount > 0)
            if (netAmount > 0) {
                previewBox.className = 'scenario-box scenario-box-A';
                title.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> KỊCH BẢN A: KHÁCH CẦN THANH TOÁN THÊM';
                amount.innerText = `+ ${formatMoney(netAmount)} VNĐ`;
                amount.style.color = '#dc2626';
                desc.innerHTML = '<strong>Hợp đồng sẽ chuyển sang pending_liquidation</strong> và hệ thống tự động xuất một Hóa đơn cuối cùng (Final Invoice) vào collection <code>invoices</code> (status: unpaid) để thu tiền khách.';
                bankSection.style.display = 'none';

            // KỊCH BẢN B: Cọc NHIỀU HƠN Nợ (netAmount < 0)
            } else if (netAmount < 0) {
                const refund = Math.abs(netAmount);
                previewBox.className = 'scenario-box scenario-box-B';
                title.innerHTML = '<i class="fa-solid fa-circle-check"></i> KỊCH BẢN B: CÔNG TY CẦN HOÀN TRẢ KHÁCH';
                amount.innerText = `Hoàn lại: ${formatMoney(refund)} VNĐ`;
                amount.style.color = '#059669';
                desc.innerHTML = '<strong>Hợp đồng sẽ chuyển sang pending_refund</strong> và hệ thống tạo bản ghi Yêu cầu Hoàn tiền (Refund Request) đẩy lên Kế toán/Admin xử lý chuyển khoản.';
                bankSection.style.display = 'block';

            // KỊCH BẢN C: Cân bằng (netAmount == 0)
            } else {
                previewBox.className = 'scenario-box scenario-box-C';
                title.innerHTML = '<i class="fa-solid fa-scale-balanced"></i> CÂN BẰNG: ĐỐI SOÁT HÒA NỢ';
                amount.innerText = '0 VNĐ';
                amount.style.color = '#475569';
                desc.innerHTML = 'Tổng nợ bằng đúng tiền cọc. Hợp đồng sẽ được thanh lý hoàn tất (terminated) ngay và mở lại phòng trống (available).';
                bankSection.style.display = 'none';
            }
        }

        /**
         * BƯỚC 3: SUBMIT FORM PHIẾU QUYẾT TOÁN (CHỐT CÔNG NỢ & RẼ NHÁNH BACKEND)
         */
        async function submitLiquidationForm() {
            if (!currentContractData || !currentSettlementCalculation) return;

            const contractId = currentContractData.id;
            const calc = currentSettlementCalculation;
            const checkoutDate = document.getElementById('calc_checkout_date').value;

            if (!checkoutDate) {
                Swal.fire('Lỗi', 'Vui lòng chọn ngày trả phòng thực tế!', 'warning');
                return;
            }

            const netAmount = calc.net_amount;
            let confirmTitle = '';
            let confirmHtml = '';
            let confirmButtonColor = '#0284c7';

            if (netAmount > 0) {
                confirmTitle = 'Xác nhận chốt KỊCH BẢN A?';
                confirmHtml = `
                    <div style="text-align: left; font-size: 0.9rem; color: #475569; line-height: 1.5;">
                        Tổng nợ phát sinh: <strong>${formatMoney(calc.total_debt)} đ</strong><br>
                        Tiền cọc cấn trừ: <strong>-${formatMoney(calc.deposit_amount)} đ</strong><br>
                        <hr style="margin: 8px 0; border: none; border-top: 1px dashed #cbd5e1;">
                        <span style="color: #dc2626; font-size: 1.05rem; font-weight: 800;">
                            Khách cần thanh toán thêm: ${formatMoney(netAmount)} VNĐ
                        </span><br><br>
                        Hệ thống sẽ chuyển hợp đồng sang <code>pending_liquidation</code> và tạo Hóa đơn cuối cùng để thu tiền.
                    </div>
                `;
                confirmButtonColor = '#dc2626';
            } else if (netAmount < 0) {
                const refund = Math.abs(netAmount);
                confirmTitle = 'Xác nhận chốt KỊCH BẢN B?';
                confirmHtml = `
                    <div style="text-align: left; font-size: 0.9rem; color: #475569; line-height: 1.5;">
                        Tổng nợ phát sinh: <strong>${formatMoney(calc.total_debt)} đ</strong><br>
                        Tiền cọc đối trọng: <strong>${formatMoney(calc.deposit_amount)} đ</strong><br>
                        <hr style="margin: 8px 0; border: none; border-top: 1px dashed #cbd5e1;">
                        <span style="color: #059669; font-size: 1.05rem; font-weight: 800;">
                            Công ty cần hoàn trả khách: ${formatMoney(refund)} VNĐ
                        </span><br><br>
                        Hệ thống sẽ chuyển hợp đồng sang <code>pending_refund</code> và gửi Yêu cầu Hoàn tiền lên Kế toán.
                    </div>
                `;
                confirmButtonColor = '#059669';
            } else {
                confirmTitle = 'Xác nhận thanh lý hòa nợ?';
                confirmHtml = 'Tổng nợ bằng đúng tiền cọc. Hợp đồng sẽ được hoàn tất thanh lý (terminated) ngay.';
            }

            const { isConfirmed } = await Swal.fire({
                title: confirmTitle,
                html: confirmHtml,
                icon: (netAmount > 0) ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Xác nhận chốt quyết toán',
                cancelButtonText: 'Xem lại',
                reverseButtons: true
            });

            if (!isConfirmed) return;

            // Thu thập toàn bộ payload
            const payload = {
                checkout_date: checkoutDate,
                is_prorated: document.getElementById('calc_is_prorated').checked ? 1 : 0,
                stay_days: parseInt(document.getElementById('calc_stay_days').value) || 1,
                electricity_old: parseFloat(document.getElementById('calc_elec_old').value) || 0,
                electricity_new: parseFloat(document.getElementById('calc_elec_new').value) || 0,
                water_old: parseFloat(document.getElementById('calc_water_old').value) || 0,
                water_new: parseFloat(document.getElementById('calc_water_new').value) || 0,
                damage_fee: parseFloat(document.getElementById('calc_damage_fee').value) || 0,
                damage_reason: document.getElementById('calc_damage_reason').value,
                note: document.getElementById('calc_general_note').value,
                refund_bank_account: document.getElementById('calc_refund_bank_account').value,
                refund_bank_name: document.getElementById('calc_refund_bank_name').value,
                refund_bank_holder: document.getElementById('calc_refund_bank_holder').value,
            };

            Swal.fire({
                title: 'Đang xử lý quyết toán...',
                text: 'Hệ thống đang rẽ nhánh nghiệp vụ và cập nhật dữ liệu MongoDB...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`/api/contracts/${contractId}/settle-liquidation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (!data.success) {
                    throw new Error(data.message || 'Không thể xử lý quyết toán.');
                }

                closeLiquidationModal();

                // Hiển thị thông báo theo Kịch bản chính xác theo đề bài
                if (data.scenario === 'A') {
                    // Trả về thông báo (chữ đỏ): "Khách cần thanh toán thêm: X VNĐ. Đã tạo hóa đơn thu tiền."
                    Swal.fire({
                        icon: 'warning',
                        title: '<span style="color: #dc2626;">KỊCH BẢN A: CHỐT CÔNG NỢ</span>',
                        html: `
                            <div style="font-size: 1rem; color: #dc2626; font-weight: 800; margin-bottom: 8px;">
                                ${data.message}
                            </div>
                            <div style="font-size: 0.85rem; color: #475569;">
                                Mã hóa đơn: <strong>${data.invoice_code}</strong><br>
                                Trạng thái hợp đồng đã chuyển thành: <code>pending_liquidation</code>.
                            </div>
                        `,
                        confirmButtonText: '<i class="fa-solid fa-receipt"></i> Xem hóa đơn thu tiền',
                        showCancelButton: true,
                        cancelButtonText: 'Đóng',
                        confirmButtonColor: '#dc2626'
                    }).then((result) => {
                        if (result.isConfirmed && data.invoice_url) {
                            window.open(data.invoice_url, '_blank');
                        }
                        location.reload();
                    });

                } else if (data.scenario === 'B') {
                    // Trả về thông báo (chữ xanh): "Công ty cần hoàn trả khách: Y VNĐ. Đã gửi yêu cầu lên Kế toán."
                    Swal.fire({
                        icon: 'success',
                        title: '<span style="color: #059669;">KỊCH BẢN B: HOÀN TRẢ TIỀN CỌC</span>',
                        html: `
                            <div style="font-size: 1rem; color: #059669; font-weight: 800; margin-bottom: 8px;">
                                ${data.message}
                            </div>
                            <div style="font-size: 0.85rem; color: #475569;">
                                Số tiền hoàn: <strong>${data.formatted}</strong><br>
                                Trạng thái hợp đồng đã chuyển thành: <code>pending_refund</code>.
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã hoàn tất thanh lý!',
                        text: data.message,
                        confirmButtonColor: '#0284c7'
                    }).then(() => {
                        location.reload();
                    });
                }

            } catch (err) {
                Swal.fire('Thất bại', err.message, 'error');
            }
        }

        /**
         * BƯỚC 4 (Kịch bản B): Kế toán bấm "Xác nhận đã hoàn tiền"
         */
        async function confirmRefundDirect(contractId, tenantName, refundAmount) {
            const { value: refCode, isConfirmed } = await Swal.fire({
                title: 'Xác nhận hoàn tiền cọc?',
                html: `
                    <div style="text-align: left; font-size: 0.9rem; color: #475569; line-height: 1.5;">
                        Kế toán xác nhận công ty đã chuyển khoản hoàn trả <strong>${formatMoney(refundAmount)} VNĐ</strong> cho khách hàng <strong>"${tenantName}"</strong>.<br><br>
                        <span style="color: #059669; font-weight: 700;">
                            Hành động này sẽ chính thức thanh lý hợp đồng (terminated) và tự động mở lại phòng trống (status = 'available').
                        </span>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Xác nhận đã chuyển tiền',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            });

            if (!isConfirmed) return;

            Swal.fire({
                title: 'Đang cập nhật...',
                text: 'Hệ thống đang giải phóng phòng về available và hoàn tất hợp đồng...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`/api/contracts/${contractId}/confirm-refund`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();

                if (!data.success) {
                    throw new Error(data.message || 'Không thể xác nhận hoàn tiền.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Hoàn tất quy trình!',
                    text: data.message,
                    confirmButtonColor: '#059669'
                }).then(() => location.reload());

            } catch (err) {
                Swal.fire('Lỗi', err.message, 'error');
            }
        }

        /**
         * BƯỚC 4 (Kịch bản A): Thu ngân / Kế toán xác nhận đã thu đủ tiền Hóa đơn cuối cùng
         */
        async function confirmFinalPaidDirect(contractId, tenantName, debtAmount) {
            const { isConfirmed } = await Swal.fire({
                title: 'Xác nhận đã thu nợ?',
                html: `
                    <div style="text-align: left; font-size: 0.9rem; color: #475569; line-height: 1.5;">
                        Xác nhận khách hàng <strong>"${tenantName}"</strong> đã thanh toán đủ <strong>${formatMoney(debtAmount)} VNĐ</strong> tiền nợ phát sinh tháng cuối.<br><br>
                        <span style="color: #dc2626; font-weight: 700;">
                            Hóa đơn cuối cùng sẽ chuyển sang PAID, hợp đồng chuyển sang TERMINATED và phòng sẽ tự động chuyển về AVAILABLE để cho thuê tiếp.
                        </span>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-receipt"></i> Xác nhận đã thu đủ tiền',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            });

            if (!isConfirmed) return;

            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Hệ thống đang thanh lý hợp đồng và mở lại phòng trống...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`/api/contracts/${contractId}/confirm-final-paid`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ payment_method: 'cash' })
                });
                const data = await res.json();

                if (!data.success) {
                    throw new Error(data.message || 'Không thể xác nhận thu tiền.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    confirmButtonColor: '#0284c7'
                }).then(() => location.reload());

            } catch (err) {
                Swal.fire('Lỗi', err.message, 'error');
            }
        }

        // Helper format money
        function formatMoney(num) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(num || 0));
        }
    </script>
</body>

</html>