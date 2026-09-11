<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tạo Hóa Đơn Hàng Tháng & Chốt Điện Nước - RentHome</title>

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
                            900: '#0b132a',
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
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

        @media print {
            header, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-full-width {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-bold text-skybrand-600 uppercase tracking-widest mt-0.5">Quản Lý Vận Hành Chủ Nhà</span>
                </div>
            </a>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ url('qlvan_hanh/Room_list') }}" 
                   class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-2 border border-slate-200 transition-all">
                    <i class="fa-solid fa-arrow-left text-slate-500"></i>
                    <span>Danh Sách Phòng</span>
                </a>

                <a href="{{ url('/') }}" 
                   class="py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md transition-all">
                    <i class="fa-solid fa-house"></i>
                    <span>Trở Về Trang Chủ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 print-full-width">

        <!-- HEADER BANNER -->
        <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-skybrand-500/20 border border-skybrand-400/30 text-skybrand-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-bolt-lightning"></i> Quy Trình Chốt Điện Nước & Tạo Hóa Đơn Hàng Tháng
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Form Tạo Hóa Đơn Tiền Nhà & Điện Nước
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base max-w-2xl font-normal">
                        Tính toán chi tiết các khoản phí tháng (tiền phòng, chốt chỉ số điện, nước, các phí dịch vụ wifi, rác, gửi xe) để xuất hóa đơn thanh toán cho người thuê.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 no-print">
                    <button onclick="window.print()" class="py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-2 border border-slate-700 transition-all">
                        <i class="fa-solid fa-print"></i>
                        <span>In / Xuất Hóa Đơn</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- BANNER THÔNG BÁO THÀNH CÔNG NẾU CÓ -->
        <div id="savedBannerContainer"></div>

        <!-- FORM LAYOUT (LEFT: FORM INPUTS | RIGHT: REALTIME SUMMARY) -->
        <form id="invoiceForm" onsubmit="handleInvoiceSubmit(event)" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: INPUT SECTIONS (8 COLS) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. PHẦN THÔNG TIN CHUNG HỢP ĐỒNG & KỲ THANH TOÁN -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-skybrand-50 text-skybrand-600 flex items-center justify-center font-bold text-lg border border-skybrand-100">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">1. Thông Tin Chung Hợp Đồng & Khách Thuê</h2>
                                <p class="text-xs text-slate-500">Chọn phòng để hệ thống tự động điền giá phòng, khách thuê và chỉ số cũ</p>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-extrabold text-xs border border-slate-200">
                            Mã HĐ: <span id="display_contract_id" class="text-skybrand-600">HD-2026-302</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Chọn phòng cần tạo hóa đơn (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-door-closed absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select id="room_select" onchange="onRoomChange(this.value)"
                                        class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none transition-all">
                                    <option value="NH-302">Phòng Trọ Cao Cấp Nguyên Hồng – Phòng 302 (Mã: NH-302)</option>
                                    <option value="VH-1208">Căn Hộ Vinhomes Central Park – Landmark 2 (Mã: VH-1208)</option>
                                    <option value="MT-1504">Căn Hộ Masteri Thảo Điền – Tháp T3-1504 (Mã: MT-1504)</option>
                                    <option value="KVC-201">Phòng Trọ Studio Kha Vạn Cân – Phòng 201 (Mã: KVC-201)</option>
                                    <option value="NX-101">Nhà Nguyên Căn Nguyễn Xí – Số 48/15 (Mã: NX-101)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tên khách thuê đại diện (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="tenant_name" readonly value="Nguyễn Văn An (SĐT: 0912 345 678)" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-100/80 font-bold text-slate-800 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kỳ hóa đơn / Tháng tính tiền (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-days absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="billing_period" value="Tháng 10/2026" oninput="calculateInvoice()"
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Hạn thanh toán hóa đơn (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-clock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" id="due_date" value="2026-10-05" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PHẦN CHI TIẾT CÁC KHOẢN CHI PHÍ HÀNG THÁNG -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg border border-rose-100">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">2. Các Khoản Chi Phí Tháng (Tiền Phòng, Điện, Nước & Dịch Vụ)</h2>
                                <p class="text-xs text-slate-500">Nhập chỉ số điện nước mới và các phí phát sinh để tính toán tự động</p>
                            </div>
                        </div>

                        <span class="text-xs text-slate-400 italic">Tự động tính toán real-time</span>
                    </div>

                    <!-- MỤC A: TIỀN PHÒNG -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-house-user text-brand-600"></i> A. Tiền Phòng Tháng Này
                            </h3>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600">
                                <input type="checkbox" id="full_month_check" checked onchange="toggleFullMonth(this.checked)" class="rounded text-brand-600 focus:ring-brand-500 w-4 h-4">
                                <span>Tính trọn 1 tháng</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Giá thuê (VNĐ/tháng):</label>
                                <input type="number" id="room_rent_price" value="3500000" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>

                            <div id="days_container" class="opacity-50 pointer-events-none">
                                <label class="font-bold text-slate-600 block mb-1">Số ngày ở trong tháng:</label>
                                <input type="number" id="days_stayed" value="30" oninput="calculateInvoice()" min="1" max="31"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền phòng:</label>
                                <div class="px-3 py-2 rounded-xl bg-brand-50 border border-brand-200 font-extrabold text-brand-700 text-right text-sm" id="room_rent_total_display">
                                    3,500,000 VNĐ
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MỤC B: TIỀN ĐIỆN HÀNG THÁNG (CHỐT ĐIỆN) -->
                    <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-bolt text-amber-500"></i> B. Tiền Điện Hàng Tháng (Chốt Chỉ Số Cũ & Mới)
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold" id="elec_usage_badge">
                                Tiêu thụ: 80 kWh
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số điện CŨ (kWh):</label>
                                <input type="number" id="elec_old" value="1420" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số điện MỚI (kWh):</label>
                                <input type="number" id="elec_new" value="1500" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-amber-50/50 font-extrabold text-amber-900 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Đơn giá (VNĐ/kWh):</label>
                                <input type="number" id="elec_rate" value="3500" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền điện:</label>
                                <div class="px-3 py-2 rounded-xl bg-amber-100/80 border border-amber-200 font-extrabold text-amber-800 text-right text-sm" id="elec_total_display">
                                    280,000 VNĐ
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MỤC C: TIỀN NƯỚC HÀNG THÁNG (CHỐT NƯỚC) -->
                    <div class="p-4 rounded-2xl bg-skybrand-50/50 border border-skybrand-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-droplet text-skybrand-500"></i> C. Tiền Nước Hàng Tháng (Chốt Chỉ Số Cũ & Mới)
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-skybrand-100 text-skybrand-800 text-[11px] font-bold" id="water_usage_badge">
                                Tiêu thụ: 10 m³
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số nước CŨ (m³):</label>
                                <input type="number" id="water_old" value="45" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số nước MỚI (m³):</label>
                                <input type="number" id="water_new" value="55" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-skybrand-300 bg-skybrand-50/50 font-extrabold text-skybrand-900 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Đơn giá (VNĐ/m³):</label>
                                <input type="number" id="water_rate" value="15000" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền nước:</label>
                                <div class="px-3 py-2 rounded-xl bg-skybrand-100/80 border border-skybrand-200 font-extrabold text-skybrand-800 text-right text-sm" id="water_total_display">
                                    150,000 VNĐ
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MỤC D: CÁC PHÍ DỊCH VỤ VÀ KHẤU TRỪ BỔ SUNG -->
                    <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-200/80 space-y-3">
                        <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-concierge-bell text-indigo-500"></i> D. Phí Dịch Vụ & Phụ Thu Bổ Sung
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Internet / Wifi (VNĐ):</label>
                                <input type="number" id="fee_wifi" value="100000" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Vệ sinh / Rác (VNĐ):</label>
                                <input type="number" id="fee_garbage" value="50000" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Phí giữ xe / Khác (VNĐ):</label>
                                <input type="number" id="fee_parking" value="100000" oninput="calculateInvoice()" 
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Khấu trừ / Giảm giá (VNĐ):</label>
                                <input type="number" id="discount_amount" value="0" oninput="calculateInvoice()" placeholder="VD: 50000"
                                       class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/50 font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. PHƯƠNG THỨC THANH TOÁN & QR CODE -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg border border-emerald-100">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">3. Thông Tin Thanh Toán & Mã VietQR Transfer</h2>
                            <p class="text-xs text-slate-500">Mã QR tự động sinh theo số tiền thực tế để khách thuê quét chuyển khoản tức thì</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                        <div class="space-y-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Ngân hàng thụ hưởng:</label>
                                <input type="text" value="MB Bank (Ngân Hàng Quân Đội)" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-800">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Số tài khoản & Chủ tài khoản:</label>
                                <input type="text" value="9999 8888 999 - NGUYEN VAN CHU NHA" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-800">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Nội dung chuyển khoản mặc định:</label>
                                <input type="text" id="transfer_memo" value="NH302 THANG 10/2026" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-skybrand-700">
                            </div>
                        </div>

                        <!-- LIVE QR CODE PREVIEW -->
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 flex flex-col items-center justify-center text-center space-y-2">
                            <div class="w-32 h-32 bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex items-center justify-center relative group">
                                <img id="qr_code_img" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=RentHome_Invoice_NH302_4180000" alt="VietQR Code" class="w-full h-full object-contain">
                            </div>
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved text-emerald-500"></i> Quét mã VietQR chuyển khoản tự động
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: REALTIME SUMMARY CARD (4 COLS - STICKY) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-6 sticky top-24">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-emerald-600"></i> Tóm Tắt Hóa Đơn
                        </h2>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider">
                            Real-time
                        </span>
                    </div>

                    <!-- BREAKDOWN LIST -->
                    <div class="space-y-3 text-xs border-b border-slate-100 pb-4">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>+ Tiền phòng:</span>
                            <strong class="text-slate-800" id="summary_room">3,500,000 VNĐ</strong>
                        </div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span>+ Tiền điện (<span id="summary_elec_kwh">80</span> kWh):</span>
                            <strong class="text-amber-700" id="summary_elec">280,000 VNĐ</strong>
                        </div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span>+ Tiền nước (<span id="summary_water_m3">10</span> m³):</span>
                            <strong class="text-skybrand-700" id="summary_water">150,000 VNĐ</strong>
                        </div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span>+ Phí dịch vụ (Wifi, Rác, Xe):</span>
                            <strong class="text-indigo-700" id="summary_services">250,000 VNĐ</strong>
                        </div>

                        <div class="flex items-center justify-between text-slate-600" id="summary_discount_row">
                            <span>- Khấu trừ / Giảm giá:</span>
                            <strong class="text-emerald-600" id="summary_discount">0 VNĐ</strong>
                        </div>
                    </div>

                    <!-- CHỌN TRẠNG THÁI HÓA ĐƠN -->
                    <div class="space-y-2">
                        <label class="font-bold text-slate-700 text-xs block">Trạng thái thu tiền (*):</label>
                        <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-rose-200 bg-rose-50/50 text-rose-700 cursor-pointer has-[:checked]:ring-2 has-[:checked]:ring-rose-500">
                                <input type="radio" name="payment_status" value="unpaid" checked onchange="updateStatusTheme('unpaid')" class="text-rose-600 focus:ring-rose-500">
                                <span>Chưa Thu Tiền</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/50 text-emerald-700 cursor-pointer has-[:checked]:ring-2 has-[:checked]:ring-emerald-500">
                                <input type="radio" name="payment_status" value="paid" onchange="updateStatusTheme('paid')" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Đã Thu Tiền</span>
                            </label>
                        </div>
                    </div>

                    <!-- KẾT QUẢ TỔNG TIỀN -->
                    <div id="grandTotalBox" class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-1 transition-all">
                        <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block" id="grandTotalLabel">KẾT QUẢ: TỔNG TIỀN CẦN THANH TOÁN</span>
                        <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight" id="grandTotalAmount">
                            4,180,000 VNĐ
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium" id="grandTotalDesc">
                            Tổng tiền hóa đơn tháng 10/2026 gửi người thuê.
                        </p>
                    </div>

                    <!-- NÚT ACTION LƯU & PHÁT HÀNH -->
                    <div class="space-y-2.5 pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-glow hover:shadow-lg transition-all">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>XÁC NHẬN TẠO & PHÁT HÀNH HÓA ĐƠN</span>
                        </button>

                        <button type="button" onclick="sendZaloNotification()"
                                class="w-full py-2.5 px-4 rounded-xl bg-skybrand-500 hover:bg-skybrand-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                            <i class="fa-solid fa-comment-sms"></i>
                            <span>Gửi Zalo / Thông Báo Cho Khách</span>
                        </button>
                    </div>

                </div>
            </div>

        </form>

    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 mt-12 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-6 h-6 object-contain">
                <span class="font-bold text-white text-sm">RentHome Việt Nam</span>
                <span>© 2026 Hệ thống quản lý vận hành chủ nhà.</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="#" class="hover:text-white transition-colors">Điều khoản</a>
                <a href="#" class="hover:text-white transition-colors">Bảo mật</a>
                <a href="#" class="hover:text-white transition-colors">Hỗ trợ 24/7</a>
            </div>
        </div>
    </footer>

    <!-- SUCCESS MODAL (BƯỚC TIẾP THEO SAU KHU TẠO HÓA ĐƠN) -->
    <div id="successModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100 transform transition-all animate-in fade-in zoom-in duration-200">
            
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto border-4 border-emerald-50">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Tạo & Phát Hành Hóa Đơn Thành Công!</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Hóa đơn tháng cho <strong id="modal_room_title" class="text-slate-800">Phòng 302</strong> đã được cập nhật vào danh sách thu tiền.
                </p>
            </div>

            <!-- MODAL DETAILS SUMMARY -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Khách thuê:</span>
                    <strong id="modal_tenant" class="text-slate-800">Nguyễn Văn An</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kỳ hóa đơn:</span>
                    <strong id="modal_period" class="text-slate-800">Tháng 10/2026</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tổng tiền hóa đơn:</span>
                    <strong id="modal_total" class="text-brand-600 font-extrabold text-sm">4,180,000 VNĐ</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Trạng thái:</span>
                    <span id="modal_status_badge" class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[11px]">Chưa Thu Tiền</span>
                </div>
            </div>

            <!-- ACTION STEPS FOR USER -->
            <div class="space-y-3 pt-2">
                <p class="text-xs font-bold text-slate-700">Các bước thao tác tiếp theo:</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <button onclick="sendZaloNotification()" class="py-2.5 px-3 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Gửi Zalo Cho Khách</span>
                    </button>

                    <button onclick="window.print()" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-print"></i>
                        <span>In File Hóa Đơn PDF</span>
                    </button>
                </div>

                <a href="{{ url('qlvan_hanh/Room_list') }}" class="w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Trở Về Danh Sách Quản Lý Phòng</span>
                </a>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // DATA DICTIONARY FOR ROOMS
        const roomData = {
            'NH-302': {
                contractId: 'HD-2026-302',
                roomName: 'Phòng Trọ Cao Cấp Nguyên Hồng (Phòng 302 - Tòa A)',
                tenantName: 'Nguyễn Văn An (SĐT: 0912 345 678)',
                rent: 3500000,
                elecOld: 1420,
                elecNew: 1500,
                elecRate: 3500,
                waterOld: 45,
                waterNew: 55,
                waterRate: 15000,
                wifi: 100000,
                garbage: 50000,
                parking: 100000,
                memo: 'NH302 THANG 10/2026'
            },
            'VH-1208': {
                contractId: 'HD-2026-VH',
                roomName: 'Căn Hộ Vinhomes Central Park – Landmark 2 (Căn 12.08)',
                tenantName: 'Trần Thị Minh (SĐT: 0908 777 666)',
                rent: 7500000,
                elecOld: 2130,
                elecNew: 2250,
                elecRate: 3500,
                waterOld: 100,
                waterNew: 115,
                waterRate: 18000,
                wifi: 150000,
                garbage: 50000,
                parking: 150000,
                memo: 'VH1208 THANG 10/2026'
            },
            'MT-1504': {
                contractId: 'HD-2026-MT',
                roomName: 'Căn Hộ Masteri Thảo Điền – Tháp T3-1504',
                tenantName: 'Lê Hoàng Nam (SĐT: 0938 111 222)',
                rent: 10500000,
                elecOld: 1780,
                elecNew: 1920,
                elecRate: 3500,
                waterOld: 80,
                waterNew: 92,
                waterRate: 18000,
                wifi: 150000,
                garbage: 50000,
                parking: 200000,
                memo: 'MT1504 THANG 10/2026'
            },
            'KVC-201': {
                contractId: 'HD-2026-KVC',
                roomName: 'Phòng Trọ Studio Kha Vạn Cân – Phòng 201',
                tenantName: 'Phạm Văn Hải (SĐT: 0977 444 333)',
                rent: 4200000,
                elecOld: 950,
                elecNew: 1020,
                elecRate: 3500,
                waterOld: 35,
                waterNew: 42,
                waterRate: 15000,
                wifi: 100000,
                garbage: 50000,
                parking: 100000,
                memo: 'KVC201 THANG 10/2026'
            },
            'NX-101': {
                contractId: 'HD-2026-NX',
                roomName: 'Nhà Nguyên Căn Nguyễn Xí – Số 48/15 Nguyễn Xí',
                tenantName: 'Vũ Thị Lan (SĐT: 0903 888 999)',
                rent: 12000000,
                elecOld: 3100,
                elecNew: 3280,
                elecRate: 3500,
                waterOld: 150,
                waterNew: 175,
                waterRate: 18000,
                wifi: 200000,
                garbage: 50000,
                parking: 0,
                memo: 'NX101 THANG 10/2026'
            }
        };

        // HELPER FORMAT CURRENCY
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
        }

        // ROOM SWITCH HANDLER
        function onRoomChange(roomCode) {
            const data = roomData[roomCode];
            if (!data) return;

            document.getElementById('display_contract_id').innerText = data.contractId;
            document.getElementById('tenant_name').value = data.tenantName;
            document.getElementById('room_rent_price').value = data.rent;
            document.getElementById('elec_old').value = data.elecOld;
            document.getElementById('elec_new').value = data.elecNew;
            document.getElementById('elec_rate').value = data.elecRate;
            document.getElementById('water_old').value = data.waterOld;
            document.getElementById('water_new').value = data.waterNew;
            document.getElementById('water_rate').value = data.waterRate;
            document.getElementById('fee_wifi').value = data.wifi;
            document.getElementById('fee_garbage').value = data.garbage;
            document.getElementById('fee_parking').value = data.parking;
            document.getElementById('transfer_memo').value = data.memo;

            calculateInvoice();
        }

        // FULL MONTH CHECKBOX TOGGLE
        function toggleFullMonth(isFull) {
            const container = document.getElementById('days_container');
            if (isFull) {
                container.classList.add('opacity-50', 'pointer-events-none');
                document.getElementById('days_stayed').value = 30;
            } else {
                container.classList.remove('opacity-50', 'pointer-events-none');
            }
            calculateInvoice();
        }

        // REAL-TIME INVOICE CALCULATION ENGINE
        function calculateInvoice() {
            // 1. Tiền phòng
            const isFullMonth = document.getElementById('full_month_check').checked;
            const rentPrice = parseFloat(document.getElementById('room_rent_price').value) || 0;
            const days = parseFloat(document.getElementById('days_stayed').value) || 30;
            let roomTotal = isFullMonth ? rentPrice : Math.round((rentPrice / 30) * days);

            document.getElementById('room_rent_total_display').innerText = formatVND(roomTotal);
            document.getElementById('summary_room').innerText = formatVND(roomTotal);

            // 2. Tiền điện
            const elecOld = parseFloat(document.getElementById('elec_old').value) || 0;
            const elecNew = parseFloat(document.getElementById('elec_new').value) || 0;
            const elecRate = parseFloat(document.getElementById('elec_rate').value) || 0;
            const elecUsage = Math.max(0, elecNew - elecOld);
            const elecTotal = elecUsage * elecRate;

            document.getElementById('elec_usage_badge').innerText = `Tiêu thụ: ${elecUsage} kWh`;
            document.getElementById('elec_total_display').innerText = formatVND(elecTotal);
            document.getElementById('summary_elec_kwh').innerText = elecUsage;
            document.getElementById('summary_elec').innerText = formatVND(elecTotal);

            // 3. Tiền nước
            const waterOld = parseFloat(document.getElementById('water_old').value) || 0;
            const waterNew = parseFloat(document.getElementById('water_new').value) || 0;
            const waterRate = parseFloat(document.getElementById('water_rate').value) || 0;
            const waterUsage = Math.max(0, waterNew - waterOld);
            const waterTotal = waterUsage * waterRate;

            document.getElementById('water_usage_badge').innerText = `Tiêu thụ: ${waterUsage} m³`;
            document.getElementById('water_total_display').innerText = formatVND(waterTotal);
            document.getElementById('summary_water_m3').innerText = waterUsage;
            document.getElementById('summary_water').innerText = formatVND(waterTotal);

            // 4. Các phí dịch vụ
            const wifi = parseFloat(document.getElementById('fee_wifi').value) || 0;
            const garbage = parseFloat(document.getElementById('fee_garbage').value) || 0;
            const parking = parseFloat(document.getElementById('fee_parking').value) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const serviceTotal = wifi + garbage + parking;

            document.getElementById('summary_services').innerText = formatVND(serviceTotal);
            document.getElementById('summary_discount').innerText = formatVND(discount);

            // 5. Tổng cộng
            const grandTotal = roomTotal + elecTotal + waterTotal + serviceTotal - discount;
            const formattedGrandTotal = formatVND(grandTotal);

            document.getElementById('grandTotalAmount').innerText = formattedGrandTotal;

            // Update QR code data
            const roomCode = document.getElementById('room_select').value;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=RentHome_${roomCode}_${grandTotal}`;
            document.getElementById('qr_code_img').src = qrUrl;
        }

        // THEME SWITCH BASED ON PAYMENT STATUS
        function updateStatusTheme(status) {
            const box = document.getElementById('grandTotalBox');
            const label = document.getElementById('grandTotalLabel');
            const amount = document.getElementById('grandTotalAmount');

            if (status === 'paid') {
                box.className = 'p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-1 transition-all';
                label.className = 'text-xs font-bold text-emerald-600 uppercase tracking-wider block';
                amount.className = 'text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight';
            } else {
                box.className = 'p-5 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-1 transition-all';
                label.className = 'text-xs font-bold text-rose-600 uppercase tracking-wider block';
                amount.className = 'text-2xl sm:text-3xl font-black text-rose-600 tracking-tight';
            }
        }

        // FORM SUBMIT HANDLER
        function handleInvoiceSubmit(e) {
            e.preventDefault();

            const roomCode = document.getElementById('room_select').value;
            const selectedOptText = document.getElementById('room_select').options[document.getElementById('room_select').selectedIndex].text;
            const tenant = document.getElementById('tenant_name').value.split('(')[0].trim();
            const period = document.getElementById('billing_period').value;
            const total = document.getElementById('grandTotalAmount').innerText;
            const statusVal = document.querySelector('input[name="payment_status"]:checked').value;

            // Fill Modal
            document.getElementById('modal_room_title').innerText = selectedOptText;
            document.getElementById('modal_tenant').innerText = tenant;
            document.getElementById('modal_period').innerText = period;
            document.getElementById('modal_total').innerText = total;

            const modalBadge = document.getElementById('modal_status_badge');
            if (statusVal === 'paid') {
                modalBadge.className = 'px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]';
                modalBadge.innerText = 'Đã Thu Tiền';
            } else {
                modalBadge.className = 'px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[11px]';
                modalBadge.innerText = 'Chưa Thu Tiền';
            }

            // Show Top Banner notification
            const bannerContainer = document.getElementById('savedBannerContainer');
            bannerContainer.innerHTML = `
                <div class="p-4 rounded-2xl bg-emerald-500 text-white border border-emerald-600 shadow-lg flex items-center justify-between gap-4 animate-in fade-in slide-in-from-top duration-300">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                        <div>
                            <strong class="font-extrabold block text-sm">Đã lưu & phát hành hóa đơn thành công!</strong>
                            <span class="text-xs text-emerald-100">Hóa đơn kỳ ${period} của ${tenant} đã sẵn sàng để gửi khách.</span>
                        </div>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-100 hover:text-white font-bold text-sm px-2">✕</button>
                </div>
            `;

            // Open Modal
            document.getElementById('successModal').classList.remove('hidden');
        }

        // SEND ZALO NOTIFICATION FUNCTION
        function sendZaloNotification() {
            const tenant = document.getElementById('tenant_name').value;
            const period = document.getElementById('billing_period').value;
            const total = document.getElementById('grandTotalAmount').innerText;
            const roomText = document.getElementById('room_select').options[document.getElementById('room_select').selectedIndex].text;

            const msg = `[RentHome] Kính gửi anh/chị ${tenant},\nRentHome xin gửi thông báo hóa đơn ${period} cho ${roomText}.\n- Tổng tiền thanh toán: ${total}.\n- Nội dung CK: ${document.getElementById('transfer_memo').value}.\nXin cảm ơn!`;
            
            navigator.clipboard.writeText(msg).then(() => {
                alert('Đã sao chép nội dung tin nhắn Zalo gửi khách thuê!\n\nNội dung:\n' + msg);
            }).catch(() => {
                alert('Nội dung tin nhắn Zalo:\n\n' + msg);
            });
        }

        // ON INITIAL LOAD: READ URL QUERY PARAMETER ?room=
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const roomParam = params.get('room');
            if (roomParam && roomData[roomParam]) {
                document.getElementById('room_select').value = roomParam;
                onRoomChange(roomParam);
            } else {
                calculateInvoice();
            }
        });
    </script>
</body>

</html>
