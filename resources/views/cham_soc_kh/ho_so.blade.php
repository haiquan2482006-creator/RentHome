<!DOCTYPE html>
<html lang="vi">
<head>
    @include('partials.modern-notification')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hồ Sơ Cá Nhân - RentHome CSKH</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --sidebar-width: 260px;
            --primary: #0284c7;
            --primary-light: #e0f2fe;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --danger: #ef4444;
            --danger-light: #fee2e2;
        }

        body {
            font-family: var(--font-family);
            background-color: #f8fafc;
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }

        .brand-box, .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo, .logo-box {
            width: 44px;
            height: 44px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo img, .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-info h2, .logo-text h3 {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-info .role-badge, .logo-text h3 span {
            font-size: 11px;
            padding: 2px 6px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 6px;
            font-weight: 700;
        }

        .brand-info p, .logo-text p {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .nav-section-title {
            padding: 20px 24px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }

        .nav-menu {
            list-style: none;
            padding: 0 12px;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s;
        }

        .nav-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link-left i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .nav-link.active {
            color: white;
            background: var(--primary);
        }

        .user-footer {
            margin-top: auto;
            padding: 14px 16px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .user-footer-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            text-decoration: none;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }

        .user-meta {
            min-width: 0;
        }

        .user-meta h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta p {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            color: var(--text-muted);
            background: none;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: var(--danger-light);
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
            min-width: 0;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar" id="cskhSidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo">
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">CSKH</span></h2>
                <p>Trung tâm Chăm sóc khách hàng</p>
            </div>
        </div>

        <p class="nav-section-title">CHĂM SÓC KHÁCH HÀNG</p>
        <ul class="nav-menu">
            <li>
                <a href="{{ url('/cskh/tong-quan') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tổng quan</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Quản lý Khiếu nại</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.notifications') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.history') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lịch sử hoạt động</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.profile') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Hồ sơ cá nhân</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <a href="{{ route('cskh.profile') }}" class="user-footer-left hover:opacity-85 transition-opacity" title="👤 Hồ sơ cá nhân">
                <div class="avatar-circle" id="sidebarAvatarContainer">
                    @if(!empty($user->avatar))
                        <img src="{{ $user->display_avatar }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
                    @else
                        CSKH
                    @endif
                </div>
                <div class="user-meta">
                    <h4 id="sidebarUserName">{{ $user->display_name ?: ($user->account_name ?? 'Chăm Sóc Khách Hàng') }}</h4>
                    <p>{{ $user->email ?? 'chamsockh@gmail.com' }}</p>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('cskh.profile') }}" class="logout-btn text-blue-600 bg-blue-50" title="👤 Hồ sơ cá nhân">
                    <i class="fa-solid fa-user-pen text-xs"></i>
                </a>
                <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        
        <!-- TOPBAR / PAGE HEADER -->
        <div class="flex items-center justify-between flex-wrap gap-4 pb-6 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                    <a href="{{ url('/cskh/tong-quan') }}" class="hover:text-blue-600">CSKH</a>
                    <span>/</span>
                    <span class="text-slate-600">Hồ sơ cá nhân</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg shadow-2xs">
                        👤
                    </span>
                    <span>Hồ Sơ Cá Nhân Nhân Sự Nội Bộ</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Quản lý thông tin định danh, bí danh giao tiếp khách hàng và bảo mật mật khẩu tài khoản
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80 shadow-2xs">
                    <i class="fa-solid fa-shield-halved text-amber-600"></i>
                    <span>Audit Trail: Tên thật & Email khóa cứng</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>{{ $roleTitle ?? 'Chăm Sóc Khách Hàng (CSKH)' }}</span>
                </span>
            </div>
        </div>

        <!-- 2 KHỐI CHÍNH (CARD GRID) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">

            <!-- ════════════════════════════════════════════════════════════
                 KHỐI 1: THÔNG TIN CÁ NHÂN (7 CỘT)
                 ════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Thông Tin Cá Nhân & Bí Danh</h2>
                            <p class="text-xs text-slate-500">Định danh nhân sự nội bộ và tên hiển thị hỗ trợ khách hàng</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                        Cá nhân
                    </span>
                </div>

                <!-- Form Thông Tin Cá Nhân -->
                <form id="profileForm" action="{{ route('cskh.profile.update') }}" method="POST" enctype="multipart/form-data" onsubmit="handleProfileSubmit(event)">
                    @csrf

                    <!-- 1. PHẦN AVATAR -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70 mb-6">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('avatarInput').click()">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-white flex items-center justify-center">
                                <img id="avatarPreview" 
                                     src="{{ $user->display_avatar }}" 
                                     alt="Avatar" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="absolute inset-0 bg-slate-900/50 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fa-solid fa-camera text-xl mb-1"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Đổi ảnh</span>
                            </div>
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center justify-center sm:justify-start gap-2">
                                <span>Ảnh đại diện nhân sự</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Tròn</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Tải lên hình ảnh khuôn mặt rõ nét để tăng tính chuyên nghiệp khi giao tiếp và tương tác với khách thuê.
                            </p>
                            
                            <div class="mt-3 flex items-center justify-center sm:justify-start gap-2">
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                <button type="button" 
                                        onclick="document.getElementById('avatarInput').click()" 
                                        class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-arrow-up-from-bracket text-blue-600"></i>
                                    <span>Chọn ảnh mới</span>
                                </button>
                                <span class="text-[11px] text-slate-400">Hỗ trợ JPG, PNG, WEBP &lt; 3MB</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. CÁC TRƯỜNG DỮ LIỆU -->
                    <div class="space-y-4">
                        
                        <!-- Ô Input Tên thật (Họ và tên gốc) - KHÓA CỨNG -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-id-card text-slate-400"></i>
                                    <span>Họ và tên gốc (Tên thật):</span>
                                </span>
                                <span class="text-[11px] font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200/60">
                                    🔒 Khóa cứng (Audit Trail)
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       value="{{ $user->name ?: ($user->ho_ten ?: ($user->account_name ?: 'Nhân sự RentHome')) }}" 
                                       readonly 
                                       disabled 
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-500 font-semibold text-sm border border-slate-200 cursor-not-allowed select-none pl-10 shadow-inner">
                                <div class="absolute left-3.5 top-3 text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-slate-400"></i>
                                <span>Tên thật được đăng ký theo hợp đồng lao động và hồ sơ nhân sự, không thể tự chỉnh sửa.</span>
                            </p>
                        </div>

                        <!-- 2 Cột: Email & Chức Vụ - KHÓA CỨNG -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope text-slate-400"></i>
                                    <span>Email nội bộ:</span>
                                    <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded">🔒 Khóa</span>
                                </label>
                                <div class="relative">
                                    <input type="email" 
                                           value="{{ $user->email ?? 'chamsockh@gmail.com' }}" 
                                           readonly 
                                           disabled 
                                           class="w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-500 font-semibold text-sm border border-slate-200 cursor-not-allowed select-none pl-10 shadow-inner">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Chức vụ -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-briefcase text-slate-400"></i>
                                    <span>Chức vụ / Phòng ban:</span>
                                    <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded">🔒 Khóa</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           value="{{ $roleTitle ?? 'Chăm Sóc Khách Hàng (CSKH)' }}" 
                                           readonly 
                                           disabled 
                                           class="w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-500 font-semibold text-sm border border-slate-200 cursor-not-allowed select-none pl-10 shadow-inner">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-shield text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ô Input Tên hiển thị (Bí danh) - MỞ KHÓA -->
                        <div>
                            <label for="input_display_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-signature text-blue-600"></i>
                                    <span>Tên hiển thị (Bí danh giao tiếp):</span>
                                </span>
                                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                    <i class="fa-solid fa-pen-to-square"></i> Cho phép sửa
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="input_display_name" 
                                       name="display_name" 
                                       value="{{ old('display_name', $user->display_name ?: ($user->ten_hien_thi ?: '')) }}" 
                                       placeholder="Ví dụ: CSKH Hoàng Nam, CSKH Minh Trang..." 
                                       maxlength="50"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 font-semibold text-sm border border-slate-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all pl-10 shadow-xs">
                                <div class="absolute left-3.5 top-3 text-blue-500">
                                    <i class="fa-solid fa-user-tag text-sm"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 italic flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                <span>Tên dùng để giao tiếp với khách hàng (hiển thị trên các biên bản xử lý, trả lời khiếu nại, chat hỗ trợ).</span>
                            </p>
                        </div>

                        <!-- Ô Input Số điện thoại - MỞ KHÓA -->
                        <div>
                            <label for="input_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-blue-600"></i>
                                    <span>Số điện thoại liên hệ:</span>
                                </span>
                                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                    <i class="fa-solid fa-pen-to-square"></i> Cho phép sửa
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="input_phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone ?? '') }}" 
                                       placeholder="Ví dụ: 0912 345 678" 
                                       maxlength="15"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 font-semibold text-sm border border-slate-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all pl-10 shadow-xs">
                                <div class="absolute left-3.5 top-3 text-blue-500">
                                    <i class="fa-solid fa-phone text-sm"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-info text-slate-400"></i>
                                <span>Số điện thoại dùng để nhận mã xác minh hoặc liên hệ điều phối nội bộ khi cần gấp.</span>
                            </p>
                        </div>

                    </div>

                    <!-- Nút Submit Thông Tin Cá Nhân -->
                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                id="btnSaveProfile" 
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Lưu thay đổi</span>
                        </button>
                    </div>

                </form>

            </div>


            <!-- ════════════════════════════════════════════════════════════
                 KHỐI 2: BẢO MẬT & MẬT KHẨU (5 CỘT)
                 ════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                
                <div>
                    <!-- Card Header -->
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">Bảo Mật & Mật Khẩu</h2>
                                <p class="text-xs text-slate-500">Thay đổi mật khẩu đăng nhập tài khoản nội bộ</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                            Bảo mật
                        </span>
                    </div>

                    <!-- Form Đổi Mật Khẩu -->
                    <form id="passwordForm" action="{{ route('cskh.profile.password') }}" method="POST" onsubmit="handlePasswordSubmit(event)">
                        @csrf

                        <div class="space-y-4">
                            
                            <!-- 1. Mật khẩu hiện tại -->
                            <div>
                                <label for="input_current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-key text-indigo-500"></i>
                                    <span>Mật khẩu hiện tại:</span>
                                    <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="input_current_password" 
                                           name="current_password" 
                                           required 
                                           placeholder="Nhập mật khẩu đang dùng"
                                           class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 font-semibold text-sm border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all pl-10 pr-10 shadow-xs">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('input_current_password', this)" 
                                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Mật khẩu mới -->
                            <div>
                                <label for="input_new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-unlock-keyhole text-indigo-500"></i>
                                    <span>Mật khẩu mới:</span>
                                    <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="input_new_password" 
                                           name="new_password" 
                                           required 
                                           minlength="6"
                                           placeholder="Tối thiểu 6 ký tự"
                                           class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 font-semibold text-sm border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all pl-10 pr-10 shadow-xs">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-shield text-sm"></i>
                                    </div>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('input_new_password', this)" 
                                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Nhập lại mật khẩu mới -->
                            <div>
                                <label for="input_new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-check-double text-indigo-500"></i>
                                    <span>Nhập lại mật khẩu mới:</span>
                                    <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="input_new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           required 
                                           minlength="6"
                                           placeholder="Xác nhận lại mật khẩu mới"
                                           class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 font-semibold text-sm border border-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all pl-10 pr-10 shadow-xs">
                                    <div class="absolute left-3.5 top-3 text-slate-400">
                                        <i class="fa-solid fa-circle-check text-sm"></i>
                                    </div>
                                    <button type="button" 
                                            onclick="togglePasswordVisibility('input_new_password_confirmation', this)" 
                                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- Gợi ý bảo mật -->
                        <div class="mt-5 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 leading-relaxed flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5 shrink-0"></i>
                            <div>
                                <strong class="font-bold">Khuyến cáo bảo mật nhân sự:</strong>
                                <p class="mt-0.5 text-amber-800">
                                    Mật khẩu nên chứa tối thiểu 6 ký tự, gồm cả chữ hoa, chữ thường và chữ số để ngăn chặn việc dò quét trái phép tài khoản hệ thống.
                                </p>
                            </div>
                        </div>

                        <!-- Nút Submit Đổi Mật Khẩu -->
                        <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" 
                                    id="btnSavePassword" 
                                    class="w-full px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                                <i class="fa-solid fa-key"></i>
                                <span>Đổi mật khẩu</span>
                            </button>
                        </div>

                    </form>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 text-center text-xs text-slate-400">
                    Hệ thống RentHome Internal Security v2.0
                </div>

            </div>

        </div>

    </main>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // 1. Toast Notification
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const isSuccess = type === 'success';
            const bgClass = isSuccess ? 'bg-slate-900 text-white border-l-4 border-emerald-500' : 'bg-slate-900 text-white border-l-4 border-rose-500';
            const iconHtml = isSuccess 
                ? '<i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>' 
                : '<i class="fa-solid fa-circle-xmark text-rose-400 text-base"></i>';

            toast.className = `${bgClass} px-5 py-3.5 rounded-xl shadow-2xl text-xs sm:text-sm font-semibold flex items-center gap-3 transition-all duration-300 opacity-0 transform translate-y-[-10px] pointer-events-auto max-w-md`;
            toast.innerHTML = `
                <div class="shrink-0">${iconHtml}</div>
                <div class="flex-1">${message}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white shrink-0 ml-2">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);

            // Trigger animation
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-[-10px]');
                toast.classList.add('opacity-100', 'translate-y-0');
            });

            // Tự tắt sau 3.5s
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // 2. Preview Avatar ngay khi chọn file
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 3 * 1024 * 1024) {
                    showToast('Kích thước ảnh đại diện không được vượt quá 3MB!', 'error');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // 3. Toggle hiển thị mật khẩu
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-sm text-indigo-600';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye text-sm text-slate-400';
            }
        }

        // 4. Submit Form Profile bằng AJAX
        async function handleProfileSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('profileForm');
            const submitBtn = document.getElementById('btnSaveProfile');
            const origHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang lưu...</span>';

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showToast(data.message || 'Lưu thay đổi hồ sơ cá nhân thành công!', 'success');
                    
                    // Cập nhật tên hiển thị ở sidebar và topbar nếu có
                    if (data.display_name) {
                        const sidebarName = document.getElementById('sidebarUserName');
                        if (sidebarName) sidebarName.innerText = data.display_name;
                    }

                    // Cập nhật avatar ở sidebar nếu có
                    if (data.avatar) {
                        const sidebarAvContainer = document.getElementById('sidebarAvatarContainer');
                        if (sidebarAvContainer) {
                            sidebarAvContainer.innerHTML = `<img src="${data.avatar}" alt="Avatar" class="w-full h-full rounded-full object-cover">`;
                        }
                    }
                } else {
                    let errMsg = data.message || 'Có lỗi xảy ra khi lưu hồ sơ.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        errMsg = data.errors[firstKey][0];
                    }
                    showToast(errMsg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ khi lưu hồ sơ.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;
            }
        }

        // 5. Submit Form Password bằng AJAX
        async function handlePasswordSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('passwordForm');
            const submitBtn = document.getElementById('btnSavePassword');
            const origHtml = submitBtn.innerHTML;

            const curPass = document.getElementById('input_current_password').value;
            const newPass = document.getElementById('input_new_password').value;
            const confirmPass = document.getElementById('input_new_password_confirmation').value;

            if (newPass.length < 6) {
                showToast('Mật khẩu mới phải có tối thiểu 6 ký tự!', 'error');
                return;
            }

            if (newPass !== confirmPass) {
                showToast('Xác nhận mật khẩu mới không khớp!', 'error');
                return;
            }

            if (newPass === curPass) {
                showToast('Mật khẩu mới không được trùng với mật khẩu hiện tại!', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang đổi...</span>';

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showToast(data.message || 'Đổi mật khẩu thành công!', 'success');
                    form.reset();
                } else {
                    let errMsg = data.message || 'Mật khẩu hiện tại không chính xác.';
                    if (data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        errMsg = data.errors[firstKey][0];
                    }
                    showToast(errMsg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ khi đổi mật khẩu.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;
            }
        }

        // Flash session toast nếu reload
        @if(session('success'))
            showToast(@js(session('success')), 'success');
        @endif
        @if(session('error'))
            showToast(@js(session('error')), 'error');
        @endif
        @if($errors->any())
            showToast(@js($errors->first()), 'error');
        @endif
    </script>
</body>
</html>

