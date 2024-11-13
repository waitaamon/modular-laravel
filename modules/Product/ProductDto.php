<?php

namespace Modules\Product;

use Modules\Product\Models\Product;

readonly class ProductDto
{
    public function __construct(public int $id, public int $priceInCents, public int $unisInStock)
    {
    }

    public static function fromEloquentModel(Product $product):ProductDto
    {
        return new ProductDto($product->id, $product->price_in_cents, $product->stock);
    }
}
