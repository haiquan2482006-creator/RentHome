<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch Sử Hoạt Động - RentHome Admin</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --brand-primary: #0284c7;
            --brand-bg: #e0f2fe;
            --accent-amber: #d97706;
            --accent-red: #dc2626;
            --accent-emerald: #059669;
            --accent-purple: #7c3aed;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 30px 36px;
            min-height: 100vh;
            background-color: var(--bg-main);
            width: calc(100% - var(--sidebar-width));
        }

        /* --- Header --- */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--brand-primary);
            font-size: 1.45rem;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-secondary);
            box-shadow: var(--shadow-subtle);
        }

        .btn-header:hover {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background-color: #f0f9ff;
            transform: translateY(-1px);
        }

        /* --- Card Chứa Bảng Lịch Sử --- */
        .content-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        .card-header-bar {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            background-color: #ffffff;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-search-box {
            position: relative;
            min-width: 280px;
        }

        .card-search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .card-search-box input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.825rem;
            background-color: #f8fafc;
            outline: none;
            transition: var(--transition);
        }

        .card-search-box input:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.12);
        }

        /* --- Table Styling --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .history-table th {
            padding: 14px 18px;
            font-size: 0.775rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .history-table td {
            padding: 15px 18px;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .history-table tbody tr {
            transition: var(--transition);
        }

        .history-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .act-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-create {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .badge-update {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .badge-lock {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-unlock, .badge-restore {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-delete, .badge-force_delete {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-config, .badge-notify {
            background-color: #faf5ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }

        /* Detail Cell */
        .title-text {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .desc-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
            line-height: 1.4;
        }

        .target-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.725rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #334155;
            padding: 2px 7px;
            border-radius: 4px;
            margin-top: 4px;
            border: 1px solid #e2e8f0;
        }

        .time-box {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        /* Action Buttons */
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--brand-primary);
            background-color: var(--brand-bg);
            border: 1px solid #bae6fd;
            text-decoration: none;
            transition: var(--transition);
            white-space: nowrap;
            cursor: pointer;
        }

        .btn-table-action:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
        }

        .btn-table-unlock {
            background-color: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .btn-table-unlock:hover {
            background-color: #059669;
            color: #ffffff;
            border-color: #059669;
        }

        /* Empty */
        .empty-box {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }

        .empty-box i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #cbd5e1;
            display: block;
        }
    </style>
