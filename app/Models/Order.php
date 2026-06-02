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
<<<<<<< HEAD
        'cancelled_by',
        'cancelled_at',
=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    ];

    protected $casts = [
        'is_dispatched' => 'boolean',
<<<<<<< HEAD
        'cancelled_at'  => 'datetime',
=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    ];

    // Valid status transitions
    public const STATUSES = [
        'placed', 'confirmed', 'shipped', 'delivered',
        'cancellation_requested', 'refund_initiated', 'refunded', 'cancelled',
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
        // Must be in a valid status
        if (! in_array($this->status, ['placed', 'confirmed', 'shipped', 'delivered'])) {
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

        // Time window: 30 days from order creation
        if ($this->created_at->diffInDays(now()) > 30) {
            return false;
        }

        return true;
    }

    public function refundWindowDaysLeft(): int
    {
        return max(0, 30 - (int) $this->created_at->diffInDays(now()));
    }

    public function isCOD(): bool
    {
        return $this->payment_method === 'cod';
    }
}
