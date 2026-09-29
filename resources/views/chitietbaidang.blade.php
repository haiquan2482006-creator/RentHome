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
    </style>
</head>
<body class="text-slate-800 antialiased pt-24">

    <!-- Header -->
    @include('partials.header-home')

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-slate-500 mb-6 font-medium bg-white px-4 py-3 rounded-2xl shadow-sm border border-slate-100 w-fit">
            <a href="{{ url('/') }}" class="hover:text-brand-600 transition-colors"><i class="fa-solid fa-house mr-1"></i> Trang chủ</a>
            <span class="mx-3 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
            <a href="{{ url('/baidang') }}" class="hover:text-brand-600 transition-colors">Thuê nhà</a>
            <span class="mx-3 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
            <span class="text-brand-600 font-bold line-clamp-1 max-w-[200px] sm:max-w-md">{{ $post->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Images & Details -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Main Header Info -->
                <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-200/60">
                    <div class="flex flex-wrap items-center gap-3 mb-5">
                        @php
                            $typeMap = [
                                'NHA_O' => 'Nhà ở',
                                'PHONG_TRO' => 'Phòng trọ',
                                'MAT_BANG' => 'Mặt bằng',
                                'CAN_HO' => 'Căn hộ',
                                'VAN_PHONG' => 'Văn phòng',
                            ];
                            $typeStr = $typeMap[$post->property_type] ?? $post->property_type ?? 'Bất động sản';
                        @endphp
                        <span class="px-4 py-1.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-bold rounded-full uppercase tracking-wider shadow-sm">
                            {{ $typeStr }}
                        </span>
                        <span class="text-slate-400 text-sm font-medium flex items-center gap-1.5 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                            <i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 leading-[1.3] mb-4 tracking-tight">{{ $post->title }}</h1>
                    
                    <div class="flex items-start sm:items-center gap-2 text-slate-500 font-medium text-base sm:text-lg">
                        <i class="fa-solid fa-location-dot text-brand-500 mt-1 sm:mt-0"></i>
                        <span>{{ $post->address ?? ($post->ward . ', ' . $post->district . ', ' . $post->province) }}</span>
                    </div>
                </div>

                <!-- Image Gallery -->
                <div class="rounded-[2rem] shadow-lg border border-slate-200/50 overflow-hidden bg-slate-900 relative group">
                    <div class="aspect-[16/9] w-full relative">
                        <img src="{{ $post->display_image }}" alt="{{ $post->title }}" class="w-full h-full object-contain sm:object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent pointer-events-none"></div>

                        @if($post->status === 'approved')
                        <div class="absolute top-5 left-5 bg-white/90 backdrop-blur-md text-emerald-600 text-xs font-black px-4 py-2 rounded-full shadow-lg flex items-center gap-2 border border-white/50">
                            <i class="fa-solid fa-circle-check text-base"></i> Đã kiểm duyệt
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Price and Area Cards -->
                <div class="grid grid-cols-2 gap-4 sm:gap-6">
                    <div class="bg-gradient-to-br from-brand-50 to-white rounded-3xl p-6 shadow-sm border border-brand-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-all">
                        <div class="absolute -right-4 -bottom-4 opacity-5 text-brand-600 text-7xl transition-transform group-hover:scale-110">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <span class="text-sm text-brand-600/80 font-bold mb-1 uppercase tracking-wider">Mức giá</span>
                        <span class="text-2xl sm:text-3xl font-black text-brand-600">{{ number_format($post->price, 0, ',', '.') }} <span class="text-lg sm:text-xl font-bold">{{ $post->price_unit }}</span></span>
                    </div>

                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-all">
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] text-slate-900 text-7xl transition-transform group-hover:scale-110">
                            <i class="fa-solid fa-vector-square"></i>
                        </div>
                        <span class="text-sm text-slate-400 font-bold mb-1 uppercase tracking-wider">Diện tích</span>
                        <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $post->area }} <span class="text-lg sm:text-xl font-bold">m&sup2;</span></span>
                    </div>
                </div>

                <!-- Description & Amenities -->
                <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-200/60 space-y-8">
                    
                    <!-- Description -->
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-5 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg"><i class="fa-solid fa-align-left"></i></span> 
                            Thông tin mô tả
                        </h3>
                        <div class="text-slate-600 leading-loose whitespace-pre-line text-base bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            {{ $post->description }}
                        </div>
                    </div>

                    <!-- Amenities -->
                    @if(!empty($post->amenities))
                    <div class="pt-8 border-t border-slate-100">
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg"><i class="fa-solid fa-layer-group"></i></span> 
                            Tiện ích nổi bật
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @php
                                $amenities = is_string($post->amenities) ? json_decode($post->amenities, true) : $post->amenities;
                                if (!is_array($amenities)) $amenities = [$post->amenities];
                            @endphp
                            @foreach($amenities as $amenity)
                            <div class="flex items-center gap-3 text-slate-700 font-semibold bg-white p-4 rounded-2xl border border-slate-200/60 shadow-sm hover:border-brand-300 hover:shadow-md transition-all group">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <span>{{ $amenity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: User/Contact Info -->
            <div class="lg:col-span-4 space-y-6">
                <!-- User Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 sticky top-28">
                    <h3 class="text-xs font-black text-slate-400 mb-4 uppercase tracking-widest pb-3 border-b border-slate-100">Thông tin liên hệ</h3>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-brand-100 flex items-center justify-center text-xl text-brand-600 font-bold border-2 border-brand-200 shrink-0">
                            {{ strtoupper(substr($post->user->account_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900">{{ $post->user->account_name ?? 'Người dùng' }}</h4>
                            @if(($post->user->account_type ?? '') === 'doanhnghiep')
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

                    <div class="space-y-3">
                        <button onclick="document.getElementById('contactModal').classList.remove('hidden'); document.getElementById('contactModal').classList.add('flex');" class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fa-solid fa-phone-volume"></i> Xem thông tin liên hệ
                        </button>
                    </div>

                    @if(isset($post->user) && ($post->user->is_locked || $post->user->status === 'locked' || $post->user->is_deleted))
                    <div class="mt-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm font-medium text-center">
                        <i class="fa-solid fa-triangle-exclamation"></i> Tài khoản này hiện đang bị khóa.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="bg-white py-8 mt-12 border-t border-slate-200 text-center">
        <p class="text-slate-500 font-medium text-sm">&copy; {{ date('Y') }} {{ $themeSettings['brand_name'] ?? 'RentHome' }}. Tất cả các quyền được bảo lưu.</p>
    </footer>

    <!-- Contact Modal (Business Card Style) -->
    <div id="contactModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="this.parentElement.classList.add('hidden'); this.parentElement.classList.remove('flex');"></div>
        
        <!-- Modal Content (Business Card) -->
        <div class="relative bg-white rounded-[2rem] shadow-2xl w-[90%] max-w-sm overflow-hidden animate-fade-in-up">
            <!-- Decorative Header -->
            <div class="h-32 bg-gradient-to-br from-brand-500 to-brand-700 relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 16px 16px;"></div>
                <button onclick="document.getElementById('contactModal').classList.add('hidden'); document.getElementById('contactModal').classList.remove('flex');" class="absolute top-4 right-4 w-8 h-8 bg-black/10 hover:bg-black/20 text-white rounded-full flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="px-8 pb-8">
                <!-- Avatar -->
                <div class="flex justify-center -mt-14 mb-4 relative z-10">
                    <div class="w-28 h-28 rounded-full bg-white p-2 shadow-xl">
                        <div class="w-full h-full rounded-full bg-brand-50 flex items-center justify-center text-4xl text-brand-600 font-black border-2 border-brand-100">
                            {{ strtoupper(substr($post->user->account_name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-black text-slate-900">{{ $post->user->account_name ?? 'Người dùng' }}</h3>
                    <p class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 uppercase tracking-widest mt-2 bg-slate-100 px-3 py-1 rounded-full">
                        @if(($post->user->account_type ?? '') === 'doanhnghiep')
                            <i class="fa-solid fa-building text-brand-500"></i> Doanh nghiệp
                        @else
                            <i class="fa-solid fa-user text-brand-500"></i> Cá nhân
                        @endif
                    </p>
                </div>
                
                <div class="space-y-3">
                    <a href="tel:{{ $post->user->phone ?? '' }}" class="flex items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-brand-300 hover:shadow-md transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">SỐ ĐIỆN THOẠI</p>
                            <p class="text-lg font-black text-slate-800">{{ $post->user->phone ?? 'Chưa cập nhật' }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-brand-500 transition-colors"></i>
                    </a>
                    
                    <a href="https://zalo.me/{{ $post->user->phone ?? '' }}" target="_blank" class="flex items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-300 hover:shadow-md transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">NHẮN TIN QUA</p>
                            <p class="text-lg font-black text-slate-800">Zalo</p>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
