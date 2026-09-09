<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trang Quản Trị Admin - RentHome</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Thuần CSS (Vanilla CSS) - Chủ đạo Trắng & Xanh Nước Biển -->
    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            
            /* Bảng màu hệ thống Trắng & Xanh Nước Biển (Ocean Blue) */
            --bg-main: #f4f6fa;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            
            --brand-primary: #0284c7; /* Ocean Blue */
            --brand-hover: #0369a1;
            --brand-light: #38bdf8;
            --brand-bg: rgba(2, 132, 199, 0.1);
            
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            
            --accent-emerald: #059669;
            --accent-amber: #d97706;
            --accent-blue: #0284c7;
            --accent-red: #dc2626;
            
            --sidebar-width: 260px;
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
            display: flex;
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* --- Sidebar Navigation (Chiều dọc phía bên trái) --- */
        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.03);
        }

        /* Header Sidebar: Logo & Tên trang */
        .sidebar-header {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 0;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .logo-icon:hover {
            transform: scale(1.05);
        }

        .logo-icon i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-style: normal;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            filter: drop-shadow(0 2px 6px rgba(2, 132, 199, 0.2));
        }

        .logo-details {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .admin-badge {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 6px;
            background: var(--brand-bg);
            color: var(--brand-primary);
            border-radius: 4px;
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        .brand-subtext {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* Danh sách Menu */
        .sidebar-menu {
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu-title {
            padding: 0 12px 8px 12px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--brand-primary);
            background-color: var(--brand-bg);
        }

        .nav-link:hover i {
            color: var(--brand-primary);
        }

        /* Trạng thái Menu đang active */
        .nav-link.active {
            background-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        }

        .nav-link.active i {
            color: #ffffff;
        }

        /* Footer Sidebar / Admin Profile */
        .sidebar-footer {
            padding: 14px;
            margin: 12px;
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e0f2fe;
            color: var(--brand-primary);
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .user-email {
            font-size: 0.7rem;
            color: var(--text-secondary);
            line-height: 1.2;
        }

        .btn-logout {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            padding: 6px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-logout:hover {
            color: var(--accent-red);
            background-color: rgba(220, 38, 38, 0.08);
        }

        /* --- Vùng nội dung chính (Main Content) --- */
        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
            min-height: 100vh;
            background-color: var(--bg-main);
        }

        .page-header {
            margin-bottom: 32px;
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

        .btn-main-site {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background-color: var(--bg-card);
            color: var(--brand-primary);
            font-size: 0.875rem;
            font-weight: 700;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            text-decoration: none;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .btn-main-site:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }

        /* Grid thẻ thống kê tổng quan */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            padding: 24px;
            border-radius: 16px;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .stat-card:hover {
            border-color: var(--brand-primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -4px rgba(2, 132, 199, 0.12);
        }

        .card-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .card-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .text-emerald { color: var(--accent-emerald); }
        .text-amber { color: var(--accent-amber); }
        .text-blue { color: var(--accent-blue); }

        /* CSS cho phần Tài khoản đăng ký mới */
        .recent-users-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            padding: 24px;
            margin-top: 8px;
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-subtitle {
            font-size: 0.825rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: var(--brand-bg);
            color: var(--brand-primary);
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
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
            padding: 14px 16px;
            font-size: 0.875rem;
            color: var(--text-primary);
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background-color: #f8fafc;
        }

        .user-meta-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background-color: var(--brand-bg);
            color: var(--brand-primary);
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-avatar-sm.bg-blue { background-color: #e0f2fe; color: #0284c7; }
        .user-avatar-sm.bg-purple { background-color: #f3e8ff; color: #9333ea; }
        .user-avatar-sm.bg-emerald { background-color: #d1fae5; color: #059669; }
        .user-avatar-sm.bg-amber { background-color: #fef3c7; color: #d97706; }

        .user-meta-info {
            display: flex;
            flex-direction: column;
        }

        .user-name-text {
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .user-username-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-personal {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .badge-business {
            background-color: #f3e8ff;
            color: #7e22ce;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .status-active {
            background-color: #d1fae5;
            color: #047857;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
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
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background-color: var(--brand-bg);
        }

        .btn-icon-danger:hover {
            color: var(--accent-red);
            border-color: var(--accent-red);
            background-color: rgba(220, 38, 38, 0.08);
        }
    </style>
</head>

<body>

    <!-- Sidebar Header (Thanh menu dọc phía bên trái) -->
    <aside class="admin-sidebar">
        <div>
            <!-- Logo & Tên trang web -->
            <div class="sidebar-header">
                <div class="logo-icon">
                    <i><img src="{{asset('img/logo.png')}}" alt="logo"></i>
                </div>
                <div class="logo-details">
                    <span class="brand-name">RentHome<span class="admin-badge">Admin</span></span>
                    <span class="brand-subtext">Hệ thống quản trị</span>
                </div>
            </div>

            <!-- Danh sách Menu -->
            <nav class="sidebar-menu">
                <div class="menu-title">Danh mục quản lý</div>

                <!-- 1. Tổng quan -->
                <a href="#" class="nav-link active">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Tổng quan</span>
                </a>

                <!-- 2. Quản lý người dùng -->
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>

                <!-- 3. Chỉnh sửa giao diện -->
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-palette"></i>
                    <span>Chỉnh sửa giao diện</span>
                </a>

                <!-- 4. Quản lý tin tức -->
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-newspaper"></i>
                    <span>Quản lý tin tức</span>
                </a>

                <!-- 5. Yêu cầu kỷ luật -->
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-gavel"></i>
                    <span>Yêu cầu kỷ luật</span>
                </a>

                <!-- 6. Lịch sử -->
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Lịch sử</span>
                </a>
            </nav>
        </div>

        <!-- Thông tin User Admin / Đăng xuất -->
        <div class="sidebar-footer">
            <div class="user-profile">
                @php
                    $adminUser = Auth::user();
                    $adminName = $adminUser ? ($adminUser->account_name ?? $adminUser->username ?? 'Admin') : 'Admin';
                    $adminEmail = $adminUser->email ?? 'admin@renthome.vn';
                    $adminInitials = strtoupper(mb_substr($adminName, 0, 2, 'UTF-8'));
                @endphp
                <div class="avatar">{{ $adminInitials }}</div>
                <div class="user-details">
                    <span class="user-name">{{ $adminName }}</span>
                    <span class="user-email">{{ $adminEmail }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
                @csrf
                <button type="submit" title="Đăng xuất" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Khu vực nội dung chính (Main Content) -->
    <main class="admin-main">
        <header class="page-header">
            <div>
                <h1 class="page-title">Tổng quan hệ thống</h1>
                <p class="page-subtitle">Chào mừng trở lại trang quản trị RentHome.</p>
            </div>
            <a href="/" class="btn-main-site">
                <i class="fa-solid fa-globe"></i>
                <span>Xem giao diện</span>
            </a>
        </header>

        <!-- 4 thẻ thống kê -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="card-label">Tổng người dùng</div>
                <div class="card-value">{{ isset($totalUsers) ? number_format($totalUsers) : '1,248' }}</div>
            </div>
            <div class="stat-card">
                <div class="card-label">Tổng số bài đăng</div>
                <div class="card-value text-emerald">542</div>
            </div>
            <div class="stat-card">
                <div class="card-label">Số tài khoản vi phạm</div>
                <div class="card-value text-amber">12</div>
            </div>
            <div class="stat-card">
                <div class="card-label">Lượt truy cập</div>
                <div class="card-value text-blue">8,920</div>
            </div>
        </div>

        <!-- Phần tài khoản đăng ký mới -->
        <div class="recent-users-card">
            <div class="card-header-flex">
                <div>
                    <h2 class="card-title">
                        <i class="fa-solid fa-user-plus text-blue"></i>
                        Tài khoản đăng ký mới
                    </h2>
                    <p class="card-subtitle">Danh sách tài khoản vừa đăng ký tham gia hệ thống gần đây</p>
                </div>
                <a href="#" class="btn-secondary">
                    <span>Xem tất cả</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Loại tài khoản</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers ?? [] as $user)
                            @if(($user->role ?? '') !== 'admin' && ($user->account_type ?? '') !== 'admin')
                                @php
                                    $displayName = $user->account_type === 'doanhnghiep' 
                                        ? ($user->company_name ?? $user->username) 
                                        : ($user->account_name ?? $user->username);
                                    $initials = mb_substr($displayName, 0, 2, 'UTF-8');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="user-meta-cell">
                                            <div class="user-avatar-sm bg-blue">
                                                {{ strtoupper($initials) }}
                                            </div>
                                            <div class="user-meta-info">
                                                <span class="user-name-text">{{ $displayName }}</span>
                                                <span class="user-username-sub">{{ '@' . $user->username }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '---' }}</td>
                                    <td>
                                        @if($user->account_type === 'doanhnghiep')
                                            <span class="badge badge-business">Doanh nghiệp</span>
                                        @else
                                            <span class="badge badge-personal">Cá nhân</span>
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
                                            <button class="btn-icon" title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <button class="btn-icon btn-icon-danger" title="Khóa tài khoản">
                                                <i class="fa-solid fa-user-slash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                                    <i class="fa-solid fa-users-slash" style="font-size: 2.2rem; margin-bottom: 10px; color: #cbd5e1; display: block;"></i>
                                    <span style="font-size: 0.9rem; font-weight: 600;">Chưa có tài khoản mới nào đăng ký</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>

</html>
