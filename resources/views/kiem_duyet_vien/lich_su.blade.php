<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lịch Sử Kiểm Duyệt - RentHome</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --bg-body: #f4f7fb;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            padding: 20px 16px;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 18px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: #0f172a;
            color: #38bdf8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .brand-info h2 {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-badge {
            font-size: 10px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .brand-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .nav-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
        }

        .badge-warning-pill { background: var(--warning-light); color: #b45309; }
        .badge-danger-pill { background: var(--danger-light); color: #b91c1c; }

        .user-footer {
            margin-top: auto;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-footer-left {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-meta h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta p {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
            padding: 6px;
            border-radius: 8px;
            transition: 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: var(--danger-light);
        }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px 32px;
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100vw - var(--sidebar-width));
            min-height: 100vh;
            box-sizing: border-box;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title-box h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title-box h1 i {
            color: var(--primary);
        }

        .page-title-box p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-outline {
            background: #fff;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
        }

        .summary-val {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .summary-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .summary-icon.blue { background: var(--primary-light); color: var(--primary); }
        .summary-icon.green { background: var(--success-light); color: var(--success); }
        .summary-icon.red { background: var(--danger-light); color: var(--danger); }
        .summary-icon.amber { background: var(--warning-light); color: #b45309; }

        /* Filter Toolbar */
        .filter-toolbar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: var(--text-main);
            background: #f1f5f9;
        }

        .tab-btn.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .search-box {
            position: relative;
            min-width: 260px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .search-input {
            width: 100%;
            padding: 8px 14px 8px 34px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            font-size: 13px;
            color: var(--text-main);
            outline: none;
        }

        .search-input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13.5px;
        }

        .history-table th {
            background: #f8fafc;
            padding: 13px 18px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .history-table td {
            padding: 15px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .history-table tr:hover {
            background: #fbfcfe;
        }

        .badge-act {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-act-approve { background: var(--success-light); color: #047857; }
        .badge-act-reject { background: var(--danger-light); color: #b91c1c; }
        .badge-act-default { background: var(--primary-light); color: var(--primary); }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 60px;
            height: 60px;
            background: #f1f5f9;
            color: #94a3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 16px;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .empty-desc {
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    @php
        $userObj = Auth::user();
        $pCount = $pendingCount ?? \App\Models\Post::where('status', 'pending')->count();
        $cCount = $complaintCount ?? \App\Models\Complaint::where('status', 'pending')->count();
        $activityList = $activities ?? \App\Models\AdminActivity::where('category', 'post')
            ->orWhereIn('action_type', ['approve', 'reject'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalActs = count($activityList);
        $approvedActs = $activityList->filter(fn($a) => in_array($a->action_type, ['approve', 'duyet']))->count();
        $rejectedActs = $activityList->filter(fn($a) => in_array($a->action_type, ['reject', 'tu_choi']))->count();
    @endphp

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">Kiểm Duyệt</span></h2>
                <p>Hệ thống kiểm duyệt nội bộ</p>
            </div>
        </div>

        <div class="nav-section-title">Nghiệp Vụ Kiểm Duyệt</div>
        <ul class="nav-list">
            <li>
                <a href="{{ url('/kiem-duyet/tong-quan') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tổng quan</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/qlkiem-duyet') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Quản lý kiểm duyệt</span>
                    </span>
                    @if($pCount > 0)
                        <span class="nav-badge badge-warning-pill">{{ $pCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    @if($cCount > 0)
                        <span class="nav-badge badge-danger-pill">{{ $cCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/qltin-tuc') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Quản lý tin tức</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/lich-su') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lịch sử duyệt</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/thong-bao') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-footer-left">
                <div class="avatar-circle">
                    {{ strtoupper(mb_substr($userObj->account_name ?? 'KD', 0, 2)) }}
                </div>
                <div class="user-meta">
                    <h4>{{ $userObj->account_name ?? 'Kiểm Duyệt Viên' }}</h4>
                    <p>{{ $userObj->email ?? 'kiemduyet2006@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        <!-- Header -->
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Lịch Sử Hoạt Động Kiểm Duyệt
                </h1>
                <p>Theo dõi chi tiết nhật ký phê duyệt, từ chối bài đăng và xử lý nghiệp vụ trên hệ thống RentHome.</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.reload()">
                    <i class="fa-solid fa-rotate"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- 4 Summary Cards -->
        <section class="summary-grid">
            <div class="summary-card">
                <div>
                    <div class="summary-val">{{ $totalActs }}</div>
                    <div class="summary-label">Tổng lượt xử lý</div>
                </div>
                <div class="summary-icon blue">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>

            <div class="summary-card">
                <div>
                    <div class="summary-val" style="color: #047857;">{{ $approvedActs }}</div>
                    <div class="summary-label">Bài đã phê duyệt</div>
                </div>
                <div class="summary-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="summary-card">
                <div>
                    <div class="summary-val" style="color: #b91c1c;">{{ $rejectedActs }}</div>
                    <div class="summary-label">Bài bị từ chối</div>
                </div>
                <div class="summary-icon red">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>

            <div class="summary-card">
                <div>
                    <div class="summary-val" style="color: #b45309;">{{ $pCount }}</div>
                    <div class="summary-label">Đang chờ duyệt</div>
                </div>
                <div class="summary-icon amber">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </section>

        <!-- Filter Toolbar -->
        <div class="filter-toolbar">
            <div class="filter-tabs" id="filterTabs">
                <button type="button" class="tab-btn active" onclick="filterActs('all', this)">Tất cả ({{ $totalActs }})</button>
                <button type="button" class="tab-btn" onclick="filterActs('approve', this)">Đã phê duyệt ({{ $approvedActs }})</button>
                <button type="button" class="tab-btn" onclick="filterActs('reject', this)">Từ chối ({{ $rejectedActs }})</button>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchAct" class="search-input" placeholder="Tìm theo tiêu đề, người duyệt, lý do..." oninput="handleSearchAct()">
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            @if(count($activityList) > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width: 170px;">Thời gian</th>
                            <th style="width: 140px;">Hành động</th>
                            <th>Nội dung / Bài viết tác động</th>
                            <th style="width: 180px;">Kiểm duyệt viên</th>
                            <th style="width: 130px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="historyTbody">
                        @foreach($activityList as $act)
                            @php
                                $type = $act->action_type ?? 'other';
                                $isApprove = in_array($type, ['approve', 'duyet']);
                                $isReject = in_array($type, ['reject', 'tu_choi']);
                                $actType = $isApprove ? 'approve' : ($isReject ? 'reject' : 'other');
                            @endphp
                            <tr data-type="{{ $actType }}" data-search="{{ mb_strtolower(($act->title ?? '') . ' ' . ($act->description ?? '') . ' ' . ($act->admin_name ?? '')) }}">
                                <td style="font-size: 12.5px; color: #64748b;">
                                    <i class="fa-regular fa-clock mr-1"></i>
                                    {{ $act->created_at ? $act->created_at->format('d/m/Y H:i:s') : 'Mới đây' }}
                                </td>
                                <td>
                                    @if($isApprove)
                                        <span class="badge-act badge-act-approve">
                                            <i class="fa-solid fa-check"></i> Duyệt bài
                                        </span>
                                    @elseif($isReject)
                                        <span class="badge-act badge-act-reject">
                                            <i class="fa-solid fa-xmark"></i> Từ chối
                                        </span>
                                    @else
                                        <span class="badge-act badge-act-default">
                                            <i class="fa-solid fa-circle-info"></i> {{ ucfirst($type) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                        {{ $act->title ?? 'Thao tác kiểm duyệt' }}
                                    </div>
                                    <div style="font-size: 12.5px; color: #475569;">
                                        {{ $act->description ?? 'Không có mô tả chi tiết' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 13px; color: #0f172a;">
                                        {{ $act->admin_name ?? 'Kiểm duyệt viên' }}
                                    </div>
                                    <div style="font-size: 11.5px; color: #94a3b8;">
                                        {{ $act->admin_email ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 12px; font-weight: 700; color: {{ $isApprove ? '#059669' : ($isReject ? '#dc2626' : '#2563eb') }};">
                                        {{ $isApprove ? 'Thành công' : ($isReject ? 'Đã ghi nhận' : 'Hoàn tất') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="empty-title">Chưa có lịch sử duyệt bài</div>
                    <div class="empty-desc">Khi bạn thực hiện thao tác Duyệt hoặc Từ chối tin đăng, toàn bộ nhật ký sẽ được lưu vết chi tiết tại đây.</div>
                </div>
            @endif
        </div>
    </main>

    <script>
        let currentActTab = 'all';

        function filterActs(tab, btn) {
            currentActTab = tab;
            document.querySelectorAll('#filterTabs .tab-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            applyActFilters();
        }

        function handleSearchAct() {
            applyActFilters();
        }

        function applyActFilters() {
            const kw = (document.getElementById('searchAct')?.value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#historyTbody tr');

            rows.forEach(row => {
                const type = row.getAttribute('data-type');
                const search = row.getAttribute('data-search') || '';

                const matchTab = (currentActTab === 'all') || (currentActTab === type);
                const matchKw = !kw || search.includes(kw);

                row.style.display = (matchTab && matchKw) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
