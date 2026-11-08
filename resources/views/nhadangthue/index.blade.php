<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nhà Đang Thuê & Thanh Toán Hóa Đơn - RentHome</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        /* Modal Biên bản quyết toán thanh lý - Ép chiều rộng to rõ ràng */
        div.swal2-container div.swal2-popup.settlement-receipt-modal {
            width: 950px !important;
            max-width: 95vw !important;
            min-width: min(850px, 95vw) !important;
            padding: 24px 28px !important;
            border-radius: 24px !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        }
        div.swal2-container div.swal2-popup.settlement-receipt-modal .swal2-title {
            padding: 0 0 10px 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        div.swal2-container div.swal2-popup.settlement-receipt-modal .swal2-html-container {
            max-width: 100% !important;
            width: 100% !important;
            margin: 10px 0 0 0 !important;
            padding: 0 !important;
            text-align: left !important;
            overflow-x: hidden !important;
        }
        @media (min-width: 768px) {
            .settlement-grid-2cols {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 16px !important;
            }
        }
        @media (max-width: 767px) {
            .settlement-grid-2cols {
                display: flex !important;
                flex-direction: column !important;
                gap: 14px !important;
            }
        }

        #tenantLegalContractSheet, #tenantLegalContractSheet * {
            font-family: 'Times New Roman', Times, 'Liberation Serif', serif !important;
        }
        #tenantLegalContractSheet {
            background-color: #ffffff !important;
        }
        @media print {
            body * {
                visibility: hidden !important;
            }
            #tenantContractModal, #tenantContractModal * {
                visibility: visible !important;
            }
            #tenantContractModal {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            #tenantLegalContractSheet {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col md:flex-row">

@php
    $currentUser = Auth::user();
    $reqType = request()->query('role', request()->query('type', ''));
    $accountType = (!empty($reqType) && in_array($reqType, ['canhan', 'doanhnghiep'])) ? $reqType : ($currentUser->role ?? 'canhan');
    $isEnterprise = ($accountType === 'doanhnghiep' || ($currentUser && $currentUser->isEnterprise()));
    $displayName = $currentUser 
        ? ($isEnterprise ? ($currentUser->company_name ?? $currentUser->account_name ?? 'Tập Đoàn BĐS Đạt Phát') : ($currentUser->account_name ?? $currentUser->username ?? 'Nguyễn Văn Tuấn'))
        : ($isEnterprise ? 'Tập Đoàn BĐS Đạt Phát' : 'Nguyễn Văn Tuấn');
    $badgeText = $isEnterprise ? 'Doanh Nghiệp' : 'Cá Nhân';
    $subTitle = $isEnterprise ? 'Đối tác Doanh Nghiệp' : 'Tài khoản Cá nhân';
