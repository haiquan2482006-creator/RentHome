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
        --sidebar-collapsed-width: 78px;
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
        padding: 20px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--border-color);
        position: relative;
        transition: var(--transition);
    }

    .logo-icon {
        width: 48px;
        height: 48px;
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

    .logo-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 2px 6px rgba(2, 132, 199, 0.2));
    }

    .logo-details {
        display: flex;
        flex-direction: column;
        transition: var(--transition);
        white-space: nowrap;
        overflow: hidden;
    }

    .brand-name {
        font-weight: 800;
        font-size: 1.15rem;
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

    /* Nút Thu Gọn / Mở Rộng Sidebar (Toggle Button) */
    .sidebar-toggle-btn {
        position: absolute;
        right: -14px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        z-index: 1001;
        transition: var(--transition);
    }

    .sidebar-toggle-btn:hover {
        background-color: var(--brand-primary);
        color: #ffffff;
        border-color: var(--brand-primary);
        transform: translateY(-50%) scale(1.1);
    }

    /* Danh sách Menu */
    .sidebar-menu {
        padding: 20px 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        overflow-x: hidden;
    }

    .menu-title {
        padding: 0 12px 6px 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        white-space: nowrap;
        transition: var(--transition);
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
        white-space: nowrap;
    }

    .nav-link i {
        width: 20px;
        text-align: center;
        font-size: 1.05rem;
        color: var(--text-muted);
        transition: var(--transition);
        flex-shrink: 0;
    }

    .nav-link span {
        transition: var(--transition);
    }

    .nav-link:hover {
        color: var(--brand-primary);
        background-color: var(--brand-bg);
    }

    .nav-link:hover i {
        color: var(--brand-primary);
    }

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
        padding: 12px;
        margin: 12px;
        background-color: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: var(--transition);
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow: hidden;
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
        flex-shrink: 0;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        white-space: nowrap;
        transition: var(--transition);
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
        flex-shrink: 0;
    }

    .btn-logout:hover {
        color: var(--accent-red);
        background-color: rgba(220, 38, 38, 0.08);
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

    .btn-view-website {
        background-color: rgba(2, 132, 199, 0.08);
        color: var(--brand-primary) !important;
        border: 1px solid rgba(2, 132, 199, 0.2);
        font-weight: 700 !important;
        margin-top: 4px;
    }

    .btn-view-website i {
        color: var(--brand-primary) !important;
    }

    .btn-view-website:hover {
        background-color: var(--brand-primary) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
    }

    /* --- Vùng nội dung chính (Main Content) --- */
    .admin-main {
        margin-left: var(--sidebar-width, 260px);
        width: calc(100% - var(--sidebar-width, 260px));
        flex: 1;
        min-height: 100vh;
        background-color: var(--bg-main, #f4f6fa);
        transition: var(--transition);
    }

    /* ============================================================ */
    /* TRẠNG THÁI SIDEBAR THU GỌN (COLLAPSED MODE)                 */
    /* ============================================================ */
    body.sidebar-collapsed {
        --sidebar-width: 78px;
    }

    body.sidebar-collapsed .admin-sidebar {
        width: 78px;
    }

    body.sidebar-collapsed .logo-details,
    body.sidebar-collapsed .brand-subtext,
    body.sidebar-collapsed .menu-title,
    body.sidebar-collapsed .nav-link span,
    body.sidebar-collapsed .user-details {
        opacity: 0;
        visibility: hidden;
        width: 0;
        display: none !important;
    }

    body.sidebar-collapsed .sidebar-header {
        padding: 16px 14px;
        justify-content: center;
    }

    body.sidebar-collapsed .logo-icon {
        width: 40px;
        height: 40px;
    }

    body.sidebar-collapsed .nav-link {
        justify-content: center;
        padding: 12px 10px;
    }

    body.sidebar-collapsed .nav-link i {
        font-size: 1.2rem;
    }

    body.sidebar-collapsed .sidebar-footer {
        justify-content: center;
        padding: 10px 4px;
        margin: 8px 6px;
    }

    body.sidebar-collapsed .admin-main {
        margin-left: 78px !important;
        width: calc(100% - 78px) !important;
    }
</style>

<aside class="admin-sidebar" id="admin-sidebar">
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

            <!-- Nút Thu Gọn / Mở Rộng Sidebar -->
            <button type="button" id="sidebar-toggle-btn" class="sidebar-toggle-btn" title="Thu gọn / Mở rộng Sidebar" onclick="toggleSidebarMenu()">
                <i class="fa-solid fa-chevron-left" id="sidebar-toggle-icon"></i>
            </button>
        </div>

        <!-- Danh sách Menu -->
        <nav class="sidebar-menu">
            <div class="menu-title">Danh mục quản lý</div>

            <!-- 1. Tổng quan -->
            <a href="{{ url('Admin') }}" class="nav-link {{ request()->is('Admin') ? 'active' : '' }}" title="Tổng quan">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Tổng quan</span>
            </a>

            <!-- 2. Quản lý người dùng -->
            <a href="{{ url('qlnguoi_dung') }}" class="nav-link {{ request()->is('qlnguoi_dung') ? 'active' : '' }}" title="Quản lý người dùng">
                <i class="fa-solid fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>

            <!-- 3. Chỉnh sửa giao diện -->
            <a href="{{ url('sua_giao_dien') }}" class="nav-link {{ request()->is('sua_giao_dien') ? 'active' : '' }}" title="Quản lý giao diện">
                <i class="fa-solid fa-palette"></i>
                <span>Quản lý giao diện</span>
            </a>

            <!-- 4. Quản lý tin tức -->
            <a href="{{ url('qltin_tuc') }}" class="nav-link {{ request()->is('qltin_tuc') ? 'active' : '' }}" title="Quản lý tin tức">
                <i class="fa-solid fa-newspaper"></i>
                <span>Quản lý tin tức</span>
            </a>

            <!-- 5. Yêu cầu kỷ luật -->
            <a href="{{ url('yeu_cau_ki_luat') }}" class="nav-link {{ request()->is('yeu_cau_ki_luat') ? 'active' : '' }}" title="Yêu cầu kỷ luật">
                <i class="fa-solid fa-gavel"></i>
                <span>Yêu cầu kỷ luật</span>
            </a>

            <!-- 6. Lịch sử -->
            <a href="{{ url('lich_su') }}" class="nav-link {{ request()->is('lich_su') ? 'active' : '' }}" title="Lịch sử">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Lịch sử</span>
            </a>

            <div class="menu-title" style="margin-top: 14px;">Lối tắt Website</div>
            <!-- Xem giao diện -->
            <a href="{{ url('/') }}" class="nav-link btn-view-website" title="Xem giao diện người dùng Website">
                <i class="fa-solid fa-globe"></i>
                <span>Xem giao diện</span>
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

<!-- SCRIPT XỬ LÝ THU GỌN / MỞ RỘNG SIDEBAR CHUNG CHO CÁC TRANG -->
<script>
    function toggleSidebarMenu() {
        document.body.classList.toggle('sidebar-collapsed');
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        
        updateToggleIcon(isCollapsed);
    }

    function updateToggleIcon(isCollapsed) {
        const icon = document.getElementById('sidebar-toggle-icon');
        if (icon) {
            if (isCollapsed) {
                icon.className = 'fa-solid fa-chevron-right';
            } else {
                icon.className = 'fa-solid fa-chevron-left';
            }
        }
    }

    // Tự động khôi phục trạng thái Thu gọn / Mở rộng từ localStorage
    (function() {
        const savedState = localStorage.getItem('admin_sidebar_collapsed');
        if (savedState === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        updateToggleIcon(isCollapsed);
    });
</script>