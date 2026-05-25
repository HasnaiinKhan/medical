<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PinCode;
use App\Services\CartService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayController extends Controller
{
    private const FREE_DELIVERY_ABOVE_PAISE = 500_00;
    private const DELIVERY_FEE_PAISE        = 40_00;

    public function __construct(private CartService $cart) {}

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Show checkout form (same as COD, payment_method selector added)
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): View|RedirectResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $lines            = $this->cart->lines();
        $subtotalPaise    = $this->cart->subtotalPaise();
        $deliveryFeePaise = $subtotalPaise >= self::FREE_DELIVERY_ABOVE_PAISE ? 0 : self::DELIVERY_FEE_PAISE;
        $totalPaise       = $subtotalPaise + $deliveryFeePaise;
        $razorpayKeyId    = config('razorpay.key_id');

        return view('checkout.index', compact(
            'lines', 'subtotalPaise', 'deliveryFeePaise', 'totalPaise', 'razorpayKeyId'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — Validate form + create Razorpay order → return JSON to JS
    // ─────────────────────────────────────────────────────────────────────────

    public function createOrder(Request $request): JsonResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'address_line1'  => ['required', 'string', 'max:255'],
            'address_line2'  => ['nullable', 'string', 'max:255'],
            'delivery_pin'   => ['required', 'regex:/^[0-9]{6}$/'],
            'payment_method' => ['required', 'in:cod,online'],
        ]);

        $pin = PinCode::query()->where('code', $data['delivery_pin'])->first();
        if (! $pin) {
            return response()->json(['errors' => ['delivery_pin' => ['Enter a valid Ahmedabad-area pin code.']]], 422);
        }

        $subtotalPaise    = $this->cart->subtotalPaise();
        $deliveryFeePaise = $subtotalPaise >= self::FREE_DELIVERY_ABOVE_PAISE ? 0 : self::DELIVERY_FEE_PAISE;
        $totalPaise       = $subtotalPaise + $deliveryFeePaise;

        // ── COD path ─────────────────────────────────────────────────────────
        if ($data['payment_method'] === 'cod') {
            $order = $this->persistOrder($data, $pin, $totalPaise, $subtotalPaise, $deliveryFeePaise, 'cod', 'pending');
            $this->cart->clear();
            $request->session()->put('delivery_pin',  $pin->code);
            $request->session()->put('delivery_area', $pin->area);

            // Send order confirmation email to customer
            try {
                $order->load('items');
                Mail::to(Auth::user()->email)->send(new OrderPlaced($order));
            } catch (\Throwable $e) {
                Log::error('OrderPlaced mail failed: ' . $e->getMessage());
            }

            // Notify admin
            NotificationService::notifyAdmin($order);

            return response()->json([
                'method'      => 'cod',
                'redirect_url' => route('checkout.thankyou', $order),
            ]);
        }

        // ── Online payment path ───────────────────────────────────────────────
        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        $rzpOrder = $api->order->create([
            'amount'          => $totalPaise,          // Razorpay expects paise
            'currency'        => 'INR',
            'receipt'         => 'AHM-'.strtoupper(Str::random(8)),
            'payment_capture' => 1,
        ]);

        // Persist a pending order so we can verify later
        $order = $this->persistOrder(
            $data, $pin, $totalPaise, $subtotalPaise, $deliveryFeePaise,
            'online', 'pending', $rzpOrder->id
        );

        $request->session()->put('delivery_pin',  $pin->code);
        $request->session()->put('delivery_area', $pin->area);

        return response()->json([
            'method'           => 'online',
            'razorpay_order_id' => $rzpOrder->id,
            'amount'           => $totalPaise,
            'currency'         => 'INR',
            'order_id'         => $order->id,
            'name'             => config('app.name'),
            'description'      => 'Medicine order '.$order->order_number,
            'prefill'          => [
                'name'    => $data['customer_name'],
                'contact' => '91'.$data['customer_phone'],
                'email'   => Auth::user()->email,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Verify Razorpay signature after payment success
    // ─────────────────────────────────────────────────────────────────────────

    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
            'order_id'            => ['required', 'integer'],
        ]);

        $order = Order::findOrFail($request->order_id);

        // Ensure this order belongs to the logged-in user
        abort_unless($order->user_id === Auth::id(), 403);

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature mismatch', [
                'order_id'   => $order->id,
                'rzp_order'  => $request->razorpay_order_id,
                'exception'  => $e->getMessage(),
            ]);

            $order->update(['payment_status' => 'failed', 'status' => 'payment_failed']);

            return response()->json(['ok' => false, 'message' => 'Payment verification failed. Please contact support.'], 422);
        }

        // Signature valid — mark as paid
        $order->update([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature'  => $request->razorpay_signature,
            'payment_status'      => 'paid',
            'status'              => 'confirmed',
        ]);

        $this->cart->clear();

        // Send order confirmation email
        try {
            $order->load('items');
            Mail::to(Auth::user()->email)->send(new OrderPlaced($order));
        } catch (\Throwable $e) {
            Log::error('OrderPlaced mail failed: ' . $e->getMessage());
        }

        // Notify admin
        NotificationService::notifyAdmin($order);

        return response()->json([
            'ok'           => true,
            'redirect_url' => route('checkout.thankyou', $order),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Thank-you page (shared with COD)
    // ─────────────────────────────────────────────────────────────────────────

    public function thankyou(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items');

        return view('checkout.thankyou', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helper — write order + items to DB
    // ─────────────────────────────────────────────────────────────────────────

    private function persistOrder(
        array   $data,
        PinCode $pin,
        int     $totalPaise,
        int     $subtotalPaise,
        int     $deliveryFeePaise,
        string  $paymentMethod,
        string  $paymentStatus,
        ?string $razorpayOrderId = null,
    ): Order {
        return DB::transaction(function () use (
            $data, $pin, $totalPaise, $subtotalPaise, $deliveryFeePaise,
            $paymentMethod, $paymentStatus, $razorpayOrderId
        ) {
            $order = Order::create([
                'user_id'             => Auth::id(),
                'order_number'        => 'AHM-'.strtoupper(Str::random(10)),
                'customer_name'       => $data['customer_name'],
                'customer_phone'      => $data['customer_phone'],
                'delivery_pin'        => $pin->code,
                'delivery_area'       => $pin->area,
                'address_line1'       => $data['address_line1'],
                'address_line2'       => $data['address_line2'] ?? null,
                'subtotal_paise'      => $subtotalPaise,
                'delivery_fee_paise'  => $deliveryFeePaise,
                'total_paise'         => $totalPaise,
                'payment_method'      => $paymentMethod,
                'payment_status'      => $paymentStatus,
                'razorpay_order_id'   => $razorpayOrderId,
                'status'              => 'placed',
            ]);

            foreach ($this->cart->lines() as $line) {
                /** @var \App\Models\Medicine $medicine */
                $medicine = $line['medicine'];
                $qty      = (int) $line['quantity'];

                OrderItem::create([
                    'order_id'               => $order->id,
                    'medicine_id'            => $medicine->id,
                    'quantity'               => $qty,
                    'unit_price_paise'       => $medicine->price_paise,
                    'line_total_paise'       => $medicine->price_paise * $qty,
                    'medicine_name_snapshot' => $medicine->name,
                ]);
            }

            return $order;
        });
    }
}
