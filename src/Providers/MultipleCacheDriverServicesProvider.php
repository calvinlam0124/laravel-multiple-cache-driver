<?php

namespace CalvinLam\LaravelMultipleCacheDriver\Providers;

use CalvinLam\LaravelMultipleCacheDriver\Extensions\MultipleStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;


class MultipleCacheDriverServicesProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::extend('multiple-driver', function ($app) {
            return Cache::repository(new MultipleStore($app));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

