<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Generate an invoice PDF for the given order and return the raw PDF string.
     */
    public function generate(Order $order): string
    {
        $order->loadMissing('items');

        $pdf = Pdf::loadView('emails.invoice', compact('order'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
            ]);

        return $pdf->output();
    }

    /**
     * Return a suitable filename for the invoice attachment.
     */
    public function filename(Order $order): string
    {
        return 'Invoice-' . $order->order_number . '.pdf';
    }
}
