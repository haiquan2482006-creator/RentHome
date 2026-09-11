<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Vận Hành & Cho Thuê Nhà - RentHome</title>

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

        .filter-btn.active {
            background-color: #0284c7;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 14px 0 rgba(2, 132, 199, 0.39);
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    <!-- TOP HEADER BAR (Bên phải có nút Tạo hợp đồng, Đổi mật khẩu, Đăng xuất) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-bold text-skybrand-600 uppercase tracking-widest mt-0.5">Quản Lý Vận Hành Chủ Nhà</span>
                </div>
            </a>

            <!-- Right Actions: NÚT TẠO HỢP ĐỒNG (GÓC TRÊN BÊN PHẢI TRANG) + Đổi Mật Khẩu + Đăng Xuất -->
            <div class="flex items-center gap-3">
                <!-- NÚT TẠO HỢP ĐỒNG (TRÊN BÊN PHẢI TRANG) -->
                <button onclick="openCreateContractModal()" 
                        class="py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-file-circle-plus text-base"></i>
                    <span>+ Tạo Hợp Đồng</span>
                </button>

                <!-- Nút Trở Về Trang Chủ -->
                <a href="{{ url('/') }}" 
                   class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm flex items-center gap-2 border border-slate-200 shadow-sm hover:shadow transition-all">
                    <i class="fa-solid fa-house text-skybrand-600"></i>
                    <span>Trở Về Trang Chủ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTAINER -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- BANNER QUẢN LÝ VẬN HÀNH (MÀU DARK NAVY #0b132a SẮC NÉT) -->
        <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/20 border border-brand-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-building-circle-check"></i> Quản Lý Vận Hành Tòa Nhà & Phòng Cho Thuê
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Danh Sách Tòa Nhà & Phòng Cho Thuê
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base max-w-2xl font-normal">
                        Theo dõi tiến độ đóng tiền (Đã thu tiền / Chưa thu tiền), chốt điện nước hàng tháng, xem hợp đồng & chốt công nợ thanh lý.
                    </p>
                </div>

                <!-- Thống kê trạng thái thu tiền -->
                <div class="flex items-center gap-4 bg-[#142248] border border-sky-500/30 px-5 py-3.5 rounded-2xl shrink-0 shadow-lg">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm animate-pulse"></span>
                        <div>
                            <p class="text-[10px] text-sky-200 uppercase font-extrabold">Đã Thu Tiền</p>
                            <p class="text-lg font-black text-emerald-400" id="statPaidCount">3 Phòng</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-slate-700/80"></div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-rose-500 shadow-sm animate-pulse"></span>
                        <div>
                            <p class="text-[10px] text-sky-200 uppercase font-extrabold">Chưa Thu Tiền</p>
                            <p class="text-lg font-black text-rose-400" id="statUnpaidCount">2 Phòng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- BỘ LỌC TÌM KIẾM & BẢNG CHÚ THÍCH TRẠNG THÁI (ĐẸP MẮT & TINH TẾ) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Chú thích chấm xanh / chấm đỏ -->
            <div class="flex flex-wrap items-center gap-5 text-xs font-bold">
                <span class="text-slate-400 uppercase tracking-wider text-[10px]">Trạng Thái:</span>
                <button onclick="filterRooms('all')" id="filter-all" class="filter-btn active px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-700 transition-all">
                    Tất cả (5)
                </button>
                <button onclick="filterRooms('paid')" id="filter-paid" class="filter-btn px-3.5 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50/60 text-emerald-700 hover:bg-emerald-100 transition-all flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shadow-sm"></span>
                    <span>Đã thu tiền (Chấm Xanh)</span>
                </button>
                <button onclick="filterRooms('unpaid')" id="filter-unpaid" class="filter-btn px-3.5 py-1.5 rounded-xl border border-rose-200 bg-rose-50/60 text-rose-700 hover:bg-rose-100 transition-all flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block shadow-sm"></span>
                    <span>Chưa thu tiền (Chấm Đỏ)</span>
                </button>
            </div>

            <!-- Ô tìm kiếm mã phòng / tên khách -->
            <div class="relative w-full md:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" oninput="searchRooms()" placeholder="Tìm tên khách hoặc mã phòng..." 
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-skybrand-500 focus:outline-none bg-slate-50">
            </div>
        </div>


        <!-- DANH SÁCH CÁC TÒA NHÀ & PHÒNG ĐANG CHO THUÊ -->
        <div class="space-y-6" id="roomListContainer">

            <!-- PHÒNG 1: CHẤM ĐỎ (CHƯA THU TIỀN) - CÓ NÚT "CÔNG NỢ" HIỂN THỊ -->
            <div id="card-NH-302" class="room-card unpaid bg-white rounded-3xl border-2 border-rose-200 overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6 relative">
                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-2xl border border-rose-200">
                            <i class="fa-solid fa-door-closed"></i>
                        </div>
                        <!-- CHẤM ĐỎ CHƯA THU TIỀN -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md" title="Chưa thu tiền"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> CHƯA THU TIỀN
                            </span>
                            <span class="text-xs font-bold text-slate-400">Mã phòng: NH-302</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Phòng Trọ Cao Cấp Nguyên Hồng – Phòng 302</h3>
                        <p class="text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">Nguyễn Văn An</strong> (SĐT: 0912 345 678)
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs pt-1">
                            <span>Giá thuê: <strong class="text-brand-600 font-bold">3,500,000đ/tháng</strong></span>
                            <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">3,500,000đ</strong></span>
                            <span>Kỳ nợ: <strong class="text-rose-600 font-bold">Tháng 10/2026 (3,850,000đ)</strong></span>
                        </div>
                    </div>
                </div>

                <!-- NÚT THAO TÁC CHO PHÒNG 1 -->
                <div class="flex flex-wrap items-center lg:justify-end gap-2 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <!-- Nút Thanh Lý Hợp Đồng (Mở Form Chốt Công Nợ) -->
                    <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}"
                       class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý Hợp Đồng</span>
                    </a>

                    <!-- Nút Chốt Điện Nước -->
                    <button onclick="openMonthlyBillModal('NH-302', 'Nguyễn Văn An', 1500, 55)"
                            class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Chốt Điện Nước</span>
                    </button>

                    <!-- Nút Xem Hóa Đơn -->
                    <button onclick="openViewInvoiceModal('NH-302', 'HD-202610-302', 'Nguyễn Văn An', '3,850,000đ', 'Chưa thu tiền')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Xem Hóa Đơn</span>
                    </button>

                    <!-- Nút Xem Hợp Đồng -->
                    <button onclick="openViewContractModal('NH-302', 'HD-2026-302', 'Nguyễn Văn An', '0912 345 678', '3,500,000đ', '3,500,000đ', '01/06/2026', '01/06/2027')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Xem Hợp Đồng</span>
                    </button>
                </div>
            </div>

            <!-- PHÒNG 2: CHẤM XANH (ĐÃ THU TIỀN) -->
            <div class="room-card paid bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl border border-emerald-200">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <!-- CHẤM XANH ĐÃ THU TIỀN -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="Đã thu tiền"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN
                            </span>
                            <span class="text-xs font-bold text-slate-400">Mã phòng: VH-1208</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Căn Hộ Vinhomes Central Park – Landmark 2 (Căn 12.08)</h3>
                        <p class="text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">Trần Thị Minh</strong> (SĐT: 0908 777 666)
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs pt-1">
                            <span>Giá thuê: <strong class="text-brand-600 font-bold">7,500,000đ/tháng</strong></span>
                            <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">7,500,000đ</strong></span>
                            <span>Đã đóng: <strong class="text-emerald-600 font-bold">Kỳ Tháng 09/2026</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center lg:justify-end gap-2 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}"
                       class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý Hợp Đồng</span>
                    </a>

                    <button onclick="openMonthlyBillModal('VH-1208', 'Trần Thị Minh', 2250, 115)"
                            class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Chốt Điện Nước</span>
                    </button>

                    <button onclick="openViewInvoiceModal('VH-1208', 'HD-202609-VH', 'Trần Thị Minh', '7,850,000đ', 'Đã thu tiền')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Xem Hóa Đơn</span>
                    </button>

                    <button onclick="openViewContractModal('VH-1208', 'HD-2026-VH', 'Trần Thị Minh', '0908 777 666', '7,500,000đ', '7,500,000đ', '15/01/2026', '15/01/2027')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Xem Hợp Đồng</span>
                    </button>
                </div>
            </div>

            <!-- PHÒNG 3: CHẤM XANH (ĐÃ THU TIỀN) -->
            <div class="room-card paid bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl border border-emerald-200">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="Đã thu tiền"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN
                            </span>
                            <span class="text-xs font-bold text-slate-400">Mã phòng: MT-1504</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Căn Hộ Masteri Thảo Điền – Tháp T3-1504</h3>
                        <p class="text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">Lê Hoàng Nam</strong> (SĐT: 0938 111 222)
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs pt-1">
                            <span>Giá thuê: <strong class="text-brand-600 font-bold">10,500,000đ/tháng</strong></span>
                            <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">10,500,000đ</strong></span>
                            <span>Đã đóng: <strong class="text-emerald-600 font-bold">Kỳ Tháng 09/2026</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center lg:justify-end gap-2 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}"
                       class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý Hợp Đồng</span>
                    </a>

                    <button onclick="openMonthlyBillModal('MT-1504', 'Lê Hoàng Nam', 1920, 92)"
                            class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Chốt Điện Nước</span>
                    </button>

                    <button onclick="openViewInvoiceModal('MT-1504', 'HD-202609-MT', 'Lê Hoàng Nam', '10,950,000đ', 'Đã thu tiền')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Xem Hóa Đơn</span>
                    </button>

                    <button onclick="openViewContractModal('MT-1504', 'HD-2026-MT', 'Lê Hoàng Nam', '0938 111 222', '10,500,000đ', '10,500,000đ', '10/02/2026', '10/02/2027')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Xem Hợp Đồng</span>
                    </button>
                </div>
            </div>

            <!-- PHÒNG 4: CHẤM ĐỎ (CHƯA THU TIỀN NỢ THỨ 2) - CÓ NÚT "FORM CÔNG NỢ" HIỂN THỊ -->
            <div id="card-KVC-201" class="room-card unpaid bg-white rounded-3xl border-2 border-rose-200 overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6 relative">
                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-2xl border border-rose-200">
                            <i class="fa-solid fa-door-closed"></i>
                        </div>
                        <!-- CHẤM ĐỎ CHƯA THU TIỀN -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md" title="Chưa thu tiền"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> CHƯA THU TIỀN
                            </span>
                            <span class="text-xs font-bold text-slate-400">Mã phòng: KVC-201</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Phòng Trọ Studio Kha Vạn Cân – Phòng 201</h3>
                        <p class="text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">Phạm Văn Hải</strong> (SĐT: 0977 444 333)
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs pt-1">
                            <span>Giá thuê: <strong class="text-brand-600 font-bold">4,200,000đ/tháng</strong></span>
                            <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">4,200,000đ</strong></span>
                            <span>Kỳ nợ: <strong class="text-rose-600 font-bold">Tháng 10/2026 (4,620,000đ)</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center lg:justify-end gap-2 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <!-- Nút Thanh Lý Hợp Đồng (Mở Form Chốt Công Nợ) -->
                    <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}"
                       class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý Hợp Đồng</span>
                    </a>

                    <!-- Nút Chốt Điện Nước -->
                    <button onclick="openMonthlyBillModal('KVC-201', 'Phạm Văn Hải', 1020, 42)"
                            class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Chốt Điện Nước</span>
                    </button>

                    <!-- Nút Xem Hóa Đơn -->
                    <button onclick="openViewInvoiceModal('KVC-201', 'HD-202610-KVC', 'Phạm Văn Hải', '4,620,000đ', 'Chưa thu tiền')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Xem Hóa Đơn</span>
                    </button>

                    <!-- Nút Xem Hợp Đồng -->
                    <button onclick="openViewContractModal('KVC-201', 'HD-2026-KVC', 'Phạm Văn Hải', '0977 444 333', '4,200,000đ', '4,200,000đ', '01/04/2026', '01/04/2027')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Xem Hợp Đồng</span>
                    </button>
                </div>
            </div>

            <!-- PHÒNG 5: CHẤM XANH (ĐÃ THU TIỀN) -->
            <div class="room-card paid bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl border border-emerald-200">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="Đã thu tiền"></span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN
                            </span>
                            <span class="text-xs font-bold text-slate-400">Mã phòng: NX-101</span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Nhà Nguyên Căn Nguyễn Xí – Số 48/15 Nguyễn Xí</h3>
                        <p class="text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">Vũ Thị Lan</strong> (SĐT: 0903 888 999)
                        </p>
                        <div class="flex flex-wrap gap-4 text-xs pt-1">
                            <span>Giá thuê: <strong class="text-brand-600 font-bold">12,000,000đ/tháng</strong></span>
                            <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">12,000,000đ</strong></span>
                            <span>Đã đóng: <strong class="text-emerald-600 font-bold">Kỳ Tháng 09/2026</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center lg:justify-end gap-2 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}"
                       class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý Hợp Đồng</span>
                    </a>

                    <button onclick="openMonthlyBillModal('NX-101', 'Vũ Thị Lan', 3280, 175)"
                            class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Chốt Điện Nước</span>
                    </button>

                    <button onclick="openViewInvoiceModal('NX-101', 'HD-202609-NX', 'Vũ Thị Lan', '12,650,000đ', 'Đã thu tiền')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Xem Hóa Đơn</span>
                    </button>

                    <button onclick="openViewContractModal('NX-101', 'HD-2026-NX', 'Vũ Thị Lan', '0903 888 999', '12,000,000đ', '12,000,000đ', '15/08/2026', '15/08/2027')"
                            class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Xem Hợp Đồng</span>
                    </button>
                </div>
            </div>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-6 h-6 object-contain">
                <span class="font-bold text-white text-sm">RentHome Việt Nam</span>
                <span>© 2026 Hệ thống quản lý vận hành chủ nhà.</span>
            </div>
            <p class="text-slate-500">Hỗ trợ chốt điện nước, thanh lý hợp đồng & quản lý công nợ.</p>
        </div>
    </footer>


    <!-- ========================================================================= -->
    <!-- MODAL 1: TẠO HỢP ĐỒNG MỚI (NÚT TRÊN BÊN PHẢI TRANG) -->
    <!-- ========================================================================= -->
    <div id="createContractModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 border border-slate-100 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-signature text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tạo Hợp Đồng Cho Thuê Mới</h3>
                        <p class="text-xs text-slate-500">Điền thông tin khách thuê & điều khoản hợp đồng</p>
                    </div>
                </div>
                <button onclick="closeCreateContractModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form onsubmit="handleCreateContractSubmit(event)" class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Chọn Tòa nhà / Phòng cho thuê (*):</label>
                    <select required class="w-full p-2.5 rounded-xl border border-slate-200 font-semibold focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Chọn phòng --</option>
                        <option value="Phòng Trọ Studio Kha Vạn Cân (KVC-202)">Phòng Trọ Studio Kha Vạn Cân (KVC-202) - 4,200,000đ/tháng</option>
                        <option value="Phòng Trọ Cao Cấp Nguyên Hồng (NH-303)">Phòng Trọ Cao Cấp Nguyên Hồng (NH-303) - 3,500,000đ/tháng</option>
                        <option value="Căn Hộ Vinhomes Central Park (VH-1509)">Căn Hộ Vinhomes Central Park (VH-1509) - 8,000,000đ/tháng</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Họ & Tên khách thuê (*):</label>
                        <input type="text" required placeholder="Nguyễn Văn A" class="w-full p-2.5 rounded-xl border border-slate-200 font-medium">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Số điện thoại khách (*):</label>
                        <input type="text" required placeholder="0912 345 678" class="w-full p-2.5 rounded-xl border border-slate-200 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Số CCCD / CMND (*):</label>
                        <input type="text" required placeholder="079123456789" class="w-full p-2.5 rounded-xl border border-slate-200 font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Giá thuê hàng tháng (VNĐ) (*):</label>
                        <input type="number" required placeholder="3500000" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-brand-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Số tiền cọc giữ phòng (VNĐ) (*):</label>
                        <input type="number" required placeholder="3500000" class="w-full p-2.5 rounded-xl border border-slate-200 font-bold text-skybrand-600">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Ngày đóng tiền hàng tháng (*):</label>
                        <select required class="w-full p-2.5 rounded-xl border border-slate-200 font-medium">
                            <option value="1">Ngày 01 hàng tháng</option>
                            <option value="5" selected>Ngày 05 hàng tháng</option>
                            <option value="10">Ngày 10 hàng tháng</option>
                            <option value="15">Ngày 15 hàng tháng</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Ngày bắt đầu hợp đồng (*):</label>
                        <input type="date" required value="2026-10-01" class="w-full p-2.5 rounded-xl border border-slate-200 font-medium">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Thời hạn hợp đồng (*):</label>
                        <select required class="w-full p-2.5 rounded-xl border border-slate-200 font-medium">
                            <option value="6">6 Tháng</option>
                            <option value="12" selected>1 Năm (12 Tháng)</option>
                            <option value="24">2 Năm (24 Tháng)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Đơn giá điện & nước thỏa thuận:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" value="Điện: 3.500đ / kWh" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                        <input type="text" value="Nước: 14.000đ / m3" class="p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeCreateContractModal()" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold">Hủy bỏ</button>
                    <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md transition-all">
                        <i class="fa-solid fa-check"></i> Xác Nhận Tạo Hợp Đồng
                    </button>
                </div>
            </form>
        </div>
    </div>





    <!-- ========================================================================= -->
    <!-- MODAL 3: FORM CÔNG NỢ ĐANG CHỜ (CÓ NÚT XÁC NHẬN THU TIỀN) -->
    <!-- ========================================================================= -->
    <div id="pendingDebtModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Form Công Nợ Đang Chờ</h3>
                        <p class="text-xs text-slate-500" id="pdRoomCode">Mã phòng: NH-302</p>
                    </div>
                </div>
                <button onclick="closePendingDebtModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 text-center space-y-1">
                    <span class="text-slate-500 block">Số tiền người thuê chưa thanh toán:</span>
                    <h4 class="text-2xl font-black text-rose-600" id="pdAmount">3,850,000 VNĐ</h4>
                    <p class="text-xs text-slate-600 font-semibold" id="pdNote">Hóa đơn tiền thuê tháng 10 + Điện nước</p>
                </div>

                <div class="space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <p class="flex justify-between"><span class="text-slate-500">Người thuê:</span> <strong class="text-slate-800" id="pdTenant">Nguyễn Văn An</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Trạng thái:</span> <strong class="text-rose-600 font-bold">Đang chờ người thuê trả công nợ</strong></p>
                    <p class="text-[11px] text-amber-700 italic bg-amber-50/50 p-2 rounded-lg border border-amber-100">
                        * Chủ nhà có thể thoát ra màn hình chính, khi nhấn lại vào Quản lý vận hành hệ thống sẽ tự khôi phục form công nợ đang chờ này.
                    </p>
                </div>

                <!-- NÚT XÁC NHẬN THU TIỀN -->
                <button onclick="confirmCollectMoneyFromModal()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>Xác Nhận Thu Tiền (Chuyển Thành Chấm Xanh)</span>
                </button>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 4: CHỐT ĐIỆN NƯỚC HÀNG THÁNG (TẠO HÓA ĐƠN MỚI) -->
    <!-- ========================================================================= -->
    <div id="monthlyBillModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-skybrand-100 text-skybrand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-bolt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Chốt Điện Nước Hàng Tháng</h3>
                        <p class="text-xs text-slate-500" id="mbRoomTitle">Mã phòng: NH-302</p>
                    </div>
                </div>
                <button onclick="closeMonthlyBillModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form onsubmit="handleMonthlyBillSubmit(event)" class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tháng tính tiền (*):</label>
                    <input type="month" value="2026-10" required class="w-full p-2.5 rounded-xl border border-slate-200 font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Chỉ số Điện mới (*):</label>
                        <input type="number" id="mbElec" required class="w-full p-2.5 rounded-xl border border-slate-200 font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Chỉ số Nước mới (*):</label>
                        <input type="number" id="mbWater" required class="w-full p-2.5 rounded-xl border border-slate-200 font-mono">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Phí Internet / Dịch vụ cộng thêm:</label>
                    <input type="number" value="100000" class="w-full p-2.5 rounded-xl border border-slate-200">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeMonthlyBillModal()" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold">Hủy bỏ</button>
                    <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold shadow-md transition-all">
                        <i class="fa-solid fa-file-invoice"></i> Tạo Hóa Đơn Mới
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 5: XEM HÓA ĐƠN & CÓ NÚT XÁC NHẬN ĐÃ THU TIỀN -->
    <!-- ========================================================================= -->
    <div id="viewInvoiceModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-receipt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Chi Tiết Hóa Đơn</h3>
                        <p class="text-xs text-slate-500" id="viCode">Mã HĐ: HD-202610-302</p>
                    </div>
                </div>
                <button onclick="closeViewInvoiceModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div>
                        <span class="text-slate-500 block">Khách thuê:</span>
                        <strong class="text-slate-900 text-sm" id="viTenant">Nguyễn Văn An</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block">Trạng thái:</span>
                        <span id="viStatusBadge" class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px]">
                            Chưa thu tiền
                        </span>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 font-bold">
                            <tr>
                                <th class="p-2.5">Hạng mục</th>
                                <th class="p-2.5 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr><td class="p-2.5">Tiền phòng tháng 10</td><td class="p-2.5 text-right font-bold">3,500,000đ</td></tr>
                            <tr><td class="p-2.5">Tiền điện (80 kWh)</td><td class="p-2.5 text-right font-bold">280,000đ</td></tr>
                            <tr><td class="p-2.5">Tiền nước (5 m3)</td><td class="p-2.5 text-right font-bold">70,000đ</td></tr>
                            <tr><td class="p-2.5">Phí Internet / Dịch vụ</td><td class="p-2.5 text-right font-bold">100,000đ</td></tr>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr><td class="p-2.5 text-slate-900">Tổng cộng:</td><td class="p-2.5 text-right text-rose-600 text-sm font-black" id="viTotal">3,850,000đ</td></tr>
                        </tfoot>
                    </table>
                </div>

                <!-- NÚT XÁC NHẬN ĐÃ THU TIỀN -->
                <div id="viCollectBtnContainer" class="pt-2">
                    <button onclick="confirmCollectMoneyFromInvoiceModal()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        <span>Xác Nhận Đã Thu Tiền</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 6: XEM HỢP ĐỒNG -->
    <!-- ========================================================================= -->
    <div id="viewContractModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-100 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-skybrand-100 text-skybrand-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-contract text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Hợp Đồng Cho Thuê Nhà</h3>
                        <p class="text-xs text-slate-500" id="vcContractCode">Mã HĐ: HD-2026-302</p>
                    </div>
                </div>
                <button onclick="closeViewContractModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                    <p class="flex justify-between"><span class="text-slate-500">Mã phòng cho thuê:</span> <strong id="vcRoomCode" class="text-slate-900">NH-302</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Khách thuê:</span> <strong id="vcTenant" class="text-slate-900">Nguyễn Văn An</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Số điện thoại liên hệ:</span> <strong id="vcPhone" class="text-skybrand-600 font-mono">0912 345 678</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Giá thuê thỏa thuận:</span> <strong id="vcRentPrice" class="text-brand-600 font-bold">3,500,000đ/tháng</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Tiền cọc đảm bảo:</span> <strong id="vcDeposit" class="text-skybrand-600 font-bold">3,500,000đ</strong></p>
                    <p class="flex justify-between"><span class="text-slate-500">Thời hạn hợp đồng:</span> <strong id="vcDates" class="text-slate-800 font-semibold">01/06/2026 - 01/06/2027</strong></p>
                </div>

                <div class="p-3 bg-skybrand-50 rounded-xl border border-skybrand-200 text-slate-700 space-y-1">
                    <p class="font-bold text-skybrand-800"><i class="fa-solid fa-shield-check"></i> Cam kết hợp đồng:</p>
                    <p class="text-[11px] leading-relaxed">Hợp đồng có hiệu lực pháp lý đầy đủ. Khách thuê cam kết thanh toán đúng hạn trước ngày 05 hàng tháng.</p>
                </div>

                <button onclick="closeViewContractModal()" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold">Đóng Cửa Sổ</button>
            </div>
        </div>
    </div>


    <!-- Toast Notification Container -->
    <div id="toast" class="fixed bottom-5 right-5 z-50 hidden bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-semibold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
        <span id="toastMessage">Thao tác thành công!</span>
    </div>


    <!-- JAVASCRIPT LOGIC IMPLEMENTATION -->
    <script>
        // Toggle Landlord Profile Menu
        function toggleLandlordMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('landlord-dropdown-menu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('landlord-dropdown-menu');
            const container = document.getElementById('landlord-menu-container');
            if (menu && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // ---------------------------------------------------------------------
        // QUẢN LÝ VÀ KHÔI PHỤC CÔNG NỢ ĐANG CHỜ KHI QUAY LẠI TỪ TRANG CHÍNH
        // ---------------------------------------------------------------------
        let activePendingDebt = {
            roomCode: 'NH-302',
            tenantName: 'Nguyễn Văn An',
            amount: '3,850,000 VNĐ',
            note: 'Hóa đơn tiền thuê tháng 10 + Điện nước',
            status: 'pending'
        };

        function dismissPendingDebtNotice() {
            const banner = document.getElementById('pendingDebtNoticeBanner');
            if (banner) banner.classList.add('hidden');
        }

        // Filter Rooms by status (Tất cả / Đã thu / Chưa thu)
        function filterRooms(type) {
            const btnAll = document.getElementById('filter-all');
            const btnPaid = document.getElementById('filter-paid');
            const btnUnpaid = document.getElementById('filter-unpaid');

            [btnAll, btnPaid, btnUnpaid].forEach(btn => btn.classList.remove('active'));

            const cards = document.querySelectorAll('.room-card');
            cards.forEach(card => {
                if (type === 'all') {
                    card.classList.remove('hidden');
                    btnAll.classList.add('active');
                } else if (type === 'paid') {
                    btnPaid.classList.add('active');
                    if (card.classList.contains('paid')) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                } else if (type === 'unpaid') {
                    btnUnpaid.classList.add('active');
                    if (card.classList.contains('unpaid')) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });
        }

        // Live Search Rooms
        function searchRooms() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.room-card');

            cards.forEach(card => {
                const text = card.innerText.toLowerCase();
                if (text.includes(query)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        // Show Toast Notification
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3500);
        }

        // 1. MODAL TẠO HỢP ĐỒNG (NÚT BÊN PHẢI TRÊN TRANG)
        function openCreateContractModal() {
            document.getElementById('createContractModal').classList.remove('hidden');
        }
        function closeCreateContractModal() {
            document.getElementById('createContractModal').classList.add('hidden');
        }
        function handleCreateContractSubmit(e) {
            e.preventDefault();
            closeCreateContractModal();
            showToast("Đã khởi tạo hợp đồng cho thuê thành công và lưu vào hệ thống!");
        }



        // 3. FORM CÔNG NỢ ĐANG CHỜ (MỞ TRỰC TIẾP TỪ NÚT "FORM CÔNG NỢ" VÀ CÓ NÚT XÁC NHẬN THU TIỀN)
        function openPendingDebtModal(roomCode, tenantName, amount, note) {
            activePendingDebt = { roomCode, tenantName, amount, note, status: 'pending' };

            document.getElementById('pdRoomCode').innerText = "Mã phòng: " + roomCode;
            document.getElementById('pdTenant').innerText = tenantName;
            document.getElementById('pdAmount').innerText = amount;
            document.getElementById('pdNote').innerText = note;
            document.getElementById('pendingDebtModal').classList.remove('hidden');
        }

        function closePendingDebtModal() {
            document.getElementById('pendingDebtModal').classList.add('hidden');
        }

        function confirmCollectMoneyFromModal() {
            closePendingDebtModal();
            
            const targetRoomCode = activePendingDebt ? activePendingDebt.roomCode : 'NH-302';
            const targetCard = document.getElementById('card-' + targetRoomCode);
            
            if (targetCard) {
                targetCard.classList.remove('unpaid', 'border-rose-200');
                targetCard.classList.add('paid', 'border-slate-200/90');
                targetCard.querySelector('.relative').innerHTML = `
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl border border-emerald-200">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="Đã thu tiền"></span>
                `;
                const badgeSpan = targetCard.querySelector('.space-y-1 .flex span');
                if (badgeSpan) {
                    badgeSpan.className = "px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1";
                    badgeSpan.innerHTML = `<span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN`;
                }
                const debtBtn = document.getElementById('btnDebt' + targetRoomCode.replace('-', ''));
                if (debtBtn) debtBtn.remove();
            }

            // Tính toán lại số phòng chưa thu tiền còn lại
            const remainingUnpaid = document.querySelectorAll('.room-card.unpaid').length;
            const remainingPaid = 5 - remainingUnpaid;

            const statPaid = document.getElementById('statPaidCount');
            const statUnpaid = document.getElementById('statUnpaidCount');
            if (statPaid) statPaid.innerText = remainingPaid + " Phòng";
            if (statUnpaid) statUnpaid.innerText = remainingUnpaid + " Phòng";

            // Cập nhật banner công nợ
            const bannerTitle = document.getElementById('pendingNoticeTitle');
            const bannerText = document.getElementById('pendingNoticeText');
            const btnNH302InBanner = document.querySelector('#pendingNoticeActions button[onclick*="NH-302"]');
            const btnKVC201InBanner = document.querySelector('#pendingNoticeActions button[onclick*="KVC-201"]');

            if (targetRoomCode === 'NH-302' && btnNH302InBanner) btnNH302InBanner.remove();
            if (targetRoomCode === 'KVC-201' && btnKVC201InBanner) btnKVC201InBanner.remove();

            if (remainingUnpaid === 1) {
                if (bannerTitle) bannerTitle.innerText = "Có 1 Form Công Nợ Đang Chờ Người Thuê Trả!";
                if (targetRoomCode === 'NH-302' && bannerText) {
                    bannerText.innerText = "KVC-201 (Phạm Văn Hải: 4,620,000đ)";
                } else if (targetRoomCode === 'KVC-201' && bannerText) {
                    bannerText.innerText = "NH-302 (Nguyễn Văn An: 3,850,000đ)";
                }
            } else if (remainingUnpaid === 0) {
                dismissPendingDebtNotice();
            }

            showToast("Đã xác nhận thu tiền phòng " + targetRoomCode + " thành công! Công nợ đã xóa & chuyển phòng sang Chấm Xanh.");
        }

        // 4. CHỐT ĐIỆN NƯỚC HÀNG THÁNG
        function openMonthlyBillModal(roomCode, tenantName, elec, water) {
            document.getElementById('mbRoomTitle').innerText = "Mã phòng: " + roomCode + " (" + tenantName + ")";
            document.getElementById('mbElec').value = elec + 75;
            document.getElementById('mbWater').value = water + 5;
            document.getElementById('monthlyBillModal').classList.remove('hidden');
        }

        function closeMonthlyBillModal() {
            document.getElementById('monthlyBillModal').classList.add('hidden');
        }

        function handleMonthlyBillSubmit(e) {
            e.preventDefault();
            closeMonthlyBillModal();
            showToast("Đã chốt chỉ số điện nước & phát hành hóa đơn mới thành công!");
        }

        // 5. XEM HÓA ĐƠN
        function openViewInvoiceModal(roomCode, code, tenant, total, status) {
            document.getElementById('viCode').innerText = "Mã HĐ: " + code;
            document.getElementById('viTenant').innerText = tenant;
            document.getElementById('viTotal').innerText = total;

            const badge = document.getElementById('viStatusBadge');
            const btnBox = document.getElementById('viCollectBtnContainer');

            if (status === 'Đã thu tiền') {
                badge.className = "px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px]";
                badge.innerText = "Đã thu tiền";
                btnBox.classList.add('hidden');
            } else {
                badge.className = "px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px]";
                badge.innerText = "Chưa thu tiền";
                btnBox.classList.remove('hidden');
            }

            document.getElementById('viewInvoiceModal').classList.remove('hidden');
        }

        function closeViewInvoiceModal() {
            document.getElementById('viewInvoiceModal').classList.add('hidden');
        }

        function confirmCollectMoneyFromInvoiceModal() {
            closeViewInvoiceModal();
            confirmCollectMoneyFromModal();
        }

        // 6. XEM HỢP ĐỒNG
        function openViewContractModal(roomCode, contractCode, tenant, phone, price, deposit, startDate, endDate) {
            document.getElementById('vcRoomCode').innerText = roomCode;
            document.getElementById('vcContractCode').innerText = "Mã HĐ: " + contractCode;
            document.getElementById('vcTenant').innerText = tenant;
            document.getElementById('vcPhone').innerText = phone;
            document.getElementById('vcRentPrice').innerText = price + "/tháng";
            document.getElementById('vcDeposit').innerText = deposit;
            document.getElementById('vcDates').innerText = startDate + " - " + endDate;

            document.getElementById('viewContractModal').classList.remove('hidden');
        }

        function closeViewContractModal() {
            document.getElementById('viewContractModal').classList.add('hidden');
        }
    </script>
</body>

</html>
