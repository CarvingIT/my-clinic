<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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
        DB::statement("SET time_zone = '+05:30'");

        // Blade::if('admin', function () {
        //     return Auth::check() && Auth::user()->role === 'admin'; // Or is_admin === true
        // });

        Request::macro('hasAdminMiddleware', function () {
        return request()->attributes->get('_admin_middleware') === true;
    });

    // Custom Blade directive
    Blade::if('adminmiddleware', function () {
        return request()->hasAdminMiddleware();
    });
    }
}
