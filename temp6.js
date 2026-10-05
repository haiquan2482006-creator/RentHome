
    // Xử lý giữ thanh gạch chân khi click trên desktop
    function setActiveNav(element) {
        const navLinks = document.querySelectorAll('#desktop-nav .nav-link');
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        element.classList.add('active');
    }

    // Xử lý cuộn chuột tới đâu sáng tới đó (Scrollspy)
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#desktop-nav .nav-link');
        
        window.addEventListener('scroll', function() {
            let currentId = '';
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href.includes('#')) {
                    const id = href.split('#')[1];
                    const section = document.getElementById(id);
                    if (section) {
                        const sectionTop = section.offsetTop;
                        // Trừ hao 150px cho chiều cao của header cố định
                        if (window.scrollY >= sectionTop - 150) {
                            currentId = id;
                        }
                    }
                }
            });

            // Nếu cuộn chạm đáy trang, luôn chọn phần tử cuối (Liên hệ)
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
                const lastLink = Array.from(navLinks).filter(link => link.getAttribute('href').includes('#')).pop();
                if (lastLink) {
                    currentId = lastLink.getAttribute('href').split('#')[1];
                }
            }
            
            if (currentId) {
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href.includes('#')) {
                        if (href.endsWith('#' + currentId)) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    }
                });
            }
        });
    });

    function toggleMobileNav() {
        const menu = document.getElementById('mobile-nav-menu');
        const icon = document.getElementById('mobile-nav-icon');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    }

    function toggleUserDropdown(event) {
        if (event) event.stopPropagation();
        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favMenu) favMenu.classList.add('hidden');

        const menu = document.getElementById('user-dropdown-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function toggleFavoriteDropdown(event) {
        if (event) event.stopPropagation();
        const userMenu = document.getElementById('user-dropdown-menu');
        if (userMenu) userMenu.classList.add('hidden');

        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favMenu) {
            favMenu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(event) {
        const userContainer = document.getElementById('user-dropdown-container');
        const userMenu = document.getElementById('user-dropdown-menu');
        if (userContainer && userMenu && !userContainer.contains(event.target)) {
            userMenu.classList.add('hidden');
        }

        const favContainer = document.getElementById('favorite-dropdown-container');
        const favMenu = document.getElementById('favorite-dropdown-menu');
        if (favContainer && favMenu && !favContainer.contains(event.target)) {
            favMenu.classList.add('hidden');
        }
    });

    // ==========================================
    // RENTHOME FAVORITES CONTROLLER (GLOBAL)
    // ==========================================
    window.isUserLoggedIn = true;
    window.currentUserId = "user123";
    window.currentUserType = "canhan";
    window.csrfToken = "token";

    window.RentHomeFavorites = {
        favorites: [],
        favoriteIds: new Set(),
        isLoaded: false,

        async init() {
            if (window.isUserLoggedIn && window.currentUserType !== 'doanhnghiep') {
                await this.fetchFavorites();
            } else {
                this.favorites = [];
                this.favoriteIds = new Set();
                this.renderList();
            }
        },

        async fetchFavorites() {
            if (window.currentUserType === 'doanhnghiep') return;

            try {
                const res = await fetch('/api/favorites');
                if (res.ok) {
                    const data = await res.json();
                    if (data.success) {
                        this.favorites = data.favorites || [];
                        this.favoriteIds = new Set((data.favorite_ids || []).map(id => String(id)));
                        this.isLoaded = true;
                        this.renderList();
                        this.updateCardButtons();
                    }
                }
            } catch (e) {
                console.error('Lỗi khi tải bài đăng yêu thích:', e);
            }
        },

        has(postId) {
            return this.favoriteIds.has(String(postId));
        },

        async toggle(postId, event, ownerId = '') {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!window.isUserLoggedIn) {
                this.showToast('Vui lòng đăng nhập bằng tài khoản cá nhân để lưu bài viết yêu thích!', 'warning');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1300);
                return;
            }

            // 1. Kiểm tra tài khoản Doanh nghiệp: không được phép yêu thích
            if (window.currentUserType === 'doanhnghiep') {
                this.showToast('Tài khoản Doanh nghiệp không thể thực hiện chức năng yêu thích.', 'warning');
                return;
            }

            // 2. Kiểm tra nếu là bài đăng của chính mình
            if (window.currentUserId && ownerId && String(ownerId) === String(window.currentUserId)) {
                this.showToast('Bạn không thể tự yêu thích bài đăng của chính mình!', 'warning');
                return;
            }

            const idStr = String(postId);
            const cardBtn = document.querySelector(`[data-fav-id="${idStr}"]`);
            if (cardBtn) {
                cardBtn.classList.add('fav-bounce');
                setTimeout(() => cardBtn.classList.remove('fav-bounce'), 450);
            }

            try {
                const res = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ post_id: idStr })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.is_favorite) {
                        this.favoriteIds.add(idStr);
                        if (data.post) {
                            this.favorites = [data.post, ...this.favorites.filter(p => String(p.id) !== idStr)];
                        } else {
                            await this.fetchFavorites();
                        }
                        this.showToast(data.message || 'Đã thêm vào danh sách yêu thích!', 'success');
                    } else {
                        this.favoriteIds.delete(idStr);
                        this.favorites = this.favorites.filter(p => String(p.id) !== idStr);
                        this.showToast(data.message || 'Đã xóa khỏi danh sách yêu thích.', 'info');
                    }
                    this.renderList();
                    this.updateCardButtons();
                } else {
                    if (data.require_login) {
                        this.showToast(data.message, 'warning');
                        setTimeout(() => window.location.href = '/login', 1300);
                    } else {
                        this.showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
                    }
                }
            } catch (e) {
                console.error('Lỗi khi cập nhật yêu thích:', e);
                this.showToast('Lỗi kết nối máy chủ.', 'error');
            }
        },

        async remove(postId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            await this.toggle(postId);
        },

        renderList() {
            const listContainer = document.getElementById('header-favorite-list');
            const badge = document.getElementById('header-favorite-badge');
            const countTag = document.getElementById('header-favorite-count-tag');
            const mobileBadge = document.getElementById('mobile-favorite-count');

            const count = this.favorites.length;

            if (badge) {
                badge.innerText = count;
                badge.style.display = count > 0 ? 'inline-flex' : 'none';
            }
            if (countTag) {
                countTag.innerText = count + ' bài';
            }
            if (mobileBadge) {
                mobileBadge.innerText = count;
            }

            if (!listContainer) return;

            if (count === 0) {
                listContainer.innerHTML = `
                    <div class="py-8 px-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-400 flex items-center justify-center mx-auto mb-2.5 shadow-2xs">
                            <i class="fa-regular fa-heart text-xl"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Chưa có bài đăng yêu thích</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] mx-auto leading-relaxed">
                            Nhấn biểu tượng trái tim ở các bài đăng để lưu lại xem nhanh tại đây!
                        </p>
                    </div>
                `;
                return;
            }

            listContainer.innerHTML = this.favorites.map(post => `
                <div class="py-2.5 px-2 hover:bg-slate-50/80 rounded-xl transition-colors flex items-center gap-3 group relative">
                    <a href="/chitietbaidang/${post.id}" class="flex-1 flex items-center gap-3 min-w-0">
                        <img src="${post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=400&q=80'}" 
                             class="w-12 h-12 rounded-xl object-cover shrink-0 shadow-xs border border-slate-200 group-hover:scale-105 transition-transform">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-slate-800 truncate group-hover:text-brand-600 transition-colors">
                                ${post.title}
                            </h5>
                            <p class="text-[11px] font-extrabold text-brand-600 mt-0.5">
                                ${post.price}
                            </p>
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">
                                <i class="fa-solid fa-location-dot text-[9px] mr-1"></i>${post.address || 'Đang cập nhật'}
                            </p>
                        </div>
                    </a>
                    <button type="button" 
                            onclick="window.RentHomeFavorites.remove('${post.id}', event)" 
                            class="p-2 text-slate-300 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors shrink-0" 
                            title="Bỏ thích bài đăng này">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            `).join('');
        },

        updateCardButtons() {
            const buttons = document.querySelectorAll('.fav-toggle-btn');
            buttons.forEach(btn => {
                const id = btn.getAttribute('data-fav-id');
                const ownerId = btn.getAttribute('data-owner-id');
                const isFav = this.has(id);
                const icon = btn.querySelector('i');

                // 1. Tài khoản Doanh nghiệp: ẩn hoàn toàn nút yêu thích
                if (window.currentUserType === 'doanhnghiep') {
                    btn.style.display = 'none';
                    return;
                }

                // 2. Bài đăng của chính tài khoản hiện tại: ẩn hoàn toàn nút yêu thích
                if (window.currentUserId && ownerId && String(ownerId) === String(window.currentUserId)) {
                    btn.style.display = 'none';
                    return;
                }

                btn.style.display = 'flex';

                if (isFav) {
                    btn.className = 'fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white text-rose-500 shadow-md ring-2 ring-rose-100 flex items-center justify-center transition-all duration-200 active:scale-90 group/heart';
                    btn.title = 'Bỏ yêu thích';
                    if (icon) {
                        icon.className = 'fa-solid fa-heart text-base text-rose-500 transition-transform duration-200 group-hover/heart:scale-110';
                    }
                } else {
                    btn.className = 'fav-toggle-btn absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-slate-400 hover:text-rose-500 shadow-sm flex items-center justify-center transition-all duration-200 active:scale-90 group/heart';
                    btn.title = 'Yêu thích bài đăng';
                    if (icon) {
                        icon.className = 'fa-regular fa-heart text-base text-slate-400 group-hover/heart:text-rose-500 transition-transform duration-200 group-hover/heart:scale-110';
                    }
                }
            });
        },

        showToast(message, type = 'info') {
            const oldToast = document.getElementById('renthome-toast');
            if (oldToast) oldToast.remove();

            let icon = 'fa-info-circle';
            let bgClass = 'bg-slate-900 text-white';
            if (type === 'success') {
                icon = 'fa-circle-check text-emerald-400';
            } else if (type === 'warning') {
                icon = 'fa-circle-exclamation text-amber-400';
            } else if (type === 'error') {
                icon = 'fa-circle-xmark text-rose-400';
            }

            const toast = document.createElement('div');
            toast.id = 'renthome-toast';
            toast.className = `fixed top-24 right-4 sm:right-8 z-[99999] flex items-center gap-3 px-4 py-3 rounded-2xl ${bgClass} shadow-2xl border border-white/15 text-xs font-semibold transition-all duration-300 backdrop-blur-md`;
            toast.innerHTML = `<i class="fa-solid ${icon} text-sm"></i><span>${message}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-12px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.RentHomeFavorites.init();
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('home-notif-container')) return;
        
        fetchNotificationsHome();
        setInterval(fetchNotificationsHome, 30000);
        
        document.addEventListener('click', (e) => {
            const dd = document.getElementById('home-notif-dropdown');
            if (dd && !dd.classList.contains('hidden')) {
                const btn = e.target.closest('button[onclick*="toggleHomeNotification"]');
                const menu = e.target.closest('#home-notif-dropdown');
                if (!btn && !menu) {
                    dd.classList.add('hidden');
                }
            }
        });
    });

    function toggleHomeNotification(e) {
        if (e) e.stopPropagation();
        const dd = document.getElementById('home-notif-dropdown');
        if (dd) dd.classList.toggle('hidden');
    }

    async function fetchNotificationsHome() {
        try {
            const response = await fetch('/api/notifications?t=' + new Date().getTime());
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.notifications) {
                    renderHomeNotifications(data.notifications);
                }
            }
        } catch (error) {
            console.error("Lỗi khi fetch thông báo:", error);
        }
    }

    function renderHomeNotifications(listRaw) {
        const list = listRaw.map(n => {
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

        const topbarList = document.getElementById('home-topbar-notif-list');
        const badge = document.getElementById('home-topbar-notif-badge');
        
        if (!topbarList) return;

        const unreadCount = list.filter(n => !n.isRead).length;
        if (badge) {
            badge.innerText = unreadCount;
            badge.style.display = unreadCount > 0 ? 'flex' : 'none';
        }

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
                    <div onclick="window.location.href='/Overview?tab=notifications'" class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3 ${!n.isRead ? 'bg-rose-50/30' : ''}">
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

