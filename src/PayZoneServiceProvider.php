<?php

namespace Svodya\PayZone;

use Illuminate\Support\ServiceProvider;

class PayZoneServiceProvider extends ServiceProvider
{
    protected $defer = false;
    public function boot(){
        $this->loadRoutesFrom(__DIR__ . '/route/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'payzone');

        $this->publishes([
            __DIR__.'/config/payzone.php' => config_path('payzone.php'),
        ]);

        $this->publishes([
            __DIR__.'/assets' => public_path('assets'),
        ], 'public');

    }

    public function register(){}

}
