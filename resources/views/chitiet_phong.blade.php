<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $post->title }} - Chi Tiết Bất Động Sản | RentHome</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
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
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    @include('partials.header-home')

    @php
        $unitText = '/tháng';
        if ($post->price_unit === 'nam') $unitText = '/năm';
        elseif ($post->price_unit === 'm2') $unitText = '/m²';
        elseif ($post->price_unit === 'tong') $unitText = '';
        
        $priceFormatted = is_numeric($post->price) ? number_format((float)$post->price, 0, ',', '.') . ' VNĐ' . $unitText : ($post->price ?? 'Thỏa thuận');
        
        $images = $post->all_images ?? [$post->display_image];
        if (empty($images)) {
            $images = ['https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80'];
        }

        $addressParts = array_filter([$post->address, $post->ward, $post->district, $post->province]);
        $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : 'Đang cập nhật địa chỉ';

        $poster = $post->user;
        $posterName = $poster ? ($poster->account_name ?: $poster->username) : 'Ban Quản Lý RentHome';
        $posterPhone = $poster->phone ?? '0988888888';
        $isEnterprise = $poster && $poster->account_type === 'enterprise';

        $propertyTypeName = 'Căn hộ / Phòng trọ';
        $pType = $post->property_type ?? '';
        if ($pType === 'ch' || str_contains(mb_strtolower($post->title), 'chung cư') || str_contains(mb_strtolower($post->title), 'căn hộ')) {
            $propertyTypeName = 'Căn hộ / Chung cư';
        } elseif ($pType === 'villa' || str_contains(mb_strtolower($post->title), 'biệt thự')) {
            $propertyTypeName = 'Biệt thự / Villa';
        } elseif ($pType === 'nnc' || str_contains(mb_strtolower($post->title), 'nguyên căn')) {
            $propertyTypeName = 'Nhà nguyên căn';
        } elseif ($pType === 'phong_tro' || str_contains(mb_strtolower($post->title), 'phòng trọ')) {
            $propertyTypeName = 'Phòng trọ sinh viên';
        } elseif ($pType === 'dat_nen') {
            $propertyTypeName = 'Mặt bằng / Đất nền';
        }

        $amenities = is_array($post->amenities) ? $post->amenities : [];
    @endphp

    <!-- Main Container -->
    <main class="pt-28 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        <!-- Breadcrumb & Back button -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <nav class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                <a href="{{ url('/') }}" class="hover:text-brand-600 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-xs"></i> Trang chủ
                </a>
                <span>/</span>
                <a href="{{ url('/baidang') }}" class="hover:text-brand-600 transition-colors">Tất cả bài đăng</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold truncate max-w-[200px] sm:max-w-[350px]">{{ $post->title }}</span>
            </nav>

            <a href="{{ url('/baidang') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách phòng
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Main Details (8 cols) -->
            <div class="lg:col-span-8 flex flex-col gap-8">

                <!-- Image Gallery Card -->
                <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Main Preview Image -->
                    <div class="relative w-full aspect-[16/10] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-slate-900 group">
                        <img id="main-gallery-img" src="{{ $images[0] }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105">
                        
                        <!-- Badges floating over image -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 rounded-xl bg-brand-600 text-white font-bold text-xs shadow-lg flex items-center gap-1.5 backdrop-blur-md">
                                <i class="fa-solid fa-circle-check"></i> Đã kiểm duyệt
                            </span>
                            <span class="px-3 py-1.5 rounded-xl bg-slate-900/80 text-white font-bold text-xs shadow-lg backdrop-blur-md">
                                {{ $propertyTypeName }}
                            </span>
                            @if($post->building)
                                <span class="px-3 py-1.5 rounded-xl bg-purple-600 text-white font-bold text-xs shadow-lg flex items-center gap-1.5">
                                    <i class="fa-solid fa-building"></i> Dự án: {{ $post->building->name ?? 'Tòa nhà' }}
                                </span>
                            @endif
                        </div>

                        <!-- Price Tag floating bottom left -->
                        <div class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-md px-4 py-2 rounded-2xl shadow-xl border border-white/50">
                            <span class="text-xs text-slate-500 font-semibold block">Giá thuê niêm yết:</span>
                            <span class="text-xl sm:text-2xl font-extrabold text-brand-600">{{ $priceFormatted }}</span>
                        </div>
                    </div>

                    <!-- Thumbnails row if more than 1 image -->
                    @if(count($images) > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mt-4">
                            @foreach($images as $index => $img)
                                <button type="button" onclick="switchGalleryImage('{{ $img }}', this)" class="thumbnail-btn aspect-[4/3] rounded-xl overflow-hidden border-2 transition-all {{ $index === 0 ? 'border-brand-600 ring-2 ring-brand-500/30' : 'border-slate-200 hover:border-slate-300' }}">
                                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="Thumbnail">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Property Info & Specifications Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold border border-brand-200">
                            Mã tin: #{{ substr((string)($post->_id ?? $post->id), -6) }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            <i class="fa-regular fa-clock mr-1"></i> Đăng ngày {{ $post->created_at ? $post->created_at->format('d/m/Y') : date('d/m/Y') }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug mb-4">
                        {{ $post->title }}
                    </h1>

                    <div class="flex items-start gap-2.5 text-slate-600 mb-6 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <i class="fa-solid fa-location-dot text-rose-500 text-lg mt-0.5 shrink-0"></i>
                        <span class="text-sm sm:text-base font-medium">{{ $fullAddress }}</span>
                    </div>

                    <!-- Key Specifications Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-100 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-ruler-combined"></i>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-semibold block">Diện tích</span>
                                <span class="text-base font-extrabold text-slate-800">{{ $post->area }} m²</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-semibold block">Mức giá</span>
                                <span class="text-base font-extrabold text-slate-800">{{ is_numeric($post->price) ? number_format((float)$post->price, 0, ',', '.') : $post->price }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-semibold block">Loại hình</span>
                                <span class="text-base font-extrabold text-slate-800">{{ $propertyTypeName }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-semibold block">Trạng thái</span>
                                <span class="text-base font-extrabold text-emerald-600">Đang sẵn phòng</span>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-sparkles text-brand-600"></i> Tiện ích có sẵn
                        </h3>

                        @if(!empty($amenities))
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($amenities as $item)
                                    @php
                                        $icon = 'fa-check';
                                        $itemLower = mb_strtolower($item);
                                        if (str_contains($itemLower, 'bảo vệ')) $icon = 'fa-shield-halved';
                                        elseif (str_contains($itemLower, 'đỗ xe') || str_contains($itemLower, 'xe')) $icon = 'fa-square-parking';
                                        elseif (str_contains($itemLower, 'mặt tiền')) $icon = 'fa-road';
                                        elseif (str_contains($itemLower, 'chợ') || str_contains($itemLower, 'siêu thị')) $icon = 'fa-cart-shopping';
                                        elseif (str_contains($itemLower, 'điện') || str_contains($itemLower, 'nước')) $icon = 'fa-bolt';
                                        elseif (str_contains($itemLower, 'máy lạnh') || str_contains($itemLower, 'điều hòa')) $icon = 'fa-snowflake';
                                        elseif (str_contains($itemLower, 'tủ lạnh')) $icon = 'fa-temperature-arrow-down';
                                        elseif (str_contains($itemLower, 'máy giặt')) $icon = 'fa-shirt';
                                        elseif (str_contains($itemLower, 'wifi') || str_contains($itemLower, 'mạng')) $icon = 'fa-wifi';
                                        elseif (str_contains($itemLower, 'thang máy')) $icon = 'fa-elevator';
                                    @endphp
                                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 font-semibold text-sm">
                                        <i class="fa-solid {{ $icon }} text-brand-600 w-4 text-center"></i>
                                        <span>{{ $item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 italic">Chưa có thông tin tiện ích chi tiết.</p>
                        @endif
                    </div>

                    <!-- Description Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-brand-600"></i> Mô tả chi tiết phòng
                        </h3>
                        <div class="prose max-w-none text-slate-600 text-sm sm:text-base leading-relaxed whitespace-pre-line bg-slate-50/50 p-5 rounded-2xl border border-slate-100">
                            {{ $post->description ?: 'Chủ nhà chưa cập nhật mô tả chi tiết cho căn phòng này. Quý khách vui lòng liên hệ trực tiếp chủ nhà qua số điện thoại bên phải để được tư vấn cụ thể.' }}
                        </div>
                    </div>

                    <!-- RentHome Guarantees -->
                    <div class="p-5 rounded-2xl bg-brand-50 border border-brand-100 flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand-600 text-white flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <div class="text-center sm:text-left">
                            <h4 class="font-bold text-brand-900 text-base">Cam kết an toàn từ nền tảng RentHome</h4>
                            <p class="text-xs sm:text-sm text-brand-700 mt-0.5">Tin đăng đã được đội ngũ quản trị viên kiểm tra thực tế. Người thuê được miễn phí 100% chi phí môi giới khi xem phòng.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Sticky Landlord & Action Sidebar (4 cols) -->
            <div class="lg:col-span-4 sticky top-28 flex flex-col gap-6">

                <!-- Landlord Profile Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-4">Thông tin chủ nhà / Người đăng</span>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold shrink-0 shadow-sm border border-slate-100 {{ $isEnterprise ? 'bg-blue-50 text-blue-600' : 'bg-brand-50 text-brand-600' }}">
                            <i class="fa-solid {{ $isEnterprise ? 'fa-building' : 'fa-user' }}"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-extrabold text-slate-900 text-base sm:text-lg truncate">{{ $posterName }}</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold mt-1 {{ $isEnterprise ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-600' }}">
                                <i class="fa-solid fa-circle-check text-[10px] text-brand-500"></i>
                                {{ $isEnterprise ? 'Doanh nghiệp xác thực' : 'Chính chủ cho thuê' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <!-- Call Hotline -->
                        <a href="tel:{{ $posterPhone }}" class="w-full py-3.5 px-4 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-brand-500/30">
                            <i class="fa-solid fa-phone"></i> Gọi ngay: {{ $posterPhone }}
                        </a>

                        <!-- Zalo Chat -->
                        <a href="https://zalo.me/{{ $posterPhone }}" target="_blank" class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20">
                            <i class="fa-solid fa-comment-dots"></i> Nhắn tin qua Zalo
                        </a>

                        <!-- Schedule Visit Button -->
                        <button type="button" onclick="openScheduleModal()" class="w-full py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2 border border-slate-200">
                            <i class="fa-regular fa-calendar-check text-brand-600"></i> Đặt lịch hẹn xem phòng
                        </button>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 text-center">
                        <span class="text-xs text-slate-500 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-brand-500"></i> Thông tin số điện thoại đã được xác minh
                        </span>
                    </div>
                </div>

                <!-- Fast Actions & Tools -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h4 class="font-bold text-slate-900 text-sm mb-3">Công cụ tiện ích</h4>
                    <div class="flex flex-col gap-2.5">
                        <button type="button" onclick="copyCurrentUrl()" class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors flex items-center justify-between border border-slate-200">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-copy text-slate-500"></i> Sao chép liên kết phòng</span>
                            <span id="copy-status" class="text-brand-600 text-[11px] font-bold"></span>
                        </button>

                        <button type="button" onclick="alert('Đã lưu bài đăng vào mục yêu thích của bạn!')" class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-2 border border-slate-200">
                            <i class="fa-regular fa-heart text-rose-500"></i> Lưu tin đăng yêu thích
                        </button>

                        <button type="button" onclick="alert('Cảm ơn bạn. Ban Quản Trị RentHome sẽ tiếp nhận và kiểm tra phản ánh về bài đăng này trong vòng 24h.')" class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-semibold text-xs transition-colors flex items-center gap-2 border border-slate-200">
                            <i class="fa-solid fa-flag text-slate-400"></i> Báo cáo tin đăng sai giá / lừa đảo
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Modal: Đặt Lịch Hẹn Xem Phòng -->
    <div id="schedule-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-brand-600"></i> Đặt Lịch Xem Phòng
                </h3>
                <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4">
                Để lại thông tin, chủ nhà hoặc chuyên viên RentHome sẽ liên hệ xác nhận lịch hẹn trong ít phút:
            </p>

            <form onsubmit="handleScheduleSubmit(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên của bạn</label>
                    <input type="text" required placeholder="Nhập tên của bạn" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại liên hệ</label>
                    <input type="tel" required placeholder="Ví dụ: 0988 888 888" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Thời gian bạn muốn đến xem</label>
                    <input type="datetime-local" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú thêm (nếu có)</label>
                    <textarea rows="2" placeholder="Ví dụ: Mình muốn xem phòng vào buổi tối sau 6h..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none resize-none"></textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeScheduleModal()" class="w-1/2 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-sm text-slate-700 transition-colors">
                        Đóng
                    </button>
                    <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 font-bold text-sm text-white transition-colors shadow-lg shadow-brand-500/30">
                        Xác nhận đặt hẹn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Chat Assistant Widget -->
    @include('partials.ai-chat-widget')

    <script>
        function switchGalleryImage(url, btn) {
            const mainImg = document.getElementById('main-gallery-img');
            if (mainImg) {
                mainImg.src = url;
            }
            document.querySelectorAll('.thumbnail-btn').forEach(b => {
                b.classList.remove('border-brand-600', 'ring-2', 'ring-brand-500/30');
                b.classList.add('border-slate-200');
            });
            if (btn) {
                btn.classList.add('border-brand-600', 'ring-2', 'ring-brand-500/30');
                btn.classList.remove('border-slate-200');
            }
        }

        function copyCurrentUrl() {
            navigator.clipboard.writeText(window.location.href);
            const status = document.getElementById('copy-status');
            if (status) {
                status.innerText = 'Đã sao chép! ✓';
                setTimeout(() => {
                    status.innerText = '';
                }, 2500);
            }
        }

        function openScheduleModal() {
            const modal = document.getElementById('schedule-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeScheduleModal() {
            const modal = document.getElementById('schedule-modal');
            if (modal) modal.classList.add('hidden');
        }

        function handleScheduleSubmit(e) {
            e.preventDefault();
            closeScheduleModal();
            alert('🎉 ĐẶT LỊCH HẸN THÀNH CÔNG!\n\nChủ nhà {{ $posterName }} đã nhận được yêu cầu hẹn xem phòng của bạn và sẽ liên hệ xác nhận trong thời gian sớm nhất!');
        }
    </script>
</body>
</html>
