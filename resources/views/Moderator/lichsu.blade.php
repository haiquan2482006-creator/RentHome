<!DOCTYPE html>
<html lang="vi">
<head>
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

        <div class="history-list">
            <div class="history-item">
                <div>
                    <strong>Phê duyệt bài đăng:</strong> "Căn hộ cao cấp 2PN VinHomes" (ID: #101)
                    <div style="font-size:0.8rem; color:#64748b;">13/09/2026 14:20 • Thực hiện bởi Kiểm Duyệt Viên System</div>
                </div>
                <span style="background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:12px; font-size:0.78rem; font-weight:700;">Thành công</span>
            </div>

            <div class="history-item">
                <div>
                    <strong>Phản hồi khiếu nại:</strong> Đơn #KN-8801 - Báo cáo tin giả
                    <div style="font-size:0.8rem; color:#64748b;">13/09/2026 11:45 • Thực hiện bởi Kiểm Duyệt Viên System</div>
                </div>
                <span style="background:#e0f2fe; color:#0284c7; padding:4px 10px; border-radius:12px; font-size:0.78rem; font-weight:700;">Đã phản hồi</span>
            </div>
        </div>
    </main>
</body>
</html>
