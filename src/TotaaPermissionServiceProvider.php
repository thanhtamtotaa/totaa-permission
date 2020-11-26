<?php

namespace Totaa\TotaaPermission;

use Illuminate\Support\ServiceProvider;

class TotaaPermissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'totaa-permission');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'totaa-permission');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/permission.php' => config_path('permission.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/2020_11_17_091443_create_permission_tables.php' => database_path('migrations/2020_11_17_091443_create_permission_tables.php'),
            ], 'migrations');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/totaa-permission'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/totaa-permission'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/totaa-permission'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);


            /*
            |--------------------------------------------------------------------------
            | Seed Service Provider need on boot() method
            |--------------------------------------------------------------------------
            */
            $this->app->register(SeedServiceProvider::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'totaa-permission');

        // Register the main class to use with the facade
        $this->app->singleton('totaa-permission', function () {
            return new TotaaPermission;
        });
    }
}
