<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme saas-sidebar">

    <!-- SAAS BRAND HEADER -->
    <div class="saas-brand-header">
        <a href="{{ route('dashboard') }}" class="saas-brand-link">
            <div class="saas-brand-logo-container">
                @if(isset($cafeSetting) && $cafeSetting->logo)
                    <img src="{{ asset('storage/' . $cafeSetting->logo) }}" alt="BreakZone Logo" class="saas-brand-img">
                @else
                    <img src="{{ asset('admin/assets/img/logo-right.png') }}" alt="BreakZone Logo" class="saas-brand-img">
                @endif
            </div>
            <div class="saas-brand-details">
                <div class="saas-brand-name">{{ $cafeSetting->cafe_name ?? 'BreakZone Cafe' }}</div>
                <div class="saas-brand-status">
                    <span class="saas-pulse-dot"></span>
                    <span class="saas-status-text">Cloud POS • Active</span>
                </div>
            </div>
        </a>

        <a href="javascript:void(0);" id="saas-sidebar-toggle" class="saas-toggle-btn" title="Toggle Sidebar">
            <i class="bx bx-chevron-left bx-sm align-middle" id="saas-sidebar-toggle-icon"></i>
        </a>
    </div>

    <!-- SIDEBAR MENU SCROLLABLE BODY -->
    <ul class="menu-inner py-1 saas-menu-inner">

        <!-- SECTION: MAIN -->
        <li class="saas-section-header">
            <span class="saas-header-text">MAIN</span>
            <span class="saas-header-line"></span>
        </li>

        <!-- Dashboard -->
        <li class="menu-item saas-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-title="Dashboard">
            <a href="{{ route('dashboard') }}" class="menu-link" title="Dashboard">
                <span class="saas-icon-badge icon-badge-emerald">
                    <i class="bx bx-grid-alt"></i>
                </span>
                <span class="saas-link-text">Dashboard</span>
                <span class="saas-active-pill"></span>
            </a>
        </li>

        <!-- SECTION: CAFE OPERATIONS -->
        <li class="saas-section-header">
            <span class="saas-header-text">CAFE OPERATIONS</span>
            <span class="saas-header-line"></span>
        </li>

        @can('store management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('stores.*') ? 'active open' : '' }}" data-title="Stores">
                <a href="{{ route('stores.index') }}" class="menu-link" title="Stores">
                    <span class="saas-icon-badge icon-badge-green">
                        <i class="bx bx-store-alt"></i>
                    </span>
                    <span class="saas-link-text">Stores</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('supplier management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('suppliers.*') ? 'active open' : '' }}" data-title="Suppliers">
                <a href="{{ route('suppliers.index') }}" class="menu-link" title="Suppliers">
                    <span class="saas-icon-badge icon-badge-teal">
                        <i class="bx bx-group"></i>
                    </span>
                    <span class="saas-link-text">Suppliers</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('brand management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('brands.*') ? 'active open' : '' }}" data-title="Brands">
                <a href="{{ route('brands.index') }}" class="menu-link" title="Brands">
                    <span class="saas-icon-badge icon-badge-sky">
                        <i class="bx bx-badge-check"></i>
                    </span>
                    <span class="saas-link-text">Brands</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('category management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('categories.*') ? 'active open' : '' }}" data-title="Categories">
                <a href="{{ route('categories.index') }}" class="menu-link" title="Categories">
                    <span class="saas-icon-badge icon-badge-amber">
                        <i class="bx bx-category-alt"></i>
                    </span>
                    <span class="saas-link-text">Categories</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('unit management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('units.*') ? 'active open' : '' }}" data-title="Units">
                <a href="{{ route('units.index') }}" class="menu-link" title="Units">
                    <span class="saas-icon-badge icon-badge-rose">
                        <i class="bx bx-ruler"></i>
                    </span>
                    <span class="saas-link-text">Units</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('item management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('items.*') ? 'active open' : '' }}" data-title="Items">
                <a href="{{ route('items.index') }}" class="menu-link" title="Items">
                    <span class="saas-icon-badge icon-badge-purple">
                        <i class="bx bx-coffee"></i>
                    </span>
                    <span class="saas-link-text">Items</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('ingredient management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('ingredients.*') ? 'active open' : '' }}" data-title="Ingredients">
                <a href="{{ route('ingredients.index') }}" class="menu-link" title="Ingredients">
                    <span class="saas-icon-badge icon-badge-emerald">
                        <i class="bx bx-dish"></i>
                    </span>
                    <span class="saas-link-text">Ingredients</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('food management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('foods.*') ? 'active open' : '' }}" data-title="Foods">
                <a href="{{ route('foods.index') }}" class="menu-link" title="Foods">
                    <span class="saas-icon-badge icon-badge-orange">
                        <i class="bx bx-restaurant"></i>
                    </span>
                    <span class="saas-link-text">Foods</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        @can('purchase management')
            <li class="menu-item saas-menu-item {{ request()->routeIs('purchases.*') ? 'active open' : '' }}" data-title="Purchases">
                <a href="{{ route('purchases.index') }}" class="menu-link" title="Purchases">
                    <span class="saas-icon-badge icon-badge-yellow">
                        <i class="bx bx-cart-alt"></i>
                    </span>
                    <span class="saas-link-text">Purchases</span>
                    <span class="saas-active-pill"></span>
                </a>
            </li>
        @endcan

        <!-- SECTION: ACCESS CONTROL -->
        @if(auth()->user()->can('user management') || auth()->user()->can('role management') || auth()->user()->can('permission management'))
            <li class="saas-section-header">
                <span class="saas-header-text">ACCESS CONTROL</span>
                <span class="saas-header-line"></span>
            </li>

            @can('user management')
                <li class="menu-item saas-menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}" data-title="Users">
                    <a href="{{ route('users.index') }}" class="menu-link" title="Users">
                        <span class="saas-icon-badge icon-badge-blue">
                            <i class="bx bx-user-pin"></i>
                        </span>
                        <span class="saas-link-text">Users</span>
                        <span class="saas-active-pill"></span>
                    </a>
                </li>
            @endcan

            {{-- Roles --}}
            @can('role management')
                <li class="menu-item saas-menu-item {{ request()->routeIs('roles*') ? 'active' : '' }}" data-title="Roles">
                    <a href="{{ route('roles') }}" class="menu-link" title="Roles">
                        <span class="saas-icon-badge icon-badge-indigo">
                            <i class="bx bx-shield-quarter"></i>
                        </span>
                        <span class="saas-link-text">Roles</span>
                        <span class="saas-active-pill"></span>
                    </a>
                </li>
            @endcan

            {{-- Permissions --}}
            @can('permission management')
                <li class="menu-item saas-menu-item {{ request()->routeIs('permissions*') ? 'active' : '' }}" data-title="Permissions">
                    <a href="{{ route('permissions') }}" class="menu-link" title="Permissions">
                        <span class="saas-icon-badge icon-badge-pink">
                            <i class="bx bx-key"></i>
                        </span>
                        <span class="saas-link-text">Permissions</span>
                        <span class="saas-active-pill"></span>
                    </a>
                </li>
            @endcan
        @endif

        <!-- SECTION: SYSTEM PREFERENCES -->
        <li class="saas-section-header">
            <span class="saas-header-text">SYSTEM</span>
            <span class="saas-header-line"></span>
        </li>

        {{-- Cafe Settings --}}
        <li class="menu-item saas-menu-item {{ request()->routeIs('cafe-settings.*') ? 'active' : '' }}" data-title="Cafe Settings">
            <a href="{{ route('cafe-settings.index') }}" class="menu-link" title="Cafe Settings">
                <span class="saas-icon-badge icon-badge-slate">
                    <i class="bx bx-slider-alt"></i>
                </span>
                <span class="saas-link-text">Cafe Settings</span>
                <span class="saas-active-pill"></span>
            </a>
        </li>

        {{-- Cache Clear --}}
        <li class="menu-item saas-menu-item {{ request()->routeIs('clearcache') ? 'active' : '' }}" data-title="Cache Clear">
            <a href="{{ route('clearcache') }}" class="menu-link" title="Cache Clear">
                <span class="saas-icon-badge icon-badge-cyan">
                    <i class="bx bx-refresh"></i>
                </span>
                <span class="saas-link-text">Cache Clear</span>
                <span class="saas-active-pill"></span>
            </a>
        </li>

    </ul>

    <!-- SAAS SIDEBAR FOOTER (USER & SYSTEM CARD) -->
    <div class="saas-sidebar-footer">
        <div class="saas-user-card">
            <div class="saas-user-avatar-wrap">
                <div class="saas-user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'BZ', 0, 2)) }}
                </div>
                <span class="saas-avatar-dot"></span>
            </div>
            <div class="saas-user-details">
                <div class="saas-user-name" title="{{ Auth::user()->name ?? 'Admin' }}">
                    {{ Auth::user()->name ?? 'Admin' }}
                </div>
                <div class="saas-user-subtitle">
                    {{ Auth::user()->roles->first()->name ?? 'Store Admin' }}
                </div>
            </div>
            <div class="saas-footer-actions">
                <a href="{{ route('cafe-settings.index') }}" class="saas-footer-icon-btn" title="Cafe Settings">
                    <i class="bx bx-slider-alt"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 d-inline">
                    @csrf
                    <button type="submit" class="saas-footer-icon-btn text-danger" title="Logout" style="border: none; background: transparent; cursor: pointer;">
                        <i class="bx bx-log-out"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="saas-status-bar">
            <span class="saas-status-indicator"></span>
            <span>BreakZone POS v2.4 • Online</span>
        </div>
    </div>

</aside>
