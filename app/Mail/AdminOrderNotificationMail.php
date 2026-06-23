<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotificationMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order #' . $this->order->order_number . ' - Rx Plus 365 Admin',
        );
    }

    public function content(): Content
    {
        // Always reload relations at render time so they are never stripped
        // by SerializesModels serialization/deserialization.
        $this->order->load(['items', 'user']);

        return new Content(view: 'emails.admin-order-notification');
    }
}
