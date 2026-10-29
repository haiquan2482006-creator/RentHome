<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lịch Sử Hoạt Động - RentHome CSKH</title>

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --bg-body: #f8fafc;
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
            width: 44px;
            height: 44px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-info h2 {
            font-size: 16.5px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-badge {
            font-size: 10.5px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 700;
        }

        .brand-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 10px 12px 6px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-link.active {
            background: #e0f2fe;
            color: var(--primary);
            font-weight: 700;
        }

        .nav-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-badge {
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 700;
        }

        .badge-danger-pill {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-warning-pill {
            background: #fef3c7;
            color: #d97706;
        }

        .user-footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 14px;
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
            background: #e0f2fe;
            color: #0369a1;
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
            color: #94a3b8;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #ef4444;
            background: #fee2e2;
        }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px 32px;
            max-width: calc(100vw - var(--sidebar-width));
            min-height: 100vh;
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

        .page-title-box p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ================= KPI CARDS ================= */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }

        .kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .kpi-icon.blue { background: #e0f2fe; color: #0284c7; }
        .kpi-icon.purple { background: #f3e8ff; color: #8b5cf6; }
        .kpi-icon.emerald { background: #d1fae5; color: #10b981; }
        .kpi-icon.amber { background: #fef3c7; color: #f59e0b; }

        .kpi-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .kpi-title {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        /* ================= FILTER PANEL ================= */
        .card-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-input-box {
            position: relative;
            flex: 1;
            min-width: 260px;
        }

        .search-input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .search-input-box input {
            width: 100%;
            padding: 9.5px 14px 9.5px 38px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 13.5px;
            background: #f8fafc;
            color: var(--text-main);
            transition: all 0.2s;
        }

        .search-input-box input:focus {
            outline: none;
            background: #ffffff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .filter-select {
            padding: 9.5px 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 13.5px;
            background: #f8fafc;
            color: #334155;
            cursor: pointer;
            font-weight: 500;
        }

        .filter-select:focus {
            outline: none;
            background: #ffffff;
            border-color: #38bdf8;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: #ffffff;
            border-color: var(--border-color);
            color: #475569;
        }

        .btn-outline:hover {
            background: #f8fafc;
            color: var(--text-main);
        }

        /* ================= TABLE ================= */
        .table-responsive {
            overflow-x: auto;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        .activity-table thead tr {
            border-bottom: 1px solid var(--border-color);
            background: #f8fafc;
        }

        .activity-table th {
            padding: 12px 14px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .activity-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .activity-table tbody tr:hover {
            background: #f8fafc;
        }

        .activity-table td {
            padding: 14px;
            vertical-align: middle;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            :root {
                --sidebar-width: 0px;
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .main-wrapper {
                margin-left: 0;
                max-width: 100vw;
                padding: 16px;
            }
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR CHĂM SÓC KHÁCH HÀNG (CSKH) ================= -->
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-logo">
                <img src="{{ asset('img/logo.png') }}" alt="RentHome Logo">
            </div>
            <div class="brand-info">
                <h2>RentHome <span class="role-badge">CSKH</span></h2>
                <p>Trung tâm Chăm sóc khách hàng</p>
            </div>
        </div>

        <div class="nav-section-title">Chăm Sóc Khách Hàng</div>
        <ul class="nav-list">
            <li>
                <a href="{{ url('/cskh/tong-quan') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Tổng quan</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/cskh/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Quản lý Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill" id="sidebarComplaintBadge">{{ $pendingCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.notifications') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </span>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="nav-badge badge-warning-pill" id="sidebarNotifBadge">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('cskh.history') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lịch sử hoạt động</span>
                    </span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-footer-left">
                <div class="avatar-circle">CSKH</div>
                <div class="user-meta">
                    <h4>{{ $currentUser->account_name ?? ($currentUser->username ?? 'Chăm Sóc Khách Hàng') }}</h4>
                    <p>{{ $currentUser->email ?? 'chamsockh@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-wrapper">
        <!-- 1. TIÊU ĐỀ TRANG -->
        <header class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                    <span>Lịch Sử Hoạt Động CSKH</span>
                </h1>
                <p>Nhật ký ghi nhận chi tiết toàn bộ thao tác tiếp nhận, trao đổi, hòa giải và trình khiếu nại của kênh CSKH</p>
            </div>
        </header>

        <!-- 2. DANH SÁCH BẢNG NHẬT KÝ HOẠT ĐỘNG -->
        <section class="card-panel p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th style="width: 170px;">THỜI GIAN</th>
                            <th style="width: 170px;">LOẠI HÀNH ĐỘNG</th>
                            <th style="width: 150px;">MÃ HỒ SƠ / ĐỐI TƯỢNG</th>
                            <th>MÔ TẢ CHI TIẾT THAO TÁC</th>
                            <th style="width: 160px;">NGƯỜI THỰC HIỆN</th>
                            <th style="width: 110px; text-align: right;">CHI TIẾT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $act)
                            @php
                                $type = $act->action_type ?? 'info';
                                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                $badgeIcon = 'fa-circle-info';
                                $badgeLabel = 'Thao tác hệ thống';

                                if (in_array($type, ['escalate', 'escalate_ticket'])) {
                                    $badgeClass = 'bg-purple-100 text-purple-800 border border-purple-200';
                                    $badgeIcon = 'fa-shield-halved';
                                    $badgeLabel = 'Trình Admin (Cấp 2)';
                                } elseif (in_array($type, ['resolve', 'resolve_contact', 'approve'])) {
                                    $badgeClass = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                                    $badgeIcon = 'fa-circle-check';
                                    $badgeLabel = 'CSKH xử lý / Đóng đơn';
                                } elseif (in_array($type, ['reply', 'message'])) {
                                    $badgeClass = 'bg-sky-100 text-sky-800 border border-sky-200';
                                    $badgeIcon = 'fa-comment-dots';
                                    $badgeLabel = 'Phản hồi khách';
                                } elseif (in_array($type, ['warning', 'remind_staff'])) {
                                    $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-200';
                                    $badgeIcon = 'fa-triangle-exclamation';
                                    $badgeLabel = 'Nhắc nhở vi phạm';
                                } elseif (in_array($type, ['reject', 'reject_complaint', 'dismiss'])) {
                                    $badgeClass = 'bg-rose-100 text-rose-800 border border-rose-200';
                                    $badgeIcon = 'fa-ban';
                                    $badgeLabel = 'Bác bỏ / Từ chối';
                                }

                                $timeFormatted = !empty($act->created_at) ? \Carbon\Carbon::parse($act->created_at)->format('H:i:s - d/m/Y') : '--';
                                $diffForHumans = !empty($act->created_at) ? \Carbon\Carbon::parse($act->created_at)->diffForHumans() : '';
                                $targetName = $act->target_name ?: ($act->metadata['code'] ?? ($act->target_id ? '#' . substr($act->target_id, -5) : '--'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-bold text-slate-800">{{ $timeFormatted }}</div>
                                    <div class="text-xs text-slate-400 font-medium">{{ $diffForHumans }}</div>
                                </td>
                                <td>
                                    <span class="status-pill {{ $badgeClass }}">
                                        <i class="fa-solid {{ $badgeIcon }}"></i>
                                        <span>{{ $badgeLabel }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-mono font-bold text-xs border border-slate-200">
                                        {{ $targetName }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 mb-0.5">{{ $act->title }}</div>
                                    <div class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $act->description ?: 'Không có ghi chú thêm.' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold text-slate-800 flex items-center gap-1.5">
                                        <i class="fa-solid fa-user-gear text-sky-600 text-[11px]"></i>
                                        <span>{{ $act->admin_name ?: 'Chăm Sóc Khách Hàng' }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $act->ip_address ?: '127.0.0.1' }}</div>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" 
                                            onclick='viewActivityDetail(@json($act))' 
                                            class="p-2 text-slate-500 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition" 
                                            title="Xem chi tiết thao tác">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-clock-rotate-left text-4xl text-slate-300 mb-3 block"></i>
                                    <h3 class="text-base font-bold text-slate-700 mb-1">Chưa có lịch sử hoạt động phù hợp</h3>
                                    <p class="text-xs text-slate-400">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm để tra cứu nhật ký khác.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PHÂN TRANG -->
            @if($activities->hasPages())
                <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Hiển thị {{ $activities->firstItem() }} - {{ $activities->lastItem() }} trên tổng số {{ $activities->total() }} bản ghi
                    </div>
                    <div>
                        {{ $activities->links() }}
                    </div>
                </div>
            @endif
        </section>
    </main>

    <!-- ================= MODAL XEM CHI TIẾT HOẠT ĐỘNG ================= -->
    <script>
        function viewActivityDetail(act) {
            let metaHtml = '';
            if (act.metadata && Object.keys(act.metadata).length > 0) {
                metaHtml = `<div class="mt-3 p-3 bg-slate-50 rounded-lg text-left text-xs font-mono text-slate-700 overflow-x-auto border border-slate-200">
                    <strong>Dữ liệu phụ trợ (Metadata):</strong><br>
                    <pre class="mt-1">${JSON.stringify(act.metadata, null, 2)}</pre>
                </div>`;
            }

            Swal.fire({
                title: `<div class="text-left font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-sky-600"></i>
                    <span>Chi Tiết Nhật Ký Hoạt Động</span>
                </div>`,
                html: `
                    <div class="text-left space-y-2.5 text-xs text-slate-700 pt-2">
                        <div><strong>Hành động:</strong> <span class="text-sky-700 font-semibold">${act.title || '--'}</span></div>
                        <div><strong>Thời gian:</strong> <span class="font-medium text-slate-900">${act.created_at || '--'}</span></div>
                        <div><strong>Người thực hiện:</strong> ${act.admin_name || 'Chăm Sóc Khách Hàng'} (${act.admin_email || 'chamsockh@gmail.com'})</div>
                        <div><strong>Đối tượng tác động:</strong> <span class="font-mono bg-slate-100 px-2 py-0.5 rounded font-bold">${act.target_name || act.target_id || '--'}</span></div>
                        <div><strong>Địa chỉ IP:</strong> <span class="font-mono">${act.ip_address || '127.0.0.1'}</span></div>
                        <div><strong>Thiết bị / Trình duyệt:</strong> ${act.user_agent || 'Chrome / Cốc Cốc trên Windows'}</div>
                        <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-lg text-blue-900 leading-relaxed mt-2">
                            <strong>Nội dung ghi nhận:</strong><br>
                            ${act.description || 'Không có mô tả chi tiết.'}
                        </div>
                        ${metaHtml}
                    </div>
                `,
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#0284c7',
                width: '540px'
            });
        }
    </script>
</body>
</html>

