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
        $result        = $this->cart->linesWithStockCheck();
        $lines         = $result['lines'];
        $subtotalPaise = $lines->sum('line_total_paise');
        $stockWarnings = [];

        foreach ($result['removed'] as $name) {
            $stockWarnings[] = "⚠️ \"{$name}\" is out of stock and was removed from your cart.";
        }
        foreach ($result['clamped'] as $item) {
            $stockWarnings[] = "⚠️ \"{$item['name']}\" quantity reduced to {$item['new']} (only {$item['new']} left in stock).";
        }

        return view('cart.index', compact('lines', 'subtotalPaise', 'stockWarnings'));
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'quantity'    => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $medicine = Medicine::query()->findOrFail($data['medicine_id']);

        $qty      = (int) ($data['quantity'] ?? 1);

        // Stock check — account for what's already in cart
        $inCart    = $this->cart->quantity($medicine->id);
        $available = $medicine->stock;

        if ($available <= 0) {
            $msg = "{$medicine->displayName()} is out of stock.";
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $msg, 'out_of_stock' => true], 422);
            }
            return back()->with('error', $msg);
        }

        if ($inCart + $qty > $available) {
            $canAdd = max(0, $available - $inCart);
            if ($canAdd === 0) {
                $msg = "You already have all {$available} available unit(s) of {$medicine->displayName()} in your cart.";
            } else {
                $msg = "Only {$available} unit(s) of {$medicine->displayName()} available. You can add {$canAdd} more.";
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'         => false,
                    'message'    => $msg,
                    'stock_limit' => true,
                    'available'  => $available,
                    'in_cart'    => $inCart,
                ], 422);
            }
            return back()->with('error', $msg);
        }

        $this->cart->add($medicine->id, $qty);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'        => true,
                'message'   => 'Added to cart: ' . $medicine->displayName(),
                'cartCount' => $this->cart->count(),
                'quantity'  => $this->cart->quantity($medicine->id),
            ]);
        }

        return back()->with('status', 'Added to cart: ' . $medicine->displayName());
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $qty = (int) $data['quantity'];

        // Stock check for increases (allow 0 = remove)
        if ($qty > 0 && $qty > $medicine->stock) {
            $available = $medicine->stock;
            if ($available <= 0) {
                $msg = "{$medicine->name} is now out of stock.";
                // Remove from cart since stock is gone
                $this->cart->remove($medicine->id);
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok'          => false,
                        'removed'     => true,
                        'out_of_stock'=> true,
                        'message'     => $msg,
                        'cartCount'   => $this->cart->count(),
                        'linesCount'  => $this->cart->lines()->count(),
                        'subtotalPaise' => $this->cart->subtotalPaise(),
                        'quantity'    => 0,
                    ], 422);
                }
                return back()->with('error', $msg);
            }
            // Clamp to available stock
            $msg = "Only {$available} unit(s) of {$medicine->name} available. Quantity set to {$available}.";
            $qty = $available;
            if ($request->expectsJson()) {
                $this->cart->setQuantity($medicine->id, $qty);
                return response()->json([
                    'ok'           => false,
                    'stock_limit'  => true,
                    'message'      => $msg,
                    'cartCount'    => $this->cart->count(),
                    'linesCount'   => $this->cart->lines()->count(),
                    'subtotalPaise'=> $this->cart->subtotalPaise(),
                    'quantity'     => $qty,
                    'lineTotalPaise' => $medicine->price_paise * $qty,
                ], 422);
            }
            return back()->with('error', $msg);
        }

        $this->cart->setQuantity($medicine->id, $qty);
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
