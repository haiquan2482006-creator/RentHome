<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử yêu cầu liên hệ - RentHome</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">

    @php
        $currentUser = Auth::user();
        $isEnterprise = ($currentUser->account_type ?? 'canhan') === 'doanhnghiep';
        
        $displayName = $currentUser->account_name ?? ($currentUser->username ?? ($currentUser->name ?? 'Người dùng'));
        $subTitle = $isEnterprise ? 'Đối tác Doanh Nghiệp' : 'Tài khoản Cá nhân';
        $badgeText = $isEnterprise ? 'Doanh Nghiệp' : 'Cá Nhân';
    @endphp

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between shadow-sm">
        <a href="/" class="flex items-center gap-2.5 group">
            <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain rounded-xl shadow-xs group-hover:scale-105 transition-transform">
            <div class="flex flex-col">
                <span class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight leading-none">Rent<span class="text-brand-600">Home</span></span>
                <span class="text-[9px] sm:text-[10px] font-medium text-slate-500 uppercase tracking-widest mt-0.5 hidden sm:block">Lịch sử liên hệ</span>
            </div>
        </a>
        
        <div class="flex items-center gap-3 sm:gap-6">
            <a href="/" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">
                <i class="fa-solid fa-house"></i> Trang Chủ
            </a>
            
            <div class="relative group">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 sm:gap-3 p-1.5 sm:p-2 sm:pr-4 rounded-full sm:rounded-2xl {{ $isEnterprise ? 'bg-purple-50 hover:bg-purple-100 border-purple-100' : 'bg-emerald-50 hover:bg-emerald-100 border-emerald-100' }} border transition-all cursor-pointer">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full sm:rounded-xl {{ $isEnterprise ? 'bg-purple-600' : 'bg-brand-600' }} text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-sm">
                        <i class="fa-solid {{ $isEnterprise ? 'fa-briefcase' : 'fa-user' }}"></i>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-slate-900 max-w-[120px] truncate" title="{{ $displayName }}">{{ $displayName }}</p>
                        <p class="text-[10px] font-semibold {{ $isEnterprise ? 'text-purple-700' : 'text-emerald-700' }} mt-0.5">{{ $badgeText }}</p>
                    </div>
                </a>
                
                <!-- Dropdown -->
                <div class="absolute right-0 top-full mt-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                    <div class="bg-white rounded-xl shadow-xl border border-slate-100 p-2 flex flex-col gap-1 relative before:absolute before:right-4 before:-top-2 before:w-0 before:h-0 before:border-x-8 before:border-x-transparent before:border-b-8 before:border-b-white before:drop-shadow-sm">
                        <a href="/" class="sm:hidden flex items-center gap-3 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 rounded-lg transition-colors">
                            <i class="fa-solid fa-house w-4 text-center"></i> Trang Chủ
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-600 rounded-lg transition-colors">
                            <i class="fa-solid fa-user-pen w-4 text-center"></i> Tài khoản
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        @auth
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-left">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Đăng xuất
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 min-h-[calc(100vh-72px)]">

        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Lịch sử yêu cầu liên hệ</h2>
            <p class="text-sm text-slate-500 mt-1">Theo dõi trạng thái các yêu cầu xem nhà bạn đã gửi.</p>
        </div>

        <!-- List View -->
        <div class="space-y-4">
                @forelse($myContacts as $contact)
                    @php
                        // Resolve property type and status
                        $typeMap = [
                            'NHA_O' => 'Nhà ở', 'PHONG_TRO' => 'Phòng trọ', 'MAT_BANG' => 'Mặt bằng',
                            'CAN_HO' => 'Căn hộ', 'VAN_PHONG' => 'Văn phòng',
                        ];
                        $propertyType = $contact->post ? ($typeMap[$contact->post->property_type] ?? $contact->post->property_type) : 'BĐS';
                    @endphp

                    <div x-data="{ isRevoked: false, isRevoking: false }" x-show="!isRevoked" x-transition.opacity.duration.400ms class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col lg:flex-row gap-5 hover:border-brand-300 transition-colors">
                        
                        @if($contact->post)
                        <!-- Cột trái: Thumbnail -->
                        <div class="shrink-0 w-full lg:w-48 h-32 lg:h-full relative rounded-xl overflow-hidden bg-slate-100 border border-slate-200/60">
                            <img src="{{ $contact->post->display_image }}" alt="Thumbnail" class="w-full h-full object-cover">
                            <div class="absolute top-2 left-2 bg-brand-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                                {{ $propertyType }}
                            </div>
                        </div>

                        <!-- Cột giữa: Thông tin bài đăng & Lời nhắn -->
                        <div class="flex-1 flex flex-col justify-center min-w-0">
                            <a href="{{ route('post.detail', $contact->post->_id) }}" class="text-lg font-extrabold text-slate-900 leading-tight hover:text-brand-600 transition-colors truncate block mb-1" title="{{ $contact->post->title }}">
                                {{ $contact->post->title }}
                            </a>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-brand-600 font-black text-sm">{{ number_format($contact->post->price, 0, ',', '.') }} {{ $contact->post->price_unit }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-xs text-slate-500 truncate"><i class="fa-solid fa-location-dot mr-1"></i>{{ $contact->post->address ?? ($contact->post->ward . ', ' . $contact->post->district . ', ' . $contact->post->province) }}</span>
                            </div>
                            
                            <!-- Thông tin liên hệ đã gửi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                                <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg">
                                    <i class="fa-solid fa-phone text-slate-400 w-3 text-center"></i>
                                    <span class="font-semibold">{{ $contact->phone }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg">
                                    <i class="fa-solid fa-envelope text-slate-400 w-3 text-center"></i>
                                    <span class="font-medium truncate">{{ $contact->email }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-brand-700 bg-brand-50 border border-brand-100 px-3 py-2 rounded-lg sm:col-span-2">
                                    <i class="fa-solid fa-calendar-check w-3 text-brand-500 text-center"></i>
                                    <span class="font-bold">{{ $contact->note ?? 'Lịch hẹn: Chưa xác định' }}</span>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl mt-auto">
                                <p class="text-xs font-semibold text-slate-500 mb-1">Bạn đã gửi lời nhắn:</p>
                                <p class="text-[13px] text-slate-700 italic line-clamp-2 leading-relaxed">"{{ $contact->message }}"</p>
                            </div>
                        </div>
                        @else
                        <!-- Nếu bài đăng đã bị xóa -->
                        <div class="shrink-0 w-full lg:w-48 h-32 lg:h-full relative rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center border border-slate-200/60 text-slate-400">
                            <i class="fa-regular fa-image text-3xl"></i>
                        </div>
                        <div class="flex-1 flex flex-col justify-center min-w-0">
                            <h3 class="text-lg font-extrabold text-slate-400 line-through mb-3">Bài đăng đã bị xóa hoặc ẩn</h3>
                            
                            <!-- Thông tin liên hệ đã gửi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                                <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg">
                                    <i class="fa-solid fa-phone text-slate-400 w-3 text-center"></i>
                                    <span class="font-semibold">{{ $contact->phone }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 border border-slate-100 px-3 py-2 rounded-lg">
                                    <i class="fa-solid fa-envelope text-slate-400 w-3 text-center"></i>
                                    <span class="font-medium truncate">{{ $contact->email }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-brand-700 bg-brand-50 border border-brand-100 px-3 py-2 rounded-lg sm:col-span-2">
                                    <i class="fa-solid fa-calendar-check w-3 text-brand-500 text-center"></i>
                                    <span class="font-bold">{{ $contact->note ?? 'Lịch hẹn: Chưa xác định' }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl mt-auto">
                                <p class="text-xs font-semibold text-slate-500 mb-1">Bạn đã gửi lời nhắn:</p>
                                <p class="text-[13px] text-slate-700 italic line-clamp-2">"{{ $contact->message }}"</p>
                            </div>
                        </div>
                        @endif

                        <!-- Cột phải: Status Badge & Ngày gửi -->
                        <div class="shrink-0 lg:w-48 flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center border-t lg:border-t-0 lg:border-l border-slate-100 pt-4 lg:pt-0 lg:pl-5 mt-2 lg:mt-0">
                            <div class="text-left lg:text-right w-full">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 hidden lg:block">Trạng thái</p>
                                
                                @switch($contact->status)
                                    @case('pending')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 border border-amber-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-hourglass-half"></i> Đang chờ phản hồi
                                        </div>
                                        @break
                                    @case('communicating')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-comments"></i> Đang trao đổi
                                        </div>
                                        @break
                                    @case('approved')
                                    @case('đã ký hợp đồng')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-circle-check"></i> Đã chốt / Duyệt
                                        </div>
                                        @break
                                    @case('rejected')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-circle-xmark"></i> Đã từ chối
                                        </div>
                                        @break
                                    @case('closed')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 border border-slate-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-lock"></i> Đã đóng
                                        </div>
                                        @break
                                    @default
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-600 border border-slate-200 rounded-lg text-[11px] font-bold uppercase">
                                            <i class="fa-solid fa-circle-info"></i> {{ $contact->status }}
                                        </div>
                                @endswitch
                            </div>

                            <div class="text-right mt-0 lg:mt-4 flex flex-col items-end justify-between h-full">
                                <div class="w-full text-right">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ngày gửi</p>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $contact->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                
                                @if($contact->status === 'pending')
                                <button @click="if(confirm('Bạn có chắc chắn muốn thu hồi yêu cầu liên hệ này không? Hành động này không thể hoàn tác.')) { 
                                        isRevoking = true; 
                                        fetch('{{ route('contact.revoke', $contact->_id ?? $contact->id) }}', {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        }).then(res => res.json()).then(data => {
                                            if(data.success) {
                                                isRevoked = true;
                                            } else {
                                                alert(data.message || 'Có lỗi xảy ra');
                                                isRevoking = false;
                                            }
                                        }).catch(err => {
                                            alert('Lỗi mạng hoặc server');
                                            isRevoking = false;
                                        });
                                    }"
                                    class="text-rose-500 hover:bg-rose-50 border border-rose-200 rounded-lg px-3 py-1.5 text-xs font-bold transition-colors flex items-center gap-1.5 mt-2"
                                    :disabled="isRevoking"
                                    :class="{ 'opacity-50 cursor-not-allowed': isRevoking }"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span x-text="isRevoking ? 'Đang thu hồi...' : 'Thu hồi yêu cầu'"></span>
                                </button>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 shadow-inner">
                            <i class="fa-solid fa-box-open text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa có lịch sử liên hệ</h3>
                        <p class="text-sm text-slate-500 mb-6 max-w-sm">Bạn chưa gửi yêu cầu liên hệ xem nhà nào. Hãy khám phá hàng ngàn phòng trọ, căn hộ đang cho thuê ngay hôm nay!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>
</body>
</html>
