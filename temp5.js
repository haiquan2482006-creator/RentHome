
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
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(34, 197, 94, 0.4)',
                        'card': '0 10px 30px -5px rgba(0, 0, 0, 0.08)',
                    }
                }
            }
        }
    

        // Dynamic Management for Featured Posts Section (Dành cho Ban Quản Lý)
        // Đã xóa bỏ mảng tĩnh DEFAULT_FEATURED_POSTS

        async function fetchFeaturedPosts() {
            try {
                const response = await fetch('/api/posts/featured');
                if (response.ok) {
                    const data = await response.json();
                    return Array.isArray(data) ? data : [];
                }
            } catch (error) {
                console.error("Lỗi khi fetch bài đăng nổi bật:", error);
            }
            return [];
        }
        let allRealPosts = [];
        let currentDisplayCount = 6;
        let hasLoadedMore = false;

        async function fetchRealPosts() {
            const container = document.getElementById('real-posts-container');
            if (!container) return;
            try {
                const response = await fetch('/api/posts/approved');
                if (response.ok) {
                    const data = await response.json();
                    const posts = data.posts || [];
                    if (!Array.isArray(posts) || posts.length === 0) {
                        container.innerHTML = '<p class="col-span-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                        const loadMoreContainer = document.getElementById('load-more-container');
                        if (loadMoreContainer) loadMoreContainer.style.display = 'none';
                        return;
                    }
                    
                    allRealPosts = posts;
                    renderPosts();
                } else {
                    container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10">Lỗi khi tải dữ liệu bài đăng!</p>';
                }
            } catch (error) {
                console.error('Error fetching real posts:', error);
            }
        }

        function renderPosts() {
            const container = document.getElementById('real-posts-container');
            const loadMoreBtn = document.getElementById('load-more-btn');
            const goToBaidangBtn = document.getElementById('go-to-baidang-btn');
            
            let html = '';
            const postsToShow = allRealPosts.slice(0, currentDisplayCount);
            
            postsToShow.forEach(post => {
                                                const userName = post.user ? (post.user.account_name || post.user.username) : 'Người dùng';
                        const userType = post.user ? (post.user.account_type === 'doanhnghiep' ? 'Doanh nghiệp' : 'Cá nhân') : 'Cá nhân';
                        const userBadgeClass = (post.user && post.user.account_type === 'doanhnghiep') ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-500 border border-slate-200';
                        const userIconColor = (post.user && post.user.account_type === 'doanhnghiep') ? 'text-blue-500 bg-blue-50' : 'text-slate-500 bg-slate-100';
                        const userIcon = (post.user && post.user.account_type === 'doanhnghiep') ? 'fa-building' : 'fa-user';
                        // ĐOẠN CODE MỚI
                        let imgUrl = post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                        let unitText = 'đ/tháng';
                        if (post.price_unit === 'nam') unitText = 'đ/năm';
                        else if (post.price_unit === 'm2') unitText = 'đ/m²';
                        else if (post.price_unit === 'tong') unitText = 'VNĐ';
                        const price = new Intl.NumberFormat('vi-VN').format(Number(post.price) || 0) + ' ' + unitText;
                        const address = [post.address, post.ward, post.district, post.province].filter(Boolean).join(', ');
                        
                        const postId = post.id || post._id;
                        const postOwnerId = String(post.user_id || (post.user && (post.user._id || post.user.id)) || '');
                        const isFav = window.RentHomeFavorites && window.RentHomeFavorites.has(postId);
                        const isEnterprise = window.currentUserType === 'doanhnghiep';
                        const isMyPost = Boolean(window.currentUserId && postOwnerId && postOwnerId === String(window.currentUserId));
                        const canFavorite = !isEnterprise && !isMyPost;

                        let favButtonHtml = '';
                        if (canFavorite) {
                            favButtonHtml = `
                                <!-- Favorite Button -->
                                <button type="button" 
                                        onclick="window.RentHomeFavorites.toggle('${postId}', event, '${postOwnerId}')" 
                                        data-fav-id="${postId}"
                                        data-owner-id="${postOwnerId}"
                                        class="fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full ${isFav ? 'bg-white text-rose-500 shadow-md ring-2 ring-rose-100' : 'bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-sm'} flex items-center justify-center transition-all duration-200 active:scale-90 group/heart"
                                        title="${isFav ? 'Bỏ yêu thích' : 'Yêu thích bài đăng'}">
                                    <i class="${isFav ? 'fa-solid fa-heart text-rose-500' : 'fa-regular fa-heart text-slate-400 group-hover/heart:text-rose-500'} text-base transition-transform duration-200 group-hover/heart:scale-110"></i>
                                </button>
                            `;
                        }

                        html += `
                            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group relative">
                                <div class="relative aspect-[4/3] overflow-hidden">
                                    <img src="${imgUrl}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    
                                    ${favButtonHtml}

                                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl font-bold text-xs shadow-sm">
                                        <span class="text-brand-600">${price}</span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-slate-900 mb-2 leading-tight truncate">
                                        ${post.title}
                                    </h3>
                                    <div class="flex flex-col gap-1.5 mb-4 text-xs font-semibold text-slate-500">
                                        <div class="flex items-center gap-2 truncate">
                                            <i class="fa-solid fa-location-dot w-4 text-center"></i>
                                            <span>${address}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-combined w-4 text-center"></i>
                                            <span>${post.area} m²</span>
                                                                                </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100 mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shrink-0 shadow-sm border border-slate-100 ${userIconColor}">
                                                <i class="fa-solid ${userIcon}"></i>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <span class="text-sm font-bold text-slate-800 truncate max-w-[120px] sm:max-w-[160px]">${userName}</span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mt-0.5 w-max ${userBadgeClass}">${userType}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/chitietbaidang/${post.id || post._id}" class="block w-full text-center py-2.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-sm transition-all duration-300 border border-brand-100 hover:border-brand-600">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    if (window.RentHomeFavorites) {
                        window.RentHomeFavorites.updateCardButtons();
                    }

            if (!loadMoreBtn || !goToBaidangBtn) return;

            if (hasLoadedMore) {
                loadMoreBtn.style.display = 'none';
                goToBaidangBtn.style.display = 'inline-flex';
                goToBaidangBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.style.display = 'inline-flex';
                goToBaidangBtn.style.display = 'none';
            }
        }

        function loadMorePosts() {
            if (!hasLoadedMore) {
                currentDisplayCount = 12;
                hasLoadedMore = true;
                renderPosts();
            }
        }

        const IS_ADMIN_USER = false && ((Auth::user()->account_type ?? '') === 'admin' || (Auth::user()->role ?? '') === 'admin'));
        let featuredAutoPlayTimer = null;
        let isFeaturedScrolling = false;

        function startFeaturedAutoPlay() {
            stopFeaturedAutoPlay();
            featuredAutoPlayTimer = setInterval(() => {
                scrollFeaturedPosts('right');
            }, 3500);
        }

        function stopFeaturedAutoPlay() {
            if (featuredAutoPlayTimer) {
                clearInterval(featuredAutoPlayTimer);
                featuredAutoPlayTimer = null;
            }
        }



        async function renderFeaturedPostsSection() {
            const track = document.getElementById('featured-posts-track');
            const slider = document.getElementById('featured-posts-slider');
            if (!track) return;

            stopFeaturedAutoPlay();
            const posts = await fetchFeaturedPosts();
            if (!posts || posts.length === 0) {
                track.innerHTML = '<p class="w-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                return;
            }

            track.style.transition = 'none';
            track.style.transform = 'translate3d(0, 0, 0)';

            track.innerHTML = posts.map(post => {
                const postOwnerId = String(post.user_id || '');
                const isEnterprise = window.currentUserType === 'doanhnghiep';
                const isMyPost = Boolean(window.currentUserId && postOwnerId && postOwnerId === String(window.currentUserId));
                const canFavorite = !isEnterprise && !isMyPost;
                const isFav = window.RentHomeFavorites && window.RentHomeFavorites.has(post.id);

                let favBtnHtml = '';
                if (canFavorite) {
                    favBtnHtml = `
                        <!-- Favorite Button for Featured Post -->
                        <button type="button" 
                                onclick="window.RentHomeFavorites.toggle('${post.id}', event, '${postOwnerId}')" 
                                data-fav-id="${post.id}"
                                data-owner-id="${postOwnerId}"
                                class="fav-toggle-btn absolute top-4 right-4 z-10 w-9 h-9 rounded-full ${isFav ? 'bg-white text-rose-500 shadow-md ring-2 ring-rose-100' : 'bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-md'} flex items-center justify-center transition-all duration-200 active:scale-90 group/heart"
                                title="${isFav ? 'Bỏ yêu thích' : 'Yêu thích bài đăng'}">
                            <i class="${isFav ? 'fa-solid fa-heart text-base text-rose-500' : 'fa-regular fa-heart text-base text-slate-400 group-hover/heart:text-rose-500'} transition-transform duration-200 group-hover/heart:scale-110"></i>
                        </button>
                    `;
                }

                return `
                <div class="w-[310px] sm:w-[350px] md:w-[370px] shrink-0 bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-card hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group relative">
                    ${IS_ADMIN_USER ? `
                    <!-- Delete/Unfeature button for Admin only -->
                    <button onclick="removeFeaturedPost('${post.id}')" title="Gỡ khỏi danh sách nổi bật (Admin)"
                        class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-slate-900/70 hover:bg-rose-600 text-white backdrop-blur-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-md cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                    ` : ''}

                    <!-- Image Frame -->
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="${post.image}" alt="${post.title}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-amber-500 text-white font-extrabold text-[11px] uppercase tracking-wider shadow-md flex items-center gap-1">
                            <i class="fa-solid fa-star text-amber-200 text-[10px]"></i> ${post.tag || 'NỔI BẬT'}
                        </span>

                        ${favBtnHtml}
                        <div class="absolute bottom-4 left-4 bg-slate-900/85 backdrop-blur-md text-white px-3 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1 shadow-md">
                            <span class="text-amber-400 text-sm font-black">${post.price}</span> ${post.period || '/ tháng'}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-xs font-semibold text-amber-600 mb-2">
                                <i class="fa-solid fa-shield-check text-emerald-500"></i> ${post.note || 'Đã kiểm duyệt bởi Ban Quản Lý'}
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-amber-600 transition-colors line-clamp-2 leading-snug">
                                ${post.title}
                            </h3>
                            <p class="mt-2 text-xs text-slate-500 flex items-center gap-1.5 line-clamp-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i> ${post.location}
                            </p>
                        </div>

                        <!-- Specs Row -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-slate-600 text-xs font-semibold">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bed text-amber-500"></i> ${post.beds}</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bath text-amber-500"></i> ${post.baths}</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-vector-square text-amber-500"></i> ${post.area}</span>
                        </div>

                        <!-- Card Action -->
                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Ưu tiên hiển thị
                            </span>
                            <button onclick="window.location.href='/chitietbaidang/${post.id}'"
                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-amber-500 text-white text-xs font-bold transition-colors shadow-sm cursor-pointer">
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            if (slider) {
                slider.onmouseenter = stopFeaturedAutoPlay;
                slider.onmouseleave = startFeaturedAutoPlay;
            }

            if (window.RentHomeFavorites) {
                window.RentHomeFavorites.updateCardButtons();
            }

            startFeaturedAutoPlay();
        }

        function scrollFeaturedPosts(direction) {
            const track = document.getElementById('featured-posts-track');
            if (!track || isFeaturedScrolling) return;

            const cards = track.children;
            if (cards.length <= 1) return;

            isFeaturedScrolling = true;

            const firstCard = cards[0];
            const trackGap = parseFloat(window.getComputedStyle(track).gap) || 24;
            const step = firstCard.offsetWidth + trackGap;

            if (direction === 'right') {
                track.style.transition = 'transform 0.7s cubic-bezier(0.25, 1, 0.5, 1)';
                track.style.transform = `translate3d(-${step}px, 0, 0)`;

                const handleTransitionEnd = (e) => {
                    if (e.target !== track) return;
                    track.removeEventListener('transitionend', handleTransitionEnd);

                    track.style.transition = 'none';
                    if (track.firstElementChild) {
                        track.appendChild(track.firstElementChild);
                    }
                    track.style.transform = 'translate3d(0, 0, 0)';

                    // Force browser reflow to flush transform
                    void track.offsetHeight;
                    isFeaturedScrolling = false;
                };

                track.addEventListener('transitionend', handleTransitionEnd);
            } else {
                track.style.transition = 'none';
                const lastCard = track.lastElementChild;
                if (lastCard) {
                    track.insertBefore(lastCard, track.firstElementChild);
                }
                track.style.transform = `translate3d(-${step}px, 0, 0)`;

                // Force browser reflow
                void track.offsetHeight;

                track.style.transition = 'transform 0.7s cubic-bezier(0.25, 1, 0.5, 1)';
                track.style.transform = 'translate3d(0, 0, 0)';

                const handleTransitionEnd = (e) => {
                    if (e.target !== track) return;
                    track.removeEventListener('transitionend', handleTransitionEnd);
                    isFeaturedScrolling = false;
                };

                track.addEventListener('transitionend', handleTransitionEnd);
            }
        }

        function openAddFeaturedModal() {
            const modal = document.getElementById('modal-add-featured-post');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAddFeaturedModal() {
            const modal = document.getElementById('modal-add-featured-post');
            if (modal) modal.classList.add('hidden');
        }

        function setPresetImage(url) {
            const imgInput = document.getElementById('feat-image');
            if (imgInput) imgInput.value = url;
        }

        function handleSaveFeaturedPost(e) {
            e.preventDefault();
            const title = document.getElementById('feat-title').value.trim();
            const price = document.getElementById('feat-price').value.trim();
            const tag = document.getElementById('feat-tag').value;
            const location = document.getElementById('feat-location').value.trim();
            let image = document.getElementById('feat-image').value.trim();
            const beds = document.getElementById('feat-beds').value.trim() || '2 PN';
            const baths = document.getElementById('feat-baths').value.trim() || '2 WC';
            const area = document.getElementById('feat-area').value.trim() || '80 m²';
            const note = document.getElementById('feat-note').value.trim() || 'Ưu tiên hiển thị bởi Quản Lý';

            if (!image) {
                image = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
            }

            const newPost = {
                id: 'feat-' + Date.now(),
                title,
                price,
                period: '/ tháng',
                location,
                image,
                tag,
                beds,
                baths,
                area,
                note,
                verified: true
            };

            // Scroll slider back to start
            const container = document.getElementById('featured-posts-slider');
            if (container) container.scrollTo({ left: 0, behavior: 'smooth' });

            alert('Tính năng lưu cần được kết nối với API thực tế.');
        }

        function removeFeaturedPost(id) {
            if (!confirm('Bạn có chắc chắn muốn gỡ bài đăng này khỏi danh sách bài đăng nổi bật?')) return;
            // API thật sẽ được gọi ở đây, ví dụ: fetch(`/api/posts/featured/${id}`, { method: 'DELETE' })
            alert('Cần API để xóa bài đăng nổi bật thật.');
        }

        
        // Crossfade background transition mỗi 3.5 giây
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-bg-slide');

        function nextSlide() {
            if (!slides || slides.length === 0) return;
            slides[currentSlide].classList.remove('opacity-100');
            slides[currentSlide].classList.add('opacity-0');

            currentSlide = (currentSlide + 1) % slides.length;

            slides[currentSlide].classList.remove('opacity-0');
            slides[currentSlide].classList.add('opacity-100');
        }

        if (slides.length > 0) {
            setInterval(nextSlide, 3500);
        }

        function toggleWishlist(btn) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid', 'text-rose-500');
            } else {
                icon.classList.remove('fa-solid', 'text-rose-500');
                icon.classList.add('fa-regular');
            }
        }

        function handleSearch() {
            alert('Đang tìm kiếm danh sách nhà thuê phù hợp với tiêu chí của bạn...');
        }

        function openModal(title, price, location) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalPrice').innerText = price + ' / tháng';
            document.getElementById('modalLocation').innerText = location;
            document.getElementById('propertyModal').classList.remove('hidden');
            document.getElementById('propertyModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('propertyModal').classList.add('hidden');
            document.getElementById('propertyModal').classList.remove('flex');
        }

        // Xử lý Modal Đăng nhập / Đăng ký
        function openAuthModal(tab = 'login') {
            switchAuthTab(tab);
            document.getElementById('authModal').classList.remove('hidden');
            document.getElementById('authModal').classList.add('flex');
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
            document.getElementById('authModal').classList.remove('flex');
        }

        function switchAuthTab(tab) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');

                tabLoginBtn.classList.add('text-brand-600', 'border-brand-600');
                tabLoginBtn.classList.remove('text-slate-400', 'border-transparent');

                tabRegisterBtn.classList.remove('text-brand-600', 'border-brand-600');
                tabRegisterBtn.classList.add('text-slate-400', 'border-transparent');
            } else {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');

                tabRegisterBtn.classList.add('text-brand-600', 'border-brand-600');
                tabRegisterBtn.classList.remove('text-slate-400', 'border-transparent');

                tabLoginBtn.classList.remove('text-brand-600', 'border-brand-600');
                tabLoginBtn.classList.add('text-slate-400', 'border-transparent');
            }
        }

        function handleAuthSubmit(type) {
            if (type === 'login') {
                alert('Đăng nhập thành công!');
            } else {
                alert('Đăng ký tài khoản thành công!');
            }
            closeAuthModal();
        }


        // Dữ liệu 63 Tỉnh / Thành phố Việt Nam
        const vietnamLocations = {
            centralCities: [
                "TP. Hồ Chí Minh",
                "Hà Nội",
                "Đà Nẵng",
                "Hải Phòng",
                "Cần Thơ"
            ],
            provinces: [
                "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", "Bắc Ninh",
                "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", "Cà Mau",
                "Cao Bằng", "Đắc Lắk", "Đắc Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp",
                "Gia Lai", "Hà Giang", "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang",
                "Hòa Bình", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu",
                "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An",
                "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam",
                "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", "Tây Ninh",
                "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh",
                "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
            ]
        };

        let currentLocation = "TP. Hồ Chí Minh";

        function removeVietnameseTones(str) {
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                .toLowerCase();
        }

        function renderProvinces(filterKeyword = '') {
            const container = document.getElementById('provinces-list');
            if (!container) return;

            const cleanKeyword = removeVietnameseTones(filterKeyword.trim());
            let html = '';

            // 1. Tất cả địa điểm Option
            const isAllSelected = currentLocation === 'Tất cả địa điểm';
            if (!cleanKeyword || removeVietnameseTones('tất cả địa điểm').includes(cleanKeyword)) {
                html += `
                    <div onclick="selectLocation('Tất cả địa điểm')" class="px-3.5 py-2.5 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-bold transition-colors ${isAllSelected ? 'bg-emerald-50/80 text-brand-600' : 'text-slate-700'}">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-slate-400 text-xs"></i> Tất cả địa điểm</span>
                        ${isAllSelected ? '<i class="fa-solid fa-check text-brand-600"></i>' : ''}
                    </div>
                `;
            }

            // 2. Thành Phố Trực Thuộc Trung Ương
            const filteredCities = vietnamLocations.centralCities.filter(c =>
                removeVietnameseTones(c).includes(cleanKeyword)
            );

            if (filteredCities.length > 0) {
                html +=
                    `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏙️ Thành phố trực thuộc TW</div>`;
                filteredCities.forEach(city => {
                    const isSelected = currentLocation === city;
                    html += `
                        <div onclick="selectLocation('${city}')" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-semibold transition-colors ${isSelected ? 'bg-emerald-50/80 text-brand-600 font-bold' : 'text-slate-800'}">
                            <span>${city}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            // 3. Các Tỉnh Thành
            const filteredProvinces = vietnamLocations.provinces.filter(p =>
                removeVietnameseTones(p).includes(cleanKeyword)
            );

            if (filteredProvinces.length > 0) {
                html +=
                    `<div class="px-3.5 py-1.5 bg-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-600 border-y border-slate-200/70">🏔️ Tỉnh thành</div>`;
                filteredProvinces.forEach(prov => {
                    const isSelected = currentLocation === prov;
                    html += `
                        <div onclick="selectLocation('${prov}')" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs font-medium transition-colors ${isSelected ? 'bg-emerald-50/80 text-brand-600 font-bold' : 'text-slate-700'}">
                            <span>${prov}</span>
                            ${isSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs"></i>' : ''}
                        </div>
                    `;
                });
            }

            if (filteredCities.length === 0 && filteredProvinces.length === 0 && (!cleanKeyword || !removeVietnameseTones(
                    'tất cả địa điểm').includes(cleanKeyword))) {
                html =
                    `<div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">Không tìm thấy tỉnh / thành phố phù hợp</div>`;
            }

            container.innerHTML = html;
        }

        function toggleLocationDropdown(event) {
            if (event) event.stopPropagation();
            const panel = document.getElementById('location-dropdown-panel');
            const arrow = document.getElementById('location-dropdown-arrow');
            const searchInput = document.getElementById('location-search-input');

            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                panel.classList.remove('hidden');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                renderProvinces();
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 50);
            } else {
                panel.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }

        function filterProvinces() {
            const input = document.getElementById('location-search-input');
            const clearBtn = document.getElementById('clear-search-btn');
            const val = input ? input.value : '';

            if (clearBtn) {
                if (val.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            renderProvinces(val);
        }

        function clearLocationSearch() {
            const input = document.getElementById('location-search-input');
            const clearBtn = document.getElementById('clear-search-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            renderProvinces('');
            if (input) input.focus();
        }

        function selectLocation(name) {
            currentLocation = name;
            const textEl = document.getElementById('selected-location-text');
            const inputEl = document.getElementById('selected-location-input');
            if (textEl) textEl.innerText = name;
            if (inputEl) inputEl.value = name;

            const panel = document.getElementById('location-dropdown-panel');
            const arrow = document.getElementById('location-dropdown-arrow');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }

        // Close location dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const locationContainer = document.getElementById('location-select-container');
            const locationPanel = document.getElementById('location-dropdown-panel');
            const locationArrow = document.getElementById('location-dropdown-arrow');

            if (locationContainer && locationPanel && !locationContainer.contains(event.target)) {
                locationPanel.classList.add('hidden');
                if (locationArrow) locationArrow.style.transform = 'rotate(0deg)';
            }
        });

        // Initialize Featured Posts on load
        document.addEventListener('DOMContentLoaded', function() {
            renderFeaturedPostsSection();
            fetchRealPosts();
        });
    