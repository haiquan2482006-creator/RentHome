<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RentHome - Tin Tức & Thị Trường Bất Động Sản Cho Thuê</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    @php
        $tColor = $themeSettings['theme_color'] ?? '#16a34a';
        \Carbon\Carbon::setLocale('vi');
    @endphp

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
                            200: '#bbf7d0',
                            500: '#22c55e',
                            600: '{{ $tColor }}',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                    }
                }
            }
        }
    </script>

    <style>
        .news-card-hover {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .news-card-hover:hover {
            transform: translateY(-4px);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-600 selection:text-white min-h-screen flex flex-col justify-between">

    <!-- Header Navigation -->
    @include('partials.header-home')

    <!-- Main Content Container -->
    <main class="pt-28 sm:pt-32 pb-20 flex-grow">
        
        <!-- Hero Header Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-brand-600 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-[11px]"></i> Trang chủ
                </a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-700">Tin tức thị trường</span>
            </nav>

            <!-- Banner Hero Card -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-8 sm:p-12 shadow-xl border border-slate-700/50">
                <!-- Decorative Background Glow -->
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-emerald-400 text-xs font-bold uppercase tracking-wider mb-4 border border-white/10">
                        <i class="fa-solid fa-newspaper"></i> Cổng thông tin & Kiểm duyệt
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight sm:leading-none mb-4">
                        Tin Tức & Thị Trường <span class="text-emerald-400">Cho Thuê</span>
                    </h1>
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal mb-6">
                        Khám phá toàn bộ tin tức bất động sản, diễn biến giá thuê căn hộ, nhà trọ và cẩm nang pháp lý đã được ban quản trị kiểm duyệt và phê duyệt công khai.
                    </p>

                    <!-- Stats Badges -->
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-semibold text-slate-200">
                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <i class="fa-solid fa-circle-check text-emerald-400"></i>
                            <span><strong id="badge-total-count" class="text-white">{{ $articles->count() }}</strong> tin đã duyệt</span>
                        </div>
                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <i class="fa-solid fa-rss text-amber-400"></i>
                            <span>Nguồn: VnExpress, CafeF, Dân Trí, Batdongsan</span>
                        </div>
                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <i class="fa-solid fa-shield-halved text-sky-400"></i>
                            <span>100% Đã kiểm chứng</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter Section (Lọc Toàn Bộ Tin Tức) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Left: Category Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-none" id="category-pills">
                        <button type="button" onclick="selectCategory('all', this)"
                            data-category="all"
                            class="category-pill px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ !request('category') || request('category') === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Tất cả chuyên mục
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" onclick="selectCategory('{{ $cat }}', this)"
                                data-category="{{ $cat }}"
                                class="category-pill px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ request('category') === $cat ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Right Controls: Source filter, Sort order & Reset button -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <!-- Source Selector -->
                        <div class="relative min-w-[160px]">
                            <select id="source-filter" onchange="applyFilters()" 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-3.5 pr-8 text-xs sm:text-sm focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/20 outline-none transition-all text-slate-700 font-medium appearance-none">
                                <option value="all">📰 Tất cả nguồn báo</option>
                                @foreach($sources as $src)
                                    <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>
                                        {{ $src }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>

                        <!-- Sort Order Selector -->
                        <div class="relative min-w-[150px]">
                            <select id="sort-filter" onchange="applyFilters()" 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-3.5 pr-8 text-xs sm:text-sm focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/20 outline-none transition-all text-slate-700 font-medium appearance-none">
                                <option value="latest" {{ request('sort') !== 'oldest' ? 'selected' : '' }}>⏱️ Mới nhất trước</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>⏳ Cũ nhất trước</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>

                        <!-- Reset Filter Button -->
                        <button type="button" onclick="resetFilters()" title="Làm mới bộ lọc"
                            class="px-3.5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors text-xs sm:text-sm font-semibold flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-rotate-right"></i>
                            <span class="hidden sm:inline">Đặt lại</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Article (Hero Spotlight) if available -->
        @if ($featuredArticle && (!request('category') || request('category') === 'all') && (!request('source') || request('source') === 'all'))
        <section id="featured-article-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-600 animate-pulse"></span>
                <h2 class="text-sm font-extrabold uppercase tracking-widest text-slate-500">Bài Viết Tiêu Điểm</h2>
            </div>

            <article class="group relative rounded-3xl overflow-hidden min-h-[380px] sm:min-h-[440px] flex flex-col justify-end p-6 sm:p-10 shadow-lg border border-slate-200/80 bg-slate-900 transition-all duration-300">
                <img src="{{ $featuredArticle->image_url ?? 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80' }}"
                    alt="{{ $featuredArticle->title }}"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-75"
                    onerror="this.src='https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80'">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

                <!-- Badges -->
                <div class="relative z-10 flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold uppercase shadow-sm">
                        {{ $featuredArticle->is_featured ? 'Nổi bật' : ($featuredArticle->category ?? 'Tin thị trường') }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-xs font-semibold">
                        {{ $featuredArticle->source_name ?? 'RentHome' }}
                    </span>
                    <span class="text-xs font-medium text-slate-300 ml-2">
                        <i class="fa-regular fa-clock"></i> 
                        {{ $featuredArticle->published_at ? \Carbon\Carbon::parse($featuredArticle->published_at)->diffForHumans() : 'Mới cập nhật' }}
                    </span>
                </div>

                <!-- Content -->
                <div class="relative z-10 max-w-4xl">
                    <h3 class="text-xl sm:text-3xl font-black text-white group-hover:text-emerald-300 transition-colors leading-snug">
                        <a href="{{ $featuredArticle->source_url ?: '#' }}" target="_blank" rel="noopener noreferrer">
                            {{ $featuredArticle->title }}
                        </a>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-200 mt-2.5 line-clamp-2 sm:line-clamp-3 leading-relaxed font-normal">
                        {{ $featuredArticle->summary }}
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <a href="{{ $featuredArticle->source_url ?: '#' }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md transition-all">
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Đọc chi tiết
                        </a>
                    </div>
                </div>
            </article>
        </section>
        @endif

        <!-- News Grid Header & Counter -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                    Danh Sách Tin Tức Đã Duyệt
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5" id="news-counter-text">
                    Hiển thị <span class="font-bold text-slate-800" id="current-visible-count">{{ $articles->count() }}</span> bài viết
                </p>
            </div>
        </section>

        <!-- All News Grid Cards ("Form" Chứa Toàn Bộ Tin Tức) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="news-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($articles as $art)
                    @php
                        $source = strtolower($art->source_name ?? '');
                        $sourceBadgeColor = 'bg-brand-600 text-white';
                        if (str_contains($source, 'vnexpress')) $sourceBadgeColor = 'bg-slate-900 text-white';
                        elseif (str_contains($source, 'batdongsan')) $sourceBadgeColor = 'bg-blue-600 text-white';
                        elseif (str_contains($source, 'cafef')) $sourceBadgeColor = 'bg-amber-600 text-white';
                        elseif (str_contains($source, 'dân trí') || str_contains($source, 'dan tri')) $sourceBadgeColor = 'bg-emerald-600 text-white';
                    @endphp

                    <article 
                        data-id="{{ $art->id }}"
                        data-category="{{ $art->category ?? '' }}"
                        data-source="{{ $art->source_name ?? '' }}"
                        data-date="{{ $art->published_at ? \Carbon\Carbon::parse($art->published_at)->timestamp : ($art->created_at ? \Carbon\Carbon::parse($art->created_at)->timestamp : 0) }}"
                        class="news-card news-card-hover bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-sm flex flex-col justify-between group">
                        
                        <!-- Top Image & Badges -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <a href="{{ $art->source_url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
                                <img src="{{ $art->image_url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80' }}"
                                    alt="{{ $art->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    onerror="this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80'">
                            </a>

                            <!-- Gradient bottom shadow on image -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent opacity-80 pointer-events-none"></div>

                            <!-- Top Badges -->
                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm {{ $sourceBadgeColor }}">
                                    {{ $art->source_name ?? 'Tin BĐS' }}
                                </span>
                                @if ($art->is_featured)
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500 text-white text-[10px] font-bold uppercase shadow-sm">
                                        <i class="fa-solid fa-star text-[9px]"></i> Nổi bật
                                    </span>
                                @endif
                            </div>

                            <!-- Bottom Category & Time on Image -->
                            <div class="absolute bottom-2.5 left-3 right-3 flex items-center justify-between text-white text-[11px] font-medium pointer-events-none">
                                <span class="px-2 py-0.5 rounded-md bg-slate-900/70 backdrop-blur-xs font-semibold">
                                    {{ $art->category ?? 'Thị trường' }}
                                </span>
                                <span class="text-slate-200 drop-shadow-sm">
                                    <i class="fa-regular fa-clock"></i> 
                                    {{ $art->published_at ? \Carbon\Carbon::parse($art->published_at)->diffForHumans() : 'Mới cập nhật' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug mb-2.5">
                                    <a href="{{ $art->source_url ?: '#' }}" target="_blank" rel="noopener noreferrer">
                                        {{ $art->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-4">
                                    {{ $art->summary ?? 'Bấm xem chi tiết để đọc nội dung tin tức bất động sản đã được duyệt.' }}
                                </p>
                            </div>

                            <!-- Card Footer Action Buttons -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                                <a href="{{ $art->source_url ?: '#' }}" target="_blank" rel="noopener noreferrer"
                                    class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-brand-600 text-slate-700 hover:text-white text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 group/btn">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform"></i>
                                    <span>Đọc chi tiết</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div id="no-articles-state" class="col-span-full py-16 text-center">
                        <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Hiện chưa có tin tức nào được phê duyệt</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Vui lòng quay lại sau hoặc liên hệ ban kiểm duyệt để cập nhật thêm tin tức mới.</p>
                    </div>
                @endforelse
            </div>

            <!-- Empty Search State (Hidden by default, shown via JS if filtered to 0) -->
            <div id="empty-search-state" class="hidden py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mx-auto mb-3 border border-amber-100">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Không tìm thấy bài viết nào phù hợp</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Thử chọn chuyên mục khác hoặc đặt lại bộ lọc để xem toàn bộ tin tức.</p>
                <button type="button" onclick="resetFilters()" class="mt-4 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition-all shadow-sm">
                    Xóa bộ lọc & Hiển thị tất cả
                </button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Client-Side Reactive Filter Scripts -->
    <script>
        let currentCategory = '{{ request('category', 'all') }}';
        let currentSource = '{{ request('source', 'all') }}';
        let currentSort = '{{ request('sort', 'latest') }}';

        // Lọc bài viết client-side ngay lập tức
        function applyFilters() {
            const source = document.getElementById('source-filter')?.value || 'all';
            const sort = document.getElementById('sort-filter')?.value || 'latest';
            
            currentSource = source;
            currentSort = sort;

            const gridContainer = document.getElementById('news-grid-container');
            const cards = Array.from(document.querySelectorAll('.news-card'));
            let visibleCount = 0;

            // Lọc theo chuyên mục & nguồn báo
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category') || '';
                const cardSrc = card.getAttribute('data-source') || '';

                const matchesCat = (currentCategory === 'all' || cardCat === currentCategory);
                const matchesSrc = (currentSource === 'all' || cardSrc === currentSource);

                if (matchesCat && matchesSrc) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Sắp xếp bài viết
            cards.sort((a, b) => {
                const dateA = parseInt(a.getAttribute('data-date') || '0', 10);
                const dateB = parseInt(b.getAttribute('data-date') || '0', 10);
                return sort === 'oldest' ? dateA - dateB : dateB - dateA;
            });
            cards.forEach(card => gridContainer.appendChild(card));

            // Ẩn/Hiện empty state
            const emptyState = document.getElementById('empty-search-state');
            const counterText = document.getElementById('current-visible-count');

            if (counterText) counterText.innerText = visibleCount;

            if (visibleCount === 0 && cards.length > 0) {
                emptyState?.classList.remove('hidden');
            } else {
                emptyState?.classList.add('hidden');
            }

            // Ẩn/Hiện hero article khi đang lọc
            const featuredSection = document.getElementById('featured-article-section');
            if (featuredSection) {
                if (currentCategory !== 'all' || currentSource !== 'all') {
                    featuredSection.style.display = 'none';
                } else {
                    featuredSection.style.display = 'block';
                }
            }

            // Cập nhật URL trình duyệt (không reload)
            const url = new URL(window.location);
            url.searchParams.delete('keyword');

            if (currentCategory !== 'all') url.searchParams.set('category', currentCategory);
            else url.searchParams.delete('category');

            if (currentSource !== 'all') url.searchParams.set('source', currentSource);
            else url.searchParams.delete('source');

            if (currentSort !== 'latest') url.searchParams.set('sort', currentSort);
            else url.searchParams.delete('sort');

            window.history.replaceState({}, '', url);
        }

        function selectCategory(category, buttonElement) {
            currentCategory = category;
            
            // Cập nhật style nút category
            document.querySelectorAll('.category-pill').forEach(pill => {
                pill.classList.remove('bg-brand-600', 'text-white', 'shadow-xs');
                pill.classList.add('bg-slate-100', 'text-slate-600');
            });

            if (buttonElement) {
                buttonElement.classList.remove('bg-slate-100', 'text-slate-600');
                buttonElement.classList.add('bg-brand-600', 'text-white', 'shadow-xs');
            }

            applyFilters();
        }

        function resetFilters() {
            currentCategory = 'all';
            currentSource = 'all';
            currentSort = 'latest';

            const sourceSelect = document.getElementById('source-filter');
            if (sourceSelect) sourceSelect.value = 'all';

            const sortSelect = document.getElementById('sort-filter');
            if (sortSelect) sortSelect.value = 'latest';

            // Reset category pills
            document.querySelectorAll('.category-pill').forEach(pill => {
                if (pill.getAttribute('data-category') === 'all') {
                    pill.classList.add('bg-brand-600', 'text-white', 'shadow-xs');
                    pill.classList.remove('bg-slate-100', 'text-slate-600');
                } else {
                    pill.classList.remove('bg-brand-600', 'text-white', 'shadow-xs');
                    pill.classList.add('bg-slate-100', 'text-slate-600');
                }
            });

            applyFilters();
        }
    </script>
</body>
</html>
