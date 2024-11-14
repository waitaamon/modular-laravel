<?php

namespace Modules\Order\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $localizedOrderTotal)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: null,
            html: "<h1>Thank you for your order!</h1><p>Your order total is: {$this->localizedOrderTotal}</p>",
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
