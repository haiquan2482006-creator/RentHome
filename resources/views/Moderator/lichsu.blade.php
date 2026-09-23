<!DOCTYPE html>
<html lang="vi">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Kiểm Duyệt - RentHome Moderator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --bg-main: #f4f6fa;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --brand-primary: #0284c7;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --sidebar-width: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-family); background-color: var(--bg-main); color: var(--text-primary); }
        .moderator-container { margin-left: var(--sidebar-width); padding: 32px 40px; min-height: 100vh; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 1.75rem; font-weight: 800; }
        .history-list { background: #fff; border: 1px solid var(--border-color); border-radius: 14px; padding: 20px; }
        .history-item { padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .history-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        <div class="page-header">
            <h1><i class="fa-solid fa-clock-rotate-left" style="color: var(--brand-primary); margin-right: 8px;"></i> Lịch Sử Kiểm Duyệt</h1>
            <p style="color: var(--text-secondary);">Nhật ký các thao tác phê duyệt bài đăng và xử lý khiếu nại đã thực hiện</p>
        </div>

        <div class="history-list" id="moderator-history-container">
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                <p style="font-size: 1rem; font-weight: 600; color: #475569;">Không có dữ liệu lịch sử thao tác</p>
            </div>
        </div>
    </main>

    
    <script>
        function fetchHistoryPosts() {
            fetch('/api/moderator/posts/history')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('moderator-history-container');
                    if (data.posts && data.posts.length > 0) {
                        let html = '';
                        data.posts.forEach(post => {
                            const postId = post.id || post._id;
                            const isApproved = post.status === 'approved' || post.status === 'active';
                            const statusColor = isApproved ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50';
                            const statusIcon = isApproved ? 'fa-check-circle' : 'fa-times-circle';
                            const statusText = isApproved ? 'Thành công / Đã duyệt' : 'Đã từ chối';
                            
                            html += `
                            <div class="history-item flex flex-col md:flex-row gap-4 p-4 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full ${statusColor} flex items-center justify-center text-xl shrink-0">
                                        <i class="fa-solid ${statusIcon}"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900">${post.title || 'Chưa có tiêu đề'}</h3>
                                        <p class="text-sm text-slate-500 mt-1">
                                            <span class="font-semibold text-slate-700">ID: #${postId}</span> • 
                                            <i class="fa-regular fa-clock"></i> Cập nhật lúc: ${new Date(post.updated_at).toLocaleString('vi-VN')}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold ${statusColor}">
                                        ${statusText}
                                    </span>
                                    ${!isApproved ? `
                                        <button onclick="restorePost('${postId}')" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg text-sm font-bold transition-colors shadow-sm">
                                            <i class="fa-solid fa-rotate-left mr-1"></i> Khôi phục
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                        <div style="text-align: center; padding: 40px 20px;">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <p style="font-size: 1rem; font-weight: 600; color: #475569;">Không có dữ liệu lịch sử thao tác</p>
                        </div>
                        `;
                    }
                });
        }

        function restorePost(id) {
            if (!confirm('Bạn có chắc chắn muốn khôi phục bài đăng này về trạng thái Chờ phê duyệt?')) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/api/moderator/posts/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Đã khôi phục thành công! Bài đăng đã được chuyển về danh sách chờ phê duyệt.');
                    window.location.href = '/moderator/qlpheduyet';
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            fetchHistoryPosts();
        });
    </script>
</body>
</html>
