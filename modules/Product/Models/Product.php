<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Database\Factories\ProductFactory;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'price_in_cents', 'stock'];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
