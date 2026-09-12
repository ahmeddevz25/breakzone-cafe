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

        /* Global DataTable Sorting Override */
        table.dataTable thead th,
        table.dataTable thead td {
            pointer-events: none;
            /* Disable clicks */
            cursor: default !important;
            /* Reset cursor */
            background-image: none !important;
            /* Remove any sorting arrows */
            padding-right: 10px !important;
            /* Reset padding reserved for arrows */
        }

        table.dataTable thead th::before,
        table.dataTable thead th::after,
        table.dataTable thead td::before,
        table.dataTable thead td::after {
            display: none !important;
            /* Hide pseudo-element arrows */
        }
    </style>
</head>
