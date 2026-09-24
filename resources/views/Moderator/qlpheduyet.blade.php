<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Đăng Phê Duyệt - RentHome Moderator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --bg-main: #f4f6fa;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --brand-primary: #0284c7;
            --brand-hover: #0369a1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-emerald: #059669;
            --accent-red: #dc2626;
            --sidebar-width: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-family); background-color: var(--bg-main); color: var(--text-primary); }
        .moderator-container { margin-left: var(--sidebar-width); padding: 32px 40px; min-height: 100vh; }
        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 1.75rem; font-weight: 800; }
        .items-grid { display: grid; gap: 20px; }
        .item-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px 24px; display: grid; grid-template-columns: 100px 1fr auto; gap: 24px; align-items: center; }
        .item-thumb { width: 100px; height: 80px; border-radius: 10px; object-fit: cover; }
        .btn-action { padding: 10px 18px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
        .btn-approve { background-color: var(--accent-emerald); color: #fff; }
        .btn-approve:hover { background-color: #047857; }
        .btn-reject { background-color: #fff; border: 1px solid var(--accent-red); color: var(--accent-red); }
        .btn-reject:hover { background-color: var(--accent-red); color: #fff; }

        /* Modal Overlay & Styling */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: #fff;
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--text-primary);
        }

        .form-textarea {
            width: 100%;
            height: 110px;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-family: inherit;
            font-size: 0.9rem;
            margin-bottom: 20px;
            outline: none;
            resize: vertical;
        }

        .form-textarea:focus { border-color: var(--brand-primary); }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: var(--text-secondary);
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        /* Modal Preview Cụ Thể */
        .preview-modal-card {
            background: #fff;
            width: 90%;
            max-width: 1200px;
            height: 90vh;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.2s ease-out;
            display: flex;
            overflow: hidden;
        }

        .preview-col-left {
            width: 70%;
            overflow-y: auto;
            padding: 32px;
            border-right: 1px solid var(--border-color);
        }

        .preview-col-right {
            width: 30%;
            padding: 32px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Customize scrollbar */
        .preview-col-left::-webkit-scrollbar { width: 8px; }
        .preview-col-left::-webkit-scrollbar-track { background: #f1f1f1; }
        .preview-col-left::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .preview-col-left::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @media (max-width: 992px) {
            .moderator-container { margin-left: 0; padding: 20px; }
            .item-card { grid-template-columns: 1fr; }
            .preview-modal-card { flex-direction: column; overflow-y: auto; height: 95vh; }
            .preview-col-left, .preview-col-right { width: 100%; border-right: none; }
            .preview-col-right { border-top: 1px solid var(--border-color); }
        }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        @if (session('success'))
            <div style="background:#dcfce7; color:#15803d; padding:14px 20px; border-radius:12px; margin-bottom:20px; font-weight:600; display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <div class="page-header">
            <h1><i class="fa-solid fa-file-signature" style="color: var(--brand-primary); margin-right: 8px;"></i> Quản Lý Bài Đăng Phê Duyệt</h1>
            <p style="color: var(--text-secondary);">Kiểm duyệt và thẩm định các tin đăng cho thuê phòng / nhà mới gửi lên hệ thống</p>
        </div>

        <div class="items-grid" id="pending-posts-container">
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Không có bài đăng nào cần phê duyệt</p>
            </div>
        </div>
    </main>

    <!-- Modal Từ Chối Bài Đăng -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-card">
            <h2 class="modal-title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-red); margin-right: 8px;"></i> Từ Chối Bài Đăng</h2>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 14px;" id="rejectItemTitle"></p>

            <form id="rejectForm" method="POST">
                @csrf
                <label style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">Lý do từ chối (sẽ gửi thông báo tới người đăng):</label>
                <textarea name="reason" class="form-textarea" placeholder="Nhập lý do chi tiết (ví dụ: Ảnh bị mờ, địa chỉ sai thực tế, thông tin giá chưa rõ ràng...)" required></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('rejectModal')">Hủy bỏ</button>
                    <button type="submit" class="btn-action btn-reject" style="background: var(--accent-red); color: #fff;"><i class="fa-solid fa-paper-plane"></i> Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Xem Trước Bài Đăng -->
    <div class="modal-overlay" id="detailPreviewModal">
        <div class="preview-modal-card">
            <!-- Cột trái: Chi tiết -->
            <div class="preview-col-left">
                <!-- Nút đóng mobile -->
                <button onclick="closeModal('detailPreviewModal')" class="md:hidden absolute top-4 right-4 text-slate-500 hover:text-slate-800 z-10">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
                
                <!-- Hình ảnh -->
                <div class="mb-6">
                    <div class="w-full h-[400px] bg-slate-100 rounded-2xl mb-3 overflow-hidden relative">
                        <img id="preview-cover-img" src="" alt="Cover" class="w-full h-full object-cover transition-all duration-300">
                    </div>
                    <div id="preview-thumbnails" class="grid grid-cols-5 gap-3">
                        <!-- Thumbnails will be rendered here -->
                    </div>
                </div>

                <!-- Thông tin chính -->
                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <div id="preview-building-badge"></div>
                        <div id="preview-priority-badge"></div>
                    </div>
                    <h2 id="preview-title" class="text-2xl font-extrabold text-slate-900 mb-4 leading-snug"></h2>
                    <div class="flex flex-wrap items-center gap-6 mb-4">
                        <div class="text-3xl font-extrabold" style="color: var(--brand-primary);" id="preview-price"></div>
                        <div class="text-xl font-bold text-slate-700 border-l-2 border-slate-200 pl-6" id="preview-area"></div>
                    </div>
                    <div class="flex items-start gap-2 text-slate-600 mb-4">
                        <i class="fa-solid fa-location-dot mt-1 text-rose-500"></i>
                        <span id="preview-address" class="font-medium text-[15px]"></span>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-semibold text-slate-700" id="preview-room-container" style="display: none;">
                            <i class="fa-solid fa-door-open text-slate-400"></i> Phòng: <span id="preview-room-number"></span>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-semibold text-slate-700" id="preview-bedrooms-container">
                            <i class="fa-solid fa-bed text-slate-400"></i> <span id="preview-bedrooms"></span> PN
                        </div>
                        <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg text-sm font-semibold text-slate-700" id="preview-bathrooms-container">
                            <i class="fa-solid fa-bath text-slate-400"></i> <span id="preview-bathrooms"></span> PT
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200 my-6">

                <!-- Tiện ích -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Tiện ích</h3>
                    <div id="preview-amenities" class="flex flex-wrap gap-2">
                        <!-- Amenities here -->
                    </div>
                </div>

                <!-- Mô tả -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Mô tả chi tiết</h3>
                    <div id="preview-description" class="text-slate-600 leading-relaxed whitespace-pre-line text-[15px]">
                    </div>
                </div>
            </div>

            <!-- Cột phải: Nghiệp vụ -->
            <div class="preview-col-right relative">
                <button onclick="closeModal('detailPreviewModal')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Thông tin người đăng</h3>
                    <div class="flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl shrink-0" style="background: #e0f2fe; color: var(--brand-primary);">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div id="preview-author-name" class="font-bold text-slate-900 text-base truncate"></div>
                            <div class="text-xs text-slate-500 mt-0.5">ID: <span id="preview-author-id"></span></div>
                            <div id="preview-author-type" class="mt-1 inline-block px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[11px] font-bold rounded">Cá nhân</div>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-slate-500 flex items-center gap-2">
                        <i class="fa-regular fa-calendar"></i> Gửi lúc: <span id="preview-created-at" class="font-medium text-slate-700"></span>
                    </div>
                </div>

                <hr class="border-slate-200">

                <div class="flex-1 flex flex-col justify-end">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Hành động kiểm duyệt</h3>
                    
                    <div id="preview-building-alert" class="mb-4 bg-amber-50 border border-amber-200 text-amber-700 p-3 rounded-xl text-sm font-medium leading-snug" style="display: none;">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lưu ý: Tin đăng thuộc Cụm tòa nhà. Vui lòng đối chiếu tính đồng nhất của hình ảnh và địa chỉ tòa nhà gốc trước khi duyệt.
                    </div>

                    <button id="preview-btn-approve" class="w-full text-white py-3.5 rounded-xl font-bold text-base flex items-center justify-center gap-2 transition-colors shadow-sm mb-6" style="background: var(--accent-emerald); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1), 0 2px 4px -1px rgba(16, 185, 129, 0.06);">
                        <i class="fa-solid fa-check-circle text-xl"></i> Phê duyệt bài đăng
                    </button>

                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <label class="text-sm font-bold text-slate-700 block mb-2">Từ chối bài đăng</label>
                        <textarea id="preview-reject-reason" class="w-full h-24 p-3 rounded-lg border border-slate-300 text-sm focus:outline-none mb-3 resize-none" placeholder="Nhập lý do từ chối..." style="focus:border-color: var(--brand-primary);"></textarea>
                        <button id="preview-btn-reject" class="w-full bg-white border-2 text-rose-500 py-2.5 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-rose-50 transition-colors" style="border-color: var(--accent-red); color: var(--accent-red);">
                            <i class="fa-solid fa-ban"></i> Từ chối
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>
        function fetchPendingPosts() {
    fetch('/api/moderator/posts/pending')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('pending-posts-container');
            const sidebarBadge = document.getElementById('mod-sidebar-pending-count');
            
            const pendingCount = data.posts ? data.posts.length : 0;
            if (sidebarBadge) {
                if (pendingCount > 0) {
                    sidebarBadge.innerText = pendingCount;
                    sidebarBadge.style.display = 'inline-block';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }

            if (data.posts && data.posts.length > 0) {
                window.pendingPostsData = data.posts;
                let html = '';
                data.posts.forEach(post => {
                    let imgUrl = post.display_image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=400&q=80'

                    // 2. FIX LỖI UNDEFINED ID: Bắt cả 'id' (Laravel) và '_id' (MongoDB)
                    const postId = post.id || post._id || 'N/A';

                    const userName = post.user ? (post.user.account_name || post.user.username) : 'Người dùng';
                    const price = new Intl.NumberFormat('vi-VN').format(post.price || 0);
                    
                    html += `
                    <div class="flex flex-col md:flex-row bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden gap-4 p-4 md:p-5 cursor-pointer" id="post-${postId}" onclick="openDetailPreview('${postId}')">
                        
                        <!-- Khối Hình Ảnh -->
                        <div class="w-full md:w-48 h-32 shrink-0 relative group bg-slate-100">
                            <img src="${imgUrl}" onerror="this.src='https://placehold.co/400x300?text=No+Image'" alt="Thumbnail" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-2 left-2 px-2 py-1 bg-amber-500 text-white text-[10px] font-bold uppercase rounded-lg shadow-sm">
                                Chờ duyệt
                            </span>
                        </div>

                        <!-- Khối Nội Dung -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 line-clamp-2 mb-2">
                                    ${post.title || 'Chưa có tiêu đề'}
                                </h3>
                                <div class="text-xs text-slate-500 flex flex-wrap items-center gap-3 mb-3">
                                    <span class="flex items-center gap-1.5 font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                        <i class="fa-solid fa-hashtag text-slate-400"></i> ${postId}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-slate-400"></i> ${new Date(post.created_at).toLocaleString('vi-VN')}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-user text-slate-400"></i> ${userName}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-brand-600 font-extrabold text-base">
                                    <i class="fa-solid fa-tags text-sm mr-1"></i> ${price} đ
                                </span>
                                <span class="text-slate-700 font-medium text-sm border-l border-slate-300 pl-4">
                                    <i class="fa-solid fa-ruler-combined text-slate-400 mr-1"></i> ${post.area || 0} m²
                                </span>
                            </div>
                        </div>

                        <!-- Khối Hành Động -->
                        <div class="flex flex-row md:flex-col justify-end gap-2 shrink-0 md:border-l md:border-slate-100 md:pl-4 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100 mt-2 md:mt-0">
                            <button onclick="event.stopPropagation(); approvePost('${postId}')" class="bg-emerald-500 text-white px-4 py-2 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-emerald-600 transition-colors w-full md:w-auto">
                                <i class="fa-solid fa-check"></i> Phê duyệt
                            </button>
                            <button onclick="event.stopPropagation(); openRejectModal('${postId}', '${(post.title || '').replace(/'/g, "\\'")}')" class="bg-white border border-rose-500 text-rose-500 px-4 py-2 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-rose-50 transition-colors w-full md:w-auto">
                                <i class="fa-solid fa-xmark"></i> Từ chối
                            </button>
                        </div>
                    </div>
                    `;
                });
                container.innerHTML = html;
                
                // Tự động mở modal nếu có query param preview_id (từ trang Tổng quan)
                const urlParams = new URLSearchParams(window.location.search);
                const previewId = urlParams.get('preview_id');
                if (previewId && document.getElementById('post-' + previewId)) {
                    openDetailPreview(previewId);
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            } else {
                container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <p style="font-size: 1.1rem; font-weight: 600; color: #475569;">Không có bài đăng nào cần phê duyệt</p>
                </div>`;
            }
        })
        .catch(err => console.error(err));
}

        function approvePost(id) {
            if (!confirm('Bạn có chắc chắn muốn duyệt bài này?')) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            
            fetch(`/api/moderator/posts/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Cập nhật lại danh sách để tự động xử lý huy hiệu đếm số và màn hình trống
                    fetchPendingPosts();
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            });
        }

        function getFullImageUrl(rawImg) {
            if (!rawImg) return 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=400&q=80';
            if (rawImg.startsWith('http') || rawImg.startsWith('data:image')) return rawImg;
            return rawImg.startsWith('/') ? rawImg : '/storage/' + rawImg;
        }

        function openDetailPreview(postId) {
            if (!window.pendingPostsData) return;
            
            const post = window.pendingPostsData.find(p => (p.id || p._id) == postId);
            if (!post) return;

            // 1. Hình ảnh
            const coverImg = document.getElementById('preview-cover-img');
            const thumbnailsContainer = document.getElementById('preview-thumbnails');
            
            if (post.display_image) {
                coverImg.src = post.display_image;
                thumbnailsContainer.innerHTML = ''; // Tạm ẩn thumbnail để tránh lỗi load MongoDB ID
            } else {
                coverImg.src = 'https://placehold.co/800x400?text=No+Image';
                thumbnailsContainer.innerHTML = '';
            }

            // 2. Nội dung chính

            // Nhãn Tòa nhà / Đăng lẻ
            const buildingBadgeContainer = document.getElementById('preview-building-badge');
            const buildingAlert = document.getElementById('preview-building-alert');
            
            // Kiểm tra nghiêm ngặt building_id
            const hasBuilding = post.building_id && post.building_id !== '' && post.building_id !== 'null';

            if (hasBuilding) {
                // Nếu API có trả về object post.building thì lấy tên, nếu không thì hiện ID
                const buildingName = (post.building && post.building.name) ? post.building.name : 'ID Tòa nhà #' + post.building_id;
                buildingBadgeContainer.innerHTML = `<span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-building"></i> Thuộc Tòa Nhà / Dự Án</span> <span class="text-sm text-slate-500 ml-1 font-medium italic">Trực thuộc: ${buildingName}</span>`;
                buildingAlert.style.display = 'block';
            } else {
                buildingBadgeContainer.innerHTML = `<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fa-solid fa-house"></i> Nhà Đăng Lẻ</span>`;
                buildingAlert.style.display = 'none';
            }

            // Mức độ ưu tiên
            const priorityBadgeContainer = document.getElementById('preview-priority-badge');
            if (post.priority_level && post.priority_level > 0) {
                 priorityBadgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full border border-amber-200">
                    <i class="fa-solid fa-star"></i> Tin ưu tiên (Mức ${post.priority_level})
                </span>`;
            } else {
                 priorityBadgeContainer.innerHTML = '';
            }

            document.getElementById('preview-title').innerText = post.title || 'Chưa có tiêu đề';
            document.getElementById('preview-price').innerText = new Intl.NumberFormat('vi-VN').format(post.price || 0) + ' đ';
            document.getElementById('preview-area').innerText = (post.area || 0) + ' m²';
            
            // Địa chỉ
            const addressParts = [];
            if (post.street) addressParts.push(post.street);
            if (post.ward) addressParts.push(post.ward);
            if (post.district) addressParts.push(post.district);
            if (post.province) addressParts.push(post.province);
            document.getElementById('preview-address').innerText = addressParts.length > 0 ? addressParts.join(', ') : 'Chưa cập nhật địa chỉ';

            // Thông số
            if (post.room_number) {
                document.getElementById('preview-room-number').innerText = post.room_number;
                document.getElementById('preview-room-container').style.display = 'flex';
            } else {
                document.getElementById('preview-room-container').style.display = 'none';
            }
            
            if (post.bedrooms) {
                document.getElementById('preview-bedrooms').innerText = post.bedrooms;
                document.getElementById('preview-bedrooms-container').style.display = 'flex';
            } else {
                document.getElementById('preview-bedrooms-container').style.display = 'none';
            }
            if (post.bathrooms) {
                document.getElementById('preview-bathrooms').innerText = post.bathrooms;
                document.getElementById('preview-bathrooms-container').style.display = 'flex';
            } else {
                document.getElementById('preview-bathrooms-container').style.display = 'none';
            }

            // Tiện ích
            const amenitiesContainer = document.getElementById('preview-amenities');
            if (post.amenities && post.amenities.length > 0) {
                let amHtml = '';
                post.amenities.forEach(am => {
                    amHtml += `<span class="px-3 py-1 bg-sky-50 text-sky-700 text-sm font-semibold rounded-full border border-sky-100">${am}</span>`;
                });
                amenitiesContainer.innerHTML = amHtml;
            } else {
                amenitiesContainer.innerHTML = '<span class="text-sm text-slate-500 italic">Không có tiện ích nổi bật</span>';
            }

            // Mô tả
            document.getElementById('preview-description').innerText = post.description || 'Chưa có mô tả chi tiết';

            // 3. Nghiệp vụ (Cột phải)
            const userName = post.user ? (post.user.account_name || post.user.username || 'Người dùng') : 'Người dùng';
            const userId = post.user ? (post.user.id || post.user._id || 'N/A') : 'N/A';
            const userType = post.user && post.user.role === 'business' ? 'Doanh nghiệp' : 'Cá nhân';
            
            document.getElementById('preview-author-name').innerText = userName;
            document.getElementById('preview-author-id').innerText = userId;
            document.getElementById('preview-author-type').innerText = userType;
            document.getElementById('preview-created-at').innerText = new Date(post.created_at).toLocaleString('vi-VN');

            // Nút bấm
            document.getElementById('preview-btn-approve').onclick = function() {
                closeModal('detailPreviewModal');
                approvePost(postId);
            };

            const rejectReasonInput = document.getElementById('preview-reject-reason');
            rejectReasonInput.value = ''; // Reset
            
            document.getElementById('preview-btn-reject').onclick = function() {
                const reason = rejectReasonInput.value.trim();
                if (!reason) {
                    alert('Vui lòng nhập lý do từ chối');
                    rejectReasonInput.focus();
                    return;
                }
                
                if(!confirm('Xác nhận từ chối bài đăng này?')) return;
                
                fetch('/api/moderator/posts/' + postId + '/reject', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(res => res.json())
                .then(resData => {
                    alert(resData.message || 'Đã từ chối!');
                    closeModal('detailPreviewModal');
                    fetchPendingPosts();
                })
                .catch(err => {
                    alert('Có lỗi xảy ra');
                    console.error(err);
                });
            };

            // Mở modal
            document.getElementById('detailPreviewModal').style.display = 'flex';
        }

        document.addEventListener("DOMContentLoaded", function() {
            fetchPendingPosts();
        });

        function openRejectModal(id, title) {
            document.getElementById('rejectItemTitle').innerText = 'Bài đăng: "' + title + '" (ID: #' + id + ')';
            document.getElementById('rejectForm').action = '/api/moderator/posts/' + id + '/reject';
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const actionUrl = this.action;
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch(actionUrl, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(resData => {
                alert(resData.message || 'Đã từ chối!');
                closeModal('rejectModal');
                fetchPendingPosts();
            })
            .catch(err => {
                alert('Có lỗi xảy ra');
                console.error(err);
            });
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>
