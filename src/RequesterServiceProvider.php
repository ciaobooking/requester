<?php

namespace Requester;

use Illuminate\Support\ServiceProvider;

/**
 * Class RequesterServiceProvider
 * @package Requester
 */
class RequesterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfigs();
    }

    /**
     *
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     *
     */
    public function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '../config/requester.php' => config_path('requester.php'),
        ]);
    }
}
