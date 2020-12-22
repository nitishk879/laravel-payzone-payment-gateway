<?php

namespace Svodya\PayZone;

use Illuminate\Support\ServiceProvider;

class PayZoneServiceProvider extends ServiceProvider
{
    protected $defer = false;
    public function boot(){
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'payzone');

        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateConfigurationsTable') || (!class_exists('CreateTransactionsTable'))) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_configurations_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_configurations_table.php'),
                    __DIR__ . '/../database/migrations/create_transactions_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_transactions_table.php'),
                ], 'migrations');
            }
            $this->publishes([
                __DIR__.'/config/payzone.php' => config_path('payzone.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/assets' => public_path('assets'),
            ], 'public');

        }
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'payzone');

    }

    public function register(){
//        $this->mergeConfigFrom(
//            __DIR__.'/path/to/config/courier.php', 'courier'
//        );
    }

}
