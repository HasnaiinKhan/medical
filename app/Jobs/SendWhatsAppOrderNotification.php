<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $token          = Setting::get('whatsapp_api_token', env('WHATSAPP_API_TOKEN', ''));
        $phoneNumberId  = Setting::get('whatsapp_phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID', ''));
        $adminNumber    = Setting::get('admin_whatsapp_number', env('WHATSAPP_NUMBER', ''));

        if (empty($token) || empty($phoneNumberId) || empty($adminNumber)) {
            Log::warning('WhatsApp order notification skipped: missing credentials.');
            return;
        }

        $order = $this->order->load('items');

        // Build items list
        $itemLines = $order->items->map(fn ($i) =>
            "• {$i->medicine_name_snapshot} x{$i->quantity} — ₹" . number_format($i->line_total_paise / 100, 2)
        )->implode("\n");

        $adminUrl = rtrim(config('app.url'), '/') . '/admin/orders/' . $order->id;

        $message = implode("\n", [
            "🛒 *New Order Received!*",
            "",
            "Order ID: *#{$order->order_number}*",
            "Customer: {$order->customer_name}",
            "Phone: +91 {$order->customer_phone}",
            "Address: {$order->address_line1}, {$order->delivery_area} - {$order->delivery_pin}",
            "",
            "*Items:*",
            $itemLines,
            "",
            "Total: *₹" . number_format($order->totalRupees(), 2) . "*",
            "Payment: " . ($order->payment_method === 'online' ? '💳 Online (Razorpay)' : '💵 Cash on Delivery'),
            "",
            "🔗 View: {$adminUrl}",
        ]);

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $adminNumber,
                    'type'              => 'text',
                    'text'              => ['body' => $message, 'preview_url' => false],
                ]);

            if (! $response->successful()) {
                Log::error('WhatsApp order notification failed', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'order_id' => $order->id,
                ]);
            } else {
                Log::info("WhatsApp order notification sent for #{$order->order_number}");
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp order notification exception: ' . $e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("WhatsApp notification job failed for order #{$this->order->order_number}: " . $e->getMessage());
    }
}
