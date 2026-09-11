<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chốt Công Nợ & Thanh Lý Hợp Đồng - RentHome</title>

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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-400/30 text-rose-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-calculator"></i> Quy Trình Thanh Lý & Chốt Công Nợ
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Form Chốt Công Nợ Thanh Lý Hợp Đồng
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base max-w-2xl font-normal">
                        Tính toán chi tiết các khoản phát sinh tháng cuối (tiền phòng, điện, nước, tài sản hư hỏng, vi phạm) cấn trừ với tiền cọc để chốt số tiền hoàn trả hoặc thu thêm.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0 no-print">
                    <button onclick="window.print()" class="py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-2 border border-slate-700 transition-all">
                        <i class="fa-solid fa-print"></i>
                        <span>In / Xuất Biên Bản</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- FORM LAYOUT (LEFT: FORM INPUTS | RIGHT: REALTIME SUMMARY) -->
        <form id="liquidationForm" onsubmit="handleLiquidationSubmit(event)" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: INPUT SECTIONS (8 COLS) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. PHẦN THÔNG TIN CHUNG (TỰ ĐỘNG ĐIỀN TỪ DATABASE) -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-5">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-skybrand-50 text-skybrand-600 flex items-center justify-center font-bold text-lg border border-skybrand-100">
                            <i class="fa-solid fa-building-circle-check"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">1. Thông Tin Chung Hợp Đồng & Khách Thuê</h2>
                            <p class="text-xs text-slate-500">Thông tin được trích xuất tự động từ hệ thống quản lý dữ liệu</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Mã phòng / Tên phòng (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-door-closed absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="room_name" readonly value="Phòng Trọ Cao Cấp Nguyên Hồng (Phòng 302 - Tòa A)" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-100/80 font-bold text-slate-800 focus:outline-none">
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
                            <label class="font-bold text-slate-700 block mb-1">Ngày bắt đầu thuê (Hợp đồng):</label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-day absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" id="start_date" readonly value="2026-06-01" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-100/80 font-medium text-slate-700 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1 text-rose-600">Ngày trả nhà thực tế (Admin chọn) (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-check absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-500"></i>
                                <input type="date" id="end_date" value="2026-10-15" onchange="calculateAll()" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border-2 border-rose-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 font-bold text-slate-900 bg-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PHẦN CÁC KHOẢN KHÁCH CẦN TRẢ (PHÁT SINH THÁNG CUỐI) -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-6">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg border border-rose-100">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">2. Các Khoản Khách CẦN TRẢ (Phát Sinh Tháng Cuối)</h2>
                            <p class="text-xs text-slate-500">Các chi phí dịch vụ, tiền phòng nợ, tài sản hư hỏng & tiền phạt (nếu có)</p>
                        </div>
                    </div>

                    <!-- 2.1 Tiền phòng tháng cuối / Nợ tiền phòng -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-house-user text-skybrand-600"></i>
                                Tiền phòng nợ / Tiền phòng tháng cuối
                            </h3>
                            <label class="inline-flex items-center gap-2 text-xs cursor-pointer font-bold text-slate-600">
                                <input type="checkbox" id="is_prorated" onchange="calculateRent()" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500">
                                <span>Tính theo số ngày ở thực tế (VD: ở giữa tháng)</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-500 block mb-1 font-semibold">Giá thuê (VNĐ/tháng):</span>
                                <div class="relative">
                                    <input type="number" id="base_rent" value="3500000" oninput="calculateRent()" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-slate-200 font-bold text-slate-800">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                                </div>
                                <span id="base_rent_display_text" class="text-[11px] font-bold text-brand-600 mt-1 block">Mệnh giá quy đổi: 3.500.000 VNĐ</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block mb-1 font-semibold">Số ngày ở trong tháng cuối:</span>
                                <input type="number" id="days_stayed" value="15" oninput="calculateRent()" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-700 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-500 block mb-1 font-bold text-rose-600">Tiền phòng thực tế</span>
                                <div class="relative">
                                    <input type="number" id="rent_amount" value="3500000" oninput="calculateAll()" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-slate-300 font-black text-rose-600 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-rose-500">VNĐ</span>
                                </div>
                                <span id="rent_amount_display_text" class="text-[11px] font-bold text-rose-600 mt-1 block">Mệnh giá quy đổi: 3.500.000 VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2.2 Tiền Điện Tháng Cuối -->
                    <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-bolt text-amber-500"></i>
                                Tiền Điện Tháng Cuối (Chỉ Số Cũ & Mới)
                            </h3>
                            <span class="text-[11px] font-bold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-full" id="electricity_kwh_display">Tiêu thụ: 80 kWh</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Chỉ số điện CŨ (kWh):</span>
                                <input type="number" id="elec_old" value="1420" oninput="calculateElectricity()" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Chỉ số điện MỚI (kWh):</span>
                                <input type="number" id="elec_new" value="1500" oninput="calculateElectricity()" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Đơn giá điện (VNĐ/kWh):</span>
                                <div class="relative">
                                    <input type="number" id="elec_rate" value="3500" oninput="calculateElectricity()" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                                </div>
                                <span id="elec_rate_display_text" class="text-[11px] font-bold text-amber-700 mt-1 block">Mệnh giá: 3.500 VNĐ / kWh</span>
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-bold text-amber-700">Thành tiền điện:</span>
                                <div class="relative">
                                    <input type="number" id="elec_amount" value="280000" readonly 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-amber-200 font-black text-amber-700 bg-amber-100/60">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-700">VNĐ</span>
                                </div>
                                <span id="elec_amount_display_text" class="text-[11px] font-bold text-amber-700 mt-1 block">Mệnh giá quy đổi: 280.000 VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2.3 Tiền Nước Tháng Cuối -->
                    <div class="p-4 rounded-2xl bg-sky-50/50 border border-sky-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-droplet text-skybrand-500"></i>
                                Tiền Nước Tháng Cuối (Chỉ Số Cũ & Mới)
                            </h3>
                            <span class="text-[11px] font-bold text-skybrand-700 bg-sky-100 px-2.5 py-0.5 rounded-full" id="water_m3_display">Tiêu thụ: 5 m³</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Chỉ số nước CŨ (m³):</span>
                                <input type="number" id="water_old" value="50" oninput="calculateWater()" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Chỉ số nước MỚI (m³):</span>
                                <input type="number" id="water_new" value="55" oninput="calculateWater()" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Đơn giá nước (VNĐ/m³):</span>
                                <div class="relative">
                                    <input type="number" id="water_rate" value="15000" oninput="calculateWater()" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-slate-200 font-semibold text-slate-800 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                                </div>
                                <span id="water_rate_display_text" class="text-[11px] font-bold text-skybrand-700 mt-1 block">Mệnh giá: 15.000 VNĐ / m³</span>
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-bold text-skybrand-700">Thành tiền nước:</span>
                                <div class="relative">
                                    <input type="number" id="water_amount" value="75000" readonly 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-sky-200 font-black text-skybrand-700 bg-sky-100/60">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-skybrand-700">VNĐ</span>
                                </div>
                                <span id="water_amount_display_text" class="text-[11px] font-bold text-skybrand-700 mt-1 block">Mệnh giá quy đổi: 75.000 VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2.4 Tiền Dịch Vụ Khác (Mạng, Rác, Vệ sinh) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-wifi text-indigo-500"></i>
                            Tiền Dịch Vụ Khác (Internet, Rác, Thang Máy...)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Mô tả dịch vụ tính tiền:</span>
                                <input type="text" value="Internet Wifi + Phí vệ sinh rác tháng 10" 
                                       class="w-full p-2.5 rounded-xl border border-slate-200 font-medium text-slate-700 bg-white">
                            </div>
                            <div>
                                <span class="text-slate-600 block mb-1 font-bold text-slate-800">Tổng phí dịch vụ (VNĐ):</span>
                                <div class="relative">
                                    <input type="number" id="service_amount" value="150000" oninput="calculateAll()" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                                </div>
                                <span id="service_amount_display_text" class="text-[11px] font-bold text-slate-800 mt-1 block">Mệnh giá quy đổi: 150.000 VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2.5 Tiền Đền Bù Tài Sản Hư Hỏng -->
                    <div class="p-4 rounded-2xl bg-rose-50/40 border border-rose-200 space-y-3">
                        <h3 class="font-extrabold text-rose-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                            Tiền Đền Bù Tài Sản Hư Hỏng (Nếu Có)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Số tiền đền bù (VNĐ):</span>
                                <div class="relative">
                                    <input type="number" id="damage_amount" value="200000" oninput="calculateAll()" placeholder="0" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-rose-200 font-bold text-rose-600 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-rose-500">VNĐ</span>
                                </div>
                                <span id="damage_amount_display_text" class="text-[11px] font-bold text-rose-600 mt-1 block">Mệnh giá quy đổi: 200.000 VNĐ</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-600 block mb-1 font-semibold">Ghi chú lý do đền bù tài sản:</span>
                                <input type="text" id="damage_reason" value="Làm hỏng điều hòa, vỡ gương" placeholder="Ví dụ: Làm hỏng điều hòa, vỡ gương..." 
                                       class="w-full p-2.5 rounded-xl border border-rose-200 font-medium text-slate-700 bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- 2.6 Tiền Phạt Vi Phạm Hợp Đồng -->
                    <div class="p-4 rounded-2xl bg-orange-50/40 border border-orange-200 space-y-3">
                        <h3 class="font-extrabold text-orange-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-gavel text-orange-500"></i>
                            Tiền Phạt Vi Phạm Hợp Đồng (Ví dụ: Phá hợp đồng trước thời hạn)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-600 block mb-1 font-semibold">Số tiền phạt (VNĐ):</span>
                                <div class="relative">
                                    <input type="number" id="penalty_amount" value="0" oninput="calculateAll()" placeholder="0" 
                                           class="w-full pr-14 p-2.5 rounded-xl border border-orange-200 font-bold text-orange-600 bg-white">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-orange-500">VNĐ</span>
                                </div>
                                <span id="penalty_amount_display_text" class="text-[11px] font-bold text-orange-600 mt-1 block">Mệnh giá quy đổi: 0 VNĐ</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-600 block mb-1 font-semibold">Ghi chú lý do vi phạm:</span>
                                <input type="text" id="penalty_reason" value="" placeholder="Ví dụ: Phạt 1 tháng tiền phòng do dọn đi trước thời hạn cam kết" 
                                       class="w-full p-2.5 rounded-xl border border-orange-200 font-medium text-slate-700 bg-white">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. PHẦN CÁC KHOẢN KHÁCH ĐÃ ĐÓNG (CẤN TRỪ) -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-5">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg border border-emerald-100">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">3. Các Khoản Khách ĐÃ ĐÓNG (Cấn Trừ)</h2>
                            <p class="text-xs text-slate-500">Số tiền cọc ban đầu đang giữ từ hợp đồng cho thuê</p>
                        </div>
                    </div>

                    <div class="text-xs">
                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200">
                            <label class="font-extrabold text-emerald-800 block mb-1">
                                <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> Tiền cọc đang giữ (Từ hợp đồng ban đầu) (*):
                            </label>
                            <div class="relative">
                                <input type="number" id="deposit_amount" value="3500000" oninput="calculateAll()" 
                                       class="w-full pr-14 p-2.5 rounded-xl border border-emerald-300 font-black text-emerald-700 text-sm bg-white">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-600">VNĐ</span>
                            </div>
                            <span id="deposit_amount_display_text" class="text-[11px] font-bold text-emerald-700 mt-1 block">Mệnh giá quy đổi: 3.500.000 VNĐ</span>
                            <span class="text-[10px] text-emerald-600 mt-0.5 block font-medium">Số tiền đặt cọc giữ phòng lấy trực tiếp từ hợp đồng</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: REALTIME FINANCIAL SUMMARY (4 COLS - STICKY SIDEBAR) -->
            <div class="lg:col-span-4 space-y-6">

                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl p-6 space-y-6 sticky top-24">
                    
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-brand-600"></i>
                            Tóm Tắt Công Nợ Thanh Lý
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">Real-time</span>
                    </div>

                    <!-- BẢNG TỔNG HỢP CHI TIẾT -->
                    <div class="space-y-3 text-xs">
                        
                        <!-- Tổng Cần Trả -->
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-100 space-y-2">
                            <div class="flex items-center justify-between text-slate-600 font-bold">
                                <span>A. TỔNG CÁC KHOẢN CẦN TRẢ:</span>
                                <span class="text-rose-600 text-sm font-black" id="total_payable_display">4,205,000 VNĐ</span>
                            </div>
                            <div class="border-t border-rose-200/60 pt-2 space-y-1 text-[11px] text-slate-500 font-medium">
                                <div class="flex justify-between"><span>• Tiền phòng:</span> <span id="summary_rent" class="font-bold">3,500,000đ</span></div>
                                <div class="flex justify-between"><span>• Tiền điện:</span> <span id="summary_elec" class="font-bold">280,000đ</span></div>
                                <div class="flex justify-between"><span>• Tiền nước:</span> <span id="summary_water" class="font-bold">75,000đ</span></div>
                                <div class="flex justify-between"><span>• Phí dịch vụ:</span> <span id="summary_service" class="font-bold">150,000đ</span></div>
                                <div class="flex justify-between"><span>• Đền bù hư hỏng:</span> <span id="summary_damage" class="font-bold">200,000đ</span></div>
                                <div class="flex justify-between"><span>• Tiền phạt vi phạm:</span> <span id="summary_penalty" class="font-bold">0đ</span></div>
                            </div>
                        </div>

                        <!-- Tổng Đã Đóng -->
                        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 space-y-2">
                            <div class="flex items-center justify-between text-slate-600 font-bold">
                                <span>B. Tiền cọc:</span>
                                <span class="text-emerald-600 text-sm font-black" id="total_paid_display">3,500,000 VNĐ</span>
                            </div>
                            <div class="border-t border-emerald-200/60 pt-2 flex items-center justify-between">
                                <span class="text-[11px] text-slate-500 font-medium">• Tiền cọc giữ:</span>
                                <span id="summary_deposit" class="font-bold text-[11px]">3,500,000đ</span>
                            </div>

                            <!-- Toggle Cấn trừ chi phí vào tiền cọc đang giữ -->
                            <div class="border-t border-emerald-200/60 pt-2">
                                <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                                    <input type="checkbox" id="toggle_deduct_deposit" checked onchange="toggleDeductDepositOption()" 
                                           class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                                    <span class="text-[11px] font-bold text-slate-800">Cấn trừ chi phí vào tiền cọc đang giữ</span>
                                </label>
                            </div>
                        </div>

                        <!-- KHỐI KẾT QUẢ TỔNG KẾT (CHUYỂN ĐỔI QUA LAI 2 GIAO DIỆN) -->
                        <div id="result_area">
                            <!-- KHỐI 1: GIAO DIỆN CẤN TRỪ (KHI CHECKBOX ĐƯỢC TICK - MẶC ĐỊNH) -->
                            <div id="deduct_result_box" class="space-y-2">
                                <div id="settlement_card" class="p-5 rounded-2xl border-2 text-center space-y-2 shadow-md transition-all">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider block" id="settlement_badge">KẾT QUẢ CHỐT SỐ TIỀN</span>
                                    <div class="text-2xl font-black" id="settlement_amount">705,000 VNĐ</div>
                                    <p class="text-xs font-bold" id="settlement_text">Khách thuê cần ĐÓNG THÊM cho chủ nhà</p>
                                </div>
                            </div>

                            <!-- KHỐI 2: GIAO DIỆN KHÔNG CẤN TRỪ (KHI CHECKBOX BỎ TICK) -->
                            <div id="no_deduct_result_box" class="hidden space-y-3">
                                <!-- Dòng 1 (Màu đỏ): Khách thuê cần thanh toán -->
                                <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 text-left space-y-1 shadow-sm">
                                    <div class="flex items-center justify-between font-bold text-xs text-rose-800">
                                        <span>🔴 Khách thuê cần thanh toán:</span>
                                        <span class="font-black text-sm text-rose-600" id="no_deduct_payable_display">4,205,000 VNĐ</span>
                                    </div>
                                    <p class="text-[10px] font-semibold text-rose-600/80">Thanh toán chi phí phát sinh tháng cuối</p>
                                </div>

                                <!-- Dòng 2 (Màu xanh lá): Chủ nhà cần hoàn trả -->
                                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-left space-y-1 shadow-sm">
                                    <div class="flex items-center justify-between font-bold text-xs text-emerald-800">
                                        <span>🟢 Chủ nhà cần hoàn trả:</span>
                                        <span class="font-black text-sm text-emerald-600" id="no_deduct_deposit_display">3,500,000 VNĐ</span>
                                    </div>
                                    <p class="text-[10px] font-semibold text-emerald-600/80">Hoàn trả 100% tiền cọc ban đầu</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- THÔNG TIN PHƯƠNG THỨC THANH TOÁN -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                        <label class="font-bold text-slate-700 block">Hình thức thanh toán / Hoàn trả (*):</label>
                        <select id="payment_method" class="w-full p-2 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-brand-500 focus:outline-none bg-white">
                            <option value="Chuyển khoản Ngân Hàng">Chuyển khoản Ngân Hàng (Momo/ZaloPay/STK)</option>
                            <option value="Tiền mặt">Thanh toán Tiền mặt trực tiếp</option>
                        </select>
                    </div>

                    <!-- HÀNH ĐỘNG CONFIRM -->
                    <div class="space-y-2 pt-2 no-print">
                        <button type="submit" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-sm shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span>Xác Nhận & Lưu Thanh Lý Hợp Đồng</span>
                        </button>
                        
                        <button type="button" onclick="window.print()" 
                                class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2 border border-slate-200">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>In Biên Bản Bản Cứng</span>
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
            <p class="text-slate-500">Form chốt công nợ & tự động thanh lý hợp đồng thuê nhà.</p>
        </div>
    </footer>


    <!-- JAVASCRIPT LOGIC TÍNH TOÁN REAL-TIME & ĐỊNH DẠNG TIỀN TỆ -->
    <script>
        // Hàm định dạng tiền tệ VNĐ
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' VNĐ';
        }
        function formatVNDShort(amount) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' VNĐ';
        }

        // Cập nhật nhãn mệnh giá quy đổi trực tiếp cho tất cả các ô nhập tiền
        function updateLiveCurrencyDisplays() {
            const baseRent = parseFloat(document.getElementById('base_rent').value) || 0;
            const rentAmount = parseFloat(document.getElementById('rent_amount').value) || 0;
            const elecRate = parseFloat(document.getElementById('elec_rate').value) || 0;
            const elecAmount = parseFloat(document.getElementById('elec_amount').value) || 0;
            const waterRate = parseFloat(document.getElementById('water_rate').value) || 0;
            const waterAmount = parseFloat(document.getElementById('water_amount').value) || 0;
            const serviceAmount = parseFloat(document.getElementById('service_amount').value) || 0;
            const damageAmount = parseFloat(document.getElementById('damage_amount').value) || 0;
            const penaltyAmount = parseFloat(document.getElementById('penalty_amount').value) || 0;
            const depositAmount = parseFloat(document.getElementById('deposit_amount').value) || 0;

            if (document.getElementById('base_rent_display_text'))
                document.getElementById('base_rent_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(baseRent);
            if (document.getElementById('rent_amount_display_text'))
                document.getElementById('rent_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(rentAmount);
            if (document.getElementById('elec_rate_display_text'))
                document.getElementById('elec_rate_display_text').innerText = "Mệnh giá: " + formatVND(elecRate) + " / kWh";
            if (document.getElementById('elec_amount_display_text'))
                document.getElementById('elec_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(elecAmount);
            if (document.getElementById('water_rate_display_text'))
                document.getElementById('water_rate_display_text').innerText = "Mệnh giá: " + formatVND(waterRate) + " / m³";
            if (document.getElementById('water_amount_display_text'))
                document.getElementById('water_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(waterAmount);
            if (document.getElementById('service_amount_display_text'))
                document.getElementById('service_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(serviceAmount);
            if (document.getElementById('damage_amount_display_text'))
                document.getElementById('damage_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(damageAmount);
            if (document.getElementById('penalty_amount_display_text'))
                document.getElementById('penalty_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(penaltyAmount);
            if (document.getElementById('deposit_amount_display_text'))
                document.getElementById('deposit_amount_display_text').innerText = "Mệnh giá quy đổi: " + formatVND(depositAmount);
        }

        // Tính tiền phòng theo số ngày ở thực tế nếu chọn prorated
        function calculateRent() {
            const baseRent = parseFloat(document.getElementById('base_rent').value) || 0;
            const daysStayed = parseFloat(document.getElementById('days_stayed').value) || 0;
            const isProrated = document.getElementById('is_prorated').checked;

            if (isProrated && daysStayed > 0) {
                // Giả định tháng có 30 ngày
                const rentProrated = (baseRent / 30) * daysStayed;
                document.getElementById('rent_amount').value = Math.round(rentProrated);
            } else {
                document.getElementById('rent_amount').value = baseRent;
            }

            calculateAll();
        }

        // Tính tiền điện
        function calculateElectricity() {
            const oldVal = parseFloat(document.getElementById('elec_old').value) || 0;
            const newVal = parseFloat(document.getElementById('elec_new').value) || 0;
            const rate = parseFloat(document.getElementById('elec_rate').value) || 0;

            const used = Math.max(0, newVal - oldVal);
            const totalElec = used * rate;

            document.getElementById('elec_amount').value = totalElec;
            document.getElementById('electricity_kwh_display').innerText = `Tiêu thụ: ${used} kWh`;

            calculateAll();
        }

        // Tính tiền nước
        function calculateWater() {
            const oldVal = parseFloat(document.getElementById('water_old').value) || 0;
            const newVal = parseFloat(document.getElementById('water_new').value) || 0;
            const rate = parseFloat(document.getElementById('water_rate').value) || 0;

            const used = Math.max(0, newVal - oldVal);
            const totalWater = used * rate;

            document.getElementById('water_amount').value = totalWater;
            document.getElementById('water_m3_display').innerText = `Tiêu thụ: ${used} m³`;

            calculateAll();
        }

        // Xử lý chuyển đổi hiển thị Cấn Trừ vs Không Cấn Trừ
        function toggleDeductDepositOption() {
            const toggleCheckbox = document.getElementById('toggle_deduct_deposit');
            const deductBox = document.getElementById('deduct_result_box');
            const noDeductBox = document.getElementById('no_deduct_result_box');

            if (toggleCheckbox && toggleCheckbox.checked) {
                if (deductBox) deductBox.classList.remove('hidden');
                if (noDeductBox) noDeductBox.classList.add('hidden');
            } else if (toggleCheckbox) {
                if (deductBox) deductBox.classList.add('hidden');
                if (noDeductBox) noDeductBox.classList.remove('hidden');
            }
        }

        // Hàm tính toán tổng hợp toàn bộ form
        function calculateAll() {
            // Cập nhật nhãn quy đổi mệnh giá trực tiếp
            updateLiveCurrencyDisplays();

            // 1. Các khoản CẦN TRẢ
            const rent = parseFloat(document.getElementById('rent_amount').value) || 0;
            const elec = parseFloat(document.getElementById('elec_amount').value) || 0;
            const water = parseFloat(document.getElementById('water_amount').value) || 0;
            const service = parseFloat(document.getElementById('service_amount').value) || 0;
            const damage = parseFloat(document.getElementById('damage_amount').value) || 0;
            const penalty = parseFloat(document.getElementById('penalty_amount').value) || 0;

            const totalPayable = rent + elec + water + service + damage + penalty;

            // 2. Các khoản ĐÃ ĐÓNG (CẤN TRỪ)
            const deposit = parseFloat(document.getElementById('deposit_amount').value) || 0;
            const totalPaid = deposit;

            // 3. Cập nhật hiển thị Breakdown
            document.getElementById('summary_rent').innerText = formatVNDShort(rent);
            document.getElementById('summary_elec').innerText = formatVNDShort(elec);
            document.getElementById('summary_water').innerText = formatVNDShort(water);
            document.getElementById('summary_service').innerText = formatVNDShort(service);
            document.getElementById('summary_damage').innerText = formatVNDShort(damage);
            document.getElementById('summary_penalty').innerText = formatVNDShort(penalty);

            document.getElementById('summary_deposit').innerText = formatVNDShort(deposit);

            document.getElementById('total_payable_display').innerText = formatVND(totalPayable);
            document.getElementById('total_paid_display').innerText = formatVND(totalPaid);

            // Cập nhật giá trị hiển thị cho Khối Không Cấn Trừ
            document.getElementById('no_deduct_payable_display').innerText = formatVND(totalPayable);
            document.getElementById('no_deduct_deposit_display').innerText = formatVND(totalPaid);

            // 4. Tính toán kết quả chốt cuối cùng (Kịch bản A vs B cho Khối Cấn Trừ)
            const settlementCard = document.getElementById('settlement_card');
            const settlementBadge = document.getElementById('settlement_badge');
            const settlementAmount = document.getElementById('settlement_amount');
            const settlementText = document.getElementById('settlement_text');

            const diff = totalPayable - totalPaid;

            if (diff > 0) {
                // Khách phải NỘP THÊM (Chủ nhà thu thêm tiền)
                settlementCard.className = "p-5 rounded-2xl border-2 border-rose-300 bg-rose-50 text-center space-y-2 shadow-md transition-all";
                settlementBadge.className = "text-[10px] font-extrabold uppercase tracking-wider block text-rose-700";
                settlementBadge.innerText = "KẾT QUẢ: KHÁCH CẦN NỘP THÊM";
                settlementAmount.className = "text-2xl font-black text-rose-600";
                settlementAmount.innerText = formatVND(diff);
                settlementText.className = "text-xs font-bold text-rose-800";
                settlementText.innerText = "Tổng tiền cọc không đủ trả chi phí. Khách cần đóng bổ sung cho chủ nhà.";
            } else if (diff < 0) {
                // Chủ nhà phải HOÀN LẠI TIỀN CỌC THỪA cho khách
                const refund = Math.abs(diff);
                settlementCard.className = "p-5 rounded-2xl border-2 border-emerald-300 bg-emerald-50 text-center space-y-2 shadow-md transition-all";
                settlementBadge.className = "text-[10px] font-extrabold uppercase tracking-wider block text-emerald-700";
                settlementBadge.innerText = "KẾT QUẢ: CHỦ NHÀ HOÀN LẠI TIỀN CỌC";
                settlementAmount.className = "text-2xl font-black text-emerald-600";
                settlementAmount.innerText = formatVND(refund);
                settlementText.className = "text-xs font-bold text-emerald-800";
                settlementText.innerText = "Sau khi trừ chi phí, chủ nhà sẽ hoàn lại số tiền thừa này cho khách thuê.";
            } else {
                // Hòa vốn (Công nợ = 0)
                settlementCard.className = "p-5 rounded-2xl border-2 border-slate-300 bg-slate-100 text-center space-y-2 shadow-md transition-all";
                settlementBadge.className = "text-[10px] font-extrabold uppercase tracking-wider block text-slate-600";
                settlementBadge.innerText = "KẾT QUẢ: HÒA VỐN (0 VNĐ)";
                settlementAmount.className = "text-2xl font-black text-slate-800";
                settlementAmount.innerText = "0 VNĐ";
                settlementText.className = "text-xs font-bold text-slate-600";
                settlementText.innerText = "Tiền cọc cấn trừ vừa đủ tất cả chi phí phát sinh tháng cuối.";
            }
        }

        // Xử lý gửi Form
        function handleLiquidationSubmit(event) {
            event.preventDefault();

            const tenantName = document.getElementById('tenant_name').value;
            const roomName = document.getElementById('room_name').value;
            const totalPayable = document.getElementById('total_payable_display').innerText;
            const totalPaid = document.getElementById('total_paid_display').innerText;
            const resultText = document.getElementById('settlement_amount').innerText;

            alert(`✅ XÁC NHẬN THANH LÝ HỢP ĐỒNG THÀNH CÔNG!\n\n• Phòng: ${roomName}\n• Khách thuê: ${tenantName}\n• Tổng chi phí phát sinh: ${totalPayable}\n• Tổng tiền cọc đã cấn trừ: ${totalPaid}\n• Kết quả thanh toán chốt: ${resultText}\n\nTrạng thái phòng đã được chuyển sang "Trống / Đã thanh lý"!`);
        }

        // Tự động khởi chạy tính toán khi tải trang
        document.addEventListener('DOMContentLoaded', function() {
            calculateElectricity();
            calculateWater();
            calculateRent();
            calculateAll();
            toggleDeductDepositOption();
        });
    </script>
</body>

</html>