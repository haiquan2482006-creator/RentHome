<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn {{ $invoice->invoice_code }} | RentHome Quản Lý Vận Hành</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
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
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        emeraldbrand: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .print-container {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                padding: 20px !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col font-sans">

    <!-- TOP HEADER -->
    <header class="bg-[#0b132a] text-white py-4 px-4 sm:px-8 border-b border-slate-800 shadow-md no-print">
        <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('qlvan_hanh.room_list') }}" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white" title="Quay về Quản lý vận hành">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 uppercase tracking-wider">
                            <i class="fa-solid fa-receipt mr-1"></i> {{ $isFirstInvoice ? 'Hóa Đơn Thanh Toán Lần Đầu #1' : 'Hóa Đơn Tiền Nhà' }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">{{ $invoice->invoice_code }}</span>
                    </div>
                    <h1 class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                        {{ $post ? ($post->title ?? 'Phòng cho thuê') : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ') }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="copyInvoiceLink()" class="py-2 px-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-2 border border-white/20 transition-all">
                    <i class="fa-solid fa-copy"></i>
                    <span id="copyLinkBtnText">Sao Chép Link</span>
                </button>

                <button type="button" onclick="window.print()" class="py-2 px-3.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all">
                    <i class="fa-solid fa-print"></i>
                    <span>In Hóa Đơn / PDF</span>
                </button>

                <a href="{{ route('qlvan_hanh.room_list') }}" class="py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-md transition-all">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Danh Sách Phòng</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN BODY -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="no-print p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <div>
                    <span class="font-bold">{{ session('success') }}</span>
                    @if($isFirstInvoice)
                        <p class="text-xs text-emerald-700 font-normal mt-0.5">
                            Quý nhân sự vui lòng xác nhận khách thuê đã chuyển khoản số tiền cọc & kỳ đầu để kích hoạt hợp đồng và giao chìa khóa phòng.
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @php
            $rawStatus = strtolower($invoice->status ?? 'unpaid');
            $rawPayStatus = strtolower($invoice->payment_status ?? '');
            $isPendingApproval = ($rawStatus === 'pending_approval' || $rawPayStatus === 'pending_approval');
            $isPaid = ($rawStatus === 'paid' || $rawPayStatus === 'paid');
            $isCash = ($invoice->payment_method === 'cash');
            $isBank = ($invoice->payment_method === 'bank_transfer');
            $contractStatus = $contract ? ($contract->status ?? '') : '';
            $isContractActive = ($contractStatus === 'active');
        @endphp

        <!-- BANNER THÔNG BÁO VÒNG ĐỜI HỢP ĐỒNG & THANH TOÁN -->
        @if($isPendingApproval)
            <!-- BANNER CHỜ DUYỆT CHUYỂN KHOẢN (KHÓA AN TOÀN CHO NHÂN SỰ) -->
            <div id="pendingApprovalBanner" class="p-4 sm:p-5 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 transition-all">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md">
                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-amber-900">
                                Khách Đã Chuyển Khoản – Chờ Admin Kế Toán Duyệt
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-200 text-amber-900 border border-amber-300 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                                Đang Chờ Duyệt
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                            Khách thuê đã thực hiện chuyển khoản ngân hàng{{ $invoice->transfer_reported_at ? ' lúc ' . date('d/m/Y H:i', strtotime($invoice->transfer_reported_at)) : '' }}. Nhân viên vận hành tạm thời <strong>không thu tiền mặt</strong> để tránh thu trùng. Sau khi Admin Kế toán kiểm tra sao kê ngân hàng và bấm duyệt, hợp đồng sẽ tự động Kích hoạt!
                        </p>
                        @if(!empty($invoice->proof_image))
                            @php
                                $proofUrl = $invoice->proof_image;
                                if (!str_starts_with($proofUrl, 'http://') && !str_starts_with($proofUrl, 'https://')) {
                                    $proofUrl = asset(ltrim($proofUrl, '/'));
                                }
                            @endphp
                            <div class="mt-2.5 flex items-center gap-2">
                                <a href="{{ $proofUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs border border-amber-300 transition">
                                    <i class="fa-solid fa-receipt text-amber-700"></i> Xem Bill Chuyển Khoản Khách Đã Gửi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <button type="button" disabled class="shrink-0 w-full md:w-auto py-3 px-5 rounded-2xl bg-slate-200 text-slate-500 font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 border border-slate-300 cursor-not-allowed shadow-inner">
                    <i class="fa-solid fa-lock text-amber-600"></i>
                    <span>Đã Khóa – Chờ Kế Toán Duyệt</span>
                </button>
            </div>
        @elseif($isPaid)
            <div id="ruleBanner" class="p-4 sm:p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-950 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 transition-all">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                        <i class="fa-solid fa-key text-lg"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-emerald-950">
                                Hợp đồng đã Chính Thức Kích Hoạt (Active) – Sẵn sàng bàn giao phòng!
                            </h3>
                            <span id="badgeContractStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-200 text-emerald-900 border border-emerald-300">
                                Hợp đồng: Đang Hoạt Động (Active)
                            </span>
                        </div>
                        <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                            Khách thuê đã hoàn tất thanh toán hóa đơn. Phương thức thanh toán: <strong>{{ $isBank ? 'Chuyển khoản ngân hàng (Admin đã duyệt)' : ($isCash ? 'Tiền mặt (Nhân sự thu)' : 'Đã thanh toán') }}</strong>.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- CHƯA THANH TOÁN (PENDING / OVERDUE): NÚT XÁC NHẬN THU TIỀN MẶT NỔI BẬT -->
            <div id="ruleBanner" class="p-4 sm:p-5 rounded-3xl bg-amber-50/90 border-2 border-amber-300 text-amber-950 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 transition-all">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-md">
                        <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base">
                                {{ $isFirstInvoice ? 'Quy tắc thanh toán lần đầu: Tiền phòng trả trước – Điện nước trả sau' : 'Hóa đơn chưa thanh toán' }}
                            </h3>
                            <span id="badgeContractStatus" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-200 text-amber-900 border border-amber-300">
                                {{ $isFirstInvoice ? 'Hợp đồng: Chờ Đóng Cọc & Nhận Phòng' : 'Chưa Thanh Toán' }}
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                            @if($isFirstInvoice)
                                Theo quy chuẩn cho thuê, <strong>Khách phải thanh toán xong Hóa đơn #1 này thì Hợp đồng mới được Kích hoạt (Active)</strong> và mới được bàn giao chìa khóa. Nhân sự nếu đã nhận tiền mặt trực tiếp từ khách thuê, hãy bấm nút <strong>"Xác Nhận Thu Tiền Mặt"</strong> bên cạnh.
                            @else
                                Hóa đơn hiện đang ở trạng thái chưa thanh toán. Khách thuê có thể chuyển khoản VietQR hoặc thanh toán tiền mặt trực tiếp cho nhân viên vận hành.
                            @endif
                        </p>
                    </div>
                </div>

                <button type="button" onclick="confirmCashCollection()" id="btnActionPayTop" class="shrink-0 w-full md:w-auto py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all">
                    <i class="fa-solid fa-money-bill-wave text-base"></i>
                    <span>Xác Nhận Thu Tiền Mặt</span>
                </button>
            </div>
        @endif

        <!-- KHỐI HÓA ĐƠN CHÍNH (INVOICE PAPER DESIGN) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden print-container">
            
            <!-- HEADER HÓA ĐƠN -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-[#0b132a] text-white p-6 sm:p-8 relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-700 pb-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-base shadow-sm">
                                R
                            </span>
                            <span class="text-xl font-black tracking-tight text-white">RentHome Management</span>
                        </div>
                        <p class="text-xs text-slate-400">Hệ Thống Quản Lý Vận Hành & Cho Thuê Bất Động Sản</p>
                        <p class="text-xs text-slate-400">Hotline: 0987.654.321 – Email: hotro@renthome.vn</p>
                    </div>

                    <div class="text-left sm:text-right space-y-1">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider 
                            @if($isPendingApproval) bg-amber-500/20 text-amber-400 border border-amber-400/40 
                            @elseif($isPaid) 
                                @if($isBank) bg-emerald-500/20 text-emerald-400 border border-emerald-400/40 
                                @else bg-teal-500/20 text-teal-300 border border-teal-400/40 
                                @endif
                            @else bg-rose-500/20 text-rose-400 border border-rose-400/40 
                            @endif" id="invoiceStatusBadge">
                            <span class="w-2 h-2 rounded-full 
                                @if($isPendingApproval) bg-amber-400 animate-pulse 
                                @elseif($isPaid) bg-emerald-400 
                                @else bg-rose-400 animate-ping 
                                @endif"></span>
                            <span id="invoiceStatusText">
                                @if($isPendingApproval) KHÁCH ĐÃ CHUYỂN KHOẢN - CHỜ DUYỆT
                                @elseif($isPaid) 
                                    @if($isBank) ĐÃ THANH TOÁN (CHUYỂN KHOẢN)
                                    @else ĐÃ THU TIỀN MẶT
                                    @endif
                                @else CHƯA THANH TOÁN (UNPAID)
                                @endif
                            </span>
                        </div>
                        <p class="text-sm font-extrabold text-white font-mono mt-1">Mã HĐ: {{ $invoice->invoice_code }}</p>
                        <p class="text-xs text-slate-400">Mã Hợp Đồng: <span class="font-bold text-slate-200">{{ $contract ? ($contract->contract_code ?? 'HD-MOI') : 'HD-MOI' }}</span></p>
                        <p class="text-xs text-slate-400">Ngày lập phiếu: {{ $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i') : date('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- THÔNG TIN 2 BÊN -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 text-xs text-slate-300">
                    <div class="space-y-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Bên Cho Thuê / Tòa Nhà:</span>
                        <p class="text-sm font-extrabold text-white">{{ $post ? ($post->title ?? 'Tòa nhà RentHome') : 'Phòng cho thuê RentHome' }}</p>
                        <p><i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i>{{ $post->address ?? 'Số 18 Trần Phú, Mộ Lao, Hà Đông, Hà Nội' }}</p>
                        <p><i class="fa-solid fa-calendar-day text-sky-400 mr-1.5"></i>Kỳ thanh toán: <strong class="text-white">{{ $invoice->billing_period ?: ('Tháng ' . $invoice->billing_month) }}</strong></p>
                    </div>

                    <div class="space-y-1.5 bg-white/5 p-4 rounded-2xl border border-white/10">
                        <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">Khách Thuê Phòng (Bên Thuê):</span>
                        <p class="text-sm font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-user text-emerald-400"></i>
                            <span>{{ $contract ? ($contract->tenant_name ?? 'Khách thuê') : ($invoice->tenant_name ?? 'Khách thuê') }}</span>
                        </p>
                        <p><i class="fa-solid fa-phone text-emerald-400 mr-1.5"></i>Số điện thoại: <strong class="text-white">{{ $contract ? ($contract->tenant_phone ?? 'Chưa cập nhật') : 'Chưa cập nhật' }}</strong></p>
                        @if($contract && !empty($contract->tenant_id_card))
                            <p><i class="fa-solid fa-id-card text-emerald-400 mr-1.5"></i>Số CCCD/CMND: <strong class="text-white">{{ $contract->tenant_id_card }}</strong></p>
                        @endif
                        @if($contract && !empty($contract->start_date))
                            <p><i class="fa-solid fa-calendar-check text-emerald-400 mr-1.5"></i>Ngày bắt đầu tính tiền: <strong class="text-white">{{ date('d/m/Y', strtotime($contract->start_date)) }}</strong></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BẢNG KÊ CHI TIẾT CÁC KHOẢN TIỀN -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b-2 border-slate-200 text-slate-500 font-extrabold text-[11px] uppercase tracking-wider">
                                <th class="pb-3 w-12 text-center">STT</th>
                                <th class="pb-3">Khoản Mục Thu</th>
                                <th class="pb-3 text-center">Chỉ Số / Diễn Giải</th>
                                <th class="pb-3 text-right">Đơn Giá / Định Mức</th>
                                <th class="pb-3 text-right">Thành Tiền (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            
                            <!-- 1. TIỀN CỌC PHÒNG -->
                            @if((float)($invoice->deposit ?? 0) > 0)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 text-center font-bold text-slate-400">01</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                                        <span>Tiền Cọc Giữ Phòng (Bảo lãnh hợp đồng)</span>
                                    </div>
                                    <span class="text-xs text-slate-500 block mt-0.5">Thu 100% trọn gói ngay lúc ký hợp đồng (Từ tháng sau không cần đóng cọc)</span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs">Trọn Gói</span>
                                </td>
                                <td class="py-3.5 text-right font-medium text-slate-600">
                                    {{ number_format($invoice->deposit) }} đ
                                </td>
                                <td class="py-3.5 text-right font-extrabold text-slate-900 text-sm">
                                    {{ number_format($invoice->deposit) }} đ
                                </td>
                            </tr>
                            @endif

                            <!-- 2. TIỀN PHÒNG KỲ ĐẦU -->
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 text-center font-bold text-slate-400">02</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-house-chimney text-blue-600"></i>
                                        <span>Tiền Thuê Phòng (Trả trước để ở)</span>
                                    </div>
                                    @php
                                        $rentFull = (float)($contract->rent_price ?? ($post->price ?? $invoice->rent_price));
                                        $isRentProrated = ($rentFull > 0 && abs($rentFull - (float)$invoice->rent_price) > 1000);
                                    @endphp
                                    @if($isRentProrated)
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[11px]">
                                                <i class="fa-solid fa-scissors mr-1"></i> Tính Lẻ Ngày (Pro-rated)
                                            </span>
                                            <span class="text-xs text-slate-500">
                                                Giá gốc: {{ number_format($rentFull) }} đ/tháng (Công thức: [{{ number_format($rentFull) }} / 30] × số ngày ở)
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500 block mt-0.5">Thu trọn vẹn 1 tháng tiền phòng của kỳ đầu</span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="font-semibold text-slate-700">{{ $invoice->billing_period ?: 'Kỳ đầu tiên' }}</span>
                                </td>
                                <td class="py-3.5 text-right font-medium text-slate-600">
                                    {{ number_format($rentFull) }} đ/tháng
                                </td>
                                <td class="py-3.5 text-right font-extrabold text-slate-900 text-sm">
                                    {{ number_format($invoice->rent_price) }} đ
                                </td>
                            </tr>

                            <!-- 3. PHÍ DỊCH VỤ CỐ ĐỊNH -->
                            @if((float)($invoice->service_amount ?? 0) > 0)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 text-center font-bold text-slate-400">03</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-wifi text-purple-600"></i>
                                        <span>Phí Dịch Vụ Cố Định (Internet, Rác, Gửi Xe)</span>
                                    </div>
                                    <span class="text-xs text-slate-500 block mt-0.5">
                                        Internet: {{ number_format($contract->internet_price ?? 0) }}đ | 
                                        Dịch vụ/vệ sinh: {{ number_format($contract->garbage_price ?? ($contract->service_fee ?? 0)) }}đ
                                        @if(!empty($contract->parking_fee))
                                            | Xe: {{ number_format($contract->parking_fee) }}đ
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="text-xs text-slate-600 font-medium">Hàng tháng</span>
                                </td>
                                <td class="py-3.5 text-right font-medium text-slate-600">
                                    Theo thỏa thuận
                                </td>
                                <td class="py-3.5 text-right font-extrabold text-slate-900 text-sm">
                                    {{ number_format($invoice->service_amount) }} đ
                                </td>
                            </tr>
                            @endif

                            <!-- 4. TIỀN ĐIỆN KỲ ĐẦU (0 VNĐ) -->
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 text-center font-bold text-slate-400">04</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-bolt text-amber-500"></i>
                                        <span>Tiền Điện (Tháng đầu ghi nhận chỉ số bàn giao)</span>
                                    </div>
                                    <span class="text-xs text-slate-500 block mt-0.5">
                                        Chỉ số công tơ bàn giao ban đầu: <strong>{{ (int)($invoice->electricity_old ?? ($contract->electricity_start ?? 0)) }}</strong> kWh (Đơn giá: {{ number_format($contract->electricity_price ?? 3500) }} đ/kWh)
                                    </span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-bold">Số đầu: {{ (int)($invoice->electricity_old ?? ($contract->electricity_start ?? 0)) }}</span>
                                </td>
                                <td class="py-3.5 text-right font-medium text-slate-600">
                                    {{ number_format($contract->electricity_price ?? 3500) }} đ/số
                                </td>
                                <td class="py-3.5 text-right font-extrabold text-emerald-600 text-sm">
                                    0 VNĐ
                                </td>
                            </tr>

                            <!-- 5. TIỀN NƯỚC KỲ ĐẦU (0 VNĐ) -->
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 text-center font-bold text-slate-400">05</td>
                                <td class="py-3.5">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-droplet text-sky-500"></i>
                                        <span>Tiền Nước (Tháng đầu ghi nhận chỉ số bàn giao)</span>
                                    </div>
                                    <span class="text-xs text-slate-500 block mt-0.5">
                                        Chỉ số đồng hồ bàn giao ban đầu: <strong>{{ (int)($invoice->water_old ?? ($contract->water_start ?? 0)) }}</strong> m³
                                    </span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-bold">Số đầu: {{ (int)($invoice->water_old ?? ($contract->water_start ?? 0)) }}</span>
                                </td>
                                <td class="py-3.5 text-right font-medium text-slate-600">
                                    {{ number_format($contract->water_price_block ?? 15000) }} đ/khối
                                </td>
                                <td class="py-3.5 text-right font-extrabold text-emerald-600 text-sm">
                                    0 VNĐ
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- TỔNG CỘNG THANH TOÁN -->
                <div class="border-t-2 border-slate-200 pt-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Ghi chú hóa đơn:</span>
                        <p class="text-xs text-slate-600 italic max-w-lg">
                            {{ $invoice->note ?: 'Khách thanh toán xong Hóa đơn #1 sẽ chính thức nhận chìa khóa phòng và kích hoạt hợp đồng.' }}
                        </p>
                    </div>

                    <div class="w-full sm:w-auto p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left sm:text-right space-y-1">
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">TỔNG TIỀN THANH TOÁN KỲ ĐẦU:</span>
                        <p class="text-2xl sm:text-3xl font-black text-rose-600">
                            {{ number_format($invoice->total_amount) }} <span class="text-sm font-extrabold text-slate-600">VNĐ</span>
                        </p>
                        <p class="text-[11px] font-semibold text-slate-500">
                            (Đã bao gồm tiền cọc + tiền phòng kỳ đầu + dịch vụ cố định)
                        </p>
                    </div>
                </div>

                <!-- PHẦN CHUYỂN KHOẢN VIETQR (NẰM TRONG HÓA ĐƠN ĐỂ TIỆN IN HOẶC QUÉT MÃ) -->
                <div class="pt-6 border-t border-slate-200">
                    <div class="bg-gradient-to-br from-slate-900 to-[#0b132a] text-white p-6 rounded-3xl border border-slate-800 shadow-inner flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-3 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 font-extrabold text-xs uppercase tracking-wider border border-emerald-400/30">
                                    <i class="fa-solid fa-qrcode mr-1"></i> VietQR Chuyển Khoản Tự Động
                                </span>
                            </div>
                            <h4 class="text-base sm:text-lg font-extrabold text-white">Quét Mã QR Hoặc Chuyển Khoản Nhanh 24/7</h4>
                            <p class="text-xs text-slate-300">
                                Hệ thống tự động ghi nhận thanh toán ngay sau khi nhận được tiền từ ngân hàng.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs">
                                <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Ngân Hàng Thụ Hưởng:</span>
                                    <strong class="text-white text-sm">{{ $bankName }}</strong>
                                </div>
                                <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Số Tài Khoản:</span>
                                    <div class="flex items-center justify-between">
                                        <strong class="text-emerald-400 text-sm font-mono tracking-wider">{{ $bankAccount }}</strong>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $bankAccount }}'); alert('Đã sao chép số tài khoản!')" class="text-slate-300 hover:text-white" title="Sao chép STK">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Chủ Tài Khoản:</span>
                                    <strong class="text-white text-sm uppercase">{{ $bankHolder }}</strong>
                                </div>
                                <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Nội Dung Chuyển Khoản:</span>
                                    <div class="flex items-center justify-between">
                                        <strong class="text-amber-300 text-sm font-mono">{{ $memo }}</strong>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ $memo }}'); alert('Đã sao chép nội dung chuyển khoản!')" class="text-slate-300 hover:text-white" title="Sao chép cú pháp">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ẢNH MÃ VIETQR -->
                        <div class="shrink-0 flex flex-col items-center bg-white p-3.5 rounded-2xl shadow-xl">
                            <img src="{{ $qrUrl }}" alt="VietQR Thanh Toán" class="w-48 h-48 sm:w-52 sm:h-52 object-contain rounded-xl">
                            <span class="text-[11px] font-extrabold text-slate-800 mt-2 flex items-center gap-1">
                                <i class="fa-solid fa-camera text-emerald-600"></i> Quét bằng mọi app Ngân Hàng
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CHỮ KÝ 2 BÊN -->
                <div class="pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
                    <div>
                        <p class="font-extrabold text-slate-900 uppercase">Người Lập Phiếu</p>
                        <p class="text-[11px] text-slate-400 italic">(Ký và ghi rõ họ tên)</p>
                        <div class="h-20"></div>
                        <p class="font-bold text-slate-800">{{ Auth::user()->account_name ?? (Auth::user()->name ?? 'Ban Quản Lý') }}</p>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 uppercase">Khách Thuê Phòng</p>
                        <p class="text-[11px] text-slate-400 italic">(Ký và ghi rõ họ tên)</p>
                        <div class="h-20"></div>
                        <p class="font-bold text-slate-800">{{ $contract ? ($contract->tenant_name ?? 'Khách thuê') : 'Khách thuê' }}</p>
                    </div>
                </div>
            </div>

            <!-- FOOTER HÀNH ĐỘNG CỦA HÓA ĐƠN -->
            <div class="bg-slate-50 p-6 sm:p-8 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4 no-print">
                <a href="{{ route('qlvan_hanh.room_list') }}" class="py-3 px-5 rounded-2xl border border-slate-300 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Trở Về Danh Sách Phòng</span>
                </a>

                <div class="flex flex-wrap items-center gap-3">
                    @if($isPendingApproval)
                        <button type="button" disabled class="py-3.5 px-6 rounded-2xl bg-slate-200 text-slate-500 font-extrabold text-xs sm:text-sm flex items-center gap-2 border border-slate-300 cursor-not-allowed shadow-inner">
                            <i class="fa-solid fa-lock text-amber-600"></i>
                            <span>Khách Đã Chuyển Khoản – Chờ Admin Kế Toán Duyệt</span>
                        </button>
                    @elseif($isPaid)
                        @if($isBank)
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-100 text-emerald-800 font-extrabold text-xs sm:text-sm border border-emerald-300">
                                <i class="fa-solid fa-building-columns text-emerald-600 text-base"></i>
                                <span>Đã Thanh Toán (Chuyển Khoản)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-teal-100 text-teal-800 font-extrabold text-xs sm:text-sm border border-teal-300">
                                <i class="fa-solid fa-hand-holding-dollar text-teal-600 text-base"></i>
                                <span>Đã Thu Tiền Mặt (Nhân Sự Đang Giữ)</span>
                            </span>
                        @endif
                    @else
                        <button type="button" onclick="confirmCashCollection()" id="btnActionPayBottom" class="py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center gap-2 shadow-lg hover:shadow-xl transition-all">
                            <i class="fa-solid fa-money-bill-wave text-base"></i>
                            <span>XÁC NHẬN THU TIỀN MẶT</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER CHUNG -->
    <footer class="bg-white border-t border-slate-200 py-4 px-4 text-center text-xs text-slate-400 no-print">
        <p>© 2026 RentHome System. Bản quyền thuộc về Ban Quản Trị Hệ Thống Cho Thuê Bất Động Sản.</p>
    </footer>

    <!-- JAVASCRIPT XỬ LÝ THANH TOÁN TIỀN MẶT & BẢO VỆ -->
    <script>
        // Sao chép link hóa đơn
        function copyInvoiceLink() {
            navigator.clipboard.writeText(window.location.href);
            const btnText = document.getElementById('copyLinkBtnText');
            if (btnText) {
                const orig = btnText.innerText;
                btnText.innerText = "Đã Sao Chép!";
                setTimeout(() => { btnText.innerText = orig; }, 2000);
            }
        }

        // Cơ chế bảo vệ SweetAlert2: Xác nhận thu tiền mặt tránh bấm nhầm
        function confirmCashCollection() {
            const invoiceCode = "{{ $invoice->invoice_code }}";
            const tenantName = "{{ $contract ? ($contract->tenant_name ?? 'khách thuê') : ($invoice->tenant_name ?? 'khách thuê') }}";
            const totalAmountStr = "{{ number_format($invoice->total_amount) }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const confirmHtml = `
                <div class="text-left space-y-3 text-xs sm:text-sm">
                    <p class="text-slate-700">
                        Bạn xác nhận <strong>đã thu [${totalAmountStr}] VNĐ tiền mặt</strong> từ khách thuê <strong>${tenantName}</strong>?
                    </p>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i>
                        Khoản tiền mặt này sẽ được ghi nhận vào mục <strong>"Tiền Mặt Đang Giữ (Chờ nộp Kế toán)"</strong> trên Bảng điều khiển của bạn cho đến khi Admin Kế toán xác nhận nộp quỹ.
                    </div>
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Xác Nhận Thu Tiền Mặt?',
                    html: confirmHtml,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Tôi Đã Thu Tiền Mặt',
                    cancelButtonText: 'Hủy Bỏ',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl border border-slate-100',
                        confirmButton: 'rounded-xl font-bold py-2.5 px-4',
                        cancelButton: 'rounded-xl font-bold py-2.5 px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        executePayCash(invoiceCode, csrfToken);
                    }
                });
            } else {
                if (confirm(`Bạn xác nhận đã thu [${totalAmountStr}] VNĐ tiền mặt từ khách thuê ${tenantName}?`)) {
                    executePayCash(invoiceCode, csrfToken);
                }
            }
        }

        function executePayCash(invoiceCode, csrfToken) {
            const btnTop = document.getElementById('btnActionPayTop');
            const btnBottom = document.getElementById('btnActionPayBottom');
            if (btnTop) { btnTop.disabled = true; btnTop.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang ghi nhận...'; }
            if (btnBottom) { btnBottom.disabled = true; btnBottom.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang ghi nhận...'; }

            fetch(`/api/invoices/${encodeURIComponent(invoiceCode)}/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ payment_method: 'cash' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Thành Công!',
                            text: data.message || 'Đã xác nhận thu tiền mặt thành công!',
                            icon: 'success',
                            confirmButtonColor: '#059669',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert('🎉 ' + data.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Không Thể Thực Hiện', data.message || 'Không thể cập nhật thanh toán', 'warning');
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể cập nhật thanh toán'));
                    }
                    if (btnTop) { btnTop.disabled = false; btnTop.innerHTML = '<i class="fa-solid fa-money-bill-wave text-base"></i><span>Xác Nhận Thu Tiền Mặt</span>'; }
                    if (btnBottom) { btnBottom.disabled = false; btnBottom.innerHTML = '<i class="fa-solid fa-money-bill-wave text-base"></i><span>XÁC NHẬN THU TIỀN MẶT</span>'; }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Lỗi', 'Có lỗi xảy ra khi kết nối máy chủ!', 'error');
                } else {
                    alert('Có lỗi xảy ra khi kết nối máy chủ!');
                }
                if (btnTop) { btnTop.disabled = false; }
                if (btnBottom) { btnBottom.disabled = false; }
            });
        }
    </script>
</body>

</html>