</head>
<body>
    @include('partials.header-admin')

    <main class="admin-main">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Lịch Sử Hoạt Động</span>
                </h1>
                <p class="page-subtitle">Nhật ký ghi nhận toàn bộ hoạt động của Quản trị viên trong hệ thống (Tài khoản, Tòa nhà, Bài đăng, Cấu hình...)</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn-header" onclick="location.reload()" title="Tải lại dữ liệu mới nhất">
                    <i class="fa-solid fa-rotate-right"></i> Làm mới
                </button>
            </div>
        </div>

        @php
            $activityList = $activities ?? collect([]);
        @endphp

        <!-- Khung Danh Sách Lịch Sử Đơn Giản -->
        <div class="content-card">
            <div class="card-header-bar">
                <div class="card-title">
                    <i class="fa-solid fa-list-ul" style="color: var(--brand-primary);"></i>
                    <span>Danh Sách Hoạt Động Gần Đây</span>
                    <span style="font-size: 0.775rem; font-weight: 700; color: var(--brand-primary); background: var(--brand-bg); padding: 2px 8px; border-radius: 6px; margin-left: 6px;">
                        {{ count($activityList) }} hoạt động
                    </span>
                </div>
                <div class="card-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="simpleSearch" placeholder="Tìm kiếm nhanh nội dung, tên đối tượng..." oninput="filterSimpleTable()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">#</th>
                            <th style="width: 170px;">Thời Gian</th>
                            <th style="width: 150px;">Loại Thao Tác</th>
                            <th>Nội Dung Hoạt Động</th>
                            <th style="width: 160px; text-align: right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @forelse ($activityList as $index => $act)
                            @php
                                $cType = $act->category ?? 'system';
                                $aType = $act->action_type ?? 'create';

                                if ($aType === 'create') {
                                    $actText = 'Thêm mới';
                                    $actClass = 'badge-create';
                                    $actIcon = 'fa-solid fa-plus';
                                } elseif ($aType === 'update') {
                                    $actText = 'Cập nhật';
                                    $actClass = 'badge-update';
                                    $actIcon = 'fa-solid fa-pen';
                                } elseif ($aType === 'lock') {
                                    $actText = 'Khóa tài khoản';
                                    $actClass = 'badge-lock';
                                    $actIcon = 'fa-solid fa-lock';
                                } elseif ($aType === 'unlock' || $aType === 'restore') {
                                    $actText = ($aType === 'restore') ? 'Khôi phục' : 'Mở khóa';
                                    $actClass = 'badge-unlock';
                                    $actIcon = 'fa-solid fa-lock-open';
                                } elseif ($aType === 'delete' || $aType === 'force_delete') {
                                    $actText = 'Xóa dữ liệu';
                                    $actClass = 'badge-delete';
                                    $actIcon = 'fa-solid fa-trash';
                                } elseif ($aType === 'config') {
                                    $actText = 'Cấu hình';
                                    $actClass = 'badge-config';
                                    $actIcon = 'fa-solid fa-palette';
                                } elseif ($aType === 'notify') {
                                    $actText = 'Thông báo';
                                    $actClass = 'badge-notify';
                                    $actIcon = 'fa-solid fa-bullhorn';
                                } else {
                                    $actText = 'Thao tác';
                                    $actClass = 'badge-update';
                                    $actIcon = 'fa-solid fa-gear';
                                }

                                $timeObj = $act->created_at ? \Carbon\Carbon::parse($act->created_at) : now();
                                $searchContent = strtolower(
                                    ($act->title ?? '') . ' ' . 
                                    ($act->description ?? '') . ' ' . 
                                    ($act->target_name ?? '') . ' ' . 
                                    $actText
                                );

                                // Xác định URL và nút Thao tác tương ứng
                                $isLockAction = ($aType === 'lock');
                                $isRestoreAction = ($aType === 'delete');

                                $actionUrl = url('Admin');
                                $actionBtnText = 'Xem chi tiết';
                                $actionBtnIcon = 'fa-solid fa-arrow-up-right-from-square';

                                if ($cType === 'building') {
                                    $actionUrl = url('qltoa_nha');
                                    $actionBtnText = 'Xem tòa nhà';
                                    $actionBtnIcon = 'fa-solid fa-city';
                                } elseif ($cType === 'post') {
                                    $actionUrl = url('qlbai_dang/Overview');
                                    $actionBtnText = 'Xem bài đăng';
                                    $actionBtnIcon = 'fa-solid fa-newspaper';
                                } elseif ($cType === 'account') {
                                    $actionUrl = url('qltai_khoan_con');
                                    $actionBtnText = 'Xem tài khoản';
                                    $actionBtnIcon = 'fa-solid fa-users-gear';
                                } elseif ($cType === 'system') {
                                    $actionUrl = url('sua_giao_dien');
                                    $actionBtnText = 'Xem cấu hình';
                                    $actionBtnIcon = 'fa-solid fa-palette';
                                }
                            @endphp
                            <tr class="activity-row" data-search="{{ $searchContent }}">
                                <td style="text-align: center; font-weight: 700; color: var(--text-muted);">
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    <div class="time-box">
                                        <i class="fa-regular fa-clock" style="color: var(--text-muted); margin-right: 4px;"></i>
                                        {{ $timeObj->format('d/m/Y H:i') }}
                                    </div>
                                    <span style="font-size: 0.725rem; color: var(--brand-primary); font-weight: 600;">
                                        {{ $timeObj->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="act-badge {{ $actClass }}">
                                        <i class="{{ $actIcon }}"></i> {{ $actText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="title-text">{{ $act->title }}</div>
                                    <div class="desc-text">{{ $act->description }}</div>
                                    @if(!empty($act->target_name))
                                        <span class="target-tag">
                                            <i class="fa-solid fa-cube" style="font-size: 10px; color: var(--brand-primary);"></i>
                                            {{ $act->target_name }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($isLockAction && !empty($act->target_id))
                                        <form action="{{ url('qlnguoi_dung/unlock/' . $act->target_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Mở khóa cho tài khoản này?');">
                                            @csrf
                                            <button type="submit" class="btn-table-action btn-table-unlock">
                                                <i class="fa-solid fa-lock-open"></i> Mở khóa
                                            </button>
                                        </form>
                                    @elseif($isRestoreAction && !empty($act->target_id))
                                        <form action="{{ url('qlnguoi_dung/restore/' . $act->target_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Khôi phục tài khoản này?');">
                                            @csrf
                                            <button type="submit" class="btn-table-action btn-table-unlock">
                                                <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ $actionUrl }}" class="btn-table-action" title="Chuyển đến trang quản lý liên quan">
                                            <i class="{{ $actionBtnIcon }}"></i> <span>{{ $actionBtnText }}</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-box">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;">Chưa có hoạt động nào được ghi nhận</div>
                                        <p style="font-size: 0.8rem; margin-top: 4px;">Mọi hoạt động quản trị của Admin sẽ tự động xuất hiện tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="noResultsRow" style="display: none;">
                            <td colspan="5">
                                <div class="empty-box">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;">Không tìm thấy hoạt động nào phù hợp</div>
                                    <p style="font-size: 0.8rem; margin-top: 4px;">Thử nhập từ khóa tìm kiếm khác.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function filterSimpleTable() {
            const query = document.getElementById('simpleSearch').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#historyTableBody .activity-row');
            let matched = 0;

            rows.forEach(r => {
                const searchData = r.getAttribute('data-search') || '';
                if (!query || searchData.includes(query)) {
                    r.style.display = '';
                    matched++;
                } else {
                    r.style.display = 'none';
                }
            });

            const noRes = document.getElementById('noResultsRow');
            if (noRes) {
                noRes.style.display = (rows.length > 0 && matched === 0) ? '' : 'none';
            }
        }
    </script>
</body>
</html>