<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - RentHome</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        @php
            $themeSettings = file_exists(storage_path('app/theme_settings.json')) 
                ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true) 
                : [];
            $tColor = $themeSettings['theme_color'] ?? '#16a34a';
        @endphp
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '{{ $tColor }}10',
                            100: '{{ $tColor }}20',
                            500: '{{ $tColor }}',
                            600: '{{ $tColor }}',
                            700: '{{ $tColor }}',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Đảm bảo popup SweetAlert2 luôn hiển thị trên cùng, đè lên các Modal có z-index cao (như reportPostModal z-[9999]) */
        .swal2-container {
            z-index: 999999 !important;
        }
    </style>
</head>
<body class="text-slate-800 antialiased pt-24">

    <!-- Header -->
    @include('partials.header-home')

    <!-- Main Content -->
    <main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-slate-500 mb-4 font-medium w-fit">
            <a href="{{ url('/') }}" class="hover:text-brand-600 transition-colors"><i class="fa-solid fa-house mr-1"></i> Trang chủ</a>
            <span class="mx-3 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
            <a href="{{ url('/baidang') }}" class="hover:text-brand-600 transition-colors">Thuê nhà</a>
            <span class="mx-3 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
            <span class="text-brand-600 font-bold line-clamp-1 max-w-[200px] sm:max-w-md">{{ $post->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Images & Details -->
            <div class="lg:col-span-8">
                <!-- Single Combined Container -->
                <div class="bg-white rounded-md p-6 sm:p-8 shadow-sm border border-slate-200/60 space-y-8">
                    @php
                        // 1. Chuẩn hóa biến Tòa nhà ($b) lên đầu Main Content để sử dụng toàn diện
                        $b = $post->building ?? ($post->building_info ?? null);
                        if (!$b && !empty($post->building_id) && class_exists(\App\Models\Building::class)) {
                            $b = \App\Models\Building::find($post->building_id);
                        }

                        // Kiểm tra tình trạng đã thuê (Dự phòng)
                        $isRented = isset($isRented) ? (bool)$isRented : (bool)(
                            $post->is_rented === true || $post->is_rented === 1 || $post->is_rented === '1' ||
                            in_array(strtolower(trim((string)($post->status ?? ''))), ['đã cho thuê', 'rented', 'dang_thue', 'đang thuê', 'da_cho_thue', 'inactive'])
                        );

                        // Chuẩn hóa mảng $typeMap hỗ trợ cả chữ hoa và chữ thường
                        $typeMap = [
                            'nha_o'               => 'Nhà ở',
                            'phong_tro'           => 'Phòng trọ',
                            'nha_tro'             => 'Phòng trọ',
                            'can_ho'              => 'Căn hộ',
                            'can_ho_dich_vu'      => 'Căn hộ dịch vụ',
                            'chung_cu_mini'       => 'Chung cư mini',
                            'chung_cu'            => 'Chung cư',
                            'mat_bang'            => 'Mặt bằng',
                            'mat_bang_kinh_doanh' => 'Mặt bằng kinh doanh',
                            'van_phong'           => 'Văn phòng',
                            'ky_tuc_xa'           => 'Ký túc xá / Sleepbox',
                            'sleepbox'            => 'Ký túc xá / Sleepbox',
                            'dat_nen'             => 'Đất nền',
                            'dat_phan_lo'         => 'Đất phân lô',
                            'nha_rieng'           => 'Nhà riêng',
                            'nha_nguyen_can'      => 'Nhà nguyên căn',
                            'cum_nha_lien_ke'     => 'Cụm nhà liền kề',
                            'biet_thu'            => 'Biệt thự',
                        ];
                        // Tự động map thêm các key viết HOA để tương thích hoàn toàn
                        foreach ($typeMap as $k => $v) {
                            $typeMap[strtoupper($k)] = $v;
                        }

                        $rawPType = trim((string)($post->property_type ?? ''));
                        $lowerPType = strtolower($rawPType);
                        $typeStr = $typeMap[$lowerPType] ?? ($typeMap[$rawPType] ?? (!empty($rawPType) ? ucwords(str_replace('_', ' ', $rawPType)) : 'Bất động sản'));
                        $floorVal = $post->floor ?? ($post->floor_number ?? null);
                        $bTypeRaw = strtolower($b->type ?? ($post->property_type ?? ''));
                        $bTypeLabel = mb_strtolower($b->business_type ?? ($b->type ?? ($post->property_type ?? '')));
                        $isLienKe = in_array($bTypeRaw, ['cum_nha_lien_ke']) || str_contains($bTypeLabel, 'liền kề') || str_contains($bTypeLabel, 'dự án');
                    @endphp

                    <!-- Main Header Info -->
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            <span class="px-4 py-1.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-bold rounded-full uppercase tracking-wider shadow-sm">
                                {{ $typeStr }}
                            </span>
                            @if(!empty($floorVal))
                            <span class="px-3.5 py-1 bg-sky-50 text-sky-700 text-xs font-bold rounded-full border border-sky-200/80 flex items-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-stairs text-sky-500"></i> {{ $isLienKe ? ($floorVal . ' tầng') : ('Tầng ' . $floorVal) }}
                            </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 leading-[1.3] mb-3 tracking-tight">{{ $post->title }}</h1>

                        <!-- Dòng Chỉ số tương tác thực tế & Thời gian đăng (FOMO Badges) -->
                        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 mb-4">
                            <span class="text-slate-500 text-xs sm:text-sm font-medium flex items-center gap-1.5 bg-slate-100/90 px-3.5 py-1.5 rounded-full border border-slate-200 shadow-2xs">
                                <i class="fa-regular fa-clock text-slate-400"></i> {{ $post->created_at->diffForHumans() }}
                            </span>
                            @php
                                $detailPostId = (string)($post->_id ?? $post->id);
                                $detailOwnerId = (string)($post->user_id ?? '');
                                $isAuth = \Illuminate\Support\Facades\Auth::check();
                                $curUser = $isAuth ? \Illuminate\Support\Facades\Auth::user() : null;
                                $isEnterprise = $curUser && ($curUser->isEnterprise() || $curUser->role === 'doanhnghiep');
                                $isMyPost = $isAuth && ($detailOwnerId === (string)\Illuminate\Support\Facades\Auth::id());
                                $canFav = !$isEnterprise && !$isMyPost;
                            @endphp
                            @if($canFav)
                            <button type="button"
                                    onclick="window.RentHomeFavorites && window.RentHomeFavorites.toggle('{{ $detailPostId }}', event, '{{ $detailOwnerId }}')"
                                    data-fav-id="{{ $detailPostId }}"
                                    data-owner-id="{{ $detailOwnerId }}"
                                    class="fav-detail-btn text-xs sm:text-sm font-bold flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-rose-200 bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-all shadow-2xs group active:scale-95 cursor-pointer"
                                    title="Nhấn để lưu hoặc bỏ lưu bài đăng">
                                <i class="fa-regular fa-heart text-slate-400 group-hover:text-rose-500 transition-colors"></i>
                                <span class="fav-action-text">Yêu thích</span>
                            </button>
                            @endif

                            <!-- Nút Báo cáo / Khiếu nại bài đăng -->
                            <button type="button"
                                    onclick="openReportModal()"
                                    class="text-xs sm:text-sm font-bold flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-amber-300 bg-amber-50/60 hover:bg-amber-100 text-amber-800 transition-all shadow-2xs group active:scale-95 cursor-pointer"
                                    title="Báo cáo / Khiếu nại thông tin bài đăng này">
                                <i class="fa-solid fa-flag text-amber-600 group-hover:scale-110 transition-transform"></i>
                                <span>Báo cáo tin</span>
                            </button>
                        </div>
                        
                        <div class="flex items-start sm:items-center gap-2 text-slate-500 font-medium text-base sm:text-lg">
                            <i class="fa-solid fa-location-dot text-brand-500 mt-1 sm:mt-0"></i>
                            <span>{{ $post->address ?? ($post->ward . ', ' . $post->district . ', ' . $post->province) }}</span>
                        </div>

                        @if(!empty($b))
                        <!-- Khung thông tin Tòa nhà / Cơ sở (Lớp Vỏ) -->
                        <div class="mt-5 p-4 rounded-xl bg-gradient-to-r from-emerald-50/90 via-teal-50/40 to-slate-50 border border-emerald-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-sm shrink-0">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div>
                                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-layer-group text-[10px]"></i> Thuộc tòa nhà / cơ sở
                                    </div>
                                    <div class="text-base sm:text-lg font-black text-slate-900 flex flex-wrap items-center gap-2 mt-0.5">
                                        <span>{{ $b->name }}</span>
                                        @php
                                            $bTypeDisplay = $b->business_type ?: ($typeMap[strtolower($b->type ?? '')] ?? ($b->type ?: 'Chung cư mini / Căn hộ dịch vụ'));
                                        @endphp
                                        @if(!empty($bTypeDisplay))
                                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300/60 font-sans">
                                                {{ $bTypeDisplay }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm font-semibold text-slate-600 sm:ml-auto">
                                @if(!empty($b->total_floors))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 border border-emerald-200/70 text-slate-700 shadow-2xs">
                                        <i class="fa-solid fa-cubes-stacked text-emerald-600"></i> Quy mô: <strong class="text-slate-900">{{ $b->total_floors }} tầng</strong>
                                    </span>
                                @endif
                                @if(!empty($floorVal))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-200/70 text-sky-800 font-bold shadow-2xs">
                                        <i class="fa-solid fa-stairs text-sky-600"></i> {{ $isLienKe ? ('Quy mô: ' . $floorVal . ' tầng') : ('Vị trí: Tầng ' . $floorVal) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Image Gallery -->
                    @php
                        $galleryImages = [];

                        // 1. Phân tích mảng $post->images
                        if (!empty($post->images) && is_iterable($post->images)) {
                            foreach ($post->images as $imgRef) {
                                $imgStr = is_array($imgRef) ? ($imgRef['url'] ?? ($imgRef['id'] ?? '')) : (is_object($imgRef) ? ($imgRef->url ?? ($imgRef->id ?? '')) : (string)$imgRef);
                                $imgStr = trim($imgStr);
                                if (empty($imgStr)) continue;

                                // Nếu là đường dẫn /images/{id} hoặc images/{id}
                                if (\Illuminate\Support\Str::startsWith($imgStr, ['/images/', 'images/', '/image/', 'image/'])) {
                                    $galleryImages[] = '/' . ltrim($imgStr, '/');
                                    continue;
                                }

                                // Nếu là chuỗi 24 hex chars (MongoDB ObjectId của Image model)
                                if (preg_match('/^[a-f\d]{24}$/i', $imgStr)) {
                                    $galleryImages[] = '/images/' . $imgStr;
                                    continue;
                                }

                                // Nếu là URL tuyệt đối hoặc Data URI
                                if (\Illuminate\Support\Str::startsWith($imgStr, ['http://', 'https://', 'data:'])) {
                                    if (str_contains($imgStr, '/images/')) {
                                        $galleryImages[] = '/images/' . substr($imgStr, strpos($imgStr, '/images/') + 8);
                                    } else {
                                        $galleryImages[] = $imgStr;
                                    }
                                    continue;
                                }

                                // Nếu là storage
                                if (\Illuminate\Support\Str::startsWith($imgStr, ['/storage/', 'storage/'])) {
                                    $galleryImages[] = asset(ltrim($imgStr, '/'));
                                    continue;
                                }

                                if (\Illuminate\Support\Str::startsWith($imgStr, '/')) {
                                    $galleryImages[] = $imgStr;
                                    continue;
                                }

                                $galleryImages[] = asset('storage/' . $imgStr);
                            }
                        }

                        // 2. Nếu $galleryImages vẫn rỗng, kiểm tra all_images accessor
                        if (empty($galleryImages) && !empty($post->all_images) && is_iterable($post->all_images)) {
                            foreach ($post->all_images as $im) {
                                $u = is_array($im) ? ($im['url'] ?? '') : (is_object($im) ? ($im->url ?? '') : (string)$im);
                                if (!empty($u)) {
                                    if (str_contains($u, '/images/')) {
                                        $u = '/images/' . substr($u, strpos($u, '/images/') + 8);
                                    }
                                    $galleryImages[] = $u;
                                }
                            }
                        }

                        // 3. Fallback sang display_image của Post
                        if (empty($galleryImages) && !empty($post->display_image)) {
                            $d = (string)$post->display_image;
                            if (str_contains($d, '/images/')) {
                                $d = '/images/' . substr($d, strpos($d, '/images/') + 8);
                            }
                            $galleryImages[] = $d;
                        }

                        // 4. Fallback sang ảnh Tòa nhà mẹ nếu phòng chưa có ảnh
                        if (empty($galleryImages) && !empty($b)) {
                            if (!empty($b->display_image)) {
                                $galleryImages[] = $b->display_image;
                            } elseif (!empty($b->image)) {
                                $galleryImages[] = \Illuminate\Support\Str::startsWith($b->image, ['http', 'data:', '/images/']) ? $b->image : asset('storage/' . ltrim($b->image, '/'));
                            }
                        }

                        // 5. Ảnh placeholder cuối cùng nếu hoàn toàn không có ảnh
                        if (empty($galleryImages)) {
                            $galleryImages[] = 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';
                        }

                        $galleryImages = array_values(array_unique(array_filter($galleryImages)));
                    @endphp

                    <div class="space-y-3">
                        <!-- Main Image -->
                        <div class="rounded-md shadow-sm border border-slate-200/50 overflow-hidden bg-slate-900 relative group">
                            <div class="aspect-[16/9] w-full relative">
                                <img id="mainImage" src="{{ $galleryImages[0] }}" alt="{{ $post->title }}" class="w-full h-full object-contain sm:object-cover transition-transform duration-700 {{ $isRented ? 'opacity-40 grayscale' : 'group-hover:scale-105 opacity-90 group-hover:opacity-100' }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent pointer-events-none"></div>

                                @if($isRented)
                                <!-- Banner lớn đè lên hình ảnh chính khi nhà đã được cho thuê thành công -->
                                <div class="absolute inset-0 bg-slate-950/75 backdrop-blur-[4px] flex flex-col items-center justify-center text-center p-6 z-20">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-2xl sm:text-3xl mb-3 shadow-xl">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                    <span class="inline-block px-3 py-1 rounded-full bg-rose-500 text-white font-black text-[11px] sm:text-xs uppercase tracking-widest mb-2 shadow-sm border border-rose-400/50">
                                        Tình trạng bất động sản
                                    </span>
                                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-wide uppercase drop-shadow-md max-w-xl">
                                        Căn nhà này đã được cho thuê thành công
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-md leading-relaxed">
                                        Phòng / Căn hộ này hiện không còn trống. Quý khách vui lòng tham khảo các bài đăng tương tự hoặc các phòng trống khác trên hệ thống.
                                    </p>
                                    <div class="flex items-center gap-3 mt-5">
                                        <a href="{{ url('/baidang') }}" class="px-5 py-2.5 rounded-xl bg-white text-slate-900 font-bold text-xs sm:text-sm hover:bg-slate-100 transition shadow-lg flex items-center gap-2">
                                            <i class="fa-solid fa-compass text-rose-500"></i> Xem danh sách phòng khác
                                        </a>
                                    </div>
                                </div>
                                @else
                                    @if($post->status === 'approved')
                                    <div class="absolute top-5 left-5 bg-white/90 backdrop-blur-md text-emerald-600 text-xs font-black px-4 py-2 rounded-full shadow-sm flex items-center gap-2 border border-white/50">
                                        <i class="fa-solid fa-circle-check text-base"></i> Đã kiểm duyệt
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if(count($galleryImages) > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2 sm:gap-3" id="galleryThumbnails">
                            @foreach($galleryImages as $index => $imgSrc)
                            <button type="button" onclick="changeMainImage('{{ $imgSrc }}', this)" class="thumb-btn relative aspect-[4/3] rounded overflow-hidden border-2 {{ $index === 0 ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-transparent hover:border-slate-300' }} focus:outline-none transition-all group bg-slate-100 shadow-sm cursor-pointer">
                                <img src="{{ $imgSrc }}" loading="lazy" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover group-hover:opacity-100 opacity-75 transition-opacity" onerror="this.onerror=null; this.src='https://placehold.co/200x150?text=No+Image';">
                            </button>
                            @endforeach
                        </div>
                        <script>
                            function changeMainImage(url, btn) {
                                const mainImg = document.getElementById('mainImage');
                                if (mainImg) mainImg.src = url;
                                document.querySelectorAll('#galleryThumbnails .thumb-btn').forEach(b => {
                                    b.classList.remove('border-brand-500', 'ring-2', 'ring-brand-500/20');
                                    b.classList.add('border-transparent');
                                });
                                if (btn) {
                                    btn.classList.remove('border-transparent');
                                    btn.classList.add('border-brand-500', 'ring-2', 'ring-brand-500/20');
                                }
                            }
                        </script>
                        @endif
                    </div>

                    <!-- Price and Area Quick Stats -->
                    @php
                        $rawUnit = strtolower(trim((string)($post->price_unit ?? 'thang')));
                        if (in_array($rawUnit, ['thang', 'tháng'])) {
                            $displayUnit = '/ tháng';
                        } elseif (in_array($rawUnit, ['nam', 'năm'])) {
                            $displayUnit = '/ năm';
                        } elseif (in_array($rawUnit, ['m2', 'm²'])) {
                            $displayUnit = '/ m²';
                        } elseif (in_array($rawUnit, ['tong', 'tổng'])) {
                            $displayUnit = 'VNĐ';
                        } else {
                            $displayUnit = '/ ' . ($post->price_unit ?: 'tháng');
                        }
                    @endphp

                    <div class="p-5 sm:p-6 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-wrap items-center gap-x-8 sm:gap-x-10 gap-y-5">
                        <!-- Mức giá -->
                        <div class="flex flex-col min-w-fit">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-brand-500 text-xs"></i> Mức giá
                            </span>
                            <div class="flex items-baseline gap-1.5 flex-nowrap">
                                @if($isRented)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-black bg-rose-100 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-circle-exclamation"></i> Đã cho thuê
                                    </span>
                                @else
                                    <span class="text-2xl sm:text-3xl font-black text-brand-600 tracking-tight whitespace-nowrap">
                                        {{ number_format($post->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-slate-600 whitespace-nowrap">
                                        {{ $displayUnit }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="w-px h-10 bg-slate-200 hidden sm:block"></div>

                        <!-- Diện tích -->
                        <div class="flex flex-col min-w-fit">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-chart-area text-slate-400 text-xs"></i> Diện tích
                            </span>
                            <div class="flex items-baseline gap-1 flex-nowrap">
                                <span class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight whitespace-nowrap">
                                    {{ $post->area }}
                                </span>
                                <span class="text-base sm:text-lg font-bold text-slate-600 whitespace-nowrap">m&sup2;</span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="w-px h-10 bg-slate-200 hidden sm:block"></div>

                        <!-- Phòng ngủ -->
                        <div class="flex flex-col min-w-fit">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-bed text-slate-400 text-xs"></i> Phòng ngủ
                            </span>
                            <div class="flex items-baseline gap-1.5 flex-nowrap">
                                <span class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight whitespace-nowrap flex items-center gap-2">
                                    <i class="fa-solid fa-bed text-slate-400 text-xl"></i> {{ $post->bedroom ?? 1 }} <span class="text-base sm:text-lg font-bold text-slate-600">PN</span>
                                </span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="w-px h-10 bg-slate-200 hidden sm:block"></div>

                        <!-- Phòng tắm / WC -->
                        <div class="flex flex-col min-w-fit">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-bath text-slate-400 text-xs"></i> Phòng tắm / WC
                            </span>
                            <div class="flex items-baseline gap-1.5 flex-nowrap">
                                <span class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight whitespace-nowrap flex items-center gap-2">
                                    <i class="fa-solid fa-bath text-slate-400 text-xl"></i> {{ $post->bathroom ?? 1 }} <span class="text-base sm:text-lg font-bold text-slate-600">WC</span>
                                </span>
                            </div>
                        </div>

                        @if(!empty($floorVal))
                        <!-- Divider -->
                        <div class="w-px h-10 bg-slate-200 hidden sm:block"></div>

                        <!-- Vị trí tầng / Số tầng -->
                        <div class="flex flex-col min-w-fit">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-stairs text-sky-500 text-xs"></i> {{ $isLienKe ? 'Số tầng nhà' : 'Vị trí tầng' }}
                            </span>
                            <span class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight whitespace-nowrap">
                                {{ $isLienKe ? ($floorVal . ' tầng') : ('Tầng ' . $floorVal) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Amenities & Description -->
                    <div class="space-y-8">
                        
                        <!-- Amenities -->
                        @if(!empty($post->amenities))
                        <div>
                            <h3 class="text-xl font-black text-slate-900 mb-5 flex items-center gap-3">
                                <span class="w-10 h-10 rounded bg-purple-50 text-purple-600 flex items-center justify-center text-lg"><i class="fa-solid fa-layer-group"></i></span> 
                                Tiện ích nổi bật
                            </h3>
                            <div class="flex flex-wrap gap-x-8 gap-y-4">
                                @php
                                    $amenities = is_string($post->amenities) ? json_decode($post->amenities, true) : $post->amenities;
                                    if (!is_array($amenities)) $amenities = [$post->amenities];
                                @endphp
                                @foreach($amenities as $amenity)
                                <div class="flex items-center gap-2.5 text-slate-700 font-medium text-base">
                                    <i class="fa-solid fa-check text-emerald-500 text-sm"></i>
                                    <span>{{ $amenity }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Bảng Giá Dịch Vụ Cơ Sở / Tòa Nhà (Lớp Vỏ) -->
                        @php
                            // Tái sử dụng biến $b đã chuẩn hóa ở đầu Main Content
                            $bTypeRaw = strtolower($b->type ?? ($post->property_type ?? ''));
                            $bTypeLabel = mb_strtolower($b->business_type ?? ($b->type ?? ($post->property_type ?? '')));
                            $isLand = in_array($bTypeRaw, ['dat_phan_lo', 'dat_nen', 'mat_bang']) || str_contains($bTypeLabel, 'phân lô') || str_contains($bTypeLabel, 'đất nền');
                            $isHouse = in_array($bTypeRaw, ['nha_rieng', 'nha_nguyen_can', 'nha_o']) || str_contains($bTypeLabel, 'nhà riêng') || str_contains($bTypeLabel, 'nhà ở') || str_contains($bTypeLabel, 'nguyên căn');
                            $isLienKe = in_array($bTypeRaw, ['cum_nha_lien_ke']) || str_contains($bTypeLabel, 'liền kề');

                            $hasFees = !empty($b) && (
                                ((float)($b->electricity_price ?? 0) > 0) ||
                                ((float)($b->water_price ?? 0) > 0) ||
                                ((float)($b->service_fee ?? 0) > 0) ||
                                ((float)($b->internet_fee ?? 0) > 0) ||
                                ((float)($b->parking_fee ?? 0) > 0)
                            );
                            $explicitPricing = !empty($b) && isset($b->has_service_pricing) && $b->has_service_pricing !== null 
                                ? (bool)$b->has_service_pricing 
                                : (!empty($b) && isset($b->apply_service_pricing) ? (bool)$b->apply_service_pricing : null);

                            if ($explicitPricing !== null) {
                                $isExcludedService = !$explicitPricing;
                            } elseif ($hasFees) {
                                $isExcludedService = false;
                            } else {
                                $isExcludedService = $isLand || $isHouse || $isLienKe;
                            }
                        @endphp

                        <div class="pt-8 border-t border-slate-100">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
                                <h3 class="text-xl font-black text-slate-900 flex items-center">
                                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600 mr-2"></i> Chi Phí Dịch Vụ Hàng Tháng (Áp dụng tại tòa nhà)
                                </h3>
                                @if(!empty($b) && !$isExcludedService)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 w-fit">
                                        <i class="fa-solid fa-building-circle-check text-[11px]"></i> {{ $b->name ?? 'Tòa nhà cơ sở' }}
                                    </span>
                                @endif
                            </div>

                            @if(empty($b) || $isExcludedService)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-600 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>
                                    <p class="text-sm font-medium">
                                        Điện, nước khách thuê tự thanh toán trực tiếp theo hóa đơn nhà nước.
                                    </p>
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3.5">
                                    <!-- 1. Điện -->
                                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-amber-300 hover:bg-amber-50/20 transition-all flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base shadow-xs shrink-0 border border-amber-100">
                                            <i class="fa-solid fa-bolt"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">⚡ Đơn giá Điện</div>
                                            <div class="text-base font-extrabold text-slate-900 truncate">
                                                @if(!empty($b->electricity_price) && (float)$b->electricity_price > 0)
                                                    {{ number_format($b->electricity_price, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">đ/kWh</span>
                                                @else
                                                    <span class="text-xs font-bold text-slate-600">Theo giá nhà nước</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Nước -->
                                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-sky-300 hover:bg-sky-50/20 transition-all flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base shadow-xs shrink-0 border border-sky-100">
                                            <i class="fa-solid fa-droplet"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">💧 Đơn giá Nước</div>
                                            <div class="text-base font-extrabold text-slate-900 truncate">
                                                @if(!empty($b->water_price) && (float)$b->water_price > 0)
                                                    {{ number_format($b->water_price, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">đ/{{ (($b->water_calc_type ?? 'm3') === 'person') ? 'người' : 'm³' }}</span>
                                                @else
                                                    <span class="text-xs font-bold text-slate-600">Theo giá nhà nước</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Vệ sinh / Dịch vụ -->
                                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-teal-300 hover:bg-teal-50/20 transition-all flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base shadow-xs shrink-0 border border-teal-100">
                                            <i class="fa-solid fa-broom"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">🧹 Vệ sinh / Dịch vụ</div>
                                            <div class="text-base font-extrabold text-slate-900 truncate">
                                                {{ number_format($b->service_fee ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">đ/phòng</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. Wifi / Internet -->
                                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-indigo-300 hover:bg-indigo-50/20 transition-all flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shadow-xs shrink-0 border border-indigo-100">
                                            <i class="fa-solid fa-wifi"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">📶 Wifi / Internet</div>
                                            <div class="text-base font-extrabold text-slate-900 truncate">
                                                {{ number_format($b->internet_fee ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">đ/phòng</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 5. Gửi xe -->
                                    <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/70 hover:border-slate-400 hover:bg-slate-100/50 transition-all flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base shadow-xs shrink-0 border border-slate-200">
                                            <i class="fa-solid fa-motorcycle"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">🛵 Phí Gửi xe</div>
                                            <div class="text-base font-extrabold text-slate-900 truncate">
                                                {{ number_format($b->parking_fee ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">đ/xe</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="pt-8 border-t border-slate-100">
                            <h3 class="text-xl font-black text-slate-900 mb-5 flex items-center gap-3">
                                <span class="w-10 h-10 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-lg"><i class="fa-solid fa-align-left"></i></span> 
                                Thông tin mô tả
                            </h3>
                            <div class="text-slate-600 leading-loose whitespace-pre-line text-base bg-slate-50 p-6 rounded-md border border-slate-100">{{ $post->description }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: User/Contact Info -->
            <div class="lg:col-span-4">
                <div class="sticky top-28 space-y-6">
                    <!-- User Card -->
                    <div class="bg-white rounded-lg p-6 shadow-sm border border-slate-200/60">
                        <h3 class="text-xs font-black text-slate-400 mb-4 uppercase tracking-widest pb-3 border-b border-slate-100">Thông tin liên hệ</h3>
                        
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-full bg-brand-100 flex items-center justify-center text-xl text-brand-600 font-bold border-2 border-brand-200 shrink-0">
                                {{ strtoupper(substr($post->user->ten_hien_thi ?? ($post->user->ho_ten ?? ($post->user->account_name ?? 'U')), 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900">{{ $post->user->ten_hien_thi ?? ($post->user->ho_ten ?? ($post->user->account_name ?? 'Người dùng')) }}</h4>
                                @php
                                    $u = $post->user;
                                    $isEnterprise = $u && (method_exists($u, 'isEnterprise') ? $u->isEnterprise() : in_array($u->role ?? '', ['doanhnghiep', 'enterprise']));
                                    $isNhansu = $u && (
                                        (method_exists($u, 'isSubAccount') && $u->isSubAccount()) ||
                                        (method_exists($u, 'isNhansu') && $u->isNhansu()) ||
                                        in_array($u->role ?? '', ['nhansu', 'sub_account', 'staff', 'nhan_su', 'nhan su']) ||
                                        !empty($u->parent_id) ||
                                        ($post->account_role ?? '') === 'nhansu'
                                    );
                                    $isAdmin = $u && (method_exists($u, 'isAdmin') ? $u->isAdmin() : ($u->role ?? '') === 'admin');
                                @endphp
                                @if($isAdmin)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider bg-amber-100 text-amber-800 mt-1">
                                    <i class="fa-solid fa-shield-halved"></i> Ban Quản Trị
                                </span>
                                @elseif($isNhansu)
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 mt-1">
                                    <i class="fa-solid fa-user-tie text-emerald-600"></i> Nhân sự quản lý tòa nhà (RentHome)
                                </span>
                                @elseif($isEnterprise)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider bg-purple-100 text-purple-700 mt-1">
                                    <i class="fa-solid fa-building"></i> Doanh nghiệp
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider bg-blue-100 text-blue-700 mt-1">
                                    <i class="fa-solid fa-user"></i> Cá nhân
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-5 mt-6 pt-6 border-t border-slate-100">
                            @if($isRented)
                            <!-- Ẩn các nút Gọi điện / Zalo khi phòng đã thuê -->
                            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-center">
                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-2 text-base">
                                    <i class="fa-solid fa-phone-slash"></i>
                                </div>
                                <h5 class="text-xs font-bold text-rose-800">Đã ngừng tiếp nhận liên hệ</h5>
                                <p class="text-[11px] text-rose-600 mt-1">Số điện thoại & Zalo đã được ẩn do căn nhà đã được cho thuê thành công.</p>
                            </div>
                            @else
                            <!-- Phone -->
                            <a href="tel:{{ $post->user->phone ?? '' }}" class="flex items-center group">
                                <div class="w-10 h-10 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-lg group-hover:scale-110 group-hover:bg-brand-100 transition-all shrink-0">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="ml-3 flex-1 overflow-hidden">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">SỐ ĐIỆN THOẠI</p>
                                    <p class="text-base font-black text-slate-800 truncate group-hover:text-brand-600 transition-colors">{{ $post->user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </a>
                            
                            <!-- Zalo -->
                            <a href="https://zalo.me/{{ $post->user->phone ?? '' }}" target="_blank" class="flex items-center group">
                                <div class="w-10 h-10 rounded bg-blue-50 text-blue-600 flex items-center justify-center text-lg group-hover:scale-110 group-hover:bg-blue-100 transition-all shrink-0">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </div>
                                <div class="ml-3 flex-1 overflow-hidden">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">ZALO</p>
                                    <p class="text-base font-black text-slate-800 truncate group-hover:text-blue-600 transition-colors">{{ $post->user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </a>

                            <!-- Email -->
                            <a href="mailto:{{ $post->user->email ?? '' }}" class="flex items-center group">
                                <div class="w-10 h-10 rounded bg-orange-50 text-orange-600 flex items-center justify-center text-lg group-hover:scale-110 group-hover:bg-orange-100 transition-all shrink-0">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="ml-3 flex-1 overflow-hidden">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">EMAIL</p>
                                    <p class="text-base font-black text-slate-800 truncate group-hover:text-orange-600 transition-colors">{{ $post->user->email ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </a>
                            @endif
                        </div>

                        @if(isset($post->user) && ($post->user->is_locked || $post->user->status === 'locked' || $post->user->is_deleted))
                        <div class="mt-4 p-3 bg-red-50 text-red-600 rounded text-sm font-medium text-center">
                            <i class="fa-solid fa-triangle-exclamation"></i> Tài khoản này hiện đang bị khóa.
                        </div>
                        @endif

                        <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                            <button type="button" onclick="openReportModal()" class="text-xs text-slate-400 hover:text-amber-600 font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                <i class="fa-regular fa-flag text-amber-500"></i> Tin đăng có sai phạm? Báo cáo ngay
                            </button>
                        </div>
                    </div>

                    <!-- Appointment/Contact Form -->
                    @if($isRented)
                    <div class="bg-white rounded-lg p-6 shadow-sm border border-slate-200/60 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-solid fa-house-circle-check"></i>
                        </div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px] uppercase tracking-wider mb-2">
                            Ngừng nhận liên hệ & đặt thuê
                        </span>
                        <h4 class="text-base font-bold text-slate-900 leading-snug">Căn nhà đã được thuê</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Form đặt thuê và gửi yêu cầu liên hệ hiện đã tạm khóa đối với bài đăng này do căn nhà đã được cho thuê thành công.
                        </p>
                        <a href="{{ url('/baidang') }}" class="mt-5 inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm phòng tương tự khác
                        </a>
                    </div>
                    @else
                    <div x-data="{ isContacted: {{ $hasContacted ? 'true' : 'false' }}, contactId: '{{ $contactId ?? '' }}', contactStatus: '{{ $contactStatus ?? '' }}', isRevoking: false }" class="bg-white rounded-lg p-6 shadow-sm border border-slate-200/60 relative">
                        <h3 class="text-xs font-black text-slate-400 mb-5 uppercase tracking-widest pb-3 border-b border-slate-100">Điền thông tin liên hệ</h3>
                        
                        @if(session('success'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(isset($errors) && $errors->any())
                            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('contact.store', $post->_id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div :class="{ 'opacity-50 pointer-events-none grayscale': isContacted }" class="space-y-4 transition-all duration-300">
                                <!-- Họ và tên -->
                                <div>
                                    <label for="fullname" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Họ và tên</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-regular fa-user text-sm"></i>
                                        </div>
                                        <input type="text" id="fullname" name="fullname" :disabled="isContacted" value="{{ auth()->check() ? (auth()->user()->account_name ?? auth()->user()->username ?? auth()->user()->name ?? '') : '' }}" placeholder="Nguyễn Văn A" class="w-full pl-10 pr-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                                    </div>
                                </div>
                                
                                <!-- SĐT -->
                                <div>
                                    <label for="phone" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Số điện thoại</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-phone text-sm"></i>
                                        </div>
                                        <input type="tel" id="phone" name="phone" :disabled="isContacted" value="{{ auth()->check() ? auth()->user()->phone : '' }}" placeholder="0901234567" class="w-full pl-10 pr-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                                    </div>
                                </div>
                                
                                <!-- Gmail -->
                                <div>
                                    <label for="email" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Gmail</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-regular fa-envelope text-sm"></i>
                                        </div>
                                        <input type="email" id="email" name="email" :disabled="isContacted" value="{{ auth()->check() ? auth()->user()->email : '' }}" placeholder="example@gmail.com" class="w-full pl-10 pr-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                                    </div>
                                </div>

                                <!-- Lịch hẹn -->
                                <div>
                                    <label for="appointment_date" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Lịch hẹn</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-regular fa-calendar-check text-sm"></i>
                                        </div>
                                        <input type="datetime-local" id="appointment_date" name="appointment_date" :disabled="isContacted" min="{{ now()->timezone('Asia/Ho_Chi_Minh')->format('Y-m-d\TH:i') }}" class="w-full pl-10 pr-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                                    </div>
                                </div>
                                
                                <!-- Lời nhắn -->
                                <div>
                                    <label for="message" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Lời nhắn</label>
                                    <div class="relative">
                                        <div class="absolute top-3 left-3.5 pointer-events-none text-slate-400">
                                            <i class="fa-regular fa-comment-dots text-sm"></i>
                                        </div>
                                        <textarea id="message" name="message" :disabled="isContacted" rows="3" placeholder="Tôi quan tâm đến bất động sản này, vui lòng liên hệ lại với tôi..." class="w-full pl-10 pr-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-medium text-slate-700 resize-none" required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" x-show="!isContacted" class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-600 hover:via-teal-600 hover:to-teal-700 text-white rounded-md font-bold transition-all duration-300 shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5 mt-3 text-base cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-sm"></i> Gửi thông tin
                            </button>
                            
                            <!-- Revoke Button -->
                            <button type="button" 
                                x-show="isContacted && contactStatus === 'pending'" 
                                x-cloak
                                @click="if(confirm('Bạn có chắc chắn muốn thu hồi yêu cầu liên hệ này không?')) {
                                    isRevoking = true;
                                    fetch('/contact/' + contactId + '/revoke', {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            isContacted = false;
                                            contactId = '';
                                            contactStatus = '';
                                            alert('Đã thu hồi yêu cầu thành công!');
                                        } else {
                                            alert(data.message || 'Lỗi khi thu hồi.');
                                        }
                                    })
                                    .catch(err => alert('Đã có lỗi mạng. Vui lòng thử lại.'))
                                    .finally(() => isRevoking = false);
                                }"
                                class="w-full py-3.5 px-4 bg-rose-50 border border-rose-500 text-rose-600 hover:bg-rose-100 rounded-md font-bold transition-all shadow-sm flex items-center justify-center gap-2 mt-2"
                                :disabled="isRevoking"
                                :class="{'opacity-50 cursor-not-allowed': isRevoking}"
                            >
                                <i class="fa-solid fa-trash-can"></i> <span x-text="isRevoking ? 'Đang xử lý...' : 'Thu hồi yêu cầu'"></span>
                            </button>

                            <!-- Status Badge when not pending -->
                            <div x-show="isContacted && contactStatus !== 'pending'" x-cloak class="w-full py-3.5 px-4 bg-slate-100 border border-slate-200 text-slate-500 rounded-md font-bold text-center mt-2 flex flex-col items-center justify-center gap-2">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-lock"></i> 
                                    <span x-text="
                                        contactStatus === 'communicating' ? 'Chủ nhà đang trao đổi' : 
                                        (contactStatus === 'approved' ? 'Yêu cầu đã được duyệt' : 
                                        (contactStatus === 'rejected' ? 'Yêu cầu bị từ chối' : 'Yêu cầu đã đóng'))
                                    "></span>
                                </div>
                                <template x-if="contactStatus === 'rejected' || contactStatus === 'closed' || contactStatus === 'liquidated'">
                                    <button type="button" 
                                        @click="isContacted = false; contactStatus = '';"
                                        class="mt-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-rotate-right"></i> Mở lại & gửi yêu cầu mới
                                    </button>
                                </template>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @php
            // Helper xử lý chuẩn thumbnail ảnh cho bài đăng (phòng cùng tòa nhà & bất động sản cùng loại)
            $resolveThumb = function($p) use ($b) {
                if (!empty($p->images) && is_iterable($p->images)) {
                    foreach ($p->images as $imgRef) {
                        $imgStr = is_array($imgRef) ? ($imgRef['url'] ?? ($imgRef['id'] ?? '')) : (is_object($imgRef) ? ($imgRef->url ?? ($imgRef->id ?? '')) : (string)$imgRef);
                        $imgStr = trim($imgStr);
                        if (empty($imgStr)) continue;

                        if (\Illuminate\Support\Str::startsWith($imgStr, ['/images/', 'images/', '/image/', 'image/'])) {
                            return '/' . ltrim($imgStr, '/');
                        }
                        if (preg_match('/^[a-f\d]{24}$/i', $imgStr)) {
                            return '/images/' . $imgStr;
                        }
                        if (\Illuminate\Support\Str::startsWith($imgStr, ['http://', 'https://', 'data:'])) {
                            if (str_contains($imgStr, '/images/')) {
                                return '/images/' . substr($imgStr, strpos($imgStr, '/images/') + 8);
                            }
                            return $imgStr;
                        }
                        if (\Illuminate\Support\Str::startsWith($imgStr, ['/storage/', 'storage/'])) {
                            return asset(ltrim($imgStr, '/'));
                        }
                        if (\Illuminate\Support\Str::startsWith($imgStr, '/')) {
                            return $imgStr;
                        }
                        return asset('storage/' . $imgStr);
                    }
                }

                if (!empty($p->all_images) && is_iterable($p->all_images)) {
                    foreach ($p->all_images as $im) {
                        $u = is_array($im) ? ($im['url'] ?? '') : (is_object($im) ? ($im->url ?? '') : (string)$im);
                        if (!empty($u)) {
                            if (str_contains($u, '/images/')) {
                                return '/images/' . substr($u, strpos($u, '/images/') + 8);
                            }
                            return $u;
                        }
                    }
                }

                if (!empty($p->display_image)) {
                    $d = (string)$p->display_image;
                    if (preg_match('/^[a-f\d]{24}$/i', $d)) {
                        return '/images/' . $d;
                    }
                    if (str_contains($d, '/images/')) {
                        return '/images/' . substr($d, strpos($d, '/images/') + 8);
                    }
                    return $d;
                }

                if (!empty($p->building)) {
                    $pB = $p->building;
                    if (!empty($pB->display_image)) return $pB->display_image;
                    if (!empty($pB->image)) {
                        return \Illuminate\Support\Str::startsWith($pB->image, ['http', 'data:', '/images/']) ? $pB->image : asset('storage/' . ltrim($pB->image, '/'));
                    }
                }

                if (!empty($b)) {
                    if (!empty($b->display_image)) return $b->display_image;
                    if (!empty($b->image)) {
                        return \Illuminate\Support\Str::startsWith($b->image, ['http', 'data:', '/images/']) ? $b->image : asset('storage/' . ltrim($b->image, '/'));
                    }
                }

                return 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';
            };

            // 4. Truy vấn danh sách $sameBuildingPosts từ cùng tòa nhà
            $sameBuildingPosts = collect([]);
            if (!empty($post->building_id) && !empty($b)) {
                $bIdStr = (string)$post->building_id;
                $sameBuildingPosts = \App\Models\Post::with(['user', 'building'])
                    ->where(function($q) use ($bIdStr, $post) {
                        $q->where('building_id', $bIdStr)
                          ->orWhere('building_id', $post->building_id);
                    })
                    ->where('_id', '!=', $post->_id)
                    ->whereIn('status', ['approved', 'active'])
                    ->latest()
                    ->get()
                    ->filter(function($p) {
                        return empty($p->is_rented) && !in_array($p->status, ['rented', 'dang_thue', 'đã cho thuê']);
                    })
                    ->take(8);
            }
        @endphp

        <!-- Section 4: Các phòng đang trống khác thuộc cùng tòa nhà (Lớp Vỏ) -->
        @if(!empty($post->building_id) && !empty($b))
        <div class="mt-12 pt-10 border-t border-slate-200">
            @if($sameBuildingPosts->count() > 0)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                            <i class="fa-solid fa-building-user text-brand-600"></i> Các phòng đang trống khác tại tòa nhà {{ $b->name }}
                        </h2>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm text-slate-500 font-medium">
                            <span><i class="fa-solid fa-location-dot text-brand-500"></i> {{ $b->full_address ?? ($b->address_detail ?? ($post->address ?? '')) }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Còn {{ $sameBuildingPosts->count() }} phòng trống khác đang mở cho thuê
                            </span>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <button onclick="document.getElementById('same-building-slider').scrollBy({ left: -320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm cursor-pointer" aria-label="Cuộn trái">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button onclick="document.getElementById('same-building-slider').scrollBy({ left: 320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm cursor-pointer" aria-label="Cuộn phải">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Container -->
                <div id="same-building-slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory pb-4 hide-scrollbar">
                    @foreach($sameBuildingPosts as $sbPost)
                    @php
                        $sbImg = $resolveThumb($sbPost);
                        $sbFloor = $sbPost->floor ?? ($sbPost->floor_number ?? null);
                        $sbType = $typeMap[strtolower($sbPost->property_type ?? '')] ?? ($typeMap[$sbPost->property_type ?? ''] ?? 'Phòng');
                    @endphp
                    <a href="{{ url('/chitietbaidang/' . $sbPost->_id) }}" class="min-w-[280px] sm:min-w-[300px] max-w-[300px] flex-none snap-start bg-white rounded-xl border border-slate-200/70 shadow-sm hover:shadow-lg transition-all group overflow-hidden flex flex-col h-full hover:-translate-y-1">
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            <img src="{{ $sbImg }}" loading="lazy" alt="{{ $sbPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';">
                            <div class="absolute top-3 left-3 bg-brand-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                {{ $sbType }}
                            </div>
                            @if(!empty($sbFloor))
                            <div class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1 border border-white/20">
                                <i class="fa-solid fa-stairs text-sky-400"></i> Tầng {{ $sbFloor }}
                            </div>
                            @endif
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="text-slate-900 font-bold text-sm mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors leading-snug">{{ $sbPost->title }}</h3>
                            <div class="mt-auto">
                                <div class="flex items-center justify-between mt-3 mb-2">
                                    <span class="text-brand-600 font-black text-base">{{ number_format($sbPost->price, 0, ',', '.') }} {{ $sbPost->price_unit }}</span>
                                    <span class="text-slate-600 font-bold text-xs">{{ $sbPost->area }} m&sup2;</span>
                                </div>
                                <div class="text-slate-500 text-xs flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2">
                                    <span class="truncate flex items-center gap-1 text-slate-600 font-medium">
                                        <i class="fa-solid fa-city text-emerald-600 text-[11px]"></i> {{ $b->name }}
                                    </span>
                                    <span class="text-brand-600 font-bold flex items-center gap-1 shrink-0 group-hover:translate-x-0.5 transition-transform">
                                        Xem <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <!-- Thông báo nhẹ nhàng khi chỉ có duy nhất 1 phòng này còn trống -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-50/70 via-emerald-50/40 to-slate-50 border border-amber-200/70 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0 border border-amber-200">
                            <i class="fa-solid fa-building-circle-check"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <span>Cơ sở / Tòa nhà: {{ $b->name }}</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Lớp Vỏ</span>
                            </h4>
                            <p class="text-sm font-medium text-slate-600 mt-0.5">
                                Hiện tại đây là phòng trống duy nhất đang mở cho thuê tại cơ sở {{ $b->name }}.
                            </p>
                        </div>
                    </div>
                    @if(!empty($b->full_address))
                    <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5 bg-white/80 px-3.5 py-2 rounded-lg border border-slate-200/80">
                        <i class="fa-solid fa-location-dot text-brand-500"></i> {{ $b->full_address }}
                    </div>
                    @endif
                </div>
            @endif
        </div>
        @endif

        <!-- Section 5: Bất động sản cùng loại (Loại trừ bài cùng tòa nhà và fallback ảnh chuẩn) -->
        @php
            // Danh sách ID cần loại trừ khỏi relatedPosts
            $excludeIds = array_filter(array_unique(array_merge(
                [(string)$post->_id, (string)($post->id ?? '')],
                $sameBuildingPosts->pluck('_id')->map(fn($id) => (string)$id)->toArray(),
                $sameBuildingPosts->pluck('id')->map(fn($id) => (string)$id)->toArray()
            )));

            $relatedPosts = \App\Models\Post::with(['user', 'building'])
                ->where('property_type', $post->property_type)
                ->whereNotIn('_id', $excludeIds)
                ->whereIn('status', ['approved', 'active'])
                ->latest()
                ->limit(15)
                ->get()
                ->filter(function($rp) use ($excludeIds) {
                    return !in_array((string)$rp->_id, $excludeIds) && !in_array((string)($rp->id ?? ''), $excludeIds);
                })
                ->take(10);
        @endphp

        @if($relatedPosts->count() > 0)
        <div class="mt-12 pt-10 border-t border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <i class="fa-solid fa-house-chimney text-brand-600"></i> Bất động sản cùng loại
                </h2>
                
                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2">
                    <button onclick="document.getElementById('related-slider').scrollBy({ left: -320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm cursor-pointer" aria-label="Cuộn trái">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button onclick="document.getElementById('related-slider').scrollBy({ left: 320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm cursor-pointer" aria-label="Cuộn phải">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Container -->
            <div id="related-slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory pb-4 hide-scrollbar">
                @foreach($relatedPosts as $rPost)
                @php
                    $rImg = $resolveThumb($rPost);
                    $rType = $typeMap[strtolower($rPost->property_type ?? '')] ?? ($typeMap[$rPost->property_type ?? ''] ?? 'Bất động sản');
                @endphp
                <a href="{{ url('/chitietbaidang/' . $rPost->_id) }}" class="min-w-[280px] sm:min-w-[300px] max-w-[300px] flex-none snap-start bg-white rounded-md border border-slate-200/60 shadow-sm hover:shadow-md transition-all group overflow-hidden flex flex-col h-full hover:-translate-y-0.5">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ $rImg }}" loading="lazy" alt="{{ $rPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';">
                        <div class="absolute top-3 left-3 bg-brand-600 text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider shadow-sm">
                            {{ $rType }}
                        </div>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-slate-900 font-bold text-sm mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">{{ $rPost->title }}</h3>
                        <div class="mt-auto">
                            <div class="flex items-center justify-between mt-3 mb-2">
                                <span class="text-brand-600 font-black text-base">{{ number_format($rPost->price, 0, ',', '.') }} {{ $rPost->price_unit }}</span>
                                <span class="text-slate-500 font-medium text-xs">{{ $rPost->area }} m&sup2;</span>
                            </div>
                            <div class="text-slate-500 text-xs flex items-center gap-1.5 truncate">
                                <i class="fa-solid fa-location-dot"></i> {{ $rPost->district ?? '' }}, {{ $rPost->province ?? '' }}
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- ================= MODAL BÁO CÁO / KHIẾU NẠI BÀI ĐĂNG (BƯỚC 1) ================= -->
    <div id="reportPostModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none" onclick="if(event.target === this) closeReportModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 transform scale-95 transition-all duration-300 max-h-[92vh] flex flex-col" id="reportModalBox">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-600 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-flag"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black leading-tight">Phản Ánh & Khiếu Nại Bài Đăng</h3>
                        <p class="text-xs text-amber-100 font-medium mt-0.5">Tiếp nhận và gửi thẳng đến bộ phận CSKH</p>
                    </div>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white cursor-pointer" title="Đóng">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="reportPostForm" action="{{ route('submit.khieu_nai') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf
                <!-- 2 thẻ Input ẩn (Hidden) để tự động lấy dữ liệu -->
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <input type="hidden" name="staff_id" value="{{ $post->staff_id }}">

                <!-- Trường 1 (Hiển thị readonly): Tiêu đề bài đăng đang bị khiếu nại -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tiêu đề bài đăng đang bị khiếu nại
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-house-chimney text-sm"></i>
                        </div>
                        <input type="text" readonly value="{{ $post->title }}" 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-700 text-xs sm:text-sm font-bold outline-none cursor-not-allowed select-all" 
                               title="{{ $post->title }}">
                    </div>
                </div>

                <!-- Trường 2 (Bắt buộc): Textarea Nội dung phản ánh -->
                <div>
                    <label for="noidung" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nội dung phản ánh <span class="text-rose-500 font-black">*</span>
                    </label>
                    <textarea id="noidung" name="noidung" rows="4" required
                              placeholder="Nội dung phản ánh (Ví dụ: Sai giá, phòng không giống ảnh, tin ảo đã cho thuê, thái độ không tốt...)"
                              class="w-full p-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none text-xs sm:text-sm text-slate-700 transition resize-none"></textarea>
                    <span class="text-[11px] text-slate-400 mt-1 block">Nội dung càng chi tiết sẽ giúp bộ phận CSKH giải quyết càng nhanh chóng.</span>
                </div>

                <!-- Trường 3 (Tùy chọn): Input upload file ảnh bằng chứng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Hình ảnh bằng chứng (Tùy chọn)
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-xl p-4 text-center cursor-pointer bg-slate-50 hover:bg-amber-50/20 transition-all"
                         onclick="document.getElementById('reportImagesInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-amber-500 mb-1"></i>
                        <p class="text-xs font-bold text-slate-700">Nhấn để chọn ảnh bằng chứng từ thiết bị</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Cho phép tải lên nhiều ảnh (JPG, PNG, WEBP, tối đa 5MB/ảnh)</p>
                    </div>
                    <input type="file" id="reportImagesInput" name="images[]" multiple accept="image/*" class="hidden" onchange="handleReportImagesPreview(event)">

                    <!-- Preview thumbnails container -->
                    <div id="reportImagesPreviewGrid" class="grid grid-cols-4 gap-2 mt-3 hidden"></div>
                </div>

                <!-- Thông báo trạng thái AJAX nếu có -->
                <div id="reportFormAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

                <!-- Modal Footer & Nút Submit -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs sm:text-sm transition cursor-pointer">
                        Hủy bỏ
                    </button>
                    <button type="submit" id="btnSubmitReport" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-rose-600 hover:from-amber-600 hover:via-orange-600 hover:to-rose-700 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-orange-500/25 flex items-center gap-2 cursor-pointer transition active:scale-95">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span id="btnSubmitReportText">Gửi báo cáo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script điều khiển Modal Báo cáo & Upload ảnh preview -->
    <script>
        function openReportModal() {
            const modal = document.getElementById('reportPostModal');
            const box = document.getElementById('reportModalBox');
            if (modal && box) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
                const textarea = document.getElementById('noidung');
                if (textarea) setTimeout(() => textarea.focus(), 150);
            }
        }

        function closeReportModal() {
            const modal = document.getElementById('reportPostModal');
            const box = document.getElementById('reportModalBox');
            if (modal && box) {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
            }
        }

        function handleReportImagesPreview(event) {
            const files = event.target.files;
            const container = document.getElementById('reportImagesPreviewGrid');
            container.innerHTML = '';

            if (files && files.length > 0) {
                container.classList.remove('hidden');
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative aspect-square rounded-lg overflow-hidden border border-slate-200 shadow-xs bg-slate-100 group';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview ${index + 1}">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[10px] font-bold">
                                    Ảnh ${index + 1}
                                </div>
                            `;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                container.classList.add('hidden');
            }
        }

        // Xử lý gửi Form khiếu nại qua AJAX và ngăn chặn gửi trùng lặp (Double Submit)
        let isSubmittingReport = false;
        document.getElementById('reportPostForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmittingReport) return;

            const btn = document.getElementById('btnSubmitReport');
            const btnText = document.getElementById('btnSubmitReportText');
            const alertBox = document.getElementById('reportFormAlert');

            const content = document.getElementById('noidung').value.trim();
            if (content.length < 5) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thông báo',
                        text: 'Vui lòng nhập nội dung khiếu nại tối thiểu 5 ký tự.',
                        confirmButtonColor: '#ea580c'
                    }).then(() => {
                        document.getElementById('noidung')?.focus();
                    });
                } else {
                    alert('Vui lòng nhập nội dung khiếu nại tối thiểu 5 ký tự.');
                    document.getElementById('noidung')?.focus();
                }
                return;
            }

            isSubmittingReport = true;
            btn.disabled = true;
            btnText.innerText = 'Đang gửi báo cáo...';
            alertBox.classList.add('hidden');

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = null;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    data = { success: response.ok };
                }

                if (response.ok && data && data.success) {
                    closeReportModal();
                    document.getElementById('reportPostForm').reset();
                    document.getElementById('reportImagesPreviewGrid').innerHTML = '';
                    document.getElementById('reportImagesPreviewGrid').classList.add('hidden');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message || 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.',
                            confirmButtonColor: '#16a34a',
                            confirmButtonText: 'Đóng'
                        });
                    } else {
                        alert(data.message || 'Cảm ơn bạn! Phản ánh của bạn đã được gửi tới Ban Quản Trị để xử lý.');
                    }
                } else {
                    const errorMsg = data?.message || (data?.errors ? Object.values(data.errors).flat().join('\n') : 'Có lỗi xảy ra khi gửi khiếu nại.');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Không thể gửi báo cáo',
                            text: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alertBox.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                        alertBox.innerText = errorMsg;
                        alertBox.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Đã có lỗi xảy ra',
                        text: 'Có lỗi kết nối khi gửi phản ánh. Vui lòng thử lại sau giây lát!',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Có lỗi kết nối khi gửi phản ánh. Vui lòng thử lại sau giây lát!');
                }
            } finally {
                isSubmittingReport = false;
                btn.disabled = false;
                btnText.innerText = 'Gửi báo cáo';
            }
        });

        // Tự động kích hoạt SweetAlert2 nếu trang tải lại kèm flash session
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: 'Đóng'
                    });
                }
            @endif

            @if(session('error'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Đã có lỗi xảy ra',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Đóng'
                    });
                }
            @endif
        });
    </script>
</body>
</html>


