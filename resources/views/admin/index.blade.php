@extends('admin.layouts')
@section('title', 'Admin Dashboard')
@section('content')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <div class="container-fluid flex-grow-1 container-p-y px-4 mt-4">
                    
                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-1">Cafe Dashboard <span class="text-muted fw-light">/ Overview</span></h4>
                        <p class="text-muted">A quick overview of today's sales and operations.</p>
                    </div>

                    <!-- Stats Grid -->
                    @php
                        $stats = [
                            ['num' => '679', 'label' => 'TOTAL TOPUP (2,875)', 'icon' => 'bx-user', 'color' => 'primary'],
                            ['num' => '5,253,000', 'label' => 'TOTAL TOPUP AMOUNT', 'icon' => 'bx-wallet', 'color' => 'success'],
                            ['num' => '0', 'label' => 'TODAY TOPUP', 'icon' => 'bx-up-arrow-alt', 'color' => 'info'],
                            ['num' => '0', 'label' => 'TODAY TOPUP AMOUNT', 'icon' => 'bx-trending-up', 'color' => 'warning'],
                            
                            ['num' => '23', 'label' => 'TODAY CASH SALES (RS 3,520)', 'icon' => 'bx-money', 'color' => 'danger'],
                            ['num' => '0', 'label' => 'TODAY CARD SALES', 'icon' => 'bx-credit-card', 'color' => 'secondary'],
                            ['num' => '0', 'label' => 'TODAY COUPON SALES', 'icon' => 'bx-purchase-tag', 'color' => 'primary'],
                            ['num' => '23', 'label' => 'TODAY SALES (RS 3,520)', 'icon' => 'bx-cart', 'color' => 'success'],
                            
                            ['num' => '3,713', 'label' => 'MONTHLY CASH (RS 890,700)', 'icon' => 'bx-line-chart', 'color' => 'info'],
                            ['num' => '114', 'label' => 'MONTHLY CARD (RS 23,930)', 'icon' => 'bx-credit-card-front', 'color' => 'warning'],
                            ['num' => '4', 'label' => 'MONTHLY COUPON (RS 15,400)', 'icon' => 'bx-coupon', 'color' => 'danger'],
                            ['num' => '3,831', 'label' => 'MONTHLY SALES (RS 930,030)', 'icon' => 'bx-bar-chart-alt-2', 'color' => 'primary'],
                        ];
                    @endphp

                    <div class="row g-4 mb-5">
                        @foreach($stats as $stat)
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s; cursor: default;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <div class="d-flex align-items-end mb-2">
                                                    <h3 class="mb-0 fw-bold text-dark">{{ $stat['num'] }}</h3>
                                                </div>
                                                <p class="mb-0 fw-semibold text-body" style="font-size: 13px;">{{ $stat['label'] }}</p>
                                            </div>
                                            <div class="avatar bg-label-{{ $stat['color'] }} rounded p-2" style="width: 48px; height: 48px;">
                                                <i class="bx {{ $stat['icon'] }} bx-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Chart Section -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                                <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1 fs-4">Daily Sales Analytics</h5>
                                        <p class="text-muted mb-0" style="font-size: 13px;">Daily performance breakdown by Sales Associates</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @foreach($salesAssociatesData as $associate)
                                            <div class="badge bg-light text-dark border px-3 py-2 d-flex align-items-center gap-2" style="border-radius: 8px;">
                                                <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: {{ $associate['color'] }};"></span>
                                                <span class="fw-bold text-dark" style="font-size: 12px;">{{ $associate['name'] }}</span>
                                                <span class="text-muted fw-bold" style="font-size: 11px;">(Rs {{ number_format($associate['total_sales']) }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card-body px-4 pb-4 pt-2">
                                    <!-- Increased height significantly for better readability -->
                                    <div style="height: 400px; width: 100%;">
                                        <canvas id="dailySalesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Procurement & Inventory Highlights -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success rounded" style="width: 5px; height: 24px;"></div>
                                <h4 class="fw-bold text-dark mb-0">Procurement & Inventory Highlights</h4>
                            </div>
                            <a href="{{ route('purchases.index') }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                                <i class="bx bx-cart me-1"></i> Manage Purchases
                            </a>
                        </div>

                        <div class="row g-4">
                            <!-- Total Purchases Spend -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-uppercase fw-bold text-dark" style="font-size: 12px; letter-spacing: 0.5px;">Total Purchases</span>
                                                <div class="d-flex align-items-end my-2">
                                                    <h4 class="mb-0 fw-bold text-dark">Rs {{ number_format($totalPurchasesAmount, 2) }}</h4>
                                                </div>
                                                <span class="badge bg-label-success rounded-pill px-2.5 py-1 fw-bold text-dark" style="font-size: 11px;">
                                                    {{ $counts['purchases'] ?? 0 }} Total {{ Str::plural('Order', $counts['purchases'] ?? 0) }}
                                                </span>
                                            </div>
                                            <div class="avatar bg-label-success rounded p-2" style="width: 48px; height: 48px;">
                                                <i class="bx bx-cart-alt bx-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Purchases Spend -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-uppercase fw-bold text-dark" style="font-size: 12px; letter-spacing: 0.5px;">This Month Purchases</span>
                                                <div class="d-flex align-items-end my-2">
                                                    <h4 class="mb-0 fw-bold text-dark">Rs {{ number_format($monthlyPurchasesAmount, 2) }}</h4>
                                                </div>
                                                <span class="badge bg-label-info rounded-pill px-2.5 py-1 fw-bold text-dark" style="font-size: 11px;">
                                                    {{ $monthlyPurchasesCount ?? 0 }} {{ Str::plural('Order', $monthlyPurchasesCount ?? 0) }} ({{ now()->format('M Y') }})
                                                </span>
                                            </div>
                                            <div class="avatar bg-label-info rounded p-2" style="width: 48px; height: 48px;">
                                                <i class="bx bx-calendar bx-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catalog Products & Stock -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-uppercase fw-bold text-dark" style="font-size: 12px; letter-spacing: 0.5px;">Menu & Food Catalog</span>
                                                <div class="d-flex align-items-end my-2">
                                                    <h4 class="mb-0 fw-bold text-dark">{{ ($counts['items'] ?? 0) + ($counts['foods'] ?? 0) }} Active</h4>
                                                </div>
                                                <span class="badge bg-label-warning rounded-pill px-2.5 py-1 fw-bold text-dark" style="font-size: 11px;">
                                                    {{ $counts['items'] ?? 0 }} Items &bull; {{ $counts['foods'] ?? 0 }} Foods
                                                </span>
                                            </div>
                                            <div class="avatar bg-label-warning rounded p-2" style="width: 48px; height: 48px;">
                                                <i class="bx bx-restaurant bx-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stores & Suppliers Network -->
                            <div class="col-12 col-sm-6 col-xl-3">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="content-left">
                                                <span class="text-uppercase fw-bold text-dark" style="font-size: 12px; letter-spacing: 0.5px;">Network Operations</span>
                                                <div class="d-flex align-items-end my-2">
                                                    <h4 class="mb-0 fw-bold text-dark">{{ $counts['stores'] ?? 0 }} Stores</h4>
                                                </div>
                                                <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fw-bold text-dark" style="font-size: 11px;">
                                                    {{ $counts['suppliers'] ?? 0 }} Suppliers &bull; {{ $counts['ingredients'] ?? 0 }} Ingredients
                                                </span>
                                            </div>
                                            <div class="avatar bg-label-primary rounded p-2" style="width: 48px; height: 48px;">
                                                <i class="bx bx-store-alt bx-sm"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links Section: Cafe -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4 gap-2">
                            <div class="bg-primary rounded" style="width: 5px; height: 24px;"></div>
                            <h4 class="fw-bold text-dark mb-0">Cafe Operations</h4>
                        </div>
                        
                        <div class="row g-3">
                            @php
                                $cafeLinks = [
                                    ['name' => 'STORES', 'icon' => 'bx-store-alt', 'color' => 'primary', 'route' => route('stores.index'), 'count' => $counts['stores'] ?? 0],
                                    ['name' => 'SUPPLIERS', 'icon' => 'bx-group', 'color' => 'success', 'route' => route('suppliers.index'), 'count' => $counts['suppliers'] ?? 0],
                                    ['name' => 'BRANDS', 'icon' => 'bx-purchase-tag', 'color' => 'info', 'route' => route('brands.index'), 'count' => $counts['brands'] ?? 0],
                                    ['name' => 'CATEGORIES', 'icon' => 'bx-category', 'color' => 'warning', 'route' => route('categories.index'), 'count' => $counts['categories'] ?? 0],
                                    ['name' => 'UNITS', 'icon' => 'bx-cube', 'color' => 'danger', 'route' => route('units.index'), 'count' => $counts['units'] ?? 0],
                                    ['name' => 'ITEMS', 'icon' => 'bx-coffee', 'color' => 'secondary', 'route' => route('items.index'), 'count' => $counts['items'] ?? 0],
                                    ['name' => 'INGREDIENTS', 'icon' => 'bx-dish', 'color' => 'primary', 'route' => route('ingredients.index'), 'count' => $counts['ingredients'] ?? 0],
                                    ['name' => 'FOODS', 'icon' => 'bx-restaurant', 'color' => 'success', 'route' => route('foods.index'), 'count' => $counts['foods'] ?? 0],
                                    ['name' => 'COMBO PRODUCTS', 'icon' => 'bx-git-merge', 'color' => 'info', 'route' => null, 'count' => null],
                                    ['name' => 'PURCHASES', 'icon' => 'bx-cart', 'color' => 'warning', 'route' => route('purchases.index'), 'count' => $counts['purchases'] ?? 0],
                                    ['name' => 'PURCHASE RETURNS', 'icon' => 'bx-cart-download', 'color' => 'danger', 'route' => null, 'count' => null],
                                    ['name' => 'CARDS', 'icon' => 'bx-credit-card', 'color' => 'primary', 'route' => null, 'count' => null],
                                    ['name' => 'SALES', 'icon' => 'bx-shopping-bag', 'color' => 'success', 'route' => null, 'count' => null],
                                    ['name' => 'SALE RETURNS', 'icon' => 'bx-receipt', 'color' => 'info', 'route' => null, 'count' => null],
                                    ['name' => 'DISPOSALS', 'icon' => 'bx-trash', 'color' => 'warning', 'route' => null, 'count' => null],
                                    ['name' => 'TRANSFERS', 'icon' => 'bx-transfer', 'color' => 'danger', 'route' => null, 'count' => null],
                                    ['name' => 'CASH CATEGORIES', 'icon' => 'bx-wallet', 'color' => 'secondary', 'route' => null, 'count' => null],
                                    ['name' => 'RECEIPTS', 'icon' => 'bx-file', 'color' => 'primary', 'route' => null, 'count' => null],
                                    ['name' => 'EXPENSES', 'icon' => 'bx-money-withdraw', 'color' => 'success', 'route' => null, 'count' => null],
                                    ['name' => 'APP NOTIFICATIONS', 'icon' => 'bx-bell', 'color' => 'info', 'route' => null, 'count' => null],
                                    ['name' => 'REPORTS', 'icon' => 'bx-spreadsheet', 'color' => 'warning', 'route' => null, 'count' => null]
                                ];
                            @endphp

                            @foreach($cafeLinks as $link)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                @if($link['route'])
                                    <a href="{{ $link['route'] }}" target="_blank" class="text-decoration-none d-block h-100">
                                        <div class="card h-100 shadow-sm border-0 position-relative" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.classList.add('shadow');" onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow');">
                                            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                <div class="avatar bg-label-{{ $link['color'] }} rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                                </div>
                                                <span class="fw-bold text-dark text-uppercase" style="font-size: 13px; line-height: 1.2;">{{ $link['name'] }}</span>
                                                <span class="badge bg-label-{{ $link['color'] }} rounded-pill px-2.5 py-1 text-dark fw-bold" style="font-size: 11px;">
                                                    {{ $link['count'] }} {{ Str::plural('Record', $link['count']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="card h-100 shadow-sm border-0 opacity-75" style="border-radius: 12px; cursor: default;">
                                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                            <div class="avatar bg-label-{{ $link['color'] }} rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                            </div>
                                            <span class="fw-bold text-dark text-uppercase" style="font-size: 13px; line-height: 1.2;">{{ $link['name'] }}</span>
                                            <span class="badge bg-label-secondary rounded-pill px-2 py-0.5 text-muted" style="font-size: 10px;">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Links Section: Management -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4 gap-2">
                            <div class="bg-danger rounded" style="width: 5px; height: 24px;"></div>
                            <h4 class="fw-bold text-dark mb-0">System Management</h4>
                        </div>
                        <div class="row g-3">
                            @php
                                $mgmtLinks = [
                                    ['name' => 'USERS TYPES', 'icon' => 'bx-shield-quarter', 'color' => 'danger', 'route' => route('roles'), 'count' => $counts['roles'] ?? 0],
                                    ['name' => 'USERS', 'icon' => 'bx-user', 'color' => 'primary', 'route' => route('users.index'), 'count' => $counts['users'] ?? 0],
                                    ['name' => 'USERS LOG', 'icon' => 'bx-list-ul', 'color' => 'success', 'route' => null, 'count' => null]
                                ];
                            @endphp

                            @foreach($mgmtLinks as $link)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                @if($link['route'])
                                    <a href="{{ $link['route'] }}" target="_blank" class="text-decoration-none d-block h-100">
                                        <div class="card h-100 shadow-sm border-0 position-relative" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.classList.add('shadow');" onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow');">
                                            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                                <div class="avatar bg-label-{{ $link['color'] }} rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                                </div>
                                                <span class="fw-bold text-dark text-uppercase" style="font-size: 13px; line-height: 1.2;">{{ $link['name'] }}</span>
                                                <span class="badge bg-label-{{ $link['color'] }} rounded-pill px-2.5 py-1 text-dark fw-bold" style="font-size: 11px;">
                                                    {{ $link['count'] }} {{ Str::plural('Record', $link['count']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="card h-100 shadow-sm border-0 opacity-75" style="border-radius: 12px; cursor: default;">
                                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center gap-2">
                                            <div class="avatar bg-label-{{ $link['color'] }} rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                            </div>
                                            <span class="fw-bold text-dark text-uppercase" style="font-size: 13px; line-height: 1.2;">{{ $link['name'] }}</span>
                                            <span class="badge bg-label-secondary rounded-pill px-2 py-0.5 text-muted" style="font-size: 10px;">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Recent Purchases Activity -->
                    <div class="mb-5">
                        <div class="card shadow-sm border-0" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">
                                        <i class="bx bx-cart text-primary me-2"></i>Recent Purchases
                                    </h5>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Latest procurement orders recorded in the system</p>
                                </div>
                                <a href="{{ route('purchases.index') }}" target="_blank" class="btn btn-sm btn-primary">
                                    View All Purchases <i class="bx bx-right-arrow-alt ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body px-4 pb-4 pt-2">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="text-dark">
                                                <th class="fw-bold text-dark">PO NO</th>
                                                <th class="fw-bold text-dark">STORE</th>
                                                <th class="fw-bold text-dark">SUPPLIER</th>
                                                <th class="fw-bold text-dark">PURCHASE DATE</th>
                                                <th class="fw-bold text-dark text-end">TOTAL AMOUNT</th>
                                                <th class="fw-bold text-dark text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentPurchases as $purchase)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-label-primary fw-bold text-dark">
                                                            {{ $purchase->po_no }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold text-dark">{{ $purchase->store->name ?? '-' }}</td>
                                                    <td class="fw-bold text-dark">{{ $purchase->supplier->name ?? '-' }}</td>
                                                    <td class="fw-semibold text-dark">{{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : '-' }}</td>
                                                    <td class="text-end fw-bold text-dark" style="color: #006037 !important;">
                                                        Rs {{ number_format($purchase->total, 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('purchases.index') }}" target="_blank" class="btn btn-sm btn-icon btn-outline-primary" title="View Purchase">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        No purchases recorded yet. <a href="{{ route('purchases.index') }}" target="_blank" class="text-primary fw-bold">Create first purchase</a>
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
        </div>
    </div>
    
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('dailySalesChart').getContext('2d');
            
            const chartLabels = @json($salesChartLabels);
            const chartDatasets = @json($salesChartDatasets);

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
                                font: { size: 14, weight: 'bold' },
                                padding: 25,
                                color: '#2b343b' // crisp dark text
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(34, 48, 62, 0.95)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 15,
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
                                font: { size: 13, weight: 'bold' },
                                color: '#2b343b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: {
                                color: '#eceef1',
                                drawTicks: false,
                            },
                            ticks: { 
                                font: { size: 13, weight: 'bold' },
                                padding: 10,
                                color: '#2b343b',
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
