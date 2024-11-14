<?php

namespace Modules\Product\Events;

use Modules\Order\Events\OrderFulfilled;
use Modules\Product\Warehouse\ProductStockManager;

class DecreaseOrderStock
{
    public function __construct(protected ProductStockManager $productStockManager)
    {
    }

    public function handle(OrderFulfilled $event): void
    {
        foreach ($event->order->lines as $orderLine) {

            $this->productStockManager->decrementStock($orderLine->productId, $orderLine->quantity);

        }
    }
}
