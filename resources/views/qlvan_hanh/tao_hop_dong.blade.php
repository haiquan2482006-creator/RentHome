@php
    // Trạng thái được coi là "Đang hiển thị" (hợp lệ cho thuê)
    $activeStatuses = ['active', 'approved'];

    if (empty($availableRooms) && empty($posts)) {
        $postsQuery = \App\Models\Post::whereIn('status', $activeStatuses)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', false);
            })
            ->whereNull('deleted_at');

        if (\Illuminate\Support\Facades\Auth::check()) {
            $currentUserId = \Illuminate\Support\Facades\Auth::id();
            $userPosts = (clone $postsQuery)->where('user_id', $currentUserId)->get();
            $availableRooms = $userPosts->isNotEmpty() ? $userPosts : $postsQuery->get();
        } else {
            $availableRooms = $postsQuery->get();
        }
    } else {
        // Nếu controller đã truyền sang biến, tiếp tục lọc loại bỏ bài đã xóa hoặc đã gỡ (hidden, deleted, pending, rejected)
        $rawRooms = collect($availableRooms ?? $posts ?? []);
        $availableRooms = $rawRooms->filter(function($post) use ($activeStatuses) {
            $status = $post->status ?? '';
            $isDeleted = $post->is_deleted ?? false;
            $deletedAt = $post->deleted_at ?? null;
            return in_array($status, $activeStatuses) && !$isDeleted && empty($deletedAt) && $status !== 'hidden' && $status !== 'deleted' && $status !== 'pending' && $status !== 'rejected';
        })->values();
    }
    $posts = $availableRooms;

    if (empty($contacts)) {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $currentUserId = \Illuminate\Support\Facades\Auth::id();
            $userContacts = \App\Models\Contact::where('owner_id', $currentUserId)->get();
            $contacts = $userContacts->isNotEmpty() ? $userContacts : \App\Models\Contact::all();
        } else {
            $contacts = \App\Models\Contact::all();
        }
    }

    $selectedContactId = $selectedContactId ?? request('contact_id');
    $selectedRoomId = $selectedRoomId ?? request('room_id') ?? request('post_id') ?? request('room');

    $selectedContact = $selectedContact ?? ($selectedContactId ? \App\Models\Contact::find($selectedContactId) : null);
    if ($selectedContact && empty($selectedRoomId) && !empty($selectedContact->post_id)) {
        $selectedRoomId = (string)$selectedContact->post_id;
    }

    $selectedRoom = $selectedRoom ?? ($selectedRoomId ? \App\Models\Post::find($selectedRoomId) : null);
    $room = $room ?? $selectedRoom;

    // Lấy thông tin tài khoản user đã gửi yêu cầu liên hệ (nếu có sender_id)
    $senderUser = null;
    if ($selectedContact && !empty($selectedContact->sender_id)) {
        $senderUser = \App\Models\User::find($selectedContact->sender_id);
    }

    $defaultTenantName = old('tenant_name', $selectedContact->sender_name ?? ($senderUser->name ?? ($senderUser->account_name ?? ($senderUser->username ?? ''))));
    $defaultTenantPhone = old('tenant_phone', $selectedContact->phone ?? ($senderUser->phone ?? ''));
    $defaultTenantEmail = old('tenant_email', $selectedContact->email ?? ($senderUser->email ?? ''));
    $defaultTenantIdCard = old('tenant_id_card', $senderUser->id_number ?? ($senderUser->cccd ?? ($senderUser->cmnd ?? '')));
    $defaultTenantAccount = old('tenant_account', $senderUser->username ?? ($senderUser->name ?? ($selectedContact->sender_name ?? '')));
    $defaultTenantMessage = old('tenant_message', $selectedContact->message ?? '');
    $defaultTenantAppointment = $selectedContact->appointment_date ?? '';

    // Tiền phòng và tiền cọc từ bài đăng đã chọn
    $roomPriceRaw = $room->price ?? null;
    $defaultRentPrice = old('rent_price', ($roomPriceRaw && is_numeric($roomPriceRaw)) ? number_format($roomPriceRaw) . ' VNĐ/tháng' : ($roomPriceRaw ?? ''));
    $roomDepositRaw = $room->deposit ?? $roomPriceRaw;
    $defaultDeposit = old('deposit', ($roomDepositRaw && is_numeric($roomDepositRaw)) ? number_format($roomDepositRaw) . ' VNĐ' : ($roomDepositRaw ?? ''));
