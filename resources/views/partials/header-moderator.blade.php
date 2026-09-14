<!-- Sidebar Navigation dành riêng cho Kiểm Duyệt Viên (Moderator) -->
<style>
    .mod-sidebar {
        width: var(--sidebar-width, 260px);
        background-color: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1000;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.03);
    }

    .mod-sidebar-header {
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #e2e8f0;
    }

    .mod-logo-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .mod-brand-title {
        font-weight: 800;
        font-size: 1.1rem;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mod-badge-header {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 2px 6px;
        background: rgba(2, 132, 199, 0.1);
        color: #0284c7;
        border-radius: 4px;
        border: 1px solid rgba(2, 132, 199, 0.2);
    }

    .mod-sidebar-menu {
        padding: 20px 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .mod-menu-title {
        padding: 0 12px 6px 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
    }

    .mod-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .mod-nav-link i {
        width: 20px;
        text-align: center;
        font-size: 1.05rem;
        color: #94a3b8;
    }

    .mod-nav-link:hover {
        color: #0284c7;
        background-color: rgba(2, 132, 199, 0.08);
    }

    .mod-nav-link:hover i {
        color: #0284c7;
    }

    .mod-nav-link.active {
        background-color: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
    }

    .mod-nav-link.active i {
        color: #ffffff;
    }

    .mod-sidebar-footer {
        padding: 12px;
        margin: 12px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .mod-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }

    .mod-user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #e0f2fe;
        color: #0284c7;
        font-weight: 800;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(2, 132, 199, 0.2);
        flex-shrink: 0;
    }

    .mod-user-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .mod-user-email {
        font-size: 0.7rem;
        color: #64748b;
        line-height: 1.2;
    }

    .mod-btn-logout {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 0.95rem;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .mod-btn-logout:hover {
        color: #dc2626;
        background-color: rgba(220, 38, 38, 0.08);
    }
</style>

<aside class="mod-sidebar">
    <div>
        <div class="mod-sidebar-header">
            <div class="mod-logo-icon" style="background: none; box-shadow: none;">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" style="width: 44px; height: 44px; object-fit: contain; border-radius: 10px;">
            </div>
            <div>
                <div class="mod-brand-title">
                    RentHome <span class="mod-badge-header">Kiểm duyệt</span>
                </div>
                <div style="font-size: 0.75rem; color: #64748b;">Hệ thống Kiểm duyệt độc lập</div>
            </div>
        </div>

        <nav class="mod-sidebar-menu">
            <div class="mod-menu-title">Chức năng kiểm duyệt</div>

            <!-- 1. Tổng quan -->
            <a href="{{ url('Moderator') }}" class="mod-nav-link {{ request()->is('Moderator') || request()->is('moderator') ? 'active' : '' }}" title="Tổng quan">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Tổng quan</span>
            </a>

            <!-- 2. Bài đăng phê duyệt -->
            <a href="{{ url('moderator/qlpheduyet') }}" class="mod-nav-link {{ request()->is('moderator/qlpheduyet') ? 'active' : '' }}" title="Bài đăng phê duyệt">
                <i class="fa-solid fa-file-signature"></i>
                <span>Bài đăng phê duyệt</span>
            </a>

            <!-- 3. Quản lý tài khoản -->
            <a href="{{ url('moderator/qlnguoidung') }}" class="mod-nav-link {{ request()->is('moderator/qlnguoidung') ? 'active' : '' }}" title="Quản lý tài khoản">
                <i class="fa-solid fa-users-gear"></i>
                <span>Quản lý tài khoản</span>
            </a>

            <!-- 4. Xử lý khiếu nại -->
            <a href="{{ url('moderator/qlkhieunai') }}" class="mod-nav-link {{ request()->is('moderator/qlkhieunai') ? 'active' : '' }}" title="Xử lý khiếu nại">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Xử lý khiếu nại</span>
            </a>

            <!-- 5. Lịch sử -->
            <a href="{{ url('moderator/lichsu') }}" class="mod-nav-link {{ request()->is('moderator/lichsu') ? 'active' : '' }}" title="Lịch sử">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Lịch sử</span>
            </a>
        </nav>
    </div>

    <div class="mod-sidebar-footer">
        @php
            $currentUser = Auth::user();
            $name = $currentUser ? ($currentUser->account_name ?? $currentUser->username ?? 'Kiểm Duyệt Viên') : 'Kiểm Duyệt Viên';
            $email = $currentUser ? $currentUser->email : 'kiemduyet@gmail.com';
            $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
        @endphp
        <div class="mod-user-info">
            <div class="mod-user-avatar">{{ $initials }}</div>
            <div>
                <div class="mod-user-name">{{ $name }}</div>
                <div class="mod-user-email">{{ $email }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" title="Đăng xuất" class="mod-btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>
</aside>
