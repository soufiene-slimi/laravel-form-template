<?php

namespace SoufieneSlimi\LaravelFormTemplate;

use Illuminate\Support\ServiceProvider;
use SoufieneSlimi\LaravelFormTemplate\Models\Template;

class LaravelFormTemplateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-form-template');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-form-template.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-form-template'),
            ], 'views');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-form-template');

        // Register the main class to use with the model
        $this->app->singleton('laravel-form-template', function () {
            return new Template;
        });

        // register their aliases
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Template', \SoufieneSlimi\LaravelFormTemplate\Models\Template::class);
    }
}
