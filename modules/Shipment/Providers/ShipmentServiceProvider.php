<?php

namespace Modules\Shipment\Providers;

use Illuminate\Support\ServiceProvider;

class ShipmentServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'order');

        $this->app->register(RouteServiceProvider::class);
    }
}
