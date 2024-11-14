<?php

namespace Modules\Order\Events;

use Illuminate\Support\Facades\Mail;
use Modules\Order\Mail\OrderReceivedMail;

class SendOrderConfirmationEmail
{
    public function handle(OrderFulfilled $event):void
    {
        Mail::to($event->user->email)->send(new OrderReceivedMail($event->order->localizedTotal));
    }
}
