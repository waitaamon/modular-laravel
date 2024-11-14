<?php

namespace Modules\Payment;

class SuccessFulPayment
{
public function __construct(
    public string $id,
    public int $amountInCents,
    public PaymentProvider $paymentProvider,
)
{
}
}
