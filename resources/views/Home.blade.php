<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
        class="min-h-screen pt-32 pb-20 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center relative overflow-hidden bg-slate-950">

        <!-- Background Crossfade Images -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            @if (!empty($themeSettings['banner_url']))
                <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-100"
                    style="background-image: url('{{ $themeSettings['banner_url'] }}');">
                </div>
                <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-0"
                    style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80');">
                </div>
            @else
                <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-100"
                    style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80');">
                </div>
            @endif
            <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-0"
                style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80');">
            </div>
            <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-0"
                style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80');">
            </div>
            <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-0"
                style="background-image: url('https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1920&q=80');">
            </div>
            <div class="hero-bg-slide absolute inset-0 bg-cover bg-center opacity-0"
                style="background-image: url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1920&q=80');">
            </div>

            <!-- Lớp phủ cố định giúp chữ luôn rõ nét -->
            <div class="absolute inset-0 bg-slate-950/70 pointer-events-none"></div>
        </div>

        <!-- CỐ ĐỊNH CHỮ VÀ NỘI DUNG -->
        <div class="max-w-4xl mx-auto text-center mt-8 relative z-10">
            <span
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-6 shadow-md">
                <i class="fa-solid fa-bolt text-yellow-400"></i> Hơn 50,000+ Căn nhà sẵn sàng cho thuê
            </span>
            <h1
                class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight drop-shadow-md">
                {{ $themeSettings['banner_title'] ?? 'Tìm Ngôi Nhà Hoàn Hảo Cho Cuộc Sống Tương Lai' }}
            </h1>
            <p class="mt-4 text-lg sm:text-xl text-slate-200 max-w-2xl mx-auto font-normal drop-shadow">
                {{ $themeSettings['banner_subtitle'] ?? 'Khám phá hàng ngàn phòng trọ, căn hộ cao cấp, và nhà nguyên căn chính chủ với mức giá tốt nhất, không qua trung gian.' }}
            </p>
        </div>

        <!-- CỐ ĐỊNH KHUNG TÌM KIẾM -->
        <div class="w-full max-w-5xl mx-auto mt-10 relative z-30">
            <div class="glass-panel p-4 sm:p-6 rounded-3xl shadow-2xl border border-white/40">
                <!-- Search Tabs -->
                <div class="flex items-center gap-3 mb-6 overflow-x-auto pb-2 border-b border-slate-200/60">
                    <button
                        class="tab-btn active px-5 py-2.5 rounded-xl font-bold text-sm bg-brand-600 text-white shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-building"></i> Tất cả loại hình
                    </button>
                    <button
                        class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-city"></i> Căn hộ / Chung cư
                    </button>
                    <button
                        class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-house"></i> Nhà nguyên căn
                    </button>
                    <button
                        class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-bed"></i> Phòng trọ
                    </button>
                </div>

                <!-- Search Inputs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Location Selector -->
                    <div class="relative z-40" id="location-select-container">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Khu vực /
                            Tỉnh thành</label>
                        <input type="hidden" id="selected-location-input" name="location" value="TP. Hồ Chí Minh">

                        <!-- Toggle Button -->
                        <button type="button" id="location-dropdown-btn" onclick="toggleLocationDropdown(event)"
                            class="w-full pl-10 pr-9 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm text-left flex items-center justify-between transition-all cursor-pointer hover:bg-slate-100/80">
                            <i
                                class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-600"></i>
                            <span id="selected-location-text" class="truncate font-bold text-slate-800">TP. Hồ Chí
                                Minh</span>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 transition-transform duration-200"
                                id="location-dropdown-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div id="location-dropdown-panel"
                            class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden text-sm animate-fadeIn w-full sm:w-72">
                            <!-- Search Bar inside dropdown -->
                            <div class="p-2.5 border-b border-slate-200 bg-white sticky top-0 z-20 shadow-xs">
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" id="location-search-input" oninput="filterProvinces()"
                                        placeholder="Tìm tỉnh / thành phố..."
                                        class="w-full pl-8 pr-7 py-2 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium text-slate-800">
                                    <button type="button" id="clear-search-btn" onclick="clearLocationSearch()"
                                        class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- List of Provinces & Cities -->
                            <div class="max-h-64 overflow-y-auto custom-scrollbar" id="provinces-list">
                                <!-- Options populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Loại Bất
                            Động Sản</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm">
                                <option value="">Tất cả kiểu nhà</option>
                                <option value="ch">Căn hộ cao cấp</option>
                                <option value="nnc">Nhà nguyên căn</option>
                                <option value="pt">Phòng trọ sinh viên</option>
                                <option value="villa">Biệt thự / Villa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Mức Giá
                            Mẫu</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-money-bill-wave absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm">
                                <option value="">Tất cả mức giá</option>
                                <option value="1">Dưới 3 triệu / tháng</option>
                                <option value="2">3 - 7 triệu / tháng</option>
                                <option value="3">7 - 15 triệu / tháng</option>
                                <option value="4">Trên 15 triệu / tháng</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button onclick="handleSearch()"
                            class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-bold text-sm shadow-lg hover:shadow-glow transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỐ ĐỊNH THÔNG SỐ -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl w-full text-center relative z-10">
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-3xl font-extrabold text-white">50k+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Tin đăng cho thuê</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-3xl font-extrabold text-emerald-400">98%</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Khách thuê hài lòng</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-3xl font-extrabold text-white">120k+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Người dùng hàng tháng</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-sm">
                <p class="text-3xl font-extrabold text-emerald-400">0%</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Phí môi giới ẩn</p>
            </div>
        </div>
    </section>

    <!-- Featured Properties Grid Section -->
    <section id="featured"
        class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto bg-slate-100/60 rounded-3xl border border-slate-200/60">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Lựa chọn hàng đầu</span>
                <h2 class="text-3xl font-black text-slate-900 mt-1">Danh Sách Nhà Cho Thuê </h2>
            </div>

            <!-- Quick Filter Tags -->
            <div class="mt-4 md:mt-0 flex items-center gap-2 overflow-x-auto pb-2">
                <button class="px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs shadow-sm">Tất
                    cả</button>
                <button
                    class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">TP.
                    Hồ Chí Minh</button>
                <button
                    class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">Hà
                    Nội</button>
                <button
                    class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">Đà
                    Nẵng</button>
            </div>
        </div>

        <!-- Property Grid -->
        <div id="real-posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Dữ liệu nhà cho thuê sẽ được load bằng Javascript (fetchRealPosts) -->
        </div>

        <div class="mt-12 text-center">
            <a href="#"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-white border border-slate-300 hover:border-brand-500 font-bold text-sm text-slate-800 hover:text-brand-600 shadow-sm hover:shadow-md transition-all">
                Xem thêm các căn nhà khác <i class="fa-solid fa-chevron-down"></i>
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
                        alt="Không gian sống tuyệt đẹp" class="w-full h-auto object-cover">
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

    <!-- Featured Posts Slider Section (Bài Đăng Nổi Bật Được Ban Quản Lý Chọn) -->
    <section id="featured-posts-section"
        class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto my-12 bg-slate-100/60 rounded-3xl border border-slate-200/60 relative">
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
                @if(Auth::check() && ((Auth::user()->account_type ?? '') === 'admin' || (Auth::user()->role ?? '') === 'admin'))
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
                <!-- Dynamic JavaScript content generated via renderFeaturedPostsSection() -->
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
            <a href="https://batdongsan.com.vn/tin-thi-truong" target="_blank" rel="noopener noreferrer"
                class="mt-4 sm:mt-0 inline-flex items-center gap-2 font-bold text-sm text-brand-600 hover:text-brand-700">
                Xem thêm tin tức <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Featured Main News Card (Extra Large Frame) -->
            <article
                class="group relative rounded-3xl overflow-hidden min-h-[460px] lg:min-h-[540px] flex flex-col justify-between p-8 sm:p-10 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80"
                    alt="Xu hướng thuê căn hộ"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div
                    class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/30 to-slate-950/90 group-hover:from-slate-950/80 transition-all">
                </div>

                <!-- Top badges -->
                <div class="relative z-10 flex items-center gap-3">
                    <span
                        class="px-3.5 py-1.5 rounded-full bg-brand-600 text-white font-bold text-xs uppercase shadow-md">BÀI
                        VIẾT NỔI BẬT</span>
                    <span
                        class="px-3.5 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs">VnExpress</span>
                </div>

                <!-- Bottom content -->
                <div class="relative z-10">
                    <div class="flex items-center gap-2 text-xs text-slate-300 mb-3 font-medium">
                        <i class="fa-regular fa-clock"></i> 2 giờ trước
                    </div>
                    <h3
                        class="text-2xl sm:text-3xl font-black text-white group-hover:text-emerald-400 transition-colors leading-snug">
                        <a href="https://vnexpress.net/bat-dong-san" target="_blank" rel="noopener noreferrer">
                            Nhu cầu thuê chung cư và nhà trọ tại các đô thị lớn tiếp tục tăng mạnh
                        </a>
                    </h3>
                    <p class="text-sm sm:text-base text-slate-200 mt-3 line-clamp-3 leading-relaxed">
                        Thị trường bất động sản cho thuê ghi nhận lượng quan tâm tăng cao vào quý mới, đặc biệt ở phân
                        khúc studio và căn hộ 2 phòng ngủ tại TP.HCM và Hà Nội.
                    </p>
                    <div class="mt-6">
                        <a href="https://vnexpress.net/bat-dong-san" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-white/20 backdrop-blur-md text-white text-xs sm:text-sm font-bold border border-white/30 group-hover:bg-brand-600 group-hover:border-brand-600 transition-all">
                            Đọc bài viết đầy đủ <i
                                class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </article>

            <!-- Right: 2x2 Grid of 4 Larger News Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-7">
                <!-- Small News Card 1 -->
                <article
                    class="group relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                    <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80"
                        alt="Đại đô thị mới"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/30 to-slate-950/85 group-hover:from-slate-950/85 transition-all">
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <span
                            class="px-3 py-1 rounded-full bg-blue-600/90 backdrop-blur-sm text-[11px] font-bold text-white uppercase">Batdongsan</span>
                        <span class="text-xs font-medium text-slate-300"><i class="fa-regular fa-clock"></i> 5h
                            trước</span>
                    </div>

                    <div class="relative z-10">
                        <h3
                            class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                            <a href="https://batdongsan.com.vn/tin-thi-truong" target="_blank"
                                rel="noopener noreferrer">
                                Điểm mặt loạt dự án căn hộ sắp bàn giao mở ra nguồn cung nhà thuê mới
                            </a>
                        </h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300">Tin thị trường</span>
                            <i
                                class="fa-solid fa-arrow-right text-xs text-white group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </article>

                <!-- Small News Card 2 -->
                <article
                    class="group relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80"
                        alt="Luật nhà ở"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/30 to-slate-950/85 group-hover:from-slate-950/85 transition-all">
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <span
                            class="px-3 py-1 rounded-full bg-amber-600/90 backdrop-blur-sm text-[11px] font-bold text-white uppercase">CafeF</span>
                        <span class="text-xs font-medium text-slate-300"><i class="fa-regular fa-clock"></i> 1 ngày
                            trước</span>
                    </div>

                    <div class="relative z-10">
                        <h3
                            class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                            <a href="https://cafef.vn/bat-dong-san.chn" target="_blank" rel="noopener noreferrer">
                                Những lưu ý pháp lý quan trọng khi ký hợp đồng thuê nhà theo quy định mới
                            </a>
                        </h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300">Cẩm nang pháp lý</span>
                            <i
                                class="fa-solid fa-arrow-right text-xs text-white group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </article>

                <!-- Small News Card 3 -->
                <article
                    class="group relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                    <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80"
                        alt="Nhà trọ sinh viên"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/30 to-slate-950/85 group-hover:from-slate-950/85 transition-all">
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <span
                            class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-sm text-[11px] font-bold text-white uppercase">VnExpress</span>
                        <span class="text-xs font-medium text-slate-300"><i class="fa-regular fa-clock"></i> 2 ngày
                            trước</span>
                    </div>

                    <div class="relative z-10">
                        <h3
                            class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                            <a href="https://vnexpress.net/kinh-doanh/bat-dong-san" target="_blank"
                                rel="noopener noreferrer">
                                Xu hướng thuê trọ 'All-in-one' tích hợp tiện ích thông minh hút giới trẻ
                            </a>
                        </h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300">Xu hướng sống</span>
                            <i
                                class="fa-solid fa-arrow-right text-xs text-white group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </article>

                <!-- Small News Card 4 -->
                <article
                    class="group relative rounded-3xl overflow-hidden min-h-[255px] flex flex-col justify-between p-7 shadow-card hover:shadow-2xl transition-all duration-500 border border-slate-200/80">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80"
                        alt="Mặt bằng giá thuê mới"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-950/30 to-slate-950/85 group-hover:from-slate-950/85 transition-all">
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <span
                            class="px-3 py-1 rounded-full bg-emerald-600/90 backdrop-blur-sm text-[11px] font-bold text-white uppercase">Dân
                            Trí</span>
                        <span class="text-xs font-medium text-slate-300"><i class="fa-regular fa-clock"></i> 3 ngày
                            trước</span>
                    </div>

                    <div class="relative z-10">
                        <h3
                            class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                            <a href="https://dantri.com.vn/bat-dong-san.htm" target="_blank"
                                rel="noopener noreferrer">
                                Giá thuê nhà mặt phố và căn hộ dịch vụ tại trung tâm thiết lập mặt bằng mới
                            </a>
                        </h3>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300">Phân tích giá</span>
                            <i
                                class="fa-solid fa-arrow-right text-xs text-white group-hover:text-emerald-400 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner for Landlords -->
    <section id="post-house" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div
            class="relative rounded-3xl bg-gradient-to-r from-slate-900 via-brand-900 to-slate-950 p-8 sm:p-14 overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 max-w-2xl">
                <span
                    class="px-3.5 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-400/30 text-xs font-bold uppercase tracking-wider">Dành
                    cho chủ nhà</span>
                <h2 class="text-3xl sm:text-4xl font-black mt-4 leading-tight">
                    Bạn Có Phòng Cho Thuê? <br> Đăng Tin Ngay Hôm Nay!
                </h2>
                <p class="mt-3 text-slate-300 text-sm sm:text-base">
                    Tiếp cận hàng triệu người thuê nhà tiềm năng mỗi tháng. Đăng tin miễn phí, duyệt nhanh trong 15
                    phút.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    @auth
                        <a href="{{ route('dangbai') }}"
                            class="px-8 py-3.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-glow transition-all inline-flex items-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Đăng tin cho thuê miễn phí
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-8 py-3.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-glow transition-all inline-flex items-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Đăng tin cho thuê miễn phí
                        </a>
                    @endauth
                    @auth
                        <a href="{{ url('Overview') }}"
                            class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors inline-flex items-center">
                            Tìm hiểu thêm
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors inline-flex items-center">
                            Tìm hiểu thêm
                        </a>
                    @endauth
                </div>
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
                <button onclick="alert('Đã gửi yêu cầu liên hệ chủ nhà!')"
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
    <script>
        // Dynamic Management for Featured Posts Section (Dành cho Ban Quản Lý)
        // Đã xóa bỏ mảng tĩnh DEFAULT_FEATURED_POSTS

        async function fetchFeaturedPosts() {
            try {
                const response = await fetch('/api/posts/featured');
                if (response.ok) {
                    const data = await response.json();
                    return Array.isArray(data) ? data : [];
                }
            } catch (error) {
                console.error("Lỗi khi fetch bài đăng nổi bật:", error);
            }
            return [];
        }

                async function fetchRealPosts() {
            const container = document.getElementById('real-posts-container');
            if (!container) return;
            try {
                const response = await fetch('/api/posts/approved');
                if (response.ok) {
                    const data = await response.json();
                    const posts = data.posts || [];
                    if (!Array.isArray(posts) || posts.length === 0) {
                        container.innerHTML = '<p class="col-span-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                        return;
                    }
                    
                    let html = '';
                    posts.forEach(post => {
                                                const userName = post.user ? (post.user.account_name || post.user.username) : 'Người dùng';
                        const userType = post.user ? (post.user.account_type === 'enterprise' ? 'Doanh nghiệp' : 'Cá nhân') : 'Cá nhân';
                        const userBadgeClass = (post.user && post.user.account_type === 'enterprise') ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-500 border border-slate-200';
                        const userIconColor = (post.user && post.user.account_type === 'enterprise') ? 'text-blue-500 bg-blue-50' : 'text-slate-500 bg-slate-100';
                        const userIcon = (post.user && post.user.account_type === 'enterprise') ? 'fa-building' : 'fa-user';
                        const imgUrl = (post.images && post.images.length > 0) ? post.images[0] : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                        let unitText = 'đ/tháng';
                        if (post.price_unit === 'nam') unitText = 'đ/năm';
                        else if (post.price_unit === 'm2') unitText = 'đ/m²';
                        else if (post.price_unit === 'tong') unitText = 'VNĐ';
                        const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' ' + unitText;
                        const address = [post.address, post.ward, post.district, post.province].filter(Boolean).join(', ');
                        
                        html += `
                            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group relative">
                                <div class="relative aspect-[4/3] overflow-hidden">
                                    <img src="${imgUrl}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl font-bold text-xs shadow-sm">
                                        <span class="text-brand-600">${price}</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-slate-900 mb-2 leading-tight truncate">
                                        ${post.title}
                                    </h3>
                                    <div class="flex flex-col gap-1.5 mb-4 text-xs font-semibold text-slate-500">
                                        <div class="flex items-center gap-2 truncate">
                                            <i class="fa-solid fa-location-dot w-4 text-center"></i>
                                            <span>${address}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-combined w-4 text-center"></i>
                                            <span>${post.area} m²</span>
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
                                    <a href="#" class="block w-full text-center py-2.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-sm transition-all duration-300 border border-brand-100 hover:border-brand-600">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10">Lỗi khi tải dữ liệu bài đăng!</p>';
                }
            } catch (error) {
                console.error('Error fetching real posts:', error);
            }
        }

        const IS_ADMIN_USER = @json(Auth::check() && ((Auth::user()->account_type ?? '') === 'admin' || (Auth::user()->role ?? '') === 'admin'));
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



        async function renderFeaturedPostsSection() {
            const track = document.getElementById('featured-posts-track');
            const slider = document.getElementById('featured-posts-slider');
            if (!track) return;

            stopFeaturedAutoPlay();
            const posts = await fetchFeaturedPosts();
            if (!posts || posts.length === 0) {
                track.innerHTML = '<p class="w-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                return;
            }

            track.style.transition = 'none';
            track.style.transform = 'translate3d(0, 0, 0)';

            track.innerHTML = posts.map(post => `
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
                        <img src="${post.image}" alt="${post.title}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-amber-500 text-white font-extrabold text-[11px] uppercase tracking-wider shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-star text-amber-200 text-[10px]"></i> ${post.tag || 'NỔI BẬT'}
                        </span>
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
                            <button onclick="openModal('${post.title.replace(/'/g, "\\'")}', '${post.price}', '${post.location.replace(/'/g, "\\'")}')"
                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-amber-500 text-white text-xs font-bold transition-colors shadow-sm cursor-pointer">
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            if (slider) {
                slider.onmouseenter = stopFeaturedAutoPlay;
                slider.onmouseleave = startFeaturedAutoPlay;
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

            alert('Tính năng lưu cần được kết nối với API thực tế.');
        }

        function removeFeaturedPost(id) {
            if (!confirm('Bạn có chắc chắn muốn gỡ bài đăng này khỏi danh sách bài đăng nổi bật?')) return;
            // API thật sẽ được gọi ở đây, ví dụ: fetch(`/api/posts/featured/${id}`, { method: 'DELETE' })
            alert('Cần API để xóa bài đăng nổi bật thật.');
        }

        
        // Crossfade background transition mỗi 3.5 giây
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-bg-slide');

        function nextSlide() {
            if (!slides || slides.length === 0) return;
            slides[currentSlide].classList.remove('opacity-100');
            slides[currentSlide].classList.add('opacity-0');

            currentSlide = (currentSlide + 1) % slides.length;

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

        function handleSearch() {
            alert('Đang tìm kiếm danh sách nhà thuê phù hợp với tiêu chí của bạn...');
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
                alert('Đăng nhập thành công!');
            } else {
                alert('Đăng ký tài khoản thành công!');
            }
            closeAuthModal();
        }

        // Xử lý đóng/mở Dropdown Menu tài khoản người dùng
        function toggleUserDropdown(event) {
            event.stopPropagation();
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

        // Dữ liệu 63 Tỉnh / Thành phố Việt Nam
        const vietnamLocations = {
            centralCities: [
                "TP. Hồ Chí Minh",
                "Hà Nội",
                "Đà Nẵng",
                "Hải Phòng",
                "Cần Thơ"
            ],
            provinces: [
                "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", "Bắc Ninh",
                "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", "Cà Mau",
                "Cao Bằng", "Đắc Lắk", "Đắc Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp",
                "Gia Lai", "Hà Giang", "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang",
                "Hòa Bình", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu",
                "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An",
                "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam",
                "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", "Tây Ninh",
                "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh",
                "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
            ]
        };

        let currentLocation = "TP. Hồ Chí Minh";

        function removeVietnameseTones(str) {
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .toLowerCase();
        }

        function renderProvinces(filterKeyword = '') {
            const container = document.getElementById('provinces-list');
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword.trim());
            let html = '';

            // 1. Tất cả địa điểm Option
            const isAllSelected = currentLocation === 'Tất cả địa điểm';
            if (!cleanKeyword || removeVietnameseTones('tất cả địa điểm').includes(cleanKeyword)) {
                html += `
                    <div onclick="selectLocation('Tất cả địa điểm')" class="px-3.5 py-2.5 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-bold transition-colors ${isAllSelected ? 'bg-emerald-50/80 text-brand-600' : 'text-slate-700'}">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-slate-400 text-xs"></i> Tất cả địa điểm</span>
                        ${isAllSelected ? '<i class="fa-solid fa-check text-brand-600"></i>' : ''}
                    </div>
                `;
            }

            // 2. Thành Phố Trực Thuộc Trung Ương
            const filteredCities = vietnamLocations.centralCities.filter(c =>
                removeVietnameseTones(c).includes(cleanKeyword)
            );

            if (filteredCities.length > 0) {
                html +=
                    `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏙️ Thành phố trực thuộc TW</div>`;
                filteredCities.forEach(city => {
                    const isSelected = currentLocation === city;
                    html += `
                        <div onclick="selectLocation('${city}')" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-emerald-50/80 text-brand-600 font-bold' : 'text-slate-800'}">
                            <span>${city}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            // 3. Các Tỉnh Thành
            const filteredProvinces = vietnamLocations.provinces.filter(p =>
                removeVietnameseTones(p).includes(cleanKeyword)
            );

            if (filteredProvinces.length > 0) {
                html +=
                    `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏔️ Tỉnh thành</div>`;
                filteredProvinces.forEach(prov => {
                    const isSelected = currentLocation === prov;
                    html += `
                        <div onclick="selectLocation('${prov}')" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-medium transition-colors ${isSelected ? 'bg-emerald-50/80 text-brand-600 font-bold' : 'text-slate-700'}">
                            <span>${prov}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            if (filteredCities.length === 0 && filteredProvinces.length === 0 && (!cleanKeyword || !removeVietnameseTones(
                    'tất cả địa điểm').includes(cleanKeyword))) {
                html =
                    `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy tỉnh / thành phố phù hợp</div>`;
            }

            container.innerHTML = html;
        }

        function toggleLocationDropdown(event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById('location-dropdown-panel');
            const arrow = document.getElementById('location-dropdown-arrow');
            const searchInput = document.getElementById('location-search-input');

            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                renderProvinces();
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 50);
            } else {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function filterProvinces() {
            const input = document.getElementById('location-search-input');
            const clearBtn = document.getElementById('clear-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderProvinces(val);
        }

        function clearLocationSearch() {
            const input = document.getElementById('location-search-input');
            const clearBtn = document.getElementById('clear-search-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            renderProvinces('');
            if (input) input.focus();
        }

        function selectLocation(name) {
            currentLocation = name;
            const textEl = document.getElementById('selected-location-text');
            const inputEl = document.getElementById('selected-location-input');
            if (textEl) textEl.innerText = name;
            if (inputEl) inputEl.value = name;

            const panel = document.getElementById('location-dropdown-panel');
            const arrow = document.getElementById('location-dropdown-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }

        // Close location dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const locationContainer = document.getElementById('location-select-container');
            const locationPanel = document.getElementById('location-dropdown-panel');
            const locationArrow = document.getElementById('location-dropdown-arrow');

            if (locationContainer && locationPanel && !locationContainer.contains(event.target)) {
                locationPanel.classList.add('hidden');
                if (locationArrow) locationArrow.style.transform = 'rotate(0deg)';
            }
        });

        // Initialize Featured Posts on load
        document.addEventListener('DOMContentLoaded', function() {
            renderFeaturedPostsSection();
            fetchRealPosts();
        });
    </script>
</body>

</html>

