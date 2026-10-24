<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Duyệt Chuyển Khoản Ngân Hàng - RentHome Admin</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --bg-main: #f4f6fa;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px -3px rgba(0, 0, 0, 0.08);

            --brand-primary: #0284c7;
            --brand-hover: #0369a1;
            --brand-bg: rgba(2, 132, 199, 0.08);

            --accent-emerald: #059669;
            --accent-emerald-dark: #047857;
            --accent-emerald-bg: rgba(5, 150, 105, 0.1);

            --accent-amber: #d97706;
            --accent-amber-bg: rgba(217, 119, 6, 0.1);

            --accent-rose: #e11d48;
            --accent-rose-bg: rgba(225, 29, 72, 0.1);

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;

            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .admin-main {
            padding: 28px 36px 60px 36px;
            max-width: 1600px;
        }

        /* Top Bar Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .header-title-group h1 {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title-group p {
            color: var(--text-secondary);
            font-size: 0.88rem;
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Thẻ thống kê (Stats Grid) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent, var(--brand-primary));
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-info .stat-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-info .stat-number {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            background: var(--stat-icon-bg, var(--brand-bg));
            color: var(--stat-icon-color, var(--brand-primary));
        }

        /* Thanh lọc & Tìm kiếm */
        .filter-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            box-shadow: var(--shadow-subtle);
        }

        .tabs-group {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
        }

        .tab-btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn.active {
            background: #ffffff;
            color: var(--brand-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .tab-badge {
            font-size: 0.7rem;
            padding: 1px 7px;
            border-radius: 12px;
            background: #fde68a;
            color: #b45309;
            font-weight: 800;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 400px;
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            font-size: 0.85rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--brand-primary);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        /* Bảng dữ liệu */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .table-header-meta {
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-title .badge-count {
            background: #e0f2fe;
            color: #0284c7;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 2px 9px;
            border-radius: 12px;
        }

        .table-responsive {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        table.finance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            font-size: 0.85rem;
        }

        table.finance-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        table.finance-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        table.finance-table tbody tr:hover {
            background-color: #f8fafc;
        }

        table.finance-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        table.finance-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Mã hóa đơn & Kỳ phí */
        .invoice-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .invoice-code-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .invoice-code-badge {
            font-family: 'SF Mono', 'Fira Code', 'Roboto Mono', Menlo, monospace;
            font-weight: 800;
            font-size: 0.82rem;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px 9px;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }

        .invoice-code-badge i {
            color: #0284c7;
            font-size: 0.78rem;
        }

        .btn-copy-code {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 3px 5px;
            border-radius: 4px;
            font-size: 0.78rem;
            transition: all 0.15s ease;
        }

        .btn-copy-code:hover {
            color: #0284c7;
            background: #e0f2fe;
        }

        .billing-period-tag {
            font-size: 0.73rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            line-height: 1.3;
        }

        .billing-period-tag i {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        /* Phòng & Hợp đồng */
        .room-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .room-title-badge {
            font-weight: 800;
            color: #0f172a;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .room-title-badge i {
            color: #3b82f6;
            font-size: 0.8rem;
        }

        .contract-sub-badge {
            font-size: 0.74rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .contract-sub-badge i {
            color: #94a3b8;
            font-size: 0.72rem;
        }

        /* Khách thuê */
        .tenant-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tenant-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            font-weight: 800;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #bae6fd;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.12);
            text-transform: uppercase;
        }

        .tenant-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .tenant-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.86rem;
            text-transform: capitalize;
            line-height: 1.25;
        }

        .tenant-phone {
            font-size: 0.75rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .tenant-phone:hover {
            color: #0284c7;
        }

        /* Số tiền & Thời gian báo */
        .amount-cell {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .amount-highlight {
            font-weight: 800;
            font-size: 1.02rem;
            color: #059669;
            letter-spacing: -0.01em;
            white-space: nowrap;
            line-height: 1.2;
        }

        .amount-currency {
            font-size: 0.82rem;
            font-weight: 700;
            margin-left: 2px;
        }

        .time-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.74rem;
            color: #64748b;
            white-space: nowrap;
        }

        .time-badge i {
            color: #0284c7;
            font-size: 0.72rem;
        }

        /* Biên lai (Bill Thumbnail) */
        .bill-preview-box {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bill-thumb-card {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            overflow: hidden;
            border: 1.5px solid #a7f3d0;
            background: #f0fdf4;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.15);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .bill-thumb-card:hover {
            transform: scale(1.1);
            border-color: #059669;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.25);
        }

        .bill-thumb-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.25s ease;
        }

        .bill-thumb-card:hover img {
            transform: scale(1.1);
        }

        .bill-thumb-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(1px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.85rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .bill-thumb-card:hover .bill-thumb-overlay {
            opacity: 1;
        }

        .no-proof-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.73rem;
            color: #94a3b8;
            background: #f8fafc;
            padding: 5px 9px;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
            white-space: nowrap;
        }

        /* Trạng thái */
        .badge-status-pending {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.78rem;
            border: 1px solid #fde68a;
            white-space: nowrap;
        }

        .status-dot-pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d97706;
            box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.6);
            animation: dotPulse 1.8s infinite;
        }

        @keyframes dotPulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.6);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(217, 119, 6, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0);
            }
        }

        .badge-status-approved {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.78rem;
            border: 1px solid #a7f3d0;
            white-space: nowrap;
        }

        /* Hành động */
        .btn-approve-payment {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .btn-approve-payment:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 5px 14px rgba(16, 185, 129, 0.38);
        }

        .btn-approve-payment:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        .action-completed-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid #a7f3d0;
            white-space: nowrap;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #e0f2fe;
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 16px auto;
        }

        .empty-state h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 0.85rem;
            max-width: 480px;
            margin: 0 auto 20px auto;
        }

        /* Modal Lightbox Xem Biên Lai */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(6px);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: #ffffff;
            border-radius: var(--radius-lg);
            max-width: 760px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1);
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: var(--transition);
        }

        .modal-close-btn:hover {
            color: var(--accent-rose);
            background: #fee2e2;
        }

        .modal-body {
            padding: 24px;
            text-align: center;
        }

        .proof-img-wrapper {
            position: relative;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px dashed var(--border-color);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .proof-img-preview {
            max-width: 100%;
            max-height: 520px;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
        }

        .proof-img-loading,
        .proof-img-fallback {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            width: 100%;
        }

        .modal-invoice-meta {
            margin-top: 18px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            text-align: left;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            font-size: 0.85rem;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        /* Toast thông báo */
        .toast-notify {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 3000;
            padding: 14px 22px;
            border-radius: var(--radius-md);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.88rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast-notify.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-notify.success { background: #059669; }
        .toast-notify.error { background: #dc2626; }
        .toast-notify.info { background: #0284c7; }
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    @include('partials.header-admin')

    <!-- Main Content -->
    <main class="admin-main">
        
        <!-- Header Page -->
        <header class="page-header">
            <div class="header-title-group">
                <h1>
                    <i class="fa-solid fa-building-columns" style="color: #0284c7;"></i>
                    <span>Duyệt Chuyển Khoản Ngân Hàng</span>
                </h1>
                <p>Xử lý Luồng 1: Kiểm tra và duyệt thanh toán cho các hóa đơn khách hàng chuyển khoản VietQR.</p>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.finance.staff_debts') }}" class="btn-main-site">
                    <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
                    <span>Sang Công Nợ Nhân Viên</span>
                </a>
            </div>
        </header>

        <!-- 4 Thẻ Thống Kê Nhanh -->
        <div class="stats-grid">
            <div class="stat-card" style="--card-accent: #f59e0b;">
                <div class="stat-info">
                    <div class="stat-label">Hóa đơn chờ duyệt</div>
                    <div class="stat-number" style="color: #d97706;" id="stat-pending-count">{{ number_format($pendingCount) }}</div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #fef3c7; --stat-icon-color: #d97706;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #0284c7;">
                <div class="stat-info">
                    <div class="stat-label">Tổng tiền chờ duyệt</div>
                    <div class="stat-number" style="color: #0284c7;">{{ number_format($pendingAmount) }} <span style="font-size: 0.9rem; font-weight: 600;">đ</span></div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #e0f2fe; --stat-icon-color: #0284c7;">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #059669;">
                <div class="stat-info">
                    <div class="stat-label">Đã duyệt hôm nay</div>
                    <div class="stat-number" style="color: #059669;">{{ number_format($approvedTodayCount) }}</div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #d1fae5; --stat-icon-color: #059669;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #6366f1;">
                <div class="stat-info">
                    <div class="stat-label">Tiền đã thu hôm nay</div>
                    <div class="stat-number" style="color: #4f46e5;">{{ number_format($approvedTodayAmount) }} <span style="font-size: 0.9rem; font-weight: 600;">đ</span></div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #e0e7ff; --stat-icon-color: #4f46e5;">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <!-- Tabs lọc trạng thái -->
            <div class="tabs-group">
                <a href="{{ route('admin.finance.bank_transfers', ['tab' => 'pending', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Chờ duyệt</span>
                    @if($pendingCount > 0)
                        <span class="tab-badge">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.finance.bank_transfers', ['tab' => 'approved', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'approved' ? 'active' : '' }}">
                    <i class="fa-solid fa-check"></i>
                    <span>Đã duyệt</span>
                </a>
                <a href="{{ route('admin.finance.bank_transfers', ['tab' => 'all', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
                    <i class="fa-solid fa-list-ul"></i>
                    <span>Tất cả</span>
                </a>
            </div>

            <!-- Ô tìm kiếm -->
            <form action="{{ route('admin.finance.bank_transfers') }}" method="GET" class="search-form">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" value="{{ $search }}" class="search-input" placeholder="Tìm theo mã HĐ, tên khách, số tiền...">
                </div>
            </form>
        </div>

        <!-- Bảng Danh Sách Hóa Đơn Chuyển Khoản -->
        <div class="table-card">
            <div class="table-header-meta">
                <div class="table-title">
                    <i class="fa-solid fa-receipt text-sky-600"></i>
                    <span>Danh Sách Hóa Đơn Báo Chuyển Khoản</span>
                    <span class="badge-count">{{ $invoices->count() }} giao dịch</span>
                </div>
                <div style="font-size: 0.78rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                    <span>Kiểm tra sao kê VietQR trước khi bấm duyệt</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">STT</th>
                            <th style="min-width: 170px;">Hóa Đơn & Kỳ Phí</th>
                            <th style="min-width: 150px;">Phòng / Hợp Đồng</th>
                            <th style="min-width: 180px;">Khách Thuê</th>
                            <th style="min-width: 160px;">Số Tiền & Thời Gian</th>
                            <th style="width: 105px; text-align: center;">Biên Lai (Bill)</th>
                            <th style="width: 120px; text-align: center;">Trạng Thái</th>
                            <th style="width: 130px; text-align: right;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody id="bank-transfers-table-body">
                        @forelse($invoices as $index => $inv)
                            @php
                                $isPending = in_array(strtolower($inv->status ?? ''), ['pending_approval', 'pending']) || in_array(strtolower($inv->payment_status ?? ''), ['pending_approval', 'pending']);
                                
                                // Xử lý thời gian báo chuyển
                                $rawTime = $inv->transfer_reported_at ?: ($inv->created_at ?? null);
                                $timeOnly = '';
                                $dateOnly = '';
                                $reportedTime = 'Mới đây';
                                if ($rawTime) {
                                    try {
                                        $dt = \Carbon\Carbon::parse($rawTime);
                                        $timeOnly = $dt->format('H:i');
                                        $dateOnly = $dt->format('d/m/Y');
                                        $reportedTime = $dt->format('H:i • d/m/Y');
                                    } catch (\Exception $e) {
                                        $reportedTime = (string)$rawTime;
                                    }
                                }

                                $roomTitle = $inv->room_display_name ?: ($inv->room_id ?? 'RentHome');
                                
                                // Chuẩn hóa tên khách và tạo initials avatar
                                $rawTenant = $inv->tenant_name ?: ($inv->tenant ? ($inv->tenant->name ?? $inv->tenant->username) : 'Khách thuê');
                                $tenantName = mb_convert_case(trim($rawTenant), MB_CASE_TITLE, 'UTF-8');
                                $nameParts = preg_split('/\s+/', trim($tenantName));
                                if (count($nameParts) >= 2) {
                                    $initials = mb_substr($nameParts[0], 0, 1, 'UTF-8') . mb_substr(end($nameParts), 0, 1, 'UTF-8');
                                } elseif (count($nameParts) === 1) {
                                    $initials = mb_substr($nameParts[0], 0, 2, 'UTF-8');
                                } else {
                                    $initials = 'KH';
                                }
                                $initials = mb_strtoupper($initials, 'UTF-8');

                                $invId = (string)($inv->_id ?? $inv->id);

                                // Xử lý rút gọn kỳ thanh toán
                                $periodStr = $inv->billing_period ?: ($inv->billing_month ? 'Tháng ' . $inv->billing_month : 'Định kỳ');
                                $shortPeriod = $periodStr;
                                if (preg_match('/(\d{1,2}\/\d{1,2}\s*-\s*\d{1,2}\/\d{1,2}\/\d{4})/', $periodStr, $matches)) {
                                    $shortPeriod = $matches[1];
                                }
                            @endphp
                            <tr id="row-invoice-{{ $invId }}">
                                <td style="text-align: center; color: #94a3b8; font-weight: 700; font-size: 0.78rem;">
                                    {{ sprintf('%02d', $index + 1) }}
                                </td>

                                <!-- Mã Hóa Đơn & Kỳ Phí -->
                                <td>
                                    <div class="invoice-cell">
                                        <div class="invoice-code-group">
                                            <div class="invoice-code-badge">
                                                <i class="fa-solid fa-receipt"></i>
                                                <span>{{ $inv->invoice_code }}</span>
                                            </div>
                                            <button type="button" class="btn-copy-code" title="Sao chép mã hóa đơn" onclick="copyInvoiceCode('{{ $inv->invoice_code }}', this)">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="billing-period-tag" title="{{ $periodStr }}">
                                            <i class="fa-regular fa-calendar-days"></i>
                                            <span>Kỳ: {{ $shortPeriod }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tên Phòng / Bất Động Sản -->
                                <td>
                                    <div class="room-cell">
                                        <span class="room-title-badge">
                                            <i class="fa-solid fa-door-open"></i>
                                            <span>{{ $roomTitle }}</span>
                                        </span>
                                        @if($inv->contract && !empty($inv->contract->contract_code))
                                            <span class="contract-sub-badge" title="Mã hợp đồng">
                                                <i class="fa-solid fa-file-contract"></i>
                                                <span>{{ $inv->contract->contract_code }}</span>
                                            </span>
                                        @else
                                            <span class="contract-sub-badge">
                                                <i class="fa-solid fa-building"></i>
                                                <span>{{ $inv->room_id ?: 'RentHome' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Khách Thuê -->
                                <td>
                                    <div class="tenant-cell">
                                        <div class="tenant-avatar">{{ $initials }}</div>
                                        <div class="tenant-info">
                                            <span class="tenant-name">{{ $tenantName }}</span>
                                            @if($inv->contract && !empty($inv->contract->tenant_phone))
                                                <a href="tel:{{ $inv->contract->tenant_phone }}" class="tenant-phone" title="Gọi cho khách">
                                                    <i class="fa-solid fa-phone"></i>
                                                    <span>{{ $inv->contract->tenant_phone }}</span>
                                                </a>
                                            @else
                                                <span class="tenant-phone" style="color: #94a3b8;">
                                                    <i class="fa-solid fa-phone"></i> Chưa có SĐT
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Số Tiền & Thời Gian -->
                                <td>
                                    <div class="amount-cell">
                                        <div class="amount-highlight">
                                            <span>{{ number_format($inv->total_amount) }}</span><span class="amount-currency">₫</span>
                                        </div>
                                        <div class="time-badge">
                                            <i class="fa-regular fa-clock"></i>
                                            <span>{{ $reportedTime }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Biên Lai (Bill Thumbnail) -->
                                <td style="text-align: center;">
                                    @php
                                        $displayProofUrl = $inv->proof_image_url ?? (!empty($inv->proof_image) ? (str_starts_with($inv->proof_image, 'http') ? $inv->proof_image : asset(ltrim($inv->proof_image, '/'))) : null);
                                    @endphp
                                    @if(!empty($displayProofUrl))
                                        <div class="bill-preview-box">
                                            <button type="button" 
                                                    class="bill-thumb-card" 
                                                    title="Bấm để xem ảnh biên lai phóng to"
                                                    onclick="openProofModal('{{ $displayProofUrl }}', '{{ $inv->invoice_code }}', '{{ number_format($inv->total_amount) }}', '{{ $tenantName }}', '{{ $reportedTime }}', '{{ $invId }}', {{ $isPending ? 'true' : 'false' }})">
                                                <img src="{{ $displayProofUrl }}" alt="Biên lai {{ $inv->invoice_code }}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=100&auto=format&fit=crop&q=80';">
                                                <div class="bill-thumb-overlay">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </button>
                                        </div>
                                    @else
                                        <div class="bill-preview-box">
                                            <span class="no-proof-badge" title="Khách chưa đính kèm ảnh chụp bill">
                                                <i class="fa-regular fa-image"></i> Chưa có bill
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Trạng Thái -->
                                <td style="text-align: center;" id="status-cell-{{ $invId }}">
                                    @if($isPending)
                                        <span class="badge-status-pending">
                                            <span class="status-dot-pulse"></span>
                                            <span>Chờ duyệt</span>
                                        </span>
                                    @else
                                        <span class="badge-status-approved">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Đã duyệt</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Hành Động: Duyệt tiền -->
                                <td style="text-align: right;" id="action-cell-{{ $invId }}">
                                    @if($isPending)
                                        <button type="button" 
                                                id="btn-approve-{{ $invId }}"
                                                class="btn-approve-payment" 
                                                title="Xác nhận đã nhận tiền vào tài khoản ngân hàng"
                                                onclick="approveBankTransfer('{{ $invId }}', '{{ $inv->invoice_code }}', '{{ number_format($inv->total_amount) }}')">
                                            <i class="fa-solid fa-check-double"></i>
                                            <span>Duyệt Tiền</span>
                                        </button>
                                    @else
                                        <span class="action-completed-pill">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Hoàn tất</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <h3>Không có chuyển khoản nào cần duyệt</h3>
                                        <p>
                                            @if($tab === 'pending')
                                                Hiện tại không có giao dịch chuyển khoản nào cần duyệt. Toàn bộ các giao dịch chuyển khoản đã được xử lý hoàn tất.
                                            @else
                                                Không tìm thấy hóa đơn chuyển khoản nào phù hợp với bộ lọc hiện tại.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Lightbox Xem Ảnh Biên Lai Chuyển Khoản -->
    <div class="modal-overlay" id="proofModal" onclick="closeProofModal(event)">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                    <span>Biên Lai Chuyển Khoản: <span id="modalInvoiceCode" style="color: var(--brand-primary);"></span></span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeProofModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div class="proof-img-wrapper" id="proofImgWrapper">
                    <div id="proofImgLoading" class="proof-img-loading">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: var(--brand-primary);"></i>
                        <p style="margin-top: 10px; color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">Đang tải ảnh biên lai...</p>
                    </div>
                    <img src="" alt="Ảnh biên lai" id="modalProofImg" class="proof-img-preview"
                         onload="handleProofImgLoad(this)"
                         onerror="handleProofImgError(this)" style="display: none;">
                    <div id="proofImgFallback" class="proof-img-fallback" style="display: none;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 10px;"></i>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Không thể tải ảnh biên lai</h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted); max-width: 360px; margin: 0 auto 12px auto; line-height: 1.5;">File ảnh có thể chưa được đồng bộ hoặc đã bị di chuyển. Bạn có thể thử mở trực tiếp liên kết.</p>
                        <a href="#" id="modalFallbackOpenBtn" target="_blank" class="btn-main-site" style="font-size: 0.78rem; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Mở thử trong tab mới
                        </a>
                    </div>
                </div>
                
                <div class="modal-invoice-meta">
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Khách hàng nộp:</span>
                        <strong id="modalTenantName" style="color: var(--text-primary);"></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Số tiền chuyển:</span>
                        <strong id="modalAmount" style="color: var(--accent-emerald-dark); font-size: 1rem;"></strong> đ
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Thời gian báo chuyển:</span>
                        <strong id="modalTime" style="color: var(--text-secondary);"></strong>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" id="btnOpenImgNewTab" target="_blank" class="btn-main-site" style="font-size: 0.8rem; padding: 8px 14px;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Mở ảnh gốc
                </a>

                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-main-site" style="font-size: 0.8rem; padding: 8px 14px;" onclick="closeProofModal()">
                        Đóng
                    </button>
                    <button type="button" id="modalBtnApprove" class="btn-approve-payment" style="padding: 8px 18px;">
                        <i class="fa-solid fa-check-double"></i>
                        <span>Duyệt Tiền Ngay</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotify" class="toast-notify">
        <i class="fa-solid fa-circle-check" id="toastIcon"></i>
        <span id="toastMessage">Thông báo</span>
    </div>

    <!-- SCRIPT XỬ LÝ DUYỆT CHUYỂN KHOẢN QUA AJAX -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function handleProofImgLoad(img) {
            const loading = document.getElementById('proofImgLoading');
            const fallback = document.getElementById('proofImgFallback');
            const openTabBtn = document.getElementById('btnOpenImgNewTab');
            if (loading) loading.style.display = 'none';
            if (fallback) fallback.style.display = 'none';
            img.style.display = 'block';
            if (openTabBtn) openTabBtn.href = img.src;
        }

        function handleProofImgError(img) {
            // Tự động hoán đổi giữa /storage/uploads/proofs/ và /uploads/proofs/ nếu 1 đường dẫn bị lỗi
            if (img.src.includes('/storage/uploads/proofs/') && !img.dataset.swapped) {
                img.dataset.swapped = 'true';
                img.src = img.src.replace('/storage/uploads/proofs/', '/uploads/proofs/');
                return;
            }
            if (img.src.includes('/uploads/proofs/') && !img.dataset.swapped) {
                img.dataset.swapped = 'true';
                img.src = img.src.replace('/uploads/proofs/', '/storage/uploads/proofs/');
                return;
            }

            img.style.display = 'none';
            const loading = document.getElementById('proofImgLoading');
            const fallback = document.getElementById('proofImgFallback');
            if (loading) loading.style.display = 'none';
            if (fallback) fallback.style.display = 'flex';
        }

        // Mở Modal xem ảnh biên lai
        function openProofModal(imgUrl, code, amount, tenant, time, invId, isPending) {
            const img = document.getElementById('modalProofImg');
            const loading = document.getElementById('proofImgLoading');
            const fallback = document.getElementById('proofImgFallback');
            const openTabBtn = document.getElementById('btnOpenImgNewTab');
            const fallbackOpenBtn = document.getElementById('modalFallbackOpenBtn');

            if (img) {
                img.removeAttribute('data-swapped');
                img.style.display = 'none';
                img.src = imgUrl;
            }
            if (loading) loading.style.display = 'flex';
            if (fallback) fallback.style.display = 'none';

            if (openTabBtn) openTabBtn.href = imgUrl;
            if (fallbackOpenBtn) fallbackOpenBtn.href = imgUrl;

            document.getElementById('modalInvoiceCode').innerText = code;
            document.getElementById('modalAmount').innerText = amount;
            document.getElementById('modalTenantName').innerText = tenant;
            document.getElementById('modalTime').innerText = time;

            const modalBtnApprove = document.getElementById('modalBtnApprove');
            if (isPending) {
                modalBtnApprove.style.display = 'inline-flex';
                modalBtnApprove.onclick = function() {
                    closeProofModal();
                    approveBankTransfer(invId, code, amount);
                };
            } else {
                modalBtnApprove.style.display = 'none';
            }

            document.getElementById('proofModal').classList.add('active');
        }

        function closeProofModal(e) {
            if (!e || e.target.id === 'proofModal' || e.target.classList.contains('modal-close-btn') || e.target.tagName === 'BUTTON') {
                document.getElementById('proofModal').classList.remove('active');
            }
        }

        // Sao chép mã hóa đơn nhanh
        function copyInvoiceCode(code, btn) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => {
                    showToast(`Đã sao chép mã ${code}`, 'success');
                }).catch(() => fallbackCopy(code));
            } else {
                fallbackCopy(code);
            }
        }

        function fallbackCopy(code) {
            const temp = document.createElement('textarea');
            temp.value = code;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            showToast(`Đã sao chép mã ${code}`, 'success');
        }

        // Action quan trọng: Duyệt chuyển khoản
        async function approveBankTransfer(invId, invoiceCode, amount) {
            if (!confirm(`Xác nhận Admin đã nhận được ${amount} đ chuyển khoản từ ngân hàng cho Hóa đơn #${invoiceCode}?\n\nHệ thống sẽ chuyển trạng thái hóa đơn sang "Đã thu tiền" (PAID) và tự động KÍCH HOẠT HỢP ĐỒNG.`)) {
                return;
            }

            const btn = document.getElementById(`btn-approve-${invId}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang duyệt...';
            }

            try {
                const response = await fetch(`/Admin/tai-chinh/hoa-don/${encodeURIComponent(invId)}/duyet`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message || 'Đã duyệt chuyển khoản thành công!', 'success');

                    // Cập nhật Cell trạng thái
                    const statusCell = document.getElementById(`status-cell-${invId}`);
                    if (statusCell) {
                        statusCell.innerHTML = '<span class="badge-status-approved"><i class="fa-solid fa-circle-check"></i> Đã duyệt</span>';
                    }

                    // Cập nhật Cell hành động
                    const actionCell = document.getElementById(`action-cell-${invId}`);
                    if (actionCell) {
                        actionCell.innerHTML = '<span class="action-completed-pill"><i class="fa-solid fa-circle-check"></i> Hoàn tất</span>';
                    }

                    // Giảm số lượng badge chờ duyệt
                    const statCount = document.getElementById('stat-pending-count');
                    if (statCount) {
                        let cur = parseInt(statCount.innerText.replace(/,/g, '')) || 0;
                        if (cur > 0) statCount.innerText = (cur - 1).toLocaleString('vi-VN');
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra khi duyệt hóa đơn!', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-check-double"></i> <span>Duyệt Tiền</span>';
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ! Vui lòng thử lại.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check-double"></i> <span>Duyệt Tiền</span>';
                }
            }
        }

        // Toast Helper
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toastNotify');
            const icon = document.getElementById('toastIcon');
            const message = document.getElementById('toastMessage');

            toast.className = `toast-notify show ${type}`;
            message.innerText = msg;

            if (type === 'success') icon.className = 'fa-solid fa-circle-check';
            else if (type === 'error') icon.className = 'fa-solid fa-triangle-exclamation';
            else icon.className = 'fa-solid fa-circle-info';

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4500);
        }

        // Tự động kiểm tra Flash message từ Session
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @elseif(session('error'))
            showToast("{{ session('error') }}", 'error');
        @elseif(session('info'))
            showToast("{{ session('info') }}", 'info');
        @endif
    </script>
</body>
</html>
