<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderCancelledMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Cancelled: ' . $this->order->order_number . ' - Rx Plus 365',
            replyTo: [new Address('support@rxplus365.com', 'Rx Plus 365')],
            tags: ['admin-order-cancelled'],
            metadata: ['order_id' => (string) $this->order->id],
        );
    }

    public function content(): Content
    {
        // Always reload relations at render time so they are never stripped
        // by SerializesModels serialization/deserialization.
        $this->order->load(['items', 'user']);

        return new Content(view: 'emails.admin-order-cancelled');
    }
}
