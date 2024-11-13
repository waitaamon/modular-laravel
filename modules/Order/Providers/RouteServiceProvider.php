<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{

    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->as('order::')
                ->group(__DIR__ . '/../routes.php');
        });
    }
}
