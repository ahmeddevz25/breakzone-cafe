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
                                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                                    <h5 class="fw-bold text-dark mb-0 fs-4">Daily Sales Analytics</h5>
                                </div>
                                <div class="card-body px-4 pb-4">
                                    <!-- Increased height significantly for better readability -->
                                    <div style="height: 400px; width: 100%;">
                                        <canvas id="dailySalesChart"></canvas>
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
                                    ['name' => 'STORES', 'icon' => 'bx-store-alt', 'color' => 'primary'],
                                    ['name' => 'SUPPLIERS', 'icon' => 'bx-group', 'color' => 'success'],
                                    ['name' => 'BRANDS', 'icon' => 'bx-medal', 'color' => 'info'],
                                    ['name' => 'CATEGORIES', 'icon' => 'bx-category', 'color' => 'warning'],
                                    ['name' => 'UNITS', 'icon' => 'bx-ruler', 'color' => 'danger'],
                                    ['name' => 'ITEMS', 'icon' => 'bx-box', 'color' => 'secondary'],
                                    ['name' => 'INGREDIENTS', 'icon' => 'bx-bowl-rice', 'color' => 'primary'],
                                    ['name' => 'FOODS', 'icon' => 'bx-restaurant', 'color' => 'success'],
                                    ['name' => 'COMBO PRODUCTS', 'icon' => 'bx-git-merge', 'color' => 'info'],
                                    ['name' => 'PURCHASES', 'icon' => 'bx-cart-alt', 'color' => 'warning'],
                                    ['name' => 'PURCHASE RETURNS', 'icon' => 'bx-cart-download', 'color' => 'danger'],
                                    ['name' => 'CARDS', 'icon' => 'bx-credit-card', 'color' => 'primary'],
                                    ['name' => 'SALES', 'icon' => 'bx-cart', 'color' => 'success'],
                                    ['name' => 'SALE RETURNS', 'icon' => 'bx-receipt', 'color' => 'info'],
                                    ['name' => 'DISPOSALS', 'icon' => 'bx-trash', 'color' => 'warning'],
                                    ['name' => 'TRANSFERS', 'icon' => 'bx-transfer', 'color' => 'danger'],
                                    ['name' => 'CASH CATEGORIES', 'icon' => 'bx-wallet', 'color' => 'secondary'],
                                    ['name' => 'RECEIPTS', 'icon' => 'bx-file', 'color' => 'primary'],
                                    ['name' => 'EXPENSES', 'icon' => 'bx-money-withdraw', 'color' => 'success'],
                                    ['name' => 'APP NOTIFICATIONS', 'icon' => 'bx-bell', 'color' => 'info'],
                                    ['name' => 'REPORTS', 'icon' => 'bx-spreadsheet', 'color' => 'warning']
                                ];
                            @endphp

                            @foreach($cafeLinks as $link)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.classList.add('shadow');" onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow');">
                                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center gap-3">
                                        <div class="avatar bg-label-{{ $link['color'] }} rounded p-2" style="width: 50px; height: 50px;">
                                            <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                        </div>
                                        <span class="fw-bold text-dark text-uppercase" style="font-size: 13px;">{{ $link['name'] }}</span>
                                    </div>
                                </div>
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
                                    ['name' => 'USERS TYPES', 'icon' => 'bx-shield-quarter', 'color' => 'danger'],
                                    ['name' => 'USERS', 'icon' => 'bx-user-circle', 'color' => 'primary'],
                                    ['name' => 'USERS LOG', 'icon' => 'bx-list-ul', 'color' => 'success']
                                ];
                            @endphp

                            @foreach($mgmtLinks as $link)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.classList.add('shadow');" onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow');">
                                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center gap-3">
                                        <div class="avatar bg-label-{{ $link['color'] }} rounded p-2" style="width: 50px; height: 50px;">
                                            <i class="bx {{ $link['icon'] }} bx-sm"></i>
                                        </div>
                                        <span class="fw-bold text-dark text-uppercase" style="font-size: 13px;">{{ $link['name'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
            
            // Vibrant Solid Colors matching Bootstrap palette
            const colorPrimary = '#696cff';
            const colorSuccess = '#71dd37';
            const colorWarning = '#ffab00';
            const colorDanger = '#ff3e1d';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['30-Aug', '31-Aug', '01-Sep', '02-Sep', '03-Sep', '04-Sep', '05-Sep'],
                    datasets: [
                        {
                            label: 'Total',
                            data: [0, 1100, 1050, 1000, 1070, 700, 20],
                            backgroundColor: colorPrimary,
                            borderRadius: 6,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Adnan Aslam',
                            data: [0, 300, 250, 250, 300, 190, 0],
                            backgroundColor: colorSuccess,
                            borderRadius: 6,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Amin Khan',
                            data: [0, 520, 520, 480, 500, 320, 20],
                            backgroundColor: colorWarning,
                            borderRadius: 6,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Sami Khan',
                            data: [0, 250, 250, 250, 250, 170, 0],
                            backgroundColor: colorDanger,
                            borderRadius: 6,
                            barPercentage: 0.6
                        }
                    ]
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
                                color: '#566a7f' // text-body color in Sneat
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(34, 48, 62, 0.95)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 15,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 13, weight: 'bold' },
                                color: '#566a7f'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 1200,
                            border: { display: false },
                            grid: {
                                color: '#eceef1',
                                drawTicks: false,
                            },
                            ticks: { 
                                font: { size: 13, weight: 'bold' },
                                padding: 10,
                                color: '#566a7f'
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
