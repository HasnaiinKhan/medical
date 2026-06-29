<?php

namespace App\Services;

use App\Models\Medicine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'shop_cart';

    /** @return array<int, int> medicine_id => quantity */
    public function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function add(int $medicineId, int $quantity = 1): void
    {
        $qty = max(1, $quantity);
        $cart = $this->items();
        $cart[$medicineId] = ($cart[$medicineId] ?? 0) + $qty;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function setQuantity(int $medicineId, int $quantity): void
    {
        $cart = $this->items();
        if ($quantity < 1) {
            unset($cart[$medicineId]);
        } else {
            $cart[$medicineId] = $quantity;
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $medicineId): void
    {
        $cart = $this->items();
        unset($cart[$medicineId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /** @return Collection<int, array{medicine: Medicine, quantity: int, line_total_paise: int}> */
    public function lines(): Collection
    {
        $ids = array_keys($this->items());
        if ($ids === []) {
            return collect();
        }

        $medicines = Medicine::query()->whereIn('id', $ids)->where('is_active', true)->get()->keyBy('id');

        return collect($this->items())->map(function (int $qty, int $id) use ($medicines) {
            $medicine = $medicines->get($id);
            if (! $medicine) {
                return null;
            }

            return [
                'medicine'        => $medicine,
                'quantity'        => $qty,
                'line_total_paise'=> $medicine->price_paise * $qty,
            ];
        })->filter()->values();
    }

    /**
     * Return lines and auto-fix quantities that exceed current stock.
     * Returns ['lines' => Collection, 'removed' => [], 'clamped' => []]
     */
    public function linesWithStockCheck(): array
    {
        $ids = array_keys($this->items());
        if ($ids === []) {
            return ['lines' => collect(), 'removed' => [], 'clamped' => []];
        }

        $medicines = Medicine::query()->whereIn('id', $ids)->where('is_active', true)->get()->keyBy('id');
        $cart      = $this->items();
        $removed   = [];
        $clamped   = [];

        foreach ($cart as $id => $qty) {
            $medicine = $medicines->get($id);
            if (! $medicine) {
                unset($cart[$id]);
                continue;
            }
            if ($medicine->stock <= 0) {
                $removed[] = $medicine->name;
                unset($cart[$id]);
            } elseif ($qty > $medicine->stock) {
                $clamped[] = ['name' => $medicine->name, 'old' => $qty, 'new' => $medicine->stock];
                $cart[$id] = $medicine->stock;
            }
        }

        // Persist corrected cart back to session
        Session::put(self::SESSION_KEY, $cart);

        $lines = collect($cart)->map(function (int $qty, int $id) use ($medicines) {
            $medicine = $medicines->get($id);
            if (! $medicine) return null;
            return [
                'medicine'        => $medicine,
                'quantity'        => $qty,
                'line_total_paise'=> $medicine->price_paise * $qty,
            ];
        })->filter()->values();

        return ['lines' => $lines, 'removed' => $removed, 'clamped' => $clamped];
    }

    public function subtotalPaise(): int
    {
        return (int) $this->lines()->sum('line_total_paise');
    }

    public function quantity(int $medicineId): int
    {
        return $this->items()[$medicineId] ?? 0;
    }
}
