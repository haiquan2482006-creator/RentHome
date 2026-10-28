@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $isStaff = $user && ($user->isSubAccount() || $user->isStaff() || in_array($user->role ?? '', ['nhansu', 'nhan su', 'nhan_su', 'sub_account', 'staff']));
    $isEnterprise = $user && ($user->isEnterprise() || in_array($user->role ?? '', ['doanhnghiep', 'enterprise'])) && !$isStaff;
    $userName = $user ? ($isStaff ? ($user->account_name ?? $user->username ?? 'Nhân sự') : ($isEnterprise ? ($user->representative ?? $user->account_name ?? $user->company_name ?? $user->username ?? 'Doanh nghiệp') : ($user->account_name ?? $user->username ?? 'Người dùng'))) : 'Người dùng';
    $initial = mb_strtoupper(mb_substr($userName, 0, 1, 'UTF-8'), 'UTF-8');
    $isDrawer = $isDrawer ?? false;

    $themeSettings = $themeSettings ?? (file_exists(storage_path('app/theme_settings.json')) 
        ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true) 
        : []);
    $webLogo = !empty($themeSettings['logo_url']) ? $themeSettings['logo_url'] : asset('img/logo.png');

    $bName = $themeSettings['brand_name'] ?? 'RentHome';
    if (str_ends_with(strtolower($bName), 'home')) {
        $p1 = substr($bName, 0, strlen($bName) - 4);
        $p2 = 'Home';
    } elseif (strlen($bName) > 4) {
        $mid = floor(strlen($bName) / 2);
        $p1 = substr($bName, 0, $mid);
        $p2 = substr($bName, $mid);
    } else {
        $p1 = $bName;
        $p2 = '';
    }

    // Đếm số lượng thông báo liên hệ chưa xử lý (pending)
    $pendingContactCount = 0;
    if ($user) {
        $postIds = \App\Models\Post::where('user_id', $user->id)->pluck('_id');
        $pendingContactCount = \App\Models\Contact::whereIn('post_id', $postIds)
                                                  ->where(function($q) {
                                                      $q->whereNull('is_hidden')->orWhere('is_hidden', '!=', true);
                                                  })
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
        <a href="{{ Auth::check() && (Auth::user()->isModerator() || in_array(Auth::user()->role ?? '', ['kiemduyet', 'moderator', 'kiem_duyet'])) ? url('/kiem-duyet/tong-quan') : url('/') }}" class="text-xl font-black flex items-center gap-3 hover:opacity-80 transition-opacity group">
            <img src="{{ $webLogo }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-md group-hover:scale-105 transition-transform">
            <span>{{ $p1 }}<span class="text-brand-500">{{ $p2 }}</span></span>
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
                <img src="{{ $user ? ($user->display_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName)) : 'https://ui-avatars.com/api/?name=' . urlencode($userName) }}" alt="{{ $userName }}" class="w-full h-full rounded-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-sm text-white truncate">{{ $userName }}</h3>
                <div class="flex items-center gap-1.5 mt-1">
                    @if($isStaff)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <i class="fa-solid fa-user-tie"></i> Nhân sự
                        </span>
                    @elseif($isEnterprise)
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

        @if(!$isEnterprise && !($user && ($user->isAdmin() || $user->isModerator() || $user->isSubAccount() || in_array($user->role ?? '', ['admin', 'kiemduyet', 'moderator', 'doanhnghiep', 'enterprise', 'staff', 'sub_account']))))
        <a href="{{ route('nha-dang-thue') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('nha-dang-thue') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-house-chimney-user w-5 text-emerald-400"></i> Nhà đang thuê
        </a>
        <a href="{{ url('/lich-su-lien-he') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('lich-su-lien-he*') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-clock-rotate-left w-5 text-teal-400"></i> Lịch sử liên hệ
        </a>
        @endif
        
        <a href="{{ route('user.change_password') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('user.change_password') ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-key w-5"></i> Đổi mật khẩu
        </a>

        @if($isStaff || $isEnterprise || ($user && ($user->isAdmin() || $user->role === 'admin')))
        <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 mt-4">Vận hành</p>
        <a href="{{ url('quan-ly-van-hanh') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ (request()->is('quan-ly-van-hanh*') || request()->is('qlvan_hanh/Room_list*')) ? 'bg-brand-600 text-white font-semibold shadow-lg shadow-brand-600/20' : 'text-slate-300 hover:bg-white/5 hover:text-white font-semibold transition-colors' }}">
            <i class="fa-solid fa-gears w-5 text-sky-400"></i> Quản lý vận hành
        </a>
        <!-- Menu con: Thanh lý & Chốt sổ -->
        <a href="{{ url('/qlvan_hanh/thanh_ly_cong_no') }}" class="flex items-center gap-2.5 py-2.5 px-4 pl-8 rounded-xl font-semibold text-xs {{ (request()->is('qlvan_hanh/thanh_ly_cong_no*') || request()->is('thanh-ly-cong-no*')) ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-rose-400' }} transition-colors group">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 group-hover:scale-125 transition-transform"></span>
            <i class="fa-solid fa-calculator w-4 text-center text-rose-400 group-hover:scale-110 transition-transform"></i>
            <span>Thanh lý & Chốt sổ</span>
        </a>
        @endif
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-white/10 space-y-1">
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
