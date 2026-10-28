<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Bài Đăng Nổi Bật (Tin VIP) - RentHome Admin</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'hover': '0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04)',
                        'glow-amber': '0 0 20px -3px rgba(245, 158, 11, 0.35)',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
        }

        /* Đồng bộ kích thước và khoảng cách với Header-Admin Sidebar */
        .admin-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            padding: 28px 36px;
            background-color: #f8fafc;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .admin-main {
            margin-left: 78px !important;
            width: calc(100% - 78px) !important;
        }

        @media (max-width: 991px) {
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 20px 16px;
            }
        }

        /* Nút gạt Toggle Switch VIP đẹp mắt */
        .toggle-switch-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 52px;
            height: 28px;
            border-radius: 9999px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            user-select: none;
            outline: none;
        }

        .toggle-switch-btn.active {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            box-shadow: 0 4px 14px -2px rgba(245, 158, 11, 0.5);
        }

        .toggle-switch-btn.inactive {
            background-color: #cbd5e1;
        }

        .toggle-switch-btn.inactive:hover {
            background-color: #94a3b8;
        }

        .toggle-switch-knob {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background-color: #ffffff;
            border-radius: 9999px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-switch-btn.active .toggle-switch-knob {
            transform: translateX(24px);
        }

        .toggle-switch-btn.loading {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Header & Sidebar Admin Dùng Chung -->
    @include('partials.header-admin')

    <!-- Vùng Nội Dung Chính (Main Content) -->
    <main class="admin-main">
        <div class="max-w-[1700px] mx-auto w-full space-y-6">

            <!-- 1. HEADER & BREADCRUMB -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1.5">
                        <a href="{{ url('Admin') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-house text-slate-400"></i> Admin
                        </a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-700">Tiếp thị & Quảng bá</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-blue-600 font-bold">Bài Đăng Nổi Bật</span>
                    </nav>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20">
                            <i class="fa-solid fa-star text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                                Quản Lý Bài Đăng Nổi Bật <span class="text-amber-500 text-lg font-bold px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200/80">Tin VIP</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                Tuyển chọn thủ công 100% các tin đăng VIP chất lượng cao để hiển thị trên Slider Nổi Bật Trang chủ.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Làm mới & Xem Trang Chủ -->
                <div class="flex items-center gap-3 self-start lg:self-center">
                    <a href="{{ url('Admin/bai-dang-noi-bat') }}" class="px-4 py-2.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs rounded-xl shadow-xs hover:bg-slate-50 transition-all flex items-center gap-2" title="Tải lại dữ liệu">
                        <i class="fa-solid fa-arrows-rotate text-slate-500"></i>
                        <span>Làm mới</span>
                    </a>
                    <a href="{{ url('/#featured-posts-section') }}" target="_blank" class="px-4 py-2.5 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition-all flex items-center gap-2 group" title="Xem trực tiếp trên trang chủ">
                        <i class="fa-solid fa-arrow-up-right-from-square group-hover:scale-110 transition-transform"></i>
                        <span>Xem Slider Trang Chủ</span>
                    </a>
                </div>
            </div>

            <!-- 2. THẺ THỐNG KÊ (CARDS SUMMARY) -->
            @php
                $pct = $maxFeatured > 0 ? min(100, round(($featuredCount / $maxFeatured) * 100)) : 0;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Thẻ 1: Trạng thái hiển thị trên Trang chủ (Theo yêu cầu: Ví dụ "Đang hiển thị 5/8 bài") -->
                <div class="bg-gradient-to-br from-amber-50 via-white to-amber-50/40 rounded-2xl p-5 border border-amber-200/80 shadow-card hover:shadow-hover transition-all relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-amber-500 text-white shadow-xs">
                                <i class="fa-solid fa-fire text-[10px]"></i> Slider Trang Chủ
                            </span>
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-900 tracking-tight" id="stat-featured-count">{{ $featuredCount }}</span>
                                <span class="text-slate-400 font-bold text-lg">/ {{ $maxFeatured }} bài</span>
                            </div>
                            <p class="text-xs font-semibold text-slate-600 mt-1" id="stat-featured-label">
                                Đang hiển thị <span class="font-bold text-amber-600" id="stat-display-fraction">{{ $featuredCount }}/{{ $maxFeatured }}</span> bài trên Trang chủ
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl font-bold shrink-0">
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4 pt-3 border-t border-amber-100">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 mb-1">
                            <span>Tỷ lệ lấp đầy mục VIP</span>
                            <span id="stat-progress-pct" class="text-amber-600 font-extrabold">{{ $pct }}%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div id="stat-progress-bar" class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 2: Tổng bài đăng đã duyệt & Đang còn trống (Available) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-hover transition-all relative overflow-hidden group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                <i class="fa-solid fa-door-open text-emerald-500 text-[10px]"></i> Phòng Còn Trống
                            </span>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalApprovedCount) }}</span>
                                <span class="text-slate-400 font-bold text-xs ml-1">phòng trống</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Đã loại trừ 100% phòng đã có khách thuê</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-house-circle-check"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Trạng thái phòng:</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span> Sẵn sàng cho thuê
                        </span>
                    </div>
                </div>

                <!-- Thẻ 3: Bài đăng thường (Chưa gán nổi bật) -->
                @php
                    $normalCount = max(0, $totalApprovedCount - $featuredCount);
                @endphp
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-hover transition-all relative overflow-hidden group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                <i class="fa-solid fa-list-check text-slate-500 text-[10px]"></i> Tin Tiêu Chuẩn
                            </span>
                            <div class="mt-3">
                                <span class="text-3xl font-black text-slate-900 tracking-tight" id="stat-normal-count">{{ number_format($normalCount) }}</span>
                                <span class="text-slate-400 font-bold text-xs ml-1">bài thường</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Chưa được gán vào Slider VIP</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-regular fa-clone"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Cơ chế quản lý:</span>
                        <span class="font-bold text-slate-700">100% Thủ công</span>
                    </div>
                </div>

                <!-- Thẻ 4: Gợi ý tối ưu & Giới hạn Slider -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-hover transition-all relative overflow-hidden group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60">
                                <i class="fa-solid fa-lightbulb text-amber-500 text-[10px]"></i> Khuyến nghị
                            </span>
                            <div class="mt-3">
                                <span class="text-xl font-extrabold text-slate-900">Khuyên dùng 6 - 8 bài</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Giúp slider trang chủ chạy mượt & cân đối trải nghiệm</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Sắp xếp:</span>
                        <span class="font-bold text-sky-600">Gán mới nhất ưu tiên đầu</span>
                    </div>
                </div>
            </div>

            <!-- 3. BỘ LỌC TABS & Ô TÌM KIẾM BÀI ĐĂNG -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-card space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <!-- 2 Nút Tabs theo yêu cầu: "Tất cả bài đăng" / "Đang nổi bật" -->
                    <div class="inline-flex p-1 bg-slate-100/90 rounded-xl border border-slate-200/70 self-start md:self-auto">
                        <!-- Tab 1: Tất cả bài đăng -->
                        <a href="{{ url('Admin/bai-dang-noi-bat?tab=all' . ($search ? '&search=' . urlencode($search) : '')) }}"
                            class="px-4 sm:px-5 py-2.5 rounded-lg text-xs sm:text-sm font-bold transition-all flex items-center gap-2 {{ $tab !== 'featured' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                            <i class="fa-solid fa-layer-group text-slate-400"></i>
                            <span>Tất cả bài đăng</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $tab !== 'featured' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $totalApprovedCount }}
                            </span>
                        </a>

                        <!-- Tab 2: Đang nổi bật -->
                        <a href="{{ url('Admin/bai-dang-noi-bat?tab=featured' . ($search ? '&search=' . urlencode($search) : '')) }}"
                            class="px-4 sm:px-5 py-2.5 rounded-lg text-xs sm:text-sm font-bold transition-all flex items-center gap-2 {{ $tab === 'featured' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm shadow-amber-500/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50' }}">
                            <i class="fa-solid fa-star {{ $tab === 'featured' ? 'text-amber-100' : 'text-amber-500' }}"></i>
                            <span>Đang nổi bật</span>
                            <span id="tab-featured-badge" class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $tab === 'featured' ? 'bg-white text-amber-700' : 'bg-amber-100 text-amber-800' }}">
                                {{ $featuredCount }}
                            </span>
                        </a>
                    </div>

                    <!-- Ô tìm kiếm bài đăng (theo tiêu đề/mã tin) -->
                    <form method="GET" action="{{ url('Admin/bai-dang-noi-bat') }}" class="flex items-center gap-2 w-full md:w-auto">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="relative flex-1 md:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </span>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Tìm theo tiêu đề, mã tin, địa chỉ..."
                                class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all placeholder:text-slate-400">
                            @if(!empty($search))
                                <a href="{{ url('Admin/bai-dang-noi-bat?tab=' . $tab) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-rose-500 transition-colors" title="Xóa tìm kiếm">
                                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                @if(!empty($search))
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs text-slate-600">
                        <span>Kết quả tìm kiếm cho:</span>
                        <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">"{{ $search }}"</span>
                        <a href="{{ url('Admin/bai-dang-noi-bat?tab=' . $tab) }}" class="text-blue-600 hover:underline font-semibold ml-2">Xóa bộ lọc</a>
                    </div>
                @endif
            </div>

            <!-- 4. BẢNG DANH SÁCH BÀI ĐĂNG (TABLE) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 w-20 text-center">Hình ảnh</th>
                                <th class="py-3.5 px-4 min-w-[280px]">Mã tin & Tiêu đề bài đăng</th>
                                <th class="py-3.5 px-4 min-w-[180px]">Người đăng (Chủ nhà)</th>
                                <th class="py-3.5 px-4 min-w-[140px]">Giá thuê & DT</th>
                                <th class="py-3.5 px-4 text-center min-w-[170px]">Trạng thái Nổi bật</th>
                                <th class="py-3.5 px-4 text-right w-24">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700" id="posts-table-body">
                            @forelse($posts as $post)
                                @php
                                    $pId = (string)($post->_id ?? $post->id);
                                    $isFeat = (bool)($post->is_featured ?? false);
                                    $imgUrl = $post->display_image ?: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=300&q=80';
                                    $userObj = $post->user;
                                    $authorName = $userObj->account_name ?? ($userObj->company_name ?? ($userObj->username ?? 'Chủ nhà'));
                                    $authorPhone = $userObj->phone ?? '---';
                                    $authorRole = $userObj->role ?? ($post->account_role ?? 'Chủ nhà');
                                    $buildingName = $post->building ? ($post->building->name ?? '') : '';
                                    $priceFormatted = number_format((float)($post->price ?? 0), 0, ',', '.') . ' VNĐ';
                                    $period = $post->price_unit === 'ngay' ? '/ ngày' : '/ tháng';
                                @endphp
                                <tr id="post-row-{{ $pId }}" class="hover:bg-slate-50/80 transition-colors {{ $isFeat ? 'bg-amber-50/20' : '' }}">
                                    <!-- Cột 1: Hình ảnh Thumbnail nhỏ -->
                                    <td class="py-3.5 px-4 text-center align-middle">
                                        <div class="relative w-14 h-14 rounded-xl overflow-hidden border border-slate-200/80 bg-slate-100 shadow-xs mx-auto group/thumb">
                                            <img src="{{ $imgUrl }}" loading="lazy" alt="{{ $post->title }}"
                                                class="w-full h-full object-cover group-hover/thumb:scale-110 transition-transform duration-300"
                                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=300&q=80';">
                                            @if($isFeat)
                                                <span class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[9px] shadow-xs" title="Tin VIP">
                                                    <i class="fa-solid fa-star"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Cột 2: Mã tin & Tiêu đề -->
                                    <td class="py-3.5 px-4 align-middle">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200" title="Mã tin MongoDB">
                                                    #{{ substr($pId, -8) }}
                                                </span>
                                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60 flex items-center gap-1" title="Phòng đang còn trống, sẵn sàng cho thuê">
                                                    <i class="fa-solid fa-circle-check text-[9px] text-emerald-500"></i> Còn trống
                                                </span>
                                                @if(!empty($buildingName))
                                                    <span class="text-[11px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200/60 flex items-center gap-1">
                                                        <i class="fa-solid fa-city text-[10px]"></i> {{ $buildingName }}
                                                    </span>
                                                @endif
                                                @if($isFeat && !empty($post->featured_at))
                                                    <span class="text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-bold border border-amber-200/60 flex items-center gap-1" title="Thời gian được gán nổi bật">
                                                        <i class="fa-regular fa-clock"></i> Gán: {{ \Carbon\Carbon::parse($post->featured_at)->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ url('/chitietbaidang/' . $pId) }}" target="_blank"
                                                class="text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors line-clamp-2 block leading-snug"
                                                title="{{ $post->title }}">
                                                {{ $post->title }}
                                            </a>
                                            <p class="text-xs text-slate-500 flex items-center gap-1 line-clamp-1">
                                                <i class="fa-solid fa-location-dot text-rose-500 text-[11px] shrink-0"></i>
                                                <span>{{ $post->address ?: ($post->district . ', ' . $post->province) }}</span>
                                            </p>
                                        </div>
                                    </td>

                                    <!-- Cột 3: Người đăng (Chủ nhà) -->
                                    <td class="py-3.5 px-4 align-middle">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs border border-white">
                                                {{ strtoupper(mb_substr($authorName, 0, 1, 'UTF-8')) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-xs text-slate-900 truncate" title="{{ $authorName }}">
                                                    {{ $authorName }}
                                                </p>
                                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                                    <i class="fa-solid fa-phone text-[10px] text-slate-400"></i> {{ $authorPhone }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Cột 4: Giá thuê & Diện tích -->
                                    <td class="py-3.5 px-4 align-middle">
                                        <div class="space-y-0.5">
                                            <span class="font-extrabold text-emerald-600 text-sm block">
                                                {{ $priceFormatted }}
                                            </span>
                                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                                <i class="fa-solid fa-vector-square text-slate-400 text-[10px]"></i>
                                                {{ $post->area ? $post->area . ' m²' : '---' }}
                                                @if(!empty($post->bedroom))
                                                    • {{ $post->bedroom }} PN
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Cột 5: Trạng thái Nổi bật (Nút gạt Toggle Switch UI đẹp mắt bằng Tailwind) -->
                                    <td class="py-3.5 px-4 align-middle text-center">
                                        <div class="inline-flex flex-col items-center gap-1.5">
                                            <!-- Nút gạt Switch Toggle -->
                                            <button type="button"
                                                id="toggle-btn-{{ $pId }}"
                                                onclick="handleToggleFeatured('{{ $pId }}', event)"
                                                class="toggle-switch-btn {{ $isFeat ? 'active' : 'inactive' }}"
                                                role="switch"
                                                aria-checked="{{ $isFeat ? 'true' : 'false' }}"
                                                title="{{ $isFeat ? 'Bấm để gỡ khỏi nổi bật' : 'Bấm để thêm vào nổi bật Trang chủ' }}">
                                                <span class="toggle-switch-knob">
                                                    <i id="knob-icon-{{ $pId }}" class="fa-solid fa-star text-[9px] {{ $isFeat ? 'text-amber-500' : 'text-slate-300' }}"></i>
                                                </span>
                                            </button>

                                            <!-- Nhãn trạng thái Text Badge -->
                                            <span id="badge-status-{{ $pId }}"
                                                class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $isFeat ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                                {{ $isFeat ? 'VIP Nổi Bật' : 'Tin Thường' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Cột 6: Xem chi tiết -->
                                    <td class="py-3.5 px-4 align-middle text-right">
                                        <a href="{{ url('/chitietbaidang/' . $pId) }}" target="_blank"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition-colors shadow-2xs"
                                            title="Xem tin chi tiết">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 px-4 text-center">
                                        <div class="max-w-sm mx-auto text-center space-y-3">
                                            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                                <i class="fa-solid fa-inbox"></i>
                                            </div>
                                            <h3 class="text-base font-bold text-slate-800">Không tìm thấy bài đăng nào</h3>
                                            <p class="text-xs text-slate-500">
                                                @if(!empty($search))
                                                    Không có bài viết nào khớp với từ khóa "{{ $search }}". Vui lòng thử tìm kiếm khác.
                                                @elseif($tab === 'featured')
                                                    Hiện chưa có bài đăng nào được gán trạng thái Nổi Bật (Tin VIP). Hãy chuyển sang tab "Tất cả bài đăng" và gạt nút để đưa bài lên Trang chủ!
                                                @else
                                                    Hiện chưa có bài đăng nào ở trạng thái đã phê duyệt.
                                                @endif
                                            </p>
                                            @if(!empty($search) || $tab === 'featured')
                                                <a href="{{ url('Admin/bai-dang-noi-bat?tab=all') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                                                    <i class="fa-solid fa-rotate-left"></i> Xem tất cả bài đăng
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang (Pagination) -->
                @if($posts->hasPages())
                    <div class="p-4 border-t border-slate-200/80 bg-slate-50/50 flex items-center justify-between flex-wrap gap-3">
                        <div class="text-xs text-slate-500 font-medium">
                            Hiển thị <span class="font-bold text-slate-800">{{ $posts->firstItem() }}</span> - <span class="font-bold text-slate-800">{{ $posts->lastItem() }}</span> trong tổng số <span class="font-bold text-slate-800">{{ $posts->total() }}</span> bài
                        </div>
                        <div>
                            {{ $posts->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer ghi chú hướng dẫn cho Quản Trị Viên -->
            <div class="p-4 rounded-xl bg-sky-50/70 border border-sky-100 text-xs text-sky-800 flex items-start gap-3">
                <i class="fa-solid fa-shield-halved text-sky-600 mt-0.5 text-sm shrink-0"></i>
                <div class="space-y-1">
                    <p class="font-bold text-sky-900">Quy tắc bảo vệ dữ liệu phòng đã thuê (Anti-Rented Data Protection):</p>
                    <ul class="list-disc list-inside space-y-0.5 text-sky-700">
                        <li><strong>Chỉ hiển thị phòng đang trống (Available):</strong> Hệ thống tự động lọc bỏ 100% các bài đăng đã có khách thuê hoặc có hợp đồng đang hiệu lực để tránh việc Admin gạt nhầm.</li>
                        <li><strong>Tự động ngầm gỡ Nổi Bật (Model Observer):</strong> Bất cứ khi nào bài đăng chuyển sang <code>rented</code> / <code>đã cho thuê</code> hoặc có hợp đồng mới hoàn tất, hệ thống tự động cập nhật <code>is_featured = false</code> và bài đăng lập tức rời khỏi Slider Trang chủ.</li>
                        <li><strong>Chặn thao tác gán nổi bật:</strong> Nếu phòng đã có khách thuê, nút gạt sẽ bị khóa hoặc API từ chối với thông báo cảnh báo rõ ràng.</li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <!-- 5. XỬ LÝ JAVASCRIPT AJAX & TOAST NOTIFICATION -->
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const CURRENT_TAB = '{{ $tab }}';
        const MAX_FEATURED = {{ $maxFeatured }};

        // SweetAlert2 Toast cấu hình chuẩn góc màn hình
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        /**
         * Hàm xử lý khi bấm nút gạt Toggle Switch
         * Gọi POST /admin/posts/{id}/toggle-featured
         */
        async function handleToggleFeatured(postId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const btn = document.getElementById(`toggle-btn-${postId}`);
            const knobIcon = document.getElementById(`knob-icon-${postId}`);
            const badge = document.getElementById(`badge-status-${postId}`);
            const row = document.getElementById(`post-row-${postId}`);

            if (!btn || btn.classList.contains('loading')) return;

            // Khóa nút tạm thời để chống double click
            btn.classList.add('loading');
            const wasActive = btn.classList.contains('active');

            try {
                const response = await fetch(`/admin/posts/${postId}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const isFeatured = Boolean(data.is_featured);

                    // 1. Cập nhật UI nút gạt
                    if (isFeatured) {
                        btn.classList.remove('inactive');
                        btn.classList.add('active');
                        btn.setAttribute('aria-checked', 'true');
                        btn.setAttribute('title', 'Bấm để gỡ khỏi nổi bật');
                        if (knobIcon) {
                            knobIcon.className = 'fa-solid fa-star text-[9px] text-amber-500';
                        }
                        if (badge) {
                            badge.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200';
                            badge.textContent = 'VIP Nổi Bật';
                        }
                        if (row) {
                            row.classList.add('bg-amber-50/20');
                        }
                    } else {
                        btn.classList.remove('active');
                        btn.classList.add('inactive');
                        btn.setAttribute('aria-checked', 'false');
                        btn.setAttribute('title', 'Bấm để thêm vào nổi bật Trang chủ');
                        if (knobIcon) {
                            knobIcon.className = 'fa-solid fa-star text-[9px] text-slate-300';
                        }
                        if (badge) {
                            badge.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200';
                            badge.textContent = 'Tin Thường';
                        }
                        if (row) {
                            row.classList.remove('bg-amber-50/20');
                        }
                    }

                    // 2. Cập nhật các thẻ thống kê tổng quan
                    const newCount = typeof data.featured_count === 'number' ? data.featured_count : (wasActive ? Math.max(0, parseInt(document.getElementById('stat-featured-count')?.innerText || 0) - 1) : parseInt(document.getElementById('stat-featured-count')?.innerText || 0) + 1);
                    updateStatsUI(newCount);

                    // 3. Nếu đang ở Tab "Đang nổi bật" và gỡ bài, ẩn dòng với hiệu ứng mượt mà
                    if (CURRENT_TAB === 'featured' && !isFeatured && row) {
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0.3';
                        row.style.transform = 'scale(0.98)';
                        setTimeout(() => {
                            row.remove();
                            // Kiểm tra nếu danh sách trống thì tải lại trang nhẹ
                            const tbody = document.getElementById('posts-table-body');
                            if (tbody && tbody.children.length === 0) {
                                window.location.reload();
                            }
                        }, 400);
                    }

                    // 4. Thông báo Toast SweetAlert2 ở góc màn hình
                    Toast.fire({
                        icon: 'success',
                        title: isFeatured ? 'Đã thêm vào danh sách nổi bật' : 'Đã gỡ khỏi nổi bật'
                    });

                } else {
                    throw new Error(data.message || 'Không thể cập nhật trạng thái');
                }
            } catch (err) {
                console.error('Lỗi khi toggle featured:', err);
                Toast.fire({
                    icon: 'error',
                    title: err.message || 'Lỗi kết nối máy chủ! Vui lòng thử lại.'
                });
            } finally {
                btn.classList.remove('loading');
            }
        }

        /**
         * Cập nhật các chỉ số thống kê & Progress Bar trên giao diện
         */
        function updateStatsUI(newFeaturedCount) {
            const countEl = document.getElementById('stat-featured-count');
            const fractionEl = document.getElementById('stat-display-fraction');
            const tabBadge = document.getElementById('tab-featured-badge');
            const normalCountEl = document.getElementById('stat-normal-count');
            const progressBar = document.getElementById('stat-progress-bar');
            const progressPct = document.getElementById('stat-progress-pct');

            if (countEl) countEl.innerText = newFeaturedCount;
            if (fractionEl) fractionEl.innerText = `${newFeaturedCount}/${MAX_FEATURED}`;
            if (tabBadge) tabBadge.innerText = newFeaturedCount;

            const pct = MAX_FEATURED > 0 ? Math.min(100, Math.round((newFeaturedCount / MAX_FEATURED) * 100)) : 0;
            if (progressBar) progressBar.style.width = `${pct}%`;
            if (progressPct) progressPct.innerText = `${pct}%`;

            if (normalCountEl) {
                const totalAppr = {{ $totalApprovedCount }};
                const newNormal = Math.max(0, totalAppr - newFeaturedCount);
                normalCountEl.innerText = newNormal.toLocaleString('vi-VN');
            }
        }
    </script>
</body>

</html>
