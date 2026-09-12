<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
    style="top: 0; height: 100vh; overflow-y: auto;">

    <!-- BRAND / LOGO -->
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link w-100 justify-content-center">
            @if(isset($cafeSetting) && $cafeSetting->logo)
                <img src="{{ asset('storage/' . $cafeSetting->logo) }}" alt="logo"
                    style="max-width: 100%; max-height: 97px; height: auto; object-fit: contain;">
            @else
                <img src="{{ asset('admin/assets/img/logo-right.png') }}" alt="logo"
                    style="max-width: 100%; max-height: 97px; height: auto; object-fit: contain;">
            @endif
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- SIDEBAR MENU -->
    <ul class="menu-inner py-1">

        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Cafe Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Cafe</span>
        </li>
        @can('store management')
            <li class="menu-item {{ request()->routeIs('stores.*') ? 'active open' : '' }}">
                <a href="{{ route('stores.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-store"></i>
                    <div>Stores</div>
                </a>
            </li>
        @endcan
        @can('supplier management')
            <li class="menu-item {{ request()->routeIs('suppliers.*') ? 'active open' : '' }}">
                <a href="{{ route('suppliers.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Suppliers</div>
                </a>
            </li>
        @endcan
        @can('brand management')
            <li class="menu-item {{ request()->routeIs('brands.*') ? 'active open' : '' }}">
                <a href="{{ route('brands.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-purchase-tag"></i>
                    <div>Brands</div>
                </a>
            </li>
        @endcan
        @can('category management')
            <li class="menu-item {{ request()->routeIs('categories.*') ? 'active open' : '' }}">
                <a href="{{ route('categories.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-category"></i>
                    <div>Categories</div>
                </a>
            </li>
        @endcan
        @can('unit management')
            <li class="menu-item {{ request()->routeIs('units.*') ? 'active open' : '' }}">
                <a href="{{ route('units.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cube"></i>
                    <div>Units</div>
                </a>
            </li>
        @endcan
        @can('item management')
            <li class="menu-item {{ request()->routeIs('items.*') ? 'active open' : '' }}">
                <a href="{{ route('items.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-coffee"></i>
                    <div>Items</div>
                </a>
            </li>
        @endcan


        @can('user management')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Users Management</span>
            </li>

            <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>View Users</div>
                </a>
            </li>
        @endcan


        {{-- Roles --}}
        @can('role management')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Roles Management</span>
            </li>

            <li class="menu-item {{ request()->routeIs('roles*') ? 'active' : '' }}">
                <a href="{{ route('roles') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                    <div>View Roles</div>
                </a>
            </li>
        @endcan


        {{-- Permissions --}}
        @can('permission management')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Permissions Management</span>
            </li>

            <li class="menu-item {{ request()->routeIs('permissions*') ? 'active' : '' }}">
                <a href="{{ route('permissions') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-lock-alt"></i>
                    <div>View Permissions</div>
                </a>
            </li>
        @endcan




        {{-- Cache Clear --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Cache Clear</span>
        </li>

        <li class="menu-item {{ request()->routeIs('clearcache') ? 'active' : '' }}">
            <a href="{{ route('clearcache') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-refresh"></i>
                <div>Cache Clear</div>
            </a>
        </li>

        {{-- Cafe Settings --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Settings</span>
        </li>

        <li class="menu-item {{ request()->routeIs('cafe-settings.*') ? 'active' : '' }}">
            <a href="{{ route('cafe-settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Cafe Settings</div>
            </a>
        </li>

    </ul>
</aside>
