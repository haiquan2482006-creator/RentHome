<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch Sử Tài Khoản - RentHome Admin</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
            min-height: 100vh;
            background-color: var(--bg-main);
        }

        .page-header {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* --- Thẻ Thống Kê Tổng Quan --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-icon-locked {
            background-color: rgba(217, 119, 6, 0.12);
            color: var(--accent-amber);
        }

        .stat-icon-deleted {
            background-color: rgba(220, 38, 38, 0.12);
            color: var(--accent-red);
        }

        .stat-icon-total {
            background-color: var(--brand-bg);
            color: var(--brand-primary);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* --- Thanh Tab Chuyển Đổi & Tìm Kiếm --- */
        .filter-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .history-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-secondary);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn:hover {
            color: var(--text-primary);
        }

        .tab-btn.active {
            background-color: #ffffff;
            color: var(--brand-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .tab-btn.active-locked {
            color: var(--accent-amber);
        }

        .tab-btn.active-deleted {
            color: var(--accent-red);
        }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 0.725rem;
            font-weight: 800;
        }

        .tab-badge-locked {
            background-color: rgba(217, 119, 6, 0.15);
            color: var(--accent-amber);
        }

        .tab-badge-deleted {
            background-color: rgba(220, 38, 38, 0.15);
            color: var(--accent-red);
        }

        .search-box {
            position: relative;
            min-width: 260px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .search-box input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.875rem;
            background-color: #f8fafc;
            outline: none;
            transition: var(--transition);
        }

        .search-box input:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* --- Section Bảng Dữ Liệu --- */
        .section-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 24px;
            margin-bottom: 28px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .section-title i {
            font-size: 1.15rem;
        }

        .section-title-locked i {
            color: var(--accent-amber);
        }

        .section-title-deleted i {
            color: var(--accent-red);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            background-color: #f8fafc;
        }

        .data-table th:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .data-table th:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .data-table td {
            padding: 16px;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* --- User Info Flex --- */
        .user-info-flex {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .avatar-locked {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
            border: 1px solid rgba(217, 119, 6, 0.25);
        }

        .avatar-deleted {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid rgba(220, 38, 38, 0.25);
        }

        .user-name-text {
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .user-id-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--brand-primary);
            background-color: rgba(2, 132, 199, 0.1);
            border-radius: 4px;
        }

        .user-subtext {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* --- Status Badges --- */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.775rem;
            font-weight: 700;
        }

        .status-locked {
            background-color: rgba(217, 119, 6, 0.1);
            color: #b45309;
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .status-deleted {
            background-color: rgba(220, 38, 38, 0.1);
            color: #b91c1c;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        /* --- Action Buttons --- */
        .btn-action-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-unlock {
            background-color: rgba(5, 150, 105, 0.1);
            color: #059669;
            border-color: rgba(5, 150, 105, 0.2);
        }

        .btn-unlock:hover {
            background-color: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }

        .btn-restore {
            background-color: rgba(2, 132, 199, 0.1);
            color: var(--brand-primary);
            border-color: rgba(2, 132, 199, 0.2);
        }

        .btn-restore:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-perm-delete {
            background-color: rgba(220, 38, 38, 0.08);
            color: var(--accent-red);
            border-color: rgba(220, 38, 38, 0.2);
        }

        .btn-perm-delete:hover {
            background-color: var(--accent-red);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        /* --- Custom Alert Banner --- */
        .alert-banner {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2.8rem;
            margin-bottom: 12px;
            color: #cbd5e1;
            display: block;
        }

        .empty-state-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-secondary);
            display: block;
        }

        .empty-state-desc {
            font-size: 0.825rem;
            margin-top: 4px;
        }

        /* Tab switching visibility */
        .tab-content {
            display: block;
        }

        .tab-content.hidden {
            display: none;
        }
    </style>
</head>
<body>
    @include('partials.header-admin')

    <main class="admin-main">
        <!-- Page Title & Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Lịch Sử Quản Lý Tài Khoản</h1>
                <p class="page-subtitle">Theo dõi, kiểm tra danh sách tài khoản người dùng bị khóa và tài khoản đã bị xóa</p>
            </div>
            <a href="{{ url('/') }}" class="btn-main-site">
                <i class="fa-solid fa-globe"></i>
                <span>Xem giao diện</span>
            </a>
        </div>

        <!-- Notification Banner -->
        @if (session('success'))
            <div class="alert-banner alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-banner alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @php
            $lockedList = $lockedUsers ?? [];
            $deletedList = $deletedUsers ?? [];

            $countLocked = is_countable($lockedList) ? count($lockedList) : 0;
            $countDeleted = is_countable($deletedList) ? count($deletedList) : 0;
            $countTotal = $countLocked + $countDeleted;
        @endphp

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-locked">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value" id="stat-locked-count">{{ $countLocked }}</span>
                    <span class="stat-label">Tài khoản bị khóa</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-deleted">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value" id="stat-deleted-count">{{ $countDeleted }}</span>
                    <span class="stat-label">Tài khoản đã xóa</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-total">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $countTotal }}</span>
                    <span class="stat-label">Tổng lịch sử ghi nhận</span>
                </div>
            </div>
        </div>

        <!-- Filter & Tab Switcher Bar -->
        <div class="filter-card">
            <div class="history-tabs">
                <button type="button" class="tab-btn active" id="btn-tab-all" onclick="switchTab('all')">
                    <i class="fa-solid fa-layer-group"></i> Tất cả
                </button>
                <button type="button" class="tab-btn" id="btn-tab-locked" onclick="switchTab('locked')">
                    <i class="fa-solid fa-user-lock"></i> Tài khoản bị khóa 
                    <span class="tab-badge tab-badge-locked">{{ $countLocked }}</span>
                </button>
                <button type="button" class="tab-btn" id="btn-tab-deleted" onclick="switchTab('deleted')">
                    <i class="fa-solid fa-user-slash"></i> Tài khoản bị xóa 
                    <span class="tab-badge tab-badge-deleted">{{ $countDeleted }}</span>
                </button>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="history-search" placeholder="Tìm theo ID, Tên, Email..." onkeyup="filterHistoryTables()">
            </div>
        </div>

        <!-- SECTION 1: PHẦN TÀI KHOẢN BỊ KHÓA -->
        <div class="section-card tab-content" id="section-locked">
            <div class="section-header">
                <div class="section-title section-title-locked">
                    <i class="fa-solid fa-user-lock"></i>
                    <span>Danh Sách Tài Khoản Đang Bị Khóa</span>
                </div>
                <span class="tab-badge tab-badge-locked" style="font-size: 0.8rem; padding: 4px 10px;">
                    {{ $countLocked }} tài khoản
                </span>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="table-locked">
                    <thead>
                        <tr>
                            <th>Tài khoản</th>
                            <th>Email / SĐT</th>
                            <th>Lý do khóa</th>
                            <th>Thời gian khóa</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lockedList as $user)
                            @php
                                $name = $user->account_name ?? $user->username ?? 'Người dùng';
                                $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                            @endphp
                            <tr class="history-row" data-search="{{ strtolower(($user->id ?? '') . ' ' . $name . ' ' . ($user->email ?? '') . ' ' . ($user->username ?? '')) }}">
                                <td>
                                    <div class="user-info-flex">
                                        <div class="user-avatar-circle avatar-locked">{{ $initials }}</div>
                                        <div>
                                            <div class="user-name-text">
                                                <span>{{ $name }}</span>
                                                <span class="user-id-badge">ID: #{{ $user->id }}</span>
                                            </div>
                                            <div class="user-subtext">{{ '@' . ($user->username ?? 'user') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary);">{{ $user->email ?? 'Chưa có email' }}</div>
                                    <div class="user-subtext">{{ $user->phone ?? 'Chưa có SĐT' }}</div>
                                </td>
                                <td>
                                    <span style="color: var(--text-secondary); font-size: 0.85rem; font-style: italic;">
                                        "{{ $user->lock_reason ?? 'Khóa bởi quản trị viên' }}"
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.825rem; font-weight: 600; color: var(--text-secondary);">
                                        <i class="fa-regular fa-clock" style="margin-right: 4px;"></i>
                                        {{ $user->locked_at ?? 'Gần đây' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-locked">
                                        <i class="fa-solid fa-lock"></i> Đã bị khóa
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="btn-action-group">
                                        <form action="{{ url('qlnguoi_dung/unlock/' . $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn MỞ KHÓA cho tài khoản {{ $name }} (ID: #{{ $user->id }}) không?');">
                                            @csrf
                                            <button type="submit" class="btn-action btn-unlock" title="Mở khóa tài khoản">
                                                <i class="fa-solid fa-lock-open"></i> Mở khóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-user-lock"></i>
                                        <span class="empty-state-title">Chưa có tài khoản bị khóa</span>
                                        <p class="empty-state-desc">Hiện tại hệ thống chưa ghi nhận tài khoản nào bị khóa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 2: PHẦN TÀI KHOẢN ĐÃ XÓA -->
        <div class="section-card tab-content" id="section-deleted">
            <div class="section-header">
                <div class="section-title section-title-deleted">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Danh Sách Tài Khoản Đã Xóa</span>
                </div>
                <span class="tab-badge tab-badge-deleted" style="font-size: 0.8rem; padding: 4px 10px;">
                    {{ $countDeleted }} tài khoản
                </span>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="table-deleted">
                    <thead>
                        <tr>
                            <th>Tài khoản đã xóa</th>
                            <th>Email / SĐT</th>
                            <th>Hình thức xóa</th>
                            <th>Thời gian xóa</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deletedList as $user)
                            @php
                                $name = $user->account_name ?? $user->username ?? 'Thành viên đã xóa';
                                $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                            @endphp
                            <tr class="history-row" data-search="{{ strtolower(($user->id ?? '') . ' ' . $name . ' ' . ($user->email ?? '') . ' ' . ($user->username ?? '')) }}">
                                <td>
                                    <div class="user-info-flex">
                                        <div class="user-avatar-circle avatar-deleted">{{ $initials }}</div>
                                        <div>
                                            <div class="user-name-text">
                                                <span>{{ $name }}</span>
                                                <span class="user-id-badge">ID: #{{ $user->id }}</span>
                                            </div>
                                            <div class="user-subtext">{{ '@' . ($user->username ?? 'user') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary);">{{ $user->email ?? 'N/A' }}</div>
                                    <div class="user-subtext">{{ $user->phone ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                        {{ $user->deleted_by ?? 'Xóa bởi Quản trị viên' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.825rem; font-weight: 600; color: var(--text-secondary);">
                                        <i class="fa-regular fa-calendar-xmark" style="margin-right: 4px;"></i>
                                        {{ $user->deleted_at ?? 'Gần đây' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-deleted">
                                        <i class="fa-solid fa-trash"></i> Đã xóa
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="btn-action-group">
                                        <!-- Nút Khôi phục -->
                                        <form action="{{ url('qlnguoi_dung/restore/' . $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có muốn KHÔI PHỤC lại tài khoản {{ $name }} (ID: #{{ $user->id }}) không?');">
                                            @csrf
                                            <button type="submit" class="btn-action btn-restore" title="Khôi phục tài khoản">
                                                <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                            </button>
                                        </form>

                                        <!-- Nút Xóa vĩnh viễn -->
                                        <form action="{{ url('qlnguoi_dung/force-delete/' . $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản {{ $name }} (ID: #{{ $user->id }}) khỏi hệ thống? Dữ liệu sẽ không thể phục hồi!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-perm-delete" title="Xóa vĩnh viễn">
                                                <i class="fa-solid fa-ban"></i> Xóa vĩnh viễn
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-trash-can-arrow-up"></i>
                                        <span class="empty-state-title">Chưa có tài khoản bị xóa</span>
                                        <p class="empty-state-desc">Hiện tại hệ thống chưa ghi nhận tài khoản nào bị xóa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Script chuyển Tab và Tìm kiếm lịch sử -->
    <script>
        function switchTab(type) {
            const sectionLocked = document.getElementById('section-locked');
            const sectionDeleted = document.getElementById('section-deleted');

            const btnAll = document.getElementById('btn-tab-all');
            const btnLocked = document.getElementById('btn-tab-locked');
            const btnDeleted = document.getElementById('btn-tab-deleted');

            // Reset tab active states
            btnAll.classList.remove('active', 'active-locked', 'active-deleted');
            btnLocked.classList.remove('active', 'active-locked', 'active-deleted');
            btnDeleted.classList.remove('active', 'active-locked', 'active-deleted');

            if (type === 'locked') {
                sectionLocked.classList.remove('hidden');
                sectionDeleted.classList.add('hidden');
                btnLocked.classList.add('active', 'active-locked');
            } else if (type === 'deleted') {
                sectionLocked.classList.add('hidden');
                sectionDeleted.classList.remove('hidden');
                btnDeleted.classList.add('active', 'active-deleted');
            } else {
                // Show all
                sectionLocked.classList.remove('hidden');
                sectionDeleted.classList.remove('hidden');
                btnAll.classList.add('active');
            }
        }

        function filterHistoryTables() {
            const query = document.getElementById('history-search').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.history-row');

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (searchData.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>