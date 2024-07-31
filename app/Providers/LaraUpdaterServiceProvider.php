<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LaraUpdaterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('laraupdater', function ($app) {
            return new \App\Helpers\LaraUpdater();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laraupdater.php' => config_path('laraupdater.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/../resources/views/laraupdater', 'laraupdater');
    }
}
