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
        /* Ẩn hoàn toàn thanh trượt / nút mũi tên tăng giảm (spinner) của input type=number */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        input[type="number"] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }

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
        <form id="invoiceForm" onsubmit="handleInvoiceSubmit(event)" class="w-full">
            <input type="hidden" name="_token" id="csrf_token" value="{{ csrf_token() }}">
            <input type="hidden" id="contract_id" name="contract_id" value="{{ $selectedData['contractId'] ?? '' }}">
            <input type="hidden" id="contract_code" name="contract_code" value="{{ $selectedData['contractCode'] ?? '' }}">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <!-- LEFT COLUMN: INPUT SECTIONS (8 COLS ON DESKTOP, 7 COLS ON LAPTOP/TABLET) -->
                <div class="md:col-span-7 lg:col-span-8 space-y-6">

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
                            Mã HĐ: <span id="display_contract_id" class="text-skybrand-600">{{ $selectedData['contractCode'] ?? 'Chưa chọn' }}</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Chọn phòng cần tạo hóa đơn (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-door-closed absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <select id="room_select" onchange="onRoomChange(this.value)"
                                        class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none transition-all">
                                    @if(!empty($contracts) && count($contracts) > 0)
                                        @foreach($contracts as $c)
                                            <option value="{{ $c->code }}" {{ ($selectedKey ?? '') === $c->code ? 'selected' : '' }}>
                                                {{ $c->title }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Chưa có hợp đồng nào đang hoạt động</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tên khách thuê đại diện (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="tenant_name" readonly value="{{ $selectedData['tenantName'] ?? '' }}" 
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-100/80 font-bold text-slate-800 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kỳ hóa đơn / Tháng tính tiền (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-days absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="billing_period" value="{{ $selectedData['billingPeriod'] ?? ('Tháng ' . date('m/Y')) }}" oninput="calculateInvoice()"
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Hạn thanh toán hóa đơn (*):</label>
                            <div class="relative">
                                <i class="fa-solid fa-clock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="date" id="due_date" value="{{ $selectedData['dueDate'] ?? date('Y-m-05') }}" 
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
                                <div class="relative">
                                    <input type="number" id="room_rent_price" value="{{ $selectedData['rent'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="room_rent_price_text" class="text-[11px] font-bold text-brand-600 mt-1 block">
                                    {{ number_format($selectedData['rent'] ?? 0) }} VNĐ
                                </span>
                            </div>

                            <div id="days_container" class="opacity-50 pointer-events-none">
                                <label class="font-bold text-slate-600 block mb-1">Số ngày ở trong tháng:</label>
                                <div class="relative">
                                    <input type="number" id="days_stayed" value="30" oninput="calculateInvoice()" min="1" max="31"
                                           class="w-full pl-3 pr-14 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">ngày</span>
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền phòng:</label>
                                <div class="px-3 py-2 rounded-xl bg-brand-50 border border-brand-200 font-extrabold text-brand-700 text-right text-sm" id="room_rent_total_display">
                                    {{ number_format($selectedData['rent'] ?? 0) }} VNĐ
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MỤC TIỀN CỌC HỢP ĐỒNG (THÁNG ĐẦU TÍNH, THÁNG SAU TỰ ĐỘNG BỎ) -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/90 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h3 class="font-extrabold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-emerald-600"></i> Tiền Cọc Hợp Đồng (Chỉ thu kỳ đầu nhận nhà)
                            </h3>
                            <div class="flex items-center gap-2">
                                <span id="deposit_badge" class="px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 {{ (!empty($selectedData['isFirstMonth'])) ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    @if(!empty($selectedData['isFirstMonth']))
                                        <i class="fa-solid fa-star text-amber-500"></i> Tháng đầu: Tự động tính tiền cọc
                                    @else
                                        <i class="fa-solid fa-check text-emerald-600"></i> Từ tháng sau: Tự động không tính cọc (0đ)
                                    @endif
                                </span>
                                <input type="hidden" id="is_first_month" value="{{ (!empty($selectedData['isFirstMonth'])) ? '1' : '0' }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Số tiền cọc quy định (VNĐ):</label>
                                <div class="relative">
                                    <input type="number" id="deposit_price" value="{{ $selectedData['contractDeposit'] ?? ($selectedData['deposit'] ?? 0) }}" oninput="calculateInvoice()" {{ empty($selectedData['isFirstMonth']) ? 'readonly' : '' }}
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 {{ empty($selectedData['isFirstMonth']) ? 'bg-slate-100 text-slate-500' : 'bg-white text-slate-800' }} font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="deposit_price_text" class="text-[11px] font-bold text-emerald-700 mt-1 block">
                                    {{ number_format($selectedData['contractDeposit'] ?? ($selectedData['deposit'] ?? 0)) }} VNĐ
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền cọc tính kỳ này:</label>
                                <div class="px-3 py-2 rounded-xl bg-emerald-100/70 border border-emerald-300 font-extrabold text-emerald-800 text-right text-sm" id="deposit_total_display">
                                    {{ number_format(!empty($selectedData['isFirstMonth']) ? ($selectedData['contractDeposit'] ?? ($selectedData['deposit'] ?? 0)) : 0) }} VNĐ
                                </div>
                                <p id="deposit_note_text" class="text-[11px] text-slate-500 mt-1">
                                    @if(!empty($selectedData['isFirstMonth']))
                                        * Hợp đồng mới ký (tháng đầu tiên): Hệ thống tự động tính tiền cọc vào hóa đơn.
                                    @else
                                        * Từ tháng thứ 2 trở đi: Tiền cọc đã thu ở kỳ đầu nên tự động không tính cọc.
                                    @endif
                                </p>
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
                                Tiêu thụ: 0 kWh
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số điện CŨ (kWh):</label>
                                <div class="relative">
                                    <input type="number" id="elec_old" value="{{ $selectedData['elecOld'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">kWh</span>
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số điện MỚI (kWh):</label>
                                <div class="relative">
                                    <input type="number" id="elec_new" value="{{ $selectedData['elecNew'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-amber-300 bg-amber-50/50 font-extrabold text-amber-900 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-amber-600 pointer-events-none">kWh</span>
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Đơn giá (VNĐ/kWh):</label>
                                <div class="relative">
                                    <input type="number" id="elec_rate" value="{{ $selectedData['elecRate'] ?? 3500 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-14 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">đ/kWh</span>
                                </div>
                                <span id="elec_rate_text" class="text-[11px] font-bold text-amber-700 mt-1 block">
                                    {{ number_format($selectedData['elecRate'] ?? 3500) }} VNĐ / kWh
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền điện:</label>
                                <div class="px-3 py-2 rounded-xl bg-amber-100/80 border border-amber-200 font-extrabold text-amber-800 text-right text-sm" id="elec_total_display">
                                    0 VNĐ
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
                                Tiêu thụ: 0 m³
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số nước CŨ (m³):</label>
                                <div class="relative">
                                    <input type="number" id="water_old" value="{{ $selectedData['waterOld'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-10 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">m³</span>
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Chỉ số nước MỚI (m³):</label>
                                <div class="relative">
                                    <input type="number" id="water_new" value="{{ $selectedData['waterNew'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-10 py-2 rounded-xl border border-skybrand-300 bg-skybrand-50/50 font-extrabold text-skybrand-900 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-skybrand-600 pointer-events-none">m³</span>
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Đơn giá (VNĐ/m³):</label>
                                <div class="relative">
                                    <input type="number" id="water_rate" value="{{ $selectedData['waterRate'] ?? 15000 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">đ/m³</span>
                                </div>
                                <span id="water_rate_text" class="text-[11px] font-bold text-skybrand-700 mt-1 block">
                                    {{ number_format($selectedData['waterRate'] ?? 15000) }} VNĐ / m³
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Thành tiền nước:</label>
                                <div class="px-3 py-2 rounded-xl bg-skybrand-100/80 border border-skybrand-200 font-extrabold text-skybrand-800 text-right text-sm" id="water_total_display">
                                    0 VNĐ
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
                                <div class="relative">
                                    <input type="number" id="fee_wifi" value="{{ $selectedData['wifi'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="fee_wifi_text" class="text-[11px] font-bold text-indigo-600 mt-1 block">
                                    {{ number_format($selectedData['wifi'] ?? 0) }} VNĐ
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Vệ sinh / Rác (VNĐ):</label>
                                <div class="relative">
                                    <input type="number" id="fee_garbage" value="{{ $selectedData['garbage'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="fee_garbage_text" class="text-[11px] font-bold text-indigo-600 mt-1 block">
                                    {{ number_format($selectedData['garbage'] ?? 0) }} VNĐ
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Phí giữ xe / Khác (VNĐ):</label>
                                <div class="relative">
                                    <input type="number" id="fee_parking" value="{{ $selectedData['parking'] ?? 0 }}" oninput="calculateInvoice()" 
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="fee_parking_text" class="text-[11px] font-bold text-indigo-600 mt-1 block">
                                    {{ number_format($selectedData['parking'] ?? 0) }} VNĐ
                                </span>
                            </div>

                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Khấu trừ / Giảm giá (VNĐ):</label>
                                <div class="relative">
                                    <input type="number" id="discount_amount" value="0" oninput="calculateInvoice()" placeholder="VD: 50000"
                                           class="w-full pl-3 pr-12 py-2 rounded-xl border border-emerald-300 bg-emerald-50/50 font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-emerald-600 pointer-events-none">VNĐ</span>
                                </div>
                                <span id="discount_amount_text" class="text-[11px] font-bold text-emerald-600 mt-1 block">
                                    0 VNĐ
                                </span>
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
                                <input type="text" id="bank_name" value="{{ $selectedData['bankName'] ?? 'VietBank' }}" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-800">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Số tài khoản & Chủ tài khoản:</label>
                                <input type="text" id="bank_account_info" value="{{ ($selectedData['bankAccount'] ?? '00939399884747') . ' - ' . ($selectedData['bankHolder'] ?? 'NGUYEN THI VAN ANH') }}" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-800">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Nội dung chuyển khoản mặc định:</label>
                                <input type="text" id="transfer_memo" value="{{ $selectedData['memo'] ?? '' }}" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 font-bold text-skybrand-700">
                            </div>
                        </div>

                        <!-- LIVE QR CODE PREVIEW -->
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 flex flex-col items-center justify-center text-center space-y-2">
                            <div class="w-32 h-32 bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex items-center justify-center relative group">
                                <img id="qr_code_img" 
                                     src="https://img.vietqr.io/image/vietbank-{{ $selectedData['bankAccount'] ?? '00939399884747' }}-compact2.png?amount={{ $selectedData['rent'] ?? 0 }}&addInfo={{ urlencode($selectedData['memo'] ?? '') }}&accountName={{ urlencode($selectedData['bankHolder'] ?? 'NGUYEN THI VAN ANH') }}" 
                                     onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('{{ $selectedData['memo'] ?? '' }}')" 
                                     alt="VietQR Code" class="w-full h-full object-contain">
                            </div>
                            <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved text-emerald-500"></i> Quét mã VietQR chuyển khoản tự động
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: REALTIME SUMMARY CARD (STICKY TOP RIGHT) -->
            <div class="md:col-span-5 lg:col-span-4 sticky top-20 z-20">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-6">
                    
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

                        <div class="flex items-center justify-between text-slate-600" id="summary_deposit_row">
                            <span>+ Tiền cọc (Kỳ đầu):</span>
                            <strong class="text-emerald-700 font-extrabold" id="summary_deposit">0 VNĐ</strong>
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

                    <!-- TRẠNG THÁI HÓA ĐƠN: Mặc định Chưa thu tiền khi phát hành mới (Nút chuyển trạng thái thu tiền đã chuyển ra danh sách phòng) -->
                    <input type="hidden" name="payment_status" id="payment_status_input" value="unpaid">

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
        // DATA DICTIONARY CHO CÁC PHÒNG TỪ DỮ LIỆU THỰC TẾ
        const roomData = @json($roomsDataMap ?? []);

        // HELPER FORMAT TIỀN TỆ VNĐ
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
        }

        // XỬ LÝ KHI CHỌN PHÒNG KHÁC TRONG DROPDOWN
        function onRoomChange(roomCode) {
            if (!roomCode) return;
            let data = roomData[roomCode];
            if (!data) {
                const norm = roomCode.replace(/\s+/g, '-');
                data = roomData[norm];
            }
            if (!data) return;

            if (document.getElementById('contract_id')) {
                document.getElementById('contract_id').value = data.contractId || '';
            }
            if (document.getElementById('contract_code')) {
                document.getElementById('contract_code').value = data.contractCode || '';
            }
            if (document.getElementById('display_contract_id')) {
                document.getElementById('display_contract_id').innerText = data.contractCode || '';
            }
            if (document.getElementById('tenant_name')) {
                document.getElementById('tenant_name').value = data.tenantName || '';
            }
            if (document.getElementById('room_rent_price')) {
                document.getElementById('room_rent_price').value = data.rent || 0;
            }

            // Đồng bộ tiền cọc: Tự động theo tháng đầu / tháng sau (hệ thống tự quyết định 100%)
            const contractDeposit = parseFloat(data.contractDeposit) || 0;
            const isFirstMonth = Boolean(data.isFirstMonth);
            const depositInput = document.getElementById('deposit_price');
            const depositBadge = document.getElementById('deposit_badge');
            const depositNote = document.getElementById('deposit_note_text');
            const isFirstMonthHidden = document.getElementById('is_first_month');

            if (isFirstMonthHidden) {
                isFirstMonthHidden.value = isFirstMonth ? '1' : '0';
            }
            if (depositInput) {
                depositInput.value = contractDeposit;
                if (!isFirstMonth) {
                    depositInput.readOnly = true;
                    depositInput.classList.add('bg-slate-100', 'text-slate-500');
                    depositInput.classList.remove('bg-white', 'text-slate-800');
                } else {
                    depositInput.readOnly = false;
                    depositInput.classList.remove('bg-slate-100', 'text-slate-500');
                    depositInput.classList.add('bg-white', 'text-slate-800');
                }
            }
            if (depositBadge) {
                if (isFirstMonth) {
                    depositBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 border border-emerald-200';
                    depositBadge.innerHTML = '<i class="fa-solid fa-star text-amber-500"></i> Tháng đầu: Tự động tính tiền cọc';
                    if (depositNote) depositNote.innerText = '* Hợp đồng mới ký (tháng đầu tiên): Hệ thống tự động tính tiền cọc vào hóa đơn.';
                } else {
                    depositBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 border border-slate-200';
                    depositBadge.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> Từ tháng sau: Tự động không tính cọc (0đ)';
                    if (depositNote) depositNote.innerText = '* Từ tháng thứ 2 trở đi: Tiền cọc đã thu ở kỳ đầu nên tự động không tính cọc.';
                }
            }
            if (document.getElementById('elec_old')) {
                document.getElementById('elec_old').value = data.elecOld || 0;
            }
            if (document.getElementById('elec_new')) {
                document.getElementById('elec_new').value = data.elecNew || 0;
            }
            if (document.getElementById('elec_rate')) {
                document.getElementById('elec_rate').value = data.elecRate || 3500;
            }
            if (document.getElementById('water_old')) {
                document.getElementById('water_old').value = data.waterOld || 0;
            }
            if (document.getElementById('water_new')) {
                document.getElementById('water_new').value = data.waterNew || 0;
            }
            if (document.getElementById('water_rate')) {
                document.getElementById('water_rate').value = data.waterRate || 15000;
            }
            if (document.getElementById('fee_wifi')) {
                document.getElementById('fee_wifi').value = data.wifi || 0;
            }
            if (document.getElementById('fee_garbage')) {
                document.getElementById('fee_garbage').value = data.garbage || 0;
            }
            if (document.getElementById('fee_parking')) {
                document.getElementById('fee_parking').value = data.parking || 0;
            }
            if (document.getElementById('bank_name') && data.bankName) {
                document.getElementById('bank_name').value = data.bankName;
            }
            if (document.getElementById('bank_account_info')) {
                document.getElementById('bank_account_info').value = (data.bankAccount || '') + ' - ' + (data.bankHolder || '');
            }
            if (document.getElementById('transfer_memo') && data.memo) {
                document.getElementById('transfer_memo').value = data.memo;
            }
            if (document.getElementById('billing_period') && data.billingPeriod) {
                document.getElementById('billing_period').value = data.billingPeriod;
            }
            if (document.getElementById('due_date') && data.dueDate) {
                document.getElementById('due_date').value = data.dueDate;
            }

            calculateInvoice();
        }

        // BẬT / TẮT TÍNH TRỌN 1 THÁNG
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

        // TÍNH TOÁN REAL-TIME CHI TIẾT TỪNG KHOẢN
        function calculateInvoice() {
            // 1. Tiền phòng
            const isFullMonth = document.getElementById('full_month_check').checked;
            const rentPrice = parseFloat(document.getElementById('room_rent_price').value) || 0;
            const days = parseFloat(document.getElementById('days_stayed').value) || 30;
            let roomTotal = isFullMonth ? rentPrice : Math.round((rentPrice / 30) * days);

            document.getElementById('room_rent_total_display').innerText = formatVND(roomTotal);
            document.getElementById('summary_room').innerText = formatVND(roomTotal);

            // 1.1 Tiền cọc (Tự động 100%: Tháng đầu tính cọc, từ tháng thứ 2 ép bằng 0đ)
            const selectedRoomCode = document.getElementById('room_select') ? document.getElementById('room_select').value : null;
            const currentRoomData = (selectedRoomCode && typeof roomData !== 'undefined') ? (roomData[selectedRoomCode] || roomData[selectedRoomCode.replace(/\s+/g, '-')]) : null;
            const isFirstMonth = currentRoomData ? Boolean(currentRoomData.isFirstMonth) : (document.getElementById('is_first_month')?.value === '1');
            const rawDepositPrice = parseFloat(document.getElementById('deposit_price') ? document.getElementById('deposit_price').value : 0) || 0;
            
            // Công thức: isFirstMonth là true thì lấy rawDepositPrice, false thì lập tức ép bằng 0
            const depositTotal = isFirstMonth ? rawDepositPrice : 0;

            if (document.getElementById('deposit_price_text')) {
                document.getElementById('deposit_price_text').innerText = formatVND(rawDepositPrice);
            }
            if (document.getElementById('deposit_total_display')) {
                document.getElementById('deposit_total_display').innerText = formatVND(depositTotal);
            }
            if (document.getElementById('summary_deposit')) {
                document.getElementById('summary_deposit').innerText = formatVND(depositTotal);
            }
            const summaryDepositRow = document.getElementById('summary_deposit_row');
            if (summaryDepositRow) {
                if (depositTotal > 0) {
                    summaryDepositRow.classList.remove('opacity-40');
                } else {
                    summaryDepositRow.classList.add('opacity-40');
                }
            }

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

            // 5. Tổng cộng (Bao gồm cả Tiền cọc nếu tháng đầu)
            const grandTotal = roomTotal + depositTotal + elecTotal + waterTotal + serviceTotal - discount;
            const formattedGrandTotal = formatVND(grandTotal);

            document.getElementById('grandTotalAmount').innerText = formattedGrandTotal;

            const period = document.getElementById('billing_period').value;
            if (document.getElementById('grandTotalDesc')) {
                document.getElementById('grandTotalDesc').innerText = `Tổng tiền hóa đơn ${period} gửi người thuê.`;
            }

            // Cập nhật text mệnh giá hiển thị bên dưới các ô nhập tiền
            if (document.getElementById('room_rent_price_text')) {
                document.getElementById('room_rent_price_text').innerText = formatVND(rentPrice);
            }
            if (document.getElementById('elec_rate_text')) {
                document.getElementById('elec_rate_text').innerText = formatVND(elecRate) + ' / kWh';
            }
            if (document.getElementById('water_rate_text')) {
                document.getElementById('water_rate_text').innerText = formatVND(waterRate) + ' / m³';
            }
            if (document.getElementById('fee_wifi_text')) {
                document.getElementById('fee_wifi_text').innerText = formatVND(wifi);
            }
            if (document.getElementById('fee_garbage_text')) {
                document.getElementById('fee_garbage_text').innerText = formatVND(garbage);
            }
            if (document.getElementById('fee_parking_text')) {
                document.getElementById('fee_parking_text').innerText = formatVND(parking);
            }
            if (document.getElementById('discount_amount_text')) {
                document.getElementById('discount_amount_text').innerText = formatVND(discount);
            }

            // Cập nhật mã VietQR Preview động
            const memo = document.getElementById('transfer_memo') ? document.getElementById('transfer_memo').value : '';
            const roomCode = document.getElementById('room_select') ? document.getElementById('room_select').value : '';
            const curData = roomData[roomCode] || {};
            const curBankAcc = curData.bankAccount || '00939399884747';
            const curBankHolder = curData.bankHolder || 'NGUYEN THI VAN ANH';
            const qrUrl = `https://img.vietqr.io/image/vietbank-${curBankAcc}-compact2.png?amount=${grandTotal}&addInfo=${encodeURIComponent(memo)}&accountName=${encodeURIComponent(curBankHolder)}`;
            const qrImg = document.getElementById('qr_code_img');
            if (qrImg) {
                qrImg.src = qrUrl;
                qrImg.onerror = function() {
                    this.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(memo + ' ' + grandTotal)}`;
                };
            }

            return {
                roomTotal,
                depositTotal,
                elecOld,
                elecNew,
                elecTotal,
                waterOld,
                waterNew,
                waterTotal,
                serviceTotal,
                discount,
                grandTotal
            };
        }

        // ĐỔI MÀU THEME KHI CHỌN ĐÃ THU HOẶC CHƯA THU
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

        // XỬ LÝ SUBMIT TẠO HÓA ĐƠN THẬT VÀO DATABASE
        function handleInvoiceSubmit(e) {
            e.preventDefault();

            const calc = calculateInvoice();
            const selectedOpt = document.getElementById('room_select');
            const selectedOptText = selectedOpt.options[selectedOpt.selectedIndex] ? selectedOpt.options[selectedOpt.selectedIndex].text : '';
            const tenant = document.getElementById('tenant_name').value.split('(')[0].trim();
            const period = document.getElementById('billing_period').value;
            const total = document.getElementById('grandTotalAmount').innerText;
            const statusVal = document.getElementById('payment_status_input')?.value || 'unpaid';

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

            // Chuẩn bị dữ liệu gửi POST lên backend lưu thật vào MongoDB
            const payload = {
                contract_id: document.getElementById('contract_id').value,
                contract_code: document.getElementById('contract_code').value,
                tenant_name: tenant,
                billing_period: period,
                rent_price: calc.roomTotal,
                deposit: calc.depositTotal,
                electricity_old: calc.elecOld,
                electricity_new: calc.elecNew,
                electricity_amount: calc.elecTotal,
                water_old: calc.waterOld,
                water_new: calc.waterNew,
                water_amount: calc.waterTotal,
                service_amount: calc.serviceTotal,
                total_amount: calc.grandTotal,
                payment_status: statusVal
            };

            const csrfToken = document.getElementById('csrf_token') ? document.getElementById('csrf_token').value : '';

            fetch("{{ url('qlvan_hanh/tao_hoa_don') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                // Hiển thị Banner thông báo thành công
                const bannerContainer = document.getElementById('savedBannerContainer');
                bannerContainer.innerHTML = `
                    <div class="p-4 rounded-2xl bg-emerald-500 text-white border border-emerald-600 shadow-lg flex items-center justify-between gap-4 animate-in fade-in slide-in-from-top duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-2xl"></i>
                            <div>
                                <strong class="font-extrabold block text-sm">Đã lưu & phát hành hóa đơn thành công! (Mã: ${data.invoice_code || 'Mới'})</strong>
                                <span class="text-xs text-emerald-100">Hóa đơn kỳ ${period} của ${tenant} đã được lưu vào hệ thống.</span>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-100 hover:text-white font-bold text-sm px-2">✕</button>
                    </div>
                `;

                // Mở Modal Thành Công
                document.getElementById('successModal').classList.remove('hidden');
            })
            .catch(err => {
                console.error('Lỗi khi lưu hóa đơn:', err);
                // Vẫn mở Modal cho người dùng xem
                document.getElementById('successModal').classList.remove('hidden');
            });
        }

        // GỬI THÔNG BÁO QUA ZALO
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

        // KHỞI TẠO BAN ĐẦU: ĐỌC URL QUERY PARAM ?room=... HOẶC NẠP DỮ LIỆU ĐƯỢC CHỌN
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            let roomParam = params.get('room') || params.get('contract');

            if (roomParam) {
                roomParam = roomParam.trim();
                const normParam = roomParam.replace(/\s+/g, '-');

                if (roomData[roomParam]) {
                    document.getElementById('room_select').value = roomData[roomParam].contractCode;
                    onRoomChange(roomParam);
                } else if (roomData[normParam]) {
                    document.getElementById('room_select').value = roomData[normParam].contractCode;
                    onRoomChange(normParam);
                } else {
                    calculateInvoice();
                }
            } else {
                const currentSelectVal = document.getElementById('room_select') ? document.getElementById('room_select').value : null;
                if (currentSelectVal && roomData[currentSelectVal]) {
                    onRoomChange(currentSelectVal);
                } else {
                    calculateInvoice();
                }
            }
        });
    </script>
</body>

</html>
