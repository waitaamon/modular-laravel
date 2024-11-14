<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Order\Actions\PurchaseItems;
use Modules\Order\DTOs\PendingPayment;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Payment\PayBuddySdk;
use Modules\Payment\PaymentGateway;
use Modules\Product\CartItemCollection;
use Modules\User\UserDto;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        protected PurchaseItems $purchaseItems,
        protected PaymentGateway $paymentGateway
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(CheckoutRequest $request)
    {

        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));

        $pendingPayment = new PendingPayment($this->paymentGateway, $request->input('payment_token'));

        $userDto = UserDto::fromEloquentModel($request->user());

        try {

            $order = $this->purchaseItems->handle(items: $cartItems, pendingPayment: $pendingPayment, user: $userDto);

        } catch (RuntimeException) {
            throw ValidationException::withMessages(['payment_token' => 'We could not process your payment.']);
        }

        return response()->json([
            'order_url' => $order->url,
        ], 201);
    }
}
