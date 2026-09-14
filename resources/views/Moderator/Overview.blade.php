<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trung Tâm Kiểm Duyệt & Xử Lý Khiếu Nại - RentHome Moderator</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js Library for Data Visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --bg-main: #f4f6fa;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 25px -5px rgba(2, 132, 199, 0.12);

            --brand-primary: #0284c7;
            --brand-hover: #0369a1;
            --brand-light: #38bdf8;
            --brand-bg: rgba(2, 132, 199, 0.1);

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;

            --accent-emerald: #059669;
            --accent-amber: #d97706;
            --accent-blue: #0284c7;
            --accent-red: #dc2626;
            --accent-purple: #7c3aed;

            --sidebar-width: 260px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Notification Bar */
        .alert-banner {
            background-color: #dcfce7;
            border-left: 4px solid var(--accent-emerald);
            color: #15803d;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-subtle);
            font-weight: 600;
        }

        /* Content Container */
        .moderator-container {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 32px 40px;
            min-height: 100vh;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title-group h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge-mod-role {
            font-size: 0.75rem;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        .mod-profile-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg-card);
            padding: 8px 16px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
        }

        .mod-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(2, 132, 199, 0.15);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
        }

        /* Stats Cards Overview */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(2, 132, 199, 0.3);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-icon.pending { background: rgba(217, 119, 6, 0.12); color: var(--accent-amber); }
        .stat-icon.approved { background: rgba(5, 150, 105, 0.12); color: var(--accent-emerald); }
        .stat-icon.complaints { background: rgba(220, 38, 38, 0.12); color: var(--accent-red); }
        .stat-icon.resolved { background: rgba(124, 58, 237, 0.12); color: var(--accent-purple); }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        /* Navigation Tabs */
        .tabs-header {
            display: flex;
            gap: 12px;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 28px;
        }

        .tab-btn {
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-secondary);
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: -2px;
        }

        .tab-btn i {
            font-size: 1.1rem;
        }

        .tab-btn.active {
            color: var(--brand-primary);
            border-bottom-color: var(--brand-primary);
        }

        .tab-btn .counter-badge {
            background: rgba(2, 132, 199, 0.12);
            color: var(--brand-primary);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .tab-btn.active .counter-badge {
            background: var(--brand-primary);
            color: #ffffff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Filter Controls */
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 280px;
            max-width: 420px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .filter-group {
            display: flex;
            gap: 8px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--brand-bg);
            color: var(--brand-primary);
            border-color: rgba(2, 132, 199, 0.3);
        }

        /* Custom Cards List */
        .items-grid {
            display: grid;
            gap: 20px;
        }

        .item-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 24px;
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 24px;
            align-items: center;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .item-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: rgba(2, 132, 199, 0.25);
        }

        .item-thumb {
            width: 100px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .item-details h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-meta {
            display: flex;
            gap: 16px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .item-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .item-price {
            font-weight: 800;
            color: var(--brand-primary);
            font-size: 1rem;
        }

        .badge-status {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-approved { background: #dcfce7; color: #15803d; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }

        /* Action buttons */
        .action-group {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-approve {
            background-color: var(--accent-emerald);
            color: #ffffff;
        }
        .btn-approve:hover {
            background-color: #047857;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .btn-reject {
            background-color: #fff;
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
        }
        .btn-reject:hover {
            background-color: var(--accent-red);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-respond {
            background-color: var(--brand-primary);
            color: #ffffff;
        }
        .btn-respond:hover {
            background-color: var(--brand-hover);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        /* Complaints Card */
        .complaint-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
        }

        .complaint-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .complaint-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .complaint-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-secondary);
        }

        .complaint-content {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 14px 18px;
            border-radius: 10px;
            margin: 12px 0 16px 0;
            font-size: 0.92rem;
            color: var(--text-primary);
        }

        .response-box {
            margin-top: 14px;
            padding: 14px;
            background: rgba(2, 132, 199, 0.05);
            border-left: 3px solid var(--brand-primary);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* Modal popup styling */
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

        .form-textarea:focus {
            border-color: var(--brand-primary);
        }

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

        /* Modern Chart Section Styles */
        .chart-section-wrapper {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px 28px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 32px;
        }

        .chart-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .chart-section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 24px;
        }

        .chart-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .chart-box-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .chart-box-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .chart-box-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-live {
            background: rgba(5, 150, 105, 0.1);
            color: var(--accent-emerald);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .donut-canvas-container {
            position: relative;
            height: 220px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bar-canvas-container {
            position: relative;
            height: 240px;
            width: 100%;
        }

        .chart-custom-legend {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
        }

        .legend-card-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
        }

        .legend-indicator {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .legend-indicator.approved { background-color: var(--accent-emerald); }
        .legend-indicator.rejected { background-color: var(--accent-red); }

        .legend-text-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            display: block;
        }

        .legend-text-val {
            font-size: 0.95rem;
            font-weight: 800;
            display: block;
        }

        @media (max-width: 992px) {
            .moderator-container {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
            .item-card {
                grid-template-columns: 1fr;
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Header Sidebar Component Dành Riêng Cho Moderator -->
    @include('partials.header-moderator')

    <!-- Main Content Area -->
    <main class="moderator-container">

        @if (session('success'))
            <div class="alert-banner">
                <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title-group">
                <h1>
                    Trung Tâm Kiểm Duyệt
                    <span class="badge-mod-role"><i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i> Kiểm Duyệt Viên</span>
                </h1>
                <p class="page-subtitle">Phụ trách kiểm duyệt bài đăng cho thuê nhà/phòng & tiếp nhận, xử lý khiếu nại từ người dùng</p>
            </div>

            <div class="mod-profile-badge">
                @php
                    $user = Auth::user();
                    $modName = $user ? ($user->account_name ?? $user->username) : 'Kiểm Duyệt Viên';
                    $modEmail = $user ? $user->email : 'kiemduyet@gmail.com';
                @endphp
                <div class="mod-avatar">{{ strtoupper(mb_substr($modName, 0, 1)) }}</div>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem;">{{ $modName }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $modEmail }}</div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Bài Đăng Chờ Duyệt</span>
                    <div class="stat-icon pending"><i class="fa-solid fa-hourglass-half"></i></div>
                </div>
                <div class="stat-value">12</div>
                <div style="font-size: 0.8rem; color: var(--accent-amber); font-weight: 600;"><i class="fa-solid fa-clock"></i> Cần xử lý ngay trong ngày</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Bài Đăng Đã Phê Duyệt</span>
                    <div class="stat-icon approved"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div class="stat-value">148</div>
                <div style="font-size: 0.8rem; color: var(--accent-emerald); font-weight: 600;"><i class="fa-solid fa-chart-line"></i> +18 bài đăng hôm nay</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Khiếu Nại Chờ Phản Hồi</span>
                    <div class="stat-icon complaints"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div class="stat-value">5</div>
                <div style="font-size: 0.8rem; color: var(--accent-red); font-weight: 600;"><i class="fa-solid fa-bell"></i> 2 yêu cầu ưu tiên cao</div>
            </div>
        </div>

        <!-- Biểu Đồ Thống Kê Phê Duyệt & Từ Chối Hôm Nay -->
        <div class="chart-section-wrapper">
            <div class="chart-section-header">
                <div class="chart-section-title">
                    <i class="fa-solid fa-chart-pie" style="color: var(--brand-primary); font-size: 1.3rem;"></i>
                    Thống Kê Kiểm Duyệt Bài Đăng Hôm Nay
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; background: #f1f5f9; padding: 6px 14px; border-radius: 20px;">
                    <i class="fa-solid fa-calendar-day" style="color: var(--brand-primary); margin-right: 4px;"></i> {{ date('d/m/Y') }}
                </div>
            </div>

            <div class="charts-grid">
                <!-- Donut Chart Box: Tỷ lệ Duyệt vs Từ Chối -->
                <div class="chart-box">
                    <div>
                        <div class="chart-box-header">
                            <div>
                                <h3 class="chart-box-title">Tỷ Lệ Duyệt & Từ Chối</h3>
                                <p class="chart-box-subtitle">Tổng số 23 bài đã xử lý trong ngày</p>
                            </div>
                            <span class="badge-live"><i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Trực tuyến</span>
                        </div>
                        <div class="donut-canvas-container">
                            <canvas id="todayStatusChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-custom-legend">
                        <div class="legend-card-item">
                            <span class="legend-indicator approved"></span>
                            <div>
                                <span class="legend-text-label">Đã Phê Duyệt</span>
                                <span class="legend-text-val" style="color: var(--accent-emerald);">18 bài (78.3%)</span>
                            </div>
                        </div>
                        <div class="legend-card-item">
                            <span class="legend-indicator rejected"></span>
                            <div>
                                <span class="legend-text-label">Đã Từ Chối</span>
                                <span class="legend-text-val" style="color: var(--accent-red);">5 bài (21.7%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Box: Thống Kê Theo Khung Giờ -->
                <div class="chart-box">
                    <div class="chart-box-header">
                        <div>
                            <h3 class="chart-box-title">Phân Phối Xử Lý Theo Khung Giờ</h3>
                            <p class="chart-box-subtitle">Số lượng bài được phê duyệt và từ chối qua từng mốc thời gian hôm nay</p>
                        </div>
                        <div style="font-size: 0.8rem; background: rgba(2, 132, 199, 0.08); color: var(--brand-primary); padding: 6px 12px; border-radius: 10px; font-weight: 700;">
                            <i class="fa-solid fa-chart-column"></i> Chi Tiết Hôm Nay
                        </div>
                    </div>
                    <div class="bar-canvas-container">
                        <canvas id="todayHourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tabs-header">
            <button class="tab-btn active" onclick="switchTab('posts-tab', this)">
                <i class="fa-solid fa-file-signature"></i>
                Duyệt Bài Đăng
                <span class="counter-badge">12</span>
            </button>
            <button class="tab-btn" onclick="switchTab('complaints-tab', this)">
                <i class="fa-solid fa-comments-question-check"></i>
                Phản Hồi Khiếu Nại
                <span class="counter-badge">5</span>
            </button>
        </div>

        <!-- TAB 1: DUYỆT BÀI ĐĂNG -->
        <div id="posts-tab" class="tab-content active">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Tìm theo tiêu đề, địa chỉ, người đăng...">
                </div>
                <div class="filter-group">
                    <button class="filter-btn active">Tất cả bài chờ duyệt (12)</button>
                    <button class="filter-btn">Căn hộ cho thuê</button>
                    <button class="filter-btn">Phòng trọ sinh viên</button>
                </div>
            </div>

            <div class="items-grid">
                <!-- Post Item 1 -->
                <div class="item-card">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=400&q=80" alt="Chung cư" class="item-thumb">
                    <div class="item-details">
                        <h3>
                            Cho thuê căn hộ cao cấp 2PN VinHomes Central Park đầy đủ nội thất
                            <span class="badge-status badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                        </h3>
                        <div class="item-meta">
                            <span><i class="fa-solid fa-user"></i> Chủ nhà: Nguyễn Văn Hùng</span>
                            <span><i class="fa-solid fa-location-dot"></i> Bình Thạnh, TP.HCM</span>
                            <span><i class="fa-solid fa-calendar"></i> Gửi lúc: 10:15 - 13/09/2026</span>
                        </div>
                        <div class="item-price">15,000,000 VNĐ / tháng</div>
                    </div>
                    <div class="action-group">
                        <form action="{{ url('/moderator/approve-post/101') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT bài đăng này?')">
                                <i class="fa-solid fa-check"></i> Duyệt Bài
                            </button>
                        </form>
                        <button class="btn-action btn-reject" onclick="openRejectModal('101', 'Cho thuê căn hộ cao cấp 2PN VinHomes Central Park')">
                            <i class="fa-solid fa-xmark"></i> Từ Chối
                        </button>
                    </div>
                </div>

                <!-- Post Item 2 -->
                <div class="item-card">
                    <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=400&q=80" alt="Phòng trọ" class="item-thumb">
                    <div class="item-details">
                        <h3>
                            Phòng trọ sinh viên khép kín gần ĐH Bách Khoa, có gác xép & máy lạnh
                            <span class="badge-status badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                        </h3>
                        <div class="item-meta">
                            <span><i class="fa-solid fa-user"></i> Chủ trọ: Trần Thị Mai</span>
                            <span><i class="fa-solid fa-location-dot"></i> Hai Bà Trưng, Hà Nội</span>
                            <span><i class="fa-solid fa-calendar"></i> Gửi lúc: 09:30 - 13/09/2026</span>
                        </div>
                        <div class="item-price">3,800,000 VNĐ / tháng</div>
                    </div>
                    <div class="action-group">
                        <form action="{{ url('/moderator/approve-post/102') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT bài đăng này?')">
                                <i class="fa-solid fa-check"></i> Duyệt Bài
                            </button>
                        </form>
                        <button class="btn-action btn-reject" onclick="openRejectModal('102', 'Phòng trọ sinh viên khép kín gần ĐH Bách Khoa')">
                            <i class="fa-solid fa-xmark"></i> Từ Chối
                        </button>
                    </div>
                </div>

                <!-- Post Item 3 -->
                <div class="item-card">
                    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=400&q=80" alt="Nhà nguyên căn" class="item-thumb">
                    <div class="item-details">
                        <h3>
                            Cho thuê nhà nguyên căn 3 tầng kinh doanh hoặc ở gia đình hẻm xe hơi
                            <span class="badge-status badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                        </h3>
                        <div class="item-meta">
                            <span><i class="fa-solid fa-building-user"></i> Công ty BĐS Hoàng Gia</span>
                            <span><i class="fa-solid fa-location-dot"></i> Quận 10, TP.HCM</span>
                            <span><i class="fa-solid fa-calendar"></i> Gửi lúc: 08:45 - 13/09/2026</span>
                        </div>
                        <div class="item-price">22,000,000 VNĐ / tháng</div>
                    </div>
                    <div class="action-group">
                        <form action="{{ url('/moderator/approve-post/103') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT bài đăng này?')">
                                <i class="fa-solid fa-check"></i> Duyệt Bài
                            </button>
                        </form>
                        <button class="btn-action btn-reject" onclick="openRejectModal('103', 'Cho thuê nhà nguyên căn 3 tầng')">
                            <i class="fa-solid fa-xmark"></i> Từ Chối
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: PHẢN HỒI KHIẾU NẠI -->
        <div id="complaints-tab" class="tab-content">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Tìm theo người khiếu nại, mã đơn, tiêu đề báo cáo...">
                </div>
                <div class="filter-group">
                    <button class="filter-btn active">Chờ phản hồi (5)</button>
                    <button class="filter-btn">Vi phạm tin giả</button>
                    <button class="filter-btn">Tranh chấp tiền cọc</button>
                </div>
            </div>

            <div class="items-grid">
                <!-- Complaint 1 -->
                <div class="complaint-card">
                    <div class="complaint-header">
                        <div class="complaint-user">
                            <div class="user-avatar-sm">LH</div>
                            <div>
                                <div style="font-weight: 700; font-size: 1rem;">Lê Hoài Nam <span style="font-size:0.8rem; color:var(--text-muted); font-weight:normal;">(Người thuê)</span></div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">Mã khiếu nại: #KN-2026-8801 • 13/09/2026</div>
                            </div>
                        </div>
                        <span class="badge-status badge-pending"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo tin đăng sai sự thật</span>
                    </div>

                    <div class="complaint-content">
                        <strong>Nội dung khiếu nại:</strong> "Bài đăng #108 ghi phòng có ban công và máy giặt riêng nhưng khi tôi đến xem thực tế thì phòng không có ban công và phải dùng máy giặt chung. Đề nghị kiểm duyệt viên xử lý bài đăng ảo này."
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: var(--brand-primary); font-weight: 600;">
                            <i class="fa-solid fa-link"></i> Bài đăng bị khiếu nại: ID #108 - Phòng trọ Quận 3
                        </div>
                        <button class="btn-action btn-respond" onclick="openRespondModal('KN-2026-8801', 'Lê Hoài Nam')">
                            <i class="fa-solid fa-reply"></i> Gửi Phản Hồi Khiếu Nại
                        </button>
                    </div>
                </div>

                <!-- Complaint 2 -->
                <div class="complaint-card">
                    <div class="complaint-header">
                        <div class="complaint-user">
                            <div class="user-avatar-sm" style="background:#fef3c7; color:#b45309;">PT</div>
                            <div>
                                <div style="font-weight: 700; font-size: 1rem;">Phạm Thanh Thảo <span style="font-size:0.8rem; color:var(--text-muted); font-weight:normal;">(Khách xem phòng)</span></div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">Mã khiếu nại: #KN-2026-8802 • 12/09/2026</div>
                            </div>
                        </div>
                        <span class="badge-status badge-pending"><i class="fa-solid fa-triangle-exclamation"></i> Tố cáo chủ trọ thái độ thiếu tôn trọng</span>
                    </div>

                    <div class="complaint-content">
                        <strong>Nội dung khiếu nại:</strong> "Tôi đã hẹn lịch xem phòng qua hệ thống lúc 15h, nhưng đến nơi chủ nhà không nghe máy và khi gọi lại thì có thái độ gắt gỏng, thu thêm phí xem phòng không có trong quy định."
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.85rem; color: var(--brand-primary); font-weight: 600;">
                            <i class="fa-solid fa-user-ninja"></i> Bị tố cáo: Chủ trọ Nguyễn Văn B (SĐT: 0912***789)
                        </div>
                        <button class="btn-action btn-respond" onclick="openRespondModal('KN-2026-8802', 'Phạm Thanh Thảo')">
                            <i class="fa-solid fa-reply"></i> Gửi Phản Hồi Khiếu Nại
                        </button>
                    </div>
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
                    <button type="submit" class="btn-action btn-reject"><i class="fa-solid fa-paper-plane"></i> Xóa / Từ chối bài</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Phản Hồi Khiếu Nại -->
    <div class="modal-overlay" id="respondModal">
        <div class="modal-card">
            <h2 class="modal-title"><i class="fa-solid fa-reply-all" style="color: var(--brand-primary); margin-right: 8px;"></i> Phản Hồi Khiếu Nại</h2>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 14px;" id="respondUserText"></p>

            <form id="respondForm" method="POST">
                @csrf
                <label style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">Nội dung phản hồi & phương án giải quyết:</label>
                <textarea name="response_content" class="form-textarea" placeholder="Nhập kết quả xác minh & phương án xử lý (ví dụ: Đã nhắc nhở chủ nhà / Đã tạm khóa tin đăng vi phạm / Cảnh cáo tài khoản...)" required></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('respondModal')">Hủy bỏ</button>
                    <button type="submit" class="btn-action btn-respond"><i class="fa-solid fa-paper-plane"></i> Gửi Phản Hồi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId, element) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            element.classList.add('active');
        }

        function openRejectModal(id, title) {
            document.getElementById('rejectItemTitle').innerText = 'Bài đăng: "' + title + '" (ID: #' + id + ')';
            document.getElementById('rejectForm').action = '{{ url("/moderator/reject-post") }}/' + id;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function openRespondModal(id, userName) {
            document.getElementById('respondUserText').innerText = 'Gửi phản hồi xử lý tới người khiếu nại: ' + userName + ' (Đơn #' + id + ')';
            document.getElementById('respondForm').action = '{{ url("/moderator/respond-complaint") }}/' + id;
            document.getElementById('respondModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking background
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        };

        // Khởi tạo biểu đồ kiểm duyệt bài đăng khi trang load xong
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Donut Chart - Tỷ lệ Phê duyệt vs Từ chối hôm nay
            const approvedCount = 18;
            const rejectedCount = 5;
            const totalCount = approvedCount + rejectedCount;

            const ctxDonut = document.getElementById('todayStatusChart');
            if (ctxDonut) {
                new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đã Phê Duyệt', 'Đã Từ Chối'],
                        datasets: [{
                            data: [approvedCount, rejectedCount],
                            backgroundColor: ['#059669', '#dc2626'],
                            hoverBackgroundColor: ['#047857', '#b91c1c'],
                            borderWidth: 4,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '74%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const val = context.raw;
                                        const percentage = ((val / totalCount) * 100).toFixed(1);
                                        return ` ${context.label}: ${val} bài (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerTextPlugin',
                        beforeDraw(chart) {
                            const { width, height, ctx } = chart;
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            
                            ctx.font = '800 24px "Plus Jakarta Sans", sans-serif';
                            ctx.fillStyle = '#0f172a';
                            ctx.fillText(totalCount, width / 2, height / 2 - 8);

                            ctx.font = '600 12px "Plus Jakarta Sans", sans-serif';
                            ctx.fillStyle = '#94a3b8';
                            ctx.fillText('Bài Đã Xử Lý', width / 2, height / 2 + 14);
                            ctx.restore();
                        }
                    }]
                });
            }

            // Chart 2: Bar Chart - Phân phối xử lý theo từng khung giờ hôm nay
            const ctxBar = document.getElementById('todayHourlyChart');
            if (ctxBar) {
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'],
                        datasets: [
                            {
                                label: 'Đã phê duyệt',
                                data: [3, 5, 2, 4, 3, 1],
                                backgroundColor: '#059669',
                                borderRadius: 6,
                                barPercentage: 0.55,
                                categoryPercentage: 0.65
                            },
                            {
                                label: 'Đã từ chối',
                                data: [1, 1, 0, 2, 1, 0],
                                backgroundColor: '#dc2626',
                                borderRadius: 6,
                                barPercentage: 0.55,
                                categoryPercentage: 0.65
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' }, color: '#64748b' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans', size: 12 }, color: '#64748b' },
                                grid: { color: '#f1f5f9' }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
                                    color: '#334155'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: '700' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
