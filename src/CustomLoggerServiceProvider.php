<?php

namespace AnhNQ\CustomLogger;

use AnhNQ\CustomLogger\Logging\CustomLogHandler;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class CustomLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish file config when running `php artisan vendor: publish`
        $this->publishes([
            __DIR__ . '/../config/custom-logger.php' => config_path('custom-logger.php'),
        ], 'logger-config');
        
        $this->publishes([
            __DIR__ . '/Logging/LoggerCustom.php' => app_path('Logging/LoggerCustom.php'),
        ], 'logger-format');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/custom-logger.php', 'custom-logger');

        // Logger registration into container
        $this->app->extend('log', function ($log, $app) {
            $request = $app->make(Request::class);
            $customHandler = new CustomLogHandler($request);
            $log->pushHandler($customHandler);
            return $log;
        });
    }
}