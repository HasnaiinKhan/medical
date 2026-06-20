<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\RefundAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        if (blank($secret)) {
            Log::critical('Razorpay webhook attempted without configured webhook secret.');
            return response('Webhook secret not configured', 503);
        }

        if (blank($signature)) {
            Log::warning('Razorpay webhook missing signature header');
            return response('Unauthorized', 401);
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($expected, $signature)) {
            Log::warning('Razorpay webhook signature mismatch');
            return response('Unauthorized', 401);
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

        DB::transaction(function () use ($refund, $data) {
            $refund->update([
                'metadata'     => array_merge($refund->metadata ?? [], ['webhook' => $data]),
                'processed_at' => now(),
            ]);
            $refund->transitionTo('processed', 'Confirmed via Razorpay webhook.', 'webhook', [
                'gateway' => 'razorpay',
                'webhook_event' => 'refund.processed',
                'gateway_refund_id' => $data['id'] ?? null,
            ]);

            $order = $refund->order;
            $order->update(['status' => 'refunded', 'payment_status' => 'refunded']);

            if (! $order->is_dispatched) {
                $order->loadMissing('items.medicine');
                foreach ($order->items as $item) {
                    if ($item->medicine) {
                        $item->medicine->increment('stock', $item->quantity);
                    }
                }
            }
        });

        // Notify customer
        try {
            $refund->load('order');
            if ($refund->order->user) {
                Mail::to($refund->order->user->email)
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

        $fromStatus = $refund->status;
        $refund->update([
            'metadata' => array_merge($refund->metadata ?? [], ['webhook_failure' => $data]),
        ]);

        if ($refund->canTransitionTo('failed')) {
            $refund->transitionTo('failed', 'Refund failed via Razorpay webhook.', 'webhook', [
                'gateway' => 'razorpay',
                'webhook_event' => 'refund.failed',
                'gateway_refund_id' => $data['id'] ?? null,
            ]);
        } else {
            RefundAuditLog::create([
                'refund_id' => $refund->id,
                'user_id' => null,
                'action' => 'failed',
                'from_status' => $fromStatus,
                'to_status' => 'failed',
                'notes' => 'Refund failed via Razorpay webhook.',
                'actor_type' => 'webhook',
                'metadata' => ['webhook_failure' => $data],
            ]);
            $refund->update(['status' => 'failed']);
        }

        Log::error("Refund {$gatewayRefundId} failed via webhook.");
    }
}
