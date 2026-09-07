<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services and define authorization Gates.
     */
    public function boot(): void
    {
        // Role Gates
        Gate::define('admin', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('customer', function (User $user) {
            return (int) $user->role === 2;
        });

        // Feature Specific Gates
        Gate::define('view-audit-logs', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-settings', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-holidays', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-promotions', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-services', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-customers', function (User $user) {
            return (int) $user->role === 1;
        });

        Gate::define('manage-sub-admins', function (User $user) {
            return (int) $user->role === 1;
        });
    }
}
