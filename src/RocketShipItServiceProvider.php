<?php

namespace DoubleOh13\RocketShipIt;

use Illuminate\Support\ServiceProvider;

class RocketShipItServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/rocketshipit.php', 'rocketshipit');

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                config('rocketshipit.path_to_binary'),
                config('rocketshipit.api_key'),
                config('rocketshipit.endpoint')
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/rocketshipit.php' => config_path('rocketshipit.php'),
            ]
        );
    }
}
