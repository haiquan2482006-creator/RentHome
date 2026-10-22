<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Công Nợ Tiền Mặt Nhân Viên - RentHome Admin</title>

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

            --accent-rose: #dc2626;
            --accent-rose-bg: rgba(220, 38, 38, 0.1);

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

        /* Stats Cards */
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

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
            margin-bottom: 32px;
        }

        .table-header-title {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: #ffffff;
        }

        .table-header-title h2 {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table.finance-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.86rem;
        }

        table.finance-table thead th {
            background: #f8fafc;
            color: var(--text-secondary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        table.finance-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        table.finance-table tbody tr:hover {
            background-color: #f8fafc;
        }

        table.finance-table tbody td {
            padding: 18px 20px;
            vertical-align: middle;
        }

        /* Staff Info Cell */
        .staff-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .staff-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e0f2fe;
            color: var(--brand-primary);
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #bae6fd;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .staff-name {
            font-weight: 800;
            color: var(--text-primary);
            font-size: 0.92rem;
            display: block;
        }

        .staff-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Badge số lượng hóa đơn */
        .invoice-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
        }

        /* Tổng tiền công nợ đang giữ */
        .debt-amount-highlight {
            font-size: 1.15rem;
            font-weight: 800;
            color: #dc2626;
            background: #fee2e2;
            padding: 6px 14px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #fca5a5;
            white-space: nowrap;
        }

        .debt-cleared-badge {
            font-size: 0.85rem;
            font-weight: 800;
            color: #059669;
            background: #d1fae5;
            padding: 6px 14px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #a7f3d0;
            white-space: nowrap;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-view-detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-md);
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-size: 0.825rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-view-detail:hover {
            background: #e0f2fe;
            color: #0284c7;
            transform: translateY(-1px);
        }

        /* NÚT ACTION QUAN TRỌNG: "Xác nhận nhân viên đã nộp tiền (Xóa nợ)" */
        .btn-remit-debt {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            font-size: 0.825rem;
            font-weight: 800;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-remit-debt:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.4);
        }

        .btn-remit-debt:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
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
            background: #d1fae5;
            color: #059669;
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
            max-width: 500px;
            margin: 0 auto 20px auto;
        }

        /* Modal Chi Tiết Hóa Đơn Nhân Viên */
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
            max-width: 860px;
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
            font-size: 1.15rem;
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
        }

        .modal-staff-summary {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .modal-invoices-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
        }

        .modal-invoices-table th {
            background: #f1f5f9;
            color: var(--text-secondary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }

        .modal-invoices-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
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
                    <i class="fa-solid fa-hand-holding-dollar" style="color: #059669;"></i>
                    <span>Quản Lý Công Nợ Tiền Mặt Nhân Viên</span>
                </h1>
                <p>Xử lý Luồng 2: Theo dõi số tiền mặt nhân viên vận hành đã thu từ khách thuê nhưng chưa nộp lại về quỹ công ty.</p>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.finance.bank_transfers') }}" class="btn-main-site">
                    <i class="fa-solid fa-building-columns text-sky-600"></i>
                    <span>Sang Duyệt Chuyển Khoản</span>
                </a>
            </div>
        </header>

        <!-- 4 Thẻ Thống Kê Tổng Quan -->
        <div class="stats-grid">
            <div class="stat-card" style="--card-accent: #dc2626;">
                <div class="stat-info">
                    <div class="stat-label">Tổng tiền mặt nhân viên đang giữ</div>
                    <div class="stat-number" style="color: #dc2626;" id="stat-total-debt">{{ number_format($totalSystemDebt) }} <span style="font-size: 0.9rem; font-weight: 600;">đ</span></div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #fee2e2; --stat-icon-color: #dc2626;">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #d97706;">
                <div class="stat-info">
                    <div class="stat-label">Nhân sự đang giữ tiền</div>
                    <div class="stat-number" style="color: #d97706;" id="stat-staff-holding">{{ number_format($totalStaffHolding) }}</div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #fef3c7; --stat-icon-color: #d97706;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #0284c7;">
                <div class="stat-info">
                    <div class="stat-label">Số hóa đơn chưa nộp quỹ</div>
                    <div class="stat-number" style="color: #0284c7;" id="stat-unremitted-invoices">{{ number_format($totalUnremittedInvoices) }}</div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #e0f2fe; --stat-icon-color: #0284c7;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <div class="stat-card" style="--card-accent: #059669;">
                <div class="stat-info">
                    <div class="stat-label">Tiền mặt đã nộp về công ty</div>
                    <div class="stat-number" style="color: #059669;">{{ number_format($totalRemittedAmount) }} <span style="font-size: 0.9rem; font-weight: 600;">đ</span></div>
                </div>
                <div class="stat-icon" style="--stat-icon-bg: #d1fae5; --stat-icon-color: #059669;">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
        </div>

        <!-- Bảng Danh Sách Nhân Sự Đang Giữ Tiền Mặt Của Công Ty -->
        <div class="table-card">
            <div class="table-header-title">
                <h2>
                    <i class="fa-solid fa-user-clock text-amber-500"></i>
                    <span>Danh Sách Nhân Sự Đang Giữ Tiền Mặt (Chưa Nộp Quỹ)</span>
                </h2>
                <span style="font-size: 0.8rem; color: var(--text-secondary);">
                    Cập nhật thời gian thực dựa trên luồng nhân viên thu tiền mặt tại phòng.
                </span>
            </div>

            <div class="table-responsive">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">STT</th>
                            <th>Tên Nhân Sự Thu Tiền</th>
                            <th style="text-align: center;">Số Lượng Hóa Đơn Đã Thu</th>
                            <th>Tổng Tiền Đang Giữ (Công Nợ)</th>
                            <th>Lần Thu Tiền Gần Nhất</th>
                            <th style="text-align: right; width: 360px;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody id="staff-debt-table-body">
                        @forelse($staffDebtsList as $index => $item)
                            @php
                                $initials = strtoupper(mb_substr($item->staff_name, 0, 2, 'UTF-8'));
                            @endphp
                            <tr id="row-staff-{{ $item->staff_id }}">
                                <td style="text-align: center; color: var(--text-muted); font-weight: 700;">
                                    {{ sprintf('%02d', $index + 1) }}
                                </td>

                                <!-- Cột 1: Tên nhân sự -->
                                <td>
                                    <div class="staff-cell">
                                        <div class="staff-avatar">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <span class="staff-name">{{ $item->staff_name }}</span>
                                            <div class="staff-sub">
                                                <span><i class="fa-solid fa-envelope mr-1"></i>{{ $item->staff_email }}</span>
                                                @if(!empty($item->staff_phone))
                                                    <span>•</span>
                                                    <span><i class="fa-solid fa-phone mr-1"></i>{{ $item->staff_phone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Cột 2: Số lượng hóa đơn đã thu -->
                                <td style="text-align: center;">
                                    <span class="invoice-count-badge" id="badge-count-{{ $item->staff_id }}">
                                        <i class="fa-solid fa-file-invoice text-slate-500"></i>
                                        <span>{{ $item->invoice_count }} hóa đơn</span>
                                    </span>
                                </td>

                                <!-- Cột 3: Tổng tiền đang giữ -->
                                <td>
                                    <div id="amount-container-{{ $item->staff_id }}">
                                        <span class="debt-amount-highlight">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ number_format($item->total_amount) }} đ</span>
                                        </span>
                                    </div>
                                </td>

                                <!-- Cột 4: Lần thu tiền gần nhất -->
                                <td>
                                    <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                        <i class="fa-regular fa-clock mr-1 text-slate-400"></i>
                                        {{ $item->latest_date ?: 'Gần đây' }}
                                    </span>
                                </td>

                                <!-- Cột 5: Nút Action -->
                                <td style="text-align: right;" id="action-cell-{{ $item->staff_id }}">
                                    <div class="action-group">
                                        <!-- Nút Action 1: "Xem chi tiết" (Mở ra list các hóa đơn nhân viên đó đã thu) -->
                                        <button type="button" 
                                                class="btn-view-detail" 
                                                onclick="openStaffInvoicesModal('{{ $item->staff_id }}', '{{ $item->staff_name }}', '{{ number_format($item->total_amount) }}', {{ $item->invoice_count }})">
                                            <i class="fa-solid fa-list-check"></i>
                                            <span>Xem chi tiết ({{ $item->invoice_count }})</span>
                                        </button>

                                        <!-- Nút Action quan trọng: "Xác nhận nhân viên đã nộp tiền (Xóa nợ)" -->
                                        <button type="button" 
                                                id="btn-remit-{{ $item->staff_id }}"
                                                class="btn-remit-debt" 
                                                onclick="confirmRemitDebt('{{ $item->staff_id }}', '{{ $item->staff_name }}', '{{ number_format($item->total_amount) }}', {{ $item->invoice_count }})">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                            <span>Xác nhận đã nộp tiền (Xóa nợ)</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fa-solid fa-shield-check"></i>
                                        </div>
                                        <h3>Không có nhân viên nào đang giữ nợ tiền mặt</h3>
                                        <p>
                                            Hiện tại không có nhân viên nào đang giữ nợ tiền mặt. Toàn bộ tiền mặt thu từ khách thuê đã được nhân sự bàn giao và nộp đầy đủ về quỹ công ty.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bảng Phụ: Lịch Sử Các Khoản Tiền Mặt Đã Nộp Quỹ Gần Đây -->
        @if(isset($remittedInvoices) && $remittedInvoices->isNotEmpty())
            <div class="table-card">
                <div class="table-header-title">
                    <h2>
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                        <span>Lịch Sử Các Hóa Đơn Tiền Mặt Đã Nộp Quỹ Gần Đây</span>
                    </h2>
                    <span style="font-size: 0.8rem; color: var(--accent-emerald); font-weight: 700;">
                        <i class="fa-solid fa-circle-check"></i> Đã hoàn tất nộp tiền về công ty
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="finance-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">STT</th>
                                <th>Mã Hóa Đơn</th>
                                <th>Khách Thuê / Phòng</th>
                                <th>Nhân Sự Nộp Tiền</th>
                                <th>Số Tiền</th>
                                <th>Thời Gian Nộp Quỹ</th>
                                <th style="text-align: center;">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remittedInvoices->take(8) as $hIndex => $rInv)
                                <tr>
                                    <td style="text-align: center; color: var(--text-muted); font-weight: 700;">
                                        {{ sprintf('%02d', $hIndex + 1) }}
                                    </td>
                                    <td>
                                        <strong style="font-family: monospace; color: var(--brand-primary);">{{ $rInv->invoice_code }}</strong>
                                    </td>
                                    <td>
                                        <span>{{ $rInv->tenant_name ?: 'Khách thuê' }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: var(--text-primary);">{{ $rInv->collector_display_name }}</strong>
                                    </td>
                                    <td>
                                        <strong style="color: var(--accent-emerald-dark);">{{ number_format($rInv->total_amount) }} đ</strong>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                            {{ $rInv->remitted_at ?: ($rInv->updated_at ? $rInv->updated_at->format('d/m/Y H:i') : '') }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="debt-cleared-badge" style="font-size: 0.75rem; padding: 3px 10px;">
                                            <i class="fa-solid fa-check"></i> Đã nộp quỹ
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </main>

    <!-- Modal Chi Tiết Các Hóa Đơn Nhân Viên Đã Thu -->
    <div class="modal-overlay" id="invoicesModal" onclick="closeStaffInvoicesModal(event)">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                    <span>Chi Tiết Hóa Đơn Đã Thu - <span id="modalStaffTitle" style="color: var(--brand-primary);"></span></span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeStaffInvoicesModal()">&times;</button>
            </div>

            <div class="modal-body">
                <div class="modal-staff-summary">
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Nhân sự phụ trách:</span>
                        <strong id="modalSummaryName" style="color: var(--text-primary); font-size: 1.05rem;"></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Số lượng hóa đơn:</span>
                        <strong id="modalSummaryCount" style="color: var(--brand-primary);"></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 0.75rem; display: block;">Tổng tiền đang giữ:</span>
                        <strong id="modalSummaryAmount" style="color: #dc2626; font-size: 1.15rem;"></strong>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="modal-invoices-table">
                        <thead>
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Phòng / Khách Thuê</th>
                                <th>Kỳ Thu</th>
                                <th>Số Tiền</th>
                                <th>Ngày Thu Tiền</th>
                                <th style="text-align: center;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="modalInvoicesTbody">
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tải danh sách hóa đơn...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-main-site" onclick="closeStaffInvoicesModal()">
                    Đóng
                </button>

                <button type="button" id="modalBtnRemitDirect" class="btn-remit-debt">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Xác Nhận Đã Thu Đủ Tiền (Xóa Nợ)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotify" class="toast-notify">
        <i class="fa-solid fa-circle-check" id="toastIcon"></i>
        <span id="toastMessage">Thông báo</span>
    </div>

    <!-- SCRIPT XỬ LÝ XÓA NỢ VÀ XEM CHI TIẾT QUA AJAX -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        let currentModalStaffId = null;
        let currentModalStaffName = null;
        let currentModalTotalFormatted = null;
        let currentModalCount = 0;

        // Mở Modal xem chi tiết các hóa đơn nhân viên đó đã thu
        async function openStaffInvoicesModal(staffId, staffName, totalAmount, count) {
            currentModalStaffId = staffId;
            currentModalStaffName = staffName;
            currentModalTotalFormatted = totalAmount;
            currentModalCount = count;

            document.getElementById('modalStaffTitle').innerText = staffName;
            document.getElementById('modalSummaryName').innerText = staffName;
            document.getElementById('modalSummaryCount').innerText = count + ' hóa đơn';
            document.getElementById('modalSummaryAmount').innerText = totalAmount + ' đ';

            const modalBtnRemit = document.getElementById('modalBtnRemitDirect');
            modalBtnRemit.onclick = function() {
                closeStaffInvoicesModal();
                confirmRemitDebt(staffId, staffName, totalAmount, count);
            };

            const tbody = document.getElementById('modalInvoicesTbody');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu hóa đơn...</td></tr>';

            document.getElementById('invoicesModal').classList.add('active');

            try {
                const res = await fetch(`/Admin/tai-chinh/nhan-vien/${encodeURIComponent(staffId)}/hoa-don`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.success && data.invoices && data.invoices.length > 0) {
                    let html = '';
                    data.invoices.forEach(inv => {
                        html += `
                            <tr>
                                <td><strong style="font-family: monospace; color: #0284c7;">${inv.invoice_code}</strong></td>
                                <td>
                                    <strong>${inv.room_title}</strong>
                                    <span style="font-size: 0.72rem; color: #64748b; display: block;">Khách: ${inv.tenant_name}</span>
                                </td>
                                <td><span style="font-size: 0.78rem;">${inv.billing_period}</span></td>
                                <td><strong style="color: #dc2626;">${inv.total_formatted}</strong></td>
                                <td><span style="font-size: 0.78rem; color: #64748b;">${inv.payment_date || 'Gần đây'}</span></td>
                                <td style="text-align: center;">
                                    <a href="${inv.view_url}" target="_blank" class="btn-main-site" style="font-size: 0.75rem; padding: 4px 10px;">
                                        <i class="fa-solid fa-eye"></i> Xem HĐ
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">Không còn hóa đơn chưa nộp nào cho nhân sự này.</td></tr>';
                }
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 24px; color: #dc2626;">Không thể tải danh sách hóa đơn.</td></tr>';
            }
        }

        function closeStaffInvoicesModal(e) {
            if (!e || e.target.id === 'invoicesModal' || e.target.classList.contains('modal-close-btn') || e.target.tagName === 'BUTTON') {
                document.getElementById('invoicesModal').classList.remove('active');
            }
        }

        // Action quan trọng: Xác nhận nhân viên đã nộp tiền (Xóa nợ)
        async function confirmRemitDebt(staffId, staffName, totalAmount, count) {
            const promptMsg = `XÁC NHẬN NỘP TIỀN QUỸ CÔNG TY:\n\nBạn có chắc chắn xác nhận nhân sự "${staffName}" đã nộp đủ số tiền ${totalAmount} VNĐ (${count} hóa đơn) về quỹ công ty không?\n\nSau khi xác nhận:\n- Toàn bộ hóa đơn tiền mặt sẽ chuyển sang trạng thái ĐÃ NỘP (is_remitted_to_admin = true).\n- TỔNG CÔNG NỢ CỦA NHÂN VIÊN SẼ LẬP TỨC VỀ 0 VNĐ.`;

            if (!confirm(promptMsg)) {
                return;
            }

            const btn = document.getElementById(`btn-remit-${staffId}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xóa nợ...';
            }

            try {
                const response = await fetch(`/Admin/tai-chinh/nhan-vien/${encodeURIComponent(staffId)}/xac-nhan-nop-tien`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message || 'Xác nhận nộp tiền thành công! Công nợ đã về 0 VNĐ.', 'success');

                    // 1. Cập nhật thẻ hiển thị công nợ sang 0 đ
                    const amountContainer = document.getElementById(`amount-container-${staffId}`);
                    if (amountContainer) {
                        amountContainer.innerHTML = '<span class="debt-cleared-badge"><i class="fa-solid fa-circle-check"></i> Đã nộp đủ (0 đ)</span>';
                    }

                    // 2. Cập nhật Cell hành động
                    const actionCell = document.getElementById(`action-cell-${staffId}`);
                    if (actionCell) {
                        actionCell.innerHTML = '<span class="debt-cleared-badge" style="font-size: 0.8rem;"><i class="fa-solid fa-check"></i> Đã xóa nợ hoàn tất</span>';
                    }

                    // 3. Cập nhật thống kê đầu trang
                    const statDebt = document.getElementById('stat-total-debt');
                    if (statDebt) {
                        let curDebt = parseInt(statDebt.innerText.replace(/[^0-9]/g, '')) || 0;
                        let remitted = data.remitted_amount || 0;
                        let newDebt = Math.max(0, curDebt - remitted);
                        statDebt.innerHTML = `${newDebt.toLocaleString('vi-VN')} <span style="font-size: 0.9rem; font-weight: 600;">đ</span>`;
                    }

                    const statStaff = document.getElementById('stat-staff-holding');
                    if (statStaff) {
                        let curStaff = parseInt(statStaff.innerText.replace(/,/g, '')) || 0;
                        statStaff.innerText = Math.max(0, curStaff - 1).toLocaleString('vi-VN');
                    }

                    const statInv = document.getElementById('stat-unremitted-invoices');
                    if (statInv) {
                        let curInv = parseInt(statInv.innerText.replace(/,/g, '')) || 0;
                        statInv.innerText = Math.max(0, curInv - (data.remitted_count || 0)).toLocaleString('vi-VN');
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra khi xác nhận nộp tiền!', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-hand-holding-dollar"></i> <span>Xác nhận đã nộp tiền (Xóa nợ)</span>';
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ! Vui lòng thử lại.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-hand-holding-dollar"></i> <span>Xác nhận đã nộp tiền (Xóa nợ)</span>';
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

        // Flash message
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
