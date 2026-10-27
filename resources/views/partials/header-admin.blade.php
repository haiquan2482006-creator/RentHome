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

    /* Dropdown & Submenu cho Menu Tài chính & Kế toán */
    .nav-dropdown {
        display: flex;
        flex-direction: column;
    }

    .nav-dropdown-toggle {
        width: 100%;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
        position: relative;
    }

    .nav-arrow {
        margin-left: auto;
        font-size: 0.72rem;
        color: var(--text-muted);
        transition: transform 0.25s ease;
        flex-shrink: 0;
    }

    .nav-dropdown.open .nav-arrow {
        transform: rotate(180deg);
        color: var(--brand-primary);
    }

    .nav-dropdown-toggle.active .nav-arrow {
        color: #ffffff;
    }

    .nav-submenu {
        display: none;
        flex-direction: column;
        gap: 3px;
        margin: 4px 0 6px 18px;
        padding-left: 10px;
        border-left: 2px solid #e2e8f0;
        transition: var(--transition);
    }

    .nav-dropdown.open .nav-submenu {
        display: flex;
    }

    .nav-sublink {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: none;
        transition: var(--transition);
        white-space: nowrap;
    }

    .nav-sublink i {
        font-size: 0.95rem;
        width: 16px;
        text-align: center;
        color: var(--text-muted);
        transition: var(--transition);
    }

    .nav-sublink:hover {
        color: var(--brand-primary);
        background-color: var(--brand-bg);
    }

    .nav-sublink:hover i {
        color: var(--brand-primary);
    }

    .nav-sublink.active {
        background-color: var(--brand-primary);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);
    }

    .nav-sublink.active i {
        color: #ffffff;
    }

    .menu-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 10px;
        margin-left: auto;
        line-height: 1.4;
    }

    .menu-badge.badge-amber {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .menu-badge.badge-emerald {
        background-color: #d1fae5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .menu-badge.badge-rose {
        background-color: #ffe4e6;
        color: #e11d48;
        border: 1px solid #fecdd3;
    }

    body.sidebar-collapsed .nav-arrow,
    body.sidebar-collapsed .nav-submenu,
    body.sidebar-collapsed .menu-badge {
        display: none !important;
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
                @php
                    $isCskhUser = Auth::check() && method_exists(Auth::user(), 'isCskh') && Auth::user()->isCskh();
                @endphp
                <span class="brand-name">RentHome<span class="admin-badge">{{ $isCskhUser ? 'CSKH' : 'Admin' }}</span></span>
                <span class="brand-subtext">{{ $isCskhUser ? 'Trung tâm CSKH' : 'Hệ thống quản trị' }}</span>
            </div>

            <!-- Nút Thu Gọn / Mở Rộng Sidebar -->
            <button type="button" id="sidebar-toggle-btn" class="sidebar-toggle-btn" title="Thu gọn / Mở rộng Sidebar" onclick="toggleSidebarMenu()">
                <i class="fa-solid fa-chevron-left" id="sidebar-toggle-icon"></i>
            </button>
        </div>

        <!-- Danh sách Menu -->
        <nav class="sidebar-menu">
            @php
                // Đếm tin nhắn liên hệ chờ xử lý
                $badgeContactPendingCount = 0;
                try {
                    $badgeContactPendingCount = \App\Models\ContactMessage::where('status', 'pending')->count();
                } catch (\Exception $e) {}
                $isContactActive = request()->is('*khach-hang-lien-he*') || request()->is('*contact-message*') || request()->is('Admin/khach-hang-lien-he*');

                // Đếm khiếu nại chờ xử lý
                $badgeComplaintPendingCount = 0;
                try {
                    $badgeComplaintPendingCount = \App\Models\Complaint::where('status', 'pending')->count();
                } catch (\Exception $e) {}
                $isComplaintActive = request()->is('cskh/khieu-nai*') || request()->is('*khieu-nai*') || request()->is('*complaint*') || request()->is('Admin/khieu-nai*');

                // Đếm khiếu nại Cấp 2 (Leo thang) chưa xử lý dành cho Admin
                $badgeEscalatedCount = 0;
                try {
                    $badgeEscalatedCount = \App\Models\SupportTicket::where(function ($q) {
                            $q->where('is_escalated', true)
                              ->orWhere('is_escalated', 1)
                              ->orWhere('is_escalated', 'true')
                              ->orWhere('is_escalated', '1');
                        })
                        ->where('status', '!=', 'resolved')
                        ->count();
                } catch (\Exception $e) {}
                $isEscalatedActive = request()->is('admin/khieu-nai-cap-2*') || request()->is('Admin/khieu-nai-cap-2*') || request()->is('Admin/khieu_nai_cap_2*');
            @endphp

            @if($isCskhUser)
                <!-- ==================== MENU DÀNH RIÊNG CHO CHĂM SÓC KHÁCH HÀNG (CSKH) ==================== -->
                <div class="menu-title">Chăm sóc khách hàng</div>

                <!-- 1. Quản lý Khiếu nại -->
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link {{ $isComplaintActive ? 'active' : '' }}" title="Quản lý Khiếu nại">
                    <i class="fa-solid fa-triangle-exclamation" style="color: {{ $isComplaintActive ? '#ffffff' : '#f59e0b' }};"></i>
                    <span>Quản lý Khiếu nại</span>
                    @if($badgeComplaintPendingCount > 0)
                        <span class="menu-badge badge-amber" style="background-color: #f59e0b; color: #ffffff; border: 1px solid #d97706; box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);" title="{{ $badgeComplaintPendingCount }} đơn khiếu nại chưa xử lý">{{ $badgeComplaintPendingCount }}</span>
                    @endif
                </a>

                <!-- 2. Thông báo hệ thống -->
                <a href="{{ url('thong_bao') }}" class="nav-link {{ request()->is('thong_bao*') || request()->is('Admin/thong-bao*') ? 'active' : '' }}" title="Thông báo">
                    <i class="fa-solid fa-bell" style="color: #f59e0b;"></i>
                    <span>Thông báo</span>
                </a>
            @else
                <!-- ==================== MENU ĐẦY ĐỦ CHO QUẢN TRỊ VIÊN (ADMIN) ==================== -->
                <div class="menu-title">Quản lý Doanh nghiệp</div>

                <!-- 1. Tổng quan hệ thống Admin -->
                <a href="{{ url('Admin') }}" class="nav-link {{ (request()->is('Admin') || request()->is('admin') || request()->is('Admin/overview*') || request()->is('admin/overview*')) ? 'active' : '' }}" title="Tổng quan hệ thống">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Tổng quan</span>
                </a>

                <!-- 2. Quản lý Bất Động Sản & Tòa Nhà -->
                <a href="{{ url('qltoa_nha') }}" class="nav-link {{ request()->is('qltoa_nha*') || request()->is('Admin/toa-nha*') || request()->is('Admin/chi-tiet-toa-nha*') || request()->is('admin/chi-tiet-toa-nha*') || request()->is('quan-ly-toa-nha*') ? 'active' : '' }}" title="Quản lý BĐS & Tòa Nhà">
                    <i class="fa-solid fa-city" style="color: #38bdf8;"></i>
                    <span>Quản lý Tòa nhà</span>
                </a>

                <!-- 3. Quản lý Tài chính & Kế toán -->
                @php
                    $isFinanceActive = request()->is('*tai-chinh*') || request()->is('*duyet-chuyen-khoan*') || request()->is('*cong-no-nhan-vien*') || request()->is('*duyet-hoan-coc*') || request()->is('*refund*') || request()->is('*finance*');
                    $isBankActive = request()->is('*duyet-chuyen-khoan*') || request()->is('*bank-transfer*');
                    $isDebtActive = request()->is('*cong-no-nhan-vien*') || request()->is('*staff-debt*');
                    $isRefundActive = request()->is('*duyet-hoan-coc*') || request()->is('*refund-request*') || request()->is('*hoan-coc*');
                    
                    $badgePendingCount = 0;
                    $badgeDebtCount = 0;
                    $badgeRefundCount = 0;
                    try {
                        $badgePendingCount = \App\Models\Invoice::where('payment_method', 'bank_transfer')
                            ->where(function($q) {
                                $q->where('status', 'pending_approval')->orWhere('payment_status', 'pending_approval');
                            })->count();
                        $badgeDebtCount = \App\Models\Invoice::where('payment_method', 'cash')
                            ->where(function($q) {
                                $q->where('status', 'paid')->orWhere('payment_status', 'paid');
                            })
                            ->where(function($q) {
                                $q->where('is_remitted_to_admin', false)->orWhere('is_remitted_to_admin', 0)->orWhereNull('is_remitted_to_admin');
                            })->count();
                        $badgeRefundCount = \App\Models\Contract::where('status', 'pending_refund')->count();
                    } catch (\Exception $e) {}
                    
                    $totalPendingFinance = $badgePendingCount + $badgeRefundCount;
                @endphp
                <div class="nav-dropdown {{ $isFinanceActive ? 'open' : '' }}" id="finance-nav-dropdown">
                    <button type="button" class="nav-link nav-dropdown-toggle {{ $isFinanceActive ? 'active' : '' }}" onclick="toggleFinanceDropdown(event)" title="Tài chính & Kế toán">
                        <i class="fa-solid fa-wallet" style="color: {{ $isFinanceActive ? '#ffffff' : '#10b981' }};"></i>
                        <span>Tài chính & Kế toán</span>
                        @if($totalPendingFinance > 0)
                            <span class="menu-badge badge-amber" style="margin-right: 4px;">{{ $totalPendingFinance }}</span>
                        @endif
                        <i class="fa-solid fa-chevron-down nav-arrow"></i>
                    </button>
                    <div class="nav-submenu">
                        <a href="{{ route('admin.finance.bank_transfers') }}" class="nav-sublink {{ $isBankActive ? 'active' : '' }}" title="Duyệt chuyển khoản">
                            <i class="fa-solid fa-building-columns"></i>
                            <span>Duyệt chuyển khoản</span>
                            @if($badgePendingCount > 0)
                                <span class="menu-badge badge-amber">{{ $badgePendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.finance.staff_debts') }}" class="nav-sublink {{ $isDebtActive ? 'active' : '' }}" title="Công nợ nhân viên">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            <span>Công nợ nhân viên</span>
                            @if($badgeDebtCount > 0)
                                <span class="menu-badge badge-emerald">{{ $badgeDebtCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.finance.refund_requests') }}" class="nav-sublink {{ $isRefundActive ? 'active' : '' }}" title="Duyệt Hoàn Cọc">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Duyệt Hoàn Cọc</span>
                            @if($badgeRefundCount > 0)
                                <span class="menu-badge badge-rose">{{ $badgeRefundCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- 4. Quản lý tài khoản con / Nhân viên -->
                <a href="{{ url('qltai_khoan_con') }}" class="nav-link {{ request()->is('qltai_khoan_con*') || request()->is('Admin/tai-khoan-con*') ? 'active' : '' }}" title="Tài khoản con & Phân quyền">
                    <i class="fa-solid fa-users-gear" style="color: #2dd4bf;"></i>
                    <span>Nhân viên & Phân quyền</span>
                </a>

                <!-- 5. Quản lý Tài khoản cá nhân / Khách thuê -->
                <a href="{{ url('qltaikhoan_ca_nhan') }}" class="nav-link {{ request()->is('qltaikhoan_ca_nhan*') || request()->is('Admin/tai-khoan-ca-nhan*') || request()->is('qltaikhoan-ca-nhan*') ? 'active' : '' }}" title="Tài khoản cá nhân (Khách thuê)">
                    <i class="fa-solid fa-users" style="color: #0ea5e9;"></i>
                    <span>Tài khoản Khách thuê</span>
                </a>


                <!-- 7.1. Khiếu nại nghiêm trọng (Leo thang - Tòa án Admin) -->
                <a href="{{ route('admin.tickets.escalated') }}" class="nav-link {{ $isEscalatedActive ? 'active' : '' }}" title="Khiếu nại nghiêm trọng">
                    <i class="fa-solid fa-gavel" style="color: {{ $isEscalatedActive ? '#ffffff' : '#dc2626' }};"></i>
                    <span>Khiếu nại nghiêm trọng</span>
                    @if($badgeEscalatedCount > 0)
                        <span class="menu-badge badge-rose" style="background-color: #dc2626; color: #ffffff; border: 1px solid #b91c1c; box-shadow: 0 0 10px rgba(220, 38, 38, 0.4);" title="{{ $badgeEscalatedCount }} khiếu nại nghiêm trọng cần Admin giải quyết">{{ $badgeEscalatedCount }}</span>
                    @endif
                </a>

                <!-- 8. Chỉnh sửa giao diện website -->
                <a href="{{ url('sua_giao_dien') }}" class="nav-link {{ request()->is('sua_giao_dien') ? 'active' : '' }}" title="Quản lý giao diện">
                    <i class="fa-solid fa-palette" style="color: #ec4899;"></i>
                    <span>Cấu hình giao diện</span>
                </a>

                <!-- 9. Lịch sử tài khoản -->
                <a href="{{ url('lich_su') }}" class="nav-link {{ request()->is('lich_su*') || request()->is('Admin/lich-su*') ? 'active' : '' }}" title="Lịch sử">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #8b5cf6;"></i>
                    <span>Lịch sử</span>
                </a>

                <!-- 10. Quản lý Thông báo -->
                <a href="{{ url('thong_bao') }}" class="nav-link {{ request()->is('thong_bao*') || request()->is('Admin/thong-bao*') ? 'active' : '' }}" title="Thông báo">
                    <i class="fa-solid fa-bell" style="color: #f59e0b;"></i>
                    <span>Thông báo</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- Thông tin User Admin / Đăng xuất -->
    <div class="sidebar-footer">
        <div class="user-profile">
            @php
                $adminUser = Auth::user();
                $adminName = $adminUser ? ($adminUser->account_name ?? $adminUser->username ?? 'Admin') : 'Admin';
                if ($adminUser && method_exists($adminUser, 'isCskh') && $adminUser->isCskh() && empty($adminUser->account_name)) {
                    $adminName = 'Chăm sóc khách hàng';
                }
                $adminEmail = $adminUser->email ?? 'admin2006@gmail.com';
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

    // Toàn cục: Ngăn chặn cuộn chuột (wheel) làm thay đổi giá trị trong các thẻ input type="number"
    document.addEventListener('wheel', function(e) {
        if (e.target && e.target.matches && e.target.matches('input[type="number"]')) {
            e.preventDefault();
            e.target.blur();
        }
    }, { passive: false });

    // Xử lý bật/tắt dropdown Tài chính & Kế toán
    function toggleFinanceDropdown(event) {
        if (event) event.preventDefault();
        const dropdown = document.getElementById('finance-nav-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('open');
        }
    }
</script>