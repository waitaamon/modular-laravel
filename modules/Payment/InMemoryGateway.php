<?php

namespace Modules\Payment;

class InMemoryGateway implements PaymentGateway
{

    public function charge(PaymentDetails $details): SuccessFulPayment
    {
        return new SuccessFulPayment(
            id: str()->uuid(),
            amountInCents: $details->amountInCents,
            paymentProvider: $this->id()
        );
    }

    public function id(): PaymentProvider
    {
        return PaymentProvider::InMemory;
    }
}
