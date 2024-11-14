<?php

namespace Modules\Payment\Exceptions;

use RuntimeException;

class PaymentFailedException extends RuntimeException
{
    public static function dueToInvalidToken(): self
    {
        return new self('The given token is invalid.');
    }
}
