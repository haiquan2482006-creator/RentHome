<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.6);
    }
    
    /* CSS cho thanh điều hướng desktop */
    .nav-link {
        color: #475569;
    }
    .nav-link:hover, .nav-link.active {
        color: var(--tw-after-bg) !important;
    }
    .nav-link::after {
        width: 0;
    }
    .nav-link:hover::after, .nav-link.active::after {
        width: 100% !important;
    }
</style>

@php
    $bName = $themeSettings['brand_name'] ?? 'RentHome';
    $tColor = $themeSettings['theme_color'] ?? '#16a34a';
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

    $navItems = [
        ['url' => url('/#hero'), 'label' => 'Trang chủ', 'active' => Request::is('/')],
        ['url' => url('/baidang'), 'label' => 'Thuê nhà', 'active' => Request::is('baidang')],
        ['url' => url('/#featured'), 'label' => 'Nhà nổi bật', 'active' => false],
        ['url' => url('/#features'), 'label' => 'Ưu điểm', 'active' => false],
        ['url' => url('/#news'), 'label' => 'Tin tức', 'active' => false],
        ['url' => url('/#contact'), 'label' => 'Liên hệ', 'active' => false],
    ];
@endphp

<!-- Header Navigation -->
<header id="navbar"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-4 glass-panel border-b border-slate-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <img src="{{ !empty($themeSettings['logo_url']) ? $themeSettings['logo_url'] : asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
            <div class="flex flex-col">
                <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">
                    {{ $p1 }}<span style="color: {{ $tColor }}">{{ $p2 }}</span>
                </span>
                <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5">
                    {{ $themeSettings['brand_slogan'] ?? 'Thuê nhà ước mơ' }}
                </span>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-8" id="desktop-nav">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] }}"
                    onclick="setActiveNav(this)"
                    class="nav-link relative py-1 text-sm font-semibold transition-colors after:content-[''] after:absolute after:bottom-0 after:left-0 after:h-0.5 after:transition-all after:duration-300 {{ $item['active'] ? 'active' : '' }}"
                    style="--tw-after-bg: {{ $tColor }};">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                @if ((Auth::user()->account_type ?? '') === 'admin' || (Auth::user()->role ?? '') === 'admin')
                    <a href="/Admin"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fa-solid fa-user-gear"></i>Vào trang quản trị
                    </a>
                @endif

                <!-- User Menu Dropdown Container -->
                <div class="relative inline-block text-left" id="user-dropdown-container">
                    <button type="button" id="user-menu-button" onclick="toggleUserDropdown(event)"
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-slate-100/80 transition-all cursor-pointer focus:outline-none">
                        <div
                            class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="flex flex-col text-left leading-tight">
                            <span class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                {{ Auth::user()->username ?? (Auth::user()->account_name ?? 'Thành viên') }}
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                            </span>
                            <span class="text-[10px] font-semibold">
                                @if ((Auth::user()->account_type ?? '') === 'doanhnghiep')
                                    <span class="text-purple-600 font-bold">• Doanh nghiệp</span>
                                @else
                                    <span class="text-sky-600 font-bold">• Cá nhân</span>
                                @endif
                            </span>
                        </div>
                    </button>

                    <!-- Dropdown Menu Items -->
                    <div id="user-dropdown-menu"
                        class="hidden absolute right-0 mt-2 w-60 rounded-2xl bg-white shadow-2xl border border-slate-100 py-2 z-50 animate-fadeIn">

                        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between gap-2">
                            <div class="truncate">
                                <p class="text-[11px] font-medium text-slate-400">Tài khoản đang đăng nhập</p>
                                <p class="text-sm font-bold text-slate-900 truncate">
                                    {{ Auth::user()->email ?? Auth::user()->username }}</p>
                            </div>
                            <div class="shrink-0">
                                @if ((Auth::user()->account_type ?? '') === 'doanhnghiep')
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold text-[10px]">Doanh
                                        nghiệp</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 font-bold text-[10px]">Cá
                                        nhân</span>
                                @endif
                            </div>
                        </div>

                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-circle-user text-brand-600 w-4 text-center"></i>
                            <span>Tài khoản</span>
                        </a>

                        @if ((Auth::user()->account_type ?? 'canhan') !== 'doanhnghiep')
                            <a href="{{ route('nha-dang-thue') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                                <i class="fa-solid fa-house-user text-brand-600 w-4 text-center"></i>
                                <span>Nhà đang thuê</span>
                            </a>
                        @endif

                        <a href="{{ url('Overview') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-newspaper text-brand-600 w-4 text-center"></i>
                            <span>Quản lý bài đăng</span>
                        </a>

                        <a href="{{ url('quan-ly-van-hanh') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-gears text-brand-600 w-4 text-center"></i>
                            <span>Quản lý vận hành</span>
                        </a>

                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-envelope-open-text text-brand-600 w-4 text-center"></i>
                            <span>Thông báo liên hệ</span>
                        </a>

                        <a href="/dat-lai-mat-khau"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-key text-brand-600 w-4 text-center"></i>
                            <span>Đổi mật khẩu</span>
                        </a>

                        <div class="my-1 border-t border-slate-100"></div>

                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">
                    <i class="fa-regular fa-user"></i> Đăng nhập
                </a>
                <a href="/Register"
                    class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-user-plus" style="color: {{ $tColor }}"></i> Đăng ký
                </a>
            @endauth
            
            <!-- Mobile Menu Toggle Button -->
            <button type="button" onclick="toggleMobileNav()" class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors focus:outline-none ml-1">
                <i class="fa-solid fa-bars text-lg" id="mobile-nav-icon"></i>
            </button>
        </div>

    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-nav-menu" class="hidden md:hidden bg-white/95 backdrop-blur-xl border-t border-slate-200 absolute w-full left-0 top-full shadow-2xl pb-4">
        <nav class="flex flex-col px-4 py-4 space-y-1.5">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] }}" onclick="toggleMobileNav()"
                    class="block px-4 py-2.5 rounded-xl text-base font-semibold transition-colors"
                    style="{{ $item['active'] ? 'color: ' . $tColor . '; background-color: ' . $tColor . '15;' : 'color: #475569;' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            
            @guest
                <div class="h-px bg-slate-100 my-2"></div>
                <a href="/login" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fa-regular fa-user w-5 text-center"></i> Đăng nhập
                </a>
                <a href="/Register" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fa-solid fa-user-plus w-5 text-center" style="color: {{ $tColor }}"></i> Đăng ký
                </a>
            @endguest
        </nav>
    </div>
