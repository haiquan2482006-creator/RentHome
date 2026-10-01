<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo liên hệ - RentHome</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e',
                            600: '#16a34a', 700: '#15803d', 900: '#14532d',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 20px rgba(34, 197, 94, 0.2)',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Desktop Mini Sidebar Styles */
        @media (min-width: 768px) {
            #sidebar-menu { transition: width 0.3s ease-in-out, transform 0.3s ease-in-out; }
            #sidebar-menu.sidebar-mini { width: 5.5rem !important; }
            #sidebar-menu.sidebar-mini .logo-text, #sidebar-menu.sidebar-mini .user-info, #sidebar-menu.sidebar-mini .menu-title, #sidebar-menu.sidebar-mini .tab-btn span, #sidebar-menu.sidebar-mini .sidebar-btn span { display: none !important; }
            #sidebar-menu.sidebar-mini .tab-btn, #sidebar-menu.sidebar-mini .sidebar-btn, #sidebar-menu.sidebar-mini .user-card { justify-content: center; padding-left: 0; padding-right: 0; }
            #sidebar-menu.sidebar-mini .tab-btn div { justify-content: center; width: 100%; }
            #sidebar-menu.sidebar-mini .tab-btn div span { display: none !important; }
            #sidebar-menu.sidebar-mini .tab-btn i, #sidebar-menu.sidebar-mini .sidebar-btn i { margin-right: 0 !important; }
            #sidebar-menu.sidebar-mini #btn-collapse-sidebar i { transform: rotate(180deg); }
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
    <header class="md:hidden bg-white border-b border-slate-200 sticky top-0 z-40 px-4 py-3 flex items-center justify-between shadow-sm">
        <a href="/" class="flex items-center gap-2.5">
            <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-8 h-8 object-contain rounded-xl shadow-xs">
            <span class="text-base font-extrabold text-slate-900 tracking-tight">Rent<span class="text-brand-600">Home</span></span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $isEnterprise ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $badgeText }}
            </span>
            <button onclick="toggleSidebar()" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 focus:outline-none">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>
    </header>

    @include('partials.sidebar-user', ['isDrawer' => true])

    <!-- Overlay for Mobile Sidebar -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

    <div class="flex min-h-screen">

        <!-- Left Vertical Sidebar -->
        <aside id="sidebar-menu" class="fixed md:sticky top-0 left-0 z-50 md:z-30 w-72 h-screen bg-white border-r border-slate-200/80 shadow-md md:shadow-none flex flex-col justify-between transition-transform duration-300 transform -translate-x-full md:translate-x-0 shrink-0">
            
            <button onclick="toggleDesktopSidebarMini()" id="btn-collapse-sidebar" class="absolute -right-3 top-8 w-6 h-6 bg-white border border-slate-200 rounded-full hidden md:flex items-center justify-center text-slate-400 hover:text-brand-600 shadow-sm z-50 transition-all hover:scale-110">
                <i class="fa-solid fa-chevron-left text-[10px] transition-transform duration-300"></i>
            </button>

            <div class="p-5 overflow-y-auto space-y-6 custom-scrollbar">
                <!-- Sidebar Brand Header -->
                <div class="flex items-center justify-between pb-2 brand-header">
                    <a href="/" class="flex items-center gap-3 group">
                        <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                        <div class="flex flex-col logo-text">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                            <span class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5">Quản lý bài đăng</span>
                        </div>
                    </a>
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- User Account Information Card -->
                <a href="{{ route('user.dashboard') }}" class="user-card block w-full text-left p-3.5 rounded-2xl {{ $isEnterprise ? 'bg-gradient-to-br from-purple-50 to-indigo-50/50 border border-purple-100' : 'bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-100' }} flex items-center gap-3 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                    <div class="w-10 h-10 rounded-xl {{ $isEnterprise ? 'bg-purple-600 text-white' : 'bg-brand-600 text-white' }} flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                        <i class="fa-solid {{ $isEnterprise ? 'fa-briefcase' : 'fa-user' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0 user-info">
                        <p class="text-xs font-bold text-slate-900 truncate" title="{{ $displayName }}">{{ $displayName }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-block w-2 h-2 rounded-full {{ $isEnterprise ? 'bg-purple-500' : 'bg-emerald-500' }}"></span>
                            <span class="text-[11px] font-semibold {{ $isEnterprise ? 'text-purple-700' : 'text-emerald-700' }}">{{ $subTitle }}</span>
                        </div>
                    </div>
                </a>

                <!-- Vertical Navigation Menu -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2 menu-title">Danh mục quản lý</p>

                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm"></i>
                        <span> Tổng quan</span>
                    </a>

                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-circle-check text-emerald-500 w-5 text-center text-sm"></i>
                        <span> Tin đang hiển thị</span>
                    </a>

                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-hourglass-half text-amber-500 w-5 text-center text-sm"></i>
                            <span> Chờ phê duyệt</span>
                        </div>
                    </a>

                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-clock-rotate-left text-sky-500 w-5 text-center text-sm"></i>
                        <span> Lịch sử đăng tin</span>
                    </a>

                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bell text-rose-500 w-5 text-center text-sm"></i>
                            <span> Thông báo</span>
                        </div>
                    </a>

                    <a href="{{ route('thong-bao-lien-he') }}" class="tab-btn active flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-xs w-full text-left transition-all bg-brand-50 text-brand-600 border border-brand-200/60 shadow-xs">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope-open-text text-amber-500 w-5 text-center text-sm"></i>
                            <span> Thông báo liên hệ</span>
                        </div>
                        @if($pendingContactCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold shadow-xs">{{ $pendingContactCount }}</span>
                        @endif
                    </a>

                    @if($isEnterprise)
                    <a href="{{ url('/Overview') }}" class="tab-btn flex items-center gap-3 px-3.5 py-3 rounded-xl font-semibold text-xs w-full text-left transition-all text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent">
                        <i class="fa-solid fa-city text-purple-500 w-5 text-center text-sm"></i>
                        <span> Quản lý Tòa Nhà</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Footer / Actions in Sidebar -->
            <div class="p-4 border-t border-slate-100 space-y-1 bg-slate-50/50">
                <a href="/" class="sidebar-btn flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-house w-5 text-center text-slate-400"></i>
                    <span>Về Trang Chủ</span>
                </a>
                <a href="{{ url('quan-ly-van-hanh') }}" class="sidebar-btn flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-slate-600 hover:bg-white hover:text-brand-600 transition-all border border-transparent hover:border-slate-200">
                    <i class="fa-solid fa-gears w-5 text-center text-slate-400"></i>
                    <span>Quản lý vận hành</span>
                </a>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="sidebar-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-xs text-rose-600 hover:bg-rose-50 transition-all text-left">
                            <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                @endauth
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 relative overflow-x-hidden" x-data="{ searchQuery: '' }">

        
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Danh sách liên hệ từ khách hàng</h2>
                <p class="text-sm text-slate-500 mt-1">Quản lý các yêu cầu xem nhà, tư vấn từ khách hàng theo từng bài đăng của bạn.</p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto flex-col sm:flex-row">
                <!-- Search Filter -->
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-phone text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" x-model="searchQuery" @input="searchQuery = searchQuery.replace(/[^0-9]/g, '')" inputmode="numeric" maxlength="20" placeholder="Tìm số điện thoại..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all shadow-sm">
                </div>
                
                <div class="flex items-center gap-2 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-bold text-sm transition-colors text-center">
                        Tất cả ({{ $totalContacts ?? 0 }})
                    </button>
                    <button class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-slate-500 hover:bg-slate-50 font-bold text-sm transition-colors text-center">
                        Chưa xử lý ({{ $pendingContacts ?? 0 }})
                    </button>
                </div>
            </div>
        </div>

        <!-- Accordion Container for Posts -->
        <div class="space-y-5">
            
            @forelse($groupedContacts as $post)
            @php
                $postPhonesJson = json_encode($post->contacts->pluck('phone')->toArray());
            @endphp
            <!-- Post Item -->
            <div x-data="{ expanded: false, phones: {{ $postPhonesJson }} }" 
                 x-show="searchQuery === '' || phones.some(p => p.includes(searchQuery))"
                 x-effect="if(searchQuery !== '' && phones.some(p => p.includes(searchQuery))) expanded = true; if(searchQuery === '') expanded = false;"
                 class="bg-white rounded-[20px] border border-slate-200 overflow-hidden shadow-soft transition-all duration-300 hover:border-brand-300">
                
                <!-- Post Header (Click to toggle) -->
                <button @click="expanded = !expanded" class="w-full px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white hover:bg-slate-50/50 transition-colors focus:outline-none">
                    <div class="flex items-center gap-4 text-left">
                        <!-- Post Thumbnail -->
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 shadow-sm">
                            <img src="{{ $post->image_path ? asset('storage/' . $post->image_path) : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80' }}" alt="Room" class="w-full h-full object-cover">
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-md bg-brand-100 text-brand-700 text-[10px] font-bold uppercase tracking-wider">{{ $post->category_name }}</span>
                                <span class="text-xs font-semibold text-slate-400"><i class="fa-solid fa-location-dot mr-1"></i>{{ $post->address }}</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 leading-tight group-hover:text-brand-600 transition-colors">
                                {{ $post->title }}
                            </h3>
                            <p class="text-[13px] font-black text-brand-600 mt-1">{{ number_format($post->price) }} Triệu / tháng</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-slate-100 pt-3 sm:pt-0">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                @foreach($post->contacts->take(3) as $c)
                                    <div class="w-8 h-8 rounded-full border-2 border-white object-cover shadow-sm bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                        {{ strtoupper(substr($c->sender_name, 0, 1)) }}
                                    </div>
                                @endforeach
                            </div>
                            <span class="px-3 py-1.5 rounded-full bg-rose-50 text-rose-600 text-xs font-bold border border-rose-100 shadow-sm">
                                {{ $post->contacts->count() }} Liên hệ
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 transition-transform duration-300" :class="expanded ? 'rotate-180 bg-brand-50 text-brand-600' : ''">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                </button>

                <!-- Contacts Dropdown Content (Expanded area) -->
                <div x-show="expanded" x-collapse x-cloak>
                    <div class="px-6 pb-6 pt-3 bg-slate-50/70 border-t border-slate-100">
                        <h4 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-address-book text-slate-400"></i> Danh sách chi tiết
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-5">
                            
                            @forelse($post->contacts as $contact)
                            @php
                                $statusTheme = match($contact->status) {
                                    'pending' => ['border' => 'bg-rose-500', 'avatar' => 'from-rose-100 to-rose-50 text-rose-600 border-rose-100', 'badge_bg' => 'bg-rose-50', 'badge_text' => 'text-rose-600', 'badge_border' => 'border-rose-100', 'label' => 'Chưa xử lý', 'note_bg' => 'bg-rose-50', 'note_text' => 'text-rose-600', 'note_border' => 'border-rose-100'],
                                    'communicating' => ['border' => 'bg-blue-500', 'avatar' => 'from-blue-100 to-blue-50 text-blue-600 border-blue-100', 'badge_bg' => 'bg-blue-50', 'badge_text' => 'text-blue-600', 'badge_border' => 'border-blue-100', 'label' => 'Đang trao đổi', 'note_bg' => 'bg-blue-50', 'note_text' => 'text-blue-600', 'note_border' => 'border-blue-100'],
                                    'approved' => ['border' => 'bg-emerald-500', 'avatar' => 'from-emerald-100 to-emerald-50 text-emerald-600 border-emerald-100', 'badge_bg' => 'bg-emerald-50', 'badge_text' => 'text-emerald-600', 'badge_border' => 'border-emerald-100', 'label' => 'Đã duyệt', 'note_bg' => 'bg-emerald-50', 'note_text' => 'text-emerald-600', 'note_border' => 'border-emerald-100'],
                                    default => ['border' => 'bg-slate-300', 'avatar' => 'bg-slate-100 text-slate-600 border-slate-200', 'badge_bg' => 'bg-slate-100', 'badge_text' => 'text-slate-600', 'badge_border' => 'border-slate-200', 'label' => 'Đã đóng', 'note_bg' => 'bg-slate-100', 'note_text' => 'text-slate-500', 'note_border' => 'border-slate-200']
                                };
                            @endphp
                            <!-- Contact Card -->
                            <div x-show="searchQuery === '' || '{{ $contact->phone }}'.includes(searchQuery)" class="bg-white p-5 rounded-[18px] border border-slate-200 shadow-sm relative overflow-hidden hover:shadow-md transition-all group flex flex-col">
                                <div class="absolute top-0 left-0 w-1.5 h-full {{ $statusTheme['border'] }}"></div>
                                
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-full bg-gradient-to-br {{ $statusTheme['avatar'] }} flex items-center justify-center font-black text-lg border shadow-sm">
                                            {{ strtoupper(substr($contact->sender_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="font-extrabold text-slate-900 text-[15px]">{{ $contact->sender_name }}</h5>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $contact->created_at->format('H:i, d/m/Y') }} • ID: #{{ $contact->id }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 {{ $statusTheme['badge_bg'] }} {{ $statusTheme['badge_text'] }} text-[10px] font-bold rounded-md border {{ $statusTheme['badge_border'] }} uppercase tracking-wider">{{ $statusTheme['label'] }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-y-2.5 mb-4 text-sm bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5 text-slate-600">
                                            <i class="fa-solid fa-phone text-slate-400"></i> 
                                            <span class="font-semibold text-slate-700">{{ $contact->phone }}</span>
                                        </div>
                                        <a href="https://zalo.me/{{ $contact->phone }}" target="_blank" class="flex items-center gap-1.5 bg-[#0068FF]/10 text-[#0068FF] hover:bg-[#0068FF] hover:text-white rounded-lg px-2.5 py-1 transition-all text-[11px] font-bold border border-[#0068FF]/20 hover:border-[#0068FF]">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21.574 11.233c0-4.996-4.707-9.043-10.513-9.043C5.253 2.19 0.547 6.237 0.547 11.233c0 2.822 1.503 5.342 3.844 7.026-.145.89-.607 2.656-.677 2.923-.105.412.336.5.642.336.19-.101 3.238-1.896 4.398-2.617a11.583 11.583 0 0 0 2.308.232c5.806 0 10.512-4.047 10.512-9.043v.143z" fill-rule="evenodd" clip-rule="evenodd"/></svg>
                                            Chat Zalo
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-2.5 text-slate-600">
                                        <i class="fa-solid fa-envelope text-slate-400"></i> 
                                        <span class="font-medium truncate text-slate-700">{{ $contact->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2.5 {{ $statusTheme['note_text'] }} font-semibold {{ $statusTheme['note_bg'] }} px-3 py-2 rounded-lg mt-1 border {{ $statusTheme['note_border'] }}">
                                        <i class="fa-solid fa-calendar-check"></i> 
                                        {{ $contact->note ?? 'Chưa có ghi chú' }}
                                    </div>
                                </div>
                                
                                <div class="bg-white rounded-xl p-3.5 text-[13px] text-slate-600 italic border border-slate-200 mb-5 relative flex-grow">
                                    <i class="fa-solid fa-quote-left text-slate-200 absolute -top-2 -left-2 bg-white px-1 text-lg"></i>
                                    "{{ $contact->message }}"
                                </div>
                                
                                <div class="mt-auto pt-1" x-data="{ state: '{{ $contact->status }}' }">
                                    <!-- Trạng thái 1: Chưa xử lý -->
                                    <div x-show="state === 'pending'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex gap-2">
                                        <button @click="state = 'rejected'" title="Từ chối" class="flex-none px-4 py-2.5 border border-slate-200 text-slate-500 hover:border-red-500 hover:text-red-500 hover:bg-red-50 text-[13px] font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 hover:-translate-y-0.5">
                                            <i class="fa-solid fa-xmark"></i> Từ chối
                                        </button>
                                        <button @click="state = 'communicating'" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-[13px] font-bold rounded-xl transition-all shadow-[0_4px_12px_rgba(37,99,235,0.2)] hover:shadow-[0_6px_16px_rgba(37,99,235,0.3)] flex items-center justify-center gap-2 hover:-translate-y-0.5">
                                            <i class="fa-solid fa-phone-volume"></i> Đánh dấu đang trao đổi
                                        </button>
                                    </div>

                                    <!-- Trạng thái 2: Đang trao đổi -->
                                    <div x-show="state === 'communicating'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex gap-2">
                                        <button @click="state = 'pending'" class="flex-none px-3 py-2.5 border border-red-300 text-red-500 hover:bg-red-50 text-[13px] font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 hover:-translate-y-0.5">
                                            <i class="fa-solid fa-ban"></i> Hủy giao dịch
                                        </button>
                                        <button @click="state = 'approved'" class="flex-1 py-2.5 bg-brand-500 hover:bg-brand-600 text-white text-[14px] font-black rounded-xl transition-all shadow-[0_4px_12px_rgba(34,197,94,0.3)] hover:shadow-[0_6px_16px_rgba(34,197,94,0.4)] flex items-center justify-center gap-2 hover:-translate-y-0.5 relative overflow-hidden group">
                                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                                            📝 Tạo hợp đồng
                                        </button>
                                    </div>
                                    
                                    <!-- Trạng thái 3: Đã duyệt / Có hợp đồng -->
                                    <div x-show="state === 'approved'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                        <button class="w-full py-2.5 bg-emerald-600 text-white text-[13px] font-bold rounded-xl flex items-center justify-center gap-2 opacity-70 cursor-not-allowed">
                                            <i class="fa-solid fa-check-double"></i> Đã có hợp đồng
                                        </button>
                                    </div>

                                    <!-- Trạng thái 4: Đã đóng / Bị từ chối -->
                                    <div x-show="state === 'rejected' || state === 'closed'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                        <button class="w-full py-2.5 bg-slate-100 text-slate-500 border border-slate-200 text-[13px] font-bold rounded-xl flex items-center justify-center gap-2 opacity-70 cursor-not-allowed">
                                            <i class="fa-solid fa-lock"></i> Đã đóng
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-8 text-center text-slate-500">
                                Không có liên hệ nào cho bài đăng này.
                            </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-[20px] border border-slate-200 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="fa-solid fa-inbox text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 mb-1">Chưa có liên hệ nào</h3>
                <p class="text-slate-500 text-sm">Khi có khách hàng liên hệ xem nhà, thông tin sẽ hiển thị tại đây.</p>
            </div>
            @endforelse

        </div>
    </main>
    </div> <!-- End flex min-h-screen -->

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-menu');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function toggleDesktopSidebarMini() {
            const sidebar = document.getElementById('sidebar-menu');
            sidebar.classList.toggle('sidebar-mini');
        }
    </script>
</body>
</html>
