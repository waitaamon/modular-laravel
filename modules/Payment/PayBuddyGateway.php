<?php

namespace Modules\Payment;

use Modules\Payment\Exceptions\PaymentFailedException;

class PayBuddyGateway implements PaymentGateway
{
    public function __construct(protected PayBuddySdk $payBuddySdk)
    {
    }

    /**
     * @throws PaymentFailedException
     */
    public function charge(PaymentDetails $details): SuccessFulPayment
    {
        try {
            $charge = $this->payBuddySdk->charge(
                token: $details->token,
                amountInCents: $details->amountInCents,
                statementDescription: $details->statementDescription
            );
        } catch (\RuntimeException $exception) {
            throw new PaymentFailedException($exception->getMessage());
        }

        return  new SuccessFulPayment(
            id: $charge['id'],
            amountInCents: $charge['amount_in_cents'],
            paymentProvider: $this->id()
        );
    }

    public function id(): PaymentProvider
    {
        return PaymentProvider::PayBuddy;
    }
}
