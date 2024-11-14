<?php

namespace Modules\Order\DTOs;

use Modules\Order\Models\Order;

readonly class OrderDto
{
    /**
     * OrderDto constructor.
     * @param int $id
     * @param int $totalInCents
     * @param string $localizedTotal
     * @param OrderLineDto[] $lines
     */
    public function __construct(
        public int    $id,
        public int    $totalInCents,
        public string $localizedTotal,
        public string $url,
        public array $lines
    )
    {
    }

    public static function fromEloquentModel(Order $order):self
    {
        return new self(
            id: $order->id,
            totalInCents: $order->total_in_cents,
            localizedTotal: $order->localizedTotal(),
            url: $order->url(),
            lines: OrderLineDto::fromEloquentCollection($order->lines)
        );
    }
}
