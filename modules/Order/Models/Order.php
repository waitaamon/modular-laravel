<?php

namespace Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Order\Exceptions\OrderMissingOrderLineException;
use Modules\Payment\Payment;
use Modules\Product\CartItem;
use Modules\Product\CartItemCollection;
use NumberFormatter;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_in_cents'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_in_cents' => 'integer',
    ];

    public const COMPLETED = 'completed';

    public const PENDING = 'pending';

    public const PAYMENT_FAILED = 'payment_failed';

    public static function startForUser(int $userId): self
    {
        return self::make([
            'user_id' => $userId,
            'status' => self::PENDING,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function lastPayment(): HasOne
    {
        return $this->payments()->one()->latest();
    }

    public function url(): string
    {
        return route('order::orders.show', $this);
    }

    /**
     * @param CartItemCollection<CartItem> $items
     */
    public function addLineFromCartItems(CartItemCollection $items): void
    {
        foreach ($items->items() as $cartItem) {
            $this->lines->push(OrderLine::make([
                'product_id' => $cartItem->product->id,
                'product_price_in_cents' => $cartItem->product->priceInCents,
                'quantity' => $cartItem->quantity,
            ]));
        }

        $this->total_in_cents = $this->lines->sum(fn(OrderLine $line) => $line->product_price_in_cents);
    }

    /**
     * @throws OrderMissingOrderLineException
     */
    public function fulfill(): void
    {
        if ($this->lines->isEmpty()) {
            throw  new OrderMissingOrderLineException();
        }

        $this->status = self::COMPLETED;
        $this->save();
        $this->lines()->saveMany($this->lines);
    }

    public function localizedTotal(): string
    {
        return (new NumberFormatter('en-US', NumberFormatter::CURRENCY))->formatCurrency($this->total_in_cents / 100, 'USD');
    }
}
