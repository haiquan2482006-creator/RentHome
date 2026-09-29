@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isEnterprise = $user && $user->account_type === 'doanhnghiep';
    $userName = $user ? ($user->account_name ?? $user->username ?? 'Người dùng') : 'Người dùng';
    $initial = strtoupper(substr($userName, 0, 1));
    $isDrawer = $isDrawer ?? false;
@endphp

@if($isDrawer)
<!-- Overlay for Drawer -->
<div id="global-sidebar-overlay" onclick="toggleGlobalSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[60] hidden transition-opacity opacity-0 duration-300"></div>
@endif

<!-- Sidebar -->
<aside id="global-sidebar-user" class="w-72 bg-slate-900 text-white flex flex-col shrink-0 shadow-2xl h-full {{ $isDrawer ? 'fixed top-0 left-0 z-[70] transform -translate-x-full transition-transform duration-300 ease-in-out' : 'hidden md:flex relative z-20' }}">
    <!-- Brand -->
    <div class="h-20 flex items-center justify-between px-8 border-b border-white/10">
        <a href="{{ url('/') }}" class="text-xl font-black flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <i class="fa-solid fa-house-chimney text-white text-lg"></i>
            </div>
            <span>Rent<span class="text-brand-500">Home</span></span>
        </a>
        @if($isDrawer)
        <button onclick="toggleGlobalSidebar()" class="text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
        @endif
    </div>

    <!-- User Profile Mini -->
    <div class="p-8 border-b border-white/10 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-brand-500/10 to-transparent"></div>
        <div class="relative z-10">
            <div class="w-20 h-20 mx-auto rounded-full bg-white p-1 mb-4 shadow-xl">
                <div class="w-full h-full rounded-full bg-brand-100 flex items-center justify-center text-3xl font-black text-brand-600">
                    {{ $initial }}
                </div>
            </div>
            <h3 class="font-bold text-lg text-white mb-1">{{ $userName }}</h3>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase {{ $isEnterprise ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-slate-700 text-slate-300 border border-slate-600' }}">
                @if($isEnterprise)
                    <i class="fa-solid fa-building"></i> Doanh nghiệp
                @else
                    <i class="fa-solid fa-user"></i> Cá nhân
                @endif
            </span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-4">Tổng quan</p>
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('user.dashboard') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-chart-pie w-5"></i> Bảng điều khiển
        </a>

        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-8">Quản lý & Vận hành</p>
        
        @if(!$isEnterprise)
        <a href="{{ route('nha-dang-thue') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('nha-dang-thue') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-house-user w-5"></i> Nhà đang thuê
        </a>
        @endif

        <a href="{{ url('Overview') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('Overview') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-newspaper w-5"></i> Quản lý bài đăng
        </a>
        
        @if(!request()->is('Overview'))
        <a href="{{ route('dangbai') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dangbai') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-plus-circle w-5"></i> Đăng bài mới
        </a>
        @endif

        <a href="{{ url('quan-ly-van-hanh') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('quan-ly-van-hanh') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-gears w-5"></i> Quản lý vận hành
        </a>
        
        @if($isEnterprise)
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors">
            <i class="fa-solid fa-city w-5"></i> Quản lý Tòa nhà
        </a>
        @endif

        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-8">Tài khoản</p>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors relative">
            <i class="fa-solid fa-envelope-open-text w-5"></i> Thông báo liên hệ
            <span class="absolute right-4 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">3</span>
        </a>
        <a href="/dat-lai-mat-khau" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors">
            <i class="fa-solid fa-key w-5"></i> Đổi mật khẩu
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-white/10">
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl text-rose-400 hover:bg-rose-500/10 font-semibold transition-colors w-full text-left">
                <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Đăng xuất
            </button>
        </form>
    </div>
</aside>

@if($isDrawer)
<script>
    function toggleGlobalSidebar() {
        const sidebar = document.getElementById('global-sidebar-user');
        const overlay = document.getElementById('global-sidebar-overlay');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            // Open
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            // small delay for transition
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
            }, 10);
        } else {
            // Close
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }
</script>
@endif
