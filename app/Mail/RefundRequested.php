<?php

namespace App\Mail;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Refund $refund) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Request Received: ' . $this->refund->refund_number . ' - Rx Plus 365',
            replyTo: [new Address('support@rxplus365.com', 'Rx Plus 365 Support')],
            tags: ['refund-requested'],
            metadata: ['refund_id' => (string) $this->refund->id],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.refund-requested');
    }
}
