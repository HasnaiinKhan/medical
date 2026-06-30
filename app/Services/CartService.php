<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Medicine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 *
 * Guest  (not logged in) → session key "shop_cart"  [array: medicine_id => qty]
 * User   (logged in)     → database table cart_items [user_id, medicine_id, quantity]
 *
 * All public methods route transparently to the correct store.
 * Call mergeGuestCartOnLogin() right after Auth::attempt() succeeds.
 */
class CartService
{
    private const SESSION_KEY = 'shop_cart';

    // ── Read ─────────────────────────────────────────────────────────────────

    /**
     * Raw cart array: [medicine_id (int) => quantity (int)]
     */
    public function items(): array
    {
        return Auth::check() ? $this->dbItems() : $this->sessionItems();
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function quantity(int $medicineId): int
    {
        return $this->items()[$medicineId] ?? 0;
    }

    // ── Write ────────────────────────────────────────────────────────────────

    public function add(int $medicineId, int $quantity = 1): void
    {
        $qty = max(1, $quantity);

        if (Auth::check()) {
            // updateOrCreate handles the unique(user_id, medicine_id) constraint
            $existing = CartItem::where('user_id', Auth::id())
                ->where('medicine_id', $medicineId)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $qty);
            } else {
                CartItem::create([
                    'user_id'     => Auth::id(),
                    'medicine_id' => $medicineId,
                    'quantity'    => $qty,
                ]);
            }
        } else {
            $cart = $this->sessionItems();
            $cart[$medicineId] = ($cart[$medicineId] ?? 0) + $qty;
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public function setQuantity(int $medicineId, int $quantity): void
    {
        if (Auth::check()) {
            if ($quantity < 1) {
                CartItem::where('user_id', Auth::id())
                    ->where('medicine_id', $medicineId)
                    ->delete();
            } else {
                CartItem::updateOrCreate(
                    ['user_id' => Auth::id(), 'medicine_id' => $medicineId],
                    ['quantity' => $quantity]
                );
            }
        } else {
            $cart = $this->sessionItems();
            if ($quantity < 1) {
                unset($cart[$medicineId]);
            } else {
                $cart[$medicineId] = $quantity;
            }
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public function remove(int $medicineId): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('medicine_id', $medicineId)
                ->delete();
        } else {
            $cart = $this->sessionItems();
            unset($cart[$medicineId]);
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    public function clear(): void
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            Session::forget(self::SESSION_KEY);
        }
    }

    // ── Merge: guest session → DB (call immediately after login) ────────────

    /**
     * Merge whatever is in the session cart into the logged-in user's DB cart.
     *
     * Rules:
     *  - Same medicine in both → sum quantities (clamped to stock)
     *  - Medicine only in session → add to DB
     *  - Medicine only in DB → untouched
     *  - Inactive / out-of-stock medicines are skipped
     *  - Runs in a DB transaction
     *  - Session cart is cleared on success
     */
    public function mergeGuestCartOnLogin(array $sessionCart): void
    {
        if (empty($sessionCart) || ! Auth::check()) {
            return;
        }

        $medicines = Medicine::query()
            ->whereIn('id', array_keys($sessionCart))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        DB::transaction(function () use ($sessionCart, $medicines) {
            foreach ($sessionCart as $medicineId => $guestQty) {
                $medicine = $medicines->get((int) $medicineId);

                if (! $medicine || $medicine->stock <= 0) {
                    continue; // skip unavailable/inactive
                }

                $row = CartItem::where('user_id', Auth::id())
                    ->where('medicine_id', $medicineId)
                    ->lockForUpdate()
                    ->first();

                $newQty = ($row ? $row->quantity : 0) + (int) $guestQty;
                $newQty = min($newQty, $medicine->stock); // respect stock

                if ($row) {
                    $row->update(['quantity' => $newQty]);
                } else {
                    CartItem::create([
                        'user_id'     => Auth::id(),
                        'medicine_id' => (int) $medicineId,
                        'quantity'    => $newQty,
                    ]);
                }
            }
        });

        // Clear guest session cart — it is now in the DB
        Session::forget(self::SESSION_KEY);
    }

    // ── Cart lines ───────────────────────────────────────────────────────────

    /**
     * @return Collection<int, array{medicine: Medicine, quantity: int, line_total_paise: int}>
     */
    public function lines(): Collection
    {
        $ids = array_keys($this->items());
        if ($ids === []) {
            return collect();
        }

        $medicines = Medicine::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($this->items())
            ->map(function (int $qty, int $id) use ($medicines) {
                $medicine = $medicines->get($id);
                if (! $medicine) return null;

                return [
                    'medicine'         => $medicine,
                    'quantity'         => $qty,
                    'line_total_paise' => $medicine->price_paise * $qty,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Lines with stock validation — auto-fixes over-stock and removes out-of-stock.
     *
     * @return array{lines: Collection, removed: string[], clamped: array[]}
     */
    public function linesWithStockCheck(): array
    {
        $ids = array_keys($this->items());
        if ($ids === []) {
            return ['lines' => collect(), 'removed' => [], 'clamped' => []];
        }

        $medicines = Medicine::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $cart    = $this->items();
        $removed = [];
        $clamped = [];

        foreach ($cart as $id => $qty) {
            $medicine = $medicines->get($id);

            if (! $medicine) {
                $this->remove($id);
                unset($cart[$id]);
                continue;
            }

            if ($medicine->stock <= 0) {
                $removed[] = $medicine->name;
                $this->remove($id);
                unset($cart[$id]);
            } elseif ($qty > $medicine->stock) {
                $clamped[] = ['name' => $medicine->name, 'old' => $qty, 'new' => $medicine->stock];
                $this->setQuantity($id, $medicine->stock);
                $cart[$id] = $medicine->stock;
            }
        }

        $lines = collect($cart)
            ->map(function (int $qty, int $id) use ($medicines) {
                $medicine = $medicines->get($id);
                if (! $medicine) return null;

                return [
                    'medicine'         => $medicine,
                    'quantity'         => $qty,
                    'line_total_paise' => $medicine->price_paise * $qty,
                ];
            })
            ->filter()
            ->values();

        return ['lines' => $lines, 'removed' => $removed, 'clamped' => $clamped];
    }

    public function subtotalPaise(): int
    {
        return (int) $this->lines()->sum('line_total_paise');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /** @return array<int, int> */
    private function sessionItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /** @return array<int, int> */
    private function dbItems(): array
    {
        return CartItem::where('user_id', Auth::id())
            ->pluck('quantity', 'medicine_id')
            ->mapWithKeys(fn ($qty, $id) => [(int) $id => (int) $qty])
            ->all();
    }
}
