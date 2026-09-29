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
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
                    
                    <!-- Main Header Info -->
                    <div>
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
                    @php
                        $galleryImages = [];
                        if (!empty($post->images) && is_array($post->images)) {
                            foreach($post->images as $imgRef) {
                                $imgStr = (string)$imgRef;
                                if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                                    $imgObj = \App\Models\Image::find($imgStr);
                                    if ($imgObj && !empty($imgObj->base64_data)) {
                                        $galleryImages[] = "data:{$imgObj->mime_type};base64,{$imgObj->base64_data}";
                                    }
                                } elseif (\Illuminate\Support\Str::startsWith($imgStr, 'http') || \Illuminate\Support\Str::startsWith($imgStr, 'data:')) {
                                    $galleryImages[] = $imgStr;
                                } else {
                                    $galleryImages[] = asset('storage/' . $imgStr);
                                }
                            }
                        }
                        
                        if(empty($galleryImages)) {
                            $galleryImages[] = $post->display_image;
                        }
                    @endphp

                    <div class="space-y-3">
                        <!-- Main Image -->
                        <div class="rounded-md shadow-sm border border-slate-200/50 overflow-hidden bg-slate-900 relative group">
                            <div class="aspect-[16/9] w-full relative">
                                <img id="mainImage" src="{{ $galleryImages[0] }}" alt="{{ $post->title }}" class="w-full h-full object-contain sm:object-cover transition-transform duration-700 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent pointer-events-none"></div>

                                @if($post->status === 'approved')
                                <div class="absolute top-5 left-5 bg-white/90 backdrop-blur-md text-emerald-600 text-xs font-black px-4 py-2 rounded-full shadow-sm flex items-center gap-2 border border-white/50">
                                    <i class="fa-solid fa-circle-check text-base"></i> Đã kiểm duyệt
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if(count($galleryImages) > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2 sm:gap-3">
                            @foreach($galleryImages as $index => $imgSrc)
                            <button onclick="document.getElementById('mainImage').src='{{ $imgSrc }}'" class="relative aspect-[4/3] rounded overflow-hidden border-2 border-transparent hover:border-brand-500 focus:border-brand-500 focus:outline-none transition-all group bg-slate-100 shadow-sm">
                                <img src="{{ $imgSrc }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover group-hover:opacity-100 opacity-60 transition-opacity">
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Price and Area -->
                    <div class="flex flex-wrap items-center gap-10">
                        <div class="flex flex-col">
                            <span class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Mức giá</span>
                            <span class="text-3xl font-black text-brand-600">{{ number_format($post->price, 0, ',', '.') }} <span class="text-xl font-bold">{{ $post->price_unit }}</span></span>
                        </div>

                        <div class="w-px h-10 bg-slate-200 hidden sm:block"></div>

                        <div class="flex flex-col">
                            <span class="text-sm text-slate-500 font-bold uppercase tracking-wider mb-1">Diện tích</span>
                            <span class="text-3xl font-black text-slate-800">{{ $post->area }} <span class="text-xl font-bold">m&sup2;</span></span>
                        </div>
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

                        <!-- Description -->
                        <div class="@if(!empty($post->amenities)) pt-8 border-t border-slate-100 @endif">
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

                        <div class="space-y-5 mt-6 pt-6 border-t border-slate-100">
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
                        </div>

                        @if(isset($post->user) && ($post->user->is_locked || $post->user->status === 'locked' || $post->user->is_deleted))
                        <div class="mt-4 p-3 bg-red-50 text-red-600 rounded text-sm font-medium text-center">
                            <i class="fa-solid fa-triangle-exclamation"></i> Tài khoản này hiện đang bị khóa.
                        </div>
                        @endif
                    </div>

                    <!-- Appointment/Contact Form -->
                    <div class="bg-white rounded-lg p-6 shadow-sm border border-slate-200/60">
                        <h3 class="text-xs font-black text-slate-400 mb-5 uppercase tracking-widest pb-3 border-b border-slate-100">Điền thông tin liên hệ</h3>
                        
                        <form action="#" method="POST" class="space-y-4">
                            @csrf
                            <!-- Họ và tên -->
                            <div>
                                <label for="fullname" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Họ và tên</label>
                                <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" class="w-full px-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                            </div>
                            
                            <!-- SĐT -->
                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" placeholder="0901234567" class="w-full px-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                            </div>
                            
                            <!-- Gmail -->
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Gmail</label>
                                <input type="email" id="email" name="email" placeholder="example@gmail.com" class="w-full px-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                            </div>

                            <!-- Lịch hẹn -->
                            <div>
                                <label for="appointment_date" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Lịch hẹn</label>
                                <input type="datetime-local" id="appointment_date" name="appointment_date" class="w-full px-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all text-sm font-medium text-slate-700" required>
                            </div>
                            
                            <!-- Lời nhắn -->
                            <div>
                                <label for="message" class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Lời nhắn</label>
                                <textarea id="message" name="message" rows="3" placeholder="Tôi quan tâm đến bất động sản này, vui lòng liên hệ lại với tôi..." class="w-full px-4 py-2.5 rounded border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all text-sm font-medium text-slate-700 resize-none" required></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="w-full py-3.5 px-4 bg-slate-900 hover:bg-brand-600 text-white rounded-md font-bold transition-all shadow hover:shadow-lg flex items-center justify-center gap-2 mt-2">
                                <i class="fa-regular fa-paper-plane"></i> Gửi thông tin
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        @php
            $relatedPosts = \App\Models\Post::where('property_type', $post->property_type)
                ->where('_id', '!=', $post->_id)
                ->where('status', 'approved')
                ->latest()
                ->limit(10)
                ->get();
        @endphp

        @if($relatedPosts->count() > 0)
        <div class="mt-12 pt-10 border-t border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <i class="fa-solid fa-house-chimney text-brand-600"></i> Bất động sản cùng loại
                </h2>
                
                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2">
                    <button onclick="document.getElementById('related-slider').scrollBy({ left: -320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button onclick="document.getElementById('related-slider').scrollBy({ left: 320, behavior: 'smooth' })" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-brand-600 text-slate-600 hover:text-white flex items-center justify-center transition-colors shadow-sm">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Container -->
            <div id="related-slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory pb-4 hide-scrollbar">
                @foreach($relatedPosts as $rPost)
                <a href="{{ url('/chitietbaidang/' . $rPost->_id) }}" class="min-w-[280px] sm:min-w-[300px] max-w-[300px] flex-none snap-start bg-white rounded-md border border-slate-200/60 shadow-sm hover:shadow-md transition-all group overflow-hidden flex flex-col h-full">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ $rPost->display_image }}" alt="{{ $rPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3 bg-brand-600 text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider shadow-sm">
                            {{ $typeMap[$rPost->property_type] ?? 'Bất động sản' }}
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

    <!-- Footer Simple -->
    <footer class="bg-white py-8 mt-12 border-t border-slate-200 text-center">
        <p class="text-slate-500 font-medium text-sm">&copy; {{ date('Y') }} {{ $themeSettings['brand_name'] ?? 'RentHome' }}. Tất cả các quyền được bảo lưu.</p>
    </footer>


</body>
</html>

