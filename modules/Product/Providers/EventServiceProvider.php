<?php

namespace Modules\Product\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;
use Modules\Order\Events\OrderFulfilled;
use Modules\Product\Events\DecreaseOrderStock;

class EventServiceProvider extends BaseEventServiceProvider
{
    protected $listen  = [
        OrderFulfilled::class => [
            DecreaseOrderStock::class
        ]
    ];
}
