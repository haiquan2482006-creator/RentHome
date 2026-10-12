@php
    $rooms = $rooms ?? [];
    $paidCount = $paidCount ?? 0;
    $unpaidCount = $unpaidCount ?? 0;
    $availableRooms = $availableRooms ?? [];
@endphp
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Vận Hành & Cho Thuê Nhà - RentHome</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        /* Tối ưu in ấn riêng cho Tờ giấy Hợp đồng Pháp lý A4 */
        @media print {
            body * {
                visibility: hidden !important;
            }
            #legalContractSheet, #legalContractSheet * {
                visibility: visible !important;
            }
            #legalContractSheet {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 15mm 20mm !important;
                background: white !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">
    @include('partials.sidebar-user', ['isDrawer' => true])

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
                <!-- NÚT TẠO HỢP ĐỒNG (TRUY CẬP TRANG TẠO HỢP ĐỒNG CHUYÊN BIỆT) -->
                <a href="{{ url('qlvan_hanh/tao_hop_dong') }}" 
                   class="py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-file-circle-plus text-base"></i>
                    <span>+ Tạo Hợp Đồng</span>
                </a>

                <!-- Nút Tài Khoản Mở Menu Ẩn -->
                <button type="button" onclick="toggleGlobalSidebar()" 
                   class="py-2 px-3 sm:px-4 rounded-xl {{ ((Auth::user()->account_type ?? '') === 'doanhnghiep') ? 'bg-purple-50 hover:bg-purple-100 text-purple-700 border-purple-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }} font-bold text-xs sm:text-sm flex items-center gap-2 border shadow-sm hover:shadow transition-all">
                    <div class="w-6 h-6 rounded-full {{ ((Auth::user()->account_type ?? '') === 'doanhnghiep') ? 'bg-purple-200 text-purple-700' : 'bg-emerald-200 text-emerald-700' }} flex items-center justify-center shrink-0 hidden sm:flex">
                        <i class="fa-solid fa-user text-[10px]"></i>
                    </div>
                    <span class="max-w-[100px] truncate hidden sm:block">{{ Auth::user()->display_name ?? Auth::user()->name ?? 'Tài khoản' }}</span>
                    <i class="fa-solid fa-bars sm:ml-1"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN CONTAINER -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

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
                        Theo dõi tiến độ đóng tiền (Đã thu tiền / Chưa thu tiền), chốt điện nước & tạo hóa đơn hàng tháng, xem hợp đồng & chốt công nợ thanh lý.
                    </p>
                </div>

                <!-- Thống kê trạng thái thu tiền -->
                <div class="flex items-center gap-4 bg-[#142248] border border-sky-500/30 px-5 py-3.5 rounded-2xl shrink-0 shadow-lg">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm animate-pulse"></span>
                        <div>
                            <p class="text-[10px] text-sky-200 uppercase font-extrabold">Đã Thu Tiền</p>
                            <p class="text-lg font-black text-emerald-400" id="statPaidCount">{{ $paidCount }} Phòng</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-slate-700/80"></div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-rose-500 shadow-sm animate-pulse"></span>
                        <div>
                            <p class="text-[10px] text-sky-200 uppercase font-extrabold">Chưa Thu Tiền</p>
                            <p class="text-lg font-black text-rose-400" id="statUnpaidCount">{{ $unpaidCount }} Phòng</p>
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
                    Tất cả ({{ count($rooms) }})
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
        @if(($unpaidCount ?? 0) > 0)
            <!-- BANNER CẢNH BÁO THỜI GIAN THỰC KHI ĐẾN HẠN ĐÓNG TIỀN PHÒNG -->
            <div id="realtimeDueAlertBanner" class="p-4 sm:p-5 rounded-3xl bg-rose-50/90 border-2 border-rose-200/90 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-in fade-in duration-300">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-md animate-pulse">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white font-black text-[10px] tracking-wider uppercase">Cảnh Báo Đến Hạn Thu Tiền</span>
                            <span class="text-xs text-rose-700 font-bold">Hôm nay: Ngày {{ now()->day }}/{{ now()->month }}/{{ now()->year }}</span>
                        </div>
                        <h4 class="font-extrabold text-sm sm:text-base text-slate-900 mt-0.5">
                            Có <span class="text-rose-600 font-black">{{ $unpaidCount }} phòng</span> đã đến hạn hoặc quá hạn đóng tiền trong tháng này!
                        </h4>
                        <p class="text-xs text-slate-600">Hệ thống đã tự động kích hoạt thông báo thời gian thực và đánh dấu thẻ phòng Chấm Đỏ.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                    <button onclick="filterRooms('unpaid')" class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Xem các phòng chưa thu
                    </button>
                </div>
            </div>
        @endif

        <div class="space-y-6" id="roomListContainer">

            @foreach($rooms as $room)
                <div id="card-{{ $room->room_code }}" 
                     class="room-card @if($room->status === 'unpaid') unpaid border-2 border-rose-200 @else paid border border-slate-200/90 @endif bg-white rounded-3xl overflow-hidden shadow-card hover:shadow-lg transition-all p-6 flex flex-col lg:flex-row justify-between gap-6 relative"
                     data-status="{{ $room->status }}">
                    <div class="flex items-start gap-4">
                        @php
                            $roomImg = !empty($room->image) 
                                ? $room->image 
                                : (!empty($room->post?->display_image) 
                                    ? $room->post->display_image 
                                    : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80');
                        @endphp
                        <div class="relative shrink-0">
                            <div id="room-img-wrapper-{{ $room->id }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border {{ $room->status === 'unpaid' ? 'border-rose-300 ring-2 ring-rose-100' : 'border-slate-200' }} bg-slate-100 shadow-sm shrink-0">
                                <img src="{{ $roomImg }}" 
                                     alt="{{ $room->name ?? $room->room_name ?? 'Ảnh phòng' }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';">
                            </div>
                            <div id="room-status-dot-container-{{ $room->id }}">
                                @if($room->status === 'unpaid')
                                    <!-- CHẤM ĐỎ CHƯA THU TIỀN -->
                                    <span class="room-status-dot-ping absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md animate-ping"></span>
                                    <span class="room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md" title="Chưa thu tiền"></span>
                                @else
                                    <!-- CHẤM XANH ĐÃ THU TIỀN HOẶC CHƯA ĐẾN KỲ THU -->
                                    <span class="room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="{{ $room->status_label ?? 'Đã thu tiền' }}"></span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <div id="room-status-badge-{{ $room->id }}" class="inline-flex">
                                    @if($room->status === 'unpaid')
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> CHƯA THU TIỀN
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> {{ strtoupper($room->status_label ?? 'ĐÃ THU TIỀN') }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-slate-400">Mã: {{ $room->room_code }}</span>
                                @if(!empty($room->bill_date))
                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                        <i class="fa-regular fa-calendar-check text-slate-400"></i> Ngày chốt: mùng {{ $room->bill_date }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">{{ $room->name ?? $room->room_name ?? ('Phòng ' . $room->room_code) }}</h3>
                            <p class="text-xs text-slate-600 font-medium">
                                <i class="fa-solid fa-user text-slate-400"></i> Người thuê: <strong class="text-slate-800">{{ $room->tenant_name }}</strong> (SĐT: {{ $room->tenant_phone }})
                            </p>
                            <div class="flex flex-wrap gap-4 text-xs pt-1">
                                <span>Giá thuê: <strong class="text-brand-600 font-bold">{{ number_format($room->price) }}đ/tháng</strong></span>
                                <span>Tiền cọc: <strong class="text-skybrand-600 font-bold">{{ number_format($room->deposit ?? $room->price) }}đ</strong></span>
                                <div id="room-debt-status-{{ $room->id }}" class="inline-block">
                                    @if($room->status === 'unpaid')
                                        <span>Kỳ nợ: <strong class="text-rose-600 font-bold">{{ $room->debt_period ?? 'Kỳ này' }} ({{ number_format($room->debt_amount ?? $room->price) }}đ)</strong></span>
                                    @else
                                        <span>Trạng thái: <strong class="text-emerald-600 font-bold">{{ $room->status_label ?? ($room->paid_period ?? 'Đã thu tiền') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NÚT THAO TÁC CHO PHÒNG & NÚT CHUYỂN TRẠNG THÁI THU TIỀN -->
                    <div class="flex flex-wrap items-center lg:justify-end gap-2.5 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                        <!-- CỤM NÚT TRẠNG THÁI THU TIỀN (CHUYỂN TỪ TRANG TẠO HÓA ĐƠN RA NGOÀI ĐÂY) -->
                        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-slate-200 shadow-inner items-center gap-1 {{ $room->status === 'paid' ? 'hidden' : '' }}" id="payment-toggle-group-{{ $room->id }}">
                            <button type="button" 
                                    onclick="updateRoomPaymentStatus('{{ $room->id }}', '{{ $room->room_code }}', 'unpaid', this)"
                                    id="btn-status-unpaid-{{ $room->id }}"
                                    title="Chuyển sang Chưa Thu Tiền"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $room->status === 'unpaid' ? 'bg-white text-rose-700 shadow-sm border border-rose-300 ring-2 ring-rose-500/20 font-extrabold' : 'text-slate-500 hover:text-rose-600 hover:bg-white/60' }}">
                                <span class="w-2.5 h-2.5 rounded-full {{ $room->status === 'unpaid' ? 'bg-rose-500 ring-2 ring-rose-200' : 'border-2 border-slate-400' }}"></span>
                                <span>Chưa Thu Tiền</span>
                            </button>
                            <button type="button" 
                                    onclick="updateRoomPaymentStatus('{{ $room->id }}', '{{ $room->room_code }}', 'paid', this)"
                                    id="btn-status-paid-{{ $room->id }}"
                                    title="Chuyển sang Đã Thu Tiền"
                                    class="px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $room->status === 'paid' ? 'bg-white text-emerald-700 shadow-sm border border-emerald-300 ring-2 ring-emerald-500/20 font-extrabold' : 'text-slate-500 hover:text-emerald-700 hover:bg-white/60' }}">
                                <span class="w-2.5 h-2.5 rounded-full {{ $room->status === 'paid' ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'border-2 border-slate-400' }}"></span>
                                <span>Đã Thu Tiền</span>
                            </button>
                        </div>

                        <!-- Nút Thanh Lý Hợp Đồng (Mở Form Chốt Công Nợ) -->
                        <a href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}?room={{ $room->room_code }}"
                           class="py-2.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-solid fa-file-contract"></i>
                            <span>Thanh Lý Hợp Đồng</span>
                        </a>

                        <!-- Nút Tạo Hóa Đơn -->
                        <a href="{{ url('qlvan_hanh/tao_hoa_don') }}?room={{ $room->room_code }}"
                           class="py-2.5 px-3.5 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Tạo Hóa Đơn</span>
                        </a>

                        <!-- Nút Xem Hóa Đơn -->
                        <button type="button" 
                                onclick="openViewInvoiceModal({{ json_encode($room->invoice_details ?? [
                                    'room_code'      => $room->room_code,
                                    'invoice_code'   => $room->invoice_code ?? $room->room_code,
                                    'tenant_name'    => $room->tenant_name ?? 'Khách thuê',
                                    'status'         => $room->status,
                                    'status_label'   => $room->status_label ?? ($room->status === 'unpaid' ? 'Chưa thu tiền' : 'Đã thu tiền'),
                                    'total_amount'   => $room->invoice_total ?? $room->debt_amount ?? $room->price,
                                    'rent_price'     => $room->price,
                                    'elec_amount'    => 0,
                                    'water_amount'   => 0,
                                    'service_amount' => 0,
                                    'deposit'        => 0,
                                ]) }})"
                                class="py-2.5 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Xem Hóa Đơn</span>
                        </button>

                        <!-- Nút Xem Hợp Đồng -->
                        <button type="button" 
                                onclick="openViewContractModal({{ json_encode($room->contract_details ?? [
                                    'room_code'              => $room->room_code,
                                    'contract_code'          => $room->contract_code ?? $room->room_code,
                                    'name'                   => $room->name ?? $room->room_name ?? ('Phòng ' . $room->room_code),
                                    'tenant_name'            => $room->tenant_name ?? 'Khách thuê',
                                    'tenant_phone'           => $room->tenant_phone ?? 'Chưa cập nhật',
                                    'tenant_id_card'         => $room->tenant_id_card ?? 'Chưa cập nhật',
                                    'price'                  => (float)($room->price ?? 0),
                                    'deposit'                => (float)($room->deposit ?? 0),
                                    'contract_start'         => $room->contract_start ?? '01/01/2026',
                                    'contract_end'           => $room->contract_end ?? '01/01/2027',
                                    'duration_months'        => (int)($room->duration_months ?? 12),
                                    'electricity_price'      => (float)($room->electricity_price ?? 0),
                                    'electricity_start'      => (int)($room->electricity_start ?? 0),
                                    'water_calculation_type' => $room->water_calculation_type ?? 'khoi',
                                    'water_price_block'      => (float)($room->water_price_block ?? 0),
                                    'water_start'            => (int)($room->water_start ?? 0),
                                    'water_price_person'     => (float)($room->water_price_person ?? 0),
                                    'water_people_count'     => (int)($room->water_people_count ?? 1),
                                    'internet_price'         => (float)($room->internet_price ?? 0),
                                    'garbage_price'          => (float)($room->garbage_price ?? 0),
                                    'payment_cycle'          => $room->payment_cycle ?? '1 tháng',
                                    'bill_date'              => $room->bill_date ?? 5,
                                    'payment_limit_days'     => $room->payment_limit_days ?? '5 ngày',
                                    'late_penalty_fee'       => $room->late_penalty_fee ?? '20.000 VNĐ / ngày',
                                    'address'                => $room->address ?? 'Số 18 Trần Phú, Phường Mộ Lao, Quận Hà Đông, TP. Hà Nội',
                                ]) }})"
                                class="py-2.5 px-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>Xem Hợp Đồng</span>
                        </button>
                    </div>
                </div>
            @endforeach

            @if(count($rooms) === 0)
                <div class="bg-white rounded-3xl border border-slate-200/90 p-12 text-center text-slate-500 shadow-card">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-building-circle-exclamation"></i>
                    </div>
                    <h4 class="font-bold text-base text-slate-700">Chưa có phòng hoặc hợp đồng nào</h4>
                    <p class="text-xs text-slate-400 mt-1">Danh sách phòng đang quản lý sẽ hiển thị tại đây khi kết nối dữ liệu.</p>
                </div>
            @endif

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
                        @if(isset($availableRooms) && count($availableRooms) > 0)
                            @foreach($availableRooms as $availRoom)
                                <option value="{{ $availRoom->id ?? $availRoom->room_code }}">
                                    {{ $availRoom->name ?? $availRoom->room_name ?? ('Phòng ' . $availRoom->room_code) }} ({{ $availRoom->room_code }}) - {{ number_format($availRoom->price) }}đ/tháng
                                </option>
                            @endforeach
                        @else
                            <option value="Phòng Trọ Studio Kha Vạn Cân (KVC-202)">Phòng Trọ Studio Kha Vạn Cân (KVC-202) - 4,200,000đ/tháng</option>
                            <option value="Phòng Trọ Cao Cấp Nguyên Hồng (NH-303)">Phòng Trọ Cao Cấp Nguyên Hồng (NH-303) - 3,500,000đ/tháng</option>
                            <option value="Căn Hộ Vinhomes Central Park (VH-1509)">Căn Hộ Vinhomes Central Park (VH-1509) - 8,000,000đ/tháng</option>
                        @endif
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
                    <label class="font-bold text-slate-700 block mb-1 flex items-center justify-between">
                        <span>Đơn giá điện & nước thỏa thuận:</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[11px] font-semibold text-slate-600 block mb-1">Giá Điện (VNĐ/số):</label>
                            <div class="relative">
                                <input type="text" id="quickElecPrice" name="quick_elec_price" value="3.500" placeholder="VD: 3000 hoặc 3.500" class="w-full pl-3 pr-10 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/số</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="quickElecDisplay" class="font-bold text-slate-800">3.500</span> VNĐ/số
                            </div>
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-600 block mb-1">Giá Nước (VNĐ/khối):</label>
                            <div class="relative">
                                <input type="text" id="quickWaterPrice" name="quick_water_price" value="25.000" placeholder="VD: 25000 hoặc 25.000" class="w-full pl-3 pr-10 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-xs text-slate-800 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/m³</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="quickWaterDisplay" class="font-bold text-slate-800">25.000</span> VNĐ/m³
                            </div>
                        </div>
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
    <!-- MODAL 5: XEM HÓA ĐƠN CHI TIẾT (DESIGN MỚI ĐẲNG CẤP DẠNG BẢNG & VIETQR) -->
    <!-- ========================================================================= -->
    <div id="viewInvoiceModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100 max-h-[92vh] flex flex-col transform transition-all animate-in fade-in zoom-in duration-200">
            
            <!-- HEADER BANNER ĐÊM SANG TRỌNG -->
            <div class="bg-[#0b132a] p-6 text-white relative overflow-hidden shrink-0">
                <div class="relative z-10 flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                            <i class="fa-solid fa-receipt"></i> Thông Tin Hóa Đơn Thuê Nhà
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-white">Chi Tiết Hóa Đơn Hàng Tháng</h3>
                        <p class="text-xs text-slate-300 font-mono" id="viCode">Mã HĐ: HD-202610-302</p>
                    </div>

                    <button onclick="closeViewInvoiceModal()" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- BODY CONTAINER -->
            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1 text-xs">
                
                <!-- THÔNG TIN KHÁCH THUÊ & TRẠNG THÁI -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200/90">
                    <div>
                        <span class="text-slate-400 font-medium block">Khách thuê đại diện:</span>
                        <strong class="text-slate-900 text-sm font-bold block mt-0.5" id="viTenant">Nguyễn Văn An</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Mã phòng:</span>
                        <strong class="text-slate-900 text-sm font-bold block mt-0.5" id="viRoomCode">NH-302</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Trạng thái thanh toán:</span>
                        <span id="viStatusBadge" class="mt-1 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-extrabold text-xs">
                            <span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> Chưa Thu Tiền
                        </span>
                    </div>
                </div>

                <!-- BẢNG CHI TIẾT HẠNG MỤC PHÍ -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-extrabold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3 px-4">Hạng mục chi phí</th>
                                <th class="py-3 px-4 text-center">Số lượng / Gói</th>
                                <th class="py-3 px-4 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="viTableBody" class="divide-y divide-slate-100 font-medium text-slate-800">
                            <!-- Render động từ dữ liệu thật của hóa đơn -->
                        </tbody>
                    </table>
                </div>

                <!-- KHUNG HIGHLIGHT TỔNG TIỀN -->
                <div id="viTotalBox" class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-1">
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block">TỔNG CỘNG TIỀN HÓA ĐƠN THÁNG</span>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight" id="viTotal">
                        0đ
                    </div>
                </div>

                <!-- THANH TOÁN VIETQR PREVIEW -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-center sm:text-left">
                        <span class="font-extrabold text-slate-800 block text-xs flex items-center gap-1.5 justify-center sm:justify-start">
                            <i class="fa-solid fa-qrcode text-emerald-600"></i> Quét VietQR Chuyển Khoản Nhanh
                        </span>
                        <p class="text-slate-500 text-[11px]">Ngân hàng <span id="viBankName" class="font-bold text-slate-800">VietBank</span>: <strong class="text-slate-800 font-mono" id="viBankAccount">00939399884747</strong></p>
                        <p class="text-slate-500 text-[11px]">Chủ TK: <strong class="text-slate-800 uppercase" id="viBankHolder">NGUYEN THI VAN ANH</strong></p>
                        <p class="text-slate-500 text-[11px]">Nội dung CK: <strong class="text-slate-800 font-mono" id="viMemo"></strong></p>
                    </div>

                    <div class="w-24 h-24 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm shrink-0">
                        <img id="viQrCodeImg" src="" alt="VietQR" class="w-full h-full object-contain">
                    </div>
                </div>

                <!-- BOTTOM ACTIONS TOOLBAR -->
                <div class="space-y-2.5 pt-2">
                    <div id="viCollectBtnContainer">
                        <button onclick="confirmCollectMoneyFromInvoiceModal()" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-glow transition-all">
                            <i class="fa-solid fa-circle-check text-sm"></i>
                            <span>XÁC NHẬN ĐÃ THU TIỀN HÓA ĐƠN</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <button onclick="sendZaloInvoiceFromModal()" class="py-2.5 px-3 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold flex items-center justify-center gap-2 transition-all">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Gửi Zalo Cho Khách</span>
                        </button>
                        <button onclick="window.print()" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold flex items-center justify-center gap-2 transition-all">
                            <i class="fa-solid fa-print"></i>
                            <span>In File PDF Hóa Đơn</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 6: XEM HỢP ĐỒNG CHI TIẾT (TỜ GIẤY HỢP ĐỒNG PHÁP LÝ A4 ĐỒNG BỘ MÀU SẮC) -->
    <!-- ========================================================================= -->
    <div id="viewContractModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-2 sm:p-5 overflow-y-auto print:p-0 print:bg-white">
        <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl overflow-hidden border border-slate-200 max-h-[94vh] flex flex-col transform transition-all my-auto print:border-none print:shadow-none print:my-0 print:max-h-none">
            
            <!-- TOP TOOLBAR: Thanh tiêu đề màu sáng trang nhã, đồng bộ tuyệt đối với tờ hợp đồng -->
            <div class="bg-white text-slate-800 px-5 sm:px-6 py-3.5 flex items-center justify-between shrink-0 border-b border-slate-200 print:hidden">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Văn Bản Hợp Đồng Pháp Lý
                    </span>
                    <span class="text-xs text-slate-500 font-mono hidden sm:inline" id="vcContractCodeHeader">Mã HĐ: HD-202610-D5FB</span>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs text-slate-700 font-sans font-semibold transition flex items-center gap-1.5 border border-slate-300">
                        <i class="fa-solid fa-print"></i>
                        <span>In A4</span>
                    </button>
                    <button onclick="closeViewContractModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition border border-slate-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
            </div>

            <!-- VIEWPORT CUỘN: Nền sáng nhẹ nhàng giúp tờ giấy trắng nổi bật liền mạch -->
            <div class="p-3 sm:p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1 bg-slate-100/90 print:p-0 print:bg-white print:overflow-visible">
                
                <!-- TỜ GIẤY A4 PHÁP LÝ (LEGAL CONTRACT SHEET): Nền trắng tinh khiết liền mạch 100% -->
                <div id="legalContractSheet" 
                     class="w-full max-w-3xl mx-auto bg-white text-slate-900 px-6 py-8 sm:px-12 sm:py-14 md:px-16 md:py-16 shadow-xl border border-slate-200/90 rounded-xl leading-relaxed text-[13.5px] sm:text-[14px] relative print:shadow-none print:border-none print:p-0 print:bg-white"
                     style="background-color: #ffffff !important; min-height: 100%; height: auto !important; font-family: 'Times New Roman', Times, 'Liberation Serif', serif;">

                    <!-- 1. QUỐC HIỆU & TIÊU NGỮ CHUẨN THỂ THỨC -->
                    <div class="text-center mb-6">
                        <p class="font-bold uppercase tracking-wider text-sm sm:text-base text-slate-900">
                            CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
                        </p>
                        <p class="font-bold text-sm sm:text-base text-slate-900 mt-0.5">
                            Độc lập - Tự do - Hạnh phúc
                        </p>
                        <div class="w-36 h-[1.5px] bg-slate-900 mx-auto mt-1.5 mb-4"></div>
                        <p class="text-right italic text-xs sm:text-sm text-slate-700" id="vcLegalAutoDate">
                            Hà Nội, ngày 11 tháng 10 năm 2026
                        </p>
                    </div>

                    <!-- 2. TIÊU ĐỀ LỚN Ở GIỮA -->
                    <div class="text-center my-6">
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-950">
                            HỢP ĐỒNG CHO THUÊ PHÒNG TRỌ / CĂN HỘ
                        </h3>
                        <p class="text-xs sm:text-sm italic text-slate-600 mt-1 font-sans" id="vcContractCode">
                            Mã HĐ: HD-202610-D5FB
                        </p>
                    </div>

                    <!-- CĂN CỨ PHÁP LÝ -->
                    <p class="italic text-xs sm:text-[13px] text-slate-600 mb-4 text-justify indent-6 leading-normal">
                        - Căn cứ Bộ luật Dân sự số 91/2015/QH13 và Luật Nhà ở hiện hành;<br/>
                        - Căn cứ vào thỏa thuận tự nguyện và trách nhiệm dân sự của hai bên tham gia ký kết.
                    </p>
                    <p class="mb-3 text-slate-800">Hôm nay, hai bên chúng tôi gồm có:</p>

                    <!-- THÔNG TIN BÊN A (BÊN CHO THUÊ - CHỦ NHÀ) -->
                    <div class="space-y-1.5 mb-4 pl-3 sm:pl-4 border-l-2 border-slate-300">
                        <p class="font-bold uppercase text-slate-950 text-xs sm:text-sm">BÊN A (BÊN CHO THUÊ):</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 gap-x-4 text-xs sm:text-sm">
                            <p>Họ và tên: <strong class="uppercase text-slate-950" id="vcPartyAName">{{ Auth::user()->account_name ?? Auth::user()->name ?? 'Nguyễn Văn Tuấn' }}</strong></p>
                            <p>CCCD/CMND: <span class="font-semibold font-mono" id="vcPartyAIdCard">{{ Auth::user()->id_card ?? '001089012345' }}</span></p>
                            <p>Số điện thoại: <span class="font-semibold font-mono" id="vcPartyAPhone">{{ Auth::user()->phone ?? '0988 777 666' }}</span></p>
                            <p>Địa chỉ: <span id="vcPartyAAddress">{{ Auth::user()->address ?? 'Số 18 Trần Phú, Mộ Lao, Hà Đông, TP. Hà Nội' }}</span></p>
                        </div>
                    </div>

                    <!-- THÔNG TIN BÊN B (BÊN THUÊ) -->
                    <div class="space-y-1.5 mb-5 pl-3 sm:pl-4 border-l-2 border-slate-300">
                        <p class="font-bold uppercase text-slate-950 text-xs sm:text-sm">BÊN B (BÊN THUÊ):</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 gap-x-4 text-xs sm:text-sm">
                            <p>Họ và tên: <strong class="uppercase text-slate-950" id="vcTenant">--</strong></p>
                            <p>CCCD/CMND: <span class="font-semibold font-mono" id="vcIdCard">--</span></p>
                            <p>Số điện thoại: <span class="font-semibold font-mono text-slate-950" id="vcPhone">--</span></p>
                            <p>Địa chỉ liên hệ: <span id="vcTenantAddress">Theo CCCD / Thông tin đăng ký thuê</span></p>
                        </div>
                    </div>

                    <p class="italic mb-4 text-justify text-slate-700">Hai bên cùng thỏa thuận và thống nhất ký kết hợp đồng thuê phòng với các điều khoản sau:</p>

                    <!-- NỘI DUNG CÁC ĐIỀU KHOẢN VĂN XUÔI -->
                    <div class="space-y-4 text-justify leading-relaxed text-slate-900">
                        
                        <!-- ĐIỀU 1: THÔNG TIN PHÒNG THUÊ -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 1. Thông tin phòng thuê</p>
                            <p class="mt-1 indent-6">
                                Bên A đồng ý cho Bên B thuê phòng số: 
                                <strong class="underline decoration-dotted text-slate-950 font-bold" id="vcRoomName">--</strong> 
                                (Mã phòng: <span id="vcRoomCode" class="font-bold">--</span>), 
                                tại địa chỉ: <span id="vcAddress" class="font-semibold text-slate-950">Số 18 Trần Phú, Hà Đông, TP. Hà Nội</span>.
                                Phòng được bàn giao với đầy đủ các trang thiết bị cơ bản và Bên B có trách nhiệm sử dụng đúng mục đích để ở.
                            </p>
                        </div>

                        <!-- ĐIỀU 2: GIÁ CẢ VÀ THỜI HẠN -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 2. Thời hạn thuê, giá cả và tiền đặt cọc</p>
                            <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                <li>
                                    <strong>Thời hạn thuê:</strong> <span class="font-semibold" id="vcDuration">12 tháng</span>, 
                                    từ ngày <span class="font-semibold" id="vcDates">--</span>.
                                </li>
                                <li>
                                    <strong>Giá thuê phòng:</strong> 
                                    <strong class="text-slate-950 font-bold" id="vcRentPrice">--</strong> 
                                    (Giá thuê cố định, thanh toán theo chu kỳ thỏa thuận).
                                </li>
                                <li>
                                    <strong>Tiền đặt cọc đảm bảo:</strong> 
                                    <strong class="text-slate-950 font-bold" id="vcDeposit">--</strong>. 
                                    Tiền cọc được Bên A bảo toàn và hoàn trả 100% cho Bên B khi kết thúc hợp đồng theo đúng cam kết sau khi trừ các khoản chi phí phát sinh hư hại (nếu có).
                                </li>
                            </ul>
                        </div>

                        <!-- ĐIỀU 3: PHÍ DỊCH VỤ ĐI KÈM -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 3. Phí dịch vụ đi kèm</p>
                            <p class="mt-1 indent-6">
                                Ngoài tiền thuê phòng cố định hàng tháng, Bên B có nghĩa vụ thanh toán đầy đủ các chi phí dịch vụ thực tế phát sinh định kỳ gồm:
                            </p>
                            <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                <li>
                                    <strong>Tiền điện tiêu thụ:</strong> 
                                    <strong id="vcElecPrice" class="font-semibold text-slate-950">--</strong> 
                                    (<span id="vcElecStart" class="text-slate-600 font-mono">Chỉ số ban đầu: 0 kWh</span>).
                                </li>
                                <li>
                                    <strong id="vcWaterTypeLabel">Tiền nước sinh hoạt:</strong> 
                                    <strong id="vcWaterPrice" class="font-semibold text-slate-950">--</strong> 
                                    (<span id="vcWaterSubInfo" class="text-slate-600 font-mono">Chỉ số ban đầu: 0 m³</span>).
                                </li>
                                <li>
                                    <strong>Phí Internet và Rác sinh hoạt:</strong> 
                                    <strong id="vcInternetGarbagePrice" class="font-semibold text-slate-950">--</strong> 
                                    (Internet/Wifi: <span id="vcInternetPrice">--</span>, Rác sinh hoạt: <span id="vcGarbagePrice">--</span>).
                                </li>
                            </ul>
                        </div>

                        <!-- ĐIỀU 4: CAM KẾT THANH TOÁN -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 4. Cam kết và quy định thanh toán</p>
                            <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                <li>
                                    <strong>Chu kỳ thanh toán:</strong> 
                                    Là <span class="font-semibold" id="vcPaymentCycle">1 tháng (Hàng tháng)</span>.
                                </li>
                                <li>
                                    <strong>Ngày chốt tiền:</strong> 
                                    Định kỳ vào ngày <strong id="vcBillDate" class="underline text-slate-950">Mùng 10 hàng tháng</strong>.
                                </li>
                                <li>
                                    <strong>Quy định chậm thanh toán:</strong> 
                                    Bên B cam kết thanh toán đúng hạn. Trường hợp chậm trễ, Bên B chấp thuận áp dụng mức 
                                    <strong class="text-rose-700" id="vcPenaltyBadge">Phạt trễ hạn: 20.000 VNĐ / ngày</strong>.
                                </li>
                            </ul>
                        </div>

                        <!-- ĐIỀU 5: CAM KẾT CHUNG -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 5. Cam kết chung và hiệu lực hợp đồng</p>
                            <p class="mt-1 indent-6">
                                Hợp đồng này được xác lập có đầy đủ hiệu lực pháp lý theo tiêu chuẩn hệ thống RentHome. Hai bên cam kết thực hiện đúng và đầy đủ các điều khoản trên. Hợp đồng được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản để thi hành.
                            </p>
                        </div>

                    </div>

                    <!-- 3. HAI CỘT CHỮ KÝ ĐẠI DIỆN BÊN A VÀ BÊN B -->
                    <div class="grid grid-cols-2 gap-8 text-center mt-12 pt-4 border-t border-slate-200">
                        <div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-950">
                                ĐẠI DIỆN BÊN A
                            </p>
                            <p class="text-[11px] sm:text-xs italic text-slate-500">
                                (Ký và ghi rõ họ tên)
                            </p>
                            <div class="h-20 sm:h-24 flex items-center justify-center">
                                <span class="text-xs italic text-slate-400 font-sans border border-dashed border-slate-300 rounded px-3 py-1.5 select-none">[Chủ nhà đã phê duyệt]</span>
                            </div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-900 mt-1" id="vcSignPartyA">
                                {{ Auth::user()->account_name ?? Auth::user()->name ?? 'Nguyễn Văn Tuấn' }}
                            </p>
                        </div>

                        <div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-950">
                                ĐẠI DIỆN BÊN B
                            </p>
                            <p class="text-[11px] sm:text-xs italic text-slate-500">
                                (Ký và ghi rõ họ tên)
                            </p>
                            <div class="h-20 sm:h-24 flex items-center justify-center">
                                <span class="text-xs italic text-slate-400 font-sans border border-dashed border-slate-300 rounded px-3 py-1.5 select-none">[Chờ khách thuê ký]</span>
                            </div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-900 mt-1" id="vcSignPartyB">
                                --
                            </p>
                        </div>
                    </div>

                </div>
                <!-- END TỜ GIẤY A4 -->

            </div>

            <!-- ================================================================= -->
            <!-- STICKY FOOTER CỐ ĐỊNH Ở DƯỚI CÙNG MODAL (NỀN TRẮNG ĐỒNG BỘ 100%) -->
            <!-- ================================================================= -->
            <div class="sticky bottom-0 z-20 bg-white px-5 sm:px-6 py-4 border-t border-slate-200 shadow-[0_-8px_25px_rgba(0,0,0,0.06)] flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0 print:hidden">
                
                <!-- Tổng thanh toán đợt đầu -->
                <div class="text-left w-full sm:w-auto">
                    <span class="text-xs text-slate-500 font-sans block">
                        Tổng tiền tháng đầu + Cọc:
                    </span>
                    <strong class="text-xl sm:text-2xl font-black text-amber-600 font-sans tracking-tight" id="vcTotalInitialPay">
                        30.000.000đ
                    </strong>
                </div>

                <!-- Nhóm nút bấm -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <!-- Nút Thanh lý tiện ích -->
                    <a id="vcBtnLiquidate" href="{{ url('qlvan_hanh/thanh_ly_cong_no') }}" class="px-3.5 py-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-sans text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-contract"></i>
                        <span>Thanh Lý HĐ</span>
                    </a>

                    <!-- Nút Từ Chối (Màu xám Outline) -->
                    <button type="button" onclick="closeViewContractModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-sans text-xs sm:text-sm font-semibold transition">
                        Từ chối
                    </button>

                    <!-- Nút Chính: Xác nhận ký & Thanh toán: [Tổng tiền tháng đầu + Cọc] (Màu cam nổi bật) -->
                    <button type="button" id="vcBtnConfirmSign" onclick="confirmSignAndPayContract()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-600 active:scale-[0.98] text-white font-sans text-xs sm:text-sm font-bold shadow-lg shadow-orange-500/25 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-signature"></i>
                        <span id="vcBtnConfirmSignText">Xác nhận ký & Thanh toán: 30.000.000đ</span>
                    </button>
                </div>

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
                
                // Cập nhật chấm trạng thái sang đã thu tiền mà không làm mất ảnh phòng
                const pingDot = targetCard.querySelector('.room-status-dot-ping');
                if (pingDot) pingDot.remove();
                const statusDot = targetCard.querySelector('.room-status-dot');
                if (statusDot) {
                    statusDot.className = "room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md";
                    statusDot.setAttribute('title', 'Đã thu tiền');
                }
                const imgContainer = targetCard.querySelector('.relative > div');
                if (imgContainer) {
                    imgContainer.classList.remove('border-rose-300', 'ring-2', 'ring-rose-100');
                    imgContainer.classList.add('border-slate-200');
                }
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
            const remainingPaid = document.querySelectorAll('.room-card').length - remainingUnpaid;

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
        let currentActiveInvoiceData = null;

        function openViewInvoiceModal(dataOrRoomCode, code, tenant, total, status) {
            let data = {};
            if (typeof dataOrRoomCode === 'object' && dataOrRoomCode !== null) {
                data = dataOrRoomCode;
            } else {
                data = {
                    room_code: dataOrRoomCode,
                    invoice_code: code,
                    tenant_name: tenant,
                    total_amount: total,
                    status: status,
                    rent_price: total,
                    elec_amount: 0,
                    water_amount: 0,
                    service_amount: 0,
                    deposit: 0
                };
            }
            currentActiveInvoiceData = data;

            // 1. Mã Hóa đơn (loại bỏ lặp prefix HD HD-)
            let rawCode = data.invoice_code || data.contract_code || data.room_code || '';
            rawCode = String(rawCode).replace(/^Mã HĐ:\s*/i, '').trim();
            rawCode = rawCode.replace(/^(HD-)+/i, 'HD-').replace(/^(HD\s+)+/i, 'HD-');
            document.getElementById('viCode').innerText = "Mã HĐ: " + rawCode;

            // 2. Tên khách & Mã phòng
            document.getElementById('viTenant').innerText = data.tenant_name || 'Khách thuê';
            document.getElementById('viRoomCode').innerText = data.room_code || '';

            // 3. Trạng thái thanh toán
            const isPaid = (data.status === 'paid' || data.status === 'Đã thu tiền' || (data.status_label && data.status_label.includes('Đã thu')));
            const badge = document.getElementById('viStatusBadge');
            const btnBox = document.getElementById('viCollectBtnContainer');
            const totalBox = document.getElementById('viTotalBox');

            if (isPaid) {
                badge.className = "mt-1 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs";
                badge.innerHTML = "<span class='w-2 h-2 rounded-full bg-emerald-600 inline-block'></span> ĐÃ THU TIỀN";
                if (btnBox) btnBox.classList.add('hidden');
                if (totalBox) {
                    totalBox.className = "p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-1";
                    const spanLabel = totalBox.querySelector('span');
                    const divAmount = totalBox.querySelector('div');
                    if (spanLabel) spanLabel.className = "text-xs font-bold text-emerald-600 uppercase tracking-wider block";
                    if (divAmount) divAmount.className = "text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight";
                }
            } else {
                badge.className = "mt-1 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-extrabold text-xs";
                badge.innerHTML = "<span class='w-2 h-2 rounded-full bg-rose-600 inline-block'></span> CHƯA THU TIỀN";
                if (btnBox) btnBox.classList.remove('hidden');
                if (totalBox) {
                    totalBox.className = "p-5 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-1";
                    const spanLabel = totalBox.querySelector('span');
                    const divAmount = totalBox.querySelector('div');
                    if (spanLabel) spanLabel.className = "text-xs font-bold text-rose-600 uppercase tracking-wider block";
                    if (divAmount) divAmount.className = "text-2xl sm:text-3xl font-black text-rose-600 tracking-tight";
                }
            }

            // 4. Bảng chi tiết hạng mục phí (Dữ liệu ĐỘNG 100% Khớp Hóa Đơn)
            const tbody = document.getElementById('viTableBody');
            if (tbody) {
                tbody.innerHTML = '';

                // Hàng 1: Tiền phòng
                const rentVal = parseFloat(data.rent_price) || 0;
                const periodText = data.billing_period || 'Trọn 1 tháng';
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3 px-4 flex items-center gap-2 font-bold">
                            <i class="fa-solid fa-house-user text-brand-600"></i> Tiền phòng kỳ này
                        </td>
                        <td class="py-3 px-4 text-center text-slate-500">${periodText}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-slate-900">${formatContractVnd(rentVal)}</td>
                    </tr>
                `;

                // Hàng 2: Tiền điện
                const elecAmount = parseFloat(data.elec_amount) || 0;
                const elecUsage = parseFloat(data.elec_usage) || 0;
                const elecRate = parseFloat(data.elec_rate) || 3500;
                let elecDesc = `${elecUsage} kWh x ${formatContractVnd(elecRate)}`;
                if (elecUsage === 0) {
                    elecDesc = `0 kWh (Chỉ số: ${data.elec_old || 0})`;
                } else if (data.elec_old !== undefined && data.elec_new !== undefined) {
                    elecDesc = `${elecUsage} kWh (${data.elec_old} → ${data.elec_new}) x ${formatContractVnd(elecRate)}`;
                }
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3 px-4 flex items-center gap-2 font-bold">
                            <i class="fa-solid fa-bolt text-amber-500"></i> Tiền điện hàng tháng
                        </td>
                        <td class="py-3 px-4 text-center text-slate-500">${elecDesc}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-amber-700">${formatContractVnd(elecAmount)}</td>
                    </tr>
                `;

                // Hàng 3: Tiền nước
                const waterAmount = parseFloat(data.water_amount) || 0;
                const waterUsage = parseFloat(data.water_usage) || 0;
                const waterRate = parseFloat(data.water_rate) || 15000;
                let waterDesc = `${waterUsage} m³ x ${formatContractVnd(waterRate)}`;
                if (waterUsage === 0) {
                    waterDesc = `0 m³ (Chỉ số: ${data.water_old || 0})`;
                } else if (data.water_old !== undefined && data.water_new !== undefined) {
                    waterDesc = `${waterUsage} m³ (${data.water_old} → ${data.water_new}) x ${formatContractVnd(waterRate)}`;
                }
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-3 px-4 flex items-center gap-2 font-bold">
                            <i class="fa-solid fa-droplet text-skybrand-500"></i> Tiền nước hàng tháng
                        </td>
                        <td class="py-3 px-4 text-center text-slate-500">${waterDesc}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-skybrand-700">${formatContractVnd(waterAmount)}</td>
                    </tr>
                `;

                // Hàng 4: Phí dịch vụ (Wifi, rác...)
                const serviceAmount = parseFloat(data.service_amount) || 0;
                if (serviceAmount > 0 || (data.wifi_price > 0 || data.garbage_price > 0)) {
                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 flex items-center gap-2 font-bold">
                                <i class="fa-solid fa-wifi text-indigo-500"></i> Phí Wifi & Vệ sinh rác
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500">Gói cố định</td>
                            <td class="py-3 px-4 text-right font-extrabold text-indigo-700">${formatContractVnd(serviceAmount)}</td>
                        </tr>
                    `;
                }

                // Hàng 5: Tiền cọc (nếu có trong hóa đơn này, ví dụ tháng đầu tiên)
                const depositVal = parseFloat(data.deposit) || 0;
                if (depositVal > 0) {
                    tbody.innerHTML += `
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 flex items-center gap-2 font-bold">
                                <i class="fa-solid fa-shield-halved text-emerald-600"></i> Tiền cọc giữ phòng (Kỳ đầu)
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500">Cọc bàn giao phòng</td>
                            <td class="py-3 px-4 text-right font-extrabold text-emerald-700">${formatContractVnd(depositVal)}</td>
                        </tr>
                    `;
                }
            }

            // 5. Tổng tiền thanh toán
            const totalNum = parseFloat(data.total_amount) || (parseFloat(data.rent_price || 0) + parseFloat(data.elec_amount || 0) + parseFloat(data.water_amount || 0) + parseFloat(data.service_amount || 0) + parseFloat(data.deposit || 0));
            document.getElementById('viTotal').innerText = formatContractVnd(totalNum);

            // 6. Ngân hàng & VietQR
            const bankName = data.bank_name || 'VietBank';
            const bankAccount = data.bank_account || '00939399884747';
            const bankHolder = data.bank_holder || 'NGUYEN THI VAN ANH';
            const memo = data.memo || (`${rawCode.replace(/[^A-Za-z0-9]/g, '')} THANG 10`);

            const elBankName = document.getElementById('viBankName');
            const elBankAcc = document.getElementById('viBankAccount');
            const elBankHolder = document.getElementById('viBankHolder');
            const elMemo = document.getElementById('viMemo');

            if (elBankName) elBankName.innerText = bankName;
            if (elBankAcc) elBankAcc.innerText = bankAccount;
            if (elBankHolder) elBankHolder.innerText = bankHolder;
            if (elMemo) elMemo.innerText = memo;

            // Mã VietQR link
            const cleanBankCode = bankName.toLowerCase().replace(/[^a-z0-9]/g, '') || 'vietbank';
            const qrImg = document.getElementById('viQrCodeImg');
            if (qrImg) {
                qrImg.src = `https://img.vietqr.io/image/${cleanBankCode}-${bankAccount}-compact2.png?amount=${Math.round(totalNum)}&addInfo=${encodeURIComponent(memo)}&accountName=${encodeURIComponent(bankHolder)}`;
            }

            document.getElementById('viewInvoiceModal').classList.remove('hidden');
        }

        function sendZaloInvoiceFromModal() {
            if (!currentActiveInvoiceData) return;
            const d = currentActiveInvoiceData;
            const code = document.getElementById('viCode').innerText;
            const total = document.getElementById('viTotal').innerText;
            const tenant = d.tenant_name || document.getElementById('viTenant').innerText;
            const roomCode = d.room_code || document.getElementById('viRoomCode').innerText;
            const bankName = d.bank_name || 'VietBank';
            const bankAccount = d.bank_account || '00939399884747';
            const bankHolder = d.bank_holder || 'NGUYEN THI VAN ANH';

            let msg = `[RentHome] Kính gửi anh/chị ${tenant},\nRentHome xin gửi thông báo ${code} cho phòng ${roomCode} (${d.billing_period || 'tháng này'}):\n`;
            msg += `- Tiền phòng: ${formatContractVnd(d.rent_price)}\n`;
            if (parseFloat(d.elec_amount) > 0) msg += `- Tiền điện: ${formatContractVnd(d.elec_amount)} (${d.elec_usage || 0} kWh)\n`;
            if (parseFloat(d.water_amount) > 0) msg += `- Tiền nước: ${formatContractVnd(d.water_amount)} (${d.water_usage || 0} m³)\n`;
            if (parseFloat(d.service_amount) > 0) msg += `- Dịch vụ (Wifi, rác): ${formatContractVnd(d.service_amount)}\n`;
            if (parseFloat(d.deposit) > 0) msg += `- Tiền cọc giữ phòng: ${formatContractVnd(d.deposit)}\n`;
            msg += `=> TỔNG CỘNG: ${total}\n`;
            msg += `Thông tin chuyển khoản:\n- Ngân hàng: ${bankName}\n- STK: ${bankAccount}\n- Chủ TK: ${bankHolder}\n- Nội dung: ${d.memo || roomCode}\nXin cảm ơn!`;

            navigator.clipboard.writeText(msg).then(() => {
                showToast("Đã sao chép nội dung tin nhắn Zalo gửi khách thuê!");
            }).catch(() => {
                alert("Nội dung tin nhắn Zalo:\n\n" + msg);
            });
        }

        function closeViewInvoiceModal() {
            document.getElementById('viewInvoiceModal').classList.add('hidden');
        }

        function confirmCollectMoneyFromInvoiceModal() {
            if (!currentActiveInvoiceData) {
                closeViewInvoiceModal();
                return;
            }
            const roomCode = currentActiveInvoiceData.room_code;
            const contractId = currentActiveInvoiceData.contract_code || roomCode;

            // Gọi API lưu trạng thái sang DB
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            fetch("{{ url('qlvan_hanh/update_payment_status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    contract_id: contractId,
                    status: 'paid'
                })
            }).catch(e => console.error(e));

            // Cập nhật card trên giao diện
            const targetCard = document.getElementById('card-' + roomCode);
            if (targetCard) {
                targetCard.classList.remove('unpaid', 'border-rose-200');
                targetCard.classList.add('paid', 'border-slate-200/90');
                targetCard.dataset.status = 'paid';

                const pingDot = targetCard.querySelector('.room-status-dot-ping');
                if (pingDot) pingDot.remove();
                const statusDot = targetCard.querySelector('.room-status-dot');
                if (statusDot) {
                    statusDot.className = "room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md";
                    statusDot.setAttribute('title', 'Đã thu tiền');
                }
                const imgContainer = targetCard.querySelector('.relative > div');
                if (imgContainer) {
                    imgContainer.classList.remove('border-rose-300', 'ring-2', 'ring-rose-100');
                    imgContainer.classList.add('border-slate-200');
                }
                const badgeContainer = targetCard.querySelector('[id^="room-status-badge-"]');
                if (badgeContainer) {
                    badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN</span>`;
                }
                const debtContainer = targetCard.querySelector('[id^="room-debt-status-"]');
                if (debtContainer) {
                    debtContainer.innerHTML = `<span>Trạng thái: <strong class="text-emerald-600 font-bold">Đã thu tiền</strong></span>`;
                }
            }

            // Cập nhật số lượng thống kê
            const remainingUnpaid = document.querySelectorAll('.room-card.unpaid').length;
            const remainingPaid = document.querySelectorAll('.room-card').length - remainingUnpaid;
            const statPaid = document.getElementById('statPaidCount');
            const statUnpaid = document.getElementById('statUnpaidCount');
            if (statPaid) statPaid.innerText = remainingPaid + " Phòng";
            if (statUnpaid) statUnpaid.innerText = remainingUnpaid + " Phòng";

            closeViewInvoiceModal();
            showToast("Đã xác nhận thu tiền phòng " + roomCode + " thành công! Chuyển phòng sang Chấm Xanh.");
        }

        // Helper format VNĐ
        function formatContractVnd(val) {
            if (val === null || val === undefined || val === '') return '0đ';
            if (typeof val === 'string' && val.includes('đ')) return val;
            const num = parseFloat(String(val).replace(/[^\d.]/g, '')) || 0;
            return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
        }

        // 6. XEM HỢP ĐỒNG CHI TIẾT (TỜ GIẤY PHÁP LÝ A4)
        function openViewContractModal(data, contractCode, tenant, phone, price, deposit, startDate, endDate) {
            let info = {};
            if (typeof data === 'object' && data !== null) {
                info = data;
            } else {
                info = {
                    room_code: data,
                    contract_code: contractCode,
                    tenant_name: tenant,
                    tenant_phone: phone,
                    price: price,
                    deposit: deposit,
                    contract_start: startDate,
                    contract_end: endDate
                };
            }

            // Tự động sinh ngày tháng năm pháp lý theo thời gian thực (Hà Nội, ngày ... tháng ... năm ...)
            const now = new Date();
            const dayStr = String(now.getDate()).padStart(2, '0');
            const monthStr = String(now.getMonth() + 1).padStart(2, '0');
            const yearStr = now.getFullYear();
            if (document.getElementById('vcLegalAutoDate')) {
                document.getElementById('vcLegalAutoDate').innerText = `Hà Nội, ngày ${dayStr} tháng ${monthStr} năm ${yearStr}`;
            }

            // 1. Bên thuê & Phòng thuê
            const roomCode = info.room_code || '--';
            const cCode = info.contract_code || roomCode || '--';
            if (document.getElementById('vcRoomCode')) document.getElementById('vcRoomCode').innerText = roomCode;
            if (document.getElementById('vcContractCode')) document.getElementById('vcContractCode').innerText = "Mã HĐ: " + cCode;
            if (document.getElementById('vcContractCodeHeader')) document.getElementById('vcContractCodeHeader').innerText = "Mã HĐ: " + cCode;
            if (document.getElementById('vcTenant')) document.getElementById('vcTenant').innerText = info.tenant_name || '--';
            if (document.getElementById('vcPhone')) document.getElementById('vcPhone').innerText = info.tenant_phone || '--';
            if (document.getElementById('vcIdCard')) {
                document.getElementById('vcIdCard').innerText = info.tenant_id_card || 'Chưa cập nhật';
            }
            if (document.getElementById('vcRoomName')) {
                document.getElementById('vcRoomName').innerText = info.name || ('Phòng ' + roomCode);
            }
            if (document.getElementById('vcTenantAddress')) {
                document.getElementById('vcTenantAddress').innerText = info.tenant_address || 'Theo CCCD / Hồ sơ đăng ký thuê';
            }
            if (document.getElementById('vcAddress')) {
                document.getElementById('vcAddress').innerText = info.address || 'Số 18 Trần Phú, Phường Mộ Lao, Quận Hà Đông, TP. Hà Nội';
            }

            // 2. Giá thuê & Tiền cọc & Thời hạn
            let rentPriceStr = '--';
            let rentPriceNum = 0;
            if (info.price !== undefined && info.price !== null) {
                rentPriceNum = typeof info.price === 'number' ? info.price : (parseFloat(String(info.price).replace(/[^\d.]/g, '')) || 0);
                rentPriceStr = typeof info.price === 'number' ? (formatContractVnd(info.price) + '/tháng') : (String(info.price).includes('/') ? info.price : info.price + '/tháng');
            }
            let depositStr = '--';
            let depositNum = 0;
            if (info.deposit !== undefined && info.deposit !== null) {
                depositNum = typeof info.deposit === 'number' ? info.deposit : (parseFloat(String(info.deposit).replace(/[^\d.]/g, '')) || 0);
                depositStr = typeof info.deposit === 'number' ? formatContractVnd(info.deposit) : String(info.deposit);
            }
            if (document.getElementById('vcRentPrice')) document.getElementById('vcRentPrice').innerText = rentPriceStr;
            if (document.getElementById('vcDeposit')) document.getElementById('vcDeposit').innerText = depositStr;

            const datesText = (info.contract_start || '--') + ' - ' + (info.contract_end || '--');
            if (document.getElementById('vcDates')) document.getElementById('vcDates').innerText = datesText;
            if (document.getElementById('vcDuration')) {
                document.getElementById('vcDuration').innerText = (info.duration_months || 12) + ' tháng';
            }

            // 3. Đơn giá Điện & Nước
            const elecPriceVal = info.electricity_price && info.electricity_price > 0 ? (formatContractVnd(info.electricity_price) + '/số') : '3.500đ/số';
            const elecStartVal = info.electricity_start !== undefined && info.electricity_start !== null ? info.electricity_start : 0;
            if (document.getElementById('vcElecPrice')) {
                document.getElementById('vcElecPrice').innerText = elecPriceVal;
            }
            if (document.getElementById('vcElecStart')) {
                document.getElementById('vcElecStart').innerText = `Chỉ số ban đầu: ${elecStartVal} kWh`;
            }

            // Nước theo khối hoặc theo người
            const waterType = info.water_calculation_type || 'khoi';
            if (waterType === 'nguoi') {
                if (document.getElementById('vcWaterTypeLabel')) {
                    document.getElementById('vcWaterTypeLabel').innerText = 'Tiền nước sinh hoạt (Theo người):';
                }
                const waterPersonVal = info.water_price_person && info.water_price_person > 0 ? (formatContractVnd(info.water_price_person) + '/người/tháng') : '100.000đ/người';
                if (document.getElementById('vcWaterPrice')) {
                    document.getElementById('vcWaterPrice').innerText = waterPersonVal;
                }
                if (document.getElementById('vcWaterSubInfo')) {
                    document.getElementById('vcWaterSubInfo').innerText = `Số người ở: ${info.water_people_count || 1} người`;
                }
            } else {
                if (document.getElementById('vcWaterTypeLabel')) {
                    document.getElementById('vcWaterTypeLabel').innerText = 'Tiền nước sinh hoạt (Theo khối):';
                }
                const waterBlockVal = info.water_price_block && info.water_price_block > 0 ? (formatContractVnd(info.water_price_block) + '/khối (m³)') : '25.000đ/khối';
                if (document.getElementById('vcWaterPrice')) {
                    document.getElementById('vcWaterPrice').innerText = waterBlockVal;
                }
                if (document.getElementById('vcWaterSubInfo')) {
                    document.getElementById('vcWaterSubInfo').innerText = `Chỉ số ban đầu: ${info.water_start || 0} m³`;
                }
            }

            // 4. Phí Dịch Vụ Cố Định (Internet + Rác) & Chu Kỳ
            const internetVal = info.internet_price && info.internet_price > 0 ? (formatContractVnd(info.internet_price) + '/tháng') : '100.000đ/tháng';
            const garbageVal = info.garbage_price && info.garbage_price > 0 ? (formatContractVnd(info.garbage_price) + '/tháng') : '30.000đ/tháng';
            if (document.getElementById('vcInternetPrice')) {
                document.getElementById('vcInternetPrice').innerText = internetVal;
            }
            if (document.getElementById('vcGarbagePrice')) {
                document.getElementById('vcGarbagePrice').innerText = garbageVal;
            }
            if (document.getElementById('vcInternetGarbagePrice')) {
                const totalService = (parseFloat(String(info.internet_price || 0).replace(/[^\d.]/g, '')) || 0) + (parseFloat(String(info.garbage_price || 0).replace(/[^\d.]/g, '')) || 0);
                document.getElementById('vcInternetGarbagePrice').innerText = totalService > 0 ? (formatContractVnd(totalService) + '/tháng') : (internetVal + ' + Rác: ' + garbageVal);
            }

            const billDateVal = info.bill_date ? `Mùng ${info.bill_date} hàng tháng` : (info.billing_date || 'Mùng 5 hàng tháng');
            if (document.getElementById('vcBillDate')) {
                document.getElementById('vcBillDate').innerText = billDateVal;
            }
            if (document.getElementById('vcPaymentCycle')) {
                document.getElementById('vcPaymentCycle').innerText = (info.payment_cycle || '1 tháng') + ` (Hạn trả: ${info.payment_limit_days || '5 ngày'})`;
            }
            if (document.getElementById('vcPenaltyBadge')) {
                document.getElementById('vcPenaltyBadge').innerText = `Phạt trễ hạn: ${info.late_penalty_fee || '20.000 VNĐ / ngày'}`;
            }

            // 5. Cột Chữ Ký Đại Diện Bên B
            if (document.getElementById('vcSignPartyB')) {
                document.getElementById('vcSignPartyB').innerText = info.tenant_name || 'Khách thuê';
            }

            // 6. Tính Tổng tiền thanh toán tháng đầu + Tiền cọc (Sticky Footer)
            const totalFirstPayment = rentPriceNum + depositNum;
            const totalFirstPaymentStr = formatContractVnd(totalFirstPayment);
            if (document.getElementById('vcTotalInitialPay')) {
                document.getElementById('vcTotalInitialPay').innerText = totalFirstPaymentStr;
            }
            if (document.getElementById('vcBtnConfirmSignText')) {
                document.getElementById('vcBtnConfirmSignText').innerText = `Xác nhận ký & Thanh toán: ${totalFirstPaymentStr}`;
            }

            // Link thanh lý hợp đồng
            const btnLiquidate = document.getElementById('vcBtnLiquidate');
            if (btnLiquidate) {
                btnLiquidate.href = `{{ url('qlvan_hanh/thanh_ly_cong_no') }}?room=${roomCode}`;
            }

            document.getElementById('viewContractModal').classList.remove('hidden');
        }

        function closeViewContractModal() {
            document.getElementById('viewContractModal').classList.add('hidden');
        }

        // Xử lý khi nhấn nút "Xác nhận ký & Thanh toán"
        function confirmSignAndPayContract() {
            const codeEl = document.getElementById('vcContractCode');
            const code = codeEl ? codeEl.innerText : 'Hợp đồng';
            const totalEl = document.getElementById('vcTotalInitialPay');
            const total = totalEl ? totalEl.innerText : '0đ';
            
            showToast(`Đã xác nhận ký ${code} thành công!`);
            setTimeout(() => {
                alert(`Xác nhận ký ${code} thành công!\nTổng tiền đợt đầu: ${total}.\nĐang tiến hành lập phiếu thu và kích hoạt hiệu lực pháp lý.`);
                closeViewContractModal();
            }, 300);
        }

        // 7. FORM TẠO HỢP ĐỒNG MỚI & CẤU HÌNH PHÒNG
        function openCreateContractModal() {
            calculateContractEndDate();
            document.getElementById('createContractModal').classList.remove('hidden');
        }

        function closeCreateContractModal() {
            document.getElementById('createContractModal').classList.add('hidden');
        }

        function switchWaterMode(mode) {
            const btnKhoi = document.getElementById('btnWaterModeKhoi');
            const btnNguoi = document.getElementById('btnWaterModeNguoi');
            const boxKhoi = document.getElementById('boxWaterKhoi');
            const boxNguoi = document.getElementById('boxWaterNguoi');

            if (mode === 'khoi') {
                btnKhoi.className = "px-3 py-1 rounded-lg bg-emerald-600 text-white shadow-xs transition-all";
                btnNguoi.className = "px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all";
                boxKhoi.classList.remove('hidden');
                boxNguoi.classList.add('hidden');
            } else {
                btnNguoi.className = "px-3 py-1 rounded-lg bg-emerald-600 text-white shadow-xs transition-all";
                btnKhoi.className = "px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all";
                boxNguoi.classList.remove('hidden');
                boxKhoi.classList.add('hidden');
            }
        }

        function calculateContractEndDate() {
            const startDateVal = document.getElementById('ccStartDate').value;
            const monthsVal = parseInt(document.getElementById('ccDurationMonths').value) || 0;
            const endDateDisplay = document.getElementById('ccEndDateDisplay');

            if (!startDateVal || monthsVal <= 0) {
                endDateDisplay.value = "--/--/----";
                return;
            }

            const startDate = new Date(startDateVal);
            if (isNaN(startDate.getTime())) return;

            startDate.setMonth(startDate.getMonth() + monthsVal);

            const day = String(startDate.getDate()).padStart(2, '0');
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const year = startDate.getFullYear();

            endDateDisplay.value = `${day}/${month}/${year}`;
        }

        function handleSaveCreateContract(e) {
            e.preventDefault();
            closeCreateContractModal();

            const selectedRoom = document.getElementById('ccRoom').value;
            showToast("Đã lưu dữ liệu hợp đồng mới vào CSDL (tbl_hop_dong) & tự động chuyển phòng " + selectedRoom + " sang 'Đã cho thuê' thành công!");
        }

        // Live formatting for quick contract modal inputs
        function bindQuickPriceFormat(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);
            if (!input || !display) return;
            const update = () => {
                const clean = String(input.value || '').replace(/[^\d]/g, '');
                const num = clean ? parseInt(clean, 10) : 0;
                display.innerText = num > 0 ? String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '0';
            };
            input.addEventListener('input', update);
            input.addEventListener('blur', () => {
                const clean = String(input.value || '').replace(/[^\d]/g, '');
                const num = clean ? parseInt(clean, 10) : 0;
                if (num > 0) input.value = String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                update();
            });
            update();
        }
        // KIỂM TRA HẠN THU TIỀN THỜI GIAN THỰC (REALTIME DUE CHECK)
        async function checkRealtimeBillingDue() {
            try {
                const response = await fetch('/api/operation/check-billing-due');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.due_count > 0) {
                        const alertBanner = document.getElementById('realtimeDueAlertBanner');
                        if (alertBanner) {
                            alertBanner.classList.remove('hidden');
                        }
                        // Khi đến hạn đóng tiền, tự động hiện nút Chưa thu / Đã thu ra cho các phòng đến hạn
                        if (data.due_rooms && Array.isArray(data.due_rooms)) {
                            data.due_rooms.forEach(r => {
                                const toggleGroup = document.getElementById(`payment-toggle-group-${r.contract_id}`);
                                if (toggleGroup) {
                                    toggleGroup.classList.remove('hidden');
                                }
                                const card = document.getElementById(`card-${r.room_code}`);
                                if (card) {
                                    card.setAttribute('data-status', 'unpaid');
                                    card.classList.remove('paid', 'border', 'border-slate-200/90');
                                    card.classList.add('unpaid', 'border-2', 'border-rose-200');
                                }
                                const badge = document.getElementById(`room-status-badge-${r.contract_id}`);
                                if (badge) {
                                    badge.innerHTML = '<span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> CHƯA THU TIỀN</span>';
                                }
                                const dotContainer = document.getElementById(`room-status-dot-container-${r.contract_id}`);
                                if (dotContainer) {
                                    dotContainer.innerHTML = '<span class="room-status-dot-ping absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md animate-ping"></span><span class="room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md" title="Chưa thu tiền"></span>';
                                }
                            });
                            recalculateHeaderCounts();
                        }
                    }
                }
            } catch (err) {
                console.debug("Check realtime due silently:", err);
            }
        }

        // CẬP NHẬT TRẠNG THÁI THU TIỀN TRỰC TIẾP TỪ BÊN NGOÀI DANH SÁCH PHÒNG
        async function updateRoomPaymentStatus(contractId, roomCode, newStatus, btnElement) {
            if (btnElement && btnElement.disabled) return;
            if (btnElement) {
                btnElement.disabled = true;
                btnElement.style.opacity = '0.7';
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                const response = await fetch("{{ route('qlvan_hanh.update_payment_status') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        contract_id: contractId,
                        status: newStatus
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message || (newStatus === 'paid' ? 'Đã thu tiền thành công!' : 'Đã chuyển thành Chưa thu tiền!'));

                    // 1. Cập nhật hiển thị/ẩn cụm nút trạng thái thu tiền: khi đã thu tiền thì ẩn đi, khi chưa thu tiền thì hiện ra
                    const toggleGroup = document.getElementById(`payment-toggle-group-${contractId}`);
                    if (toggleGroup) {
                        if (newStatus === 'paid') {
                            toggleGroup.classList.add('hidden');
                        } else {
                            toggleGroup.classList.remove('hidden');
                        }
                    }

                    const btnUnpaid = document.getElementById(`btn-status-unpaid-${contractId}`);
                    const btnPaid = document.getElementById(`btn-status-paid-${contractId}`);

                    if (newStatus === 'paid') {
                        if (btnPaid) {
                            btnPaid.className = 'px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-emerald-700 shadow-sm border border-emerald-300 ring-2 ring-emerald-500/20 font-extrabold';
                            btnPaid.innerHTML = '<span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-200"></span><span>Đã Thu Tiền</span>';
                        }
                        if (btnUnpaid) {
                            btnUnpaid.className = 'px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-500 hover:text-rose-600 hover:bg-white/60';
                            btnUnpaid.innerHTML = '<span class="w-2.5 h-2.5 rounded-full border-2 border-slate-400"></span><span>Chưa Thu Tiền</span>';
                        }
                    } else {
                        if (btnUnpaid) {
                            btnUnpaid.className = 'px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-rose-700 shadow-sm border border-rose-300 ring-2 ring-rose-500/20 font-extrabold';
                            btnUnpaid.innerHTML = '<span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-rose-200"></span><span>Chưa Thu Tiền</span>';
                        }
                        if (btnPaid) {
                            btnPaid.className = 'px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-500 hover:text-emerald-700 hover:bg-white/60';
                            btnPaid.innerHTML = '<span class="w-2.5 h-2.5 rounded-full border-2 border-slate-400"></span><span>Đã Thu Tiền</span>';
                        }
                    }

                    // 2. Cập nhật thẻ Card viền & data-status
                    const card = document.getElementById(`card-${roomCode}`);
                    if (card) {
                        card.setAttribute('data-status', newStatus);
                        if (newStatus === 'paid') {
                            card.classList.remove('unpaid', 'border-2', 'border-rose-200');
                            card.classList.add('paid', 'border', 'border-slate-200/90');
                        } else {
                            card.classList.remove('paid', 'border', 'border-slate-200/90');
                            card.classList.add('unpaid', 'border-2', 'border-rose-200');
                        }
                    }

                    // 3. Cập nhật avatar viền
                    const imgWrapper = document.getElementById(`room-img-wrapper-${contractId}`);
                    if (imgWrapper) {
                        if (newStatus === 'paid') {
                            imgWrapper.className = 'w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm shrink-0';
                        } else {
                            imgWrapper.className = 'w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden border border-rose-300 ring-2 ring-rose-100 bg-slate-100 shadow-sm shrink-0';
                        }
                    }

                    // 4. Cập nhật Chấm xanh / Chấm đỏ trên ảnh
                    const dotContainer = document.getElementById(`room-status-dot-container-${contractId}`);
                    if (dotContainer) {
                        if (newStatus === 'paid') {
                            dotContainer.innerHTML = '<span class="room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md" title="Đã thu tiền"></span>';
                        } else {
                            dotContainer.innerHTML = '<span class="room-status-dot-ping absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md animate-ping"></span><span class="room-status-dot absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-md" title="Chưa thu tiền"></span>';
                        }
                    }

                    // 5. Cập nhật Badge Trạng thái
                    const badgeContainer = document.getElementById(`room-status-badge-${contractId}`);
                    if (badgeContainer) {
                        if (newStatus === 'paid') {
                            badgeContainer.innerHTML = '<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> ĐÃ THU TIỀN</span>';
                        } else {
                            badgeContainer.innerHTML = '<span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> CHƯA THU TIỀN</span>';
                        }
                    }

                    // 6. Cập nhật dòng chữ Kỳ nợ / Trạng thái
                    const debtContainer = document.getElementById(`room-debt-status-${contractId}`);
                    if (debtContainer) {
                        if (newStatus === 'paid') {
                            debtContainer.innerHTML = '<span>Trạng thái: <strong class="text-emerald-600 font-bold">Đã thu tiền</strong></span>';
                        } else {
                            debtContainer.innerHTML = '<span>Kỳ nợ: <strong class="text-rose-600 font-bold">Kỳ này (Chưa thu)</strong></span>';
                        }
                    }

                    // 7. Cập nhật bộ đếm trên tiêu đề
                    recalculateHeaderCounts();

                } else {
                    showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                }
            } catch (err) {
                console.error('Update payment status error:', err);
                showToast('Không thể kết nối đến máy chủ!');
            } finally {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.style.opacity = '1';
                }
            }
        }

        // Cập nhật số đếm thống kê trên Header và nút bộ lọc
        function recalculateHeaderCounts() {
            const cards = document.querySelectorAll('.room-card');
            let paid = 0;
            let unpaid = 0;
            cards.forEach(c => {
                if (c.getAttribute('data-status') === 'paid') paid++;
                else if (c.getAttribute('data-status') === 'unpaid') unpaid++;
            });

            const statPaid = document.getElementById('statPaidCount');
            const statUnpaid = document.getElementById('statUnpaidCount');
            if (statPaid) statPaid.innerText = `${paid} Phòng`;
            if (statUnpaid) statUnpaid.innerText = `${unpaid} Phòng`;

            const filterPaidBtn = document.getElementById('filter-paid');
            const filterUnpaidBtn = document.getElementById('filter-unpaid');
            if (filterPaidBtn) {
                filterPaidBtn.innerHTML = `<span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shadow-sm"></span><span>Đã thu tiền (${paid})</span>`;
            }
            if (filterUnpaidBtn) {
                filterUnpaidBtn.innerHTML = `<span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block shadow-sm"></span><span>Chưa thu tiền (${unpaid})</span>`;
            }

            const alertBanner = document.getElementById('realtimeDueAlertBanner');
            if (alertBanner) {
                if (unpaid === 0) {
                    alertBanner.classList.add('hidden');
                } else {
                    alertBanner.classList.remove('hidden');
                }
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            bindQuickPriceFormat('quickElecPrice', 'quickElecDisplay');
            bindQuickPriceFormat('quickWaterPrice', 'quickWaterDisplay');
            checkRealtimeBillingDue();
            setInterval(checkRealtimeBillingDue, 60000);
        });
    </script>
</body>

</html>
