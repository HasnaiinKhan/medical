<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'delivery_pin',
        'delivery_area',
        'address_line1',
        'address_line2',
        'subtotal_paise',
        'delivery_fee_paise',
        'total_paise',
        'payment_method',
        'payment_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'status',
        'is_dispatched',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'is_dispatched' => 'boolean',
        'cancelled_at'  => 'datetime',
    ];

    // Valid status transitions
    public const STATUSES = [
        'placed', 'confirmed', 'shipped', 'delivered',
        'Refund_requested', 'refund_initiated', 'refunded', 'cancelled',
        'payment_failed', 'payment_review',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function totalRupees(): float
    {
        return round($this->total_paise / 100, 2);
    }

    public function canRequestRefund(): bool
    {
        // Only allow refund requests after the order has been delivered
        if ($this->status !== 'delivered') {
            return false;
        }

        // Must have been paid (online) or COD
        if (! in_array($this->payment_status, ['paid', 'pending'])) {
            return false;
        }

        // No active refund already exists
        if ($this->refunds()->whereIn('status', ['requested', 'approved', 'processing', 'processed'])->exists()) {
            return false;
        }

        // Time window: configurable days from order creation (default 30)
        $windowDays = (int) \App\Models\Setting::get('refund_window_days', 30);
        if ($this->created_at->diffInDays(now()) > $windowDays) {
            return false;
        }

        return true;
    }

    public function refundWindowDaysLeft(): int
    {
        $windowDays = (int) \App\Models\Setting::get('refund_window_days', 30);
        return max(0, $windowDays - (int) $this->created_at->diffInDays(now()));
    }

    public function isCOD(): bool
    {
        return $this->payment_method === 'cod';
    }
}
