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

        @media (max-width: 992px) {
            .moderator-container { margin-left: 0; padding: 20px; }
            .item-card { grid-template-columns: 1fr; }
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

        <div class="items-grid">
            <div class="item-card">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=400&q=80" class="item-thumb" alt="Chung cư">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700;">Cho thuê căn hộ cao cấp 2PN VinHomes Central Park</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 6px 0;">Chủ nhà: Nguyễn Văn Hùng • Bình Thạnh, TP.HCM</p>
                    <span style="font-weight: 800; color: var(--brand-primary);">15,000,000 VNĐ / tháng</span>
                </div>
                <div style="display:flex; gap:10px;">
                    <form action="{{ url('/moderator/approve-post/101') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-action btn-approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT bài đăng này?')">
                            <i class="fa-solid fa-check"></i> Duyệt Bài
                        </button>
                    </form>
                    <button type="button" class="btn-action btn-reject" onclick="openRejectModal('101', 'Cho thuê căn hộ cao cấp 2PN VinHomes Central Park')">
                        <i class="fa-solid fa-xmark"></i> Từ Chối
                    </button>
                </div>
            </div>

            <div class="item-card">
                <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=400&q=80" class="item-thumb" alt="Phòng trọ">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700;">Phòng trọ sinh viên khép kín gần ĐH Bách Khoa</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 6px 0;">Chủ trọ: Trần Thị Mai • Hai Bà Trưng, Hà Nội</p>
                    <span style="font-weight: 800; color: var(--brand-primary);">3,800,000 VNĐ / tháng</span>
                </div>
                <div style="display:flex; gap:10px;">
                    <form action="{{ url('/moderator/approve-post/102') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-action btn-approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT bài đăng này?')">
                            <i class="fa-solid fa-check"></i> Duyệt Bài
                        </button>
                    </form>
                    <button type="button" class="btn-action btn-reject" onclick="openRejectModal('102', 'Phòng trọ sinh viên khép kín gần ĐH Bách Khoa')">
                        <i class="fa-solid fa-xmark"></i> Từ Chối
                    </button>
                </div>
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

    <script>
        function openRejectModal(id, title) {
            document.getElementById('rejectItemTitle').innerText = 'Bài đăng: "' + title + '" (ID: #' + id + ')';
            document.getElementById('rejectForm').action = '{{ url("/moderator/reject-post") }}/' + id;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>
