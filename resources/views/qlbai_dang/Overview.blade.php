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
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen">

    @php
        $currentUser = Auth::user();
        $reqType = request()->query('account_type', request()->query('type', ''));
        if (!empty($reqType) && in_array($reqType, ['canhan', 'doanhnghiep'])) {
            $accountType = $reqType;
        } else {
            $accountType = $currentUser->account_type ?? 'canhan';
        }

        $isEnterprise = $accountType === 'doanhnghiep';

        if ($currentUser) {
            if ($isEnterprise) {
                $displayName = !empty($currentUser->company_name)
                    ? $currentUser->company_name
                    : $currentUser->account_name ?? ($currentUser->username ?? 'Tập Đoàn BĐS Đạt Phát');
                $subTitle = 'Đối tác Doanh Nghiệp';
                $badgeText = 'Doanh Nghiệp';
            } else {
                $displayName =
                    $currentUser->account_name ?? ($currentUser->username ?? ($currentUser->name ?? 'Nguyễn Văn Tuấn'));
                $subTitle = 'Tài khoản Cá nhân';
                $badgeText = 'Cá Nhân';
            }
        } else {
            if ($isEnterprise) {
                $displayName = 'Tập Đoàn BĐS Đạt Phát';
                $subTitle = 'Đối tác Doanh Nghiệp';
                $badgeText = 'Doanh Nghiệp';
            } else {
                $displayName = 'Tài khoản Cá nhân';
                $subTitle = 'Cá nhân';
                $badgeText = 'Cá Nhân';
            }
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

    <!-- Overlay for Mobile Sidebar -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <div class="flex min-h-screen">

        <!-- Left Vertical Sidebar (Header Dọc bên tay trái) -->
        <aside id="sidebar-menu"
            class="fixed md:sticky top-0 left-0 z-50 md:z-30 w-72 h-screen bg-white border-r border-slate-200/80 shadow-md md:shadow-none flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 shrink-0">

            <div class="p-5 overflow-y-auto space-y-6">
                <!-- Sidebar Brand Header -->
                <div class="flex items-center justify-between pb-2">
                    <a href="/" class="flex items-center gap-3 group">
                        <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                        <div class="flex flex-col">
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
                <div
                    class="p-3.5 rounded-2xl {{ $isEnterprise ? 'bg-gradient-to-br from-purple-50 to-indigo-50/50 border border-purple-100' : 'bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-100' }} flex items-center gap-3 shadow-xs">
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
                </div>

                <!-- Vertical Navigation Menu -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Danh mục
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
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-hourglass-half text-amber-500 w-5 text-center text-sm"></i>
                        <span> Chờ phê duyệt</span>
                        <span id="sidebar-pending-count" class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden">0</span>
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
                        <span id="sidebar-notif-badge" class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold shadow-xs">4</span>
                    </button>

                    @if($isEnterprise)
                    <button onclick="switchTab('buildings')" id="tab-buildings"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-city text-purple-500 w-5 text-center text-sm"></i>
                        <span> Quản lý Tòa Nhà</span>
                    </button>
                    @endif
                </nav>
            </div>

            <!-- Footer / Actions in Sidebar -->
            <div class="p-4 border-t border-slate-100 space-y-1 bg-slate-50/50">
                <a href="/"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-house w-5 text-center text-slate-400"></i>
                    <span>Về Trang Chủ</span>
                </a>
                <a href="{{ url('quan-ly-van-hanh') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-gears w-5 text-center text-slate-400"></i>
                    <span>Quản lý vận hành</span>
                </a>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-rose-600 hover:bg-rose-50 transition-all text-left">
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
                <div>
                    <h1 id="page-heading" class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tổng Quan
                        Hệ Thống</h1>
                    <p class="text-xs text-slate-500 mt-1">Quản lý hiệu suất, danh sách tin đăng và tương tác khách hàng
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Notification Bell Quick Button & Dropdown -->
                    <div class="relative">
                        <button onclick="toggleNotificationDropdown(event)" class="relative p-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200/80 shadow-xs transition-colors focus:outline-none" title="Thông báo hệ thống">
                            <i class="fa-solid fa-bell text-rose-500 text-sm"></i>
                            <span id="topbar-notif-badge" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center border-2 border-white shadow-xs">4</span>
                        </button>

                        <!-- Notification Dropdown Popover -->
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden">
                            <div class="p-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <h4 class="font-bold text-xs text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-bell text-rose-500"></i> Thông báo mới nhất
                                </h4>
                                <span class="text-[10px] font-semibold text-slate-500">{{ $isEnterprise ? 'Đối tác Doanh Nghiệp' : 'Tài khoản Cá Nhân' }}</span>
                            </div>
                            <div id="topbar-notif-list" class="max-h-72 overflow-y-auto divide-y divide-slate-100 text-xs">
                                <!-- Populated dynamically by JS -->
                            </div>
                            <div class="p-2.5 text-center bg-slate-50 border-t border-slate-100">
                                <button onclick="switchTab('notifications'); toggleNotificationDropdown();" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                                    Xem tất cả thông báo <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

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
                    <input type="text" placeholder="Tìm kiếm mã tin, tên bài..."
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

            <!-- ==================== TAB C: CHỜ PHÊ DUYỆT ==================== -->
            <section id="content-pending-posts" class="tab-content hidden space-y-4">
                <h3 class="font-bold text-base text-slate-900" id="pending-count-heading">Danh sách bài đăng đang chờ
                    ban quản trị duyệt (3)</h3>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Mã Tin</th>
                                    <th class="p-4">Tiêu Đề</th>
                                    <th class="p-4">Giá Bán / Thuê</th>
                                    <th class="p-4">Trạng Thái</th>
                                    <th class="p-4 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="pending-posts-tbody"
                                class="divide-y divide-slate-100 text-slate-700 font-medium">
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

            <!-- ==================== TAB G: TẠO TÒA NHÀ & QUẢN LÝ TÒA NHÀ ==================== -->
            @if($isEnterprise)
            <section id="content-buildings" class="tab-content hidden space-y-6">
                <!-- Action Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900">Danh Sách Tòa Nhà & Khu Đô Thị</h3>
                        <p class="text-xs text-slate-500">Quản lý các cụm chung cư, tòa nhà chứa danh sách phòng cho
                            thuê</p>
                    </div>
                    <!-- Nút mở form tạo tòa nhà -->
                    <button onclick="openCreateBuildingModal()"
                        class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Tạo Tòa Nhà Mới
                    </button>
                </div>

                <!-- Danh sách Tòa nhà đã tạo (Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
            </section>
            @endif

        </main>
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
                            }

                            return {
                                id: n._id || n.id,
                                title: n.title,
                                content: n.message,
                                time: new Date(n.created_at).toLocaleString('vi-VN'),
                                type: n.type,
                                isRead: n.is_read,
                                iconBg: iconBg,
                                icon: icon,
                                badgeBg: badgeBg,
                                categoryName: categoryName
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
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">${n.content}</p>
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
                                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">${n.content}</p>
                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
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

        function markNotifAsRead(id) {
            const list = getNotificationsData();
            const target = list.find(n => n.id === id);
            if (target) {
                target.isRead = true;
                saveNotificationsData(list);
            }
        }

        async function markAllNotifsAsRead() {
            const list = getNotificationsData();
            list.forEach(n => n.isRead = true);
            saveNotificationsData(list);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const token = csrfToken ? csrfToken.content : '';
                await fetch('/api/notifications/read', { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': token } 
                });
            } catch (e) { console.error(e); }
        }

        function deleteNotif(id) {
            let list = getNotificationsData();
            list = list.filter(n => n.id !== id);
            saveNotificationsData(list);
        }

        function clearReadNotifs() {
            let list = getNotificationsData();
            list = list.filter(n => !n.isRead);
            saveNotificationsData(list);
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
        function cancelPendingPost(postId) {
            if (!confirm(`Bạn có chắc chắn muốn hủy đăng bài ${postId} không?`)) return;
            // Call API: await fetch(`/api/posts/pending/${postId}`, { method: "DELETE" })
            alert("Đã gửi yêu cầu hủy bài đăng (Chờ kết nối API).");
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
                'buildings': 'Quản Lý Tòa Nhà & Khu Đô Thị'
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
                        const imgUrl = building.display_image || 'https://placehold.co/600x400?text=Building';
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
                                <img src="${imgUrl}" alt="${safeName}" class="w-full h-40 object-cover">
                                <div class="p-5 flex-1 flex flex-col">
                                    <h4 class="font-bold text-slate-900 text-sm mb-1 line-clamp-1">${building.name}</h4>
                                    <p class="text-[11px] text-slate-500 flex items-start gap-1.5 mb-3 line-clamp-2">
                                        <i class="fa-solid fa-location-dot mt-0.5 text-slate-400"></i>
                                        <span>${addr}</span>
                                    </p>
                                    <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-xs font-bold text-purple-600"><i class="fa-solid fa-door-open mr-1"></i>${building.total_rooms || 0} phòng</span>
                                        <a href="/quan-ly-toa-nha/${building.id || building._id}" class="px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 font-semibold text-[11px] transition-colors inline-block text-center">
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
                const imgUrl = post.display_image || 'https://via.placeholder.com/150';
                const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' đ';
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">${post.id || post._id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs">
                                <div>
                                    <p class="font-bold text-slate-900">${post.title}</p>
                                    <p class="text-[10px] text-slate-400">${post.location || post.address || 'Đang cập nhật'}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-brand-600">${price}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check"></i> Đang hiển thị
                            </span>
                        </td>
                        <td class="p-4 text-center font-bold text-slate-600">${post.views || 0}</td>
                        <td class="p-4 text-right">
                            <button onclick="editPost('${post.id || post._id}')" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-xs transition-colors">
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
                const imgUrl = post.display_image || 'https://via.placeholder.com/150';
                const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' đ';
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">${post.id || post._id}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs">
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
                const imgUrl = post.display_image || 'https://via.placeholder.com/150';
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
                                <img src="${imgUrl}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs">
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
    </script>

    <!-- Complaint Modal -->
    <div id="complaint-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl scale-95 transition-transform duration-300" id="complaint-modal-content">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-slate-900"><i class="fa-solid fa-reply text-indigo-500 mr-2"></i>Gửi Phản Hồi Khiếu Nại</h3>
                <button onclick="closeComplaintModal()" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form id="complaint-form" onsubmit="submitComplaint(event)">
                <input type="hidden" id="complaint-post-id" name="post_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nội dung phản hồi <span class="text-rose-500">*</span></label>
                    <textarea id="complaint-content" name="content" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Nhập chi tiết vấn đề bạn cần phản hồi..." required></textarea>
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Hình ảnh đính kèm (nếu có)</label>
                    <input type="file" id="complaint-image" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeComplaintModal()" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Hủy</button>
                    <button type="submit" id="btn-submit-complaint" class="px-5 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-500/30 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Gửi phản hồi
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
