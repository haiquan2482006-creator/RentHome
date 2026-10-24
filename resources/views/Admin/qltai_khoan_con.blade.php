<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Tài Khoản Con (Nhân Viên) - RentHome Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #0284c7;
            --brand-primary-hover: #0369a1;
            --brand-light: #f0f9ff;
            --brand-subtle: #e0f2fe;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-emerald: #059669;
            --accent-emerald-bg: #ecfdf5;
            --accent-red: #dc2626;
            --accent-red-bg: #fef2f2;
            --accent-amber: #d97706;
            --accent-amber-bg: #fffbeb;
            --accent-purple: #7c3aed;
            --accent-purple-bg: #f5f3ff;
            --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 32px;
            min-height: 100vh;
            background-color: var(--bg-main);
            max-width: calc(100vw - var(--sidebar-width));
        }

        /* Page Header */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .btn-create {
            background-color: var(--brand-primary);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            transition: var(--transition);
        }

        .btn-create:hover {
            background-color: var(--brand-primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-card);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Content Card / Table */
        .content-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
            margin-bottom: 30px;
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

        .card-title-text {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            min-width: 240px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .select-filter {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.85rem;
            background-color: #ffffff;
            color: var(--text-secondary);
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        .select-filter:focus {
            border-color: var(--brand-primary);
        }

        /* Table Design */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        .data-table th {
            padding: 14px 20px;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-secondary);
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-primary);
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Cell Styling */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
        }

        .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .badge-role-sale {
            background-color: var(--brand-subtle);
            color: var(--brand-primary);
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        .badge-role-operation {
            background-color: var(--accent-emerald-bg);
            color: var(--accent-emerald);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        .badge-role-general {
            background-color: var(--accent-purple-bg);
            color: var(--accent-purple);
            border: 1px solid rgba(124, 58, 237, 0.2);
        }

        .badge-bld-all {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            background-color: #eff6ff;
            color: #1d4ed8;
            border-radius: 6px;
            font-size: 0.725rem;
            font-weight: 700;
            margin-top: 4px;
        }

        .badge-bld-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background-color: #f1f5f9;
            color: #334155;
            border-radius: 6px;
            font-size: 0.725rem;
            font-weight: 600;
            margin: 2px 2px 0 0;
            border: 1px solid #e2e8f0;
        }

        /* Quyền Hạn Badges */
        .perm-badges-container {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-width: 220px;
        }

        .perm-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            white-space: nowrap;
        }

        .perm-badge-posts { background-color: #e0f2fe; color: #0369a1; }
        .perm-badge-contacts { background-color: #dcfce7; color: #15803d; }
        .perm-badge-contracts { background-color: #f3e8ff; color: #7e22ce; }
        .perm-badge-invoices { background-color: #fef3c7; color: #b45309; }

        /* Role Badges */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: 0.2px;
        }
        .role-badge-mod {
            background-color: #f3e8ff;
            color: #7e22ce;
            border: 1px solid #d8b4fe;
        }
        .role-badge-ent {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .role-badge-staff {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .role-badge-admin {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* KPI Badge */
        .kpi-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.775rem;
            font-weight: 700;
        }

        .kpi-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
        }

        .kpi-val {
            font-weight: 800;
            color: var(--brand-primary);
        }

        /* Status Badge */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-pill-active {
            background-color: var(--accent-emerald-bg);
            color: var(--accent-emerald);
        }

        .status-pill-locked {
            background-color: var(--accent-red-bg);
            color: var(--accent-red);
        }

        /* Actions */
        .action-btns {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .btn-act {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .btn-act:hover {
            background-color: var(--brand-light);
            color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .btn-act.btn-lock:hover {
            background-color: var(--accent-amber-bg);
            color: var(--accent-amber);
            border-color: var(--accent-amber);
        }

        .btn-act.btn-delete:hover {
            background-color: var(--accent-red-bg);
            color: var(--accent-red);
            border-color: var(--accent-red);
        }

        /* Empty State */
        .empty-placeholder {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-placeholder i {
            font-size: 2.8rem;
            margin-bottom: 12px;
            color: #cbd5e1;
        }

        /* ================== TABS NAVIGATION ================== */
        .account-tabs-wrapper {
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .account-tabs-nav {
            display: inline-flex;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            padding: 6px;
            border-radius: 14px;
            gap: 8px;
            box-shadow: var(--shadow-subtle);
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-secondary);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            user-select: none;
            white-space: nowrap;
        }

        .tab-btn i {
            font-size: 1rem;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .tab-btn:hover {
            color: var(--brand-primary);
            background-color: #f8fafc;
        }

        .tab-btn:hover i {
            color: var(--brand-primary);
        }

        .tab-btn.active {
            background-color: var(--brand-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28);
        }

        .tab-btn.active i {
            color: #ffffff;
        }

        .tab-counter-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.725rem;
            font-weight: 800;
            line-height: 1.2;
            transition: var(--transition);
        }

        .tab-btn.active .tab-counter-badge {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .tab-btn:not(.active) .tab-counter-badge {
            background-color: #f1f5f9;
            color: var(--text-secondary);
            border: 1px solid #e2e8f0;
        }

        /* Tab panes animation */
        .tab-pane {
            animation: fadeInTab 0.25s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fadeInTab {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Customer Table Specific Styles */
        .cust-avatar-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #059669, #10b981);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
            border: 2px solid #ffffff;
        }

        .cust-avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge-customer-role {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid rgba(5, 150, 105, 0.2);
            margin-top: 3px;
        }

        /* MODAL STYLES */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-dialog {
            background-color: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 760px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.95);
            transition: all 0.25s ease;
            border: 1px solid var(--border-color);
        }

        .modal-overlay.active .modal-dialog {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 26px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            padding: 24px 26px;
        }

        .form-section-title {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-primary);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 6px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 14px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.875rem;
            color: var(--text-primary);
            background-color: #ffffff;
            outline: none;
            transition: var(--transition);
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .input-group-addon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-addon .form-input {
            padding-right: 80px;
        }

        .pw-actions {
            position: absolute;
            right: 8px;
            display: flex;
            gap: 4px;
        }

        .btn-input-mini {
            background: #f1f5f9;
            border: none;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-input-mini:hover {
            background: var(--brand-primary);
            color: #ffffff;
        }

        /* Checkbox Cards for Permissions */
        .permissions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 18px;
        }

        .perm-card-item {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background-color: #f8fafc;
            transition: var(--transition);
        }

        .perm-card-item:hover {
            border-color: rgba(2, 132, 199, 0.4);
            background-color: #f0f9ff;
            transform: translateY(-1px);
        }

        .perm-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .badge-fixed-lock {
            font-size: 0.65rem;
            font-weight: 700;
            background: #e0f2fe;
            color: #0284c7;
            padding: 2px 7px;
            border-radius: 4px;
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .perm-card-info h4 {
            font-size: 0.825rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .perm-card-info p {
            font-size: 0.725rem;
            color: var(--text-secondary);
            margin-top: 2px;
            line-height: 1.3;
        }

        /* Buildings Checkbox List */
        .buildings-box {
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 12px;
            max-height: 160px;
            overflow-y: auto;
            background-color: #f8fafc;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .building-chk-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .building-chk-item:hover {
            border-color: var(--brand-primary);
        }

        .building-chk-item input {
            accent-color: var(--brand-primary);
        }

        .building-chk-item span {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-footer {
            padding: 16px 26px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background-color: #f8fafc;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .btn-cancel {
            padding: 10px 18px;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background-color: #f1f5f9;
        }

        /* Toast Alert Container */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast-item {
            pointer-events: auto;
            min-width: 300px;
            max-width: 420px;
            padding: 14px 18px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            border-left: 4px solid var(--brand-primary);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            animation: slideIn 0.3s ease forwards;
        }

        .toast-item.toast-success { border-left-color: var(--accent-emerald); }
        .toast-item.toast-error { border-left-color: var(--accent-red); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .admin-main {
                margin-left: 0;
                max-width: 100vw;
                padding: 16px;
            }
            .form-row-2, .permissions-grid, .buildings-box {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @include('partials.header-admin')

    <main class="admin-main">
        @php
            // Thống kê Doanh nghiệp / Nhân viên con
            $subList = collect($subAccounts ?? []);
            $subTotal = $subList->count();
            $subActive = $subList->filter(fn($a) => empty($a->is_locked) && ($a->status ?? 'active') === 'active')->count();
            $subLocked = $subList->filter(fn($a) => !empty($a->is_locked) || ($a->status ?? '') === 'locked')->count();
            $buildingsCount = count($allBuildings ?? []);

            // Thống kê Khách hàng cá nhân
            $custList = collect($customerAccounts ?? []);
            $custTotal = $custList->count();
            $custActive = $custList->filter(fn($c) => empty($c->is_locked) && ($c->status ?? 'active') === 'active')->count();
            $custLocked = $custList->filter(fn($c) => !empty($c->is_locked) || ($c->status ?? '') === 'locked' || ($c->status ?? '') === 'khoa')->count();
        @endphp

        <!-- Page Title & Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fa-solid fa-users-gear" style="color: var(--brand-primary);" id="pageHeaderIcon"></i>
                    <span id="pageHeaderTitle">Quản Lý Tài Khoản Con & Khách Hàng</span>
                </h1>
                <p class="page-subtitle" id="pageSubtitleText">Phân quyền chi tiết cho nhân viên (Sale, Vận hành) quản lý các tòa nhà thuộc Doanh nghiệp RentHome</p>
            </div>
            <div id="headerBtnCreateContainer">
                <button type="button" class="btn-create" onclick="openCreateModal()">
                    <i class="fa-solid fa-user-plus"></i> Tạo Tài Khoản Con
                </button>
            </div>
        </div>

        <!-- 4 Thẻ Thống Kê KPI Tổng Quan -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" id="stat-box-total" style="background-color: rgba(2, 132, 199, 0.1); color: var(--brand-primary);">
                    <i class="fa-solid fa-users-gear" id="stat-icon-total"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-total-accounts">{{ $subTotal }}</div>
                    <div class="stat-label" id="stat-label-total">Tổng Tài Khoản Con</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" id="stat-box-active" style="background-color: var(--accent-emerald-bg); color: var(--accent-emerald);">
                    <i class="fa-solid fa-user-check" id="stat-icon-active"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-active-accounts">{{ $subActive }}</div>
                    <div class="stat-label" id="stat-label-active">Đang Hoạt Động</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" id="stat-box-locked" style="background-color: var(--accent-red-bg); color: var(--accent-red);">
                    <i class="fa-solid fa-user-lock" id="stat-icon-locked"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-locked-accounts">{{ $subLocked }}</div>
                    <div class="stat-label" id="stat-label-locked">Đang Tạm Khóa</div>
                </div>
            </div>

            <div class="stat-card" id="stat-card-fourth">
                <div class="stat-icon" id="stat-box-fourth" style="background-color: var(--accent-purple-bg); color: var(--accent-purple);">
                    <i class="fa-solid fa-city" id="stat-icon-fourth"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-total-buildings">{{ $buildingsCount }}</div>
                    <div class="stat-label" id="stat-label-fourth">Tòa Nhà Trong Hệ Thống</div>
                </div>
            </div>
        </div>

        <!-- Thanh Chuyển Đổi 2 Mục (Tabs) Ngay Phía Trên Bảng Danh Sách -->
        <div class="account-tabs-wrapper">
            <div class="account-tabs-nav" role="tablist">
                <button type="button" class="tab-btn active" id="tab-btn-enterprise" onclick="switchAccountTab('enterprise')" role="tab" aria-selected="true">
                    <i class="fa-solid fa-building-user"></i>
                    <span>Tài khoản Doanh nghiệp</span>
                    <span class="tab-counter-badge" id="badge-count-enterprise">{{ $subTotal }}</span>
                </button>
                <button type="button" class="tab-btn" id="tab-btn-personal" onclick="switchAccountTab('personal')" role="tab" aria-selected="false">
                    <i class="fa-solid fa-users"></i>
                    <span>Tài khoản Cá nhân (Khách hàng)</span>
                    <span class="tab-counter-badge" id="badge-count-personal">{{ $custTotal }}</span>
                </button>
            </div>
            <div class="tabs-subtext" id="tabsSubtext">
                <span id="tabStatusIndicator" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); background: #ffffff; padding: 6px 14px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: var(--shadow-subtle);">
                    <i class="fa-solid fa-circle-dot" id="tabDotIcon" style="color: var(--brand-primary); font-size: 10px;"></i>
                    <span id="tabStatusText">Hiển thị nhân viên / tài khoản con doanh nghiệp</span>
                </span>
            </div>
        </div>

        <!-- Khung Nội Dung Danh Sách Bảng -->
        <div class="content-card">
            <div class="card-header-bar">
                <div class="card-title-text" id="tableCardTitle">
                    <i class="fa-solid fa-building-user" style="color: var(--brand-primary);" id="cardTitleIcon"></i>
                    <span id="cardTitleText">Danh Sách Tài Khoản Con Của Doanh Nghiệp</span>
                </div>
                <div class="filter-group">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="filterSearch" class="search-input" placeholder="Tìm tên, Gmail, SĐT..." oninput="filterTable()">
                    </div>
                    <select id="filterStatus" class="select-filter" onchange="filterTable()" title="Lọc theo trạng thái">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="active">Đang hoạt động</option>
                        <option value="locked">Đang tạm khóa</option>
                    </select>
                    <button type="button" class="btn-act" onclick="refreshCurrentTable(this)" title="Làm mới dữ liệu" style="width: auto; padding: 0 14px; gap: 6px; font-weight: 700; border-radius: 8px;">
                        <i class="fa-solid fa-rotate-right"></i> Làm mới
                    </button>
                </div>
            </div>

            <!-- Mục 1: Bảng Danh Sách Tài Khoản Doanh Nghiệp (Nhân Viên Con) -->
            <div id="pane-enterprise" class="tab-pane active">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nhân Viên</th>
                                <th>Liên Hệ</th>
                                <th>Tòa Nhà Phụ Trách</th>
                                <th>Vai Trò</th>
                                <th>Hiệu Suất (KPI)</th>
                                <th>Trạng Thái</th>
                                <th style="text-align: right;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="sub-accounts-table-body">
                            @forelse($subAccounts ?? [] as $acc)
                                @php
                                    $accId = (string)($acc->_id ?? $acc->id);
                                    $isLocked = !empty($acc->is_locked) || ($acc->status ?? '') === 'locked';
                                    $displayName = $acc->account_name ?: ($acc->username ?: 'Nhân viên');
                                    $initial = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));

                                    $assigned = (array)($acc->assigned_buildings ?? []);

                                    // Xác định vai trò hiển thị
                                    $r = strtolower($acc->role ?? '');
                                    $sr = trim($acc->staff_role ?? '');
                                    $srLower = strtolower($sr);

                                    $isModAccount = !empty($acc->is_moderator)
                                        || in_array($r, ['kiemduyet', 'moderator', 'kiem_duyet'], true)
                                        || str_contains($srLower, 'kiểm duyệt')
                                        || str_contains($srLower, 'kiem duyet');

                                    if ($isModAccount) {
                                        $roleLabel = 'Kiểm duyệt';
                                        $roleBadgeClass = 'role-badge-mod';
                                        $roleIcon = 'fa-solid fa-user-shield';
                                    } elseif ($r === 'admin' || str_contains($srLower, 'admin') || str_contains($srLower, 'quản trị')) {
                                        $roleLabel = 'Quản trị viên';
                                        $roleBadgeClass = 'role-badge-admin';
                                        $roleIcon = 'fa-solid fa-crown';
                                    } else {
                                        // Tất cả tài khoản con do admin tạo đều hiển thị là Nhân sự
                                        $roleLabel = (!empty($sr) && !in_array($sr, ['Doanh nghiệp', 'Nhân viên', 'Nhân viên công ty'])) ? $sr : 'Nhân sự';
                                        $roleBadgeClass = 'role-badge-staff';
                                        $roleIcon = 'fa-solid fa-user-tie';
                                    }
                                @endphp
                                <tr data-acc-id="{{ $accId }}" data-name="{{ strtolower($displayName) }}" data-email="{{ strtolower($acc->email ?? '') }}" data-phone="{{ $acc->phone ?? '' }}" data-role="{{ strtolower($roleLabel) }}" data-status="{{ $isLocked ? 'locked' : 'active' }}">
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-box">{{ $initial }}</div>
                                            <div>
                                                <div style="font-weight: 800; color: var(--text-primary); font-size: 0.92rem;">{{ $displayName }}</div>
                                                <div style="font-size: 0.775rem; color: var(--text-secondary); margin-top: 2px;">
                                                    <i class="fa-regular fa-envelope" style="color: var(--brand-primary);"></i> {{ $acc->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">
                                            <i class="fa-solid fa-phone" style="color: var(--text-muted); width: 14px;"></i> {{ $acc->phone ?: 'Chưa cập nhật' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #0284c7; margin-top: 4px; font-weight: 600;">
                                            <i class="fa-solid fa-key" style="font-size: 11px;"></i> MK: <span style="font-family: monospace; background: #e0f2fe; color: #0369a1; padding: 1px 6px; border-radius: 4px; border: 1px solid #bae6fd;">{{ $acc->plain_password ?: '123456' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @php
                                                $bldDetails = $acc->assigned_buildings_details ?? [];
                                            @endphp
                                            @if($isModAccount)
                                                <span class="badge-bld-tag" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 600; font-size: 0.775rem; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;" title="Kiểm duyệt viên phụ trách toàn hệ thống">
                                                    <i class="fa-solid fa-shield-halved" style="color: #0284c7; font-size: 11px;"></i> Toàn hệ thống (Chỉ kiểm duyệt)
                                                </span>
                                            @elseif(!empty($bldDetails) && count($bldDetails) > 0)
                                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                    @foreach($bldDetails as $bd)
                                                        <span class="badge-bld-tag" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; font-size: 0.775rem; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                                                            <i class="fa-solid fa-city" style="font-size: 10px; color: #0284c7;"></i> {{ $bd->name ?? 'Tòa nhà' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="badge-bld-tag" style="background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; font-weight: 500; font-size: 0.775rem; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fa-solid fa-ban" style="font-size: 10px; color: #cbd5e1;"></i> Chưa phân công tòa nhà
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-badge {{ $roleBadgeClass }}">
                                            <i class="{{ $roleIcon }}"></i>
                                            <span>{{ $roleLabel }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="kpi-container">
                                            <div class="kpi-item">
                                                <i class="fa-solid fa-signs-post" style="color: var(--brand-primary); width: 14px;"></i>
                                                <span>Bài đăng: <span class="kpi-val">{{ $acc->posts_count ?? 0 }}</span></span>
                                            </div>
                                            <div class="kpi-item">
                                                <i class="fa-solid fa-handshake" style="color: var(--accent-emerald); width: 14px;"></i>
                                                <span>Hợp đồng: <span class="kpi-val" style="color: var(--accent-emerald);">{{ $acc->contracts_count ?? 0 }}</span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($isLocked)
                                            <span class="status-pill status-pill-locked">
                                                <i class="fa-solid fa-lock"></i> Đã khóa
                                            </span>
                                        @else
                                            <span class="status-pill status-pill-active">
                                                <i class="fa-solid fa-circle-check"></i> Hoạt động
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="btn-act" onclick="openEditModal('{{ $accId }}')" title="Sửa thông tin / Phân quyền lại / Đổi mật khẩu">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button type="button" class="btn-act btn-lock" onclick="toggleLockAccount('{{ $accId }}')" title="{{ $isLocked ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}">
                                                <i class="fa-solid {{ $isLocked ? 'fa-unlock' : 'fa-lock' }}"></i>
                                            </button>
                                            <button type="button" class="btn-act btn-delete" onclick="deleteAccount('{{ $accId }}')" title="Xóa tài khoản con">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row-initial">
                                    <td colspan="7" class="empty-placeholder">
                                        <i class="fa-solid fa-users-slash"></i>
                                        <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Chưa có tài khoản con nào</p>
                                        <p style="font-size: 0.825rem;">Nhấn nút "Tạo Tài Khoản Con" phía trên để tạo tài khoản nhân viên (nv1, nv2...) cho doanh nghiệp.</p>
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="sub-accounts-no-results" style="display: none;">
                                <td colspan="7" class="empty-placeholder" style="padding: 35px 20px;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Không tìm thấy nhân viên phù hợp</p>
                                    <p style="font-size: 0.825rem;">Vui lòng kiểm tra lại từ khóa tìm kiếm.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mục 2: Bảng Danh Sách Toàn Bộ Tài Khoản Khách Hàng Cá Nhân -->
            <div id="pane-personal" class="tab-pane" style="display: none;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tên Tài Khoản</th>
                                <th>Gmail</th>
                                <th>SĐT</th>
                                <th>Ngày Đăng Ký</th>
                                <th>Trạng Thái</th>
                                <th style="text-align: right;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="customer-accounts-table-body">
                            @forelse($customerAccounts ?? [] as $cust)
                                @php
                                    $cId = (string)($cust->_id ?? $cust->id);
                                    $isLocked = !empty($cust->is_locked) || ($cust->status ?? '') === 'locked' || ($cust->status ?? '') === 'khoa';
                                    $displayName = $cust->account_name ?: ($cust->username ?: 'Khách hàng');
                                    $initial = strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'));
                                    $email = $cust->email ?? '';
                                    $phone = $cust->phone ?? '';
                                    $createdAt = $cust->created_at ? $cust->created_at->format('d/m/Y H:i') : 'Mới đây';
                                @endphp
                                <tr data-customer-id="{{ $cId }}" data-name="{{ strtolower($displayName) }}" data-email="{{ strtolower($email) }}" data-phone="{{ $phone }}" data-status="{{ $isLocked ? 'locked' : 'active' }}">
                                    <td>
                                        <div class="user-cell">
                                            @if(!empty($cust->avatar))
                                                <div class="cust-avatar-box">
                                                    <img src="{{ $cust->display_avatar }}" alt="{{ $displayName }}" onerror="this.onerror=null; this.parentElement.innerHTML='{{ $initial }}';">
                                                </div>
                                            @else
                                                <div class="cust-avatar-box">
                                                    {{ $initial }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 800; color: var(--text-primary); font-size: 0.92rem;">{{ $displayName }}</div>
                                                <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 8px;">
                                                    <span>{{ '@' . ($cust->username ?: 'khachhang') }}</span>
                                                    <span style="display: inline-block; padding: 1px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; background: #e0f2fe; color: #0284c7;">Khách hàng / Cá nhân</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">
                                            <i class="fa-regular fa-envelope" style="color: var(--brand-primary); width: 14px;"></i> {{ $email ?: 'Chưa cập nhật' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem;">
                                            <i class="fa-solid fa-phone" style="color: var(--text-muted); width: 14px;"></i> {{ $phone ?: 'Chưa cập nhật' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.825rem;">
                                            <i class="fa-regular fa-calendar-days" style="color: var(--text-muted); margin-right: 4px;"></i> {{ $createdAt }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($isLocked)
                                            <span class="status-pill status-pill-locked">
                                                <i class="fa-solid fa-lock"></i> Tạm khóa
                                            </span>
                                        @else
                                            <span class="status-pill status-pill-active">
                                                <i class="fa-solid fa-circle-check"></i> Hoạt động
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="btn-act btn-lock" onclick="toggleLockCustomer('{{ $cId }}')" title="{{ $isLocked ? 'Mở khóa tài khoản khách hàng' : 'Tạm khóa tài khoản khách hàng' }}">
                                                <i class="fa-solid {{ $isLocked ? 'fa-unlock' : 'fa-lock' }}"></i>
                                            </button>
                                            <button type="button" class="btn-act btn-delete" onclick="deleteCustomer('{{ $cId }}')" title="Xóa tài khoản khách hàng">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row-customers">
                                    <td colspan="6" class="empty-placeholder">
                                        <i class="fa-solid fa-users-slash"></i>
                                        <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Chưa có tài khoản khách hàng cá nhân nào</p>
                                        <p style="font-size: 0.825rem;">Hệ thống chưa ghi nhận khách hàng cá nhân tự đăng ký tài khoản.</p>
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="customer-accounts-no-results" style="display: none;">
                                <td colspan="6" class="empty-placeholder" style="padding: 35px 20px;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Không tìm thấy khách hàng phù hợp</p>
                                    <p style="font-size: 0.825rem;">Vui lòng kiểm tra lại từ khóa tìm kiếm.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tạo / Sửa Tài Khoản Con (Phần A) -->
    <div id="subAccountModal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">
                    <i class="fa-solid fa-user-plus" style="color: var(--brand-primary);"></i>
                    <span>Tạo Tài Khoản Con (Nhân Viên Mới)</span>
                </h3>
                <button type="button" onclick="closeModal()" style="border: none; background: transparent; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="subAccountForm" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="editAccountId" value="">

                <div class="modal-body">
                    
                    <!-- Mục 1: Thông tin định danh & đăng nhập -->
                    <div class="form-section-title">
                        <i class="fa-solid fa-id-card"></i> 1. Thông Tin Định Danh & Đăng Nhập
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="acc_name">Họ và tên nhân viên <span style="color: var(--accent-red)">*</span></label>
                            <input type="text" id="acc_name" class="form-input" placeholder="VD: Nguyễn Văn Nhân Viên" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="acc_email">Gmail / Email đăng nhập <span style="color: var(--accent-red)">*</span></label>
                            <input type="email" id="acc_email" class="form-input" placeholder="VD: nhanvien@gmail.com" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="acc_password">Mật khẩu đăng nhập <span id="pwRequiredStar" style="color: var(--accent-red)">*</span></label>
                            <div class="input-group-addon">
                                <input type="password" id="acc_password" class="form-input" placeholder="Tối thiểu 6 ký tự" minlength="6">
                                <div class="pw-actions">
                                    <button type="button" class="btn-input-mini" onclick="togglePasswordVisibility()" title="Ẩn/Hiện mật khẩu">
                                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                                    </button>
                                    <button type="button" class="btn-input-mini" onclick="generateRandomPassword()" title="Tạo mật khẩu ngẫu nhiên">
                                        <i class="fa-solid fa-dice"></i> Ngẫu nhiên
                                    </button>
                                </div>
                            </div>
                            <small id="pwHint" style="font-size: 0.725rem; color: var(--text-muted); display: none; margin-top: 4px;">Để trống nếu không muốn đổi mật khẩu</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="acc_phone">Số điện thoại / Zalo công việc <span style="color: var(--accent-red)">*</span></label>
                            <input type="text" id="acc_phone" class="form-input" placeholder="VD: 0912345678 (Gắn vào bài đăng)" required>
                        </div>
                    </div>


                    <!-- Mục 2: Phân công tòa nhà phụ trách -->
                    <div id="buildingSectionContainer">
                        <div class="form-section-title" style="margin-top: 20px;">
                            <i class="fa-solid fa-city"></i> 2. Phân Công Tòa Nhà Phụ Trách
                        </div>

                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="form-label" for="acc_building_select" style="margin-bottom: 0;">
                                    Danh sách tòa nhà phụ trách
                                </label>
                                <span id="buildingCountBadge" style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; background: #f1f5f9; padding: 2px 8px; border-radius: 6px;">
                                    Chưa phân công tòa nhà
                                </span>
                            </div>

                            <select id="acc_building_select" class="form-select" onchange="handleBuildingSelect(this.value)">
                                <option value="">-- Chọn tòa nhà để phân công --</option>
                                @forelse($allBuildings ?? [] as $b)
                                    @php $bId = (string)($b->_id ?? $b->id); @endphp
                                    <option value="{{ $bId }}">
                                        🏢 {{ $b->name ?? 'Tòa nhà' }}{{ !empty($b->address_detail) ? ' - ' . $b->address_detail : '' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Chưa có tòa nhà nào trong hệ thống</option>
                                @endforelse
                            </select>

                            <!-- Danh sách tags các tòa nhà đã phân công (nếu chọn tòa nhà cụ thể) -->
                            <div id="selectedBuildingsTags" style="display: none; margin-top: 8px; flex-wrap: wrap; gap: 6px;"></div>

                            <small id="buildingHelperText" style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">
                                <i class="fa-solid fa-circle-info"></i> Chọn một hoặc nhiều tòa nhà từ danh sách đổ xuống để giao cho nhân viên trực tiếp quản lý.
                            </small>
                        </div>
                    </div>

                    <!-- Thông báo cố định nếu là Kiểm duyệt viên -->
                    <div id="moderatorBuildingNotice" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-top: 20px;">
                        <div style="display: flex; align-items: center; gap: 10px; color: #334155;">
                            <i class="fa-solid fa-shield-halved" style="color: #0284c7; font-size: 1.25rem;"></i>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Toàn hệ thống (Chỉ kiểm duyệt)</div>
                                <div style="font-size: 0.775rem; color: var(--text-secondary); margin-top: 2px;">Tài khoản Kiểm duyệt viên làm nhiệm vụ duyệt bài đăng và xử lý khiếu nại trên toàn hệ thống, không trực tiếp quản lý tòa nhà.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mục 3: Quyền hạn nghiệp vụ cố định -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 22px; margin-bottom: 6px;">
                        <div class="form-section-title" style="margin: 0;">
                            <i class="fa-solid fa-shield-halved"></i> 3. Quyền Hạn Nghiệp Vụ Nhân Viên (Cố Định)
                        </div>
                        <span style="font-size: 0.725rem; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-lock" style="font-size: 10px;"></i> Cố định toàn quyền
                        </span>
                    </div>
                    <p style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 12px; margin-top: -2px;">
                        <i class="fa-solid fa-circle-info"></i> Toàn bộ nhân viên được cấp cố định 4 quyền hạn nghiệp vụ cốt lõi sau đây:
                    </p>

                    <div class="permissions-grid">
                        <div class="perm-card-item">
                            <div class="perm-icon-box" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            <div class="perm-card-info" style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <h4>Quyền Đăng Tin & Bài Đăng</h4>
                                    <span class="badge-fixed-lock"><i class="fa-solid fa-check"></i> Đã cấp</span>
                                </div>
                                <p>Được tạo, chỉnh sửa bài đăng cho thuê trên website và gắn số điện thoại nhân viên.</p>
                            </div>
                        </div>

                        <div class="perm-card-item">
                            <div class="perm-icon-box" style="background: rgba(5, 150, 105, 0.12); color: #059669;">
                                <i class="fa-solid fa-phone-volume"></i>
                            </div>
                            <div class="perm-card-info" style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <h4>Tiếp Nhận Khách & Đặt Lịch</h4>
                                    <span class="badge-fixed-lock"><i class="fa-solid fa-check"></i> Đã cấp</span>
                                </div>
                                <p>Xem danh sách khách gửi liên hệ, chat tư vấn và xác nhận lịch hẹn xem phòng.</p>
                            </div>
                        </div>

                        <div class="perm-card-item">
                            <div class="perm-icon-box" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div class="perm-card-info" style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <h4>Khởi Tạo & Quản Lý Hợp Đồng</h4>
                                    <span class="badge-fixed-lock"><i class="fa-solid fa-check"></i> Đã cấp</span>
                                </div>
                                <p>Tạo hợp đồng thuê mới, tính tiền cọc và quản lý khách thuê tại các phòng.</p>
                            </div>
                        </div>

                        <div class="perm-card-item">
                            <div class="perm-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div class="perm-card-info" style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <h4>Chốt Điện Nước & Hóa Đơn</h4>
                                    <span class="badge-fixed-lock"><i class="fa-solid fa-check"></i> Đã cấp</span>
                                </div>
                                <p>Nhập chỉ số điện nước hàng tháng, phát hành hóa đơn theo tài khoản công ty.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy Bỏ</button>
                    <button type="submit" id="btnSubmitForm" class="btn-create">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Tài Khoản Con
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Script xử lý Logic & Gọi API -->
    <script>

        // ================== LOGIC CHUYỂN ĐỔI 2 TAB (DOANH NGHIỆP / CÁ NHÂN) ==================
        let currentActiveTab = 'enterprise';

        const statsConfig = {
            enterprise: {
                total: {{ $subTotal ?? 0 }},
                active: {{ $subActive ?? 0 }},
                locked: {{ $subLocked ?? 0 }},
                fourth: {{ $buildingsCount ?? 0 }},
                showFourth: true,
                labelTotal: 'Tổng Tài Khoản Con',
                labelFourth: 'Tòa Nhà Trong Hệ Thống',
                iconTotal: 'fa-solid fa-users-gear',
                iconFourth: 'fa-solid fa-city',
                cardTitle: 'Danh Sách Tài Khoản Con Của Doanh Nghiệp',
                cardIcon: 'fa-solid fa-building-user',
                statusText: 'Hiển thị nhân viên / tài khoản con doanh nghiệp',
                dotColor: 'var(--brand-primary)',
                showCreateBtn: true
            },
            personal: {
                total: {{ $custTotal ?? 0 }},
                active: {{ $custActive ?? 0 }},
                locked: {{ $custLocked ?? 0 }},
                fourth: 0,
                showFourth: false,
                labelTotal: 'Tổng Khách Hàng',
                labelFourth: '',
                iconTotal: 'fa-solid fa-users',
                iconFourth: '',
                cardTitle: 'Danh Sách Tài Khoản Cá Nhân (Khách Hàng)',
                cardIcon: 'fa-solid fa-users',
                statusText: 'Hiển thị toàn bộ tài khoản khách hàng cá nhân',
                dotColor: 'var(--accent-emerald)',
                showCreateBtn: false
            }
        };

        function switchAccountTab(tabName) {
            currentActiveTab = tabName;

            // 1. Đổi trạng thái Active của 2 nút bấm
            const btnEnt = document.getElementById('tab-btn-enterprise');
            const btnPer = document.getElementById('tab-btn-personal');
            const paneEnt = document.getElementById('pane-enterprise');
            const panePer = document.getElementById('pane-personal');

            if (tabName === 'enterprise') {
                btnEnt.classList.add('active');
                btnEnt.setAttribute('aria-selected', 'true');
                btnPer.classList.remove('active');
                btnPer.setAttribute('aria-selected', 'false');

                paneEnt.style.display = 'block';
                panePer.style.display = 'none';
            } else {
                btnPer.classList.add('active');
                btnPer.setAttribute('aria-selected', 'true');
                btnEnt.classList.remove('active');
                btnEnt.setAttribute('aria-selected', 'false');

                panePer.style.display = 'block';
                paneEnt.style.display = 'none';
            }

            // 2. Cập nhật các ô thống kê KPI, tiêu đề bảng và nút Tạo tài khoản con
            const cfg = statsConfig[tabName];
            if (cfg) {
                document.getElementById('stat-total-accounts').textContent = cfg.total;
                document.getElementById('stat-active-accounts').textContent = cfg.active;
                document.getElementById('stat-locked-accounts').textContent = cfg.locked;

                document.getElementById('stat-label-total').textContent = cfg.labelTotal;
                document.getElementById('stat-icon-total').className = cfg.iconTotal;

                // Thẻ thống kê thứ 4: Ẩn khi sang mục cá nhân (xóa thống kê tài khoản tự đăng ký), hiện khi ở doanh nghiệp
                const cardFourth = document.getElementById('stat-card-fourth');
                if (cardFourth) {
                    if (cfg.showFourth) {
                        cardFourth.style.display = 'flex';
                        const valFourth = document.getElementById('stat-total-buildings');
                        const labelFourth = document.getElementById('stat-label-fourth');
                        const iconFourth = document.getElementById('stat-icon-fourth');
                        if (valFourth) valFourth.textContent = cfg.fourth;
                        if (labelFourth) labelFourth.textContent = cfg.labelFourth;
                        if (iconFourth) iconFourth.className = cfg.iconFourth;
                    } else {
                        cardFourth.style.display = 'none';
                    }
                }

                document.getElementById('cardTitleText').textContent = cfg.cardTitle;
                document.getElementById('cardTitleIcon').className = cfg.cardIcon;
                document.getElementById('tabStatusText').textContent = cfg.statusText;
                document.getElementById('tabDotIcon').style.color = cfg.dotColor;

                const createBtnBox = document.getElementById('headerBtnCreateContainer');
                if (createBtnBox) {
                    createBtnBox.style.display = cfg.showCreateBtn ? 'block' : 'none';
                }
            }

            // 3. Áp dụng lại bộ lọc tìm kiếm cho bảng mới mở
            filterTable();
        }

        // Ghi đè hàm filterTable() để lọc được cho cả 2 bảng (Doanh nghiệp & Cá nhân) theo từ khóa và trạng thái
        function filterTable() {
            const q = document.getElementById('filterSearch').value.toLowerCase().trim();
            const statusFilter = document.getElementById('filterStatus') ? document.getElementById('filterStatus').value : 'all';

            if (currentActiveTab === 'enterprise') {
                const rows = document.querySelectorAll('#sub-accounts-table-body tr[data-acc-id]');
                let visible = 0;
                rows.forEach(r => {
                    const name = r.dataset.name || '';
                    const email = r.dataset.email || '';
                    const phone = r.dataset.phone || '';
                    const role = r.dataset.role || '';
                    const status = r.dataset.status || 'active';

                    const matchQ = !q || name.includes(q) || email.includes(q) || phone.includes(q) || role.includes(q);
                    const matchStatus = (statusFilter === 'all') || (status === statusFilter);

                    const match = matchQ && matchStatus;
                    r.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                const noRes = document.getElementById('sub-accounts-no-results');
                if (noRes) noRes.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            } else {
                const rows = document.querySelectorAll('#customer-accounts-table-body tr[data-customer-id]');
                let visible = 0;
                rows.forEach(r => {
                    const name = r.dataset.name || '';
                    const email = r.dataset.email || '';
                    const phone = r.dataset.phone || '';
                    const status = r.dataset.status || 'active';

                    const matchQ = !q || name.includes(q) || email.includes(q) || phone.includes(q);
                    const matchStatus = (statusFilter === 'all') || (status === statusFilter);

                    const match = matchQ && matchStatus;
                    r.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                const noRes = document.getElementById('customer-accounts-no-results');
                if (noRes) noRes.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            }
        }

        // Làm mới bảng hiện tại
        function refreshCurrentTable(btn) {
            const icon = btn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
            setTimeout(() => location.reload(), 300);
        }

        // Khóa / Mở khóa tài khoản khách hàng cá nhân
        async function toggleLockCustomer(cId) {
            if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái khóa/mở khóa khách hàng này?')) return;
            try {
                const res = await fetch(`/api/customer-accounts/${cId}/toggle-lock`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Cập nhật trạng thái khách hàng thành công!', 'success');
                    sessionStorage.setItem('active_account_tab', 'personal');
                    setTimeout(() => location.reload(), 700);
                } else {
                    showToast(data.message || 'Không thể cập nhật trạng thái!', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }

        // Xóa tài khoản khách hàng cá nhân
        async function deleteCustomer(cId) {
            if (!confirm('Bạn có chắc chắn muốn xóa tài khoản khách hàng này?')) return;
            try {
                const res = await fetch(`/api/customer-accounts/${cId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Đã xóa tài khoản khách hàng!', 'success');
                    sessionStorage.setItem('active_account_tab', 'personal');
                    setTimeout(() => location.reload(), 700);
                } else {
                    showToast(data.message || 'Không thể xóa tài khoản!', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }
        
        let subAccountsData = @json($subAccounts ?? []);
        let allBuildingsData = @json($allBuildings ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            // Khôi phục tab đang chọn trước đó nếu vừa reload
            const savedTab = sessionStorage.getItem('active_account_tab');
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'personal' || savedTab === 'personal') {
                switchAccountTab('personal');
            }

            // Check nếu URL có action=create thì mở modal ngay
            if (urlParams.get('action') === 'create') {
                openCreateModal();
            }
        });

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast-item toast-${type}`;
            const icon = type === 'success' ? 'fa-circle-check text-emerald' : 'fa-triangle-exclamation text-red';
            const color = type === 'success' ? '#059669' : '#dc2626';
            toast.innerHTML = `<i class="fa-solid ${icon}" style="color: ${color}; font-size: 1.1rem;"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Ẩn / hiện mật khẩu
        function togglePasswordVisibility() {
            const pwInput = document.getElementById('acc_password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                eyeIcon.className = 'fa-regular fa-eye-slash';
            } else {
                pwInput.type = 'password';
                eyeIcon.className = 'fa-regular fa-eye';
            }
        }

        // Tạo mật khẩu ngẫu nhiên
        function generateRandomPassword() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#';
            let pass = '';
            for (let i = 0; i < 8; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const pwInput = document.getElementById('acc_password');
            pwInput.type = 'text';
            pwInput.value = pass;
            document.getElementById('eyeIcon').className = 'fa-regular fa-eye-slash';
            showToast(`Đã tạo mật khẩu ngẫu nhiên: ${pass}`, 'success');
        }


        // Tòa nhà Phân công Logic (Dropdown & Tags)
        let selectedBuildingIds = [];

        function handleBuildingSelect(val) {
            if (!val) return;
            if (val === 'all') return;
            if (!selectedBuildingIds.includes(val)) {
                selectedBuildingIds.push(val);
            }
            renderBuildingSelection();
        }

        function removeBuildingSelection(id) {
            selectedBuildingIds = selectedBuildingIds.filter(b => b !== id);
            renderBuildingSelection();
        }

        function renderBuildingSelection() {
            const selectEl = document.getElementById('acc_building_select');
            const tagsContainer = document.getElementById('selectedBuildingsTags');
            const badgeEl = document.getElementById('buildingCountBadge');
            const helperEl = document.getElementById('buildingHelperText');
            if (!selectEl) return;

            selectEl.value = '';

            if (selectedBuildingIds.length === 0) {
                if (tagsContainer) {
                    tagsContainer.style.display = 'none';
                    tagsContainer.innerHTML = '';
                }
                if (badgeEl) {
                    badgeEl.textContent = 'Chưa phân công tòa nhà';
                    badgeEl.style.color = '#94a3b8';
                    badgeEl.style.background = '#f1f5f9';
                }
                if (helperEl) {
                    helperEl.innerHTML = '<i class="fa-solid fa-circle-info"></i> Chọn một hoặc nhiều tòa nhà từ danh sách đổ xuống để giao cho nhân viên trực tiếp quản lý.';
                }
            } else {
                if (tagsContainer) {
                    tagsContainer.style.display = 'flex';
                    let tagsHtml = '';
                    selectedBuildingIds.forEach(id => {
                        const b = (allBuildingsData && allBuildingsData.length > 0)
                            ? allBuildingsData.find(item => (item._id || item.id).toString() === id.toString())
                            : null;
                        const bName = b ? (b.name || 'Tòa nhà') : 'Tòa nhà #' + id.substr(-4);
                        tagsHtml += `
                            <span style="display: inline-flex; align-items: center; gap: 6px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 8px;">
                                <i class="fa-solid fa-city" style="font-size: 11px;"></i> ${bName}
                                <button type="button" onclick="removeBuildingSelection('${id}')" style="background: none; border: none; color: #0284c7; cursor: pointer; padding: 0; font-size: 14px; font-weight: 800; line-height: 1;" title="Bỏ chọn">&times;</button>
                            </span>
                        `;
                    });
                    tagsContainer.innerHTML = tagsHtml;
                }
                if (badgeEl) {
                    badgeEl.textContent = selectedBuildingIds.length + ' tòa nhà phụ trách';
                    badgeEl.style.color = '#059669';
                    badgeEl.style.background = '#dcfce7';
                }
                if (helperEl) {
                    helperEl.innerHTML = '<i class="fa-solid fa-circle-plus"></i> Có thể chọn thêm tòa nhà khác từ danh sách đổ xuống để giao thêm cho nhân viên.';
                }
            }
        }

        // Modal Controls
        function openCreateModal() {
            document.getElementById('editAccountId').value = '';
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-plus" style="color: var(--brand-primary);"></i> <span>Tạo Tài Khoản Con (Nhân Viên Mới)</span>';
            document.getElementById('subAccountForm').reset();
            
            document.getElementById('acc_email').readOnly = false;
            const pwInput = document.getElementById('acc_password');
            const eyeIcon = document.getElementById('eyeIcon');
            pwInput.value = '';
            pwInput.type = 'password';
            if (eyeIcon) eyeIcon.className = 'fa-regular fa-eye';
            pwInput.required = true;
            document.getElementById('pwRequiredStar').style.display = 'inline';
            document.getElementById('pwHint').style.display = 'none';

            const bldContainer = document.getElementById('buildingSectionContainer');
            const modNotice = document.getElementById('moderatorBuildingNotice');
            if (bldContainer) bldContainer.style.display = 'block';
            if (modNotice) modNotice.style.display = 'none';

            selectedBuildingIds = [];
            renderBuildingSelection();

            document.getElementById('subAccountModal').classList.add('active');
        }

        function openEditModal(accId) {
            const acc = subAccountsData.find(a => (a._id || a.id).toString() === accId.toString());
            if (!acc) return;

            document.getElementById('editAccountId').value = accId;
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-pen" style="color: var(--brand-primary);"></i> <span>Chỉnh Sửa & Phân Quyền Lại Tài Khoản Con</span>';
            
            document.getElementById('acc_name').value = acc.account_name || '';
            document.getElementById('acc_email').value = acc.email || '';
            document.getElementById('acc_email').readOnly = true;
            document.getElementById('acc_phone').value = acc.phone || '';
            
            const pwInput = document.getElementById('acc_password');
            const eyeIcon = document.getElementById('eyeIcon');
            pwInput.value = acc.plain_password || '123456';
            pwInput.type = 'text';
            if (eyeIcon) eyeIcon.className = 'fa-regular fa-eye-slash';
            pwInput.required = false;
            document.getElementById('pwRequiredStar').style.display = 'none';
            document.getElementById('pwHint').style.display = 'block';
            document.getElementById('pwHint').textContent = 'Mật khẩu đã tạo trước đó (giữ nguyên hoặc nhập mật khẩu mới nếu muốn đổi)';

            const isMod = acc.is_moderator 
                || ['kiemduyet', 'moderator', 'kiem_duyet'].includes((acc.role || '').toLowerCase())
                || (acc.staff_role || '').toLowerCase().includes('kiểm duyệt');

            const bldContainer = document.getElementById('buildingSectionContainer');
            const modNotice = document.getElementById('moderatorBuildingNotice');

            if (isMod) {
                if (bldContainer) bldContainer.style.display = 'none';
                if (modNotice) modNotice.style.display = 'block';
                selectedBuildingIds = [];
            } else {
                if (bldContainer) bldContainer.style.display = 'block';
                if (modNotice) modNotice.style.display = 'none';

                // Tòa nhà phụ trách của nhân viên
                const assigned = (acc.assigned_buildings || []).filter(b => b !== 'all' && b !== '*');
                selectedBuildingIds = assigned.map(String);
                renderBuildingSelection();
            }

            document.getElementById('subAccountModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('subAccountModal').classList.remove('active');
        }

        // Tải lại dữ liệu từ API
        async function loadSubAccounts() {
            try {
                const res = await fetch('/api/sub-accounts', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    subAccountsData = data.sub_accounts || [];
                    allBuildingsData = data.all_buildings || [];
                    location.reload(); // Reload nhẹ để đồng bộ cả thống kê Blade
                }
            } catch (e) {
                console.error(e);
            }
        }

        // Submit Form
        async function handleFormSubmit(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btnSubmitForm');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            const editId = document.getElementById('editAccountId').value;
            const isEdit = !!editId;
            const existingAcc = isEdit ? subAccountsData.find(a => (a._id || a.id).toString() === editId.toString()) : null;

            const isMod = existingAcc && (
                existingAcc.is_moderator ||
                ['kiemduyet', 'moderator', 'kiem_duyet'].includes((existingAcc.role || '').toLowerCase()) ||
                (existingAcc.staff_role || '').toLowerCase().includes('kiểm duyệt')
            );

            const name = document.getElementById('acc_name').value.trim();
            const email = document.getElementById('acc_email').value.trim();
            const phone = document.getElementById('acc_phone').value.trim();
            const password = document.getElementById('acc_password').value;

            // Tòa nhà phụ trách: Nếu là Kiểm duyệt viên thì luôn là [], nếu là Sales thì lấy theo danh sách đã chọn
            let assignedBuildings = isMod ? [] : selectedBuildingIds.filter(b => b !== 'all' && b !== '*');

            // Permissions cố định toàn bộ 4 quyền cốt lõi
            const permissions = ['manage_posts', 'manage_contacts', 'manage_contracts', 'manage_invoices'];

            const payload = {
                name,
                email,
                phone,
                role: isMod ? 'kiemduyet' : 'nhansu',
                staff_role: isMod ? 'Kiểm duyệt viên' : 'Nhân sự',
                assigned_buildings: assignedBuildings,
                permissions: permissions
            };

            if (password) {
                payload.password = password;
            }

            try {
                const url = isEdit ? `/api/sub-accounts/${editId}` : '/api/sub-accounts';
                const method = isEdit ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    showToast(data.message || data.error || 'Có lỗi xảy ra khi lưu!', 'error');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu Tài Khoản Con';
                    return;
                }

                showToast(data.message || 'Thao tác thành công!', 'success');
                closeModal();
                setTimeout(() => location.reload(), 800);

            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu Tài Khoản Con';
            }
        }

        // Toggle Khóa / Mở khóa
        async function toggleLockAccount(accId) {
            if (!confirm('Bạn có chắc chắn muốn thay đổi trạng thái khóa/mở khóa tài khoản này?')) return;

            try {
                const res = await fetch(`/api/sub-accounts/${accId}/toggle-lock`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Cập nhật trạng thái thành công!', 'success');
                    setTimeout(() => location.reload(), 700);
                } else {
                    showToast(data.message || data.error || 'Có lỗi xảy ra!', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }

        // Xóa tài khoản con
        async function deleteAccount(accId) {
            if (!confirm('XÁC NHẬN: Bạn có chắc chắn muốn xóa tài khoản con này khỏi hệ thống? Dữ liệu bài đăng và hợp đồng vẫn thuộc doanh nghiệp nhưng tài khoản sẽ không thể đăng nhập nữa.')) return;

            try {
                const res = await fetch(`/api/sub-accounts/${accId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Đã xóa tài khoản con!', 'success');
                    setTimeout(() => location.reload(), 700);
                } else {
                    showToast(data.message || data.error || 'Có lỗi xảy ra!', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }
    </script>
</body>
</html>
