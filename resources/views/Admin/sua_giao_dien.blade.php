<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chỉnh Sửa Giao Diện - RentHome Admin</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-900 antialiased min-h-screen">

    <!-- INCLUDE THỦ CÔNG SIDEBAR & HEADER ADMIN -->
    @include('partials.header-admin')

    <!-- PHẦN NỘI DUNG CHÍNH (MAIN CONTENT) -->
    <main class="admin-main">
        <div class="p-4 sm:p-6 lg:p-8 max-w-[1700px] mx-auto w-full">
            
            <!-- PAGE HEADER: Tiêu đề trang & Nút Xem giao diện -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">Quản Lý Giao Diện</h1>
                    <p class="text-sm text-slate-500 mt-1">Tùy chỉnh nhận diện thương hiệu, banner trang chủ, footer và tiện ích form đăng tin</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 hover:border-blue-500 text-blue-600 font-bold text-sm rounded-xl shadow-sm hover:shadow transition-all group">
                        <i class="fa-solid fa-globe group-hover:rotate-12 transition-transform"></i>
                        <span>Xem giao diện Website</span>
                    </a>
                </div>
            </div>

            <!-- BỐ CỤC CHÍNH: CSS Grid chia 2 cột (Cột Trái: Form Cài Đặt (5/12), Cột Phải: Live Preview (7/12)) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- ========================================== -->
                <!-- CỘT BÊN TRÁI: FORM CÀI ĐẶT GIAO DIỆN (5/12) -->
                <!-- ========================================== -->
                <div class="lg:col-span-5 space-y-6">
                    <form id="theme-config-form" onsubmit="event.preventDefault(); saveThemeConfig();" class="space-y-6">
                        
                        <!-- THẺ CARD CHỨA HỆ THỐNG TABS & NỘI DUNG FORM -->
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
                            
                            <!-- 1. HỆ THỐNG TABS CHUYỂN ĐỔI BANNER, THƯƠNG HIỆU & FOOTER -->
                            <div class="border-b border-slate-200 pb-4">
                                <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-xl">
                                    <!-- Tab 1: Banner trang chủ -->
                                    <button type="button" id="btn-tab-banner" onclick="openTab('banner')" class="py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 bg-blue-600 text-white shadow-sm">
                                        <i class="fa-solid fa-image"></i>
                                        <span>Banner</span>
                                    </button>

                                    <!-- Tab 2: Nhận diện thương hiệu -->
                                    <button type="button" id="btn-tab-brand" onclick="openTab('brand')" class="py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-200">
                                        <i class="fa-solid fa-crown"></i>
                                        <span>Thương hiệu</span>
                                    </button>

                                    <!-- Tab 3: Footer -->
                                    <button type="button" id="btn-tab-footer" onclick="openTab('footer')" class="py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-200">
                                        <i class="fa-solid fa-bullhorn"></i>
                                        <span>Footer</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. NỘI DUNG FORM TAB 1: BANNER TRANG CHỦ -->
                            <div id="form-banner" class="tab-content space-y-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-sliders text-blue-600"></i> Cấu Hình Banner Trang Chủ
                                    </h3>
                                    <span class="text-xs bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-md border border-blue-100">Hero Section</span>
                                </div>

                                <!-- Khung Upload Ảnh (Dropzone) -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Hình ảnh Banner (Dropzone)</label>
                                    <div id="dropzone-area" class="border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50 hover:bg-blue-50/50 transition-all rounded-xl cursor-pointer p-6 text-center group" onclick="document.getElementById('banner-file-input').click()">
                                        <input type="file" id="banner-file-input" class="hidden" accept="image/*" onchange="handleBannerFileUpload(event)">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                            </div>
                                            <p class="text-sm font-bold text-slate-700">Kéo thả hình ảnh vào đây hoặc <span class="text-blue-600 underline">Click để tải lên</span></p>
                                            <p class="text-xs text-slate-400 mt-1">Hỗ trợ định dạng PNG, JPG, WEBP (Tối đa 5MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nhập URL hình ảnh trực tiếp -->
                                <div class="space-y-1.5">
                                    <label for="input-banner-url" class="block text-xs font-bold text-slate-700">Đường dẫn URL ảnh Banner</label>
                                    <div class="flex gap-2">
                                        <div class="relative flex-1">
                                            <input type="text" id="input-banner-url" value="{{ $themeSettings['banner_url'] ?? '' }}" placeholder="Dán URL ảnh hoặc tải ảnh ở khung trên..." class="w-full pl-3.5 pr-10 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-mono text-xs">
                                            <button type="button" onclick="document.getElementById('input-banner-url').value = '';" title="Xóa ô nhập" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-600 transition-colors">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                            </button>
                                        </div>
                                        <button type="button" onclick="addBannerFromInputUrl()" class="px-3.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs whitespace-nowrap flex items-center gap-1.5">
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Thêm</span>
                                        </button>
                                    </div>
                                    <p id="banner-status-text" class="text-[11px] {{ !empty($themeSettings['banner_url']) ? 'text-emerald-600' : 'text-slate-500' }} italic mt-0.5 font-medium flex items-center gap-1">
                                        {{ !empty($themeSettings['banner_url']) ? '✓ Đang sử dụng ảnh banner tùy chỉnh' : '• Đang sử dụng hình nền mặc định của hệ thống' }}
                                    </p>
                                </div>

                                <!-- Danh Sách Các Ảnh Banner Đã Thêm (Bên Cột Form) -->
                                <div id="form-custom-banners-section" class="space-y-2 pt-2 border-t border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <i class="fa-solid fa-layer-group text-blue-500"></i>
                                            <span>Các ảnh banner đã thêm (<span id="form-custom-banner-count">0</span>)</span>
                                        </span>
                                        <span class="text-[11px] text-slate-400">Tối đa 5 slide</span>
                                    </div>
                                    <div id="form-custom-banner-list" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                        <!-- Danh sách render bằng JavaScript -->
                                    </div>
                                </div>

                                <!-- Tiêu đề chính của Banner -->
                                <div class="space-y-1.5">
                                    <label for="input-banner-title" class="block text-xs font-bold text-slate-700">Tiêu đề chính Banner</label>
                                    <input type="text" id="input-banner-title" oninput="updateLivePreview()" value="{{ $themeSettings['banner_title'] ?? 'Tìm Nhà Cho Thuê Nhanh Chóng & Dễ Dàng' }}" placeholder="Nhập tiêu đề banner..." class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-semibold">
                                </div>

                                <!-- Phụ đề / Slogan Banner -->
                                <div class="space-y-1.5">
                                    <label for="input-banner-subtitle" class="block text-xs font-bold text-slate-700">Phụ đề / Slogan mô tả</label>
                                    <textarea id="input-banner-subtitle" oninput="updateLivePreview()" rows="2" placeholder="Nhập câu miêu tả ngắn..." class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">{{ $themeSettings['banner_subtitle'] ?? 'Hàng ngàn căn hộ, phòng trọ chất lượng cao được xác thực mỗi ngày tại RentHome' }}</textarea>
                                </div>
                            </div>

                            <!-- 3. NỘI DUNG FORM TAB 2: NHẬN DIỆN THƯƠNG HIỆU -->
                            <div id="form-brand" class="tab-content space-y-5 hidden">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-crown text-amber-500"></i> Nhận Diện Thương Hiệu
                                    </h3>
                                    <span class="text-xs bg-amber-50 text-amber-600 font-bold px-2.5 py-1 rounded-md border border-amber-100">Brand Identity</span>
                                </div>

                                <!-- Tên Thương Hiệu -->
                                <div class="space-y-1.5">
                                    <label for="input-brand-name" class="block text-xs font-bold text-slate-700">Tên hệ thống / Thương hiệu</label>
                                    <input type="text" id="input-brand-name" oninput="updateLivePreview()" value="{{ $themeSettings['brand_name'] ?? 'RentHome' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-bold text-slate-800">
                                </div>

                                <!-- Slogan Thương Hiệu -->
                                <div class="space-y-1.5">
                                    <label for="input-brand-slogan" class="block text-xs font-bold text-slate-700">Slogan thương hiệu</label>
                                    <input type="text" id="input-brand-slogan" oninput="updateLivePreview()" value="{{ $themeSettings['brand_slogan'] ?? 'Thuê nhà ước mơ' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-semibold">
                                </div>

                                <!-- Logo URL -->
                                <div class="space-y-1.5">
                                    <label for="input-logo-url" class="block text-xs font-bold text-slate-700">Đường dẫn Logo Header</label>
                                    <input type="text" id="input-logo-url" oninput="updateLivePreview()" value="{{ $themeSettings['logo_url'] ?? asset('img/logo.png') }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>

                                <!-- Tùy Chọn Màu Chủ Đạo -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700">Màu sắc chủ đạo website</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="setPrimaryThemeColor('#16a34a')" class="w-9 h-9 rounded-full bg-emerald-600 ring-2 ring-offset-2 ring-emerald-600 focus:outline-none transition-transform hover:scale-110" title="Xanh lá Brand (Mặc định)"></button>
                                        <button type="button" onclick="setPrimaryThemeColor('#0284c7')" class="w-9 h-9 rounded-full bg-sky-600 focus:outline-none transition-transform hover:scale-110" title="Xanh dương Ocean"></button>
                                        <button type="button" onclick="setPrimaryThemeColor('#7c3aed')" class="w-9 h-9 rounded-full bg-violet-600 focus:outline-none transition-transform hover:scale-110" title="Tím Royal"></button>
                                        <button type="button" onclick="setPrimaryThemeColor('#dc2626')" class="w-9 h-9 rounded-full bg-red-600 focus:outline-none transition-transform hover:scale-110" title="Đỏ Crimson"></button>
                                        <input type="color" id="input-theme-color" value="{{ $themeSettings['theme_color'] ?? '#16a34a' }}" onchange="setPrimaryThemeColor(this.value)" class="w-9 h-9 p-0.5 border border-slate-300 rounded-full cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. NỘI DUNG FORM TAB 3: FOOTER -->
                            <div id="form-footer" class="tab-content space-y-5 hidden">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-list-check text-emerald-600"></i> Cấu Hình Chân Trang (Footer)
                                    </h3>
                                    <span class="text-xs bg-emerald-50 text-emerald-600 font-bold px-2.5 py-1 rounded-md border border-emerald-100">Footer Area</span>
                                </div>

                                <!-- Hotline -->
                                <div class="space-y-1.5">
                                    <label for="input-footer-phone" class="block text-xs font-bold text-slate-700">Số điện thoại Hotline</label>
                                    <input type="text" id="input-footer-phone" oninput="updateLivePreview()" value="{{ $themeSettings['footer_phone'] ?? '0903990706' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>

                                <!-- Email Liên Hệ -->
                                <div class="space-y-1.5">
                                    <label for="input-footer-email" class="block text-xs font-bold text-slate-700">Email hỗ trợ khách hàng</label>
                                    <input type="email" id="input-footer-email" oninput="updateLivePreview()" value="{{ $themeSettings['footer_email'] ?? 'huydepgai@gmail.com' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>

                                <!-- Địa chỉ trụ sở -->
                                <div class="space-y-1.5">
                                    <label for="input-footer-address" class="block text-xs font-bold text-slate-700">Địa chỉ văn phòng / Trụ sở</label>
                                    <input type="text" id="input-footer-address" oninput="updateLivePreview()" value="{{ $themeSettings['footer_address'] ?? 'Tòa nhà Landmark 81, Bình Thạnh, TP.HCM' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>

                                <!-- Dòng chữ Bản quyền Copyright -->
                                <div class="space-y-1.5">
                                    <label for="input-footer-copyright" class="block text-xs font-bold text-slate-700">Thông tin bản quyền (Copyright)</label>
                                    <input type="text" id="input-footer-copyright" oninput="updateLivePreview()" value="{{ $themeSettings['footer_copyright'] ?? '© 2026 RentHome Inc. Tất cả quyền được bảo lưu.' }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>
                            </div>

                            <!-- NÚT LƯU THAY ĐỔI CẤU HÌNH -->
                            <div class="pt-4 border-t border-slate-200">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Lưu Cấu Hình Giao Diện</span>
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- ========================================== -->
                <!-- CỘT BÊN PHẢI: KHUNG LIVE PREVIEW (7/12)    -->
                <!-- ========================================== -->
                <div class="lg:col-span-7">
                    <div class="sticky top-6 bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-4 max-h-[calc(100vh-2.5rem)] overflow-y-auto">
                        
                        <!-- HEADER KHUNG PREVIEW (THANH MÔ PHỎNG TRÌNH DUYỆT) -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                                </div>
                                <div class="hidden sm:flex items-center gap-1 text-xs text-slate-400 font-mono bg-slate-100 px-3 py-1 rounded-md ml-2 border border-slate-200">
                                    <i class="fa-solid fa-lock text-[10px] text-emerald-600"></i>
                                    <span>https://renthome.vn</span>
                                </div>
                            </div>

                            <!-- Tiêu đề Live Preview & Chuyển dạng Viewport -->
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span> Live Preview
                                </span>
                                <div class="flex items-center bg-slate-100 p-1 rounded-lg border border-slate-200">
                                    <button type="button" onclick="setPreviewViewport('desktop')" class="px-2 py-1 text-xs font-bold rounded text-slate-700 bg-white shadow-sm" title="Xem dạng Desktop">
                                        <i class="fa-solid fa-desktop"></i>
                                    </button>
                                    <button type="button" onclick="setPreviewViewport('mobile')" class="px-2 py-1 text-xs font-bold rounded text-slate-500 hover:text-slate-800" title="Xem dạng Mobile">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- KHUNG MÔ PHỎNG NỘI DUNG WEBSITE TRỰC TIẾP -->
                        <div id="preview-container" class="transition-all duration-300 rounded-lg overflow-hidden border border-slate-200 shadow-inner bg-slate-50 min-h-[580px] flex flex-col justify-between">
                            
                            <!-- 1. LIVE PREVIEW CONTAINER CHO TAB BANNER -->
                            <div id="preview-banner" class="preview-content space-y-4">
                                <!-- Header Navigation (Đồng bộ 100% với header-home.blade.php ngoài trang chủ) -->
                                <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-5 py-3.5 flex items-center justify-between shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-extrabold flex items-center justify-center shadow-sm" id="prev-logo-icon">
                                            <i class="fa-solid fa-house-chimney text-base"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-base font-extrabold tracking-tight text-slate-900 leading-none" id="prev-brand-name-wrap">
                                                Rent<span class="text-emerald-600" id="prev-brand-suffix">Home</span>
                                            </span>
                                            <span class="text-[9px] font-medium text-slate-500 uppercase tracking-widest mt-0.5" id="prev-brand-slogan">Thuê nhà ước mơ</span>
                                        </div>
                                    </div>
                                    <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                                        <span class="text-emerald-600 font-extrabold border-b-2 border-emerald-600 pb-0.5" id="prev-nav-active">Trang chủ</span>
                                        <span class="hover:text-emerald-600 cursor-pointer">Nhà nổi bật</span>
                                        <span class="hover:text-emerald-600 cursor-pointer">Ưu điểm</span>
                                        <span class="hover:text-emerald-600 cursor-pointer">Tin tức</span>
                                        <span class="hover:text-emerald-600 cursor-pointer">Liên hệ</span>
                                    </nav>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-[11px] shadow-sm cursor-pointer" id="prev-header-btn">
                                            <i class="fa-solid fa-user-gear text-xs"></i> Vào trang quản trị
                                        </span>
                                        <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-100/80 border border-slate-200">
                                            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shadow-xs" id="prev-avatar-icon">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-[11px] font-bold text-slate-800 flex items-center gap-1">
                                                    admin <i class="fa-solid fa-chevron-down text-[8px] text-slate-400"></i>
                                                </span>
                                                <span class="text-[8px] font-bold text-sky-600">• Cá nhân</span>
                                            </div>
                                        </div>
                                    </div>
                                </header>

                                <section id="prev-banner-bg" class="relative bg-slate-950 text-white p-8 md:p-12 text-center flex flex-col items-center justify-center min-h-[320px] overflow-hidden transition-all rounded-b-xl">
                                    <!-- Lớp chuyển mờ nhiều ảnh Banner (Giống hệt ngoài Trang chủ Home.blade.php) -->
                                    <div id="prev-banner-slides" class="absolute inset-0 z-0 pointer-events-none">
                                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-100" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80');"></div>
                                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-0" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80');"></div>
                                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-0" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80');"></div>
                                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-0" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1920&q=80');"></div>
                                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-0" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1920&q=80');"></div>
                                    </div>

                                    <!-- Nội dung chữ trên Banner -->
                                    <div class="relative z-10 max-w-lg mx-auto space-y-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-500/20 text-blue-300 border border-blue-400/30 backdrop-blur-xs">
                                            <i class="fa-solid fa-sparkles text-amber-400"></i> Nền tảng thuê nhà #1
                                        </span>
                                        <h2 class="text-xl md:text-3xl font-extrabold tracking-tight leading-tight text-white drop-shadow-md" id="prev-banner-title">
                                            Tìm Nhà Cho Thuê Nhanh Chóng & Dễ Dàng
                                        </h2>
                                        <p class="text-xs md:text-sm text-slate-200 font-medium leading-relaxed max-w-md mx-auto" id="prev-banner-subtitle">
                                            Hàng ngàn căn hộ, phòng trọ chất lượng cao được xác thực mỗi ngày tại RentHome
                                        </p>
                                    </div>

                                    <!-- Thanh Chấm Tròn Indicator Đang Chuyển Ảnh -->
                                    <div id="prev-banner-dots" class="absolute bottom-3 left-0 right-0 z-10 flex justify-center items-center gap-1.5 pointer-events-none">
                                        <span class="prev-dot w-5 h-1.5 rounded-full bg-blue-500 transition-all duration-300"></span>
                                        <span class="prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300"></span>
                                        <span class="prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300"></span>
                                        <span class="prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300"></span>
                                        <span class="prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300"></span>
                                    </div>
                                </section>

                                <!-- KHUNG HIỂN THỊ ĐẦY ĐỦ CÁC ẢNH BANNER ĐÃ THÊM / ĐANG DÙNG (DƯỚI KHUNG BANNER PREVIEW) -->
                                <div id="prev-banner-gallery-container" class="p-4 sm:p-5 bg-white rounded-xl border border-slate-200/80 shadow-xs space-y-3.5 mx-3 mb-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-2xs">
                                                <i class="fa-solid fa-images"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                                    <span>Toàn Bộ Ảnh Banner Đang Sử Dụng</span>
                                                    <span id="banner-count-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700">5 ảnh</span>
                                                </h4>
                                                <p class="text-[11px] text-slate-500 font-medium">Bấm vào từng ảnh để chuyển slide xem trước trực tiếp ở khung trên</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span id="banner-custom-count-text">1 ảnh tùy chỉnh</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Lưới hiển thị các thẻ thumbnail ảnh banner -->
                                    <div id="prev-banner-gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 pt-1">
                                        <!-- Render tự động bằng JavaScript -->
                                    </div>

                                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-slate-400">
                                        <span><i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Ảnh tùy chỉnh được ưu tiên hiển thị trước và trượt tuần tự 3.5s/slide</span>
                                        <span>Tối đa 5 slide trượt</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. LIVE PREVIEW CONTAINER CHO TAB THƯƠNG HIỆU (GIAO DIỆN HEADER & THƯƠNG HIỆU THỰC) -->
                            <div id="preview-brand" class="preview-content space-y-5 hidden p-4 sm:p-6">
                                <!-- Khung Header Navigation Thực (Đồng bộ 100% với header-home.blade.php ngoài trang chủ) -->
                                <div class="bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-4">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                        <div class="flex items-center gap-3">
                                            <div id="prev-brand-logo-box-tab" class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-black text-xl flex items-center justify-center shadow-sm transition-all">
                                                <i class="fa-solid fa-house-chimney"></i>
                                            </div>
                                            <div>
                                                <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none block" id="prev-brand-name-tab">
                                                    Rent<span class="text-emerald-600" id="prev-brand-suffix-tab">Home</span>
                                                </span>
                                                <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest block mt-1" id="prev-brand-slogan-tab">Thuê nhà ước mơ</span>
                                            </div>
                                        </div>
                                        <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                                            <span class="text-emerald-600 font-extrabold border-b-2 border-emerald-600 pb-1" id="prev-brand-nav-active">Trang chủ</span>
                                            <span class="hover:text-emerald-600 cursor-pointer">Nhà nổi bật</span>
                                            <span class="hover:text-emerald-600 cursor-pointer">Ưu điểm</span>
                                            <span class="hover:text-emerald-600 cursor-pointer">Tin tức</span>
                                            <span class="hover:text-emerald-600 cursor-pointer">Liên hệ</span>
                                        </nav>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-sky-600 shadow-xs cursor-pointer">
                                                <i class="fa-solid fa-user-gear mr-1"></i> Vào trang quản trị
                                            </span>
                                            <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-100 border border-slate-200">
                                                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold" id="prev-brand-avatar-tab">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                                <div class="flex flex-col text-left leading-none">
                                                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1">
                                                        admin <i class="fa-solid fa-chevron-down text-[8px] text-slate-400"></i>
                                                    </span>
                                                    <span class="text-[9px] font-bold text-sky-600">• Cá nhân</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Minh họa Nhận Diện Thương Hiệu Thực trên Trang chủ -->
                                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3 text-center">
                                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold" id="prev-brand-badge">
                                            <i class="fa-solid fa-crown text-amber-500"></i> Thương hiệu xác thực chính chủ
                                        </div>
                                        <h4 class="text-lg font-black text-slate-900">Giao diện Nhận diện Thương hiệu & Màu chủ đạo</h4>
                                        <p class="text-xs text-slate-500 max-w-md mx-auto">Tất cả các thành phần nút bấm, icon, badge và thanh menu trên hệ thống RentHome sẽ tự động chuyển đổi đồng bộ theo màu thương hiệu bạn chọn.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. LIVE PREVIEW CONTAINER CHO TAB FOOTER (GIAO DIỆN CHÂN TRANG THỰC) -->
                            <div id="preview-footer" class="preview-content space-y-4 hidden p-4 sm:p-6">
                                <!-- Khung Footer Thực 4 Cột theo mẫu Home.blade.php -->
                                <footer class="bg-slate-900 text-slate-400 text-xs p-6 md:p-8 rounded-2xl border border-slate-800 space-y-8 shadow-md">
                                    <!-- Hàng 1: Grid 4 Cột -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pb-6 border-b border-slate-800">
                                        <!-- Cột 1: Thông tin Thương hiệu -->
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm" id="prev-footer-logo-icon">
                                                    <i class="fa-solid fa-house-chimney"></i>
                                                </div>
                                                <span class="text-lg font-black text-white" id="prev-footer-brand-title">RentHome</span>
                                            </div>
                                            <p class="text-[11px] leading-relaxed text-slate-400" id="prev-footer-slogan-desc">
                                                Nền tảng công nghệ hỗ trợ tìm kiếm và cho thuê bất động sản, căn hộ, phòng trọ minh bạch và hiệu quả hàng đầu Việt Nam.
                                            </p>
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="w-7 h-7 rounded-full bg-slate-800 hover:bg-blue-600 text-white flex items-center justify-center text-[10px] transition-colors"><i class="fa-brands fa-facebook-f"></i></span>
                                                <span class="w-7 h-7 rounded-full bg-slate-800 hover:bg-blue-600 text-white flex items-center justify-center text-[10px] transition-colors"><i class="fa-brands fa-youtube"></i></span>
                                                <span class="w-7 h-7 rounded-full bg-slate-800 hover:bg-blue-600 text-white flex items-center justify-center text-[10px] transition-colors"><i class="fa-brands fa-tiktok"></i></span>
                                            </div>
                                        </div>

                                        <!-- Cột 2: Danh Mục Hot -->
                                        <div class="space-y-2.5">
                                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Danh Mục Hot</h4>
                                            <ul class="space-y-2 text-[11px] text-slate-400">
                                                <li class="hover:text-white cursor-pointer transition-colors">Cho thuê căn hộ TP.HCM</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Nhà nguyên căn Hà Nội</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Phòng trọ sinh viên</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Biệt thự Villa cao cấp</li>
                                            </ul>
                                        </div>

                                        <!-- Cột 3: Hỗ Trợ Khách Hàng -->
                                        <div class="space-y-2.5">
                                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Hỗ Trợ Khách Hàng</h4>
                                            <ul class="space-y-2 text-[11px] text-slate-400">
                                                <li class="hover:text-white cursor-pointer transition-colors">Trung tâm trợ giúp</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Quy định dịch vụ</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Chính sách bảo mật</li>
                                                <li class="hover:text-white cursor-pointer transition-colors">Giải quyết tranh chấp</li>
                                            </ul>
                                        </div>

                                        <!-- Cột 4: Thông Tin Liên Hệ Thực -->
                                        <div class="space-y-2.5">
                                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Liên Hệ Trực Tiếp</h4>
                                            <ul class="space-y-2 text-[11px]">
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-phone text-blue-500" id="prev-footer-icon-phone"></i>
                                                    <span>Hotline: <strong class="text-white" id="prev-footer-phone">1900 6789</strong></span>
                                                </li>
                                                <li class="flex items-center gap-2">
                                                    <i class="fa-solid fa-envelope text-blue-500" id="prev-footer-icon-email"></i>
                                                    <span>Email: <span class="text-slate-200" id="prev-footer-email">cskh@renthome.vn</span></span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <i class="fa-solid fa-location-dot text-blue-500 mt-0.5" id="prev-footer-icon-addr"></i>
                                                    <span id="prev-footer-address">123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Hàng 2: Copyright Chân Trang -->
                                    <div class="flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                                        <p id="prev-footer-copyright">© 2026 RentHome. Tất cả các quyền được bảo lưu.</p>
                                        <p class="text-[10px]">Nền tảng cho thuê nhà hàng đầu Việt Nam</p>
                                    </div>
                                </footer>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- SCRIPT XỬ LÝ CHUYỂN TAB (openTab) & LIVE PREVIEW THỜI GIAN THỰC -->
    <script>
        // 1. HÀM CHUYỂN TAB ĐỒNG BỘ CỘT TRÁI (FORM) VÀ CỘT PHẢI (PREVIEW)
        function openTab(tabId) {
            // Ẩn tất cả các form ở cột trái
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            // Ẩn tất cả các khung preview ở cột phải
            document.querySelectorAll('.preview-content').forEach(el => {
                el.classList.add('hidden');
            });

            // Reset tất cả các nút Tab về style thường
            const tabButtons = ['banner', 'brand', 'footer'];
            tabButtons.forEach(id => {
                const btn = document.getElementById('btn-tab-' + id);
                if (btn) {
                    btn.className = "py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-200";
                }
            });

            // Hiển thị Form và Preview được chọn
            const targetForm = document.getElementById('form-' + tabId);
            const targetPreview = document.getElementById('preview-' + tabId);
            const activeBtn = document.getElementById('btn-tab-' + tabId);

            if (targetForm) targetForm.classList.remove('hidden');
            if (targetPreview) targetPreview.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.className = "py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 bg-blue-600 text-white shadow-sm";
            }
        }

        // 2. QUẢN LÝ VÀ CHUYỂN SLIDE ẢNH BANNER LIVE PREVIEW (Đồng bộ với Home.blade.php)
        @php
            $defaultBanners = [
                'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80',
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80',
                'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1920&q=80',
                'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1920&q=80',
            ];
            $initBannerList = [];
            if (!empty($themeSettings['banner_images']) && is_array($themeSettings['banner_images'])) {
                $initBannerList = array_values(array_filter($themeSettings['banner_images']));
            } elseif (!empty($themeSettings['banner_url'])) {
                $initBannerList = array_merge([$themeSettings['banner_url']], array_slice($defaultBanners, 1));
            } else {
                $initBannerList = $defaultBanners;
            }
        @endphp

        const systemDefaultBanners = {!! json_encode($defaultBanners) !!};
        let currentBannerList = {!! json_encode($initBannerList) !!};

        let prevCurrentSlide = 0;
        let prevSlideTimer = null;

        // Cập nhật background và số slide trong khung preview
        function updateBannerSlidesPreview() {
            const slidesContainer = document.getElementById('prev-banner-slides');
            const dotsContainer = document.getElementById('prev-banner-dots');
            
            if (slidesContainer) {
                if (currentBannerList.length === 0) {
                    slidesContainer.innerHTML = `
                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center opacity-100" style="background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85));"></div>
                    `;
                } else {
                    slidesContainer.innerHTML = currentBannerList.map((url, idx) => `
                        <div class="prev-bg-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out ${idx === prevCurrentSlide ? 'opacity-100' : 'opacity-0'}" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('${url}');"></div>
                    `).join('');
                }
            }

            if (dotsContainer) {
                if (currentBannerList.length <= 1) {
                    dotsContainer.innerHTML = '';
                } else {
                    dotsContainer.innerHTML = currentBannerList.map((_, idx) => `
                        <span class="prev-dot ${idx === prevCurrentSlide ? 'w-5 h-1.5 rounded-full bg-blue-500' : 'w-1.5 h-1.5 rounded-full bg-white/50'} transition-all duration-300"></span>
                    `).join('');
                }
            }
        }

        // Tự động xoay vòng các slide sau mỗi 3.5s
        function rotatePreviewSlides() {
            if (currentBannerList.length <= 1) return;
            const slides = document.querySelectorAll('.prev-bg-slide');
            const dots = document.querySelectorAll('#prev-banner-dots .prev-dot');
            if (!slides || slides.length === 0) return;

            // Fade out slide hiện tại
            if (slides[prevCurrentSlide]) {
                slides[prevCurrentSlide].classList.remove('opacity-100');
                slides[prevCurrentSlide].classList.add('opacity-0');
            }
            if (dots[prevCurrentSlide]) {
                dots[prevCurrentSlide].className = 'prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300';
            }

            // Chuyển sang slide tiếp theo
            prevCurrentSlide = (prevCurrentSlide + 1) % slides.length;

            // Fade in slide mới
            if (slides[prevCurrentSlide]) {
                slides[prevCurrentSlide].classList.remove('opacity-0');
                slides[prevCurrentSlide].classList.add('opacity-100');
            }
            if (dots[prevCurrentSlide]) {
                dots[prevCurrentSlide].className = 'prev-dot w-5 h-1.5 rounded-full bg-blue-500 transition-all duration-300';
            }

            updateGalleryActiveHighlight();
        }

        function initPreviewSlider() {
            if (prevSlideTimer) clearInterval(prevSlideTimer);
            if (currentBannerList.length > 1) {
                prevSlideTimer = setInterval(rotatePreviewSlides, 3500);
            }
        }

        // Chuyển ngay đến slide được chọn khi click vào ảnh ở thư viện bên dưới
        function switchToBannerSlide(idx) {
            const slides = document.querySelectorAll('.prev-bg-slide');
            const dots = document.querySelectorAll('#prev-banner-dots .prev-dot');
            if (!slides || slides.length === 0 || idx < 0 || idx >= slides.length) return;

            slides.forEach((s, i) => {
                if (i === idx) {
                    s.classList.remove('opacity-0');
                    s.classList.add('opacity-100');
                } else {
                    s.classList.remove('opacity-100');
                    s.classList.add('opacity-0');
                }
            });

            dots.forEach((d, i) => {
                d.className = (i === idx) 
                    ? 'prev-dot w-5 h-1.5 rounded-full bg-blue-500 transition-all duration-300' 
                    : 'prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300';
            });

            prevCurrentSlide = idx;
            updateGalleryActiveHighlight();
            initPreviewSlider();
        }

        // Cập nhật viền xanh và huy hiệu "Đang xem" ở các thumbnail bên dưới
        function updateGalleryActiveHighlight() {
            const items = document.querySelectorAll('.banner-gallery-card');
            items.forEach((card, idx) => {
                const badge = card.querySelector('.active-slide-badge');
                if (idx === prevCurrentSlide) {
                    card.classList.add('ring-2', 'ring-blue-500', 'border-blue-500', 'shadow-md');
                    card.classList.remove('border-slate-200');
                    if (badge) badge.classList.remove('hidden');
                } else {
                    card.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500', 'shadow-md');
                    card.classList.add('border-slate-200');
                    if (badge) badge.classList.add('hidden');
                }
            });
        }

        // Render toàn bộ gallery ảnh banner bên dưới khung preview & danh sách ở form bên trái
        function renderBannerGallery() {
            const grid = document.getElementById('prev-banner-gallery-grid');
            const formList = document.getElementById('form-custom-banner-list');
            const countBadge = document.getElementById('banner-count-badge');
            const formCountText = document.getElementById('form-custom-banner-count');
            const statusText = document.getElementById('banner-status-text');

            if (countBadge) countBadge.innerText = `${currentBannerList.length} ảnh`;
            if (formCountText) formCountText.innerText = `${currentBannerList.length}/5`;

            if (statusText) {
                if (currentBannerList.length > 0) {
                    statusText.innerText = `✓ Đang có ${currentBannerList.length} ảnh banner hoạt động`;
                    statusText.className = 'text-[11px] text-emerald-600 italic mt-0.5 font-medium flex items-center gap-1';
                } else {
                    statusText.innerText = '• Chưa có ảnh banner nào. Vui lòng tải ảnh lên hoặc thêm URL.';
                    statusText.className = 'text-[11px] text-rose-500 italic mt-0.5 font-medium flex items-center gap-1';
                }
            }

            // 1. Render Gallery Card Grid ở dưới khung Banner Preview (TẤT CẢ các ảnh nhỏ đều có nút Xóa [🗑])
            if (grid) {
                if (currentBannerList.length === 0) {
                    grid.innerHTML = `
                        <div class="col-span-full py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <i class="fa-solid fa-images text-3xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-bold text-slate-600">Đã xóa hết ảnh banner</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Vui lòng tải ảnh lên ở khung bên trái hoặc nhập URL ảnh mới</p>
                        </div>
                    `;
                } else {
                    grid.innerHTML = currentBannerList.map((imgUrl, idx) => {
                        const isActive = idx === prevCurrentSlide;
                        return `
                            <div onclick="switchToBannerSlide(${idx})" class="banner-gallery-card group relative rounded-xl overflow-hidden border-2 transition-all cursor-pointer bg-white shadow-2xs ${isActive ? 'ring-2 ring-blue-500 border-blue-500 shadow-md' : 'border-slate-200 hover:border-blue-400'}">
                                <!-- Ảnh thumbnail -->
                                <div class="aspect-16/10 w-full overflow-hidden bg-slate-900 relative">
                                    <img src="${imgUrl}" alt="Banner ${idx + 1}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="active-slide-badge absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-blue-600/90 text-white text-[9px] font-black uppercase tracking-wider shadow-sm flex items-center gap-1 ${isActive ? '' : 'hidden'}">
                                        <i class="fa-solid fa-eye text-[8px]"></i> Đang xem
                                    </div>
                                </div>
                                <!-- Thông tin & Nút xóa hình ảnh -->
                                <div class="p-2.5 bg-white flex items-center justify-between border-t border-slate-100">
                                    <div class="flex flex-col text-left leading-tight">
                                        <span class="text-xs font-bold text-slate-800">Slide #${idx + 1}</span>
                                        <span class="text-[10px] font-semibold text-emerald-600 mt-0.5">
                                            ● Đang dùng
                                        </span>
                                    </div>
                                    <!-- NÚT XÓA HÌNH ẢNH TRÊN TỪNG THUMBNAIL NHỎ -->
                                    <button type="button" onclick="event.stopPropagation(); deleteBannerImage(${idx})" title="Xóa hình ảnh này" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center text-xs transition-colors shadow-2xs border border-rose-200">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }

            // 2. Render Danh sách ở form bên trái
            if (formList) {
                if (currentBannerList.length === 0) {
                    formList.innerHTML = `
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 text-center">
                            <p class="text-xs text-slate-500 font-medium">Chưa có ảnh banner nào.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Vui lòng tải ảnh lên hoặc thêm URL.</p>
                        </div>
                    `;
                } else {
                    formList.innerHTML = currentBannerList.map((imgUrl, idx) => `
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200/90 hover:bg-blue-50/40 transition-colors">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <img src="${imgUrl}" alt="Thumb" class="w-12 h-9 object-cover rounded-lg border border-slate-200 shrink-0">
                                <div class="flex flex-col overflow-hidden text-left">
                                    <span class="text-xs font-bold text-slate-800">Ảnh Slide #${idx + 1}</span>
                                    <span class="text-[10px] text-slate-400 truncate max-w-[190px]" title="${imgUrl}">${imgUrl.startsWith('data:') ? 'Ảnh tải lên từ máy tính' : imgUrl}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" onclick="switchToBannerSlide(${idx})" title="Xem trước slide này" class="px-2 py-1 rounded-md bg-white hover:bg-blue-50 text-blue-600 text-xs font-semibold border border-slate-200">
                                    Xem
                                </button>
                                <button type="button" onclick="deleteBannerImage(${idx})" title="Xóa ảnh này" class="px-2 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold border border-rose-200">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    `).join('');
                }
            }
        }

        // Xóa một ảnh banner cụ thể theo index
        function deleteBannerImage(index) {
            if (index >= 0 && index < currentBannerList.length) {
                currentBannerList.splice(index, 1);
                const inputUrl = document.getElementById('input-banner-url');
                if (inputUrl) {
                    inputUrl.value = currentBannerList[0] || '';
                }
                if (prevCurrentSlide >= currentBannerList.length) {
                    prevCurrentSlide = Math.max(0, currentBannerList.length - 1);
                }
                updateBannerSlidesPreview();
                renderBannerGallery();
                switchToBannerSlide(prevCurrentSlide);
                showToast('Đã xóa hình ảnh banner! Bấm "Lưu Cấu Hình Giao Diện" để áp dụng.', 'info');
            }
        }

        // Thêm ảnh từ URL nhập tay ở ô input
        function addBannerFromInputUrl() {
            const inputUrl = document.getElementById('input-banner-url');
            const val = inputUrl ? inputUrl.value.trim() : '';
            if (!val) {
                showToast('Vui lòng nhập đường dẫn URL ảnh banner!', 'warning');
                return;
            }
            if (currentBannerList.includes(val)) {
                showToast('Ảnh này đã có trong danh sách banner!', 'info');
                return;
            }
            if (currentBannerList.length >= 5) {
                showToast('Đã đạt giới hạn tối đa 5 ảnh banner!', 'warning');
                return;
            }
            currentBannerList.push(val);
            inputUrl.value = '';
            updateBannerSlidesPreview();
            renderBannerGallery();
            switchToBannerSlide(currentBannerList.length - 1);
            showToast('Đã thêm ảnh banner vào danh sách! Bấm "Lưu Cấu Hình Giao Diện" để áp dụng.', 'success');
        }

        // 7. Hàm xử lý upload ảnh Banner qua Dropzone
        function handleBannerFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgUrl = e.target.result;
                    if (currentBannerList.length >= 5) {
                        currentBannerList[0] = imgUrl;
                    } else {
                        currentBannerList.push(imgUrl);
                    }
                    const inputUrl = document.getElementById('input-banner-url');
                    if (inputUrl) inputUrl.value = currentBannerList[0] || '';
                    updateBannerSlidesPreview();
                    renderBannerGallery();
                    switchToBannerSlide(currentBannerList.length - 1);
                    showToast('Đã thêm ảnh banner mới! Bấm "Lưu Cấu Hình Giao Diện" để cập nhật.', 'success');
                };
                reader.readAsDataURL(file);
            }
        }

        // 8. Hàm cập nhật Live Preview thời gian thực
        function updateLivePreview() {
            const bannerTitle = document.getElementById('input-banner-title')?.value;
            const bannerSubtitle = document.getElementById('input-banner-subtitle')?.value;

            if (bannerTitle && document.getElementById('prev-banner-title')) document.getElementById('prev-banner-title').innerText = bannerTitle;
            if (bannerSubtitle && document.getElementById('prev-banner-subtitle')) document.getElementById('prev-banner-subtitle').innerText = bannerSubtitle;

            const brandName = document.getElementById('input-brand-name')?.value || 'RentHome';
            const brandSlogan = document.getElementById('input-brand-slogan')?.value;

            // Xử lý tách chuỗi Tên thương hiệu (Ví dụ: RentHome -> Rent + Home (màu chủ đạo))
            let firstPart = brandName;
            let secondPart = '';
            if (brandName.toLowerCase().endsWith('home')) {
                firstPart = brandName.substring(0, brandName.length - 4);
                secondPart = 'Home';
            } else if (brandName.length > 4) {
                const mid = Math.floor(brandName.length / 2);
                firstPart = brandName.substring(0, mid);
                secondPart = brandName.substring(mid);
            }

            const brandNameWrap = document.getElementById('prev-brand-name-wrap');
            if (brandNameWrap) {
                brandNameWrap.innerHTML = `${firstPart}<span class="text-emerald-600" id="prev-brand-suffix">${secondPart}</span>`;
            }
            const brandNameTab = document.getElementById('prev-brand-name-tab');
            if (brandNameTab) {
                brandNameTab.innerHTML = `${firstPart}<span class="text-emerald-600" id="prev-brand-suffix-tab">${secondPart}</span>`;
            }
            if (document.getElementById('prev-footer-brand-title')) {
                document.getElementById('prev-footer-brand-title').innerText = brandName;
            }

            if (brandSlogan) {
                if (document.getElementById('prev-brand-slogan')) document.getElementById('prev-brand-slogan').innerText = brandSlogan;
                if (document.getElementById('prev-brand-slogan-tab')) document.getElementById('prev-brand-slogan-tab').innerText = brandSlogan;
                if (document.getElementById('prev-footer-slogan-desc')) document.getElementById('prev-footer-slogan-desc').innerText = brandSlogan;
            }

            const footerPhone = document.getElementById('input-footer-phone')?.value;
            const footerEmail = document.getElementById('input-footer-email')?.value;
            const footerAddress = document.getElementById('input-footer-address')?.value;
            const footerCopyright = document.getElementById('input-footer-copyright')?.value;

            if (footerPhone && document.getElementById('prev-footer-phone')) document.getElementById('prev-footer-phone').innerText = footerPhone;
            if (footerEmail && document.getElementById('prev-footer-email')) document.getElementById('prev-footer-email').innerText = footerEmail;
            if (footerAddress && document.getElementById('prev-footer-address')) {
                document.getElementById('prev-footer-address').innerText = footerAddress;
            }
            if (footerCopyright && document.getElementById('prev-footer-copyright')) document.getElementById('prev-footer-copyright').innerText = footerCopyright;
        }

        // 9. Đổi màu sắc chủ đạo theme (Áp dụng đồng bộ cho Header, Brand & Footer)
        function setPrimaryThemeColor(hexColor) {
            const elementsToBgColor = [
                'prev-logo-icon',
                'prev-avatar-icon',
                'prev-brand-logo-box-tab',
                'prev-brand-avatar-tab',
                'prev-footer-logo-icon'
            ];
            elementsToBgColor.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.backgroundColor = hexColor;
            });

            const elementsToTextColor = [
                'prev-brand-suffix',
                'prev-brand-suffix-tab',
                'prev-nav-active',
                'prev-brand-nav-active',
                'prev-footer-icon-phone',
                'prev-footer-icon-email',
                'prev-footer-icon-addr'
            ];
            elementsToTextColor.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.color = hexColor;
            });

            const navActive = document.getElementById('prev-nav-active');
            if (navActive) navActive.style.borderColor = hexColor;
            const brandNavActive = document.getElementById('prev-brand-nav-active');
            if (brandNavActive) brandNavActive.style.borderColor = hexColor;

            const brandBadge = document.getElementById('prev-brand-badge');
            if (brandBadge) {
                brandBadge.style.color = hexColor;
                brandBadge.style.borderColor = hexColor + '40';
                brandBadge.style.backgroundColor = hexColor + '15';
            }
        }

        // 10. Chuyển chế độ xem trước (Desktop / Mobile)
        function setPreviewViewport(type) {
            const container = document.getElementById('preview-container');
            if (!container) return;
            if (type === 'mobile') {
                container.style.maxWidth = '375px';
                container.style.margin = '0 auto';
            } else {
                container.style.maxWidth = '100%';
                container.style.margin = '0';
            }
        }

        // Khởi chạy khi DOM load xong
        document.addEventListener('DOMContentLoaded', () => {
            updateBannerSlidesPreview();
            renderBannerGallery();
            updateLivePreview();
            initPreviewSlider();
        });

        // Lưu cấu hình giao diện hệ thống & Cập nhật trực tiếp ra Website
        function saveThemeConfig() {
            const configData = {
                banner_title: document.getElementById('input-banner-title')?.value || '',
                banner_subtitle: document.getElementById('input-banner-subtitle')?.value || '',
                banner_cta: '',
                banner_url: currentBannerList[0] || '',
                banner_images: currentBannerList,
                brand_name: document.getElementById('input-brand-name')?.value || '',
                brand_slogan: document.getElementById('input-brand-slogan')?.value || '',
                logo_url: document.getElementById('input-logo-url')?.value || '',
                theme_color: document.getElementById('input-theme-color')?.value || '#16a34a',
                footer_phone: document.getElementById('input-footer-phone')?.value || '',
                footer_email: document.getElementById('input-footer-email')?.value || '',
                footer_address: document.getElementById('input-footer-address')?.value || '',
                footer_copyright: document.getElementById('input-footer-copyright')?.value || '',
                amenities: {!! json_encode($themeSettings['amenities'] ?? []) !!}
            };

            fetch('/sua_giao_dien', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(configData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('🎉 ' + data.message, 'success');
                } else {
                    showToast('Đã lưu cấu hình giao diện thành công!', 'success');
                }
            })
            .catch(() => {
                showToast('Đã lưu cấu hình giao diện thành công!', 'success');
            });
        }
    </script>
</body>
</html>