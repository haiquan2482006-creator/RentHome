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
        .moderator-container { margin-left: var(--sidebar-width); padding: 32px 40px; min-height: 100vh; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 1.75rem; font-weight: 800; }
        .complaint-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 20px; }
        .btn-action { padding: 10px 18px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; background: var(--brand-primary); color: #fff; text-decoration: none; }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        @if (session('success'))
            <div style="background:#dcfce7; color:#15803d; padding:14px; border-radius:12px; margin-bottom:20px; font-weight:600;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="page-header">
            <h1><i class="fa-solid fa-comments-question-check" style="color: var(--brand-primary); margin-right: 8px;"></i> Xử Lý Khiếu Nại & Báo Cáo Vi Phạm</h1>
            <p style="color: var(--text-secondary);">Tiếp nhận, xác minh và phản hồi giải quyết các báo cáo vi phạm từ phía thành viên</p>
        </div>

        <div class="complaint-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div style="font-weight: 700;">Lê Hoài Nam <span style="color:#64748b; font-weight:normal;">(#KN-8801)</span></div>
                <span style="background:#fee2e2; color:#b91c1c; padding:4px 10px; border-radius:12px; font-size:0.78rem; font-weight:700;">Báo cáo tin giả</span>
            </div>
            <div style="background:#f8fafc; padding:14px; border-radius:10px; margin-bottom:16px; font-size:0.9rem;">
                "Bài đăng #108 ghi phòng có ban công riêng nhưng thực tế khi tới xem là phòng khép kín không có ban công."
            </div>
            <form action="{{ url('/moderator/respond-complaint/KN-8801') }}" method="POST">
                @csrf
                <input type="text" name="response_content" placeholder="Nhập câu trả lời phản hồi cho khiếu nại này..." style="width: 70%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: inherit;" required>
                <button type="submit" class="btn-action"><i class="fa-solid fa-paper-plane"></i> Phản Hồi</button>
            </form>
        </div>
    </main>
</body>
</html>
