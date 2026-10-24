<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="d-flex align-items-center justify-content-between w-100">

        <!-- Left: Mobile Toggle & Page Title -->
        <div class="d-flex align-items-center gap-3">
            <a class="nav-item nav-link px-0 d-xl-none" href="javascript:void(0)" id="saas-mobile-toggle" title="Toggle Sidebar">
                <i class="bx bx-menu fs-3 text-dark"></i>
            </a>
            <div class="d-flex flex-column justify-content-center">
                <span class="text-muted fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">BreakZone Cafe</span>
                <h4 class="mb-0 fw-bold text-dark mt-1 text-nowrap" style="font-size: 16px; letter-spacing: -0.3px; line-height: 1.2;">@yield('title', 'Dashboard')</h4>
            </div>
        </div>

        <!-- Center & Right Controls: Compact Search + POS Button + User Profile -->
        <div class="d-flex align-items-center gap-3 ms-auto">
            
            <!-- Compact Quick Search Bar -->
            <div class="position-relative d-none d-md-block" style="width: 200px;">
                <div class="bz-nav-search-wrap">
                    <i class="bx bx-search bz-nav-search-icon"></i>
                    <input type="text" class="bz-nav-search-input" placeholder="Search..." id="bzGlobalSearch" autocomplete="off">
                    <kbd class="bz-nav-kbd">Ctrl+K</kbd>
                </div>
                <!-- Quick Module Search Dropdown -->
                <div class="dropdown-menu shadow-lg border-0 mt-1 w-100" id="bzSearchDropdown" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1090; border-radius: 10px; max-height: 280px; overflow-y: auto; padding: 6px;">
                </div>
            </div>

            <!-- Launch POS Terminal Button (BreakZone Orange) -->
            <a href="#" class="bz-pos-btn text-nowrap" title="Launch Point of Sale">
                <i class="bx bx-bolt-circle fs-5"></i>
                <span class="d-none d-sm-inline">POS Terminal</span>
            </a>

            <!-- User Profile Avatar Pill Dropdown -->
            <div class="dropdown">
                <a class="bz-profile-pill hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bz-profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'BZ', 0, 2)) }}
                    </div>
                    <div class="d-none d-sm-flex flex-column text-start me-1">
                        <span class="fw-bold text-dark" style="font-size: 13px; line-height: 1.2;">{{ Auth::user()->name }}</span>
                        <span class="text-muted" style="font-size: 11px; font-weight: 500; line-height: 1.2;">{{ Auth::user()->roles->first()->name ?? 'Administrator' }}</span>
                    </div>
                    <i class="bx bx-chevron-down text-muted fs-6 d-none d-sm-inline-block"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 12px; min-width: 210px; padding: 8px;">
                    <li>
                        <div class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name }}</div>
                            <small class="text-muted" style="font-size: 11px;">{{ Auth::user()->email }}</small>
                            <div class="mt-1">
                                <span class="badge bg-label-primary rounded-pill px-2 py-0.5 fw-bold" style="font-size: 10px;">
                                    {{ Auth::user()->roles->first()->name ?? 'Administrator' }}
                                </span>
                            </div>
                        </div>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded" href="{{ route('cafe-settings.index') }}">
                            <i class="bx bx-slider-alt text-primary"></i> Cafe Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded" href="{{ route('clearcache') }}">
                            <i class="bx bx-refresh text-warning"></i> Clear System Cache
                        </a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded text-danger" style="border: none; background: transparent; width: 100%;">
                                <i class="bx bx-log-out"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</nav>