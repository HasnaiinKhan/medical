<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PinCode;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private const FREE_DELIVERY_ABOVE_PAISE = 500_00;

    private const DELIVERY_FEE_PAISE = 40_00;

    public function __construct(
        private CartService $cart
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $lines = $this->cart->lines();
        $subtotalPaise = $this->cart->subtotalPaise();
        $deliveryFeePaise = $subtotalPaise >= self::FREE_DELIVERY_ABOVE_PAISE ? 0 : self::DELIVERY_FEE_PAISE;
        $totalPaise = $subtotalPaise + $deliveryFeePaise;

        return view('checkout.index', compact('lines', 'subtotalPaise', 'deliveryFeePaise', 'totalPaise'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'delivery_pin' => ['required', 'regex:/^[0-9]{6}$/'],
        ]);

        $pin = PinCode::query()->where('code', $data['delivery_pin'])->first();

        if (! $pin) {
            return back()->withInput()->withErrors(['delivery_pin' => 'Enter a valid Ahmedabad-area pin code from our list.']);
        }

        $lines = $this->cart->lines();
        $subtotalPaise = $this->cart->subtotalPaise();
        $deliveryFeePaise = $subtotalPaise >= self::FREE_DELIVERY_ABOVE_PAISE ? 0 : self::DELIVERY_FEE_PAISE;
        $totalPaise = $subtotalPaise + $deliveryFeePaise;

        $order = DB::transaction(function () use ($data, $pin, $lines, $subtotalPaise, $deliveryFeePaise, $totalPaise) {
            $order = Order::query()->create([
                'user_id'            => Auth::id(),
                'order_number'       => 'AHM-'.strtoupper(Str::random(10)),
                'customer_name'      => $data['customer_name'],
                'customer_phone'     => $data['customer_phone'],
                'delivery_pin'       => $pin->code,
                'delivery_area'      => $pin->area,
                'address_line1'      => $data['address_line1'],
                'address_line2'      => $data['address_line2'] ?? null,
                'subtotal_paise'     => $subtotalPaise,
                'delivery_fee_paise' => $deliveryFeePaise,
                'total_paise'        => $totalPaise,
                'payment_method'     => 'cod',
                'status'             => 'placed',
            ]);

            foreach ($lines as $line) {
                /** @var \App\Models\Medicine $medicine */
                $medicine = $line['medicine'];
                $qty = (int) $line['quantity'];
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $qty,
                    'unit_price_paise' => $medicine->price_paise,
                    'line_total_paise' => $medicine->price_paise * $qty,
                    'medicine_name_snapshot' => $medicine->name,
                ]);
            }

            return $order;
        });

        $this->cart->clear();
        $request->session()->put('delivery_pin', $pin->code);
        $request->session()->put('delivery_area', $pin->area);

        return redirect()
            ->route('checkout.thankyou', $order)
            ->with('status', 'Order placed. Pay cash on delivery.');
    }

    public function thankyou(Order $order): View
    {
        $order->load('items.medicine');

        return view('checkout.thankyou', compact('order'));
    }
}
