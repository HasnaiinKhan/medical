<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundAuditLog extends Model
{
    protected $fillable = [
        'refund_id', 'user_id', 'action',
        'from_status', 'to_status', 'notes',
        'actor_type', 'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function refund() { return $this->belongsTo(Refund::class); }
    public function user()   { return $this->belongsTo(User::class); }
}
