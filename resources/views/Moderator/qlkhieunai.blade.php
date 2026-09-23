<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xử Lý Khiếu Nại - RentHome Moderator</title>
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
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent-red: #dc2626;
            --sidebar-width: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-family); background-color: var(--bg-main); color: var(--text-primary); }
        body.sidebar-collapsed {
            --sidebar-width: 0px;
        }
        body.sidebar-collapsed .mod-sidebar {
            transform: translateX(-100%);
        }
        .moderator-container { margin-left: var(--sidebar-width); padding: 32px 40px; min-height: 100vh; transition: margin-left 0.3s ease; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 1.75rem; font-weight: 800; }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        @if (session('success'))
            <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded-xl mb-6 font-semibold flex items-center gap-2 border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="page-header">
            <div class="flex items-center gap-3">
                <button id="sidebarToggleBtn" class="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-[#0284c7] hover:border-[#0284c7] shadow-sm flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-[#0284c7]/30 shrink-0">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <h1><i class="fa-solid fa-comments-question-check" style="color: var(--brand-primary); margin-right: 8px;"></i> Xử Lý Khiếu Nại & Báo Cáo Vi Phạm</h1>
            </div>
            <p style="color: var(--text-secondary); margin-top: 8px;">Tiếp nhận, xác minh và phản hồi giải quyết các báo cáo vi phạm từ phía thành viên</p>
        </div>

        @if(isset($complaints) && count($complaints) > 0)
            @foreach($complaints as $complaint)
                @php
                    $cid = $complaint->_id ?? $complaint->id;
                    $cStatus = $complaint->status;
                    $cUser = $complaint->user->account_name ?? $complaint->user->username ?? 'Người dùng ẩn danh';
                    $cDate = isset($complaint->created_at) ? \Carbon\Carbon::parse($complaint->created_at)->format('H:i d/m/Y') : 'Không rõ';
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                        
                        <!-- Cột Trái: Nội dung khiếu nại (User) -->
                        <div class="md:col-span-7 p-6 border-b md:border-b-0 md:border-r border-slate-100">
                            <div class="flex flex-wrap items-start justify-between mb-4 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $cUser }} <span class="text-slate-400 font-normal ml-1">#KN-{{ substr($cid, -4) }}</span></h4>
                                        <p class="text-xs text-slate-500 mt-0.5"><i class="fa-regular fa-clock"></i> {{ $cDate }}</p>
                                    </div>
                                </div>
                                @if($cStatus === 'resolved' || $cStatus === 'replied')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200 flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-check-circle"></i> Đã phản hồi</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-200 flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-clock animate-spin"></i> Đang chờ xử lý</span>
                                @endif
                            </div>
                            
                            <div class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="font-bold text-slate-900 mb-1 text-xs uppercase tracking-wide">Nội dung khiếu nại</p>
                                {{ $complaint->content }}
                            </div>
                            
                            @if($complaint->image)
                                <div class="mt-4">
                                    <p class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Hình ảnh bằng chứng đính kèm</p>
                                    <img src="{{ $complaint->image }}" alt="Bằng chứng" class="w-32 h-32 object-cover rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:opacity-90 transition-opacity">
                                </div>
                            @endif
                        </div>

                        <!-- Cột Phải: Thông tin bài đăng (System) -->
                        <div class="md:col-span-5 p-6 bg-slate-50/50 flex flex-col">
                            <h4 class="text-xs font-bold text-slate-400 mb-3 uppercase tracking-wide">Thông tin bài đăng bị Report</h4>
                            @if($complaint->post)
                                @php
                                    $post = $complaint->post;
                                    $postImage = 'https://placehold.co/150?text=No+Image';
                                    if ($post->images && count($post->images) > 0) {
                                        $rawImg = $post->images[0];
                                        if (str_starts_with($rawImg, 'http') || str_starts_with($rawImg, 'data:image')) {
                                            $postImage = $rawImg;
                                        } else {
                                            $postImage = str_starts_with($rawImg, '/') ? $rawImg : '/storage/' . $rawImg;
                                        }
                                    }
                                    $postPrice = number_format($post->price ?? 0, 0, ',', '.') . ' đ';
                                @endphp
                                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex flex-col gap-3 flex-1">
                                    <div class="flex gap-3">
                                        <img src="{{ $postImage }}" alt="Thumbnail" class="w-20 h-20 object-cover rounded-lg border border-slate-100 shrink-0" onerror="this.src='https://placehold.co/150?text=Lỗi+Ảnh'">
                                        <div class="overflow-hidden">
                                            <h5 class="font-bold text-slate-900 text-sm line-clamp-2 leading-snug" title="{{ $post->title }}">{{ $post->title ?? 'Chưa có tiêu đề' }}</h5>
                                            <p class="text-[#0284c7] font-bold text-sm mt-1.5">{{ $postPrice }}</p>
                                            <p class="text-[11px] text-slate-500 mt-1 font-medium bg-slate-100 inline-block px-2 py-0.5 rounded">ID: #{{ $post->id ?? $post->_id }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-auto pt-2">
                                        <button onclick="openInvestigationModal('{{ $cid }}')" class="w-full py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold rounded-lg text-center transition-colors flex items-center justify-center gap-2 shadow-sm">
                                            Mở hồ sơ điều tra <i class="fa-solid fa-folder-open"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl flex items-start gap-3 text-rose-600">
                                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                                    <p class="text-sm font-semibold">Bài đăng này đã bị xóa khỏi hệ thống.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Bar: Hành động & Phản hồi -->
                    <div class="p-5 border-t border-slate-200 bg-white">
                        @if($cStatus === 'resolved' || $cStatus === 'replied')
                            <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl flex gap-3 text-indigo-700">
                                <i class="fa-solid fa-reply mt-0.5 text-indigo-500"></i>
                                <div>
                                    <p class="text-xs font-bold text-indigo-400 uppercase mb-1">Quản trị viên đã phản hồi</p>
                                    <p class="text-sm font-medium">{{ $complaint->admin_response }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <button type="button" class="border-2 border-rose-500 text-rose-600 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-rose-50 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-lock"></i> Khóa / Gỡ bài vi phạm
                                </button>
                                <button type="button" onclick="document.getElementById('reply-form-{{ $cid }}').classList.toggle('hidden')" class="bg-[#0284c7] text-white px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-[#0369a1] transition-colors flex items-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-paper-plane"></i> Phản hồi người dùng
                                </button>
                            </div>
                            
                            <!-- Form ẩn -->
                            <div id="reply-form-{{ $cid }}" class="hidden mt-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <form action="{{ url('/moderator/respond-complaint/' . $cid) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                                    @csrf
                                    <input type="text" name="response_content" placeholder="Nhập câu trả lời phản hồi cho khiếu nại này..." class="flex-1 px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:border-[#0284c7] focus:ring-1 focus:ring-[#0284c7] text-sm" required>
                                    <button type="submit" class="bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-emerald-600 transition-colors shrink-0 shadow-sm flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check"></i> Gửi phản hồi
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                <i class="fa-solid fa-clipboard-check" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; display:block;"></i>
                <p style="font-size: 1.25rem; font-weight: 700; color: #334155;">Hiện không có khiếu nại hay phản hồi nào cần xử lý!</p>
            </div>
        @endif
    </main>

    <!-- Biến toàn cục JS -->
    <script>
        window.complaintsData = @json($complaints ?? []);
        let currentInvestigationId = null;
    </script>

    <!-- Modal Hồ Sơ Điều Tra -->
    <div id="investigationModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4 md:p-6">
        <div class="bg-white w-full max-w-[1400px] h-[95vh] rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-hidden transform transition-all relative">
            <!-- Nút đóng mobile -->
            <button onclick="closeInvestigationModal()" class="md:hidden absolute top-4 right-4 z-50 bg-white/80 backdrop-blur rounded-full w-8 h-8 flex items-center justify-center text-slate-500 hover:text-slate-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <!-- Cột trái: Phục dựng hiện trường bài đăng (70%) -->
            <div class="w-full md:w-[70%] h-full overflow-y-auto border-r border-slate-200 bg-white p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-black text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-location text-[#0284c7]"></i> Hồ Sơ Bài Đăng Bị Báo Cáo
                    </h2>
                </div>

                <!-- Hình ảnh Cover -->
                <div class="relative rounded-2xl overflow-hidden bg-slate-100 aspect-video mb-4">
                    <img id="inv-cover" src="" alt="Cover" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/800x400?text=Lỗi+Hiển+Thị'">
                </div>
                
                <!-- Grid Thumbnails -->
                <div id="inv-thumbnails" class="grid grid-cols-4 gap-3 mb-6">
                    <!-- Javascript will populate this -->
                </div>

                <!-- Nội dung -->
                <h1 id="inv-title" class="text-2xl font-bold text-slate-900 mb-4 leading-tight"></h1>
                
                <div class="flex flex-wrap items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div class="bg-sky-50 text-[#0284c7] px-4 py-2 rounded-xl font-bold text-lg" id="inv-price"></div>
                    <div class="text-slate-600 font-medium text-sm flex items-center gap-2" id="inv-area"></div>
                </div>

                <div class="mb-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Địa chỉ</h3>
                    <p id="inv-address" class="text-slate-700 font-medium bg-slate-50 p-4 rounded-xl border border-slate-100"></p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Mô tả chi tiết</h3>
                    <div id="inv-description" class="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap bg-slate-50 p-4 rounded-xl border border-slate-100"></div>
                </div>
            </div>

            <!-- Cột phải: Hồ sơ vụ việc & Hành động (30% Sticky) -->
            <div class="w-full md:w-[30%] h-full bg-slate-50 flex flex-col relative">
                <!-- Header Cột Phải -->
                <div class="p-6 border-b border-slate-200 bg-white flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2"><i class="fa-solid fa-scale-balanced text-indigo-500"></i> Hồ sơ vụ việc</h3>
                    <button onclick="closeInvestigationModal()" class="text-slate-400 hover:text-slate-700 hidden md:block transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <!-- Nội dung cuộn -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Khối 1: Khách hàng nói gì -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Lời khiếu nại (Từ <span id="inv-user-name" class="text-indigo-500">User</span>)</h4>
                        <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-xl shadow-sm">
                            <p id="inv-complaint-content" class="text-sm text-slate-700 font-medium mb-3 leading-relaxed"></p>
                            <div id="inv-complaint-image-container" class="hidden">
                                <p class="text-xs text-indigo-400 font-bold mb-2">Ảnh bằng chứng:</p>
                                <img id="inv-complaint-image" src="" alt="Evidence" class="w-full rounded-lg border border-indigo-200">
                            </div>
                        </div>
                    </div>

                    <!-- Khối 2: Lịch sử hệ thống -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Lịch sử xử phạt</h4>
                        <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl flex gap-3 text-rose-700 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-bold mb-1">Lý do khóa / từ chối:</p>
                                <p id="inv-history-reason">Bài đăng vi phạm quy định nền tảng. Chứa thông tin không xác thực hoặc bị người dùng báo cáo vi phạm.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Khối 3: Phán quyết (Chỉ hiện nếu chưa xử lý) -->
                    <div id="inv-action-block">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Phán quyết của Moderator</h4>
                        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                            <textarea id="inv-response-text" rows="3" placeholder="Nhập lý do phản hồi cho user..." class="w-full p-3 rounded-lg border border-slate-300 focus:outline-none focus:border-[#0284c7] focus:ring-1 focus:ring-[#0284c7] text-sm mb-4" required></textarea>
                            
                            <div class="flex flex-col gap-2">
                                <button onclick="submitInvestigation('accept')" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors">
                                    <i class="fa-solid fa-unlock"></i> Chấp nhận - Khôi phục bài
                                </button>
                                <button onclick="submitInvestigation('reject')" class="w-full border-2 border-rose-500 text-rose-600 hover:bg-rose-50 font-bold py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 transition-colors">
                                    <i class="fa-solid fa-lock"></i> Bác bỏ - Giữ nguyên phạt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Xử lý Modal -->
    <script>
        function openInvestigationModal(complaintId) {
            currentInvestigationId = complaintId;
            const complaint = window.complaintsData.find(c => (c._id && c._id === complaintId) || (c.id && c.id == complaintId));
            
            if (!complaint || !complaint.post) return;
            const post = complaint.post;

            // Render Hình ảnh bài đăng
            const coverImg = document.getElementById('inv-cover');
            const thumbsContainer = document.getElementById('inv-thumbnails');
            
            if (post.images && post.images.length > 0) {
                const getFullUrl = (url) => (url.startsWith('http') || url.startsWith('data:image')) ? url : (url.startsWith('/') ? url : '/storage/' + url);
                coverImg.src = getFullUrl(post.images[0]);
                
                let thumbsHtml = '';
                post.images.forEach((img) => {
                    const imgUrl = getFullUrl(img);
                    thumbsHtml += `<div class="aspect-video bg-slate-100 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-indigo-500 transition-colors" onclick="document.getElementById('inv-cover').src='${imgUrl}'"><img src="${imgUrl}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/800x400?text=Lỗi+Hiển+Thị'"></div>`;
                });
                thumbsContainer.innerHTML = thumbsHtml;
            } else {
                coverImg.src = 'https://placehold.co/800x400?text=No+Image';
                thumbsContainer.innerHTML = '';
            }

            // Text Bài đăng
            document.getElementById('inv-title').innerText = post.title || 'Chưa có tiêu đề';
            document.getElementById('inv-price').innerText = new Intl.NumberFormat('vi-VN').format(post.price || 0) + ' đ' + (post.price_unit === 'thang' ? '/tháng' : '');
            document.getElementById('inv-area').innerHTML = `<i class="fa-solid fa-ruler-combined"></i> ${post.area || 0} m²`;
            
            let addr = [post.address, post.ward, post.district, post.province].filter(Boolean).join(', ');
            document.getElementById('inv-address').innerText = addr || 'Chưa xác định';
            
            document.getElementById('inv-description').innerText = post.description || 'Không có mô tả';

            // Text Khiếu nại (Cột phải)
            const userName = complaint.user ? (complaint.user.account_name || complaint.user.username) : 'User';
            document.getElementById('inv-user-name').innerText = userName;
            document.getElementById('inv-complaint-content').innerText = complaint.content || '';
            
            const imgContainer = document.getElementById('inv-complaint-image-container');
            const imgElem = document.getElementById('inv-complaint-image');
            if (complaint.image) {
                imgElem.src = complaint.image;
                imgContainer.classList.remove('hidden');
            } else {
                imgContainer.classList.add('hidden');
            }

            // Logic trạng thái
            const actionBlock = document.getElementById('inv-action-block');
            if (complaint.status === 'resolved' || complaint.status === 'replied') {
                actionBlock.style.display = 'none';
            } else {
                actionBlock.style.display = 'block';
                document.getElementById('inv-response-text').value = '';
            }

            // Mở Modal
            document.getElementById('investigationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Ngăn cuộn trang
        }

        function closeInvestigationModal() {
            document.getElementById('investigationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentInvestigationId = null;
        }

        function submitInvestigation(action) {
            if (!currentInvestigationId) return;
            
            const responseText = document.getElementById('inv-response-text').value;
            if (!responseText.trim()) {
                alert('Vui lòng nhập lý do/phán quyết của bạn!');
                return;
            }
            
            if (!confirm(`Bạn có chắc chắn muốn ${action === 'accept' ? 'KHÔI PHỤC' : 'BÁC BỎ'} bài đăng này?`)) return;

            const csrfToken = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';

            fetch(`/moderator/respond-complaint/${currentInvestigationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    response_content: responseText,
                    action: action
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message || 'Xử lý thành công!');
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xử lý'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ!');
            });
        }

        // Toggle Sidebar
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    </script>
</body>
</html>
