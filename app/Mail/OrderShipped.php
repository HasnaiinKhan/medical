<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order is On the Way - ' . $this->order->order_number . ' | Rx Plus 365',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-shipped',
        );
    }

    // public function attachments(): array
    // {
    //     try {
    //         $invoice  = app(InvoiceService::class);
    //         $pdf      = $invoice->generate($this->order);
    //         $filename = $invoice->filename($this->order);

    //         return [
    //             Attachment::fromData(fn () => $pdf, $filename)
    //                 ->withMime('application/pdf'),
    //         ];
    //     } catch (\Throwable $e) {
    //         \Illuminate\Support\Facades\Log::error('Invoice attachment failed for OrderShipped: ' . $e->getMessage());
    //         return [];
    //     }
    // }
}