@endphp

    @include('partials.sidebar-user', ['isDrawer' => true])

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

        <div class="px-4 pt-4">
            <!-- User Account Information Card -->
            <a href="{{ route('user.dashboard') }}" class="block w-full text-left p-3.5 rounded-2xl {{ $isEnterprise ? 'bg-gradient-to-br from-purple-50 to-indigo-50/50 border border-purple-100' : 'bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-100' }} flex items-center gap-3 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                <div
                    class="w-10 h-10 rounded-xl {{ $isEnterprise ? 'bg-purple-600 text-white' : 'bg-brand-600 text-white' }} flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                    <i class="fa-solid {{ $isEnterprise ? 'fa-briefcase' : 'fa-user' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate" title="{{ $displayName }}">
                        {{ $displayName }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span
                            class="inline-block w-2 h-2 rounded-full {{ $isEnterprise ? 'bg-purple-500' : 'bg-emerald-500' }}"></span>
                        <span
                            class="text-[11px] font-semibold {{ $isEnterprise ? 'text-purple-700' : 'text-emerald-700' }}">{{ $subTitle }}</span>
                    </div>
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
                        <span class="ml-auto px-2 py-0.5 rounded-full bg-skybrand-100 text-skybrand-700 font-extrabold text-[10px]" id="sidebar-rented-count">{{ $totalRentedCount ?? 0 }}</span>
                    </button>

                    <!-- 2. Form Thanh toán hóa đơn -->
                    <button type="button" id="sidebar-nav-hoadon" onclick="switchMainForm('thanh-toan-hoa-don')" 
                       class="sidebar-item w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs text-slate-700 hover:bg-slate-50 transition-all text-left">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <span class="text-sm font-semibold">Thanh toán hóa đơn</span>
                        <span class="ml-auto px-2 py-0.5 rounded-full {{ ($pendingInvoicesCount ?? 0) > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} font-extrabold text-[10px]" id="sidebar-pending-count">{{ $pendingInvoicesCount ?? 0 }}</span>
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

        <div class="p-3 border-t border-slate-100 bg-slate-50/60 hidden md:block opacity-0 pointer-events-none">
            <!-- Placeholder to maintain layout if needed, or completely removed -->
        </div>
    </aside>

    <!-- RIGHT MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 md:ml-64">



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
                                    <span id="total-rented-count-1">{{ $totalRentedCount ?? 0 }}</span> Căn hộ / Phòng trọ
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
                                <i class="fa-solid fa-circle-check text-[10px]"></i> <span id="total-active-count">{{ $totalRentedCount ?? 0 }}</span> Căn hoạt động
                            </span>
                        </div>

                        <div class="mt-6 flex items-baseline gap-3">
                            <span class="text-4xl font-black text-slate-900" id="total-rented-count-2">{{ $totalRentedCount ?? 0 }}</span>
                            <span class="text-sm font-bold text-slate-600">Căn hộ / Phòng trọ bạn đang đứng tên thuê</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-skybrand-100 flex items-center justify-between text-xs font-semibold">
                            <span class="text-emerald-600 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-shield-check"></i> Hợp đồng đầy đủ hiệu lực
                            </span>
                            <span class="text-skybrand-700 font-bold">Tổng tiền thuê: <span id="total-rent-amount">{{ number_format($totalRentAmount ?? 0) }}</span>đ/tháng</span>
                        </div>
                    </div>
                </div>

                <!-- DANH SÁCH NHÀ ĐANG THUÊ (HÌNH CHỮ NHẬT NẰM NGANG) -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-house-flag text-skybrand-600"></i>
                            Chi Tiết <span id="detail-active-count">{{ $totalRentedCount ?? 0 }}</span> Căn Nhà Đang Thuê
                        </h2>
                        <span class="px-3.5 py-1.5 rounded-full bg-skybrand-100 text-skybrand-700 text-xs font-extrabold border border-skybrand-200">
                            Tổng số: <span id="detail-total-count">{{ $totalRentedCount ?? 0 }}</span> nhà đang thuê
                        </span>
                    </div>

                    <!-- DẤU HIỆU 1: BANNER TỔNG QUAN TRẠNG THÁI THANH TOÁN -->
                    @if(($pendingInvoicesCount ?? 0) > 0)
                        <div class="p-4 rounded-2xl bg-rose-50/90 border-2 border-rose-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs shadow-xs animate-in fade-in">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-sm animate-pulse">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm">Cảnh báo thanh toán hóa đơn:</p>
                                    <p class="text-slate-600">Bạn đang có <strong class="text-rose-600 font-black">{{ $pendingInvoicesCount }} căn nhà chưa đóng tiền</strong> với tổng số tiền cần thanh toán là <strong class="text-rose-700 font-black text-sm">{{ number_format($pendingInvoicesTotal ?? 0) }} VNĐ</strong>.</p>
                                </div>
                            </div>
                            <button type="button" onclick="switchMainForm('thanh-toan-hoa-don')" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shrink-0 transition-all shadow-sm flex items-center gap-1.5 hover:scale-105 active:scale-95">
                                <i class="fa-solid fa-credit-card"></i> Thanh toán hóa đơn <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    @else
                        <div class="p-3.5 rounded-2xl bg-emerald-50/90 border border-emerald-200 flex items-center justify-between gap-3 text-xs shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-sm"></span>
                                <p class="text-emerald-900 font-bold">Tuyệt vời! Tất cả các căn nhà bạn đang thuê đều đã hoàn tất thanh toán tiền phòng & dịch vụ kỳ này.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-extrabold">Đầy đủ hiệu lực</span>
                        </div>
                    @endif

                    <div id="rented-houses-container" class="flex flex-col gap-6">
                        @php 
                            $contractJsonMap = []; 
                            $invoiceDataJsonMap = [];
                        @endphp
                        @forelse($rentedContracts ?? [] as $contract)
                            @php
                                $post = $contract->post ?: \App\Models\Post::find($contract->post_id);

                                // Tìm người đăng bài (nhân sự quản lý) của căn nhà / phòng này
                                $posterUser = null;
                                if ($post) {
                                    $posterId = $post->staff_id ?: ($post->author_id ?: $post->user_id);
                                    if ($posterId) {
                                        $posterUser = \App\Models\User::find($posterId);
                                    }
                                }
                                if (!$posterUser && !empty($contract->staff_id)) {
                                    $posterUser = \App\Models\User::find($contract->staff_id);
                                }
                                if (!$posterUser && !empty($contract->created_by)) {
                                    $posterUser = \App\Models\User::find($contract->created_by);
                                }

                                $owner = $contract->owner ?: ($post ? \App\Models\User::find($post->user_id) : null);
                                $landlord = $posterUser ?: $owner;
                                $landlordName = $landlord ? ($landlord->account_name ?? ($landlord->name ?? ($landlord->username ?? 'Chủ nhà'))) : 'Chủ nhà';
                                $landlordPhone = $landlord ? ($landlord->phone ?? ($contract->owner_phone ?? 'Chưa cập nhật')) : ($contract->owner_phone ?? 'Chưa cập nhật');
                                $landlordIdCard = $landlord ? ($landlord->id_card ?? ($landlord->cccd ?? ($owner->id_card ?? '001089012345'))) : ($owner->id_card ?? '001089012345');
                                $landlordAddress = $landlord ? ($landlord->address ?? ($owner->address ?? ($address ?: 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội'))) : ($owner->address ?? ($address ?: 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội'));
                                $houseTitle = $post ? ($post->title ?? 'Nhà / Phòng cho thuê') : ($contract->tenant_name ? 'Phòng ' . $contract->tenant_name : 'Phòng trọ');
                                $roomCode = $contract->contract_code ?? ('HD-' . substr((string)($contract->_id ?? $contract->id), -4));
                                $price = (float)($contract->rent_price ?? ($post->price ?? 0));
                                $deposit = (float)($contract->deposit ?? 0);
                                $startDate = $contract->start_date ? date('d/m/Y', strtotime($contract->start_date)) : '01/01/2026';
                                $endDate = $contract->end_date ?: ($contract->start_date ? date('d/m/Y', strtotime($contract->start_date . ' + ' . ($contract->duration_months ?? 12) . ' months')) : '01/01/2027');
                                $address = $post ? ($post->address . ($post->district ? ', ' . $post->district : '') . ($post->province ? ', ' . $post->province : '')) : 'Địa chỉ phòng trọ';
                                $imgUrl = ($post && !empty($post->display_image)) ? $post->display_image : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';

                                $contractId = (string)($contract->_id ?? $contract->id);
                                $tenantName = $contract->tenant_name ?? ($currentUser->account_name ?? ($currentUser->username ?? 'Khách thuê'));
                                $tenantPhone = $contract->tenant_phone ?? ($currentUser->phone ?? 'Chưa cập nhật');
                                $tenantIdCard = $contract->tenant_id_card ?? ($currentUser->id_card ?? '001089012345');
                                $tenantAddress = $contract->tenant_address ?? ($currentUser->address ?? 'Theo CCCD / Hồ sơ đăng ký thuê');

                                $elecPrice = (float)($contract->electricity_price ?? 3500);
                                $isWaterByPerson = ($contract->water_calculation_type ?? 'khoi') === 'nguoi';
                                $waterPeopleCount = max(1, (int)($contract->water_people_count ?? 1));
                                $waterUnitRate = $isWaterByPerson 
                                    ? (float)($contract->water_price_person ?: ($contract->water_price ?: 25000))
                                    : (float)($contract->water_price_block ?: ($contract->water_price ?: 25000));
                                $waterPrice = $isWaterByPerson ? ($waterUnitRate * $waterPeopleCount) : $waterUnitRate;
                                $servicePrice = (float)($contract->internet_price ?? 0) + (float)($contract->garbage_price ?? 0) + (float)($contract->parking_fee ?? ($contract->parking_price ?? 0));
                                // Tháng đầu tiên: Tiền điện và tiền nước bằng 0 VNĐ (chỉ ghi nhận chỉ số đầu)
                                $totalFirstMonthCalc = $deposit + $price + 0 + 0 + $servicePrice;
                                $realInvoice = \App\Models\Invoice::where('contract_id', $contractId)->first();
                                if ($realInvoice && !empty($realInvoice->total_amount)) {
                                    $totalFirstMonthCalc = (float)$realInvoice->total_amount;
                                }
                                $firstMonthInvoiceCode = ($realInvoice && !empty($realInvoice->invoice_code)) 
                                    ? $realInvoice->invoice_code 
                                    : ('INV-' . $roomCode);

                                $contractModalData = [
                                    'id' => $contractId,
                                    'contract_code' => $roomCode,
                                    'house_title' => $houseTitle,
                                    'address' => $address,
                                    'landlord_name' => $landlordName,
                                    'landlord_phone' => $landlordPhone,
                                    'landlord_id_card' => $landlordIdCard,
                                    'landlord_address' => $landlordAddress,
                                    'tenant_name' => $tenantName,
                                    'tenant_phone' => $tenantPhone,
                                    'tenant_id_card' => $tenantIdCard,
                                    'tenant_address' => $tenantAddress,
                                    'rent_price' => $price,
                                    'deposit' => $deposit,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                    'duration_months' => $contract->duration_months ?? 12,
                                    'electricity_price' => $elecPrice,
                                    'electricity_start' => $contract->electricity_start ?? 0,
                                    'water_calculation_type' => $contract->water_calculation_type ?? 'khoi',
                                    'water_price_block' => $contract->water_price_block ?? 25000,
                                    'water_start' => $contract->water_start ?? 0,
                                    'water_price_person' => $contract->water_price_person ?? 100000,
                                    'water_people_count' => $contract->water_people_count ?? 1,
                                    'internet_price' => $contract->internet_price ?? 0,
                                    'garbage_price' => $contract->garbage_price ?? 0,
                                    'parking_fee' => (float)($contract->parking_fee ?? ($contract->parking_price ?? 0)),
                                    'parking_price' => (float)($contract->parking_fee ?? ($contract->parking_price ?? 0)),
                                    'payment_cycle' => is_numeric($contract->payment_cycle) ? (((int)$contract->payment_cycle === 12) ? '1 năm/lần' : ((int)$contract->payment_cycle . ' tháng/lần')) : ($contract->payment_cycle ?? '1 tháng/lần'),
                                    'bill_date' => $contract->bill_date ?? ($contract->billing_date ? preg_replace('/[^\d]/', '', $contract->billing_date) : '10'),
                                    'payment_limit_days' => $contract->payment_limit_days ?? 5,
                                    'late_penalty_fee' => $contract->late_penalty_fee ?? '20.000 VNĐ / ngày',
                                    'status' => $contract->status ?? 'active',
                                    'initial_invoice_code' => $firstMonthInvoiceCode,
                                    'initial_invoice_total' => $totalFirstMonthCalc,
                                    'created_at' => $contract->created_at ? date('d/m/Y', strtotime($contract->created_at)) : date('d/m/Y'),
                                ];
                                $contractJsonMap[$contractId] = $contractModalData;

                                $invRowsHtml = '
                                    <tr class="bg-amber-50/70 font-bold">
                                        <td class="py-2.5 px-3.5 text-slate-900"><i class="fa-solid fa-shield-halved text-emerald-600 mr-1.5"></i> Tiền đặt cọc giữ nhà (Bảo đảm hợp đồng)</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold">1 lần</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($deposit) . ' VNĐ</td>
                                        <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-700">' . number_format($deposit) . ' VNĐ</td>
                                    </tr>
                                    <tr class="font-bold">
                                        <td class="py-2.5 px-3.5 text-slate-900"><i class="fa-solid fa-house-chimney text-skybrand-600 mr-1.5"></i> Tiền thuê nhà tháng đầu tiên</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($price) . ' VNĐ</td>
                                        <td class="py-2.5 px-3.5 text-right font-extrabold text-skybrand-700">' . number_format($price) . ' VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-bolt text-amber-500 mr-1.5"></i> Tiền điện sinh hoạt tháng đầu (Ghi nhận chỉ số đầu)</td>
                                        <td class="py-2.5 px-3.5 text-center font-mono">' . ($contract->electricity_start ?? 0) . ' kWh</td>
                                        <td class="py-2.5 px-3.5 text-center font-mono">' . ($contract->electricity_start ?? 0) . ' kWh</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 kWh (Khởi điểm)</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($elecPrice) . ' VNĐ/kWh</td>
                                        <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-600">0 VNĐ</td>
                                    </tr>
                                    <tr>
                                        ' . ($isWaterByPerson ? '
                                        <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt tháng đầu (Tính theo người: ' . $waterPeopleCount . ' người)</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 (Khởi điểm)</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($waterUnitRate) . ' VNĐ/người</td>
                                        ' : '
                                        <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt tháng đầu (Ghi nhận chỉ số đầu)</td>
                                        <td class="py-2.5 px-3.5 text-center font-mono">' . ($contract->water_start ?? 0) . ' m³</td>
                                        <td class="py-2.5 px-3.5 text-center font-mono">' . ($contract->water_start ?? 0) . ' m³</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 (Khởi điểm)</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($waterUnitRate) . ' VNĐ/m³</td>
                                        ') . '
                                        <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-600">0 VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-wifi text-purple-600 mr-1.5"></i> Phí dịch vụ cố định (Internet, Rác, Gửi xe)</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                        <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                        <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($servicePrice) . ' VNĐ</td>
                                        <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-700">' . number_format($servicePrice) . ' VNĐ</td>
                                    </tr>
                                ';

                                // Lấy thông tin tài khoản ngân hàng của Quản trị viên (Admin) RentHome để tạo mã VietQR
                                $adminBank = \App\Models\User::getAdminBankDetails();
                                $bName = $adminBank['bank_name'];
                                $bAccount = $adminBank['bank_account'];
                                $bHolder = $adminBank['bank_holder'];

                                // Lấy thông tin nhân sự phụ trách
                                $staffUser = null;
                                if (!empty($contract->staff_id)) {
                                    $staffUser = \App\Models\User::find($contract->staff_id);
                                } elseif (!empty($contract->created_by)) {
                                    $cr = \App\Models\User::find($contract->created_by);
                                    if ($cr && ($cr->isSubAccount() || $cr->isStaff() || $cr->role === 'nhansu')) $staffUser = $cr;
                                }
                                $personInCharge = $staffUser ?: ($landlord ?? $owner);
                                $staffName = $personInCharge ? ($personInCharge->account_name ?: ($personInCharge->name ?: ($personInCharge->username ?: 'Nhân sự vận hành'))) : ($landlordName ?? 'Chủ nhà');
                                $staffPhone = $personInCharge ? ($personInCharge->phone ?: ($landlordPhone ?? 'Chưa cập nhật')) : ($landlordPhone ?? 'Chưa cập nhật');

                                // XÁC ĐỊNH TRẠNG THÁI THANH TOÁN CỦA CĂN NHÀ
                                $unpaidInvoice = \App\Models\Invoice::where('contract_id', $contractId)
                                    ->where(function($q) {
                                        $q->where('status', 'unpaid')->orWhere('payment_status', 'unpaid');
                                    })
                                    ->latest()
                                    ->first();

                                $pendingApprovalInvoice = \App\Models\Invoice::where('contract_id', $contractId)
                                    ->where(function($q) {
                                        $q->where('status', 'pending_approval')->orWhere('payment_status', 'pending_approval');
                                    })
                                    ->latest()
                                    ->first();

                                $isHousePendingApproval = !empty($pendingApprovalInvoice);
                                $isHousePaid = false;
                                if (!$unpaidInvoice && !$isHousePendingApproval) {
                                    $isHousePaid = true;
                                } else {
                                    if ($contract->payment_status === 'paid' && ($contract->last_paid_period === date('m/Y'))) {
                                        $isHousePaid = true;
                                    } else {
                                        $isHousePaid = false;
                                    }
                                }

                                $activeInv = $pendingApprovalInvoice ?: ($unpaidInvoice ?: null);
                                $unpaidAmount = $activeInv ? (float)($activeInv->total_amount ?? $totalFirstMonthCalc) : 0;
                                $invoiceActionCode = $activeInv ? ($activeInv->invoice_code ?? $firstMonthInvoiceCode) : $firstMonthInvoiceCode;
                                if ($isHousePaid) {
                                    $latestPaidInvoice = \App\Models\Invoice::where('contract_id', $contractId)->latest()->first();
                                    if ($latestPaidInvoice && !empty($latestPaidInvoice->invoice_code)) {
                                        $invoiceActionCode = $latestPaidInvoice->invoice_code;
                                    }
                                }

                                $invoiceItem = [
                                    'code' => $invoiceActionCode,
                                    'contractId' => $contractId,
                                    'houseName' => $houseTitle,
                                    'landlordName' => $landlordName,
                                    'landlordPhone' => $landlordPhone,
                                    'staffName' => $staffName,
                                    'staffPhone' => $staffPhone,
                                    'landlordInfo' => ($staffUser ? 'Nhân sự: ' : 'Chủ nhà: ') . $staffName . ' (SĐT: ' . $staffPhone . ')',
                                    'dueDate' => $contract->billing_date ? ('Hạn ' . $contract->billing_date . ' hàng tháng') : 'Hạn Mùng 10 hàng tháng',
                                    'total' => number_format($unpaidAmount ?: $totalFirstMonthCalc) . ' VNĐ',
                                    'totalRaw' => number_format($unpaidAmount ?: $totalFirstMonthCalc),
                                    'bankShort' => $bName,
                                    'bankFull' => $bName,
                                    'bankAccount' => $bAccount,
                                    'bankOwner' => $bHolder,
                                    'rowsHtml' => $invRowsHtml,
                                    'isPaid' => $isHousePaid,
                                    'isPendingApproval' => $isHousePendingApproval,
                                    'status' => $activeInv ? $activeInv->status : ($isHousePaid ? 'paid' : 'unpaid'),
                                    'proofImage' => !empty($activeInv?->proof_image) ? (str_starts_with($activeInv->proof_image, 'http') ? $activeInv->proof_image : asset(ltrim($activeInv->proof_image, '/'))) : null,
                                    'customQr' => null, // Luôn ưu tiên sinh mã VietQR tự động của Admin
                                ];
                                $invoiceDataJsonMap[$contractId] = $invoiceItem;
                                $invoiceDataJsonMap[$firstMonthInvoiceCode] = $invoiceItem;
                                $invoiceDataJsonMap[$invoiceActionCode] = $invoiceItem;
                                $invoiceDataJsonMap[$roomCode] = $invoiceItem;
                                if ($realInvoice && !empty($realInvoice->_id)) {
                                    $invoiceDataJsonMap[(string)$realInvoice->_id] = $invoiceItem;
                                }
                            @endphp

                            <!-- DẤU HIỆU 2: VIỀN THẺ NHÀ PHẢN ÁNH TRẠNG THÁI -->
                            <div id="contract-card-{{ $contractId }}" 
                                 class="contract-card bg-white rounded-3xl border {{ $isHousePendingApproval ? 'border-2 border-amber-300 ring-4 ring-amber-50/70 shadow-md' : (!$isHousePaid ? 'border-2 border-rose-300 ring-4 ring-rose-50/70 shadow-md' : 'border border-slate-200/90') }} overflow-hidden shadow-card hover:shadow-lg transition-all flex flex-col md:flex-row relative"
                                 data-contract-id="{{ $contractId }}"
                                 data-contract-code="{{ $roomCode }}">
                                <div class="relative w-full md:w-80 lg:w-96 shrink-0 min-h-[220px] md:min-h-full bg-slate-100 overflow-hidden">
                                    <img src="{{ $imgUrl }}" alt="{{ $houseTitle }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent md:bg-gradient-to-r md:from-transparent md:to-slate-950/20"></div>
                                    
                                    <div class="absolute top-3 left-3 flex flex-wrap gap-2 z-10">
                                        <span class="px-3 py-1 rounded-full bg-emerald-500/90 text-white text-xs font-bold backdrop-blur-md shadow-sm">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Đang thuê
                                        </span>
                                        <span class="px-3 py-1 rounded-full bg-black/60 text-white text-xs font-semibold backdrop-blur-md">
                                            Mã: {{ $roomCode }}
                                        </span>
                                        <!-- DẤU HIỆU 3: HUY HIỆU BADGE TRỰC DIỆN TRÊN ẢNH PHÒNG -->
                                        @if($isHousePendingApproval)
                                            <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-black backdrop-blur-md shadow-md flex items-center gap-1.5 border border-white/20">
                                                <i class="fa-solid fa-clock-rotate-left text-[10px] animate-spin"></i> ĐANG CHỜ DUYỆT CK
                                            </span>
                                        @elseif(!$isHousePaid)
                                            <span class="px-3 py-1 rounded-full bg-rose-600 text-white text-xs font-black backdrop-blur-md shadow-md flex items-center gap-1.5 border border-white/20 animate-pulse">
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                                <i class="fa-solid fa-clock text-[10px]"></i> CHƯA THANH TOÁN
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-black backdrop-blur-md shadow-md flex items-center gap-1.5 border border-white/20">
                                                <i class="fa-solid fa-circle-check text-[10px]"></i> ĐÃ THANH TOÁN
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 p-5 md:p-6 flex flex-col justify-between space-y-4">
                                    <div>
                                        <div class="border-b border-slate-100 pb-3">
                                            <h3 class="text-lg md:text-xl font-extrabold text-slate-900 leading-snug">{{ $houseTitle }}</h3>
                                            <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5 mt-1">
                                                <i class="fa-solid fa-location-dot text-rose-500"></i> {{ $address }}
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-100 mt-3">
                                            <div>
                                                <span class="text-slate-500 block text-[11px]">Giá thuê tháng:</span>
                                                <span class="text-sm font-extrabold text-brand-600">{{ number_format($price) }}đ <span class="text-[10px] font-normal text-slate-500">/tháng</span></span>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 block text-[11px]">Thời hạn hợp đồng:</span>
                                                <span class="font-bold text-slate-800">{{ $startDate }} - {{ $endDate }}</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 block text-[11px]">Tiền cọc đã gửi:</span>
                                                <span class="font-bold text-skybrand-600">{{ number_format($deposit) }}đ</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 block text-[11px]">Ngày đóng tiền:</span>
                                                <span class="font-bold text-amber-600">{{ $contract->billing_date ? 'Hạn ' . $contract->billing_date : 'Ngày 05 hàng tháng' }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between p-3 rounded-2xl bg-skybrand-50/60 border border-skybrand-100 mt-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-skybrand-600 text-white flex items-center justify-center font-bold text-sm">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </div>
                                                <div>
                                                    <span class="text-[10px] font-bold text-skybrand-600 uppercase">Chủ nhà / Quản lý</span>
                                                    <h4 class="text-xs font-bold text-slate-900">{{ $landlordName }}</h4>
                                                    <p class="text-[11px] text-slate-500">SĐT: {{ $landlordPhone }}</p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-lg bg-white border border-skybrand-200 text-skybrand-700 text-xs font-bold">
                                                <i class="fa-solid fa-shield-check text-emerald-500"></i> Chính chủ
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-slate-100">
                                        <button type="button" onclick="openTenantContractModal('{{ $contractId }}')"
                                            class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm hover:shadow active:scale-[0.98]">
                                            <i class="fa-solid fa-file-contract text-sm"></i> Xem hợp đồng
                                        </button>
                                        <button type="button" onclick="openContactModal('{{ addslashes($landlordName) }}', '{{ $landlordPhone }}', '{{ addslashes($houseTitle) }}')"
                                            class="w-full py-2.5 px-3 rounded-xl bg-skybrand-600 hover:bg-skybrand-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm hover:shadow active:scale-[0.98]">
                                            <i class="fa-solid fa-phone text-sm"></i> Liên hệ chủ nhà
                                        </button>
                                        @if($contract->status === 'terminated')
                                            <button type="button" disabled
                                                class="w-full py-2.5 px-3 rounded-xl bg-slate-100 text-slate-600 border border-slate-300 font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm opacity-90 cursor-not-allowed">
                                                <i class="fa-solid fa-circle-check text-emerald-500"></i> Đã bàn giao phòng (Còn trống)
                                            </button>
                                        @elseif(!empty($contract->liquidation_details) || in_array($contract->status, ['pending_liquidation', 'pending_refund']))
                                            @if($contract->status === 'pending_liquidation')
                                                <button type="button" onclick="switchMainForm('thanh-toan-hoa-don')"
                                                    class="w-full py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition-all animate-pulse"
                                                    title="Ban Quản lý đã lập bảng thanh lý chốt công nợ. Vui lòng thanh toán hóa đơn để hoàn tất.">
                                                    <i class="fa-solid fa-credit-card"></i> Đã lập bảng thanh lý (Thanh toán nợ)
                                                </button>
                                            @else
                                                <button type="button" disabled
                                                    class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-300 font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm opacity-95 cursor-not-allowed"
                                                    title="Ban Quản lý đã lập bảng thanh lý và gửi yêu cầu hoàn tiền cọc lên Kế toán">
                                                    <i class="fa-solid fa-file-circle-check text-emerald-600"></i> Đã lập bảng thanh lý (Chờ nhận tiền)
                                                </button>
                                            @endif
                                        @elseif(!empty($contract->checkout_request) && ($contract->checkout_request['status'] ?? '') === 'pending')
                                            <div class="flex items-center gap-1.5 w-full">
                                                <div class="flex-1 py-2.5 px-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-300 font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs truncate select-none" title="Đã gửi yêu cầu trả phòng (Đang chờ xử lý)">
                                                    <i class="fa-solid fa-clock-rotate-left text-xs text-amber-600 animate-pulse"></i> Đã gửi yêu cầu
                                                </div>
                                                <button type="button" onclick="cancelCheckoutRequest('{{ $contractId }}', '{{ $roomCode }}')"
                                                    class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-700 border border-rose-300 font-extrabold text-xs flex items-center justify-center gap-1 transition-all shadow-xs shrink-0 cursor-pointer"
                                                    title="Hủy / Thu hồi yêu cầu trả phòng này">
                                                    <i class="fa-solid fa-rotate-left"></i> Thu hồi
                                                </button>
                                            </div>
                                        @else
                                            <button type="button" onclick="openReturnModal('{{ $contractId }}', '{{ addslashes($houseTitle) }}', '{{ $roomCode }}', '{{ addslashes($landlordName) }}', '{{ number_format($deposit) }}đ')"
                                                class="w-full py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs flex items-center justify-center gap-1.5 transition-all hover:bg-rose-100/80 active:scale-[0.98]">
                                                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i> Gửi yêu cầu trả nhà
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px dashed #cbd5e1;">
                                <i class="fa-solid fa-house-crack" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                                <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Bạn chưa thuê nhà/phòng nào</p>
                            </div>
                        @endforelse
                    </div>

                    @if(isset($terminatedContracts) && $terminatedContracts->isNotEmpty())
                        <!-- DANH SÁCH CÁC CĂN ĐÃ THANH LÝ & BÀN GIAO PHÒNG TRỐNG -->
                        <div id="terminated-contracts-section" class="mt-8 pt-8 border-t border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-800">Lịch Sử Thuê & Hợp Đồng Đã Thanh Lý <span id="terminated-contracts-count">({{ $terminatedContracts->count() }})</span></h3>
                                        <p class="text-xs text-slate-500">Các căn nhà đã hoàn tất trả phòng, đối soát công nợ và chuyển trạng thái phòng trống</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 self-start sm:self-auto">
                                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Phòng đã bàn giao (Còn trống)
                                    </span>
                                    @if($terminatedContracts->count() > 1)
                                        <button type="button" onclick="clearAllContractHistory()" 
                                            class="px-3 py-1 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold border border-rose-200 flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer shadow-xs"
                                            title="Xóa toàn bộ lịch sử thuê đã thanh lý">
                                            <i class="fa-regular fa-trash-can"></i> Xóa tất cả
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-5">
                                @foreach($terminatedContracts as $termContract)
                                    @php
                                        $tPost = $termContract->post ?: \App\Models\Post::find($termContract->post_id);
                                        $tPosterUser = null;
                                        if ($tPost) {
                                            $tPosterId = $tPost->staff_id ?: ($tPost->author_id ?: $tPost->user_id);
                                            if ($tPosterId) {
                                                $tPosterUser = \App\Models\User::find($tPosterId);
                                            }
                                        }
                                        if (!$tPosterUser && !empty($termContract->staff_id)) {
                                            $tPosterUser = \App\Models\User::find($termContract->staff_id);
                                        }
                                        if (!$tPosterUser && !empty($termContract->created_by)) {
                                            $tPosterUser = \App\Models\User::find($termContract->created_by);
                                        }
                                        $tOwner = $termContract->owner ?: ($tPost ? \App\Models\User::find($tPost->user_id) : null);
                                        $tLandlord = $tPosterUser ?: $tOwner;
                                        $tLandlordName = $tLandlord ? ($tLandlord->account_name ?? ($tLandlord->name ?? ($tLandlord->username ?? 'Chủ nhà'))) : 'Chủ nhà';
                                        $tLandlordPhone = $tLandlord ? ($tLandlord->phone ?? ($termContract->owner_phone ?? 'Chưa cập nhật')) : ($termContract->owner_phone ?? 'Chưa cập nhật');
                                        $tLandlordIdCard = $tLandlord ? ($tLandlord->id_card ?? ($tLandlord->cccd ?? ($tOwner->id_card ?? '001089012345'))) : ($tOwner->id_card ?? '001089012345');
                                        $tLandlordAddress = $tLandlord ? ($tLandlord->address ?? ($tOwner->address ?? ($tAddress ?: 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội'))) : ($tOwner->address ?? ($tAddress ?: 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội'));
                                        $tHouseTitle = $tPost ? ($tPost->title ?? 'Nhà / Phòng cho thuê') : ($termContract->tenant_name ? 'Phòng ' . $termContract->tenant_name : 'Phòng trọ');
                                        $tRoomCode = $termContract->contract_code ?? ('HD-' . substr((string)($termContract->_id ?? $termContract->id), -4));
                                        $tPrice = (float)($termContract->rent_price ?? ($tPost->price ?? 0));
                                        $tDeposit = (float)($termContract->deposit ?? 0);
                                        $tStartDate = $termContract->start_date ? date('d/m/Y', strtotime($termContract->start_date)) : '01/01/2026';
                                        $tEndDate = $termContract->end_date ?: ($termContract->start_date ? date('d/m/Y', strtotime($termContract->start_date . ' + ' . ($termContract->duration_months ?? 12) . ' months')) : '01/01/2027');
                                        $tAddress = $tPost ? ($tPost->address . ($tPost->district ? ', ' . $tPost->district : '') . ($tPost->province ? ', ' . $tPost->province : '')) : 'Địa chỉ phòng trọ';
                                        $tImgUrl = ($tPost && !empty($tPost->display_image)) ? $tPost->display_image : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';
                                        $tContractId = (string)($termContract->_id ?? $termContract->id);
                                        $tLiquidation = (array)($termContract->liquidation_details ?? []);
                                        if (!empty($tLiquidation)) {
                                            if (!empty($termContract->checkout_request)) {
                                                $cReq = (array)$termContract->checkout_request;
                                                if (empty($tLiquidation['bank_account'])) $tLiquidation['bank_account'] = $cReq['bank_account'] ?? '';
                                                if (empty($tLiquidation['bank_name'])) $tLiquidation['bank_name'] = $cReq['bank_name'] ?? '';
                                                if (empty($tLiquidation['bank_holder'])) $tLiquidation['bank_holder'] = $cReq['bank_holder'] ?? ($termContract->tenant_name ?? '');
                                            }
                                            if (empty($tLiquidation['rent_price'])) {
                                                $tLiquidation['rent_price'] = $tPrice;
                                            }
                                        }
                                        $contractJsonMap[$tContractId] = [
                                            'id' => $tContractId,
                                            'contract_code' => $tRoomCode,
                                            'house_title' => $tHouseTitle,
                                            'address' => $tAddress,
                                            'landlord_name' => $tLandlordName,
                                            'landlord_phone' => $tLandlordPhone,
                                            'landlord_id_card' => $tLandlordIdCard,
                                            'landlord_address' => $tLandlordAddress,
                                            'tenant_name' => $termContract->tenant_name ?? 'Khách thuê',
                                            'tenant_phone' => $termContract->tenant_phone ?? '',
                                            'rent_price' => $tPrice,
                                            'deposit' => $tDeposit,
                                            'start_date' => $tStartDate,
                                            'end_date' => $tEndDate,
                                            'duration_months' => $termContract->duration_months ?? 12,
                                            'status' => 'terminated',
                                        ];
                                    @endphp
                                    <div id="terminated-contract-card-{{ $tContractId }}" class="bg-slate-50/90 rounded-3xl border border-slate-200 overflow-hidden shadow-sm flex flex-col md:flex-row relative hover:shadow transition-all">
                                        <div class="relative w-full md:w-72 lg:w-80 shrink-0 min-h-[190px] bg-slate-200 overflow-hidden">
                                            <img src="{{ $tImgUrl }}" alt="{{ $tHouseTitle }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-slate-900/30"></div>
                                            <div class="absolute top-3 left-3 flex flex-wrap gap-2 z-10">
                                                <span class="px-3 py-1 rounded-full bg-slate-800/90 text-white text-xs font-bold backdrop-blur-md shadow-sm">
                                                    <i class="fa-solid fa-flag-checkered text-[10px] text-amber-400"></i> ĐÃ THANH LÝ
                                                </span>
                                                <span class="px-2.5 py-1 rounded-full bg-white/90 text-slate-800 text-[11px] font-bold shadow-sm">
                                                    Mã: {{ $tRoomCode }}
                                                </span>
                                            </div>
                                            <div class="absolute bottom-3 left-3 right-3 z-10">
                                                <span class="w-full px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-extrabold flex items-center justify-center gap-1 shadow-md">
                                                    <i class="fa-solid fa-door-open"></i> Phòng đã bàn giao (Còn trống)
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex-1 p-5 md:p-6 flex flex-col justify-between space-y-3">
                                            <div>
                                                <div class="border-b border-slate-200/80 pb-2.5 flex items-start justify-between gap-3">
                                                    <div>
                                                        <h3 class="text-base md:text-lg font-bold text-slate-800 leading-snug">{{ $tHouseTitle }}</h3>
                                                        <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                                            <i class="fa-solid fa-location-dot text-rose-500"></i> {{ $tAddress }}
                                                        </p>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shrink-0">
                                                        <i class="fa-solid fa-check-double"></i> Đã hoàn tất công nợ
                                                    </span>
                                                </div>

                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs bg-white p-3 rounded-xl border border-slate-200/70 mt-2.5">
                                                    <div>
                                                        <span class="text-slate-400 block text-[11px]">Giá thuê từng áp dụng:</span>
                                                        <span class="font-bold text-slate-700">{{ number_format($tPrice) }}đ/tháng</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-slate-400 block text-[11px]">Thời gian thuê:</span>
                                                        <span class="font-semibold text-slate-600">{{ $tStartDate }} - {{ $tEndDate }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-slate-400 block text-[11px]">Ngày bàn giao trả:</span>
                                                        <span class="font-bold text-slate-800">{{ !empty($tLiquidation['checkout_date']) ? date('d/m/Y', strtotime($tLiquidation['checkout_date'])) : ($termContract->terminated_at ? date('d/m/Y', strtotime($termContract->terminated_at)) : 'Đã bàn giao') }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-slate-400 block text-[11px]">Trạng thái phòng:</span>
                                                        <span class="font-black text-emerald-600">Còn trống (Đã bàn giao)</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap sm:flex-nowrap gap-2.5 pt-2 border-t border-slate-200/80 items-center justify-between">
                                                <div class="flex flex-wrap gap-2.5 w-full sm:w-auto">
                                                    <button type="button" onclick="openTenantContractModal('{{ $tContractId }}')"
                                                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                                        <i class="fa-solid fa-file-contract"></i> Xem lại hợp đồng
                                                    </button>
                                                    <button type="button" onclick="openSettlementReceipt('{{ $tContractId }}', '{{ addslashes($tHouseTitle) }}', '{{ $tRoomCode }}', this)"
                                                        data-details='@json($tLiquidation)'
                                                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-skybrand-50 hover:bg-skybrand-100 text-skybrand-700 border border-skybrand-200 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                                                        <i class="fa-solid fa-receipt"></i> Xem biên bản quyết toán thanh lý
                                                    </button>
                                                </div>
                                                <button type="button" onclick="deleteContractHistory('{{ $tContractId }}', '{{ $tRoomCode }}')"
                                                    class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 hover:border-rose-300 font-bold text-xs flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer shadow-xs"
                                                    title="Xóa mục này khỏi lịch sử thuê của bạn">
                                                    <i class="fa-regular fa-trash-can"></i> Xóa lịch sử
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
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
                            <div class="w-10 h-10 rounded-xl {{ ($pendingInvoicesCount ?? 0) > 0 ? 'bg-amber-600' : 'bg-emerald-600' }} text-white flex items-center justify-center text-lg font-bold shadow-md">
                                <i class="fa-solid {{ ($pendingInvoicesCount ?? 0) > 0 ? 'fa-qrcode' : 'fa-check-double' }}"></i>
                            </div>
                            <div>
                                @if(($pendingInvoicesCount ?? 0) > 0)
                                    <p class="text-xs text-amber-200 font-medium">Cần thanh toán (<span id="pending-houses-count">{{ $pendingInvoicesCount }}</span> nhà)</p>
                                    <p class="text-sm font-extrabold text-rose-400">
                                        <span id="pending-total-amount">{{ number_format($pendingInvoicesTotal ?? 0) }}</span> VNĐ
                                    </p>
                                @else
                                    <p class="text-xs text-emerald-200 font-medium">Trạng thái công nợ</p>
                                    <p class="text-sm font-extrabold text-emerald-400">
                                        <i class="fa-solid fa-circle-check"></i> Đã thanh toán đầy đủ
                                    </p>
                                @endif
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
                                <button onclick="switchTab('all')" id="tab-all" class="tab-btn active px-3.5 py-2 rounded-xl transition-all">
                                    <i class="fa-solid fa-list-check text-skybrand-600"></i> Tất cả hóa đơn (<span id="tab-all-count">{{ ($allInvoices ?? collect())->count() }}</span>)
                                </button>
                                <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn px-3.5 py-2 rounded-xl transition-all text-slate-600">
                                    <i class="fa-solid fa-clock text-amber-500"></i> Cần thanh toán (<span id="tab-pending-count">{{ $pendingInvoicesCount ?? 0 }}</span>)
                                </button>
                                <button onclick="switchTab('history')" id="tab-history" class="tab-btn px-3.5 py-2 rounded-xl transition-all text-slate-600">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Đã thanh toán (<span id="tab-paid-count">{{ ($paidInvoices ?? collect())->count() }}</span>)
                                </button>
                                <button onclick="switchTab('liabilities')" id="tab-liabilities" class="tab-btn px-3.5 py-2 rounded-xl transition-all text-slate-600">
                                    <i class="fa-solid fa-scale-balanced text-purple-600"></i> Công nợ hợp đồng
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 1: TẤT CẢ HÓA ĐƠN -->
                    <div id="content-all" class="p-6 space-y-6">
                        <div id="all-invoices-container" class="space-y-4">
                            @forelse($allInvoices ?? [] as $inv)
                                @php
                                    $invContract = $inv->contract ?: \App\Models\Contract::find($inv->contract_id);
                                    $invPost = $inv->post ?: ($invContract ? ($invContract->post ?: \App\Models\Post::find($invContract->post_id)) : null);
                                    $invPosterUser = null;
                                    if ($invPost) {
                                        $invPosterId = $invPost->staff_id ?: ($invPost->author_id ?: $invPost->user_id);
                                        if ($invPosterId) {
                                            $invPosterUser = \App\Models\User::find($invPosterId);
                                        }
                                    }
                                    if (!$invPosterUser && $invContract && !empty($invContract->staff_id)) {
                                        $invPosterUser = \App\Models\User::find($invContract->staff_id);
                                    }
                                    if (!$invPosterUser && $invContract && !empty($invContract->created_by)) {
                                        $invPosterUser = \App\Models\User::find($invContract->created_by);
                                    }
                                    $invOwner = $inv->owner ?: ($invPost ? \App\Models\User::find($invPost->user_id) : null);
                                    $invLandlord = $invPosterUser ?: $invOwner;
                                    $invLandlordName = $invLandlord ? ($invLandlord->account_name ?? ($invLandlord->name ?? ($invLandlord->username ?? 'Chủ nhà'))) : 'Chủ nhà';
                                    $invLandlordPhone = $invLandlord ? ($invLandlord->phone ?? 'Chưa cập nhật') : 'Chưa cập nhật';
                                    $invHouseTitle = $invPost ? ($invPost->title ?? 'Nhà / Phòng cho thuê') : ($invContract->tenant_name ? 'Phòng ' . $invContract->tenant_name : 'Phòng trọ');
                                    $invCode = $inv->invoice_code ?? ('INV-' . substr((string)($inv->_id ?? $inv->id), -4));
                                    $invContractId = (string)($inv->contract_id ?? ($invContract ? ($invContract->_id ?? $invContract->id) : ''));
                                    $invId = (string)($inv->_id ?? $inv->id);

                                    $isFirstMonth = ((float)($inv->deposit ?? 0) > 0) || ($inv->billing_period && (str_contains($inv->billing_period, 'Kỳ đầu tiên') || str_contains($inv->billing_period, 'Cọc')));
                                    $isPaid = ($inv->status === 'paid' || $inv->payment_status === 'paid');

                                    // Tạo bảng chi tiết cho từng hóa đơn
                                    if ($isFirstMonth) {
                                        $depositVal = (float)($inv->deposit ?: ($invContract ? ($invContract->deposit ?? 0) : 0));
                                        $rentVal = (float)($inv->rent_price ?: ($invContract ? ($invContract->rent_price ?? 0) : 0));
                                        $servVal = (float)($inv->service_amount ?? 0);
                                        $currentRowsHtml = '
                                            <tr class="bg-amber-50/70 font-bold">
                                                <td class="py-2.5 px-3.5 text-slate-900"><i class="fa-solid fa-shield-halved text-emerald-600 mr-1.5"></i> Tiền đặt cọc giữ nhà (Bảo đảm hợp đồng)</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold">1 lần</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($depositVal) . ' VNĐ</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-700">' . number_format($depositVal) . ' VNĐ</td>
                                            </tr>
                                            <tr class="font-bold">
                                                <td class="py-2.5 px-3.5 text-slate-900"><i class="fa-solid fa-house-chimney text-skybrand-600 mr-1.5"></i> Tiền thuê nhà tháng đầu tiên</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($rentVal) . ' VNĐ</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-skybrand-700">' . number_format($rentVal) . ' VNĐ</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-bolt text-amber-500 mr-1.5"></i> Tiền điện sinh hoạt tháng đầu (Ghi nhận chỉ số đầu)</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . ($inv->electricity_old ?? ($invContract->electricity_start ?? 0)) . ' kWh</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . ($inv->electricity_new ?? ($invContract->electricity_start ?? 0)) . ' kWh</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 kWh (Khởi điểm)</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($invContract->electricity_price ?? 3500) . ' VNĐ/kWh</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-600">0 VNĐ</td>
                                            </tr>
                                            <tr>
                                                ' . (($invContract && ($invContract->water_calculation_type ?? 'khoi') === 'nguoi') ? '
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt tháng đầu (Tính theo người: ' . ($invContract->water_people_count ?? 1) . ' người)</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 (Khởi điểm)</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($invContract->water_price_person ?: ($invContract->water_price ?: 25000)) . ' VNĐ/người</td>
                                                ' : '
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt tháng đầu (Ghi nhận chỉ số đầu)</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . ($inv->water_old ?? ($invContract->water_start ?? 0)) . ' m³</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . ($inv->water_new ?? ($invContract->water_start ?? 0)) . ' m³</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-slate-500">0 (Khởi điểm)</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($invContract->water_price_block ?? 25000) . ' VNĐ/m³</td>
                                                ') . '
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-emerald-600">0 VNĐ</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-wifi text-purple-600 mr-1.5"></i> Phí dịch vụ cố định (Internet, Rác, Gửi xe)</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($servVal) . ' VNĐ</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-700">' . number_format($servVal) . ' VNĐ</td>
                                            </tr>
                                        ';
                                    } else {
                                        $rentVal = (float)($inv->rent_price ?? ($invContract ? ($invContract->rent_price ?? 0) : 0));
                                        $elecOld = (float)($inv->electricity_old ?? 0);
                                        $elecNew = (float)($inv->electricity_new ?? $elecOld);
                                        $elecUsage = max(0, $elecNew - $elecOld);
                                        $elecAmount = (float)($inv->electricity_amount ?? 0);

                                        $waterOld = (float)($inv->water_old ?? 0);
                                        $waterNew = (float)($inv->water_new ?? $waterOld);
                                        $waterUsage = max(0, $waterNew - $waterOld);
                                        $waterAmount = (float)($inv->water_amount ?? 0);

                                        $servVal = (float)($inv->service_amount ?? 0);

                                        $currentRowsHtml = '
                                            <tr class="font-bold">
                                                <td class="py-2.5 px-3.5 text-slate-900"><i class="fa-solid fa-house-chimney text-skybrand-600 mr-1.5"></i> Tiền thuê nhà định kỳ</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($rentVal) . ' VNĐ</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-skybrand-700">' . number_format($rentVal) . ' VNĐ</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-bolt text-amber-500 mr-1.5"></i> Tiền điện sinh hoạt kỳ này</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . $elecOld . ' kWh</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . $elecNew . ' kWh</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-amber-600">' . $elecUsage . ' kWh</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . ($elecUsage > 0 ? number_format(round($elecAmount / $elecUsage)) : number_format($invContract->electricity_price ?? 3500)) . ' VNĐ/kWh</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-amber-600">' . number_format($elecAmount) . ' VNĐ</td>
                                            </tr>
                                            <tr>
                                                ' . (($invContract && ($invContract->water_calculation_type ?? 'khoi') === 'nguoi') ? '
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt kỳ này (Tính theo đầu người)</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-sky-600">' . ($invContract->water_people_count ?? 1) . ' người</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($invContract->water_price_person ?: ($invContract->water_price ?: 25000)) . ' VNĐ/người</td>
                                                ' : '
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-faucet-drip text-sky-500 mr-1.5"></i> Tiền nước sinh hoạt kỳ này</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . $waterOld . ' m³</td>
                                                <td class="py-2.5 px-3.5 text-center font-mono">' . $waterNew . ' m³</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold text-sky-600">' . $waterUsage . ' m³</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . ($waterUsage > 0 ? number_format(round($waterAmount / $waterUsage)) : number_format($invContract->water_price_block ?? 25000)) . ' VNĐ/m³</td>
                                                ') . '
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-sky-600">' . number_format($waterAmount) . ' VNĐ</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3.5 font-bold text-slate-900"><i class="fa-solid fa-wifi text-purple-600 mr-1.5"></i> Phí dịch vụ cố định (Internet, Rác, Gửi xe)</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center text-slate-400">-</td>
                                                <td class="py-2.5 px-3.5 text-center font-semibold">1 tháng</td>
                                                <td class="py-2.5 px-3.5 text-right font-semibold">' . number_format($servVal) . ' VNĐ</td>
                                                <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-700">' . number_format($servVal) . ' VNĐ</td>
                                            </tr>
                                            <tr class="bg-slate-50 text-slate-500 text-[11px]">
                                                <td class="py-2 px-3.5"><i class="fa-solid fa-circle-check text-emerald-600 mr-1.5"></i> Tiền đặt cọc (Bảo đảm hợp đồng)</td>
                                                <td class="py-2 px-3.5 text-center" colspan="4">Đã đóng ở tháng đầu tiên khi nhận nhà</td>
                                                <td class="py-2 px-3.5 text-right font-bold text-emerald-600">0 VNĐ</td>
                                            </tr>
                                        ';
                                    }

                                     $invStaff = null;
                                    if ($invContract && !empty($invContract->staff_id)) {
                                        $invStaff = \App\Models\User::find($invContract->staff_id);
                                    }
                                    if (!$invStaff && !empty($inv->staff_id)) {
                                        $invStaff = \App\Models\User::find($inv->staff_id);
                                    }
                                    if (!$invStaff && $invContract && !empty($invContract->created_by)) {
                                        $cUser = \App\Models\User::find($invContract->created_by);
                                        if ($cUser && in_array($cUser->role, ['staff', 'nhan_vien', 'sub_account'])) {
                                            $invStaff = $cUser;
                                        }
                                    }
                                    $staffName = $invStaff->account_name ?? ($invStaff->name ?? ($invStaff->username ?? $invLandlordName));
                                    $staffPhone = $invStaff->phone ?? $invLandlordPhone;

                                    // Lấy thông tin tài khoản ngân hàng của Quản trị viên (Admin) RentHome để tạo mã VietQR
                                    $adminBank = \App\Models\User::getAdminBankDetails();
                                    $invBankName = $adminBank['bank_name'];
                                    $invBankAccount = $adminBank['bank_account'];
                                    $invBankOwner = $adminBank['bank_holder'];
                                    $invBankShort = !empty($invBankName) ? explode(' ', trim($invBankName))[0] : 'MB Bank';
                                    $isPendingApproval = ($inv->status === 'pending_approval' || $inv->payment_status === 'pending_approval');
                                    $paymentMethod = $inv->payment_method ?? ($inv->payment_type ?? '');

                                    $invItemData = [
                                        'code' => $invCode,
                                        'contractId' => $invContractId,
                                        'houseName' => $invHouseTitle,
                                        'landlordName' => $invLandlordName,
                                        'landlordPhone' => $invLandlordPhone,
                                        'staffName' => $staffName,
                                        'staffPhone' => $staffPhone,
                                        'landlordInfo' => $invLandlordName . ' (SĐT: ' . $invLandlordPhone . ')',
                                        'dueDate' => $invContract && $invContract->billing_date ? ('Hạn ' . $invContract->billing_date . ' hàng tháng') : 'Hạn Mùng 10 hàng tháng',
                                        'total' => number_format($inv->total_amount ?? 0) . ' VNĐ',
                                        'totalRaw' => number_format($inv->total_amount ?? 0),
                                        'bankShort' => $invBankShort,
                                        'bankFull' => $invBankName,
                                        'bankAccount' => $invBankAccount,
                                        'bankOwner' => $invBankOwner,
                                        'rowsHtml' => $currentRowsHtml,
                                        'isPaid' => $isPaid,
                                        'isPendingApproval' => $isPendingApproval,
                                        'proofImage' => !empty($inv->proof_image) ? (str_starts_with($inv->proof_image, 'http') ? $inv->proof_image : asset($inv->proof_image)) : null,
                                        'customQr' => null, // Luôn ưu tiên sinh mã VietQR tự động của Admin
                                        'status' => $inv->status ?? 'unpaid',
                                        'paymentMethod' => $paymentMethod,
                                        'paymentDate' => $inv->payment_date ?? date('d/m/Y'),
                                    ];
                                    $invoiceDataJsonMap[$invCode] = $invItemData;
                                    $invoiceDataJsonMap[$invId] = $invItemData;
                                @endphp
                                <div class="bg-white rounded-2xl {{ $isPaid ? 'border border-emerald-300 shadow-xs bg-emerald-50/15' : ($isPendingApproval ? 'border-2 border-amber-300 shadow-sm bg-amber-50/20' : 'border-2 border-rose-200/90 shadow-sm') }} p-5 hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($isPaid)
                                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1 border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Đã thanh toán
                                                </span>
                                            @elseif($isPendingApproval)
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[11px] flex items-center gap-1 border border-amber-300 animate-pulse">
                                                    <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Đang Chờ Duyệt CK
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1 border border-rose-200">
                                                    <i class="fa-solid fa-clock"></i> Chưa thanh toán
                                                </span>
                                            @endif
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                                Mã: {{ $invCode }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-semibold text-[11px] border border-amber-200">
                                                {{ $inv->billing_period ?? ($isFirstMonth ? 'Kỳ đầu tiên (Cọc & Tháng đầu)' : 'Hóa đơn định kỳ') }}
                                            </span>
                                            @if($isPaid && !empty($inv->payment_date))
                                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-medium">
                                                    <i class="fa-regular fa-calendar-check text-emerald-600"></i> Ngày đóng: {{ $inv->payment_date }}
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-base font-extrabold text-slate-900">{{ $invHouseTitle }}</h3>
                                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-3">
                                            <span>Chủ nhà: <strong class="text-slate-800">{{ $invLandlordName }}</strong> ({{ $invLandlordPhone }})</span>
                                            @if(!empty($staffName) && $staffName !== $invLandlordName)
                                                <span>•</span>
                                                <span>Nhân sự phụ trách: <strong class="text-slate-800">{{ $staffName }}</strong> ({{ $staffPhone }})</span>
                                            @endif
                                            <span>•</span>
                                            <span>Tiền phòng: <strong class="text-skybrand-700">{{ number_format($inv->rent_price ?: ($invContract ? ($invContract->rent_price ?? 0) : 0)) }}đ</strong></span>
                                            @if($isFirstMonth)
                                                <span>•</span>
                                                <span>Tiền cọc: <strong class="text-emerald-700">{{ number_format($inv->deposit ?: ($invContract ? ($invContract->deposit ?? 0) : 0)) }}đ</strong></span>
                                            @else
                                                <span>•</span>
                                                <span>Điện: <strong class="text-amber-600">{{ number_format($inv->electricity_amount ?? 0) }}đ</strong></span>
                                                <span>•</span>
                                                <span>Nước: <strong class="text-sky-600">{{ number_format($inv->water_amount ?? 0) }}đ</strong></span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 shrink-0">
                                        <div class="text-left md:text-right">
                                            <span class="text-xs {{ $isPendingApproval ? 'text-amber-700 font-bold' : 'text-slate-500' }} block">
                                                {{ $isPaid ? 'Số tiền đã đóng:' : ($isPendingApproval ? 'Đang chờ Kế toán duyệt:' : 'Tổng tiền thanh toán:') }}
                                            </span>
                                            <span class="text-xl font-black {{ $isPaid ? 'text-emerald-700' : ($isPendingApproval ? 'text-amber-600' : 'text-rose-600') }}">{{ number_format($inv->total_amount ?? 0) }} VNĐ</span>
                                        </div>
                                        @if($isPaid)
                                            <button onclick="openInvoiceDetailModal('{{ $invCode }}')" class="py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs flex items-center gap-2 shadow-xs transition-all active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-receipt text-emerald-600"></i> Xem chi tiết hóa đơn
                                            </button>
                                        @elseif($isPendingApproval)
                                            <button onclick="openInvoiceDetailModal('{{ $invCode }}')" class="py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-hourglass-half"></i> Xem Trạng Thái Duyệt
                                            </button>
                                        @else
                                            <button onclick="openInvoiceDetailModal('{{ $invCode }}')" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-receipt"></i> Chi tiết & Thanh toán
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 60px 20px; border-radius: 20px; border: 1px dashed #cbd5e1;">
                                    <i class="fa-solid fa-file-invoice-dollar text-slate-300 text-4xl mb-3"></i>
                                    <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Bạn chưa có hóa đơn nào</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB 2: HÓA ĐƠN CẦN THANH TOÁN -->
                    <div id="content-pending" class="hidden p-6 space-y-6">
                        <div id="pending-invoices-container" class="space-y-4">
                            @forelse($pendingInvoices ?? [] as $inv)
                                @php
                                    $invContract = $inv->contract ?: \App\Models\Contract::find($inv->contract_id);
                                    $invPost = $inv->post ?: ($invContract ? ($invContract->post ?: \App\Models\Post::find($invContract->post_id)) : null);
                                    $invPosterUser = null;
                                    if ($invPost) {
                                        $invPosterId = $invPost->staff_id ?: ($invPost->author_id ?: $invPost->user_id);
                                        if ($invPosterId) {
                                            $invPosterUser = \App\Models\User::find($invPosterId);
                                        }
                                    }
                                    if (!$invPosterUser && $invContract && !empty($invContract->staff_id)) {
                                        $invPosterUser = \App\Models\User::find($invContract->staff_id);
                                    }
                                    if (!$invPosterUser && $invContract && !empty($invContract->created_by)) {
                                        $invPosterUser = \App\Models\User::find($invContract->created_by);
                                    }
                                    $invOwner = $inv->owner ?: ($invPost ? \App\Models\User::find($invPost->user_id) : null);
                                    $invLandlord = $invPosterUser ?: $invOwner;
                                    $invLandlordName = $invLandlord ? ($invLandlord->account_name ?? ($invLandlord->name ?? ($invLandlord->username ?? 'Chủ nhà'))) : 'Chủ nhà';
                                    $invLandlordPhone = $invLandlord ? ($invLandlord->phone ?? 'Chưa cập nhật') : 'Chưa cập nhật';
                                    $invHouseTitle = $invPost ? ($invPost->title ?? 'Nhà / Phòng cho thuê') : ($invContract->tenant_name ? 'Phòng ' . $invContract->tenant_name : 'Phòng trọ');
                                    $invCode = $inv->invoice_code ?? ('INV-' . substr((string)($inv->_id ?? $inv->id), -4));
                                    $isFirstMonth = ((float)($inv->deposit ?? 0) > 0) || ($inv->billing_period && (str_contains($inv->billing_period, 'Kỳ đầu tiên') || str_contains($inv->billing_period, 'Cọc')));
                                    $isPendingApproval = ($inv->status === 'pending_approval' || $inv->payment_status === 'pending_approval');
                                    
                                    $invStaff = null;
                                    if ($invContract && !empty($invContract->staff_id)) {
                                        $invStaff = \App\Models\User::find($invContract->staff_id);
                                    }
                                    if (!$invStaff && !empty($inv->staff_id)) {
                                        $invStaff = \App\Models\User::find($inv->staff_id);
                                    }
                                    if (!$invStaff && $invContract && !empty($invContract->created_by)) {
                                        $cUser = \App\Models\User::find($invContract->created_by);
                                        if ($cUser && in_array($cUser->role, ['staff', 'nhan_vien', 'sub_account'])) {
                                            $invStaff = $cUser;
                                        }
                                    }
                                    $staffName = $invStaff->account_name ?? ($invStaff->name ?? ($invStaff->username ?? $invLandlordName));
                                    $staffPhone = $invStaff->phone ?? $invLandlordPhone;
                                @endphp
                                <div class="bg-white rounded-2xl border-2 {{ $isPendingApproval ? 'border-amber-300 shadow-sm bg-amber-50/20' : 'border-rose-200/90 shadow-sm' }} p-5 hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($isPendingApproval)
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[11px] flex items-center gap-1 border border-amber-300 animate-pulse">
                                                    <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Đang Chờ Duyệt CK
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[11px] flex items-center gap-1 border border-rose-200">
                                                    <i class="fa-solid fa-clock"></i> Chưa thanh toán
                                                </span>
                                            @endif
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                                Mã: {{ $invCode }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-semibold text-[11px] border border-amber-200">
                                                {{ $inv->billing_period ?? ($isFirstMonth ? 'Kỳ đầu tiên (Cọc & Tháng đầu)' : 'Hóa đơn định kỳ') }}
                                            </span>
                                        </div>
                                        <h3 class="text-base font-extrabold text-slate-900">{{ $invHouseTitle }}</h3>
                                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-3">
                                            <span>Chủ nhà: <strong class="text-slate-800">{{ $invLandlordName }}</strong> ({{ $invLandlordPhone }})</span>
                                            @if(!empty($staffName) && $staffName !== $invLandlordName)
                                                <span>•</span>
                                                <span>Nhân sự phụ trách: <strong class="text-slate-800">{{ $staffName }}</strong> ({{ $staffPhone }})</span>
                                            @endif
                                            <span>•</span>
                                            <span>Tiền phòng: <strong class="text-skybrand-700">{{ number_format($inv->rent_price ?: ($invContract ? ($invContract->rent_price ?? 0) : 0)) }}đ</strong></span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 shrink-0">
                                        <div class="text-left md:text-right">
                                            <span class="text-xs {{ $isPendingApproval ? 'text-amber-700 font-bold' : 'text-slate-500' }} block">
                                                {{ $isPendingApproval ? 'Đang chờ Kế toán duyệt:' : 'Tổng tiền thanh toán:' }}
                                            </span>
                                            <span class="text-xl font-black {{ $isPendingApproval ? 'text-amber-600' : 'text-rose-600' }}">{{ number_format($inv->total_amount ?? 0) }} VNĐ</span>
                                        </div>
                                        @if($isPendingApproval)
                                            <button onclick="openInvoiceDetailModal('{{ $invCode }}')" class="py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-hourglass-half"></i> Xem Trạng Thái Duyệt
                                            </button>
                                        @else
                                            <button onclick="openInvoiceDetailModal('{{ $invCode }}')" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                                <i class="fa-solid fa-receipt"></i> Chi tiết & Thanh toán
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 px-6 rounded-3xl border border-dashed border-emerald-200 bg-emerald-50/30 space-y-3">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-sm">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-800">Không có hóa đơn nào cần thanh toán</h3>
                                    <p class="text-xs text-slate-500 max-w-md mx-auto">Tất cả hóa đơn tiền phòng và dịch vụ của bạn đã được thanh toán đầy đủ. Trạng thái công nợ hiện tại: 0 VNĐ.</p>
                                    <button type="button" onclick="switchTab('history')" class="mt-2 px-4 py-2 rounded-xl bg-white border border-emerald-300 text-emerald-700 font-bold text-xs shadow-xs hover:bg-emerald-50 transition-all cursor-pointer">
                                        <i class="fa-solid fa-history mr-1"></i> Xem các hóa đơn đã thanh toán ({{ ($paidInvoices ?? collect())->count() }})
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB 3: HÓA ĐƠN CỦ (LỊCH SỬ ĐÃ THANH TOÁN) -->
                    <div id="content-history" class="hidden p-6 space-y-6">
                        @if(($paidInvoices ?? collect())->isNotEmpty())
                            <div class="overflow-x-auto custom-scrollbar border border-slate-200 rounded-2xl">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-slate-600 font-extrabold bg-slate-50 uppercase text-[11px]">
                                            <th class="py-3 px-4">Mã hóa đơn</th>
                                            <th class="py-3 px-4">Nhà / Phòng</th>
                                            <th class="py-3 px-4">Kỳ thanh toán</th>
                                            <th class="py-3 px-4 text-right">Tổng tiền</th>
                                            <th class="py-3 px-4">Ngày thanh toán</th>
                                            <th class="py-3 px-4 text-center">Trạng thái</th>
                                            <th class="py-3 px-4 text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($paidInvoices as $pInv)
                                            @php
                                                $pCode = $pInv->invoice_code ?? ('INV-' . substr((string)($pInv->_id ?? $pInv->id), -4));
                                                $pContract = $pInv->contract ?: \App\Models\Contract::find($pInv->contract_id);
                                                $pPost = $pInv->post ?: ($pContract ? ($pContract->post ?: \App\Models\Post::find($pContract->post_id)) : null);
                                                $pTitle = $pPost ? ($pPost->title ?? 'Nhà / Phòng cho thuê') : ($pContract->tenant_name ? 'Phòng ' . $pContract->tenant_name : 'Phòng trọ');
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="py-3.5 px-4 font-mono font-bold text-skybrand-700">{{ $pCode }}</td>
                                                <td class="py-3.5 px-4 font-bold text-slate-800">{{ $pTitle }}</td>
                                                <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $pInv->billing_period ?? 'Kỳ hàng tháng' }}</td>
                                                <td class="py-3.5 px-4 text-right font-black text-emerald-700">{{ number_format($pInv->total_amount ?? 0) }}đ</td>
                                                <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $pInv->payment_date ?? date('d/m/Y') }}</td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] border border-emerald-200">
                                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Đã thanh toán
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button onclick="openInvoiceDetailModal('{{ $pCode }}')" class="px-3 py-1.5 rounded-lg bg-skybrand-50 hover:bg-skybrand-100 text-skybrand-700 border border-skybrand-200 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                                                        <i class="fa-solid fa-receipt mr-1"></i> Xem chi tiết
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 px-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 space-y-2">
                                <i class="fa-solid fa-receipt text-slate-300 text-3xl"></i>
                                <p class="text-sm font-semibold text-slate-600">Chưa có hóa đơn nào trong lịch sử đã thanh toán</p>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 3: CÔNG NỢ HỢP ĐỒNG -->
                    <div id="content-liabilities" class="hidden p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="p-5 rounded-2xl bg-skybrand-50/70 border border-skybrand-200 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-skybrand-700 uppercase tracking-wider">Tổng tiền cọc {{ $totalRentedCount ?? 0 }} nhà đang giữ</span>
                                    <i class="fa-solid fa-shield-halved text-skybrand-600 text-lg"></i>
                                </div>
                                <p class="text-2xl font-black text-slate-900"><span id="total-deposit-amount">{{ number_format($rentedContracts->sum('deposit') ?? 0) }}</span> VNĐ</p>
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
                <input type="hidden" id="returnContractId" value="">
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
                    <select id="returnReasonSelect" required onchange="handleReturnReasonChange(this)" class="w-full p-2.5 rounded-xl border border-slate-200 font-medium focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                        <option value="">-- Chọn lý do --</option>
                        <option value="Hết thời hạn hợp đồng thuê nhà">Hết thời hạn hợp đồng thuê nhà</option>
                        <option value="Chuyển địa điểm làm việc / chuyển vùng">Chuyển địa điểm làm việc / chuyển vùng</option>
                        <option value="Đã mua được nhà mới">Đã mua được nhà mới</option>
                        <option value="khac">Lý do cá nhân khác...</option>
                    </select>
                    <input type="text" id="returnReasonCustom" class="w-full p-2.5 rounded-xl border border-slate-200 font-medium hidden mt-2" placeholder="Nhập lý do cụ thể...">
                </div>

                <div class="space-y-2 border-t border-slate-100 pt-3">
                    <label class="font-bold text-slate-800 block">Thông tin ngân hàng nhận lại tiền cọc:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <select id="returnBankName" required onchange="handleReturnBankChange(this)" class="p-2.5 rounded-xl border border-slate-200 bg-white font-medium text-xs focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="MBBank">MBBank (Ngân hàng Quân Đội)</option>
                            <option value="Vietcombank">Vietcombank (Ngoại Thương Việt Nam)</option>
                            <option value="VietinBank">VietinBank (Công Thương Việt Nam)</option>
                            <option value="BIDV">BIDV (Đầu tư & Phát triển Việt Nam)</option>
                            <option value="Techcombank">Techcombank (Kỹ Thương Việt Nam)</option>
                            <option value="VPBank">VPBank (Việt Nam Thịnh Vượng)</option>
                            <option value="ACB">ACB (Á Châu)</option>
                            <option value="Agribank">Agribank (Nông nghiệp & PTNT)</option>
                            <option value="TPBank">TPBank (Tiên Phong)</option>
                            <option value="VIB">VIB (Quốc tế Việt Nam)</option>
                            <option value="HDBank">HDBank (Phát triển TP.HCM)</option>
                            <option value="Sacombank">Sacombank (Sài Gòn Thương Tín)</option>
                            <option value="SHB">SHB (Sài Gòn - Hà Nội)</option>
                            <option value="MSB">MSB (Hàng Hải Việt Nam)</option>
                            <option value="OCB">OCB (Phương Đông)</option>
                            <option value="SeABank">SeABank (Đông Nam Á)</option>
                            <option value="LPBank">LPBank (Lộc Phát Việt Nam)</option>
                            <option value="Nam A Bank">Nam A Bank (Nam Á)</option>
                            <option value="Bac A Bank">Bac A Bank (Bắc Á)</option>
                            <option value="PVcomBank">PVcomBank (Đại Chúng Việt Nam)</option>
                            <option value="VietBank">VietBank (Việt Nam Thương Tín)</option>
                            <option value="Kienlongbank">Kienlongbank (Kiên Long)</option>
                            <option value="Saigonbank">Saigonbank (Sài Gòn Công Thương)</option>
                            <option value="BVBank">BVBank (Bản Việt)</option>
                            <option value="PGBank">PGBank (Thịnh vượng & Phát triển)</option>
                            <option value="BaoViet Bank">BaoViet Bank (Bảo Việt)</option>
                            <option value="VietABank">VietABank (Việt Á)</option>
                            <option value="Shinhan Bank">Shinhan Bank (Shinhan Việt Nam)</option>
                            <option value="Woori Bank">Woori Bank (Woori Việt Nam)</option>
                            <option value="Public Bank">Public Bank (Public Bank Việt Nam)</option>
                            <option value="HSBC">HSBC (HSBC Việt Nam)</option>
                            <option value="Standard Chartered">Standard Chartered (Standard Chartered VN)</option>
                            <option value="UOB">UOB (United Overseas Bank VN)</option>
                            <option value="CIMB">CIMB (CIMB Việt Nam)</option>
                            <option value="Indovina Bank">Indovina Bank (Indovina)</option>
                            <option value="VRB">VRB (Việt - Nga)</option>
                            <option value="khac">Ngân hàng khác...</option>
                        </select>
                        <input type="text" id="returnBankAccount" placeholder="Số tài khoản" required class="p-2.5 rounded-xl border border-slate-200 font-mono text-xs focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                    </div>
                    <input type="text" id="returnBankNameCustom" class="w-full p-2.5 rounded-xl border border-slate-200 font-medium text-xs hidden" placeholder="Nhập tên ngân hàng của bạn...">
                    <input type="text" id="returnBankHolder" placeholder="Tên chủ tài khoản nhận tiền" required class="w-full p-2.5 rounded-xl border border-slate-200 uppercase text-xs focus:ring-2 focus:ring-skybrand-500 focus:outline-none">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Ghi chú bổ sung cho chủ nhà:</label>
                    <textarea id="returnNote" rows="2" class="w-full p-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-skybrand-500 focus:outline-none" placeholder="Hẹn giờ bàn giao chìa khóa, tình trạng phòng..."></textarea>
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
            <div id="detailBannerHeader" class="relative bg-gradient-to-r from-rose-500 via-rose-600 to-amber-600 p-4 sm:p-5 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
                    <div id="detailStatusBadge" class="bg-white text-rose-600 px-4 py-2 rounded-xl shadow-lg border-2 border-rose-200 text-center font-black tracking-wider uppercase text-sm animate-pulse">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i> CHƯA THANH TOÁN
                    </div>
                </div>
            </div>

            <!-- Khung thông báo khi hóa đơn đã thanh toán -->
            <div id="detailPaidAlert" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-bold text-xs flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg shrink-0"></i>
                <span id="detailPaidAlertText">Hóa đơn này đã được xác nhận thanh toán thành công!</span>
            </div>

            <!-- Khung thông báo khi hóa đơn Đang Chờ Duyệt CK (YÊU CẦU 2) -->
            <div id="detailPendingApprovalAlert" class="hidden p-3.5 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl text-amber-900 shadow-sm space-y-2">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <i class="fa-solid fa-hourglass-half text-base animate-spin" style="animation-duration: 3s;"></i>
                    </div>
                    <div class="space-y-0.5 flex-1">
                        <h5 class="font-extrabold text-sm text-amber-900">Bạn đã báo cáo thanh toán thành công. Vui lòng chờ Kế toán duyệt để hoàn tất!</h5>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Hệ thống sẽ tự động gạch nợ và đổi trạng thái hóa đơn sang màu xanh ngay sau khi Kế toán hoàn tất đối soát ngân hàng.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-200 text-amber-900 text-[10px] font-black uppercase tracking-wider shrink-0">
                        Đang Chờ Duyệt CK
                    </span>
                </div>
                <div id="detailProofContainer" class="hidden pt-2 border-t border-amber-200/80 flex items-center gap-3">
                    <span class="text-xs font-semibold text-amber-900">Biên lai giao dịch:</span>
                    <a id="detailProofLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white border border-amber-300 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-xs">
                        <i class="fa-solid fa-image text-amber-600"></i> Xem ảnh biên lai đã gửi
                    </a>
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
                        <tr id="detailFooterRow" class="bg-rose-50/90 border-t-2 border-rose-300 font-extrabold">
                            <td colspan="5" class="py-3 px-3.5 text-xs sm:text-sm text-slate-900 uppercase tracking-wide">
                                <i class="fa-solid fa-calculator text-rose-600"></i> <span id="detailTotalLabel">TỔNG CỘNG CẦN THANH TOÁN:</span>
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

                            <!-- Hiển thị ảnh mã QR riêng nếu có -->
                            <div id="detailCustomQrWrapper" class="hidden p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-1.5">
                                <img id="detailCustomQrImg" src="" alt="Mã QR Chuyển Khoản" class="w-36 h-36 mx-auto object-contain rounded-lg shadow-xs bg-white p-1 border border-slate-200">
                                <span class="text-[11px] text-emerald-700 font-bold flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-qrcode text-emerald-600"></i> Mã QR Thanh Toán
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-1">
                            <button id="btnTriggerVietQR" 
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-sm transition-all active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-qrcode text-sm"></i> Quét mã VietQR chuyển khoản ngay
                            </button>

                            <!-- Nút Báo Đã Chuyển Khoản (YÊU CẦU 1) -->
                            <button id="btnTriggerReportTransfer" type="button" onclick="openReportTransferModal()" 
                                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-sm"></i> Báo Đã Chuyển Khoản
                            </button>

                            <!-- Dòng thông báo khi Đang Chờ Duyệt CK (YÊU CẦU 2) -->
                            <div id="transferPendingMessage" class="hidden p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-300 text-amber-900 text-xs font-semibold space-y-1 shadow-xs">
                                <div class="flex items-center gap-2 font-bold text-amber-800">
                                    <i class="fa-solid fa-clock-rotate-left text-amber-600 text-base"></i>
                                    <span>Bạn đã báo cáo thanh toán thành công. Vui lòng chờ Kế toán duyệt để hoàn tất!</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HÌNH THỨC 2: TIỀN MẶT (YÊU CẦU 3) -->
                    <div class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-200 shadow-xs space-y-2.5 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-[11px] font-extrabold border border-amber-300">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Hình thức 2: Tiền mặt
                                </span>
                                <span class="text-[10px] text-amber-700 font-bold">Đóng trực tiếp</span>
                            </div>
                            
                            <div class="p-2.5 bg-white/90 rounded-xl border border-amber-200 space-y-2 text-xs">
                                <p class="text-amber-900 font-bold flex items-center gap-2 text-xs">
                                    <i class="fa-solid fa-hand-holding-dollar text-amber-600"></i>
                                    <span>Đóng tiền mặt cho Nhân sự phụ trách:</span>
                                </p>
                                <div class="bg-amber-50/70 p-2 rounded-lg border border-amber-200/80 text-[11px] space-y-1">
                                    <p class="flex justify-between">
                                        <span class="text-slate-600">Nhân sự phụ trách:</span>
                                        <strong id="detailStaffName" class="text-slate-900">--</strong>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-slate-600">Số điện thoại:</span>
                                        <strong id="detailStaffPhone" class="text-skybrand-700 font-mono">--</strong>
                                    </p>
                                </div>
                                <p id="detailCashNote" class="text-slate-600 leading-relaxed text-[11px]">
                                    Sau khi bạn đưa tiền mặt, Nhân sự sẽ xác nhận trên hệ thống và hóa đơn của bạn sẽ chuyển sang màu xanh (Đã thanh toán).
                                </p>
                            </div>
                        </div>

                        <!-- Nút Gọi Nhân Sự Thu Tiền (YÊU CẦU 3) -->
                        <a id="btnCallStaffCash" href="tel:0912345678" 
                           class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all shadow-sm active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-phone"></i> Gọi Nhân Sự Thu Tiền
                        </a>
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
                <button onclick="closePayQRModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center cursor-pointer">
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

            <button onclick="handleQRCompleted()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all active:scale-95 cursor-pointer">
                <i class="fa-solid fa-circle-check"></i> Tôi đã hoàn tất chuyển khoản
            </button>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL: BÁO ĐÃ CHUYỂN KHOẢN (YÊU CẦU 1 - UPLOAD BIÊN LAI / BILL GIAO DỊCH) -->
    <!-- ========================================================================= -->
    <div id="reportTransferModal" onclick="if(event.target === this) closeReportTransferModal()" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 cursor-pointer">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all cursor-default">
            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl shadow-xs">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight">Xác Nhận Đã Chuyển Khoản</h3>
                        <p class="text-xs text-amber-100">Báo cáo để Kế toán duyệt hóa đơn ngay</p>
                    </div>
                </div>
                <button type="button" onclick="closeReportTransferModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body Form -->
            <form id="formReportTransfer" onsubmit="submitReportTransfer(event)" class="p-5 space-y-4">
                <input type="hidden" id="reportInvoiceCode" name="invoice_code" value="">

                <!-- Tóm tắt hóa đơn -->
                <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200/90 text-xs space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Mã hóa đơn:</span>
                        <strong id="reportCodeDisplay" class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-amber-200">HD-092026</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Số tiền thanh toán:</span>
                        <strong id="reportAmountDisplay" class="text-base font-black text-rose-600">3.605.000 VNĐ</strong>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-500 pt-1 border-t border-amber-200/60">
                        <span>Ngân hàng nhận:</span>
                        <span id="reportBankDisplay" class="font-bold text-slate-700">MB Bank - 0912 987 654 888</span>
                    </div>
                </div>

                <!-- Upload Ảnh Biên Lai (YÊU CẦU 1) -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800">
                        <i class="fa-solid fa-camera text-amber-600 mr-1"></i> Ảnh biên lai / Bill giao dịch thành công:
                        <span class="text-[11px] font-normal text-slate-500">(Không bắt buộc, nhưng khuyến khích)</span>
                    </label>

                    <!-- Dropzone / Input wrapper -->
                    <div id="dropzoneProof" onclick="document.getElementById('proofImageInput').click()" 
                         class="group relative border-2 border-dashed border-amber-300 hover:border-amber-500 bg-amber-50/30 hover:bg-amber-50/70 rounded-2xl p-4 text-center cursor-pointer transition-all">
                        <input type="file" id="proofImageInput" name="proof_image" accept="image/*" class="hidden" onchange="previewProofImage(this)">
                        
                        <div id="dropzonePrompt" class="space-y-1.5 py-2">
                            <div class="w-10 h-10 mx-auto rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Chạm để tải ảnh hoặc chụp biên lai giao dịch</p>
                            <p class="text-[11px] text-slate-400">Hỗ trợ JPG, PNG, WEBP (Tối đa 5MB)</p>
                        </div>

                        <!-- Image Preview -->
                        <div id="proofPreviewContainer" class="hidden relative">
                            <img id="proofPreviewImage" src="#" alt="Biên lai giao dịch" class="max-h-52 mx-auto rounded-xl shadow-md border border-slate-200 object-contain">
                            <button type="button" onclick="event.stopPropagation(); removeProofImage();" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-lg hover:bg-rose-700 transition-colors cursor-pointer">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ghi chú thêm -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-800">
                        <i class="fa-regular fa-comment-dots text-slate-500 mr-1"></i> Lời nhắn hoặc Ghi chú (Nếu có):
                    </label>
                    <input type="text" id="reportNote" name="note" placeholder="Vd: Đã CK từ ngân hàng Techcombank số đuôi ...889 lúc 14h30" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Lưu ý bảo đảm -->
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center gap-2 text-[11px] text-slate-600">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-base shrink-0"></i>
                    <span>Sau khi bấm xác nhận, hệ thống sẽ chuyển hóa đơn sang <strong>Đang Chờ Duyệt CK</strong> để Kế toán đối soát và gạch nợ!</span>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-2 flex items-center gap-3">
                    <button type="button" onclick="closeReportTransferModal()" class="w-1/3 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors cursor-pointer">
                        Để sau
                    </button>
                    <button type="submit" id="btnSubmitReportTransfer" class="w-2/3 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Xác Nhận & Báo Chuyển Khoản</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- MODAL 4: XEM HỢP ĐỒNG PHÁP LÝ A4 (DÀNH CHO KHÁCH THUÊ) -->
    <!-- ========================================================================= -->
    <div id="tenantContractModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-2 sm:p-5 overflow-y-auto print:p-0 print:bg-white">
        <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl overflow-hidden border border-slate-200 max-h-[94vh] flex flex-col transform transition-all my-auto print:border-none print:shadow-none print:my-0 print:max-h-none">
            
            <!-- TOP TOOLBAR: Thanh tiêu đề màu sáng trang nhã, đồng bộ tuyệt đối với tờ hợp đồng -->
            <div class="bg-white text-slate-800 px-5 sm:px-6 py-3.5 flex items-center justify-between shrink-0 border-b border-slate-200 print:hidden">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Văn Bản Hợp Đồng Pháp Lý
                    </span>
                    <span class="text-xs text-slate-500 font-mono hidden sm:inline" id="tcContractCodeHeader">Mã HĐ: HD-202610-006C</span>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs text-slate-700 font-sans font-semibold transition flex items-center gap-1.5 border border-slate-300">
                        <i class="fa-solid fa-print"></i>
                        <span>In A4</span>
                    </button>
                    <button onclick="closeTenantContractModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center transition border border-slate-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>
            </div>

            <!-- VIEWPORT CUỘN: Nền sáng nhẹ nhàng giúp tờ giấy trắng nổi bật liền mạch -->
            <div class="p-3 sm:p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1 bg-slate-100/90 print:p-0 print:bg-white print:overflow-visible">
                
                <!-- TỜ GIẤY A4 PHÁP LÝ (LEGAL CONTRACT SHEET): Nền trắng tinh khiết liền mạch 100% -->
                <div id="tenantLegalContractSheet" 
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
                        <p class="text-right italic text-xs sm:text-sm text-slate-700" id="tcLegalDate">
                            Hà Nội, ngày 11 tháng 10 năm 2026
                        </p>
                    </div>

                    <!-- 2. TIÊU ĐỀ LỚN Ở GIỮA -->
                    <div class="text-center my-6">
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-950">
                            HỢP ĐỒNG CHO THUÊ NHÀ Ở / PHÒNG TRỌ
                        </h3>
                        <p class="text-xs sm:text-sm italic text-slate-600 mt-1 font-sans" id="tcContractCode">
                            Mã HĐ: HD-202610-006C
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
                        <p class="font-bold uppercase text-slate-950 text-xs sm:text-sm flex items-center justify-between">
                            <span>BÊN A (BÊN CHO THUÊ / CHỦ NHÀ):</span>
                            <span class="text-[11px] font-normal text-emerald-700 normal-case bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Đã xác minh chính chủ</span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 gap-x-4 text-xs sm:text-sm">
                            <p>Họ và tên: <strong class="uppercase text-slate-950" id="tcPartyAName">--</strong></p>
                            <p>CCCD/CMND: <span class="font-semibold font-mono" id="tcPartyAIdCard">--</span></p>
                            <p>Số điện thoại: <span class="font-semibold font-mono" id="tcPartyAPhone">--</span></p>
                            <p>Địa chỉ: <span id="tcPartyAAddress">--</span></p>
                        </div>
                    </div>

                    <!-- THÔNG TIN BÊN B (BÊN THUÊ) -->
                    <div class="space-y-1.5 mb-5 pl-3 sm:pl-4 border-l-2 border-slate-300">
                        <p class="font-bold uppercase text-slate-950 text-xs sm:text-sm flex items-center justify-between">
                            <span>BÊN B (BÊN THUÊ NHÀ):</span>
                            <span class="text-[11px] font-normal text-skybrand-700 normal-case bg-sky-50 px-2 py-0.5 rounded border border-sky-200">Đại diện khách thuê</span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 gap-x-4 text-xs sm:text-sm">
                            <p>Họ và tên: <strong class="uppercase text-slate-950" id="tcTenant">--</strong></p>
                            <p>CCCD/CMND: <span class="font-semibold font-mono" id="tcTenantIdCard">--</span></p>
                            <p>Số điện thoại: <span class="font-semibold font-mono text-slate-950" id="tcTenantPhone">--</span></p>
                            <p>Địa chỉ liên hệ: <span id="tcTenantAddress">--</span></p>
                        </div>
                    </div>

                    <p class="italic mb-4 text-justify text-slate-700">Hai bên cùng thỏa thuận và thống nhất ký kết hợp đồng thuê với các điều khoản sau:</p>

                    <!-- NỘI DUNG CÁC ĐIỀU KHOẢN VĂN XUÔI -->
                    <div class="space-y-4 text-justify leading-relaxed text-slate-900">
                        
                        <!-- ĐIỀU 1: THÔNG TIN PHÒNG THUÊ -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 1. Thông tin tài sản cho thuê</p>
                            <p class="mt-1 indent-6">
                                Bên A đồng ý cho Bên B thuê căn hộ / phòng: 
                                <strong class="underline decoration-dotted text-slate-950 font-bold" id="tcHouseTitle">--</strong> 
                                (Mã: <span id="tcRoomCode" class="font-bold">--</span>), 
                                tại địa chỉ: <span id="tcAddress" class="font-semibold text-slate-950">--</span>.
                                Bên B cam kết sử dụng tài sản đúng mục đích để ở và chấp hành đầy đủ quy định phòng cháy chữa cháy, an ninh trật tự địa phương.
                            </p>
                        </div>

                        <!-- ĐIỀU 2: GIÁ CẢ VÀ THỜI HẠN -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 2. Thời hạn thuê, giá cả và tiền đặt cọc</p>
                            <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                <li>
                                    <strong>Thời hạn thuê:</strong> <span class="font-semibold" id="tcDuration">12 tháng</span>, 
                                    từ ngày <span class="font-semibold" id="tcDates">--</span>.
                                </li>
                                <li>
                                    <strong>Giá thuê phòng:</strong> 
                                    <strong class="text-slate-950 font-bold" id="tcRentPrice">--</strong> 
                                    (Giá thuê cố định, thanh toán theo chu kỳ thỏa thuận).
                                </li>
                                <li>
                                    <strong>Tiền đặt cọc đảm bảo:</strong> 
                                    <strong class="text-slate-950 font-bold" id="tcDeposit">--</strong>. 
                                    Tiền cọc được Bên A bảo toàn và hoàn trả 100% cho Bên B khi kết thúc hợp đồng theo đúng cam kết sau khi trừ các khoản chi phí phát sinh hư hại (nếu có).
                                </li>
                            </ul>
                        </div>

                        <!-- ĐIỀU 3: PHÍ DỊCH VỤ ĐI KÈM -->
                        <div id="tcServiceSection">
                            <p class="font-bold text-slate-950" id="tcServiceTitle">Điều 3. Phí dịch vụ đi kèm</p>

                            <!-- KHỐI 1: Khi cơ sở / bài đăng KHÔNG thu phí dịch vụ -->
                            <div id="tcNoServiceBlock" class="hidden mt-1 text-slate-800">
                                <p class="indent-6 leading-relaxed">
                                    Cơ sở này không thu phí dịch vụ tập trung. Bên B (Khách thuê) tự ký hợp đồng và trực tiếp thanh toán tiền điện, nước sinh hoạt theo chỉ số công tơ/đồng hồ riêng với các đơn vị cung cấp theo biểu giá quy định của Nhà nước. Bên A không thu thêm bất kỳ khoản phí dịch vụ nào.
                                </p>
                            </div>

                            <!-- KHỐI 2: Khi CÓ thu phí dịch vụ thông thường -->
                            <div id="tcHasServiceBlock">
                                <p class="mt-1 indent-6">
                                    Ngoài tiền thuê cố định hàng tháng, Bên B có nghĩa vụ thanh toán đầy đủ các chi phí dịch vụ thực tế phát sinh định kỳ gồm:
                                </p>
                                <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                    <li>
                                        <strong>Tiền điện sinh hoạt:</strong> 
                                        <strong id="tcElecPrice" class="font-semibold text-slate-950">--</strong> 
                                        (<span id="tcElecStart" class="text-slate-600 font-mono">Chỉ số đầu: 0 kWh</span>).
                                    </li>
                                    <li>
                                        <strong id="tcWaterTypeLabel">Tiền nước sinh hoạt:</strong> 
                                        <strong id="tcWaterPrice" class="font-semibold text-slate-950">--</strong> 
                                        (<span id="tcWaterSubInfo" class="text-slate-600 font-mono">Chỉ số đầu: 0 m³</span>).
                                    </li>
                                    <li>
                                        <strong>Phí Internet và Rác sinh hoạt:</strong> 
                                        <strong id="tcInternetGarbagePrice" class="font-semibold text-slate-950">--</strong> 
                                        (Internet/Wifi: <span id="tcInternetPrice">--</span>, Rác sinh hoạt: <span id="tcGarbagePrice">--</span>).
                                    </li>
                                    <li>
                                        <strong>Phí gửi xe:</strong> 
                                        <strong id="tcParkingFee" class="font-semibold text-slate-950">--</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- ĐIỀU 4: CAM KẾT THANH TOÁN -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 4. Cam kết và quy định thanh toán</p>
                            <ul class="list-disc pl-6 space-y-1 mt-1 text-slate-800">
                                <li>
                                    <strong>Chu kỳ thanh toán:</strong> 
                                    Là <span class="font-semibold" id="tcPaymentCycle">1 tháng (Hàng tháng)</span>.
                                </li>
                                <li>
                                    <strong>Ngày chốt tiền & thanh toán:</strong> 
                                    Định kỳ vào ngày <strong id="tcBillDate" class="underline text-slate-950">Mùng 10 hàng tháng</strong>.
                                </li>
                                <li>
                                    <strong>Quy định chậm thanh toán:</strong> 
                                    Bên B cam kết thanh toán đúng hạn. Trường hợp chậm trễ, Bên B chấp thuận áp dụng mức 
                                    <strong class="text-rose-700" id="tcPenaltyBadge">Phạt trễ hạn: 20.000 VNĐ / ngày</strong>.
                                </li>
                            </ul>
                        </div>

                        <!-- ĐIỀU 5: CAM KẾT CHUNG -->
                        <div>
                            <p class="font-bold text-slate-950">Điều 5. Cam kết chung và hiệu lực hợp đồng</p>
                            <p class="mt-1 indent-6">
                                Hợp đồng này được xác lập có đầy đủ hiệu lực pháp lý theo tiêu chuẩn hệ thống RentHome. Hai bên cam kết thực hiện đúng và đầy đủ các điều khoản trên. Hợp đồng được lưu trữ điện tử an toàn và minh bạch cho cả hai bên cùng theo dõi.
                            </p>
                        </div>



                    </div>

                    <!-- 3. HAI CỘT CHỮ KÝ ĐẠI DIỆN BÊN A VÀ BÊN B -->
                    <div class="grid grid-cols-2 gap-8 text-center mt-12 pt-4 border-t border-slate-200">
                        <div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-950">
                                ĐẠI DIỆN BÊN A (CHỦ NHÀ)
                            </p>
                            <p class="text-[11px] sm:text-xs italic text-slate-500">
                                (Ký và ghi rõ họ tên)
                            </p>
                            <div class="h-20 sm:h-24 flex items-center justify-center flex-col">
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-300 shadow-xs">
                                    <i class="fa-solid fa-certificate text-emerald-600"></i> Đã phê duyệt hợp đồng
                                </span>
                                <span class="text-[10px] text-slate-400 mt-1 font-mono">Chữ ký điện tử xác thực</span>
                            </div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-900 mt-1" id="tcSignPartyA">
                                --
                            </p>
                        </div>

                        <div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-950">
                                ĐẠI DIỆN BÊN B (KHÁCH THUÊ)
                            </p>
                            <p class="text-[11px] sm:text-xs italic text-slate-500">
                                (Ký và ghi rõ họ tên)
                            </p>
                            <div class="h-20 sm:h-24 flex items-center justify-center flex-col">
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-300 shadow-xs">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Đã ký nhận điện tử
                                </span>
                                <span class="text-[10px] text-slate-400 mt-1 font-mono">Xác thực tài khoản người thuê</span>
                            </div>
                            <p class="font-bold uppercase text-xs sm:text-sm text-slate-900 mt-1" id="tcSignPartyB">
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
                
                <!-- Tóm tắt trạng thái hợp đồng -->
                <div class="text-left w-full sm:w-auto">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Đang hiệu lực
                        </span>
                        <span class="text-xs text-slate-500 font-sans" id="tcFooterSummary">
                            Tiền cọc: 3.000.000đ • Giá thuê: 130.000.000đ/tháng
                        </span>
                    </div>
                </div>

                <!-- Nhóm nút bấm -->
                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 hover:bg-slate-100 text-slate-700 font-sans text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>In Hợp Đồng</span>
                    </button>
                    <button type="button" onclick="closeTenantContractModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-sans text-xs font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Đóng</span>
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


    <!-- JavaScript Functions -->
    <script>
        // Data Store for Detailed Invoices (Image 3)
        let invoiceDataMap = @json($invoiceDataJsonMap ?? []);

        // Data Store for Tenant Contracts
        const tenantContractsMap = @json($contractJsonMap ?? []);

        let currentViewingContractId = null;

        // Helper format VNĐ
        function formatVnd(val) {
            if (val === null || val === undefined || val === '') return '0đ';
            if (typeof val === 'string' && val.includes('đ')) return val;
            const num = parseFloat(String(val).replace(/[^\d.]/g, '')) || 0;
            return new Intl.NumberFormat('vi-VN').format(num) + 'đ';
        }

        // Logic mở Modal Xem Hợp Đồng Khách Thuê
        function openTenantContractModal(contractId) {
            currentViewingContractId = contractId;
            const data = tenantContractsMap[contractId];
            if (!data) {
                showToast('Không tìm thấy thông tin hợp đồng này!');
                return;
            }

            const now = new Date();
            const dayStr = String(now.getDate()).padStart(2, '0');
            const monthStr = String(now.getMonth() + 1).padStart(2, '0');
            const yearStr = now.getFullYear();
            if (document.getElementById('tcLegalDate')) {
                document.getElementById('tcLegalDate').innerText = `Hà Nội, ngày ${dayStr} tháng ${monthStr} năm ${yearStr}`;
            }

            const cCode = data.contract_code || ('HD-' + contractId.slice(-4));
            if (document.getElementById('tcContractCodeHeader')) document.getElementById('tcContractCodeHeader').innerText = 'Mã HĐ: ' + cCode;
            if (document.getElementById('tcContractCode')) document.getElementById('tcContractCode').innerText = 'Mã HĐ: ' + cCode;
            if (document.getElementById('tcHouseTitle')) document.getElementById('tcHouseTitle').innerText = data.house_title || 'Nhà / Căn hộ cho thuê';
            if (document.getElementById('tcRoomCode')) document.getElementById('tcRoomCode').innerText = cCode;
            if (document.getElementById('tcAddress')) document.getElementById('tcAddress').innerText = data.address || 'Địa chỉ căn nhà';

            // Bên A
            if (document.getElementById('tcPartyAName')) document.getElementById('tcPartyAName').innerText = data.landlord_name || 'Chủ nhà';
            if (document.getElementById('tcPartyAPhone')) document.getElementById('tcPartyAPhone').innerText = data.landlord_phone || 'Chưa cập nhật';
            if (document.getElementById('tcPartyAIdCard')) document.getElementById('tcPartyAIdCard').innerText = data.landlord_id_card || 'Chưa cập nhật';
            if (document.getElementById('tcPartyAAddress')) document.getElementById('tcPartyAAddress').innerText = data.landlord_address || data.address || 'Chưa cập nhật';
            if (document.getElementById('tcSignPartyA')) document.getElementById('tcSignPartyA').innerText = data.landlord_name || 'Chủ nhà';

            // Bên B
            if (document.getElementById('tcTenant')) document.getElementById('tcTenant').innerText = data.tenant_name || 'Khách thuê';
            if (document.getElementById('tcTenantPhone')) document.getElementById('tcTenantPhone').innerText = data.tenant_phone || 'Chưa cập nhật';
            if (document.getElementById('tcTenantIdCard')) document.getElementById('tcTenantIdCard').innerText = data.tenant_id_card || 'Chưa cập nhật';
            if (document.getElementById('tcTenantAddress')) document.getElementById('tcTenantAddress').innerText = data.tenant_address || 'Theo hồ sơ đăng ký';
            if (document.getElementById('tcSignPartyB')) document.getElementById('tcSignPartyB').innerText = data.tenant_name || 'Khách thuê';

            // Điều 2
            const rentPriceFormatted = formatVnd(data.rent_price);
            const depositFormatted = formatVnd(data.deposit);
            if (document.getElementById('tcRentPrice')) document.getElementById('tcRentPrice').innerText = rentPriceFormatted + ' / tháng';
            if (document.getElementById('tcDeposit')) document.getElementById('tcDeposit').innerText = depositFormatted;
            if (document.getElementById('tcDuration')) document.getElementById('tcDuration').innerText = (data.duration_months || 12) + ' tháng';
            if (document.getElementById('tcDates')) document.getElementById('tcDates').innerText = (data.start_date || '--') + ' - ' + (data.end_date || '--');

            // Điều 3: Phí Dịch Vụ
            const elecPriceNum = parseFloat(String(data.electricity_price || 0).replace(/[^\d.]/g, '')) || 0;
            const waterBlockNum = parseFloat(String(data.water_price_block || 0).replace(/[^\d.]/g, '')) || 0;
            const waterPersonNum = parseFloat(String(data.water_price_person || 0).replace(/[^\d.]/g, '')) || 0;
            const internetNum = parseFloat(String(data.internet_price || 0).replace(/[^\d.]/g, '')) || 0;
            const garbageNum = parseFloat(String(data.garbage_price || 0).replace(/[^\d.]/g, '')) || 0;
            const parkingNum = parseFloat(String(data.parking_fee || data.parking_price || 0).replace(/[^\d.]/g, '')) || 0;

            const isNoServiceFee = Boolean(
                data.no_service_fee === true || 
                data.no_service_fee === 'true' || 
                data.no_service_fee === 1 ||
                (elecPriceNum <= 0 && waterBlockNum <= 0 && waterPersonNum <= 0 && internetNum <= 0 && garbageNum <= 0 && parkingNum <= 0)
            );

            const tcTitle = document.getElementById('tcServiceTitle');
            const tcNoBlock = document.getElementById('tcNoServiceBlock');
            const tcHasBlock = document.getElementById('tcHasServiceBlock');

            if (isNoServiceFee) {
                if (tcTitle) tcTitle.innerText = 'Điều 3. Quy định về chi phí điện, nước và dịch vụ sinh hoạt';
                if (tcNoBlock) tcNoBlock.classList.remove('hidden');
                if (tcHasBlock) tcHasBlock.classList.add('hidden');
            } else {
                if (tcTitle) tcTitle.innerText = 'Điều 3. Phí dịch vụ đi kèm';
                if (tcNoBlock) tcNoBlock.classList.add('hidden');
                if (tcHasBlock) tcHasBlock.classList.remove('hidden');

                if (document.getElementById('tcElecPrice')) document.getElementById('tcElecPrice').innerText = formatVnd(data.electricity_price) + ' / kWh';
                if (document.getElementById('tcElecStart')) document.getElementById('tcElecStart').innerText = `Chỉ số đầu: ${data.electricity_start || 0} kWh`;

                if (data.water_calculation_type === 'nguoi') {
                    if (document.getElementById('tcWaterTypeLabel')) document.getElementById('tcWaterTypeLabel').innerText = 'Tiền nước sinh hoạt (Theo người):';
                    if (document.getElementById('tcWaterPrice')) document.getElementById('tcWaterPrice').innerText = formatVnd(data.water_price_person) + ' / người / tháng';
                    if (document.getElementById('tcWaterSubInfo')) document.getElementById('tcWaterSubInfo').innerText = `Số người ở: ${data.water_people_count || 1} người`;
                } else {
                    if (document.getElementById('tcWaterTypeLabel')) document.getElementById('tcWaterTypeLabel').innerText = 'Tiền nước sinh hoạt (Theo khối):';
                    if (document.getElementById('tcWaterPrice')) document.getElementById('tcWaterPrice').innerText = formatVnd(data.water_price_block) + ' / m³';
                    if (document.getElementById('tcWaterSubInfo')) document.getElementById('tcWaterSubInfo').innerText = `Chỉ số đầu: ${data.water_start || 0} m³`;
                }

                const netPrice = formatVnd(data.internet_price);
                const garbagePrice = formatVnd(data.garbage_price);
                if (document.getElementById('tcInternetPrice')) document.getElementById('tcInternetPrice').innerText = netPrice;
                if (document.getElementById('tcGarbagePrice')) document.getElementById('tcGarbagePrice').innerText = garbagePrice;
                if (document.getElementById('tcInternetGarbagePrice')) {
                    const totalNetGarbage = (parseFloat(data.internet_price || 0) + parseFloat(data.garbage_price || 0));
                    document.getElementById('tcInternetGarbagePrice').innerText = totalNetGarbage > 0 ? (formatVnd(totalNetGarbage) + ' / tháng') : `${netPrice} + Rác: ${garbagePrice}`;
                }
                if (document.getElementById('tcParkingFee')) {
                    document.getElementById('tcParkingFee').innerText = parkingNum > 0 ? (formatVnd(parkingNum) + ' / xe / tháng') : 'Miễn phí (0đ/xe)';
                }
            }

            // Điều 4
            if (document.getElementById('tcPaymentCycle')) {
                let cycleText = data.payment_cycle || '1 tháng/lần';
                if (/^\d+$/.test(String(cycleText).trim())) {
                    const cNum = parseInt(cycleText, 10);
                    cycleText = (cNum === 12) ? '1 năm/lần' : `${cNum} tháng/lần`;
                } else if (!String(cycleText).includes('tháng') && !String(cycleText).includes('năm')) {
                    cycleText = `${cycleText} tháng/lần`;
                }
                document.getElementById('tcPaymentCycle').innerText = cycleText + ` (Hạn trả: ${data.payment_limit_days || 5} ngày)`;
            }
            if (document.getElementById('tcBillDate')) document.getElementById('tcBillDate').innerText = data.bill_date ? `Hạn Mùng ${data.bill_date} hàng tháng` : 'Hạn Mùng 10 hàng tháng';
            if (document.getElementById('tcPenaltyBadge')) document.getElementById('tcPenaltyBadge').innerText = `Phạt trễ hạn: ${data.late_penalty_fee || '20.000 VNĐ / ngày'}`;

            // Hóa đơn tháng đầu tiên
            const initInvCode = data.initial_invoice_code || ('INV-' + cCode);
            if (document.getElementById('tcInvoiceCodeBadge')) {
                document.getElementById('tcInvoiceCodeBadge').innerText = 'Mã HĐ: ' + initInvCode;
            }

            // Footer summary
            if (document.getElementById('tcFooterSummary')) {
                document.getElementById('tcFooterSummary').innerText = `Tiền cọc: ${depositFormatted} • Giá thuê: ${rentPriceFormatted}/tháng`;
            }

            document.getElementById('tenantContractModal').classList.remove('hidden');
        }

        function closeTenantContractModal() {
            document.getElementById('tenantContractModal').classList.add('hidden');
        }

        // Logic nhấn nút "Đóng tiền cọc đầu tháng" từ modal Hợp đồng -> Nhảy ra Hóa đơn đóng tiền đầu tháng
        function payInitialInvoiceFromContract() {
            if (!currentViewingContractId) {
                showToast('Chưa chọn hợp đồng!');
                return;
            }
            closeTenantContractModal();
            setTimeout(() => {
                openInvoiceDetailModal(currentViewingContractId);
            }, 100);
        }

        // Đóng modal khi bấm ra ngoài vùng giấy
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('tenantContractModal');
            if (modal && e.target === modal) {
                closeTenantContractModal();
            }
        });

        

        let currentActiveInvoiceCode = null;
        let currentActiveInvoiceData = null;

        function openInvoiceDetailModal(houseKey) {
            const data = invoiceDataMap[houseKey];
            if (!data) return;

            currentActiveInvoiceCode = data.code;
            currentActiveInvoiceData = data;

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

            // Xử lý hiển thị trạng thái đã thanh toán / chờ duyệt / chưa thanh toán
            const bannerHeader = document.getElementById('detailBannerHeader');
            const statusBadge = document.getElementById('detailStatusBadge');
            const paidAlert = document.getElementById('detailPaidAlert');
            const paidAlertText = document.getElementById('detailPaidAlertText');
            const pendingApprovalAlert = document.getElementById('detailPendingApprovalAlert');
            const proofContainer = document.getElementById('detailProofContainer');
            const proofLink = document.getElementById('detailProofLink');
            const footerRow = document.getElementById('detailFooterRow');
            const totalLabel = document.getElementById('detailTotalLabel');
            const totalAmount = document.getElementById('detailTotalAmount');
            const btnVietQR = document.getElementById('btnTriggerVietQR');
            const btnReportTransfer = document.getElementById('btnTriggerReportTransfer');
            const transferPendingMsg = document.getElementById('transferPendingMessage');
            const staffNameElem = document.getElementById('detailStaffName');
            const staffPhoneElem = document.getElementById('detailStaffPhone');
            const btnCallStaff = document.getElementById('btnCallStaffCash');

            // Cập nhật thông tin nhân sự phụ trách thu tiền mặt (YÊU CẦU 3)
            const staffDisplayName = data.staffName || data.landlordName || 'Nhân sự phụ trách';
            const staffDisplayPhone = data.staffPhone || data.landlordPhone || '';
            if (staffNameElem) staffNameElem.innerText = staffDisplayName;
            if (staffPhoneElem) staffPhoneElem.innerText = staffDisplayPhone || 'Chưa cập nhật';
            if (btnCallStaff) {
                btnCallStaff.href = staffDisplayPhone ? ('tel:' + staffDisplayPhone) : '#';
                btnCallStaff.innerHTML = `<i class="fa-solid fa-phone"></i> Gọi Nhân Sự Thu Tiền (${staffDisplayName} - ${staffDisplayPhone})`;
            }

            if (data.isPaid) {
                // TRƯỜNG HỢP: ĐÃ THANH TOÁN (PAID)
                if (bannerHeader) bannerHeader.className = 'relative bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-700 p-4 sm:p-5 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3';
                if (statusBadge) {
                    statusBadge.className = 'bg-white text-emerald-700 px-4 py-2 rounded-xl shadow-lg border-2 border-emerald-300 text-center font-black tracking-wider uppercase text-sm flex items-center gap-1.5';
                    statusBadge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> ĐÃ THANH TOÁN';
                }
                if (paidAlert) {
                    paidAlert.classList.remove('hidden');
                    if (paidAlertText) paidAlertText.innerHTML = `Hóa đơn này đã được xác nhận thanh toán thành công vào ngày <strong>${data.paymentDate || 'gần đây'}</strong>.`;
                }
                if (pendingApprovalAlert) pendingApprovalAlert.classList.add('hidden');
                if (footerRow) footerRow.className = 'bg-emerald-50/90 border-t-2 border-emerald-300 font-extrabold';
                if (totalLabel) totalLabel.innerHTML = '<i class="fa-solid fa-calculator text-emerald-600"></i> TỔNG CỘNG ĐÃ THANH TOÁN:';
                if (totalAmount) totalAmount.className = 'py-3 px-3.5 text-right text-lg sm:text-xl font-black text-emerald-700';
                if (btnVietQR) btnVietQR.innerHTML = '<i class="fa-solid fa-receipt text-sm"></i> Xem lại thông tin mã VietQR chuyển khoản';
                if (btnReportTransfer) btnReportTransfer.classList.add('hidden');
                if (transferPendingMsg) transferPendingMsg.classList.add('hidden');
            } else if (data.isPendingApproval) {
                // TRƯỜNG HỢP: ĐANG CHỜ DUYỆT CK (PENDING_APPROVAL - YÊU CẦU 2)
                if (bannerHeader) bannerHeader.className = 'relative bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 p-4 sm:p-5 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3';
                if (statusBadge) {
                    statusBadge.className = 'bg-white text-amber-700 px-4 py-2 rounded-xl shadow-lg border-2 border-amber-300 text-center font-black tracking-wider uppercase text-sm animate-pulse flex items-center gap-1.5';
                    statusBadge.innerHTML = '<i class="fa-solid fa-clock-rotate-left text-amber-600"></i> ĐANG CHỜ DUYỆT CK';
                }
                if (paidAlert) paidAlert.classList.add('hidden');
                if (pendingApprovalAlert) pendingApprovalAlert.classList.remove('hidden');
                
                // Hiển thị nút xem biên lai nếu đã upload
                if (proofContainer && proofLink) {
                    if (data.proofImage) {
                        proofContainer.classList.remove('hidden');
                        proofLink.href = data.proofImage;
                    } else {
                        proofContainer.classList.add('hidden');
                    }
                }

                if (footerRow) footerRow.className = 'bg-amber-50/90 border-t-2 border-amber-300 font-extrabold';
                if (totalLabel) totalLabel.innerHTML = '<i class="fa-solid fa-calculator text-amber-600"></i> TỔNG CỘNG ĐANG CHỜ DUYỆT:';
                if (totalAmount) totalAmount.className = 'py-3 px-3.5 text-right text-lg sm:text-xl font-black text-amber-600';
                if (btnVietQR) btnVietQR.innerHTML = '<i class="fa-solid fa-qrcode text-sm"></i> Xem lại mã VietQR chuyển khoản';
                
                // Ẩn nút "Báo Đã Chuyển Khoản" và hiện dòng thông báo chờ kế toán duyệt (YÊU CẦU 2)
                if (btnReportTransfer) btnReportTransfer.classList.add('hidden');
                if (transferPendingMsg) transferPendingMsg.classList.remove('hidden');
            } else {
                // TRƯỜNG HỢP: CHƯA THANH TOÁN (UNPAID / PENDING / OVERDUE)
                if (bannerHeader) bannerHeader.className = 'relative bg-gradient-to-r from-rose-500 via-rose-600 to-amber-600 p-4 sm:p-5 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3';
                if (statusBadge) {
                    statusBadge.className = 'bg-white text-rose-600 px-4 py-2 rounded-xl shadow-lg border-2 border-rose-200 text-center font-black tracking-wider uppercase text-sm animate-pulse flex items-center gap-1.5';
                    statusBadge.innerHTML = '<i class="fa-solid fa-circle-exclamation text-rose-600"></i> CHƯA THANH TOÁN';
                }
                if (paidAlert) paidAlert.classList.add('hidden');
                if (pendingApprovalAlert) pendingApprovalAlert.classList.add('hidden');
                if (footerRow) footerRow.className = 'bg-rose-50/90 border-t-2 border-rose-300 font-extrabold';
                if (totalLabel) totalLabel.innerHTML = '<i class="fa-solid fa-calculator text-rose-600"></i> TỔNG CỘNG CẦN THANH TOÁN:';
                if (totalAmount) totalAmount.className = 'py-3 px-3.5 text-right text-lg sm:text-xl font-black text-rose-600';
                if (btnVietQR) btnVietQR.innerHTML = '<i class="fa-solid fa-qrcode text-sm"></i> Quét mã VietQR chuyển khoản ngay';

                // Hiển thị nút "Báo Đã Chuyển Khoản" và ẩn thông báo chờ duyệt
                if (btnReportTransfer) btnReportTransfer.classList.remove('hidden');
                if (transferPendingMsg) transferPendingMsg.classList.add('hidden');
            }

            const detailCustomQrWrapper = document.getElementById('detailCustomQrWrapper');
            const detailCustomQrImg = document.getElementById('detailCustomQrImg');
            if (detailCustomQrWrapper && detailCustomQrImg) {
                if (data.customQr) {
                    detailCustomQrImg.src = data.customQr;
                    detailCustomQrWrapper.classList.remove('hidden');
                } else {
                    detailCustomQrWrapper.classList.add('hidden');
                }
            }

            document.getElementById('btnTriggerVietQR').onclick = function() {
                closeInvoiceDetailModal();
                openPayQRModal(data.code, data.totalRaw, data.landlordName, data.bankAccount, data.bankShort, data.houseName, data.customQr);
            };

            document.getElementById('invoiceDetailModal').classList.remove('hidden');
        }

        function closeInvoiceDetailModal() {
            document.getElementById('invoiceDetailModal').classList.add('hidden');
        }

        // =========================================================================
        // LOGIC CHO MODAL BÁO ĐÃ CHUYỂN KHOẢN (YÊU CẦU 1)
        // =========================================================================
        function openReportTransferModal(code) {
            const invCode = code || currentActiveInvoiceCode;
            if (!invCode) {
                showToast('Chưa chọn hóa đơn cần báo chuyển khoản!');
                return;
            }

            const data = invoiceDataMap[invCode] || currentActiveInvoiceData;
            if (!data) {
                showToast('Không tìm thấy thông tin hóa đơn!');
                return;
            }

            currentActiveInvoiceCode = invCode;

            // Điền thông tin vào form
            document.getElementById('reportInvoiceCode').value = invCode;
            document.getElementById('reportCodeDisplay').innerText = invCode;
            document.getElementById('reportAmountDisplay').innerText = data.total || '0 VNĐ';
            document.getElementById('reportBankDisplay').innerText = `${data.bankShort || 'Ngân hàng'} - ${data.bankAccount || ''} (${data.bankOwner || ''})`;

            // Reset upload và ghi chú
            removeProofImage();
            const noteInput = document.getElementById('reportNote');
            if (noteInput) noteInput.value = '';

            // Đóng modal chi tiết nếu đang mở
            closeInvoiceDetailModal();

            // Mở modal báo chuyển khoản
            document.getElementById('reportTransferModal').classList.remove('hidden');
        }

        function closeReportTransferModal() {
            document.getElementById('reportTransferModal').classList.add('hidden');
        }

        // Preview ảnh biên lai khi người dùng chọn file
        function previewProofImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ảnh quá lớn',
                        text: 'Vui lòng chọn ảnh dung lượng dưới 5MB để tải lên nhanh hơn!',
                        confirmButtonColor: '#f59e0b'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('proofPreviewImage').src = e.target.result;
                    document.getElementById('dropzonePrompt').classList.add('hidden');
                    document.getElementById('proofPreviewContainer').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Xóa ảnh đã chọn
        function removeProofImage() {
            const input = document.getElementById('proofImageInput');
            if (input) input.value = '';
            const previewImg = document.getElementById('proofPreviewImage');
            if (previewImg) previewImg.src = '#';
            const previewContainer = document.getElementById('proofPreviewContainer');
            if (previewContainer) previewContainer.classList.add('hidden');
            const promptBox = document.getElementById('dropzonePrompt');
            if (promptBox) promptBox.classList.remove('hidden');
        }

        // Xử lý submit Form Báo Đã Chuyển Khoản
        function submitReportTransfer(e) {
            e.preventDefault();
            const invCode = document.getElementById('reportInvoiceCode').value;
            if (!invCode) {
                showToast('Thiếu mã hóa đơn!');
                return;
            }

            const btnSubmit = document.getElementById('btnSubmitReportTransfer');
            const originalBtnHtml = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi báo cáo...';

            const formData = new FormData();
            const proofInput = document.getElementById('proofImageInput');
            if (proofInput && proofInput.files[0]) {
                formData.append('proof_image', proofInput.files[0]);
            }
            const noteInput = document.getElementById('reportNote');
            if (noteInput && noteInput.value.trim()) {
                formData.append('note', noteInput.value.trim());
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch('/api/invoices/' + encodeURIComponent(invCode) + '/report-transfer', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(resData => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;

                if (resData.success) {
                    closeReportTransferModal();

                    // Cập nhật trạng thái trong invoiceDataMap ngay lập tức
                    if (invoiceDataMap[invCode]) {
                        invoiceDataMap[invCode].isPendingApproval = true;
                        invoiceDataMap[invCode].status = 'pending_approval';
                        if (resData.proof_image) {
                            invoiceDataMap[invCode].proofImage = resData.proof_image;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Đã Báo Chuyển Khoản Thành Công!',
                        html: `
                            <div class="text-left space-y-2 text-xs sm:text-sm text-slate-700">
                                <p>Hệ thống đã ghi nhận thông báo chuyển khoản của bạn cho hóa đơn <strong>${invCode}</strong>.</p>
                                <div class="p-3 bg-amber-50 rounded-xl border border-amber-300 text-amber-900 font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-hourglass-half text-amber-600 text-base"></i>
                                    <span>Trạng thái: Đang Chờ Duyệt CK</span>
                                </div>
                                <p class="text-slate-500 text-xs">Vui lòng chờ Kế toán đối soát và gạch nợ hoàn tất nhé!</p>
                            </div>
                        `,
                        confirmButtonText: 'Đã hiểu',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Không thể gửi báo cáo',
                        text: resData.message || 'Có lỗi xảy ra, vui lòng thử lại sau!',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể kết nối tới máy chủ. Vui lòng kiểm tra lại mạng!',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        // Xử lý khi khách bấm "Tôi đã hoàn tất chuyển khoản" trên VietQR Modal
        function handleQRCompleted() {
            closePayQRModal();
            const code = document.getElementById('qrContentText').innerText.trim();
            // Mở ngay modal upload biên lai / báo chuyển khoản
            openReportTransferModal(code);
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
        // Switch Tabs inside Form 2
        function switchTab(tabName) {
            const tabs = ['all', 'pending', 'history', 'liabilities'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                const content = document.getElementById('content-' + t);
                if (btn && content) {
                    if (t === tabName) {
                        btn.classList.add('active');
                        btn.classList.remove('text-slate-600');
                        content.classList.remove('hidden');
                    } else {
                        btn.classList.remove('active');
                        btn.classList.add('text-slate-600');
                        content.classList.add('hidden');
                    }
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
        function openReturnModal(contractId, houseName, roomCode, landlord, deposit) {
            document.getElementById('returnContractId').value = contractId || '';
            document.getElementById('returnHouseName').value = houseName + ' (Mã: ' + roomCode + ')';
            document.getElementById('returnLandlord').value = landlord;
            document.getElementById('returnDeposit').value = deposit;
            
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 15);
            document.getElementById('returnDate').value = tomorrow.toISOString().split('T')[0];

            if (document.getElementById('returnBankName')) document.getElementById('returnBankName').value = '';
            if (document.getElementById('returnBankNameCustom')) {
                document.getElementById('returnBankNameCustom').value = '';
                document.getElementById('returnBankNameCustom').classList.add('hidden');
                document.getElementById('returnBankNameCustom').required = false;
            }

            document.getElementById('returnModal').classList.remove('hidden');
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
        }

        function handleReturnReasonChange(selectEl) {
            const customInput = document.getElementById('returnReasonCustom');
            if (selectEl.value === 'khac') {
                customInput.classList.remove('hidden');
                customInput.required = true;
                customInput.focus();
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
            }
        }

        function handleReturnBankChange(selectEl) {
            const customInput = document.getElementById('returnBankNameCustom');
            if (!customInput) return;
            if (selectEl.value === 'khac') {
                customInput.classList.remove('hidden');
                customInput.required = true;
                customInput.focus();
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
                customInput.value = '';
            }
        }

        async function handleReturnSubmit(e) {
            e.preventDefault();
            const contractId = document.getElementById('returnContractId').value;
            if (!contractId) {
                showToast('Không xác định được hợp đồng cần trả phòng!', 'error');
                return;
            }

            const expectedDate = document.getElementById('returnDate').value;
            if (!expectedDate) {
                showToast('Vui lòng chọn ngày dự kiến bàn giao trả phòng!', 'warning');
                return;
            }

            const reasonSelect = document.getElementById('returnReasonSelect').value;
            const reasonCustom = document.getElementById('returnReasonCustom').value.trim();
            const reason = (reasonSelect === 'khac' ? reasonCustom : reasonSelect) || 'Khách thuê yêu cầu trả phòng.';

            const bankAccount = document.getElementById('returnBankAccount').value.trim();
            const bankSelect = document.getElementById('returnBankName').value.trim();
            const bankCustom = document.getElementById('returnBankNameCustom') ? document.getElementById('returnBankNameCustom').value.trim() : '';
            const bankName = (bankSelect === 'khac' ? bankCustom : bankSelect);
            const bankHolder = document.getElementById('returnBankHolder').value.trim();
            const note = document.getElementById('returnNote').value.trim();

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi yêu cầu...';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch(`/api/contracts/${contractId}/request-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        expected_checkout_date: expectedDate,
                        reason: reason + (note ? (' - Ghi chú: ' + note) : ''),
                        bank_account: bankAccount,
                        bank_name: bankName,
                        bank_holder: bankHolder
                    })
                });

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Gửi yêu cầu không thành công');
                }

                closeReturnModal();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã gửi yêu cầu trả phòng!',
                        text: data.message || 'Yêu cầu của bạn đã được chuyển tới Ban Quản lý và Nhân sự vận hành.',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'Đã hiểu'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    showToast(data.message || 'Đã gửi yêu cầu trả nhà thành công! Nhân sự sẽ liên hệ đối soát.', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch (err) {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Lỗi', err.message, 'error');
                } else {
                    showToast(err.message, 'error');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        // Logic thu hồi (hủy) yêu cầu trả phòng của khách thuê
        function cancelCheckoutRequest(contractId, roomCode) {
            const confirmAction = () => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch(`/api/contracts/${contractId}/cancel-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã thu hồi yêu cầu!',
                                text: data.message || 'Hợp đồng của bạn tiếp tục duy trì hiệu lực bình thường.',
                                confirmButtonColor: '#059669',
                                confirmButtonText: 'Đã hiểu'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            showToast(data.message || 'Đã thu hồi yêu cầu trả phòng thành công!');
                            setTimeout(() => window.location.reload(), 1200);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Không thể thu hồi!',
                                text: data.message || 'Không thể thu hồi yêu cầu trả phòng.',
                                confirmButtonColor: '#e11d48',
                                confirmButtonText: 'Đã hiểu'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            showToast(data.message || 'Lỗi khi thu hồi!', 'error');
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Đã có lỗi xảy ra khi gửi yêu cầu!', 'error');
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Thu Hồi Yêu Cầu Trả Phòng?',
                    text: `Bạn có chắc chắn muốn hủy yêu cầu trả phòng cho hợp đồng [${roomCode}]? Hợp đồng thuê sẽ tiếp tục có hiệu lực bình thường.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-rotate-left mr-1"></i> Xác nhận thu hồi',
                    cancelButtonText: 'Đóng',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl p-6',
                        confirmButton: 'px-4 py-2.5 rounded-xl font-bold text-xs',
                        cancelButton: 'px-4 py-2.5 rounded-xl font-bold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmAction();
                    }
                });
            } else {
                if (confirm(`Bạn có chắc muốn thu hồi yêu cầu trả phòng cho hợp đồng [${roomCode}]?`)) {
                    confirmAction();
                }
            }
        }

        function getVietQRBankCode(bankName) {
            if (!bankName) return 'vietinbank';
            const clean = bankName.toLowerCase().replace(/[^a-z0-9]/g, '');
            if (clean.includes('vietin') || clean.includes('icb') || clean.includes('ctg')) return 'vietinbank';
            if (clean.includes('vietcom') || clean.includes('vcb')) return 'vietcombank';
            if (clean.includes('bidv')) return 'bidv';
            if (clean.includes('mb') || clean.includes('quan-doi')) return 'mbbank';
            if (clean.includes('techcom') || clean.includes('tcb')) return 'techcombank';
            if (clean.includes('agri') || clean.includes('vba')) return 'agribank';
            if (clean.includes('acb')) return 'acb';
            if (clean.includes('vp') || clean.includes('vpb')) return 'vpbank';
            if (clean.includes('tp') || clean.includes('tpb')) return 'tpbank';
            if (clean.includes('sacom') || clean.includes('stb')) return 'sacombank';
            if (clean.includes('hd') || clean.includes('hdb')) return 'hdbank';
            if (clean.includes('vib')) return 'vib';
            if (clean.includes('shb')) return 'shb';
            if (clean.includes('sea') || clean.includes('ssb')) return 'seabank';
            if (clean.includes('msb')) return 'msb';
            if (clean.includes('ocb')) return 'ocb';
            if (clean.includes('lp') || clean.includes('lienviet')) return 'lpbank';
            if (clean.includes('exim') || clean.includes('eib')) return 'eximbank';
            if (clean.includes('baca') || clean.includes('bab')) return 'bacabank';
            if (clean.includes('nama') || clean.includes('nab')) return 'namabank';
            if (clean.includes('pvcom') || clean.includes('pvcb')) return 'pvcombank';
            if (clean.includes('saigon') || clean.includes('sgb')) return 'saigonbank';
            if (clean.includes('baoviet') || clean.includes('bvb')) return 'baovietbank';
            if (clean.includes('bvbank') || clean.includes('banviet')) return 'bvbank';
            if (clean.includes('kienlong') || clean.includes('klb')) return 'kienlongbank';
            if (clean.includes('pgbank') || clean.includes('pgb')) return 'pgbank';
            if (clean.includes('vietbank') || clean.includes('vbb')) return 'vietbank';
            return clean || 'vietinbank';
        }

        // Pay QR Modal Logic
        function openPayQRModal(code, amount, landlordName, bankAccount, bankName, houseName, customQr) {
            document.getElementById('qrContentText').innerText = code;
            document.getElementById('qrAmountText').innerText = amount + ' VNĐ';
            if (landlordName) document.getElementById('qrLandlordNameText').innerText = landlordName;
            if (bankAccount) document.getElementById('qrBankAccountText').innerText = bankAccount;
            if (bankName) document.getElementById('qrBankNameText').innerText = bankName;
            if (houseName) document.getElementById('qrHouseNameText').innerText = houseName;

            const cleanAmt = parseInt(String(amount).replace(/[^0-9]/g, '')) || 0;
            const bName = bankName || 'MB Bank';
            const bCode = getVietQRBankCode(bName);
            const bAcc = (bankAccount || '0912987654888').replace(/\s+/g, '');
            const bHolder = encodeURIComponent(landlordName || 'ADMIN RENTHOME');
            const bMemo = encodeURIComponent(code);

            const vietQrUrl = `https://img.vietqr.io/image/${bCode}-${bAcc}-compact2.png?amount=${cleanAmt}&addInfo=${bMemo}&accountName=${bHolder}`;
            const qrImage = document.getElementById('qrImage');
            if (qrImage) {
                qrImage.src = vietQrUrl;
                qrImage.onerror = function() {
                    this.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + bMemo;
                };
            }

            document.getElementById('payQRModal').classList.remove('hidden');
        }

        function closePayQRModal() {
            document.getElementById('payQRModal').classList.add('hidden');
        }

        function confirmPaidSuccess() {
            const code = document.getElementById('qrContentText').innerText.trim();
            closePayQRModal();
            showToast('Đang ghi nhận xác nhận thanh toán...');
            fetch('/api/invoices/' + encodeURIComponent(code) + '/pay', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message || 'Thanh toán thành công! Trạng thái hóa đơn đã được cập nhật.');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(err => {
                showToast('Hệ thống đã ghi nhận thông báo chuyển khoản của bạn!');
            });
        }

        // Xem Biên bản quyết toán thanh lý (Popup SweetAlert2)
        function openSettlementReceipt(contractId, houseTitle, roomCode, details) {
            let d = {};
            try {
                if (details && typeof details === 'object' && details.getAttribute && details.getAttribute('data-details')) {
                    d = JSON.parse(details.getAttribute('data-details'));
                } else if (typeof details === 'string') {
                    d = JSON.parse(details);
                } else if (details && typeof details === 'object') {
                    d = details;
                }
            } catch (e) {
                console.error("Error parsing liquidation details", e);
                d = {};
            }
            const formatVnd = (num) => new Intl.NumberFormat('vi-VN').format(Math.round(num || 0)) + ' đ';

            const checkoutDate = d.checkout_date ? new Date(d.checkout_date).toLocaleDateString('vi-VN') : 'Đã bàn giao';
            const settledDate = d.settled_at ? new Date(d.settled_at).toLocaleDateString('vi-VN') : checkoutDate;
            const settledByName = d.settled_by_name || 'Ban Quản Lý';

            const stayDays = Number(d.stay_days || 0);
            const rentPrice = Number(d.rent_price || 0);
            const proratedRent = Number(d.prorated_rent || 0);
            const damageFee = Number(d.damage_fee || 0);
            const damageReason = d.damage_reason || '';
            const elecAmount = Number(d.electricity_amount || 0);
            const waterAmount = Number(d.water_amount || 0);
            const fixedServices = Number(d.fixed_services_amount || 0);
            const depositAmount = Number(d.deposit_amount || 0);
            const netAmount = Number(d.net_amount || 0);
            const isNoServiceFee = Boolean(d.is_no_service_fee);

            // Tính toán tổng nợ
            const totalDebt = Number(d.total_debt || (proratedRent + elecAmount + waterAmount + fixedServices + damageFee));

            // Xác định kịch bản
            const scenario = d.scenario || (netAmount > 0 ? 'A' : (netAmount < 0 ? 'B' : 'C'));

            // Tiền phòng trả trước hoàn lại (unused rent refund)
            let unusedRentRefund = Number(d.unused_rent_refund || 0);
            let unusedDays = Number(d.unused_days || 0);
            if (!unusedRentRefund && scenario === 'B') {
                const inferredRefund = (Math.abs(netAmount) + totalDebt) - depositAmount;
                if (inferredRefund > 0) {
                    unusedRentRefund = Math.round(inferredRefund);
                }
            }
            if (!unusedDays && unusedRentRefund > 0 && stayDays > 0 && stayDays < 30) {
                unusedDays = Math.max(0, 30 - stayDays);
            }

            // Tổng quyền lợi của khách (Tiền cọc + Hoàn tiền phòng trả trước chưa ở)
            const totalCredits = depositAmount + unusedRentRefund;

            // Dòng điện nước
            let utilityRowsHtml = '';
            if (isNoServiceFee) {
                utilityRowsHtml = `
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">⚡ Điện & 💧 Nước:</span>
                            <div style="font-size: 11px; color: #059669; margin-top: 1px;">🌿 Khách tự thanh toán cho Nhà nước theo thỏa thuận</div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #0f172a; vertical-align: top;">0 đ</td>
                    </tr>
                `;
            } else {
                const elecUsageText = d.electricity_usage !== undefined ? d.electricity_usage + ' kWh' : ((d.electricity_new || 0) - (d.electricity_old || 0)) + ' kWh';
                const waterUsageText = d.water_calc_type === 'nguoi' 
                    ? (d.water_usage || 1) + ' người'
                    : (d.water_usage !== undefined ? d.water_usage + ' m³' : 'Khối tiêu thụ');

                utilityRowsHtml = `
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">⚡ Điện chốt cuối:</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Chỉ số: ${d.electricity_old || 0} ➔ ${d.electricity_new || 0} (${elecUsageText})</div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #0f172a; vertical-align: top;">${formatVnd(elecAmount)}</td>
                    </tr>
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">💧 Nước chốt cuối:</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Định mức/tiêu thụ: ${waterUsageText}</div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #0f172a; vertical-align: top;">${formatVnd(waterAmount)}</td>
                    </tr>
                `;
            }

            // Dịch vụ cố định (nếu có)
            let fixedServicesRow = '';
            if (fixedServices > 0) {
                fixedServicesRow = `
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">🌐 Dịch vụ tiện ích:</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">Internet, rác, vệ sinh tòa nhà theo ngày ở</div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #0f172a; vertical-align: top;">${formatVnd(fixedServices)}</td>
                    </tr>
                `;
            }

            // Bồi thường hư hại
            let damageRow = `
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                        <span style="font-weight: 600; color: #1e293b;">🔨 Bồi thường hư hại / Phạt:</span>
                        ${damageFee > 0 && damageReason ? `<div style="font-size: 11px; color: #e11d48; margin-top: 1px;">Lý do: ${damageReason}</div>` : ''}
                    </td>
                    <td style="padding: 7px 0; text-align: right; font-weight: 700; color: ${damageFee > 0 ? '#e11d48' : '#0f172a'}; vertical-align: top;">${formatVnd(damageFee)}</td>
                </tr>
            `;

            // Tiền phòng tháng cuối
            let rentRow = '';
            if (proratedRent === 0) {
                rentRow = `
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">🏠 Tiền phòng tháng cuối (${stayDays} ngày ở):</span>
                            <div style="font-size: 11px; color: #16a34a; font-weight: 600; margin-top: 1px;">
                                <i class="fa-solid fa-circle-check"></i> Đã đóng đủ trọn chu kỳ trước đó (Không nợ tiền phòng)
                            </div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #16a34a; vertical-align: top;">0 đ</td>
                    </tr>
                `;
            } else {
                rentRow = `
                    <tr style="border-bottom: 1px dashed #e2e8f0;">
                        <td style="padding: 7px 0; color: #64748b; vertical-align: top;">
                            <span style="font-weight: 600; color: #1e293b;">🏠 Tiền phòng tính theo ngày ở (${stayDays} ngày):</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">${formatVnd(rentPrice)} / 30 ngày x ${stayDays} ngày ở</div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 700; color: #0f172a; vertical-align: top;">${formatVnd(proratedRent)}</td>
                    </tr>
                `;
            }

            // Dòng hoàn tiền phòng trả trước chưa ở (nếu có)
            let unusedRentRow = '';
            if (unusedRentRefund > 0) {
                unusedRentRow = `
                    <tr style="border-bottom: 1px dashed #bbf7d0;">
                        <td style="padding: 7px 0; color: #166534; vertical-align: top;">
                            <span style="font-weight: 700; color: #15803d;">💰 Hoàn lại tiền phòng đóng trước chưa ở:</span>
                            <div style="font-size: 11px; color: #166534; margin-top: 1px;">
                                <i class="fa-solid fa-circle-check"></i> Hoàn trả ${unusedDays > 0 ? unusedDays + ' ngày thừa' : 'thời gian còn lại'} do dời đi trước hạn chu kỳ
                            </div>
                        </td>
                        <td style="padding: 7px 0; text-align: right; font-weight: 800; color: #15803d; vertical-align: top;">+${formatVnd(unusedRentRefund)}</td>
                    </tr>
                `;
            }

            // Hộp kết quả cuối cùng
            let resultBoxHtml = '';
            if (scenario === 'B') {
                resultBoxHtml = `
                    <div style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; padding: 14px 18px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; font-weight: 800; opacity: 0.95; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-circle-check mr-1"></i> KẾT QUẢ QUYẾT TOÁN THỰC NHẬN
                            </div>
                            <div style="font-size: 13px; font-weight: 700; margin-top: 2px;">CÔNG TY ĐÃ HOÀN TRẢ CHO BẠN:</div>
                        </div>
                        <div style="font-size: 22px; font-weight: 900; letter-spacing: -0.5px; text-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                            +${formatVnd(Math.abs(netAmount))}
                        </div>
                    </div>
                `;
            } else if (scenario === 'A') {
                resultBoxHtml = `
                    <div style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); color: #ffffff; padding: 14px 18px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.25);">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; font-weight: 800; opacity: 0.95; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> KẾT QUẢ QUYẾT TOÁN CÔNG NỢ
                            </div>
                            <div style="font-size: 13px; font-weight: 700; margin-top: 2px;">KHÁCH HÀNG CẦN THANH TOÁN THÊM:</div>
                        </div>
                        <div style="font-size: 22px; font-weight: 900; letter-spacing: -0.5px;">
                            ${formatVnd(Math.abs(netAmount))}
                        </div>
                    </div>
                `;
            } else {
                resultBoxHtml = `
                    <div style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); color: #ffffff; padding: 14px 18px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; font-weight: 800; opacity: 0.95;">
                                <i class="fa-solid fa-scale-balanced mr-1"></i> KẾT QUẢ ĐỐI SOÁT
                            </div>
                            <div style="font-size: 13px; font-weight: 700; margin-top: 2px;">HÒA CÔNG NỢ (TIỀN CỌC VỪA ĐỦ BÙ TRỪ):</div>
                        </div>
                        <div style="font-size: 22px; font-weight: 900;">0 đ</div>
                    </div>
                `;
            }

            // Box thông tin ngân hàng nhận tiền hoàn
            let bankInfoHtml = '';
            if (d.bank_account) {
                bankInfoHtml = `
                    <div style="margin-top: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #334155; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-building-columns text-sky-600" style="font-size: 16px;"></i>
                            <div>
                                <span style="color: #64748b; font-size: 11px; display: block;">Tài khoản nhận tiền hoàn của khách:</span>
                                <strong>STK: ${d.bank_account}</strong> - <strong>${d.bank_name || 'Ngân hàng'}</strong> (Chủ TK: <strong>${d.bank_holder || ''}</strong>)
                            </div>
                        </div>
                        <span style="padding: 2px 8px; border-radius: 6px; background: #ecfdf5; color: #059669; font-weight: 700; font-size: 11px;">
                            <i class="fa-solid fa-check"></i> Đã ghi nhận
                        </span>
                    </div>
                `;
            }

            // Ghi chú của BQL
            let noteHtml = '';
            if (d.note) {
                noteHtml = `
                    <div style="margin-top: 10px; font-size: 11.5px; color: #92400e; background: #fefce8; border: 1px solid #fef08a; border-radius: 8px; padding: 8px 12px;">
                        <strong>Ghi chú Ban Quản Lý:</strong> ${d.note}
                    </div>
                `;
            }

            const html = `
                <div style="width: 100%; min-width: min(850px, 100%); text-align: left; font-size: 13px; line-height: 1.5; color: #334155;">
                    <!-- Thông tin căn nhà / hợp đồng -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                            <div style="font-weight: 800; font-size: 15px; color: #0f172a;">${houseTitle}</div>
                            <span style="padding: 3px 10px; border-radius: 20px; background: #ecfdf5; color: #059669; font-weight: 700; font-size: 11.5px; border: 1px solid #a7f3d0;">
                                <i class="fa-solid fa-circle-check"></i> Đã bàn giao (Phòng trống)
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 12px;">
                            <span>Mã HĐ: <strong style="color: #0f172a;">${roomCode}</strong></span>
                            <span>Ngày bàn giao: <strong style="color: #0f172a;">${checkoutDate}</strong></span>
                            <span>Người lập: <strong style="color: #0f172a;">${settledByName}</strong></span>
                        </div>
                    </div>

                    <!-- LƯỚI 2 CỘT CHIỀU NGANG RỘNG (RESPONSIVE 2 COLUMNS) -->
                    <div class="settlement-grid-2cols" style="margin-bottom: 14px;">
                        
                        <!-- PHẦN 1: CÁC KHOẢN CHI PHÍ & NỢ PHÁT SINH THÁNG CUỐI -->
                        <div style="background: #ffffff; border: 1px solid #fed7aa; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ffedd5; padding-bottom: 8px; margin-bottom: 8px;">
                                    <span style="font-weight: 800; font-size: 13px; color: #9a3412; text-transform: uppercase;">
                                        <i class="fa-solid fa-file-invoice-dollar mr-1"></i> 1. Chi phí & Nợ phát sinh
                                    </span>
                                    <span style="font-size: 11px; color: #ea580c; font-weight: 700; background: #fff7ed; padding: 2px 8px; border-radius: 6px;">(Khách phải trả)</span>
                                </div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                    <tbody>
                                        ${rentRow}
                                        ${utilityRowsHtml}
                                        ${fixedServicesRow}
                                        ${damageRow}
                                    </tbody>
                                </table>
                            </div>
                            <div style="background: #fff7ed; border-radius: 10px; padding: 10px 12px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 800; color: #9a3412; font-size: 12.5px;">TỔNG NỢ PHÁT SINH (A):</span>
                                <span style="font-weight: 900; color: #ea580c; font-size: 15px;">${formatVnd(totalDebt)}</span>
                            </div>
                        </div>

                        <!-- PHẦN 2: CÁC KHOẢN CÔNG TY HOÀN TRẢ & CẤN TRỪ -->
                        <div style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dcfce7; padding-bottom: 8px; margin-bottom: 8px;">
                                    <span style="font-weight: 800; font-size: 13px; color: #166534; text-transform: uppercase;">
                                        <i class="fa-solid fa-shield-halved mr-1"></i> 2. Tiền cọc & Quyền lợi hoàn lại
                                    </span>
                                    <span style="font-size: 11px; color: #16a34a; font-weight: 700; background: #f0fdf4; padding: 2px 8px; border-radius: 6px;">(Khách được nhận)</span>
                                </div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                    <tbody>
                                        <tr style="border-bottom: 1px dashed #bbf7d0;">
                                            <td style="padding: 7px 0; color: #166534; vertical-align: top;">
                                                <span style="font-weight: 700; color: #15803d;">🛡️ Tiền cọc bảo đảm hợp đồng:</span>
                                                <div style="font-size: 11px; color: #166534; margin-top: 1px;">Tiền cọc ban đầu dùng để bù trừ công nợ & hoàn trả</div>
                                            </td>
                                            <td style="padding: 7px 0; text-align: right; font-weight: 800; color: #15803d; vertical-align: top; white-space: nowrap;">+${formatVnd(depositAmount)}</td>
                                        </tr>
                                        ${unusedRentRow}
                                    </tbody>
                                </table>
                            </div>
                            <div style="background: #f0fdf4; border-radius: 10px; padding: 10px 12px; margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 800; color: #166534; font-size: 12.5px;">TỔNG TIỀN CỌC & HOÀN LẠI (B):</span>
                                <span style="font-weight: 900; color: #16a34a; font-size: 15px;">+${formatVnd(totalCredits)}</span>
                            </div>
                        </div>

                    </div>

                    <!-- PHẦN 3: ĐỐI SOÁT & KẾT QUẢ QUYẾT TOÁN CUỐI CÙNG -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; margin-bottom: 12px; font-size: 12.5px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <span style="color: #64748b; font-weight: 600;">Phép tính đối soát công nợ cuối cùng:</span>
                        <span style="font-weight: 700; color: #0f172a;">
                            [Khoản được hoàn (B): <span style="color: #16a34a;">+${formatVnd(totalCredits)}</span>] &nbsp;—&nbsp; [Tổng nợ (A): <span style="color: #ea580c;">${formatVnd(totalDebt)}</span>]
                        </span>
                    </div>

                    ${resultBoxHtml}
                    ${bankInfoHtml}
                    ${noteHtml}
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<span style="font-size: 20px; font-weight: 800; color: #0f172a;"><i class="fa-solid fa-receipt text-sky-600 mr-2"></i> Biên Bản Quyết Toán Thanh Lý Hợp Đồng</span>',
                    html: html,
                    width: '920px',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Đã Hiểu & Đóng Biên Bản',
                    confirmButtonColor: '#0284c7',
                    customClass: {
                        popup: 'settlement-receipt-modal rounded-3xl shadow-2xl text-slate-800',
                        confirmButton: 'px-7 py-3 rounded-xl font-bold text-sm shadow-md transition-all hover:scale-105 active:scale-95'
                    }
                });
            } else {
                showToast('Biên bản quyết toán phòng ' + roomCode + ': nợ ' + formatVnd(totalDebt) + ', hoàn trả ' + formatVnd(Math.abs(netAmount)), 'info');
            }
        }

        // Xóa 1 hợp đồng khỏi lịch sử thuê của khách
        function deleteContractHistory(contractId, contractCode) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Xóa lịch sử thuê?',
                    text: `Bạn có chắc muốn xóa hợp đồng [${contractCode}] khỏi danh sách lịch sử thuê của bạn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-regular fa-trash-can mr-1"></i> Xóa lịch sử',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl p-6',
                        confirmButton: 'px-4 py-2.5 rounded-xl font-bold text-xs',
                        cancelButton: 'px-4 py-2.5 rounded-xl font-bold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDeleteContractHistory(contractId);
                    }
                });
            } else {
                if (confirm(`Bạn có chắc muốn xóa hợp đồng [${contractCode}] khỏi lịch sử thuê?`)) {
                    doDeleteContractHistory(contractId);
                }
            }
        }

        function doDeleteContractHistory(contractId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || '{{ csrf_token() }}';

            fetch('/api/tenant/contract-history/' + encodeURIComponent(contractId), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById('terminated-contract-card-' + contractId);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.remove();
                            const remainingCards = document.querySelectorAll('[id^="terminated-contract-card-"]');
                            if (remainingCards.length === 0) {
                                const section = document.getElementById('terminated-contracts-section');
                                if (section) section.remove();
                            } else {
                                const countEl = document.getElementById('terminated-contracts-count');
                                if (countEl) countEl.innerText = `(${remainingCards.length})`;
                            }
                        }, 300);
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: data.message || 'Đã xóa hợp đồng khỏi lịch sử thành công.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        showToast(data.message || 'Đã xóa thành công!');
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Lỗi', data.message || 'Không thể xóa lịch sử.', 'error');
                    } else {
                        alert(data.message || 'Lỗi khi xóa!');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Lỗi', 'Đã có lỗi xảy ra khi kết nối máy chủ.', 'error');
                } else {
                    alert('Lỗi kết nối máy chủ!');
                }
            });
        }

        // Xóa tất cả hợp đồng đã thanh lý khỏi lịch sử
        function clearAllContractHistory() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Xóa toàn bộ lịch sử?',
                    text: 'Bạn có chắc muốn xóa tất cả các hợp đồng đã thanh lý khỏi lịch sử thuê của bạn?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-regular fa-trash-can mr-1"></i> Xóa tất cả',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'rounded-3xl shadow-2xl p-6',
                        confirmButton: 'px-4 py-2.5 rounded-xl font-bold text-xs',
                        cancelButton: 'px-4 py-2.5 rounded-xl font-bold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        doClearAllContractHistory();
                    }
                });
            } else {
                if (confirm('Bạn có chắc muốn xóa tất cả các hợp đồng đã thanh lý khỏi lịch sử thuê?')) {
                    doClearAllContractHistory();
                }
            }
        }

        function doClearAllContractHistory() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || '{{ csrf_token() }}';

            fetch('/api/tenant/contract-history', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const section = document.getElementById('terminated-contracts-section');
                    if (section) {
                        section.style.transition = 'all 0.3s ease';
                        section.style.opacity = '0';
                        setTimeout(() => section.remove(), 300);
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Đã xóa tất cả!',
                            text: data.message || 'Đã xóa toàn bộ lịch sử thuê thành công.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Lỗi', data.message || 'Không thể xóa.', 'error');
                    } else {
                        alert(data.message || 'Lỗi!');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Lỗi', 'Đã có lỗi xảy ra khi kết nối máy chủ.', 'error');
                }
            });
        }

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('toast');
            if (toast) {
                document.getElementById('toastMessage').innerText = msg;
                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 5000);
            }
        }

        // Realtime Polling: Tự động loại bỏ hợp đồng đã hủy / thanh lý mà không cần load lại trang
        async function checkTenantActiveContractsRealtime() {
            try {
                const res = await fetch('/api/tenant/active-contracts', { cache: 'no-store' });
                if (!res.ok) return;
                const data = await res.json();
                if (data.success && Array.isArray(data.active_ids)) {
                    const cards = document.querySelectorAll('.contract-card');
                    let removedAny = false;
                    cards.forEach(card => {
                        const cId = card.getAttribute('data-contract-id');
                        const cCode = card.getAttribute('data-contract-code');
                        const isActive = (cId && data.active_ids.includes(cId)) || 
                                         (cCode && data.active_codes && data.active_codes.includes(cCode));
                        if (!isActive) {
                            removedAny = true;
                            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.96) translateY(-12px)';
                            setTimeout(() => {
                                card.remove();
                                const remaining = document.querySelectorAll('.contract-card');
                                if (remaining.length === 0) {
                                    const container = document.getElementById('rented-houses-container');
                                    if (container) {
                                        container.innerHTML = `
                                            <div id="no-rented-houses-placeholder" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 20px; border: 1px dashed #cbd5e1;">
                                                <i class="fa-solid fa-house-crack" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                                                <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Bạn chưa thuê nhà/phòng nào</p>
                                            </div>
                                        `;
                                    }
                                }
                            }, 600);
                        }
                    });
                    if (removedAny) {
                        showToast('Hợp đồng đã hoàn tất thanh lý/hủy. Danh sách phòng đã được tự động cập nhật!');
                        // Sau 2.5 giây tự động reload nhẹ để cập nhật số đếm hóa đơn & mục lịch sử
                        setTimeout(() => window.location.reload(), 2500);
                    }
                }
            } catch (e) {
                console.debug('Realtime tenant contracts check warning:', e);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            setInterval(checkTenantActiveContractsRealtime, 6000);
        });
    </script>
</body>
</html>
