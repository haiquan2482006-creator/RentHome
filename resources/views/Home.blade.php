<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RentHome - Kênh Tìm Kiếm & Cho Thuê Nhà Hàng Đầu Việt Nam</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(34, 197, 94, 0.4)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.08)',
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

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);  
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .badge-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Chuyển mờ hình ảnh mượt mà */
        .hero-bg-slide {
            transition: opacity 1.5s ease-in-out;
            will-change: opacity;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    @include('partials.header-home')

    <!-- Hero Section -->
    <section id="hero"
        class="pt-24 pb-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center relative overflow-hidden bg-slate-950">

        <!-- Background Crossfade Images -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            @php
                $customBanners = [];
                if (!empty($themeSettings['banner_images']) && is_array($themeSettings['banner_images'])) {
                    $customBanners = array_values(array_filter($themeSettings['banner_images']));
                } elseif (!empty($themeSettings['banner_url'])) {
                    $customBanners = [$themeSettings['banner_url']];
                }
                $defaultBanners = [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80',
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80',
                    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1920&q=80',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1920&q=80',
                ];
                $activeBanners = !empty($customBanners) 
                    ? array_merge($customBanners, array_slice($defaultBanners, count($customBanners))) 
                    : $defaultBanners;
            @endphp
            @foreach($activeBanners as $bIdx => $bUrl)
                <div class="hero-bg-slide absolute inset-0 bg-cover bg-center {{ $bIdx === 0 ? 'opacity-100' : 'opacity-0' }}"
                    style="{{ $bIdx === 0 ? 'background-image: url(\'' . $bUrl . '\');' : '' }}"
                    {!! $bIdx > 0 ? 'data-bg="url(\'' . $bUrl . '\')"' : '' !!}>
                </div>
            @endforeach

            <!-- Lớp phủ cố định giúp chữ luôn rõ nét -->
            <div class="absolute inset-0 bg-slate-950/70 pointer-events-none"></div>
        </div>

        <!-- CỐ ĐỊNH CHỮ VÀ NỘI DUNG -->
        <div class="max-w-4xl mx-auto text-center mt-2 relative z-10">
            <span
                class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3 shadow-md">
                <i class="fa-solid fa-bolt text-yellow-400"></i> Hơn 50,000+ Căn nhà sẵn sàng cho thuê
            </span>
            <h1
                class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight drop-shadow-md">
                {{ $themeSettings['banner_title'] ?? 'Tìm Ngôi Nhà Hoàn Hảo Cho Cuộc Sống Tương Lai' }}
            </h1>
            <p class="mt-2 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto font-normal drop-shadow">
                {{ $themeSettings['banner_subtitle'] ?? 'Khám phá hàng ngàn phòng trọ, căn hộ cao cấp, và nhà nguyên căn chính chủ với mức giá tốt nhất, không qua trung gian.' }}
            </p>
        </div>

        <!-- CỐ ĐỊNH KHUNG TÌM KIẾM -->
        <div class="w-full max-w-5xl mx-auto mt-6 relative z-30">
            <div class="glass-panel p-4 sm:p-5 rounded-3xl shadow-2xl border border-white/40">
                <!-- Search Tabs -->
                <div class="flex items-center gap-2 sm:gap-3 mb-5 overflow-x-auto pb-2 border-b border-slate-200/60 select-none [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" id="search-tabs">
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

                <!-- Search Inputs Grid -->
                <form id="home-search-form" onsubmit="event.preventDefault(); handleSearch();" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_215px_auto] gap-3.5 items-end">
                    <!-- 1. Khu vực / Tỉnh thành (Dạng danh sách kèm tìm kiếm - mở rộng bằng Loại BĐS) -->
                    @include('partials.location-select-picker')

                    <!-- 2. Loại Bất Động Sản -->
                    <div class="relative w-full">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Loại Bất Động Sản</label>
                        <div class="relative">
                            <i class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select id="search-property-type" name="type" onchange="syncTabWithType(this.value)"
                                class="w-full h-11.5 pl-10 pr-8 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm cursor-pointer appearance-none">
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

                    <!-- 3. Mức Giá Mẫu -->
                    <div class="relative w-full">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mức Giá Mẫu</label>
                        <div class="relative">
                            <i class="fa-solid fa-money-bill-wave absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select id="search-price-range" name="price_range"
                                class="w-full h-11.5 pl-10 pr-8 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm cursor-pointer appearance-none">
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
                    <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                        <button type="submit" id="search-submit-btn"
                            class="group flex-1 sm:flex-initial h-11.5 px-6 sm:px-7 min-w-[135px] rounded-xl bg-gradient-to-r from-emerald-600 via-brand-600 to-teal-500 hover:from-emerald-700 hover:via-brand-700 hover:to-teal-600 text-white font-bold text-sm shadow-md shadow-brand-500/25 hover:shadow-lg hover:shadow-brand-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer whitespace-nowrap shrink-0 border border-white/10">
                            <i class="fa-solid fa-magnifying-glass text-sm text-emerald-100 group-hover:text-white group-hover:scale-110 transition-transform duration-200 shrink-0"></i>
                            <span id="search-btn-text" class="tracking-wide whitespace-nowrap">Tìm kiếm</span>
                        </button>
                        <button type="button" onclick="resetSearchFilters()" title="Đặt lại bộ lọc"
                            class="group h-11.5 w-11.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-800 shadow-xs hover:shadow transition-all duration-200 flex items-center justify-center cursor-pointer shrink-0 active:scale-95">
                            <i class="fa-solid fa-rotate-left text-sm group-hover:-rotate-90 transition-transform duration-300"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CỐ ĐỊNH THÔNG SỐ -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-4xl w-full text-center relative z-10">
            <div class="py-3 px-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-2xl font-extrabold text-white">50k+</p>
                <p class="text-xs text-slate-300 font-medium mt-0.5">Tin đăng cho thuê</p>
            </div>
            <div class="py-3 px-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-2xl font-extrabold text-emerald-400">98%</p>
                <p class="text-xs text-slate-300 font-medium mt-0.5">Khách thuê hài lòng</p>
            </div>
            <div class="py-3 px-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-2xl font-extrabold text-white">120k+</p>
                <p class="text-xs text-slate-300 font-medium mt-0.5">Người dùng hàng tháng</p>
            </div>
            <div class="py-3 px-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-2xl font-extrabold text-emerald-400">0%</p>
                <p class="text-xs text-slate-300 font-medium mt-0.5">Phí môi giới ẩn</p>
            </div>
        </div>
    </section>

    <!-- Featured Posts Slider Section (Bài Đăng Nổi Bật Được Ban Quản Lý Chọn) -->
    <section id="featured-posts-section"
        class="scroll-mt-24 py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto my-12 bg-slate-100/60 rounded-3xl border border-slate-200/60 relative">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-600 font-extrabold text-xs uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-star text-amber-500"></i> Bài đăng nổi bật
                </div>
                <h2 class="text-3xl font-black text-slate-900 mt-1">Bất Động Sản Nổi Bật </h2>
                <p class="text-xs sm:text-sm text-slate-500 font-semibold mt-1">
                    Danh sách các bài đăng uy tín, chất lượng cao do Ban Quản Lý lựa chọn & ưu tiên hiển thị
                </p>
            </div>

            <!-- Action controls & Admin Add Button -->
            <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3">
                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->isAdmin()))
                <!-- Admin Add Featured Post Button -->
                <button onclick="openAddFeaturedModal()"
                    class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2 group cursor-pointer">
                    <i class="fa-solid fa-plus-circle text-sm group-hover:rotate-90 transition-transform duration-300"></i>
                    <span>Thêm bài đăng nổi bật</span>
                </button>
                @endif

                <!-- Navigation Slider Arrows -->
                <div class="flex items-center gap-2">
                    <button onclick="scrollFeaturedPosts('left')" aria-label="Trượt sang trái"
                        class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-sm hover:bg-slate-50 text-slate-700 flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button onclick="scrollFeaturedPosts('right')" aria-label="Trượt sang phải"
                        class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-sm hover:bg-slate-50 text-slate-700 flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Container for Featured Posts -->
        <div id="featured-posts-slider" class="overflow-hidden relative w-full py-2 min-h-[460px]">
            <div id="featured-posts-track" class="flex gap-6 w-max">
                <!-- Hiển thị Skeleton Loader mặc định trong lúc Javascript tải bài đăng nổi bật -->
                @include('components.skeleton-featured-card', ['count' => 4])
            </div>
        </div>

        <!-- Template Skeleton dùng cho lúc Javascript load lại bài đăng nổi bật -->
        <template id="featured-posts-skeleton-template">
            @include('components.skeleton-featured-card', ['count' => 4])
        </template>
    </section>

    <!-- Featured Properties Grid Section -->
    <section id="featured"
        class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto bg-slate-100/60 rounded-3xl border border-slate-200/60">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Lựa chọn hàng đầu</span>
                <h2 class="text-3xl font-black text-slate-900 mt-1">Danh Sách Nhà Cho Thuê</h2>
                
                <!-- Search Results Banner -->
                <div id="search-results-banner" class="hidden mt-3 flex flex-wrap items-center gap-2">
                    <span id="search-results-count" class="text-xs sm:text-sm font-semibold text-slate-700"></span>
                    <div id="search-active-tags" class="flex flex-wrap items-center gap-1.5"></div>
                    <button type="button" onclick="resetSearchFilters()" class="text-xs font-bold text-rose-500 hover:text-rose-700 ml-1 inline-flex items-center gap-1 cursor-pointer transition-colors">
                        <i class="fa-solid fa-rotate-left"></i> Đặt lại bộ lọc
                    </button>
                </div>
            </div>
        </div>

        <!-- Property Grid -->
        <div id="real-posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Hiển thị Skeleton Loading trong lúc Javascript gọi API fetchRealPosts -->
            @include('components.skeleton-card', ['count' => 6])
        </div>

        <!-- Template Skeleton dùng cho lúc Javascript load lại bài đăng hoặc tìm kiếm -->
        <template id="real-posts-skeleton-template">
            @include('components.skeleton-card', ['count' => 6])
        </template>

        <div class="mt-12 text-center" id="load-more-container">
            <button id="load-more-btn" onclick="loadMorePosts()"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-white border border-slate-300 hover:border-brand-500 font-bold text-sm text-slate-800 hover:text-brand-600 shadow-sm hover:shadow-md transition-all">
                Xem thêm các căn nhà khác <i class="fa-solid fa-chevron-down"></i>
            </button>
            <a href="/baidang" id="go-to-baidang-btn"
                class="hidden inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-brand-600 border border-brand-600 hover:bg-brand-700 font-bold text-sm text-white shadow-sm hover:shadow-md transition-all">
                Xem thêm <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Why Choose Us / Features -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Tại sao chọn
                    RentHome?</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2 leading-tight">
                    Trải Nghiệm Thuê Nhà An Toàn, Nhanh Chóng & Minh Bạch
                </h2>
                <p class="text-slate-600 mt-4 text-sm leading-relaxed">
                    RentHome giải quyết nỗi lo tìm nhà trọ trôi nổi, giá ảo. Chúng tôi kiểm duyệt 100% hình ảnh và thông
                    tin từ chủ nhà thực tế.
                </p>

                <div class="mt-8 space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Thông tin đã xác minh chính chủ</h4>
                            <p class="text-xs text-slate-500 mt-1">Tất cả bài đăng đều được kiểm tra địa chỉ, hợp đồng
                                và giấy tờ rõ ràng trước khi hiển thị.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Không tốn phí môi giới gian lận</h4>
                            <p class="text-xs text-slate-500 mt-1">Kết nối trực tiếp khách thuê với chủ nhà, không qua
                                trung gian nâng giá.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Hợp đồng thuê nhà trực tuyến</h4>
                            <p class="text-xs text-slate-500 mt-1">Hỗ trợ tạo hợp đồng thuê nhà điện tử đúng chuẩn pháp
                                lý nhanh chóng trong 5 phút.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Image Box -->
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80"
                        loading="lazy" alt="Không gian sống tuyệt đẹp" class="w-full h-auto object-cover">
                </div>
                <!-- Floating Card Badge -->
                <div
                    class="absolute -bottom-6 -left-6 glass-panel p-5 rounded-2xl shadow-2xl border border-white max-w-xs hidden sm:block">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">4.9 / 5.0 Đánh giá</p>
                            <p class="text-xs text-slate-500">Từ hơn 25,000 khách hàng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comprehensive Management Solution Section (Dành Cho Chủ Nhà & Doanh Nghiệp) -->
    <section id="management-solution" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-gradient-to-b from-slate-50/90 via-slate-50/50 to-white rounded-3xl p-8 sm:p-12 lg:p-14 border border-slate-200/80 shadow-sm relative overflow-hidden">
            <!-- Decorative Background Glow -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-500/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-14 relative z-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-700 font-extrabold text-xs uppercase tracking-wider mb-3 shadow-2xs">
                    <i class="fa-solid fa-layer-group text-brand-600"></i> Giải Pháp Vận Hành Toàn Diện
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                    Quản Lý Bất Động Sản Toàn Diện Với <span class="bg-gradient-to-r from-brand-600 to-emerald-600 bg-clip-text text-transparent">RentHome</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3.5 leading-relaxed font-medium">
                    Hệ sinh thái công nghệ quản lý vận hành khép kín, tối ưu hóa dòng tiền và hỗ trợ chủ nhà, doanh nghiệp kiểm soát hàng chục tòa nhà, hàng trăm phòng trọ chỉ trên một nền tảng duy nhất.
                </p>
            </div>

            <!-- 4 Feature Icon Boxes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                
                <!-- Box 1: Quản lý Hợp đồng Điện tử -->
                <div class="group bg-white rounded-3xl p-7 border border-slate-200/90 hover:border-teal-400/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 border border-teal-100/80 flex items-center justify-center text-teal-600 mb-6 group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-2xs">
                            <i class="fa-solid fa-file-signature text-2xl"></i>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 font-bold text-[10px] uppercase tracking-wider mb-2 border border-teal-100">
                            <i class="fa-solid fa-shield-halved text-[9px]"></i> An toàn & Số hóa
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-teal-700 transition-colors leading-snug mb-2.5">
                            Quản lý Hợp đồng Điện tử
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                            Số hóa toàn bộ quy trình ký kết, lưu trữ an toàn ảnh CCCD, quản lý trạng thái hợp đồng dễ dàng.
                        </p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-teal-600 text-[10px]"></i> Lưu ảnh CCCD 2 mặt
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-teal-600 text-[10px]"></i> Theo dõi hạn hợp đồng
                        </span>
                    </div>
                </div>

                <!-- Box 2: Tự động hóa Hóa đơn & Tài chính -->
                <div class="group bg-white rounded-3xl p-7 border border-slate-200/90 hover:border-emerald-400/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-400 to-green-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100/80 flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-2xs">
                            <i class="fa-solid fa-calculator text-2xl"></i>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] uppercase tracking-wider mb-2 border border-emerald-100">
                            <i class="fa-solid fa-bolt text-[9px]"></i> Tự động tính toán
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug mb-2.5">
                            Tự động hóa Hóa đơn & Tài chính
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                            Công cụ chốt điện nước định kỳ, tự động sinh hóa đơn linh hoạt theo chu kỳ (1 tháng, 3 tháng, 6 tháng), tự động tính toán tiền lẻ ngày chính xác.
                        </p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Chốt số điện nước
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Tính tiền lẻ ngày
                        </span>
                    </div>
                </div>

                <!-- Box 3: Kiểm soát Nợ đọng & Thanh toán -->
                <div class="group bg-white rounded-3xl p-7 border border-slate-200/90 hover:border-blue-400/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100/80 flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-2xs">
                            <i class="fa-solid fa-qrcode text-2xl"></i>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px] uppercase tracking-wider mb-2 border border-blue-100">
                            <i class="fa-solid fa-receipt text-[9px]"></i> Thanh toán thông minh
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug mb-2.5">
                            Kiểm soát Nợ đọng & Thanh toán
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                            Tích hợp thanh toán QR Code tự động, gạch nợ nhanh chóng, quản lý công nợ khách thuê minh bạch.
                        </p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-blue-600 text-[10px]"></i> Quét mã VietQR
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-blue-600 text-[10px]"></i> Báo cáo nợ tồn đọng
                        </span>
                    </div>
                </div>

                <!-- Box 4: Phân quyền Quản lý Đa tầng -->
                <div class="group bg-white rounded-3xl p-7 border border-slate-200/90 hover:border-purple-400/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-purple-400 to-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 border border-purple-100/80 flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-2xs">
                            <i class="fa-solid fa-sitemap text-2xl"></i>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-bold text-[10px] uppercase tracking-wider mb-2 border border-purple-100">
                            <i class="fa-solid fa-users-gear text-[9px]"></i> Đa chi nhánh
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-purple-700 transition-colors leading-snug mb-2.5">
                            Phân quyền Quản lý Đa tầng
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                            Hệ thống tài khoản phân cấp rõ ràng (Admin, CSKH, Sale), dễ dàng quản lý hàng chục tòa nhà và hàng trăm phòng trọ.
                        </p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-purple-600 text-[10px]"></i> Phân cấp vai trò
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-check text-purple-600 text-[10px]"></i> Quản lý chuỗi tòa nhà
                        </span>
                    </div>
                </div>

            </div>

            <!-- Bottom Support Banner -->
            <div class="mt-10 pt-8 border-t border-slate-200/70 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left relative z-10">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Bạn là chủ nhà hoặc doanh nghiệp quản trị căn hộ?</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Trải nghiệm bộ công cụ vận hành bất động sản tự động ngay hôm nay.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if(Auth::check())
                        @if(Auth::user()->isEnterprise() || Auth::user()->isStaff() || Auth::user()->isAdmin())
                        <a href="{{ url('/Overview') }}" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                            <span>Vào bảng quản trị</span> <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                        @else
                        <a href="{{ url('/#post-house') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                            <span>Đăng tin cho thuê</span> <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                            <span>Đăng ký tài khoản</span> <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- News & Market Updates Section (Larger Frame Grid Layout) -->
    <section id="news" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Thị trường & Xu
                    hướng</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-1">Tin Tức Cho Thuê & Dự Án Nổi Bật</h2>
            </div>
            <a href="{{ url('/tintuc') }}"
                class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-sm transition-all duration-300 border border-brand-200 hover:border-brand-600 shadow-xs">
                Xem toàn bộ tin tức <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div id="approved-news-container" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Hiển thị Skeleton Loading trong lúc Javascript gọi API fetchApprovedNews -->
            @include('components.skeleton-news')
        </div>

        <!-- Template Skeleton dùng cho lúc Javascript load lại tin tức -->
        <template id="approved-news-skeleton-template">
            @include('components.skeleton-news')
        </template>
    </section>

    <!-- Call to Action Banner for Landlords -->
    <section id="post-house" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div
            class="relative rounded-3xl bg-gradient-to-r from-slate-900 via-brand-900 to-slate-950 p-8 sm:p-14 overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 max-w-2xl">
                @auth
                    @if(Auth::user()->isIndividual())
                        <span class="px-3.5 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-400/30 text-xs font-bold uppercase tracking-wider">
                            Dành cho người thuê
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black mt-4 leading-tight">
                            Tìm Căn Phòng Mơ Ước <br> Nhanh Chóng Cùng RentHome!
                        </h2>
                        <p class="mt-3 text-slate-300 text-sm sm:text-base">
                            Hệ thống căn hộ, phòng trọ được xác thực và bảo chứng chất lượng bởi RentHome. Hợp đồng minh bạch, hỗ trợ xem phòng tức thì.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <a href="{{ url('/baidang') }}"
                                class="px-8 py-3.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-glow transition-all inline-flex items-center">
                                <i class="fa-solid fa-magnifying-glass mr-2"></i> Khám phá phòng trống ngay
                            </a>
                            <a href="{{ route('nha-dang-thue') }}"
                                class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors inline-flex items-center">
                                <i class="fa-solid fa-house-user mr-2"></i> Phòng của bạn
                            </a>
                        </div>
                    @else
                        <span class="px-3.5 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-400/30 text-xs font-bold uppercase tracking-wider">
                            Quản trị & Vận hành RentHome
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black mt-4 leading-tight">
                            Trung Tâm Vận Hành & <br> Quản Lý Tòa Nhà RentHome
                        </h2>
                        <p class="mt-3 text-slate-300 text-sm sm:text-base">
                            Quản lý tập trung các tòa nhà, phân quyền cho nhân viên phụ trách và thực hiện kiểm duyệt tin đăng nhanh chóng.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <a href="{{ url('Overview') }}"
                                class="px-8 py-3.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-glow transition-all inline-flex items-center">
                                <i class="fa-solid fa-gauge-high mr-2"></i> Vào bảng điều khiển quản lý
                            </a>
                            <a href="{{ route('dangbai') }}"
                                class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors inline-flex items-center">
                                <i class="fa-solid fa-plus mr-2"></i> Thêm phòng mới
                            </a>
                        </div>
                    @endif
                @else
                    <span class="px-3.5 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-400/30 text-xs font-bold uppercase tracking-wider">
                        Trải nghiệm tốt nhất
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black mt-4 leading-tight">
                        Thuê Phòng Dễ Dàng <br> Cùng Hệ Thống RentHome
                    </h2>
                    <p class="mt-3 text-slate-300 text-sm sm:text-base">
                        Khám phá hàng ngàn phòng trọ và căn hộ dịch vụ cao cấp, đầy đủ tiện ích và giá thuê minh bạch.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ url('/baidang') }}"
                            class="px-8 py-3.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-glow transition-all inline-flex items-center">
                            <i class="fa-solid fa-magnifying-glass mr-2"></i> Tìm phòng ngay
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors inline-flex items-center">
                            Đăng nhập
                        </a>
                    </div>
                @endauth
            </div>
            <!-- Decorative Icon -->
            <i
                class="fa-solid fa-house-circle-check absolute -right-10 -bottom-10 text-[220px] text-white/5 pointer-events-none"></i>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-slate-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
                <!-- Brand Info -->
                <div class="space-y-4">
                    @php
                        $fBrandName = $themeSettings['brand_name'] ?? 'RentHome';
                        $fColor = $themeSettings['theme_color'] ?? '#16a34a';
                        if (str_ends_with(strtolower($fBrandName), 'home')) {
                            $fp1 = substr($fBrandName, 0, strlen($fBrandName) - 4);
                            $fp2 = 'Home';
                        } elseif (strlen($fBrandName) > 4) {
                            $fmid = floor(strlen($fBrandName) / 2);
                            $fp1 = substr($fBrandName, 0, $fmid);
                            $fp2 = substr($fBrandName, $fmid);
                        } else {
                            $fp1 = $fBrandName;
                            $fp2 = '';
                        }
                    @endphp
                    <div class="flex items-center gap-3">
                        <img src="{{ !empty($themeSettings['logo_url']) ? $themeSettings['logo_url'] : asset('img/logo.png') }}"
                            alt="RentHome Logo" class="w-9 h-9 object-contain rounded-xl shadow-sm">
                        <span class="text-xl font-extrabold text-white">{{ $fp1 }}<span
                                style="color: {{ $fColor }}">{{ $fp2 }}</span></span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">
                        {{ $themeSettings['brand_slogan'] ?? 'Nền tảng công nghệ hỗ trợ tìm kiếm và cho thuê bất động sản, căn hộ, phòng trọ minh bạch và hiệu quả hàng đầu Việt Nam.' }}
                    </p>
                    <div class="flex gap-3 pt-2">
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                            style="background-color: {{ $fColor }}"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                            style="background-color: {{ $fColor }}"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs transition-colors hover:opacity-80"
                            style="background-color: {{ $fColor }}"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Danh Mục Hot</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#" class="hover:text-white transition-colors">Cho thuê căn hộ TP.HCM</a>
                        </li>
                        <li><a href="#" class="hover:text-white transition-colors">Cho thuê nhà nguyên căn Hà
                                Nội</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Phòng trọ giá rẻ sinh
                                viên</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Biệt thự Villa nghỉ dưỡng</a>
                        </li>
                    </ul>
                </div>

                <!-- Support Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Hỗ Trợ Khách Hàng</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#" class="hover:text-white transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Quy định dịch vụ</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Giải quyết tranh chấp</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Liên Hệ</h4>
                    <ul class="space-y-3 text-xs">
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone" style="color: {{ $fColor }}"></i> Hotline:
                            {{ $themeSettings['footer_phone'] ?? '0903990706' }}
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope" style="color: {{ $fColor }}"></i> Email:
                            {{ $themeSettings['footer_email'] ?? 'cskh@renthome.vn' }}
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot mt-0.5" style="color: {{ $fColor }}"></i>
                            {{ $themeSettings['footer_address'] ?? 'Tòa nhà Landmark 81, Bình Thạnh, TP.HCM' }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>{{ $themeSettings['footer_copyright'] ?? '© 2026 RentHome Inc. Tất cả quyền được bảo lưu.' }}</p>
                <p>Thiết kế bởi Hoàng Hải - Laravel Framework 12</p>
            </div>
        </div>
    </footer>

    <!-- Property Detail Modal -->
    <div id="propertyModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative animate-fade-in">
            <button onclick="closeModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <span class="px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-bold">Thông tin nhanh</span>
            <h3 id="modalTitle" class="text-xl font-bold text-slate-900 mt-2"></h3>
            <p id="modalLocation" class="text-xs text-slate-500 mt-1"></p>
            <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500">Giá thuê tham khảo:</p>
                <p id="modalPrice" class="text-2xl font-black text-brand-600 mt-0.5"></p>
            </div>
            <div class="mt-6 flex gap-3">
                <button onclick="showToast('Đã gửi yêu cầu liên hệ chủ nhà!', 'success')"
                    class="flex-1 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm transition-colors">
                    <i class="fa-solid fa-phone mr-1.5"></i> Gọi điện chủ nhà
                </button>
                <button onclick="closeModal()"
                    class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 font-semibold text-sm text-slate-700">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Auth Modal (Login / Register) -->
    <div id="authModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
            <!-- Close Button -->
            <button onclick="closeAuthModal()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Modal Header / Tabs -->
            <div class="flex items-center gap-6 border-b border-slate-100 pb-3 mb-6">
                <button id="tabLoginBtn" onclick="switchAuthTab('login')"
                    class="text-lg font-bold pb-2 text-brand-600 border-b-2 border-brand-600 transition-all">
                    Đăng nhập
                </button>
                <button id="tabRegisterBtn" onclick="switchAuthTab('register')"
                    class="text-lg font-bold pb-2 text-slate-400 hover:text-slate-700 border-b-2 border-transparent transition-all">
                    Đăng ký
                </button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" onsubmit="event.preventDefault(); handleAuthSubmit('login');" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email / Số
                        điện thoại</label>
                    <div class="relative">
                        <i
                            class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" required placeholder="example@gmail.com hoặc 0903..."
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mật
                        khẩu</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                        <input type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="font-semibold text-brand-600 hover:underline">Quên mật khẩu?</a>
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-bold text-sm shadow-md hover:shadow-glow transition-all">
                    Đăng Nhập
                </button>

                <p class="text-center text-xs text-slate-500 mt-4">
                    Chưa có tài khoản?
                    <button type="button" onclick="switchAuthTab('register')"
                        class="font-bold text-brand-600 hover:underline">Đăng ký ngay</button>
                </p>
            </form>

            <!-- Register Form -->
            <form id="registerForm" onsubmit="event.preventDefault(); handleAuthSubmit('register');"
                class="space-y-4 hidden">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Họ và
                        tên</label>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" required placeholder="Nguyễn Văn A"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email / Số
                        điện thoại</label>
                    <div class="relative">
                        <i
                            class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" required placeholder="example@gmail.com"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mật
                        khẩu</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" required placeholder="Tối thiểu 6 ký tự"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Xác nhận mật
                        khẩu</label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" required placeholder="Nhập lại mật khẩu"
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800 text-sm">
                    </div>
                </div>

                <div class="text-xs text-slate-500 leading-relaxed">
                    Bằng việc đăng ký, bạn đồng ý với <a href="#"
                        class="text-brand-600 font-medium hover:underline">Điều khoản dịch vụ</a> & <a href="#"
                        class="text-brand-600 font-medium hover:underline">Chính sách bảo mật</a> của RentHome.
                </div>

                <button type="submit"
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-bold text-sm shadow-md hover:shadow-glow transition-all">
                    Tạo Tài Khoản
                </button>

                <p class="text-center text-xs text-slate-500 mt-4">
                    Đã có tài khoản?
                    <button type="button" onclick="switchAuthTab('login')"
                        class="font-bold text-brand-600 hover:underline">Đăng nhập</button>
                </p>
            </form>
        </div>
    </div>

    <!-- MODAL THÊM BÀI ĐĂNG NỔI BẬT (DÀNH CHO BAN QUẢN LÝ) -->
    <div id="modal-add-featured-post"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-lg">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Thêm Bài Đăng Nổi Bật</h3>
                        <p class="text-xs text-slate-500 font-medium">Cấu hình bài đăng hiển thị nổi bật trên trang chủ (Ban Quản Lý)</p>
                    </div>
                </div>
                <button onclick="closeAddFeaturedModal()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="form-add-featured" onsubmit="handleSaveFeaturedPost(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tiêu đề bài đăng <span class="text-rose-500">*</span></label>
                    <input type="text" id="feat-title" required placeholder="Ví dụ: Villa Thảo Điền 4PN View Sông Đắc Địa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Giá thuê <span class="text-rose-500">*</span></label>
                        <input type="text" id="feat-price" required placeholder="Ví dụ: 25.0 triệu"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Huy hiệu (Tag) <span class="text-rose-500">*</span></label>
                        <select id="feat-tag"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none bg-white">
                            <option value="HOT NỔI BẬT">🔥 HOT NỔI BẬT</option>
                            <option value="VIP CHÍNH CHỦ">⭐ VIP CHÍNH CHỦ</option>
                            <option value="PENTHOUSE VIP">🏰 PENTHOUSE VIP</option>
                            <option value="VILLA LUXURY">🏡 VILLA LUXURY</option>
                            <option value="SHOPHOUSE HOT">🏬 SHOPHOUSE HOT</option>
                            <option value="GIÁ TỐT NHẤT">💰 GIÁ TỐT NHẤT</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Địa chỉ / Khu vực <span class="text-rose-500">*</span></label>
                    <input type="text" id="feat-location" required placeholder="Ví dụ: Thảo Điền, Quận 2, TP. Hồ Chí Minh"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Link Hình Ảnh (URL)</label>
                    <input type="url" id="feat-image" placeholder="https://images.unsplash.com/..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none mb-2">
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="text-slate-400 font-medium">Gợi ý mẫu ảnh:</span>
                        <button type="button" onclick="setPresetImage('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80')" class="text-brand-600 underline font-semibold hover:text-brand-700 cursor-pointer">Mẫu Villa</button>
                        <button type="button" onclick="setPresetImage('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80')" class="text-brand-600 underline font-semibold hover:text-brand-700 cursor-pointer">Mẫu Penthouse</button>
                        <button type="button" onclick="setPresetImage('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80')" class="text-brand-600 underline font-semibold hover:text-brand-700 cursor-pointer">Mẫu Căn Hộ</button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Số phòng ngủ</label>
                        <input type="text" id="feat-beds" value="2 PN" placeholder="2 PN"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Số phòng tắm</label>
                        <input type="text" id="feat-baths" value="2 WC" placeholder="2 WC"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Diện tích</label>
                        <input type="text" id="feat-area" value="85 m²" placeholder="85 m²"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Ghi chú kiểm duyệt Ban Quản Lý</label>
                    <input type="text" id="feat-note" placeholder="Ví dụ: Đã xác thực chính chủ • Giảm 5% hợp đồng dài hạn"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-800 outline-none">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="resetFeaturedPostsToDefault()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors mr-auto cursor-pointer">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Khôi phục mặc định
                    </button>
                    <button type="button" onclick="closeAddFeaturedModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs shadow-lg transition-all cursor-pointer">
                        <i class="fa-solid fa-check mr-1.5"></i> Thêm Nổi Bật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/province-picker.js') }}"></script>
    <script>
        // Dynamic Management for Featured Posts Section (Dành cho Ban Quản Lý)
        // Đã xóa bỏ mảng tĩnh DEFAULT_FEATURED_POSTS

        async function fetchFeaturedPosts() {
            try {
                const response = await fetch('/api/posts/featured?_t=' + Date.now(), { cache: 'no-store' });
                if (response.ok) {
                    const data = await response.json();
                    return Array.isArray(data) ? data : [];
                }
            } catch (error) {
                console.error("Lỗi khi fetch bài đăng nổi bật:", error);
            }
            return [];
        }
        let allRealPosts = [];
        let currentDisplayCount = 6;
        let hasLoadedMore = false;

        let activeFilterParams = null;

        async function fetchRealPosts(filterParams = null) {
            const container = document.getElementById('real-posts-container');
            if (!container) return;

            activeFilterParams = filterParams;
            currentDisplayCount = 6;
            hasLoadedMore = false;

            // Hiệu ứng Loading dạng Skeleton Loader (thay cho spinner tròn)
            const skeletonTpl = document.getElementById('real-posts-skeleton-template');
            if (skeletonTpl) {
                container.innerHTML = skeletonTpl.innerHTML;
            }

            try {
                const params = new URLSearchParams();
                if (filterParams) {
                    if (filterParams.location && filterParams.location !== 'Tất cả địa điểm') {
                        params.append('location', filterParams.location);
                    }
                    if (filterParams.type) {
                        params.append('type', filterParams.type);
                    }
                    if (filterParams.price_range) {
                        params.append('price_range', filterParams.price_range);
                    }
                }

                const queryStr = params.toString();
                const url = '/api/posts/approved' + (queryStr ? '?' + queryStr + '&_t=' + Date.now() : '?_t=' + Date.now());

                // Cập nhật đường dẫn nút "Xem thêm" để giữ nguyên tiêu chí lọc khi chuyển sang trang /baidang
                const goToBaidangBtn = document.getElementById('go-to-baidang-btn');
                if (goToBaidangBtn) {
                    goToBaidangBtn.href = '/baidang' + (queryStr ? '?' + queryStr : '');
                }

                const response = await fetch(url, { cache: 'no-store' });
                if (response.ok) {
                    const data = await response.json();
                    const posts = data.posts || [];
                    allRealPosts = posts;

                    // Cập nhật banner thông báo tiêu chí tìm kiếm
                    updateSearchResultsBanner(filterParams, posts.length);

                    if (!Array.isArray(posts) || posts.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border border-slate-200/80 shadow-xs">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl">
                                    <i class="fa-solid fa-house-circle-xmark"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-2">Chưa tìm thấy bất động sản phù hợp</h3>
                                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Không có bài đăng nào khớp với các tiêu chí tìm kiếm bạn đã chọn. Hãy thử chọn "Tất cả địa điểm" hoặc mở rộng khoảng giá.</p>
                                <button type="button" onclick="resetSearchFilters()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                                    <i class="fa-solid fa-rotate-left"></i> Xem tất cả bài đăng
                                </button>
                            </div>
                        `;
                        const loadMoreContainer = document.getElementById('load-more-container');
                        if (loadMoreContainer) loadMoreContainer.style.display = 'none';
                        return;
                    }

                    const loadMoreContainer = document.getElementById('load-more-container');
                    if (loadMoreContainer) loadMoreContainer.style.display = 'block';
                    
                    renderPosts();
                } else {
                    container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10 font-bold">Lỗi khi tải dữ liệu bài đăng!</p>';
                }
            } catch (error) {
                console.error('Error fetching real posts:', error);
                container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10 font-bold">Không thể kết nối đến máy chủ!</p>';
            } finally {
                const searchBtnText = document.getElementById('search-btn-text');
                if (searchBtnText) searchBtnText.innerText = 'Tìm kiếm';
            }
        }

        function updateSearchResultsBanner(filterParams, count) {
            const banner = document.getElementById('search-results-banner');
            const countEl = document.getElementById('search-results-count');
            const tagsEl = document.getElementById('search-active-tags');
            if (!banner || !countEl || !tagsEl) return;

            const hasFilter = filterParams && (
                (filterParams.location && filterParams.location !== 'Tất cả địa điểm') ||
                filterParams.type ||
                filterParams.price_range
            );

            if (!hasFilter) {
                banner.classList.add('hidden');
                return;
            }

            banner.classList.remove('hidden');
            countEl.innerHTML = `Tìm thấy <span class="text-brand-600 font-extrabold text-base">${count}</span> bài đăng phù hợp:`;

            let tagsHtml = '';
            if (filterParams.location && filterParams.location !== 'Tất cả địa điểm') {
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-brand-700 border border-emerald-200 text-xs font-bold flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-brand-500"></i> ${filterParams.location}</span>`;
            }
            if (filterParams.type) {
                const typeLabels = {
                    'can_ho': 'Căn hộ / Chung cư',
                    'nha_nguyen_can': 'Nhà nguyên căn',
                    'nha_rieng': 'Nhà nguyên căn',
                    'phong_tro': 'Phòng trọ / Nhà trọ',
                    'nha_tro': 'Phòng trọ / Nhà trọ',
                    'cum_nha_lien_ke': 'Biệt thự / Nhà liền kề',
                    'biet_thu': 'Biệt thự / Nhà liền kề',
                    'mat_bang': 'Mặt bằng kinh doanh'
                };
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold flex items-center gap-1.5"><i class="fa-solid fa-layer-group text-blue-500"></i> ${typeLabels[filterParams.type] || filterParams.type}</span>`;
            }
            if (filterParams.price_range) {
                const priceLabels = {
                    '1': 'Dưới 3 triệu',
                    '2': '3 - 7 triệu',
                    '3': '7 - 15 triệu',
                    '4': 'Trên 15 triệu'
                };
                tagsHtml += `<span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold flex items-center gap-1.5"><i class="fa-solid fa-money-bill-wave text-amber-500"></i> ${priceLabels[filterParams.price_range] || filterParams.price_range}</span>`;
            }
            tagsEl.innerHTML = tagsHtml;
        }

        function renderPosts() {
            const container = document.getElementById('real-posts-container');
            const loadMoreBtn = document.getElementById('load-more-btn');
            const goToBaidangBtn = document.getElementById('go-to-baidang-btn');
            
            let html = '';
            const postsToShow = allRealPosts.slice(0, currentDisplayCount);
            
            postsToShow.forEach(post => {
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
                        // ĐOẠN CODE MỚI
                        let imgUrl = post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                        let unitText = 'đ/tháng';
                        if (post.price_unit === '2_nam') unitText = 'đ/2 năm';
                        else if (post.price_unit === '3_nam') unitText = 'đ/3 năm';
                        else if (post.price_unit === '5_nam') unitText = 'đ/5 năm';
                        else if (post.price_unit === 'tren_5_nam') unitText = 'đ/trên 5 năm';
                        else if (post.price_unit === 'nam') unitText = 'đ/năm';
                        else if (post.price_unit === 'm2') unitText = 'đ/m²';
                        else if (post.price_unit === 'tong') unitText = 'VNĐ';
                        const price = new Intl.NumberFormat('vi-VN').format(Number(post.price) || 0) + ' ' + unitText;
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

            if (!loadMoreBtn || !goToBaidangBtn) return;

            if (hasLoadedMore) {
                loadMoreBtn.style.display = 'none';
                goToBaidangBtn.style.display = 'inline-flex';
                goToBaidangBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.style.display = 'inline-flex';
                goToBaidangBtn.style.display = 'none';
            }
        }

        function loadMorePosts() {
            if (!hasLoadedMore) {
                currentDisplayCount = 12;
                hasLoadedMore = true;
                renderPosts();
            }
        }

        const IS_ADMIN_USER = @json(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->isAdmin()));
        let featuredAutoPlayTimer = null;
        let isFeaturedScrolling = false;

        function startFeaturedAutoPlay() {
            stopFeaturedAutoPlay();
            featuredAutoPlayTimer = setInterval(() => {
                scrollFeaturedPosts('right');
            }, 3500);
        }

        function stopFeaturedAutoPlay() {
            if (featuredAutoPlayTimer) {
                clearInterval(featuredAutoPlayTimer);
                featuredAutoPlayTimer = null;
            }
        }



        async function renderFeaturedPostsSection(forceSkeleton = false) {
            const track = document.getElementById('featured-posts-track');
            const slider = document.getElementById('featured-posts-slider');
            if (!track) return;

            stopFeaturedAutoPlay();

            // Hiển thị Skeleton Loader nếu track chưa có hoặc khi yêu cầu tải lại
            const skeletonTpl = document.getElementById('featured-posts-skeleton-template');
            if (skeletonTpl && (forceSkeleton || track.children.length === 0 || !track.querySelector('.animate-pulse'))) {
                track.innerHTML = skeletonTpl.innerHTML;
            }

            const posts = await fetchFeaturedPosts();
            if (!posts || posts.length === 0) {
                track.innerHTML = '<p class="w-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                return;
            }

            track.style.transition = 'none';
            track.style.transform = 'translate3d(0, 0, 0)';

            track.innerHTML = posts.map(post => {
                const postOwnerId = String(post.user_id || '');
                const isEnterprise = window.currentUserType === 'doanhnghiep';
                const isMyPost = Boolean(window.currentUserId && postOwnerId && postOwnerId === String(window.currentUserId));
                const canFavorite = !isEnterprise && !Boolean(window.isStaffUser) && !isMyPost;
                const isFav = window.RentHomeFavorites && window.RentHomeFavorites.has(post.id);

                let favBtnHtml = '';
                if (canFavorite) {
                    favBtnHtml = `
                        <!-- Favorite Button for Featured Post -->
                        <button type="button" 
                                onclick="window.RentHomeFavorites.toggle('${post.id}', event, '${postOwnerId}')" 
                                data-fav-id="${post.id}"
                                data-owner-id="${postOwnerId}"
                                class="fav-toggle-btn absolute top-4 right-4 z-10 w-9 h-9 rounded-full ${isFav ? 'bg-white text-rose-500 shadow-md ring-2 ring-rose-100' : 'bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-md'} flex items-center justify-center transition-all duration-200 active:scale-90 group/heart"
                                title="${isFav ? 'Bỏ yêu thích' : 'Yêu thích bài đăng'}">
                            <i class="${isFav ? 'fa-solid fa-heart text-base text-rose-500' : 'fa-regular fa-heart text-base text-slate-400 group-hover/heart:text-rose-500'} transition-transform duration-200 group-hover/heart:scale-110"></i>
                        </button>
                    `;
                }

                return `
                <div class="w-[310px] sm:w-[350px] md:w-[370px] shrink-0 bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group relative">
                    ${IS_ADMIN_USER ? `
                    <!-- Delete/Unfeature button for Admin only -->
                    <button onclick="removeFeaturedPost('${post.id}')" title="Gỡ khỏi danh sách nổi bật (Admin)"
                        class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-slate-900/70 hover:bg-rose-600 text-white backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-md cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                    ` : ''}

                    <!-- Image Frame -->
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="${post.image}" loading="lazy" alt="${post.title}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-amber-500 text-white font-extrabold text-[11px] uppercase tracking-wider shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-star text-amber-200 text-[10px]"></i> ${post.tag || 'NỔI BẬT'}
                        </span>

                        ${favBtnHtml}
                        <div class="absolute bottom-4 left-4 bg-slate-900/85 backdrop-blur-md text-white px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1 shadow-md">
                            <span class="text-amber-400 text-sm font-black">${post.price}</span> ${post.period || '/ tháng'}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-xs font-semibold text-amber-600 mb-2">
                                <i class="fa-solid fa-shield-check text-emerald-500"></i> ${post.note || 'Đã kiểm duyệt bởi Ban Quản Lý'}
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                                ${post.title}
                            </h3>
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i> ${post.location}
                            </p>
                        </div>

                        <!-- Specs Row -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-slate-600 text-xs font-semibold">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bed text-amber-500"></i> ${post.beds}</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bath text-amber-500"></i> ${post.baths}</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-vector-square text-amber-500"></i> ${post.area}</span>
                        </div>

                        <!-- Card Action -->
                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Ưu tiên hiển thị
                            </span>
                            <button onclick="window.location.href='/chitietbaidang/${post.id}'"
                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-amber-500 text-white text-xs font-bold transition-colors shadow-sm cursor-pointer">
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>
                `;
            }).join('');

            if (slider) {
                slider.onmouseenter = stopFeaturedAutoPlay;
                slider.onmouseleave = startFeaturedAutoPlay;
            }

            if (window.RentHomeFavorites) {
                window.RentHomeFavorites.updateCardButtons();
            }

            startFeaturedAutoPlay();
        }

        function scrollFeaturedPosts(direction) {
            const track = document.getElementById('featured-posts-track');
            if (!track || isFeaturedScrolling) return;

            const cards = track.children;
            if (cards.length <= 1) return;

            isFeaturedScrolling = true;

            const firstCard = cards[0];
            const trackGap = parseFloat(window.getComputedStyle(track).gap) || 24;
            const step = firstCard.offsetWidth + trackGap;

            if (direction === 'right') {
                track.style.transition = 'transform 0.7s cubic-bezier(0.25, 1, 0.5, 1)';
                track.style.transform = `translate3d(-${step}px, 0, 0)`;

                const handleTransitionEnd = (e) => {
                    if (e.target !== track) return;
                    track.removeEventListener('transitionend', handleTransitionEnd);

                    track.style.transition = 'none';
                    if (track.firstElementChild) {
                        track.appendChild(track.firstElementChild);
                    }
                    track.style.transform = 'translate3d(0, 0, 0)';

                    // Force browser reflow to flush transform
                    void track.offsetHeight;
                    isFeaturedScrolling = false;
                };

                track.addEventListener('transitionend', handleTransitionEnd);
            } else {
                track.style.transition = 'none';
                const lastCard = track.lastElementChild;
                if (lastCard) {
                    track.insertBefore(lastCard, track.firstElementChild);
                }
                track.style.transform = `translate3d(-${step}px, 0, 0)`;

                // Force browser reflow
                void track.offsetHeight;

                track.style.transition = 'transform 0.7s cubic-bezier(0.25, 1, 0.5, 1)';
                track.style.transform = 'translate3d(0, 0, 0)';

                const handleTransitionEnd = (e) => {
                    if (e.target !== track) return;
                    track.removeEventListener('transitionend', handleTransitionEnd);
                    isFeaturedScrolling = false;
                };

                track.addEventListener('transitionend', handleTransitionEnd);
            }
        }

        function openAddFeaturedModal() {
            const modal = document.getElementById('modal-add-featured-post');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAddFeaturedModal() {
            const modal = document.getElementById('modal-add-featured-post');
            if (modal) modal.classList.add('hidden');
        }

        function setPresetImage(url) {
            const imgInput = document.getElementById('feat-image');
            if (imgInput) imgInput.value = url;
        }

        function handleSaveFeaturedPost(e) {
            e.preventDefault();
            const title = document.getElementById('feat-title').value.trim();
            const price = document.getElementById('feat-price').value.trim();
            const tag = document.getElementById('feat-tag').value;
            const location = document.getElementById('feat-location').value.trim();
            let image = document.getElementById('feat-image').value.trim();
            const beds = document.getElementById('feat-beds').value.trim() || '2 PN';
            const baths = document.getElementById('feat-baths').value.trim() || '2 WC';
            const area = document.getElementById('feat-area').value.trim() || '80 m²';
            const note = document.getElementById('feat-note').value.trim() || 'Ưu tiên hiển thị bởi Quản Lý';

            if (!image) {
                image = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
            }

            const newPost = {
                id: 'feat-' + Date.now(),
                title,
                price,
                period: '/ tháng',
                location,
                image,
                tag,
                beds,
                baths,
                area,
                note,
                verified: true
            };

            // Scroll slider back to start
            const container = document.getElementById('featured-posts-slider');
            if (container) container.scrollTo({ left: 0, behavior: 'smooth' });

            showToast('Tính năng lưu cần được kết nối với API thực tế.', 'info');
        }

        async function removeFeaturedPost(id) {
            const confirmed = await showConfirmDialog({
                title: 'Gỡ bài đăng nổi bật?',
                text: 'Bạn có chắc chắn muốn gỡ bài đăng này khỏi danh sách bài đăng nổi bật?',
                icon: 'warning',
                confirmText: 'Gỡ bài đăng',
                cancelText: 'Hủy',
                danger: true
            });
            if (!confirmed) return;
            // API thật sẽ được gọi ở đây, ví dụ: fetch(`/api/posts/featured/${id}`, { method: 'DELETE' })
            showToast('Cần API để xóa bài đăng nổi bật thật.', 'info');
        }

        
        // Crossfade background transition mỗi 3.5 giây (Lazy-loaded)
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-bg-slide');

        function loadSlideBg(slide) {
            if (slide && slide.dataset.bg && !slide.style.backgroundImage) {
                slide.style.backgroundImage = slide.dataset.bg;
            }
        }

        window.addEventListener('load', () => {
            setTimeout(() => {
                slides.forEach(s => loadSlideBg(s));
            }, 1000);
        });

        function nextSlide() {
            if (!slides || slides.length === 0) return;
            slides[currentSlide].classList.remove('opacity-100');
            slides[currentSlide].classList.add('opacity-0');

            currentSlide = (currentSlide + 1) % slides.length;
            loadSlideBg(slides[currentSlide]);

            slides[currentSlide].classList.remove('opacity-0');
            slides[currentSlide].classList.add('opacity-100');
        }

        if (slides.length > 0) {
            setInterval(nextSlide, 3500);
        }

        function toggleWishlist(btn) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid', 'text-rose-500');
            } else {
                icon.classList.remove('fa-solid', 'text-rose-500');
                icon.classList.add('fa-regular');
            }
        }

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

        function resetSearchButtonState() {
            const searchBtn = document.getElementById('search-submit-btn');
            if (searchBtn) {
                searchBtn.innerHTML = '<i class="fa-solid fa-magnifying-glass text-sm text-emerald-100 group-hover:text-white group-hover:scale-110 transition-transform duration-200 shrink-0"></i> <span id="search-btn-text" class="tracking-wide whitespace-nowrap">Tìm kiếm</span>';
                searchBtn.removeAttribute('disabled');
                searchBtn.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
            }
        }

        // Tự động khôi phục trạng thái nút tìm kiếm khi người dùng bấm nút Quay lại (Back) trên trình duyệt
        window.addEventListener('pageshow', function(event) {
            resetSearchButtonState();
        });

        window.addEventListener('pagehide', function() {
            resetSearchButtonState();
        });

        window.addEventListener('popstate', function() {
            resetSearchButtonState();
        });

        function handleSearch() {
            const locationInput = document.getElementById('selected-location-input');
            const typeSelect = document.getElementById('search-property-type');
            const priceSelect = document.getElementById('search-price-range');
            const searchBtn = document.getElementById('search-submit-btn');

            const location = locationInput ? locationInput.value.trim() : '';
            const type = typeSelect ? typeSelect.value.trim() : '';
            const price_range = priceSelect ? priceSelect.value.trim() : '';

            if (searchBtn) {
                searchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm shrink-0"></i> <span id="search-btn-text" class="whitespace-nowrap">Đang tìm...</span>';
                searchBtn.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
            }

            const params = new URLSearchParams();
            if (location && location !== 'Tất cả địa điểm' && location !== 'Tất cả tỉnh thành') {
                params.append('location', location);
            }
            if (type) {
                params.append('type', type);
            }
            if (price_range) {
                params.append('price_range', price_range);
            }

            const queryString = params.toString();
            window.location.href = '/baidang' + (queryString ? '?' + queryString : '');
        }

        function resetSearchFilters() {
            resetSearchButtonState();
            if (typeof setSelectedLocation === 'function') {
                setSelectedLocation('');
            } else {
                const locationInput = document.getElementById('selected-location-input');
                if (locationInput) locationInput.value = '';
            }

            const typeSelect = document.getElementById('search-property-type');
            if (typeSelect) typeSelect.value = '';

            const priceSelect = document.getElementById('search-price-range');
            if (priceSelect) priceSelect.value = '';

            const tabs = document.querySelectorAll('#search-tabs .tab-btn');
            tabs.forEach((t, idx) => {
                if (idx === 0) {
                    t.classList.add('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                    t.classList.remove('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
                } else {
                    t.classList.remove('active', 'bg-brand-600', 'text-white', 'shadow-sm', 'font-bold');
                    t.classList.add('text-slate-600', 'hover:bg-slate-100', 'font-semibold');
                }
            });

            fetchRealPosts(null);

            const featuredSection = document.getElementById('featured');
            if (featuredSection) {
                featuredSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function openModal(title, price, location) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalPrice').innerText = price + ' / tháng';
            document.getElementById('modalLocation').innerText = location;
            document.getElementById('propertyModal').classList.remove('hidden');
            document.getElementById('propertyModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('propertyModal').classList.add('hidden');
            document.getElementById('propertyModal').classList.remove('flex');
        }

        // Xử lý Modal Đăng nhập / Đăng ký
        function openAuthModal(tab = 'login') {
            switchAuthTab(tab);
            document.getElementById('authModal').classList.remove('hidden');
            document.getElementById('authModal').classList.add('flex');
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
            document.getElementById('authModal').classList.remove('flex');
        }

        function switchAuthTab(tab) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');

                tabLoginBtn.classList.add('text-brand-600', 'border-brand-600');
                tabLoginBtn.classList.remove('text-slate-400', 'border-transparent');

                tabRegisterBtn.classList.remove('text-brand-600', 'border-brand-600');
                tabRegisterBtn.classList.add('text-slate-400', 'border-transparent');
            } else {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');

                tabRegisterBtn.classList.add('text-brand-600', 'border-brand-600');
                tabRegisterBtn.classList.remove('text-slate-400', 'border-transparent');

                tabLoginBtn.classList.remove('text-brand-600', 'border-brand-600');
                tabLoginBtn.classList.add('text-slate-400', 'border-transparent');
            }
        }

        function handleAuthSubmit(type) {
            if (type === 'login') {
                showToast('Đăng nhập thành công!', 'success');
            } else {
                showToast('Đăng ký tài khoản thành công!', 'success');
            }
            closeAuthModal();
        }


        // ==================== FETCH APPROVED NEWS CHO TRANG CHỦ ====================
        function formatNewsTimeAgo(dateStr) {
            if (!dateStr) return 'Vừa xong';
            const date = new Date(dateStr);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            if (diffMins < 1) return 'Vừa xong';
            if (diffMins < 60) return `${diffMins} phút trước`;
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return `${diffHours} giờ trước`;
            const diffDays = Math.floor(diffHours / 24);
            if (diffDays < 30) return `${diffDays} ngày trước`;
            return date.toLocaleDateString('vi-VN');
        }

        async function fetchApprovedNews() {
            const container = document.getElementById('approved-news-container');
            if (!container) return;

            // Hiển thị hiệu ứng Skeleton Loader nếu container chưa có Skeleton
            const skeletonTpl = document.getElementById('approved-news-skeleton-template');
            if (skeletonTpl && !container.querySelector('.animate-pulse')) {
                container.innerHTML = skeletonTpl.innerHTML;
            }

            try {
                const res = await fetch('/api/news/approved');
                const data = await res.json();
                if (!data.success || !data.articles || data.articles.length === 0) {
                    return;
                }

                const articles = data.articles;
                const featured = articles[0];
                const smallArticles = articles.slice(1, 5);

                const getSourceBadge = (source) => {
                    const s = (source || '').toLowerCase();
                    if (s.includes('vnexpress')) return 'bg-slate-900/80';
                    if (s.includes('batdongsan')) return 'bg-blue-600/90';
                    if (s.includes('cafef')) return 'bg-amber-600/90';
                    if (s.includes('dân trí') || s.includes('dan tri')) return 'bg-emerald-600/90';
                    return 'bg-brand-600/90';
                };

                let html = `
                    <!-- Left: Featured Main News Card (Extra Large Frame) -->
                    <article class="group relative rounded-3xl overflow-hidden min-h-[460px] lg:min-h-[540px] flex flex-col justify-between p-8 sm:p-10 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                        <img src="${featured.image_url || 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80'}"
                            loading="lazy"
                            alt="${featured.title}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            onerror="this.src='https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80'">
                        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/30 to-slate-950/90 group-hover:from-slate-950/80 transition-all"></div>

                        <!-- Top badges -->
                        <div class="relative z-10 flex items-center gap-3">
                            <span class="px-3.5 py-1.5 rounded-full bg-brand-600 text-white font-bold text-xs uppercase shadow-md">
                                ${featured.is_featured ? 'BÀI VIẾT NỔI BẬT' : (featured.category || 'TIN NỔI BẬT')}
                            </span>
                            <span class="px-3.5 py-1.5 rounded-full ${getSourceBadge(featured.source_name)} backdrop-blur-md text-white font-semibold text-xs">
                                ${featured.source_name || 'RentHome'}
                            </span>
                        </div>

                        <!-- Bottom content -->
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 text-xs text-slate-300 mb-3 font-medium">
                                <i class="fa-regular fa-clock"></i> ${formatNewsTimeAgo(featured.published_at || featured.created_at)}
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-black text-white group-hover:text-emerald-400 transition-colors leading-snug">
                                <a href="${featured.source_url || '#'}" target="_blank" rel="noopener noreferrer">
                                    ${featured.title}
                                </a>
                            </h3>
                            <p class="text-sm sm:text-base text-slate-200 mt-3 line-clamp-3 leading-relaxed">
                                ${featured.summary || ''}
                            </p>
                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a href="${featured.source_url || '#'}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-brand-600 text-white text-xs sm:text-sm font-bold shadow-md hover:bg-brand-700 transition-all">
                                    Đọc chi tiết <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    <!-- Right: 2x2 Grid of 4 Larger News Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-7">
                        ${smallArticles.map(art => `
                            <article class="group relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                                <img src="${art.image_url || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80'}"
                                    loading="lazy"
                                    alt="${art.title}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    onerror="this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80'">
                                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/30 to-slate-950/85 group-hover:from-slate-950/85 transition-all"></div>

                                <div class="relative z-10 flex items-center justify-between">
                                    <span class="px-3 py-1 rounded-full ${getSourceBadge(art.source_name)} backdrop-blur-sm text-[11px] font-bold text-white uppercase">
                                        ${art.source_name || 'Báo chí'}
                                    </span>
                                    <span class="text-xs font-medium text-slate-300">
                                        <i class="fa-regular fa-clock"></i> ${formatNewsTimeAgo(art.published_at || art.created_at)}
                                    </span>
                                </div>

                                <div class="relative z-10">
                                    <h3 class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                                        <a href="${art.source_url || '#'}" target="_blank" rel="noopener noreferrer">
                                            ${art.title}
                                        </a>
                                    </h3>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-xs font-medium text-slate-300">${art.category || 'Tin tức'}</span>
                                        <a href="${art.source_url || '#'}" target="_blank" rel="noopener noreferrer"
                                            class="text-xs text-white group-hover:text-emerald-400 flex items-center gap-1.5 font-semibold transition-colors">
                                            Đọc chi tiết <i class="fa-solid fa-arrow-up-right-from-square text-xs transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        `).join('')}
                    </div>
                `;

                container.innerHTML = html;
            } catch (err) {
                console.error('Lỗi khi tải tin tức đã duyệt:', err);
            }
        }

        // Initialize Featured Posts & News on load
        document.addEventListener('DOMContentLoaded', function() {
            // Đọc params nếu có
            const urlParams = new URLSearchParams(window.location.search);
            const initialLoc = urlParams.get('location');
            if (initialLoc && initialLoc !== 'Tất cả địa điểm' && initialLoc !== 'Tất cả tỉnh thành') {
                if (typeof setSelectedLocation === 'function') {
                    setSelectedLocation(initialLoc);
                }
            }
            const initialType = urlParams.get('type');
            if (initialType) {
                const typeSelect = document.getElementById('search-property-type');
                if (typeSelect) typeSelect.value = initialType;
                syncTabWithType(initialType);
            }
            const initialPrice = urlParams.get('price_range') || urlParams.get('price');
            if (initialPrice && document.getElementById('search-price-range')) {
                document.getElementById('search-price-range').value = initialPrice;
            }

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

            resetSearchButtonState();
            renderFeaturedPostsSection();
            fetchRealPosts();
            fetchApprovedNews();
        });

        // Tự động làm mới dữ liệu lượt xem và lượt thích thật khi người dùng nhấn xem bài đăng và thoát/quay lại
        window.addEventListener('pageshow', function(event) {
            fetchRealPosts();
            renderFeaturedPostsSection();
        });
    </script>
</body>

</html>

