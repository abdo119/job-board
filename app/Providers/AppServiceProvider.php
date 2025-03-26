<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Proper database service registration
        $this->app->singleton('db', function ($app) {
            return new \Illuminate\Database\DatabaseManager($app, $app['db.factory']);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
