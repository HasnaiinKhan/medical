<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart
    ) {}

    public function index(): View
    {
        $lines = $this->cart->lines();
        $subtotalPaise = $this->cart->subtotalPaise();

        return view('cart.index', compact('lines', 'subtotalPaise'));
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $medicine = Medicine::query()->findOrFail($data['medicine_id']);
        $qty = (int) ($data['quantity'] ?? 1);

        $this->cart->add($medicine->id, $qty);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Added to cart: '.$medicine->name,
                'cartCount' => $this->cart->count(),
                'quantity' => $this->cart->quantity($medicine->id),
            ]);
        }

        return back()->with('status', 'Added to cart: '.$medicine->name);
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $qty = (int) $data['quantity'];
        $this->cart->setQuantity($medicine->id, $qty);

        // If qty < 1, item was removed from cart
        $removed = $qty < 1;

        if ($request->expectsJson()) {
            return response()->json([
                'ok'            => true,
                'removed'       => $removed,
                'message'       => $removed
                    ? 'Removed from cart: ' . $medicine->name
                    : 'Cart updated: ' . $medicine->name,
                'cartCount'     => $this->cart->count(),
                'linesCount'    => $this->cart->lines()->count(),
                'subtotalPaise' => $this->cart->subtotalPaise(),
                'quantity'      => $qty,
                'lineTotalPaise'=> $removed ? 0 : $medicine->price_paise * $qty,
            ]);
        }

        return back()->with('status', $removed ? 'Removed from cart.' : 'Cart updated.');
    }

    public function remove(Medicine $medicine): RedirectResponse|JsonResponse
    {
        $this->cart->remove($medicine->id);

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Removed from cart: '.$medicine->name,
                'cartCount' => $this->cart->count(),
                'linesCount' => $this->cart->lines()->count(),
                'subtotalPaise' => $this->cart->subtotalPaise(),
            ]);
        }

        return back()->with('status', 'Removed from cart.');
    }
}
