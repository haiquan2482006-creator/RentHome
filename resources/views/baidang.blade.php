<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RentHome - Tất cả bài đăng</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN for instant rendering -->
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    @include('partials.header-home')

    <!-- Main Content -->
    <main class="pt-32 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Filter Section -->
        <div class="mb-12 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
            <form id="filter-form" onsubmit="event.preventDefault(); fetchAllPosts();" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search by Keyword/Address -->
                <div class="md:col-span-4 lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400"></i>
                        </div>
                        <input type="text" name="keyword" class="pl-10 w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium text-slate-700" placeholder="Nhập địa chỉ, tên đường...">
                    </div>
                </div>

                <!-- Property Type -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Loại bất động sản</label>
                    <div class="relative">
                        <select name="type" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-3 pr-10 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all text-slate-700 appearance-none font-medium">
                            <option value="">Tất cả loại nhà</option>
                            <option value="nha_tro">Nhà trọ / Phòng trọ</option>
                            <option value="nha_dat">Nhà đất</option>
                            <option value="dat_dai">Đất đai</option>
                            <option value="can_ho">Căn hộ / Chung cư</option>
                            <option value="mat_bang">Mặt bằng kinh doanh</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Khu vực</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                        </div>
                        <input type="text" name="location" class="pl-10 w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium text-slate-700" placeholder="Quận/Huyện, Tỉnh/TP">
                    </div>
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-brand-500/30">
                        <i class="fa-solid fa-filter"></i> Lọc kết quả
                    </button>
                </div>
            </form>
        </div>

        <div id="all-posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
            <!-- Loading Skeleton lúc mới vào trang -->
            @include('components.skeleton-card', ['count' => 8])
        </div>

        <!-- Template Skeleton dùng cho lúc filter tìm kiếm -->
        <template id="skeleton-card-template">
            @include('components.skeleton-card', ['count' => 8])
        </template>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchAllPosts();
        });

        // Tự động làm mới dữ liệu lượt xem và lượt thích thật khi người dùng thoát/quay lại trang danh sách
        window.addEventListener('pageshow', function(event) {
            fetchAllPosts();
        });

        async function fetchAllPosts() {
            const container = document.getElementById('all-posts-container');
            if (!container) return;

            // Get filter values
            const keyword = document.querySelector('input[name="keyword"]')?.value || '';
            const type = document.querySelector('select[name="type"]')?.value || '';
            const location = document.querySelector('input[name="location"]')?.value || '';

            // Build query params
            const params = new URLSearchParams();
            if (keyword) params.append('keyword', keyword);
            if (type) params.append('type', type);
            if (location) params.append('location', location);
            params.append('_t', Date.now());
            
            const queryString = params.toString();
            const url = '/api/posts/approved?' + queryString;

            // Show loading skeleton khi bấm lọc
            const skeletonTpl = document.getElementById('skeleton-card-template');
            if (skeletonTpl) {
                container.innerHTML = skeletonTpl.innerHTML;
            }

            try {
                const response = await fetch(url, { cache: 'no-store' });
                if (response.ok) {
                    const data = await response.json();
                    const posts = data.posts || [];
                    if (!Array.isArray(posts) || posts.length === 0) {
                        container.innerHTML = '<p class="col-span-full text-center text-slate-500 py-10">Hiện chưa có bài đăng nào.</p>';
                        return;
                    }
                    
                    let html = '';
                    posts.forEach(post => {
                        const userName = post.user ? (post.user.ten_hien_thi || post.user.ho_ten || post.user.account_name || post.user.username || 'Người dùng') : 'Người dùng';
                        // Phân loại vai trò tài khoản đăng bài: Admin / Nhân sự / Doanh nghiệp / Cá nhân
                        let userType = 'Cá nhân';
                        let userBadgeClass = 'bg-slate-50 text-slate-500 border border-slate-200';
                        let userIconColor = 'text-slate-500 bg-slate-100';
                        let userIcon = 'fa-user';

                        const isStaffUser = Boolean(
                            (post.account_role && ['nhansu', 'staff', 'sub_account', 'nhan_su', 'nhan su'].includes(String(post.account_role).toLowerCase())) ||
                            (post.staff_id && String(post.staff_id).trim() !== '') ||
                            (post.user && (
                                ['nhansu', 'staff', 'sub_account', 'nhan_su', 'nhan su'].includes(String(post.user.role || '').toLowerCase()) ||
                                Boolean(post.user.parent_id) ||
                                (post.user.staff_role && String(post.user.staff_role).toLowerCase().includes('nhân sự'))
                            ))
                        );

                        const isAdminUser = Boolean(
                            post.user && (post.user.role === 'admin' || post.user.is_admin)
                        );

                        const isEnterpriseUser = Boolean(
                            !isStaffUser && post.user && (post.user.role === 'doanhnghiep' || post.user.role === 'enterprise') && !post.user.parent_id
                        );

                        if (isAdminUser) {
                            userType = 'Ban Quản Trị';
                            userBadgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                            userIconColor = 'text-amber-600 bg-amber-50';
                            userIcon = 'fa-shield-halved';
                        } else if (isStaffUser) {
                            userType = 'Nhân sự';
                            userBadgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                            userIconColor = 'text-emerald-600 bg-emerald-50';
                            userIcon = 'fa-user-tie';
                        } else if (isEnterpriseUser) {
                            userType = 'Doanh nghiệp';
                            userBadgeClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                            userIconColor = 'text-blue-500 bg-blue-50';
                            userIcon = 'fa-building';
                        } else {
                            userType = 'Cá nhân';
                            userBadgeClass = 'bg-slate-50 text-slate-500 border border-slate-200';
                            userIconColor = 'text-slate-500 bg-slate-100';
                            userIcon = 'fa-user';
                        }
                        
                        let imgUrl = post.display_image || 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                        let unitText = 'đ/tháng';
                        if (post.price_unit === '2_nam') unitText = 'đ/2 năm';
                        else if (post.price_unit === '3_nam') unitText = 'đ/3 năm';
                        else if (post.price_unit === '5_nam') unitText = 'đ/5 năm';
                        else if (post.price_unit === 'tren_5_nam') unitText = 'đ/trên 5 năm';
                        else if (post.price_unit === 'nam') unitText = 'đ/năm';
                        else if (post.price_unit === 'm2') unitText = 'đ/m²';
                        else if (post.price_unit === 'tong') unitText = 'VNĐ';
                        const price = new Intl.NumberFormat('vi-VN').format(post.price) + ' ' + unitText;
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
                                    <img src="${imgUrl}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    
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
                                        ${post.building && post.building.name ? `
                                        <div class="flex items-center gap-2 text-brand-600 font-bold truncate">
                                            <i class="fa-solid fa-building w-4 text-center"></i>
                                            <span>${post.building.name}</span>
                                        </div>` : ''}
                                        <div class="flex items-center gap-2 truncate">
                                            <i class="fa-solid fa-location-dot w-4 text-center"></i>
                                            <span>${address}</span>
                                        </div>
                                        <div class="flex items-center gap-3.5 text-xs text-slate-500 font-medium pt-1">
                                            <span class="flex items-center gap-1.5" title="Lượt xem thực tế">
                                                <i class="fa-solid fa-eye text-slate-400"></i> ${Number(post.views || 0).toLocaleString('vi-VN')}
                                            </span>
                                            <span class="flex items-center gap-1.5" title="Lượt yêu thích">
                                                <i class="fa-solid fa-heart text-rose-500"></i> <span data-fav-counter-id="${postId}">${Number(post.favorites_count || post.likes || 0).toLocaleString('vi-VN')}</span>
                                            </span>
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
                } else {
                    container.innerHTML = '<p class="col-span-full text-center text-rose-500 py-10">Lỗi khi tải dữ liệu bài đăng!</p>';
                }
            } catch (error) {
                console.error('Error fetching real posts:', error);
            }
        }
    </script>
</body>
</html>
