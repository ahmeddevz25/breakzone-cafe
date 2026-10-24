@extends('admin.layouts')
@section('title', 'Cafe Dashboard')
@section('content')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="container-fluid flex-grow-1 px-4 py-4">

                    <!-- =================================================================
                         1. HERO ACTION BANNER (BreakZone Charcoal & Orange Brand Theme)
                         ================================================================= -->
                    @php
                        $hour = (int) now()->format('H');
                        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                        $adminName = Auth::user()->name ?? 'Administrator';
                    @endphp

                    <div class="bz-hero-banner">
                        <div class="row align-items-center g-4">
                            <div class="col-12 col-lg-8 col-xl-9">
                                <div class="bz-hero-greeting">{{ $greeting }}, {{ $adminName }}</div>
                                <h1 class="bz-hero-headline">
                                    BreakZone Cafe Management & POS Overview
                                </h1>
                                <p class="bz-hero-subtext">
                                    Real-time tracking of multi-branch sales, topups, kitchen inventory, and procurement orders across all active outlets.
                                </p>
                                <div class="bz-hero-actions">
                                    <a href="#" class="bz-hero-btn-primary">
                                        <i class="bx bx-bolt-circle fs-5"></i>
                                        <span>Launch POS Terminal</span>
                                    </a>
                                    <a href="{{ route('ingredients.index') }}" class="bz-hero-btn-glass">
                                        <i class="bx bx-dish fs-5"></i>
                                        <span>Kitchen Ingredients</span>
                                    </a>
                                    <a href="{{ route('purchases.index') }}" class="bz-hero-btn-glass">
                                        <i class="bx bx-cart-alt fs-5"></i>
                                        <span>Manage Purchases</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 col-xl-3 d-flex justify-content-lg-end justify-content-start">
                                <div class="bz-hero-badge-wrap">
                                    <div class="bz-hero-star-bg"></div>
                                    <div class="bz-hero-badge-content">
                                        <div class="bz-hero-badge-num">{{ $stockHealthPercentage ?? 94 }}%</div>
                                        <div class="bz-hero-badge-label">Catalog & Stock Health</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================================
                         2. CORE STATS GRID (All 12 Original Data Points, Clean Modern Look)
                         ================================================================= -->
                    @php
                        $topupStats = [
                            ['num' => '679', 'sub' => 'Total Transactions: 2,875', 'label' => 'Total Topup Count', 'icon' => 'bx-user', 'color' => '#3b82f6'],
                            ['num' => 'Rs 5,253,000', 'sub' => 'Lifetime balance topup', 'label' => 'Total Topup Amount', 'icon' => 'bx-wallet', 'color' => '#10b981'],
                            ['num' => '0', 'sub' => 'Today Transactions', 'label' => 'Today Topup Count', 'icon' => 'bx-up-arrow-alt', 'color' => '#6366f1'],
                            ['num' => 'Rs 0', 'sub' => 'Processed today', 'label' => 'Today Topup Amount', 'icon' => 'bx-trending-up', 'color' => '#ea580c'],
                        ];

                        $todaySalesStats = [
                            ['num' => 'Rs 3,520', 'sub' => '23 Cash orders today', 'label' => 'Today Cash Sales', 'icon' => 'bx-money', 'color' => '#ef4444'],
                            ['num' => 'Rs 0', 'sub' => '0 Card orders today', 'label' => 'Today Card Sales', 'icon' => 'bx-credit-card', 'color' => '#64748b'],
                            ['num' => 'Rs 0', 'sub' => '0 Coupon vouchers redeemed', 'label' => 'Today Coupon Sales', 'icon' => 'bx-purchase-tag', 'color' => '#8b5cf6'],
                            ['num' => 'Rs 3,520', 'sub' => '23 Total orders today', 'label' => 'Today Total Sales', 'icon' => 'bx-cart', 'color' => '#ea580c'],
                        ];

                        $monthlySalesStats = [
                            ['num' => 'Rs 890,700', 'sub' => '3,713 Cash orders this month', 'label' => 'Monthly Cash Sales', 'icon' => 'bx-line-chart', 'color' => '#0ea5e9'],
                            ['num' => 'Rs 23,930', 'sub' => '114 Card orders this month', 'label' => 'Monthly Card Sales', 'icon' => 'bx-credit-card-front', 'color' => '#f59e0b'],
                            ['num' => 'Rs 15,400', 'sub' => '4 Coupon orders this month', 'label' => 'Monthly Coupon Sales', 'icon' => 'bx-coupon', 'color' => '#ec4899'],
                            ['num' => 'Rs 930,030', 'sub' => '3,831 Total orders this month', 'label' => 'Monthly Total Sales', 'icon' => 'bx-bar-chart-alt-2', 'color' => '#10b981'],
                        ];
                    @endphp

                    <!-- Topups Row -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Account Topups & Balances</h6>
                    </div>
                    <div class="row g-3 mb-4">
                        @foreach($topupStats as $stat)
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">{{ $stat['label'] }}</span>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(234, 88, 12, 0.08); color: #ea580c;">
                                            <i class="bx {{ $stat['icon'] }} fs-5"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">{{ $stat['num'] }}</div>
                                        <div class="text-muted" style="font-size: 11.5px; font-weight: 500;">{{ $stat['sub'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Today's Sales Row -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Today's Live Sales Performance</h6>
                    </div>
                    <div class="row g-3 mb-4">
                        @foreach($todaySalesStats as $stat)
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">{{ $stat['label'] }}</span>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(234, 88, 12, 0.08); color: #ea580c;">
                                            <i class="bx {{ $stat['icon'] }} fs-5"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">{{ $stat['num'] }}</div>
                                        <div class="text-muted" style="font-size: 11.5px; font-weight: 500;">{{ $stat['sub'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Monthly Sales Row -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Monthly Consolidated Sales</h6>
                    </div>
                    <div class="row g-3 mb-4">
                        @foreach($monthlySalesStats as $stat)
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">{{ $stat['label'] }}</span>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(234, 88, 12, 0.08); color: #ea580c;">
                                            <i class="bx {{ $stat['icon'] }} fs-5"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">{{ $stat['num'] }}</div>
                                        <div class="text-muted" style="font-size: 11.5px; font-weight: 500;">{{ $stat['sub'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- =================================================================
                         3. DAILY SALES ANALYTICS CHART (By Sales Associates)
                         ================================================================= -->
                    <div class="bz-section-card mb-4">
                        <div class="bz-section-header">
                            <div>
                                <h3 class="bz-section-title">Daily Sales Analytics</h3>
                                <p class="bz-section-desc">Performance breakdown across Sales Associates</p>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @foreach($salesAssociatesData as $associate)
                                    <div class="badge bg-light text-dark border px-2.5 py-1.5 d-flex align-items-center gap-2" style="border-radius: 8px;">
                                        <span class="rounded-circle d-inline-block" style="width: 9px; height: 9px; background-color: {{ $associate['color'] }};"></span>
                                        <span class="fw-bold text-dark" style="font-size: 12px;">{{ $associate['name'] }}</span>
                                        <span class="text-muted fw-bold" style="font-size: 11px;">(Rs {{ number_format($associate['total_sales']) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-4">
                            <div style="height: 340px; width: 100%;">
                                <canvas id="dailySalesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================================
                         4. PROCUREMENT & INVENTORY HIGHLIGHTS
                         ================================================================= -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                                <h5 class="fw-bold text-dark mb-0">Procurement & Inventory Highlights</h5>
                            </div>
                            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px; font-size: 12px;">
                                <i class="bx bx-cart me-1"></i> Manage Purchases
                            </a>
                        </div>

                        <div class="row g-3">
                            <!-- Total Purchases Spend -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">Total Purchases Spend</span>
                                        <i class="bx bx-cart-alt bz-kpi-icon"></i>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">Rs {{ number_format($totalPurchasesAmount, 2) }}</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11px;">
                                            {{ $counts['purchases'] ?? 0 }} Total {{ Str::plural('Order', $counts['purchases'] ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Purchases Spend -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">This Month Purchases</span>
                                        <i class="bx bx-calendar bz-kpi-icon"></i>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">Rs {{ number_format($monthlyPurchasesAmount, 2) }}</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11px;">
                                            {{ $monthlyPurchasesCount ?? 0 }} Orders ({{ now()->format('M Y') }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu & Food Catalog -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">Menu & Food Catalog</span>
                                        <i class="bx bx-restaurant bz-kpi-icon"></i>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">{{ ($counts['items'] ?? 0) + ($counts['foods'] ?? 0) }} Active</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11px;">
                                            {{ $counts['items'] ?? 0 }} Items &bull; {{ $counts['foods'] ?? 0 }} Foods
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stores & Suppliers Network -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="bz-kpi-card">
                                    <div class="bz-kpi-top">
                                        <span class="bz-kpi-label">Network Operations</span>
                                        <i class="bx bx-store-alt bz-kpi-icon"></i>
                                    </div>
                                    <div>
                                        <div class="bz-kpi-number">{{ $counts['stores'] ?? 0 }} Stores</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11px;">
                                            {{ $counts['suppliers'] ?? 0 }} Suppliers &bull; {{ $counts['ingredients'] ?? 0 }} Ingredients
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================================
                         5. QUICK OPERATIONS LINKS (All 21 Original Cafe Modules)
                         ================================================================= -->
                    <div class="bz-section-card p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                            <h5 class="fw-bold text-dark mb-0">Cafe Operations Modules</h5>
                        </div>

                        @php
                            $cafeLinks = [
                                ['name' => 'STORES', 'icon' => 'bx-store-alt', 'color' => '#ea580c', 'route' => route('stores.index'), 'count' => $counts['stores'] ?? 0],
                                ['name' => 'SUPPLIERS', 'icon' => 'bx-group', 'color' => '#10b981', 'route' => route('suppliers.index'), 'count' => $counts['suppliers'] ?? 0],
                                ['name' => 'BRANDS', 'icon' => 'bx-purchase-tag', 'color' => '#3b82f6', 'route' => route('brands.index'), 'count' => $counts['brands'] ?? 0],
                                ['name' => 'CATEGORIES', 'icon' => 'bx-category', 'color' => '#f59e0b', 'route' => route('categories.index'), 'count' => $counts['categories'] ?? 0],
                                ['name' => 'UNITS', 'icon' => 'bx-cube', 'color' => '#ec4899', 'route' => route('units.index'), 'count' => $counts['units'] ?? 0],
                                ['name' => 'ITEMS', 'icon' => 'bx-coffee', 'color' => '#8b5cf6', 'route' => route('items.index'), 'count' => $counts['items'] ?? 0],
                                ['name' => 'INGREDIENTS', 'icon' => 'bx-dish', 'color' => '#ea580c', 'route' => route('ingredients.index'), 'count' => $counts['ingredients'] ?? 0],
                                ['name' => 'FOODS', 'icon' => 'bx-restaurant', 'color' => '#10b981', 'route' => route('foods.index'), 'count' => $counts['foods'] ?? 0],
                                ['name' => 'COMBO PRODUCTS', 'icon' => 'bx-git-merge', 'color' => '#3b82f6', 'route' => route('combo-products.index'), 'count' => 3],
                                ['name' => 'PURCHASES', 'icon' => 'bx-cart', 'color' => '#f59e0b', 'route' => route('purchases.index'), 'count' => $counts['purchases'] ?? 0],
                                ['name' => 'PURCHASE RETURNS', 'icon' => 'bx-cart-download', 'color' => '#ef4444', 'route' => null, 'count' => null],
                                ['name' => 'CARDS', 'icon' => 'bx-credit-card', 'color' => '#6366f1', 'route' => null, 'count' => null],
                                ['name' => 'SALES', 'icon' => 'bx-shopping-bag', 'color' => '#10b981', 'route' => null, 'count' => null],
                                ['name' => 'SALE RETURNS', 'icon' => 'bx-receipt', 'color' => '#0ea5e9', 'route' => null, 'count' => null],
                                ['name' => 'DISPOSALS', 'icon' => 'bx-trash', 'color' => '#f97316', 'route' => null, 'count' => null],
                                ['name' => 'TRANSFERS', 'icon' => 'bx-transfer', 'color' => '#ef4444', 'route' => null, 'count' => null],
                                ['name' => 'CASH CATEGORIES', 'icon' => 'bx-wallet', 'color' => '#64748b', 'route' => null, 'count' => null],
                                ['name' => 'RECEIPTS', 'icon' => 'bx-file', 'color' => '#3b82f6', 'route' => null, 'count' => null],
                                ['name' => 'EXPENSES', 'icon' => 'bx-money-withdraw', 'color' => '#10b981', 'route' => null, 'count' => null],
                                ['name' => 'APP NOTIFICATIONS', 'icon' => 'bx-bell', 'color' => '#f59e0b', 'route' => null, 'count' => null],
                                ['name' => 'REPORTS', 'icon' => 'bx-spreadsheet', 'color' => '#8b5cf6', 'route' => null, 'count' => null]
                            ];
                        @endphp

                        <div class="row g-2 g-md-3">
                            @foreach($cafeLinks as $link)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    @if($link['route'])
                                        <a href="{{ $link['route'] }}" class="text-decoration-none d-block h-100">
                                            <div class="p-3 rounded-3 text-center h-100 d-flex flex-column align-items-center justify-content-center" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#ea580c'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.04)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: rgba(234, 88, 12, 0.08); color: #ea580c;">
                                                    <i class="bx {{ $link['icon'] }} fs-4"></i>
                                                </div>
                                                <span class="fw-bold text-dark text-uppercase d-block text-truncate w-100" style="font-size: 12px;">{{ $link['name'] }}</span>
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 mt-1" style="font-size: 10px;">
                                                    {{ $link['count'] }} Records
                                                </span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-3 rounded-3 text-center h-100 d-flex flex-column align-items-center justify-content-center opacity-75" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: #e2e8f0; color: #64748b;">
                                                <i class="bx {{ $link['icon'] }} fs-4"></i>
                                            </div>
                                            <span class="fw-bold text-muted text-uppercase d-block text-truncate w-100" style="font-size: 12px;">{{ $link['name'] }}</span>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 mt-1" style="font-size: 10px;">
                                                Pending
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- =================================================================
                         6. SYSTEM MANAGEMENT LINKS (All 3 Original Management Modules)
                         ================================================================= -->
                    <div class="bz-section-card p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="rounded-pill d-inline-block" style="width: 4px; height: 18px; background-color: #ea580c;"></span>
                            <h5 class="fw-bold text-dark mb-0">System & User Management</h5>
                        </div>

                        @php
                            $mgmtLinks = [
                                ['name' => 'USERS TYPES', 'icon' => 'bx-shield-quarter', 'route' => route('roles'), 'count' => $counts['roles'] ?? 0],
                                ['name' => 'USERS', 'icon' => 'bx-user', 'route' => route('users.index'), 'count' => $counts['users'] ?? 0],
                                ['name' => 'USERS LOG', 'icon' => 'bx-list-ul', 'route' => null, 'count' => null]
                            ];
                        @endphp

                        <div class="row g-2 g-md-3">
                            @foreach($mgmtLinks as $link)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    @if($link['route'])
                                        <a href="{{ $link['route'] }}" class="text-decoration-none d-block h-100">
                                            <div class="p-3 rounded-3 text-center h-100 d-flex flex-column align-items-center justify-content-center" style="background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#ea580c'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)';">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: rgba(234, 88, 12, 0.08); color: #ea580c;">
                                                    <i class="bx {{ $link['icon'] }} fs-4"></i>
                                                </div>
                                                <span class="fw-bold text-dark text-uppercase d-block text-truncate w-100" style="font-size: 12px;">{{ $link['name'] }}</span>
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 mt-1" style="font-size: 10px;">
                                                    {{ $link['count'] }} Records
                                                </span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-3 rounded-3 text-center h-100 d-flex flex-column align-items-center justify-content-center opacity-75" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: #e2e8f0; color: #64748b;">
                                                <i class="bx {{ $link['icon'] }} fs-4"></i>
                                            </div>
                                            <span class="fw-bold text-muted text-uppercase d-block text-truncate w-100" style="font-size: 12px;">{{ $link['name'] }}</span>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 mt-1" style="font-size: 10px;">
                                                Pending
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- =================================================================
                         7. RECENT PURCHASES ACTIVITY TABLE
                         ================================================================= -->
                    <div class="bz-section-card mb-4">
                        <div class="bz-section-header">
                            <div>
                                <h3 class="bz-section-title">
                                    <i class="bx bx-cart text-primary me-2"></i>Recent Inward Purchases
                                </h3>
                                <p class="bz-section-desc">Latest procurement invoices recorded in the system</p>
                            </div>
                            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-primary fw-bold" style="border-radius: 8px;">
                                View All Purchases <i class="bx bx-right-arrow-alt ms-1"></i>
                            </a>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr style="border-bottom: 2px solid #f1f5f9;">
                                        <th class="fw-bold text-muted text-uppercase" style="font-size: 11px;">PO NO</th>
                                        <th class="fw-bold text-muted text-uppercase" style="font-size: 11px;">STORE</th>
                                        <th class="fw-bold text-muted text-uppercase" style="font-size: 11px;">SUPPLIER</th>
                                        <th class="fw-bold text-muted text-uppercase" style="font-size: 11px;">PURCHASE DATE</th>
                                        <th class="fw-bold text-muted text-uppercase text-end" style="font-size: 11px;">TOTAL AMOUNT</th>
                                        <th class="fw-bold text-muted text-uppercase text-center" style="font-size: 11px;">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPurchases as $purchase)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border fw-bold" style="font-size: 12px; border-radius: 6px;">
                                                    {{ $purchase->po_no }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark" style="font-size: 13.5px;">{{ $purchase->store->name ?? '-' }}</td>
                                            <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td class="text-muted" style="font-size: 12.5px;">{{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : '-' }}</td>
                                            <td class="text-end fw-bold" style="font-size: 14px; color: #ea580c !important;">
                                                Rs {{ number_format($purchase->total, 2) }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-icon btn-outline-primary" title="View Purchase">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                No purchases recorded yet. <a href="{{ route('purchases.index') }}" class="text-primary fw-bold">Create first purchase</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Layout Overlay for Mobile Drawer -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Chart.js for Daily Sales Analytics Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('dailySalesChart').getContext('2d');
            
            const chartLabels = {!! json_encode($salesChartLabels ?? []) !!};
            const chartDatasets = {!! json_encode($salesChartDatasets ?? []) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: chartDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                font: { size: 13, weight: 'bold', family: 'Plus Jakarta Sans' },
                                padding: 20,
                                color: '#0f172a'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleFont: { size: 13, weight: 'bold', family: 'Plus Jakarta Sans' },
                            bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': Rs ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 12, weight: '600', family: 'Plus Jakarta Sans' },
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: {
                                color: '#f1f5f9',
                                drawTicks: false,
                            },
                            ticks: { 
                                font: { size: 12, weight: '600', family: 'Plus Jakarta Sans' },
                                padding: 8,
                                color: '#64748b',
                                callback: function(value) {
                                    return 'Rs ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
@endsection
