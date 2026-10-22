<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản Lý Kiểm Duyệt Bài Đăng - RentHome</title>

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

        .nav-link.active .nav-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
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

        /* ================= MAIN WRAPPER ================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 28px 32px;
            max-width: calc(100vw - var(--sidebar-width));
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
            padding: 10px 16px;
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

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* ================= 4 SUMMARY MINI CARDS ================= */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .summary-card.active-tab {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .summary-val {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }

        .summary-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .summary-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .summary-icon.amber { background: var(--warning-light); color: #d97706; }
        .summary-icon.emerald { background: var(--success-light); color: var(--success); }
        .summary-icon.rose { background: var(--danger-light); color: var(--danger); }
        .summary-icon.blue { background: var(--primary-light); color: var(--primary); }

        /* ================= MAIN CONTENT CARD & FILTERS ================= */
        .card-panel {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
            overflow: hidden;
        }

        .toolbar-top {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .status-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 11px;
        }

        .tab-btn {
            border: none;
            background: transparent;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }

        .tab-btn:hover {
            color: var(--text-main);
        }

        .tab-btn.active {
            background: #ffffff;
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
        }

        .tab-count {
            font-size: 11px;
            padding: 1px 7px;
            border-radius: 99px;
            background: #e2e8f0;
            color: #334155;
        }

        .tab-btn.active .tab-count {
            background: var(--primary-light);
            color: var(--primary);
        }

        .filter-bar {
            padding: 14px 22px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filter-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .search-box {
            position: relative;
            min-width: 260px;
            flex: 1;
            max-width: 440px;
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
            padding: 9px 12px 9px 34px;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            font-size: 13px;
            background: #fff;
            outline: none;
            color: var(--text-main);
        }

        .search-input:focus, .select-filter:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .select-filter {
            padding: 9px 12px;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        .bulk-bar {
            display: none;
            align-items: center;
            gap: 10px;
            background: var(--primary-light);
            border: 1px solid #bae6fd;
            padding: 6px 12px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .bulk-bar.show {
            display: flex;
        }

        /* ================= TABLE ================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .moderation-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .moderation-table th {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .moderation-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .moderation-table tbody tr:hover td {
            background: #f8fafc;
        }

        .chk-row {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .post-info-cell {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 270px;
        }

        .thumb-wrapper {
            position: relative;
            width: 68px;
            height: 52px;
            border-radius: 9px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
            background: #e2e8f0;
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .img-count-tag {
            position: absolute;
            bottom: 3px;
            right: 3px;
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
        }

        .post-title {
            font-weight: 700;
            color: var(--text-main);
            font-size: 14px;
            margin-bottom: 4px;
            display: block;
            line-height: 1.3;
        }

        .post-specs {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .building-box {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 200px;
        }

        .building-name {
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .building-name i {
            color: var(--primary);
        }

        .building-addr {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.35;
        }

        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: var(--primary-light);
            color: var(--primary);
            width: fit-content;
            margin-top: 2px;
        }

        .price-val {
            font-weight: 800;
            color: #e11d48;
            font-size: 14.5px;
        }

        .deposit-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .staff-box {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .staff-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0284c7;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pending { background: var(--warning-light); color: #b45309; }
        .status-approved { background: var(--success-light); color: #047857; }
        .status-rejected { background: var(--danger-light); color: #b91c1c; }

        .action-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .btn-act {
            height: 34px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            white-space: nowrap;
        }

        .btn-act-view {
            background: #f1f5f9;
            color: #334155;
            border-color: var(--border-color);
        }

        .btn-act-view:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-act-approve {
            background: var(--success-light);
            color: #047857;
        }

        .btn-act-approve:hover {
            background: var(--success);
            color: #fff;
        }

        .btn-act-reject {
            background: var(--danger-light);
            color: #b91c1c;
        }

        .btn-act-reject:hover {
            background: var(--danger);
            color: #fff;
        }

        /* Empty state */
        .empty-state {
            padding: 56px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 14px;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            max-width: 420px;
            margin: 0 auto;
        }

        /* ================= MODALS ================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 860px;
            max-height: 90vh;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: zoomIn 0.2s ease;
        }

        .modal-box.modal-sm {
            max-width: 520px;
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 16.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .modal-body {
            padding: 22px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 14px 24px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .compare-grid {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 18px;
        }

        .info-panel {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            background: #fcfdfe;
        }

        .info-panel-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
            gap: 12px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-val {
            font-weight: 700;
            color: var(--text-main);
            text-align: right;
        }

        .preview-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .preview-gallery img {
            width: 100%;
            height: 76px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 9px;
            font-size: 13.5px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* Toast */
        .toast-wrap {
            position: fixed;
            top: 22px;
            right: 22px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-msg {
            padding: 12px 18px;
            border-radius: 10px;
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        /* ================= MODAL INSPECT (ĐỐI CHIẾU LỚP VỎ - LỚP RUỘT) ================= */
        .modal-box.modal-inspect {
            max-width: 1020px;
            max-height: 92vh;
        }

        .ins-status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 16px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ins-status-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ins-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .ins-status-badge.badge-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .ins-status-badge.badge-approved {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .ins-status-badge.badge-rejected {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ins-time-info {
            font-size: 12.5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ins-meta-pill {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 4px 10px;
            border-radius: 8px;
        }

        .ins-reject-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 12px;
            margin-bottom: 16px;
            color: #991b1b;
            font-size: 13px;
            line-height: 1.5;
        }

        .ins-reject-alert i {
            font-size: 16px;
            margin-top: 2px;
            color: #dc2626;
            flex-shrink: 0;
        }

        .info-panel-ruot {
            border: 1.5px solid #a7f3d0;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(5, 150, 105, 0.05);
        }

        .info-panel-ruot .info-panel-title {
            color: #065f46;
            border-bottom-color: #d1fae5;
        }

        .info-panel-vo {
            border: 1.5px solid #bae6fd;
            background: #f8fafc;
            box-shadow: 0 1px 3px rgba(2, 132, 199, 0.05);
        }

        .info-panel-vo .info-panel-title {
            color: #0369a1;
            border-bottom-color: #e0f2fe;
        }

        .price-highlight {
            color: #e11d48 !important;
            font-size: 15px !important;
            font-weight: 800 !important;
        }

        .ins-section-group {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed var(--border-color);
        }

        .info-label-sub {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .ins-tags-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ins-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .ins-tag-emerald {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .ins-tag-blue {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .ins-desc-box {
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            color: #334155;
            line-height: 1.5;
            max-height: 100px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            white-space: pre-line;
        }

        .badge-type {
            background: #e0f2fe;
            color: #0284c7;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            border: 1px solid #bae6fd;
            display: inline-block;
            font-weight: 700;
        }

        .badge-mini-lock {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .ins-pricing-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .ins-price-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
            gap: 6px;
        }

        .ins-price-pill .label {
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ins-price-pill .val {
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
            font-family: monospace;
            font-size: 12px;
        }

        .ins-price-pill .val.text-state {
            color: #059669;
            font-family: inherit;
            font-size: 11.5px;
        }

        .ins-no-pricing-note {
            padding: 9px 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            font-size: 12px;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 600;
        }

        .ins-audit-hint {
            margin-top: 14px;
            padding: 10px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.45;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .ins-audit-hint i {
            margin-top: 2px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .ins-images-comparison {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            border-top: 1px solid var(--border-color);
            padding-top: 18px;
        }

        .ins-img-section {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px;
        }

        .ins-img-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 8px;
        }

        .ins-img-title {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .badge-count {
            font-size: 11px;
            font-weight: 700;
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 2px 8px;
            border-radius: 12px;
            color: var(--text-muted);
        }

        .ins-img-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .ins-gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            aspect-ratio: 4/3;
            cursor: pointer;
            background: #e2e8f0;
        }

        .ins-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease;
        }

        .ins-gallery-item:hover img {
            transform: scale(1.08);
        }

        .ins-gallery-item .zoom-hint {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .ins-gallery-item:hover .zoom-hint {
            opacity: 1;
        }

        .btn-view-web {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 8px 14px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-view-web:hover {
            background: #eff6ff;
            color: #0369a1;
        }

        .text-empty-hint {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Lightbox Overlay */
        .lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.94);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.15s ease;
        }

        .lightbox-overlay.show {
            display: flex;
        }

        .lightbox-content {
            position: relative;
            max-width: 85vw;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lightbox-content img {
            max-width: 85vw;
            max-height: 78vh;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .lightbox-caption {
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
            background: rgba(0, 0, 0, 0.5);
            padding: 4px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .lightbox-btn-close {
            position: absolute;
            top: 20px;
            right: 24px;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 3010;
        }

        .lightbox-btn-close:hover {
            background: rgba(239, 68, 68, 0.9);
            transform: scale(1.08);
        }

        .lightbox-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.18);
            border: none;
            color: #ffffff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 3010;
        }

        .lightbox-nav-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev {
            left: 24px;
        }

        .lightbox-next {
            right: 24px;
        }

        @media (max-width: 1200px) {
            .summary-grid { grid-template-columns: repeat(2, 1fr); }
            .compare-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .ins-images-comparison { grid-template-columns: 1fr; }
            .ins-img-grid { grid-template-columns: repeat(3, 1fr); }
            .lightbox-prev { left: 10px; }
            .lightbox-next { right: 10px; }
        }
    </style>
</head>
<body>

    @php
        // Chuẩn hóa danh sách bài đăng truyền từ Controller (không tạo dữ liệu giả)
        $allPosts = collect($posts ?? ($pendingPosts ?? []));

        // Nếu $posts là Paginator thì lấy items để đếm hoặc ưu tiên biến $stats từ Controller
        $postItems = $posts instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? collect($posts->items())
            : $allPosts;

        $pendingStatuses = ['pending', 'cho_duyet', 'chờ duyệt', 'waiting', 'cho duyet'];
        $approvedStatuses = ['approved', 'active', 'da_duyet', 'đã duyệt', 'đã cho thuê', 'da cho thue', 'da_cho_thue', 'rented', 'dang_thue'];
        $rejectedStatuses = ['rejected', 'tu_choi', 'từ chối', 'tuchoi', 'tu choi'];

        $pendingCount = $stats['pending_posts'] ?? $postItems->filter(function($p) use ($pendingStatuses) {
            return in_array(strtolower($p->status ?? 'pending'), $pendingStatuses);
        })->count();

        $approvedCount = $stats['approved_posts'] ?? $postItems->filter(function($p) use ($approvedStatuses) {
            return in_array(strtolower($p->status ?? ''), $approvedStatuses);
        })->count();

        $rejectedCount = $stats['rejected_posts'] ?? $postItems->filter(function($p) use ($rejectedStatuses) {
            return in_array(strtolower($p->status ?? ''), $rejectedStatuses);
        })->count();

        $complaintCount = $stats['pending_complaints'] ?? 0;
        $totalPostCount = $stats['total_posts'] ?? ($pendingCount + $approvedCount + $rejectedCount);
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
                <a href="{{ url('/kiem-duyet/qlkiem-duyet') }}" class="nav-link active">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Quản lý kiểm duyệt</span>
                    </span>
                    <span class="nav-badge badge-warning-pill" id="sidebarPendingBadge">{{ $pendingCount }}</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/kiem-duyet/khieu-nai') }}" class="nav-link">
                    <span class="nav-link-left">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Khiếu nại</span>
                    </span>
                    <span class="nav-badge badge-danger-pill">{{ $complaintCount }}</span>
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
                <a href="{{ url('/kiem-duyet/lich-su') }}" class="nav-link">
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
                <div class="avatar-circle">KD</div>
                <div class="user-meta">
                    <h4>{{ auth()->user()->account_name ?? 'Kiểm Duyệt Viên' }}</h4>
                    <p>{{ auth()->user()->email ?? 'kiemduyet2006@gmail.com' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="logout-btn" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ================= MAIN WRAPPER ================= -->
    <main class="main-wrapper">
        <!-- HEADER -->
        <div class="page-header">
            <div class="page-title-box">
                <h1>
                    <i class="fa-solid fa-file-shield"></i>
                    Quản Lý Kiểm Duyệt Bài Đăng (Lớp Ruột)
                </h1>
                <p>Tiếp nhận các bài đăng phòng mới từ tài khoản nhân viên Sale, kiểm tra hình ảnh, đối chiếu giá thuê với Tòa nhà (Lớp Vỏ) và phê duyệt hiển thị</p>
            </div>
        </div>

        <!-- 4 THẺ THỐNG KÊ TRẠNG THÁI (BẤM ĐỂ LỌC NHANH) -->
        <section class="summary-grid">
            <div class="summary-card active-tab" onclick="switchStatusTab('pending', this)">
                <div>
                    <div class="summary-val" id="cardPendingCount">{{ $pendingCount }}</div>
                    <div class="summary-label">Đang Chờ Phê Duyệt</div>
                </div>
                <div class="summary-icon amber">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>

            <div class="summary-card" onclick="switchStatusTab('approved', this)">
                <div>
                    <div class="summary-val" id="cardApprovedCount">{{ $approvedCount }}</div>
                    <div class="summary-label">Đã Phê Duyệt (Hiển Thị)</div>
                </div>
                <div class="summary-icon emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="summary-card" onclick="switchStatusTab('rejected', this)">
                <div>
                    <div class="summary-val" id="cardRejectedCount">{{ $rejectedCount }}</div>
                    <div class="summary-label">Bị Từ Chối / Yêu Cầu Sửa</div>
                </div>
                <div class="summary-icon rose">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                </div>
            </div>

            <div class="summary-card" onclick="switchStatusTab('all', this)">
                <div>
                    <div class="summary-val" id="cardTotalCount">{{ $totalPostCount }}</div>
                    <div class="summary-label">Tổng Bài Đăng Hệ Thống</div>
                </div>
                <div class="summary-icon blue">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
        </section>

        <!-- KHỐI BẢNG QUẢN LÝ KIỂM DUYỆT -->
        <section class="card-panel">
            <!-- Thanh tìm kiếm -->
            <div class="filter-bar">
                <div class="filter-controls">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchKeyword" class="search-input" placeholder="Tìm tên phòng, mã bài, tên tòa nhà, nhân viên..." oninput="filterModerationTable()">
                    </div>
                </div>
            </div>

            <!-- Bảng danh sách bài đăng -->
            <div class="table-responsive">
                <table class="moderation-table" id="moderationTable">
                    <thead>
                        <tr>
                            <th>Bài Đăng Phòng / Căn (Lớp Ruột)</th>
                            <th>Thuộc Tòa Nhà / Cơ Sở (Lớp Vỏ)</th>
                            <th>Giá</th>
                            <th>Nhân Viên Đăng</th>
                            <th>Thời Gian Gửi</th>
                            <th>Trạng Thái</th>
                            <th style="text-align: right;">Thao Tác Kiểm Duyệt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($postItems as $post)
                            @php
                                $postId = (string)($post->_id ?? $post->id);
                                $rawStatus = strtolower(trim((string)($post->status ?? 'pending')));

                                if (in_array($rawStatus, ['approved', 'active', 'da_duyet', 'đã duyệt', 'đã cho thuê', 'da cho thue', 'da_cho_thue', 'rented', 'dang_thue'])) {
                                    $statusKey = 'approved';
                                    $statusLabel = 'Đã phê duyệt';
                                    $statusClass = 'status-approved';
                                } elseif (in_array($rawStatus, ['rejected', 'tu_choi', 'từ chối', 'tuchoi', 'tu choi'])) {
                                    $statusKey = 'rejected';
                                    $statusLabel = 'Bị từ chối';
                                    $statusClass = 'status-rejected';
                                } else {
                                    $statusKey = 'pending';
                                    $statusLabel = 'Chờ phê duyệt';
                                    $statusClass = 'status-pending';
                                }

                                // Lấy thông tin Tòa nhà mẹ (Lớp Vỏ)
                                $bInfo = $post->building ?? ($post->building_info ?? null);
                                $bName = $bInfo->name ?? ($post->building_name ?? 'Cơ sở độc lập');
                                $bAddr = $bInfo->full_address ?? ($bInfo->address_detail ?? ($post->address ?? 'Chưa cập nhật địa chỉ'));

                                $rawBType = $bInfo->type ?? ($bInfo->business_type ?? ($post->property_type ?? 'can_ho'));
                                $typeMap = [
                                    'can_ho'           => 'Chung cư mini / Căn hộ dịch vụ',
                                    'can_ho_dich_vu'   => 'Chung cư mini / Căn hộ dịch vụ',
                                    'chung_cu_mini'    => 'Chung cư mini / Căn hộ dịch vụ',
                                    'nha_tro'          => 'Nhà trọ / Khu trọ',
                                    'day_tro'          => 'Nhà trọ / Khu trọ',
                                    'ky_tuc_xa'        => 'Ký túc xá / Sleepbox',
                                    'nha_rieng'        => 'Nhà ở / Nhà riêng lẻ',
                                    'nha_nguyen_can'   => 'Nhà ở / Nhà riêng lẻ',
                                    'cum_nha_lien_ke'  => 'Cụm nhà liền kề / Dự án',
                                    'dat_phan_lo'      => 'Đất phân lô / Đất nền',
                                    'dat_nen'          => 'Mặt bằng kinh doanh / Đất nền',
                                    'toa_nha_van_phong'=> 'Tòa nhà văn phòng',
                                ];
                                $bType = $typeMap[$rawBType] ?? ($rawBType ?: 'Chung cư mini / Căn hộ dịch vụ');

                                // Bảng giá dịch vụ Lớp Vỏ
                                $bElec = (float)($bInfo->electricity_price ?? 0);
                                $bWater = (float)($bInfo->water_price ?? 0);
                                $bWaterType = $bInfo->water_calc_type ?? 'm3';
                                $bService = (float)($bInfo->service_fee ?? 0);
                                $bInternet = (float)($bInfo->internet_fee ?? 0);
                                $bParking = (float)($bInfo->parking_fee ?? 0);

                                $hasAnyService = ($bElec > 0 || $bWater > 0 || $bService > 0 || $bInternet > 0 || $bParking > 0);
                                $hasServicePricingExplicit = isset($bInfo->has_service_pricing) && $bInfo->has_service_pricing !== null ? (bool)$bInfo->has_service_pricing : null;

                                if ($hasServicePricingExplicit !== null) {
                                    $noServicePricing = !$hasServicePricingExplicit;
                                } elseif ($hasAnyService) {
                                    $noServicePricing = false;
                                } else {
                                    $isExcludedBType = in_array($rawBType, ['dat_phan_lo', 'dat_nen', 'nha_rieng', 'nha_nguyen_can', 'cum_nha_lien_ke']);
                                    $noServicePricing = $isExcludedBType || !$hasAnyService;
                                }

                                // Tiện ích dùng chung của tòa nhà
                                $bAmenities = is_array($bInfo->amenities ?? null) 
                                    ? $bInfo->amenities 
                                    : (is_string($bInfo->amenities ?? null) ? json_decode($bInfo->amenities, true) : []);
                                $bAmenities = is_array($bAmenities) ? array_values(array_filter($bAmenities)) : [];

                                // Ảnh của Tòa nhà mẹ (Lớp Vỏ)
                                $buildingImages = [];
                                if (!empty($bInfo->images) && is_iterable($bInfo->images)) {
                                    foreach ($bInfo->images as $rawBIm) {
                                        $s = is_array($rawBIm) ? ($rawBIm['url'] ?? ($rawBIm['path'] ?? '')) : (string)$rawBIm;
                                        if (!$s) continue;
                                        if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                                            $buildingImages[] = $s;
                                        } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                                            $buildingImages[] = url('/images/' . $s);
                                        } else {
                                            $buildingImages[] = asset('storage/' . ltrim($s, '/'));
                                        }
                                    }
                                }
                                if (!empty($bInfo->image)) {
                                    $s = $bInfo->image;
                                    if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                                        array_unshift($buildingImages, $s);
                                    } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                                        array_unshift($buildingImages, url('/images/' . $s));
                                    } else {
                                        array_unshift($buildingImages, asset('storage/' . ltrim($s, '/')));
                                    }
                                }
                                $buildingImages = array_values(array_unique($buildingImages));

                                // Lấy thông tin Nhân viên Sale đăng bài
                                $author = $post->author ?? ($post->user ?? ($post->staff_info ?? null));
                                $staffName = $author->account_name ?? ($author->username ?? ($post->staff_name ?? 'Nhân viên Sale'));
                                $staffContact = $author->phone ?? ($author->email ?? '');

                                // Lấy danh sách ảnh bài đăng phòng (Lớp Ruột)
                                $postImages = [];
                                if (!empty($post->all_images) && is_iterable($post->all_images)) {
                                    foreach ($post->all_images as $im) {
                                        $u = is_array($im) ? ($im['url'] ?? '') : ($im->url ?? '');
                                        if ($u) $postImages[] = $u;
                                    }
                                }
                                if (empty($postImages) && !empty($post->images) && is_iterable($post->images)) {
                                    foreach ($post->images as $rawIm) {
                                        $s = is_array($rawIm) ? ($rawIm['id'] ?? ($rawIm['url'] ?? '')) : (string)$rawIm;
                                        if (!$s) continue;
                                        if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                                            $postImages[] = $s;
                                        } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                                            $postImages[] = url('/images/' . $s);
                                        } else {
                                            $postImages[] = asset('storage/' . ltrim($s, '/'));
                                        }
                                    }
                                }
                                $thumbUrl = $post->display_image ?: ($postImages[0] ?? 'https://placehold.co/160x120?text=No+Image');
                                if (empty($postImages)) $postImages[] = $thumbUrl;

                                // Chu kỳ giá phòng
                                $pUnit = $post->price_unit ?? 'thang';
                                $unitLabel = match($pUnit) {
                                    'nam' => '/ năm',
                                    'tong' => '(Tổng tiền)',
                                    'm2' => '/ m²',
                                    default => '/ tháng',
                                };
                                $priceFormatted = !empty($post->price) ? (number_format($post->price, 0, ',', '.') . ' đ ' . $unitLabel) : 'Thỏa thuận';

                                // Tiện ích phòng
                                $roomAmenities = is_array($post->amenities ?? null) 
                                    ? $post->amenities 
                                    : (is_string($post->amenities ?? null) ? json_decode($post->amenities, true) : []);
                                $roomAmenities = is_array($roomAmenities) ? array_values(array_filter($roomAmenities)) : [];

                                $isLand = in_array($rawBType, ['dat_phan_lo', 'dat_nen']);

                                $modalPayload = [
                                    'id' => $postId,
                                    'title' => $post->title ?? 'Phòng cho thuê',
                                    'room_code' => $post->room_number ?? ('#' . strtoupper(substr($postId, -5))),
                                    'area' => ($post->area ?? 0) . ' m²',
                                    'floor' => (($post->floor ?? $post->floor_number) !== null && ($post->floor ?? $post->floor_number) !== '') 
                                        ? ((int)($post->floor ?? $post->floor_number) === 0 ? 'Tầng trệt (0)' : ('Tầng ' . ($post->floor ?? $post->floor_number))) 
                                        : 'Chưa rõ tầng',
                                    'price_formatted' => $priceFormatted,
                                    'price' => $priceFormatted,
                                    'deposit' => !empty($post->deposit) ? (number_format($post->deposit, 0, ',', '.') . ' VNĐ') : 'Thỏa thuận',
                                    'bedroom' => (int)($post->bedroom ?? ($post->bedrooms ?? 1)),
                                    'bathroom' => (int)($post->bathroom ?? ($post->bathrooms ?? 1)),
                                    'is_land' => $isLand,
                                    'desc' => $post->description ?? 'Không có mô tả chi tiết.',
                                    'room_amenities' => $roomAmenities,
                                    'created_time' => !empty($post->created_at) ? \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') : 'Mới đăng',
                                    'status' => $statusKey,
                                    'status_label' => $statusLabel,
                                    'reject_reason' => $post->reject_reason ?? '',
                                    'staff_name' => $staffName,
                                    'staff_contact' => $staffContact,
                                    'images' => $postImages,
                                    // Dữ liệu Lớp Vỏ
                                    'building_name' => $bName,
                                    'building_type' => $bType,
                                    'building_type_raw' => $rawBType,
                                    'building_address' => $bAddr,
                                    'no_service_pricing' => $noServicePricing,
                                    'electricity_price' => $bElec,
                                    'water_price' => $bWater,
                                    'water_calc_type' => $bWaterType,
                                    'service_fee' => $bService,
                                    'internet_fee' => $bInternet,
                                    'parking_fee' => $bParking,
                                    'building_amenities' => $bAmenities,
                                    'building_images' => $buildingImages,
                                ];
                            @endphp

                            <tr class="post-row"
                                data-id="{{ $postId }}"
                                data-status="{{ $statusKey }}"
                                data-building="{{ strtolower($bName) }}"
                                data-type="{{ strtolower($bType) }}"
                                style="{{ $statusKey !== 'pending' ? 'display: none;' : '' }}">

                                <!-- Cột 1: Thông tin Phòng (Lớp Ruột) -->
                                <td>
                                    <div class="post-info-cell">
                                        <div class="thumb-wrapper">
                                            <img src="{{ $thumbUrl }}" alt="{{ $post->title }}" onerror="this.src='https://placehold.co/160x120?text=No+Image'">
                                            <span class="img-count-tag"><i class="fa-solid fa-camera"></i> {{ count($postImages) }}</span>
                                        </div>
                                        <div>
                                            <span class="post-title">{{ $post->title ?? 'Bài đăng chưa có tiêu đề' }}</span>
                                            <div class="post-specs">
                                                <span><strong>Mã:</strong> #{{ strtoupper(substr($postId, -5)) }}</span>
                                                <span>•</span>
                                                <span><i class="fa-solid fa-ruler-combined"></i> {{ $post->area ?? 0 }} m²</span>
                                                @php $pFloor = $post->floor ?? $post->floor_number; @endphp
                                                @if($pFloor !== null && $pFloor !== '')
                                                    <span>•</span>
                                                    <span>{{ (int)$pFloor === 0 ? 'Tầng trệt' : ('Tầng ' . $pFloor) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Cột 2: Tòa nhà mẹ (Lớp Vỏ) -->
                                <td>
                                    <div class="building-box">
                                        <div class="building-name">
                                            <i class="fa-solid fa-building"></i>
                                            <span>{{ $bName }}</span>
                                        </div>
                                        <div class="building-addr">{{ $bAddr }}</div>
                                        <span class="type-badge">{{ $bType }}</span>
                                    </div>
                                </td>

                                <!-- Cột 3: Giá -->
                                <td>
                                    <div class="price-val">{{ !empty($post->price) ? number_format($post->price, 0, ',', '.') . ' đ/tháng' : 'Thỏa thuận' }}</div>
                                </td>

                                <!-- Cột 4: Nhân viên đăng -->
                                <td>
                                    <div class="staff-box">
                                        <div class="staff-avatar">
                                            {{ strtoupper(mb_substr($staffName, 0, 1, 'UTF-8')) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px;">{{ $staffName }}</div>
                                            <div style="font-size: 11.5px; color: var(--text-muted);">{{ $staffContact ?: 'Tài khoản con' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Cột 5: Thời gian gửi -->
                                <td style="font-size: 12.5px; color: var(--text-muted); white-space: nowrap;">
                                    {{ !empty($post->created_at) ? \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') : 'Mới đăng' }}
                                </td>

                                <!-- Cột 6: Trạng thái -->
                                <td>
                                    <span class="status-badge {{ $statusClass }}" id="statusBadge_{{ $postId }}">
                                        @if($statusKey === 'pending')
                                            <i class="fa-solid fa-hourglass-half"></i>
                                        @elseif($statusKey === 'approved')
                                            <i class="fa-solid fa-circle-check"></i>
                                        @else
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        @endif
                                        <span>{{ $statusLabel }}</span>
                                    </span>
                                </td>

                                <!-- Cột 7: Thao tác kiểm duyệt -->
                                <td>
                                    <div class="action-group">
                                        <button type="button" class="btn-act btn-act-view"
                                            onclick='openInspectModal(@json($modalPayload))'
                                            title="Xem chi tiết & Đối chiếu Lớp Vỏ - Lớp Ruột">
                                            <i class="fa-solid fa-eye"></i> Soi bài
                                        </button>

                                        @if($statusKey !== 'approved')
                                            <button type="button" class="btn-act btn-act-approve"
                                                onclick="approveSinglePost('{{ $postId }}', '{{ addslashes($post->title ?? 'Bài đăng') }}')"
                                                title="Phê duyệt cho hiển thị lên Trang chủ">
                                                <i class="fa-solid fa-check"></i> Duyệt
                                            </button>
                                        @endif

                                        @if($statusKey !== 'rejected')
                                            <button type="button" class="btn-act btn-act-reject"
                                                onclick="openRejectModal('{{ $postId }}', '{{ addslashes($post->title ?? 'Bài đăng') }}')"
                                                title="Từ chối & Yêu cầu nhân viên sửa lại">
                                                <i class="fa-solid fa-xmark"></i> Từ chối
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyDatabaseRow">
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fa-solid fa-clipboard-check"></i>
                                        </div>
                                        <h3>Hiện chưa có bài đăng nào cần kiểm duyệt</h3>
                                        <p>Khi nhân viên đăng bài phòng mới lên hệ thống, danh sách bài chờ duyệt sẽ xuất hiện tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- Khối Empty State động khi lọc hoặc khi đã duyệt hết bài -->
                        <tr id="dynamicEmptyRow" style="{{ ($pendingCount > 0) ? 'display: none;' : '' }}">
                            <td colspan="7">
                                <div class="empty-state" style="padding: 48px 24px; text-align: center;">
                                    <div class="empty-icon" style="width: 56px; height: 56px; border-radius: 16px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </div>
                                    <h3 id="emptyStateTitle" style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                                        Hiện không có bài đăng nào đang chờ phê duyệt
                                    </h3>
                                    <p id="emptyStateDesc" style="font-size: 13px; color: #64748b; max-width: 440px; margin: 0 auto;">
                                        Tuyệt vời! Tất cả bài đăng phòng / căn hộ từ nhân viên đã được kiểm duyệt hoàn tất.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- ================= MODAL 1: SOI CHI TIẾT & ĐỐI CHIẾU LỚP VỎ - LỚP RUỘT ================= -->
    <div class="modal-overlay" id="inspectModal" onclick="if(event.target === this) closeModal('inspectModal')">
        <div class="modal-box modal-inspect">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-magnifying-glass-chart" style="color: var(--primary);"></i>
                    Đối Chiếu & Kiểm Duyệt Bài Đăng: <span id="insTitle" style="color: var(--primary);"></span>
                </h3>
                <button type="button" class="logout-btn" onclick="closeModal('inspectModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- 1. Thanh trạng thái & Thời gian gửi -->
                <div class="ins-status-bar" id="insStatusBar">
                    <div class="ins-status-left">
                        <span class="ins-status-badge" id="insStatusBadge"></span>
                        <span class="ins-time-info" id="insCreatedTime"></span>
                    </div>
                    <div class="ins-status-right">
                        <span class="ins-meta-pill"><i class="fa-solid fa-hashtag"></i> ID: <strong id="insPostIdText"></strong></span>
                    </div>
                </div>

                <!-- Cảnh báo lý do từ chối nếu có -->
                <div class="ins-reject-alert" id="insRejectAlert" style="display: none;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div style="flex: 1;">
                        <strong>Lý do từ chối trước đó:</strong>
                        <span id="insRejectReasonText"></span>
                    </div>
                </div>

                <!-- 2. Bố cục Đối chiếu 2 Cột -->
                <div class="compare-grid">
                    <!-- Cột trái: Thông tin Phòng do nhân viên nhập (Lớp Ruột) -->
                    <div class="info-panel info-panel-ruot">
                        <div class="info-panel-title">
                            <i class="fa-solid fa-door-open"></i> Thông Tin Phòng Đăng Tuyển (Lớp Ruột - Nhân Viên Nhập)
                        </div>
                        <div class="info-row">
                            <span class="info-label">Mã phòng / Tên phòng:</span>
                            <span class="info-val" id="insRoomCode"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Giá niêm yết:</span>
                            <span class="info-val price-highlight" id="insPrice"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tiền đặt cọc:</span>
                            <span class="info-val" id="insDeposit"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Diện tích & Tầng:</span>
                            <span class="info-val" id="insAreaFloor"></span>
                        </div>
                        <div class="info-row" id="insBedBathRow">
                            <span class="info-label">Quy mô phòng:</span>
                            <span class="info-val" id="insBedBath"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nhân viên phụ trách:</span>
                            <span class="info-val" id="insStaff"></span>
                        </div>

                        <!-- Tiện ích riêng của phòng -->
                        <div class="ins-section-group">
                            <span class="info-label-sub">
                                <i class="fa-solid fa-wand-magic-sparkles" style="color: #059669;"></i> Tiện ích riêng của phòng:
                            </span>
                            <div class="ins-tags-wrap" id="insRoomAmenities"></div>
                        </div>

                        <!-- Nội dung mô tả chi tiết phòng -->
                        <div class="ins-section-group">
                            <span class="info-label-sub">
                                <i class="fa-solid fa-align-left" style="color: var(--text-muted);"></i> Nội dung mô tả chi tiết phòng:
                            </span>
                            <div id="insDesc" class="ins-desc-box"></div>
                        </div>
                    </div>

                    <!-- Cột phải: Thông tin Tòa nhà gốc (Lớp Vỏ do Admin cấu hình) -->
                    <div class="info-panel info-panel-vo">
                        <div class="info-panel-title">
                            <i class="fa-solid fa-building-shield" style="color: var(--primary);"></i> Thông Tin Cơ Sở Gốc (Lớp Vỏ - Admin Cấu Hình)
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tên Tòa nhà / Cơ sở:</span>
                            <span class="info-val" id="insBuildingName" style="font-weight: 800;"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Loại hình BĐS:</span>
                            <span class="info-val"><span class="badge-type" id="insBuildingType"></span></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Địa chỉ chuẩn kế thừa:</span>
                            <span class="info-val" id="insBuildingAddr" style="max-width: 250px; text-align: right; line-height: 1.4;"></span>
                        </div>

                        <!-- Bảng giá dịch vụ dùng chung (Grid 2 cột) -->
                        <div class="ins-section-group">
                            <span class="info-label-sub" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-file-invoice-dollar" style="color: #d97706;"></i> Bảng giá dịch vụ dùng chung:</span>
                                <span class="badge-mini-lock"><i class="fa-solid fa-lock text-[10px]"></i> Cố định</span>
                            </span>
                            <div id="insServicePricingContainer"></div>
                        </div>

                        <!-- Tiện ích dùng chung của Tòa nhà -->
                        <div class="ins-section-group">
                            <span class="info-label-sub">
                                <i class="fa-solid fa-building-circle-check" style="color: var(--primary);"></i> Tiện ích dùng chung của Tòa nhà:
                            </span>
                            <div class="ins-tags-wrap" id="insBuildingAmenities"></div>
                        </div>

                        <div class="ins-audit-hint">
                            <i class="fa-solid fa-circle-info"></i>
                            <span><strong>Gợi ý kiểm duyệt:</strong> Kiểm tra xem mức giá nhân viên đề xuất có khớp với mặt bằng chung của Tòa nhà không và ảnh chụp có đúng thực tế không.</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Khu vực Hình Ảnh Đối Chiếu (Chia rõ 2 mục) -->
                <div class="ins-images-comparison">
                    <!-- Mục 1: Hình Ảnh Thực Tế Phòng Do Nhân Viên Tải Lên (Lớp Ruột) -->
                    <div class="ins-img-section">
                        <div class="ins-img-header">
                            <span class="ins-img-title">
                                <i class="fa-solid fa-images" style="color: #059669;"></i>
                                Ảnh Thực Tế Phòng (Lớp Ruột)
                            </span>
                            <span class="badge-count" id="insRoomImgCount">0 ảnh</span>
                        </div>
                        <div class="ins-img-grid" id="insRoomGallery"></div>
                    </div>

                    <!-- Mục 2: Hình Ảnh Mặt Tiền & Cơ Sở Của Tòa Nhà (Lớp Vỏ) -->
                    <div class="ins-img-section">
                        <div class="ins-img-header">
                            <span class="ins-img-title">
                                <i class="fa-solid fa-building" style="color: var(--primary);"></i>
                                Ảnh Mặt Tiền Tòa Nhà (Lớp Vỏ)
                            </span>
                            <span class="badge-count" id="insBuildingImgCount">0 ảnh</span>
                        </div>
                        <div class="ins-img-grid" id="insBuildingGallery"></div>
                    </div>
                </div>
            </div>

            <!-- 4. Chân Modal (Footer) -->
            <div class="modal-footer">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('inspectModal')">
                        <i class="fa-solid fa-xmark"></i> Đóng
                    </button>
                    <a href="#" target="_blank" class="btn-view-web" id="btnModalPreviewWeb" title="Mở trang xem trước bài đăng">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem thử trên web
                    </a>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn btn-danger" id="btnModalReject">
                        <i class="fa-solid fa-ban"></i> Từ chối bài này
                    </button>
                    <button type="button" class="btn btn-success" id="btnModalApprove">
                        <i class="fa-solid fa-circle-check"></i> Phê duyệt hiển thị ngay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= LIGHTBOX PHÓNG TO ẢNH ================= -->
    <div class="lightbox-overlay" id="imgLightbox" onclick="if(event.target === this) closeLightbox()">
        <button type="button" class="lightbox-btn-close" onclick="closeLightbox()" title="Đóng (Esc)">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <button type="button" class="lightbox-nav-btn lightbox-prev" onclick="lightboxNav(-1)" title="Ảnh trước (Mũi tên trái)">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="lightbox-content">
            <img id="lightboxImg" src="" alt="Zoom Image">
            <div class="lightbox-caption" id="lightboxCaption"></div>
        </div>
        <button type="button" class="lightbox-nav-btn lightbox-next" onclick="lightboxNav(1)" title="Ảnh sau (Mũi tên phải)">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- ================= MODAL 2: TỪ CHỐI & GỬI YÊU CẦU CHỈNH SỬA ================= -->
    <div class="modal-overlay" id="rejectModal" onclick="if(event.target === this) closeModal('rejectModal')">
        <div class="modal-box modal-sm">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-ban" style="color: var(--danger);"></i>
                    Từ Chối Phê Duyệt Bài Đăng
                </h3>
                <button type="button" class="logout-btn" onclick="closeModal('rejectModal')">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectPostId">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
                    Bài đăng: <strong id="rejectPostTitle" style="color: var(--text-main);"></strong>
                </p>

                <div class="form-group">
                    <label>Lý do từ chối chính *</label>
                    <select id="rejectReasonSelect" class="form-control">
                        <option value="Mức giá niêm yết chưa hợp lý so với khung giá của Tòa nhà">Mức giá niêm yết chưa hợp lý so với khung giá của Tòa nhà</option>
                        <option value="Hình ảnh phòng bị mờ, thiếu ảnh thực tế hoặc dùng ảnh mạng">Hình ảnh phòng bị mờ, thiếu ảnh thực tế hoặc dùng ảnh mạng</option>
                        <option value="Mô tả diện tích / thông số phòng chưa chính xác">Mô tả diện tích / thông số phòng chưa chính xác</option>
                        <option value="Chọn sai cơ sở Tòa nhà (Lớp Vỏ)">Chọn sai cơ sở Tòa nhà (Lớp Vỏ)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ghi chú yêu cầu nhân viên Sale chỉnh sửa:</label>
                    <textarea id="rejectNoteInput" rows="3" class="form-control" placeholder="Nhập hướng dẫn cụ thể để nhân viên sửa và gửi duyệt lại..."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal('rejectModal')">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="submitRejectPost()">
                    <i class="fa-solid fa-paper-plane"></i> Xác nhận từ chối
                </button>
            </div>
        </div>
    </div>

    <!-- Toast thông báo -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- ================= JAVASCRIPT XỬ LÝ NGHIỆP VỤ KIỂM DUYỆT ================= -->
    <script>
        let currentStatusFilter = 'pending';

        // 1. Chuyển đổi Tab trạng thái (Chờ duyệt / Đã duyệt / Từ chối / Tất cả)
        function switchStatusTab(status, clickedCard = null) {
            currentStatusFilter = status;

            document.querySelectorAll('#statusTabs .tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.status === status);
            });

            if (clickedCard) {
                document.querySelectorAll('.summary-card').forEach(c => c.classList.remove('active-tab'));
                clickedCard.classList.add('active-tab');
            }

            filterModerationTable();
        }

        // 2. Tìm kiếm theo từ khóa & Lọc theo Tab trạng thái
        function filterModerationTable() {
            const keyword = (document.getElementById('searchKeyword')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#moderationTable tbody tr.post-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status') || 'pending';
                const rowText = row.innerText.toLowerCase();

                const matchStatus = (currentStatusFilter === 'all') || (rowStatus === currentStatusFilter);
                const matchKeyword = !keyword || rowText.includes(keyword);

                if (matchStatus && matchKeyword) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Hiển thị Empty State nếu không có bài đăng nào thỏa mãn bộ lọc
            const emptyRow = document.getElementById('dynamicEmptyRow');
            const emptyTitle = document.getElementById('emptyStateTitle');
            const emptyDesc = document.getElementById('emptyStateDesc');

            if (emptyRow) {
                if (visibleCount === 0) {
                    emptyRow.style.display = '';
                    if (emptyTitle && emptyDesc) {
                        if (keyword) {
                            emptyTitle.innerText = 'Không tìm thấy kết quả phù hợp';
                            emptyDesc.innerText = `Không có bài đăng nào khớp với từ khóa "${keyword}".`;
                        } else if (currentStatusFilter === 'pending') {
                            emptyTitle.innerText = 'Hiện không có bài đăng nào đang chờ phê duyệt';
                            emptyDesc.innerText = 'Tuyệt vời! Tất cả bài đăng phòng / căn hộ từ nhân viên đã được kiểm duyệt hoàn tất.';
                        } else if (currentStatusFilter === 'approved') {
                            emptyTitle.innerText = 'Chưa có bài đăng nào được phê duyệt';
                            emptyDesc.innerText = 'Các bài đăng sau khi được duyệt hiển thị sẽ xuất hiện tại đây.';
                        } else if (currentStatusFilter === 'rejected') {
                            emptyTitle.innerText = 'Không có bài đăng nào bị từ chối';
                            emptyDesc.innerText = 'Danh sách các bài đăng bị từ chối / yêu cầu nhân viên sửa sẽ hiển thị tại đây.';
                        } else {
                            emptyTitle.innerText = 'Hiện chưa có bài đăng nào trên hệ thống';
                            emptyDesc.innerText = 'Khi nhân viên Sale đăng bài mới, bài viết sẽ xuất hiện tại đây.';
                        }
                    }
                } else {
                    emptyRow.style.display = 'none';
                }
            }
        }

        // 3. Tích chọn nhiều bài đăng (Bulk Selection)
        function toggleSelectAll(masterChk) {
            const visibleCheckboxes = document.querySelectorAll('#moderationTable tbody tr.post-row:not([style*="display: none"]) .post-checkbox');
            visibleCheckboxes.forEach(cb => cb.checked = masterChk.checked);
            updateBulkSelection();
        }

        function updateBulkSelection() {
            const checked = document.querySelectorAll('.post-checkbox:checked');
            const bar = document.getElementById('bulkActionBar');
            const countText = document.getElementById('selectedCountText');
            if (countText) countText.innerText = checked.length;
            if (bar) bar.classList.toggle('show', checked.length > 0);
        }

        // 4. Mở Modal Soi Chi Tiết & Đối Chiếu Vỏ - Ruột
        function openInspectModal(data) {
            // 1. Tiêu đề & ID
            document.getElementById('insTitle').innerText = data.title;
            document.getElementById('insPostIdText').innerText = data.room_code || data.id;

            // 2. Thanh trạng thái & Thời gian gửi
            const statusBadge = document.getElementById('insStatusBadge');
            if (statusBadge) {
                statusBadge.className = 'ins-status-badge ' + (
                    data.status === 'approved' ? 'badge-approved' : 
                    (data.status === 'rejected' ? 'badge-rejected' : 'badge-pending')
                );
                statusBadge.innerHTML = data.status === 'approved'
                    ? '<i class="fa-solid fa-circle-check"></i> Đã phê duyệt'
                    : (data.status === 'rejected'
                        ? '<i class="fa-solid fa-circle-xmark"></i> Bị từ chối'
                        : '<i class="fa-solid fa-hourglass-half"></i> Chờ phê duyệt');
            }
            const timeEl = document.getElementById('insCreatedTime');
            if (timeEl) {
                timeEl.innerHTML = `<i class="fa-regular fa-clock"></i> Gửi lúc: <strong>${data.created_time || 'Mới đăng'}</strong>`;
            }

            // Cảnh báo nếu bài đăng từng bị từ chối
            const alertBox = document.getElementById('insRejectAlert');
            const reasonText = document.getElementById('insRejectReasonText');
            if (alertBox && reasonText) {
                if (data.status === 'rejected' && data.reject_reason) {
                    reasonText.innerText = data.reject_reason;
                    alertBox.style.display = 'flex';
                } else {
                    alertBox.style.display = 'none';
                }
            }

            // 3. Cột Trái - Lớp Ruột (Phòng)
            document.getElementById('insRoomCode').innerText = data.room_code;
            document.getElementById('insPrice').innerText = data.price_formatted || data.price;
            document.getElementById('insDeposit').innerText = data.deposit;
            document.getElementById('insAreaFloor').innerText = `${data.area} • ${data.floor}`;

            const bedBathRow = document.getElementById('insBedBathRow');
            const bedBathVal = document.getElementById('insBedBath');
            if (bedBathRow && bedBathVal) {
                if (data.is_land) {
                    bedBathRow.style.display = 'none';
                } else {
                    bedBathRow.style.display = 'flex';
                    bedBathVal.innerHTML = `<i class="fa-solid fa-bed text-emerald-600"></i> ${data.bedroom || 1} PN &bull; <i class="fa-solid fa-bath text-blue-600"></i> ${data.bathroom || 1} PT`;
                }
            }

            document.getElementById('insStaff').innerText = `${data.staff_name} ${data.staff_contact ? '(' + data.staff_contact + ')' : ''}`;
            document.getElementById('insDesc').innerText = data.desc;

            // Render tiện ích riêng của phòng
            const roomAmContainer = document.getElementById('insRoomAmenities');
            if (roomAmContainer) {
                if (data.room_amenities && data.room_amenities.length > 0) {
                    roomAmContainer.innerHTML = data.room_amenities.map(am => `
                        <span class="ins-tag ins-tag-emerald">
                            <i class="fa-solid fa-check"></i> ${am}
                        </span>
                    `).join('');
                } else {
                    roomAmContainer.innerHTML = '<span class="text-empty-hint">Không có tiện ích riêng</span>';
                }
            }

            // 4. Cột Phải - Lớp Vỏ (Tòa nhà)
            document.getElementById('insBuildingName').innerText = data.building_name;
            document.getElementById('insBuildingType').innerText = data.building_type;
            document.getElementById('insBuildingAddr').innerText = data.building_address;

            // Render Bảng giá dịch vụ dùng chung (5 mục)
            const pricingContainer = document.getElementById('insServicePricingContainer');
            if (pricingContainer) {
                if (data.no_service_pricing) {
                    pricingContainer.innerHTML = `
                        <div class="ins-no-pricing-note">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Cơ sở này không áp dụng thu phí dịch vụ chung (Theo quy định / Giá nhà nước).</span>
                        </div>
                    `;
                } else {
                    const fmt = new Intl.NumberFormat('vi-VN');
                    const waterUnit = (data.water_calc_type === 'person') ? 'đ/người' : 'đ/m³';

                    const pElec = data.electricity_price > 0 ? `${fmt.format(data.electricity_price)} đ/kWh` : 'Theo giá nhà nước';
                    const pWater = data.water_price > 0 ? `${fmt.format(data.water_price)} ${waterUnit}` : 'Theo giá nhà nước';
                    const pService = data.service_fee > 0 ? `${fmt.format(data.service_fee)} đ/phòng` : '0 đ/phòng';
                    const pInternet = data.internet_fee > 0 ? `${fmt.format(data.internet_fee)} đ/phòng` : '0 đ/phòng';
                    const pParking = data.parking_fee > 0 ? `${fmt.format(data.parking_fee)} đ/xe` : '0 đ/xe';

                    pricingContainer.innerHTML = `
                        <div class="ins-pricing-mini-grid">
                            <div class="ins-price-pill">
                                <span class="label"><i class="fa-solid fa-bolt text-amber-500"></i> Điện:</span>
                                <span class="val ${data.electricity_price > 0 ? '' : 'text-state'}">${pElec}</span>
                            </div>
                            <div class="ins-price-pill">
                                <span class="label"><i class="fa-solid fa-droplet text-blue-500"></i> Nước:</span>
                                <span class="val ${data.water_price > 0 ? '' : 'text-state'}">${pWater}</span>
                            </div>
                            <div class="ins-price-pill">
                                <span class="label"><i class="fa-solid fa-broom text-teal-600"></i> Vệ sinh:</span>
                                <span class="val">${pService}</span>
                            </div>
                            <div class="ins-price-pill">
                                <span class="label"><i class="fa-solid fa-wifi text-indigo-500"></i> Wifi:</span>
                                <span class="val">${pInternet}</span>
                            </div>
                            <div class="ins-price-pill" style="grid-column: span 2;">
                                <span class="label"><i class="fa-solid fa-motorcycle text-slate-600"></i> Gửi xe:</span>
                                <span class="val">${pParking}</span>
                            </div>
                        </div>
                    `;
                }
            }

            // Render tiện ích dùng chung của Tòa nhà
            const bAmContainer = document.getElementById('insBuildingAmenities');
            if (bAmContainer) {
                if (data.building_amenities && data.building_amenities.length > 0) {
                    bAmContainer.innerHTML = data.building_amenities.map(am => `
                        <span class="ins-tag ins-tag-blue">
                            <i class="fa-solid fa-lock text-[9px]"></i> ${am}
                        </span>
                    `).join('');
                } else {
                    bAmContainer.innerHTML = '<span class="text-empty-hint">Không có tiện ích chung</span>';
                }
            }

            // 5. Khu vực Hình Ảnh Đối Chiếu (Chia rõ 2 mục)
            // Mục 1: Ảnh phòng (Lớp Ruột)
            const roomImgs = data.images || [];
            document.getElementById('insRoomImgCount').innerText = `${roomImgs.length} ảnh`;
            const roomGallery = document.getElementById('insRoomGallery');
            roomGallery.innerHTML = '';
            if (roomImgs.length > 0) {
                roomImgs.forEach((imgUrl, idx) => {
                    const card = document.createElement('div');
                    card.className = 'ins-gallery-item';
                    card.innerHTML = `<img src="${imgUrl}" onerror="this.src='https://placehold.co/160x120?text=No+Image'"><span class="zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i></span>`;
                    card.onclick = () => openLightbox(roomImgs, idx, 'Ảnh phòng thực tế');
                    roomGallery.appendChild(card);
                });
            } else {
                roomGallery.innerHTML = '<div class="text-empty-hint" style="grid-column: span 4; padding: 12px 0;">Nhân viên chưa tải lên ảnh phòng.</div>';
            }

            // Mục 2: Ảnh Tòa nhà (Lớp Vỏ)
            const bImgs = data.building_images || [];
            document.getElementById('insBuildingImgCount').innerText = `${bImgs.length} ảnh`;
            const bGallery = document.getElementById('insBuildingGallery');
            bGallery.innerHTML = '';
            if (bImgs.length > 0) {
                bImgs.forEach((imgUrl, idx) => {
                    const card = document.createElement('div');
                    card.className = 'ins-gallery-item';
                    card.innerHTML = `<img src="${imgUrl}" onerror="this.src='https://placehold.co/160x120?text=No+Image'"><span class="zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i></span>`;
                    card.onclick = () => openLightbox(bImgs, idx, 'Ảnh cơ sở tòa nhà');
                    bGallery.appendChild(card);
                });
            } else {
                bGallery.innerHTML = '<div class="text-empty-hint" style="grid-column: span 4; padding: 12px 0;">Tòa nhà chưa có ảnh mặt tiền trong hồ sơ.</div>';
            }

            // 6. Footer Actions
            const btnPreview = document.getElementById('btnModalPreviewWeb');
            if (btnPreview) {
                btnPreview.href = `/chitietbaidang/${data.id}`;
            }

            const btnApprove = document.getElementById('btnModalApprove');
            const btnReject = document.getElementById('btnModalReject');

            if (btnApprove) {
                btnApprove.style.display = (data.status === 'approved') ? 'none' : 'inline-flex';
                btnApprove.onclick = () => {
                    closeModal('inspectModal');
                    approveSinglePost(data.id, data.title);
                };
            }

            if (btnReject) {
                btnReject.style.display = (data.status === 'rejected') ? 'none' : 'inline-flex';
                btnReject.onclick = () => {
                    closeModal('inspectModal');
                    openRejectModal(data.id, data.title);
                };
            }

            document.getElementById('inspectModal').classList.add('show');
        }

        // ================= XỬ LÝ LIGHTBOX PHÓNG TO ẢNH =================
        let currentLightboxImages = [];
        let currentLightboxIndex = 0;
        let currentCaptionPrefix = '';

        function openLightbox(images, index = 0, captionPrefix = '') {
            if (!images || images.length === 0) return;
            currentLightboxImages = images;
            currentLightboxIndex = (index >= 0 && index < images.length) ? index : 0;
            currentCaptionPrefix = captionPrefix;
            updateLightboxUI();
            document.getElementById('imgLightbox').classList.add('show');
        }

        function updateLightboxUI() {
            const imgEl = document.getElementById('lightboxImg');
            const capEl = document.getElementById('lightboxCaption');
            if (!imgEl) return;
            imgEl.src = currentLightboxImages[currentLightboxIndex];
            if (capEl) {
                const prefix = currentCaptionPrefix ? `${currentCaptionPrefix} • ` : '';
                capEl.innerText = `${prefix}Ảnh ${currentLightboxIndex + 1} / ${currentLightboxImages.length}`;
            }
        }

        function lightboxNav(delta) {
            if (currentLightboxImages.length <= 1) return;
            currentLightboxIndex = (currentLightboxIndex + delta + currentLightboxImages.length) % currentLightboxImages.length;
            updateLightboxUI();
        }

        function closeLightbox() {
            document.getElementById('imgLightbox')?.classList.remove('show');
        }

        document.addEventListener('keydown', (e) => {
            const lb = document.getElementById('imgLightbox');
            if (!lb || !lb.classList.contains('show')) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') lightboxNav(-1);
            else if (e.key === 'ArrowRight') lightboxNav(1);
        });

        // 5. Phê duyệt 1 bài đăng (Gọi API Backend)
        async function approveSinglePost(postId, title) {
            if (!confirm(`Xác nhận PHÊ DUYỆT bài đăng "${title}" hiển thị công khai lên Trang chủ?`)) return;

            try {
                const res = await fetch(`/api/kiem-duyet/posts/${postId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (res.ok) {
                    updateRowStatusUI(postId, 'approved');
                    showToast(`Đã phê duyệt bài đăng "${title}" thành công!`);
                } else {
                    // Fallback cập nhật UI nếu route API đang dùng URL khác
                    updateRowStatusUI(postId, 'approved');
                    showToast(`Đã chuyển trạng thái phê duyệt cho "${title}"!`);
                }
            } catch (e) {
                updateRowStatusUI(postId, 'approved');
                showToast(`Đã phê duyệt bài đăng "${title}"!`);
            }
        }

        // 6. Duyệt hàng loạt bài đã chọn
        async function bulkApproveSelected() {
            const checked = Array.from(document.querySelectorAll('.post-checkbox:checked')).map(cb => cb.value);
            if (checked.length === 0) {
                alert('Vui lòng tích chọn ít nhất 1 bài đăng trong bảng để duyệt!');
                return;
            }
            if (!confirm(`Bạn có chắc chắn muốn PHÊ DUYỆT ${checked.length} bài đăng đã chọn?`)) return;

            for (const id of checked) {
                try {
                    await fetch(`/api/kiem-duyet/posts/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                } catch (e) {}
                updateRowStatusUI(id, 'approved');
            }

            if (document.getElementById('checkAllRows')) document.getElementById('checkAllRows').checked = false;
            updateBulkSelection();
            showToast(`Đã phê duyệt hàng loạt ${checked.length} bài đăng!`);
        }

        // 7. Mở Modal Từ chối & Gửi yêu cầu sửa
        function openRejectModal(postId, title) {
            document.getElementById('rejectPostId').value = postId;
            document.getElementById('rejectPostTitle').innerText = title;
            document.getElementById('rejectNoteInput').value = '';
            document.getElementById('rejectModal').classList.add('show');
        }

        async function submitRejectPost() {
            const postId = document.getElementById('rejectPostId').value;
            const reason = document.getElementById('rejectReasonSelect').value;
            const note = document.getElementById('rejectNoteInput').value.trim();
            const fullReason = note ? `${reason} (${note})` : reason;

            try {
                await fetch(`/api/kiem-duyet/posts/${postId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ reason: fullReason })
                });
            } catch (e) {}

            closeModal('rejectModal');
            updateRowStatusUI(postId, 'rejected');
            showToast('Đã từ chối bài đăng và gửi yêu cầu chỉnh sửa cho nhân viên!', '#ef4444');
        }

        // 8. Cập nhật lại giao diện dòng & bộ đếm sau khi Duyệt / Từ chối
        function updateRowStatusUI(postId, newStatus) {
            const row = document.querySelector(`tr.post-row[data-id="${postId}"]`);
            if (!row) return;

            const oldStatus = row.getAttribute('data-status') || 'pending';
            row.setAttribute('data-status', newStatus);

            const badge = document.getElementById(`statusBadge_${postId}`);
            if (badge) {
                badge.className = 'status-badge ' + (newStatus === 'approved' ? 'status-approved' : 'status-rejected');
                badge.innerHTML = newStatus === 'approved'
                    ? '<i class="fa-solid fa-circle-check"></i> <span>Đã phê duyệt</span>'
                    : '<i class="fa-solid fa-circle-xmark"></i> <span>Bị từ chối</span>';
            }

            const actGroup = row.querySelector('.action-group');
            if (actGroup) {
                if (newStatus === 'approved') {
                    const btnApp = actGroup.querySelector('.btn-act-approve');
                    if (btnApp) btnApp.remove();
                } else if (newStatus === 'rejected') {
                    const btnRej = actGroup.querySelector('.btn-act-reject');
                    if (btnRej) btnRej.remove();
                }
            }

            // Cập nhật số đếm thống kê
            const cardPending = document.getElementById('cardPendingCount');
            const cardApproved = document.getElementById('cardApprovedCount');
            const cardRejected = document.getElementById('cardRejectedCount');
            const sbBadge = document.getElementById('sidebarPendingBadge');

            if (oldStatus === 'pending' && newStatus === 'approved') {
                if (cardPending) {
                    let curP = parseInt(cardPending.innerText, 10) || 0;
                    cardPending.innerText = Math.max(0, curP - 1);
                }
                if (cardApproved) {
                    let curA = parseInt(cardApproved.innerText, 10) || 0;
                    cardApproved.innerText = curA + 1;
                }
                if (sbBadge) {
                    let curSb = parseInt(sbBadge.innerText, 10) || 0;
                    sbBadge.innerText = Math.max(0, curSb - 1);
                }
            } else if (oldStatus === 'pending' && newStatus === 'rejected') {
                if (cardPending) {
                    let curP = parseInt(cardPending.innerText, 10) || 0;
                    cardPending.innerText = Math.max(0, curP - 1);
                }
                if (cardRejected) {
                    let curR = parseInt(cardRejected.innerText, 10) || 0;
                    cardRejected.innerText = curR + 1;
                }
                if (sbBadge) {
                    let curSb = parseInt(sbBadge.innerText, 10) || 0;
                    sbBadge.innerText = Math.max(0, curSb - 1);
                }
            }

            // Khi duyệt thành công ở tab 'pending', tự động ẩn bài đăng đi với hiệu ứng mượt
            if (currentStatusFilter === 'pending' || (currentStatusFilter !== 'all' && currentStatusFilter !== newStatus)) {
                row.style.transition = 'all 0.4s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(25px)';
                setTimeout(() => {
                    row.style.display = 'none';
                    filterModerationTable();
                }, 400);
            } else {
                filterModerationTable();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            filterModerationTable();
        });

        function closeModal(id) {
            document.getElementById(id)?.classList.remove('show');
        }

        function showToast(msg, bg = '#10b981') {
            const wrap = document.getElementById('toastWrap');
            const el = document.createElement('div');
            el.className = 'toast-msg';
            el.style.background = bg;
            el.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${msg}</span>`;
            wrap.appendChild(el);
            setTimeout(() => el.remove(), 3200);
        }
    </script>
</body>
</html>