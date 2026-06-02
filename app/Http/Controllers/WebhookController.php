<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Razorpay webhook events.
     * Configure webhook URL in Razorpay dashboard: POST /webhooks/razorpay
     * Events to subscribe: refund.processed, refund.failed, refund.speed_changed
     */
    public function razorpay(Request $request): Response
    {
        $secret    = config('razorpay.webhook_secret');
        $signature = $request->header('X-Razorpay-Signature');
        $payload   = $request->getContent();

        // Verify webhook signature
        if ($secret && $signature) {
            $expected = hash_hmac('sha256', $payload, $secret);
            if (! hash_equals($expected, $signature)) {
                Log::warning('Razorpay webhook signature mismatch');
                return response('Unauthorized', 401);
            }
        }

        $event = $request->input('event');
        $data  = $request->input('payload.refund.entity', []);

        Log::info('Razorpay webhook received', ['event' => $event]);

        match ($event) {
            'refund.processed'      => $this->handleRefundProcessed($data),
            'refund.failed'         => $this->handleRefundFailed($data),
            'refund.speed_changed'  => $this->handleRefundProcessed($data),
            default                 => null,
        };

        return response('OK', 200);
    }

    private function handleRefundProcessed(array $data): void
    {
        $gatewayRefundId = $data['id'] ?? null;
        if (! $gatewayRefundId) return;

        $refund = Refund::where('refund_id_gateway', $gatewayRefundId)->first();
        if (! $refund || $refund->status === 'processed') return;

        $refund->update([
            'metadata'     => array_merge($refund->metadata ?? [], ['webhook' => $data]),
            'processed_at' => now(),
        ]);
        $refund->transitionTo('processed', 'Confirmed via Razorpay webhook.', 'webhook');

        $refund->order->update(['status' => 'refunded', 'payment_status' => 'refunded']);

        // Notify customer
        try {
            $refund->load('order');
            if ($refund->order->user) {
                \Illuminate\Support\Facades\Mail::to($refund->order->user->email)
                    ->send(new \App\Mail\RefundProcessed($refund));
            }
        } catch (\Throwable $e) {
            Log::error('Webhook RefundProcessed mail failed: ' . $e->getMessage());
        }

        Log::info("Refund {$gatewayRefundId} confirmed via webhook.");
    }

    private function handleRefundFailed(array $data): void
    {
        $gatewayRefundId = $data['id'] ?? null;
        if (! $gatewayRefundId) return;

        $refund = Refund::where('refund_id_gateway', $gatewayRefundId)->first();
        if (! $refund) return;

        $refund->update([
            'status'   => 'failed',
            'metadata' => array_merge($refund->metadata ?? [], ['webhook_failure' => $data]),
        ]);

        Log::error("Refund {$gatewayRefundId} failed via webhook.");
    }
}
