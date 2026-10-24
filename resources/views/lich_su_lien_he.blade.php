<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Yêu Cầu Liên Hệ - RentHome</title>

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
            $tColor = $themeSettings['theme_color'] ?? '#16a34a';
        @endphp
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '{{ $tColor }}10',
                            100: '{{ $tColor }}20',
                            500: '{{ $tColor }}',
                            600: '{{ $tColor }}',
                            700: '{{ $tColor }}',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 20px rgba(22, 163, 74, 0.2)',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .tab-btn.active {
            color: #ffffff !important;
            background-color: {{ $tColor }} !important;
            box-shadow: 0 4px 12px {{ $tColor }}40;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col justify-between text-slate-800 antialiased">

    <!-- Header Chung -->
    @include('partials.header-home')

    @php
        $contacts = $myContacts ?? collect();
        $totalCount = $contacts->count();
        $pendingCount = $contacts->filter(fn($c) => in_array(strtolower($c->status ?? ''), ['pending', 'chờ phản hồi', 'chờ duyệt']))->count();
        $communicatingCount = $contacts->filter(fn($c) => in_array(strtolower($c->status ?? ''), ['communicating', 'approved', 'đang liên hệ', 'đồng ý']))->count();
        $contractedCount = $contacts->filter(fn($c) => in_array(strtolower($c->status ?? ''), ['completed', 'đã ký hợp đồng', 'signed']))->count();
        $rejectedCount = $contacts->filter(fn($c) => in_array(strtolower($c->status ?? ''), ['rejected', 'từ chối', 'closed', 'đã đóng']))->count();
    @endphp

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-32 pb-12">
        
        <!-- Breadcrumb & Tiêu đề -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-3">
                <a href="/" class="hover:text-brand-600 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-house"></i> Trang chủ
                </a>
                <span>/</span>
                <span class="text-slate-800 font-bold">Lịch sử liên hệ</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shadow-sm">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </span>
                        <span>Lịch Sử Yêu Cầu Liên Hệ & Xem Phòng</span>
                    </h1>
                    <p class="text-slate-500 text-sm mt-1.5">
                        Theo dõi tiến độ, lịch hẹn và phản hồi của chủ nhà / quản lý tòa nhà đối với các yêu cầu của bạn.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/baidang') }}" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-600/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Tìm thêm phòng
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Thẻ Thống Kê Tổng Quan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalCount }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Tổng yêu cầu</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-600">{{ $pendingCount }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Chờ phản hồi</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-sky-600">{{ $communicatingCount }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Đang trao đổi</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-soft flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-600">{{ $contractedCount }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Đã ký hợp đồng</div>
                </div>
            </div>
        </div>

        <!-- Thanh Lọc Trạng Thái & Ô Tìm Kiếm -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 mb-6 shadow-soft flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Tabs lọc trạng thái -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0" id="filter-tabs">
                <button type="button" onclick="filterByStatus('all', this)" class="tab-btn active px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition-all whitespace-nowrap">
                    Tất cả ({{ $totalCount }})
                </button>
                <button type="button" onclick="filterByStatus('pending', this)" class="tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition-all whitespace-nowrap">
                    Chờ phản hồi ({{ $pendingCount }})
                </button>
                <button type="button" onclick="filterByStatus('communicating', this)" class="tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition-all whitespace-nowrap">
                    Đang trao đổi ({{ $communicatingCount }})
                </button>
                <button type="button" onclick="filterByStatus('contracted', this)" class="tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition-all whitespace-nowrap">
                    Đã ký HĐ ({{ $contractedCount }})
                </button>
                <button type="button" onclick="filterByStatus('rejected', this)" class="tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 transition-all whitespace-nowrap">
                    Từ chối / Đóng ({{ $rejectedCount }})
                </button>
            </div>

            <!-- Ô tìm kiếm bài đăng / ghi chú -->
            <div class="relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="search-keyword" oninput="searchContacts()" placeholder="Tìm theo tên bài đăng, ghi chú..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
            </div>
        </div>

        <!-- Danh Sách Các Yêu Cầu Liên Hệ -->
        <div id="contact-list" class="space-y-4">
            @forelse($contacts as $contact)
                @php
                    $cId = (string)($contact->_id ?? $contact->id);
                    $post = $contact->post;
                    $st = strtolower($contact->status ?? 'pending');
                    
                    // Phân nhóm trạng thái chuẩn hóa
                    $statusGroup = 'pending';
                    if (in_array($st, ['pending', 'chờ phản hồi', 'chờ duyệt'])) {
                        $statusGroup = 'pending';
                        $statusBadgeClass = 'bg-amber-100 text-amber-700 border-amber-200';
                        $statusIcon = 'fa-hourglass-half';
                        $statusLabel = 'Chờ chủ nhà phản hồi';
                    } elseif (in_array($st, ['communicating', 'approved', 'đang liên hệ', 'đồng ý'])) {
                        $statusGroup = 'communicating';
                        $statusBadgeClass = 'bg-sky-100 text-sky-700 border-sky-200';
                        $statusIcon = 'fa-comments';
                        $statusLabel = 'Chủ nhà đã đồng ý trao đổi';
                    } elseif (in_array($st, ['completed', 'đã ký hợp đồng', 'signed'])) {
                        $statusGroup = 'contracted';
                        $statusBadgeClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                        $statusIcon = 'fa-circle-check';
                        $statusLabel = 'Đã ký hợp đồng';
                    } else {
                        $statusGroup = 'rejected';
                        $statusBadgeClass = 'bg-rose-100 text-rose-700 border-rose-200';
                        $statusIcon = 'fa-circle-xmark';
                        $statusLabel = 'Đã từ chối / Đã đóng';
                    }

                    // Xử lý ảnh bài đăng
                    $postThumb = 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=800&auto=format&fit=crop';
                    if ($post && !empty($post->images) && is_array($post->images) && count($post->images) > 0) {
                        $imgStr = (string)$post->images[0];
                        if (strlen($imgStr) === 24 && ctype_xdigit($imgStr)) {
                            $postThumb = url('/images/' . $imgStr);
                        } elseif (\Illuminate\Support\Str::startsWith($imgStr, 'http') || \Illuminate\Support\Str::startsWith($imgStr, 'data:')) {
                            $postThumb = $imgStr;
                        } elseif (\Illuminate\Support\Str::startsWith($imgStr, '/storage/')) {
                            $postThumb = asset(ltrim($imgStr, '/'));
                        } else {
                            $postThumb = asset('storage/' . $imgStr);
                        }
                    }

                    $postTitle = $post ? ($post->title ?? 'Phòng cho thuê') : 'Bài đăng không còn tồn tại hoặc đã gỡ';
                    $postPrice = $post ? (isset($post->price) ? number_format($post->price) . ' đ/tháng' : 'Thỏa thuận') : '---';
                    $postAddress = $post ? ($post->address ?? ($post->address_detail ?? 'Địa chỉ đang cập nhật')) : '---';
                    $postDetailUrl = $post ? route('post.detail', $post->_id ?? $post->id) : '#';

                    $createdAt = $contact->created_at ? $contact->created_at->format('d/m/Y H:i') : 'Mới đây';
                    $appDate = !empty($contact->appointment_date) ? $contact->appointment_date : 'Chưa đặt hẹn xem phòng';
                @endphp

                <div class="contact-card bg-white rounded-2xl border border-slate-200/80 p-5 shadow-soft hover:shadow-md transition-all"
                    data-status="{{ $statusGroup }}"
                    data-search="{{ mb_strtolower($postTitle . ' ' . $contact->message . ' ' . $contact->note . ' ' . $postAddress) }}"
                    id="contact-card-{{ $cId }}">
                    
                    <div class="flex flex-col md:flex-row gap-5">
                        <!-- Thumbnail bài đăng -->
                        <div class="w-full md:w-56 h-40 md:h-36 rounded-xl overflow-hidden shrink-0 relative bg-slate-100">
                            <img src="{{ $postThumb }}" alt="{{ $postTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-slate-900/80 text-white font-extrabold text-xs backdrop-blur-sm">
                                {{ $postPrice }}
                            </span>
                        </div>

                        <!-- Thông tin chi tiết yêu cầu -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold border {{ $statusBadgeClass }}">
                                        <i class="fa-solid {{ $statusIcon }}"></i>
                                        <span>{{ $statusLabel }}</span>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-400">
                                        <i class="fa-regular fa-clock mr-1"></i> Gửi lúc: {{ $createdAt }}
                                    </span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-slate-900 line-clamp-1 hover:text-brand-600 transition-colors">
                                    <a href="{{ $postDetailUrl }}" target="_blank">
                                        {{ $postTitle }}
                                    </a>
                                </h2>

                                <p class="text-xs text-slate-500 mt-1 line-clamp-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-rose-500 shrink-0"></i>
                                    <span>{{ $postAddress }}</span>
                                </p>

                                <!-- Chi tiết lịch hẹn & Lời nhắn -->
                                <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar-check text-brand-600 text-sm"></i>
                                        <div>
                                            <span class="font-semibold text-slate-400 block text-[11px]">Lịch hẹn xem phòng:</span>
                                            <span class="font-bold text-slate-800">{{ $appDate }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-comment-dots text-sky-600 text-sm"></i>
                                        <div>
                                            <span class="font-semibold text-slate-400 block text-[11px]">Lời nhắn / Ghi chú:</span>
                                            <span class="font-medium text-slate-700 italic truncate block max-w-[280px]">
                                                "{{ $contact->message ?: ($contact->note ?: 'Khách hàng quan tâm muốn xem phòng sớm') }}"
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hành động -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                <div class="text-xs text-slate-500 font-medium">
                                    @if($statusGroup === 'communicating')
                                        <span class="text-sky-600 font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-bell"></i> Chủ nhà đã chấp nhận! Hãy chú ý điện thoại để nhận liên hệ hẹn xem phòng.
                                        </span>
                                    @elseif($statusGroup === 'contracted')
                                        <span class="text-emerald-600 font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check"></i> Hợp đồng thuê phòng đã được ký kết thành công.
                                        </span>
                                    @elseif($statusGroup === 'pending')
                                        <span class="text-amber-600 font-medium flex items-center gap-1.5">
                                            <i class="fa-solid fa-info-circle"></i> Đang chờ chủ nhà phản hồi yêu cầu của bạn.
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2.5 ml-auto">
                                    @if($post)
                                        <a href="{{ $postDetailUrl }}" target="_blank"
                                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem bài đăng
                                        </a>
                                    @endif

                                    @if($statusGroup === 'contracted')
                                        <a href="{{ route('nha-dang-thue') }}"
                                            class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors flex items-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-house-chimney-user"></i> Nhà đang thuê
                                        </a>
                                    @endif

                                    @if($statusGroup === 'pending')
                                        <button type="button" onclick="revokeContact('{{ $cId }}')"
                                            class="px-3.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-rotate-left"></i> Thu hồi yêu cầu
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-soft">
                    <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-3xl mb-4">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Bạn chưa gửi yêu cầu liên hệ nào</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                        Khám phá hàng ngàn phòng trọ, căn hộ chất lượng trên RentHome và gửi lịch hẹn xem phòng ngay hôm nay!
                    </p>
                    <a href="{{ url('/baidang') }}" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md transition-all inline-flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Khám phá bài đăng cho thuê
                    </a>
                </div>
            @endforelse

            <!-- Placeholder khi tìm kiếm không ra kết quả -->
            <div id="no-search-results" class="hidden bg-white rounded-2xl border border-slate-200/80 p-10 text-center shadow-soft">
                <i class="fa-solid fa-magnifying-glass text-slate-300 text-4xl mb-3"></i>
                <p class="text-sm font-bold text-slate-700">Không tìm thấy yêu cầu liên hệ nào khớp với từ khóa</p>
                <p class="text-xs text-slate-400 mt-1">Vui lòng thử tìm kiếm bằng từ khóa hoặc bộ lọc khác.</p>
            </div>
        </div>

    </main>

    <!-- Footer Chung -->
    @include('partials.footer')

    <script>
        let currentFilterStatus = 'all';

        function filterByStatus(status, element) {
            currentFilterStatus = status;

            // Đổi active tab
            document.querySelectorAll('#filter-tabs .tab-btn').forEach(btn => btn.classList.remove('active'));
            if (element) {
                element.classList.add('active');
            }

            applyFilters();
        }

        function searchContacts() {
            applyFilters();
        }

        function applyFilters() {
            const keyword = (document.getElementById('search-keyword')?.value || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.contact-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const cardSearch = card.getAttribute('data-search') || '';

                const matchesStatus = (currentFilterStatus === 'all') || (cardStatus === currentFilterStatus);
                const matchesKeyword = !keyword || cardSearch.includes(keyword);

                if (matchesStatus && matchesKeyword) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noResultsBox = document.getElementById('no-search-results');
            if (noResultsBox) {
                noResultsBox.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
            }
        }

        // Xử lý thu hồi yêu cầu liên hệ
        function revokeContact(contactId) {
            Swal.fire({
                title: 'Thu hồi yêu cầu liên hệ?',
                text: 'Bạn có chắc chắn muốn hủy yêu cầu xem phòng này không?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý thu hồi',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/contact/${contactId}/revoke`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message || 'Đã thu hồi yêu cầu liên hệ thành công.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            const card = document.getElementById(`contact-card-${contactId}`);
                            if (card) {
                                card.remove();
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else {
                            Swal.fire('Lỗi!', data.message || 'Không thể thu hồi yêu cầu này.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Lỗi!', 'Đã có lỗi xảy ra trong quá trình xử lý.', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
