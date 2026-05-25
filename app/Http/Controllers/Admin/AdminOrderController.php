<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    private const STATUSES = ['placed', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

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
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('delivery_pin', 'like', "%{$q}%");
            });
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Summary stats for the filtered set (without pagination)
        $statsQuery = Order::query();
        $totalRevenue   = (clone $statsQuery)->where('payment_status', 'paid')->sum('total_paise') / 100;
        $totalOrders    = (clone $statsQuery)->count();
        $pendingOrders  = (clone $statsQuery)->where('status', 'placed')->count();
        $todayOrders    = (clone $statsQuery)->whereDate('created_at', today())->count();

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

        $old = $order->status;
        $order->update(['status' => $request->status]);

        // Send shipped notification email
        if ($request->status === 'shipped' && $old !== 'shipped' && $order->user) {
            try {
                $order->load('items');
                Mail::to($order->user->email)->send(new OrderShipped($order));
            } catch (\Throwable $e) {
                Log::error('OrderShipped mail failed: ' . $e->getMessage());
            }
        }

        return back()->with('status', "Order #{$order->order_number} status changed from {$old} → {$request->status}.");
    }

    public function bulkStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'order_ids'   => ['required', 'array'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status'      => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        $count = Order::whereIn('id', $request->order_ids)->update(['status' => $request->status]);

        return back()->with('status', "{$count} order(s) updated to '{$request->status}'.");
    }
}
