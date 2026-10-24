<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminRedirect
{
    public function handle($request, Closure $next)
    {
        $isAuthUrl = $request->is('/') || $request->is('admin') || $request->is('admin/') || $request->is('login') || $request->is('admin/login');

        // Agar user already logged in hai, toh login / root / admin URLs say dashboard par bhej do
        if (Auth::check()) {
            if ($isAuthUrl) {
                return redirect()->route('dashboard');
            }
        } else {
            // Agar guest hai aur root, admin, ya login par aaye, toh admin/login par bhej do
            if ($request->is('/') || $request->is('admin') || $request->is('admin/') || $request->is('login')) {
                return redirect()->route('login');
            }
        }

        // Baaqi routes ke liye aage badh jao (next middleware ya controller)
        return $next($request);
    }
}
