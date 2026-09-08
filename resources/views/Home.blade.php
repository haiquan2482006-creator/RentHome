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
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .hero-bg {
            background-image: linear-gradient(to bottom, rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        .badge-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-4 glass-panel border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-400 flex items-center justify-center text-white shadow-glow group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-house-chimney text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5">Thuê nhà ước mơ</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="#hero" class="text-sm font-semibold text-brand-600">Trang chủ</a>
                <a href="#featured" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Nhà nổi bật</a>
                <a href="#locations" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Khu vực</a>
                <a href="#features" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Ưu điểm</a>
                <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Liên hệ</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="#" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">
                    <i class="fa-regular fa-user"></i> Đăng nhập
                </a>
                <a href="#" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold transition-colors">
                    <i class="fa-solid fa-user-plus text-brand-600"></i> Đăng ký
                </a>
                <a href="#post-house" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-semibold text-sm shadow-md hover:shadow-glow transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus-circle"></i> Đăng tin cho thuê
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section with Search Bar -->
    <section id="hero" class="hero-bg min-h-screen pt-32 pb-20 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center relative">
        <div class="max-w-4xl mx-auto text-center mt-8">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-6">
                <i class="fa-solid fa-bolt text-yellow-400"></i> Hơn 50,000+ Căn nhà sẵn sàng cho thuê
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight tracking-tight">
                Tìm Ngôi Nhà Hoàn Hảo <br class="hidden sm:inline">Cho Cuộc Sống Tương Lai
            </h1>
            <p class="mt-4 text-lg sm:text-xl text-slate-300 max-w-2xl mx-auto font-normal">
                Khám phá hàng ngàn phòng trọ, căn hộ cao cấp, và nhà nguyên căn chính chủ với mức giá tốt nhất, không qua trung gian.
            </p>
        </div>

        <!-- Search Box Container -->
        <div class="w-full max-w-5xl mx-auto mt-10">
            <div class="glass-panel p-4 sm:p-6 rounded-3xl shadow-2xl border border-white/40">
                <!-- Search Tabs -->
                <div class="flex items-center gap-3 mb-6 overflow-x-auto pb-2 border-b border-slate-200/60">
                    <button class="tab-btn active px-5 py-2.5 rounded-xl font-bold text-sm bg-brand-600 text-white shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-building"></i> Tất cả loại hình
                    </button>
                    <button class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-city"></i> Căn hộ / Chung cư
                    </button>
                    <button class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-house"></i> Nhà nguyên căn
                    </button>
                    <button class="tab-btn px-5 py-2.5 rounded-xl font-semibold text-sm text-slate-600 hover:bg-slate-100 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-bed"></i> Phòng trọ
                    </button>
                </div>

                <!-- Search Inputs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Location Selector -->
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Khu vực / Tỉnh thành</label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm">
                                <option value="">Tất cả địa điểm</option>
                                <option value="hcm" selected>TP. Hồ Chí Minh</option>
                                <option value="hn">Hà Nội</option>
                                <option value="dn">Đà Nẵng</option>
                                <option value="bd">Bình Dương</option>
                            </select>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Loại Bất Động Sản</label>
                        <div class="relative">
                            <i class="fa-solid fa-layer-group absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm">
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
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Mức Giá Mẫu</label>
                        <div class="relative">
                            <i class="fa-solid fa-money-bill-wave absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm">
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
                        <button onclick="handleSearch()" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-600 to-emerald-500 hover:from-brand-700 hover:to-emerald-600 text-white font-bold text-sm shadow-lg hover:shadow-glow transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Banner -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl w-full text-center">
            <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <p class="text-3xl font-extrabold text-white">50k+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Tin đăng cho thuê</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <p class="text-3xl font-extrabold text-emerald-400">98%</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Khách thuê hài lòng</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <p class="text-3xl font-extrabold text-white">120k+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Người dùng hàng tháng</p>
            </div>
            <div class="p-4 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <p class="text-3xl font-extrabold text-emerald-400">0%</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Phí môi giới ẩn</p>
            </div>
        </div>
    </section>

    <!-- Featured Categories Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Loại hình phổ biến</span>
                <h2 class="text-3xl font-black text-slate-900 mt-1">Danh Mục Cho Thuê Hot</h2>
            </div>
            <a href="#" class="mt-4 sm:mt-0 inline-flex items-center gap-2 font-bold text-sm text-brand-600 hover:text-brand-700">
                Xem tất cả danh mục <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Category 1 -->
            <div class="group p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-brand-500/30 transition-all duration-300 cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-brand-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-building-user"></i>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-brand-600 transition-colors">Căn Hộ Chung Cư</h3>
                <p class="text-xs text-slate-500 mt-1">Hơn 12,400+ căn nhà đầy đủ tiện ích</p>
            </div>

            <!-- Category 2 -->
            <div class="group p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-brand-500/30 transition-all duration-300 cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-house-user"></i>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Nhà Nguyên Căn</h3>
                <p class="text-xs text-slate-500 mt-1">Hơn 8,200+ căn cho gia đình</p>
            </div>

            <!-- Category 3 -->
            <div class="group p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-brand-500/30 transition-all duration-300 cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-bed"></i>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Phòng Trọ Giá Rẻ</h3>
                <p class="text-xs text-slate-500 mt-1">Dành cho sinh viên & NVVP</p>
            </div>

            <!-- Category 4 -->
            <div class="group p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-brand-500/30 transition-all duration-300 cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Biệt Thự & Studio</h3>
                <p class="text-xs text-slate-500 mt-1">Không gian sang trọng, đẳng cấp</p>
            </div>
        </div>
    </section>

    <!-- Featured Properties Grid Section -->
    <section id="featured" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto bg-slate-100/60 rounded-3xl border border-slate-200/60">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Lựa chọn hàng đầu</span>
                <h2 class="text-3xl font-black text-slate-900 mt-1">Danh Sách Nhà Cho Thuê Nổi Bật</h2>
            </div>
            
            <!-- Quick Filter Tags -->
            <div class="mt-4 md:mt-0 flex items-center gap-2 overflow-x-auto pb-2">
                <button class="px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs shadow-sm">Tất cả</button>
                <button class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">TP. Hồ Chí Minh</button>
                <button class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">Hà Nội</button>
                <button class="px-4 py-2 rounded-xl bg-white text-slate-600 hover:bg-slate-200 font-semibold text-xs transition-colors">Đà Nẵng</button>
            </div>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- House Card 1 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card hover:shadow-2xl transition-all duration-300 group flex flex-col">
                <div class="relative overflow-hidden aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80" alt="Căn hộ Vinhomes Central Park" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 rounded-full bg-brand-600 text-white font-bold text-xs uppercase tracking-wider shadow-md">HOT</span>
                        <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white font-semibold text-xs">Chính chủ</span>
                    </div>
                    <button onclick="toggleWishlist(this)" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/80 backdrop-blur-md hover:bg-white text-slate-600 hover:text-rose-500 flex items-center justify-center transition-colors shadow-md">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <div class="absolute bottom-4 left-4 bg-slate-900/80 backdrop-blur-md text-white px-3 py-1.5 rounded-xl font-bold text-sm">
                        <span class="text-emerald-400 text-lg">12.5 triệu</span> / tháng
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-brand-600 mb-2">
                            <i class="fa-solid fa-circle-check"></i> Đã xác thực giấy tờ
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 line-clamp-1 group-hover:text-brand-600 transition-colors">
                            Căn Hộ Vinhomes Central Park 2PN Full Nội Thất
                        </h3>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> Nguyễn Hữu Cảnh, Quận Bình Thạnh, TP. Hồ Chí Minh
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bed text-brand-600"></i> 2 PN
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bath text-brand-600"></i> 2 WC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-vector-square text-brand-600"></i> 75 m²
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Chủ nhà" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-xs font-bold text-slate-700">Chị Thu Thảo</span>
                        </div>
                        <button onclick="openModal('Căn Hộ Vinhomes Central Park 2PN', '12.5 triệu', 'Quận Bình Thạnh, TP.HCM')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition-colors">
                            Xem chi tiết
                        </button>
                    </div>
                </div>
            </div>

            <!-- House Card 2 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card hover:shadow-2xl transition-all duration-300 group flex flex-col">
                <div class="relative overflow-hidden aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Nhà nguyên căn Hẻm Xe Hơi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 rounded-full bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider shadow-md">MỚI NĂM 2026</span>
                    </div>
                    <button onclick="toggleWishlist(this)" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/80 backdrop-blur-md hover:bg-white text-slate-600 hover:text-rose-500 flex items-center justify-center transition-colors shadow-md">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <div class="absolute bottom-4 left-4 bg-slate-900/80 backdrop-blur-md text-white px-3 py-1.5 rounded-xl font-bold text-sm">
                        <span class="text-emerald-400 text-lg">18 triệu</span> / tháng
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-brand-600 mb-2">
                            <i class="fa-solid fa-shield-halved"></i> Giảm 5% cho HĐ 1 năm
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 line-clamp-1 group-hover:text-brand-600 transition-colors">
                            Nhà Phố 1 Trệt 2 Lầu Hẻm Xe Hơi Quận 7
                        </h3>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> Đường Lâm Văn Bền, Quận 7, TP. Hồ Chí Minh
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bed text-brand-600"></i> 3 PN
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bath text-brand-600"></i> 3 WC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-vector-square text-brand-600"></i> 120 m²
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Chủ nhà" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-xs font-bold text-slate-700">Anh Hoàng Nam</span>
                        </div>
                        <button onclick="openModal('Nhà Phố 1 Trệt 2 Lầu Hẻm Xe Hơi', '18 triệu', 'Quận 7, TP.HCM')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition-colors">
                            Xem chi tiết
                        </button>
                    </div>
                </div>
            </div>

            <!-- House Card 3 -->
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card hover:shadow-2xl transition-all duration-300 group flex flex-col">
                <div class="relative overflow-hidden aspect-[4/3]">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80" alt="Phòng Studio Cao Cấp" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 rounded-full bg-purple-600 text-white font-bold text-xs uppercase tracking-wider shadow-md">STUDIO</span>
                    </div>
                    <button onclick="toggleWishlist(this)" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/80 backdrop-blur-md hover:bg-white text-slate-600 hover:text-rose-500 flex items-center justify-center transition-colors shadow-md">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <div class="absolute bottom-4 left-4 bg-slate-900/80 backdrop-blur-md text-white px-3 py-1.5 rounded-xl font-bold text-sm">
                        <span class="text-emerald-400 text-lg">5.8 triệu</span> / tháng
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-brand-600 mb-2">
                            <i class="fa-solid fa-wifi"></i> Miễn phí Wifi & Giặt sấy
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 line-clamp-1 group-hover:text-brand-600 transition-colors">
                            Phòng Studio Ban Công Thoáng Mát Cầu Giấy
                        </h3>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> Đường Xuân Thủy, Q. Cầu Giấy, Hà Nội
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-600 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bed text-brand-600"></i> 1 PN
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-bath text-brand-600"></i> 1 WC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-vector-square text-brand-600"></i> 35 m²
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80" alt="Chủ nhà" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-xs font-bold text-slate-700">Cô Minh Hải</span>
                        </div>
                        <button onclick="openModal('Phòng Studio Ban Công Cầu Giấy', '5.8 triệu', 'Cầu Giấy, Hà Nội')" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition-colors">
                            Xem chi tiết
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-12 text-center">
            <a href="#" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-white border border-slate-300 hover:border-brand-500 font-bold text-sm text-slate-800 hover:text-brand-600 shadow-sm hover:shadow-md transition-all">
                Xem thêm 1,200+ căn nhà khác <i class="fa-solid fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Popular Locations Section -->
    <section id="locations" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Địa điểm lý tưởng</span>
            <h2 class="text-3xl font-black text-slate-900 mt-1">Khu Vực Cho Thuê Nhiều Nhất</h2>
            <p class="text-sm text-slate-500 mt-2">Dễ dàng tìm nhà tại các quận trung tâm đông đúc và thuận tiện giao thông</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Location 1 -->
            <div class="relative h-80 rounded-3xl overflow-hidden group cursor-pointer shadow-lg">
                <img src="https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=800&q=80" alt="TP. Hồ Chí Minh" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6 text-white">
                    <span class="px-3 py-1 rounded-full bg-brand-500 text-white text-[10px] font-bold uppercase tracking-wider">Top 1 Phổ Biến</span>
                    <h3 class="text-2xl font-black mt-2">TP. Hồ Chí Minh</h3>
                    <p class="text-xs text-slate-300 mt-1">Hơn 28,500+ tin đăng đang cho thuê</p>
                </div>
            </div>

            <!-- Location 2 -->
            <div class="relative h-80 rounded-3xl overflow-hidden group cursor-pointer shadow-lg">
                <img src="https://images.unsplash.com/photo-1509030450996-93f2e3d84074?auto=format&fit=crop&w=800&q=80" alt="Hà Nội" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6 text-white">
                    <span class="px-3 py-1 rounded-full bg-blue-500 text-white text-[10px] font-bold uppercase tracking-wider">Thủ Đô</span>
                    <h3 class="text-2xl font-black mt-2">Hà Nội</h3>
                    <p class="text-xs text-slate-300 mt-1">Hơn 18,200+ tin đăng đang cho thuê</p>
                </div>
            </div>

            <!-- Location 3 -->
            <div class="relative h-80 rounded-3xl overflow-hidden group cursor-pointer shadow-lg">
                <img src="https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=800&q=80" alt="Đà Nẵng" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6 text-white">
                    <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-[10px] font-bold uppercase tracking-wider">Thành Phố Biển</span>
                    <h3 class="text-2xl font-black mt-2">Đà Nẵng</h3>
                    <p class="text-xs text-slate-300 mt-1">Hơn 6,400+ tin đăng đang cho thuê</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Features -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600">Tại sao chọn RentHome?</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2 leading-tight">
                    Trải Nghiệm Thuê Nhà An Toàn, Nhanh Chóng & Minh Bạch
                </h2>
                <p class="text-slate-600 mt-4 text-sm leading-relaxed">
                    RentHome giải quyết nỗi lo tìm nhà trọ trôi nổi, giá ảo. Chúng tôi kiểm duyệt 100% hình ảnh và thông tin từ chủ nhà thực tế.
                </p>

                <div class="mt-8 space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Thông tin đã xác minh chính chủ</h4>
                            <p class="text-xs text-slate-500 mt-1">Tất cả bài đăng đều được kiểm tra địa chỉ, hợp đồng và giấy tờ rõ ràng trước khi hiển thị.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Không tốn phí môi giới gian lận</h4>
                            <p class="text-xs text-slate-500 mt-1">Kết nối trực tiếp khách thuê với chủ nhà, không qua trung gian nâng giá.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">Hợp đồng thuê nhà trực tuyến</h4>
                            <p class="text-xs text-slate-500 mt-1">Hỗ trợ tạo hợp đồng thuê nhà điện tử đúng chuẩn pháp lý nhanh chóng trong 5 phút.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Image Box -->
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80" alt="Không gian sống tuyệt đẹp" class="w-full h-auto object-cover">
                </div>
                <!-- Floating Card Badge -->
                <div class="absolute -bottom-6 -left-6 glass-panel p-5 rounded-2xl shadow-2xl border border-white max-w-xs hidden sm:block">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold">
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

    <!-- Call to Action Banner for Landlords -->
    <section id="post-house" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="relative rounded-3xl bg-gradient-to-r from-slate-900 via-brand-900 to-slate-950 p-8 sm:p-14 overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 max-w-2xl">
                <span class="px-3.5 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-400/30 text-xs font-bold uppercase tracking-wider">Dành cho chủ nhà</span>
                <h2 class="text-3xl sm:text-4xl font-black mt-4 leading-tight">
                    Bạn Có Phòng Cho Thuê? <br> Đăng Tin Ngay Hôm Nay!
                </h2>
                <p class="mt-3 text-slate-300 text-sm sm:text-base">
                    Tiếp cận hàng triệu người thuê nhà tiềm năng mỗi tháng. Đăng tin miễn phí, duyệt nhanh trong 15 phút.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="#" class="px-8 py-3.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm shadow-glow transition-all">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Đăng tin cho thuê miễn phí
                    </a>
                    <a href="#" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-colors">
                        Tìm hiểu thêm
                    </a>
                </div>
            </div>
            <!-- Decorative Icon -->
            <i class="fa-solid fa-house-circle-check absolute -right-10 -bottom-10 text-[220px] text-white/5 pointer-events-none"></i>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-slate-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
                <!-- Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold">
                            <i class="fa-solid fa-house-chimney"></i>
                        </div>
                        <span class="text-xl font-extrabold text-white">Rent<span class="text-brand-500">Home</span></span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Nền tảng công nghệ hỗ trợ tìm kiếm và cho thuê bất động sản, căn hộ, phòng trọ minh bạch và hiệu quả hàng đầu Việt Nam.
                    </p>
                    <div class="flex gap-3 pt-2">
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-brand-600 text-white flex items-center justify-center text-xs transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-brand-600 text-white flex items-center justify-center text-xs transition-colors"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-brand-600 text-white flex items-center justify-center text-xs transition-colors"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Danh Mục Hot</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Cho thuê căn hộ TP.HCM</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Cho thuê nhà nguyên căn Hà Nội</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Phòng trọ giá rẻ sinh viên</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Biệt thự Villa nghỉ dưỡng</a></li>
                    </ul>
                </div>

                <!-- Support Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Hỗ Trợ Khách Hàng</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Quy định dịch vụ</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-brand-400 transition-colors">Giải quyết tranh chấp</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Liên Hệ</h4>
                    <ul class="space-y-3 text-xs">
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone text-brand-500"></i> Hotline: 1900 6868
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-brand-500"></i> Email: support@renthome.vn
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot text-brand-500 mt-0.5"></i> Tòa nhà Landmark 81, Bình Thạnh, TP.HCM
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; 2026 RentHome Inc. Tất cả quyền được bảo lưu.</p>
                <p>Thiết kế bởi Hoàng Hải - Laravel Framework 12</p>
            </div>
        </div>
    </footer>

    <!-- Property Detail Modal -->
    <div id="propertyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative animate-fade-in">
            <button onclick="closeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
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
                <button onclick="alert('Đã gửi yêu cầu liên hệ chủ nhà!')" class="flex-1 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm transition-colors">
                    <i class="fa-solid fa-phone mr-1.5"></i> Gọi điện chủ nhà
                </button>
                <button onclick="closeModal()" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 font-semibold text-sm text-slate-700">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Simple Interactivity Scripts -->
    <script>
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
    </script>
</body>

</html>
