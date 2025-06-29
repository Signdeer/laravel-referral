<?php

namespace Jijunair\LaravelReferral\Providers;

use Illuminate\Support\ServiceProvider;

class ReferralServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     *
     * @return void
     */
    public function register()
    {
        // Merge the package's configuration file with the application's configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/referral.php', 'referral');
    }

    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot()
    {
        // $viewPath = __DIR__ . '/../../../resources/views';
        $viewPath = base_path('vendor/signdeer/laravel-referral/resources/views');

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // ✅ Load views from inside the package
        $this->loadViewsFrom($viewPath, 'laravel-referral');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../../config/referral.php' => config_path('referral.php'),
            ], 'laravel-referral-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'laravel-referral-migrations');

            // Publish views
            $this->publishes([
                $viewPath => resource_path('views/vendor/laravel-referral'),
            ], 'referral-views');
        }

        // Load package routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
