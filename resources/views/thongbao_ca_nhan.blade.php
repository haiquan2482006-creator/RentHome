<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thông Báo Của Tôi - RentHome</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @php
            $themeSettings = file_exists(storage_path('app/theme_settings.json')) 
                ? json_decode(file_get_contents(storage_path('app/theme_settings.json')), true) 
                : [];
            $tColor = $themeSettings['theme_color'] ?? '#0d9488'; // Teal theme
        @endphp
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .tab-btn.active {
            color: #ffffff !important;
            background-color: #0d9488 !important;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col justify-between bg-slate-50 text-slate-800 antialiased">

    <!-- Header Chung (Không có Sidebar của Admin) -->
    @include('partials.header-home')

    <!-- Main Content Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 pt-24 sm:pt-28 pb-14">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4">
            <a href="/" class="hover:text-teal-600 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
            <span>/</span>
            <span class="text-slate-800 font-bold">Thông báo của tôi</span>
        </nav>

        <!-- Tiêu đề & Nút thao tác -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shadow-xs shrink-0 border border-teal-100">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                Thông Báo Của Tôi
                            </h1>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-xs font-bold shadow-xs">
                                    {{ $unreadCount }} chưa đọc
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Cập nhật thông tin hợp đồng, tiền cọc, yêu cầu xem phòng và thông báo từ ban quản lý.
                        </p>
                    </div>
                </div>

                <!-- Nút hành động góc phải -->
                <div class="flex items-center gap-2 sm:self-center shrink-0">
                    <button type="button" 
                            onclick="markAllAsRead()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-teal-700 bg-teal-50/80 hover:bg-teal-100 border border-teal-200/80 transition shadow-xs active:scale-95 cursor-pointer"
                            title="Đánh dấu tất cả thông báo là đã đọc">
                        <i class="fa-solid fa-check-double text-teal-600"></i>
                        <span>Đánh dấu tất cả là đã đọc</span>
                    </button>

                    <button type="button" 
                            onclick="deleteReadNotifications()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50/70 hover:bg-rose-100 border border-rose-200/70 transition shadow-xs active:scale-95 cursor-pointer"
                            title="Xóa toàn bộ các thông báo đã đọc">
                        <i class="fa-regular fa-trash-can"></i>
                        <span>Xóa thông báo đã đọc</span>
                    </button>
                </div>
            </div>

            <!-- Bộ lọc (Tabs) -->
            <div class="flex items-center justify-between border-t border-slate-100 mt-5 pt-4">
                <div class="flex items-center gap-2">
                    <!-- Tab Tất cả -->
                    <a href="{{ route('thongbao.canhan', ['tab' => 'all']) }}" 
                       class="tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($currentTab ?? 'all') === 'all' ? 'active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50 border border-slate-200/60' }}">
                        <span>Tất cả</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($currentTab ?? 'all') === 'all' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $totalCount ?? 0 }}
                        </span>
                    </a>

                    <!-- Tab Chưa đọc -->
                    <a href="{{ route('thongbao.canhan', ['tab' => 'unread']) }}" 
                       class="tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($currentTab ?? '') === 'unread' ? 'active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50 border border-slate-200/60' }}">
                        <span>Chưa đọc</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($currentTab ?? '') === 'unread' ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $unreadCount ?? 0 }}
                        </span>
                    </a>

                    <!-- Tab Đã đọc -->
                    <a href="{{ route('thongbao.canhan', ['tab' => 'read']) }}" 
                       class="tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($currentTab ?? '') === 'read' ? 'active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 bg-slate-50 border border-slate-200/60' }}">
                        <span>Đã đọc</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($currentTab ?? '') === 'read' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $readCount ?? 0 }}
                        </span>
                    </a>
                </div>

                <div class="text-xs text-slate-400 hidden sm:block">
                    Hiển thị {{ $notifications->count() }} / {{ $notifications->total() }} thông báo
                </div>
            </div>
        </div>

        <!-- Danh sách thông báo (List) -->
        <div class="space-y-3" id="notif-list-container">
            @forelse($notifications as $n)
                @php
                    $isUnread = !$n->is_read;
                    $notifId = (string)($n->_id ?? $n->id);
                    $category = strtolower($n->category ?? '');
                    $type = strtolower($n->type ?? '');

                    // Phân loại Icon và Màu sắc (màu xanh ngọc teal cho Hợp đồng / Hoàn tiền theo yêu cầu)
                    $iconClass = 'fa-solid fa-bell';
                    $iconBg = 'bg-teal-50 text-teal-600 border border-teal-200';
                    $badgeBg = 'bg-teal-50 text-teal-700 border border-teal-200';
                    $badgeText = 'Hệ thống';

                    if (str_contains($category, 'building') || str_contains($type, 'building') || str_contains(strtolower($n->title), 'tòa nhà')) {
                        $iconClass = 'fa-solid fa-city';
                        $iconBg = 'bg-purple-100 text-purple-700 border border-purple-200';
                        $badgeBg = 'bg-purple-50 text-purple-700 border border-purple-200';
                        $badgeText = 'Tòa nhà';
                    } elseif (str_contains($category, 'contract') || str_contains($type, 'contract') || str_contains(strtolower($n->title), 'hợp đồng')) {
                        $iconClass = 'fa-solid fa-file-signature';
                        $iconBg = 'bg-teal-100 text-teal-700 border border-teal-200';
                        $badgeBg = 'bg-teal-50 text-teal-700 border border-teal-200';
                        $badgeText = 'Hợp đồng';
                    } elseif (str_contains($category, 'refund') || str_contains($type, 'refund') || str_contains(strtolower($n->title), 'hoàn tiền')) {
                        $iconClass = 'fa-solid fa-hand-holding-dollar';
                        $iconBg = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                        $badgeBg = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                        $badgeText = 'Hoàn tiền';
                    } elseif (str_contains($category, 'invoice') || str_contains($type, 'invoice') || str_contains(strtolower($n->title), 'hóa đơn') || str_contains(strtolower($n->title), 'tiền cọc')) {
                        $iconClass = 'fa-solid fa-file-invoice-dollar';
                        $iconBg = 'bg-sky-100 text-sky-700 border border-sky-200';
                        $badgeBg = 'bg-sky-50 text-sky-700 border border-sky-200';
                        $badgeText = 'Hóa đơn / Cọc';
                    } elseif (str_contains($category, 'contact') || str_contains(strtolower($n->title), 'yêu cầu') || str_contains(strtolower($n->title), 'liên hệ')) {
                        $iconClass = 'fa-solid fa-comments';
                        $iconBg = 'bg-amber-100 text-amber-700 border border-amber-200';
                        $badgeBg = 'bg-amber-50 text-amber-700 border border-amber-200';
                        $badgeText = 'Liên hệ';
                    } elseif ($type === 'success') {
                        $iconClass = 'fa-solid fa-circle-check';
                        $iconBg = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                        $badgeBg = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                        $badgeText = 'Thành công';
                    } elseif ($type === 'error' || $type === 'danger') {
                        $iconClass = 'fa-solid fa-circle-xmark';
                        $iconBg = 'bg-rose-100 text-rose-700 border border-rose-200';
                        $badgeBg = 'bg-rose-50 text-rose-700 border border-rose-200';
                        $badgeText = 'Cảnh báo';
                    }
                @endphp

                <div id="notif-card-{{ $notifId }}" 
                     class="rounded-xl p-4 sm:p-5 transition-all duration-200 shadow-sm border {{ $isUnread ? 'bg-teal-50/30 border-teal-200/90 ring-1 ring-teal-200/60' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                    <div class="flex items-start gap-3.5 sm:gap-4">
                        <!-- Icon Thông báo -->
                        <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0 text-base shadow-xs mt-0.5">
                            <i class="{{ $iconClass }}"></i>
                        </div>

                        <!-- Nội dung Thông báo -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 mb-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $badgeBg }}">
                                        {{ $badgeText }}
                                    </span>
                                    @if($isUnread)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-teal-700 bg-teal-100/90 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-pulse"></span> Mới
                                        </span>
                                    @endif
                                </div>

                                <!-- Nút thao tác nhanh góc phải từng dòng -->
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($isUnread)
                                        <button type="button" 
                                                onclick="markSingleAsRead('{{ $notifId }}')" 
                                                title="Đánh dấu đã đọc" 
                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-teal-600 hover:bg-teal-50 flex items-center justify-center transition text-xs cursor-pointer">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                    <button type="button" 
                                            onclick="deleteSingleNotification('{{ $notifId }}')" 
                                            title="Xóa thông báo này" 
                                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition text-xs cursor-pointer">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tiêu đề -->
                            <h3 class="text-sm sm:text-base leading-snug {{ $isUnread ? 'font-extrabold text-slate-900' : 'font-medium text-slate-700' }}">
                                {{ $n->title }}
                            </h3>

                            <!-- Nội dung chi tiết -->
                            <p class="text-xs sm:text-sm mt-1 leading-relaxed break-words {{ $isUnread ? 'font-medium text-slate-700' : 'text-slate-500' }}">
                                {{ $n->message }}
                            </p>

                            @php
                                $attachedPost = null;
                                if (!empty($n->post_id)) {
                                    $attachedPost = \App\Models\Post::where('_id', $n->post_id)->first() ?: \App\Models\Post::find($n->post_id);
                                } elseif (str_contains($category, 'complaint') || str_contains(strtolower($n->title . ' ' . $n->message), 'khiếu nại')) {
                                    if (preg_match('/#?KN-[A-Z0-9]+/i', $n->message, $matches)) {
                                        $matchedCode = ltrim($matches[0], '#');
                                        $foundTicket = \App\Models\SupportTicket::where('ticket_code', 'like', "%{$matchedCode}%")
                                            ->orWhere('complaint_code', 'like', "%{$matchedCode}%")
                                            ->first();
                                        if ($foundTicket && !empty($foundTicket->post_id)) {
                                            $attachedPost = \App\Models\Post::where('_id', $foundTicket->post_id)->first() ?: \App\Models\Post::find($foundTicket->post_id);
                                        }
                                    }
                                }
                            @endphp

                            @if($attachedPost)
                                <div class="mt-2.5 p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200/90 hover:border-teal-300 hover:bg-teal-50/40 transition-all group max-w-xl">
                                    <a href="{{ url('/chitietbaidang/' . ($attachedPost->_id ?? $attachedPost->id)) }}" class="flex items-center gap-3">
                                        @php
                                            $thumb = $attachedPost->avatar ?? ($attachedPost->hinh_anh[0] ?? ($attachedPost->images[0] ?? null));
                                        @endphp
                                        @if($thumb)
                                            <img src="{{ $thumb }}" alt="{{ $attachedPost->title }}" class="w-12 h-12 rounded-lg object-cover shrink-0 border border-slate-200 shadow-2xs group-hover:scale-105 transition-transform">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-100/80 px-1.5 py-0.5 rounded">Bài đăng khiếu nại</span>
                                                @if(!empty($attachedPost->gia_thue ?? $attachedPost->price))
                                                    <span class="text-xs font-black text-emerald-600 ml-auto">
                                                        {{ is_numeric($attachedPost->gia_thue ?? $attachedPost->price) ? number_format($attachedPost->gia_thue ?? $attachedPost->price) . ' đ/tháng' : ($attachedPost->gia_thue ?? $attachedPost->price) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-teal-700 truncate mt-0.5">
                                                {{ $attachedPost->title }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                                <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                                                <span>{{ $attachedPost->dia_chi ?? ($attachedPost->address ?? 'RentHome') }}</span>
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-slate-400 group-hover:text-teal-600 pl-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <!-- Meta: Thời gian & Link điều hướng -->
                            <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-2.5 border-t border-slate-100 text-[11px] text-slate-400">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <i class="fa-regular fa-clock text-[11px] text-slate-400"></i>
                                        {{ \Carbon\Carbon::parse($n->created_at)->format('H:i - d/m/Y') }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span>{{ \Carbon\Carbon::parse($n->created_at)->locale('vi')->diffForHumans() }}</span>
                                </div>

                                <!-- Link hành động trực tiếp nếu liên quan đến hợp đồng/liên hệ/phòng trọ -->
                                @php
                                    $actionUrl = $n->action_url ?? null;
                                    // Chặn triệt để link dẫn về Overview của chủ nhà
                                    if ($actionUrl && str_contains($actionUrl, 'Overview')) {
                                        $actionUrl = null;
                                    }
                                    if ($attachedPost) {
                                        $actionUrl = url('/chitietbaidang/' . ($attachedPost->_id ?? $attachedPost->id));
                                        $actionLabel = 'Xem bài đăng';
                                    } elseif (!$actionUrl || $actionUrl === url('/thong-bao') || $actionUrl === url('/thong-bao-ca-nhan')) {
                                        if (str_contains($category, 'contract') || str_contains(strtolower($n->title . ' ' . $n->message), 'hợp đồng') || str_contains(strtolower($n->title . ' ' . $n->message), 'tiền cọc')) {
                                            $actionUrl = route('nha-dang-thue');
                                            $actionLabel = 'Xem tại Nhà đang thuê';
                                        } elseif (str_contains($category, 'contact') || str_contains(strtolower($n->title . ' ' . $n->message), 'liên hệ')) {
                                            $actionUrl = url('/lich-su-lien-he');
                                            $actionLabel = 'Xem Lịch sử liên hệ';
                                        } elseif (!empty($n->post_id)) {
                                            $actionUrl = url('/chitietbaidang/' . $n->post_id);
                                            $actionLabel = 'Xem bài đăng';
                                        } else {
                                            $actionUrl = null;
                                        }
                                    } else {
                                        $actionLabel = (str_contains($category, 'building') || !empty($n->building_id)) ? 'Quản lý tòa nhà' : 'Xem chi tiết';
                                    }
                                @endphp

                                @if($actionUrl)
                                    <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1 font-bold text-teal-600 hover:text-teal-700 hover:underline">
                                        <span>{{ $actionLabel ?? 'Xem chi tiết' }}</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-16 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Không có thông báo nào</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        {{ ($currentTab ?? 'all') === 'unread' ? 'Bạn đã đọc toàn bộ thông báo! Không có thông báo mới.' : 'Bạn chưa nhận được thông báo nào từ hệ thống hoặc chủ nhà.' }}
                    </p>
                    <div class="mt-5 flex items-center justify-center gap-3">
                        @if(($currentTab ?? 'all') !== 'all')
                            <a href="{{ route('thongbao.canhan', ['tab' => 'all']) }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                Xem tất cả thông báo
                            </a>
                        @endif
                        <a href="{{ url('/baidang') }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition">
                            Khám phá phòng trọ
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Phân trang (Pagination) -->
        @if($notifications->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $notifications->links('pagination::tailwind') }}
            </div>
        @endif

    </main>

    <!-- Footer Chung -->
    @include('partials.footer')

    <!-- Interactive Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // 1. Đánh dấu tất cả là đã đọc
        function markAllAsRead() {
            Swal.fire({
                title: 'Đánh dấu đã đọc?',
                text: 'Toàn bộ thông báo chưa đọc của bạn sẽ được chuyển thành đã đọc.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("thongbao.markAllRead") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message || 'Đã đánh dấu tất cả thông báo là đã đọc.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Lỗi', 'Không thể hoàn tất yêu cầu lúc này.', 'error');
                    });
                }
            });
        }

        // 2. Xóa toàn bộ thông báo đã đọc
        function deleteReadNotifications() {
            Swal.fire({
                title: 'Xóa thông báo đã đọc?',
                text: 'Tất cả các thông báo bạn đã đọc sẽ bị xóa vĩnh viễn khỏi danh sách.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("thongbao.deleteRead") }}', {
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa!',
                                text: data.message || 'Đã xóa toàn bộ thông báo đã đọc.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            Swal.fire('Lỗi', data.message || 'Không thể xóa thông báo', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ.', 'error');
                    });
                }
            });
        }

        // 3. Đánh dấu 1 thông báo là đã đọc
        function markSingleAsRead(id) {
            fetch(`/thong-bao-ca-nhan/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`notif-card-${id}`);
                    if (card) {
                        card.className = 'rounded-xl p-4 sm:p-5 transition-all duration-200 shadow-sm border bg-white border-slate-200/80 hover:border-slate-300';
                        const titleEl = card.querySelector('h3');
                        if (titleEl) {
                            titleEl.className = 'text-sm sm:text-base leading-snug font-medium text-slate-700';
                        }
                        const pEl = card.querySelector('p');
                        if (pEl) {
                            pEl.className = 'text-xs sm:text-sm mt-1 leading-relaxed break-words text-slate-500';
                        }
                        // Xóa nút check
                        const btnCheck = card.querySelector('button[title="Đánh dấu đã đọc"]');
                        if (btnCheck) btnCheck.remove();
                        // Xóa badge "Mới"
                        const newBadge = card.querySelector('.animate-pulse')?.parentElement;
                        if (newBadge) newBadge.remove();
                    }
                }
            })
            .catch(err => console.error(err));
        }

        // 4. Xóa 1 thông báo
        function deleteSingleNotification(id) {
            Swal.fire({
                title: 'Xóa thông báo?',
                text: 'Bạn có chắc chắn muốn xóa thông báo này không?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/thong-bao-ca-nhan/${id}`, {
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
                            const card = document.getElementById(`notif-card-${id}`);
                            if (card) {
                                card.style.transition = 'all 0.3s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'translateY(-10px)';
                                setTimeout(() => card.remove(), 300);
                            }
                        } else {
                            Swal.fire('Lỗi', data.message || 'Không thể xóa thông báo này.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ.', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
