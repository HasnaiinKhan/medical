<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminOrderCancelledMail;
use App\Mail\OrderCancelled;
use App\Mail\OrderDelivered;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    private const STATUSES = ['placed', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    /**
     * Revenue = sum of paid orders that are NOT cancelled.
     * This is the single source of truth used everywhere.
     */
    public static function revenueQuery()
    {
        return Order::where('payment_status', 'paid')
                    ->where('status', '!=', 'cancelled');
    }

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        $status        = $request->input('status', 'all');
        $payment       = $request->input('payment', 'all');
        $paymentStatus = $request->input('payment_status', 'all');
        $q             = trim((string) $request->input('q', ''));
        $from          = $request->input('from');
        $to            = $request->input('to');

        if ($status !== 'all' && $status !== '') {
            $query->where('status', $status);
        }
        if ($payment !== 'all' && $payment !== '') {
            $query->where('payment_method', $payment);
        }
        if ($paymentStatus !== 'all' && $paymentStatus !== '') {
            $query->where('payment_status', $paymentStatus);
        }
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('delivery_pin', 'like', "%{$q}%");
            });
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalRevenue  = self::revenueQuery()->sum('total_paise') / 100;
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'placed')->count();
        $todayOrders   = Order::whereDate('created_at', today())->count();

        return view('admin.orders.index', compact(
            'orders', 'totalRevenue', 'totalOrders', 'pendingOrders', 'todayOrders'
        ));
    }

    public function show(Order $order): View
    {
        $order->load(['items.medicine', 'user']);
        $statusFlow = self::STATUSES;
        return view('admin.orders.show', compact('order', 'statusFlow'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        $newStatus = $request->status;
        $old       = $order->status;

        // ── Cancellation: require a reason ──────────────────────────────
        if ($newStatus === 'cancelled') {
            $request->validate([
                'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
            ]);

            $order->update([
                'status'              => 'cancelled',
                'cancellation_reason' => trim($request->cancellation_reason),
                'cancelled_by'        => 'admin',
                'cancelled_at'        => now(),
            ]);

            // Restore stock for cancelled orders
            // (only if stock was already decremented — COD always, online only if paid/confirmed)
            $stockWasDeducted = $order->payment_method === 'cod'
                || in_array($order->payment_status, ['paid'])
                || in_array($order->status, ['confirmed', 'shipped']);

            if ($stockWasDeducted) {
                $order->loadMissing('items.medicine');
                foreach ($order->items as $item) {
                    if ($item->medicine) {
                        $item->medicine->increment('stock', $item->quantity);
                    }
                }
            }

            // Notify customer
            if ($order->user) {
                try {
                    $order->load('items');
                    Mail::to($order->user->email)->send(new OrderCancelled($order));
                } catch (\Throwable $e) {
                    Log::error('OrderCancelled customer mail failed: ' . $e->getMessage());
                }
            }

            // Notify admin
            try {
                $adminEmail = Setting::get('admin_email', config('mail.from.address'));
                if ($adminEmail) {
                    $order->loadMissing('items');
                    Mail::to($adminEmail)->send(new AdminOrderCancelledMail($order));
                }
            } catch (\Throwable $e) {
                Log::error('AdminOrderCancelled mail failed: ' . $e->getMessage());
            }

            return back()->with('status', "Order #{$order->order_number} has been cancelled.");
        }

        // ── Normal status update ─────────────────────────────────────────

        // Shipped → notify customer
        if ($newStatus === 'shipped' && $old !== 'shipped') {
            $order->update(['status' => $newStatus]);

            if ($order->user) {
                try {
                    $order->load('items');
                    Mail::to($order->user->email)->send(new OrderShipped($order));
                } catch (\Throwable $e) {
                    Log::error('OrderShipped mail failed: ' . $e->getMessage());
                }
            }
        }

        // Delivered → notify customer + mark COD as paid
        elseif ($newStatus === 'delivered') {
            $updates = ['status' => $newStatus];

            // COD orders are paid on delivery — always sync payment_status
            if ($order->payment_method === 'cod' && $order->payment_status !== 'paid') {
                $updates['payment_status'] = 'paid';
            }

            $order->update($updates);

            // Send delivery email only if this is a fresh transition
            if ($old !== 'delivered' && $order->user) {
                try {
                    $order->load('items');
                    Mail::to($order->user->email)->send(new OrderDelivered($order));
                } catch (\Throwable $e) {
                    Log::error('OrderDelivered mail failed: ' . $e->getMessage());
                }
            }
        }

        // All other status changes (placed, confirmed, etc.)
        else {
            $order->update(['status' => $newStatus]);
        }

        return back()->with('status', "Order #{$order->order_number} status changed from {$old} → {$newStatus}.");
    }

    public function bulkStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'order_ids'   => ['required', 'array'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status'      => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        // Bulk cancel is not allowed — must be done individually with a reason
        if ($request->status === 'cancelled') {
            return back()->with('status', 'Bulk cancellation is not allowed. Please cancel orders individually to provide a reason.');
        }

        // When marking as delivered, also set COD pending orders to paid
        if ($request->status === 'delivered') {
            Order::whereIn('id', $request->order_ids)
                ->where('payment_method', 'cod')
                ->where('payment_status', 'pending')
                ->update(['status' => 'delivered', 'payment_status' => 'paid']);

            Order::whereIn('id', $request->order_ids)
                ->where(fn ($q) => $q->where('payment_method', '!=', 'cod')
                                     ->orWhere('payment_status', '!=', 'pending'))
                ->update(['status' => 'delivered']);

            $count = count($request->order_ids);
        } else {
            $count = Order::whereIn('id', $request->order_ids)->update(['status' => $request->status]);
        }

        return back()->with('status', "{$count} order(s) updated to '{$request->status}'.");
    }
}
