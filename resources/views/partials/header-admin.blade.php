<!-- Google Fonts & Icons cho tất cả các trang Admin -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
</style>

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
            <a href="{{ url('Admin') }}" class="nav-link {{ request()->is('Admin') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Tổng quan</span>
            </a>

            <!-- 2. Quản lý người dùng -->
            <a href="{{ url('qlnguoi_dung') }}" class="nav-link {{ request()->is('qlnguoi_dung') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>

            <!-- 3. Chỉnh sửa giao diện -->
            <a href="{{ url('sua_giao_dien') }}" class="nav-link {{ request()->is('sua_giao_dien') ? 'active' : '' }}">
                <i class="fa-solid fa-palette"></i>
                <span>Chỉnh sửa giao diện</span>
            </a>

            <!-- 4. Quản lý tin tức -->
            <a href="{{ url('qltin_tuc') }}" class="nav-link {{ request()->is('qltin_tuc') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i>
                <span>Quản lý tin tức</span>
            </a>

            <!-- 5. Yêu cầu kỷ luật -->
            <a href="{{ url('yeu_cau_ki_luat') }}" class="nav-link {{ request()->is('yeu_cau_ki_luat') ? 'active' : '' }}">
                <i class="fa-solid fa-gavel"></i>
                <span>Yêu cầu kỷ luật</span>
            </a>

            <!-- 6. Lịch sử -->
            <a href="{{ url('lich_su') }}" class="nav-link {{ request()->is('lich_su') ? 'active' : '' }}">
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