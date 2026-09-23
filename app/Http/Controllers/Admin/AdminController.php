<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use App\Models\Ingredient;
use App\Models\Food;
use App\Models\Purchase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // Dynamic module counts
        $counts = [
            'stores' => Store::count(),
            'suppliers' => Supplier::count(),
            'brands' => Brand::count(),
            'categories' => Category::count(),
            'units' => Unit::count(),
            'items' => Item::count(),
            'ingredients' => Ingredient::count(),
            'foods' => Food::count(),
            'purchases' => Purchase::count(),
            'roles' => Role::count(),
            'users' => User::count(),
        ];

        // Purchase metrics
        $totalPurchasesAmount = Purchase::sum('total') ?? 0;
        $todayPurchasesCount = Purchase::whereDate('purchase_date', $today)->count();
        $todayPurchasesAmount = Purchase::whereDate('purchase_date', $today)->sum('total') ?? 0;
        $monthlyPurchasesCount = Purchase::whereMonth('purchase_date', $now->month)
            ->whereYear('purchase_date', $now->year)
            ->count();
        $monthlyPurchasesAmount = Purchase::whereMonth('purchase_date', $now->month)
            ->whereYear('purchase_date', $now->year)
            ->sum('total') ?? 0;

        // Recent 5 Purchases for quick activity
        $recentPurchases = Purchase::with(['store', 'supplier'])
            ->latest('purchase_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Sales Associates & Daily Sales Analytics
        $salesAssociatesUsers = User::role('Sales Associate')->get();
        if ($salesAssociatesUsers->isEmpty()) {
            $salesAssociatesUsers = User::take(3)->get();
        }

        $palette = ['#71dd37', '#ffab00', '#ff3e1d', '#00cfe8', '#8592a3', '#e83e8c', '#fd7e14', '#20c997'];

        $salesChartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $salesChartLabels[] = Carbon::now()->subDays($i)->format('d-M');
        }

        $sampleTemplates = [
            [120, 300, 250, 250, 300, 190, 80],
            [200, 520, 520, 480, 500, 320, 150],
            [150, 250, 250, 250, 250, 170, 90],
            [180, 280, 260, 310, 290, 210, 110],
            [140, 240, 220, 270, 250, 180, 95],
        ];

        $salesAssociatesData = [];
        $totalDaily = array_fill(0, count($salesChartLabels), 0);

        foreach ($salesAssociatesUsers as $index => $user) {
            $color = $palette[$index % count($palette)];
            $daily = $sampleTemplates[$index % count($sampleTemplates)];

            foreach ($daily as $dIdx => $val) {
                $totalDaily[$dIdx] += $val;
            }

            $salesAssociatesData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'color' => $color,
                'daily' => $daily,
                'total_sales' => array_sum($daily),
            ];
        }

        $salesChartDatasets = [
            [
                'label' => 'Total',
                'data' => $totalDaily,
                'backgroundColor' => '#696cff',
                'borderRadius' => 6,
                'barPercentage' => 0.6,
            ]
        ];

        foreach ($salesAssociatesData as $assoc) {
            $salesChartDatasets[] = [
                'label' => $assoc['name'],
                'data' => $assoc['daily'],
                'backgroundColor' => $assoc['color'],
                'borderRadius' => 6,
                'barPercentage' => 0.6,
            ];
        }

        return view('admin.index', compact(
            'counts',
            'totalPurchasesAmount',
            'todayPurchasesCount',
            'todayPurchasesAmount',
            'monthlyPurchasesCount',
            'monthlyPurchasesAmount',
            'recentPurchases',
            'salesChartLabels',
            'salesChartDatasets',
            'salesAssociatesData'
        ));
    }

    public function LoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Welcome to Dashboard!');
            }

            return back()->with('error', 'Invalid email or password.');
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        return response()->json([
            ['label' => 'Test Search', 'type' => 'Debug', 'url' => '/'],
        ]);
    }
    public function clearcache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Cache Cleared successfully!');
    }
}
