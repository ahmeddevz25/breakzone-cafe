<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CafeSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // Share cafe settings with all views dynamically
        try {
            $cafeSetting = Cache::rememberForever('cafe_setting', function () {
                return CafeSetting::first();
            });
            View::share('cafeSetting', $cafeSetting);
        } catch (\Exception $e) {
            // If table doesn't exist yet (e.g. during migrations), don't break the app
            View::share('cafeSetting', null);
        }
    }
}
