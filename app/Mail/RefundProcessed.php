<?php

namespace App\Mail;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Refund $refund) {}

    public function envelope(): Envelope
    {
<<<<<<< HEAD
        return new Envelope(subject: 'Refund Processed — ' . $this->refund->refund_number . ' | Medikart');
=======
        return new Envelope(subject: '💚 Refund Processed — ' . $this->refund->refund_number . ' | Medikart');
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    }

    public function content(): Content
    {
        return new Content(view: 'emails.refund-processed');
    }
}
