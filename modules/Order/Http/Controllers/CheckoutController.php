<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Models\Order;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;

class CheckoutController extends Controller
{
    public function __invoke(CheckoutRequest $request)
    {
        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));

        $orderTotalInCents = $cartItems->totalInCents();


        $payBuddy = PayBuddy::make();

        try {
            $charge = $payBuddy->charge($request->input('payment_token'), $orderTotalInCents, 'Modularization');
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['payment_token' => 'We could not process your payment. Please try again.']);
        }

        $order = Order::create([
            'total_in_cents' => $orderTotalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
            'user_id' => $request->user()->id,
        ]);

        foreach ($cartItems->items() as $cartItem) {

            $cartItem->product->decrement('stock');

            $order->lines()->create([
                'product_id' => $cartItem->product->id,
                'product_price_in_cents' => $cartItem->product->price_in_cents,
                'quantity' => $cartItem->quantity,
            ]);

        }

        return response()->json([], 201);
    }
}
