<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Refund extends Model
{
    protected $fillable = [
        'order_id', 'refund_number', 'refund_id_gateway',
        'amount_paise', 'status', 'type', 'reason',
        'admin_notes', 'metadata', 'bank_account_number',
        'bank_ifsc', 'bank_account_name', 'upi_id',
        'proof_image_path', 'approved_by', 'approved_at',
        'refund_window_days', 'processed_at',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'processed_at'=> 'datetime',
        'approved_at' => 'datetime',
    ];

    // Valid state transitions
    private const TRANSITIONS = [
        'requested'  => ['approved', 'rejected', 'processing'],
        'approved'   => ['processing', 'processed', 'failed'],
        'processing' => ['processed', 'failed'],
        'processed'  => [],
        'failed'     => ['requested'],   // allow re-request after failure
        'rejected'   => [],
    ];

    public function order(): BelongsTo   { return $this->belongsTo(Order::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function auditLogs(): HasMany { return $this->hasMany(RefundAuditLog::class); }

    public function amountRupees(): float
    {
        return round($this->amount_paise / 100, 2);
    }

    /** Enforce valid state transitions */
    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? []);
    }

    /** Transition with audit log */
    public function transitionTo(string $newStatus, string $notes = '', string $actorType = 'system'): bool
    {
        if (! $this->canTransitionTo($newStatus)) {
            return false;
        }

        $old = $this->status;
        $this->update(['status' => $newStatus]);

        RefundAuditLog::create([
            'refund_id'   => $this->id,
            'user_id'     => Auth::id(),
            'action'      => $newStatus,
            'from_status' => $old,
            'to_status'   => $newStatus,
            'notes'       => $notes,
            'actor_type'  => $actorType,
        ]);

        return true;
    }

    public function statusBadge(): array
    {
        return match ($this->status) {

            'requested'  => ['bg-amber-100 text-amber-800',  'Requested'],
            'approved'   => ['bg-blue-100 text-blue-800',    'Approved'],
            'processing' => ['bg-purple-100 text-purple-800','Processing'],
            'processed'  => ['bg-green-100 text-green-800',  'Processed'],
            'failed'     => ['bg-red-100 text-red-800',      'Failed'],
            'rejected'   => ['bg-slate-100 text-slate-700',  'Rejected'],

            default      => ['bg-slate-100 text-slate-700',  ucfirst($this->status)],
        };
    }
}
