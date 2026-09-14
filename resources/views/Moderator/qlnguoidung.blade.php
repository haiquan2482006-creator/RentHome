<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Khoản - RentHome Moderator</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --bg-main: #f4f6fa;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --brand-primary: #0284c7;
            --brand-hover: #0369a1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --sidebar-width: 260px;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .moderator-container {
            margin-left: var(--sidebar-width);
            padding: 32px 40px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        /* Search Card */
        .search-card {
            background: var(--bg-card);
            padding: 20px 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
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

        .search-input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-primary);
            background: #f8fafc;
            outline: none;
            transition: all 0.2s;
        }

        .search-input:focus {
            background: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.1);
        }

        .btn-search {
            background: var(--brand-primary);
            color: #ffffff;
            border: none;
            padding: 12px 22px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-search:hover {
            background: var(--brand-hover);
        }

        .btn-reset {
            background: #f1f5f9;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: var(--text-primary);
        }

        .search-result-info {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 600;
            margin-top: 12px;
        }

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            background: #f8fafc;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.03em;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .user-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(2, 132, 199, 0.12);
            color: var(--brand-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            margin-right: 10px;
        }

        .badge-type {
            background: #e0f2fe;
            color: #0284c7;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .badge-status {
            background: #dcfce7;
            color: #15803d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .pagination-container {
            padding: 16px 20px;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
        }

        .btn-view-detail {
            background: rgba(2, 132, 199, 0.08);
            color: var(--brand-primary);
            border: 1px solid rgba(2, 132, 199, 0.2);
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.825rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-view-detail:hover {
            background: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
        }

        .user-link-title {
            color: var(--text-primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .user-link-title:hover {
            color: var(--brand-primary);
        }

        @media (max-width: 992px) {
            .moderator-container {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa-solid fa-users-gear" style="color: var(--brand-primary);"></i> Quản Lý Tài Khoản Người Dùng</h1>
            <p>Tra cứu & tìm kiếm thông tin tài khoản thành viên trong hệ thống RentHome</p>
        </div>

        <!-- Search Bar Card -->
        <div class="search-card">
            <form action="{{ url('/moderator/qlnguoidung') }}" method="GET" class="search-form">
                <div class="search-input-group">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Tìm theo tên người dùng, username, email...">
                </div>
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                </button>

                @if(!empty($search))
                    <a href="{{ url('/moderator/qlnguoidung') }}" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i> Xóa lọc
                    </a>
                @endif
            </form>

            @if(!empty($search))
                <div class="search-result-info">
                    <i class="fa-solid fa-circle-info" style="color: var(--brand-primary); margin-right: 4px;"></i>
                    Kết quả tìm kiếm cho từ khóa: <strong style="color: var(--text-primary);">"{{ $search }}"</strong>
                    ({{ method_exists($users, 'total') ? $users->total() : count($users) }} tài khoản)
                </div>
            @endif
        </div>

        <!-- Data Table Card -->
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tên người dùng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Loại tài khoản</th>
                        <th>Trạng thái</th>
                        <th style="text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $displayName = $user->account_name ?? $user->username ?? 'Thành viên';
                            $firstLetter = strtoupper(mb_substr($displayName, 0, 1));
                            $detailUrl = url('/moderator/chitiet-nguoidung/' . $user->id);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ $detailUrl }}" style="text-decoration: none; display: flex; align-items: center;">
                                    <div class="user-avatar-sm">{{ $firstLetter }}</div>
                                    <div>
                                        <div class="user-link-title" style="font-weight: 700;">{{ $displayName }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">@ {{ $user->username ?? 'user' }}</div>
                                    </div>
                                </a>
                            </td>
                            <td style="color: var(--text-secondary); font-weight: 500;">{{ $user->email }}</td>
                            <td style="color: var(--text-secondary);">{{ $user->phone ?? '---' }}</td>
                            <td>
                                <span class="badge-type">{{ ucfirst($user->account_type ?? 'Thành viên') }}</span>
                            </td>
                            <td>
                                <span class="badge-status"><i class="fa-solid fa-circle-check" style="margin-right: 4px;"></i> Hoạt động</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ $detailUrl }}" class="btn-view-detail">
                                    <i class="fa-solid fa-id-card"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                <i class="fa-solid fa-user-slash" style="font-size: 2rem; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                                Không tìm thấy người dùng nào phù hợp
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($users, 'hasPages') && $users->hasPages())
                <div class="pagination-container">
                    {{ $users->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </main>
</body>
</html>
