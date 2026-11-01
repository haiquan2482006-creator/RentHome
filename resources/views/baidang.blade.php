<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RentHome - Tất cả bài đăng</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN for instant rendering -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                    }
                }
            }
        }
    </script>

    <style>
        #search-tabs {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
        #search-tabs::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.18s ease-out forwards;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    @include('partials.header-home')

    <!-- Main Content -->
    <main class="pt-32 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Filter & Search Section -->
        <div class="mb-8 bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/90 relative z-30">
            <!-- Search Tabs -->
            <div class="flex items-center gap-2 sm:gap-3 mb-5 overflow-x-auto pb-2 border-b border-slate-100 select-none [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" id="search-tabs">
                <button type="button" onclick="selectSearchTab('', this)" data-type=""
                    class="tab-btn active px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-brand-600 text-white shadow-sm flex items-center gap-2 cursor-pointer transition-all shrink-0">
                    <i class="fa-solid fa-layer-group"></i> Tất cả loại hình
                </button>
                <button type="button" onclick="selectSearchTab('can_ho', this)" data-type="can_ho"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-city"></i> Chung cư mini / Căn hộ dịch vụ
                </button>
                <button type="button" onclick="selectSearchTab('nha_tro', this)" data-type="nha_tro"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-door-open"></i> Nhà trọ / Khu trọ
                </button>
                <button type="button" onclick="selectSearchTab('ky_tuc_xa', this)" data-type="ky_tuc_xa"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-bed"></i> Ký túc xá / Sleepbox
                </button>
                <button type="button" onclick="selectSearchTab('dat_phan_lo', this)" data-type="dat_phan_lo"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-map-location-dot"></i> Đất phân lô / Đất nền
                </button>
                <button type="button" onclick="selectSearchTab('nha_rieng', this)" data-type="nha_rieng"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-house"></i> Nhà ở / Nhà riêng lẻ
                </button>
                <button type="button" onclick="selectSearchTab('cum_nha_lien_ke', this)" data-type="cum_nha_lien_ke"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-cubes"></i> Cụm nhà liền kề / Dự án
                </button>
                <button type="button" onclick="selectSearchTab('dat_nen', this)" data-type="dat_nen"
                    class="tab-btn px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <i class="fa-solid fa-shop"></i> Mặt bằng kinh doanh
                </button>
            </div>

            <!-- Filter Inputs Grid -->
            <form id="filter-form" onsubmit="event.preventDefault(); fetchAllPosts();" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                <!-- 1. Khu vực / Tỉnh thành (Nhập chữ tự do, không số, không ký tự đặc biệt) -->
                <div class="relative lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Khu vực / Tỉnh thành</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-600 pointer-events-none"></i>
                        <input type="text" id="selected-location-input" name="location"
                            placeholder="Nhập tỉnh, thành phố, quận..."
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm transition-all placeholder:text-slate-400 placeholder:font-normal"
                            autocomplete="off">
                    </div>
                </div>

                <!-- 2. Property Type -->
                <div class="relative lg:col-span-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Loại Bất Động Sản</label>
                    <div class="relative">
                        <i class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select id="search-property-type" name="type" onchange="syncTabWithType(this.value); fetchAllPosts();"
                            class="w-full pl-10 pr-8 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm cursor-pointer appearance-none">
                            <option value="">Tất cả loại hình</option>
                            <option value="can_ho">Chung cư mini / Căn hộ dịch vụ</option>
                            <option value="nha_tro">Nhà trọ / Khu trọ</option>
                            <option value="ky_tuc_xa">Ký túc xá / Sleepbox</option>
                            <option value="dat_phan_lo">Đất phân lô / Đất nền</option>
                            <option value="nha_rieng">Nhà ở / Nhà riêng lẻ</option>
                            <option value="cum_nha_lien_ke">Cụm nhà liền kề / Dự án</option>
                            <option value="dat_nen">Mặt bằng kinh doanh</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- 3. Price Range -->
                <div class="relative lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mức Giá Mẫu</label>
                    <div class="relative">
                        <i class="fa-solid fa-money-bill-wave absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select id="search-price-range" name="price_range" onchange="fetchAllPosts();"
                            class="w-full pl-10 pr-8 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm cursor-pointer appearance-none">
                            <option value="">Tất cả mức giá</option>
                            <option value="1">Dưới 3 triệu / tháng</option>
                            <option value="2">3 - 7 triệu / tháng</option>
                            <option value="3">7 - 15 triệu / tháng</option>
                            <option value="4">Trên 15 triệu / tháng</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- 4. Submit & Reset Actions -->
                <div class="flex items-center gap-2 lg:col-span-2">
                    <button type="submit" id="search-submit-btn"
                        class="flex-1 py-3 px-3 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-bold text-sm shadow-md hover:shadow-brand-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <i class="fa-solid fa-magnifying-glass"></i> <span>Tìm kiếm</span>
                    </button>
                    <button type="button" onclick="resetAllFilters()" title="Đặt lại bộ lọc"
                        class="w-11 h-11.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center transition-all cursor-pointer shrink-0">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter Status Banner -->
        <div id="filter-status-banner" class="hidden mb-6 flex flex-wrap items-center justify-between gap-3 bg-brand-50/80 border border-brand-200/80 rounded-2xl px-5 py-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-700" id="filter-status-count">Đang lọc theo:</span>
                <div id="filter-active-tags" class="flex flex-wrap items-center gap-1.5"></div>
            </div>
            <button type="button" onclick="resetAllFilters()" class="text-xs font-bold text-rose-500 hover:text-rose-700 inline-flex items-center gap-1 cursor-pointer transition-colors">
                <i class="fa-solid fa-xmark"></i> Xóa tất cả bộ lọc
            </button>
        </div>

        <div id="all-posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
            <!-- Loading Skeleton lúc mới vào trang -->
            @include('components.skeleton-card', ['count' => 8])
        </div>

        <!-- Template Skeleton dùng cho lúc filter tìm kiếm -->
        <template id="skeleton-card-template">
            @include('components.skeleton-card', ['count' => 8])
        </template>
    </main>

    <script>
        // Khởi tạo bộ lọc cho ô nhập Khu vực / Tỉnh thành: chỉ cho phép chữ cái tiếng Việt/tiếng Anh và khoảng trắng, chặn số và ký tự đặc biệt
        function initLocationInputFilter() {
            const locInput = document.getElementById('selected-location-input');
            if (!locInput) return;

            // 1. Chặn gõ trực tiếp các phím số hoặc ký tự đặc biệt
            locInput.addEventListener('keydown', function(e) {
                // Cho phép các phím chức năng/điều khiển: Backspace, Tab, Enter, Delete, phím mũi tên, Ctrl+A/C/V/Z...
                if (e.ctrlKey || e.altKey || e.metaKey || e.key.length > 1) {
                    return;
                }
                // Nếu không phải chữ cái (\p{L}) và không phải khoảng trắng (\s) thì chặn
                if (!e.isComposing && !/^[\p{L}\s]$/u.test(e.key)) {
                    e.preventDefault();
                }
            });

            // 2. Tự động loại bỏ số và ký tự đặc biệt khi dán (paste) hoặc nhập văn bản
            locInput.addEventListener('input', function() {
                const originalVal = this.value;
                const sanitizedVal = originalVal.replace(/[^\p{L}\s]/gu, '');
                if (originalVal !== sanitizedVal) {
                    this.value = sanitizedVal;
                }
            });
        }

        // Xử lý chuyển Tabs loại hình & đồng bộ với select
        function selectSearchTab(type, btn) {
            const tabs = document.querySelectorAll('#search-tabs .tab-btn');
            tabs.forEach(t => {
                t.classList.remove('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                t.classList.add('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
            });

            if (btn) {
                btn.classList.add('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                btn.classList.remove('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
            }

            const typeSelect = document.getElementById('search-property-type');
            if (typeSelect) {
                typeSelect.value = type;
            }

            fetchAllPosts();
        }

        function syncTabWithType(val) {
            const tabs = document.querySelectorAll('#search-tabs .tab-btn');
            let matched = false;
            tabs.forEach(t => {
                const tabType = t.getAttribute('data-type');
                if (tabType === val) {
                    t.classList.add('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                    t.classList.remove('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
                    t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                    matched = true;
                } else {
                    t.classList.remove('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                    t.classList.add('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
                }
            });

            if (!matched && tabs.length > 0) {
                tabs[0].classList.add('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                tabs[0].classList.remove('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Cuộn ngang thanh tabs khi di chuột vào và lăn con lăn chuột
            const searchTabs = document.getElementById('search-tabs');
            if (searchTabs) {
                searchTabs.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        const delta = e.deltaMode === 1 ? e.deltaY * 33 : e.deltaY;
                        searchTabs.scrollLeft += delta;
                    }
                }, { passive: false });
            }

            initLocationInputFilter();

            // Đọc params từ URL nếu được chuyển tiếp từ Trang Chủ
            const urlParams = new URLSearchParams(window.location.search);
            const kw = urlParams.get('keyword');
            const type = urlParams.get('type');
            const loc = urlParams.get('location');
            const price = urlParams.get('price_range') || urlParams.get('price');

            if (kw && document.getElementById('search-keyword')) {
                document.getElementById('search-keyword').value = kw;
            }
            if (type) {
                const typeSelect = document.getElementById('search-property-type');
                if (typeSelect) typeSelect.value = type;
                syncTabWithType(type);
            }
            if (loc && loc !== 'Tất cả địa điểm') {
                const locInput = document.getElementById('selected-location-input');
                if (locInput) locInput.value = loc.replace(/[^\p{L}\s]/gu, '');
            }
            if (price && document.getElementById('search-price-range')) {
                document.getElementById('search-price-range').value = price;
            }

            fetchAllPosts();
        });

        // Tự động làm mới dữ liệu lượt xem và lượt thích thật khi người dùng thoát/quay lại trang danh sách
        window.addEventListener('pageshow', function(event) {
            fetchAllPosts();
        });

        async function fetchAllPosts() {
            const container = document.getElementById('all-posts-container');
            if (!container) return;

            // Get filter values
            const keyword = document.getElementById('search-keyword')?.value.trim() || '';
            const type = document.getElementById('search-property-type')?.value || '';
            const location = document.getElementById('selected-location-input')?.value.trim() || '';
            const price_range = document.getElementById('search-price-range')?.value || '';

            // Build query params
            const params = new URLSearchParams();
            if (keyword) params.append('keyword', keyword);
            if (type) params.append('type', type);
            if (location && location !== 'Tất cả địa điểm') params.append('location', location);
            if (price_range) params.append('price_range', price_range);
            params.append('_t', Date.now());
            
            const queryString = params.toString();
            const url = '/api/posts/approved?' + queryString;

            // Show loading skeleton khi bấm lọc
            const skeletonTpl = document.getElementById('skeleton-card-template');
            if (skeletonTpl) {
                container.innerHTML = skeletonTpl.innerHTML;
            }

            try {
                const response = await fetch(url, { cache: 'no-store' });
                if (response.ok) {
                    const data = await response.json();
                    const posts = data.posts || [];

                    // Update filter status banner
                    updateFilterBanner({ keyword, type, location, price_range }, posts.length);

                    if (!Array.isArray(posts) || posts.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border border-slate-200 shadow-xs">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl">
                                    <i class="fa-solid fa-house-circle-xmark"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-2">Không tìm thấy bài đăng phù hợp</h3>
                                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Không có bất động sản nào khớp với bộ lọc hiện tại của bạn. Hãy thử thay đổi tiêu chí hoặc xem tất cả bài đăng.</p>
                                <button type="button" onclick="resetAllFilters()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                                    <i class="fa-solid fa-rotate-left"></i> Xem tất cả bài đăng
                                </button>
                            </div>
                        `;
                        return;
                    }
                    
                    let html = '';
                    posts.forEach(post => {
                        const userName = post.user ? (post.user.ten_hien_thi || post.user.ho_ten || post.user.account_name || post.user.username || 'Người dùng') : 'Người dùng';
                        // Phân loại vai trò tài khoản đăng bài: Admin / Nhân sự / Doanh nghiệp / Cá nhân
                        let userType = 'Cá nhân';
                        let userBadgeClass = 'bg-slate-50 text-slate-500 border border-slate-200';
                        let userIconColor = 'text-slate-500 bg-slate-100';
                        let userIcon = 'fa-user';

                        const isStaffUser = Boolean(
                            (post.account_role && ['nhansu', 'staff', 'sub_account', 'nhan_su', 'nhan su'].includes(String(post.account_role).toLowerCase())) ||
                            (post.staff_id && String(post.staff_id).trim() !== '') ||
                            (post.user && (
                                ['nhansu', 'staff', 'sub_account', 'nhan_su', 'nhan su'].includes(String(post.user.role || '').toLowerCase()) ||
                                Boolean(post.user.parent_id) ||
                                (post.user.staff_role && String(post.user.staff_role).toLowerCase().includes('nhân sự'))
                            ))
                        );

                        const isAdminUser = Boolean(
                            post.user && (post.user.role === 'admin' || post.user.is_admin)
                        );

                        const isEnterpriseUser = Boolean(
                            !isStaffUser && post.user && (post.user.role === 'doanhnghiep' || post.user.role === 'enterprise') && !post.user.parent_id
                        );

                        if (isAdminUser) {
                            userType = 'Ban Quản Trị';
                            userBadgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                            userIconColor = 'text-amber-600 bg-amber-50';
                            userIcon = 'fa-shield-halved';
                        } else if (isStaffUser) {
                            userType = 'Nhân sự';
                            userBadgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                            userIconColor = 'text-emerald-600 bg-emerald-50';
                            userIcon = 'fa-user-tie';
                        } else if (isEnterpriseUser) {
                            userType = 'Doanh nghiệp';
                            userBadgeClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                            userIconColor = 'text-blue-500 bg-blue-50';
                            userIcon = 'fa-building';
                        } else {
                            userType = 'Cá nhân';
                            userBadgeClass = 'bg-slate-50 text-slate-500 border border-slate-200';
                            userIconColor = 'text-slate-500 bg-slate-100';
                            userIcon = 'fa-user';
                        }
                        
                        let imgUrl = post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                        let unitText = 'đ/tháng';
                        if (post.price_unit === '2_nam') unitText = 'đ/2 năm';
                        else if (post.price_unit === '3_nam') unitText = 'đ/3 năm';
                        else if (post.price_unit === '5_nam') unitText = 'đ/5 năm';
                        else if (post.price_unit === 'tren_5_nam') unitText = 'đ/trên 5 năm';
                        else if (post.price_unit === 'nam') unitText = 'đ/năm';
                        else if (post.price_unit === 'm2') unitText = 'đ/m²';
                        else if (post.price_unit === 'tong') unitText = 'VNĐ';
                        const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' ' + unitText;
                        const address = [post.address, post.ward, post.district, post.province].filter(Boolean).join(', ');
                        
                        const postId = post.id || post._id;
                        const postOwnerId = String(post.user_id || (post.user && (post.user._id || post.user.id)) || '');
                        const isFav = window.RentHomeFavorites && window.RentHomeFavorites.has(postId);
                        const isEnterprise = window.currentUserType === 'doanhnghiep';
                        const isMyPost = Boolean(window.currentUserId && postOwnerId && postOwnerId === String(window.currentUserId));
                        const canFavorite = !isEnterprise && !Boolean(window.isStaffUser) && !isMyPost;

                        let favButtonHtml = '';
                        if (canFavorite) {
                            favButtonHtml = `
                                <!-- Favorite Button -->
                                <button type="button" 
                                        onclick="window.RentHomeFavorites.toggle('${postId}', event, '${postOwnerId}')" 
                                        data-fav-id="${postId}"
                                        data-owner-id="${postOwnerId}"
                                        class="fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full ${isFav ? 'bg-white text-rose-500 shadow-md ring-2 ring-rose-100' : 'bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-sm'} flex items-center justify-center transition-all duration-200 active:scale-90 group/heart"
                                        title="${isFav ? 'Bỏ yêu thích' : 'Yêu thích bài đăng'}">
                                    <i class="${isFav ? 'fa-solid fa-heart text-rose-500' : 'fa-regular fa-heart text-slate-400 group-hover/heart:text-rose-500'} text-base transition-transform duration-200 group-hover/heart:scale-110"></i>
                                </button>
                            `;
                        }
                        
                        html += `
                            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group relative">
                                <div class="relative aspect-[4/3] overflow-hidden">
                                    <img src="${imgUrl}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    
                                    ${favButtonHtml}

                                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl font-bold text-xs shadow-sm">
                                        <span class="text-brand-600">${price}</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-slate-900 mb-2 leading-tight truncate">
                                        ${post.title}
                                    </h3>
                                    <div class="flex flex-col gap-1.5 mb-4 text-xs font-semibold text-slate-500">
                                        ${post.building && post.building.name ? `
                                        <div class="flex items-center gap-2 text-brand-600 font-bold truncate">
                                            <i class="fa-solid fa-building w-4 text-center"></i>
                                            <span>${post.building.name}</span>
                                        </div>` : ''}
                                        <div class="flex items-center gap-2 truncate">
                                            <i class="fa-solid fa-location-dot w-4 text-center"></i>
                                            <span>${address}</span>
                                        </div>
                                        <div class="flex items-center gap-3.5 text-xs text-slate-500 font-medium pt-1">
                                            <span class="flex items-center gap-1.5" title="Lượt xem thực tế">
                                                <i class="fa-solid fa-eye text-slate-400"></i> ${Number(post.views || 0).toLocaleString('vi-VN')}
                                            </span>
                                            <span class="flex items-center gap-1.5" title="Lượt yêu thích">
                                                <i class="fa-solid fa-heart text-rose-500"></i> <span data-fav-counter-id="${postId}">${Number(post.favorites_count || post.likes || 0).toLocaleString('vi-VN')}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100 mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shrink-0 shadow-sm border border-slate-100 ${userIconColor}">
                                                <i class="fa-solid ${userIcon}"></i>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <span class="text-sm font-bold text-slate-800 truncate max-w-[120px] sm:max-w-[160px]">${userName}</span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mt-0.5 w-max ${userBadgeClass}">${userType}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/chitietbaidang/${post.id || post._id}" class="block w-full text-center py-2.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-sm transition-all duration-300 border border-brand-100 hover:border-brand-600">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    if (window.RentHomeFavorites) {
                        window.RentHomeFavorites.updateCardButtons();
                    }
                } else {
                    container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10 font-bold">Lỗi khi tải dữ liệu bài đăng!</p>';
                }
            } catch (error) {
                console.error('Error fetching real posts:', error);
                container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10 font-bold">Không thể kết nối đến máy chủ!</p>';
            }
        }

        function updateFilterBanner(filters, count) {
            const banner = document.getElementById('filter-status-banner');
            const countEl = document.getElementById('filter-status-count');
            const tagsEl = document.getElementById('filter-active-tags');
            if (!banner || !countEl || !tagsEl) return;

            const hasFilter = Boolean(filters.keyword || filters.type || (filters.location && filters.location !== 'Tất cả địa điểm') || filters.price_range);

            if (!hasFilter) {
                banner.classList.add('hidden');
                return;
            }

            banner.classList.remove('hidden');
            countEl.innerHTML = `Tìm thấy <strong class="text-brand-700 font-extrabold">${count}</strong> bất động sản phù hợp:`;

            let tagsHtml = '';
            if (filters.keyword) {
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 flex items-center gap-1"><i class="fa-solid fa-magnifying-glass text-slate-400"></i> "${filters.keyword}"</span>`;
            }
            if (filters.location && filters.location !== 'Tất cả địa điểm') {
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-xs font-bold text-emerald-700 flex items-center gap-1"><i class="fa-solid fa-location-dot text-emerald-500"></i> ${filters.location}</span>`;
            }
            if (filters.type) {
                const typeLabels = {
                    'can_ho': 'Chung cư mini / Căn hộ dịch vụ',
                    'nha_tro': 'Nhà trọ / Khu trọ',
                    'phong_tro': 'Nhà trọ / Khu trọ',
                    'ky_tuc_xa': 'Ký túc xá / Sleepbox',
                    'dat_phan_lo': 'Đất phân lô / Đất nền',
                    'nha_rieng': 'Nhà ở / Nhà riêng lẻ',
                    'nha_nguyen_can': 'Nhà ở / Nhà riêng lẻ',
                    'cum_nha_lien_ke': 'Cụm nhà liền kề / Dự án',
                    'biet_thu': 'Cụm nhà liền kề / Dự án',
                    'dat_nen': 'Mặt bằng kinh doanh',
                    'mat_bang': 'Mặt bằng kinh doanh'
                };
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-white border border-blue-300 text-xs font-bold text-blue-700 flex items-center gap-1"><i class="fa-solid fa-layer-group text-blue-500"></i> ${typeLabels[filters.type] || filters.type}</span>`;
            }
            if (filters.price_range) {
                const priceLabels = {
                    '1': 'Dưới 3 triệu',
                    '2': '3 - 7 triệu',
                    '3': '7 - 15 triệu',
                    '4': 'Trên 15 triệu'
                };
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-white border border-amber-300 text-xs font-bold text-amber-700 flex items-center gap-1"><i class="fa-solid fa-money-bill-wave text-amber-500"></i> ${priceLabels[filters.price_range] || filters.price_range}</span>`;
            }
            tagsEl.innerHTML = tagsHtml;
        }

        function resetAllFilters() {
            const loc = document.getElementById('selected-location-input');
            const type = document.getElementById('search-property-type');
            const price = document.getElementById('search-price-range');

            if (loc) loc.value = '';
            if (type) type.value = '';
            if (price) price.value = '';

            syncTabWithType('');

            window.history.replaceState({}, '', window.location.pathname);
            fetchAllPosts();
        }
    </script>
</body>
</html>
