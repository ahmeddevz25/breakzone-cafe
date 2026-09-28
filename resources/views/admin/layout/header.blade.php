<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <style>
        #layout-navbar {
            width: calc(100% - 2rem) !important;
            margin-left: 1rem !important;
            margin-right: 1rem !important;
            margin-top: 0.85rem !important;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        @media (min-width: 1200px) {
            #layout-navbar {
                width: calc(100% - 260px - 2.5rem) !important;
                margin-left: calc(260px + 1.25rem) !important;
                margin-right: 1.25rem !important;
            }
            html.layout-menu-collapsed #layout-navbar {
                width: calc(100% - 76px - 2.5rem) !important;
                margin-left: calc(76px + 1.25rem) !important;
                margin-right: 1.25rem !important;
            }
        }
    </style>
    <div class="layout-menu-toggle navbar-nav align-items-center me-3 me-xl-0 d-flex" id="saas-mobile-toggle" style="cursor: pointer;" title="Toggle Sidebar">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <div class="d-flex align-items-center gap-3">
                <!-- User Name -->
                <span class="text-dark me-2" style="font-size: 14px;">{{ Auth::user()->name }}</span>
                
                <!-- POS Button -->
                <a href="#" class="btn btn-outline-danger fw-normal px-3 py-1" style="border-radius: 4px;">POS</a>
                
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-dark d-flex align-items-center gap-2 px-3 py-1" style="border-radius: 4px; background-color: #333333; border-color: #333333;">
                        <i class="bx bx-log-out bx-xs"></i> Logout
                    </button>
                </form>
            </div>
        </ul>

    </div>
</nav>