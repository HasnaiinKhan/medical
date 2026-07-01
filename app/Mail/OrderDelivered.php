<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDelivered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order ' . $this->order->order_number . ' Has Been Delivered - Rx Plus 365',
            replyTo: [new Address('support@rxplus365.com', 'Rx Plus 365 Support')],
            tags: ['order-delivered'],
            metadata: ['order_id' => (string) $this->order->id],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-delivered');
    }

    public function attachments(): array
    {
        try {
            $invoice  = app(InvoiceService::class);
            $pdf      = $invoice->generate($this->order);
            $filename = $invoice->filename($this->order);

            return [
                Attachment::fromData(fn () => $pdf, $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Invoice attachment failed for OrderPlaced: ' . $e->getMessage());
            return [];
        }
    }

}
