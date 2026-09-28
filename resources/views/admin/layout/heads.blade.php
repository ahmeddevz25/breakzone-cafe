<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard') - Breakzone Cafe</title>

    <!-- Early Sidebar State Restore (Prevents Layout Flash) -->
    <script>
        (function() {
            try {
                if (window.innerWidth >= 1200 && localStorage.getItem('breakzone_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('layout-menu-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    @if(isset($cafeSetting) && $cafeSetting->logo)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $cafeSetting->logo) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/img/logo-right.png') }}" />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/core.css?v=2.1"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/css/theme-default.css?v=2.1"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/demo.css?v=2.1" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/datatable.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('admin') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('admin') }}/assets/js/config.js?v=2.1"></script>
    <!-- Global Styles & Button Press Override (Remove Purple) -->
    <style>
        :root {
            --bs-primary: #006037 !important;
            --bs-primary-rgb: 0, 96, 55 !important;
            --bs-link-color: #006037 !important;
            --bs-link-hover-color: #004d2c !important;
            --bs-purple: #006037 !important;
        }

        /* Button Primary - Default */
        .btn-primary,
        button.btn-primary,
        a.btn-primary,
        input[type="submit"].btn-primary,
        input[type="button"].btn-primary {
            background-color: #006037 !important;
            border-color: #006037 !important;
            color: #ffffff !important;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(0, 96, 55, 0.3) !important;
        }

        /* Button Primary - Hover */
        .btn-primary:hover,
        button.btn-primary:hover,
        a.btn-primary:hover {
            background-color: #004d2c !important;
            border-color: #004d2c !important;
            color: #ffffff !important;
            box-shadow: 0 0.25rem 0.5rem 0 rgba(0, 96, 55, 0.35) !important;
            transform: translateY(-1px);
        }

        /* Button Primary - Press / Active / Focus / Checked (No Purple) */
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active,
        .btn-primary:focus-visible,
        .btn-check:checked + .btn-primary,
        .btn-check:active + .btn-primary,
        .show > .btn-primary.dropdown-toggle,
        button.btn-primary:focus,
        button.btn-primary:active,
        a.btn-primary:focus,
        a.btn-primary:active,
        .btn-check:focus + .btn-primary,
        .btn-primary.focus {
            background-color: #004225 !important;
            border-color: #004225 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 96, 55, 0.25) !important;
            outline: none !important;
            transform: translateY(0);
        }

        /* Active + Focus combination */
        .btn-primary:active:focus,
        .btn-primary.active:focus,
        .btn-check:checked + .btn-primary:focus,
        .btn-check:active + .btn-primary:focus,
        .show > .btn-primary.dropdown-toggle:focus {
            background-color: #00361e !important;
            border-color: #00361e !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 96, 55, 0.3) !important;
        }

        /* =========================================================
           PRIMARY OUTLINE BUTTONS & VIEW PERMISSIONS BUTTON
           ========================================================= */
        /* Normal State (Not Hovered / Not Active) */
        .btn-outline-primary:not(:hover):not(:active),
        .view-permissions-modal-btn:not(:hover):not(:active),
        table.dataTable tbody tr td .view-permissions-modal-btn:not(:hover):not(:active),
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:not(:hover):not(:active),
        .table tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) {
            background-color: transparent !important;
            border: 1.5px solid #006037 !important;
            color: #006037 !important;
            transition: all 0.2s ease-in-out !important;
        }

        .btn-outline-primary:not(:hover):not(:active) i,
        .btn-outline-primary:not(:hover):not(:active) span:not(.badge),
        .view-permissions-modal-btn:not(:hover):not(:active) i,
        .view-permissions-modal-btn:not(:hover):not(:active) span:not(.badge),
        table.dataTable tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) i,
        table.dataTable tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) span:not(.badge),
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:not(:hover):not(:active) i,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:not(:hover):not(:active) span:not(.badge),
        .table tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) i,
        .table tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) span:not(.badge) {
            color: #006037 !important;
            transition: color 0.2s ease-in-out !important;
        }

        .btn-outline-primary:not(:hover):not(:active) .badge,
        .view-permissions-modal-btn:not(:hover):not(:active) .badge,
        table.dataTable tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) .badge,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:not(:hover):not(:active) .badge,
        .table tbody tr td .view-permissions-modal-btn:not(:hover):not(:active) .badge {
            background-color: #006037 !important;
            color: #ffffff !important;
            transition: all 0.2s ease-in-out !important;
        }

        /* Hover & Active States (solid brand green with crisp pure white text and icon) */
        .btn-outline-primary:hover,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .view-permissions-modal-btn:hover,
        .view-permissions-modal-btn:active,
        table.dataTable tbody tr td .view-permissions-modal-btn:hover,
        table.dataTable tbody tr td .view-permissions-modal-btn:active,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active,
        .table tbody tr td .view-permissions-modal-btn:hover,
        .table tbody tr td .view-permissions-modal-btn:active {
            background-color: #006037 !important;
            border-color: #006037 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(0, 96, 55, 0.3) !important;
        }

        .btn-outline-primary:hover *,
        .btn-outline-primary:active *,
        .btn-outline-primary.active *,
        .view-permissions-modal-btn:hover *,
        .view-permissions-modal-btn:active *,
        .view-permissions-modal-btn:hover i,
        .view-permissions-modal-btn:hover span,
        .view-permissions-modal-btn:hover span:not(.badge),
        .view-permissions-modal-btn:active i,
        .view-permissions-modal-btn:active span,
        .view-permissions-modal-btn:active span:not(.badge),
        table.dataTable tbody tr td .view-permissions-modal-btn:hover *,
        table.dataTable tbody tr td .view-permissions-modal-btn:hover i,
        table.dataTable tbody tr td .view-permissions-modal-btn:hover span,
        table.dataTable tbody tr td .view-permissions-modal-btn:hover span:not(.badge),
        table.dataTable tbody tr td .view-permissions-modal-btn:active *,
        table.dataTable tbody tr td .view-permissions-modal-btn:active i,
        table.dataTable tbody tr td .view-permissions-modal-btn:active span,
        table.dataTable tbody tr td .view-permissions-modal-btn:active span:not(.badge),
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover *,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover i,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover span,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover span:not(.badge),
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active *,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active i,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active span,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active span:not(.badge),
        .table tbody tr td .view-permissions-modal-btn:hover *,
        .table tbody tr td .view-permissions-modal-btn:hover i,
        .table tbody tr td .view-permissions-modal-btn:hover span,
        .table tbody tr td .view-permissions-modal-btn:active *,
        .table tbody tr td .view-permissions-modal-btn:active i,
        .table tbody tr td .view-permissions-modal-btn:active span {
            color: #ffffff !important;
        }

        .btn-outline-primary:hover .badge,
        .btn-outline-primary:active .badge,
        .view-permissions-modal-btn:hover .badge,
        .view-permissions-modal-btn:active .badge,
        table.dataTable tbody tr td .view-permissions-modal-btn:hover .badge,
        table.dataTable tbody tr td .view-permissions-modal-btn:active .badge,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:hover .badge,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:active .badge,
        .table tbody tr td .view-permissions-modal-btn:hover .badge,
        .table tbody tr td .view-permissions-modal-btn:active .badge {
            background-color: #ffffff !important;
            color: #006037 !important;
        }

        /* Focus state: ALWAYS transparent outline, green text, no dark fill */
        .btn-outline-primary:focus,
        .btn-outline-primary:focus:not(:hover),
        .view-permissions-modal-btn:focus,
        .view-permissions-modal-btn:focus:not(:hover),
        table.dataTable tbody tr td .view-permissions-modal-btn:focus,
        table.dataTable tbody tr td .view-permissions-modal-btn:focus:not(:hover),
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:focus:not(:hover),
        .table tbody tr td .view-permissions-modal-btn:focus,
        .table tbody tr td .view-permissions-modal-btn:focus:not(:hover) {
            background-color: transparent !important;
            border-color: #006037 !important;
            color: #006037 !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 96, 55, 0.2) !important;
        }

        .btn-outline-primary:focus:not(:hover) *,
        .view-permissions-modal-btn:focus:not(:hover) *,
        .view-permissions-modal-btn:focus:not(:hover) i,
        .view-permissions-modal-btn:focus:not(:hover) span,
        table.dataTable tbody tr td .view-permissions-modal-btn:focus:not(:hover) *,
        table.dataTable tbody tr td .view-permissions-modal-btn:focus:not(:hover) i,
        table.dataTable tbody tr td .view-permissions-modal-btn:focus:not(:hover) span,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:focus:not(:hover) *,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:focus:not(:hover) i,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:focus:not(:hover) span,
        .table tbody tr td .view-permissions-modal-btn:focus:not(:hover) *,
        .table tbody tr td .view-permissions-modal-btn:focus:not(:hover) i,
        .table tbody tr td .view-permissions-modal-btn:focus:not(:hover) span {
            color: #006037 !important;
        }

        .btn-outline-primary:focus:not(:hover) .badge,
        .view-permissions-modal-btn:focus:not(:hover) .badge,
        table.dataTable tbody tr td .view-permissions-modal-btn:focus:not(:hover) .badge,
        table.dataTable tbody tr:hover td .view-permissions-modal-btn:focus:not(:hover) .badge,
        .table tbody tr td .view-permissions-modal-btn:focus:not(:hover) .badge {
            background-color: #006037 !important;
            color: #ffffff !important;
        }

        /* Remove purple outline / glow on ANY button press/focus */
        .btn:focus,
        .btn:active,
        button:focus,
        button:active {
            outline: none !important;
        }

        .text-primary {
            color: #006037 !important;
        }
        .bg-primary {
            background-color: #006037 !important;
        }

        /* Status Badges */
        .badge.bg-success {
            background-color: #22c55e !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .badge.bg-danger {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Table Action Buttons */
        .table-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none !important;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 1.25rem;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
        .action-btn-view {
            color: #0284c7 !important;
        }
        .action-btn-view:hover {
            background-color: #e0f2fe;
            color: #0369a1 !important;
        }
        .action-btn-edit {
            color: #006037 !important;
        }
        .action-btn-edit:hover {
            background-color: #e8f5e9;
            color: #004d2c !important;
        }
        .action-btn-delete {
            color: #ef4444 !important;
        }
        .action-btn-delete:hover {
            background-color: #fee2e2;
            color: #dc2626 !important;
        }

        /* Modern Modal Styling */
        .modal-content {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal .btn-close,
        .modal .modal-header .btn-close,
        .modal-header .btn-close,
        .modal-header button.btn-close {
            position: static !important;
            margin: 0 !important;
            margin-top: 0 !important;
            margin-right: 0 !important;
            transform: none !important;
            padding: 0.5rem !important;
            opacity: 0.75 !important;
            box-shadow: none !important;
            background-color: transparent !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            visibility: visible !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }
        .modal .btn-close:hover,
        .modal .btn-close:focus,
        .modal .btn-close:active,
        .modal .modal-header .btn-close:hover,
        .modal-header button.btn-close:hover {
            opacity: 1 !important;
            transform: none !important;
            box-shadow: none !important;
            outline: none !important;
            background-color: rgba(0, 0, 0, 0.06) !important;
        }
        .modal-title {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .modal-body {
            padding: 24px;
            color: #000000 !important;
        }
        .modal-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 14px 24px;
        }

        /* High-Contrast Crisp Solid Black Text for ALL Modals, Inputs & Selects */
        .modal,
        .modal-dialog,
        .modal-content,
        .modal-header,
        .modal-title,
        .modal-body,
        .modal-footer {
            color: #000000 !important;
        }

        .modal label,
        .modal .form-label,
        .modal .col-form-label,
        .modal .form-label.fw-semibold {
            color: #000000 !important;
            font-weight: 600 !important;
        }

        /* In modal, convert any muted or grey text to pure black for high contrast */
        .modal .text-muted,
        .modal small.text-muted,
        .modal span.text-muted,
        .modal p.text-muted,
        .modal div.text-muted,
        .modal .text-secondary {
            color: #000000 !important;
            font-weight: 600 !important;
        }

        /* Form Inputs & Selects in Modals */
        .modal .form-control,
        .modal .form-select,
        .modal select,
        .modal select.product-select,
        .modal input[type="text"],
        .modal input[type="number"],
        .modal input[type="date"],
        .modal input[type="email"],
        .modal input[type="tel"],
        .modal input[type="password"],
        .modal textarea {
            color: #000000 !important;
            font-weight: 600 !important;
            border-color: #94a3b8 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #000000 !important;
        }

        .modal .form-select option,
        .modal select option {
            color: #000000 !important;
            background-color: #ffffff !important;
            font-weight: 500 !important;
        }

        .modal .form-control:focus,
        .modal .form-select:focus,
        .modal select:focus,
        .modal input:focus,
        .modal textarea:focus {
            color: #000000 !important;
            border-color: #006037 !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 96, 55, 0.2) !important;
            -webkit-text-fill-color: #000000 !important;
        }

        .modal .form-control:disabled,
        .modal .form-control[readonly],
        .modal .form-select:disabled,
        .modal input:disabled,
        .modal input[readonly] {
            color: #000000 !important;
            background-color: #f8fafc !important;
            opacity: 1 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 600 !important;
        }

        .modal .form-control::placeholder,
        .modal input::placeholder,
        .modal textarea::placeholder {
            color: #64748b !important;
            opacity: 1 !important;
            -webkit-text-fill-color: #64748b !important;
            font-weight: 400 !important;
        }

        /* Modal Tables: Headers, Data Cells, Footers */
        .modal table thead th,
        .modal table thead tr th,
        .modal .table thead th,
        .modal table thead tr,
        .modal .table thead tr {
            color: #000000 !important;
            font-weight: 700 !important;
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }

        .modal table tbody tr td,
        .modal table tbody tr th,
        .modal .table tbody tr td,
        .modal table tbody tr {
            color: #000000 !important;
            font-weight: 500 !important;
            border-color: #e2e8f0 !important;
        }

        .modal table tfoot,
        .modal table tfoot tr td,
        .modal table tfoot tr span:not(.text-success):not(.text-danger):not(.badge) {
            color: #000000 !important;
            font-weight: 700 !important;
        }

        .modal .row-unit-badge {
            color: #000000 !important;
            background-color: #f1f5f9 !important;
            font-weight: 600 !important;
            border: 1px solid #cbd5e1 !important;
        }

        .modal .row-available-stock,
        .modal .row-total-price,
        .modal .row-index {
            color: #000000 !important;
            font-weight: 700 !important;
        }

        .modal .input-group-text {
            color: #000000 !important;
            font-weight: 600 !important;
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
        }

        /* Number inputs without overlapping stepper arrows */
        input[type=number].no-spin::-webkit-inner-spin-button, 
        input[type=number].no-spin::-webkit-outer-spin-button { 
            -webkit-appearance: none !important; 
            margin: 0 !important;
            display: none !important;
        }
        input[type=number].no-spin {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }

        /* Crisp High-Contrast Black Text for ALL Tables across ALL Modules */
        table.dataTable tbody tr td,
        .table tbody tr td,
        .table tbody tr th {
            color: #111827 !important;
            font-weight: 500 !important;
            font-size: 0.9375rem !important; /* Standard full body size (15px) */
        }

        table.dataTable tbody tr td .font-monospace,
        .table tbody tr td .font-monospace {
            color: #111827 !important;
            font-weight: 500 !important;
        }

        table.dataTable tbody tr:hover td,
        .table tbody tr:hover td {
            color: #000000 !important;
        }

        /* Global DataTable Sorting & Pagination Override */
        table.dataTable thead th,
        table.dataTable thead td {
            pointer-events: none;
            cursor: default !important;
            background-image: none !important;
            padding-right: 10px !important;
        }

        table.dataTable thead th::before,
        table.dataTable thead th::after,
        table.dataTable thead td::before,
        table.dataTable thead td::after {
            display: none !important;
        }

        .page-item.active .page-link,
        .pagination .active > .page-link,
        div.dataTables_wrapper div.dataTables_paginate ul.pagination li.active a {
            background-color: #006037 !important;
            border-color: #006037 !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        /* SweetAlert2 Theme Customization */
        .swal2-popup {
            border-radius: 14px !important;
            padding: 1.75rem !important;
            font-family: inherit !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.16) !important;
        }
        .swal2-title {
            font-size: 1.35rem !important;
            font-weight: 700 !important;
            color: #2b3445 !important;
            margin-bottom: 0.5rem !important;
        }
        .swal2-html-container {
            font-size: 0.95rem !important;
            color: #566a7f !important;
            margin-top: 0.5rem !important;
            line-height: 1.5 !important;
        }
        .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 0.75rem !important;
        }
        .swal2-actions .btn {
            border-radius: 8px !important;
            padding: 0.55rem 1.35rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            box-shadow: none !important;
        }

        /* ==========================================================================
           ULTRA-PREMIUM MODERN SAAS PRODUCT SIDEBAR
           ========================================================================== */
        #layout-menu.saas-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 260px !important;
            height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            background: #ffffff !important;
            border-right: 1px solid #edf2f7 !important;
            box-shadow: 2px 0 18px rgba(0, 0, 0, 0.025) !important;
            z-index: 1050 !important;
            overflow: visible !important;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .layout-page {
            transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        @media (min-width: 1200px) {
            .layout-page {
                padding-left: 260px !important;
            }
        }

        /* 1. SaaS Brand Header */
        .saas-brand-header {
            flex-shrink: 0 !important;
            height: 76px !important;
            padding: 14px 16px !important;
            margin: 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #ffffff !important;
            position: relative !important;
        }

        .saas-brand-link {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            text-decoration: none !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .saas-brand-logo-container {
            width: 44px !important;
            height: 44px !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 1px solid #eef2f6 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .saas-brand-img {
            max-width: 90% !important;
            max-height: 90% !important;
            object-fit: contain !important;
        }

        .saas-brand-details {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
        }

        .saas-brand-name {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.25 !important;
            letter-spacing: -0.015em !important;
        }

        .saas-brand-status {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 10.5px !important;
            color: #059669 !important;
            font-weight: 600 !important;
            margin-top: 3px !important;
        }

        .saas-pulse-dot {
            width: 6px !important;
            height: 6px !important;
            border-radius: 50% !important;
            background-color: #10b981 !important;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7) !important;
            animation: saasPulse 2s infinite !important;
        }

        @keyframes saasPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .saas-toggle-btn {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0 !important;
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            flex-shrink: 0 !important;
        }
        .saas-toggle-btn:hover {
            background-color: #006037 !important;
            border-color: #006037 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(0, 96, 55, 0.25) !important;
            transform: scale(1.05) !important;
        }

        /* 2. Menu Inner Scrollable Body */
        .saas-menu-inner {
            flex: 1 1 0 !important;
            height: 0 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 10px 12px 24px 12px !important;
            margin: 0 !important;
            list-style: none !important;
            scrollbar-width: thin !important;
            scrollbar-color: #e2e8f0 transparent !important;
        }

        .saas-menu-inner::-webkit-scrollbar {
            width: 4px !important;
        }
        .saas-menu-inner::-webkit-scrollbar-track {
            background: transparent !important;
        }
        .saas-menu-inner::-webkit-scrollbar-thumb {
            background: #e2e8f0 !important;
            border-radius: 10px !important;
        }
        .saas-menu-inner:hover::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }

        /* 3. Section Headers */
        .saas-section-header {
            margin: 14px 6px 5px 6px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            list-style: none !important;
        }
        .saas-header-text {
            font-size: 10px !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
            color: #1e293b !important;
            white-space: nowrap !important;
        }
        .saas-header-line {
            flex: 1 !important;
            height: 1px !important;
            background: #e2e8f0 !important;
        }

        /* 4. Menu Items */
        .saas-menu-item {
            margin: 3px 0 !important;
            list-style: none !important;
        }

        .saas-menu-item .menu-link {
            display: flex !important;
            align-items: center !important;
            padding: 7.5px 10px !important;
            border-radius: 10px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            text-decoration: none !important;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            border: none !important;
            background: transparent !important;
        }

        /* Micro Icon Badges */
        .saas-icon-badge {
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.15rem !important;
            margin-right: 11px !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .icon-badge-emerald { background: #ecfdf5 !important; color: #059669 !important; }
        .icon-badge-green   { background: #f0fdf4 !important; color: #16a34a !important; }
        .icon-badge-teal    { background: #f0fdfa !important; color: #0d9488 !important; }
        .icon-badge-sky     { background: #f0f9ff !important; color: #0284c7 !important; }
        .icon-badge-amber   { background: #fffbeb !important; color: #d97706 !important; }
        .icon-badge-rose    { background: #fff1f2 !important; color: #e11d48 !important; }
        .icon-badge-purple  { background: #faf5ff !important; color: #7c3aed !important; }
        .icon-badge-orange  { background: #fff7ed !important; color: #ea580c !important; }
        .icon-badge-yellow  { background: #fefce8 !important; color: #ca8a04 !important; }
        .icon-badge-blue    { background: #eff6ff !important; color: #2563eb !important; }
        .icon-badge-indigo  { background: #eef2ff !important; color: #4f46e5 !important; }
        .icon-badge-pink    { background: #fdf2f8 !important; color: #db2777 !important; }
        .icon-badge-slate   { background: #f1f5f9 !important; color: #334155 !important; }
        .icon-badge-cyan    { background: #ecfeff !important; color: #0891b2 !important; }

        .saas-link-text {
            flex: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        /* Hover State */
        .saas-menu-item:not(.active) .menu-link:hover {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            transform: translateX(3px) !important;
        }

        .saas-menu-item:not(.active) .menu-link:hover .saas-link-text {
            color: #000000 !important;
            font-weight: 700 !important;
        }

        .saas-menu-item:not(.active) .menu-link:hover .saas-icon-badge {
            transform: scale(1.08) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
        }

        /* Active State (BreakZone Signature Emerald Gradient Pill) */
        .saas-menu-item.active .menu-link {
            background: linear-gradient(135deg, #006037 0%, #004d2c 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 14px rgba(0, 96, 55, 0.28) !important;
            transform: none !important;
        }

        .saas-menu-item.active .saas-link-text {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        .saas-menu-item.active .saas-icon-badge {
            background: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .saas-menu-item.active .saas-active-pill {
            display: block !important;
            width: 5px !important;
            height: 5px !important;
            border-radius: 50% !important;
            background: #ffffff !important;
            opacity: 0.9 !important;
            margin-left: 6px !important;
        }

        /* 5. SaaS Sidebar Footer Card (Permanently Pinned Bottom) */
        .saas-sidebar-footer {
            flex-shrink: 0 !important;
            margin-top: auto !important;
            padding: 12px 14px !important;
            border-top: 1px solid #f1f5f9 !important;
            background: #fafbfc !important;
        }

        .saas-user-card {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            border: 1px solid #eef2f6 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        .saas-user-avatar-wrap {
            position: relative !important;
            flex-shrink: 0 !important;
        }

        .saas-user-avatar {
            width: 34px !important;
            height: 34px !important;
            border-radius: 9px !important;
            background: linear-gradient(135deg, #006037 0%, #004d2c 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12.5px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            letter-spacing: 0.5px !important;
        }

        .saas-avatar-dot {
            position: absolute !important;
            bottom: -1px !important;
            right: -1px !important;
            width: 8.5px !important;
            height: 8.5px !important;
            border-radius: 50% !important;
            background-color: #10b981 !important;
            border: 2px solid #ffffff !important;
        }

        .saas-user-details {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .saas-user-name {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #000000 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.25 !important;
        }

        .saas-user-subtitle {
            font-size: 11px !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            line-height: 1.25 !important;
            margin-top: 2px !important;
        }

        .saas-footer-actions {
            display: flex !important;
            align-items: center !important;
            gap: 2px !important;
        }

        .saas-footer-icon-btn {
            width: 28px !important;
            height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 6px !important;
            color: #1e293b !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
            font-size: 16px !important;
        }

        .saas-footer-icon-btn:hover {
            background-color: #f1f5f9 !important;
            color: #006037 !important;
        }

        .saas-footer-icon-btn.text-danger:hover {
            background-color: #fef2f2 !important;
            color: #ef4444 !important;
        }

        .saas-status-bar {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            margin-top: 9px !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: 0.02em !important;
        }

        .saas-status-indicator {
            width: 6px !important;
            height: 6px !important;
            border-radius: 50% !important;
            background-color: #10b981 !important;
        }

        /* ==========================================================================
           DESKTOP COLLAPSED STATE (html.layout-menu-collapsed)
           ========================================================================== */
        @media (min-width: 1200px) {
            html.layout-menu-collapsed #layout-menu.saas-sidebar {
                width: 76px !important;
                min-width: 76px !important;
                max-width: 76px !important;
                overflow: visible !important;
                box-shadow: 2px 0 16px rgba(0, 0, 0, 0.04) !important;
            }

            html.layout-menu-collapsed .layout-page {
                padding-left: 76px !important;
            }

            /* Brand Header Collapsed */
            html.layout-menu-collapsed .saas-brand-header {
                height: 72px !important;
                padding: 12px 0 !important;
                justify-content: center !important;
                position: relative !important;
                overflow: visible !important;
            }

            html.layout-menu-collapsed .saas-brand-link {
                justify-content: center !important;
                gap: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                flex: none !important;
            }

            html.layout-menu-collapsed .saas-brand-logo-container {
                width: 42px !important;
                height: 42px !important;
                border-radius: 10px !important;
                margin: 0 auto !important;
            }

            html.layout-menu-collapsed .saas-brand-details {
                display: none !important;
            }

            /* Floating Edge Toggle Pill */
            html.layout-menu-collapsed .saas-toggle-btn {
                position: absolute !important;
                right: -13px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                width: 26px !important;
                height: 26px !important;
                border-radius: 50% !important;
                background: #ffffff !important;
                border: 1.5px solid #cbd5e1 !important;
                color: #0f172a !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.14) !important;
                z-index: 1070 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
            }

            html.layout-menu-collapsed .saas-toggle-btn:hover {
                background: #006037 !important;
                border-color: #006037 !important;
                color: #ffffff !important;
                box-shadow: 0 3px 10px rgba(0, 96, 55, 0.35) !important;
                transform: translateY(-50%) scale(1.12) !important;
            }

            /* Menu Scrollable Body Collapsed */
            html.layout-menu-collapsed .saas-menu-inner,
            html.layout-menu-collapsed .menu-inner {
                flex: 1 1 0 !important;
                height: 0 !important;
                min-height: 0 !important;
                width: 76px !important;
                min-width: 76px !important;
                max-width: 76px !important;
                padding: 10px 0 40px 0 !important;
                margin: 0 !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                scrollbar-width: none !important; /* Hide scrollbar completely so it never overlaps icons */
                -ms-overflow-style: none !important;
            }

            html.layout-menu-collapsed .saas-menu-inner::-webkit-scrollbar,
            html.layout-menu-collapsed .menu-inner::-webkit-scrollbar,
            html.layout-menu-collapsed .ps__rail-y,
            html.layout-menu-collapsed .ps__rail-x {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            /* Overwrite Sneat core.css hidden divs / opacity */
            html.layout-menu-collapsed .menu-inner > .menu-item,
            html.layout-menu-collapsed .saas-menu-item {
                width: 76px !important;
                min-width: 76px !important;
                max-width: 76px !important;
                height: 48px !important;
                min-height: 48px !important;
                margin: 4px 0 !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                position: relative !important;
                list-style: none !important;
                opacity: 1 !important;
                visibility: visible !important;
                flex-shrink: 0 !important; /* Prevents icons from shrinking into each other */
            }

            html.layout-menu-collapsed .saas-menu-item .menu-link {
                width: 48px !important;
                height: 48px !important;
                padding: 0 !important;
                margin: 0 auto !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 12px !important;
                background: transparent !important;
                transform: none !important;
                position: relative !important;
                opacity: 1 !important;
                visibility: visible !important;
                flex-shrink: 0 !important;
            }

            html.layout-menu-collapsed .saas-menu-item.active .menu-link {
                background: linear-gradient(135deg, #006037 0%, #004d2c 100%) !important;
                box-shadow: 0 4px 12px rgba(0, 96, 55, 0.35) !important;
            }

            html.layout-menu-collapsed .saas-menu-item:not(.active) .menu-link:hover {
                background-color: #f1f5f9 !important;
                transform: scale(1.08) !important;
            }

            /* Micro Icon Badges in Collapsed Mode */
            html.layout-menu-collapsed .saas-icon-badge {
                width: 38px !important;
                height: 38px !important;
                margin: 0 auto !important;
                padding: 0 !important;
                border-radius: 10px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
                flex-shrink: 0 !important;
            }

            html.layout-menu-collapsed .saas-icon-badge i {
                display: inline-block !important;
                font-size: 1.3rem !important;
                opacity: 1 !important;
                visibility: visible !important;
            }

            html.layout-menu-collapsed .saas-menu-item.active .saas-icon-badge {
                background: rgba(255, 255, 255, 0.22) !important;
                color: #ffffff !important;
            }

            /* Hide Text & Pill in Collapsed Mode */
            html.layout-menu-collapsed .saas-link-text,
            html.layout-menu-collapsed .saas-active-pill,
            html.layout-menu-collapsed .saas-header-text {
                display: none !important;
            }

            /* Section Dividers in Collapsed Mode */
            html.layout-menu-collapsed .saas-section-header {
                width: 76px !important;
                margin: 12px 0 6px 0 !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                list-style: none !important;
                flex-shrink: 0 !important; /* Prevents dividers from collapsing */
            }

            html.layout-menu-collapsed .saas-header-line {
                width: 30px !important;
                height: 1.5px !important;
                background: #e2e8f0 !important;
                margin: 0 auto !important;
                flex: none !important;
                display: block !important;
            }

            /* Hover Tooltips in Collapsed Mode */
            html.layout-menu-collapsed .saas-menu-item[data-title]:hover::after {
                content: attr(data-title);
                position: absolute;
                left: calc(100% + 8px);
                top: 50%;
                transform: translateY(-50%);
                background: #0f172a;
                color: #ffffff;
                font-size: 12.5px;
                font-weight: 600;
                padding: 6px 13px;
                border-radius: 7px;
                white-space: nowrap;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.22);
                z-index: 1080;
                pointer-events: none;
                animation: saasTooltipIn 0.15s ease-out;
            }

            html.layout-menu-collapsed .saas-menu-item[data-title]:hover::before {
                content: "";
                position: absolute;
                left: calc(100% + 2px);
                top: 50%;
                transform: translateY(-50%);
                border: 6px solid transparent;
                border-right-color: #0f172a;
                z-index: 1080;
                pointer-events: none;
            }

            @keyframes saasTooltipIn {
                from { opacity: 0; transform: translateY(-50%) translateX(-4px); }
                to { opacity: 1; transform: translateY(-50%) translateX(0); }
            }

            /* Footer Collapsed */
            html.layout-menu-collapsed .saas-sidebar-footer {
                width: 76px !important;
                min-width: 76px !important;
                max-width: 76px !important;
                flex: 0 0 68px !important;
                height: 68px !important;
                padding: 0 !important;
                margin-top: auto !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                background: #ffffff !important;
                border-top: 1px solid #f1f5f9 !important;
                box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.04) !important;
                z-index: 30 !important;
                position: relative !important;
                overflow: visible !important;
            }

            html.layout-menu-collapsed .saas-user-card {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                background: transparent !important;
                box-shadow: none !important;
                justify-content: center !important;
                display: flex !important;
                position: relative !important;
            }

            html.layout-menu-collapsed .saas-user-card:hover::after {
                content: "Admin Profile";
                position: absolute;
                left: calc(100% + 8px);
                top: 50%;
                transform: translateY(-50%);
                background: #0f172a;
                color: #ffffff;
                font-size: 12px;
                font-weight: 600;
                padding: 5px 12px;
                border-radius: 6px;
                white-space: nowrap;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
                z-index: 1080;
                pointer-events: none;
            }

            html.layout-menu-collapsed .saas-user-card:hover::before {
                content: "";
                position: absolute;
                left: calc(100% + 2px);
                top: 50%;
                transform: translateY(-50%);
                border: 6px solid transparent;
                border-right-color: #0f172a;
                z-index: 1080;
                pointer-events: none;
            }

            html.layout-menu-collapsed .saas-user-avatar {
                width: 40px !important;
                height: 40px !important;
                border-radius: 11px !important;
            }

            html.layout-menu-collapsed .saas-user-details,
            html.layout-menu-collapsed .saas-footer-actions,
            html.layout-menu-collapsed .saas-status-bar {
                display: none !important;
            }
        }

        /* ==========================================================================
           MOBILE / TABLET DRAWER (< 1200px)
           ========================================================================== */
        @media (max-width: 1199.98px) {
            #layout-menu.saas-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 270px !important;
                transform: translateX(-100%) !important;
                transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: none !important;
                z-index: 1100 !important;
            }

            html.layout-menu-expanded #layout-menu.saas-sidebar {
                transform: translateX(0) !important;
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.3) !important;
            }

            .layout-page {
                padding-left: 0 !important;
            }

            #layout-navbar {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .saas-toggle-btn {
                display: inline-flex !important;
            }
        }
    </style>
</head>
