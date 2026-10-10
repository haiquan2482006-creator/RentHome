<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Cá Nhân - RentHome</title>
    
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; }
        /* Tùy chỉnh input bỏ đi webkit mặc định */
        input:focus { outline: none !important; }
        
        /* Ẩn thanh trượt cho sidebar và các thẻ khác */
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .custom-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden text-slate-800 bg-white">

    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $isEnterprise = $user && $user->account_type === 'doanhnghiep';
        $userName = $user ? ($isEnterprise ? ($user->representative ?? $user->account_name ?? $user->company_name ?? $user->username ?? 'Doanh nghiệp') : ($user->account_name ?? $user->username ?? 'Người dùng')) : 'Người dùng';
    @endphp

    @include('partials.sidebar-user')
    @include('partials.sidebar-user', ['isDrawer' => true])

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-white relative">
        <!-- Top Header (Chiếm toàn bộ chiều ngang tính từ lề phải sidebar) -->
        <header class="h-20 border-b border-slate-100 flex items-center justify-between px-6 lg:px-10 z-10 shrink-0 bg-white/90 backdrop-blur-md">
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Nút toggle sidebar cho màn hình nhỏ -->
                <button type="button" onclick="toggleGlobalSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Hồ sơ cá nhân</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium hidden sm:block">Cập nhật thông tin tài khoản và giấy tờ xác minh</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 sm:gap-3">
                <button type="button" onclick="enableEditMode()" id="btn-edit-mode" class="flex px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold transition-colors shadow-sm items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                </button>
                
                <button type="button" onclick="cancelEditMode()" id="btn-cancel-edit" class="hidden px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold transition-colors shadow-sm items-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Hủy
                </button>
                
                <button type="submit" form="profile-form" id="btn-save-edit" class="hidden px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold transition-colors shadow-md shadow-brand-500/20 items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                </button>

                <div class="w-px h-6 bg-slate-200 mx-1 hidden sm:block"></div>
                <a href="{{ url('/') }}" class="hidden sm:flex px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-xl transition-colors border border-slate-200/60 items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ra trang chủ
                </a>
            </div>
        </header>

        <!-- Profile Form Content: Chiếm trọn chiều ngang, không nằm trong các khung hộp bó hẹp -->
        <div class="flex-1 overflow-y-auto w-full px-6 lg:px-10 py-8 custom-scrollbar">
            <form id="profile-form" action="{{ url('/trangcanhanuser') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-10 pb-28">
                @csrf
                
                @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                @endif

                <!-- KHU VỰC ĐỈNH: AVATAR & THÔNG TIN TỔNG QUAN (Trải dài toàn bộ chiều ngang) -->
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-8 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start lg:items-center gap-6">
                        <!-- Avatar -->
                        <div class="flex flex-col items-center justify-center gap-2 shrink-0">
                            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden relative group bg-slate-100 shadow-sm border-2 border-slate-100">
                                <img id="preview-avatar" src="{{ $user->display_avatar }}" alt="Avatar" class="w-full h-full object-cover">
                                <div id="avatar-upload-overlay" onclick="document.getElementById('avatar-upload').click()" class="absolute inset-0 bg-black/40 hidden items-center justify-center transition-all cursor-pointer backdrop-blur-[2px]">
                                    <i class="fa-solid fa-camera text-white text-2xl"></i>
                                </div>
                                <input type="file" name="avatar" id="avatar-upload" disabled class="absolute inset-0 opacity-0 cursor-pointer w-full h-full disabled:cursor-not-allowed" accept="image/*">
                            </div>
                            <button type="button" id="btn-change-avatar" onclick="document.getElementById('avatar-upload').click()" class="text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors bg-transparent opacity-50 cursor-not-allowed pointer-events-none flex items-center gap-1.5 py-1 px-2 rounded-lg">
                                <i class="fa-solid fa-camera text-xs"></i> Đổi ảnh
                            </button>
                        </div>

                        <!-- Thông tin cá nhân cơ bản -->
                        <div class="flex flex-col items-center sm:items-start text-center sm:text-left">
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $userName }}</h2>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $isEnterprise ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-brand-50 text-brand-700 border-brand-200' }} text-xs font-bold border">
                                    <i class="fa-solid {{ $isEnterprise ? 'fa-building' : 'fa-user' }}"></i> {{ $isEnterprise ? 'Tài khoản Doanh nghiệp' : 'Tài khoản Cá nhân' }}
                                </span>
                            </div>
                            <p class="text-slate-500 mt-1.5 font-medium text-sm flex items-center gap-2">
                                <i class="fa-regular fa-envelope text-slate-400"></i> {{ $user->email ?? 'Chưa cập nhật email' }}
                            </p>
                            @if(!empty($user->phone))
                            <p class="text-slate-500 mt-1 font-medium text-sm flex items-center gap-2">
                                <i class="fa-solid fa-phone text-slate-400"></i> {{ $user->phone }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Huy hiệu trạng thái tài khoản -->
                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-start sm:justify-end">
                        <div class="px-4 py-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Xác minh</p>
                                <p class="text-xs font-bold text-slate-700">
                                    {{ ($user->id_front && $user->id_back) ? 'Đã tải lên giấy tờ' : 'Chưa đủ giấy tờ' }}
                                </p>
                            </div>
                        </div>

                        <div class="px-4 py-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ngân hàng</p>
                                <p class="text-xs font-bold text-slate-700 truncate max-w-[140px]">
                                    {{ !empty($user->bank_name) ? $user->bank_name : 'Chưa cập nhật' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC 1: THÔNG TIN CƠ BẢN (Không nằm trong khung hộp, mở rộng toàn bộ bề ngang) -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Thông tin cơ bản</h3>
                            <p class="text-xs text-slate-500 font-medium">Hồ sơ định danh cá nhân và thông tin liên hệ chính thức</p>
                        </div>
                    </div>
                    
                    @if($isEnterprise)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                        <!-- 1. HÀNG 1: Tên doanh nghiệp & Người đại diện -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-building text-slate-400"></i> Tên doanh nghiệp
                            </label>
                            <input type="text" name="company_name" value="{{ $user->company_name ?? '' }}" disabled placeholder="Nhập tên doanh nghiệp / công ty..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-user-tie text-slate-400"></i> Người đại diện theo pháp luật
                            </label>
                            <input type="text" name="representative" value="{{ $user->representative ?? $user->account_name ?? '' }}" disabled placeholder="Họ và tên người đại diện pháp luật" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <!-- 2. HÀNG 2: Mã số thuế & Ngày cấp GPKD -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice text-slate-400"></i> Mã số thuế
                            </label>
                            <input type="text" name="tax_code" value="{{ $user->tax_code ?? '' }}" disabled placeholder="VD: 0101234567" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-regular fa-calendar-check text-slate-400"></i> Ngày cấp Giấy phép kinh doanh
                            </label>
                            <input type="date" name="business_license_date" value="{{ $user->business_license_date ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <!-- 3. HÀNG 3: Số điện thoại & Email -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-phone text-slate-400"></i> Số điện thoại
                            </label>
                            <input type="text" name="phone" value="{{ $user->phone ?? '' }}" disabled placeholder="Nhập số điện thoại liên hệ..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center justify-between">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-envelope text-slate-400"></i> Email</span>
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1"><i class="fa-solid fa-lock text-[10px]"></i> Cố định</span>
                            </label>
                            <input type="email" value="{{ $user->email ?? '' }}" readonly class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200/80 text-slate-500 cursor-not-allowed font-medium select-none">
                        </div>

                        <!-- 4. HÀNG 4: Số CCCD người đại diện & (Ngày cấp + Nơi cấp) -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-id-card-clip text-slate-400"></i> Số CCCD / CMND (Người đại diện)
                            </label>
                            <input type="text" name="representative_id_card" value="{{ $user->representative_id_card ?? $user->cccd_number ?? '' }}" disabled placeholder="Nhập 12 số CCCD người đại diện..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-slate-400"></i> Ngày cấp
                                </label>
                                <input type="date" name="representative_id_date" value="{{ $user->representative_id_date ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                    <i class="fa-solid fa-landmark text-slate-400"></i> Nơi cấp
                                </label>
                                <input type="text" name="representative_id_place" value="{{ $user->representative_id_place ?? '' }}" disabled placeholder="Cục CS QLHC..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                            </div>
                        </div>

                        <!-- 5. HÀNG 5: Địa chỉ trụ sở chính (Full 2 cột) -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-slate-400"></i> Địa chỉ trụ sở chính
                            </label>
                            <input type="text" name="address" value="{{ $user->address ?? $user->company_address ?? '' }}" disabled placeholder="Nhập địa chỉ trụ sở chính đăng ký kinh doanh..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <!-- 6. HÀNG 6: Link Website / Fanpage & Hotline Zalo -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-globe text-slate-400"></i> Link Website / Fanpage (Tùy chọn)
                            </label>
                            <input type="url" name="website" value="{{ $user->website ?? '' }}" disabled placeholder="https://congty.vn hoặc Fanpage..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-headset text-slate-400"></i> Hotline / Zalo Doanh nghiệp (Tùy chọn)
                            </label>
                            <input type="text" name="hotline" value="{{ $user->hotline ?? '' }}" disabled placeholder="Hotline hỗ trợ CSKH hoặc số Zalo..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <!-- 7. HÀNG 7: Giới thiệu doanh nghiệp (Full 2 cột) -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-slate-400"></i> Giới thiệu doanh nghiệp
                            </label>
                            <textarea name="company_description" rows="4" disabled placeholder="Giới thiệu ngắn về dịch vụ cho thuê của công ty bạn..." class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed resize-none leading-relaxed">{{ $user->company_description ?? '' }}</textarea>
                        </div>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-1">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-user-tag text-slate-400"></i> Tên hiển thị
                            </label>
                            <input type="text" name="username" value="{{ $user->username ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-signature text-slate-400"></i> Họ tên
                            </label>
                            <input type="text" name="account_name" value="{{ $user->account_name ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-phone text-slate-400"></i> Số điện thoại
                            </label>
                            <input type="text" name="phone" value="{{ $user->phone ?? '' }}" disabled class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium text-slate-800 placeholder-slate-400 disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center justify-between">
                                <span class="flex items-center gap-2"><i class="fa-solid fa-envelope text-slate-400"></i> Email</span>
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1"><i class="fa-solid fa-lock text-[10px]"></i> Cố định</span>
                            </label>
                            <input type="email" value="{{ $user->email ?? '' }}" readonly class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200/80 text-slate-500 cursor-not-allowed font-medium select-none">
                        </div>
                    </div>
                    @endif
                </section>

                @php
                    $hasFront = !empty($user->id_front);
                    $hasBack = !empty($user->id_back);
                    $hasLicense = !empty($user->business_license);
                @endphp

                <!-- KHU VỰC 2: GIẤY TỜ XÁC MINH (CCCD 2 mặt theo thiết kế mẫu) -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Giấy tờ xác minh</h3>
                            <p class="text-xs text-slate-500 font-medium">Vui lòng tải lên ảnh chụp căn cước công dân (CCCD/CMND) rõ nét 2 mặt.</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-8 pt-1 w-full">
                        <!-- CCCD Mặt trước -->
                        <label class="doc-label relative flex flex-col items-center justify-between p-3.5 w-full sm:w-[330px] rounded-2xl transition-all cursor-not-allowed group overflow-hidden {{ $hasFront ? 'bg-white border-2 border-slate-200/90 opacity-100 shadow-xs' : 'bg-[#f0fbf5] border-2 border-dashed border-teal-300/80 opacity-90' }}">
                            
                            <!-- Trạng thái ĐÃ TẢI LÊN (dựa như trong ảnh) -->
                            <div id="wrapper-preview-id-front" class="{{ $hasFront ? 'flex' : 'hidden' }} flex-col items-center w-full">
                                <div class="w-full h-44 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100 flex items-center justify-center">
                                    <img id="preview-id-front" src="{{ $hasFront ? $user->id_front_url : '' }}" alt="CCCD mặt trước" class="w-full h-full object-cover">
                                </div>
                                <p class="mt-2.5 mb-1 text-xs sm:text-sm font-semibold text-emerald-600 flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> Đã tải lên (Bấm để đổi)
                                </p>
                            </div>

                            <!-- Trạng thái CHƯA TẢI LÊN (dựa như trong ảnh) -->
                            <div id="placeholder-id-front" class="{{ $hasFront ? 'hidden' : 'flex' }} flex-col items-center justify-center text-center p-6 h-[210px] w-full">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200/80 text-teal-600 flex items-center justify-center text-lg mb-2.5 doc-icon-wrapper transition-transform">
                                    <i class="fa-regular fa-id-card doc-icon transition-colors"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 doc-text transition-colors">CCCD Mặt Trước <span class="text-emerald-500">*</span></span>
                                <span class="text-xs text-slate-400 mt-1 font-medium doc-subtext">Nhấp để tải ảnh mặt trước</span>
                            </div>

                            <input type="file" name="id_front" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-20" accept="image/*">
                        </label>

                        <!-- CCCD Mặt sau -->
                        <label class="doc-label relative flex flex-col items-center justify-between p-3.5 w-full sm:w-[330px] rounded-2xl transition-all cursor-not-allowed group overflow-hidden {{ $hasBack ? 'bg-white border-2 border-slate-200/90 opacity-100 shadow-xs' : 'bg-[#f0fbf5] border-2 border-dashed border-teal-300/80 opacity-90' }}">
                            
                            <!-- Trạng thái ĐÃ TẢI LÊN (dựa như trong ảnh) -->
                            <div id="wrapper-preview-id-back" class="{{ $hasBack ? 'flex' : 'hidden' }} flex-col items-center w-full">
                                <div class="w-full h-44 rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100 flex items-center justify-center">
                                    <img id="preview-id-back" src="{{ $hasBack ? $user->id_back_url : '' }}" alt="CCCD mặt sau" class="w-full h-full object-cover">
                                </div>
                                <p class="mt-2.5 mb-1 text-xs sm:text-sm font-semibold text-emerald-600 flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> Đã tải lên (Bấm để đổi)
                                </p>
                            </div>

                            <!-- Trạng thái CHƯA TẢI LÊN (dựa như trong ảnh) -->
                            <div id="placeholder-id-back" class="{{ $hasBack ? 'hidden' : 'flex' }} flex-col items-center justify-center text-center p-6 h-[210px] w-full">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200/80 text-teal-600 flex items-center justify-center text-lg mb-2.5 doc-icon-wrapper transition-transform">
                                    <i class="fa-regular fa-rectangle-list doc-icon transition-colors"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 doc-text transition-colors">CCCD Mặt Sau <span class="text-emerald-500">*</span></span>
                                <span class="text-xs text-slate-400 mt-1 font-medium doc-subtext">Nhấp để tải ảnh mặt sau</span>
                            </div>

                            <input type="file" name="id_back" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-20" accept="image/*">
                        </label>
                    </div>
                </section>

                @if($isEnterprise)
                <!-- KHU VỰC: GIẤY PHÉP KINH DOANH (Khổ đứng A4 Portrait) -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-file-contract"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Giấy phép kinh doanh</h3>
                            <p class="text-xs text-slate-500 font-medium">Bản chụp hoặc scan Giấy chứng nhận đăng ký kinh doanh (ĐKKD) còn thời hạn hiệu lực</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-center mt-4 w-full">
                        <!-- Khung GPKD Khổ đứng Portrait (Tỷ lệ A4 aspect-[1/1.414]) -->
                        <label class="doc-label relative flex flex-col items-center justify-center w-72 md:w-80 aspect-[1/1.414] rounded-2xl transition-all cursor-not-allowed group overflow-hidden {{ $hasLicense ? 'bg-slate-50 border-2 border-slate-200 opacity-100 shadow-xs' : 'bg-slate-50/50 border-2 border-dashed border-slate-300 opacity-90 hover:border-brand-400 hover:bg-slate-50' }}">
                            
                            <!-- Trạng thái ĐÃ CÓ ẢNH PREVIEW -->
                            <div id="wrapper-preview-business-license" class="{{ $hasLicense ? 'flex' : 'hidden' }} relative w-full h-full items-center justify-center">
                                <!-- Nút nhỏ "Thay đổi ảnh" ở góc trên bên phải -->
                                <div class="absolute top-3 right-3 z-30 px-2.5 py-1 rounded-lg bg-slate-900/75 text-white text-[11px] font-bold flex items-center gap-1.5 shadow-md backdrop-blur-xs transition-transform group-hover:scale-105 pointer-events-none">
                                    <i class="fa-solid fa-camera text-[10px]"></i> Thay đổi ảnh
                                </div>

                                <img id="preview-business-license" src="{{ $hasLicense ? $user->business_license_url : '' }}" alt="Giấy phép ĐKKD" class="w-full h-full object-contain bg-slate-50 rounded-2xl">
                            </div>

                            <!-- Trạng thái CHƯA CÓ ẢNH (sắp xếp dọc theo chiều đứng của khung) -->
                            <div id="placeholder-business-license" class="{{ $hasLicense ? 'hidden' : 'flex' }} flex-col items-center justify-center text-center p-6 h-full w-full">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 text-slate-500 flex items-center justify-center text-2xl mb-4 doc-icon-wrapper transition-transform group-hover:scale-105 group-hover:text-brand-600">
                                    <i class="fa-regular fa-file-lines doc-icon transition-colors"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 doc-text transition-colors">Giấy phép ĐKKD <span class="text-emerald-500">*</span></span>
                                <p class="text-xs text-slate-400 mt-2 font-medium leading-relaxed px-2 doc-subtext">Kéo thả hoặc Click để tải lên bản chụp/scan Giấy phép ĐKKD (định dạng ảnh/PDF tối đa 5MB)</p>
                            </div>

                            <input type="file" name="business_license" disabled class="doc-upload absolute inset-0 w-full h-full opacity-0 cursor-not-allowed disabled:cursor-not-allowed z-40" accept="image/*,application/pdf">
                        </label>
                    </div>
                </section>
                @endif

                <!-- KHU VỰC 3: THÔNG TIN THANH TOÁN (Trải rộng 3 cột toàn màn hình) -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-building-columns"></i>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Thông tin thanh toán</h3>
                            <p class="text-xs text-slate-500 font-medium">Tài khoản ngân hàng dùng để nhận thanh toán tiền thuê hoặc hoàn cọc từ hệ thống</p>
                        </div>
                    </div>
                    
                    <!-- Form Inputs -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-6 pt-1">
                        <!-- Custom Bank Dropdown -->
                        <div class="space-y-2 relative" id="bank-dropdown-container">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-building-columns text-slate-400"></i> Ngân hàng thụ hưởng
                            </label>
                            <input type="hidden" name="bank_name" id="input-bank-name" value="{{ $user->bank_name ?? '' }}">
                            
                            <button type="button" disabled id="btn-select-bank" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-left flex items-center justify-between disabled:opacity-75 disabled:cursor-not-allowed group">
                                <div class="flex items-center gap-3 overflow-hidden min-w-0">
                                    <div id="selected-bank-logo-container" class="w-8 h-8 rounded-lg bg-white shadow-xs border border-slate-100 flex items-center justify-center p-1 hidden shrink-0">
                                        <img id="selected-bank-logo" src="" class="w-full h-full object-contain">
                                    </div>
                                    <span id="selected-bank-text" class="font-bold text-slate-800 truncate text-sm">{{ $user->bank_name ?? 'Chọn ngân hàng...' }}</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 group-hover:text-brand-500 transition-colors shrink-0 ml-2"></i>
                            </button>
                            
                            <!-- Dropdown List -->
                            <div id="bank-dropdown-list" class="hidden absolute bottom-full mb-2 left-0 right-0 bg-white rounded-2xl shadow-2xl border border-slate-100 max-h-72 overflow-y-auto z-50 py-2">
                                <div class="p-4 text-center text-sm text-slate-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu...</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-hashtag text-slate-400"></i> Số tài khoản
                            </label>
                            <input type="text" inputmode="numeric" id="input-bank-account" name="bank_account" value="{{ $user->bank_account ?? '' }}" disabled placeholder="VD: 190300000000" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-slate-800 placeholder-slate-400 font-mono tracking-wider disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                                <i class="fa-solid fa-user text-slate-400"></i> Tên chủ tài khoản
                            </label>
                            <input type="text" id="input-bank-owner" name="bank_owner" value="{{ $user->bank_owner ?? '' }}" disabled placeholder="VD: NGUYEN VAN A" class="editable-input w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-bold text-slate-800 placeholder-slate-400 uppercase disabled:opacity-75 disabled:cursor-not-allowed">
                        </div>
                    </div>
                </section>

            </form>
        </div>
    </main>

    <script>
        function enableEditMode() {
            // Ẩn nút chỉnh sửa, hiện Lưu và Hủy
            document.getElementById('btn-edit-mode').classList.add('hidden');
            document.getElementById('btn-edit-mode').classList.remove('flex');
            
            const btnCancel = document.getElementById('btn-cancel-edit');
            btnCancel.classList.remove('hidden');
            btnCancel.classList.add('flex');
            
            const btnSave = document.getElementById('btn-save-edit');
            btnSave.classList.remove('hidden');
            btnSave.classList.add('flex');

            // Mở khóa các ô input chữ
            const inputs = document.querySelectorAll('.editable-input');
            inputs.forEach(input => {
                input.removeAttribute('disabled');
            });
            
            // Mở khóa avatar
            const avatarUpload = document.getElementById('avatar-upload');
            if (avatarUpload) {
                avatarUpload.removeAttribute('disabled');
                avatarUpload.classList.remove('disabled:cursor-not-allowed');
            }
            
            const avatarOverlay = document.getElementById('avatar-upload-overlay');
            if (avatarOverlay) {
                avatarOverlay.classList.add('group-hover:flex');
            }
            
            const btnAvatar = document.getElementById('btn-change-avatar');
            if (btnAvatar) {
                btnAvatar.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                btnAvatar.classList.add('hover:bg-slate-100');
            }

            // Mở khóa tải giấy tờ
            const docUploads = document.querySelectorAll('.doc-upload');
            docUploads.forEach(input => {
                input.removeAttribute('disabled');
                input.classList.remove('disabled:cursor-not-allowed');
                input.classList.add('cursor-pointer');
            });
            
            const docLabels = document.querySelectorAll('.doc-label');
            docLabels.forEach(label => {
                label.classList.remove('opacity-90', 'cursor-not-allowed');
                label.classList.add('hover:border-teal-400', 'cursor-pointer');
                
                // Add hover styles manually using group-hover classes
                const iconWrapper = label.querySelector('.doc-icon-wrapper');
                if (iconWrapper) iconWrapper.classList.add('group-hover:scale-105');
                
                const icon = label.querySelector('.doc-icon');
                if (icon) icon.classList.add('group-hover:text-teal-600');
                
                const text = label.querySelector('.doc-text');
                if (text) text.classList.add('group-hover:text-emerald-700');
            });
            
            // Focus vào input đầu tiên
            if (inputs.length > 0) {
                inputs[0].focus();
            }
        }

        function cancelEditMode() {
            // Hủy bỏ các thay đổi bằng cách tải lại trang
            window.location.reload();
        }

        // JS preview hình ảnh cơ bản
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                if(this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        if (input.name === 'avatar') {
                            const preview = document.getElementById('preview-avatar');
                            if (preview) preview.src = e.target.result;
                        } else if (input.name === 'id_front') {
                            const preview = document.getElementById('preview-id-front');
                            if (preview) preview.src = e.target.result;

                            const wrapper = document.getElementById('wrapper-preview-id-front');
                            if (wrapper) {
                                wrapper.classList.remove('hidden');
                                wrapper.classList.add('flex');
                            }

                            const placeholder = document.getElementById('placeholder-id-front');
                            if (placeholder) {
                                placeholder.classList.add('hidden');
                                placeholder.classList.remove('flex');
                            }
                            
                            const label = input.closest('label');
                            if (label) {
                                label.classList.remove('bg-[#f0fbf5]', 'border-dashed', 'border-teal-300/80', 'opacity-90');
                                label.classList.add('bg-white', 'border-solid', 'border-slate-200/90', 'opacity-100');
                            }
                        } else if (input.name === 'id_back') {
                            const preview = document.getElementById('preview-id-back');
                            if (preview) preview.src = e.target.result;

                            const wrapper = document.getElementById('wrapper-preview-id-back');
                            if (wrapper) {
                                wrapper.classList.remove('hidden');
                                wrapper.classList.add('flex');
                            }

                            const placeholder = document.getElementById('placeholder-id-back');
                            if (placeholder) {
                                placeholder.classList.add('hidden');
                                placeholder.classList.remove('flex');
                            }
                            
                            const label = input.closest('label');
                            if (label) {
                                label.classList.remove('bg-[#f0fbf5]', 'border-dashed', 'border-teal-300/80', 'opacity-90');
                                label.classList.add('bg-white', 'border-solid', 'border-slate-200/90', 'opacity-100');
                            }
                        } else if (input.name === 'business_license') {
                            const preview = document.getElementById('preview-business-license');
                            if (preview) preview.src = e.target.result;

                            const wrapper = document.getElementById('wrapper-preview-business-license');
                            if (wrapper) {
                                wrapper.classList.remove('hidden');
                                wrapper.classList.add('flex');
                            }

                            const placeholder = document.getElementById('placeholder-business-license');
                            if (placeholder) {
                                placeholder.classList.add('hidden');
                                placeholder.classList.remove('flex');
                            }
                            
                            const label = input.closest('label');
                            if (label) {
                                label.classList.remove('bg-slate-50/50', 'bg-[#f0fbf5]', 'border-dashed', 'border-slate-300', 'border-teal-300/80', 'opacity-90');
                                label.classList.add('bg-slate-50', 'border-solid', 'border-slate-200', 'opacity-100');
                            }
                        }
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // ============================================
        // JS Logic Ngân Hàng - VietQR API
        // ============================================
        const bankDropdownBtn = document.getElementById('btn-select-bank');
        const bankDropdownList = document.getElementById('bank-dropdown-list');
        const inputBankName = document.getElementById('input-bank-name');
        
        const inputBankAccount = document.getElementById('input-bank-account');
        const inputBankOwner = document.getElementById('input-bank-owner');
        
        // 1. Fetch Banks
        let banksData = [];
        fetch('https://api.vietqr.io/v2/banks')
            .then(res => res.json())
            .then(data => {
                if(data.code === '00') {
                    banksData = data.data;
                    renderBanks(banksData);
                    
                    // Khôi phục UI logo nếu đã có bank_name từ server
                    const currentBank = inputBankName.value;
                    if(currentBank) {
                        const bank = banksData.find(b => b.shortName.toLowerCase() === currentBank.toLowerCase());
                        if(bank) {
                            updateSelectedBank(bank);
                        }
                    }
                }
            })
            .catch(err => {
                bankDropdownList.innerHTML = '<div class="p-4 text-center text-sm text-rose-500">Lỗi tải danh sách ngân hàng. Vui lòng thử lại sau.</div>';
            });
            
        // 2. Render Bank List
        function renderBanks(banks) {
            bankDropdownList.innerHTML = '';
            
            // Search Input
            const searchDiv = document.createElement('div');
            searchDiv.className = 'px-3 pb-2 mb-2 border-b border-slate-100 sticky top-0 bg-white z-10 pt-2';
            searchDiv.innerHTML = '<input type="text" id="search-bank" placeholder="Tìm ngân hàng..." class="w-full px-4 py-2 bg-slate-50 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">';
            bankDropdownList.appendChild(searchDiv);
            
            const listContainer = document.createElement('div');
            bankDropdownList.appendChild(listContainer);
            
            const renderList = (data) => {
                listContainer.innerHTML = '';
                if(data.length === 0) {
                    listContainer.innerHTML = '<div class="p-4 text-center text-sm text-slate-500">Không tìm thấy ngân hàng</div>';
                    return;
                }
                
                data.forEach(bank => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 hover:bg-slate-50 transition-colors flex items-center gap-3 border-b border-slate-50 last:border-0';
                    btn.innerHTML = `
                        <div class="w-12 h-8 rounded bg-white shadow-xs border border-slate-100 p-1 flex items-center justify-center shrink-0">
                            <img src="${bank.logo}" class="w-full h-full object-contain">
                        </div>
                        <div class="flex flex-col overflow-hidden w-full">
                            <span class="font-bold text-sm text-slate-800">${bank.shortName}</span>
                            <span class="text-xs text-slate-500 truncate">${bank.name}</span>
                        </div>
                    `;
                    btn.onclick = () => {
                        updateSelectedBank(bank);
                        bankDropdownList.classList.add('hidden');
                    };
                    listContainer.appendChild(btn);
                });
            };
            
            renderList(banks);
            
            // Lắng nghe tìm kiếm
            const searchInputEl = document.getElementById('search-bank');
            if (searchInputEl) {
                searchInputEl.addEventListener('input', function(e) {
                    const keyword = e.target.value.toLowerCase();
                    const filtered = banks.filter(b => b.shortName.toLowerCase().includes(keyword) || b.name.toLowerCase().includes(keyword));
                    renderList(filtered);
                });
            }
        }
        
        // 3. Update Selected Bank
        function updateSelectedBank(bank) {
            // Save value for Form submit
            inputBankName.value = bank.shortName;
            
            // Update Dropdown UI
            document.getElementById('selected-bank-text').textContent = bank.shortName + ' - ' + bank.name;
            document.getElementById('selected-bank-logo').src = bank.logo;
            document.getElementById('selected-bank-logo-container').classList.remove('hidden');
        }
        
        // 4. Toggle Dropdown Menu
        if (bankDropdownBtn) {
            bankDropdownBtn.addEventListener('click', () => {
                bankDropdownList.classList.toggle('hidden');
                if(!bankDropdownList.classList.contains('hidden')) {
                    const searchInput = document.getElementById('search-bank');
                    if(searchInput) setTimeout(() => searchInput.focus(), 100);
                }
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if(bankDropdownBtn && bankDropdownList && !bankDropdownBtn.contains(e.target) && !bankDropdownList.contains(e.target)) {
                bankDropdownList.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
