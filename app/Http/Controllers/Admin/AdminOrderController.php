<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Mail\AdminOrderCancelledMail;
use App\Mail\OrderCancelled;
use App\Mail\OrderDelivered;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Setting;
=======
use App\Mail\OrderShipped;
use App\Models\Order;
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    private const STATUSES = ['placed', 'confirmed', 'shipped', 'delivered', 'cancelled'];

<<<<<<< HEAD
    /**
     * Revenue = sum of paid orders that are NOT cancelled.
     * This is the single source of truth used everywhere.
     */
    public static function revenueQuery()
    {
        return Order::where('payment_status', 'paid')
                    ->where('status', '!=', 'cancelled');
    }

=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

<<<<<<< HEAD
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
=======
        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment') && $request->payment !== 'all') {
            $query->where('payment_method', $request->payment);
        }

        // Filter by payment status
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number, customer name, or phone
        if ($request->filled('q')) {
            $q = $request->q;
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('delivery_pin', 'like', "%{$q}%");
            });
        }
<<<<<<< HEAD
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
=======

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        }

        $orders = $query->paginate(15)->withQueryString();

<<<<<<< HEAD
        $totalRevenue  = self::revenueQuery()->sum('total_paise') / 100;
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'placed')->count();
        $todayOrders   = Order::whereDate('created_at', today())->count();
=======
        // Summary stats for the filtered set (without pagination)
        $statsQuery = Order::query();
        $totalRevenue   = (clone $statsQuery)->where('payment_status', 'paid')->sum('total_paise') / 100;
        $totalOrders    = (clone $statsQuery)->count();
        $pendingOrders  = (clone $statsQuery)->where('status', 'placed')->count();
        $todayOrders    = (clone $statsQuery)->whereDate('created_at', today())->count();
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb

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

<<<<<<< HEAD
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
        $order->update(['status' => $newStatus]);

        // Shipped → notify customer
        if ($newStatus === 'shipped' && $old !== 'shipped' && $order->user) {
=======
        $old = $order->status;
        $order->update(['status' => $request->status]);

        // Send shipped notification email
        if ($request->status === 'shipped' && $old !== 'shipped' && $order->user) {
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            try {
                $order->load('items');
                Mail::to($order->user->email)->send(new OrderShipped($order));
            } catch (\Throwable $e) {
                Log::error('OrderShipped mail failed: ' . $e->getMessage());
            }
        }

<<<<<<< HEAD
        // Delivered → notify customer
        if ($newStatus === 'delivered' && $old !== 'delivered' && $order->user) {
            try {
                $order->load('items');
                Mail::to($order->user->email)->send(new OrderDelivered($order));
            } catch (\Throwable $e) {
                Log::error('OrderDelivered mail failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', "Order #{$order->order_number} status changed from {$old} → {$newStatus}.");
=======
        return back()->with('status', "Order #{$order->order_number} status changed from {$old} → {$request->status}.");
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    }

    public function bulkStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'order_ids'   => ['required', 'array'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status'      => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

<<<<<<< HEAD
        // Bulk cancel is not allowed — must be done individually with a reason
        if ($request->status === 'cancelled') {
            return back()->with('status', 'Bulk cancellation is not allowed. Please cancel orders individually to provide a reason.');
        }

=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        $count = Order::whereIn('id', $request->order_ids)->update(['status' => $request->status]);

        return back()->with('status', "{$count} order(s) updated to '{$request->status}'.");
    }
}
