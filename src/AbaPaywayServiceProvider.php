<?php

namespace SourngSeng\AbaPayway;

use Illuminate\Support\ServiceProvider;
use SourngSeng\AbaPayway\Services\AbaPaywayService;

class AbaPaywayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/aba-payway.php', 'aba-payway'
        );

        $this->app->singleton('aba-payway', function ($app) {
            return new AbaPaywayService(
                config('aba-payway.merchant_id'),
                config('aba-payway.merchant_secret'),
                config('aba-payway.api_url'),
                config('aba-payway.sandbox')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/aba-payway.php' => config_path('aba-payway.php'),
        ], 'aba-payway-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'aba-payway-migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'aba-payway');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/aba-payway'),
        ], 'aba-payway-views');
    }
}