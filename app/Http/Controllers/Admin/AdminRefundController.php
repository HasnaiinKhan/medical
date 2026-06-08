<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RefundController;
use App\Mail\RefundProcessed;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminRefundController extends Controller
{
    public function __construct(private RefundController $refundController) {}

    public function index(Request $request): View
    {
        $query = Refund::with(['order.user'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('refund_number', 'like', "%{$q}%")
                    ->orWhereHas('order', fn ($o) =>
                        $o->where('order_number', 'like', "%{$q}%")
                          ->orWhere('customer_name', 'like', "%{$q}%")
                    );
            });
        }

        $refunds = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Refund::count(),
            'requested'  => Refund::where('status', 'requested')->count(),
            'processing' => Refund::whereIn('status', ['approved', 'processing'])->count(),
            'processed'  => Refund::where('status', 'processed')->count(),
            'failed'     => Refund::whereIn('status', ['failed', 'rejected'])->count(),
        ];

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    public function show(Refund $refund): View
    {
        $refund->load(['order.items', 'order.user', 'auditLogs.user']);
        return view('admin.refunds.show', compact('refund'));
    }

    public function approve(Refund $refund): RedirectResponse
    {
        if ($refund->status !== 'requested') {
            return back()->with('error', 'Only requested refunds can be approved.');
        }

        $order = $refund->order;

        // All non-gateway types require manual transfer
        if (in_array($refund->type, ['cod_bank_transfer', 'cod_upi', 'online_bank_transfer', 'online_upi'])) {
            $method = match($refund->type) {
                'cod_upi', 'online_upi' => 'UPI transfer',
                default                 => 'bank transfer',
            };
            $refund->transitionTo('approved', "Admin approved refund. Awaiting {$method}.", 'admin');
            $order->update(['status' => 'refund_initiated']);

            RefundAuditLog::create([
                'refund_id'  => $refund->id,
                'user_id'    => Auth::id(),
                'action'     => 'approved',
                'from_status'=> 'requested',
                'to_status'  => 'approved',
                'notes'      => "Admin approved. Manual {$method} required.",
                'actor_type' => 'admin',
            ]);

            $refund->update(['approved_by' => Auth::id(), 'approved_at' => now()]);

            return back()->with('status', "Refund #{$refund->refund_number} approved. Process {$method} manually.");
        }

        // Online: trigger gateway
        $this->refundController->processGatewayRefund($order, $refund);

        $fresh = $refund->fresh();
        $msg = $fresh->status === 'processed'
            ? "Refund #{$refund->refund_number} processed via Razorpay."
            : "Gateway refund failed for #{$refund->refund_number}. Flagged for manual review.";

        return back()->with('status', $msg);
    }

    public function markProcessed(Request $request, Refund $refund): RedirectResponse
    {
        $request->validate(['admin_notes' => ['required', 'string', 'max:500']]);

        if (! in_array($refund->status, ['approved', 'processing'])) {
            return back()->with('error', 'Refund must be approved before marking as processed.');
        }

        $refund->update([
            'admin_notes'  => $request->admin_notes,
            'processed_at' => now(),
        ]);
        $refund->transitionTo('processed', 'Transfer completed. Ref: ' . $request->admin_notes, 'admin');

        $refund->order->update(['status' => 'refunded', 'payment_status' => 'refunded']);

        RefundAuditLog::create([
            'refund_id'  => $refund->id,
            'user_id'    => Auth::id(),
            'action'     => 'processed',
            'from_status'=> 'approved',
            'to_status'  => 'processed',
            'notes'      => ($refund->type === 'cod_upi' ? 'UPI transfer done.' : 'Bank transfer done.') . ' Ref: ' . $request->admin_notes,
            'actor_type' => 'admin',
        ]);

        // Notify customer
        try {
            $refund->load('order');
            if ($refund->order->user) {
                Mail::to($refund->order->user->email)->send(new RefundProcessed($refund));
            }
        } catch (\Throwable $e) {
            Log::error('RefundProcessed mail failed: ' . $e->getMessage());
        }

        return back()->with('status', "Refund #{$refund->refund_number} marked as processed.");
    }

    public function reject(Request $request, Refund $refund): RedirectResponse
    {
        $request->validate(['admin_notes' => ['required', 'string', 'max:500']]);

        $refund->update(['admin_notes' => $request->admin_notes]);
        $refund->transitionTo('rejected', 'Admin rejected: ' . $request->admin_notes, 'admin');

        RefundAuditLog::create([
            'refund_id'  => $refund->id,
            'user_id'    => Auth::id(),
            'action'     => 'rejected',
            'from_status'=> $refund->getOriginal('status'),
            'to_status'  => 'rejected',
            'notes'      => $request->admin_notes,
            'actor_type' => 'admin',
        ]);

        $refund->order->update(['status' => 'delivered']);

        return back()->with('status', "Refund #{$refund->refund_number} rejected.");
    }

    public function toggleDispatched(Order $order): RedirectResponse
    {
        $order->update(['is_dispatched' => ! $order->is_dispatched]);
        return back()->with('status', "Order #{$order->order_number} dispatch status updated.");
    }
}