@endphp
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tạo Hợp Đồng Cho Thuê Mới - RentHome</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
                            900: '#0b132a',
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
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-10 h-10 object-contain rounded-xl group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">Rent<span class="text-brand-600">Home</span></span>
                    <span class="text-[10px] font-bold text-skybrand-600 uppercase tracking-widest mt-0.5">Quản Lý Vận Hành Chủ Nhà</span>
                </div>
            </a>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ url('qlvan_hanh/Room_list') }}" 
                   class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-2 border border-slate-200 transition-all">
                    <i class="fa-solid fa-arrow-left text-slate-500"></i>
                    <span>Danh Sách Phòng</span>
                </a>

                <a href="{{ url('/') }}" 
                   class="py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md transition-all">
                    <i class="fa-solid fa-house"></i>
                    <span>Trở Về Trang Chủ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- BANNER HERO BANNER (ĐỒNG MÀU DARK NAVY NÉT NHƯ CÁC TRANG QUẢN LÝ VẬN HÀNH) -->
        <div class="bg-[#0b132a] rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-file-signature"></i> Tạo Hợp Đồng Cho Thuê Mới
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Tạo Hợp Đồng & Cấu Hình Phòng Cho Thuê</h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Điền đầy đủ thông tin bên thuê, giá cọc, cấu hình đơn giá điện nước và thiết lập chu kỳ chốt hóa đơn định kỳ cho hệ thống.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ url('qlvan_hanh/Room_list') }}" 
                       class="py-3 px-5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm flex items-center gap-2 backdrop-blur-md transition-all border border-white/20">
                        <i class="fa-solid fa-xmark"></i> Hủy Bỏ
                    </a>
                </div>
            </div>
        </div>

        <!-- FORM TRANG TẠO HỢP ĐỒNG ĐẦY ĐỦ 15 CẤU HÌNH -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-slate-200 space-y-6">
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form id="fullCreateContractForm" method="POST" action="{{ route('contracts.store') }}" class="space-y-6 text-xs sm:text-sm">
                @csrf
                
                <!-- TRƯỜNG ẨN ĐÓNG VAI TRÒ FLAG ĐÁNH DẤU NGUỒN FORM (KHÁCH LIÊN HỆ HAY KHÁCH VÃNG LAI) -->
                <input type="hidden" name="contact_id" id="fContactIdFlag" value="{{ $contact_id ?? ($selectedContactId ?? '') }}">
                <input type="hidden" name="water_calculation_type" id="fWaterMode" value="khoi">
                
                <!-- PHẦN 1: THÔNG TIN BÊN THUÊ & PHÒNG THUÊ -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <div class="flex flex-wrap items-center justify-between border-b border-slate-200 pb-3 gap-2">
                        <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-user-tag text-emerald-600 text-base"></i> 1. Thông Tin Khách Thuê & Phòng Cho Thuê
                        </h3>
                        @if($selectedContact)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 shadow-xs">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Tự động đồng bộ từ yêu cầu liên hệ
                            </span>
                        @endif
                    </div>

                    @if($selectedContact)
                    <!-- BANNER THÔNG TIN LIÊN KẾT TỪ THÔNG BÁO LIÊN HỆ -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-green-50 border border-emerald-200 text-emerald-950 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-xl shadow-md shrink-0">
                                {{ strtoupper(substr($defaultTenantName ?: 'K', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-extrabold text-sm sm:text-base text-emerald-950">{{ $defaultTenantName }}</h4>
                                    @if($defaultTenantAccount)
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-200/80 text-emerald-800">
                                            <i class="fa-solid fa-user-tag"></i> {{ $defaultTenantAccount }}
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 text-blue-700">
                                        ID Liên hệ: #{{ $selectedContactId }}
                                    </span>
                                </div>
                                <div class="text-xs text-emerald-800 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                                    <span><i class="fa-solid fa-phone text-emerald-600 mr-1"></i><strong>{{ $defaultTenantPhone ?: 'Chưa cập nhật' }}</strong></span>
                                    <span><i class="fa-solid fa-envelope text-emerald-600 mr-1"></i>{{ $defaultTenantEmail ?: 'Chưa cập nhật' }}</span>
                                    @if($defaultTenantAppointment)
                                        <span class="text-rose-700 font-semibold"><i class="fa-solid fa-calendar-check mr-1"></i>Hẹn: {{ $defaultTenantAppointment }}</span>
                                    @endif
                                </div>
                                @if($defaultTenantMessage)
                                    <div class="text-xs text-slate-700 italic mt-1.5 bg-white/90 px-3 py-1.5 rounded-xl border border-emerald-100 inline-block shadow-2xs">
                                        <i class="fa-solid fa-quote-left text-emerald-400 mr-1 text-[10px]"></i>{{ $defaultTenantMessage }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-left md:text-right shrink-0 bg-white/90 p-3 rounded-xl border border-emerald-200 shadow-2xs w-full md:w-auto">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">Tòa nhà / Bài đăng gửi đến:</span>
                            <span class="text-xs sm:text-sm font-extrabold text-emerald-800 block">
                                {{ $room->title ?? 'Bài đăng liên quan' }}
                            </span>
                            @if(isset($room->price))
                                <span class="text-xs font-black text-rose-600 mt-0.5 block">
                                    {{ is_numeric($room->price) ? number_format($room->price) . ' VNĐ/tháng' : $room->price }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- LỰA CHỌN KHÁCH & BÀI ĐĂNG LIÊN QUAN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Dropdown chọn người liên hệ -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Bên thuê (Người gửi yêu cầu liên hệ nếu có)</label>
                            <select id="fTenant" name="contact_select" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="">-- Khách vãng lai / Trực tiếp (hoặc chọn từ danh sách liên hệ) --</option>
                                @foreach($contacts as $c)
                                    @php
                                        $cId = (string)($c->id ?? $c->_id);
                                        $cUser = !empty($c->sender_id) ? \App\Models\User::find($c->sender_id) : null;
                                        $cName = $c->sender_name ?? ($cUser->name ?? ($cUser->account_name ?? ($cUser->username ?? 'Khách thuê')));
                                        $cPhone = $c->phone ?? ($cUser->phone ?? '');
                                        $cEmail = $c->email ?? ($cUser->email ?? '');
                                        $cUsername = $cUser->username ?? ($cUser->name ?? $cName);
                                        $cIdCard = $cUser->id_number ?? ($cUser->cccd ?? ($cUser->cmnd ?? ''));
                                        $cMessage = $c->message ?? '';
                                        $cPostId = (string)($c->post_id ?? '');
                                        $isCurSelected = ($selectedContactId && (string)$selectedContactId === $cId) || old('contact_id') == $cId;
                                    @endphp
                                    <option value="{{ $cId }}" 
                                            data-name="{{ $cName }}"
                                            data-phone="{{ $cPhone }}"
                                            data-email="{{ $cEmail }}"
                                            data-username="{{ $cUsername }}"
                                            data-id-card="{{ $cIdCard }}"
                                            data-message="{{ $cMessage }}"
                                            data-post-id="{{ $cPostId }}"
                                            @if($isCurSelected) selected @endif>
                                        {{ $cName }} (SĐT: {{ $cPhone ?: 'Chưa cập nhật' }}) @if($cUsername && $cUsername !== $cName) - [{{ $cUsername }}] @endif
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">* Đổi khách sẽ tự động cập nhật thông tin & chọn bài đăng tương ứng</span>
                        </div>

                        <!-- 2. Phòng / Bài đăng cho thuê -->
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Tòa nhà / Bài đăng cho thuê <span class="text-rose-500">*</span></label>
                            <select id="fRoom" name="room_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="">-- Chọn bài đăng nhà, phòng cho thuê --</option>
                                @foreach($availableRooms as $post)
                                    @php
                                        $postStatus = $post->status ?? '';
                                        $isDeleted = $post->is_deleted ?? false;
                                        $deletedAt = $post->deleted_at ?? null;

                                        $postId = (string)($post->id ?? $post->_id);
                                        $isRoomMatch = (isset($selectedRoomId) && (string)$selectedRoomId === $postId) || old('room_id') == $postId;

                                        // Bỏ qua bài đăng đã xóa, đã gỡ, trừ khi nó là bài đăng khớp với khách liên hệ
                                        if (!$isRoomMatch && (!in_array($postStatus, ['active', 'approved']) || $isDeleted || !empty($deletedAt) || in_array($postStatus, ['hidden', 'deleted', 'pending', 'rejected']))) {
                                            continue;
                                        }

                                        $postTitle = $post->title ?? $post->name ?? ('Bài đăng #' . $postId);
                                        $priceVal = $post->price ?? null;
                                        $priceFormatted = (isset($priceVal) && is_numeric($priceVal)) ? number_format($priceVal) . 'đ/tháng' : ($priceVal ?? '');
                                        $location = $post->district ?? $post->province ?? '';
                                    @endphp
                                    <option value="{{ $postId }}" 
                                            data-price="{{ $priceVal ?? '' }}"
                                            data-title="{{ $postTitle }}"
                                            @if($isRoomMatch) selected @endif>
                                        {{ $postTitle }} @if($priceFormatted) - {{ $priceFormatted }} @endif @if($location) ({{ $location }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-emerald-700 font-medium mt-1 block">* Tự động chọn bài đăng khách gửi đến & chuyển sang "Đã cho thuê"</span>
                        </div>
                    </div>

                    <!-- THÔNG TIN CHI TIẾT BÊN THUÊ (TỰ ĐỘNG ĐIỀN TỪ TÀI KHOẢN YÊU CẦU LIÊN HỆ) -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-id-card-clip text-emerald-600"></i> Chi Tiết Khách Thuê (Tự động nạp từ tài khoản liên hệ)
                            </h4>
                            <span class="text-[11px] text-emerald-700 font-medium italic"><i class="fa-solid fa-lock-open text-[10px]"></i> Có thể chỉnh sửa trực tiếp</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Họ và tên người thuê <span class="text-rose-500">*</span></label>
                                <input type="text" id="fTenantName" name="tenant_name" value="{{ $defaultTenantName }}" placeholder="Họ và tên người thuê" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                                <input type="tel" id="fTenantPhone" name="tenant_phone" value="{{ $defaultTenantPhone }}" placeholder="Số điện thoại" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Email liên hệ</label>
                                <input type="email" id="fTenantEmail" name="tenant_email" value="{{ $defaultTenantEmail }}" placeholder="Email khách thuê" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-medium text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số CCCD / CMND</label>
                                <input type="text" id="fTenantIdCard" name="tenant_id_card" value="{{ $defaultTenantIdCard }}" placeholder="Nhập số CCCD/CMND người thuê" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Tài khoản liên kết RentHome</label>
                                <input type="text" id="fTenantAccount" name="tenant_account" value="{{ $defaultTenantAccount }}" readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-600 bg-slate-100 shadow-xs cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Ghi chú / Nhu cầu khách</label>
                                <input type="text" id="fTenantMessage" name="tenant_message" value="{{ $defaultTenantMessage }}" placeholder="Ghi chú từ yêu cầu liên hệ" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            </div>
                        </div>
                    </div>

                    <!-- GIÁ THUÊ & TIỀN CỌC -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">
                                Tiền phòng hàng tháng (VNĐ/tháng) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="fRentPrice" name="rent_price" value="{{ $defaultRentPrice }}" placeholder="Nhập giá thuê (VD: 3000000 hoặc 3.000.000)" required class="w-full pl-3.5 pr-12 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/tháng</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="fRentPriceDisplay" class="font-bold text-slate-800">0</span> VNĐ / tháng
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">
                                Tiền cọc giữ phòng (VNĐ) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="fDeposit" name="deposit" value="{{ $defaultDeposit }}" placeholder="Nhập số tiền cọc (VD: 3000000 hoặc 3.000.000)" required class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">VNĐ</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="fDepositDisplay" class="font-bold text-slate-800">0</span> VNĐ
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 2: CẤU HÌNH ĐIỆN & NƯỚC (NƠI LƯU CẤU HÌNH ĐỂ THÁNG SAU TỰ TÍNH TIỀN) -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-bolt-lightning text-amber-500 text-base"></i> 2. Cấu Hình Đơn Giá Điện & Nước Thỏa Thuận
                    </h3>

                    <!-- CẤU HÌNH ĐIỆN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">
                                Giá Điện (VNĐ/số) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="fElecPrice" name="electricity_price" value="{{ old('electricity_price', $room->electricity_price ?? '') }}" placeholder="Nhập giá điện (VD: 3000 hoặc 3.500)" required class="w-full pl-3.5 pr-12 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none shadow-xs transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/số</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="fElecPriceDisplay" class="font-bold text-slate-800">0</span> VNĐ / số
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">* Cấu hình sẽ được lưu vào hợp đồng để hệ thống tự động tính tiền điện</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Số điện ban đầu (Chỉ số bàn giao) <span class="text-rose-500">*</span></label>
                            <input type="number" id="fElecStart" name="electricity_start" value="{{ old('electricity_start', $room->electricity_start ?? '') }}" placeholder="Nhập chỉ số điện bàn giao ban đầu" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Chỉ số trên đồng hồ công tơ tại thời điểm ký kết</span>
                        </div>
                    </div>

                    <!-- CẤU HÌNH NƯỚC (NÚT CHỌN TOGGLE: TÍNH KHỐI / TÍNH NGƯỜI) -->
                    <div class="pt-3 space-y-3 border-t border-slate-200">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="font-extrabold text-slate-900 text-xs sm:text-sm flex items-center gap-2">
                                <i class="fa-solid fa-droplet text-skybrand-600"></i> Giá Nước Thỏa Thuận (Chọn hình thức tính):
                            </label>
                            <div class="inline-flex rounded-xl bg-slate-200 p-1 font-bold text-xs shadow-inner">
                                <button type="button" id="btnModeKhoi" onclick="switchWaterForm('khoi')" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all">
                                    <i class="fa-solid fa-cube"></i> Tính Theo Khối
                                </button>
                                <button type="button" id="btnModeNguoi" onclick="switchWaterForm('nguoi')" class="px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all">
                                    <i class="fa-solid fa-users"></i> Tính Theo Người
                                </button>
                            </div>
                        </div>

                        <!-- SUB-FORM 1: TÍNH THEO KHỐI -->
                        <div id="subWaterKhoi" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    Số tiền (VNĐ/khối) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="fWaterPriceKhoi" name="water_price_block" value="{{ old('water_price_block', $room->water_price_block ?? '') }}" placeholder="Nhập giá nước (VD: 25000 hoặc 25.000)" class="w-full pl-3.5 pr-14 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/khối</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 font-medium">
                                    Hiển thị: <span id="fWaterKhoiDisplay" class="font-bold text-slate-800">0</span> VNĐ / khối
                                </div>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số nước ban đầu (Chỉ số bàn giao)</label>
                                <input type="number" id="fWaterStart" name="water_start" value="{{ old('water_start', $room->water_start ?? '') }}" placeholder="Nhập chỉ số nước ban đầu" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white">
                                <span class="text-[11px] text-slate-500 mt-1 block">* Chỉ số trên đồng hồ nước tại thời điểm ký kết</span>
                            </div>
                        </div>

                        <!-- SUB-FORM 2: TÍNH THEO NGƯỜI -->
                        <div id="subWaterNguoi" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">
                                    Số tiền (VNĐ/người) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="fWaterPriceNguoi" name="water_price_person" value="{{ old('water_price_person', $room->water_price_person ?? '') }}" placeholder="Nhập giá nước (VD: 100000 hoặc 100.000)" class="w-full pl-3.5 pr-16 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/người</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 font-medium">
                                    Hiển thị: <span id="fWaterNguoiDisplay" class="font-bold text-slate-800">0</span> VNĐ / người
                                </div>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-800 mb-1">Số người ở đăng ký</label>
                                <input type="number" id="fWaterPeopleCount" name="water_people_count" value="{{ old('water_people_count', '1') }}" placeholder="Số người ở" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white">
                                <span class="text-[11px] text-slate-500 mt-1 block">* Dùng để nhân với đơn giá nước theo người mỗi tháng</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 3: PHÍ DỊCH VỤ CỐ ĐỊNH -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-wifi text-purple-600 text-base"></i> 3. Phí Dịch Vụ Cố Định Hàng Tháng
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">
                                Internet / Wifi (VNĐ/tháng)
                            </label>
                            <div class="relative">
                                <input type="text" id="fInternetPrice" name="internet_price" value="{{ old('internet_price', $room->internet_price ?? '') }}" placeholder="VD: 100000 hoặc 100.000" class="w-full pl-3.5 pr-16 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-xs transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/tháng</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="fInternetPriceDisplay" class="font-bold text-slate-800">0</span> VNĐ / tháng
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">
                                Rác sinh hoạt (VNĐ/phòng)
                            </label>
                            <div class="relative">
                                <input type="text" id="fGarbagePrice" name="garbage_price" value="{{ old('garbage_price', $room->garbage_price ?? '') }}" placeholder="VD: 30000 hoặc 30.000" class="w-full pl-3.5 pr-16 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-xs transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">đ/phòng</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 font-medium">
                                Hiển thị: <span id="fGarbagePriceDisplay" class="font-bold text-slate-800">0</span> VNĐ / phòng
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 4: CHU KỲ, HẠN CHỐT & PHÍ PHẠT QUÁ HẠN -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-calendar-check text-skybrand-600 text-base"></i> 4. Chu Kỳ Thanh Toán, Ngày Chốt Hóa Đơn & Phí Phạt
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Chu kỳ thanh toán</label>
                            <select id="fCycle" name="payment_cycle" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                                <option value="1 tháng" {{ old('payment_cycle', '1 tháng') == '1 tháng' ? 'selected' : '' }}>1 tháng (Hàng tháng)</option>
                                <option value="3 tháng" {{ old('payment_cycle') == '3 tháng' ? 'selected' : '' }}>3 tháng (Theo quý)</option>
                                <option value="6 tháng" {{ old('payment_cycle') == '6 tháng' ? 'selected' : '' }}>6 tháng (Nửa năm)</option>
                                <option value="1 năm" {{ old('payment_cycle') == '1 năm' ? 'selected' : '' }}>1 năm (Theo năm)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày chốt Hóa đơn</label>
                            <input type="text" id="fBillDate" name="billing_date" value="{{ old('billing_date', 'Mùng 5') }}" placeholder="VD: Mùng 5" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Đến ngày này hệ thống nhắc Chủ nhà ghi điện nước</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Thời hạn thanh toán</label>
                            <input type="text" id="fPayDays" name="payment_limit_days" value="{{ old('payment_limit_days', '5 ngày') }}" placeholder="VD: 5 ngày" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Ngày chốt + 5 ngày = Hạn chót đóng tiền</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Phí phạt quá hạn</label>
                            <input type="text" id="fPenalty" name="late_penalty_fee" value="{{ old('late_penalty_fee', '20.000 VNĐ / ngày') }}" placeholder="VD: 20.000 VNĐ / ngày" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-rose-600 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Tự lấy số ngày trễ × con số này để cộng vào hóa đơn</span>
                        </div>
                    </div>
                </div>

                <!-- PHẦN 5: THỜI HẠN HỢP ĐỒNG & TỰ ĐỘNG TÍNH NGÀY KẾT THÚC -->
                <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 pb-3">
                        <i class="fa-solid fa-clock text-rose-500 text-base"></i> 5. Thời Hạn Hợp Đồng & Tự Động Tính Ngày Kết Thúc
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày bắt đầu tính tiền <span class="text-rose-500">*</span></label>
                            <input type="date" id="fStartDate" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" onchange="calcEndDate()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Ngày khách chính thức dọn vào ở</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Thời hạn hợp đồng (Số tháng) <span class="text-rose-500">*</span></label>
                            <input type="number" id="fDurationMonths" name="duration_months" value="{{ old('duration_months', '12') }}" min="1" max="60" oninput="calcEndDate()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-bold text-slate-900 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">* Nhập số tháng hợp đồng (VD: 8)</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-800 mb-1.5">Ngày kết thúc (Tự động tính)</label>
                            <input type="text" id="fEndDateDisplay" name="end_date_display" value="{{ old('end_date_display', '') }}" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 text-xs sm:text-sm font-extrabold text-emerald-700 bg-emerald-50/90 shadow-xs">
                            <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">* Kết quả tự động hiển thị ngày kết thúc</span>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT ACTIONS -->
                <div class="pt-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200">
                    <a href="{{ url('qlvan_hanh/Room_list') }}" class="py-3 px-6 rounded-2xl border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i> Hủy Bỏ Quay Lại
                    </a>

                    <button type="submit" class="py-3.5 px-8 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center gap-2 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-floppy-disk text-base"></i>
                        <span>LƯU HỢP ĐỒNG (Đẩy vào CSDL tbl_hop_dong)</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Toast Notification Container -->
    <div id="toast" class="fixed bottom-5 right-5 z-50 hidden bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-700 text-xs font-semibold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
        <span id="toastMessage">Thao tác thành công!</span>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Trích xuất số nguyên từ chuỗi (bỏ dấu chấm, phẩy, chữ)
        function parseNumberOnly(val) {
            if (typeof val === 'number') return val;
            if (!val) return 0;
            const cleanStr = String(val).replace(/[^\d]/g, '');
            return cleanStr ? parseInt(cleanStr, 10) : 0;
        }

        // Định dạng hàng nghìn với dấu chấm (VD: 3000 -> 3.000)
        function formatThousands(num) {
            if (!num || isNaN(num) || num === 0) return '0';
            return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Đọc số tiền thành chữ tiếng Việt
        function numberToVietnameseWords(num) {
            if (!num || isNaN(num) || num <= 0) return '';
            const units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];
            const digits = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
            
            function readThreeDigits(n, showZeroHundred) {
                let h = Math.floor(n / 100);
                let t = Math.floor((n % 100) / 10);
                let u = n % 10;
                let res = '';
                if (h > 0 || showZeroHundred) {
                    res += digits[h] + ' trăm ';
                }
                if (t > 1) {
                    res += digits[t] + ' mươi ';
                    if (u === 1) res += 'mốt ';
                    else if (u === 5) res += 'lăm ';
                    else if (u > 0) res += digits[u] + ' ';
                } else if (t === 1) {
                    res += 'mười ';
                    if (u === 5) res += 'lăm ';
                    else if (u > 0) res += digits[u] + ' ';
                } else if (t === 0 && (h > 0 || showZeroHundred) && u > 0) {
                    res += 'lẻ ' + digits[u] + ' ';
                } else if (u > 0) {
                    res += digits[u] + ' ';
                }
                return res.trim();
            }

            let nStr = String(Math.floor(num));
            let blocks = [];
            while (nStr.length > 0) {
                blocks.unshift(nStr.slice(-3));
                nStr = nStr.slice(0, -3);
            }

            let words = [];
            let len = blocks.length;
            for (let i = 0; i < len; i++) {
                let bVal = parseInt(blocks[i], 10);
                if (bVal > 0) {
                    let blockWord = readThreeDigits(bVal, i > 0);
                    let unit = units[len - 1 - i];
                    words.push(blockWord + (unit ? ' ' + unit : ''));
                }
            }
            let result = words.join(' ').replace(/\s+/g, ' ').trim();
            if (!result) return '';
            return result.charAt(0).toUpperCase() + result.slice(1) + ' đồng';
        }

        // Cập nhật hiển thị số bên dưới ô nhập
        function updateFieldDisplay(inputId, displayId) {
            const input = document.getElementById(inputId);
            const display = document.getElementById(displayId);
            if (!input || !display) return;

            const num = parseNumberOnly(input.value);
            display.textContent = num > 0 ? formatThousands(num) : '0';
        }

        // Danh sách các trường tiền tệ cần hỗ trợ hiển thị trực tiếp
        const priceFieldConfigs = [
            { id: 'fElecPrice', display: 'fElecPriceDisplay' },
            { id: 'fWaterPriceKhoi', display: 'fWaterKhoiDisplay' },
            { id: 'fWaterPriceNguoi', display: 'fWaterNguoiDisplay' },
            { id: 'fRentPrice', display: 'fRentPriceDisplay' },
            { id: 'fDeposit', display: 'fDepositDisplay' },
            { id: 'fInternetPrice', display: 'fInternetPriceDisplay' },
            { id: 'fGarbagePrice', display: 'fGarbagePriceDisplay' },
        ];

        function initPriceFormatting() {
            priceFieldConfigs.forEach(item => {
                const el = document.getElementById(item.id);
                if (!el) return;

                // Khi gõ số: cập nhật tức thì số bên dưới (VD: 3000 -> 3.000)
                el.addEventListener('input', () => {
                    updateFieldDisplay(item.id, item.display);
                });

                // Khi rời khỏi ô: chuẩn hóa định dạng dấu chấm trong ô nhập
                el.addEventListener('blur', () => {
                    const num = parseNumberOnly(el.value);
                    if (num > 0) {
                        el.value = formatThousands(num);
                    }
                    updateFieldDisplay(item.id, item.display);
                });

                // Cập nhật giá trị khởi tạo khi vừa load trang
                updateFieldDisplay(item.id, item.display);
            });
        }

        // Switch water calculation mode
        function switchWaterForm(mode) {
            const btnKhoi = document.getElementById('btnModeKhoi');
            const btnNguoi = document.getElementById('btnModeNguoi');
            const subKhoi = document.getElementById('subWaterKhoi');
            const subNguoi = document.getElementById('subWaterNguoi');
            const waterModeInput = document.getElementById('fWaterMode');
            if (waterModeInput) waterModeInput.value = mode;

            if (mode === 'khoi') {
                btnKhoi.className = "px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all";
                btnNguoi.className = "px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all";
                subKhoi.classList.remove('hidden');
                subNguoi.classList.add('hidden');
            } else {
                btnNguoi.className = "px-4 py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition-all";
                btnKhoi.className = "px-4 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 transition-all";
                subNguoi.classList.remove('hidden');
                subKhoi.classList.add('hidden');
            }
        }

        // Calculate end date based on start date + duration months
        function calcEndDate() {
            const startDateVal = document.getElementById('fStartDate').value;
            const monthsVal = parseInt(document.getElementById('fDurationMonths').value) || 0;
            const endDateDisplay = document.getElementById('fEndDateDisplay');

            if (!startDateVal || monthsVal <= 0) {
                endDateDisplay.value = "--/--/----";
                return;
            }

            const startDate = new Date(startDateVal);
            if (isNaN(startDate.getTime())) return;

            startDate.setMonth(startDate.getMonth() + monthsVal);

            const day = String(startDate.getDate()).padStart(2, '0');
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const year = startDate.getFullYear();

            endDateDisplay.value = `${day}/${month}/${year}`;
        }

        // Show Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            if (!toast || !toastMsg) return;
            toastMsg.innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Initial Calculation & Event Listeners
        window.addEventListener('DOMContentLoaded', () => {
            calcEndDate();
            initPriceFormatting();

            const tenantSelect = document.getElementById('fTenant');
            const roomSelect = document.getElementById('fRoom');
            const rentPriceInput = document.getElementById('fRentPrice');
            const depositInput = document.getElementById('fDeposit');

            // Đồng bộ dữ liệu phòng khi chọn bài đăng
            function syncRoomData() {
                if (!roomSelect) return;
                const selectedOpt = roomSelect.options[roomSelect.selectedIndex];
                if (selectedOpt && selectedOpt.dataset.price) {
                    const price = selectedOpt.dataset.price;
                    const numPrice = parseNumberOnly(price);
                    if (rentPriceInput && numPrice > 0) {
                        rentPriceInput.value = formatThousands(numPrice);
                        updateFieldDisplay('fRentPrice', 'fRentPriceDisplay');
                    }
                    if (depositInput && numPrice > 0 && (!depositInput.value || depositInput.value === '')) {
                        depositInput.value = formatThousands(numPrice);
                        updateFieldDisplay('fDeposit', 'fDepositDisplay');
                    }
                }
            }

            // Đồng bộ toàn bộ dữ liệu khách thuê khi đổi khách trong dropdown
            function syncTenantData() {
                if (!tenantSelect) return;
                const opt = tenantSelect.options[tenantSelect.selectedIndex];
                const flagInput = document.getElementById('fContactIdFlag');
                if (flagInput) {
                    flagInput.value = (opt && opt.value) ? opt.value : '';
                }

                if (!opt || !opt.value) return;

                if (opt.dataset.name !== undefined) document.getElementById('fTenantName').value = opt.dataset.name;
                if (opt.dataset.phone !== undefined) document.getElementById('fTenantPhone').value = opt.dataset.phone;
                if (opt.dataset.email !== undefined) document.getElementById('fTenantEmail').value = opt.dataset.email;
                if (opt.dataset.idCard !== undefined) document.getElementById('fTenantIdCard').value = opt.dataset.idCard;
                if (opt.dataset.username !== undefined) document.getElementById('fTenantAccount').value = opt.dataset.username;
                if (opt.dataset.message !== undefined) document.getElementById('fTenantMessage').value = opt.dataset.message;

                // Tự động chọn bài đăng / tòa nhà phòng mà tài khoản yêu cầu liên hệ gửi đến
                if (opt.dataset.postId && roomSelect) {
                    const targetPostId = opt.dataset.postId;
                    let foundOpt = false;
                    for (let i = 0; i < roomSelect.options.length; i++) {
                        if (roomSelect.options[i].value === targetPostId) {
                            roomSelect.selectedIndex = i;
                            foundOpt = true;
                            break;
                        }
                    }
                    if (foundOpt) {
                        syncRoomData();
                    }
                }
            }

            if (tenantSelect) {
                tenantSelect.addEventListener('change', syncTenantData);
            }

            if (roomSelect) {
                roomSelect.addEventListener('change', syncRoomData);
            }

            // Kích hoạt đồng bộ ban đầu nếu đã có lựa chọn từ URL
            if (tenantSelect && tenantSelect.value) {
                const currentOpt = tenantSelect.options[tenantSelect.selectedIndex];
                if (currentOpt && currentOpt.dataset.postId && roomSelect && (!roomSelect.value || roomSelect.value === '')) {
                    roomSelect.value = currentOpt.dataset.postId;
                }
            }
            if (roomSelect && roomSelect.value && (!rentPriceInput.value || rentPriceInput.value === '')) {
                syncRoomData();
            }
        });
    </script>
</body>

</html>
