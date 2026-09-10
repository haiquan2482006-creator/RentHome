<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Người Dùng - RentHome Admin</title>

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

        /* --- Khung Tìm Kiếm & Lọc --- */
        .search-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 20px 24px;
            margin-bottom: 24px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-input-group {
            position: relative;
            flex: 1;
            min-width: 280px;
        }

        .search-input-group i.search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 11px 40px 11px 40px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--text-primary);
            background-color: #f8fafc;
            transition: var(--transition);
            outline: none;
        }

        .search-input:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .clear-search-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 50%;
            transition: var(--transition);
        }

        .clear-search-btn:hover {
            color: var(--accent-red);
            background-color: rgba(220, 38, 38, 0.08);
        }

        .filter-select {
            padding: 11px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-secondary);
            background-color: #f8fafc;
            outline: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            border-color: var(--brand-primary);
            background-color: #ffffff;
        }

        .btn-search {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            background-color: var(--brand-primary);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-search:hover {
            background-color: var(--brand-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 11px 16px;
            background-color: #f1f5f9;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-reset:hover {
            background-color: #e2e8f0;
            color: var(--text-primary);
        }

        /* --- Stats Meta Chips --- */
        .search-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px dashed var(--border-color);
            font-size: 0.825rem;
            color: var(--text-secondary);
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            background-color: var(--brand-bg);
            color: var(--brand-primary);
            font-weight: 700;
        }

        /* --- Bảng dữ liệu Người Dùng --- */
        .users-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 24px;
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

        .user-info-flex {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: var(--brand-primary);
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(2, 132, 199, 0.2);
            flex-shrink: 0;
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
            letter-spacing: 0.02em;
        }

        .user-subtext {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* Badge phân loại tài khoản */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-personal {
            background-color: rgba(2, 132, 199, 0.1);
            color: var(--brand-primary);
        }

        .badge-business {
            background-color: rgba(147, 51, 234, 0.1);
            color: #9333ea;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .status-active {
            color: var(--accent-emerald);
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-icon:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            background-color: var(--brand-bg);
        }

        .btn-icon-danger {
            color: var(--accent-red, #dc2626);
        }

        .btn-icon-danger:hover {
            border-color: var(--accent-red, #dc2626);
            color: #ffffff;
            background-color: var(--accent-red, #dc2626);
        }

        .alert-message {
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

        .pagination-container {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>
    @include('partials.header-admin')

    <main class="admin-main">
        <!-- Page Title & Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Quản Lý Người Dùng</h1>
                <p class="page-subtitle">Tìm kiếm, lọc và xem thông tin danh sách tài khoản thành viên hệ thống</p>
            </div>
            <a href="{{ url('/') }}" class="btn-main-site">
                <i class="fa-solid fa-globe"></i>
                <span>Xem giao diện</span>
            </a>
        </div>

        @if (session('success'))
            <div class="alert-message alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-message alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Thanh Tìm Kiếm & Bộ Lọc Tài Khoản -->
        <div class="search-card">
            <form action="{{ url('qlnguoi_dung') }}" method="GET" class="search-form">
                <!-- Nhập từ khóa tìm kiếm -->
                <div class="search-input-group">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Tìm kiếm theo ID, Tên tài khoản, Email, SĐT, Tên doanh nghiệp..." 
                        value="{{ $search ?? '' }}"
                        autocomplete="off"
                    >
                    @if (!empty($search))
                        <a href="{{ url('qlnguoi_dung', array_filter(['account_type' => $accountType ?? ''])) }}" class="clear-search-btn" title="Xóa từ khóa tìm kiếm">&times;</a>
                    @endif
                </div>

                <!-- Lọc loại tài khoản -->
                <select name="account_type" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả loại tài khoản</option>
                    <option value="canhan" {{ ($accountType ?? '') === 'canhan' ? 'selected' : '' }}>Cá nhân</option>
                    <option value="doanhnghiep" {{ ($accountType ?? '') === 'doanhnghiep' ? 'selected' : '' }}>Doanh nghiệp</option>
                </select>

                <!-- Nút Tìm kiếm -->
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-search"></i> Tìm kiếm
                </button>

                <!-- Nút Đặt lại/Xóa bộ lọc -->
                @if (!empty($search) || !empty($accountType))
                    <a href="{{ url('qlnguoi_dung') }}" class="btn-reset" title="Đặt lại bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i> Xóa tìm kiếm
                    </a>
                @endif
            </form>

            <!-- Meta thông tin kết quả -->
            <div class="search-meta">
                <div>
                    Hiển thị <strong>{{ $users->count() }}</strong> / Tổng <strong>{{ $totalUsers ?? $users->total() }}</strong> tài khoản
                </div>
                @if (!empty($search))
                    <div>
                        Từ khóa: <span class="meta-chip">"{{ $search }}"</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bảng Danh Sách Người Dùng -->
        <div class="users-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tài khoản / Người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Loại tài khoản</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $name = $user->account_name ?? $user->username ?? 'Thành viên';
                                $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-info-flex">
                                        <div class="user-avatar-circle">{{ $initials }}</div>
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
                                    <span style="font-weight: 600; color: var(--text-primary);">{{ $user->email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{ $user->phone ?? 'Chưa cập nhật' }}
                                </td>
                                <td>
                                    @if (($user->account_type ?? '') === 'doanhnghiep')
                                        <span class="badge badge-business">
                                            <i class="fa-solid fa-building"></i> Doanh nghiệp
                                        </span>
                                    @else
                                        <span class="badge badge-personal">
                                            <i class="fa-solid fa-user"></i> Cá nhân
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Mới đây' }}
                                </td>
                                <td>
                                    <span class="status-pill status-active">
                                        <i class="fa-solid fa-circle-check"></i> Hoạt động
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-buttons">
                                        <button class="btn-icon" title="Xem chi tiết tài khoản">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <form action="{{ url('qlnguoi_dung/lock/' . $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản {{ $name }} (ID: #{{ $user->id }}) không?');">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-icon-warning" title="Khóa tài khoản">
                                                <i class="fa-solid fa-user-slash"></i>
                                            </button>
                                        </form>
                                        <form action="{{ url('qlnguoi_dung/' . $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ $name }} (ID: #{{ $user->id }}) không? Tài khoản sẽ được chuyển vào mục Lịch Sử.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-danger" title="Xóa tài khoản">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                                    <i class="fa-solid fa-user-xmark" style="font-size: 2.5rem; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-secondary); display: block;">Không tìm thấy tài khoản nào khớp với từ khóa</span>
                                    <p style="font-size: 0.825rem; margin-top: 6px;">Vui lòng thử tìm kiếm bằng tên, email hoặc số điện thoại khác.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            @if ($users->hasPages())
                <div class="pagination-container">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </main>
</body>
</html>
