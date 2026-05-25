<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayRefundService implements RefundServiceInterface
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('razorpay.key_id'),
            config('razorpay.key_secret')
        );
    }

    public function process(Order $order, float $amountRupees, string $reason): array
    {
        // Razorpay requires paise (integer)
        $paisaAmount = (int) round($amountRupees * 100);

        // Must have a Razorpay payment ID to refund
        if (empty($order->razorpay_payment_id)) {
            return [
                'success'   => false,
                'refund_id' => null,
                'raw'       => [],
                'error'     => 'No Razorpay payment ID found on this order.',
            ];
        }

        try {
            $payment = $this->api->payment->fetch($order->razorpay_payment_id);

            $refund = $payment->refund([
                'amount' => $paisaAmount,
                'notes'  => ['reason' => $reason, 'order_number' => $order->order_number],
            ]);

            return [
                'success'   => true,
                'refund_id' => $refund->id,
                'raw'       => $refund->toArray(),
                'error'     => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Razorpay refund failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success'   => false,
                'refund_id' => null,
                'raw'       => [],
                'error'     => $e->getMessage(),
            ];
        }
    }
}