</header>

<script>
    // Xử lý giữ thanh gạch chân khi click trên desktop
    function setActiveNav(element) {
        const navLinks = document.querySelectorAll('#desktop-nav .nav-link');
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        element.classList.add('active');
    }

    // Xử lý cuộn chuột tới đâu sáng tới đó (Scrollspy)
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#desktop-nav .nav-link');
        
        window.addEventListener('scroll', function() {
            let currentId = '';
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href.includes('#')) {
                    const id = href.split('#')[1];
                    const section = document.getElementById(id);
                    if (section) {
                        const sectionTop = section.offsetTop;
                        // Trừ hao 150px cho chiều cao của header cố định
                        if (window.scrollY >= sectionTop - 150) {
                            currentId = id;
                        }
                    }
                }
            });

            // Nếu cuộn chạm đáy trang, luôn chọn phần tử cuối (Liên hệ)
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
                const lastLink = Array.from(navLinks).filter(link => link.getAttribute('href').includes('#')).pop();
                if (lastLink) {
                    currentId = lastLink.getAttribute('href').split('#')[1];
                }
            }
            
            if (currentId) {
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href.includes('#')) {
                        if (href.endsWith('#' + currentId)) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    }
                });
            }
        });
    });

    function toggleMobileNav() {
        const menu = document.getElementById('mobile-nav-menu');
        const icon = document.getElementById('mobile-nav-icon');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    }

    function toggleUserDropdown(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('user-dropdown-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(event) {
        const container = document.getElementById('user-dropdown-container');
        const menu = document.getElementById('user-dropdown-menu');
        if (container && menu && !container.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
</script>