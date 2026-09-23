<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nhà Đang Thuê & Thanh Toán Hóa Đơn - RentHome</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

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
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        skybrand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(34, 197, 94, 0.4)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.05)',
                        'sky-glow': '0 0 25px -5px rgba(2, 132, 199, 0.3)',
                    }
                }
            }
        }
    </script>

    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .tab-btn.active {
            color: #0284c7;
            border-bottom-color: #0284c7;
            background-color: #f0f9ff;
            font-weight: 700;
        }

        .sidebar-item.active {
            background-color: #f0f9ff;
            color: #0284c7;
            border-right: 4px solid #0284c7;
            font-weight: 700;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col md:flex-row">

    <!-- SIDEBAR HÀNG DỌC BÊN TRÁI (FIXED LEFT SIDEBAR) -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0 md:fixed md:top-0 md:bottom-0 md:left-0 md:h-screen md:z-50 overflow-y-auto">
        <!-- Sidebar Header / Logo -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5">Thuê nhà ước mơ</span>
                </div>
            </a>
        </div>

        <!-- Sidebar Navigation Menu: 2 FORM RIÊNG BIỆT -->
        <div class="p-4 space-y-6 flex-1">
            <div>
                <p class="px-3 text-[11px] font-extrabold uppercase text-slate-400 tracking-wider mb-2">Quản lý cá nhân</p>
                <nav class="space-y-1">
                    <!-- 1. Form Nhà đang thuê -->
                    <button type="button" id="sidebar-nav-nha" onclick="switchMainForm('nha-dang-thue')" 
                       class="sidebar-item active w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs text-slate-700 hover:bg-slate-50 transition-all text-left">
                        <div class="w-7 h-7 rounded-lg bg-skybrand-100 text-skybrand-600 flex items-center justify-center font-bold shrink-0">
                            <i class="fa-solid fa-house-user text-sm"></i>
                        </div>
                        <span class="text-sm font-semibold">Nhà đang thuê</span>
                        <span class="ml-auto px-2 py-0.5 rounded-full bg-skybrand-100 text-skybrand-700 font-extrabold text-[10px]" id="sidebar-rented-count">0</span>
                    </button>

                    <!-- 2. Form Thanh toán hóa đơn -->
                    <button type="button" id="sidebar-nav-hoadon" onclick="switchMainForm('thanh-toan-hoa-don')" 
                       class="sidebar-item w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs text-slate-700 hover:bg-slate-50 transition-all text-left">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <span class="text-sm font-semibold">Thanh toán hóa đơn</span>
                        <span class="ml-auto px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[10px]" id="sidebar-pending-count">0</span>
                    </button>

                    <div class="my-2 border-t border-slate-100"></div>

                    <!-- 3. Trở về trang chủ -->
                    <a href="{{ url('/') }}" class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs text-slate-700 hover:bg-slate-50 transition-all text-left">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold shrink-0">
                            <i class="fa-solid fa-arrow-left text-sm"></i>
                        </div>
                        <span class="text-sm font-semibold">Trở về trang chủ</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar Footer / Account Tag & Logout Icon Button CẠNH ADMIN -->
        <div class="p-3 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center justify-between p-2.5 rounded-2xl bg-white border border-slate-200 shadow-sm gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-skybrand-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="truncate text-left leading-tight">
                        <p class="text-xs font-extrabold text-slate-800 truncate">
                            {{ Auth::user()->username ?? Auth::user()->name ?? 'admin' }}
                        </p>
                        <span class="text-[9px] font-bold text-skybrand-600 bg-skybrand-50 px-1.5 py-0.5 rounded inline-block mt-0.5">Cá nhân</span>
                    </div>
                </div>

                <!-- Chỉ để Nút Icon Đăng xuất ở CẠNH Admin -->
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 shrink-0">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold flex items-center justify-center transition-all shadow-sm">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- RIGHT MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 md:ml-64">

        <!-- TOP BAR -->
        <header class="py-3 px-4 sm:px-8 bg-white/80 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-end z-40 sticky top-0">
            <!-- User Profile Dropdown (ĐÃ BỎ NÚT ĐĂNG XUẤT CẠNH HÌNH 3) -->
            <div class="relative inline-block text-left" id="user-dropdown-container">
                <button type="button" onclick="toggleUserDropdown(event)"
                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-slate-100/80 transition-all cursor-pointer border border-slate-200/80 bg-white">
                    <div class="w-8 h-8 rounded-full bg-skybrand-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="flex flex-col text-left leading-tight">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                            {{ Auth::user()->username ?? Auth::user()->name ?? 'admin' }}
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </span>
                        <span class="text-[10px] font-semibold text-skybrand-600 flex items-center gap-1">
                            <i class="fa-solid fa-user-check text-[9px]"></i> Cá nhân
                        </span>
                    </div>
                </button>

                <div id="user-dropdown-menu" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white shadow-2xl border border-slate-100 py-2 z-50">
                    <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between gap-2">
                        <div class="truncate">
                            <p class="text-[11px] font-medium text-slate-400">Tài khoản đang đăng nhập</p>
                            <p class="text-xs font-bold text-slate-900 truncate">
                                {{ Auth::user()->email ?? Auth::user()->username ?? 'admin@renthome.vn' }}
                            </p>
                        </div>
                        <div class="shrink-0">
                            @if ((Auth::user()->account_type ?? '') === 'doanhnghiep')
                                <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold text-[10px]">Doanh nghiệp</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 font-bold text-[10px]">Cá nhân</span>
                            @endif
                        </div>
                    </div>

                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-circle-user text-emerald-600 w-4 text-center"></i>
                        <span>Tài khoản</span>
                    </a>

                    <a href="{{ url('Overview') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-newspaper text-emerald-600 w-4 text-center"></i>
                        <span>Quản lý bài đăng</span>
                    </a>

                    <a href="{{ url('quan-ly-van-hanh') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-gears text-emerald-600 w-4 text-center"></i>
                        <span>Quản lý vận hành</span>
                    </a>

                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-envelope-open-text text-emerald-600 w-4 text-center"></i>
                        <span>Thông báo liên hệ</span>
                    </a>

                    <a href="/dat-lai-mat-khau" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-key text-emerald-600 w-4 text-center"></i>
                        <span>Đổi mật khẩu</span>
                    </a>

                    <div class="my-1 border-t border-slate-100"></div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT CONTAINER -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- ========================================================================= -->
            <!-- FORM 1: QUẢN LÝ NHÀ ĐANG THUÊ (DEFAULT VIEW) -->
            <!-- ========================================================================= -->
            <div id="form-nha-dang-thue" class="space-y-8">
                <!-- Banner Form 1: Đồng Màu Dark Navy Nét Như Hình 2 -->
                <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-skybrand-500/20 border border-skybrand-400/30 text-sky-300 text-xs font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-user-shield"></i> FORM 1: QUẢN LÝ NHÀ ĐANG THUÊ
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                                Danh Sách Nhà Đang Thuê
                            </h1>
                            <p class="text-slate-300 text-sm sm:text-base max-w-2xl font-normal">
                                Tra cứu thông tin hợp đồng, thông tin chủ hộ, gửi yêu cầu trả nhà & liên hệ hỗ trợ trực tiếp.
                            </p>
                        </div>

                        <!-- Khung thông tin bên phải sắc nét -->
                        <div class="flex items-center gap-3.5 bg-[#142248] border border-sky-500/30 px-5 py-3.5 rounded-2xl shrink-0 shadow-lg">
                            <div class="w-10 h-10 rounded-xl bg-skybrand-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                                <i class="fa-solid fa-house-user"></i>
                            </div>
                            <div>
                                <p class="text-xs text-sky-200 font-medium">Tổng căn đang thuê</p>
                                <p class="text-sm font-extrabold text-white">
                                    <span id="total-rented-count-1">0</span> Căn hộ / Phòng trọ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stat Summary Card Form 1 -->
                <div class="w-full">
                    <div class="w-full bg-gradient-to-br from-white via-skybrand-50/40 to-emerald-50/30 p-6 rounded-3xl border-2 border-skybrand-200 shadow-card flex flex-col justify-between relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-skybrand-600 text-white flex items-center justify-center text-xl shadow-sky-glow">
                                    <i class="fa-solid fa-house-user"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-extrabold text-skybrand-700 uppercase tracking-wider">THỐNG KÊ DANH SÁCH</span>
                                    <h3 class="text-lg font-extrabold text-slate-900">NHÀ ĐANG THUÊ HỢP ĐỒNG</h3>
                                </div>
                            </div>
                            <span class="px-3.5 py-1 rounded-full bg-emerald-500 text-white text-xs font-bold shadow-sm">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> <span id="total-active-count">0</span> Căn hoạt động
                            </span>
                        </div>

                        <div class="mt-6 flex items-baseline gap-3">
                            <span class="text-4xl font-black text-slate-900" id="total-rented-count-2">0</span>
                            <span class="text-sm font-bold text-slate-600">Căn hộ / Phòng trọ bạn đang đứng tên thuê</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-skybrand-100 flex items-center justify-between text-xs font-semibold">
                            <span class="text-emerald-600 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-shield-check"></i> Hợp đồng đầy đủ hiệu lực
                            </span>
                            <span class="text-skybrand-700 font-bold">Tổng tiền thuê: <span id="total-rent-amount">0</span>đ/tháng</span>
                        </div>
                    </div>
                </div>

                <!-- DANH SÁCH 5 NHÀ ĐANG THUÊ (HÌNH CHỮ NHẬT NẰM NGANG) -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-house-flag text-skybrand-600"></i>
                            Chi Tiết <span id="detail-active-count">0</span> Căn Nhà Đang Thuê
                        </h2>
                        <span class="px-3.5 py-1.5 rounded-full bg-skybrand-100 text-skybrand-700 text-xs font-extrabold border border-skybrand-200">
                            Tổng số: <span id="detail-total-count">0</span> nhà đang thuê
                        </span>
                    </div>

                    <div id="rented-houses-container" class="flex flex-col gap-6">
                        <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px dashed #cbd5e1;">
                            <i class="fa-solid fa-house-crack" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Bạn chưa thuê nhà/phòng nào</p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- ========================================================================= -->
            <!-- FORM 2: QUẢN LÝ THANH TOÁN HÓA ĐƠN & CÔNG NỢ HỢP ĐỒNG (DESICATED VIEW) -->
            <!-- ========================================================================= -->
            <div id="form-thanh-toan-hoa-don" class="hidden space-y-8">
                <!-- Banner Form 2: Đồng Màu Dark Navy Nét Như Hình 2 -->
                <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-file-invoice-dollar"></i> FORM 2: THANH TOÁN HÓA ĐƠN & CÔNG NỢ
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                                Quản Lý Hóa Đơn & Chu Kỳ Đóng Tiền
                            </h1>
                            <p class="text-slate-300 text-sm sm:text-base max-w-2xl font-normal">
                                Tra cứu hóa đơn dịch vụ hàng tháng, quét mã VietQR thanh toán nhanh và đối soát tiền cọc công nợ hợp đồng.
                            </p>
                        </div>

                        <!-- Khung thông tin bên phải sắc nét -->
                        <div class="flex items-center gap-3.5 bg-[#142248] border border-emerald-500/30 px-5 py-3.5 rounded-2xl shrink-0 shadow-lg">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-md">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div>
                                <p class="text-xs text-emerald-200 font-medium">Cần thanh toán (<span id="pending-houses-count">0</span> nhà)</p>
                                <p class="text-sm font-extrabold text-rose-400">
                                    <span id="pending-total-amount">0</span> VNĐ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BẢNG HÓA ĐƠN VÀ CÔNG NỢ HỢP ĐỒNG -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
                    <div class="p-6 border-b border-slate-200/80 bg-slate-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                                    Chi Tiết Hóa Đơn & Đối Soát Công Nợ
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Thanh toán bằng VietQR, tra cứu lịch sử hóa đơn và cọc bảo đảm hợp đồng.</p>
                            </div>

                            <!-- Filter Tabs -->
                            <div class="flex items-center gap-1 bg-slate-200/70 p-1 rounded-2xl text-xs font-semibold self-start sm:self-auto">
                                <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn active px-3.5 py-2 rounded-xl transition-all">
                                    <i class="fa-solid fa-clock text-amber-500"></i> Hóa đơn cần thanh toán (2)
                                </button>
                                <button onclick="switchTab('history')" id="tab-history" class="tab-btn px-3.5 py-2 rounded-xl transition-all text-slate-600">
                                    <i class="fa-solid fa-history text-skybrand-600"></i> Hóa đơn cũ
                                </button>
                                <button onclick="switchTab('liabilities')" id="tab-liabilities" class="tab-btn px-3.5 py-2 rounded-xl transition-all text-slate-600">
                                    <i class="fa-solid fa-scale-balanced text-purple-600"></i> Công nợ hợp đồng
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 1: HÓA ĐƠN CẦN THANH TOÁN -->
                    <div id="content-pending" class="p-6 space-y-6">
                        <div id="pending-invoices-container">
                            <div style="text-align: center; padding: 60px 20px; border-radius: 20px; border: 1px dashed #cbd5e1;">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                                <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Không có hóa đơn nào cần thanh toán</p>
                            </div>
                        </div>
                    
                    </div>

                    <!-- TAB 2: HÓA ĐƠN CỦ (LỊCH SỬ) -->
                    <div id="content-history" class="hidden p-6">
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 font-bold bg-slate-50">
                                        <th class="py-3 px-4">Mã hóa đơn</th>
                                        <th class="py-3 px-4">Nhà / Phòng</th>
                                        <th class="py-3 px-4">Kỳ thanh toán</th>
                                        <th class="py-3 px-4">Tổng tiền</th>
                                        <th class="py-3 px-4">Ngày thanh toán</th>
                                        <th class="py-3 px-4">Hình thức</th>
                                        <th class="py-3 px-4 text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="history-invoices-tbody">
                                    </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: CÔNG NỢ HỢP ĐỒNG -->
                    <div id="content-liabilities" class="hidden p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="p-5 rounded-2xl bg-skybrand-50/70 border border-skybrand-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-skybrand-700 uppercase tracking-wider">Tổng tiền cọc 5 nhà đang giữ</span>
                                    <i class="fa-solid fa-shield-halved text-skybrand-600 text-lg"></i>
                                </div>
                                <p class="text-2xl font-black text-slate-900"><span id="total-deposit-amount">0</span> VNĐ</p>
                                <p class="text-xs text-slate-600">Tiền cọc được bảo toàn 100% và sẽ hoàn trả cho bạn khi thanh lý hợp đồng & chủ nhà xác nhận.</p>
                            </div>

                            <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Công nợ quá hạn</span>
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                                </div>
                                <p class="text-2xl font-black text-emerald-600">0 VNĐ</p>
                                <p class="text-xs text-slate-600">Tài khoản uy tín: Bạn không có bất kỳ khoản nợ đọng hay tiền phạt quá hạn nào.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-house-chimney"></i>
                    </div>
                    <span class="font-bold text-white text-sm">RentHome Việt Nam</span>
                    <span>© 2026 Tất cả quyền được bảo lưu.</span>
                </div>
                <p class="text-slate-500">Dành riêng cho Tài Khoản Cá Nhân quản lý nhà đang thuê & hóa đơn.</p>
            </div>
        </footer>

    </div>

    <!-- MODAL 1: LIÊN HỆ CHỦ NHÀ -->
    <div id="contactModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 transform transition-all border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-skybrand-100 text-skybrand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-headset text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Liên Hệ Chủ Nhà</h3>
                        <p class="text-xs text-slate-500" id="contactHouseTitle">Nhà đang thuê</p>
                    </div>
                </div>
                <button onclick="closeContactModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-xs text-slate-500">Tên chủ hộ / Quản lý:</p>
                    <p class="text-lg font-extrabold text-slate-900" id="contactLandlordName">Nguyễn Văn Hùng</p>
                    <p class="text-sm font-bold text-skybrand-600 mt-0.5" id="contactLandlordPhone">0908 123 456</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a id="btnCallDirect" href="tel:0908123456" 
                       class="py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                        <i class="fa-solid fa-phone"></i> Gọi điện thoại
                    </a>
                    <a id="btnZalo" href="https://zalo.me/0908123456" target="_blank"
                       class="py-3 px-4 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                        <i class="fa-solid fa-comment-dots"></i> Nhắn tin Zalo
                    </a>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 block">Gửi lời nhắn nhanh cho chủ nhà:</label>
                    <select id="quickMessageSelect" onchange="updateMessageText()" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 bg-slate-50 font-medium">
                        <option value="Tôi muốn hỏi về hóa đơn tiền điện nước tháng này.">Hỏi về hóa đơn tiền điện nước</option>
                        <option value="Xin báo chủ nhà cho mình gia hạn ngày đóng tiền phòng vài ngày.">Xin gia hạn ngày đóng tiền</option>
                        <option value="Phòng có thiết bị bị hỏng cần hỗ trợ sửa chữa khẩn cấp.">Báo hỏng hóc thiết bị trong phòng</option>
                        <option value="Xin chào chủ nhà, mình cần hỏi thông tin về hợp đồng.">Tra cứu hợp đồng thuê nhà</option>
                    </select>
                    <textarea id="quickMessageArea" rows="3" class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-skybrand-500 focus:outline-none" placeholder="Nhập tin nhắn..."></textarea>
                </div>

                <button onclick="sendQuickMessage()" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-paper-plane"></i> Gửi tin nhắn trực tiếp
                </button>
            </div>
        </div>
    </div>


    <!-- MODAL 2: GỬI YÊU CẦU TRẢ NHÀ -->
    <div id="returnModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 transform transition-all border border-slate-100 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Gửi Yêu Cầu Trả Nhà</h3>
                        <p class="text-xs text-slate-500">Thông báo kết thúc hợp đồng & nhận lại tiền cọc</p>
                    </div>
                </div>
                <button onclick="closeReturnModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Notice workflow banner -->
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                <p class="font-bold flex items-center gap-1.5 text-amber-800">
                    <i class="fa-solid fa-circle-info"></i> Quy trình xác nhận trả nhà:
                </p>
                <p class="leading-relaxed">
                    Sau khi bạn gửi yêu cầu, <strong>chủ nhà sẽ nhận được thông tin thông báo</strong>. Chủ nhà tiến hành kiểm tra tình trạng tài sản phòng, xác nhận thanh lý hợp đồng và <strong>chuyển trạng thái phòng thành "Còn trống"</strong> trên hệ thống.
                </p>
            </div>

            <form id="returnHouseForm" onsubmit="handleReturnSubmit(event)" class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Căn hộ / Phòng trọ trả:</label>
                    <input type="text" id="returnHouseName" readonly class="w-full p-3 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Chủ hộ tiếp nhận:</label>
                        <input type="text" id="returnLandlord" readonly class="w-full p-2.5 rounded-xl border border-slate-200 bg-slate-100 font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Số tiền cọc dự kiến hoàn:</label>
                        <input type="text" id="returnDeposit" readonly class="w-full p-2.5 rounded-xl border border-slate-200 bg-emerald-50 text-emerald-700 font-extrabold">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Ngày dự kiến bàn giao trả nhà (*):</label>
                    <input type="date" required id="returnDate" class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Lý do trả nhà (*):</label>
                    <select required class="w-full p-2.5 rounded-xl border border-slate-200 font-medium focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                        <option value="">-- Chọn lý do --</option>
                        <option value="hethan">Hết thời hạn hợp đồng thuê nhà</option>
                        <option value="chuyencongtac">Chuyển địa điểm làm việc / chuyển vùng</option>
                        <option value="muanha">Đã mua được nhà mới</option>
                        <option value="khac">Lý do cá nhân khác</option>
                    </select>
                </div>

                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <label class="font-bold text-slate-800 block">Thông tin ngân hàng nhận lại tiền cọc:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" placeholder="Tên Ngân hàng (MB, VCB...)" required class="p-2.5 rounded-xl border border-slate-200">
                        <input type="text" placeholder="Số tài khoản" required class="p-2.5 rounded-xl border border-slate-200 font-mono">
                    </div>
                    <input type="text" placeholder="Tên chủ tài khoản nhận tiền" required class="w-full p-2.5 rounded-xl border border-slate-200 uppercase">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Ghi chú bổ sung cho chủ nhà:</label>
                    <textarea rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-skybrand-500 focus:outline-none" placeholder="Hẹn giờ bàn giao chìa khóa, tình trạng phòng..."></textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeReturnModal()" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold">Hủy bỏ</button>
                    <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition-all">
                        Xác nhận gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL CHI TIẾT HÓA ĐƠN & THANH TOÁN (ẢNH 3) -->
    <div id="invoiceDetailModal" onclick="if(event.target === this) closeInvoiceDetailModal()" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 cursor-pointer">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-4 sm:p-5 shadow-2xl space-y-4 transform transition-all border border-slate-100 max-h-[96vh] overflow-y-auto cursor-default" style="scrollbar-width: none; -ms-overflow-style: none;">
            <!-- Header Banner Phiếu tính tiền chi tiết -->
            <div class="relative bg-gradient-to-r from-rose-500 via-rose-600 to-amber-600 p-4 sm:p-5 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="detailInvoiceCode" class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-extrabold backdrop-blur-md">
                            Mã hóa đơn: #HD-092026
                        </span>
                        <span id="detailHouseName" class="px-2.5 py-0.5 rounded-full bg-black/30 text-white text-[11px] font-bold backdrop-blur-md">
                            Phòng Trọ Cao Cấp Nguyên Hồng (NH-302)
                        </span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-white tracking-tight">PHIẾU TÍNH TIỀN CHI TIẾT – THÁNG 09/2026</h3>
                    <p class="text-xs text-rose-100 flex flex-wrap items-center gap-2">
                        <span>Chủ nhà: <strong id="detailLandlordInfo">Lê Thị Mai (SĐT: 0912 987 654)</strong></span>
                        <span class="hidden sm:inline">•</span>
                        <span class="font-bold bg-white/20 px-2 py-0.5 rounded text-white"><i class="fa-solid fa-calendar-day"></i> Hạn thanh toán: <span id="detailDueDate">10/10/2026</span> (Do chủ nhà cài đặt)</span>
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="bg-white text-rose-600 px-4 py-2 rounded-xl shadow-lg border-2 border-rose-200 text-center font-black tracking-wider uppercase text-sm animate-pulse">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i> CHƯA THANH TOÁN
                    </div>
                </div>
            </div>

            <!-- Bảng tính tiền chi tiết minh bạch -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-600 font-extrabold uppercase bg-slate-100/80">
                            <th class="py-2.5 px-3.5">Hạng mục thanh toán</th>
                            <th class="py-2.5 px-3.5 text-center">Số mới</th>
                            <th class="py-2.5 px-3.5 text-center">Số cũ</th>
                            <th class="py-2.5 px-3.5 text-center">Sử dụng</th>
                            <th class="py-2.5 px-3.5 text-right">Đơn giá</th>
                            <th class="py-2.5 px-3.5 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="detailInvoiceTableBody" class="divide-y divide-slate-100 font-medium">
                        <!-- Loaded dynamically -->
                    </tbody>
                    <tfoot>
                        <tr class="bg-rose-50/90 border-t-2 border-rose-300 font-extrabold">
                            <td colspan="5" class="py-3 px-3.5 text-xs sm:text-sm text-slate-900 uppercase tracking-wide">
                                <i class="fa-solid fa-calculator text-rose-600"></i> TỔNG CỘNG CẦN THANH TOÁN:
                            </td>
                            <td class="py-3 px-3.5 text-right text-lg sm:text-xl font-black text-rose-600" id="detailTotalAmount">3.605.000 VNĐ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Hướng dẫn & Thông tin thanh toán (2 Hình thức) -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-2">
                    <i class="fa-solid fa-credit-card text-emerald-600"></i> Hướng Dẫn & Thông Tin Thanh Toán
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <!-- HÌNH THỨC 1: CHUYỂN KHOẢN -->
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-2.5 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-extrabold border border-emerald-200">
                                    <i class="fa-solid fa-qrcode"></i> Hình thức 1: Chuyển khoản (Recommended)
                                </span>
                                <span id="detailBankShortName" class="text-[10px] text-slate-400 font-bold">MB Bank</span>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl space-y-1 text-xs border border-slate-100">
                                <p class="flex justify-between"><span class="text-slate-500">Tên ngân hàng:</span> <strong id="detailBankFullName" class="text-slate-900 font-bold">MB Bank (Ngân hàng Quân Đội)</strong></p>
                                <p class="flex justify-between"><span class="text-slate-500">Số tài khoản:</span> <strong id="detailBankAccount" class="text-emerald-700 font-mono font-black text-sm">0912 987 654 888</strong></p>
                                <p class="flex justify-between"><span class="text-slate-500">Chủ tài khoản:</span> <strong id="detailBankOwner" class="text-slate-900 font-bold uppercase">LÊ THỊ MAI</strong></p>
                                <p class="flex justify-between"><span class="text-slate-500">Cú pháp CK:</span> <strong id="detailTransferSyntax" class="text-skybrand-600 font-mono font-bold">HD-092026</strong></p>
                            </div>
                        </div>

                        <button id="btnTriggerVietQR" 
                                class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                            <i class="fa-solid fa-qrcode text-sm"></i> Quét mã VietQR chuyển khoản ngay
                        </button>
                    </div>

                    <!-- HÌNH THỨC 2: TIỀN MẶT -->
                    <div class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-200 shadow-xs space-y-2.5 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-300">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Hình thức 2: Tiền mặt
                                </span>
                                <span class="text-[10px] text-amber-700 font-bold">Đóng trực tiếp</span>
                            </div>
                            
                            <div class="p-2.5 bg-white/90 rounded-xl border border-amber-200 space-y-1.5 text-xs">
                                <p class="text-amber-900 font-bold flex items-center gap-2 text-xs">
                                    <i class="fa-solid fa-comments-dollar text-amber-600"></i>
                                    "Vui lòng gặp chủ nhà để đóng tiền mặt"
                                </p>
                                <p id="detailCashNote" class="text-slate-600 leading-relaxed text-[11px]">
                                    Chủ nhà: <strong>Lê Thị Mai</strong> (SĐT: 0912 987 654). Sau khi đóng tiền mặt, chủ nhà sẽ xác nhận hóa đơn hoàn tất trên hệ thống.
                                </p>
                            </div>
                        </div>

                        <button id="btnTriggerContactLandlord" 
                                class="w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all">
                            <i class="fa-solid fa-phone"></i> Liên hệ chủ nhà đóng tiền mặt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL 3: THANH TOÁN QR VIETQR -->
    <div id="payQRModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 transform transition-all border border-slate-100 text-center">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-qrcode text-skybrand-600"></i> Thanh Toán Qua VietQR
                </h3>
                <button onclick="closePayQRModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div class="inline-block p-3 rounded-2xl bg-slate-50 border border-slate-200 shadow-sm">
                    <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=RentHome_Payment_HD202610302" 
                         alt="Mã QR Thanh toán" class="w-44 h-44 mx-auto rounded-lg">
                </div>

                <div>
                    <span id="qrHouseNameText" class="text-xs text-slate-500 font-bold block">Phòng Trọ Cao Cấp Nguyên Hồng (NH-302)</span>
                    <span id="qrAmountText" class="text-2xl font-black text-rose-600">3,850,000 VNĐ</span>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl text-xs text-left space-y-1.5 border border-slate-100 font-medium">
                    <p class="flex justify-between"><span class="text-slate-500">Ngân hàng:</span> <strong id="qrBankNameText" class="text-slate-800">MB Bank (Nội địa)</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Số tài khoản:</span> <strong id="qrBankAccountText" class="text-slate-800 font-mono">0912 987 654 888</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Chủ tài khoản:</span> <strong id="qrLandlordNameText" class="text-slate-800">LE THI MAI</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Cú pháp CK:</span> <strong id="qrContentText" class="text-skybrand-600 font-mono">HD-202610-302</strong></p>
                </div>
            </div>

            <button onclick="confirmPaidSuccess()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                <i class="fa-solid fa-circle-check"></i> Tôi đã hoàn tất chuyển khoản
            </button>
        </div>
    </div>


    <!-- Toast Notification Container -->
    <div id="toast" class="fixed bottom-5 right-5 z-50 hidden bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-semibold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
        <span id="toastMessage">Thao tác thành công!</span>
    </div>


    <!-- JavaScript Functions -->
    <script>
        // Data Store for Detailed Invoices (Image 3)
        let invoiceDataMap = {};

        

        function openInvoiceDetailModal(houseKey) {
            const data = invoiceDataMap[houseKey];
            if (!data) return;

            document.getElementById('detailInvoiceCode').innerText = 'Mã hóa đơn: ' + data.code;
            document.getElementById('detailHouseName').innerText = data.houseName;
            document.getElementById('detailLandlordInfo').innerText = data.landlordInfo;
            document.getElementById('detailDueDate').innerText = data.dueDate;
            document.getElementById('detailTotalAmount').innerText = data.total;
            document.getElementById('detailInvoiceTableBody').innerHTML = data.rowsHtml;

            document.getElementById('detailBankShortName').innerText = data.bankShort;
            document.getElementById('detailBankFullName').innerText = data.bankFull;
            document.getElementById('detailBankAccount').innerText = data.bankAccount;
            document.getElementById('detailBankOwner').innerText = data.bankOwner;
            document.getElementById('detailTransferSyntax').innerText = data.code;

            document.getElementById('detailCashNote').innerHTML = `Chủ nhà: <strong>${data.landlordName}</strong> (SĐT: <a href="tel:${data.landlordPhone}" class="text-skybrand-600 font-bold hover:underline">${data.landlordPhone}</a>). Sau khi đóng tiền mặt, chủ nhà sẽ xác nhận hóa đơn hoàn tất trên hệ thống.`;

            document.getElementById('btnTriggerVietQR').onclick = function() {
                closeInvoiceDetailModal();
                openPayQRModal(data.code, data.totalRaw, data.landlordName, data.bankAccount, data.bankShort, data.houseName);
            };

            document.getElementById('btnTriggerContactLandlord').onclick = function() {
                closeInvoiceDetailModal();
                openContactModal(data.landlordName, data.landlordPhone, data.houseName);
            };

            document.getElementById('invoiceDetailModal').classList.remove('hidden');
        }

        function closeInvoiceDetailModal() {
            document.getElementById('invoiceDetailModal').classList.add('hidden');
        }
        // Switch Between Form 1 (Nhà đang thuê) and Form 2 (Thanh toán hóa đơn)
        function switchMainForm(formName) {
            const formNha = document.getElementById('form-nha-dang-thue');
            const formHoaDon = document.getElementById('form-thanh-toan-hoa-don');
            const navNha = document.getElementById('sidebar-nav-nha');
            const navHoaDon = document.getElementById('sidebar-nav-hoadon');

            if (formName === 'nha-dang-thue') {
                formNha.classList.remove('hidden');
                formHoaDon.classList.add('hidden');
                navNha.classList.add('active');
                navHoaDon.classList.remove('active');
            } else if (formName === 'thanh-toan-hoa-don') {
                formNha.classList.add('hidden');
                formHoaDon.classList.remove('hidden');
                navNha.classList.remove('active');
                navHoaDon.classList.add('active');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Toggle User Dropdown Menu
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('user-dropdown-menu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function (e) {
            const menu = document.getElementById('user-dropdown-menu');
            const container = document.getElementById('user-dropdown-container');
            if (menu && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Switch Tabs inside Form 2
        function switchTab(tabName) {
            const tabs = ['pending', 'history', 'liabilities'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                const content = document.getElementById('content-' + t);
                if (t === tabName) {
                    btn.classList.add('active');
                    btn.classList.remove('text-slate-600');
                    content.classList.remove('hidden');
                } else {
                    btn.classList.remove('active');
                    btn.classList.add('text-slate-600');
                    content.classList.add('hidden');
                }
            });
        }

        // Filter Invoices by House
        function filterHouseInvoice(houseId) {
            const cards = document.querySelectorAll('.invoice-house-card');
            const btns = document.querySelectorAll('.house-filter-btn');

            btns.forEach(btn => {
                if (btn.id === 'btn-filter-house-' + houseId) {
                    btn.classList.add('bg-skybrand-600', 'text-white', 'font-bold');
                    btn.classList.remove('bg-white', 'text-slate-700');
                } else {
                    btn.classList.remove('bg-skybrand-600', 'text-white', 'font-bold');
                    btn.classList.add('bg-white', 'text-slate-700');
                }
            });

            cards.forEach(card => {
                if (houseId === 'all' || card.id === 'invoice-card-' + houseId) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        // Contact Modal Logic
        function openContactModal(name, phone, house) {
            document.getElementById('contactLandlordName').innerText = name;
            document.getElementById('contactLandlordPhone').innerText = phone;
            document.getElementById('contactHouseTitle').innerText = house;
            document.getElementById('btnCallDirect').href = 'tel:' + phone;
            document.getElementById('btnZalo').href = 'https://zalo.me/' + phone;
            updateMessageText();
            document.getElementById('contactModal').classList.remove('hidden');
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.add('hidden');
        }

        function updateMessageText() {
            const val = document.getElementById('quickMessageSelect').value;
            document.getElementById('quickMessageArea').value = val;
        }

        function sendQuickMessage() {
            closeContactModal();
            showToast('Đã gửi lời nhắn trực tiếp tới chủ nhà!');
        }

        // Return Request Modal Logic
        function openReturnModal(houseName, roomCode, landlord, deposit) {
            document.getElementById('returnHouseName').value = houseName + ' (Mã: ' + roomCode + ')';
            document.getElementById('returnLandlord').value = landlord;
            document.getElementById('returnDeposit').value = deposit;
            
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 15);
            document.getElementById('returnDate').value = tomorrow.toISOString().split('T')[0];

            document.getElementById('returnModal').classList.remove('hidden');
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
        }

        function handleReturnSubmit(e) {
            e.preventDefault();
            closeReturnModal();
            showToast('Đã gửi yêu cầu trả nhà thành công! Chủ nhà sẽ nhận được thông tin và tiến hành xác nhận chuyển trạng thái phòng thành "Còn trống".');
        }

        // Pay QR Modal Logic
        function openPayQRModal(code, amount, landlordName, bankAccount, bankName, houseName) {
            document.getElementById('qrContentText').innerText = code;
            document.getElementById('qrAmountText').innerText = amount + ' VNĐ';
            if (landlordName) document.getElementById('qrLandlordNameText').innerText = landlordName;
            if (bankAccount) document.getElementById('qrBankAccountText').innerText = bankAccount;
            if (bankName) document.getElementById('qrBankNameText').innerText = bankName;
            if (houseName) document.getElementById('qrHouseNameText').innerText = houseName;

            const qrData = encodeURIComponent('RentHome_' + code + '_' + amount.replace(/,/g, ''));
            document.getElementById('qrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + qrData;

            document.getElementById('payQRModal').classList.remove('hidden');
        }

        function closePayQRModal() {
            document.getElementById('payQRModal').classList.add('hidden');
        }

        function confirmPaidSuccess() {
            closePayQRModal();
            showToast('Hệ thống đã ghi nhận thông báo chuyển khoản của bạn. Chủ nhà sẽ đối soát và cập nhật hóa đơn!');
        }

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>
