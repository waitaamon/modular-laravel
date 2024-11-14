<?php

namespace Modules\Payment;

interface PaymentGateway
{
    public function charge(PaymentDetails $details): SuccessFulPayment;
    public function id(): PaymentProvider;
}
