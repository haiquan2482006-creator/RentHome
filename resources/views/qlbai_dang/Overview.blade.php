<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Bài Đăng - RentHome</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(34, 197, 94, 0.4)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.08)',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Desktop Mini Sidebar Styles */
        @media (min-width: 768px) {
            #sidebar-menu {
                transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
            }
            #sidebar-menu.sidebar-mini {
                width: 5.5rem !important;
            }
            #sidebar-menu.sidebar-mini .logo-text,
            #sidebar-menu.sidebar-mini .user-info,
            #sidebar-menu.sidebar-mini .menu-title,
            #sidebar-menu.sidebar-mini .tab-btn span,
            #sidebar-menu.sidebar-mini .sidebar-btn span {
                display: none !important;
            }
            #sidebar-menu.sidebar-mini .tab-btn,
            #sidebar-menu.sidebar-mini .sidebar-btn,
            #sidebar-menu.sidebar-mini .user-card {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            #sidebar-menu.sidebar-mini .tab-btn div {
                justify-content: center;
                width: 100%;
            }
            #sidebar-menu.sidebar-mini .tab-btn div span {
                display: none !important;
            }
            #sidebar-menu.sidebar-mini .tab-btn i,
            #sidebar-menu.sidebar-mini .sidebar-btn i {
                margin-right: 0 !important;
            }
            #sidebar-menu.sidebar-mini #btn-collapse-sidebar i {
                transform: rotate(180deg);
            }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen">

    @php
        $currentUser = Auth::user();
        $accountType = $currentUser->role ?? 'user';
        $isEnterprise = $currentUser && ($currentUser->isEnterprise() || $accountType === 'doanhnghiep');
        $isSubAccount = $currentUser && ($currentUser->isSubAccount() || $currentUser->isStaff() || $accountType === 'sub_account' || ($currentUser->role ?? '') === 'staff');
        $isAdmin = $currentUser && ($currentUser->role === 'admin' || $currentUser->isAdmin());

        if ($isSubAccount) {
            $displayName = $currentUser->account_name ?? ($currentUser->username ?? 'Quản lý tòa nhà');
            $subTitle = 'Nhân viên quản lý tòa nhà';
            $badgeText = 'Nhân Viên Vận Hành';
        } elseif ($isEnterprise || $isAdmin) {
            $displayName = !empty($currentUser->company_name) ? $currentUser->company_name : ($currentUser->account_name ?? 'RentHome Doanh Nghiệp');
            $subTitle = 'Tổng Doanh Nghiệp RentHome';
            $badgeText = 'RentHome Doanh Nghiệp';
        } else {
            $displayName = $currentUser->account_name ?? ($currentUser->username ?? ($currentUser->name ?? 'Người dùng'));
            $subTitle = 'Tài khoản Cá nhân';
            $badgeText = 'Cá Nhân';
        }

        // Đếm số lượng bài đăng chờ duyệt (chỉ dành cho Doanh nghiệp RentHome hoặc Admin)
        $globalPendingModerationCount = 0;
        if ($isEnterprise || $isAdmin) {
            $globalPendingModerationCount = \App\Models\Post::where('status', 'pending')->count();
        }

        // Đếm số lượng thông báo liên hệ chưa xử lý (pending)
        $pendingContactCount = 0;
        if ($currentUser) {
            $postIds = \App\Models\Post::where('user_id', $currentUser->id)->pluck('_id');
            $pendingContactCount = \App\Models\Contact::whereIn('post_id', $postIds)
                                                      ->where('status', 'pending')
                                                      ->count();
        }
    @endphp

    <!-- Mobile Top Navigation Header -->
    <header
        class="md:hidden bg-white border-b border-slate-200 sticky top-0 z-40 px-4 py-3 flex items-center justify-between shadow-sm">
        <a href="/" class="flex items-center gap-2.5">
            <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-8 h-8 object-contain rounded-xl shadow-xs">
            <span class="text-base font-extrabold text-slate-900 tracking-tight">Rent<span
                    class="text-brand-600">Home</span></span>
        </a>
        <div class="flex items-center gap-2">
            <span
                class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $isEnterprise ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $badgeText }}
            </span>
            <button onclick="toggleSidebar()"
                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 focus:outline-none">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>
    </header>

    @include('partials.sidebar-user', ['isDrawer' => true])

    <!-- Overlay for Mobile Sidebar -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <div class="flex min-h-screen">

        <!-- Left Vertical Sidebar (Header Dọc bên tay trái) -->
        <aside id="sidebar-menu"
            class="fixed md:sticky top-0 left-0 z-50 md:z-30 w-72 h-screen bg-white border-r border-slate-200/80 shadow-md md:shadow-none flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 shrink-0">
            
            <!-- Nút Thu gọn (Desktop) -->
            <button onclick="toggleDesktopSidebarMini()" id="btn-collapse-sidebar" class="absolute -right-3 top-8 w-6 h-6 bg-white border border-slate-200 rounded-full hidden md:flex items-center justify-center text-slate-400 hover:text-brand-600 shadow-sm z-50 transition-all hover:scale-110">
                <i class="fa-solid fa-chevron-left text-[10px] transition-transform duration-300"></i>
            </button>

            <div class="p-5 pb-0 space-y-6 shrink-0">
                <!-- Sidebar Brand Header -->
                <div class="flex items-center justify-between pb-2 brand-header">
                    <a href="/" class="flex items-center gap-3 group">
                        <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                        <div class="flex flex-col logo-text">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span
                                    class="text-brand-600">Home</span></span>
                            <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5">Quản
                                lý bài đăng</span>
                        </div>
                    </a>
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- User Account Information Card -->
                <!-- User Account Information Card -->
                <div class="relative group w-full user-card-wrapper z-50">
                    <a href="{{ route('user.dashboard') }}" class="user-card block w-full text-left p-3.5 rounded-2xl {{ ($isEnterprise && !$isSubAccount) ? 'bg-gradient-to-br from-purple-50 to-indigo-50/50 border border-purple-100' : 'bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-100' }} flex items-center gap-3 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                        <div class="w-10 h-10 rounded-xl {{ ($isEnterprise && !$isSubAccount) ? 'bg-purple-600 text-white' : 'bg-brand-600 text-white' }} flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            <i class="fa-solid {{ ($isEnterprise && !$isSubAccount) ? 'fa-briefcase' : 'fa-user' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0 user-info">
                            <p class="text-xs font-bold text-slate-900 truncate" title="{{ $displayName }}">{{ $displayName }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="inline-block w-2 h-2 rounded-full {{ ($isEnterprise && !$isSubAccount) ? 'bg-purple-500' : 'bg-emerald-500' }}"></span>
                                <span class="text-[11px] font-semibold {{ ($isEnterprise && !$isSubAccount) ? 'text-purple-700' : 'text-emerald-700' }}">{{ $subTitle }}</span>
                            </div>
                        </div>
                    </a>
                    
                    <div class="absolute left-full top-0 ml-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="bg-white rounded-xl shadow-xl border border-slate-100 p-1 flex flex-col gap-1 relative before:absolute before:-left-2 before:top-4 before:w-0 before:h-0 before:border-y-8 before:border-y-transparent before:border-r-8 before:border-r-white before:drop-shadow-sm">
                            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition-colors">
                                <i class="fa-solid fa-user-pen w-4 text-center"></i> Hồ sơ
                            </a>
                            <a href="{{ route('user.change_password') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition-colors">
                                <i class="fa-solid fa-key w-4 text-center"></i> Đổi mật khẩu
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="px-5 pt-6 pb-5 overflow-y-auto flex-1 custom-scrollbar">
                <!-- Vertical Navigation Menu -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2 menu-title">Danh mục
                        quản lý</p>

                    <button onclick="switchTab('overview')" id="tab-overview"
                        class="tab-btn active flex items-center gap-3 px-3.5 py-3 rounded-xl font-bold text-xs w-full text-left transition-all bg-brand-50 text-brand-600 border border-brand-200/60 shadow-xs">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm"></i>
                        <span> Tổng quan</span>
                    </button>

                    <button onclick="switchTab('active-posts')" id="tab-active-posts"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-circle-check text-emerald-500 w-5 text-center text-sm"></i>
                        <span> Tin đang hiển thị</span>
                    </button>

                    <button onclick="switchTab('pending-posts')" id="tab-pending-posts"
                        class="tab-btn flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-hourglass-half text-amber-500 w-5 text-center text-sm"></i>
                            <span> Chờ phê duyệt</span>
                        </div>
                        <span id="sidebar-pending-count" class="hidden px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-bold shadow-xs">0</span>
                    </button>

                    <button onclick="switchTab('history-posts')" id="tab-history-posts"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-clock-rotate-left text-sky-500 w-5 text-center text-sm"></i>
                        <span> Lịch sử đăng tin</span>
                    </button>

                    <button onclick="switchTab('notifications')" id="tab-notifications"
                        class="tab-btn flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bell text-rose-500 w-5 text-center text-sm"></i>
                            <span> Thông báo</span>
                        </div>
                        <span id="sidebar-notif-badge" class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold shadow-xs">0</span>
                    </button>

                    <a href="{{ route('contact.index') }}" id="tab-contact-notif"
                        class="tab-btn flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope-open-text text-amber-500 w-5 text-center text-sm"></i>
                            <span> Khách hàng liên hệ</span>
                        </div>
                        @if($pendingContactCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold shadow-xs">{{ $pendingContactCount }}</span>
                        @endif
                    </a>

                    @if($isEnterprise || $isSubAccount || $isAdmin)
                    <button onclick="switchTab('buildings')" id="tab-buildings"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-city text-purple-500 w-5 text-center text-sm"></i>
                        <span> {{ $isSubAccount ? 'Tòa nhà được giao' : 'Quản lý Tòa Nhà' }}</span>
                    </button>
                    @endif

                    @if(($isEnterprise || $isAdmin) && !$isSubAccount)
                    <button onclick="switchTab('sub-accounts')" id="tab-sub-accounts"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-users-gear text-teal-600 w-5 text-center text-sm"></i>
                        <span> Nhân viên & Phân quyền</span>
                    </button>
                    @endif
                </nav>
            </div>

            <!-- Footer / Actions in Sidebar -->
            <div class="p-4 border-t border-slate-100 space-y-1 bg-slate-50/50">
                <a href="/"
                    class="sidebar-btn flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-house w-5 text-center text-slate-400"></i>
                    <span>Về Trang Chủ</span>
                </a>
                <a href="{{ url('quan-ly-van-hanh') }}"
                    class="sidebar-btn flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-gears w-5 text-center text-slate-400"></i>
                    <span>Quản lý vận hành</span>
                </a>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                            class="sidebar-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-rose-600 hover:bg-rose-50 transition-all text-left">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">

            <!-- Top Action Header in Main Content -->
            <div
                class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 id="page-heading" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tổng Quan
                            Hệ Thống</h1>
                        <p class="text-xs text-slate-500 mt-1">Quản lý hiệu suất, danh sách tin đăng và tương tác khách hàng
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">


                    <a href="/"
                        class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200/80 shadow-xs transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Trang chủ
                    </a>
                    <a href="{{ url('dangbai') }}"
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md hover:shadow-glow transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Đăng tin mới
                    </a>
                </div>
            </div>

            <!-- ==================== TAB A: TỔNG QUAN ==================== -->
            <section id="content-overview" class="tab-content space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between hover:border-brand-500/30 transition-all">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng lượt xem</p>
                            <p id="total-views" class="text-2xl font-black text-slate-900 mt-1">0</p>
                            <span class="text-[11px] text-emerald-600 font-bold"><i
                                    class="fa-solid fa-arrow-trend-up"></i>
                                +12% tuần này</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between hover:border-brand-500/30 transition-all">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng lượt thích
                            </p>
                            <p id="total-likes" class="text-2xl font-black text-slate-900 mt-1">0</p>
                            <span class="text-[11px] text-emerald-600 font-bold"><i
                                    class="fa-solid fa-arrow-trend-up"></i>
                                +5.4% tuần này</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between hover:border-brand-500/30 transition-all">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tin đang hiển thị
                            </p>
                            <p id="active-posts-count" class="text-2xl font-black text-slate-900 mt-1">0</p>
                            <span class="text-[11px] text-slate-400">Trên tổng 24 tin</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-house-circle-check"></i>
                        </div>
                    </div>

                    @if($isEnterprise)
                    <div
                        class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between hover:border-brand-500/30 transition-all">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tòa nhà / Dự án
                            </p>
                            <p id="total-buildings-count" class="text-2xl font-black text-slate-900 mt-1">0</p>
                            <span class="text-[11px] text-purple-600 font-bold">120 phòng đang quản lý</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-city"></i>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Detailed Post Performance Table -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-sm">Các bài đăng đã được phê duyệt</h3>
                        <span class="text-xs text-slate-400">Cập nhật lúc 15:00 hôm nay</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50/75 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-left text-xs">
                                    <th class="p-4">Mã tin</th>
                                    <th class="p-4">Tên bài đăng</th>
                                    <th class="p-4">Giá thuê</th>
                                    <th class="p-4">Trạng thái</th>
                                    <th class="p-4">Lượt xem</th>
                                    <th class="p-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="active-posts-tbody" class="divide-y divide-slate-100 text-slate-700 font-medium">
                            </tbody>
                        </table>
                    </div>

                <!-- Recent Notifications Card Widget in Overview -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900">Thông báo mới nhất ({{ $isEnterprise ? 'Doanh Nghiệp' : 'Cá Nhân' }})</h3>
                                <p class="text-[11px] text-slate-500">Cập nhật tự động thông tin bài đăng, lịch hẹn và quản lý vận hành</p>
                            </div>
                        </div>
                        <button onclick="switchTab('notifications')" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                            Xem tất cả thông báo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>

                    <!-- Dynamic Recent Notifications Grid/List -->
                    <div id="overview-recent-notifs" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <!-- Rendered by JS -->
                    </div>
                </div>
            </section>

            <!-- ==================== TAB B: TIN ĐANG HIỂN THỊ ==================== -->
            <section id="content-active-posts" class="tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <h3 id="active-posts-heading" class="font-bold text-base text-slate-900">Danh sách tin đang hiển thị</h3>
                    <input type="text" id="active-posts-search-input" onkeyup="filterActivePostsTable(this.value)" placeholder="Tìm kiếm mã tin, tên bài..."
                        class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Mã Tin</th>
                                    <th class="p-4">Tên Bài Đăng</th>
                                    <th class="p-4">Giá Thuê</th>
                                    <th class="p-4">Trạng Thái</th>
                                    <th class="p-4 text-center">Lượt Xem</th>
                                    <th class="p-4 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="active-posts-tab-tbody" class="divide-y divide-slate-100 text-slate-700">
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB C: BÀI ĐĂNG CHỜ PHÊ DUYỆT ==================== -->
            <section id="content-pending-posts" class="tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 id="pending-count-heading" class="font-bold text-base text-slate-900">Danh sách bài đăng đang chờ ban quản trị duyệt</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Các tin đăng đang trong quá trình xét duyệt nội dung trước khi hiển thị công khai</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Mã Tin</th>
                                    <th class="p-4">Tên Bài Đăng</th>
                                    <th class="p-4">Giá Thuê</th>
                                    <th class="p-4">Trạng Thái</th>
                                    <th class="p-4 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="pending-posts-tbody" class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 font-medium">
                                        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block text-brand-500"></i>
                                        Đang tải danh sách bài đăng chờ duyệt...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB D: LỊCH SỬ ĐĂNG TIN ==================== -->
            <section id="content-history-posts" class="tab-content hidden space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Lịch sử bài đăng tin</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Danh sách các tin đã đăng cùng trạng thái đã phê duyệt hoặc đang chờ phê duyệt</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="filterHistoryPosts('all')" id="hist-filter-all"
                            class="hist-filter-btn px-3 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-xs transition-colors">
                            Tất cả
                        </button>
                        <button onclick="filterHistoryPosts('approved')" id="hist-filter-approved"
                            class="hist-filter-btn px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                            Đã được phê duyệt
                        </button>
                        <button onclick="filterHistoryPosts('pending')" id="hist-filter-pending"
                            class="hist-filter-btn px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                            Đang chờ phê duyệt
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Mã Tin</th>
                                    <th class="p-4">Tên Bài Đăng</th>
                                    <th class="p-4">Giá Thuê</th>
                                    <th class="p-4">Trạng Thái</th>
                                    <th class="p-4">Ngày Đăng</th>
                                    <th class="p-4 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="history-posts-tbody" class="divide-y divide-slate-100 text-slate-700">
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB F: THÔNG BÁO ==================== -->
            <section id="content-notifications" class="tab-content hidden space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-bell text-rose-500"></i> Hộp Thư & Thông Báo Hệ Thống
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Thông báo người quan tâm tới ngôi nhà bạn đã đăng và thông báo hệ thống {{ $isEnterprise ? 'Doanh Nghiệp' : 'Cá Nhân' }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button onclick="markAllNotifsAsRead()" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-check-double text-emerald-600"></i> Đọc tất cả
                        </button>
                        <button onclick="clearReadNotifs()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can text-slate-500"></i> Xóa đã đọc
                        </button>
                    </div>
                </div>

                <!-- Filter Tabs for Notifications -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <button onclick="filterNotifs('all')" id="notif-filter-all"
                        class="notif-filter-btn px-3.5 py-1.5 rounded-xl bg-slate-900 text-white font-bold text-xs transition-colors flex items-center gap-1.5 shadow-xs">
                        <span>Tất cả</span>
                        <span id="badge-count-all" class="px-1.5 py-0.2 rounded-full bg-slate-700 text-white text-[10px]">0</span>
                    </button>

                    <button onclick="filterNotifs('favorite')" id="notif-filter-favorite"
                        class="notif-filter-btn px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-rose-50 hover:text-rose-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-heart text-rose-500 text-xs"></i>
                        <span>Thông báo yêu thích</span>
                        <span id="badge-count-favorite" class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-700 text-[10px]">0</span>
                    </button>

                    <button onclick="filterNotifs('unread')" id="notif-filter-unread"
                        class="notif-filter-btn px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-amber-50 hover:text-amber-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-envelope-open-text text-amber-500 text-xs"></i>
                        <span>Thông báo chưa đọc</span>
                        <span id="badge-count-unread" class="px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[10px]">0</span>
                    </button>
                </div>

                <!-- Notifications List Container -->
                <div id="notifications-list-container" class="space-y-3 pt-2">
                    <!-- Rendered dynamically by JS -->
                </div>
            </section>

            <!-- ==================== TAB G: TÒA NHÀ ĐƯỢC GIAO & QUẢN LÝ TÒA NHÀ ==================== -->
            @if($isEnterprise || $isSubAccount || $isAdmin)
            <section id="content-buildings" class="tab-content hidden space-y-6">
                <!-- Action Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900">{{ $isSubAccount ? 'Danh Sách Tòa Nhà Được Giao Phụ Trách' : 'Danh Sách Tòa Nhà & Khu Đô Thị (RentHome)' }}</h3>
                        <p class="text-xs text-slate-500">{{ $isSubAccount ? 'Bạn chỉ có quyền quản lý danh sách phòng và tin đăng thuộc các tòa nhà được giao.' : 'Quản lý toàn bộ cụm chung cư, tòa nhà và phân quyền nhân viên phụ trách' }}</p>
                    </div>
                    @if(!$isSubAccount)
                    <!-- Nút mở form tạo tòa nhà (chỉ Doanh nghiệp RentHome hoặc Admin) -->
                    <button onclick="openCreateBuildingModal()"
                        class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tạo Tòa Nhà Mới
                    </button>
                    @endif
                </div>

                <!-- Danh sách Tòa nhà đã tạo (Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
            </section>
            @endif



            <!-- ==================== TAB: QUẢN LÝ TÀI KHOẢN CON & PHÂN QUYỀN (RENTHOME ENTERPRISE) ==================== -->
            @if($isEnterprise || $isAdmin)
            <section id="content-sub-accounts" class="tab-content hidden space-y-6">
                <!-- Action Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-users-gear text-teal-600"></i> Quản Lý Tài Khoản Con & Phân Quyền Tòa Nhà
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tạo tài khoản nhân viên vận hành và phân công tòa nhà phụ trách. Nhân viên chỉ quản lý đúng các tòa nhà được chỉ định.</p>
                    </div>
                    
                    <button onclick="openCreateSubAccountModal()"
                        class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Thêm Nhân Viên Mới
                    </button>
                </div>

                <!-- Stats summary for sub-accounts -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tổng nhân viên vận hành</p>
                            <p id="sub-total-count" class="text-xl font-black text-slate-900 mt-1">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tài khoản đang hoạt động</p>
                            <p id="sub-active-count" class="text-xl font-black text-emerald-600 mt-1">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tài khoản tạm khóa</p>
                            <p id="sub-locked-count" class="text-xl font-black text-rose-600 mt-1">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-lock"></i>
                        </div>
                    </div>
                </div>

                <!-- Sub-account Table -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h4 class="font-bold text-slate-900 text-sm">Danh Sách Nhân Viên & Tòa Nhà Phụ Trách</h4>
                        <input type="text" id="sub-search-input" onkeyup="filterSubAccountsTable(this.value)" placeholder="Tìm kiếm tên, email, tòa nhà..."
                            class="px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Nhân viên</th>
                                    <th class="p-4">Email đăng nhập</th>
                                    <th class="p-4">Số điện thoại</th>
                                    <th class="p-4">Tòa nhà được phân quyền</th>
                                    <th class="p-4">Trạng thái</th>
                                    <th class="p-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="sub-accounts-table-tbody" class="divide-y divide-slate-100 text-slate-700 font-medium">
                                <!-- Rendered dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            @endif

        </main>
    </div>

    <!-- ==================== MODAL: TỪ CHỐI DUYỆT BÀI ĐĂNG ==================== -->
    <div id="modRejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-rose-500"></i> Từ Chối Phê Duyệt Bài Đăng
                </h3>
                <button onclick="closeModRejectModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <form id="formModReject" onsubmit="modSubmitRejectPost(event)" class="space-y-4">
                <input type="hidden" id="mod_reject_post_id" value="">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Chọn nhanh lý do từ chối:</label>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <button type="button" onclick="setRejectReason('Thông tin bài đăng không chính xác hoặc thiếu số phòng')" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg text-[11px] font-semibold text-slate-600 transition-colors">
                            Thiếu số phòng / sai địa chỉ
                        </button>
                        <button type="button" onclick="setRejectReason('Hình ảnh mờ, không đúng thực tế hoặc vi phạm tiêu chuẩn')" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg text-[11px] font-semibold text-slate-600 transition-colors">
                            Hình ảnh không rõ ràng
                        </button>
                        <button type="button" onclick="setRejectReason('Mức giá niêm yết không phù hợp với quy định tòa nhà RentHome')" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg text-[11px] font-semibold text-slate-600 transition-colors">
                            Giá thuê không hợp lý
                        </button>
                        <button type="button" onclick="setRejectReason('Nội dung mô tả chứa thông tin liên hệ không đúng quy định')" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg text-[11px] font-semibold text-slate-600 transition-colors">
                            Vi phạm quy chuẩn
                        </button>
                    </div>

                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Chi tiết lý do từ chối gửi tới người đăng <span class="text-rose-500">*</span></label>
                    <textarea id="mod_reject_reason" required rows="3" placeholder="Nhập lý do cụ thể để người đăng chỉnh sửa..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModRejectModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Hủy
                    </button>
                    <button type="submit" id="btn-confirm-reject" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-md">
                        Xác nhận từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: XEM CHI TIẾT BÀI ĐĂNG TRƯỚC KHI DUYỆT ==================== -->
    <div id="modDetailPreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-eye text-indigo-600"></i> Xem Chi Tiết Tin Đăng Trước Khi Phê Duyệt
                </h3>
                <button onclick="closeModDetailPreviewModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="space-y-4 text-xs">
                <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img id="preview-cover-img" src="" alt="Ảnh bài đăng" class="w-full h-64 object-cover">
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span id="preview-building-badge" class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-bold text-xs"></span>
                    <span id="preview-price" class="text-base font-extrabold text-brand-600"></span>
                </div>

                <div>
                    <h4 id="preview-title" class="text-base font-extrabold text-slate-900"></h4>
                    <p id="preview-address" class="text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-slate-400"></i> <span id="preview-address-text"></span>
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Diện tích</span>
                        <span id="preview-area" class="font-bold text-slate-800"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Số phòng</span>
                        <span id="preview-room" class="font-bold text-slate-800"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Người đăng</span>
                        <span id="preview-author" class="font-bold text-slate-800"></span>
                    </div>
                </div>

                <div>
                    <h5 class="font-bold text-slate-800 mb-1">Mô tả bài đăng:</h5>
                    <p id="preview-description" class="text-slate-600 whitespace-pre-line bg-slate-50 p-3 rounded-xl border border-slate-100 max-h-36 overflow-y-auto"></p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModDetailPreviewModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Đóng
                    </button>
                    <button type="button" id="preview-btn-reject-action" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold transition-colors">
                        <i class="fa-solid fa-xmark mr-1"></i> Từ chối
                    </button>
                    <button type="button" id="preview-btn-approve-action" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-all shadow-md">
                        <i class="fa-solid fa-check mr-1"></i> Phê duyệt ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL: TẠO TÀI KHOẢN CON & PHÂN QUYỀN ==================== -->
    <div id="createSubAccountModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-teal-600"></i> Thêm Tài Khoản Con (Nhân Viên Vận Hành)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tạo tài khoản và phân quyền chỉ định các tòa nhà nhân viên được phép quản lý</p>
                </div>
                <button onclick="closeCreateSubAccountModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formCreateSubAccount" onsubmit="submitCreateSubAccount(event)" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Họ và tên nhân viên <span class="text-rose-500">*</span></label>
                        <input type="text" id="sub_name" name="name" required placeholder="VD: Nguyễn Văn Anh"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số điện thoại <span class="text-rose-500">*</span></label>
                        <input type="text" id="sub_phone" name="phone" required placeholder="VD: 0912345678"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email đăng nhập <span class="text-rose-500">*</span></label>
                        <input type="email" id="sub_email" name="email" required placeholder="nhanvien@gmail.com"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Mật khẩu khởi tạo <span class="text-rose-500">*</span></label>
                        <input type="password" id="sub_password" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700">Phân quyền tòa nhà phụ trách <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-teal-600 font-semibold cursor-pointer hover:underline" onclick="selectAllSubBuildings('create')">Chọn tất cả</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-2">Nhân viên này CHỈ có quyền xem, tạo phòng và quản lý tin đăng thuộc các tòa nhà được tích chọn dưới đây:</p>
                    <div id="sub-create-buildings-checklist" class="max-h-48 overflow-y-auto space-y-2 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs custom-scrollbar">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateSubAccountModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Hủy
                    </button>
                    <button type="submit" id="btn-submit-create-sub" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Tạo tài khoản & Phân quyền
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: SỬA PHÂN QUYỀN TÒA NHÀ & TÀI KHOẢN CON ==================== -->
    <div id="editSubAccountModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-teal-600"></i> Cập Nhật Phân Quyền Tòa Nhà
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" id="edit_sub_subtitle">Chỉnh sửa tòa nhà phân công và thông tin nhân viên</p>
                </div>
                <button onclick="closeEditSubAccountModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formEditSubAccount" onsubmit="submitEditSubAccount(event)" class="space-y-4">
                <input type="hidden" id="edit_sub_id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Họ và tên nhân viên <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_sub_name" name="name" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Số điện thoại <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_sub_phone" name="phone" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Mật khẩu mới (để trống nếu không muốn đổi)</label>
                    <input type="password" id="edit_sub_password" name="password" minlength="6" placeholder="Để trống nếu giữ nguyên mật khẩu cũ"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700">Tòa nhà được giao quản lý <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-teal-600 font-semibold cursor-pointer hover:underline" onclick="selectAllSubBuildings('edit')">Chọn tất cả</span>
                    </div>
                    <div id="sub-edit-buildings-checklist" class="max-h-48 overflow-y-auto space-y-2 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs custom-scrollbar">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditSubAccountModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Hủy
                    </button>
                    <button type="submit" id="btn-submit-edit-sub" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi phân quyền
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 1: FORM TẠO TÒA NHÀ ==================== -->
    <div id="createBuildingModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-extrabold text-slate-900">Tạo Tòa Nhà / Khu Đô Thị Mới</h3>
                <button onclick="closeCreateBuildingModal()"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formCreateBuilding" onsubmit="submitCreateBuilding(event);" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên tòa nhà -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tên tòa nhà / Khu đô thị</label>
                    <input type="text" id="b_name" name="name" required placeholder="VD: Tòa Landmark Plus..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <!-- Loại hình tòa nhà -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Loại hình</label>
                    <select id="b_type" name="type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">Chọn loại hình...</option>
                        <option value="Chung cư">Chung cư</option>
                        <option value="Căn hộ dịch vụ">Căn hộ dịch vụ</option>
                        <option value="Khu trọ">Khu trọ</option>
                        <option value="Tòa nhà văn phòng">Tòa nhà văn phòng</option>
                    </select>
                </div>

                <!-- Tỉnh / Thành phố -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Tỉnh / Thành Phố <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="b_toggleCustomProvinceMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="b_toggle-custom-text">Tự nhập khác</span>
                        </button>
                    </div>
                    
                    <div id="b_province-select-wrapper" class="relative">
                        <input type="hidden" id="b_select-province" name="province" required>
                        <button type="button" onclick="b_toggleProvinceDropdown(event)"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                            <span id="b_form-province-text" class="truncate font-bold text-slate-800">Đang tải danh sách...</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="b_form-province-arrow"></i>
                        </button>

                        <div id="b_form-province-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs">
                            <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" id="b_form-province-search" oninput="b_filterProvinces()" placeholder="Tìm tỉnh / thành phố..."
                                        class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                </div>
                            </div>
                            <div class="max-h-56 overflow-y-auto custom-scrollbar" id="b_form-provinces-list"></div>
                        </div>
                    </div>

                    <div id="b_province-custom-wrapper" class="hidden relative">
                        <input type="text" id="b_custom-province-input" oninput="b_onCustomProvinceInput()" placeholder="Nhập tên tỉnh / thành phố..."
                            class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                </div>

                <!-- Quận / Huyện -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Quận / Huyện <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="b_toggleCustomDistrictMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="b_toggle-custom-district-text">Tự nhập khác</span>
                        </button>
                    </div>

                    <div id="b_district-select-wrapper" class="relative">
                        <input type="hidden" id="b_select-district" name="district" required>
                        <button type="button" onclick="b_toggleDistrictDropdown(event)"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                            <span id="b_form-district-text" class="truncate font-bold text-slate-800">Chọn Quận / Huyện</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="b_form-district-arrow"></i>
                        </button>

                        <div id="b_form-district-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs">
                            <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" id="b_form-district-search" oninput="b_filterDistricts()" placeholder="Tìm quận / huyện..."
                                        class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                </div>
                            </div>
                            <div class="max-h-56 overflow-y-auto custom-scrollbar" id="b_form-districts-list"></div>
                        </div>
                    </div>

                    <div id="b_district-custom-wrapper" class="hidden relative">
                        <input type="text" id="b_custom-district-input" oninput="b_onCustomDistrictInput()" placeholder="Nhập tên quận / huyện..."
                            class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                </div>

                <!-- Phường / Xã -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Phường / Xã <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="b_toggleCustomWardMode()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square"></i> <span id="b_toggle-custom-ward-text">Tự nhập khác</span>
                        </button>
                    </div>

                    <div id="b_ward-select-wrapper" class="relative">
                        <input type="hidden" id="b_select-ward" name="ward" required>
                        <button type="button" onclick="b_toggleWardDropdown(event)"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-semibold text-slate-800 text-left flex items-center justify-between focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer">
                            <span id="b_form-ward-text" class="truncate font-bold text-slate-800">Chọn Phường / Xã</span>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs" id="b_form-ward-arrow"></i>
                        </button>

                        <div id="b_form-ward-panel" class="hidden absolute top-full left-0 right-0 mt-1 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-xs">
                            <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0 z-10">
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" id="b_form-ward-search" oninput="b_filterWards()" placeholder="Tìm phường / xã..."
                                        class="w-full pl-8 pr-7 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium text-slate-800">
                                </div>
                            </div>
                            <div class="max-h-56 overflow-y-auto custom-scrollbar" id="b_form-wards-list"></div>
                        </div>
                    </div>

                    <div id="b_ward-custom-wrapper" class="hidden relative">
                        <input type="text" id="b_custom-ward-input" oninput="b_onCustomWardInput()" placeholder="Nhập tên phường / xã..."
                            class="w-full px-4 py-3 rounded-2xl bg-amber-50/40 border border-amber-300 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                    </div>
                </div>

                <!-- Địa chỉ chi tiết -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Địa chỉ chi tiết (Tên đường, số nhà)</label>
                    <input type="text" id="b_address_detail" name="address_detail" placeholder="Ví dụ: Số 12, Ngõ 45"
                        class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>

                <!-- Tổng số phòng -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tổng số phòng / Nhà dự kiến</label>
                    <input type="number" id="b_total_rooms" name="total_rooms" required placeholder="VD: 50"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
                
                <!-- Ảnh đại diện -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Ảnh đại diện tòa nhà</label>
                    <label for="b_image" class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:bg-slate-50 cursor-pointer block relative overflow-hidden h-[100px]">
                        <input type="file" id="b_image" name="image" class="hidden" accept="image/*" onchange="previewBuildingImage(event)">
                        <div id="b_image_placeholder" class="flex flex-col items-center justify-center h-full">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-xs text-slate-600 mt-1 font-semibold">Tải lên hình ảnh</p>
                            <p class="text-[10px] text-slate-400">PNG, JPG</p>
                        </div>
                        <img id="b_image_preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                    </label>
                </div>

                <!-- Nội quy / Mô tả chung -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nội quy / Mô tả chung</label>
                    <textarea id="b_description" name="description" rows="3" placeholder="Mô tả tòa nhà, nội quy..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                </div>

                <div class="md:col-span-2 pt-2 flex gap-3">
                    <button type="submit" id="btnSubmitBuilding"
                        class="flex-1 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <span>Hoàn thành</span>
                    </button>
                    <button type="button" onclick="closeCreateBuildingModal()"
                        class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL 2: CHI TIẾT TÒA NHÀ & PHÒNG ==================== -->
    <div id="buildingDetailModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div
            class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-100">
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-900">Chi Tiết Tòa Nhà & Danh Sách Phòng</h3>
                <button onclick="closeBuildingDetailModal()"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Building Summary View -->
            <div class="mt-6 flex flex-col sm:flex-row gap-6 items-start">
                <img id="detailBuildingImg" src=""
                    class="w-full sm:w-64 aspect-[16/10] rounded-2xl object-cover border border-slate-200 shadow-sm">
                <div class="flex-1 space-y-2">
                    <h4 id="detailBuildingName" class="text-xl font-black text-slate-900"></h4>
                    <p id="detailBuildingAddress" class="text-xs text-slate-600 flex items-center gap-1.5"></p>
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-bold text-xs mt-2">
                        <i class="fa-solid fa-door-open"></i> Tổng số: <span id="detailBuildingTotalRooms"></span>
                        phòng
                    </div>
                </div>
            </div>

            <!-- Inside Rooms Table -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="font-bold text-sm text-slate-900">Danh sách các phòng, nhà bên trong tòa</h5>
                    <button
                        class="px-3 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition-colors">
                        <i class="fa-solid fa-plus mr-1"></i> Thêm phòng vào tòa
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <th class="p-3.5">Mã Phòng</th>
                                <th class="p-3.5">Loại Phòng</th>
                                <th class="p-3.5">Diện Tích</th>
                                <th class="p-3.5">Giá Thuê</th>
                                <th class="p-3.5">Trạng Thái</th>
                                <th class="p-3.5 text-right">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700 font-medium">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Controller -->
    <script>
        // ==========================================
        // QUẢN LÝ THÔNG BÁO (PHÂN QUYỀN DOANH NGHIỆP / CÁ NHÂN)
        // ==========================================
        const currentAccountType = "{{ $accountType }}";

        let appNotifications = [];
        let currentNotifFilter = 'all';

        async function fetchNotifications() {
            try {
                const response = await fetch('/api/notifications?t=' + new Date().getTime());
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.notifications) {
                        appNotifications = data.notifications.map(n => {
                            // Convert from DB model to UI model
                            let iconBg = 'bg-blue-100 text-blue-600 border-blue-200';
                            let icon = 'fa-bell';
                            let badgeBg = 'bg-blue-50 text-blue-600 border-blue-100';
                            let categoryName = 'Hệ thống';

                            if (n.type === 'success') {
                                iconBg = 'bg-emerald-100 text-emerald-600 border-emerald-200';
                                icon = 'fa-check-circle';
                                badgeBg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                categoryName = 'Đã duyệt';
                            } else if (n.type === 'error') {
                                iconBg = 'bg-rose-100 text-rose-600 border-rose-200';
                                icon = 'fa-circle-xmark';
                                badgeBg = 'bg-rose-50 text-rose-600 border-rose-100';
                                categoryName = 'Từ chối';
                            } else if (n.type === 'favorite') {
                                iconBg = 'bg-rose-100 text-rose-600 border-rose-200';
                                icon = 'fa-heart';
                                badgeBg = 'bg-rose-50 text-rose-600 border-rose-100';
                                categoryName = 'Yêu thích';
                            } else if (n.type === 'building' || n.category === 'toanha') {
                                iconBg = 'bg-sky-100 text-sky-600 border-sky-200';
                                icon = 'fa-building-circle-check';
                                badgeBg = 'bg-sky-50 text-sky-700 border-sky-200';
                                categoryName = 'Cập nhật tòa nhà';
                            }

                            return {
                                id: n._id || n.id,
                                title: n.title,
                                content: n.message,
                                changes: Array.isArray(n.changes) ? n.changes : [],
                                time: new Date(n.created_at).toLocaleString('vi-VN'),
                                type: n.type,
                                isRead: n.is_read,
                                iconBg: iconBg,
                                icon: icon,
                                badgeBg: badgeBg,
                                categoryName: categoryName,
                                actionUrl: n.action_url || (n.building_id ? `/quan-ly-toa-nha/${n.building_id}` : null)
                            };
                        });
                        renderNotifications();
                    }
                }
            } catch (error) {
                console.error("Lỗi khi fetch thông báo:", error);
            }
        }
        
        function getNotificationsData() {
            return appNotifications; 
        }

        function saveNotificationsData(data) {
            appNotifications = data;
            renderNotifications();
        }

        async function markAllRead() {
            try {
                const res = await fetch('/api/notifications/read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                if (res.ok) {
                    appNotifications.forEach(n => n.isRead = true);
                    renderNotifications();
                }
            } catch (e) { console.error(e); }
        }

        function renderNotifications() {
            const list = getNotificationsData();

            const container = document.getElementById('notifications-list-container');
            const overviewContainer = document.getElementById('overview-recent-notifs');
            const topbarList = document.getElementById('topbar-notif-list');

            // Count badges
            const unreadCount = list.filter(n => !n.isRead).length;
            const favoriteCount = list.filter(n => n.type === 'favorite').length;
            const messageCount = list.filter(n => n.type === 'message').length;

            const sidebarBadge = document.getElementById('sidebar-notif-badge');
            const topbarBadge = document.getElementById('topbar-notif-badge');

            if (sidebarBadge) {
                sidebarBadge.innerText = unreadCount;
                sidebarBadge.style.display = unreadCount > 0 ? 'inline-block' : 'none';
            }
            if (topbarBadge) {
                topbarBadge.innerText = unreadCount;
                topbarBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
            }

            const bAll = document.getElementById('badge-count-all');
            const bFavorite = document.getElementById('badge-count-favorite');
            const bMessage = document.getElementById('badge-count-message');
            const bUR = document.getElementById('badge-count-unread');

            if (bAll) bAll.innerText = list.length;
            if (bFavorite) bFavorite.innerText = favoriteCount;
            if (bMessage) bMessage.innerText = messageCount;
            if (bUR) bUR.innerText = unreadCount;

            // Render Overview Top 4 recent notifications
            if (overviewContainer) {
                const recent = list.slice(0, 4);
                if (recent.length === 0) {
                    overviewContainer.innerHTML = `<p class="col-span-2 text-slate-400 py-4 text-center">Không có thông báo mới.</p>`;
                } else {
                    overviewContainer.innerHTML = recent.map(n => {
                        let badgeHtml = `<span class="px-2 py-0.5 rounded-full ${n.badgeBg} font-bold text-[9px] border">${n.categoryName}</span>`;
                        if (n.type === 'favorite') {
                            badgeHtml = `<span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9px]"><i class="fa-solid fa-heart mr-1"></i>Yêu thích</span>`;
                        } else if (n.type === 'message') {
                            badgeHtml = `<span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-bold text-[9px]"><i class="fa-solid fa-comment-dots mr-1"></i>Tin nhắn mới</span>`;
                        }

                        return `
                            <div class="p-3 rounded-xl border ${n.isRead ? 'bg-slate-50/60 border-slate-200/80' : 'bg-white border-brand-200 shadow-xs'} flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg ${n.iconBg} flex items-center justify-center shrink-0 text-sm">
                                    <i class="fa-solid ${n.icon}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        ${badgeHtml}
                                        <span class="text-[10px] text-slate-400">${n.time}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-xs mt-1 truncate">${n.title}</h4>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">${(n.changes && n.changes.length > 0) ? ('Thay đổi: ' + n.changes.join(' • ')) : n.content}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }

            // Render Topbar Dropdown list
            if (topbarList) {
                if (list.length === 0) {
                    topbarList.innerHTML = `<div class="p-4 text-center text-slate-400">Không có thông báo.</div>`;
                } else {
                    topbarList.innerHTML = list.slice(0, 5).map(n => {
                        let badgeHtml = `<span class="px-1.5 py-0.2 rounded-full ${n.badgeBg} font-bold text-[9px] border">${n.categoryName}</span>`;
                        if (n.type === 'favorite') {
                            badgeHtml = `<span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9px]">Yêu thích</span>`;
                        } else if (n.type === 'message') {
                            badgeHtml = `<span class="px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-bold text-[9px]">Tin nhắn</span>`;
                        }

                        return `
                            <div onclick="markNotifAsRead('${n.id}'); switchTab('notifications');" class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3 ${!n.isRead ? 'bg-rose-50/30' : ''}">
                                <div class="w-7 h-7 rounded-lg ${n.iconBg} flex items-center justify-center shrink-0 text-xs mt-0.5">
                                    <i class="fa-solid ${n.icon}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        ${badgeHtml}
                                        <span class="text-[10px] text-slate-400">${n.time}</span>
                                    </div>
                                    <p class="font-bold text-slate-900 text-xs truncate mt-0.5 ${!n.isRead ? 'text-slate-900 font-extrabold' : 'text-slate-700'}">${n.title}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">${(n.changes && n.changes.length > 0) ? ('Thay đổi: ' + n.changes.join(' • ')) : n.content}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }

            // Filter for Main Notifications Tab
            if (container) {
                let filtered = [...list];
                if (currentNotifFilter === 'favorite') {
                    filtered = filtered.filter(n => n.type === 'favorite');
                } else if (currentNotifFilter === 'message') {
                    filtered = filtered.filter(n => n.type === 'message');
                } else if (currentNotifFilter === 'unread') {
                    filtered = filtered.filter(n => !n.isRead);
                }

                if (filtered.length === 0) {
                    container.innerHTML = `
                        <div class="p-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                            <i class="fa-solid fa-bell-slash text-4xl mb-3 text-slate-300 block"></i>
                            <p class="font-bold text-slate-700 text-sm">Không có thông báo nào trong mục này</p>
                            <p class="text-xs text-slate-400 mt-1">Các thông báo mới từ hệ thống sẽ hiển thị tại đây.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = filtered.map(n => {
                    let badgeHtml = `<span class="px-2.5 py-0.5 rounded-full ${n.badgeBg} font-bold text-[10px] border shadow-2xs">${n.categoryName}</span>`;
                    if (n.type === 'favorite') {
                        badgeHtml = `<span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[10px] shadow-2xs"><i class="fa-solid fa-heart mr-1"></i>Thông báo yêu thích</span>`;
                    } else if (n.type === 'message') {
                        badgeHtml = `<span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-bold text-[10px] shadow-2xs"><i class="fa-solid fa-comment-dots mr-1"></i>Tin nhắn mới</span>`;
                    }

                    let contentBodyHtml = `<p class="text-xs text-slate-600 mt-1.5 leading-relaxed whitespace-pre-line">${n.content}</p>`;
                    if (n.changes && n.changes.length > 0) {
                        const introText = n.content.includes('\n\n📋') ? n.content.split('\n\n📋')[0] : n.content;
                        contentBodyHtml = `
                            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">${introText}</p>
                            <div class="mt-2.5 p-3 rounded-xl bg-amber-50/80 border border-amber-200/90 text-slate-800">
                                <div class="text-[11px] font-extrabold text-amber-900 mb-2 flex items-center gap-1.5 uppercase tracking-wide">
                                    <i class="fa-solid fa-pen-to-square text-amber-600"></i> Các nội dung đã thay đổi (${n.changes.length}):
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                    ${n.changes.map(c => `
                                        <div class="flex items-start gap-2 bg-white/95 px-3 py-2 rounded-lg border border-amber-200/70 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                                            <span class="text-slate-800 font-semibold leading-relaxed">${c}</span>
                                        </div>
                                    `).join('')}
                                </div>
                                <p class="text-[11px] text-amber-800/80 italic mt-2">Vui lòng kiểm tra lại thông tin chi tiết trên hệ thống.</p>
                            </div>
                        `;
                    }

                    return `
                        <div class="p-4 rounded-2xl ${n.isRead ? 'bg-white border-slate-200' : 'bg-gradient-to-r from-slate-50 to-rose-50/30 border-rose-200 shadow-xs'} border shadow-sm flex items-start gap-4 hover:border-slate-300 transition-all">
                            <div class="w-10 h-10 rounded-xl ${n.iconBg} flex items-center justify-center shrink-0 text-lg shadow-xs mt-0.5">
                                <i class="fa-solid ${n.icon}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        ${badgeHtml}
                                        <h4 class="font-extrabold text-slate-900 text-sm ${!n.isRead ? 'text-slate-900' : 'text-slate-800'}">${n.title}</h4>
                                    </div>
                                    <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i> ${n.time}
                                    </span>
                                </div>
                                ${contentBodyHtml}
                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        ${n.actionUrl ? `
                                            <a href="${n.actionUrl}" class="px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-[11px] transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem tòa nhà
                                            </a>
                                        ` : ''}
                                        ${!n.isRead ? `
                                            <button onclick="markNotifAsRead('${n.id}')" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] transition-colors">
                                                <i class="fa-solid fa-check mr-1"></i> Đánh dấu đã đọc
                                            </button>
                                        ` : `
                                            <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1"><i class="fa-solid fa-check-double text-emerald-500"></i> Đã đọc</span>
                                        `}
                                    </div>
                                    <button onclick="deleteNotif('${n.id}')" class="text-slate-400 hover:text-rose-600 text-xs font-semibold transition-colors">
                                        <i class="fa-solid fa-xmark"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        function filterNotifs(type) {
            currentNotifFilter = type;
            document.querySelectorAll('.notif-filter-btn').forEach(btn => {
                btn.classList.remove('bg-slate-900', 'text-white');
                btn.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-700');
            });
            const activeBtn = document.getElementById('notif-filter-' + type);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-700');
                activeBtn.classList.add('bg-slate-900', 'text-white');
            }
            renderNotifications();
        }

        async function markNotifAsRead(id) {
            const list = getNotificationsData();
            const target = list.find(n => n.id === id);
            if (target) {
                target.isRead = true;
                saveNotificationsData(list);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    await fetch(`/api/notifications/${id}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    console.error('Lỗi đánh dấu đã đọc:', e);
                }
            }
        }

        async function markAllNotifsAsRead() {
            const list = getNotificationsData();
            list.forEach(n => n.isRead = true);
            saveNotificationsData(list);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                await fetch('/api/notifications/read', { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    } 
                });
            } catch (e) { console.error(e); }
        }

        async function deleteNotif(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn thông báo này khỏi hệ thống?')) {
                return;
            }

            // Cập nhật giao diện ngay lập tức (Optimistic UI)
            let list = getNotificationsData();
            list = list.filter(n => n.id !== id);
            saveNotificationsData(list);

            // Gửi request xóa trực tiếp trong MongoDB database
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/api/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!data.success) {
                    console.error('Lỗi xóa thông báo:', data.message);
                    fetchNotifications();
                }
            } catch (e) {
                console.error('Lỗi kết nối khi xóa thông báo:', e);
                fetchNotifications();
            }
        }

        async function clearReadNotifs() {
            const list = getNotificationsData();
            const readNotifs = list.filter(n => n.isRead);
            if (readNotifs.length === 0) {
                alert('Không có thông báo đã đọc nào để xóa.');
                return;
            }

            if (!confirm(`Bạn có chắc chắn muốn xóa toàn bộ ${readNotifs.length} thông báo đã đọc khỏi hệ thống?`)) {
                return;
            }

            // Cập nhật giao diện ngay lập tức
            saveNotificationsData(list.filter(n => !n.isRead));

            // Gửi request xóa các thông báo đã đọc trong MongoDB database
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch('/api/notifications/clear-read', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!data.success) {
                    console.error('Lỗi khi xóa thông báo đã đọc:', data.message);
                    fetchNotifications();
                }
            } catch (e) {
                console.error('Lỗi khi xóa thông báo đã đọc:', e);
                fetchNotifications();
            }
        }

        function toggleNotificationDropdown(e) {
            if (e) e.stopPropagation();
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.classList.toggle('hidden');
        }

        document.addEventListener('click', (e) => {
            const dd = document.getElementById('notif-dropdown');
            if (dd && !dd.classList.contains('hidden')) {
                const btn = e.target.closest('button[onclick*="toggleNotificationDropdown"]');
                const menu = e.target.closest('#notif-dropdown');
                if (!btn && !menu) {
                    dd.classList.add('hidden');
                }
            }
        });

        // Các bài đăng chờ duyệt mẫu ban đầu
        const defaultPendingPosts = [];

        // Hiển thị danh sách bài đăng chờ phê duyệt (mẫu + bài đăng mới từ localStorage)
        function renderHistoryPosts() {
            const tbody = document.getElementById('history-posts-tbody');
            if (!tbody) return;

            let allHistory = []; // Lấy từ API: await fetch("/api/posts/history")

            if (currentHistoryFilter === 'approved') {
                allHistory = allHistory.filter(item => item.status === 'approved');
            } else if (currentHistoryFilter === 'pending') {
                allHistory = allHistory.filter(item => item.status === 'pending');
            }

            if (allHistory.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                            Không tìm thấy bài đăng nào trong lịch sử.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = allHistory.map(post => {
                const isApproved = (post.status === 'approved');
                const badge = isApproved
                    ? `<span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Đã được phê duyệt
                       </span>`
                    : `<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-clock animate-spin"></i> Đang chờ phê duyệt
                       </span>`;

                const action = isApproved
                    ? `<button class="px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition-colors">
                        <i class="fa-solid fa-arrows-rotate mr-1"></i> Đăng lại phòng
                       </button>`
                    : `<button onclick="cancelPendingPost('${post.id}')" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">
                        <i class="fa-solid fa-ban mr-1"></i> Hủy đăng
                       </button>`;

                return `
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 font-bold text-slate-900">${post.id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${post.image}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs">
                                <div>
                                    <p class="font-bold text-slate-900">${post.title}</p>
                                    ${post.location ? `<p class="text-[10px] text-slate-400">${post.location}</p>` : ''}
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-brand-600">${post.price}</td>
                        <td class="p-4">${badge}</td>
                        <td class="p-4 text-slate-500 font-medium">${post.date}</td>
                        <td class="p-4 text-right">${action}</td>
                    </tr>
                `;
            }).join('');
        }

        function filterHistoryPosts(filterType) {
            currentHistoryFilter = filterType;

            document.querySelectorAll('.hist-filter-btn').forEach(btn => {
                btn.classList.remove('bg-slate-900', 'text-white');
                btn.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-600');
            });

            const activeBtn = document.getElementById('hist-filter-' + filterType);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-600');
                activeBtn.classList.add('bg-slate-900', 'text-white');
            }

            fetchHistoryPosts();
        }

        // Hủy bài đăng chờ duyệt
        async function cancelPendingPost(postId) {
            if (!confirm(`Bạn có chắc chắn muốn hủy đăng bài này không?`)) return;
            try {
                const res = await fetch(`/api/posts/${postId}/cancel`, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã hủy bài đăng thành công!');
                } else {
                    alert(data.message || 'Không thể hủy bài đăng!');
                }
            } catch (err) {
                console.error("Lỗi hủy bài:", err);
            }
            fetchPendingPosts();
            fetchHistoryPosts();
        }

        const B_API_BASE = 'https://esgoo.net/api-tinhthanh';
        let b_provincesList = [];
        let b_currentDistrictsList = [];
        let b_currentWardsList = [];
        
        async function b_fetchProvinces() {
            try {
                const res = await fetch(`${B_API_BASE}/1/0.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    b_provincesList = json.data.map(p => ({ code: p.id, name: p.full_name || p.name }));
                    document.getElementById('b_form-province-text').innerText = 'Chọn Tỉnh / Thành phố';
                }
            } catch (err) {
                document.getElementById('b_form-province-text').innerText = 'Không thể tải dữ liệu';
            }
        }

        async function b_fetchDistricts(provinceCode) {
            if (!provinceCode) return;
            try {
                const res = await fetch(`${B_API_BASE}/2/${provinceCode}.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    b_currentDistrictsList = json.data.map(d => ({ code: d.id, name: d.full_name || d.name }));
                    document.getElementById('b_form-district-text').innerText = 'Chọn Quận / Huyện';
                    document.getElementById('b_select-district').value = '';
                    b_resetWard(); 
                }
            } catch (err) {}
        }

        async function b_fetchWards(districtCode) {
            if (!districtCode) return;
            try {
                const res = await fetch(`${B_API_BASE}/3/${districtCode}.htm`);
                const json = await res.json();
                if(json.error === 0) {
                    b_currentWardsList = json.data.map(w => ({ code: w.id, name: w.full_name || w.name }));
                    document.getElementById('b_form-ward-text').innerText = 'Chọn Phường / Xã';
                    document.getElementById('b_select-ward').value = '';
                }
            } catch (err) {}
        }

        function b_resetWard() {
            document.getElementById('b_select-ward').value = '';
            document.getElementById('b_form-ward-text').innerText = 'Chọn Phường / Xã';
            b_currentWardsList = [];
        }

        function b_removeVietnameseTones(str) {
            if (!str) return '';
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase();
        }

        // --- Province ---
        let b_isCustomProvince = false;
        function b_toggleCustomProvinceMode() {
            b_isCustomProvince = !b_isCustomProvince;
            const selectWrapper = document.getElementById('b_province-select-wrapper');
            const customWrapper = document.getElementById('b_province-custom-wrapper');
            const toggleText = document.getElementById('b_toggle-custom-text');
            const hiddenInput = document.getElementById('b_select-province');
            const customInput = document.getElementById('b_custom-province-input');

            if (b_isCustomProvince) {
                selectWrapper.classList.add('hidden');
                customWrapper.classList.remove('hidden');
                toggleText.innerText = 'Chọn từ danh sách';
                hiddenInput.value = customInput.value.trim();
            } else {
                selectWrapper.classList.remove('hidden');
                customWrapper.classList.add('hidden');
                toggleText.innerText = 'Tự nhập khác';
                hiddenInput.value = document.getElementById('b_form-province-text').innerText !== 'Chọn Tỉnh / Thành phố' ? document.getElementById('b_form-province-text').innerText : '';
            }
        }

        function b_toggleProvinceDropdown(e) {
            if(e) e.stopPropagation();
            const panel = document.getElementById('b_form-province-panel');
            const arrow = document.getElementById('b_form-province-arrow');
            
            document.getElementById('b_form-district-panel')?.classList.add('hidden');
            document.getElementById('b_form-ward-panel')?.classList.add('hidden');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                if(arrow) arrow.style.transform = 'rotate(180deg)';
                b_renderProvinces();
            } else {
                panel.classList.add('hidden');
                if(arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function b_renderProvinces(filter = '') {
            const container = document.getElementById('b_form-provinces-list');
            const cleanKeyword = b_removeVietnameseTones(filter);
            const filtered = b_provincesList.filter(p => b_removeVietnameseTones(p.name).includes(cleanKeyword));
            
            if(filtered.length === 0) {
                container.innerHTML = '<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy kết quả</div>';
                return;
            }
            
            let html = '';
            filtered.forEach(p => {
                html += `<div onclick="b_selectProvince('${p.name}', '${p.code}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-xs font-semibold text-slate-800">${p.name}</div>`;
            });
            container.innerHTML = html;
        }

        function b_filterProvinces() {
            b_renderProvinces(document.getElementById('b_form-province-search').value);
        }

        function b_selectProvince(name, code) {
            document.getElementById('b_select-province').value = name;
            document.getElementById('b_form-province-text').innerText = name;
            document.getElementById('b_form-province-panel').classList.add('hidden');
            document.getElementById('b_form-province-arrow').style.transform = 'rotate(0deg)';
            b_fetchDistricts(code);
        }

        function b_onCustomProvinceInput() {
            document.getElementById('b_select-province').value = document.getElementById('b_custom-province-input').value.trim();
        }

        // --- District ---
        let b_isCustomDistrict = false;
        function b_toggleCustomDistrictMode() {
            b_isCustomDistrict = !b_isCustomDistrict;
            const selectWrapper = document.getElementById('b_district-select-wrapper');
            const customWrapper = document.getElementById('b_district-custom-wrapper');
            const toggleText = document.getElementById('b_toggle-custom-district-text');
            const hiddenInput = document.getElementById('b_select-district');
            const customInput = document.getElementById('b_custom-district-input');

            if (b_isCustomDistrict) {
                selectWrapper.classList.add('hidden');
                customWrapper.classList.remove('hidden');
                toggleText.innerText = 'Chọn từ danh sách';
                hiddenInput.value = customInput.value.trim();
            } else {
                selectWrapper.classList.remove('hidden');
                customWrapper.classList.add('hidden');
                toggleText.innerText = 'Tự nhập khác';
                hiddenInput.value = document.getElementById('b_form-district-text').innerText !== 'Chọn Quận / Huyện' ? document.getElementById('b_form-district-text').innerText : '';
            }
        }

        function b_toggleDistrictDropdown(e) {
            if(e) e.stopPropagation();
            const panel = document.getElementById('b_form-district-panel');
            const arrow = document.getElementById('b_form-district-arrow');
            
            document.getElementById('b_form-province-panel')?.classList.add('hidden');
            document.getElementById('b_form-ward-panel')?.classList.add('hidden');

            if (panel.classList.contains('hidden')) {
                if (b_currentDistrictsList.length === 0) {
                    alert('Vui lòng chọn Tỉnh / Thành phố trước');
                    return;
                }
                panel.classList.remove('hidden');
                if(arrow) arrow.style.transform = 'rotate(180deg)';
                b_renderDistricts();
            } else {
                panel.classList.add('hidden');
                if(arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function b_renderDistricts(filter = '') {
            const container = document.getElementById('b_form-districts-list');
            const cleanKeyword = b_removeVietnameseTones(filter);
            const filtered = b_currentDistrictsList.filter(p => b_removeVietnameseTones(p.name).includes(cleanKeyword));
            
            if(filtered.length === 0) {
                container.innerHTML = '<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy kết quả</div>';
                return;
            }
            
            let html = '';
            filtered.forEach(p => {
                html += `<div onclick="b_selectDistrict('${p.name}', '${p.code}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-xs font-semibold text-slate-800">${p.name}</div>`;
            });
            container.innerHTML = html;
        }

        function b_filterDistricts() {
            b_renderDistricts(document.getElementById('b_form-district-search').value);
        }

        function b_selectDistrict(name, code) {
            document.getElementById('b_select-district').value = name;
            document.getElementById('b_form-district-text').innerText = name;
            document.getElementById('b_form-district-panel').classList.add('hidden');
            document.getElementById('b_form-district-arrow').style.transform = 'rotate(0deg)';
            b_fetchWards(code);
        }

        function b_onCustomDistrictInput() {
            document.getElementById('b_select-district').value = document.getElementById('b_custom-district-input').value.trim();
        }

        // --- Ward ---
        let b_isCustomWard = false;
        function b_toggleCustomWardMode() {
            b_isCustomWard = !b_isCustomWard;
            const selectWrapper = document.getElementById('b_ward-select-wrapper');
            const customWrapper = document.getElementById('b_ward-custom-wrapper');
            const toggleText = document.getElementById('b_toggle-custom-ward-text');
            const hiddenInput = document.getElementById('b_select-ward');
            const customInput = document.getElementById('b_custom-ward-input');

            if (b_isCustomWard) {
                selectWrapper.classList.add('hidden');
                customWrapper.classList.remove('hidden');
                toggleText.innerText = 'Chọn từ danh sách';
                hiddenInput.value = customInput.value.trim();
            } else {
                selectWrapper.classList.remove('hidden');
                customWrapper.classList.add('hidden');
                toggleText.innerText = 'Tự nhập khác';
                hiddenInput.value = document.getElementById('b_form-ward-text').innerText !== 'Chọn Phường / Xã' ? document.getElementById('b_form-ward-text').innerText : '';
            }
        }

        function b_toggleWardDropdown(e) {
            if(e) e.stopPropagation();
            const panel = document.getElementById('b_form-ward-panel');
            const arrow = document.getElementById('b_form-ward-arrow');
            
            document.getElementById('b_form-province-panel')?.classList.add('hidden');
            document.getElementById('b_form-district-panel')?.classList.add('hidden');

            if (panel.classList.contains('hidden')) {
                if (b_currentWardsList.length === 0) {
                    alert('Vui lòng chọn Quận / Huyện trước');
                    return;
                }
                panel.classList.remove('hidden');
                if(arrow) arrow.style.transform = 'rotate(180deg)';
                b_renderWards();
            } else {
                panel.classList.add('hidden');
                if(arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function b_renderWards(filter = '') {
            const container = document.getElementById('b_form-wards-list');
            const cleanKeyword = b_removeVietnameseTones(filter);
            const filtered = b_currentWardsList.filter(p => b_removeVietnameseTones(p.name).includes(cleanKeyword));
            
            if(filtered.length === 0) {
                container.innerHTML = '<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy kết quả</div>';
                return;
            }
            
            let html = '';
            filtered.forEach(p => {
                html += `<div onclick="b_selectWard('${p.name}', '${p.code}')" class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-xs font-semibold text-slate-800">${p.name}</div>`;
            });
            container.innerHTML = html;
        }

        function b_filterWards() {
            b_renderWards(document.getElementById('b_form-ward-search').value);
        }

        function b_selectWard(name, code) {
            document.getElementById('b_select-ward').value = name;
            document.getElementById('b_form-ward-text').innerText = name;
            document.getElementById('b_form-ward-panel').classList.add('hidden');
            document.getElementById('b_form-ward-arrow').style.transform = 'rotate(0deg)';
        }

        function b_onCustomWardInput() {
            document.getElementById('b_select-ward').value = document.getElementById('b_custom-ward-input').value.trim();
        }

        document.addEventListener('click', function(e) {
            const pPanel = document.getElementById('b_form-province-panel');
            const dPanel = document.getElementById('b_form-district-panel');
            const wPanel = document.getElementById('b_form-ward-panel');
            
            if (pPanel && !pPanel.contains(e.target) && e.target.id !== 'b_form-province-text' && e.target.id !== 'b_form-province-arrow' && !e.target.closest('button[onclick="b_toggleProvinceDropdown(event)"]')) {
                pPanel.classList.add('hidden');
                const pArrow = document.getElementById('b_form-province-arrow');
                if(pArrow) pArrow.style.transform = 'rotate(0deg)';
            }
            if (dPanel && !dPanel.contains(e.target) && e.target.id !== 'b_form-district-text' && e.target.id !== 'b_form-district-arrow' && !e.target.closest('button[onclick="b_toggleDistrictDropdown(event)"]')) {
                dPanel.classList.add('hidden');
                const dArrow = document.getElementById('b_form-district-arrow');
                if(dArrow) dArrow.style.transform = 'rotate(0deg)';
            }
            if (wPanel && !wPanel.contains(e.target) && e.target.id !== 'b_form-ward-text' && e.target.id !== 'b_form-ward-arrow' && !e.target.closest('button[onclick="b_toggleWardDropdown(event)"]')) {
                wPanel.classList.add('hidden');
                const wArrow = document.getElementById('b_form-ward-arrow');
                if(wArrow) wArrow.style.transform = 'rotate(0deg)';
            }
        });
        
        function previewBuildingImage(event) {
            const file = event.target.files[0];
            const placeholder = document.getElementById('b_image_placeholder');
            const preview = document.getElementById('b_image_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                preview.src = '';
            }
        }

        // Chuyển đổi giữa các Tab Menu A, B, C, D, F, G
        function switchTab(tabId) {
            // Ẩn toàn bộ nội dung tab
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Gỡ active trên các nút tab menu dọc
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-brand-50', 'text-brand-600', 'border-brand-200/60', 'font-bold');
                btn.classList.add('border-transparent', 'text-slate-600', 'font-semibold');
            });

            // Kích hoạt tab được bấm
            const targetContent = document.getElementById('content-' + tabId);
            const targetBtn = document.getElementById('tab-' + tabId);

            if (targetContent) targetContent.classList.remove('hidden');
            if (targetBtn) {
                targetBtn.classList.remove('border-transparent', 'text-slate-600', 'font-semibold');
                targetBtn.classList.add('bg-brand-50', 'text-brand-600', 'border-brand-200/60', 'font-bold');
            }

            // Cap nhat tieu de theo tab
            const headingMap = {
                'overview': 'Tổng Quan Hệ Thống',
                'active-posts': 'Tin Đang Hiển Thị',
                'pending-posts': 'Bài Đăng Chờ Phê Duyệt',
                'history-posts': 'Lịch Sử Đăng Tin',
                'notifications': 'Thông Báo Hệ Thống',
                'buildings': '{{ $isSubAccount ? "Danh Sách Tòa Nhà Được Giao Phụ Trách" : "Quản Lý Tòa Nhà & Khu Đô Thị" }}',
                'moderation': 'Trung Tâm Kiểm Duyệt Tin Đăng RentHome',
                'sub-accounts': 'Quản Lý Tài Khoản Con & Phân Quyền Tòa Nhà'
            };
            const pageHeading = document.getElementById('page-heading');
            if (pageHeading && headingMap[tabId]) {
                pageHeading.innerText = headingMap[tabId];
            }

            // GỌI API LẤY DỮ LIỆU TƯƠNG ỨNG VỚI TAB
            if (tabId === 'overview') {
                fetchOverviewStats();
                fetchActivePosts(); // Tab tổng quan chứa cả bảng danh sách hiển thị
            }
            if (tabId === 'active-posts') {
                fetchActivePosts();
            }
            if (tabId === 'pending-posts') {
                fetchPendingPosts();
            }
            if (tabId === 'history-posts') {
                fetchHistoryPosts();
            }
            if (tabId === 'notifications' || tabId === 'overview') {
                fetchNotifications();
            }
            if (tabId === 'buildings') {
                fetchBuildings();
            }
            if (tabId === 'sub-accounts') {
                fetchSubAccounts();
            }

            // Đồng bộ tab vào URL để khi reload không bị mất tab hiện tại
            if (window.history && window.history.replaceState) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('tab', tabId);
                window.history.replaceState(null, '', currentUrl.toString());
            }

            // Tu dong dong menu mobile khi click tab
            const sidebar = document.getElementById('sidebar-menu');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth < 768) {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
            }
        }

        // Tự động load dữ liệu ngay khi vừa F5
        window.addEventListener('DOMContentLoaded', () => {
            b_fetchProvinces(); // Load data for locations in modal
            const urlParams = new URLSearchParams(window.location.search);
            // Nếu không có param trên URL, mặc định mở tab overview
            const tabParam = urlParams.get('tab') || 'overview'; 
            
            // Chạy trigger giả lập việc user click vào tab để load dữ liệu
            switchTab(tabParam);
            
            // Gọi chạy ngầm các tab khác để lấy số lượng (Badge đếm số)
            if(tabParam !== 'pending-posts') fetchPendingPosts();
            if(tabParam !== 'history-posts') fetchHistoryPosts();
        });
        // Toggle Sidebar Navigation on Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-menu');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            if (overlay) {
                overlay.classList.toggle('hidden');
            }
        }

        // Modal Tạo Tòa Nhà
        function openCreateBuildingModal() {
            document.getElementById('createBuildingModal').classList.remove('hidden');
            document.getElementById('createBuildingModal').classList.add('flex');
        }

        function closeCreateBuildingModal() {
            document.getElementById('createBuildingModal').classList.add('hidden');
            document.getElementById('createBuildingModal').classList.remove('flex');
        }

        async function submitCreateBuilding(event) {
            if(event) event.preventDefault();
            
            const form = document.getElementById('formCreateBuilding');
            const formData = new FormData(form);
            
            const btn = document.getElementById('btnSubmitBuilding');
            const originalBtnText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            
            try {
                const response = await fetch('/api/buildings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    alert('Tạo tòa nhà thành công!');
                    closeCreateBuildingModal();
                    form.reset();
                    
                    // Reset image preview
                    document.getElementById('b_image_preview').classList.add('hidden');
                    document.getElementById('b_image_placeholder').classList.remove('hidden');
                    document.getElementById('b_image_preview').src = '';
                    
                    // Reset dropdown texts
                    document.getElementById('b_form-province-text').innerText = 'Chọn Tỉnh / Thành phố';
                    document.getElementById('b_form-district-text').innerText = 'Chọn Quận / Huyện';
                    document.getElementById('b_form-ward-text').innerText = 'Chọn Phường / Xã';
                    
                    fetchBuildings();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể tạo tòa nhà'));
                }
            } catch (error) {
                console.error('Lỗi khi tạo tòa nhà:', error);
                alert('Đã xảy ra lỗi kết nối!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
            }
        }

        async function fetchBuildings() {
            const container = document.querySelector('#content-buildings .grid');
            if (!container) return;
            
            container.innerHTML = '<div class="col-span-full text-center py-10"><i class="fa-solid fa-spinner fa-spin text-2xl text-purple-500"></i><p class="mt-2 text-xs text-slate-500">Đang tải danh sách tòa nhà...</p></div>';

            try {
                const response = await fetch('/api/buildings');
                const data = await response.json();
                
                if (data && data.buildings && data.buildings.length > 0) {
                    container.innerHTML = '';
                    data.buildings.forEach(building => {
                        let imgUrl = building.display_image || building.image || '';
                        if (!imgUrl) {
                            imgUrl = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80';
                        } else {
                            imgUrl = String(imgUrl).trim();
                            imgUrl = imgUrl.replace(/\/storage\/\/storage\//g, '/storage/').replace(/\/storage\/storage\//g, '/storage/');
                        }
                        const addrParts = [];
                        if (building.address_detail) addrParts.push(building.address_detail);
                        if (building.ward) addrParts.push(building.ward);
                        if (building.district) addrParts.push(building.district);
                        if (building.province) addrParts.push(building.province);
                        const addr = addrParts.join(', ');
                        
                        const safeName = building.name ? building.name.replace(/'/g, "\\'") : '';
                        const safeAddr = addr.replace(/'/g, "\\'");
                        
                        const card = `
                            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col">
                                <img src="${imgUrl}" alt="${safeName}" class="w-full h-40 object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80';">
                                <div class="p-5 flex-1 flex flex-col">
                                    <h4 class="font-bold text-slate-900 text-sm mb-1 line-clamp-1">${building.name}</h4>
                                    <p class="text-[11px] text-slate-500 flex items-start gap-1.5 mb-3 line-clamp-2">
                                        <i class="fa-solid fa-location-dot mt-0.5 text-slate-400"></i>
                                        <span>${addr}</span>
                                    </p>
                                    <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-xs font-bold text-purple-600"><i class="fa-solid fa-door-open mr-1"></i>${building.total_rooms || 0} phòng</span>
                                        <a href="/quan-ly-toa-nha/${building.id || building._id}?ref=buildings" class="px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 font-semibold text-[11px] transition-colors inline-block text-center">
                                            Quản lý phòng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', card);
                    });
                } else {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-10">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-building text-2xl text-slate-400"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Chưa có tòa nhà nào</p>
                            <p class="text-xs text-slate-500 mt-1">Bấm "Tạo Tòa Nhà Mới" để bắt đầu quản lý</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error fetching buildings:', error);
                container.innerHTML = '<div class="col-span-full text-center py-10 text-rose-500 text-sm font-semibold">Lỗi tải dữ liệu. Vui lòng thử lại sau.</div>';
            }
        }

        // ==================== API FETCH: THỐNG KÊ TỔNG QUAN ====================
function fetchOverviewStats() {
    fetch('/api/user/posts/stats')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.stats) {
                if(document.getElementById('total-views')) document.getElementById('total-views').innerText = data.stats.total_views || 0;
                if(document.getElementById('total-likes')) document.getElementById('total-likes').innerText = data.stats.total_likes || 0;
                if(document.getElementById('active-posts-count')) document.getElementById('active-posts-count').innerText = data.stats.active_posts_count || 0;
                if(document.getElementById('total-buildings-count')) document.getElementById('total-buildings-count').innerText = data.stats.total_buildings_count || 0;
            }
        })
        .catch(err => console.error("Lỗi lấy thống kê:", err));
}

// ==================== API FETCH: TIN ĐANG HIỂN THỊ (ACTIVE) ====================
function fetchActivePosts() {
    fetch('/api/user/posts?status=active')
        .then(res => res.json())
        .then(data => {
            const tbodyOverview = document.getElementById('active-posts-tbody');
            const tbodyTab = document.getElementById('active-posts-tab-tbody');
            const heading = document.getElementById('active-posts-heading');
            
            if (heading && data.posts) {
                heading.innerText = `Danh sách tin đang hiển thị (${data.posts.length})`;
            }
            
            if (!tbodyOverview && !tbodyTab) return;
            
            let html = '';
            if (!data.posts || data.posts.length === 0) {
                html = `<tr><td colspan="6" class="p-8 text-center text-slate-400 font-medium"><i class="fa-solid fa-box-open text-3xl mb-2 block"></i>Không có bài đăng nào đang hiển thị.</td></tr>`;
            } else {
                window.allPosts = window.allPosts || {};
                html = data.posts.map(post => {
                window.allPosts[post.id || post._id] = post;
                const imgUrl = (post.display_image || '').replace(/\/storage\/\/storage\//g, '/storage/').replace(/\/storage\/storage\//g, '/storage/') || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';
                const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' đ';
                const isRented = Boolean(post.is_rented === true || post.status === 'đã cho thuê' || post.status === 'rented' || post.status === 'dang_thue');
                return `
                    <tr class="transition-all ${isRented ? 'opacity-50 hover:opacity-85 bg-slate-50/70 border-l-4 border-l-amber-500' : 'hover:bg-slate-50'}">
                        <td class="p-4 font-bold text-slate-900">${post.id || post._id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs ${isRented ? 'grayscale-[25%]' : ''}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';">
                                <div>
                                    <p class="font-bold text-slate-900 flex items-center gap-2">
                                        ${post.title}
                                        ${isRented ? '<span class="text-[9px] bg-amber-100 text-amber-800 font-extrabold px-1.5 py-0.5 rounded border border-amber-200 uppercase tracking-wider">Đang thuê</span>' : ''}
                                    </p>
                                    <p class="text-[10px] text-slate-400">${post.location || post.address || 'Đang cập nhật'}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-bold ${isRented ? 'text-slate-600' : 'text-brand-600'}">${price}</td>
                        <td class="p-4">
                            ${isRented ? `
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px] inline-flex items-center gap-1.5 border border-amber-200 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 inline-block animate-pulse"></span> Đang thuê
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Đang hiển thị
                                </span>
                            `}
                        </td>
                        <td class="p-4 text-center font-bold text-slate-600">${post.views || 0}</td>
                        <td class="p-4 text-right">
                            <button onclick="editPost('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg ${isRented ? 'bg-slate-100 hover:bg-slate-200 text-slate-600' : 'bg-blue-50 hover:bg-blue-100 text-blue-600'} font-bold text-xs transition-colors" title="${isRented ? 'Bài đăng đang cho thuê' : 'Cập nhật bài đăng'}">
                                <i class="fa-solid fa-pen mr-1"></i> Cập nhật
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            }
            
            if (tbodyOverview) tbodyOverview.innerHTML = html;
            if (tbodyTab) tbodyTab.innerHTML = html;
        }).catch(err => console.error("Lỗi lấy tin hiển thị:", err));
}

function filterActivePostsTable(query) {
    const term = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#active-posts-tab-tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(term) ? '' : 'none';
    });
}

// ==================== API FETCH: TIN CHỜ PHÊ DUYỆT (PENDING) ====================
function fetchPendingPosts() {
    fetch('/api/user/posts?status=pending')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('pending-posts-tbody');
            const heading = document.getElementById('pending-count-heading');
            const sidebarBadge = document.getElementById('sidebar-pending-count');
            
            const pendingCount = data.posts ? data.posts.length : 0;
            
            if (sidebarBadge) {
                if (pendingCount > 0) {
                    sidebarBadge.innerText = pendingCount;
                    sidebarBadge.classList.remove('hidden');
                } else {
                    sidebarBadge.classList.add('hidden');
                }
            }

            if (!tbody) return;

            if (heading) heading.innerText = `Danh sách bài đăng đang chờ ban quản trị duyệt (${pendingCount})`;

            if (!data.posts || data.posts.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-slate-400 font-medium"><i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>Không có bài đăng nào đang chờ duyệt.</td></tr>`;
                return;
            }

            window.allPosts = window.allPosts || {};

            tbody.innerHTML = data.posts.map(post => {
                window.allPosts[post.id || post._id] = post;
                const imgUrl = (post.display_image || '').replace(/\/storage\/\/storage\//g, '/storage/').replace(/\/storage\/storage\//g, '/storage/') || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';
                const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' đ';
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">${post.id || post._id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';">
                                <div>
                                    <p class="font-bold text-slate-900">${post.title}</p>
                                    <p class="text-[10px] text-slate-400">${post.location || post.address || 'Đang cập nhật'}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-brand-600">${price}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-clock animate-spin"></i> Chờ phê duyệt
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="cancelPendingPost('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">
                                <i class="fa-solid fa-ban mr-1"></i> Hủy đăng
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }).catch(err => console.error("Lỗi lấy tin chờ duyệt:", err));
}

// ==================== API FETCH: LỊCH SỬ (HISTORY) ====================
function fetchHistoryPosts() {
    fetch('/api/user/posts/history')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('history-posts-tbody');
            if (!tbody) return;

            let allHistory = data.posts || [];
            
            // Bộ lọc cục bộ (nếu user bấm các nút lọc trên giao diện)
            if (typeof currentHistoryFilter !== 'undefined') {
                if (currentHistoryFilter === 'approved') allHistory = allHistory.filter(item => item.status === 'active' || item.status === 'approved');
                if (currentHistoryFilter === 'pending') allHistory = allHistory.filter(item => item.status === 'pending');
            }

            if (allHistory.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400 font-medium"><i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>Không có bài đăng nào.</td></tr>`;
                return;
            }

            tbody.innerHTML = allHistory.map(post => {
                const imgUrl = (post.display_image || '').replace(/\/storage\/\/storage\//g, '/storage/').replace(/\/storage\/storage\//g, '/storage/') || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';
                const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' đ';
                const isApproved = (post.status === 'active' || post.status === 'approved');
                
                const badge = isApproved
                    ? `<span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> Đã được phê duyệt</span>`
                    : (post.status === 'rejected' 
                        ? `<span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-xmark text-rose-500"></i> Đã từ chối</span>`
                        : `<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-clock animate-spin"></i> Đang chờ phê duyệt</span>`);

                let action = '';
                if (isApproved) {
                    action = `<button class="px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition-colors">Xem tin</button>`;
                } else if (post.status === 'rejected') {
                    action = `<button onclick="openComplaintModal('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs transition-colors mr-1"><i class="fa-solid fa-reply mr-1"></i>Phản hồi</button>` +
                             `<button onclick="cancelPendingPost('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">Hủy đăng</button>`;
                } else {
                    action = `<button onclick="cancelPendingPost('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">Hủy đăng</button>`;
                }

                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">${post.id || post._id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=300&q=80';">
                                <div>
                                    <p class="font-bold text-slate-900">${post.title}</p>
                                    <p class="text-[10px] text-slate-400">${post.location || post.address || ''}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-brand-600">${price}</td>
                        <td class="p-4">${badge}</td>
                        <td class="p-4 text-slate-500 font-medium">${new Date(post.created_at).toLocaleDateString('vi-VN')}</td>
                        <td class="p-4 text-right">${action}</td>
                    </tr>
                `;
            }).join('');
        }).catch(err => console.error("Lỗi lấy lịch sử:", err));
}

        function editPost(id) {
            if (window.allPosts && window.allPosts[id]) {
                sessionStorage.setItem('edit_post_data', JSON.stringify(window.allPosts[id]));
                window.location.href = "{{ url('dangbai') }}";
            } else {
                alert('Không tìm thấy dữ liệu bài đăng!');
            }
        }

        function openComplaintModal(postId) {
            document.getElementById('complaint-post-id').value = postId;
            document.getElementById('complaint-form').reset();
            const modal = document.getElementById('complaint-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                document.getElementById('complaint-modal-content').classList.remove('scale-95');
                document.getElementById('complaint-modal-content').classList.add('scale-100');
            }, 10);
        }

        function closeComplaintModal() {
            const modal = document.getElementById('complaint-modal');
            document.getElementById('complaint-modal-content').classList.remove('scale-100');
            document.getElementById('complaint-modal-content').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        async function submitComplaint(e) {
            e.preventDefault();
            const form = document.getElementById('complaint-form');
            const formData = new FormData(form);
            const btnSubmit = document.getElementById('btn-submit-complaint');
            
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
            
            try {
                const res = await fetch('/api/complaints', {
                    method: 'POST',
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                const data = await res.json();
                if (res.ok) {
                    alert(data.message || 'Đã gửi phản hồi thành công!');
                    closeComplaintModal();
                } else {
                    alert(data.error || data.message || 'Đã xảy ra lỗi, vui lòng thử lại!');
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối tới máy chủ!');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Gửi phản hồi';
            }
        }
        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('sidebar-menu');
            sidebar.classList.toggle('md:hidden');
            sidebar.classList.toggle('md:flex');
        }

        function toggleDesktopSidebarMini() {
            const sidebar = document.getElementById('sidebar-menu');
            sidebar.classList.toggle('sidebar-mini');
        }

        // Tự động mở menu khi click vào bất kỳ mục nào
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtns = document.querySelectorAll('.tab-btn, .sidebar-btn, .user-card');
            menuBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const sidebar = document.getElementById('sidebar-menu');
                    if(sidebar.classList.contains('sidebar-mini')) {
                        sidebar.classList.remove('sidebar-mini');
                    }
                });
            });
        });

        // ==================== JAVASCRIPT: QUẢN LÝ TÀI KHOẢN CON & PHÂN QUYỀN ====================
        let allSubAccountsList = [];
        let allBuildingsForSubAccounts = [];

        async function fetchSubAccounts() {
            const tbody = document.getElementById('sub-accounts-table-tbody');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-slate-400 font-medium"><i class="fa-solid fa-spinner fa-spin text-xl text-teal-500 mr-2"></i> Đang tải danh sách nhân viên...</td></tr>';

            try {
                const res = await fetch('/api/sub-accounts');
                const data = await res.json();
                allSubAccountsList = data.sub_accounts || [];
                allBuildingsForSubAccounts = data.all_buildings || [];

                // Cập nhật stats
                if (document.getElementById('sub-total-count')) document.getElementById('sub-total-count').innerText = allSubAccountsList.length;
                if (document.getElementById('sub-active-count')) document.getElementById('sub-active-count').innerText = allSubAccountsList.filter(s => !s.is_locked).length;
                if (document.getElementById('sub-locked-count')) document.getElementById('sub-locked-count').innerText = allSubAccountsList.filter(s => s.is_locked).length;

                renderSubAccountsTable(allSubAccountsList);
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-rose-500 font-medium">Lỗi tải danh sách tài khoản con!</td></tr>';
            }
        }

        function renderSubAccountsTable(accounts) {
            const tbody = document.getElementById('sub-accounts-table-tbody');
            if (!tbody) return;

            if (accounts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-slate-400 font-medium"><i class="fa-solid fa-users text-3xl text-slate-300 mb-2 block"></i>Chưa có tài khoản nhân viên nào. Bấm "+ Thêm Nhân Viên Mới" để tạo.</td></tr>';
                return;
            }

            tbody.innerHTML = accounts.map(acc => {
                const accId = String(acc.id || acc._id);
                const name = acc.account_name || acc.name || acc.username || 'Nhân viên';
                const email = acc.email || '';
                const phone = acc.phone || 'Chưa có SĐT';
                const isLocked = acc.is_locked;
                const assignedBuildings = acc.assigned_buildings_details || [];
                
                let buildingBadges = '';
                if (acc.is_all_buildings || !acc.assigned_buildings || acc.assigned_buildings.length === 0 || acc.assigned_buildings.includes('all')) {
                    buildingBadges = `<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200 inline-flex items-center gap-1"><i class="fa-solid fa-globe text-[9px]"></i> Tất cả tòa nhà (${allBuildingsForSubAccounts.length})</span>`;
                } else if (assignedBuildings.length > 0) {
                    buildingBadges = assignedBuildings.map(b => 
                        `<span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-100 mr-1 mb-1 inline-block"><i class="fa-solid fa-building text-[9px] mr-1"></i>${b.name}</span>`
                    ).join('');
                } else {
                    buildingBadges = '<span class="text-slate-400 text-[11px] italic">Chưa được giao tòa nhà nào</span>';
                }

                const statusBadge = isLocked
                    ? '<span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-lock text-rose-500"></i> Đang tạm khóa</span>'
                    : '<span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-emerald-500"></i> Hoạt động</span>';

                const lockBtnIcon = isLocked ? 'fa-lock-open' : 'fa-lock';
                const lockBtnText = isLocked ? 'Mở khóa' : 'Khóa';
                const lockBtnClass = isLocked ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100';

                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">${name}</p>
                                    <p class="text-[10px] text-teal-600 font-semibold">Nhân viên quản lý tòa nhà</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-slate-700">${email}</td>
                        <td class="p-4 font-medium text-slate-700">${phone}</td>
                        <td class="p-4 max-w-xs">${buildingBadges}</td>
                        <td class="p-4">${statusBadge}</td>
                        <td class="p-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <button onclick="openEditSubAccountModal('${accId}')" class="px-2.5 py-1.5 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-bold text-[11px] transition-colors" title="Phân quyền tòa nhà & sửa thông tin">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Phân quyền
                                </button>
                                <button onclick="toggleLockSubAccount('${accId}')" class="px-2.5 py-1.5 rounded-lg ${lockBtnClass} font-bold text-[11px] transition-colors" title="${lockBtnText}">
                                    <i class="fa-solid ${lockBtnIcon} mr-1"></i> ${lockBtnText}
                                </button>
                                <button onclick="deleteSubAccount('${accId}')" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Xóa tài khoản">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function filterSubAccountsTable(query) {
            const q = (query || '').toLowerCase().trim();
            if (!q) {
                renderSubAccountsTable(allSubAccountsList);
                return;
            }
            const filtered = allSubAccountsList.filter(acc => {
                const name = (acc.account_name || acc.name || '').toLowerCase();
                const email = (acc.email || '').toLowerCase();
                const phone = (acc.phone || '').toLowerCase();
                const bNames = (acc.assigned_buildings_details || []).map(b => (b.name || '').toLowerCase()).join(' ');
                return name.includes(q) || email.includes(q) || phone.includes(q) || bNames.includes(q);
            });
            renderSubAccountsTable(filtered);
        }

        function openCreateSubAccountModal() {
            document.getElementById('formCreateSubAccount').reset();
            const container = document.getElementById('sub-create-buildings-checklist');
            
            if (allBuildingsForSubAccounts.length === 0) {
                container.innerHTML = '<p class="text-slate-400 italic text-center py-4">Hệ thống chưa có tòa nhà nào để phân quyền. Vui lòng tạo tòa nhà trước.</p>';
            } else {
                container.innerHTML = allBuildingsForSubAccounts.map(b => {
                    const bId = String(b.id || b._id);
                    return `
                        <label class="flex items-start gap-2.5 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-slate-200">
                            <input type="checkbox" name="assigned_buildings[]" value="${bId}" class="mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900">${b.name}</p>
                                <p class="text-[10px] text-slate-400 truncate">${b.address_detail ? (b.address_detail + ', ') : ''}${b.province || ''}</p>
                            </div>
                        </label>
                    `;
                }).join('');
            }

            document.getElementById('createSubAccountModal').classList.remove('hidden');
            document.getElementById('createSubAccountModal').classList.add('flex');
        }

        function closeCreateSubAccountModal() {
            document.getElementById('createSubAccountModal').classList.add('hidden');
            document.getElementById('createSubAccountModal').classList.remove('flex');
        }

        function selectAllSubBuildings(type) {
            const selector = type === 'create' ? '#sub-create-buildings-checklist input[type="checkbox"]' : '#sub-edit-buildings-checklist input[type="checkbox"]';
            const checkboxes = document.querySelectorAll(selector);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }

        async function submitCreateSubAccount(e) {
            e.preventDefault();
            const form = document.getElementById('formCreateSubAccount');
            const checkboxes = form.querySelectorAll('input[name="assigned_buildings[]"]:checked');
            
            const assigned = (checkboxes.length > 0) ? Array.from(checkboxes).map(cb => cb.value) : ['all'];
            const payload = {
                name: document.getElementById('sub_name').value.trim(),
                phone: document.getElementById('sub_phone').value.trim(),
                email: document.getElementById('sub_email').value.trim(),
                password: document.getElementById('sub_password').value,
                assigned_buildings: assigned
            };

            const btn = document.getElementById('btn-submit-create-sub');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tạo...';

            try {
                const res = await fetch('/api/sub-accounts', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Tạo tài khoản nhân viên thành công!');
                    closeCreateSubAccountModal();
                    fetchSubAccounts();
                } else {
                    let errMsg = data.error || data.message || 'Đã có lỗi xảy ra!';
                    if (data.errors && typeof data.errors === 'object') {
                        errMsg = Object.values(data.errors).flat().join('\n');
                    }
                    alert(errMsg);
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Tạo tài khoản & Phân quyền';
            }
        }

        function openEditSubAccountModal(accId) {
            const acc = allSubAccountsList.find(a => (a.id || a._id) == accId);
            if (!acc) return;

            document.getElementById('edit_sub_id').value = accId;
            document.getElementById('edit_sub_name').value = acc.account_name || acc.name || '';
            document.getElementById('edit_sub_phone').value = acc.phone || '';
            document.getElementById('edit_sub_password').value = '';
            document.getElementById('edit_sub_subtitle').innerText = `Email: ${acc.email} - Phân quyền tòa nhà phụ trách`;

            const assignedIds = (acc.assigned_buildings || []).map(String);
            const container = document.getElementById('sub-edit-buildings-checklist');

            container.innerHTML = allBuildingsForSubAccounts.map(b => {
                const bId = String(b.id || b._id);
                const isChecked = assignedIds.includes(bId) ? 'checked' : '';
                return `
                    <label class="flex items-start gap-2.5 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-slate-200">
                        <input type="checkbox" name="edit_assigned_buildings[]" value="${bId}" ${isChecked} class="mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-900">${b.name}</p>
                            <p class="text-[10px] text-slate-400 truncate">${b.address_detail ? (b.address_detail + ', ') : ''}${b.province || ''}</p>
                        </div>
                    </label>
                `;
            }).join('');

            document.getElementById('editSubAccountModal').classList.remove('hidden');
            document.getElementById('editSubAccountModal').classList.add('flex');
        }

        function closeEditSubAccountModal() {
            document.getElementById('editSubAccountModal').classList.add('hidden');
            document.getElementById('editSubAccountModal').classList.remove('flex');
        }

        async function submitEditSubAccount(e) {
            e.preventDefault();
            const accId = document.getElementById('edit_sub_id').value;
            const checkboxes = document.querySelectorAll('input[name="edit_assigned_buildings[]"]:checked');
            
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất 1 tòa nhà để phân quyền cho nhân viên này!');
                return;
            }

            const assigned = Array.from(checkboxes).map(cb => cb.value);
            const payload = {
                name: document.getElementById('edit_sub_name').value.trim(),
                phone: document.getElementById('edit_sub_phone').value.trim(),
                password: document.getElementById('edit_sub_password').value || null,
                assigned_buildings: assigned
            };

            const btn = document.getElementById('btn-submit-edit-sub');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            try {
                const res = await fetch(`/api/sub-accounts/${accId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Cập nhật phân quyền thành công!');
                    closeEditSubAccountModal();
                    fetchSubAccounts();
                } else {
                    let errMsg = data.error || data.message || 'Đã có lỗi xảy ra!';
                    if (data.errors && typeof data.errors === 'object') {
                        errMsg = Object.values(data.errors).flat().join('\n');
                    }
                    alert(errMsg);
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi phân quyền';
            }
        }

        async function toggleLockSubAccount(accId) {
            if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái khóa/mở khóa tài khoản này?')) return;
            try {
                const res = await fetch(`/api/sub-accounts/${accId}/toggle-lock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    fetchSubAccounts();
                } else {
                    alert(data.error || 'Có lỗi xảy ra!');
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            }
        }

        async function deleteSubAccount(accId) {
            if (!confirm('Bạn có chắc chắn muốn xóa tài khoản nhân viên này khỏi hệ thống? Hành động này không thể hoàn tác.')) return;
            try {
                const res = await fetch(`/api/sub-accounts/${accId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Đã xóa tài khoản!');
                    fetchSubAccounts();
                } else {
                    alert(data.error || 'Có lỗi xảy ra!');
                }
            } catch (err) {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            }
        }
    </script>

</body>

</html>
