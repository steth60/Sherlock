<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        //
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
    
        Blade::if('department', function ($department) {
            return auth()->check() && auth()->user()->hasDepartment($department);
        });
    }
}
