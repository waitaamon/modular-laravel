<?php

namespace Modules\Product;

use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

class CartItemCollection
{
    /**
     * CartItemCollection constructor.
     * @param Collection<CartItem> $items
     */
    public function __construct(protected Collection $items)
    {
    }

    public static function fromCheckoutData(array $data): self
    {
        $cartItems = collect($data)
            ->map(fn(array $cartItemDetails) => new CartItem(
                product: ProductDto::fromEloquentModel(Product::find($cartItemDetails['id'])),
                quantity: $cartItemDetails['quantity']
            ));

        return new self($cartItems);
    }

    public function totalInCents(): mixed
    {
        return $this->items->sum(fn(CartItem $cartItem) => $cartItem->product->priceInCents * $cartItem->quantity);
    }

    public function items(): Collection
    {

        return $this->items;
    }
}
