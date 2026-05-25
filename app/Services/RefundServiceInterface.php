<?php

namespace App\Services;

use App\Models\Order;

interface RefundServiceInterface
{
    /**
     * Process a refund via the payment gateway.
     *
     * @return array{success: bool, refund_id: string|null, raw: array, error: string|null}
     */
    public function process(Order $order, float $amountRupees, string $reason): array;
}
