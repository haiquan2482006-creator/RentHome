<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hồ Sơ Quản Trị Viên - RentHome Admin</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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
<body class="bg-slate-100 text-slate-900 antialiased min-h-screen">

    <!-- INCLUDE SIDEBAR & HEADER ADMIN -->
    @include('partials.header-admin')

    <!-- PHẦN NỘI DUNG CHÍNH (MAIN CONTENT) -->
    <main class="admin-main">
        <div class="p-4 sm:p-6 lg:p-8 max-w-[1700px] mx-auto w-full space-y-6">

            <!-- ════════════════════════════════════════════════════════════
                 THÔNG BÁO FLASH MESSAGE
                 ════════════════════════════════════════════════════════════ -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xl"></i>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-1 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-sm text-rose-700 mb-1">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>Có lỗi xảy ra, vui lòng kiểm tra lại:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-2">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ════════════════════════════════════════════════════════════
                 BANNER HỒ SƠ ADMIN & AVATAR
                 ════════════════════════════════════════════════════════════ -->
            <div class="bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
                <!-- Họa tiết nền mờ -->
                <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-10 -top-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-center justify-between gap-6">
                    <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
                        <!-- Khối Avatar Admin -->
                        <div class="relative group cursor-pointer shrink-0" onclick="document.getElementById('adminAvatarInput').click()">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-4 border-white/20 shadow-2xl bg-slate-800 flex items-center justify-center relative">
                                <img id="adminAvatarPreview" 
                                     src="{{ $user->display_avatar }}" 
                                     alt="Admin Avatar" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-950/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fa-solid fa-camera text-white text-xl mb-1"></i>
                                    <span class="text-[10px] uppercase font-extrabold tracking-wider text-sky-300">Đổi ảnh</span>
                                </div>
                            </div>
                            <button type="button" 
                                    class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-sky-500 hover:bg-sky-400 text-white flex items-center justify-center shadow-lg border-2 border-slate-900 transition-transform active:scale-90" 
                                    title="Tải ảnh mới">
                                <i class="fa-solid fa-camera text-xs"></i>
                            </button>
                        </div>

                        <!-- Thông tin Admin -->
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                    {{ $user->account_name ?: ($user->name ?: 'Ban Quản Trị RentHome') }}
                                </h1>
                                <span class="px-3 py-1 rounded-full bg-gradient-to-r from-sky-500 to-blue-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-sm flex items-center gap-1.5 border border-sky-400/30">
                                    <i class="fa-solid fa-shield-halved text-amber-300"></i>
                                    <span>Super Admin</span>
                                </span>
                            </div>

                            <p class="text-slate-300 text-sm font-medium flex flex-wrap items-center justify-center sm:justify-start gap-4">
                                @if(!empty($user->username))
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-user text-sky-400"></i>
                                    <span>{{ $user->username }}</span>
                                </span>
                                @endif
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope text-sky-400"></i>
                                    <span>{{ $user->email }}</span>
                                </span>
                                @if(!empty($user->phone))
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-sky-400"></i>
                                    <span>{{ $user->phone }}</span>
                                </span>
                                @endif
                            </p>

                            <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    <span>Đặc quyền Quản trị cấp cao nhất</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-lg bg-sky-500/20 text-sky-300 text-xs font-bold border border-sky-500/30 flex items-center gap-1.5">
                                    <i class="fa-solid fa-qrcode text-sky-400"></i>
                                    <span>VietQR Thụ hưởng: {{ $user->bank_name ?: 'Chưa cấu hình' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Nút thao tác nhanh -->
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-globe"></i>
                            <span>Xem Website</span>
                        </a>
                        <a href="{{ url('Admin') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition-all flex items-center gap-2 shadow-md">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Bảng Tổng Quan</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════════════════════════
                 THỐNG KÊ NHANH ĐẶC QUYỀN ADMIN (4 METRIC CARDS)
                 ════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- 1. Tòa nhà -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-city"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tòa nhà quản trị</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalBuildings ?? 0) }}</h4>
                    </div>
                </div>

                <!-- 2. Phòng / Tin đăng -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phòng & Tin đăng</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalRooms ?? 0) }}</h4>
                    </div>
                </div>

                <!-- 3. Nhân sự -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nhân sự & Phân quyền</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalSubAccounts ?? 0) }}</h4>
                    </div>
                </div>

                <!-- 4. Hợp đồng -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hợp đồng vận hành</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalContracts ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════════════════════════
                 BỐ CỤC CHÍNH (2 CỘT): FORM THÔNG TIN (7/12) & BẢO MẬT/NHẬT KÝ (5/12)
                 ════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- ========================================================
                     CỘT TRÁI: FORM CẬP NHẬT HỒ SƠ QUẢN TRỊ VIÊN (7/12)
                     ======================================================== -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-8">

                    <!-- Header khối Form -->
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg font-bold shadow-inner">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">Thông Tin Hồ Sơ & Doanh Nghiệp</h2>
                                <p class="text-xs text-slate-500">Cập nhật thông tin định danh Admin và tài khoản thụ hưởng nhận tiền hệ thống</p>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                            Hồ sơ chính
                        </span>
                    </div>

                    <form id="adminProfileForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Input File Ẩn Cho Avatar -->
                        <input type="file" id="adminAvatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAdminAvatar(this)">

                        <!-- ───────────────────────────────────────────────
                             MỤC 1: ĐỊNH DANH QUẢN TRỊ VIÊN
                             ─────────────────────────────────────────────── -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                <i class="fa-solid fa-user-shield text-sky-600"></i>
                                <span>1. Thông Tin Định Danh Quản Trị Viên</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Họ tên hiển thị -->
                                <div class="sm:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Họ và Tên Quản Trị Viên <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="account_name" 
                                               value="{{ old('account_name', $user->account_name ?: $user->name) }}" 
                                               required
                                               placeholder="Nhập họ và tên..."
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-user text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Địa Chỉ Email Quản Trị <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required
                                               placeholder="admin@example.com"
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-envelope text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Số điện thoại -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Số Điện Thoại Liên Hệ
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="phone" 
                                               value="{{ old('phone', $user->phone) }}" 
                                               placeholder="0912 345 678"
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-phone text-sm"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ───────────────────────────────────────────────
                             MỤC 2: DOANH NGHIỆP & TRỤ SỞ VẬN HÀNH
                             ─────────────────────────────────────────────── -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                <i class="fa-solid fa-building text-sky-600"></i>
                                <span>2. Thông Tin Doanh Nghiệp & Trụ Sở Điều Hành</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Tên công ty / Doanh nghiệp -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Tên Đơn Vị / Công Ty Quản Trị
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="company_name" 
                                               value="{{ old('company_name', $user->company_name ?: 'Hệ Thống Bất Động Sản RentHome Việt Nam') }}" 
                                               placeholder="Tên công ty hoặc doanh nghiệp..."
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-building-flag text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mã số thuế -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Mã Số Thuế (MST)
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="tax_code" 
                                               value="{{ old('tax_code', $user->tax_code) }}" 
                                               placeholder="VD: 0109988776"
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-receipt text-sm"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Địa chỉ trụ sở -->
                                <div class="sm:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Địa Chỉ Trụ Sở Văn Phòng
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="address" 
                                               value="{{ old('address', $user->address ?: 'Hà Nội, Việt Nam') }}" 
                                               placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố..."
                                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all pl-10">
                                        <div class="absolute left-3.5 top-3 text-slate-400">
                                            <i class="fa-solid fa-location-dot text-sm"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ───────────────────────────────────────────────
                             MỤC 3: TÀI KHOẢN NGÂN HÀNG THỤ HƯỞNG (VIETQR)
                             ─────────────────────────────────────────────── -->
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                    <i class="fa-solid fa-qrcode text-sky-600"></i>
                                    <span>3. Tài Khoản Ngân Hàng Thụ Hưởng Doanh Nghiệp (VietQR)</span>
                                </div>
                                <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    NAPAS 247 Realtime
                                </span>
                            </div>

                            <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-100 flex flex-col md:flex-row items-center gap-6">
                                <!-- Khung xem trước mã VietQR -->
                                <div class="flex flex-col items-center gap-2 shrink-0">
                                    <div class="w-36 h-36 bg-white rounded-2xl p-2 border border-slate-200 shadow-sm flex items-center justify-center overflow-hidden">
                                        <img id="liveVietQrImage" 
                                             src="https://img.vietqr.io/image/{{ $bankCode }}-{{ preg_replace('/\s+/', '', $user->bank_account ?: '0912987654888') }}-compact2.png?accountName={{ urlencode($user->bank_owner ?: 'CHU NHA') }}&addInfo=RENTHOME" 
                                             alt="Mã VietQR Thụ Hưởng" 
                                             class="w-full h-full object-contain">
                                    </div>
                                    <span class="text-[10px] font-extrabold text-slate-500 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Quét chuyển khoản trực tiếp
                                    </span>
                                </div>

                                <!-- Form điền thông tin tài khoản ngân hàng -->
                                <div class="flex-1 w-full space-y-3">
                                    <!-- Ngân hàng thụ hưởng dạng danh sách chọn tất cả ngân hàng -->
                                    <div class="space-y-1 relative" id="admin_bank_dropdown_container">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-building-columns text-sky-600"></i>
                                                <span>Ngân Hàng Thụ Hưởng</span>
                                            </span>
                                            <span class="text-[11px] text-sky-600 font-bold hover:text-sky-700 cursor-pointer flex items-center gap-1" onclick="toggleAdminBankDropdown(event)">
                                                <i class="fa-solid fa-list-ul"></i> Chọn từ danh sách
                                            </span>
                                        </label>

                                        <!-- Input ẩn lưu giá trị submit form -->
                                        <input type="hidden" 
                                               id="admin_bank_name"
                                               name="bank_name" 
                                               value="{{ old('bank_name', $user->bank_name ?: 'MB Bank (Ngân hàng Quân Đội)') }}">

                                        <!-- Nút bấm hiển thị ngân hàng đang chọn -->
                                        <button type="button" 
                                                id="btn_admin_select_bank"
                                                onclick="toggleAdminBankDropdown(event)"
                                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 hover:border-sky-500 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all text-left flex items-center justify-between group shadow-xs">
                                            <div class="flex items-center gap-3 overflow-hidden min-w-0">
                                                <div id="admin_selected_bank_logo_box" class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                                    <img id="admin_selected_bank_logo" src="https://api.vietqr.io/img/MB.png" alt="Logo" class="w-full h-full object-contain" onerror="this.src='https://api.vietqr.io/img/MB.png'">
                                                </div>
                                                <div class="flex flex-col min-w-0">
                                                    <span id="admin_selected_bank_title" class="font-bold text-slate-800 truncate text-sm">
                                                        {{ old('bank_name', $user->bank_name ?: 'MB Bank (Ngân hàng Quân Đội)') }}
                                                    </span>
                                                    <span id="admin_selected_bank_sub" class="text-[11px] text-slate-400 truncate">
                                                        Bấm để chọn từ danh sách tất cả ngân hàng
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-slate-400 group-hover:text-sky-600 shrink-0 ml-2">
                                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="admin_bank_chevron"></i>
                                            </div>
                                        </button>

                                        <!-- Dropdown Menu danh sách ngân hàng -->
                                        <div id="admin_bank_dropdown_list" 
                                             class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-2xl border border-slate-200 max-h-80 overflow-hidden z-50 flex flex-col animate-fade-in">
                                            
                                            <!-- Ô tìm kiếm ngân hàng -->
                                            <div class="p-2.5 border-b border-slate-100 bg-slate-50 sticky top-0 z-10 flex items-center gap-2">
                                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs ml-1"></i>
                                                <input type="text" 
                                                       id="admin_bank_search_input" 
                                                       placeholder="Tìm tên ngân hàng (VD: MB, Vietcombank, BIDV, Techcombank, ACB...)" 
                                                       oninput="filterAdminBanks(this.value)"
                                                       class="w-full bg-white px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                <button type="button" onclick="clearAdminBankSearch()" class="text-slate-400 hover:text-slate-600 text-xs px-1.5">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>

                                            <!-- Danh sách ngân hàng cuộn dọc -->
                                            <div id="admin_bank_items_container" class="overflow-y-auto max-h-64 divide-y divide-slate-50 custom-scrollbar p-1">
                                                <div class="p-4 text-center text-xs text-slate-400">
                                                    <i class="fa-solid fa-spinner fa-spin mr-1.5 text-sky-500"></i> Đang nạp danh sách ngân hàng...
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Số tài khoản (Chỉ cho phép điền số) -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                            <span>Số Tài Khoản Ngân Hàng</span>
                                            <span class="text-[10px] text-slate-400 font-semibold normal-case"><i class="fa-solid fa-lock text-[9px] mr-1"></i>Chỉ nhập số</span>
                                        </label>
                                        <input type="text" 
                                               id="admin_bank_account"
                                               name="bank_account" 
                                               inputmode="numeric"
                                               value="{{ old('bank_account', $user->bank_account ?: '0912 987 654 888') }}" 
                                               oninput="handleBankAccountInput(this)"
                                               placeholder="VD: 0912 987 654 888"
                                               class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all font-mono">
                                    </div>

                                    <!-- Tên chủ tài khoản (Không dấu & Không ký tự đặc biệt) -->
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                            <span>Chủ Tài Khoản</span>
                                            <span class="text-[10px] text-amber-600 font-semibold normal-case"><i class="fa-solid fa-shield-halved text-[9px] mr-1"></i>Không dấu & ký tự đặc biệt</span>
                                        </label>
                                        <input type="text" 
                                               id="admin_bank_owner"
                                               name="bank_owner" 
                                               value="{{ old('bank_owner', $user->bank_owner ?: 'NGUYEN VAN A') }}" 
                                               oninput="handleBankOwnerInput(this)"
                                               placeholder="VD: NGUYEN VAN A"
                                               class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all uppercase">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút lưu thay đổi hồ sơ -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="submit" 
                                    class="px-8 py-3 rounded-2xl bg-sky-600 hover:bg-sky-500 text-white font-extrabold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Lưu Thay Đổi Hồ Sơ</span>
                            </button>
                        </div>
                    </form>
                </div>


                <!-- ========================================================
                     CỘT PHẢI: BẢO MẬT, ĐỔI MẬT KHẨU & NHẬT KÝ ADMIN (5/12)
                     ======================================================== -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- KHỐI 1: BẢO MẬT & ĐỔI MẬT KHẨU ADMIN -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900">Bảo Mật & Mật Khẩu</h3>
                                    <p class="text-xs text-slate-500">Đổi mật khẩu tài khoản Quản trị viên</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Cấp cao
                            </span>
                        </div>

                        <form id="adminPasswordForm" action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Mật khẩu hiện tại -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mật Khẩu Hiện Tại <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="admin_current_password" 
                                           name="current_password" 
                                           required 
                                           placeholder="Nhập mật khẩu đang dùng..."
                                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all pl-10 pr-10">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                    <button type="button" onclick="togglePassVisibility('admin_current_password', this)" class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 p-1">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Mật khẩu mới -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mật Khẩu Mới (Tối thiểu 6 ký tự) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="admin_new_password" 
                                           name="new_password" 
                                           required 
                                           minlength="6"
                                           placeholder="Nhập mật khẩu mới..."
                                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all pl-10 pr-10">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-shield-halved text-sm"></i>
                                    </div>
                                    <button type="button" onclick="togglePassVisibility('admin_new_password', this)" class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 p-1">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Xác nhận mật khẩu mới -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Xác Nhận Mật Khẩu Mới <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="admin_new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           required 
                                           placeholder="Nhập lại mật khẩu mới..."
                                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all pl-10 pr-10">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-check-double text-sm"></i>
                                    </div>
                                    <button type="button" onclick="togglePassVisibility('admin_new_password_confirmation', this)" class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 p-1">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full mt-2 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                                <i class="fa-solid fa-shield-check"></i>
                                <span>Cập Nhật Mật Khẩu</span>
                            </button>
                        </form>
                    </div>

                    <!-- KHỐI 2: NHẬT KÝ HOẠT ĐỘNG GẦN ĐÂY CỦA ADMIN -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                                <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                                <span>Nhật Ký Thao Tác Gần Đây</span>
                            </div>
                            <a href="{{ url('lich_su') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                                <span>Xem tất cả</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <div class="space-y-3 custom-scrollbar max-h-80 overflow-y-auto pr-1">
                            @forelse($recentActivities ?? [] as $act)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition-all space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-extrabold text-slate-800 line-clamp-1">
                                            {{ $act->title ?? ($act->action_type ?? 'Thao tác hệ thống') }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                            {{ $act->created_at ? date('H:i d/m', strtotime($act->created_at)) : 'Vừa xong' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $act->description ?? 'Quản trị viên đã thực hiện cập nhật dữ liệu.' }}
                                    </p>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 text-xs font-medium">
                                    <i class="fa-regular fa-clock text-2xl mb-1 block opacity-50"></i>
                                    Chưa có nhật ký hoạt động nào gần đây.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- ════════════════════════════════════════════════════════════
         JAVASCRIPT XỬ LÝ GIAO DIỆN HỒ SƠ ADMIN & DANH SÁCH NGÂN HÀNG
         ════════════════════════════════════════════════════════════ -->
    <script>
        // Danh sách hơn 40 Ngân hàng thương mại & Quốc tế tại Việt Nam (Chuẩn NAPAS 24/7)
        const DEFAULT_BANKS = [
            { code: "MB", shortName: "MBBank", name: "Ngân hàng Quân Đội", bin: "970422", logo: "https://api.vietqr.io/img/MB.png" },
            { code: "VCB", shortName: "Vietcombank", name: "Ngân hàng Ngoại Thương Việt Nam", bin: "970436", logo: "https://api.vietqr.io/img/VCB.png" },
            { code: "CTG", shortName: "VietinBank", name: "Ngân hàng Công Thương Việt Nam", bin: "970415", logo: "https://api.vietqr.io/img/ICB.png" },
            { code: "BIDV", shortName: "BIDV", name: "Ngân hàng Đầu tư & Phát triển Việt Nam", bin: "970418", logo: "https://api.vietqr.io/img/BIDV.png" },
            { code: "TCB", shortName: "Techcombank", name: "Ngân hàng Kỹ Thương Việt Nam", bin: "970407", logo: "https://api.vietqr.io/img/TCB.png" },
            { code: "VPB", shortName: "VPBank", name: "Ngân hàng Việt Nam Thịnh Vượng", bin: "970432", logo: "https://api.vietqr.io/img/VPB.png" },
            { code: "ACB", shortName: "ACB", name: "Ngân hàng Á Châu", bin: "970416", logo: "https://api.vietqr.io/img/ACB.png" },
            { code: "VBA", shortName: "Agribank", name: "Ngân hàng Nông nghiệp & PT Nông thôn VN", bin: "970405", logo: "https://api.vietqr.io/img/VBA.png" },
            { code: "TPB", shortName: "TPBank", name: "Ngân hàng Tiên Phong", bin: "970423", logo: "https://api.vietqr.io/img/TPB.png" },
            { code: "VIB", shortName: "VIB", name: "Ngân hàng Quốc tế Việt Nam", bin: "970441", logo: "https://api.vietqr.io/img/VIB.png" },
            { code: "HDB", shortName: "HDBank", name: "Ngân hàng Phát triển TP.HCM", bin: "970437", logo: "https://api.vietqr.io/img/HDB.png" },
            { code: "STB", shortName: "Sacombank", name: "Ngân hàng Sài Gòn Thương Tín", bin: "970403", logo: "https://api.vietqr.io/img/STB.png" },
            { code: "SHB", shortName: "SHB", name: "Ngân hàng Sài Gòn - Hà Nội", bin: "970443", logo: "https://api.vietqr.io/img/SHB.png" },
            { code: "MSB", shortName: "MSB", name: "Ngân hàng Hàng Hải Việt Nam", bin: "970426", logo: "https://api.vietqr.io/img/MSB.png" },
            { code: "OCB", shortName: "OCB", name: "Ngân hàng Phương Đông", bin: "970448", logo: "https://api.vietqr.io/img/OCB.png" },
            { code: "SEAB", shortName: "SeABank", name: "Ngân hàng Đông Nam Á", bin: "970440", logo: "https://api.vietqr.io/img/SEAB.png" },
            { code: "LPB", shortName: "LPBank", name: "Ngân hàng Lộc Phát Việt Nam", bin: "970449", logo: "https://api.vietqr.io/img/LPB.png" },
            { code: "NAB", shortName: "Nam A Bank", name: "Ngân hàng Nam Á", bin: "970428", logo: "https://api.vietqr.io/img/NAB.png" },
            { code: "BAB", shortName: "Bac A Bank", name: "Ngân hàng Bắc Á", bin: "970409", logo: "https://api.vietqr.io/img/BAB.png" },
            { code: "PVC", shortName: "PVcomBank", name: "Ngân hàng Đại Chúng Việt Nam", bin: "970412", logo: "https://api.vietqr.io/img/PVC.png" },
            { code: "VBB", shortName: "VietBank", name: "Ngân hàng Việt Nam Thương Tín", bin: "970433", logo: "https://api.vietqr.io/img/VBB.png" },
            { code: "KLB", shortName: "Kienlongbank", name: "Ngân hàng Kiên Long", bin: "970452", logo: "https://api.vietqr.io/img/KLB.png" },
            { code: "SGB", shortName: "Saigonbank", name: "Ngân hàng Sài Gòn Công Thương", bin: "970400", logo: "https://api.vietqr.io/img/SGB.png" },
            { code: "BVB", shortName: "BVBank", name: "Ngân hàng Bản Việt", bin: "970454", logo: "https://api.vietqr.io/img/BVB.png" },
            { code: "PGB", shortName: "PGBank", name: "Ngân hàng Thịnh vượng & Phát triển", bin: "970430", logo: "https://api.vietqr.io/img/PGB.png" },
            { code: "BVBK", shortName: "BaoViet Bank", name: "Ngân hàng Bảo Việt", bin: "970438", logo: "https://api.vietqr.io/img/BVBK.png" },
            { code: "VAB", shortName: "VietABank", name: "Ngân hàng Việt Á", bin: "970427", logo: "https://api.vietqr.io/img/VAB.png" },
            { code: "SHBVN", shortName: "Shinhan Bank", name: "Ngân hàng TNHH MTV Shinhan Việt Nam", bin: "970424", logo: "https://api.vietqr.io/img/SHBVN.png" },
            { code: "WOO", shortName: "Woori Bank", name: "Ngân hàng TNHH MTV Woori Việt Nam", bin: "970457", logo: "https://api.vietqr.io/img/WOO.png" },
            { code: "PBVN", shortName: "Public Bank", name: "Ngân hàng Public Bank Việt Nam", bin: "970439", logo: "https://api.vietqr.io/img/PBVN.png" },
            { code: "HSBC", shortName: "HSBC", name: "Ngân hàng TNHH MTV HSBC Việt Nam", bin: "458761", logo: "https://api.vietqr.io/img/HSBC.png" },
            { code: "SCVN", shortName: "Standard Chartered", name: "Ngân hàng Standard Chartered VN", bin: "970410", logo: "https://api.vietqr.io/img/SCVN.png" },
            { code: "UOB", shortName: "UOB", name: "Ngân hàng United Overseas Bank VN", bin: "970458", logo: "https://api.vietqr.io/img/UOB.png" },
            { code: "CIMB", shortName: "CIMB", name: "Ngân hàng TNHH MTV CIMB Việt Nam", bin: "422589", logo: "https://api.vietqr.io/img/CIMB.png" },
            { code: "IVB", shortName: "Indovina Bank", name: "Ngân hàng TNHH Indovina", bin: "970434", logo: "https://api.vietqr.io/img/IVB.png" },
            { code: "VRB", shortName: "VRB", name: "Ngân hàng Liên doanh Việt - Nga", bin: "970421", logo: "https://api.vietqr.io/img/VRB.png" }
        ];

        let allBanksList = [...DEFAULT_BANKS];

        // 1. Khởi tạo danh sách ngân hàng & kết nối API VietQR
        function initBankList() {
            renderBankItems(allBanksList);
            matchCurrentBankLogo();

            // Gọi API VietQR để cập nhật danh sách và logo ngân hàng mới nhất
            fetch('https://api.vietqr.io/v2/banks')
                .then(res => res.json())
                .then(resData => {
                    if (resData && resData.code === '00' && Array.isArray(resData.data) && resData.data.length > 0) {
                        allBanksList = resData.data;
                        renderBankItems(allBanksList);
                        matchCurrentBankLogo();
                    }
                })
                .catch(err => {
                    console.log('Using default local banks list.');
                });
        }

        // 2. Render các phần tử ngân hàng vào dropdown
        function renderBankItems(banks) {
            const container = document.getElementById('admin_bank_items_container');
            if (!container) return;

            if (!banks || banks.length === 0) {
                container.innerHTML = `
                    <div class="p-4 text-center text-xs text-slate-400">
                        <i class="fa-solid fa-circle-exclamation mr-1 text-amber-500"></i>
                        Không tìm thấy ngân hàng nào phù hợp.
                    </div>
                `;
                return;
            }

            const currentVal = (document.getElementById('admin_bank_name')?.value || '').toLowerCase();

            let html = '';
            banks.forEach(b => {
                const sName = b.shortName || b.short_name || b.code || '';
                const fName = b.name || '';
                const logo = b.logo || `https://api.vietqr.io/img/${b.code || 'MB'}.png`;
                const isSelected = currentVal.includes(sName.toLowerCase()) || currentVal.includes(fName.toLowerCase());

                html += `
                    <button type="button" 
                            onclick="selectAdminBank('${sName}', '${fName}', '${logo}')" 
                            class="w-full text-left p-2.5 hover:bg-sky-50 transition-colors flex items-center justify-between gap-3 group/item ${isSelected ? 'bg-sky-50/70' : ''}">
                        <div class="flex items-center gap-3 overflow-hidden min-w-0">
                            <div class="w-10 h-7 rounded bg-white shadow-2xs border border-slate-200 p-0.5 flex items-center justify-center shrink-0">
                                <img src="${logo}" alt="${sName}" class="w-full h-full object-contain" onerror="this.src='https://api.vietqr.io/img/MB.png'">
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-extrabold text-xs text-slate-800 group-hover/item:text-sky-600 truncate">
                                    ${sName}
                                </span>
                                <span class="text-[11px] text-slate-400 truncate">
                                    ${fName}
                                </span>
                            </div>
                        </div>
                        ${isSelected ? '<i class="fa-solid fa-circle-check text-sky-600 text-xs shrink-0 mr-1"></i>' : ''}
                    </button>
                `;
            });

            container.innerHTML = html;
        }

        // 3. Tìm kiếm / lọc ngân hàng
        function filterAdminBanks(keyword) {
            const kw = (keyword || '').trim().toLowerCase();
            if (!kw) {
                renderBankItems(allBanksList);
                return;
            }

            const filtered = allBanksList.filter(b => {
                const sName = (b.shortName || b.short_name || b.code || '').toLowerCase();
                const fName = (b.name || '').toLowerCase();
                const code = (b.code || '').toLowerCase();
                return sName.includes(kw) || fName.includes(kw) || code.includes(kw);
            });

            renderBankItems(filtered);
        }

        function clearAdminBankSearch() {
            const input = document.getElementById('admin_bank_search_input');
            if (input) {
                input.value = '';
                renderBankItems(allBanksList);
                input.focus();
            }
        }

        // 4. Chọn ngân hàng từ danh sách
        function selectAdminBank(shortName, fullName, logo) {
            const hiddenInput = document.getElementById('admin_bank_name');
            const titleEl = document.getElementById('admin_selected_bank_title');
            const subEl = document.getElementById('admin_selected_bank_sub');
            const logoEl = document.getElementById('admin_selected_bank_logo');

            const formattedName = `${shortName} (${fullName})`;

            if (hiddenInput) hiddenInput.value = formattedName;
            if (titleEl) titleEl.innerText = formattedName;
            if (subEl) subEl.innerText = fullName;
            if (logoEl && logo) logoEl.src = logo;

            closeAdminBankDropdown();
            updateLiveVietQr();
        }

        // 5. Bật / tắt mở dropdown
        function toggleAdminBankDropdown(event) {
            if (event) event.stopPropagation();
            const list = document.getElementById('admin_bank_dropdown_list');
            const chevron = document.getElementById('admin_bank_chevron');
            if (!list) return;

            const isHidden = list.classList.contains('hidden');
            if (isHidden) {
                list.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
                const searchInput = document.getElementById('admin_bank_search_input');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 50);
                }
            } else {
                closeAdminBankDropdown();
            }
        }

        function closeAdminBankDropdown() {
            const list = document.getElementById('admin_bank_dropdown_list');
            const chevron = document.getElementById('admin_bank_chevron');
            if (list) list.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const container = document.getElementById('admin_bank_dropdown_container');
            if (container && !container.contains(e.target)) {
                closeAdminBankDropdown();
            }
        });

        // 6. Tự động tìm logo ngân hàng đang được chọn lúc ban đầu
        function matchCurrentBankLogo() {
            const currentVal = (document.getElementById('admin_bank_name')?.value || '').toLowerCase();
            if (!currentVal) return;

            const match = allBanksList.find(b => {
                const sName = (b.shortName || b.short_name || b.code || '').toLowerCase();
                const fName = (b.name || '').toLowerCase();
                return currentVal.includes(sName) || currentVal.includes(fName);
            });

            if (match && match.logo) {
                const logoEl = document.getElementById('admin_selected_bank_logo');
                if (logoEl) logoEl.src = match.logo;
            }
        }

        // 7. Xem trước ảnh Avatar khi Admin chọn ảnh mới
        function previewAdminAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('adminAvatarPreview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 8. Chuyển đổi tên ngân hàng sang mã slug VietQR
        function getBankSlug(bankName) {
            if (!bankName) return 'mbbank';
            const b = bankName.toLowerCase();
            if (b.includes('vietinbank') || b.includes('icb') || b.includes('công thương')) return 'vietinbank';
            if (b.includes('vietcombank') || b.includes('vcb') || b.includes('ngoại thương')) return 'vietcombank';
            if (b.includes('bidv') || b.includes('đầu tư')) return 'bidv';
            if (b.includes('agribank') || b.includes('nông nghiệp')) return 'agribank';
            if (b.includes('mbbank') || b.includes('quân đội') || b.includes('mb')) return 'mbbank';
            if (b.includes('techcombank') || b.includes('kỹ thương') || b.includes('tcb')) return 'techcombank';
            if (b.includes('vpbank') || b.includes('thịnh vượng')) return 'vpbank';
            if (b.includes('tpbank') || b.includes('tiên phong')) return 'tpbank';
            if (b.includes('acb') || b.includes('á châu')) return 'acb';
            if (b.includes('vib') || b.includes('quốc tế')) return 'vib';
            if (b.includes('hdbank') || b.includes('phát triển')) return 'hdbank';
            if (b.includes('shb') || b.includes('sài gòn hà nội')) return 'shb';
            if (b.includes('sacombank') || b.includes('sài gòn thương tín')) return 'sacombank';
            if (b.includes('msb') || b.includes('hàng hải')) return 'msb';
            if (b.includes('seabank') || b.includes('đông nam á')) return 'seabank';
            if (b.includes('ocb') || b.includes('phương đông')) return 'ocb';
            if (b.includes('bacabank') || b.includes('bắc á')) return 'bacabank';
            if (b.includes('vietbank')) return 'vietbank';
            if (b.includes('lpbank') || b.includes('lộc phát') || b.includes('lienviet')) return 'lpbank';
            if (b.includes('pvcombank')) return 'pvcombank';
            if (b.includes('nam a') || b.includes('nama')) return 'namabank';
            if (b.includes('kienlong')) return 'kienlongbank';
            if (b.includes('saigonbank')) return 'saigonbank';
            if (b.includes('baoviet')) return 'baovietbank';
            if (b.includes('pgbank')) return 'pgbank';
            if (b.includes('shinhan')) return 'shinhan';
            if (b.includes('woori')) return 'woori';
            return 'mbbank';
        }

        // 9. Cập nhật mã VietQR theo thời gian thực khi Admin gõ thông tin ngân hàng
        function updateLiveVietQr() {
            const bName = document.getElementById('admin_bank_name')?.value || 'MB Bank';
            const bAcc = (document.getElementById('admin_bank_account')?.value || '0912987654888').replace(/\s+/g, '');
            const bOwner = encodeURIComponent(document.getElementById('admin_bank_owner')?.value || 'CHU NHA');
            const bSlug = getBankSlug(bName);

            const qrUrl = `https://img.vietqr.io/image/${bSlug}-${bAcc}-compact2.png?accountName=${bOwner}&addInfo=RENTHOME`;
            const qrImg = document.getElementById('liveVietQrImage');
            if (qrImg) {
                qrImg.src = qrUrl;
            }
        }

        // 10. Chuyển đổi bỏ dấu tiếng Việt và loại bỏ toàn bộ ký tự đặc biệt
        function removeVietnameseTones(str) {
            if (!str) return '';
            str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/gi, 'a');
            str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/gi, 'e');
            str = str.replace(/ì|í|ị|ỉ|ĩ/gi, 'i');
            str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/gi, 'o');
            str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/gi, 'u');
            str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/gi, 'y');
            str = str.replace(/đ/gi, 'd');
            str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, 'A');
            str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, 'E');
            str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'I');
            str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, 'O');
            str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'U');
            str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, 'Y');
            str = str.replace(/Đ/g, 'D');
            // Loại bỏ hoàn toàn ký tự đặc biệt, chỉ giữ lại A-Z, 0-9 và khoảng trắng
            str = str.replace(/[^A-Za-z0-9\s]/g, '');
            return str.toUpperCase();
        }

        // 11. Xử lý ô Số tài khoản: không được điền chữ hay ký tự đặc biệt (chỉ nhập số)
        function handleBankAccountInput(input) {
            input.value = input.value.replace(/[^0-9\s]/g, '');
            updateLiveVietQr();
        }

        // 12. Xử lý ô Chủ tài khoản: không được điền dấu tiếng Việt và không chứa ký tự đặc biệt
        function handleBankOwnerInput(input) {
            input.value = removeVietnameseTones(input.value);
            updateLiveVietQr();
        }

        // 13. Bật / tắt ẩn hiện mật khẩu
        function togglePassVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        // Khởi động khi tải xong DOM
        document.addEventListener('DOMContentLoaded', function() {
            initBankList();
        });
    </script>
</body>
</html>

