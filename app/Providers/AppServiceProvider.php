<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        try {
            $setting = Cache::rememberForever('app_settings', function () {
                return Setting::latest()->first();
            });

            if ($setting && ! empty($setting->timezone)) {
                config(['app.timezone' => $setting->timezone]);
                date_default_timezone_set($setting->timezone);
            }
        } catch (\Throwable) {
            $setting = null;
        }

        View::composer('components.sidebar', function ($view) use (&$setting) {
            $view->with('sidebarSettings', $setting);
        });
    }
}
