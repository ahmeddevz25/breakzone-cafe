<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard') - Breakzone Cafe</title>

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

        /* Outline Primary */
        .btn-outline-primary {
            color: #006037 !important;
            border-color: #006037 !important;
            background-color: transparent !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active,
        .btn-outline-primary.active,
        .btn-check:checked + .btn-outline-primary,
        .btn-check:active + .btn-outline-primary,
        .btn-check:focus + .btn-outline-primary {
            background-color: #006037 !important;
            border-color: #006037 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 96, 55, 0.25) !important;
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
        .table tbody tr td .font-monospace,
        table.dataTable tbody tr td span:not(.badge),
        .table tbody tr td span:not(.badge) {
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
    </style>
</head>
