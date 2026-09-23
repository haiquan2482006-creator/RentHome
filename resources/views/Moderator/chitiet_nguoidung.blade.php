<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Tài Khoản - RentHome Moderator</title>

    <!-- Google Fonts & Icons -->
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
            --accent-amber: #d97706;
            --accent-red: #dc2626;
            --sidebar-width: 260px;
            --shadow-subtle: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 25px -5px rgba(2, 132, 199, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .moderator-container {
            margin-left: var(--sidebar-width);
            padding: 32px 40px;
            min-height: 100vh;
        }

        /* Top Navigation Back */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            transition: all 0.2s;
        }

        .btn-back:hover {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background: rgba(2, 132, 199, 0.05);
        }

        /* Alert Banner */
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

        /* Layout Grid */
        .details-layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        /* Card Component */
        .info-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        /* User Header Info */
        .user-profile-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-big-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.6rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .user-title-name {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-type {
            background: #e0f2fe;
            color: #0284c7;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Unified Table Styling */
        .user-details-table td {
            padding: 14px 24px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .user-details-table tbody tr:last-child td {
            border-bottom: none;
        }

        .table-label-col {
            width: 38%;
            color: var(--text-secondary);
            font-weight: 600;
            background: #ffffff;
        }

        .table-val-col {
            width: 62%;
            color: var(--text-primary);
        }

        .icon-col {
            margin-right: 8px;
            color: var(--brand-primary);
            width: 18px;
            text-align: center;
        }

        .badge-verified {
            background: #dcfce7;
            color: #15803d;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-doc-view {
            background: #f1f5f9;
            color: var(--brand-primary);
            border: 1px solid var(--border-color);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-doc-view:hover { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }

        /* Action Panel Styling */
        .action-panel-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            border: 2px solid rgba(2, 132, 199, 0.2);
            box-shadow: var(--shadow-subtle);
            position: sticky;
            top: 20px;
        }

        .action-panel-header {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .action-panel-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-panel-act {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-act-warn {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }
        .btn-act-warn:hover { background: #fde047; color: #78350f; }

        .btn-act-verify {
            background: rgba(2, 132, 199, 0.1);
            color: var(--brand-primary);
            border: 1px solid rgba(2, 132, 199, 0.3);
        }
        .btn-act-verify:hover { background: var(--brand-primary); color: #ffffff; }

        .btn-act-ban {
            background: #fef2f2;
            color: var(--accent-red);
            border: 1px solid #fca5a5;
        }
        .btn-act-ban:hover { background: var(--accent-red); color: #ffffff; }

        /* Posts Section */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .posts-grid { display: grid; gap: 20px; }

        .post-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            display: grid;
            grid-template-columns: 140px 1fr auto;
            gap: 20px;
            align-items: center;
            transition: all 0.2s;
        }

        .post-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: rgba(2, 132, 199, 0.3);
        }

        .post-thumb {
            width: 140px;
            height: 95px;
            border-radius: 12px;
            object-fit: cover;
        }

        .post-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .post-meta {
            display: flex;
            gap: 16px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .post-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--brand-primary);
        }

        .badge-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-active { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }

        .post-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 130px;
        }

        .btn-post-action {
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-post-view {
            background: rgba(2, 132, 199, 0.1);
            color: var(--brand-primary);
            border: 1px solid rgba(2, 132, 199, 0.2);
        }
        .btn-post-view:hover { background: var(--brand-primary); color: #ffffff; }

        .btn-post-remove {
            background: #ffffff;
            color: var(--accent-red);
            border: 1px solid rgba(220, 38, 38, 0.3);
        }
        .btn-post-remove:hover { background: var(--accent-red); color: #ffffff; }

        /* Modal Overlay & Card */
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
            font-size: 1.2rem;
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
            .details-layout-grid { grid-template-columns: 1fr; }
            .action-panel-card { position: static; }
            .post-card { grid-template-columns: 1fr; }
            .post-actions { flex-direction: row; }
        }
    </style>
</head>
<body>
    @include('partials.header-moderator')

    <main class="moderator-container">
        <!-- Back Link -->
        <a href="{{ url('/moderator/qlnguoidung') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Quay lại Danh sách Tài khoản
        </a>

        @if (session('success'))
            <div class="alert-banner">
                <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @php
            $name = $targetUser->account_name ?? $targetUser->username ?? 'Thành Viên';
            $firstChar = strtoupper(mb_substr($name, 0, 1));
        @endphp

        <!-- Main Details & Action Panel Grid -->
        <div class="details-layout-grid">
            <!-- Left Side: Gộp tất cả Thông tin Tài khoản vào 1 Bảng duy nhất -->
            <div>
                <div class="info-card">
                    <!-- Header Hồ sơ người dùng -->
                    <div style="padding: 20px 24px; background: #ffffff; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                        <div class="user-profile-header">
                            <div class="user-big-avatar">{{ $firstChar }}</div>
                            <div>
                                <div class="user-title-name">
                                    {{ $name }}
                                    <span class="badge-type">{{ ucfirst($targetUser->account_type ?? 'Doanh nghiệp') }}</span>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">
                                    @ {{ $targetUser->username ?? 'takimanh531' }}
                                </div>
                            </div>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">
                            <i class="fa-solid fa-calendar-day" style="color: var(--brand-primary); margin-right: 4px;"></i> Ngày đăng ký: <strong>{{ $targetUser->created_at ?? '10/09/2026' }}</strong>
                        </div>
                    </div>

                    <!-- Bảng dữ liệu gộp tất cả thông tin -->
                    <table class="user-details-table" style="width: 100%; border-collapse: collapse;">
                        <!-- SECT 1: ĐỊNH DANH -->
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
                                <th colspan="2" style="padding: 12px 24px; text-align: left; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--brand-primary); letter-spacing: 0.03em;">
                                    <i class="fa-solid fa-id-card" style="margin-right: 6px;"></i> 1. Thông Tin Định Danh & Trạng Thái Xác Thực
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-user icon-col"></i> Họ tên / Tên thương hiệu</td>
                                <td class="table-val-col"><strong>{{ $name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-envelope icon-col"></i> Địa chỉ Email</td>
                                <td class="table-val-col">{{ $targetUser->email }}</td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-phone icon-col"></i> Số điện thoại liên hệ</td>
                                <td class="table-val-col">{{ $targetUser->phone ?? '06524543456' }}</td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-address-card icon-col"></i> Căn cước công dân (CCCD)</td>
                                <td class="table-val-col">
                                    <span style="font-weight: 700; margin-right: 12px;">Mã số: {{ $targetUser->cccd_number ?? '079201089921' }}</span>
                                    <button type="button" class="btn-doc-view" onclick="openCccdModal()">
                                        <i class="fa-solid fa-image"></i> Xem CCCD mặt trước/sau
                                    </button>
                                </td>
                            </tr>

                            <!-- SECT 2: LỊCH SỬ & UY TÍN -->
                            <tr style="background: #f8fafc; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                                <th colspan="2" style="padding: 12px 24px; text-align: left; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--brand-primary); letter-spacing: 0.03em;">
                                    <i class="fa-solid fa-shield-heart" style="margin-right: 6px;"></i> 2. Lịch Sử & Độ Uy Tín (Lý Lịch Trích Ngang)
                                </th>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-calendar-day icon-col"></i> Ngày tham gia hệ thống</td>
                                <td class="table-val-col">{{ $targetUser->created_at ?? '10/09/2026' }} (Thành viên 4 ngày)</td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-triangle-exclamation icon-col"></i> Thống kê vi phạm / Khiếu nại</td>
                                <td class="table-val-col">
                                    <span style="color: var(--accent-emerald); font-weight: 700;">0 lần bị khiếu nại</span> • 
                                    <span style="color: var(--accent-emerald); font-weight: 700;">0 lần bị cảnh cáo</span>
                                </td>
                            </tr>

                            <!-- SECT 3: DOANH NGHIỆP & NGÂN HÀNG -->
                            <tr style="background: #f8fafc; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                                <th colspan="2" style="padding: 12px 24px; text-align: left; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--brand-primary); letter-spacing: 0.03em;">
                                    <i class="fa-solid fa-building" style="margin-right: 6px;"></i> 3. Thông Tin Doanh Nghiệp & Tài Khoản Ngân Hàng
                                </th>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-building-flag icon-col"></i> Tên doanh nghiệp</td>
                                <td class="table-val-col"><strong>{{ $targetUser->company_name ?? 'Công ty TNHH Bất Động Sản TakiManh Việt Nam' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-file-invoice icon-col"></i> Mã số thuế (MST)</td>
                                <td class="table-val-col"><span style="font-family: monospace; font-size: 0.95rem; font-weight: 700;">{{ $targetUser->tax_code ?? '0316988231' }}</span></td>
                            </tr>
                            <tr>
                                <td class="table-label-col"><i class="fa-solid fa-file-contract icon-col"></i> Giấy phép ĐKKD</td>
                                <td class="table-val-col">
                                    <button type="button" class="btn-doc-view" onclick="openLicenseModal()">
                                        <i class="fa-solid fa-file-image"></i> Xem giấy phép kinh doanh
                                    </button>
                                </td>
                            </tr>
                            <tr style="background: #f0f9ff;">
                                <td class="table-label-col" style="color: #0369a1;"><i class="fa-solid fa-building-columns icon-col" style="color: #0284c7;"></i> Ngân hàng liên kết</td>
                                <td class="table-val-col" style="color: #0369a1;"><strong>{{ $targetUser->bank_name ?? 'Ngân hàng TMCP Quân Đội (MB Bank)' }}</strong></td>
                            </tr>
                            <tr style="background: #f0f9ff;">
                                <td class="table-label-col" style="color: #0369a1;"><i class="fa-solid fa-credit-card icon-col" style="color: #0284c7;"></i> Số tài khoản ngân hàng</td>
                                <td class="table-val-col" style="color: #0369a1; font-family: monospace; font-size: 1rem; font-weight: 800;">{{ $targetUser->bank_account_no ?? '999988886666' }}</td>
                            </tr>
                            <tr style="background: #f0f9ff;">
                                <td class="table-label-col" style="color: #0369a1;"><i class="fa-solid fa-user-check icon-col" style="color: #0284c7;"></i> Tên chủ tài khoản</td>
                                <td class="table-val-col" style="color: #0369a1;"><strong>{{ $targetUser->bank_account_holder ?? 'CONG TY TNHH BDS TAKIMANH VIET NAM' }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Side: Action Panel dành riêng cho Kiểm duyệt viên -->
            <div>
                <div class="action-panel-card">
                    <div class="action-panel-header">
                        <i class="fa-solid fa-user-shield" style="color: var(--brand-primary);"></i>
                        Khu Vực Hành Động (Action Panel)
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 16px;">
                        Bộ công cụ quản trị dành riêng cho Kiểm duyệt viên xử lý tài khoản
                    </p>

                    <div class="action-panel-btns">
                        <!-- 1. Nút Gửi cảnh báo vi phạm -->
                        <button type="button" class="btn-panel-act btn-act-warn" onclick="openModal('warnModal')">
                            <i class="fa-solid fa-triangle-exclamation"></i> Gửi cảnh báo vi phạm
                        </button>

                        <!-- 2. Nút Yêu cầu cập nhật thông tin -->
                        <button type="button" class="btn-panel-act btn-act-verify" onclick="openModal('reqVerifyModal')">
                            <i class="fa-solid fa-user-clock"></i> Yêu cầu cập nhật CCCD/Giấy phép
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Tabs cho Tòa Nhà & Bài Đăng -->
        <div class="user-tabs" style="display: flex; gap: 16px; margin-top: 40px; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0;">
            <button class="tab-btn" id="tab-btn-buildings" onclick="switchTab('buildings')" style="background: none; border: none; font-size: 1rem; font-weight: 700; color: var(--brand-primary); padding: 12px 20px; border-bottom: 3px solid var(--brand-primary); cursor: pointer; transition: all 0.2s;">
                <i class="fa-solid fa-building" style="margin-right: 6px;"></i> Tòa Nhà & Khu Đô Thị
            </button>
            <button class="tab-btn" id="tab-btn-posts" onclick="switchTab('posts')" style="background: none; border: none; font-size: 1rem; font-weight: 700; color: var(--text-muted); padding: 12px 20px; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.2s;">
                <i class="fa-solid fa-layer-group" style="margin-right: 6px;"></i> Bài Đăng Lẻ
            </button>
        </div>

        <!-- Tab 1: Tòa Nhà -->
        <div id="tab-content-buildings" class="tab-content" style="display: block;">
            <div class="buildings-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                @forelse($buildings as $building)
                    <div class="building-card" style="background: #fff; border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <img src="{{ !empty($building->image) ? (Str::startsWith($building->image, 'http') ? $building->image : asset('storage/' . $building->image)) : 'https://placehold.co/600x400?text=Building' }}" alt="{{ $building->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        <div style="padding: 16px; display: flex; flex-direction: column; flex: 1;">
                            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 0 0 8px 0; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ $building->name }}</h3>
                            
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0 0 12px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <i class="fa-solid fa-location-dot" style="color: var(--brand-primary); margin-right: 4px;"></i> 
                                {{ implode(', ', array_filter([$building->address_detail ?? '', $building->ward ?? '', $building->district ?? '', $building->province ?? ''])) ?: 'Đang cập nhật địa chỉ' }}
                            </p>
                            
                            <div style="margin-top: auto; margin-bottom: 16px;">
                                <span style="display: inline-flex; align-items: center; padding: 6px 12px; background: #f3e8ff; color: #7e22ce; font-size: 0.75rem; font-weight: 700; border-radius: 8px;">
                                    <i class="fa-solid fa-door-open" style="margin-right: 4px;"></i> {{ $building->total_rooms ?? 0 }} phòng
                                </span>
                                <span style="display: inline-flex; align-items: center; padding: 6px 12px; background: #e0f2fe; color: #0369a1; font-size: 0.75rem; font-weight: 700; border-radius: 8px; margin-left: 6px;">
                                    {{ $building->type ?? 'Tòa nhà' }}
                                </span>
                            </div>
                            
                            <div style="display: flex; gap: 8px; margin-top: auto;">
                                <a href="{{ url('/moderator/toa-nha/' . ($building->id ?? $building->_id)) }}" target="_blank" style="flex: 1; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; border: none; cursor: pointer; transition: background 0.2s; background: #e0f2fe; color: #0284c7; text-decoration: none;">
                                    <i class="fa-solid fa-eye" style="margin-right: 6px;"></i> Xem các phòng
                                </a>
                                <button type="button" class="btn-act-warn" style="flex: 1; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; border: none; cursor: pointer; transition: background 0.2s; background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;" onclick="openWarnBuildingModal('{{ $building->id ?? $building->_id }}', '{{ addslashes($building->name) }}')">
                                    <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Khóa tòa nhà
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; background: #fff; padding: 40px; border-radius: 16px; text-align: center; color: var(--text-muted); border: 1px solid var(--border-color);">
                        <i class="fa-solid fa-building-circle-xmark" style="font-size: 2.5rem; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
                        Tài khoản này chưa tạo tòa nhà nào trên hệ thống.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Bài Đăng Lẻ -->
        <div id="tab-content-posts" class="tab-content" style="display: none;">
            <div class="section-header" style="margin-top: 0;">
                <h2 class="section-title">
                    <i class="fa-solid fa-layer-group" style="color: var(--brand-primary);"></i>
                    Lịch Sử Bài Đăng Của {{ $name }}
                </h2>
                <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 600;">Tổng cộng {{ count($posts) }} tin đã đăng</span>
            </div>

            <div class="posts-grid">
                @forelse($posts as $post)
                    <div class="post-card" id="post-card-{{ $post->id }}">
                        <img src="{{ $post->thumb }}" alt="{{ $post->title }}" class="post-thumb">
                        
                        <div>
                            <div class="post-title">
                                {{ $post->title }}
                                @if($post->status === 'active')
                                    <span class="badge-status-pill badge-active"><i class="fa-solid fa-circle-check"></i> Đang hiển thị</span>
                                @else
                                    <span class="badge-status-pill badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                                @endif
                            </div>
                            <div class="post-meta">
                                <span><i class="fa-solid fa-location-dot"></i> {{ $post->address }}</span>
                                <span><i class="fa-solid fa-clock"></i> {{ $post->created_at }}</span>
                                <span><i class="fa-solid fa-hashtag"></i> ID: #{{ $post->id }}</span>
                            </div>
                            <div class="post-price">{{ $post->price }}</div>
                        </div>

                        <div class="post-actions">
                            <button type="button" class="btn-post-action btn-post-view" onclick="openPreviewModal('{{ $post->id }}', '{{ addslashes($post->title) }}', '{{ addslashes($post->address) }}', '{{ addslashes($post->price) }}', '{{ $post->thumb }}')">
                                <i class="fa-solid fa-eye"></i> Xem
                            </button>

                            <button type="button" class="btn-post-action btn-post-remove" onclick="openRemoveModal('{{ $post->id }}', '{{ addslashes($post->title) }}')">
                                <i class="fa-solid fa-trash-can"></i> Gỡ bài
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="background: #fff; padding: 40px; border-radius: 16px; text-align: center; color: var(--text-muted); border: 1px solid var(--border-color);">
                        <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; color: #cbd5e1; display: block;"></i>
                        Tài khoản này chưa đăng bài viết nào trên hệ thống.
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Modal Xem CCCD -->
    <div class="modal-overlay" id="cccdModal">
        <div class="modal-card" style="max-width: 680px;">
            <h2 class="modal-title" style="display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa-solid fa-id-card" style="color: var(--brand-primary); margin-right: 8px;"></i> Căn Cước Công Dân (CCCD) Xác Thực</span>
                <button onclick="closeModal('cccdModal')" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#64748b;"><i class="fa-solid fa-xmark"></i></button>
            </h2>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="font-size:0.8rem; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Mặt trước CCCD</label>
                    <img src="{{ $targetUser->cccd_front }}" alt="Mặt trước CCCD" style="width:100%; height:160px; object-fit:cover; border-radius:12px; border:1px solid #cbd5e1;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Mặt sau CCCD</label>
                    <img src="{{ $targetUser->cccd_back }}" alt="Mặt sau CCCD" style="width:100%; height:160px; object-fit:cover; border-radius:12px; border:1px solid #cbd5e1;">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('cccdModal')">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Modal Xem Giấy phép kinh doanh -->
    <div class="modal-overlay" id="licenseModal">
        <div class="modal-card" style="max-width: 600px;">
            <h2 class="modal-title" style="display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa-solid fa-file-contract" style="color: var(--brand-primary); margin-right: 8px;"></i> Giấy Phép Đăng Ký Kinh Doanh</span>
                <button onclick="closeModal('licenseModal')" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#64748b;"><i class="fa-solid fa-xmark"></i></button>
            </h2>

            <img src="{{ $targetUser->license_image }}" alt="Giấy phép ĐKKD" style="width:100%; height:280px; object-fit:cover; border-radius:12px; border:1px solid #cbd5e1; margin-bottom:20px;">

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('licenseModal')">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Modal 1: Gửi cảnh báo vi phạm -->
    <div class="modal-overlay" id="warnModal">
        <div class="modal-card">
            <h2 class="modal-title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-amber); margin-right: 8px;"></i> Gửi Cảnh Báo Vi Phạm</h2>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 14px;">
                Thông báo/email nhắc nhở sẽ được gửi trực tiếp tới tài khoản <strong>{{ $name }}</strong>.
            </p>

            <form action="{{ url('/moderator/user-warn/' . $targetUser->id) }}" method="POST">
                @csrf
                <label style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">Nội dung cảnh báo vi phạm:</label>
                <textarea name="warning_message" class="form-textarea" placeholder="Nhập nội dung nhắc nhở (ví dụ: Tin đăng sai giá thực tế, đăng lặp bài, thái độ làm việc với khách xem phòng...)" required></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('warnModal')">Hủy bỏ</button>
                    <button type="submit" class="btn-panel-act btn-act-warn" style="width:auto; padding:10px 18px;"><i class="fa-solid fa-paper-plane"></i> Gửi cảnh báo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Yêu cầu cập nhật thông tin -->
    <div class="modal-overlay" id="reqVerifyModal">
        <div class="modal-card">
            <h2 class="modal-title"><i class="fa-solid fa-user-clock" style="color: var(--brand-primary); margin-right: 8px;"></i> Yêu Cầu Cập Nhật Thông Tin</h2>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 14px;">
                Bắt buộc tài khoản <strong>{{ $name }}</strong> bổ sung hồ sơ xác minh CCCD/Giấy phép kinh doanh.
            </p>

            <form action="{{ url('/moderator/user-request-verify/' . $targetUser->id) }}" method="POST">
                @csrf
                <label style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">Yêu cầu chi tiết cần bổ sung:</label>
                <textarea name="verify_note" class="form-textarea" placeholder="Nhập yêu cầu (ví dụ: Ảnh CCCD mặt sau bị mờ, Yêu cầu gửi lại Giấy phép kinh doanh bản rõ nét hơn...)" required></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('reqVerifyModal')">Hủy bỏ</button>
                    <button type="submit" class="btn-panel-act btn-act-verify" style="width:auto; padding:10px 18px;"><i class="fa-solid fa-paper-plane"></i> Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Preview Bài Đăng -->
    <div class="modal-overlay" id="previewModal">
        <div class="modal-card" style="max-width: 640px;">
            <h2 class="modal-title" style="display:flex; justify-content:space-between; align-items:center;">
                <span><i class="fa-solid fa-file-lines" style="color: var(--brand-primary); margin-right: 8px;"></i> Chi Tiết Bài Đăng</span>
                <button onclick="closeModal('previewModal')" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#64748b;"><i class="fa-solid fa-xmark"></i></button>
            </h2>

            <img id="previewImage" src="" alt="" style="width: 100%; height: 220px; object-fit: cover; border-radius: 14px; margin-bottom: 16px;">
            <h3 id="previewTitle" style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin-bottom: 10px;"></h3>
            <p id="previewAddress" style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 8px;"><i class="fa-solid fa-location-dot" style="color: var(--brand-primary);"></i> <span></span></p>
            <div id="previewPrice" style="font-size: 1.2rem; font-weight: 800; color: var(--brand-primary); margin-bottom: 16px;"></div>

            <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                <strong>Mô tả chi tiết:</strong> Phòng/Nhà cho thuê chính chủ, không qua trung gian. Đầy đủ tiện ích điện nước sinh hoạt, camera an ninh 24/7, nằm gần các trường đại học & siêu thị tiện lợi.
            </p>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('previewModal')">Đóng cửa sổ</button>
            </div>
        </div>
    </div>

    <!-- Modal Gỡ Bài Đăng -->
    <div class="modal-overlay" id="removeModal">
        <div class="modal-card">
            <h2 class="modal-title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-red); margin-right: 8px;"></i> Xác Nhận Gỡ Bài Đăng</h2>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 14px;" id="removeItemTitle"></p>

            <form id="removeForm" method="POST">
                @csrf
                <label style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 6px;">Lý do gỡ bài đăng (sẽ thông báo cho chủ tài khoản):</label>
                <textarea name="reason" class="form-textarea" placeholder="Nhập lý do chi tiết (ví dụ: Tin ảo, bài trùng lặp, thông tin sai sự thật...)" required></textarea>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('removeModal')">Hủy bỏ</button>
                    <button type="submit" class="btn-post-action btn-post-remove" style="padding: 10px 18px;"><i class="fa-solid fa-trash-can"></i> Xác nhận gỡ bài</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function openCccdModal() {
            openModal('cccdModal');
        }

        function openLicenseModal() {
            openModal('licenseModal');
        }

        function openPreviewModal(id, title, address, price, thumb) {
            document.getElementById('previewTitle').innerText = title + ' (ID: #' + id + ')';
            document.getElementById('previewAddress').querySelector('span').innerText = address;
            document.getElementById('previewPrice').innerText = price;
            document.getElementById('previewImage').src = thumb;
            openModal('previewModal');
        }

        function openRemoveModal(id, title) {
            document.getElementById('removeItemTitle').innerText = 'Bài đăng: "' + title + '" (ID: #' + id + ')';
            document.getElementById('removeForm').action = '{{ url("/moderator/remove-post") }}/' + id;
            openModal('removeModal');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Reset tab buttons style
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.color = 'var(--text-muted)';
                btn.style.borderBottomColor = 'transparent';
            });
            
            // Show selected tab
            document.getElementById('tab-content-' + tabId).style.display = 'block';
            const activeBtn = document.getElementById('tab-btn-' + tabId);
            if (activeBtn) {
                activeBtn.style.color = 'var(--brand-primary)';
                activeBtn.style.borderBottomColor = 'var(--brand-primary)';
            }
        }

        function openWarnBuildingModal(id, name) {
            // Placeholder for opening building warning modal
            alert('Chức năng khóa tòa nhà "' + name + '" (ID: ' + id + ') đang được cập nhật!');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>
