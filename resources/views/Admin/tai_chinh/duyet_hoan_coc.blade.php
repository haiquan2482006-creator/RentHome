<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Duyệt Hoàn Cọc Thanh Lý - RentHome Admin</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
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

            --accent-rose: #dc2626;
            --accent-rose-bg: rgba(220, 38, 38, 0.1);

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;

            --sidebar-width: 260px;
            --sidebar-collapsed-width: 78px;

            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
            display: flex;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Đồng bộ layout Admin co giãn chuẩn theo thanh Sidebar */
        .admin-main {
            margin-left: var(--sidebar-width);
            width: calc(100vw - var(--sidebar-width));
            max-width: calc(100vw - var(--sidebar-width));
            min-height: 100vh;
            flex: 1;
            min-width: 0;
            padding: 26px 32px 60px 32px;
            background-color: var(--bg-main);
            box-sizing: border-box;
            transition: var(--transition);
        }

        body.sidebar-collapsed .admin-main {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100vw - var(--sidebar-collapsed-width));
            max-width: calc(100vw - var(--sidebar-collapsed-width));
        }

        /* Top Bar Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }

        .header-title-group h1 {
            font-size: 1.6rem;
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

        .btn-header-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .btn-header-link:hover {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background: #f8fafc;
        }

        /* Banner Nghiệp vụ Kế toán */
        .guideline-alert {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1px solid #a7f3d0;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            position: relative;
        }

        .guideline-alert i.icon-info {
            color: #059669;
            font-size: 1.25rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .guideline-alert-content {
            font-size: 0.84rem;
            color: #065f46;
            line-height: 1.55;
            padding-right: 24px;
        }

        .guideline-alert-content strong {
            color: #047857;
        }

        .guideline-alert-content code {
            background: rgba(5, 150, 105, 0.12);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-family: var(--font-mono);
            font-size: 0.8rem;
        }

        .btn-dismiss-alert {
            position: absolute;
            top: 10px;
            right: 12px;
            background: transparent;
            border: none;
            color: #059669;
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .btn-dismiss-alert:hover {
            opacity: 1;
        }

        /* Thẻ thống kê (KPI Stats Grid) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
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
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-info .stat-label {
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-info .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stat-info .stat-sub {
            font-size: 0.76rem;
            color: var(--text-secondary);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            background: var(--stat-icon-bg, var(--brand-bg));
            color: var(--stat-icon-color, var(--brand-primary));
        }

        /* Thanh lọc Tabs & Search */
        .filter-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
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
            padding: 2px 7px;
            border-radius: 12px;
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 800;
        }

        .tab-badge.badge-green {
            background: #d1fae5;
            color: #047857;
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
            padding: 8px 14px 8px 36px;
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

        /* Bảng Dữ Liệu Container */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-subtle);
            overflow-x: auto;
            width: 100%;
            -webkit-overflow-scrolling: touch;
        }

        .custom-table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.88rem;
        }

        .custom-table thead {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--border-color);
        }

        .custom-table th {
            padding: 15px 18px;
            font-size: 0.74rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background 0.15s ease;
        }

        .custom-table tbody tr:hover {
            background: #fbfcfe;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }

        .custom-table td {
            padding: 16px 18px;
            vertical-align: middle;
        }

        /* Cột 1: Mã HĐ & Phòng */
        .contract-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .contract-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.82rem;
            color: #0369a1;
            background: #e0f2fe;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #bae6fd;
            white-space: nowrap;
            width: fit-content;
        }

        .room-title-tag {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .room-building-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Cột 2: Tên Khách thuê */
        .tenant-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tenant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
        }

        .tenant-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .tenant-name {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 0.92rem;
            text-transform: capitalize;
        }

        .tenant-phone {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.15s ease;
        }

        .tenant-phone:hover {
            color: var(--brand-primary);
        }

        /* Cột 3: Số tiền cần hoàn (VNĐ) */
        .refund-amount-cell {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .refund-amount-val {
            font-weight: 800;
            font-size: 1.15rem;
            color: #059669;
            letter-spacing: -0.01em;
            white-space: nowrap;
            line-height: 1.2;
        }

        .refund-breakdown-sub {
            font-size: 0.74rem;
            color: #64748b;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Cột 4: Thông tin nhận tiền của khách (Rất quan trọng) */
        .bank-info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 9px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 240px;
            max-width: 300px;
            transition: all 0.2s ease;
        }

        .bank-info-card:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .bank-name-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 0.825rem;
            color: #0284c7;
        }

        .bank-name-row i {
            font-size: 0.85rem;
        }

        .bank-account-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 3px 8px;
        }

        .bank-account-num {
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.95rem;
            color: #0f172a;
            letter-spacing: 0.5px;
            user-select: all;
        }

        .btn-copy-acc {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 4px;
            padding: 2px 7px;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: var(--transition);
        }

        .btn-copy-acc:hover {
            background: #1d4ed8;
            color: #ffffff;
        }

        .bank-holder-row {
            font-size: 0.78rem;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .bank-source-tag {
            font-size: 0.68rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 1px;
        }

        /* Cột 5: Trạng thái */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.78rem;
            white-space: nowrap;
        }

        .badge-pending-refund {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-terminated-done {
            background: #d1fae5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        /* Cột 6: Nút hành động 'Xác nhận đã chuyển khoản' */
        .action-cell {
            text-align: center;
            white-space: nowrap;
        }

        .btn-confirm-transfer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 0.825rem;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .btn-confirm-transfer:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.38);
        }

        .btn-confirm-transfer:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        .action-completed-box {
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
            align-items: center;
        }

        .action-completed-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid #a7f3d0;
            white-space: nowrap;
        }

        .action-ref-sub {
            font-family: var(--font-mono);
            font-size: 0.72rem;
            color: #64748b;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
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

        @media (max-width: 1200px) {
            .admin-main {
                padding: 20px 18px 60px 18px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    @include('partials.header-admin')

    <!-- Main Content Co Giãn Chuẩn -->
    <main class="admin-main">

        <!-- Header Page -->
        <header class="page-header">
            <div class="header-title-group">
                <h1>
                    <i class="fa-solid fa-money-bill-transfer" style="color: #059669;"></i>
                    <span>Duyệt Hoàn Cọc Thanh Lý (Kịch bản B)</span>
                </h1>
                <p>Khu vực Kế toán & Quản trị xử lý chuyển khoản hoàn cọc cho khách thuê và tự động giải phóng phòng</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.finance.refund_requests') }}" class="btn-header-link" title="Làm mới dữ liệu">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Làm mới</span>
                </a>
                <a href="{{ url('/qlvan_hanh/thanh_ly_cong_no') }}" class="btn-header-link" target="_blank" title="Xem Sổ Quyết Toán Nhân Sự">
                    <i class="fa-solid fa-scale-balanced" style="color: #0284c7;"></i>
                    <span>Sổ Thanh Lý Nhân Sự</span>
                </a>
            </div>
        </header>

        <!-- Banner Nghiệp Vụ Kế Toán -->
        <div class="guideline-alert">
            <i class="fa-solid fa-circle-info icon-info"></i>
            <div class="guideline-alert-content">
                <strong>Quy trình Enterprise - Kịch bản B (Cọc nhiều hơn nợ):</strong> Sau khi Nhân sự chốt đối soát công nợ thanh lý, hệ thống chuyển hợp đồng sang trạng thái <code>pending_refund</code> và tạo bản ghi chuyển lên Kế toán. Kế toán kiểm tra số tài khoản ngân hàng của khách, thực hiện chuyển tiền từ tài khoản công ty, sau đó bấm <strong>"Xác nhận đã chuyển khoản"</strong> để hệ thống tự động:
                <em>(1) Hoàn tất bản ghi hoàn cọc (completed); (2) Đóng hợp đồng (terminated); (3) Giải phóng phòng về sẵn sàng đón khách mới (available / active); (4) Gửi thông báo đến khách thuê.</em>
            </div>
            <button type="button" class="btn-dismiss-alert" onclick="this.parentElement.style.display='none'" title="Đóng thông báo">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Thẻ Thống Kê (KPI Stats Grid) -->
        <section class="stats-grid">
            <!-- Thẻ 1: Chờ hoàn tiền cọc -->
            <div class="stat-card" style="--card-accent: #d97706;">
                <div class="stat-info">
                    <div class="stat-label">Yêu Cầu Chờ Hoàn Cọc</div>
                    <div class="stat-number" style="color: #d97706;">
                        {{ number_format($totalPendingCount) }}
                    </div>
                    <div class="stat-sub">
                        <i class="fa-solid fa-clock" style="color: #d97706;"></i>
                        <span>Đang chờ Kế toán xuất quỹ</span>
                    </div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: rgba(217, 119, 6, 0.1); --stat-icon-color: #d97706;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>

            <!-- Thẻ 2: Tổng số tiền cần xuất quỹ -->
            <div class="stat-card" style="--card-accent: #dc2626;">
                <div class="stat-info">
                    <div class="stat-label">Tổng Tiền Cần Xuất Quỹ</div>
                    <div class="stat-number" style="color: #dc2626;">
                        {{ number_format($totalPendingAmount) }} <span style="font-size: 1rem; font-weight: 700;">đ</span>
                    </div>
                    <div class="stat-sub">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i>
                        <span>Khoản cọc dư cần chuyển trả khách</span>
                    </div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: rgba(220, 38, 38, 0.1); --stat-icon-color: #dc2626;">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>

            <!-- Thẻ 3: Đã hoàn cọc thành công -->
            <div class="stat-card" style="--card-accent: #059669;">
                <div class="stat-info">
                    <div class="stat-label">Đã Hoàn Cọc Thành Công</div>
                    <div class="stat-number" style="color: #059669;">
                        {{ number_format($totalCompletedCount) }}
                    </div>
                    <div class="stat-sub">
                        <i class="fa-solid fa-circle-check" style="color: #059669;"></i>
                        <span>Hợp đồng đã kết thúc thanh lý</span>
                    </div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: rgba(5, 150, 105, 0.1); --stat-icon-color: #059669;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <!-- Thẻ 4: Tổng tiền đã giải ngân hoàn cọc -->
            <div class="stat-card" style="--card-accent: #0284c7;">
                <div class="stat-info">
                    <div class="stat-label">Tổng Tiền Đã Giải Ngân</div>
                    <div class="stat-number" style="color: #0284c7;">
                        {{ number_format($totalCompletedAmount) }} <span style="font-size: 1rem; font-weight: 700;">đ</span>
                    </div>
                    <div class="stat-sub">
                        <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i>
                        <span>Đã thanh toán đúng hạn cho khách</span>
                    </div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: rgba(2, 132, 199, 0.1); --stat-icon-color: #0284c7;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </section>

        <!-- Thanh Lọc Tabs & Search -->
        <section class="filter-bar">
            <div class="tabs-group">
                <a href="{{ route('admin.finance.refund_requests', ['tab' => 'pending', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Chờ Hoàn Cọc</span>
                    @if($totalPendingCount > 0)
                        <span class="tab-badge">{{ $totalPendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.finance.refund_requests', ['tab' => 'completed', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'completed' ? 'active' : '' }}">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Đã Hoàn Tất</span>
                    @if($totalCompletedCount > 0)
                        <span class="tab-badge badge-green">{{ $totalCompletedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.finance.refund_requests', ['tab' => 'all', 'search' => $search]) }}" 
                   class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i>
                    <span>Tất Cả</span>
                </a>
            </div>

            <form action="{{ route('admin.finance.refund_requests') }}" method="GET" class="search-form">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Tìm Mã HĐ, Tên khách, SĐT, Số TK..." 
                           class="search-input">
                </div>
                @if(!empty($search))
                    <a href="{{ route('admin.finance.refund_requests', ['tab' => $tab]) }}" class="btn-header-link" style="padding: 8px 12px;" title="Xóa tìm kiếm">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </section>

        <!-- Bảng Dữ Liệu Yêu Cầu Hoàn Cọc -->
        <section class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 18%;">Mã HĐ & Phòng</th>
                        <th style="width: 17%;">Tên Khách Thuê</th>
                        <th style="width: 17%;">Số Tiền Cần Hoàn</th>
                        <th style="width: 25%;">Thông Tin Nhận Tiền Của Khách</th>
                        <th style="width: 10%;">Trạng Thái</th>
                        <th style="width: 13%; text-align: center;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $c)
                        @php
                            $cId = (string)($c->_id ?? $c->id);
                            $isPending = ($c->status === 'pending_refund');
                            $refundAmt = $c->calc_refund_amount ?? 0;
                            $depositSafe = $c->deposit_safe ?? ($c->deposit_amount ?? 0);
                            $liqDet = (array)($c->liquidation_details ?? []);
                            $totalDebt = (float)($liqDet['total_debt'] ?? 0);

                            // Chuẩn hóa tên ngân hàng hiển thị
                            $rawBank = trim($c->bank_name ?? '');
                            $bankUpper = strtoupper($rawBank);
                            $bankMap = [
                                'VTB' => 'VietinBank (VTB)',
                                'VIETINBANK' => 'VietinBank',
                                'VCB' => 'Vietcombank',
                                'VIETCOMBANK' => 'Vietcombank',
                                'TCB' => 'Techcombank',
                                'TECHCOMBANK' => 'Techcombank',
                                'MB' => 'MB Bank',
                                'MBB' => 'MB Bank',
                                'MBBANK' => 'MB Bank',
                                'ACB' => 'ACB',
                                'BIDV' => 'BIDV',
                                'TPB' => 'TPBank',
                                'TPBANK' => 'TPBank',
                                'VPB' => 'VPBank',
                                'VPBANK' => 'VPBank',
                                'STB' => 'Sacombank',
                                'SACOMBANK' => 'Sacombank',
                                'VBA' => 'Agribank',
                                'AGRIBANK' => 'Agribank',
                                'SHB' => 'SHB',
                                'HDB' => 'HDBank',
                                'MSB' => 'MSB',
                                'OCB' => 'OCB',
                                'VIB' => 'VIB',
                            ];
                            $displayBankName = $bankMap[$bankUpper] ?? ($rawBank ? ucwords($rawBank) : 'Chưa cập nhật');
                            $displayHolder = mb_strtoupper($c->bank_holder ?: ($c->tenant_name ?: 'CHỦ TÀI KHOẢN'), 'UTF-8');
                            $displayTenant = mb_convert_case($c->tenant_name ?? 'Khách thuê', MB_CASE_TITLE, 'UTF-8');
                        @endphp
                        <tr id="row-contract-{{ $cId }}">
                            <!-- 1. Mã HĐ & Phòng -->
                            <td>
                                <div class="contract-cell">
                                    <span class="contract-code-badge" title="Mã hợp đồng">
                                        <i class="fa-solid fa-file-contract"></i>
                                        <span>{{ $c->contract_code }}</span>
                                    </span>
                                    <div class="room-title-tag" title="{{ $c->display_room_name }}">
                                        <i class="fa-solid fa-door-open" style="color: #0284c7; font-size: 0.85rem;"></i>
                                        <span>{{ $c->display_room_name }}</span>
                                    </div>
                                    @if($c->post && !empty($c->post->address))
                                        <div class="room-building-sub" title="{{ $c->post->address }}">
                                            <i class="fa-solid fa-location-dot"></i> {{ \Illuminate\Support\Str::limit($c->post->address, 30) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Tên Khách thuê -->
                            <td>
                                <div class="tenant-cell">
                                    <div class="tenant-avatar">
                                        {{ mb_strtoupper(mb_substr($c->tenant_name ?? 'K', 0, 1, 'UTF-8'), 'UTF-8') }}
                                    </div>
                                    <div class="tenant-info">
                                        <div class="tenant-name">{{ $displayTenant }}</div>
                                        @if(!empty($c->tenant_phone))
                                            <a href="tel:{{ $c->tenant_phone }}" class="tenant-phone" title="Gọi cho khách">
                                                <i class="fa-solid fa-phone"></i>
                                                <span>{{ $c->tenant_phone }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 3. Số tiền cần hoàn (VNĐ) -->
                            <td>
                                <div class="refund-amount-cell">
                                    <div class="refund-amount-val">
                                        {{ number_format($refundAmt) }} <span style="font-size: 0.8rem; font-weight: 700;">VNĐ</span>
                                    </div>
                                    <div class="refund-breakdown-sub" title="Chi tiết cấn trừ">
                                        <span>Cọc: {{ number_format($depositSafe) }}đ</span>
                                        @if($totalDebt > 0)
                                            <span> - Nợ: {{ number_format($totalDebt) }}đ</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Thông tin nhận tiền của khách (Tên NH, STK, Tên chủ TK) -->
                            <td>
                                <div class="bank-info-card">
                                    <!-- Tên Ngân Hàng -->
                                    <div class="bank-name-row">
                                        <i class="fa-solid fa-building-columns"></i>
                                        <span>{{ $displayBankName }}</span>
                                    </div>

                                    <!-- Số Tài Khoản + Nút Copy nhanh -->
                                    <div class="bank-account-row">
                                        <span class="bank-account-num" id="acc-text-{{ $cId }}">{{ $c->bank_account ?: 'Chưa có STK' }}</span>
                                        @if(!empty($c->bank_account) && $c->bank_account !== 'Chưa cập nhật')
                                            <button type="button" 
                                                    class="btn-copy-acc" 
                                                    onclick="copyBankAccount('{{ $c->bank_account }}', this)" 
                                                    title="Sao chép số tài khoản">
                                                <i class="fa-regular fa-copy"></i>
                                                <span>Copy</span>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Tên Chủ Tài Khoản -->
                                    <div class="bank-holder-row">
                                        <i class="fa-solid fa-user-check"></i>
                                        <span>{{ $displayHolder }}</span>
                                    </div>

                                    <!-- Nguồn dữ liệu -->
                                    <div class="bank-source-tag">
                                        <i class="fa-solid fa-shield-check" style="color: #059669;"></i>
                                        <span>Dữ liệu từ Form trả phòng của khách</span>
                                    </div>
                                </div>
                            </td>

                            <!-- 5. Trạng thái -->
                            <td>
                                @if($isPending)
                                    <span class="status-badge badge-pending-refund" title="Hợp đồng đang chờ Kế toán chuyển khoản hoàn trả tiền cọc">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Chờ hoàn cọc
                                    </span>
                                @else
                                    <span class="status-badge badge-terminated-done" title="Hợp đồng đã hoàn tất thanh lý">
                                        <i class="fa-solid fa-circle-check"></i> Đã hoàn cọc
                                    </span>
                                @endif
                            </td>

                            <!-- 6. Cột Hành Động: Nút xanh 'Xác nhận đã chuyển khoản' -->
                            <td class="action-cell">
                                @if($isPending)
                                    <button type="button" 
                                            class="btn-confirm-transfer" 
                                            onclick="openConfirmRefundModal('{{ $cId }}', '{{ $c->contract_code }}', '{{ addslashes($displayTenant) }}', {{ $refundAmt }}, '{{ addslashes($displayBankName) }}', '{{ addslashes($c->bank_account) }}', '{{ addslashes($displayHolder) }}', '{{ addslashes($c->display_room_name) }}')"
                                            title="Kế toán xác nhận đã chuyển khoản hoàn cọc cho khách">
                                        <i class="fa-solid fa-check"></i>
                                        <span>Xác nhận đã chuyển khoản</span>
                                    </button>
                                @else
                                    <div class="action-completed-box">
                                        <span class="action-completed-pill">
                                            <i class="fa-solid fa-check"></i> Đã xuất quỹ
                                        </span>
                                        @if(!empty($c->refund_payment_reference) || (!empty($c->refund_request) && !empty($c->refund_request->payment_reference)))
                                            <span class="action-ref-sub" title="Mã giao dịch ngân hàng">
                                                Mã: {{ $c->refund_payment_reference ?: $c->refund_request->payment_reference }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <h3>Không có yêu cầu hoàn cọc nào!</h3>
                                    <p>
                                        @if($tab === 'pending')
                                            Hiện không có hợp đồng nào đang chờ xuất quỹ hoàn cọc. Tất cả các yêu cầu trả phòng đã được xử lý xong.
                                        @else
                                            Không tìm thấy dữ liệu hợp đồng hoàn cọc phù hợp với bộ lọc hiện tại.
                                        @endif
                                    </p>
                                    @if(!empty($search))
                                        <a href="{{ route('admin.finance.refund_requests', ['tab' => $tab]) }}" class="btn-header-link">
                                            <i class="fa-solid fa-arrows-rotate"></i> Xem tất cả yêu cầu
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

    </main>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Hàm sao chép số tài khoản nhanh
        function copyBankAccount(accountNum, btn) {
            if (!accountNum) return;
            navigator.clipboard.writeText(accountNum).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã copy!';
                btn.style.background = '#059669';
                btn.style.color = '#ffffff';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.style.background = '';
                    btn.style.color = '';
                }, 1800);
            }).catch(err => {
                prompt('Số tài khoản của khách (hãy copy):', accountNum);
            });
        }

        // Định dạng tiền tệ VNĐ
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        /**
         * Bước 3: Modal xác nhận đã chuyển khoản hoàn cọc
         * Kế toán bấm "Xác nhận đã chuyển khoản" -> Gọi API Controller xử lý đồng thời 4 lệnh
         */
        async function openConfirmRefundModal(contractId, contractCode, tenantName, refundAmount, bankName, bankAccount, bankHolder, roomName) {
            const formattedMoney = formatMoney(refundAmount);

            const { isConfirmed } = await Swal.fire({
                title: '<span style="font-size: 1.25rem; font-weight: 800; color: #0f172a;">Xác Nhận Đã Chuyển Khoản Hoàn Cọc?</span>',
                html: `
                    <div style="text-align: left; font-size: 0.88rem; line-height: 1.6; color: #334155;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="color: #64748b;">Hợp đồng & Phòng:</span>
                                <strong>${contractCode} (${roomName})</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="color: #64748b;">Khách nhận tiền:</span>
                                <strong>${tenantName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="color: #64748b;">Ngân hàng:</span>
                                <strong style="color: #0284c7;">${bankName || 'Chưa rõ'}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="color: #64748b;">Số tài khoản:</span>
                                <strong style="font-family: monospace; font-size: 1rem; color: #0f172a; background: #e2e8f0; padding: 1px 6px; border-radius: 4px;">${bankAccount || 'Chưa có'}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="color: #64748b;">Chủ tài khoản:</span>
                                <strong style="color: #047857; text-transform: uppercase;">${bankHolder || tenantName}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px dashed #cbd5e1;">
                                <span style="color: #065f46; font-weight: 700;">Số tiền cần hoàn:</span>
                                <strong style="color: #059669; font-size: 1.15rem; font-weight: 800;">${formattedMoney} VNĐ</strong>
                            </div>
                        </div>

                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 12px; font-size: 0.78rem; color: #1e40af; line-height: 1.5;">
                            <i class="fa-solid fa-bolt" style="color: #2563eb;"></i> <strong>Hệ thống sẽ thực hiện đồng thời:</strong><br>
                            • Cập nhật bản ghi hoàn tiền thành <code>completed</code>.<br>
                            • Chuyển trạng thái hợp đồng thành <code>terminated</code>.<br>
                            • Tự động giải phóng phòng về trạng thái <code>available / active</code>.<br>
                            • Gửi thông báo thành công cho Khách thuê.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-check"></i> Xác nhận đã chuyển khoản',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                focusConfirm: false
            });

            if (!isConfirmed) return;

            // Loading state
            Swal.fire({
                title: 'Đang xử lý xuất quỹ...',
                html: 'Hệ thống đang cập nhật hợp đồng, tự động mở lại phòng trống và gửi thông báo...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch(`/Admin/tai-chinh/xac-nhan-hoan-coc/${contractId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Hoàn Cọc Thành Công!',
                        html: `
                            <div style="font-size: 0.95rem; color: #065f46; line-height: 1.6;">
                                ${data.message}
                            </div>
                        `,
                        confirmButtonText: 'Tuyệt vời',
                        confirmButtonColor: '#059669'
                    });

                    // Tải lại trang để đồng bộ thống kê
                    location.reload();
                } else {
                    throw new Error(data.message || 'Không thể xác nhận hoàn cọc. Vui lòng thử lại!');
                }

            } catch (err) {
                console.error('Lỗi xác nhận hoàn cọc:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi Xử Lý',
                    text: err.message || 'Đã có lỗi xảy ra trong quá trình xác nhận hoàn cọc.',
                    confirmButtonColor: '#dc2626'
                });
            }
        }
    </script>
</body>
</html>
