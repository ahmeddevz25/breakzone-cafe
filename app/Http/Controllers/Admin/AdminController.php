<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Visitor;
use App\Models\Phase;
use App\Models\Block;
use App\Models\Plot;
use App\Models\PlotBooking;
use App\Models\Customer;
use App\Models\PlotAdvancePayment;
use App\Models\PlotInstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{

    public function index()
    {
        $now = now();

        // Visitor stats
        $todayVisitors = Visitor::whereDate('visit_date', $now->toDateString())->count();
        $allVisitors = Visitor::count();

        return view('admin.index', compact(
            'todayVisitors',
            'allVisitors',
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
