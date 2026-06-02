<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'customer_name',
        'customer_phone',
        'delivery_pin',
        'delivery_area',
        'address_line1',
        'address_line2',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** One-line summary for display in the dropdown */
    public function summary(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->delivery_area,
            $this->delivery_pin,
        ]);

        return implode(', ', $parts);
    }
}
