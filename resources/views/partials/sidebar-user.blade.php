@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isEnterprise = $user && $user->account_type === 'doanhnghiep';
    $userName = $user ? ($user->account_name ?? $user->username ?? 'Người dùng') : 'Người dùng';
    $initial = strtoupper(substr($userName, 0, 1));
    $isDrawer = $isDrawer ?? false;

    // Đếm số lượng thông báo liên hệ chưa xử lý (pending)
    $pendingContactCount = 0;
    if ($user) {
        $postIds = \App\Models\Post::where('user_id', $user->id)->pluck('_id');
        $pendingContactCount = \App\Models\Contact::whereIn('post_id', $postIds)
                                                  ->where('status', 'pending')
                                                  ->count();
    }
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
    <div class="p-4 border-b border-white/10">
        <div class="flex items-center gap-3 bg-transparent rounded-xl p-3 border border-transparent hover:bg-white/5 transition-colors">
            <div class="w-12 h-12 rounded-full bg-white p-0.5 shadow-md overflow-hidden shrink-0">
                <img src="{{ $user->display_avatar }}" alt="{{ $userName }}" class="w-full h-full rounded-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-sm text-white truncate">{{ $userName }}</h3>
                <div class="flex items-center gap-1.5 mt-1">
                    @if($isEnterprise)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">
                            <i class="fa-solid fa-building"></i> Doanh nghiệp
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase bg-slate-700 text-slate-300 border border-slate-600">
                            <i class="fa-solid fa-user"></i> Cá nhân
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-4">Tài khoản</p>
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('user.dashboard') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-chart-pie w-5"></i> Hồ Sơ
        </a>
        
        <a href="/dat-lai-mat-khau" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors">
            <i class="fa-solid fa-key w-5"></i> Đổi mật khẩu
        </a>
        
        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-6">Quản lý</p>
        <a href="{{ url('/Overview') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('Overview*') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-list-check w-5"></i> Quản lý bài đăng
        </a>
        
        <a href="{{ route('quan-ly-van-hanh') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('quan-ly-van-hanh') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-gears w-5"></i> Quản lý vận hành
        </a>
        
        <a href="{{ route('thong-bao-lien-he') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('thong-bao-lien-he') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }} relative">
            <i class="fa-solid fa-envelope-open-text w-5"></i> Thông báo liên hệ
            @if($pendingContactCount > 0)
                <span class="absolute right-4 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $pendingContactCount }}</span>
            @endif
        </a>

        @if(!$isEnterprise)
        <a href="{{ route('nha-dang-thue') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('nha-dang-thue') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-house-user w-5"></i> Nhà đang thuê
        </a>
        @endif

    </nav>

    <!-- Danger & Logout -->
    <div class="p-4 border-t border-white/10 space-y-1">
        <button type="button" class="flex items-center gap-3 px-4 py-3 rounded-xl text-rose-400/70 hover:text-rose-400 hover:bg-rose-500/10 font-semibold transition-all w-full text-left">
            <i class="fa-solid fa-trash-can w-5"></i> Xóa tài khoản
        </button>
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 font-semibold transition-all w-full text-left">
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
