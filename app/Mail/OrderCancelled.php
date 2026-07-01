<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order ' . $this->order->order_number . ' Has Been Cancelled - Rx Plus 365',
            replyTo: [new Address('support@rxplus365.com', 'Rx Plus 365 Support')],
            tags: ['order-cancelled'],
            metadata: ['order_id' => (string) $this->order->id],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-cancelled');
    }
}
