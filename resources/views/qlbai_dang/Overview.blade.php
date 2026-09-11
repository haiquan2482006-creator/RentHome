<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    </button>

                    <button onclick="switchTab('history-posts')" id="tab-history-posts"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-clock-rotate-left text-sky-500 w-5 text-center text-sm"></i>
                        <span> Lịch sử đăng tin</span>
                    </button>

                    <button onclick="switchTab('notifications')" id="tab-notifications"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-bell text-rose-500 w-5 text-center text-sm"></i>
                        <span> Thông báo</span>
                    </button>

                    <button onclick="switchTab('buildings')" id="tab-buildings"
                        class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-city text-purple-500 w-5 text-center text-sm"></i>
                        <span> Quản lý Tòa Nhà</span>
                    </button>
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
                            <p class="text-2xl font-black text-slate-900 mt-1">45,820</p>
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
                            <p class="text-2xl font-black text-slate-900 mt-1">1,940</p>
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
                            <p class="text-2xl font-black text-slate-900 mt-1">18</p>
                            <span class="text-[11px] text-slate-400">Trên tổng 24 tin</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-house-circle-check"></i>
                        </div>
                    </div>

                    <div
                        class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between hover:border-brand-500/30 transition-all">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tòa nhà / Dự án
                            </p>
                            <p class="text-2xl font-black text-slate-900 mt-1">4</p>
                            <span class="text-[11px] text-purple-600 font-bold">120 phòng đang quản lý</span>
                        </div>
                        <div
                            class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-city"></i>
                        </div>
                    </div>
                </div>

                <!-- Detailed Post Performance Table -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-sm">Thống kê tương tác theo bài đăng</h3>
                        <span class="text-xs text-slate-400">Cập nhật lúc 15:00 hôm nay</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50/75 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Mã tin</th>
                                    <th class="p-4">Tên bài đăng</th>
                                    <th class="p-4 text-center">Lượt xem</th>
                                    <th class="p-4 text-center">Lượt thích</th>
                                    <th class="p-4">Hiệu suất</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-4 font-bold text-slate-900">#RH-9921</td>
                                    <td class="p-4 font-semibold text-slate-800">Căn Hộ Landmark Plus 2PN Tầng Cao Full
                                        Nội Thất</td>
                                    <td class="p-4 text-center font-bold text-emerald-600">8,420</td>
                                    <td class="p-4 text-center font-bold text-rose-500">412</td>
                                    <td class="p-4"><span
                                            class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">Rất
                                            cao</span></td>
                                </tr>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-4 font-bold text-slate-900">#RH-9915</td>
                                    <td class="p-4 font-semibold text-slate-800">Studio Vinhomes Grand Park S5.02 Ban
                                        Công Thoáng</td>
                                    <td class="p-4 text-center font-bold text-emerald-600">6,105</td>
                                    <td class="p-4 text-center font-bold text-rose-500">230</td>
                                    <td class="p-4"><span
                                            class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px]">Cao</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB B: TIN ĐANG HIỂN THỊ ==================== -->
            <section id="content-active-posts" class="tab-content hidden space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-slate-900">Danh sách tin đang hiển thị (18)</h3>
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
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <!-- Item 1 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 font-bold text-slate-900">#RH-9921</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=80&q=80"
                                                class="w-10 h-10 rounded-lg object-cover">
                                            <div>
                                                <p class="font-bold text-slate-900">Căn Hộ Landmark Plus 2PN</p>
                                                <p class="text-[10px] text-slate-400">Tòa Landmark Plus • Bình Thạnh
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-brand-600">14.5 Tr/tháng</td>
                                    <td class="p-4">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang hiển thị
                                            (Còn trống)
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-800">8,420</td>
                                    <td class="p-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700 transition-colors"
                                                title="Cập nhật thông tin">
                                                <i class="fa-solid fa-pen-to-square"></i> Cập nhật
                                            </button>
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 font-semibold text-amber-700 transition-colors"
                                                title="Đánh dấu đã thuê">
                                                <i class="fa-solid fa-key"></i> Đã cho thuê
                                            </button>
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 font-semibold text-rose-600 transition-colors"
                                                title="Gỡ tin này">
                                                <i class="fa-solid fa-arrow-down"></i> Gỡ tin
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Item 2 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 font-bold text-slate-900">#RH-9915</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=80&q=80"
                                                class="w-10 h-10 rounded-lg object-cover">
                                            <div>
                                                <p class="font-bold text-slate-900">Studio Vinhomes Grand Park S5.02
                                                </p>
                                                <p class="text-[10px] text-slate-400">Tòa S5.02 • TP. Thủ Đức</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-brand-600">6.0 Tr/tháng</td>
                                    <td class="p-4">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang hiển thị
                                            (Còn trống)
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-bold text-slate-800">6,105</td>
                                    <td class="p-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700 transition-colors"><i
                                                    class="fa-solid fa-pen-to-square"></i> Cập nhật</button>
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 font-semibold text-amber-700 transition-colors"><i
                                                    class="fa-solid fa-key"></i> Đã cho thuê</button>
                                            <button
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 font-semibold text-rose-600 transition-colors"><i
                                                    class="fa-solid fa-arrow-down"></i> Gỡ tin</button>
                                        </div>
                                    </td>
                                </tr>
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
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 font-bold text-slate-900">#RH-9988</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=80&q=80"
                                                class="w-10 h-10 rounded-lg object-cover">
                                            <div>
                                                <p class="font-bold text-slate-900">Penthouse Duplex Saigon Pearl View
                                                    Sông Sài Gòn</p>
                                                <p class="text-[10px] text-slate-400">Gửi lúc 10:15 - Hôm nay</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-brand-600">38.0 Tr/tháng</td>
                                    <td class="p-4">
                                        <span
                                            class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-clock animate-spin"></i> Chờ phê duyệt
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">
                                            <i class="fa-solid fa-ban mr-1"></i> Hủy đăng
                                        </button>
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
                                <!-- Dynamic rendering via JS renderHistoryPosts() -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB F: THÔNG BÁO ==================== -->
            <section id="content-notifications" class="tab-content hidden space-y-4">
                <h3 class="font-bold text-base text-slate-900">Hộp thư & Thông báo hệ thống</h3>

                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 text-sm">Bài đăng #RH-9921 đã được phê duyệt thành
                                    công
                                </h4>
                                <span class="text-[11px] text-slate-400">10 phút trước</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1">Bài viết của bạn đã hiển thị công khai trên hệ thống
                                RentHome toàn quốc.</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-lg">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 text-sm">Có 1 khách hàng đăng ký hẹn xem phòng</h4>
                                <span class="text-[11px] text-slate-400">1 giờ trước</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1">Khách hàng Nguyễn Văn Tuấn muốn xem căn hộ Studio
                                Vinhomes Grand Park vào 9:00 sáng mai.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==================== TAB G: TẠO TÒA NHÀ & QUẢN LÝ TÒA NHÀ ==================== -->
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
                    <!-- Tòa nhà 1 -->
                    <div onclick="openBuildingDetailModal('Tòa Landmark Plus', '208 Nguyễn Hữu Cảnh, P.22, Bình Thạnh, TP.HCM', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 45)"
                        class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition-all cursor-pointer group">
                        <div class="aspect-[16/9] overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span
                                class="absolute top-3 right-3 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white font-bold text-[10px]">
                                45 Phòng / Căn hộ
                            </span>
                        </div>
                        <div class="p-5">
                            <h4
                                class="font-bold text-base text-slate-900 group-hover:text-purple-600 transition-colors">
                                Tòa Landmark Plus</h4>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 line-clamp-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i> 208 Nguyễn Hữu Cảnh, P.22, Bình
                                Thạnh, TP.HCM
                            </p>
                            <div
                                class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-purple-600">
                                <span>Xem danh sách phòng</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tòa nhà 2 -->
                    <div onclick="openBuildingDetailModal('Khu Căn Hộ S5 Vinhomes Grand Park', 'Đường Nguyễn Xiển, Long Thạnh Mỹ, TP. Thủ Đức', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', 75)"
                        class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition-all cursor-pointer group">
                        <div class="aspect-[16/9] overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span
                                class="absolute top-3 right-3 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white font-bold text-[10px]">
                                75 Phòng / Căn hộ
                            </span>
                        </div>
                        <div class="p-5">
                            <h4
                                class="font-bold text-base text-slate-900 group-hover:text-purple-600 transition-colors">
                                Khu Căn Hộ S5 Vinhomes Grand Park</h4>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 line-clamp-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i> Đường Nguyễn Xiển, Long Thạnh
                                Mỹ, TP. Thủ Đức
                            </p>
                            <div
                                class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-purple-600">
                                <span>Xem danh sách phòng</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- ==================== MODAL 1: FORM TẠO TÒA NHÀ ==================== -->
    <div id="createBuildingModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Tạo Tòa Nhà / Khu Đô Thị Mới</h3>
                <button onclick="closeCreateBuildingModal()"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form onsubmit="event.preventDefault(); submitCreateBuilding();" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tên tòa
                        nhà</label>
                    <input type="text" required placeholder="VD: Tòa Landmark Plus, Chung cư Moonlight..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Địa
                        chỉ</label>
                    <input type="text" required placeholder="VD: 208 Nguyễn Hữu Cảnh, Quận Bình Thạnh..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Số phòng /
                        Nhà</label>
                    <input type="number" required placeholder="VD: 50"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Hình ảnh tòa
                        nhà</label>
                    <div
                        class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:bg-slate-50 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400"></i>
                        <p class="text-xs text-slate-600 mt-1 font-semibold">Tải lên hình ảnh đại diện tòa nhà</p>
                        <p class="text-[10px] text-slate-400">PNG, JPG tối đa 5MB</p>
                    </div>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-all">
                        Hoàn thành
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
                            <tr>
                                <td class="p-3.5 font-bold text-slate-900">P.1204</td>
                                <td class="p-3.5">2 Phòng ngủ, 2 WC</td>
                                <td class="p-3.5">75 m²</td>
                                <td class="p-3.5 font-bold text-brand-600">14.5 Tr/tháng</td>
                                <td class="p-3.5"><span
                                        class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">Còn
                                        trống</span></td>
                                <td class="p-3.5 text-right"><button
                                        class="text-purple-600 font-bold hover:underline">Sửa</button></td>
                            </tr>
                            <tr>
                                <td class="p-3.5 font-bold text-slate-900">P.1802</td>
                                <td class="p-3.5">1 Phòng ngủ (Studio)</td>
                                <td class="p-3.5">42 m²</td>
                                <td class="p-3.5 font-bold text-brand-600">9.0 Tr/tháng</td>
                                <td class="p-3.5"><span
                                        class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px]">Đã
                                        cho thuê</span></td>
                                <td class="p-3.5 text-right"><button
                                        class="text-purple-600 font-bold hover:underline">Sửa</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Controller -->
    <script>
        // Các bài đăng chờ duyệt mẫu ban đầu
        const defaultPendingPosts = [{
                id: '#RH-9988',
                title: 'Penthouse Duplex Saigon Pearl View Sông Sài Gòn',
                time: 'Gửi lúc 10:15 - Hôm nay',
                price: '38.0 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=80&q=80',
                status: 'Chờ phê duyệt'
            },
            {
                id: '#RH-9972',
                title: 'Căn Hộ Shophouse Masteri An Phú Mặt Tiền Hà Nội',
                time: 'Gửi lúc 09:30 - Hôm nay',
                price: '25.0 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=80&q=80',
                status: 'Chờ phê duyệt'
            },
            {
                id: '#RH-9960',
                title: 'Nhà Phố KDC Cityland Park Hills Gò Vấp',
                time: 'Gửi lúc Hôm qua',
                price: '45.0 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=80&q=80',
                status: 'Chờ phê duyệt'
            }
        ];

        // Hiển thị danh sách bài đăng chờ phê duyệt (mẫu + bài đăng mới từ localStorage)
        function renderPendingPosts() {
            const tbody = document.getElementById('pending-posts-tbody');
            const heading = document.getElementById('pending-count-heading');
            if (!tbody) return;

            let storedPosts = [];
            try {
                storedPosts = JSON.parse(localStorage.getItem('pendingPosts') || '[]');
            } catch (e) {
                storedPosts = [];
            }

            let cancelledDefaultIds = [];
            try {
                cancelledDefaultIds = JSON.parse(localStorage.getItem('cancelledDefaultPendingPosts') || '[]');
            } catch (e) {}

            const activeDefaultPosts = defaultPendingPosts.filter(p => !cancelledDefaultIds.includes(p.id));
            const allPending = [...storedPosts, ...activeDefaultPosts];

            if (heading) {
                heading.innerText = `Danh sách bài đăng đang chờ ban quản trị duyệt (${allPending.length})`;
            }

            if (allPending.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 font-medium">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                            Không có bài đăng nào đang chờ phê duyệt.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = allPending.map(post => `
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="p-4 font-bold text-slate-900">${post.id}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="${post.image}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-xs">
                            <div>
                                <p class="font-bold text-slate-900">${post.title}</p>
                                <p class="text-[10px] text-slate-400">${post.location ? post.location + ' • ' : ''}${post.time}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 font-bold text-brand-600">${post.price}</td>
                    <td class="p-4">
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-[10px] inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-clock animate-spin"></i> Chờ phê duyệt
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <button onclick="cancelPendingPost('${post.id}')"
                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors">
                            <i class="fa-solid fa-ban mr-1"></i> Hủy đăng
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Các bài đăng đã duyệt mẫu ban đầu
        const defaultApprovedPosts = [{
                id: '#RH-9921',
                title: 'Căn Hộ Landmark Plus 2PN Tầng Cao Full Nội Thất',
                location: 'Tòa Landmark Plus • Bình Thạnh',
                price: '14.5 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=80&q=80',
                status: 'approved',
                date: '10/09/2026'
            },
            {
                id: '#RH-9915',
                title: 'Studio Vinhomes Grand Park S5.02 Ban Công Thoáng',
                location: 'Tòa S5.02 • TP. Thủ Đức',
                price: '6.0 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=80&q=80',
                status: 'approved',
                date: '08/09/2026'
            },
            {
                id: '#RH-8812',
                title: 'Căn 1PN Masteri Thảo Điền Tháp T2',
                location: 'Masteri Thảo Điền • Quận 2',
                price: '11.0 Tr/tháng',
                image: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=80&q=80',
                status: 'approved',
                date: '15/08/2026'
            }
        ];

        let currentHistoryFilter = 'all';

        // Hiển thị danh sách lịch sử bài đăng (Đã được phê duyệt + Đang chờ phê duyệt)
        function renderHistoryPosts() {
            const tbody = document.getElementById('history-posts-tbody');
            if (!tbody) return;

            let storedPosts = [];
            try {
                storedPosts = JSON.parse(localStorage.getItem('pendingPosts') || '[]');
            } catch (e) {
                storedPosts = [];
            }

            let cancelledDefaultIds = [];
            try {
                cancelledDefaultIds = JSON.parse(localStorage.getItem('cancelledDefaultPendingPosts') || '[]');
            } catch (e) {}

            const activeDefaultPending = defaultPendingPosts.filter(p => !cancelledDefaultIds.includes(p.id));

            // Format pending items
            const pendingFormatted = [...storedPosts, ...activeDefaultPending].map(p => ({
                id: p.id,
                title: p.title,
                location: p.location || '',
                price: p.price,
                image: p.image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=80&q=80',
                status: 'pending',
                statusLabel: 'Đang chờ phê duyệt',
                date: p.time || 'Hôm nay'
            }));

            // Format approved items
            const approvedFormatted = defaultApprovedPosts.map(a => ({
                id: a.id,
                title: a.title,
                location: a.location || '',
                price: a.price,
                image: a.image,
                status: 'approved',
                statusLabel: 'Đã được phê duyệt',
                date: a.date
            }));

            // Combine history: pending items first, then approved items
            let allHistory = [...pendingFormatted, ...approvedFormatted];

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

            renderHistoryPosts();
        }

        // Hủy bài đăng chờ duyệt
        function cancelPendingPost(postId) {
            if (!confirm(`Bạn có chắc chắn muốn hủy đăng bài ${postId} không?`)) return;

            let storedPosts = JSON.parse(localStorage.getItem('pendingPosts') || '[]');
            const storedIndex = storedPosts.findIndex(p => p.id === postId);

            if (storedIndex !== -1) {
                storedPosts.splice(storedIndex, 1);
                localStorage.setItem('pendingPosts', JSON.stringify(storedPosts));
            } else {
                let cancelledDefaultIds = JSON.parse(localStorage.getItem('cancelledDefaultPendingPosts') || '[]');
                if (!cancelledDefaultIds.includes(postId)) {
                    cancelledDefaultIds.push(postId);
                    localStorage.setItem('cancelledDefaultPendingPosts', JSON.stringify(cancelledDefaultIds));
                }
            }
            renderPendingPosts();
            renderHistoryPosts();
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

            if (tabId === 'pending-posts') {
                renderPendingPosts();
            }
            if (tabId === 'history-posts') {
                renderHistoryPosts();
            }

            // Tu dong dong menu mobile khi click tab
            const sidebar = document.getElementById('sidebar-menu');
            const overlay = document.getElementById('sidebar-overlay');
            if (window.innerWidth < 768) {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
            }
        }

        // Tự động chọn tab nếu có trong URL parameters hoặc hash khi trang load
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                switchTab(tabParam);
            }
            renderPendingPosts();
            renderHistoryPosts();
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

        function submitCreateBuilding() {
            alert('Đã hoàn thành tạo tòa nhà mới vào hệ thống!');
            closeCreateBuildingModal();
        }

        // Modal Xem Chi Tiết Tòa Nhà & Danh Sách Phòng
        function openBuildingDetailModal(name, address, imgUrl, totalRooms) {
            document.getElementById('detailBuildingName').innerText = name;
            document.getElementById('detailBuildingAddress').innerHTML =
                `<i class="fa-solid fa-location-dot text-rose-500"></i> ${address}`;
            document.getElementById('detailBuildingImg').src = imgUrl;
            document.getElementById('detailBuildingTotalRooms').innerText = totalRooms;

            document.getElementById('buildingDetailModal').classList.remove('hidden');
            document.getElementById('buildingDetailModal').classList.add('flex');
        }

        function closeBuildingDetailModal() {
            document.getElementById('buildingDetailModal').classList.add('hidden');
            document.getElementById('buildingDetailModal').classList.remove('flex');
        }
    </script>
</body>

</html>
