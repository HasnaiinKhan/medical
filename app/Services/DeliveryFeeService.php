<?php

namespace App\Services;

/**
 * Distance-based delivery fee calculator for Ahmedabad pincodes.
 *
 * Zones (distance from store):
 *   0–5 km   → FREE if order subtotal ≥ ₹500, else ₹40
 *   5–10 km  → ₹40 flat
 *   10–15 km → ₹50 flat
 *   15+ km   → ₹50 flat
 *
 * Unknown / unlisted pincodes default to the 10–15 km zone (₹50).
 */
class DeliveryFeeService
{
    // ── Zone definitions ──────────────────────────────────────────────────────

    private const ZONE_0_5 = [
        '380051', '380055', '380015', '380007', '380006',
    ];

    private const ZONE_5_10 = [
        '380052', '380013', '380022', '380028', '380009',
        '380061', '380054', '380058', '380059', '380027', '380050',
    ];

    private const ZONE_10_15 = [
        '380001', '380002', '380004', '380005', '380008',
        '380014', '380016', '380018', '380019', '380021',
        '380023', '380024', '380026', '380060', '382481',
    ];

    private const ZONE_15_PLUS = [
        '382424',
    ];

    // ── Fee constants (in paise) ──────────────────────────────────────────────

    /** Minimum subtotal for free delivery within 0–5 km zone (₹500) */
    public const FREE_DELIVERY_MIN_PAISE = 500_00;

    public const FEE_0_5_BELOW_MIN = 40_00;   // ₹40 when subtotal < ₹500
    public const FEE_5_10          = 40_00;   // ₹40
    public const FEE_10_PLUS       = 50_00;   // ₹50

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Return the zone label for a pincode.
     * '0_5' | '5_10' | '10_15' | '15_plus' | 'unknown'
     */
    public static function zone(string $pin): string
    {
        $pin = trim($pin);
        if (in_array($pin, self::ZONE_0_5,     true)) return '0_5';
        if (in_array($pin, self::ZONE_5_10,    true)) return '5_10';
        if (in_array($pin, self::ZONE_10_15,   true)) return '10_15';
        if (in_array($pin, self::ZONE_15_PLUS, true)) return '15_plus';
        return 'unknown';
    }

    /**
     * Calculate delivery fee in paise.
     *
     * @param  string  $pin           6-digit delivery pincode
     * @param  int     $subtotalPaise Cart subtotal in paise
     * @return int                    Delivery fee in paise
     */
    public static function calculate(string $pin, int $subtotalPaise): int
    {
        $zone = self::zone($pin);

        return match($zone) {
            '0_5'  => $subtotalPaise >= self::FREE_DELIVERY_MIN_PAISE ? 0 : self::FEE_0_5_BELOW_MIN,
            '5_10' => self::FEE_5_10,
            default => self::FEE_10_PLUS, // 10_15, 15_plus, unknown
        };
    }

    /**
     * Human-readable label describing the delivery fee rule for a pincode.
     * Used in the checkout UI.
     */
    public static function label(string $pin, int $subtotalPaise): string
    {
        $zone = self::zone($pin);
        $fee  = self::calculate($pin, $subtotalPaise);

        if ($fee === 0) {
            return 'FREE delivery';
        }

        $feeRupees = number_format($fee / 100, 0);

        return match($zone) {
            '0_5'  => "₹{$feeRupees} delivery (free above ₹500)",
            '5_10' => "₹{$feeRupees} delivery (5–10 km zone)",
            default => "₹{$feeRupees} delivery (10+ km zone)",
        };
    }
}
