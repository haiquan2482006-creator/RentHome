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

    @keyframes favBounce {
        0% { transform: scale(1); }
        45% { transform: scale(1.35); }
        75% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
    .fav-bounce {
        animation: favBounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
            @if (!Auth::check() || (Auth::user()->account_type ?? 'canhan') !== 'doanhnghiep')
            <!-- Favorites Dropdown Container (Dành riêng cho khách vãng lai và Tài khoản Cá nhân) -->
            <div class="relative inline-block text-left" id="favorite-dropdown-container">
                <button type="button" id="favorite-menu-button" onclick="toggleFavoriteDropdown(event)"
                    class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-rose-50 text-slate-700 hover:text-rose-600 transition-all cursor-pointer focus:outline-none border border-slate-200/80 hover:border-rose-200 bg-white/70 shadow-2xs">
                    <div class="relative flex items-center justify-center">
                        <i class="fa-solid fa-heart text-rose-500 text-sm sm:text-base"></i>
                        <span id="header-favorite-badge"
                            class="hidden absolute -top-2.5 -right-3 bg-rose-500 text-white font-extrabold text-[10px] min-w-[17px] h-[17px] px-1 rounded-full items-center justify-center shadow-xs border-2 border-white">
                            0
                        </span>
                    </div>
                    <span class="text-xs font-bold hidden sm:inline">Yêu thích</span>
                </button>

                <!-- Favorite Dropdown Popover List -->
                <div id="favorite-dropdown-menu"
                    class="hidden absolute right-0 sm:right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl border border-slate-100 py-2.5 z-50 animate-fadeIn">
                    <!-- Dropdown Header -->
                    <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 leading-none">Bài đăng yêu thích</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Danh sách nhà bạn đã quan tâm</p>
                            </div>
                        </div>
                        <span id="header-favorite-count-tag" class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 font-bold text-[10px]">
                            0 bài
                        </span>
                    </div>

                    <!-- Dropdown List Items -->
                    <div id="header-favorite-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 px-2 py-1">
                        <div class="p-6 text-center text-slate-400">
                            <i class="fa-solid fa-circle-notch fa-spin text-brand-600 text-lg"></i>
                            <p class="text-xs mt-2">Đang tải...</p>
                        </div>
                    </div>

                    <!-- Dropdown Footer -->
                    <div class="pt-2 px-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-400">Bấm bài đăng để xem chi tiết</span>
                        <a href="{{ url('/baidang') }}" class="font-bold text-brand-600 hover:text-brand-700 hover:underline flex items-center gap-1 text-xs">
                            Xem tất cả <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

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
                        <img src="{{ Auth::user()->display_avatar ?? Auth::user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200">
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

                        <a href="{{ route('user.dashboard') }}"
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

                        <a href="{{ route('thong-bao-lien-he') }}"
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
            
            @if (!Auth::check() || (Auth::user()->account_type ?? 'canhan') !== 'doanhnghiep')
            <button type="button" onclick="toggleFavoriteDropdown(event); toggleMobileNav();" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-base font-semibold text-rose-600 hover:bg-rose-50 w-full text-left">
                <span class="flex items-center gap-2.5"><i class="fa-solid fa-heart text-rose-500"></i> Bài đăng yêu thích</span>
                <span id="mobile-favorite-count" class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-xs">0</span>
            </button>
            @endif
            
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
        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favMenu) favMenu.classList.add('hidden');

        const menu = document.getElementById('user-dropdown-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function toggleFavoriteDropdown(event) {
        if (event) event.stopPropagation();
        const userMenu = document.getElementById('user-dropdown-menu');
        if (userMenu) userMenu.classList.add('hidden');

        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favMenu) {
            favMenu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(event) {
        const userContainer = document.getElementById('user-dropdown-container');
        const userMenu = document.getElementById('user-dropdown-menu');
        if (userContainer && userMenu && !userContainer.contains(event.target)) {
            userMenu.classList.add('hidden');
        }

        const favContainer = document.getElementById('favorite-dropdown-container');
        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favContainer && favMenu && !favContainer.contains(event.target)) {
            favMenu.classList.add('hidden');
        }
    });

    // ==========================================
    // RENTHOME FAVORITES CONTROLLER (GLOBAL)
    // ==========================================
    window.isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    window.currentUserId = "{{ Auth::check() ? (string)Auth::id() : '' }}";
    window.currentUserType = "{{ Auth::check() ? (Auth::user()->account_type ?? 'canhan') : '' }}";
    window.csrfToken = "{{ csrf_token() }}";

    window.RentHomeFavorites = {
        favorites: [],
        favoriteIds: new Set(),
        isLoaded: false,

        async init() {
            if (window.isUserLoggedIn && window.currentUserType !== 'doanhnghiep') {
                await this.fetchFavorites();
            } else {
                this.favorites = [];
                this.favoriteIds = new Set();
                this.renderList();
            }
        },

        async fetchFavorites() {
            if (window.currentUserType === 'doanhnghiep') return;

            try {
                const res = await fetch('/api/favorites');
                if (res.ok) {
                    const data = await res.json();
                    if (data.success) {
                        this.favorites = data.favorites || [];
                        this.favoriteIds = new Set((data.favorite_ids || []).map(id => String(id)));
                        this.isLoaded = true;
                        this.renderList();
                        this.updateCardButtons();
                    }
                }
            } catch (e) {
                console.error('Lỗi khi tải bài đăng yêu thích:', e);
            }
        },

        has(postId) {
            return this.favoriteIds.has(String(postId));
        },

        async toggle(postId, event, ownerId = '') {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!window.isUserLoggedIn) {
                this.showToast('Vui lòng đăng nhập bằng tài khoản cá nhân để lưu bài viết yêu thích!', 'warning');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1300);
                return;
            }

            // 1. Kiểm tra tài khoản Doanh nghiệp: không được phép yêu thích
            if (window.currentUserType === 'doanhnghiep') {
                this.showToast('Tài khoản Doanh nghiệp không thể thực hiện chức năng yêu thích.', 'warning');
                return;
            }

            // 2. Kiểm tra nếu là bài đăng của chính mình
            if (window.currentUserId && ownerId && String(ownerId) === String(window.currentUserId)) {
                this.showToast('Bạn không thể tự yêu thích bài đăng của chính mình!', 'warning');
                return;
            }

            const idStr = String(postId);
            const cardBtn = document.querySelector(`[data-fav-id="${idStr}"]`);
            if (cardBtn) {
                cardBtn.classList.add('fav-bounce');
                setTimeout(() => cardBtn.classList.remove('fav-bounce'), 450);
            }

            try {
                const res = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ post_id: idStr })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.is_favorite) {
                        this.favoriteIds.add(idStr);
                        if (data.post) {
                            this.favorites = [data.post, ...this.favorites.filter(p => String(p.id) !== idStr)];
                        } else {
                            await this.fetchFavorites();
                        }
                        this.showToast(data.message || 'Đã thêm vào danh sách yêu thích!', 'success');
                    } else {
                        this.favoriteIds.delete(idStr);
                        this.favorites = this.favorites.filter(p => String(p.id) !== idStr);
                        this.showToast(data.message || 'Đã xóa khỏi danh sách yêu thích.', 'info');
                    }
                    this.renderList();
                    this.updateCardButtons();
                } else {
                    if (data.require_login) {
                        this.showToast(data.message, 'warning');
                        setTimeout(() => window.location.href = '/login', 1300);
                    } else {
                        this.showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
                    }
                }
            } catch (e) {
                console.error('Lỗi khi cập nhật yêu thích:', e);
                this.showToast('Lỗi kết nối máy chủ.', 'error');
            }
        },

        async remove(postId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            await this.toggle(postId);
        },

        renderList() {
            const listContainer = document.getElementById('header-favorite-list');
            const badge = document.getElementById('header-favorite-badge');
            const countTag = document.getElementById('header-favorite-count-tag');
            const mobileBadge = document.getElementById('mobile-favorite-count');

            const count = this.favorites.length;

            if (badge) {
                badge.innerText = count;
                badge.style.display = count > 0 ? 'inline-flex' : 'none';
            }
            if (countTag) {
                countTag.innerText = count + ' bài';
            }
            if (mobileBadge) {
                mobileBadge.innerText = count;
            }

            if (!listContainer) return;

            if (count === 0) {
                listContainer.innerHTML = `
                    <div class="py-8 px-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-400 flex items-center justify-center mx-auto mb-2.5 shadow-2xs">
                            <i class="fa-regular fa-heart text-xl"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Chưa có bài đăng yêu thích</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] mx-auto leading-relaxed">
                            Nhấn biểu tượng trái tim ở các bài đăng để lưu lại xem nhanh tại đây!
                        </p>
                    </div>
                `;
                return;
            }

            listContainer.innerHTML = this.favorites.map(post => `
                <div class="py-2.5 px-2 hover:bg-slate-50/80 rounded-xl transition-colors flex items-center gap-3 group relative">
                    <a href="/chitietbaidang/${post.id}" class="flex-1 flex items-center gap-3 min-w-0">
                        <img src="${post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=400&q=80'}" 
                             class="w-12 h-12 rounded-xl object-cover shrink-0 shadow-xs border border-slate-200 group-hover:scale-105 transition-transform">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-slate-800 truncate group-hover:text-brand-600 transition-colors">
                                ${post.title}
                            </h5>
                            <p class="text-[11px] font-extrabold text-brand-600 mt-0.5">
                                ${post.price}
                            </p>
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">
                                <i class="fa-solid fa-location-dot text-[9px] mr-1"></i>${post.address || 'Đang cập nhật'}
                            </p>
                        </div>
                    </a>
                    <button type="button" 
                            onclick="window.RentHomeFavorites.remove('${post.id}', event)" 
                            class="p-2 text-slate-300 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors shrink-0" 
                            title="Bỏ thích bài đăng này">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            `).join('');
        },

        updateCardButtons() {
            const buttons = document.querySelectorAll('.fav-toggle-btn');
            buttons.forEach(btn => {
                const id = btn.getAttribute('data-fav-id');
                const ownerId = btn.getAttribute('data-owner-id');
                const isFav = this.has(id);
                const icon = btn.querySelector('i');

                // 1. Tài khoản Doanh nghiệp: ẩn hoàn toàn nút yêu thích
                if (window.currentUserType === 'doanhnghiep') {
                    btn.style.display = 'none';
                    return;
                }

                // 2. Bài đăng của chính tài khoản hiện tại: ẩn hoàn toàn nút yêu thích
                if (window.currentUserId && ownerId && String(ownerId) === String(window.currentUserId)) {
                    btn.style.display = 'none';
                    return;
                }

                btn.style.display = 'flex';

                if (isFav) {
                    btn.className = 'fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white text-rose-500 shadow-md ring-2 ring-rose-100 flex items-center justify-center transition-all duration-200 active:scale-90 group/heart';
                    btn.title = 'Bỏ yêu thích';
                    if (icon) {
                        icon.className = 'fa-solid fa-heart text-base text-rose-500 transition-transform duration-200 group-hover/heart:scale-110';
                    }
                } else {
                    btn.className = 'fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-sm flex items-center justify-center transition-all duration-200 active:scale-90 group/heart';
                    btn.title = 'Yêu thích bài đăng';
                    if (icon) {
                        icon.className = 'fa-regular fa-heart text-base text-slate-400 group-hover/heart:text-rose-500 transition-transform duration-200 group-hover/heart:scale-110';
                    }
                }
            });
        },

        showToast(message, type = 'info') {
            const oldToast = document.getElementById('renthome-toast');
            if (oldToast) oldToast.remove();

            let icon = 'fa-info-circle';
            let bgClass = 'bg-slate-900 text-white';
            if (type === 'success') {
                icon = 'fa-circle-check text-emerald-400';
            } else if (type === 'warning') {
                icon = 'fa-circle-exclamation text-amber-400';
            } else if (type === 'error') {
                icon = 'fa-circle-xmark text-rose-400';
            }

            const toast = document.createElement('div');
            toast.id = 'renthome-toast';
            toast.className = `fixed top-24 right-4 sm:right-8 z-[99999] flex items-center gap-3 px-4 py-3 rounded-2xl ${bgClass} shadow-2xl border border-white/15 text-xs font-semibold transition-all duration-300 backdrop-blur-md`;
            toast.innerHTML = `<i class="fa-solid ${icon} text-sm"></i><span>${message}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-12px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.RentHomeFavorites.init();
    });
</script>

<!-- RentHome AI Assistant Chat Widget -->
@include('partials.ai-chat-widget')