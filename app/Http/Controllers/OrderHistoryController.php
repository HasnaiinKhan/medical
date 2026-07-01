<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(): View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items.medicine');
        return view('orders.show', compact('order'));
    }

    /**
     * Customer cancels their own order.
     * Only allowed when status is 'placed' or 'confirmed'.
     * Uses a DB transaction: status update + stock restore are atomic.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        // 1. Ownership check — prevent URL manipulation
        abort_unless($order->user_id === Auth::id(), 403);

        // 2. Business-rule check — already shipped or beyond
        if (! $order->canBeCancelledByUser()) {
            return back()->with('error',
                'This order can no longer be cancelled because it has already been ' . $order->status . '.'
            );
        }

        // 3. Idempotency — already cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'This order has already been cancelled.');
        }

        $reason = trim((string) $request->input('cancellation_reason', ''));

        // 4. Atomic: update status + restore stock
        DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $reason ?: 'Cancelled by customer.',
                'cancelled_by'        => 'user',
                'cancelled_at'        => now(),
            ]);

            // Restore stock for each item
            $order->loadMissing('items.medicine');
            foreach ($order->items as $item) {
                if ($item->medicine) {
                    $item->medicine->increment('stock', $item->quantity);
                }
            }
        });

        return redirect()->route('orders.show', $order)
            ->with('status', 'Your order #' . $order->order_number . ' has been cancelled.');
    }

    public function reorder(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->loadMissing('items.medicine');

        $added   = 0;
        $skipped = [];

        foreach ($order->items as $item) {
            $medicine = $item->medicine;

            if (! $medicine || $medicine->stock <= 0) {
                $skipped[] = $item->medicine_name_snapshot;
                continue;
            }

            $inCart  = $this->cart->quantity($medicine->id);
            $canAdd  = max(0, $medicine->stock - $inCart);
            $qty     = min($item->quantity, $canAdd);

            if ($qty > 0) {
                $this->cart->add($medicine->id, $qty);
                $added++;
            } else {
                $skipped[] = $item->medicine_name_snapshot;
            }
        }

        if ($added === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'None of the items from this order are currently in stock.');
        }

        $msg = $added . ' item' . ($added !== 1 ? 's' : '') . ' added to your cart.';
        if (count($skipped)) {
            $msg .= ' ' . count($skipped) . ' item' . (count($skipped) !== 1 ? 's were' : ' was') . ' out of stock and skipped.';
        }

        return redirect()->route('cart.index')->with('status', $msg);
    }
}
