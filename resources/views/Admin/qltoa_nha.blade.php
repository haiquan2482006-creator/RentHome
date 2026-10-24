<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản Lý Tòa Nhà & Cơ Sở - RentHome Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        /* SweetAlert2 Custom Styling */
        .swal2-popup.swal2-custom-popup {
            border-radius: 18px !important;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            padding: 24px !important;
        }
        .swal2-title {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            letter-spacing: -0.01em;
        }
        .swal2-styled.swal2-confirm, .swal2-styled.swal2-cancel {
            border-radius: 10px !important;
            font-weight: 700 !important;
            padding: 10px 22px !important;
            font-size: 0.9rem !important;
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
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid var(--border-color);
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
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Content Card */
        .content-card {
            background-color: var(--bg-card);
            border-radius: 14px;
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
        }

        .card-title-text {
            font-size: 1.05rem;
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
            display: flex;
            align-items: center;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .search-input {
            padding: 8px 14px 8px 36px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            width: 260px;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .search-input:focus {
            border-color: var(--brand-primary);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            border-color: var(--brand-primary);
            background-color: #ffffff;
        }

        /* Table */
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
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-weight: 700;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .data-table td {
            padding: 16px 18px;
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

        /* Building Cell (Cột 1: Cơ Sở) */
        .building-cell {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .building-thumb {
            width: 68px;
            height: 54px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
            background-color: #e2e8f0;
        }

        .building-thumb-placeholder {
            width: 68px;
            height: 54px;
            border-radius: 8px;
            background-color: #e0f2fe;
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            border: 1px solid #bae6fd;
        }

        .building-name {
            font-weight: 800;
            color: var(--text-primary);
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.725rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            margin-top: 4px;
        }

        .type-ccmini { background-color: #e0f2fe; color: #0284c7; }
        .type-chdv { background-color: #f3e8ff; color: #7c3aed; }
        .type-ktx { background-color: #fef3c7; color: #b45309; }
        .type-nhatro { background-color: #ecfdf5; color: #059669; }
        .type-nhanguyencan { background-color: #fef2f2; color: #dc2626; }
        .type-lienke { background-color: #fdf4ff; color: #a21caf; }
        .type-datnen { background-color: #f0fdf4; color: #166534; }
        .type-datphanlo { background-color: #ecfeff; color: #0891b2; }
        .type-matbang { background-color: #fff7ed; color: #c2410c; }
        .type-vanphong { background-color: #e0e7ff; color: #4338ca; }

        /* Cột 3: Quy Mô & Dịch Vụ */
        .scale-service-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.775rem;
        }

        .scale-service-grid .price-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
        }

        .scale-service-grid .price-val {
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Action Buttons (Cột 5: Thao Tác) */
        .action-btns {
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: flex-end;
        }

        .btn-act {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-secondary);
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-act:hover {
            background-color: var(--bg-main);
            color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .btn-act-rooms {
            background-color: #f0f9ff;
            color: var(--brand-primary);
            border-color: #bae6fd;
            font-weight: 700;
        }

        .btn-act-rooms:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
        }

        .btn-act-danger:hover {
            background-color: var(--accent-red-bg);
            color: var(--accent-red);
            border-color: var(--accent-red);
        }

        /* Empty State */
        .empty-state {
            padding: 48px 24px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 12px;
            color: #cbd5e1;
        }

        .empty-state h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        /* Modal Overlay & Container */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            padding: 16px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            transform: scale(0.96);
            transition: var(--transition);
            overflow: hidden;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1);
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-close-modal {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close-modal:hover {
            color: var(--text-primary);
            background-color: var(--bg-main);
        }

        #buildingForm {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            max-height: calc(90vh - 130px);
        }

        .modal-footer {
            padding: 14px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            background-color: #f8fafc;
            flex-shrink: 0;
        }

        .btn-cancel {
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.875rem;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background-color: var(--bg-main);
            color: var(--text-primary);
        }

        /* Form Card Groups */
        .form-section-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .form-section-title {
            font-size: 0.875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--brand-primary);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 8px;
        }

        .form-row-1 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 12px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 12px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            margin-bottom: 12px;
        }

        /* Switch Toggle cho Bảng giá dịch vụ Nhóm 2 */
        .service-pricing-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }
        .service-pricing-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .service-pricing-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #cbd5e1;
            transition: .25s ease-in-out;
            border-radius: 9999px;
        }
        .service-pricing-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .25s ease-in-out;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.25);
        }
        .service-pricing-switch input:checked + .service-pricing-slider {
            background-color: var(--brand-primary);
        }
        .service-pricing-switch input:checked + .service-pricing-slider:before {
            transform: translateX(20px);
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-input, .form-select, .form-textarea {
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

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .form-textarea {
            resize: vertical;
            min-height: 70px;
        }

        .input-group-unit {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-unit .form-input {
            padding-right: 70px;
        }

        .unit-label {
            position: absolute;
            right: 12px;
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--text-muted);
            pointer-events: none;
        }

        /* Ghi chú tiền tệ VNĐ */
        .money-vnd-hint {
            margin-top: 6px;
            background-color: #f0f9ff;
            border: 1px dashed #bae6fd;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 0.775rem;
            color: #0369a1;
            line-height: 1.35;
        }

        .money-vnd-hint .hint-main {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .money-vnd-hint i {
            color: #0284c7;
            font-size: 0.75rem;
        }

        .money-vnd-hint .vnd-text {
            color: #0284c7;
            font-weight: 800;
        }

        .money-vnd-hint .vnd-words {
            display: block;
            font-size: 0.715rem;
            color: #64748b;
            font-style: italic;
            margin-top: 2px;
        }

        /* Notice Banner */
        .notice-banner {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #0369a1;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        /* Amenities Grid Checkboxes */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .amenity-chk-card {
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            background-color: #f8fafc;
            transition: var(--transition);
            user-select: none;
        }

        .amenity-chk-card:hover {
            border-color: rgba(2, 132, 199, 0.4);
            background-color: #f0f9ff;
        }

        .amenity-chk-card input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--brand-primary);
            cursor: pointer;
        }

        .amenity-chk-card.checked {
            border-color: var(--brand-primary);
            background-color: #f0f9ff;
            color: #0284c7;
            font-weight: 700;
        }

        .amenity-chk-card span {
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Uploader Ảnh Bìa / Cơ Sở */
        .uploader-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .image-counter-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
            transition: var(--transition);
        }

        .image-counter-badge.full {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .image-dropzone {
            border: 2px dashed #bae6fd;
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            margin-bottom: 12px;
            user-select: none;
        }

        .image-dropzone:hover, .image-dropzone.dragover {
            border-color: var(--brand-primary);
            background: #f0f9ff;
            transform: translateY(-1px);
        }

        .image-dropzone i {
            font-size: 1.8rem;
            color: var(--brand-primary);
            margin-bottom: 6px;
            display: inline-block;
        }

        .image-dropzone-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .image-dropzone-hint {
            font-size: 0.725rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .image-preview-gallery {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }

        .gallery-slot {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 10px;
            overflow: hidden;
            border: 1.5px solid var(--border-color);
            background-color: #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: var(--transition);
        }

        .gallery-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .gallery-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-slot-badge {
            position: absolute;
            top: 5px;
            left: 5px;
            background: rgba(2, 132, 199, 0.92);
            color: #ffffff;
            font-size: 0.625rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            backdrop-filter: blur(4px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            pointer-events: none;
        }

        .gallery-slot-delete {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.9);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .gallery-slot-delete:hover {
            background: #b91c1c;
            transform: scale(1.15);
        }

        .gallery-empty-hint {
            grid-column: span 5;
            text-align: center;
            padding: 12px;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-style: italic;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px dashed var(--border-color);
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-item {
            padding: 12px 18px;
            border-radius: 10px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease;
        }

        .toast-success { background-color: var(--accent-emerald); }
        .toast-error { background-color: var(--accent-red); }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 900px) {
            .form-row-2, .form-row-3 {
                grid-template-columns: 1fr;
            }
            .admin-main {
                padding: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin chung của hệ thống RentHome -->
    @include('partials.header-admin')

    <main class="admin-main">
        <!-- Header trang -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fa-solid fa-city" style="color: var(--brand-primary);"></i>
                    Quản Lý Tòa Nhà & Cơ Sở (Lớp Vỏ)
                </h1>
                <p class="page-subtitle">
                    Hệ thống tạo và phân công quản lý cơ sở kinh doanh - Cấu hình bảng giá dịch vụ dùng chung & phân quyền Sale
                </p>
            </div>
            <div>
                <button type="button" class="btn-create" onclick="openCreateModal()" title="Thêm Tòa Nhà / Cơ Sở Mới">
                    <i class="fa-solid fa-plus"></i> + Thêm Tòa Nhà Mới
                </button>
            </div>
        </div>

        <!-- Thống kê nhanh BĐS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--brand-subtle); color: var(--brand-primary);">
                    <i class="fa-solid fa-city"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-total-buildings">{{ $stats['total_buildings'] ?? count($buildings ?? []) }}</div>
                    <div class="stat-label">Tổng Cơ Sở / Tòa Nhà</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--accent-emerald-bg); color: var(--accent-emerald);">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-total-rooms">{{ $stats['total_rooms'] ?? 0 }}</div>
                    <div class="stat-label">Tổng Số Phòng Trực Thuộc</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--accent-purple-bg); color: var(--accent-purple);">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-assigned-buildings">{{ $stats['assigned_buildings'] ?? $buildings->filter(fn($b) => !empty($b->manager_id))->count() }}</div>
                    <div class="stat-label">Đã Phân Công Quản Lý</div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách Tòa Nhà (Data Table 5 Cột) -->
        <div class="content-card">
            <div class="card-header-bar">
                <div class="card-title-text">
                    <i class="fa-solid fa-list-check" style="color: var(--brand-primary);"></i>
                    Danh Sách Tòa Nhà Trực Thuộc Doanh Nghiệp
                </div>
                <div class="filter-group">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="filterSearch" class="search-input" placeholder="Tìm tên tòa nhà, địa chỉ, NV..." oninput="filterTable()">
                    </div>
                    <select id="filterType" class="filter-select" onchange="filterTable()">
                        <option value="">Tất cả loại hình</option>
                        <option value="can_ho">Chung cư mini / Căn hộ dịch vụ</option>
                        <option value="nha_tro">Nhà trọ / Khu trọ</option>
                        <option value="ky_tuc_xa">Ký túc xá / Sleepbox</option>
                        <option value="dat_phan_lo">Đất phân lô / Đất nền</option>
                        <option value="nha_rieng">Nhà ở / Nhà riêng lẻ</option>
                        <option value="cum_nha_lien_ke">Cụm nhà liền kề / Dự án</option>
                        <option value="dat_nen">Mặt bằng kinh doanh</option>
                    </select>
                    <button type="button" class="btn-act" onclick="location.reload()" title="Làm mới" style="width: auto; padding: 0 12px; gap: 6px; font-weight: 700;">
                        <i class="fa-solid fa-rotate-right"></i> Làm mới
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 26%;">Cơ Sở</th>
                            <th style="width: 26%;">Vị Trí</th>
                            <th style="width: 24%;">Quy Mô & Dịch Vụ</th>
                            <th style="width: 14%;">Nhân Viên Phụ Trách</th>
                            <th style="width: 10%; text-align: right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="buildings-table-body">
                        @forelse($buildings ?? [] as $b)
                            @php
                                $bId = (string)($b->_id ?? $b->id);
                                $imgSrc = $b->display_image ?: '';
                                $bType = $b->business_type ?: ($b->type ?: 'Chung cư mini / Căn hộ dịch vụ');
                                $typeClass = match(strtolower($bType)) {
                                    'can_ho', 'căn hộ dịch vụ', 'chung cư mini', 'can ho dich vu', 'chung cu mini', 'chung cư mini / căn hộ dịch vụ' => 'type-chdv',
                                    'ky_tuc_xa', 'ký túc xá', 'sleepbox', 'ky tuc xa', 'ký túc xá / sleepbox' => 'type-ktx',
                                    'nha_tro', 'nhà trọ', 'nha tro', 'nhà trọ / khu trọ' => 'type-nhatro',
                                    'nha_rieng', 'nhà ở / nhà riêng lẻ', 'nhà riêng lẻ', 'nhà riêng', 'nhà nguyên căn', 'nha nguyen can' => 'type-nhanguyencan',
                                    'cum_nha_lien_ke', 'cụm nhà liền kề / dự án', 'cụm nhà liền kề', 'liền kề', 'dự án', 'cum nha lien ke' => 'type-lienke',
                                    'dat_phan_lo', 'đất phân lô / đất nền', 'đất phân lô', 'đất nền', 'dat phan lo', 'dat nen' => 'type-datphanlo',
                                    'dat_nen', 'mặt bằng kinh doanh', 'mat bang kinh doanh', 'mặt bằng' => 'type-matbang',
                                    'mat_bang', 'văn phòng', 'van phong' => 'type-vanphong',
                                    default => 'type-ccmini'
                                };
                                $isLand = in_array(strtolower($b->type ?? ''), ['dat_phan_lo']) || str_contains(mb_strtolower($bType), 'phân lô') || str_contains(mb_strtolower($bType), 'đất');
                                $isDatPhanLo = str_contains(mb_strtolower($bType), 'phân lô') || str_contains(mb_strtolower($bType), 'đất');
                                $isLienKe = in_array(strtolower($b->type ?? ''), ['cum_nha_lien_ke']) || str_contains(mb_strtolower($bType), 'liền kề') || str_contains(mb_strtolower($bType), 'dự án');
                                $isHouse = !$isLienKe && (str_contains(mb_strtolower($bType), 'nhà riêng') || str_contains(mb_strtolower($bType), 'nhà ở') || str_contains(mb_strtolower($bType), 'nguyên căn'));
                                $isMatBang = str_contains(mb_strtolower($bType), 'mặt bằng');
                                $manager = $b->manager_info;
                            @endphp
                            <tr data-id="{{ $bId }}" 
                                data-name="{{ strtolower($b->name ?? '') }}" 
                                data-address="{{ strtolower($b->full_address ?? '') }}"
                                data-type="{{ strtolower($bType) }}"
                                data-manager="{{ strtolower($manager->account_name ?? ($manager->username ?? '')) }}"
                                data-posts-count="{{ (int)($b->actual_posts_count ?? 0) }}"
                                data-rented-count="{{ (int)($b->active_rent_count ?? 0) }}"
                                data-can-delete="{{ !empty($b->can_delete) ? '1' : '0' }}">
                                
                                {{-- CỘT 1 (Cơ Sở): Ảnh bìa, Tên Tòa nhà và Phân loại --}}
                                <td>
                                    <div class="building-cell">
                                        <div style="position: relative; flex-shrink: 0;">
                                            @if(!empty($imgSrc) && !str_contains($imgSrc, 'placeholder') && !str_contains($imgSrc, 'placehold.co'))
                                                <img src="{{ $imgSrc }}" alt="{{ $b->name }}" class="building-thumb" onerror="this.outerHTML='<div class=\'building-thumb-placeholder\'><i class=\'fa-solid fa-city\'></i></div>'">
                                            @else
                                                <div class="building-thumb-placeholder">
                                                    <i class="fa-solid fa-city"></i>
                                                </div>
                                            @endif
                                            @php $imgTotalCount = count($b->images ?? []); @endphp
                                            @if($imgTotalCount > 1)
                                                <span style="position: absolute; bottom: 4px; right: 4px; background: rgba(15, 23, 42, 0.85); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 1px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 3px; backdrop-filter: blur(2px);">
                                                    <i class="fa-solid fa-images" style="font-size: 8px;"></i> {{ $imgTotalCount }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="building-name">{{ $b->name }}</div>
                                            <span class="type-badge {{ $typeClass }}">
                                                <i class="fa-solid fa-tag"></i> {{ $bType }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- CỘT 2 (Vị Trí): Toàn bộ địa chỉ hành chính --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary); font-size: 0.85rem; line-height: 1.4; max-width: 280px;">
                                        <i class="fa-solid fa-location-dot" style="color: var(--brand-primary); margin-right: 4px;"></i>
                                        {{ $b->full_address }}
                                    </div>
                                </td>

                                {{-- CỘT 3 (Quy Mô & Dịch Vụ): Số tầng, tổng số phòng, giá Điện, Nước (Không có giá thuê) --}}
                                <td>
                                    <div class="scale-service-grid">
                                        @if(!$isLand && !$isLienKe)
                                            <div class="price-item">
                                                <i class="fa-solid fa-layer-group" style="color: var(--brand-primary); width: 14px;"></i>
                                                <span>Số tầng:</span>
                                                <span class="price-val">{{ $b->total_floors ?? 1 }} tầng</span>
                                            </div>
                                        @endif
                                        @if($isLienKe)
                                            <div class="price-item">
                                                <i class="fa-solid fa-city" style="color: var(--brand-primary); width: 14px;"></i>
                                                <span>Tổng số căn:</span>
                                                <span class="price-val" style="color: var(--brand-primary);">{{ $b->total_rooms ?? 0 }} căn</span>
                                            </div>
                                        @elseif(!$isHouse)
                                            <div class="price-item">
                                                <i class="fa-solid fa-{{ $isLand ? 'map-location-dot' : 'door-closed' }}" style="color: var(--brand-primary); width: 14px;"></i>
                                                <span>{{ $isDatPhanLo ? 'Tổng số lô:' : ($isMatBang ? 'Số mặt bằng:' : 'Tổng số phòng:') }}</span>
                                                <span class="price-val" style="color: var(--brand-primary);">{{ $b->total_rooms ?? 0 }} {{ $isDatPhanLo ? 'lô' : ($isMatBang ? 'mặt bằng' : 'phòng') }}</span>
                                            </div>
                                        @else
                                            <div class="price-item">
                                                <i class="fa-solid fa-house-chimney" style="color: var(--brand-primary); width: 14px;"></i>
                                                <span>Quy mô:</span>
                                                <span class="price-val" style="color: var(--brand-primary);">Nhà riêng lẻ</span>
                                            </div>
                                        @endif
                                        @php
                                            $hasElec = !is_null($b->electricity_price) && $b->electricity_price !== '' && (float)$b->electricity_price > 0;
                                            $hasWater = !is_null($b->water_price) && $b->water_price !== '' && (float)$b->water_price > 0;
                                            $isPricingExplicit = isset($b->has_service_pricing) && $b->has_service_pricing !== null ? (bool)$b->has_service_pricing : null;
                                            $hasServiceActive = ($isPricingExplicit === true) || ($isPricingExplicit === null && ($hasElec || $hasWater));
                                        @endphp
                                        @if($hasServiceActive && ($hasElec || $hasWater))
                                            <div class="price-item">
                                                <i class="fa-solid fa-bolt" style="color: #f59e0b; width: 14px;"></i>
                                                <span>Điện:</span>
                                                <span class="price-val">{{ $hasElec ? (number_format($b->electricity_price) . ' đ/kWh') : 'Theo giá nhà nước' }}</span>
                                            </div>
                                            <div class="price-item">
                                                <i class="fa-solid fa-droplet" style="color: #0284c7; width: 14px;"></i>
                                                <span>Nước:</span>
                                                <span class="price-val">
                                                    {{ $hasWater ? (number_format($b->water_price) . ' đ/' . (($b->water_calc_type ?? 'm3') === 'person' ? 'người' : 'm³')) : 'Theo giá nhà nước' }}
                                                </span>
                                            </div>
                                        @elseif($isLienKe)
                                            <div class="price-item">
                                                <i class="fa-solid fa-bolt" style="color: #f59e0b; width: 14px;"></i>
                                                <span>Điện, Nước:</span>
                                                <span class="price-val" style="color: var(--text-muted); font-size: 0.725rem;">Hộ gia đình tự thanh toán</span>
                                            </div>
                                        @elseif($isHouse)
                                            <div class="price-item">
                                                <i class="fa-solid fa-bolt" style="color: #f59e0b; width: 14px;"></i>
                                                <span>Điện, Nước:</span>
                                                <span class="price-val" style="color: var(--text-muted); font-size: 0.725rem;">Theo giá nhà nước</span>
                                            </div>
                                        @elseif($isLand)
                                            <div class="price-item">
                                                <i class="fa-solid fa-receipt" style="color: #64748b; width: 14px;"></i>
                                                <span>Dịch vụ:</span>
                                                <span class="price-val" style="color: var(--text-muted); font-size: 0.725rem;">Không áp dụng</span>
                                            </div>
                                        @else
                                            <div class="price-item">
                                                <i class="fa-solid fa-receipt" style="color: #64748b; width: 14px;"></i>
                                                <span>Điện, Nước:</span>
                                                <span class="price-val" style="color: var(--text-muted); font-size: 0.725rem;">Theo giá nhà nước / Thỏa thuận</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- CỘT 4 (Nhân Viên Phụ Trách): Avatar, Tên và SĐT nhân viên Sale --}}
                                <td>
                                    @php
                                        $mgr = $b->manager_info;
                                        $mgrList = $b->assigned_staffs ?? ($mgr ? collect([$mgr]) : collect([]));
                                    @endphp
                                    @if($mgrList && $mgrList->count() > 0)
                                        <div style="display: flex; flex-direction: column; gap: 6px;">
                                            @foreach($mgrList as $m)
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; border: 1.5px solid #bae6fd; flex-shrink: 0;">
                                                        {{ strtoupper(mb_substr($m->account_name ?: ($m->username ?: 'NV'), 0, 1, 'UTF-8')) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--text-primary); line-height: 1.2;">
                                                            {{ $m->account_name ?: $m->username }}
                                                        </div>
                                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                                                            <i class="fa-solid fa-phone" style="font-size: 10px; color: var(--brand-primary);"></i> 
                                                            {{ $m->phone ?: ($m->email ?: 'Chưa có SĐT') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($manager)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; border: 1.5px solid #bae6fd; flex-shrink: 0;">
                                                {{ strtoupper(mb_substr($manager->account_name ?: ($manager->username ?: 'NV'), 0, 1, 'UTF-8')) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; font-size: 0.85rem; color: var(--text-primary); line-height: 1.2;">
                                                    {{ $manager->account_name ?: $manager->username }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                                                    <i class="fa-solid fa-phone" style="font-size: 10px; color: var(--brand-primary);"></i> 
                                                    {{ $manager->phone ?: ($manager->email ?: 'Chưa có SĐT') }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size: 0.75rem; color: var(--text-muted); background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa-solid fa-user-slash"></i> Chưa phân công
                                        </span>
                                    @endif
                                </td>

                                {{-- CỘT 5 (Thao Tác): Xem danh sách phòng (Quản lý ruột), Sửa, Xóa --}}
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ url('/Admin/chi-tiet-toa-nha/' . $bId) }}" class="btn-act btn-act-rooms" title="Xem chi tiết vận hành & quản lý phòng (Admin)">
                                            <i class="fa-solid fa-door-open"></i>
                                        </a>
                                        <button type="button" class="btn-act" onclick="openEditModal('{{ $bId }}')" title="Chỉnh sửa tòa nhà">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn-act btn-act-danger" 
                                                onclick="deleteBuilding('{{ $bId }}', '{{ addslashes($b->name) }}', {{ (int)($b->actual_posts_count ?? 0) }}, {{ (int)($b->active_rent_count ?? 0) }})" 
                                                title="{{ !empty($b->can_delete) ? 'Xóa tòa nhà' : 'Tòa nhà đang có ' . ($b->actual_posts_count ?? 0) . ' bài đăng - Không thể xóa' }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-city"></i>
                                        <h3>Chưa có tòa nhà / cơ sở nào</h3>
                                        <p>Nhấn nút "+ Thêm Tòa Nhà Mới" ở góc trên để khởi tạo cơ sở kinh doanh đầu tiên cho doanh nghiệp.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL TẠO MỚI / CHỈNH SỬA TÒA NHÀ (3 NHÓM CHUẨN XÁC) -->
    <div id="buildingModal" class="modal-overlay" onclick="if(event.target === this) closeModal()">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">
                    <i class="fa-solid fa-city" style="color: var(--brand-primary);"></i>
                    <span>Tạo Mới Tòa Nhà / Cơ Sở</span>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="buildingForm" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
                <input type="hidden" id="editBuildingId" value="">

                <div class="modal-body">

                    <!-- NHÓM 1: THÔNG TIN ĐỊNH DANH -->
                    <div class="form-section-card">
                        <div class="form-section-title">
                            <span><i class="fa-solid fa-id-card"></i> Nhóm 1: Thông Tin Định Danh</span>
                            <small style="color: var(--text-muted); font-weight: 500; text-transform: none;">(*) Bắt buộc</small>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" for="b_name">Tên tòa nhà / cơ sở <span style="color: var(--accent-red)">*</span></label>
                                <input type="text" id="b_name" class="form-input" placeholder="VD: Tòa nhà RentHome 1 / Khu trọ Tuấn Anh" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="property-type">Loại hình kinh doanh <span style="color: var(--accent-red)">*</span></label>
                                <select name="type" class="form-select" id="property-type" onchange="handleTypeChange()" required>
                                    <option value="can_ho">Chung cư mini / Căn hộ dịch vụ</option>
                                    <option value="nha_tro">Nhà trọ / Khu trọ</option>
                                    <option value="ky_tuc_xa">Ký túc xá / Sleepbox</option>
                                    <option value="dat_phan_lo">Đất phân lô / Đất nền</option>
                                    <option value="nha_rieng">Nhà ở / Nhà riêng lẻ</option>
                                    <option value="cum_nha_lien_ke">Cụm nhà liền kề / Dự án</option>
                                    <option value="dat_nen">Mặt bằng kinh doanh</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3 Dropdown Tỉnh/Quận/Phường (dùng API open-api.vn) -->
                        <div class="form-row-3">
                            <div class="form-group">
                                <label class="form-label" for="province">Tỉnh / Thành phố <span style="color: var(--accent-red)">*</span></label>
                                <select id="province" class="form-select" onchange="handleProvinceChange()" required>
                                    <option value="">-- Chọn Tỉnh / Thành phố --</option>
                                </select>
                                <input type="hidden" id="province_name" name="province_name" value="">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="district">Quận / Huyện <span style="color: var(--accent-red)">*</span></label>
                                <select id="district" class="form-select" onchange="handleDistrictChange()" disabled required>
                                    <option value="">-- Chọn Quận / Huyện --</option>
                                </select>
                                <input type="hidden" id="district_name" name="district_name" value="">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="ward">Phường / Xã</label>
                                <select id="ward" class="form-select" onchange="handleWardChange()" disabled>
                                    <option value="">-- Chọn Phường / Xã --</option>
                                </select>
                                <input type="hidden" id="ward_name" name="ward_name" value="">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="b_address_detail">Địa chỉ chi tiết (Số nhà, tên đường, ngõ ngách) <span style="color: var(--accent-red)">*</span></label>
                            <input type="text" id="b_address_detail" class="form-input" placeholder="VD: Số 26 Ngõ 165 Đường Cầu Giấy" required>
                        </div>
                    </div>

                    <!-- NHÓM 2: BẢNG GIÁ DỊCH VỤ DÙNG CHUNG (Tuyệt đối không có trường Giá Thuê Phòng) -->
                    <div class="form-section-card" id="group2PricingSection">
                        <div class="form-section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <span><i class="fa-solid fa-receipt"></i> Nhóm 2: Bảng Giá Dịch Vụ Dùng Chung</span>
                            <span id="service_pricing_badge" style="font-size: 0.725rem; background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 6px; text-transform: none; font-weight: 700;">
                                Áp dụng chung cho cả cơ sở
                            </span>
                        </div>

                        <!-- 1. Công tắc Bật/Tắt Bảng giá dịch vụ -->
                        <div class="service-toggle-box" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-bottom: 14px; transition: all 0.2s ease;">
                            <label for="apply_service_pricing" style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 0.875rem; font-weight: 700; color: #1e293b; user-select: none; margin-bottom: 0;">
                                <label class="service-pricing-switch">
                                    <input type="checkbox" id="apply_service_pricing" name="apply_service_pricing" value="1" checked onchange="toggleServicePricing(this.checked)">
                                    <span class="service-pricing-slider"></span>
                                </label>
                                <span>Áp dụng thu phí dịch vụ chung cho cơ sở này</span>
                            </label>
                            <span id="service_toggle_status" style="font-size: 0.725rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 4px 10px; border-radius: 9999px; border: 1px solid #a7f3d0; display: inline-flex; items-center; gap: 4px;">
                                <i class="fa-solid fa-circle-check"></i> Đang bật
                            </span>
                        </div>

                        <!-- Thông báo khi tắt công tắc -->
                        <div id="service_pricing_disabled_notice" style="display: none; padding: 12px 16px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; margin-bottom: 14px; font-size: 0.8rem; color: #92400e; line-height: 1.5;">
                            <i class="fa-solid fa-circle-info" style="color: #d97706; margin-right: 6px;"></i>
                            Cơ sở này <strong>không áp dụng bảng phí dịch vụ chung</strong> (phù hợp với Nhà ở / Nhà riêng lẻ, Cụm liền kề, Đất phân lô). Khách thuê tự thanh toán điện, nước theo hóa đơn nhà nước hoặc theo thỏa thuận riêng. Khi lưu, các trường phí dịch vụ sẽ mang giá trị trống/0.
                        </div>

                        <!-- Vùng chứa các ô nhập liệu bên dưới (Điện, Nước, Phí dịch vụ, Internet, Gửi xe) -->
                        <div id="service_pricing_fields">
                            <div class="notice-banner">
                                <i class="fa-solid fa-circle-info" style="font-size: 1.15rem; flex-shrink: 0; color: #0284c7;"></i>
                                <div>
                                    <strong>Lưu ý nghiệp vụ Lớp Vỏ:</strong> Tòa nhà chỉ cấu hình bảng giá dịch vụ dùng chung (Điện, Nước, Dịch vụ, Internet, Gửi xe). <strong>Tuyệt đối không có trường "Giá thuê phòng"</strong> tại đây. Giá thuê và đặt cọc được quản lý độc lập tại từng phòng con (Lớp Ruột).
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label" for="b_electricity_price">Đơn giá Điện</label>
                                    <div class="input-group-unit">
                                        <input type="number" id="b_electricity_price" class="form-input" placeholder="VD: 3500 (Để trống: Giá nhà nước)" min="0" step="100">
                                        <span class="unit-label">đ/kWh</span>
                                    </div>
                                    <div class="money-vnd-hint" id="hint_b_electricity_price">
                                        <div class="hint-main"><i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">-- VNĐ</strong> / kWh</div>
                                        <span class="vnd-words">Chưa nhập số tiền (Thỏa thuận / Theo giá nhà nước)</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="b_water_price">Đơn giá Nước</label>
                                    <div class="input-group-unit">
                                        <input type="number" id="b_water_price" class="form-input" placeholder="VD: 25000 (Để trống: Giá nhà nước)" min="0" step="500">
                                        <span class="unit-label" id="waterUnitLabel">đ/m³</span>
                                    </div>
                                    <div class="money-vnd-hint" id="hint_b_water_price">
                                        <div class="hint-main"><i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">-- VNĐ</strong> / m³</div>
                                        <span class="vnd-words">Chưa nhập số tiền (Thỏa thuận / Theo giá nhà nước)</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="b_water_calc_type">Cách tính tiền nước</label>
                                    <select id="b_water_calc_type" class="form-select">
                                        <option value="m3">Tính theo khối (m³)</option>
                                        <option value="person">Tính theo đầu người / tháng</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label" for="b_service_fee">Phí dịch vụ chung / Vệ sinh</label>
                                    <div class="input-group-unit">
                                        <input type="number" id="b_service_fee" class="form-input" placeholder="VD: 150000 (Để trống: 0đ)" min="0" step="1000">
                                        <span class="unit-label">đ/phòng</span>
                                    </div>
                                    <div class="money-vnd-hint" id="hint_b_service_fee">
                                        <div class="hint-main"><i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">-- VNĐ</strong> / phòng</div>
                                        <span class="vnd-words">Chưa nhập số tiền</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="b_internet_fee">Phí Wifi / Internet</label>
                                    <div class="input-group-unit">
                                        <input type="number" id="b_internet_fee" class="form-input" placeholder="VD: 100000 (Để trống: 0đ)" min="0" step="1000">
                                        <span class="unit-label">đ/phòng</span>
                                    </div>
                                    <div class="money-vnd-hint" id="hint_b_internet_fee">
                                        <div class="hint-main"><i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">-- VNĐ</strong> / phòng</div>
                                        <span class="vnd-words">Chưa nhập số tiền</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="b_parking_fee">Phí gửi xe</label>
                                    <div class="input-group-unit">
                                        <input type="number" id="b_parking_fee" class="form-input" placeholder="VD: 100000 (Để trống: 0đ)" min="0" step="1000">
                                        <span class="unit-label">đ/xe</span>
                                    </div>
                                    <div class="money-vnd-hint" id="hint_b_parking_fee">
                                        <div class="hint-main"><i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">-- VNĐ</strong> / xe</div>
                                        <span class="vnd-words">Chưa nhập số tiền</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NHÓM 3: QUY MÔ & PHÂN QUYỀN -->
                    <div class="form-section-card" style="margin-bottom: 0;">
                        <div class="form-section-title">
                            <span><i class="fa-solid fa-layer-group"></i> Nhóm 3: Quy Mô & Phân Quyền</span>
                        </div>

                        <input type="hidden" id="b_status" value="active">

                        <!-- Hàng: Số tầng, Tổng số phòng/lô, Nhân viên phụ trách -->
                        <div class="form-row-3" id="row_scale_staff">
                            <div class="form-group" id="group_total_floors">
                                <label class="form-label" id="label_total_floors" for="b_total_floors">Số tầng của tòa nhà</label>
                                <input type="number" id="b_total_floors" class="form-input" placeholder="VD: 5" min="1">
                            </div>
                            <div class="form-group" id="group_total_rooms">
                                <label class="form-label" id="label_total_rooms" for="b_total_rooms">Tổng số phòng</label>
                                <input type="number" id="b_total_rooms" class="form-input" placeholder="VD: 15" min="1">
                            </div>
                            <div class="form-group" id="group_manager_staff">
                                <label class="form-label" for="b_manager_id">Nhân viên phụ trách (Sale/Staff)</label>
                                <select name="manager_id" id="b_manager_id" class="form-select">
                                    <option value="">-- Chọn nhân viên phụ trách --</option>
                                    @forelse($staffs ?? $staffList ?? [] as $st)
                                        @php $sId = (string)($st->_id ?? $st->id); @endphp
                                        <option value="{{ $sId }}">
                                            👤 {{ $st->account_name ?: $st->username }} ({{ $st->phone ?: $st->email }})
                                        </option>
                                    @empty
                                        <option value="" disabled>Chưa có nhân viên nào</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <!-- Tiện ích tòa nhà (Checkbox) -->
                        <div class="form-group" id="group_amenities">
                            <label class="form-label">Tiện ích chung của tòa nhà</label>
                            <div class="amenities-grid">
                                @php
                                    $allAmenities = [
                                        'Thang máy' => 'fa-elevator',
                                        'Hầm để xe' => 'fa-warehouse',
                                        'Bảo vệ 24/7' => 'fa-user-shield',
                                        'Camera an ninh' => 'fa-video',
                                        'PCCC đạt chuẩn' => 'fa-fire-extinguisher',
                                        'Khóa cổng vân tay' => 'fa-fingerprint',
                                        'Máy giặt chung' => 'fa-soap',
                                        'Giờ giấc tự do' => 'fa-clock'
                                    ];
                                @endphp
                                @foreach($allAmenities as $amenityName => $amenityIcon)
                                    <label class="amenity-chk-card" id="amenity_label_{{ Str::slug($amenityName) }}">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenityName }}" onchange="toggleAmenityStyle(this)">
                                        <span><i class="fa-solid {{ $amenityIcon }}"></i> {{ $amenityName }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Upload ảnh bìa tòa nhà (Mặt tiền / cơ sở - Tối đa 5 ảnh) -->
                        <div class="form-group">
                            <div class="uploader-header">
                                <label class="form-label" style="margin-bottom: 0;">
                                    <i class="fa-solid fa-images" style="color: var(--brand-primary); margin-right: 4px;"></i>
                                    Hình ảnh mặt tiền / cơ sở tòa nhà (Tối đa 5 ảnh)
                                </label>
                                <span id="imageCounter" class="image-counter-badge">Đã chọn: 0/5 ảnh</span>
                            </div>

                            <div class="image-dropzone" id="imageDropzone" onclick="document.getElementById('b_image_files').click()">
                                <input type="file" id="b_image_files" style="display: none;" accept="image/jpeg,image/png,image/webp,image/jpg" multiple onchange="handleFilesSelect(this.files)">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <div class="image-dropzone-title">Nhấn để chọn ảnh mặt tiền hoặc kéo thả vào đây</div>
                                <div class="image-dropzone-hint">Hỗ trợ tối đa 5 ảnh (JPG, PNG, WEBP). Ảnh đầu tiên sẽ là Ảnh bìa đại diện của Tòa nhà.</div>
                            </div>

                            <div id="imageGalleryGrid" class="image-preview-gallery">
                                <div class="gallery-empty-hint" id="galleryEmptyHint">
                                    <i class="fa-regular fa-image" style="margin-right: 4px;"></i> Chưa có ảnh nào được chọn. Hãy tải lên ảnh mặt tiền cho tòa nhà.
                                </div>
                            </div>
                        </div>

                        <!-- Mô tả tóm tắt tòa nhà -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="b_description">Mô tả tóm tắt về tòa nhà</label>
                            <textarea id="b_description" class="form-textarea" placeholder="Mô tả tóm tắt đặc điểm tòa nhà (Vị trí đắc địa, gần trường học, đường lớn, khu dân trí cao...)"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy Bỏ</button>
                    <button type="submit" id="btnSubmitForm" class="btn-create">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Tòa Nhà / Cơ Sở
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        // ==========================================
        // 1. CHUYỂN ĐỔI TIỀN TỆ VNĐ SANG CHỮ
        // ==========================================
        function readVietnameseCurrency(number) {
            if (isNaN(number) || number === null || number === '') return '';
            number = Math.floor(Number(number));
            if (number === 0) return 'Không đồng';
            if (number < 0) return 'Số tiền không hợp lệ';

            const defaultNumbers = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
            const units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ'];

            function readThreeDigits(n, full) {
                let h = Math.floor(n / 100);
                let t = Math.floor((n % 100) / 10);
                let o = n % 10;
                let res = '';
                if (h > 0 || full) res += defaultNumbers[h] + ' trăm ';
                if (t > 1) {
                    res += defaultNumbers[t] + ' mươi ';
                    if (o === 1) res += 'mốt ';
                    else if (o === 5) res += 'lăm ';
                    else if (o > 0) res += defaultNumbers[o] + ' ';
                } else if (t === 1) {
                    res += 'mười ';
                    if (o === 5) res += 'lăm ';
                    else if (o > 0) res += defaultNumbers[o] + ' ';
                } else if (t === 0 && o > 0) {
                    if (h > 0 || full) res += 'lẻ ';
                    res += defaultNumbers[o] + ' ';
                }
                return res;
            }

            let groups = [];
            let temp = number;
            while (temp > 0) {
                groups.push(temp % 1000);
                temp = Math.floor(temp / 1000);
            }

            let result = '';
            for (let i = groups.length - 1; i >= 0; i--) {
                let g = groups[i];
                if (g > 0) {
                    let full = (i < groups.length - 1);
                    let s = readThreeDigits(g, full);
                    result += s + units[i] + ' ';
                }
            }
            result = result.trim() + ' đồng';
            return result.charAt(0).toUpperCase() + result.slice(1);
        }

        function updateMoneyVndNotes() {
            const configs = [
                { id: 'b_electricity_price', hintId: 'hint_b_electricity_price', suffix: ' / kWh' },
                { 
                    id: 'b_water_price', 
                    hintId: 'hint_b_water_price', 
                    getSuffix: () => (document.getElementById('b_water_calc_type')?.value === 'person' ? ' / người' : ' / m³')
                },
                { id: 'b_service_fee', hintId: 'hint_b_service_fee', suffix: ' / phòng' },
                { id: 'b_internet_fee', hintId: 'hint_b_internet_fee', suffix: ' / phòng' },
                { id: 'b_parking_fee', hintId: 'hint_b_parking_fee', suffix: ' / xe' }
            ];

            configs.forEach(cfg => {
                const input = document.getElementById(cfg.id);
                const hint = document.getElementById(cfg.hintId);
                if (!input || !hint) return;

                const val = input.value;
                const num = parseFloat(val);
                const suffix = cfg.suffix !== undefined ? cfg.suffix : cfg.getSuffix();

                let vndText = '';
                let wordsText = '';

                if (isNaN(num) || val === '') {
                    if (cfg.id === 'b_electricity_price' || cfg.id === 'b_water_price') {
                        vndText = 'Thỏa thuận / Giá nhà nước';
                        wordsText = 'Không thu qua chủ cơ sở';
                    } else {
                        vndText = '-- VNĐ';
                        wordsText = 'Chưa nhập số tiền';
                    }
                } else if (num === 0) {
                    vndText = '0 VNĐ';
                    wordsText = 'Miễn phí';
                } else {
                    vndText = num.toLocaleString('vi-VN') + ' VNĐ';
                    wordsText = readVietnameseCurrency(num);
                }

                hint.innerHTML = `
                    <div class="hint-main">
                        <i class="fa-solid fa-coins"></i> Ghi chú: <strong class="vnd-text">${vndText}</strong>${suffix}
                    </div>
                    <span class="vnd-words">${wordsText}</span>
                `;
            });
        }

        // ==========================================
        // 2. CÔNG TẮC BẬT/TẮT BẢNG GIÁ DỊCH VỤ NHÓM 2
        // ==========================================
        function toggleServicePricing(isApply) {
            const chk = document.getElementById('apply_service_pricing');
            if (chk) chk.checked = !!isApply;

            const fields = document.getElementById('service_pricing_fields');
            const notice = document.getElementById('service_pricing_disabled_notice');
            const badge = document.getElementById('service_pricing_badge');
            const toggleStatus = document.getElementById('service_toggle_status');

            if (isApply) {
                if (fields) fields.style.display = 'block';
                if (notice) notice.style.display = 'none';
                if (badge) {
                    badge.innerText = 'Áp dụng chung cho cả cơ sở';
                    badge.style.background = '#e0f2fe';
                    badge.style.color = '#0284c7';
                }
                if (toggleStatus) {
                    toggleStatus.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đang bật';
                    toggleStatus.style.color = '#059669';
                    toggleStatus.style.background = '#ecfdf5';
                    toggleStatus.style.borderColor = '#a7f3d0';
                }
            } else {
                if (fields) fields.style.display = 'none';
                if (notice) notice.style.display = 'block';
                if (badge) {
                    badge.innerText = 'Tự túc / Giá nhà nước';
                    badge.style.background = '#fef3c7';
                    badge.style.color = '#b45309';
                }
                if (toggleStatus) {
                    toggleStatus.innerHTML = '<i class="fa-solid fa-ban"></i> Đang tắt';
                    toggleStatus.style.color = '#dc2626';
                    toggleStatus.style.background = '#fef2f2';
                    toggleStatus.style.borderColor = '#fecaca';
                }
            }
        }

        // ==========================================
        // 2.1 TỰ ĐỘNG ẨN/HIỆN CÁC TRƯỜNG THEO LOẠI HÌNH
        // ==========================================
        function handleTypeChange(autoToggle = true) {
            const typeSelect = document.getElementById('property-type');
            if (!typeSelect) return;
            const val = typeSelect.value.toLowerCase().trim();

            const groupFloors = document.getElementById('group_total_floors');
            const labelFloors = document.getElementById('label_total_floors');
            const inputFloors = document.getElementById('b_total_floors');

            const groupRooms = document.getElementById('group_total_rooms');
            const labelRooms = document.getElementById('label_total_rooms');
            const inputRooms = document.getElementById('b_total_rooms');

            const group2Section = document.getElementById('group2PricingSection');
            const electInput = document.getElementById('b_electricity_price');
            const waterInput = document.getElementById('b_water_price');
            const amenitiesGroup = document.getElementById('group_amenities');
            const rowScaleStaff = document.getElementById('row_scale_staff');

            // Nhóm 2 luôn luôn hiển thị thẻ khung (để Admin có thể tự tay bật/tắt qua công tắc)
            if (group2Section) group2Section.style.display = 'block';

            // Điện & Nước không bao giờ bắt buộc required
            if (electInput) electInput.removeAttribute('required');
            if (waterInput) waterInput.removeAttribute('required');

            // 1. ĐẤT PHÂN LÔ / ĐẤT NỀN ('dat_phan_lo')
            if (val === 'dat_phan_lo') {
                // Ẩn trường "Số tầng"
                if (groupFloors) groupFloors.style.display = 'none';
                if (inputFloors) {
                    inputFloors.value = '';
                    inputFloors.removeAttribute('required');
                }

                // Đổi thành "Tổng số lô / nền đất"
                if (groupRooms) groupRooms.style.display = '';
                if (labelRooms) labelRooms.innerHTML = 'Tổng số lô / nền đất <span style="color: var(--accent-red)">*</span>';
                if (inputRooms) inputRooms.placeholder = 'VD: 20 lô';

                // Tự động tắt công tắc Nhóm 2 nếu autoToggle
                if (autoToggle) toggleServicePricing(false);

                // Ẩn tiện ích chung (Thang máy, máy giặt...)
                if (amenitiesGroup) amenitiesGroup.style.display = 'none';

                // Bố cục 2 cột: Số lô/nền | Nhân viên phụ trách
                if (rowScaleStaff) rowScaleStaff.className = 'form-row-2';
            }
            // 2. NHÀ Ở / NHÀ RIÊNG LẺ ('nha_rieng')
            else if (val === 'nha_rieng') {
                // Hiện số tầng của nhà
                if (groupFloors) groupFloors.style.display = '';
                if (labelFloors) labelFloors.innerText = 'Số tầng của nhà';
                if (inputFloors && !inputFloors.value) inputFloors.value = '1';
                if (inputFloors) inputFloors.placeholder = 'VD: 3 tầng';

                // Xóa/Ẩn ô "Số phòng ngủ / phòng ở" theo yêu cầu
                if (groupRooms) groupRooms.style.display = 'none';
                if (inputRooms) inputRooms.value = '1';

                // Tự động tắt công tắc Nhóm 2 nếu autoToggle
                if (autoToggle) toggleServicePricing(false);

                // Hiện tiện ích (Camera, Khóa vân tay, Chỗ để xe, Giờ giấc tự do...)
                if (amenitiesGroup) amenitiesGroup.style.display = '';

                // Bố cục 2 cột cân đối: Số tầng của nhà | Nhân viên phụ trách
                if (rowScaleStaff) rowScaleStaff.className = 'form-row-2';
            }
            // 2.5 CỤM NHÀ LIỀN KỀ / DỰ ÁN ('cum_nha_lien_ke')
            else if (val === 'cum_nha_lien_ke') {
                // Ẩn trường "Số tầng" theo yêu cầu
                if (groupFloors) groupFloors.style.display = 'none';
                if (inputFloors) {
                    inputFloors.value = '1';
                    inputFloors.removeAttribute('required');
                }

                // Hiện "Tổng số căn liền kề / dự án"
                if (groupRooms) groupRooms.style.display = '';
                if (labelRooms) labelRooms.innerHTML = 'Tổng số căn liền kề / dự án <span style="color: var(--accent-red)">*</span>';
                if (inputRooms) inputRooms.placeholder = 'VD: 20 căn';

                // Tự động tắt công tắc Nhóm 2 nếu autoToggle
                if (autoToggle) toggleServicePricing(false);

                // Hiện tiện ích (Camera, Bảo vệ 24/7, PCCC, Chỗ để xe, Khuôn viên...)
                if (amenitiesGroup) amenitiesGroup.style.display = '';

                // Bố cục 2 cột cân đối: Tổng số căn liền kề / dự án | Nhân viên phụ trách
                if (rowScaleStaff) rowScaleStaff.className = 'form-row-2';
            }
            // 3. MẶT BẰNG KINH DOANH ('dat_nen')
            else if (val === 'dat_nen') {
                // Hiện số tầng mặt bằng
                if (groupFloors) groupFloors.style.display = '';
                if (labelFloors) labelFloors.innerText = 'Số tầng mặt bằng';
                if (inputFloors && !inputFloors.value) inputFloors.value = '1';
                if (inputFloors) inputFloors.placeholder = 'VD: 1 tầng trệt hoặc 2 tầng';

                // Hiện tổng số mặt bằng / gian hàng
                if (groupRooms) groupRooms.style.display = '';
                if (labelRooms) labelRooms.innerText = 'Tổng số mặt bằng / gian hàng';
                if (inputRooms) inputRooms.placeholder = 'VD: 1 mặt bằng hoặc 5 gian hàng';

                // Mặc định bật dịch vụ cho mặt bằng kinh doanh
                if (autoToggle) toggleServicePricing(true);

                // Hiện tiện ích (Bảo vệ 24/7, Camera, PCCC, Chỗ để xe...)
                if (amenitiesGroup) amenitiesGroup.style.display = '';

                // Bố cục 3 cột: Số tầng | Số mặt bằng | Nhân viên phụ trách
                if (rowScaleStaff) rowScaleStaff.className = 'form-row-3';
            }
            // 4. CÁC MÔ HÌNH NHÀ TRỌ / CĂN HỘ / KÝ TÚC XÁ ('can_ho', 'nha_tro', 'ky_tuc_xa')
            else {
                // Hiện số tầng
                if (groupFloors) groupFloors.style.display = '';
                if (inputFloors && !inputFloors.value) inputFloors.value = '1';

                if (val === 'ky_tuc_xa') {
                    if (labelFloors) labelFloors.innerText = 'Số tầng cơ sở';
                    if (inputFloors) inputFloors.placeholder = 'VD: 4 tầng';
                    if (labelRooms) labelRooms.innerText = 'Tổng số phòng / Sleepbox';
                    if (inputRooms) inputRooms.placeholder = 'VD: 30 box';
                } else if (val === 'nha_tro') {
                    if (labelFloors) labelFloors.innerText = 'Số tầng khu trọ';
                    if (inputFloors) inputFloors.placeholder = 'VD: 3 tầng';
                    if (labelRooms) labelRooms.innerText = 'Tổng số phòng trọ';
                    if (inputRooms) inputRooms.placeholder = 'VD: 15 phòng';
                } else {
                    if (labelFloors) labelFloors.innerText = 'Số tầng của tòa nhà';
                    if (inputFloors) inputFloors.placeholder = 'VD: 7 tầng';
                    if (labelRooms) labelRooms.innerText = 'Tổng số phòng / căn hộ';
                    if (inputRooms) inputRooms.placeholder = 'VD: 24 căn';
                }

                if (groupRooms) groupRooms.style.display = '';

                // Tự động bật công tắc Nhóm 2 nếu chọn Chung cư mini hoặc Ký túc xá / Nhà trọ
                if (autoToggle) toggleServicePricing(true);

                // Hiện tiện ích
                if (amenitiesGroup) amenitiesGroup.style.display = '';

                // Bố cục 3 cột
                if (rowScaleStaff) rowScaleStaff.className = 'form-row-3';
            }
        }

        // ==========================================
        // 3. DỮ LIỆU ĐỊA CHÍNH (provinces.open-api.vn)
        // ==========================================
        const PROVINCES_API_BASE = 'https://provinces.open-api.vn/api';
        const districtCache = {};
        const wardCache = {};

        async function loadProvinces() {
            const provinceSelect = document.getElementById('province');
            if (!provinceSelect) return;

            try {
                const res = await fetch(`${PROVINCES_API_BASE}/p/`);
                const provinces = await res.json();

                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.code;
                    opt.text = p.name;
                    provinceSelect.appendChild(opt);
                });
            } catch (err) {
                console.error('Lỗi kết nối API Tỉnh/Thành:', err);
                provinceSelect.innerHTML = '<option value="">-- Không tải được dữ liệu --</option>';
            }
        }

        async function handleProvinceChange() {
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            const provinceNameInput = document.getElementById('province_name');
            const districtNameInput = document.getElementById('district_name');
            const wardNameInput = document.getElementById('ward_name');

            const selectedCode = provinceSelect.value;
            const selectedText = provinceSelect.selectedIndex > 0 ? provinceSelect.options[provinceSelect.selectedIndex].text : '';

            provinceNameInput.value = selectedText;

            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            districtNameInput.value = '';
            wardNameInput.value = '';
            districtSelect.disabled = true;
            wardSelect.disabled = true;

            if (!selectedCode) return;

            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Đang tải Quận / Huyện...</option>';

            try {
                let districts = districtCache[selectedCode];
                if (!districts) {
                    const res = await fetch(`${PROVINCES_API_BASE}/p/${selectedCode}?depth=2`);
                    const data = await res.json();
                    districts = data.districts || [];
                    districtCache[selectedCode] = districts;
                }

                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.code;
                    opt.text = d.name;
                    districtSelect.appendChild(opt);
                });
                districtSelect.disabled = false;
            } catch (err) {
                console.error('Lỗi tải danh sách Quận/Huyện:', err);
                districtSelect.innerHTML = '<option value="">-- Lỗi tải danh sách --</option>';
                districtSelect.disabled = false;
            }
        }

        async function handleDistrictChange() {
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            const districtNameInput = document.getElementById('district_name');
            const wardNameInput = document.getElementById('ward_name');

            const selectedCode = districtSelect.value;
            const selectedText = districtSelect.selectedIndex > 0 ? districtSelect.options[districtSelect.selectedIndex].text : '';

            districtNameInput.value = selectedText;

            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardNameInput.value = '';
            wardSelect.disabled = true;

            if (!selectedCode) return;

            wardSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">Đang tải Phường / Xã...</option>';

            try {
                let wards = wardCache[selectedCode];
                if (!wards) {
                    const res = await fetch(`${PROVINCES_API_BASE}/d/${selectedCode}?depth=2`);
                    const data = await res.json();
                    wards = data.wards || [];
                    wardCache[selectedCode] = wards;
                }

                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.code;
                    opt.text = w.name;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false;
            } catch (err) {
                console.error('Lỗi tải danh sách Phường/Xã:', err);
                wardSelect.innerHTML = '<option value="">-- Lỗi tải danh sách --</option>';
                wardSelect.disabled = false;
            }
        }

        function handleWardChange() {
            const wardSelect = document.getElementById('ward');
            const wardNameInput = document.getElementById('ward_name');
            const selectedText = wardSelect.selectedIndex > 0 ? wardSelect.options[wardSelect.selectedIndex].text : '';
            wardNameInput.value = selectedText;
        }

        async function setAddressByText(provText, distText, wardText) {
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            if (!provText) return;

            if (provinceSelect.options.length <= 1) {
                await loadProvinces();
            }

            const matchedProv = Array.from(provinceSelect.options).find(opt => opt.text.trim().toLowerCase() === provText.trim().toLowerCase());
            if (matchedProv) {
                provinceSelect.value = matchedProv.value;
                await handleProvinceChange();

                if (distText) {
                    const matchedDist = Array.from(districtSelect.options).find(opt => opt.text.trim().toLowerCase() === distText.trim().toLowerCase());
                    if (matchedDist) {
                        districtSelect.value = matchedDist.value;
                        await handleDistrictChange();

                        if (wardText) {
                            const matchedWard = Array.from(wardSelect.options).find(opt => opt.text.trim().toLowerCase() === wardText.trim().toLowerCase());
                            if (matchedWard) {
                                wardSelect.value = matchedWard.value;
                                handleWardChange();
                            }
                        }
                    }
                }
            }
        }

        // ==========================================
        // 4. QUẢN LÝ ẢNH BÌA / MẶT TIỀN (TỐI ĐA 5 ẢNH)
        // ==========================================
        let newSelectedFiles = [];
        let existingImagesList = [];

        function handleFilesSelect(fileList) {
            if (!fileList || fileList.length === 0) return;

            const filesArray = Array.from(fileList);
            const currTotal = existingImagesList.length + newSelectedFiles.length;
            const remaining = 5 - currTotal;

            if (remaining <= 0) {
                showToast('Bạn đã chọn tối đa 5 ảnh! Hãy xóa bớt nếu muốn thêm mới.', 'error');
                const fi = document.getElementById('b_image_files');
                if (fi) fi.value = '';
                return;
            }

            let filesToAdd = filesArray;
            if (filesArray.length > remaining) {
                showToast(`Chỉ được tải tối đa 5 ảnh. Hệ thống đã lấy ${remaining} ảnh đầu tiên.`, 'error');
                filesToAdd = filesArray.slice(0, remaining);
            }

            filesToAdd.forEach(file => {
                if (!file.type.startsWith('image/')) {
                    showToast(`Tệp "${file.name}" không phải là hình ảnh hợp lệ!`, 'error');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    showToast(`Ảnh "${file.name}" vượt quá giới hạn 10MB!`, 'error');
                    return;
                }
                newSelectedFiles.push(file);
            });

            renderGallery();
            const fi = document.getElementById('b_image_files');
            if (fi) fi.value = '';
        }

        function removeNewFile(idx) {
            if (idx >= 0 && idx < newSelectedFiles.length) {
                newSelectedFiles.splice(idx, 1);
                renderGallery();
            }
        }

        function removeExistingImage(idx) {
            if (idx >= 0 && idx < existingImagesList.length) {
                existingImagesList.splice(idx, 1);
                renderGallery();
            }
        }

        function renderGallery() {
            const grid = document.getElementById('imageGalleryGrid');
            const counter = document.getElementById('imageCounter');
            if (!grid || !counter) return;

            const total = existingImagesList.length + newSelectedFiles.length;
            counter.innerText = `Đã chọn: ${total}/5 ảnh`;

            if (total >= 5) {
                counter.classList.add('full');
            } else {
                counter.classList.remove('full');
            }

            if (total === 0) {
                grid.innerHTML = `
                    <div class="gallery-empty-hint" id="galleryEmptyHint">
                        <i class="fa-regular fa-image" style="margin-right: 4px;"></i> Chưa có ảnh nào được chọn. Hãy tải lên ảnh mặt tiền cho tòa nhà.
                    </div>
                `;
                return;
            }

            let html = '';
            let globalIndex = 0;

            existingImagesList.forEach((img, i) => {
                const isCover = (globalIndex === 0);
                html += `
                    <div class="gallery-slot" title="Ảnh đã lưu trên hệ thống">
                        <img src="${img.url}" alt="Ảnh cơ sở" onerror="this.src='https://placehold.co/400x300?text=Image'">
                        ${isCover ? '<span class="gallery-slot-badge"><i class="fa-solid fa-star"></i> Ảnh Bìa</span>' : ''}
                        <button type="button" class="gallery-slot-delete" onclick="removeExistingImage(${i})" title="Gỡ ảnh này">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
                globalIndex++;
            });

            newSelectedFiles.forEach((file, i) => {
                const isCover = (globalIndex === 0);
                const blobUrl = URL.createObjectURL(file);
                html += `
                    <div class="gallery-slot" title="${file.name}">
                        <img src="${blobUrl}" alt="Ảnh tải lên">
                        ${isCover ? '<span class="gallery-slot-badge"><i class="fa-solid fa-star"></i> Ảnh Bìa</span>' : ''}
                        <button type="button" class="gallery-slot-delete" onclick="removeNewFile(${i})" title="Gỡ ảnh này">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
                globalIndex++;
            });

            grid.innerHTML = html;
        }

        function toggleAmenityStyle(chk) {
            const label = chk.closest('.amenity-chk-card');
            if (label) {
                if (chk.checked) {
                    label.classList.add('checked');
                } else {
                    label.classList.remove('checked');
                }
            }
        }

        function showToast(msg, type = 'success') {
            const c = document.getElementById('toastContainer');
            const item = document.createElement('div');
            item.className = `toast-item toast-${type}`;
            item.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-exclamation'}"></i> <span>${msg}</span>`;
            c.appendChild(item);
            setTimeout(() => item.remove(), 3500);
        }

        // ==========================================
        // 5. BỘ LỌC BẢNG DANH SÁCH
        // ==========================================
        function filterTable() {
            const q = document.getElementById('filterSearch').value.toLowerCase().trim();
            const typeVal = document.getElementById('filterType').value.toLowerCase().trim();

            const rows = document.querySelectorAll('#buildings-table-body tr[data-id]');

            rows.forEach(r => {
                const name = r.dataset.name || '';
                const addr = r.dataset.address || '';
                const type = r.dataset.type || '';
                const manager = r.dataset.manager || '';

                const matchQ = !q || name.includes(q) || addr.includes(q) || manager.includes(q);
                let matchType = !typeVal;
                if (typeVal) {
                    if (type === typeVal || type.includes(typeVal)) {
                        matchType = true;
                    } else if (typeVal === 'can_ho' && (type.includes('căn hộ') || type.includes('chung cư'))) {
                        matchType = true;
                    } else if (typeVal === 'nha_tro' && (type.includes('nhà trọ') || type.includes('khu trọ'))) {
                        matchType = true;
                    } else if (typeVal === 'ky_tuc_xa' && (type.includes('ký túc') || type.includes('sleepbox'))) {
                        matchType = true;
                    } else if (typeVal === 'dat_phan_lo' && (type.includes('phân lô') || type.includes('đất nền') || type.includes('đất'))) {
                        matchType = true;
                    } else if (typeVal === 'nha_rieng' && (type.includes('nhà riêng') || type.includes('nhà ở') || type.includes('nguyên căn')) && !type.includes('liền kề')) {
                        matchType = true;
                    } else if (typeVal === 'cum_nha_lien_ke' && (type.includes('liền kề') || type.includes('dự án') || type.includes('cum_nha_lien_ke'))) {
                        matchType = true;
                    } else if (typeVal === 'dat_nen' && (type.includes('mặt bằng kinh doanh') || type.includes('mặt bằng'))) {
                        matchType = true;
                    }
                }

                r.style.display = (matchQ && matchType) ? '' : 'none';
            });
        }

        // ==========================================
        // 6. MODAL TẠO MỚI & CHỈNH SỬA
        // ==========================================
        function openCreateModal() {
            document.getElementById('editBuildingId').value = '';
            document.getElementById('buildingForm').reset();

            const titleEl = document.getElementById('modalTitle');
            if (titleEl) {
                titleEl.innerHTML = `<i class="fa-solid fa-city" style="color: var(--brand-primary);"></i> <span>Tạo Mới Tòa Nhà / Cơ Sở</span>`;
            }

            newSelectedFiles = [];
            existingImagesList = [];
            renderGallery();

            document.querySelectorAll('.amenity-chk-card').forEach(c => c.classList.remove('checked'));

            document.getElementById('property-type').value = 'can_ho';
            toggleServicePricing(true);
            document.getElementById('b_electricity_price').value = '3500';
            document.getElementById('b_water_price').value = '25000';
            document.getElementById('b_water_calc_type').value = 'm3';
            document.getElementById('waterUnitLabel').innerText = 'đ/m³';
            document.getElementById('b_service_fee').value = '';
            document.getElementById('b_internet_fee').value = '';
            document.getElementById('b_parking_fee').value = '';
            document.getElementById('b_total_floors').value = '1';
            document.getElementById('b_total_rooms').value = '';
            document.getElementById('b_manager_id').value = '';
            document.getElementById('b_status').value = 'active';

            const provSelect = document.getElementById('province');
            if (provSelect) {
                provSelect.value = '';
                document.getElementById('province_name').value = '';
                const distSelect = document.getElementById('district');
                distSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                distSelect.disabled = true;
                document.getElementById('district_name').value = '';
                const wardSelect = document.getElementById('ward');
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                wardSelect.disabled = true;
                document.getElementById('ward_name').value = '';
            }

            handleTypeChange();
            updateMoneyVndNotes();

            document.getElementById('buildingModal').classList.add('active');
        }

        async function openEditModal(buildingId) {
            try {
                const res = await fetch(`/api/buildings/${buildingId}`);
                const data = await res.json();

                if (!data.success || !data.building) {
                    showToast('Không tải được thông tin tòa nhà!', 'error');
                    return;
                }

                const b = data.building;

                document.getElementById('editBuildingId').value = buildingId;
                document.getElementById('buildingForm').reset();

                const titleEl = document.getElementById('modalTitle');
                if (titleEl) {
                    titleEl.innerHTML = `<i class="fa-solid fa-pen-to-square" style="color: var(--brand-primary);"></i> <span>Chỉnh Sửa Tòa Nhà / Cơ Sở</span>`;
                }

                // Nhóm 1: Định danh
                document.getElementById('b_name').value = b.name || '';
                const rawType = b.type || b.business_type || 'can_ho';
                const typeSelect = document.getElementById('property-type');
                if (typeSelect) {
                    typeSelect.value = rawType;
                    if (typeSelect.selectedIndex < 0) {
                        for (let opt of typeSelect.options) {
                            if (opt.value === rawType || opt.text.toLowerCase().includes(rawType.toLowerCase())) {
                                typeSelect.value = opt.value;
                                break;
                            }
                        }
                    }
                }
                document.getElementById('b_address_detail').value = b.address_detail || '';

                if (b.province) {
                    await setAddressByText(b.province, b.district, b.ward);
                }

                document.getElementById('province_name').value = b.province || '';
                document.getElementById('district_name').value = b.district || '';
                document.getElementById('ward_name').value = b.ward || '';

                // Cập nhật ẩn hiện trường theo loại hình (không autoToggle để giữ trạng thái đã lưu)
                handleTypeChange(false);

                // Xác định trạng thái công tắc Nhóm 2 theo dữ liệu của tòa nhà
                const hasPricing = (b.has_service_pricing !== undefined && b.has_service_pricing !== null)
                    ? Boolean(b.has_service_pricing)
                    : (Boolean(b.electricity_price && Number(b.electricity_price) > 0) || 
                       Boolean(b.water_price && Number(b.water_price) > 0) || 
                       !['nha_rieng', 'cum_nha_lien_ke', 'dat_phan_lo'].includes(rawType));
                
                toggleServicePricing(hasPricing);

                // Nhóm 2: Bảng giá dịch vụ dùng chung
                document.getElementById('b_electricity_price').value = (b.electricity_price !== null && b.electricity_price !== undefined && b.electricity_price !== '' && b.electricity_price != 0) ? b.electricity_price : '';
                document.getElementById('b_water_price').value = (b.water_price !== null && b.water_price !== undefined && b.water_price !== '' && b.water_price != 0) ? b.water_price : '';
                document.getElementById('b_water_calc_type').value = b.water_calc_type || 'm3';
                document.getElementById('waterUnitLabel').innerText = (b.water_calc_type === 'person') ? 'đ/người' : 'đ/m³';
                document.getElementById('b_service_fee').value = (b.service_fee !== null && b.service_fee !== undefined && b.service_fee !== '' && b.service_fee != 0) ? b.service_fee : '';
                document.getElementById('b_internet_fee').value = (b.internet_fee !== null && b.internet_fee !== undefined && b.internet_fee !== '' && b.internet_fee != 0) ? b.internet_fee : '';
                document.getElementById('b_parking_fee').value = (b.parking_fee !== null && b.parking_fee !== undefined && b.parking_fee !== '' && b.parking_fee != 0) ? b.parking_fee : '';
                updateMoneyVndNotes();

                // Nhóm 3: Quy mô & Phân quyền
                document.getElementById('b_total_floors').value = b.total_floors || 1;
                document.getElementById('b_total_rooms').value = b.total_rooms || '';
                document.getElementById('b_description').value = b.description || '';

                const mgrSelect = document.getElementById('b_manager_id');
                if (mgrSelect) {
                    const assignedList = Array.isArray(b.manager_ids) ? b.manager_ids : (Array.isArray(b.user_ids) ? b.user_ids : (b.manager_id ? [b.manager_id] : []));
                    mgrSelect.value = (assignedList.length > 0) ? String(assignedList[0]) : (b.manager_id ? String(b.manager_id) : '');
                }
                document.getElementById('b_status').value = b.status || 'active';

                // Checkboxes Tiện ích
                const amenities = Array.isArray(b.amenities) ? b.amenities : [];
                document.querySelectorAll('input[name="amenities[]"]').forEach(chk => {
                    const isChecked = amenities.includes(chk.value);
                    chk.checked = isChecked;
                    toggleAmenityStyle(chk);
                });

                // Nạp gallery ảnh cũ
                newSelectedFiles = [];
                existingImagesList = [];
                if (data.all_images && Array.isArray(data.all_images) && data.all_images.length > 0) {
                    existingImagesList = data.all_images.map(img => ({ id: img.id || '', url: img.url || '' }));
                } else if (b.all_images && Array.isArray(b.all_images) && b.all_images.length > 0) {
                    existingImagesList = b.all_images.map(img => ({ id: img.id || '', url: img.url || '' }));
                } else if (b.display_image && !b.display_image.includes('placehold.co')) {
                    existingImagesList = [{ id: b.image || '', url: b.display_image }];
                }
                renderGallery();

                document.getElementById('buildingModal').classList.add('active');

            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            }
        }

        function closeModal() {
            document.getElementById('buildingModal').classList.remove('active');
        }

        // ==========================================
        // 7. SUBMIT FORM QUA API POST / PUT
        // ==========================================
        async function handleFormSubmit(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btnSubmitForm');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            const editId = document.getElementById('editBuildingId').value;
            const isEdit = !!editId;

            const formData = new FormData();
            formData.append('name', document.getElementById('b_name').value.trim());

            const typeSelect = document.getElementById('property-type');
            const typeVal = typeSelect ? typeSelect.value : 'can_ho';
            const typeText = typeSelect && typeSelect.selectedIndex >= 0 ? typeSelect.options[typeSelect.selectedIndex].text.trim() : typeVal;
            formData.append('type', typeVal);
            formData.append('business_type', typeText);

            // Địa chỉ hành chính
            const provName = document.getElementById('province_name').value.trim() || (document.getElementById('province').selectedIndex > 0 ? document.getElementById('province').options[document.getElementById('province').selectedIndex].text : '');
            const distName = document.getElementById('district_name').value.trim() || (document.getElementById('district').selectedIndex > 0 ? document.getElementById('district').options[document.getElementById('district').selectedIndex].text : '');
            const wardName = document.getElementById('ward_name').value.trim() || (document.getElementById('ward').selectedIndex > 0 ? document.getElementById('ward').options[document.getElementById('ward').selectedIndex].text : '');

            formData.append('province', provName);
            formData.append('province_name', provName);
            formData.append('district', distName);
            formData.append('district_name', distName);
            formData.append('ward', wardName);
            formData.append('ward_name', wardName);
            formData.append('address_detail', document.getElementById('b_address_detail').value.trim());

            // Nhóm 2: Bảng giá dịch vụ dùng chung
            const isPricingApplied = document.getElementById('apply_service_pricing')?.checked ?? false;
            formData.append('has_service_pricing', isPricingApplied ? '1' : '0');

            if (isPricingApplied) {
                const elecVal = document.getElementById('b_electricity_price').value.trim();
                const waterVal = document.getElementById('b_water_price').value.trim();
                const servVal = document.getElementById('b_service_fee').value.trim();
                const netVal = document.getElementById('b_internet_fee').value.trim();
                const parkVal = document.getElementById('b_parking_fee').value.trim();

                if (elecVal !== '') formData.append('electricity_price', elecVal);
                if (waterVal !== '') formData.append('water_price', waterVal);
                formData.append('water_calc_type', document.getElementById('b_water_calc_type').value || 'm3');
                if (servVal !== '') formData.append('service_fee', servVal);
                if (netVal !== '') formData.append('internet_fee', netVal);
                if (parkVal !== '') formData.append('parking_fee', parkVal);
            } else {
                formData.append('electricity_price', '');
                formData.append('water_price', '');
                formData.append('water_calc_type', 'm3');
                formData.append('service_fee', '');
                formData.append('internet_fee', '');
                formData.append('parking_fee', '');
            }

            // Nhóm 3: Quy mô & Phân quyền
            formData.append('total_floors', document.getElementById('b_total_floors').value || 1);
            formData.append('total_rooms', document.getElementById('b_total_rooms').value || 1);
            formData.append('description', document.getElementById('b_description').value.trim());

            const mgrSelect = document.getElementById('b_manager_id');
            if (mgrSelect && mgrSelect.value) {
                formData.append('manager_id', mgrSelect.value);
                formData.append('manager_ids[]', mgrSelect.value);
            }

            formData.append('status', document.getElementById('b_status').value || 'active');

            // Amenities
            const chkBoxes = document.querySelectorAll('input[name="amenities[]"]:checked');
            chkBoxes.forEach(chk => {
                formData.append('amenities[]', chk.value);
            });

            // Ảnh cũ
            existingImagesList.forEach(img => {
                if (img.id) {
                    formData.append('existing_images[]', img.id);
                }
            });

            // Ảnh mới tải lên
            newSelectedFiles.forEach(file => {
                formData.append('images[]', file);
            });

            try {
                const url = isEdit ? `/api/buildings/${editId}` : '/api/buildings';

                if (isEdit) {
                    formData.append('_method', 'PUT');
                }

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    showToast(data.message || 'Có lỗi xảy ra khi lưu tòa nhà!', 'error');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu Tòa Nhà / Cơ Sở';
                    return;
                }

                showToast(data.message || 'Thao tác thành công!', 'success');
                closeModal();
                setTimeout(() => location.reload(), 700);

            } catch (err) {
                console.error(err);
                showToast('Lỗi kết nối máy chủ!', 'error');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu Tòa Nhà / Cơ Sở';
            }
        }

        // ==========================================
        // 8. XÓA TÒA NHÀ (QUY TẮC BẢO VỆ DỮ LIỆU TUYỆT ĐỐI - HARD BLOCK)
        // ==========================================
        async function deleteBuilding(id, name, postsCount, rentedCount) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (postsCount === undefined && row && row.dataset.postsCount !== undefined) {
                postsCount = parseInt(row.dataset.postsCount, 10);
            }
            if (rentedCount === undefined && row && row.dataset.rentedCount !== undefined) {
                rentedCount = parseInt(row.dataset.rentedCount, 10);
            }

            // Nếu chưa xác định được số lượng (ví dụ gọi từ phiên bản cũ), kiểm tra nhanh trước
            if (postsCount === undefined) {
                try {
                    const checkRes = await fetch(`/api/buildings/${id}/rooms-detail`);
                    const checkData = await checkRes.json();
                    const rooms = checkData.rooms || [];
                    postsCount = rooms.length;
                    rentedCount = rooms.filter(r => r.room_status === 'rented').length;
                } catch (e) {
                    postsCount = 0;
                    rentedCount = 0;
                }
            }

            // =========================================================================
            // QUY TẮC CHẶN TUYỆT ĐỐI (HARD BLOCK):
            // NẾU VẪN CÒN BÀI ĐĂNG HOẶC ĐANG CÓ PHÒNG CHO THUÊ TRONG TÒA NHÀ
            // => LẬP TỨC HIỂN THỊ POPUP ĐỎ CẢNH BÁO
            // => TUYỆT ĐỐI KHÔNG CÓ NÚT "ĐỒNG Ý" (CHỈ CÓ DUY NHẤT NÚT "ĐÃ HIỂU")
            // =========================================================================
            if (postsCount > 0 || rentedCount > 0) {
                let reasons = [];
                if (postsCount > 0) reasons.push(`<strong>${postsCount} bài đăng / căn nhà</strong>`);
                if (rentedCount > 0) reasons.push(`<strong style="color: #dc2626;">${rentedCount} căn đang có hợp đồng thuê</strong>`);
                const reasonStr = reasons.join(' và ');

                await Swal.fire({
                    icon: 'error',
                    iconColor: '#dc2626',
                    title: '<span style="color: #dc2626; font-weight: 800; font-size: 1.35rem;">🚨 Không thể xóa tòa nhà!</span>',
                    html: `
                        <div style="font-size: 0.95rem; color: #1e293b; text-align: left; line-height: 1.6; margin-top: 10px;">
                            <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                                <p style="color: #991b1b; font-weight: 600; margin: 0;">
                                    Tòa nhà <strong>"${name}"</strong> hiện đang chứa ${reasonStr} bên trong.
                                </p>
                            </div>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; font-size: 0.875rem; color: #475569;">
                                <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626; margin-right: 6px;"></i>
                                <strong>Quy tắc bảo vệ dữ liệu:</strong> Bạn phải thanh lý hợp đồng thuê và xóa toàn bộ các bài đăng / nhà bên trong trước khi có thể xóa tòa nhà này.
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: '<i class="fa-solid fa-shield-halved"></i> Đã hiểu',
                    showCancelButton: false, // TUYỆT ĐỐI KHÔNG CÓ NÚT HỦY / ĐỒNG Ý
                    allowOutsideClick: true,
                    customClass: {
                        popup: 'swal2-custom-popup'
                    }
                });
                return; // DỪNG LẠI NGAY LẬP TỨC - KHÔNG HIỂN THỊ NÚT ĐỒNG Ý
            }

            // =========================================================================
            // CHỈ KHI TÒA NHÀ HOÀN TOÀN TRỐNG (0 BÀI ĐĂNG & 0 ĐANG THUÊ)
            // MỚI BẬT SWEETALERT2 HỎI XÁC NHẬN VỚI NÚT "ĐỒNG Ý"
            // =========================================================================
            const confirmResult = await Swal.fire({
                title: 'Xác nhận xóa tòa nhà',
                html: `
                    <div style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                        Bạn có chắc chắn muốn xóa tòa nhà này không?
                    </div>
                    <div style="font-size: 0.925rem; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; margin-top: 10px;">
                        Tòa nhà: <strong style="color: #0284c7;">"${name}"</strong>
                        <div style="color: #059669; font-size: 0.8rem; font-weight: 600; margin-top: 4px;">
                            <i class="fa-solid fa-circle-check"></i> Tòa nhà trống hoàn toàn (0 bài đăng, không có phòng thuê)
                        </div>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#f59e0b',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Đồng ý',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'swal2-custom-popup'
                }
            });

            if (!confirmResult.isConfirmed) {
                return;
            }

            // Hiển thị trạng thái đang xử lý xóa
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Hệ thống đang thực hiện xóa tòa nhà...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const res = await fetch(`/api/buildings/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json().catch(() => ({}));

                // Nếu backend phát hiện lỗi bất ngờ
                if (!res.ok || data.success === false) {
                    await Swal.fire({
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: '<span style="color: #dc2626; font-weight: 800; font-size: 1.35rem;">🚨 Không thể xóa tòa nhà!</span>',
                        text: data.message || 'Tòa nhà vẫn còn bài đăng hoặc phòng thuê bên trong!',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: '<i class="fa-solid fa-shield-halved"></i> Đã hiểu',
                        showCancelButton: false,
                        customClass: {
                            popup: 'swal2-custom-popup'
                        }
                    });
                    return;
                }

                // Xóa thành công (HTTP 200)
                await Swal.fire({
                    icon: 'success',
                    iconColor: '#059669',
                    title: '<span style="color: #059669; font-weight: 800;">Xóa thành công!</span>',
                    text: data.message || `Đã xóa tòa nhà "${name}" thành công!`,
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: '<i class="fa-solid fa-check"></i> Hoàn tất',
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-custom-popup'
                    }
                });

                if (row) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(40px)';
                    setTimeout(() => row.remove(), 400);
                }
                setTimeout(() => location.reload(), 1000);

            } catch (err) {
                console.error('Lỗi khi gửi yêu cầu xóa tòa nhà:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể kết nối tới máy chủ. Vui lòng thử lại sau!',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Đóng'
                });
            }
        }

        // ==========================================
        // 9. KHỞI TẠO TRANG
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            loadProvinces();
            updateMoneyVndNotes();

            ['b_electricity_price', 'b_water_price', 'b_service_fee', 'b_internet_fee', 'b_parking_fee'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateMoneyVndNotes);
                    el.addEventListener('change', updateMoneyVndNotes);
                }
            });

            const waterCalc = document.getElementById('b_water_calc_type');
            if (waterCalc) {
                waterCalc.addEventListener('change', () => {
                    const isPerson = waterCalc.value === 'person';
                    const unitLbl = document.getElementById('waterUnitLabel');
                    if (unitLbl) unitLbl.innerText = isPerson ? 'đ/người' : 'đ/m³';
                    updateMoneyVndNotes();
                });
            }

            const dropzone = document.getElementById('imageDropzone');
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('dragover');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('dragover');
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    if (dt && dt.files) {
                        handleFilesSelect(dt.files);
                    }
                }, false);
            }
        });
    </script>
</body>
</html>
