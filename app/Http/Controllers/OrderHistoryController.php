<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
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
}
