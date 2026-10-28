<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Khiếu Nại Nghiêm Trọng (Leo Thang) - Admin Dashboard RentHome</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            padding: 28px 36px;
            background-color: #f8fafc;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .admin-main {
            margin-left: 78px !important;
            width: calc(100% - 78px) !important;
        }

        @media (max-width: 991px) {
            .admin-main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 20px 16px;
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        .swal2-container {
            z-index: 100000 !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Header & Sidebar Admin Dùng Chung -->
    @include('partials.header-admin')

    <!-- Vùng Nội Dung Chính (Main Content) -->
    <main class="admin-main">
        <div class="max-w-[1700px] mx-auto w-full space-y-6">

            <!-- 1. HEADER & BREADCRUMB -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1.5">
                        <a href="{{ url('Admin') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-house text-slate-400"></i> Admin
                        </a>
                        <span class="text-slate-300">/</span>
                        <a href="{{ url('cskh/khieu-nai') }}" class="hover:text-blue-600 transition-colors">Khiếu nại & CSKH</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-rose-600 font-bold">Khiếu nại nghiêm trọng</span>
                    </nav>

                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-rose-600 to-red-600 text-white flex items-center justify-center shadow-lg shadow-rose-600/25">
                            <i class="fa-solid fa-gavel text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                                <span>Khiếu Nại Nghiêm Trọng</span>
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200/80 uppercase tracking-wide">
                                    Admin Quyền Lực
                                </span>
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                Kênh tiếp nhận và áp dụng chế tài đặc quyền cho các hồ sơ khiếu nại nghiêm trọng được bộ phận CSKH leo thang lên Ban Quản Trị.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Tải lại & Quay lại CSKH -->
                <div class="flex items-center gap-3 self-start lg:self-center">
                    <button type="button" onclick="window.location.reload()" class="px-4 py-2.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs rounded-xl shadow-xs hover:bg-slate-50 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-arrows-rotate text-slate-500"></i>
                        <span>Làm mới</span>
                    </button>
                    <a href="{{ url('/cskh/khieu-nai') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-2">
                        <i class="fa-solid fa-headset text-slate-500"></i>
                        <span>Kênh CSKH</span>
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <!-- 2. THẺ THỐNG KÊ (CARDS SUMMARY) -->
            @php
                $totalEscalated = $tickets->count();
                $postComplaintCount = $tickets->filter(fn($t) => !empty($t->post_id) || !empty($t->target_id))->count();
                $staffInvolvedCount = $tickets->filter(fn($t) => !empty($t->staff_id) || ($t->post && !empty($t->post->staff_id ?? $t->post->user_id)))->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-rose-200/90 shadow-xs flex items-center justify-between bg-gradient-to-br from-white to-rose-50/40">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Đơn Cấp 2 Cần Phán Quyết</span>
                        <div class="text-2xl font-extrabold text-rose-700 mt-1">{{ $totalEscalated }}</div>
                        <span class="text-xs text-rose-800/80 font-medium">CSKH đã leo thang lên Admin</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Bài Đăng Bị Phản Ánh</span>
                        <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $postComplaintCount }}</div>
                        <span class="text-xs text-slate-500 font-medium">Đối tượng cần xem xét gỡ tin</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Nhân Viên Liên Quan</span>
                        <div class="text-2xl font-extrabold text-slate-800 mt-1">{{ $staffInvolvedCount }}</div>
                        <span class="text-xs text-slate-500 font-medium">Đối tượng áp dụng chế tài kỷ luật</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
            </div>

            <!-- 3. BẢNG DANH SÁCH CÁC ĐƠN KHIẾU NẠI CẤP 2 -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"></span>
                        <h2 class="text-base font-bold text-slate-900">Danh Sách Hồ Sơ Đang Chờ Admin Xử Lý</h2>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ $totalEscalated }}</span>
                    </div>

                    <!-- Ghi chú nguyên tắc -->
                    <div class="text-xs text-slate-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-rose-500"></i>
                        <span>Thẩm quyền Admin: Gỡ bài vi phạm, Khóa nhân viên sale & Đóng hồ sơ</span>
                    </div>
                </div>

                @if($tickets->isEmpty())
                    <!-- Trạng thái trống -->
                    <div class="p-16 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-2xl mb-4 border border-emerald-100 shadow-xs">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Không Có Hồ Sơ Cấp 2 Chờ Xử Lý</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                            Tuyệt vời! Hiện tại không có đơn khiếu nại nghiêm trọng nào được bộ phận CSKH leo thang lên Ban Quản Trị.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                                    <th class="py-3.5 px-6">Mã Ticket & Thời Gian</th>
                                    <th class="py-3.5 px-6">Bài Đăng Vi Phạm</th>
                                    <th class="py-3.5 px-6">Nhân Viên Phụ Trách</th>
                                    <th class="py-3.5 px-6 min-w-[300px]">Ghi Chú Của CSKH (Lý Do Leo Thang)</th>
                                    <th class="py-3.5 px-6 text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                @foreach($tickets as $ticket)
                                    @php
                                        $ticketId = (string)($ticket->_id ?? $ticket->id);
                                        $ticketCode = $ticket->formatted_ticket_code;
                                        $post = $ticket->post ?: (!empty($ticket->post_id) ? (\App\Models\Post::where('_id', $ticket->post_id)->first() ?: \App\Models\Post::find($ticket->post_id)) : (!empty($ticket->target_id) ? (\App\Models\Post::where('_id', $ticket->target_id)->first() ?: \App\Models\Post::find($ticket->target_id)) : null));
                                        
                                        // Tìm thông tin staff
                                        $staff = $ticket->staff;
                                        if (!$staff && $ticket->staff_id) {
                                            $staff = \App\Models\User::where('_id', $ticket->staff_id)->first() ?: \App\Models\User::find($ticket->staff_id);
                                        }
                                        if (!$staff && $post) {
                                            $staffId = $post->staff_id ?? ($post->user_id ?? null);
                                            if ($staffId) {
                                                $staff = \App\Models\User::where('_id', $staffId)->first() ?: \App\Models\User::find($staffId);
                                            }
                                        }

                                        $staffName = $staff ? ($staff->account_name ?? ($staff->username ?? $staff->email)) : 'Chưa gán NV';
                                        $staffPhone = $staff ? ($staff->phone ?? 'Không có SĐT') : 'N/A';

                                        // Ghi chú của CSKH
                                        $cskhNote = $ticket->cskh_note 
                                            ?: ($ticket->escalation_reason 
                                            ?: ($ticket->admin_note 
                                            ?: ($ticket->note ?: 'CSKH đã leo thang đơn này lên Ban Quản Trị do nhận thấy dấu hiệu vi phạm quy chuẩn nghiêm trọng.')));

                                        // Nội dung gốc của khách
                                        $originalContent = $ticket->content ?? ($ticket->message ?? ($ticket->reason ?? 'Không có nội dung chi tiết.'));

                                        // Ảnh đính kèm
                                        $images = [];
                                        if (!empty($ticket->images) && is_array($ticket->images)) {
                                            $images = $ticket->images;
                                        } elseif (!empty($ticket->image)) {
                                            $images = [$ticket->image];
                                        }

                                        // Dữ liệu đóng gói cho Modal JS
                                        $payload = [
                                            'id' => $ticketId,
                                            'code' => $ticketCode,
                                            'created_at' => $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'Mới đây',
                                            'escalated_at' => $ticket->escalated_at ? $ticket->escalated_at->format('d/m/Y H:i') : ($ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : ''),
                                            'sender_name' => $ticket->sender_name ?? ($ticket->user->username ?? 'Khách hàng'),
                                            'sender_email' => $ticket->sender_email ?? ($ticket->email ?? 'N/A'),
                                            'sender_phone' => $ticket->sender_phone ?? ($ticket->phone ?? 'N/A'),
                                            'topic' => $ticket->subject ?? ($ticket->reason ?? 'Khiếu nại bài đăng'),
                                            'content' => $originalContent,
                                            'images' => $images,
                                            'cskh_note' => $cskhNote,
                                            'is_post_hidden' => (bool)($ticket->is_post_hidden || ($post && in_array($post->status, ['hidden', 'inactive', 'rejected']))),
                                            'is_staff_locked' => (bool)($ticket->is_staff_locked || ($staff && !empty($staff->is_locked))),
                                            'post' => $post ? [
                                                'id' => (string)($post->_id ?? $post->id),
                                                'title' => $post->title ?? 'Bài đăng #'.$ticket->post_id,
                                                'price' => number_format($post->price ?? 0) . ' ' . ($post->price_unit ?? 'VNĐ/tháng'),
                                                'address' => $post->address ?? '',
                                                'status' => $post->status ?? 'active',
                                                'image' => !empty($post->images[0]) ? (str_starts_with($post->images[0], 'http') ? $post->images[0] : asset(ltrim($post->images[0], '/'))) : asset('img/no-image.png'),
                                                'url' => url('/chitietbaidang/' . ($post->_id ?? $post->id))
                                            ] : null,
                                            'staff' => $staff ? [
                                                'id' => (string)($staff->_id ?? $staff->id),
                                                'name' => $staffName,
                                                'phone' => $staffPhone,
                                                'email' => $staff->email ?? '',
                                                'is_locked' => (bool)$staff->is_locked
                                            ] : null
                                        ];
                                    @endphp

                                    <tr id="row-ticket-{{ $ticketId }}" class="hover:bg-slate-50/80 transition-colors">
                                        <!-- CỘT 1: MÃ TICKET & THỜI GIAN ĐẨY LÊN -->
                                        <td class="py-4 px-6 align-top">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-extrabold text-xs px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                                        {{ $ticketCode }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">
                                                        <i class="fa-solid fa-angles-up"></i> Cấp 2
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5 pt-0.5">
                                                    <i class="fa-regular fa-clock text-slate-400"></i>
                                                    <span>Đẩy lên: {{ $ticket->escalated_at ? $ticket->escalated_at->format('d/m/Y H:i') : ($ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : 'Mới đây') }}</span>
                                                </div>
                                                <div class="text-[11px] text-slate-400">
                                                    Gửi ban đầu: {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y') : '' }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- CỘT 2: BÀI ĐĂNG VI PHẠM -->
                                        <td class="py-4 px-6 align-top">
                                            @if($post)
                                                <div class="flex items-start gap-3 min-w-[240px]">
                                                    <div class="w-12 h-12 rounded-xl bg-slate-100 shrink-0 overflow-hidden border border-slate-200 shadow-2xs">
                                                        @php
                                                            $pImg = !empty($post->images[0]) ? $post->images[0] : (!empty($post->avatar) ? $post->avatar : null);
                                                            $pImgUrl = $pImg ? (str_starts_with($pImg, 'http') ? $pImg : asset(ltrim($pImg, '/'))) : asset('img/no-image.png');
                                                        @endphp
                                                        <img src="{{ $pImgUrl }}" alt="Ảnh bài đăng" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="min-w-0 space-y-1">
                                                        <a href="{{ url('/chitietbaidang/' . ($post->_id ?? $post->id)) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-2 transition-colors flex items-center gap-1 group">
                                                            <span>{{ $post->title }}</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-blue-600 shrink-0"></i>
                                                        </a>
                                                        <div class="text-[11px] text-slate-500 truncate">{{ $post->address ?? 'Không có địa chỉ' }}</div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-extrabold text-emerald-600">{{ number_format($post->price ?? 0) }} đ/th</span>
                                                            @if(in_array($post->status, ['hidden', 'inactive', 'rejected']) || !empty($ticket->is_post_hidden))
                                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                                    Đã gỡ bài
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Không tìm thấy bài đăng (hoặc đã bị xóa)</span>
                                            @endif
                                        </td>

                                        <!-- CỘT 3: NHÂN VIÊN PHỤ TRÁCH -->
                                        <td class="py-4 px-6 align-top">
                                            @if($staff)
                                                <div class="space-y-1 min-w-[180px]">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-xs shrink-0">
                                                            {{ strtoupper(mb_substr($staffName, 0, 1, 'UTF-8')) }}
                                                        </div>
                                                        <div class="min-w-0">
                                                            <span class="font-bold text-slate-900 block truncate">{{ $staffName }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 flex items-center gap-1 pl-9">
                                                        <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                                        <a href="tel:{{ $staffPhone }}" class="hover:text-blue-600 font-semibold">{{ $staffPhone }}</a>
                                                    </div>
                                                    @if(!empty($staff->is_locked) || !empty($ticket->is_staff_locked))
                                                        <div class="pl-9">
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">
                                                                <i class="fa-solid fa-lock text-[9px]"></i> Đã bị khóa
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Không có thông tin NV</span>
                                            @endif
                                        </td>

                                        <!-- CỘT 4: GHI CHÚ CỦA CSKH (HIỂN THỊ NỔI BẬT ĐỎ ĐÔ) -->
                                        <td class="py-4 px-6 align-top">
                                            <div class="p-3.5 rounded-xl bg-rose-50/90 border border-rose-200/90 shadow-2xs space-y-1.5">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wide text-rose-900">
                                                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                                        <span>CSKH Báo Cáo Khẩn Cấp:</span>
                                                    </span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/80 text-rose-800 border border-rose-200">
                                                        Nghiêm trọng
                                                    </span>
                                                </div>
                                                <!-- Text màu đỏ đô nổi bật -->
                                                <p class="text-xs sm:text-sm font-bold text-[#881337] leading-relaxed whitespace-pre-line">
                                                    "{{ $cskhNote }}"
                                                </p>
                                            </div>
                                        </td>

                                        <!-- CỘT 5: THAO TÁC (NÚT ĐỎ "MỞ HỒ SƠ XỬ LÝ") -->
                                        <td class="py-4 px-6 align-middle text-center shrink-0">
                                            <button type="button"
                                                    onclick="openCourtModal('{{ $ticketId }}')"
                                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold text-xs shadow-md shadow-rose-600/25 hover:shadow-lg transition-all transform hover:scale-[1.02] cursor-pointer whitespace-nowrap">
                                                <i class="fa-solid fa-scale-balanced"></i>
                                                <span>Xử lý</span>
                                            </button>

                                            <!-- Lưu dữ liệu JSON ẩn trong DOM để nạp vào Modal ngay lập tức -->
                                            <script id="ticket-data-{{ $ticketId }}" type="application/json">
                                                {!! json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}
                                            </script>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </main>

    @php
        if (empty($ticket) || empty($ticket->id)) {
            $ticket = $tickets->first() ?? (object)['id' => '0', '_id' => '0'];
        }
    @endphp

    <!-- ========================================================================================== -->
    <!-- BƯỚC 3: MODAL XỬ LÝ ĐẶC QUYỀN (TÒA ÁN ADMIN) - SIÊU TO 2 CỘT                               -->
    <!-- ========================================================================================== -->
    <div id="court-modal-overlay" class="fixed inset-0 z-[9999] bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-5 transition-opacity" style="z-index: 9999;">
        <div class="relative w-full max-w-6xl max-h-[92vh] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden animate-fade-in" onclick="event.stopPropagation()">
            
            <!-- MODAL HEADER -->
            <div class="px-6 py-4.5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-700">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg shadow-md shrink-0">
                        <i class="fa-solid fa-gavel"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                <span>TÒA ÁN PHÁN QUYẾT ADMIN</span>
                                <span class="font-mono font-bold text-xs px-2.5 py-0.5 rounded-md bg-rose-500/20 text-rose-300 border border-rose-500/40" id="modal-ticket-code">
                                    #KN-00000
                                </span>
                            </h3>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-rose-500 text-white uppercase tracking-wider">
                                Cấp 2 Escalated
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 font-medium truncate mt-0.5" id="modal-ticket-subtitle">
                            Hồ sơ leo thang từ CSKH RentHome
                        </p>
                    </div>
                </div>

                <button type="button" onclick="closeCourtModal()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- MODAL BODY: 2 CỘT -->
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-slate-50/60">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- ======================================================== -->
                    <!-- CỘT TRÁI (BẰNG CHỨNG) - CHIẾM 7/12                       -->
                    <!-- ======================================================== -->
                    <div class="lg:col-span-7 space-y-5">
                        
                        <!-- 1. Ghi chú khẩn cấp từ CSKH (Đỏ đô nổi bật) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-300/80 shadow-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold uppercase tracking-wide text-rose-900 flex items-center gap-2">
                                    <i class="fa-solid fa-flag text-rose-600 text-sm"></i>
                                    <span>Lý do CSKH đẩy đơn lên Admin:</span>
                                </span>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-200 text-rose-900">
                                    Khẩn cấp
                                </span>
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-[#881337] leading-relaxed bg-white/80 p-3.5 rounded-xl border border-rose-200" id="modal-cskh-note">
                                Đang tải ghi chú CSKH...
                            </div>
                        </div>

                        <!-- 2. Thông tin & Nội dung khiếu nại gốc của khách hàng -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3.5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <span class="text-xs font-extrabold uppercase tracking-wide text-slate-700 flex items-center gap-2">
                                    <i class="fa-solid fa-user-pen text-blue-600"></i>
                                    <span>Nội dung khách hàng khiếu nại:</span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium" id="modal-created-time">
                                    Thời gian gửi
                                </span>
                            </div>

                            <!-- Người gửi -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <div><strong class="text-slate-800">Người gửi:</strong> <span id="modal-sender-name">--</span></div>
                                <div><strong class="text-slate-800">SĐT:</strong> <span id="modal-sender-phone">--</span></div>
                                <div><strong class="text-slate-800">Email:</strong> <span id="modal-sender-email">--</span></div>
                            </div>

                            <!-- Chủ đề khiếu nại -->
                            <div>
                                <span class="text-xs font-bold text-slate-500 uppercase block mb-1">Chủ đề:</span>
                                <h4 class="text-sm font-bold text-slate-900" id="modal-subject">--</h4>
                            </div>

                            <!-- Nội dung chi tiết -->
                            <div>
                                <span class="text-xs font-bold text-slate-500 uppercase block mb-1">Chi tiết phản ánh:</span>
                                <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50/80 p-4 rounded-xl border border-slate-200/80" id="modal-original-content">
                                    --
                                </div>
                            </div>

                            <!-- 3. Hình ảnh bằng chứng do khách gửi -->
                            <div id="modal-images-container" class="space-y-2 pt-2">
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                                    <i class="fa-solid fa-images text-slate-400"></i>
                                    <span>Hình ảnh bằng chứng đính kèm:</span>
                                </span>
                                <div id="modal-images-grid" class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                                    <!-- Render via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- 4. Tóm tắt Bài đăng bị khiếu nại -->
                        <div id="modal-post-card" class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-2">
                            <span class="text-xs font-extrabold uppercase tracking-wide text-slate-500 flex items-center gap-2">
                                <i class="fa-solid fa-home text-slate-400"></i>
                                <span>Bài đăng bị phản ánh:</span>
                            </span>
                            <div class="flex items-center justify-between gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 rounded-lg bg-slate-200 overflow-hidden shrink-0">
                                        <img id="modal-post-img" src="" alt="Post" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate" id="modal-post-title">--</h4>
                                        <p class="text-[11px] text-slate-500 truncate" id="modal-post-address">--</p>
                                        <span class="text-xs font-bold text-emerald-600" id="modal-post-price">--</span>
                                    </div>
                                </div>
                                <a id="modal-post-link" href="#" target="_blank" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-2xs transition-colors">
                                    <span>Mở xem tin</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- ======================================================== -->
                    <!-- CỘT PHẢI (BẢNG ĐIỀU KHIỂN ĐẶC QUYỀN) - CHIẾM 5/12        -->
                    <!-- ======================================================== -->
                    <div class="lg:col-span-5 space-y-5">
                        
                        <!-- KHỐI 1: NÚT HÀNH ĐỘNG NHANH ĐẶC QUYỀN (GỠ BÀI & KHÓA NV) -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                                <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">
                                    1
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Biện Pháp Xử Lý Tức Thì</h4>
                                    <p class="text-[11px] text-slate-500">Chế tài đặc quyền áp dụng ngay lên hệ thống</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <!-- Nút Đỏ Cảnh Báo: Gỡ bài đăng -->
                                <div>
                                    <button type="button"
                                            id="btn-hide-post"
                                            onclick="triggerHidePost()"
                                            class="w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-red-600/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                        <span id="btn-hide-post-text">Gỡ / Ẩn Bài Đăng Này</span>
                                    </button>
                                    <p class="text-[11px] text-slate-400 mt-1 pl-1">
                                        Bài đăng sẽ lập tức biến mất khỏi Trang chủ và chuyển trạng thái sang [Hidden].
                                    </p>
                                </div>

                                <!-- Nút Đen: Khóa tài khoản nhân viên sale -->
                                <div>
                                    <button type="button"
                                            id="btn-lock-staff"
                                            onclick="triggerLockStaff()"
                                            class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs sm:text-sm shadow-md shadow-slate-900/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                        <i class="fa-solid fa-ban text-base text-rose-400"></i>
                                        <span id="btn-lock-staff-text">Khóa Tài Khoản Nhân Viên</span>
                                    </button>
                                    <p class="text-[11px] text-slate-400 mt-1 pl-1" id="staff-name-target-desc">
                                        Nhân viên phụ trách sẽ bị thu hồi quyền đăng nhập vào ứng dụng.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- KHỐI 2: KẾT ÁN & ĐÓNG TICKET -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-4">
                            <form id="adminVerdictForm" method="POST" action="{{ route('admin.tickets.resolve', $ticket->id) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black">
                                            2
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900">Phán Quyết Cuối Cùng & Đóng Hồ Sơ</h4>
                                            <p class="text-[11px] text-slate-500">Admin tự nhập phản hồi hoặc chọn mẫu gợi ý gửi tới Khách hàng và CSKH</p>
                                        </div>
                                    </div>

                                    <!-- Mẫu phán quyết mẫu chọn nhanh -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[11px] font-bold text-slate-500 uppercase">Mẫu gợi ý nhanh (tùy chọn):</span>
                                            <button type="button" onclick="clearVerdictNote()" class="text-[11px] text-slate-400 hover:text-rose-600 font-semibold transition-colors flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-eraser"></i> Xóa trắng để tự nhập
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                                            <button type="button" onclick="setVerdictTemplate(1)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors cursor-pointer">
                                                Gỡ bài + Xử lý NV
                                            </button>
                                            <button type="button" onclick="setVerdictTemplate(2)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors cursor-pointer">
                                                Đã xác minh bài vi phạm
                                            </button>
                                            <button type="button" onclick="setVerdictTemplate(3)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors cursor-pointer">
                                                Hòa giải & Đền bù
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Textarea: Chỉ thị điều hành gửi Người đăng bài -->
                                    <div>
                                        <label for="modal-verdict-note" class="text-xs font-bold text-slate-700 block mb-1.5">
                                            Chỉ thị / Yêu cầu xử lý gửi đến Người đăng bài: <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea id="modal-verdict-note"
                                                  name="resolution_note"
                                                  rows="6"
                                                  oninput="updateVerdictCharCount()"
                                                  placeholder="Admin nhập chỉ thị điều hành, yêu cầu đính chính bài đăng hoặc hình phạt gửi trực tiếp đến nhân viên quản lý bài đăng..."
                                                  class="w-full p-3.5 text-xs sm:text-sm bg-white border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-xl transition-all outline-hidden custom-scrollbar font-medium text-slate-800 leading-relaxed"></textarea>
                                        <div class="flex items-start justify-between gap-3 mt-1.5 text-[11px] text-slate-500 leading-normal">
                                            <span class="flex-1 min-w-0">Khách hàng báo cáo sẽ không đọc được nội dung này. Hệ thống chỉ gửi thông báo trạng thái 'Đã giải quyết' cho khách.</span>
                                            <span id="verdict-char-count" class="shrink-0 whitespace-nowrap font-medium text-slate-400">0 ký tự</span>
                                        </div>
                                    </div>

                                    <!-- Nút Gửi chỉ thị & Nút Bác bỏ -->
                                    <div class="space-y-2.5 pt-1">
                                        <!-- Nút chính: Gửi chỉ thị điều hành & Đóng Ticket -->
                                        <button type="button"
                                                id="btn-resolve-ticket"
                                                onclick="triggerResolveTicket()"
                                                class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-emerald-600/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-check-double text-base shrink-0"></i>
                                            <span id="btn-resolve-ticket-text" class="whitespace-nowrap">Gửi chỉ thị & Đóng Ticket</span>
                                        </button>

                                        <!-- Nút phụ: Bác bỏ & Minh oan cho Sale -->
                                        <button type="submit"
                                                formaction="{{ route('admin.ticket.reject', $ticket->id) }}"
                                                class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-300 hover:border-rose-300 font-bold text-xs sm:text-sm shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]">
                                            <i class="fa-solid fa-ban text-rose-500 shrink-0"></i>
                                            <span class="whitespace-nowrap">⛔ Bác bỏ & Minh oan cho Sale</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

            <!-- MODAL FOOTER -->
            <div class="px-6 py-3.5 bg-slate-100 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 shrink-0">
                <span>Hành động của Admin được ghi nhật ký hoạt động hệ thống (AdminActivity).</span>
                <button type="button" onclick="closeCourtModal()" class="px-4 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition-colors">
                    Đóng cửa sổ
                </button>
            </div>

        </div>
    </div>

    <!-- Lightbox Zoom Image Modal -->
    <div id="image-lightbox-modal" class="fixed inset-0 z-[10000] bg-slate-950/90 backdrop-blur-sm hidden items-center justify-center p-4" style="z-index: 10000;" onclick="closeImageLightbox()">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">
            <button type="button" onclick="closeImageLightbox()" class="absolute -top-10 right-0 text-white hover:text-rose-400 text-2xl font-bold transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img id="image-lightbox-src" src="" alt="Enlarged proof" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
        </div>
    </div>

    <!-- JAVASCRIPT XỬ LÝ SỰ KIỆN & AJAX CHO TÒA ÁN ADMIN -->
    <script>
        let currentTicketId = null;
        let currentTicketData = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        /**
         * Mở Modal Hồ Sơ Xử Lý Đặc Quyền
         */
        function openCourtModal(ticketId) {
            currentTicketId = ticketId;

            // Lấy dữ liệu payload từ thẻ script
            const dataScript = document.getElementById('ticket-data-' + ticketId);
            if (!dataScript) {
                Swal.fire('Lỗi', 'Không tìm thấy dữ liệu ticket!', 'error');
                return;
            }

            try {
                currentTicketData = JSON.parse(dataScript.textContent);
            } catch (e) {
                console.error(e);
                Swal.fire('Lỗi', 'Không thể đọc dữ liệu ticket!', 'error');
                return;
            }

            const data = currentTicketData;

            // Cập nhật action của form và formaction của nút reject theo ticketId
            const formVerdict = document.getElementById('adminVerdictForm');
            if (formVerdict) {
                formVerdict.action = `/admin/tickets/${ticketId}/resolve`;
            }
            const btnReject = formVerdict?.querySelector('button[formaction]');
            if (btnReject) {
                btnReject.setAttribute('formaction', `/admin/tickets/${ticketId}/reject`);
            }

            // Điền Header Modal
            document.getElementById('modal-ticket-code').textContent = data.code;
            document.getElementById('modal-ticket-subtitle').textContent = 'Thời gian leo thang: ' + (data.escalated_at || data.created_at);

            // Cột Trái: Bằng chứng
            document.getElementById('modal-cskh-note').textContent = data.cskh_note || 'Không có ghi chú cụ thể.';
            document.getElementById('modal-created-time').textContent = 'Gửi lúc: ' + data.created_at;
            document.getElementById('modal-sender-name').textContent = data.sender_name || 'Khách hàng';
            document.getElementById('modal-sender-phone').textContent = data.sender_phone || 'Không có';
            document.getElementById('modal-sender-email').textContent = data.sender_email || 'Không có';
            document.getElementById('modal-subject').textContent = data.topic;
            document.getElementById('modal-original-content').textContent = data.content;

            // Hình ảnh bằng chứng
            const imgGrid = document.getElementById('modal-images-grid');
            imgGrid.innerHTML = '';
            if (data.images && data.images.length > 0) {
                document.getElementById('modal-images-container').classList.remove('hidden');
                data.images.forEach(img => {
                    const fullImgUrl = img.startsWith('http') ? img : ('/' + img.replace(/^\/+/, ''));
                    const div = document.createElement('div');
                    div.className = 'aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100 cursor-pointer hover:shadow-md transition-all group relative';
                    div.onclick = () => openImageLightbox(fullImgUrl);
                    div.innerHTML = `
                        <img src="${fullImgUrl}" alt="Proof" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">
                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                        </div>
                    `;
                    imgGrid.appendChild(div);
                });
            } else {
                document.getElementById('modal-images-container').classList.add('hidden');
            }

            // Bài đăng
            const postCard = document.getElementById('modal-post-card');
            if (data.post) {
                postCard.classList.remove('hidden');
                document.getElementById('modal-post-img').src = data.post.image;
                document.getElementById('modal-post-title').textContent = data.post.title;
                document.getElementById('modal-post-address').textContent = data.post.address;
                document.getElementById('modal-post-price').textContent = data.post.price;
                document.getElementById('modal-post-link').href = data.post.url;
            } else {
                postCard.classList.add('hidden');
            }

            // Cột Phải: Trạng thái nút hành động nhanh
            const btnHide = document.getElementById('btn-hide-post');
            const btnHideText = document.getElementById('btn-hide-post-text');
            if (data.is_post_hidden) {
                btnHide.disabled = true;
                btnHide.className = 'w-full py-3 px-4 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2';
                btnHideText.textContent = '✓ Bài đăng đã được gỡ';
            } else {
                btnHide.disabled = false;
                btnHide.className = 'w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-red-600/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer';
                btnHideText.textContent = 'Gỡ / Ẩn Bài Đăng Này';
            }

            updateStaffLockButton(Boolean(data.is_staff_locked || (data.staff && data.staff.is_locked)));

            // Để trống textarea để Admin tự do nhập phản hồi theo từng vụ việc
            const verdictTextarea = document.getElementById('modal-verdict-note');
            if (verdictTextarea) {
                verdictTextarea.value = '';
                updateVerdictCharCount();
            }

            // Hiện Modal
            const overlay = document.getElementById('court-modal-overlay');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Tự động focus vào ô nhập phán quyết sau khi mở modal
            setTimeout(() => {
                if (verdictTextarea) verdictTextarea.focus();
            }, 300);
        }

        /**
         * Cập nhật số ký tự đã nhập
         */
        function updateVerdictCharCount() {
            const textarea = document.getElementById('modal-verdict-note');
            const countEl = document.getElementById('verdict-char-count');
            if (textarea && countEl) {
                countEl.textContent = textarea.value.length + ' ký tự';
            }
        }

        /**
         * Xóa trắng ô phản hồi để Admin tự gõ từ đầu
         */
        function clearVerdictNote() {
            const textarea = document.getElementById('modal-verdict-note');
            if (textarea) {
                textarea.value = '';
                textarea.focus();
                updateVerdictCharCount();
            }
        }

        /**
         * Đóng Modal
         */
        function closeCourtModal() {
            const overlay = document.getElementById('court-modal-overlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = '';
            currentTicketId = null;
            currentTicketData = null;
        }

        /**
         * Đóng Modal khi click ngoài
         */
        document.getElementById('court-modal-overlay')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCourtModal();
            }
        });

        /**
         * Chọn nhanh mẫu phán quyết gợi ý
         */
        function setVerdictTemplate(type) {
            const textarea = document.getElementById('modal-verdict-note');
            const rawCode = currentTicketData?.code || '';
            const code = rawCode.startsWith('#') ? rawCode : ('#' + rawCode);
            const staffName = currentTicketData?.staff?.name || 'nhân viên';

            if (type === 1) {
                textarea.value = `Ban Quản Trị RentHome thông báo: Bài đăng liên quan đến hồ sơ khiếu nại ${code} có sai phạm nghiêm trọng và đã bị gỡ vĩnh viễn khỏi hệ thống. Tài khoản của bạn bị đình chỉ xem xét vi phạm kỷ luật quy chuẩn RentHome.`;
            } else if (type === 2) {
                textarea.value = `Ban Quản Trị RentHome yêu cầu: Bài đăng ${code} có thông tin sai lệch so với thực tế bị khách hàng phản ánh. Yêu cầu bạn kiểm tra, cập nhật và đính chính ngay thông tin đúng thực tế trong vòng 24 giờ tới.`;
            } else if (type === 3) {
                textarea.value = `Ban Quản Trị RentHome thông báo: Vụ việc khiếu nại ${code} đã được Ban Quản Trị giải quyết và phân xử. Yêu cầu bạn thực hiện nghiêm túc các cam kết hỗ trợ và quyền lợi đã thống nhất với người thuê.`;
            }

            textarea.focus();
            updateVerdictCharCount();
        }

        /**
         * HÀNH ĐỘNG 1: GỠ BÀI ĐĂNG (AJAX POST /admin/tickets/{id}/hide-post)
         */
        function triggerHidePost() {
            if (!currentTicketId) return;

            Swal.fire({
                title: 'Xác nhận gỡ bài đăng?',
                text: 'Bài đăng vi phạm sẽ bị ẩn lập tức khỏi Trang chủ và không thể tìm kiếm trên RentHome.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý, Gỡ ngay!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();

                    fetch(`/admin/tickets/${currentTicketId}/hide-post`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            reason: 'Admin gỡ bài đăng vi phạm nghiêm trọng qua Tòa án Cấp 2'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Cập nhật giao diện nút
                            const btnHide = document.getElementById('btn-hide-post');
                            btnHide.disabled = true;
                            btnHide.className = 'w-full py-3 px-4 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2';
                            document.getElementById('btn-hide-post-text').textContent = '✓ BÀI ĐĂNG ĐÃ ĐƯỢC GỠ KHỎI HỆ THỐNG';

                            // Cập nhật trạng thái trong script
                            if (currentTicketData) {
                                currentTicketData.is_post_hidden = true;
                                document.getElementById('ticket-data-' + currentTicketId).textContent = JSON.stringify(currentTicketData);
                            }
                        } else {
                            Swal.fire('Thất bại', data.message || 'Không thể gỡ bài đăng.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                    });
                }
            });
        }

        /**
         * HÀNH ĐỘNG 2: KHÓA / MỞ KHÓA TÀI KHOẢN NHÂN VIÊN SALE (Đồng bộ luồng chuẩn với QL Tài khoản)
         */
        async function triggerLockStaff() {
            if (!currentTicketId) return;

            if (!currentTicketData || !currentTicketData.staff) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Không tìm thấy nhân viên',
                    text: 'Hồ sơ này không có tài khoản nhân viên phụ trách để thực hiện thao tác.'
                });
                return;
            }

            const staff = currentTicketData.staff;
            const staffName = staff.name || 'nhân viên sale';
            const isCurrentlyLocked = Boolean(currentTicketData.is_staff_locked || staff.is_locked);

            if (isCurrentlyLocked) {
                // TRƯỜNG HỢP MỞ KHÓA TÀI KHOẢN (Đồng bộ luồng)
                const result = await Swal.fire({
                    title: 'Mở khóa tài khoản?',
                    html: `Bạn có chắc chắn muốn mở khóa cho tài khoản <strong>"${staffName}"</strong>?<br>Người dùng sẽ có thể đăng nhập và sử dụng dịch vụ trở lại.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-lock-open"></i> Đồng ý mở khóa',
                    cancelButtonText: 'Hủy thao tác',
                    reverseButtons: true
                });

                if (result.isConfirmed) {
                    executeStaffLockToggle(true);
                }
            } else {
                // TRƯỜNG HỢP KHÓA TÀI KHOẢN (Đồng bộ popup chuẩn)
                const { value: formValues, isConfirmed } = await Swal.fire({
                    title: 'Khóa tài khoản này?',
                    html: `
                        <div style="text-align: left; font-size: 0.9rem; color: #475569; margin-bottom: 14px;">
                            Bạn có chắc chắn muốn khóa tài khoản <strong>"${staffName}"</strong>?<br>
                            <span style="color: #dc2626; font-weight: 700;">Người dùng sẽ không thể đăng nhập vào hệ thống.</span>
                        </div>
                        <div style="text-align: left; margin-bottom: 12px;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                Lý do khóa tài khoản: <span style="color: #dc2626;">*</span>
                            </label>
                            <input id="swal-input-reason" class="swal2-input" style="margin: 0; width: 100%; font-size: 0.875rem;" placeholder="Nhập lý do (ví dụ: Vi phạm quy định, lừa đảo, spam...)">
                        </div>
                        <div style="text-align: left;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                Thời gian khóa: <span style="color: #dc2626;">*</span>
                            </label>
                            <select id="swal-input-duration" name="lock_duration" class="swal2-select" style="margin: 0; width: 100%; font-size: 0.875rem; display: block; height: 42px; border: 1px solid #d1d5db; border-radius: 8px; padding: 0 12px; background-color: #ffffff;">
                                <option value="1">Khóa 1 ngày</option>
                                <option value="3">Khóa 3 ngày</option>
                                <option value="7" selected>Khóa 7 ngày</option>
                                <option value="30">Khóa 30 ngày (1 tháng)</option>
                                <option value="indefinite">Khóa đến khi mở lại (Thủ công)</option>
                            </select>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-lock"></i> Xác nhận khóa tài khoản',
                    cancelButtonText: 'Hủy bỏ',
                    reverseButtons: true,
                    didOpen: () => {
                        const input = document.getElementById('swal-input-reason');
                        if (input) input.focus();
                    },
                    preConfirm: () => {
                        const reason = document.getElementById('swal-input-reason').value.trim();
                        const duration = document.getElementById('swal-input-duration').value;
                        if (!reason) {
                            Swal.showValidationMessage('Vui lòng nhập lý do khóa tài khoản.');
                            return false;
                        }
                        if (!duration) {
                            Swal.showValidationMessage('Vui lòng chọn thời gian khóa.');
                            return false;
                        }
                        return { lock_reason: reason, lock_duration: duration };
                    }
                });

                if (isConfirmed && formValues) {
                    executeStaffLockToggle(false, formValues);
                }
            }
        }

        /**
         * Gửi AJAX khóa hoặc mở khóa tài khoản nhân viên
         */
        async function executeStaffLockToggle(isUnlock, lockData = {}) {
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ trong giây lát...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const bodyPayload = isUnlock
                    ? { unlock: true, action: 'unlock' }
                    : {
                        lock_reason: lockData.lock_reason || '',
                        reason: lockData.lock_reason || '',
                        lock_duration: lockData.lock_duration || '7'
                    };

                const res = await fetch(`/admin/tickets/${currentTicketId}/lock-staff`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(bodyPayload)
                });

                const data = await res.json();
                if (data.success) {
                    const isNowLocked = !isUnlock;

                    // Cập nhật state trong currentTicketData
                    if (currentTicketData) {
                        currentTicketData.is_staff_locked = isNowLocked;
                        if (currentTicketData.staff) {
                            currentTicketData.staff.is_locked = isNowLocked;
                        }
                        const dataScript = document.getElementById('ticket-data-' + currentTicketId);
                        if (dataScript) {
                            dataScript.textContent = JSON.stringify(currentTicketData);
                        }
                    }

                    // Cập nhật giao diện nút
                    updateStaffLockButton(isNowLocked);

                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || (isNowLocked ? 'Đã khóa tài khoản thành công!' : 'Đã mở khóa tài khoản thành công!'),
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Thất bại', data.message || 'Không thể cập nhật trạng thái tài khoản.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
            }
        }

        /**
         * Cập nhật màu sắc, icon và text cho nút Khóa/Mở khóa nhân viên
         */
        function updateStaffLockButton(isLocked) {
            const btnLock = document.getElementById('btn-lock-staff');
            const btnLockText = document.getElementById('btn-lock-staff-text');
            const staffDesc = document.getElementById('staff-name-target-desc');
            const staff = currentTicketData?.staff;

            if (!btnLock || !btnLockText) return;

            if (!staff) {
                btnLock.disabled = true;
                btnLock.className = 'w-full py-3 px-4 rounded-xl bg-slate-200 text-slate-500 font-bold text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2';
                btnLockText.innerHTML = '<i class="fa-solid fa-user-slash text-base"></i> <span>Chưa gán tài khoản nhân viên</span>';
                if (staffDesc) staffDesc.textContent = 'Không tìm thấy tài khoản nhân viên liên quan để khóa.';
                return;
            }

            btnLock.disabled = false;
            if (isLocked) {
                btnLock.className = 'w-full py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-amber-600/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer';
                btnLockText.innerHTML = '<i class="fa-solid fa-lock-open text-base"></i> <span>Mở Khóa Tài Khoản Nhân Viên</span>';
                if (staffDesc) staffDesc.innerHTML = `<span class="text-rose-600 font-bold">● Đang bị khóa:</span> [${staff.name}] (${staff.phone}). Bấm nút để mở khóa lại.`;
            } else {
                btnLock.className = 'w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-extrabold text-xs sm:text-sm shadow-md shadow-slate-900/25 hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer';
                btnLockText.innerHTML = '<i class="fa-solid fa-ban text-base text-rose-400"></i> <span>Khóa Tài Khoản Nhân Viên</span>';
                if (staffDesc) staffDesc.textContent = `Nhân viên phụ trách: [${staff.name}] (${staff.phone}).`;
            }
        }

        /**
         * HÀNH ĐỘNG 3: HOÀN TẤT & ĐÓNG HỒ SƠ (AJAX POST /admin/tickets/{id}/resolve)
         */
        function triggerResolveTicket() {
            if (!currentTicketId) return;

            const verdictInput = document.getElementById('modal-verdict-note');
            const verdict = verdictInput ? verdictInput.value.trim() : '';
            if (!verdict) {
                Swal.fire({
                    title: 'Chưa có nội dung chỉ thị!',
                    text: 'Admin vui lòng nhập chỉ thị / yêu cầu xử lý gửi đến Người đăng bài trước khi đóng hồ sơ.',
                    icon: 'warning',
                    confirmButtonText: 'Đã hiểu'
                }).then(() => {
                    if (verdictInput) verdictInput.focus();
                });
                return;
            }

            Swal.fire({
                title: 'Hoàn tất & Đóng hồ sơ?',
                text: 'Hồ sơ khiếu nại cấp 2 sẽ chuyển sang trạng thái "Đã giải quyết" (Resolved). Chỉ thị xử lý sẽ được gửi tới Người đăng bài và khách hàng chỉ nhận thông báo trạng thái hoàn tất.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xác nhận Đóng hồ sơ',
                cancelButtonText: 'Quay lại xem xét'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();

                    fetch(`/admin/tickets/${currentTicketId}/resolve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            resolution_note: verdict
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Xóa hoặc ẩn dòng đó trên bảng danh sách
                                const row = document.getElementById('row-ticket-' + currentTicketId);
                                if (row) {
                                    row.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                                    setTimeout(() => row.remove(), 350);
                                }
                                closeCourtModal();
                                // Tải lại sau 1s nếu danh sách hết dòng
                                setTimeout(() => window.location.reload(), 1000);
                            });
                        } else {
                            Swal.fire('Thất bại', data.message || 'Không thể đóng hồ sơ.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                    });
                }
            });
        }

        /**
         * Lightbox xem ảnh lớn
         */
        function openImageLightbox(src) {
            const modal = document.getElementById('image-lightbox-modal');
            const img = document.getElementById('image-lightbox-src');
            img.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeImageLightbox() {
            const modal = document.getElementById('image-lightbox-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

</body>
</html>
