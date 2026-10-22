<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Tài Khoản Cá Nhân (Khách Thuê) - RentHome Admin</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --bg-main: #f4f6fa;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);

            --brand-primary: #0284c7; /* Ocean Blue */
            --brand-hover: #0369a1;
            --brand-light: #f0f9ff;
            --brand-subtle: #e0f2fe;

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;

            --accent-emerald: #059669;
            --accent-emerald-bg: #ecfdf5;
            --accent-amber: #d97706;
            --accent-amber-bg: #fffbeb;
            --accent-red: #dc2626;
            --accent-red-bg: #fef2f2;
            --accent-purple: #7c3aed;
            --accent-purple-bg: #f5f3ff;

            --sidebar-width: 260px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-family);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
            min-height: 100vh;
            background-color: var(--bg-main);
            max-width: calc(100vw - var(--sidebar-width));
            transition: var(--transition);
        }

        body.sidebar-collapsed .admin-main {
            margin-left: 78px !important;
            max-width: calc(100vw - 78px) !important;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--brand-primary);
        }

        .page-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-refresh {
            background-color: #ffffff;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 9px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-refresh:hover {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            transform: translateY(-1px);
        }

        /* 4 Thẻ Thống Kê Tổng Quan */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 22px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-card);
            transform: translateY(-2px);
            border-color: var(--brand-primary);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-icon.icon-blue {
            background-color: var(--brand-subtle);
            color: var(--brand-primary);
        }

        .stat-icon.icon-emerald {
            background-color: var(--accent-emerald-bg);
            color: var(--accent-emerald);
        }

        .stat-icon.icon-amber {
            background-color: var(--accent-amber-bg);
            color: var(--accent-amber);
        }

        .stat-icon.icon-purple {
            background-color: var(--accent-purple-bg);
            color: var(--accent-purple);
        }

        .stat-val {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Card Danh Sách & Bảng */
        .content-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header-bar {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            background-color: #ffffff;
        }

        .card-title-text {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            min-width: 270px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .search-input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.85rem;
            outline: none;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .search-input:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .select-filter {
            padding: 9px 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.85rem;
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .select-filter:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
        }

        .badge-count {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            background-color: var(--brand-subtle);
            color: var(--brand-primary);
        }

        /* Bảng Danh Sách Người Dùng */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        .data-table th {
            padding: 14px 20px;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-primary);
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* User Meta Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
            font-weight: 800;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.2);
            overflow: hidden;
            border: 2px solid #ffffff;
        }

        .user-avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
        }

        .user-fullname {
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
            font-size: 0.925rem;
        }

        .user-username-badge {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Contact Meta */
        .contact-cell {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .contact-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-item:hover {
            color: var(--brand-primary);
        }

        .contact-item i {
            width: 14px;
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill-active {
            background-color: var(--accent-emerald-bg);
            color: var(--accent-emerald);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        .status-pill-locked {
            background-color: var(--accent-red-bg);
            color: var(--accent-red);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        /* Badges bài đăng & phòng thuê */
        .badges-stack {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            width: fit-content;
        }

        .badge-posts {
            background-color: var(--brand-subtle);
            color: var(--brand-primary);
        }

        .badge-renting {
            background-color: var(--accent-purple-bg);
            color: var(--accent-purple);
        }

        /* Thao tác (Action Buttons) */
        .action-btns {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-act {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Nút Xem chi tiết (Blue) */
        .btn-act.btn-view {
            color: var(--brand-primary);
            border-color: rgba(2, 132, 199, 0.3);
            background-color: var(--brand-light);
        }

        .btn-act.btn-view:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
        }

        /* Nút Khóa (Cam/Vàng) */
        .btn-act.btn-lock {
            color: var(--accent-amber);
            border-color: rgba(217, 119, 6, 0.3);
            background-color: var(--accent-amber-bg);
        }

        .btn-act.btn-lock:hover {
            background-color: var(--accent-amber);
            color: #ffffff;
            border-color: var(--accent-amber);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);
        }

        /* Nút Mở Khóa */
        .btn-act.btn-unlock {
            color: var(--accent-emerald);
            border-color: rgba(5, 150, 105, 0.3);
            background-color: var(--accent-emerald-bg);
        }

        .btn-act.btn-unlock:hover {
            background-color: var(--accent-emerald);
            color: #ffffff;
            border-color: var(--accent-emerald);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        /* Nút Gỡ bài đăng (Tím) */
        .btn-act.btn-takedown {
            color: var(--accent-purple);
            border-color: rgba(124, 58, 237, 0.3);
            background-color: var(--accent-purple-bg);
        }

        .btn-act.btn-takedown:hover {
            background-color: var(--accent-purple);
            color: #ffffff;
            border-color: var(--accent-purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
        }

        /* Nút Xóa (Đỏ) */
        .btn-act.btn-delete {
            color: var(--accent-red);
            border-color: rgba(220, 38, 38, 0.3);
            background-color: var(--accent-red-bg);
        }

        .btn-act.btn-delete:hover {
            background-color: var(--accent-red);
            color: #ffffff;
            border-color: var(--accent-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }

        /* Switch Toggle nhỏ trong bảng */
        .switch-toggle-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        /* Empty State */
        .empty-placeholder {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-placeholder i {
            font-size: 3rem;
            margin-bottom: 14px;
            color: #cbd5e1;
            display: block;
        }

        /* ==================================================== */
        /* MODAL CHI TIẾT TÀI KHOẢN (YÊU CẦU 1)                  */
        /* ==================================================== */
        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-backdrop-custom.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box-custom {
            background-color: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 860px;
            max-height: 90vh;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(20px) scale(0.98);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-backdrop-custom.active .modal-box-custom {
            transform: translateY(0) scale(1);
        }

        .modal-header-custom {
            padding: 20px 28px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .modal-user-summary {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modal-avatar-lg {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }

        .modal-avatar-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-user-meta h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .modal-user-meta p {
            font-size: 0.825rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .btn-close-modal {
            background: transparent;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.1rem;
        }

        .btn-close-modal:hover {
            background-color: #f1f5f9;
            color: var(--accent-red);
        }

        /* Modal Tabs */
        .modal-tabs-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 28px 0 28px;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-tab-btn {
            padding: 10px 18px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .modal-tab-btn:hover {
            color: var(--brand-primary);
        }

        .modal-tab-btn.active {
            color: var(--brand-primary);
            border-bottom-color: var(--brand-primary);
        }

        .modal-body-custom {
            padding: 24px 28px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-tab-pane {
            display: none;
        }

        .modal-tab-pane.active {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 12px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.925rem;
            font-weight: 700;
            color: var(--text-primary);
            word-break: break-all;
        }

        /* Post & Contract Cards inside Modal */
        .modal-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .modal-section-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-takedown-all {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.825rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
            transition: var(--transition);
        }

        .btn-takedown-all:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        }

        .item-list-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .item-list-table th {
            padding: 10px 14px;
            background-color: #f8fafc;
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            border-bottom: 1px solid var(--border-color);
        }

        .item-list-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-secondary);
        }

        .item-list-table tr:last-child td {
            border-bottom: none;
        }

        .item-list-table tr:hover td {
            background-color: #f8fafc;
        }

        .post-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .modal-footer-custom {
            padding: 16px 28px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
            flex-wrap: wrap;
            gap: 12px;
        }

        .modal-footer-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .admin-main {
                padding: 16px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Header & Sidebar Admin Dùng Chung -->
    @include('partials.header-admin')

    <!-- Vùng Nội Dung Chính -->
    <main class="admin-main">
        <!-- Tiêu đề trang & Breadcrumb -->
        <header class="page-header">
            <div class="page-title-box">
                <nav class="breadcrumb">
                    <a href="{{ url('Admin') }}"><i class="fa-solid fa-house"></i> Admin</a>
                    <span>/</span>
                    <span>Quản lý Tài khoản cá nhân</span>
                </nav>
                <h1 class="page-title">
                    <i class="fa-solid fa-users" style="color: var(--brand-primary);"></i>
                    Quản Lý Tài Khoản Cá Nhân
                </h1>
                <p class="page-subtitle">Quản trị danh sách khách thuê & người dùng phổ thông, kiểm soát trạng thái vi phạm và quản lý tin đăng.</p>
            </div>
            <div class="header-actions">
                <a href="{{ url('Admin/tai-khoan-ca-nhan') }}" class="btn-refresh" title="Tải lại dữ liệu">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Làm mới</span>
                </a>
            </div>
        </header>

        <!-- 4 Thẻ Thống Kê Tổng Quan -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="stat-val">{{ number_format($stats['total'] ?? 0) }}</div>
                    <div class="stat-label">Tổng tài khoản cá nhân</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="stat-val" style="color: var(--accent-emerald);">{{ number_format($stats['active'] ?? 0) }}</div>
                    <div class="stat-label">Đang hoạt động bình thường</div>
                </div>
            </div>

            <div class="stat-card" style="border-left: 4px solid var(--accent-red);">
                <div class="stat-icon icon-amber">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-locked-count" style="color: var(--accent-red);">{{ number_format($stats['locked'] ?? 0) }}</div>
                    <div class="stat-label">Tài khoản vi phạm / Đã khóa</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fa-solid fa-house-user"></i>
                </div>
                <div>
                    <div class="stat-val" style="color: var(--accent-purple);">{{ number_format($stats['renting'] ?? 0) }}</div>
                    <div class="stat-label">Khách hàng đang thuê phòng</div>
                </div>
            </div>
        </div>

        <!-- Khối Bảng Danh Sách Người Dùng -->
        <div class="content-card">
            <!-- Thanh Công Cụ: Tìm kiếm & Lọc -->
            <div class="card-header-bar">
                <div class="card-title-text">
                    <i class="fa-solid fa-list-ul" style="color: var(--brand-primary);"></i>
                    Danh sách người dùng
                    <span class="badge-count" id="users-count-badge">{{ count($users) }} tài khoản</span>
                </div>

                <div class="filter-group">
                    <!-- Thanh Tìm Kiếm Trực Tiếp -->
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Tìm theo tên, email, SĐT, username..." oninput="handleSearchFilter()">
                    </div>

                    <!-- Lọc Trạng Thái -->
                    <select id="statusFilter" class="select-filter" onchange="handleSearchFilter()">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="active">Đang hoạt động</option>
                        <option value="locked">Bị khóa / Vi phạm</option>
                        <option value="renting">Đang thuê phòng</option>
                        <option value="not_renting">Chưa thuê phòng</option>
                    </select>
                </div>
            </div>

            <!-- Bảng Dữ Liệu -->
            <div class="table-responsive">
                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Khách thuê / Người dùng</th>
                            <th>Thông tin liên hệ</th>
                            <th>Trạng thái</th>
                            <th>Tình trạng thuê phòng</th>
                            <th>Ngày đăng ký</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($users as $index => $u)
                            @php
                                $uId = (string)($u->_id ?? $u->id);
                                $displayName = $u->account_name ?: ($u->username ?: 'Khách hàng');
                                $handle = $u->username ?: explode('@', $u->email ?? 'user')[0];
                                $isLocked = ($u->is_locked === true || in_array($u->status, ['locked', 'khoa', 'vi_pham', 'banned']));
                                $postsCount = $u->posts_count ?? 0;
                                $rentedCount = $u->rented_rooms_count ?? 0;
                            @endphp
                            <tr class="user-row" 
                                id="user-row-{{ $uId }}" 
                                data-id="{{ $uId }}"
                                data-name="{{ mb_strtolower($displayName) }}"
                                data-username="{{ mb_strtolower($u->username ?? '') }}"
                                data-email="{{ mb_strtolower($u->email ?? '') }}"
                                data-phone="{{ $u->phone ?? '' }}"
                                data-locked="{{ $isLocked ? '1' : '0' }}"
                                data-posts="{{ $postsCount }}"
                                data-renting="{{ $rentedCount }}">
                                <td style="color: var(--text-muted); font-weight: 700;">
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-box">
                                            @if(!empty($u->avatar))
                                                <img src="{{ $u->display_avatar }}" alt="{{ $displayName }}" onerror="this.src='{{ $u->display_avatar }}'">
                                            @else
                                                {{ strtoupper(mb_substr($displayName, 0, 1, 'UTF-8')) }}
                                            @endif
                                        </div>
                                        <div class="user-info-text">
                                            <span class="user-fullname">{{ $displayName }}</span>
                                            <span class="user-username-badge">{{ '@' . $handle }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-cell">
                                        <a href="mailto:{{ $u->email ?? '' }}" class="contact-item" title="Gửi email">
                                            <i class="fa-solid fa-envelope"></i>
                                            <span>{{ $u->email ?: 'Chưa cập nhật' }}</span>
                                        </a>
                                        <a href="tel:{{ $u->phone ?? '' }}" class="contact-item" title="Gọi điện">
                                            <i class="fa-solid fa-phone"></i>
                                            <span>{{ $u->phone ?: 'Chưa cập nhật' }}</span>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div id="status-pill-container-{{ $uId }}">
                                        @if($isLocked)
                                            <span class="status-pill status-pill-locked" title="{{ $u->lock_reason ?: 'Vi phạm tiêu chuẩn cộng đồng' }}">
                                                <i class="fa-solid fa-lock"></i> Đã khóa / Vi phạm
                                            </span>
                                        @else
                                            <span class="status-pill status-pill-active">
                                                <i class="fa-solid fa-circle-check"></i> Hoạt động
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="badges-stack">
                                        @if($rentedCount > 0)
                                            <span class="mini-badge badge-renting" title="Số phòng đang thuê có hợp đồng">
                                                <i class="fa-solid fa-house-chimney-user"></i>
                                                <span>{{ $rentedCount }} phòng đang thuê</span>
                                            </span>
                                        @else
                                            <span class="mini-badge" style="background-color: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0;" title="Chưa có hợp đồng thuê phòng">
                                                <i class="fa-solid fa-bed"></i>
                                                <span>Chưa thuê phòng</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                @php
                                    $formattedCreatedAt = '--';
                                    if (!empty($u->created_at)) {
                                        try {
                                            $formattedCreatedAt = is_string($u->created_at) && preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $u->created_at)
                                                ? $u->created_at
                                                : \Carbon\Carbon::parse($u->created_at)->format('d/m/Y H:i');
                                        } catch (\Exception $e) {
                                            $formattedCreatedAt = (string)$u->created_at;
                                        }
                                    }
                                @endphp
                                <td style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 600;">
                                    {{ $formattedCreatedAt }}
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btns">
                                        <!-- YÊU CẦU 1: Nút Xem chi tiết tài khoản (Màu xanh blue / icon mắt) -->
                                        <button type="button" class="btn-act btn-view" title="Xem chi tiết tài khoản" onclick="openCustomerDetails('{{ $uId }}')">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        <!-- YÊU CẦU 2: Nút Khóa / Mở khóa tài khoản (Màu cam/vàng) -->
                                        <button type="button" 
                                            class="btn-act {{ $isLocked ? 'btn-unlock' : 'btn-lock' }}" 
                                            id="btn-lock-{{ $uId }}"
                                            title="{{ $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản vi phạm' }}" 
                                            onclick="confirmToggleLock('{{ $uId }}', '{{ addslashes($displayName) }}', {{ $isLocked ? 'true' : 'false' }})">
                                            <i class="fa-solid {{ $isLocked ? 'fa-lock-open' : 'fa-lock' }}" id="lock-icon-{{ $uId }}"></i>
                                        </button>

                                        <!-- YÊU CẦU 3: Nút Xóa tài khoản (Màu đỏ / icon thùng rác) -->
                                        <button type="button" class="btn-act btn-delete" title="Xóa tài khoản" onclick="confirmDeleteUser('{{ $uId }}', '{{ addslashes($displayName) }}')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row">
                                <td colspan="7">
                                    <div class="empty-placeholder">
                                        <i class="fa-solid fa-users-slash"></i>
                                        <p style="font-weight: 700; font-size: 1rem; color: var(--text-secondary);">Chưa có tài khoản khách thuê nào</p>
                                        <p style="font-size: 0.825rem; margin-top: 4px;">Tài khoản khách thuê cá nhân đăng ký tham gia hệ thống sẽ xuất hiện tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ==================================================== -->
    <!-- MODAL CHI TIẾT TÀI KHOẢN (POPUP ẨN KẾT HỢP AJAX)    -->
    <!-- ==================================================== -->
    <div class="modal-backdrop-custom" id="customerDetailsModal" onclick="handleBackdropClick(event)">
        <div class="modal-box-custom">
            <!-- 1. Phần Header: Tiêu đề "Thông tin chi tiết Khách hàng" & Nút đóng (X) -->
            <div class="modal-header-custom">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background-color: var(--brand-subtle); color: var(--brand-primary); display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                        <i class="fa-solid fa-address-card"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); line-height: 1.3;">
                            Thông tin chi tiết Khách hàng
                        </h2>
                        <span style="font-size: 0.78rem; color: var(--text-muted); font-weight: 600;">
                            Hồ sơ người dùng & Thống kê dữ liệu hoạt động thực tế
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeCustomerDetails()" title="Đóng (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Profile Summary Box Trên Cùng Của Modal -->
            <div style="padding: 18px 28px 12px 28px; background-color: #ffffff; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div class="modal-user-summary">
                    <div class="modal-avatar-lg" id="modalUserAvatar">
                        U
                    </div>
                    <div class="modal-user-meta">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <h3 id="modalUserName" style="font-size: 1.2rem; font-weight: 800; color: var(--text-primary);">Đang tải...</h3>
                            <span id="modalStatusBadge"></span>
                        </div>
                        <p id="modalUserSubtitle" style="color: var(--text-secondary); font-size: 0.825rem; margin-top: 2px;">
                            @username &bull; Khách thuê cá nhân
                        </p>
                    </div>
                </div>

                <!-- Phần Thống Kê Tổng Quan Dưới Dạng Badges Nổi Bật -->
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <div style="padding: 8px 18px; background-color: var(--accent-purple-bg); border-radius: 12px; border: 1px solid rgba(124, 58, 237, 0.2); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-house-chimney-user" style="color: var(--accent-purple); font-size: 1.25rem;"></i>
                        <div>
                            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Phòng đang thuê</div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: var(--accent-purple);" id="statBoxRentedCount">0 phòng</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation Trong Modal -->
            <div class="modal-tabs-nav">
                <button type="button" class="modal-tab-btn active" id="tabBtnInfo" onclick="switchModalTab('tabInfo')">
                    <i class="fa-solid fa-user"></i>
                    <span>Hồ sơ cá nhân</span>
                </button>
                <button type="button" class="modal-tab-btn" id="tabBtnContracts" onclick="switchModalTab('tabContracts')">
                    <i class="fa-solid fa-key"></i>
                    <span>Phòng đang thuê (<span id="modalContractsCountBadge">0</span>)</span>
                </button>
            </div>

            <!-- 2. Phần Body & Thống kê chi tiết -->
            <div class="modal-body-custom">
                <!-- Tab 1: Thông tin cá nhân (Avatar, Họ tên, SĐT, Email, Ngày đăng ký) -->
                <div class="modal-tab-pane active" id="tabInfo">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-signature"></i> Họ và tên</div>
                            <div class="info-value" id="infoFullName">---</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-user-tag"></i> Username (Tên tài khoản)</div>
                            <div class="info-value" id="infoUsername">---</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-envelope"></i> Địa chỉ Email</div>
                            <div class="info-value" id="infoEmail">---</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-phone"></i> Số điện thoại</div>
                            <div class="info-value" id="infoPhone">---</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-calendar-days"></i> Ngày đăng ký</div>
                            <div class="info-value" id="infoCreatedAt">---</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fa-solid fa-shield"></i> Trạng thái tài khoản</div>
                            <div class="info-value" id="infoStatusPill">---</div>
                        </div>
                        <div class="info-item" style="grid-column: span 2;">
                            <div class="info-label"><i class="fa-solid fa-location-dot"></i> Địa chỉ liên hệ</div>
                            <div class="info-value" id="infoAddress">Chưa cập nhật</div>
                        </div>
                        <div class="info-item" id="infoLockReasonBox" style="grid-column: span 2; display: none; background-color: var(--accent-red-bg); border-color: rgba(220, 38, 38, 0.2);">
                            <div class="info-label" style="color: var(--accent-red);"><i class="fa-solid fa-triangle-exclamation"></i> Lý do khóa tài khoản</div>
                            <div class="info-value" id="infoLockReason" style="color: #991b1b; font-size: 0.875rem;">---</div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Danh sách phòng đang thuê (Hợp đồng thuê) -->
                <div class="modal-tab-pane" id="tabContracts">
                    <div class="modal-section-header">
                        <div class="modal-section-title">
                            <i class="fa-solid fa-key" style="color: var(--accent-purple);"></i>
                            Danh sách phòng trọ & hợp đồng đang thuê
                        </div>
                    </div>

                    <div id="modalContractsContainer">
                        <!-- Danh sách phòng thuê sẽ render ở đây qua JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer-custom">
                <button type="button" class="btn-secondary" onclick="closeCustomerDetails()">
                    <i class="fa-solid fa-xmark"></i> Đóng
                </button>
                <div class="modal-footer-actions">
                    <button type="button" class="btn-secondary" id="modalBtnToggleLock" style="font-weight: 700;">
                        <i class="fa-solid fa-lock"></i> Khóa tài khoản
                    </button>
                    <button type="button" class="btn-secondary" id="modalBtnDelete" style="color: var(--accent-red); border-color: rgba(220, 38, 38, 0.3);">
                        <i class="fa-solid fa-trash-can"></i> Xóa tài khoản
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================================================== -->
    <!-- LOGIC JAVASCRIPT & AJAX                             -->
    <!-- ==================================================== -->
    <script>
        // Cấu hình CSRF Token cho tất cả request Fetch/AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentModalUserId = null;
        let currentModalUserData = null;

        /**
         * Tìm kiếm & Lọc thời gian thực trên bảng
         */
        function handleSearchFilter() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('.user-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const username = row.getAttribute('data-username') || '';
                const email = row.getAttribute('data-email') || '';
                const phone = row.getAttribute('data-phone') || '';
                const isLocked = row.getAttribute('data-locked') === '1';
                const posts = parseInt(row.getAttribute('data-posts') || '0');
                const renting = parseInt(row.getAttribute('data-renting') || '0');

                // Khớp từ khóa tìm kiếm
                const matchSearch = (!query) || 
                    name.includes(query) || 
                    username.includes(query) || 
                    email.includes(query) || 
                    phone.includes(query);

                // Khớp bộ lọc trạng thái
                let matchStatus = true;
                if (statusFilter === 'active') {
                    matchStatus = !isLocked;
                } else if (statusFilter === 'locked') {
                    matchStatus = isLocked;
                } else if (statusFilter === 'renting') {
                    matchStatus = (renting > 0);
                } else if (statusFilter === 'not_renting') {
                    matchStatus = (renting === 0);
                }

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const badge = document.getElementById('users-count-badge');
            if (badge) {
                badge.innerText = `${visibleCount} tài khoản`;
            }
        }

        /**
         * YÊU CẦU 1: AJAX mở Modal Xem chi tiết tài khoản khách hàng
         * Gọi Route API: /admin/customers/{id}/details
         */
        async function openCustomerDetails(id) {
            currentModalUserId = id;

            // Hiệu ứng Loading bằng SweetAlert2
            Swal.fire({
                title: 'Đang tải thông tin...',
                text: 'Vui lòng chờ trong giây lát...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Gọi AJAX / Fetch API tới Route GET: /admin/customers/{id}/details
                const response = await fetch(`/admin/customers/${id}/details`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error(`Lỗi kết nối máy chủ (${response.status})`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Không thể tải chi tiết khách hàng.');
                }

                // Đóng SweetAlert2 loading
                Swal.close();

                currentModalUserData = data;

                // Đổ dữ liệu vào Modal HTML
                renderUserDetailModal(data);

                // Hiển thị Modal lên màn hình
                const modal = document.getElementById('customerDetailsModal');
                if (modal) {
                    modal.classList.add('active');
                }

                // Mặc định về Tab thông tin cá nhân
                switchModalTab('tabInfo');

            } catch (error) {
                console.error('Lỗi khi tải chi tiết khách hàng:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi tải dữ liệu',
                    text: error.message || 'Không thể kết nối đến máy chủ.',
                    confirmButtonColor: '#0284c7'
                });
            }
        }

        // Alias tương thích
        function openUserDetailModal(userId) {
            return openCustomerDetails(userId);
        }

        function closeCustomerDetails() {
            const modal = document.getElementById('customerDetailsModal');
            if (modal) {
                modal.classList.remove('active');
            }
            currentModalUserId = null;
            currentModalUserData = null;
        }

        function closeUserDetailModal() {
            closeCustomerDetails();
        }

        function handleBackdropClick(event) {
            if (event.target.id === 'customerDetailsModal' || event.target.id === 'userDetailModal') {
                closeCustomerDetails();
            }
        }

        // Đóng modal khi nhấn phím Esc
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCustomerDetails();
            }
        });

        /**
         * Render dữ liệu vào Modal
         */
        function renderUserDetailModal(data) {
            const user = data.user;
            const posts = data.posts || [];
            const contracts = data.contracts || [];

            // Header Modal
            document.getElementById('modalUserName').innerText = user.account_name;
            document.getElementById('modalUserSubtitle').innerText = `@${user.username || 'user'} • Khách thuê cá nhân`;

            const statusBadge = document.getElementById('modalStatusBadge');
            if (statusBadge) {
                if (user.is_locked) {
                    statusBadge.innerHTML = `<span class="status-pill status-pill-locked"><i class="fa-solid fa-lock"></i> Đã khóa</span>`;
                } else {
                    statusBadge.innerHTML = `<span class="status-pill status-pill-active"><i class="fa-solid fa-circle-check"></i> Hoạt động</span>`;
                }
            }

            // Cập nhật số liệu thống kê tổng quan nổi bật
            const statRented = document.getElementById('statBoxRentedCount');
            if (statRented) statRented.innerText = `${contracts.length} phòng`;

            if (user.avatar) {
                document.getElementById('modalUserAvatar').innerHTML = `<img src="${user.avatar}" alt="${user.account_name}">`;
            } else {
                document.getElementById('modalUserAvatar').innerText = (user.account_name || 'U').substring(0, 1).toUpperCase();
            }

            // Tab 1: Thông tin
            document.getElementById('infoFullName').innerText = user.account_name || '---';
            document.getElementById('infoUsername').innerText = user.username ? `@${user.username}` : '---';
            document.getElementById('infoEmail').innerText = user.email || '---';
            document.getElementById('infoPhone').innerText = user.phone || '---';
            document.getElementById('infoCreatedAt').innerText = `${user.created_at} (${user.created_at_human})`;
            document.getElementById('infoAddress').innerText = user.address || 'Chưa cập nhật địa chỉ';

            const statusContainer = document.getElementById('infoStatusPill');
            if (user.is_locked) {
                statusContainer.innerHTML = `<span class="status-pill status-pill-locked"><i class="fa-solid fa-lock"></i> Đã bị khóa</span>`;
                document.getElementById('infoLockReasonBox').style.display = 'block';
                document.getElementById('infoLockReason').innerText = user.lock_reason || 'Vi phạm tiêu chuẩn cộng đồng hoặc bị nghi ngờ lừa đảo.';
            } else {
                statusContainer.innerHTML = `<span class="status-pill status-pill-active"><i class="fa-solid fa-circle-check"></i> Đang hoạt động</span>`;
                document.getElementById('infoLockReasonBox').style.display = 'none';
            }

            // Tab 2: Danh sách phòng đang thuê
            const contractsBadge = document.getElementById('modalContractsCountBadge');
            if (contractsBadge) contractsBadge.innerText = contracts.length;
            const contractsContainer = document.getElementById('modalContractsContainer');

            if (contracts.length > 0) {
                let contractsHtml = `
                    <div style="overflow-x: auto;">
                        <table class="item-list-table">
                            <thead>
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Phòng / Căn hộ</th>
                                    <th>Giá thuê</th>
                                    <th>Tiền cọc</th>
                                    <th>Thời hạn</th>
                                    <th>Trạng thái HĐ</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                contracts.forEach(c => {
                    let contractBadge = '';
                    if (c.status === 'active') {
                        contractBadge = `<span class="status-pill status-pill-active" style="padding: 2px 8px; font-size: 0.7rem;"><i class="fa-solid fa-circle-check"></i> Đang hiệu lực</span>`;
                    } else if (c.status === 'expired' || c.status === 'da_thanh_ly') {
                        contractBadge = `<span class="status-pill" style="background-color: #f1f5f9; color: #64748b; padding: 2px 8px; font-size: 0.7rem;"><i class="fa-solid fa-clock-rotate-left"></i> Đã thanh lý</span>`;
                    } else {
                        contractBadge = `<span class="status-pill" style="background-color: #fee2e2; color: #b91c1c; padding: 2px 8px; font-size: 0.7rem;">${c.status}</span>`;
                    }

                    contractsHtml += `
                        <tr>
                            <td style="font-weight: 700; color: var(--brand-primary);">${c.contract_code}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);">${c.room_title}</div>
                                <div style="font-size: 0.725rem; color: var(--text-muted);">${c.room_address}</div>
                            </td>
                            <td style="font-weight: 700; color: var(--accent-emerald);">${c.rent_price}</td>
                            <td>${c.deposit}</td>
                            <td style="font-size: 0.75rem;">${c.start_date} &rarr; ${c.end_date}</td>
                            <td>${contractBadge}</td>
                        </tr>
                    `;
                });

                contractsHtml += `</tbody></table></div>`;
                contractsContainer.innerHTML = contractsHtml;
            } else {
                contractsContainer.innerHTML = `
                    <div class="empty-placeholder" style="padding: 30px;">
                        <i class="fa-solid fa-door-closed" style="font-size: 2rem;"></i>
                        <p style="font-weight: 600; font-size: 0.875rem;">Hiện tại khách hàng không có hợp đồng thuê phòng nào.</p>
                    </div>
                `;
            }

            // Gắn sự kiện cho các nút footer modal
            const modalBtnLock = document.getElementById('modalBtnToggleLock');
            if (user.is_locked) {
                modalBtnLock.innerHTML = '<i class="fa-solid fa-lock-open"></i> Mở khóa tài khoản';
                modalBtnLock.style.color = 'var(--accent-emerald)';
                modalBtnLock.style.borderColor = 'rgba(5, 150, 105, 0.3)';
                modalBtnLock.onclick = () => confirmToggleLock(user.id, user.account_name, true);
            } else {
                modalBtnLock.innerHTML = '<i class="fa-solid fa-lock"></i> Khóa tài khoản';
                modalBtnLock.style.color = 'var(--accent-amber)';
                modalBtnLock.style.borderColor = 'rgba(217, 119, 6, 0.3)';
                modalBtnLock.onclick = () => confirmToggleLock(user.id, user.account_name, false);
            }

            const modalBtnDelete = document.getElementById('modalBtnDelete');
            modalBtnDelete.onclick = () => confirmDeleteUser(user.id, user.account_name);
        }

        /**
         * Chuyển tab trong modal
         */
        function switchModalTab(tabId) {
            document.querySelectorAll('.modal-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.modal-tab-pane').forEach(p => p.classList.remove('active'));

            if (tabId === 'tabInfo') {
                const btn = document.getElementById('tabBtnInfo');
                const pane = document.getElementById('tabInfo');
                if (btn) btn.classList.add('active');
                if (pane) pane.classList.add('active');
            } else if (tabId === 'tabContracts') {
                const btn = document.getElementById('tabBtnContracts');
                const pane = document.getElementById('tabContracts');
                if (btn) btn.classList.add('active');
                if (pane) pane.classList.add('active');
            }
        }

        function closeUserDetailModal() {
            const modal = document.getElementById('userDetailModal');
            modal.classList.remove('active');
            currentModalUserId = null;
            currentModalUserData = null;
        }

        function handleBackdropClick(event) {
            if (event.target.id === 'userDetailModal') {
                closeUserDetailModal();
            }
        }

        /**
         * YÊU CẦU 2: Chức năng "Khóa tài khoản" (Ban User)
         * Sử dụng SweetAlert2 hỏi xác nhận và nhập lý do khóa.
         */
        async function confirmToggleLock(userId, userName, isCurrentlyLocked) {
            if (isCurrentlyLocked) {
                // Trường hợp Mở Khóa
                const result = await Swal.fire({
                    title: 'Mở khóa tài khoản?',
                    html: `Bạn có chắc chắn muốn mở khóa cho tài khoản <strong>"${userName}"</strong>?<br>Người dùng sẽ có thể đăng nhập và sử dụng dịch vụ trở lại.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-lock-open"></i> Đồng ý mở khóa',
                    cancelButtonText: 'Hủy thao tác',
                    reverseButtons: true
                });

                if (result.isConfirmed) {
                    executeToggleLock(userId, false, null);
                }
            } else {
                // Trường hợp Khóa Tài Khoản (Theo yêu cầu: popup cảnh báo người dùng sẽ không thể đăng nhập)
                const { value: formValues, isConfirmed } = await Swal.fire({
                    title: 'Khóa tài khoản này?',
                    html: `
                        <div style="text-align: left; font-size: 0.9rem; color: #475569; margin-bottom: 12px;">
                            Bạn có chắc chắn muốn khóa tài khoản <strong>"${userName}"</strong>?<br>
                            <span style="color: #dc2626; font-weight: 700;">Người dùng sẽ không thể đăng nhập vào hệ thống.</span>
                        </div>
                        <div style="text-align: left;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                Lý do khóa tài khoản:
                            </label>
                            <input id="swal-input-reason" class="swal2-input" style="margin: 0; width: 100%; font-size: 0.875rem;" placeholder="Nhập lý do (ví dụ: Vi phạm quy định, lừa đảo, spam...)">
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-lock"></i> Xác nhận khóa tài khoản',
                    cancelButtonText: 'Hủy bỏ',
                    reverseButtons: true,
                    preConfirm: () => {
                        const reason = document.getElementById('swal-input-reason').value.trim();
                        return reason || 'Tài khoản vi phạm quy định tiêu chuẩn cộng đồng.';
                    }
                });

                if (isConfirmed) {
                    executeToggleLock(userId, true, formValues);
                }
            }
        }

        async function executeToggleLock(userId, shouldLock, reason) {
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ trong giây lát...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(`/api/admin/personal-accounts/${userId}/toggle-lock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ reason: reason })
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Xử lý thất bại.');
                }

                // Cập nhật giao diện trên hàng của bảng
                updateUserRowLockState(userId, data.is_locked, reason);

                // Cập nhật số tài khoản vi phạm trên thẻ thống kê
                if (data.total_violations !== undefined) {
                    const statViolations = document.getElementById('stat-locked-count');
                    if (statViolations) {
                        statViolations.innerText = data.total_violations;
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Nếu đang mở Modal của user này, cập nhật lại modal
                if (currentModalUserId === userId) {
                    openUserDetailModal(userId);
                }

            } catch (error) {
                console.error('Lỗi khi khóa/mở khóa:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Thao tác thất bại',
                    text: error.message || 'Đã xảy ra lỗi kết nối.',
                    confirmButtonColor: '#0284c7'
                });
            }
        }

        function updateUserRowLockState(userId, isLocked, reason) {
            const row = document.getElementById(`user-row-${userId}`);
            if (!row) return;

            row.setAttribute('data-locked', isLocked ? '1' : '0');

            // Cập nhật Pill trạng thái
            const pillContainer = document.getElementById(`status-pill-container-${userId}`);
            if (pillContainer) {
                if (isLocked) {
                    pillContainer.innerHTML = `
                        <span class="status-pill status-pill-locked" title="${reason || 'Vi phạm tiêu chuẩn cộng đồng'}">
                            <i class="fa-solid fa-lock"></i> Đã khóa / Vi phạm
                        </span>
                    `;
                } else {
                    pillContainer.innerHTML = `
                        <span class="status-pill status-pill-active">
                            <i class="fa-solid fa-circle-check"></i> Hoạt động
                        </span>
                    `;
                }
            }

            // Cập nhật nút khóa/mở khóa
            const btnLock = document.getElementById(`btn-lock-${userId}`);
            const iconLock = document.getElementById(`lock-icon-${userId}`);
            if (btnLock && iconLock) {
                if (isLocked) {
                    btnLock.className = 'btn-act btn-unlock';
                    btnLock.title = 'Mở khóa tài khoản';
                    iconLock.className = 'fa-solid fa-lock-open';
                } else {
                    btnLock.className = 'btn-act btn-lock';
                    btnLock.title = 'Khóa tài khoản vi phạm';
                    iconLock.className = 'fa-solid fa-lock';
                }
            }
        }

        /**
         * YÊU CẦU 3: Chức năng "Xóa tài khoản"
         * SweetAlert2 cảnh báo nguy hiểm chữ đỏ.
         * Khuyến nghị & áp dụng SoftDelete để không hỏng dữ liệu tài chính/hợp đồng.
         */
        async function confirmDeleteUser(userId, userName) {
            const result = await Swal.fire({
                title: '<span style="color: #dc2626; font-size: 1.35rem; font-weight: 800;">CẢNH BÁO NGUY HIỂM</span>',
                html: `
                    <div style="text-align: left; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                        <p style="color: #dc2626; font-weight: 800; font-size: 1rem; margin-bottom: 8px;">
                            CẢNH BÁO: Hành động này sẽ xóa tài khoản "${userName}" và các dữ liệu liên quan. Bạn có chắc chắn không?
                        </p>
                        <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; margin-top: 10px; font-size: 0.8rem; color: #991b1b;">
                            <i class="fa-solid fa-shield-halved"></i> <strong>Cơ chế bảo vệ dữ liệu:</strong> Hệ thống áp dụng <strong>SoftDelete</strong> nhằm bảo toàn toàn vẹn các chứng từ Hợp đồng & Hóa đơn tài chính phục vụ quyết toán thuế và kế toán.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Xác nhận xóa tài khoản',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            });

            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Đang xóa tài khoản...',
                text: 'Hệ thống đang tiến hành đóng băng và xóa an toàn...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(`/api/admin/personal-accounts/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Không thể xóa tài khoản.');
                }

                // Xóa hàng trên bảng với hiệu ứng làm mờ
                const row = document.getElementById(`user-row-${userId}`);
                if (row) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        handleSearchFilter();
                    }, 400);
                }

                // Nếu đang mở modal thì đóng modal
                closeUserDetailModal();

                Swal.fire({
                    icon: 'success',
                    title: 'Đã xóa tài khoản!',
                    html: `Tài khoản <strong>"${userName}"</strong> đã được xóa thành công khỏi danh sách người dùng.`,
                    confirmButtonColor: '#0284c7'
                });

            } catch (error) {
                console.error('Lỗi khi xóa:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi xóa tài khoản',
                    text: error.message || 'Không thể kết nối đến máy chủ.',
                    confirmButtonColor: '#0284c7'
                });
            }
        }

        /**
         * YÊU CẦU 4: Chức năng "Gỡ bài đăng"
         * Tìm toàn bộ bài đăng thuộc user_id này và chuyển sang rejected (vi phạm).
         * Không xóa cứng bài đăng để làm bằng chứng.
         */
        function triggerModalTakedownPosts() {
            if (!currentModalUserData || !currentModalUserData.user) return;
            const user = currentModalUserData.user;
            const postCount = currentModalUserData.posts ? currentModalUserData.posts.length : 0;
            confirmTakedownPosts(user.id, user.account_name, postCount);
        }

        async function confirmTakedownPosts(userId, userName, postCount) {
            const { value: reason, isConfirmed } = await Swal.fire({
                title: 'Gỡ toàn bộ bài đăng?',
                html: `
                    <div style="text-align: left; font-size: 0.9rem; line-height: 1.5; color: #475569;">
                        Admin sẽ thực hiện gỡ toàn bộ <strong>${postCount} bài đăng</strong> của tài khoản <strong>"${userName}"</strong>.<br>
                        <div style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 12px; margin-top: 10px; font-size: 0.8rem; color: #92400e;">
                            <i class="fa-solid fa-circle-info"></i> <strong>Chính sách đối soát:</strong> Toàn bộ bài đăng sẽ được chuyển sang trạng thái <strong>Vi phạm/Bị gỡ (rejected)</strong> và <span style="text-decoration: underline;">không bị xóa vĩnh viễn</span> khỏi cơ sở dữ liệu để làm bằng chứng.
                        </div>
                    </div>
                    <div style="text-align: left; margin-top: 12px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Lý do gỡ bài:
                        </label>
                        <input id="swal-input-takedown-reason" class="swal2-input" style="margin: 0; width: 100%; font-size: 0.875rem;" placeholder="Ví dụ: Người dùng đăng tin sai lệch, có dấu hiệu lừa đảo...">
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-ban"></i> Xác nhận gỡ toàn bộ bài',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                preConfirm: () => {
                    const r = document.getElementById('swal-input-takedown-reason').value.trim();
                    return r || 'Tài khoản vi phạm quy định đăng tin của RentHome.';
                }
            });

            if (!isConfirmed) return;

            Swal.fire({
                title: 'Đang gỡ bài đăng...',
                text: 'Hệ thống đang chuyển trạng thái các bài đăng...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(`/api/admin/personal-accounts/${userId}/takedown-posts`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ reason: reason })
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Không thể gỡ bài đăng.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Đã gỡ bài đăng!',
                    text: data.message,
                    confirmButtonColor: '#0284c7'
                });

                // Cập nhật lại Modal nếu đang mở
                if (currentModalUserId === userId) {
                    openUserDetailModal(userId);
                }

            } catch (error) {
                console.error('Lỗi khi gỡ bài:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Thất bại',
                    text: error.message || 'Đã xảy ra lỗi khi gỡ bài.',
                    confirmButtonColor: '#0284c7'
                });
            }
        }
    </script>
</body>

</html>
