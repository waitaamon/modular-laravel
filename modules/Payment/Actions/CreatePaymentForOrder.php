<?php

namespace Modules\Payment\Actions;

use Modules\Payment\Exceptions\PaymentFailedException;
use Modules\Payment\Payment;
use Modules\Payment\PaymentDetails;
use Modules\Payment\PaymentGateway;

class CreatePaymentForOrder
{
    public function handle(int $orderId, int $userId, int $totalInCents, PaymentGateway $paymentGateway, string $paymentToken): Payment
    {

            $charge = $paymentGateway->charge(
                new PaymentDetails(
                    token: $paymentToken,
                    amountInCents: $totalInCents,
                    statementDescription: 'Modularization'
                )
            );

        return Payment::create([
            'total_in_cents' => $totalInCents,
            'status' => 'paid',
            'payment_gateway' => $charge->paymentProvider,
            'payment_id' => $charge->id,
            'user_id' => $userId,
            'order_id' => $orderId
        ]);
    }
}
