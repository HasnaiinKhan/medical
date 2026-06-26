<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Ensure the order belongs to the logged-in user
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load('items');

        return view('orders.show', compact('order'));
    }

    public function reorder(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->loadMissing('items.medicine');

        $added    = 0;
        $skipped  = [];

        foreach ($order->items as $item) {
            $medicine = $item->medicine;

            // Medicine deleted or out of stock — skip
            if (! $medicine || $medicine->stock <= 0) {
                $skipped[] = $item->medicine_name_snapshot;
                continue;
            }

            // How much can still be added (respect stock and what's already in cart)
            $inCart    = $this->cart->quantity($medicine->id);
            $canAdd    = max(0, $medicine->stock - $inCart);
            $qty       = min($item->quantity, $canAdd);

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
