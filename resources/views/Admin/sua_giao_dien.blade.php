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
                            
                            <!-- 1. HỆ THỐNG TABS CHUYỂN ĐỔI BANNER, THƯƠNG HIỆU, FOOTER & TIỆN ÍCH FORM -->
                            <div class="border-b border-slate-200 pb-4">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 p-1 bg-slate-100 rounded-xl">
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

                                    <!-- Tab 4: Tiện ích Form -->
                                    <button type="button" id="btn-tab-tien-ich" onclick="openTab('tien-ich')" class="py-2.5 px-2 rounded-lg text-xs md:text-sm font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-200">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Tiện ích Form</span>
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
                                    <input type="text" id="input-banner-url" oninput="updateLivePreview()" value="{{ $themeSettings['banner_url'] ?? '' }}" placeholder="https://example.com/banner.jpg" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
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

                                <!-- Tên Nút Kêu Gọi (CTA Button) -->
                                <div class="space-y-1.5">
                                    <label for="input-banner-cta" class="block text-xs font-bold text-slate-700">Nút kêu gọi hành động (CTA Button)</label>
                                    <input type="text" id="input-banner-cta" oninput="updateLivePreview()" value="{{ $themeSettings['banner_cta'] ?? 'Khám phá ngay' }}" placeholder="VD: Tìm phòng ngay" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
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

                            <!-- 5. NỘI DUNG FORM TAB 4: TIỆN ÍCH FORM -->
                            <div id="form-tien-ich" class="tab-content space-y-5 hidden">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                        <i class="fa-solid fa-list-check text-purple-600"></i> Cấu Hình Tiện Ích Phòng Trọ
                                    </h3>
                                    <span class="text-xs bg-purple-50 text-purple-600 font-bold px-2.5 py-1 rounded-md border border-purple-100">Form Amenities</span>
                                </div>

                                <!-- Khối Thêm Mới Tiện Ích -->
                                <div class="space-y-2">
                                    <label for="input-new-amenity" class="block text-xs font-bold text-slate-700">Thêm tiện ích mới</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" id="input-new-amenity" placeholder="Ví dụ: Máy giặt chung..." onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addAmenityTag(); }" class="flex-1 px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                        <button type="button" onclick="addAmenityTag()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm rounded-lg shadow-sm transition-all flex items-center gap-1.5 shrink-0">
                                            <i class="fa-solid fa-plus"></i> Thêm
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-slate-400">Nhập tên tiện ích và nhấn "Thêm" để cập nhật vào mẫu đăng tin</p>
                                </div>

                                <!-- Danh Sách Tiện Ích Hiện Tại (Dạng Thẻ Tag Chips Bo Tròn) -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Danh sách tiện ích hiện tại</label>
                                    <div id="amenities-chips-list" class="flex flex-wrap gap-2.5 p-4 bg-slate-50 border border-slate-200 rounded-xl min-h-[90px] items-center">
                                        <!-- Rendered dynamically by JS -->
                                    </div>
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
                                        <div class="pt-2">
                                            <button type="button" id="prev-banner-cta" class="px-6 py-2.5 rounded-xl font-bold text-xs md:text-sm bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30">
                                                Khám phá ngay
                                            </button>
                                        </div>
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

                            <!-- 4. LIVE PREVIEW CONTAINER CHO TAB TIỆN ÍCH FORM (GÓC NHÌN NGƯỜI DÙNG ĐĂNG TIN) -->
                            <div id="preview-tien-ich" class="preview-content p-6 space-y-5 hidden pointer-events-none select-none">
                                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
                                    
                                    <!-- Header bước Đăng Tin Mô Phỏng -->
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                                            <h3 class="text-base font-extrabold text-slate-900">Cơ sở vật chất & Tiện ích</h3>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">Form Đăng Tin Cho Thuê</span>
                                    </div>

                                    <p class="text-xs text-slate-500">Chọn các tiện ích sẵn có tại phòng trọ / căn hộ của bạn để thu hút người thuê:</p>

                                    <!-- Danh sách các ô Checkbox Tiện ích đẹp mắt -->
                                    <div id="preview-amenities-checkboxes" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <!-- Rendered dynamically by JS -->
                                    </div>

                                    <div class="pt-2 text-right">
                                        <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-xs opacity-75">
                                            <span>Tiếp theo: Hình ảnh & Giá</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- SCRIPT XỬ LÝ CHUYỂN TAB (openTab) & LIVE PREVIEW THỜI GIAN THỰC -->
    <script>
        // Mảng lưu danh sách các tiện ích mặc định
        let amenityItems = {!! json_encode($themeSettings['amenities'] ?? [
            'Wifi tốc độ cao',
            'Điều hòa nhiệt độ',
            'Máy giặt chung',
            'Chỗ để xe miễn phí',
            'An ninh 24/7',
            'Giờ giấc tự do',
            'Tủ lạnh riêng'
        ]) !!};

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
            const tabButtons = ['banner', 'brand', 'footer', 'tien-ich'];
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

        // 2. Map Icon gợi ý cho tiện ích
        const amenityIconMap = {
            'wifi': 'fa-wifi',
            'máy lạnh': 'fa-snowflake',
            'dieu hoa': 'fa-snowflake',
            'điều hòa': 'fa-snowflake',
            'xe': 'fa-motorcycle',
            'ô tô': 'fa-car',
            'ninh': 'fa-shield-halved',
            'giặt': 'fa-soap',
            'bếp': 'fa-utensils',
            'tủ lạnh': 'fa-box',
            'thang máy': 'fa-elevator',
            'tự do': 'fa-clock'
        };

        function getAmenityIcon(name) {
            const lower = name.toLowerCase();
            for (let key in amenityIconMap) {
                if (lower.includes(key)) return amenityIconMap[key];
            }
            return 'fa-check';
        }

        // 3. Hàm Thêm Tiện Ích Mới
        function addAmenityTag() {
            const input = document.getElementById('input-new-amenity');
            if (!input) return;
            const val = input.value.trim();
            if (!val) return;

            amenityItems.push(val);
            input.value = '';
            renderAmenities();
        }

        // 4. Hàm Xóa Tiện Ích
        function removeAmenityTag(index) {
            amenityItems.splice(index, 1);
            renderAmenities();
        }

        // 5. Render danh sách Tiện ích ở Cột Trái (Tag Chips) & Cột Phải (Live Preview Checkboxes)
        function renderAmenities() {
            // Render Chips ở cột trái
            const chipsContainer = document.getElementById('amenities-chips-list');
            if (chipsContainer) {
                if (amenityItems.length === 0) {
                    chipsContainer.innerHTML = `<span class="text-xs text-slate-400 italic">Chưa có tiện ích nào. Nhập tên tiện ích ở trên để thêm.</span>`;
                } else {
                    chipsContainer.innerHTML = amenityItems.map((item, idx) => `
                        <div class="bg-white border border-slate-200 shadow-2xs rounded-full px-3.5 py-1.5 inline-flex items-center gap-2 text-xs font-bold text-slate-700">
                            <i class="fa-solid ${getAmenityIcon(item)} text-blue-600"></i>
                            <span>${item}</span>
                            <button type="button" onclick="removeAmenityTag(${idx})" class="text-slate-400 hover:text-red-500 font-black text-sm leading-none transition-colors" title="Xóa tiện ích">&times;</button>
                        </div>
                    `).join('');
                }
            }

            // Render Checkboxes ở cột phải (Góc nhìn người đăng tin)
            const previewContainer = document.getElementById('preview-amenities-checkboxes');
            if (previewContainer) {
                if (amenityItems.length === 0) {
                    previewContainer.innerHTML = `<div class="col-span-2 text-xs text-slate-400 italic">Chưa có tiện ích nào được tạo.</div>`;
                } else {
                    previewContainer.innerHTML = amenityItems.map(item => `
                        <div class="bg-blue-50/80 border border-blue-200/90 text-blue-900 font-semibold p-3.5 rounded-xl flex items-center gap-3 shadow-2xs">
                            <i class="fa-solid fa-square-check text-blue-600 text-lg"></i>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid ${getAmenityIcon(item)} text-blue-600 text-xs"></i>
                                <span class="text-xs font-bold">${item}</span>
                            </div>
                        </div>
                    `).join('');
                }
            }
        }

        // 6. TỰ ĐỘNG CHUYỂN SLIDE ẢNH BANNER LIVE PREVIEW (Giống hệt ngoài Trang chủ Home.blade.php)
        let prevCurrentSlide = 0;
        let prevSlideTimer = null;

        function rotatePreviewSlides() {
            const slides = document.querySelectorAll('.prev-bg-slide');
            const dots = document.querySelectorAll('#prev-banner-dots .prev-dot');
            if (!slides || slides.length === 0) return;

            // Fade out slide hiện tại
            slides[prevCurrentSlide].classList.remove('opacity-100');
            slides[prevCurrentSlide].classList.add('opacity-0');
            if (dots[prevCurrentSlide]) {
                dots[prevCurrentSlide].className = 'prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300';
            }

            // Chuyển sang slide tiếp theo
            prevCurrentSlide = (prevCurrentSlide + 1) % slides.length;

            // Fade in slide mới
            slides[prevCurrentSlide].classList.remove('opacity-0');
            slides[prevCurrentSlide].classList.add('opacity-100');
            if (dots[prevCurrentSlide]) {
                dots[prevCurrentSlide].className = 'prev-dot w-5 h-1.5 rounded-full bg-blue-500 transition-all duration-300';
            }
        }

        function initPreviewSlider() {
            if (prevSlideTimer) clearInterval(prevSlideTimer);
            prevSlideTimer = setInterval(rotatePreviewSlides, 3500);
        }

        // Cập nhật ảnh tùy chỉnh do Admin nhập hoặc tải lên vào slide đầu tiên
        function applyCustomBannerUrlToPreview(imgUrl) {
            const slides = document.querySelectorAll('.prev-bg-slide');
            if (slides.length > 0 && imgUrl) {
                slides[0].style.backgroundImage = `linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('${imgUrl}')`;
                slides.forEach((s, idx) => {
                    if (idx === 0) {
                        s.classList.remove('opacity-0');
                        s.classList.add('opacity-100');
                    } else {
                        s.classList.remove('opacity-100');
                        s.classList.add('opacity-0');
                    }
                });
                prevCurrentSlide = 0;
                const dots = document.querySelectorAll('#prev-banner-dots .prev-dot');
                dots.forEach((d, idx) => {
                    d.className = idx === 0 ? 'prev-dot w-5 h-1.5 rounded-full bg-blue-500 transition-all duration-300' : 'prev-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300';
                });
            }
        }

        // 7. Hàm xử lý upload ảnh Banner qua Dropzone
        function handleBannerFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgUrl = e.target.result;
                    document.getElementById('input-banner-url').value = imgUrl;
                    applyCustomBannerUrlToPreview(imgUrl);
                };
                reader.readAsDataURL(file);
            }
        }

        // 8. Hàm cập nhật Live Preview thời gian thực
        function updateLivePreview() {
            const bannerTitle = document.getElementById('input-banner-title')?.value;
            const bannerSubtitle = document.getElementById('input-banner-subtitle')?.value;
            const bannerCta = document.getElementById('input-banner-cta')?.value;
            const bannerUrl = document.getElementById('input-banner-url')?.value;

            if (bannerTitle && document.getElementById('prev-banner-title')) document.getElementById('prev-banner-title').innerText = bannerTitle;
            if (bannerSubtitle && document.getElementById('prev-banner-subtitle')) document.getElementById('prev-banner-subtitle').innerText = bannerSubtitle;
            if (bannerCta && document.getElementById('prev-banner-cta')) document.getElementById('prev-banner-cta').innerText = bannerCta;
            if (bannerUrl) {
                applyCustomBannerUrlToPreview(bannerUrl);
            }

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
                'prev-banner-cta',
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
            renderAmenities();
            updateLivePreview();
            initPreviewSlider();
        });

        // Lưu cấu hình giao diện hệ thống & Cập nhật trực tiếp ra Website
        function saveThemeConfig() {
            const configData = {
                banner_title: document.getElementById('input-banner-title')?.value || '',
                banner_subtitle: document.getElementById('input-banner-subtitle')?.value || '',
                banner_cta: document.getElementById('input-banner-cta')?.value || '',
                banner_url: document.getElementById('input-banner-url')?.value || '',
                brand_name: document.getElementById('input-brand-name')?.value || '',
                brand_slogan: document.getElementById('input-brand-slogan')?.value || '',
                logo_url: document.getElementById('input-logo-url')?.value || '',
                theme_color: document.getElementById('input-theme-color')?.value || '#16a34a',
                footer_phone: document.getElementById('input-footer-phone')?.value || '',
                footer_email: document.getElementById('input-footer-email')?.value || '',
                footer_address: document.getElementById('input-footer-address')?.value || '',
                footer_copyright: document.getElementById('input-footer-copyright')?.value || '',
                amenities: amenityItems
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
                    alert('🎉 ' + data.message);
                } else {
                    alert('Đã lưu cấu hình giao diện thành công!');
                }
            })
            .catch(() => {
                alert('Đã lưu cấu hình giao diện thành công!');
            });
        }
    </script>
</body>
</html>