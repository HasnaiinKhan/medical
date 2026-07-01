<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PinCode;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\DeliveryFeeService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayController extends Controller
{
    public function __construct(private CartService $cart) {}

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 - Show checkout form (same as COD, payment_method selector added)
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): View|RedirectResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $lines            = $this->cart->lines();
        $subtotalPaise    = $this->cart->subtotalPaise();

        // Use the session pin if available, otherwise default to a near zone for display
        $sessionPin       = session('delivery_pin', '380015');
        $deliveryFeePaise = DeliveryFeeService::calculate($sessionPin, $subtotalPaise);
        $totalPaise       = $subtotalPaise + $deliveryFeePaise;
        $razorpayKeyId    = config('razorpay.key_id');
        $savedAddresses   = Auth::user()->addresses()->get();

        $codEnabled    = \App\Models\Setting::get('payment_cod_enabled',    '1') === '1';
        $onlineEnabled = \App\Models\Setting::get('payment_online_enabled', '1') === '1';

        return view('checkout.index', compact(
            'lines', 'subtotalPaise', 'deliveryFeePaise', 'totalPaise',
            'razorpayKeyId', 'savedAddresses', 'codEnabled', 'onlineEnabled'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 - Validate form + create Razorpay order → return JSON to JS
    // ─────────────────────────────────────────────────────────────────────────

    public function createOrder(Request $request): JsonResponse
    {
        if ($this->cart->lines()->isEmpty()) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        // ── Enforce admin payment method settings ─────────────────────────────
        $codEnabled    = \App\Models\Setting::get('payment_cod_enabled',    '1') === '1';
        $onlineEnabled = \App\Models\Setting::get('payment_online_enabled', '1') === '1';

        $requestedMethod = $request->input('payment_method');
        if ($requestedMethod === 'cod' && ! $codEnabled) {
            return response()->json(['error' => 'Cash on Delivery is not available at the moment.'], 422);
        }
        if ($requestedMethod === 'online' && ! $onlineEnabled) {
            return response()->json(['error' => 'Online payment is not available at the moment.'], 422);
        }

        if (! $this->hasRazorpayCredentials()) {
            return response()->json([
                'error' => 'Online payment is temporarily unavailable. Please contact support.',
            ], 503);
        }

        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'address_line1'  => ['required', 'string', 'max:255'],
            'address_line2'  => ['nullable', 'string', 'max:255'],
            'delivery_pin'   => ['required', 'regex:/^[0-9]{6}$/'],
            'payment_method' => ['required', 'in:cod,online'],
            'save_address'   => ['nullable', 'boolean'],
            'address_label'  => ['nullable', 'string', 'max:30'],
            'address_id'     => ['nullable', 'integer'],   // if user picked a saved address
            'edit_address_id' => ['nullable', 'integer'],
        ]);

        $pin = PinCode::query()->where('code', $data['delivery_pin'])->first();
        if (! $pin) {
            return response()->json(['errors' => ['delivery_pin' => ['Enter a valid Ahmedabad-area pin code.']]], 422);
        }

        $subtotalPaise    = $this->cart->subtotalPaise();
        $deliveryFeePaise = DeliveryFeeService::calculate($data['delivery_pin'], $subtotalPaise);
        $totalPaise       = $subtotalPaise + $deliveryFeePaise;

        // ── Stock validation before placing order ─────────────────────────────
        foreach ($this->cart->lines() as $line) {
            $medicine = $line['medicine'];
            $qty      = $line['quantity'];
            if ($medicine->stock < $qty) {
                $available = $medicine->stock;
                $msg = $available <= 0
                    ? "{$medicine->name} is out of stock. Please remove it from your cart before proceeding."
                    : "Only {$available} unit(s) of {$medicine->name} are available. Please update your cart quantity.";
                return response()->json(['error' => $msg, 'out_of_stock' => true], 422);
            }
        }

        // ── Save address if requested ─────────────────────────────────────────
        if (!empty($data['save_address'])) {
            $this->saveAddress($data, $pin);
        }

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
    // STEP 3 - Verify Razorpay signature after payment success
    // ─────────────────────────────────────────────────────────────────────────

    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
            'order_id'            => ['required', 'integer'],
        ]);

        if (! $this->hasRazorpayCredentials()) {
            return response()->json([
                'ok' => false,
                'message' => 'Online payment verification is temporarily unavailable. Please contact support.',
            ], 503);
        }

        $requestedOrder = Order::findOrFail($request->order_id);

        // This code finds the correct order for the logged-in user, ensures the Razorpay order exists, prevents duplicate payment processing, and redirects the user appropriately if the payment has already been verified.
        
        abort_unless($requestedOrder->user_id === Auth::id(), 403);

        $order = Order::query()
            ->where('user_id', Auth::id())
            ->where('razorpay_order_id', $request->razorpay_order_id)
            ->first();

        if (! $order) {
            Log::warning('Razorpay order not found for user during verification', [
                'requested_order_id' => $requestedOrder->id,
                'received_razorpay_order_id' => $request->razorpay_order_id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Payment verification failed for this order. Please try again from checkout.',
            ], 422);
        }

        if ($order->id !== $requestedOrder->id) {
            Log::info('Resolved verification against matching Razorpay order for same user', [
                'requested_order_id' => $requestedOrder->id,
                'resolved_order_id' => $order->id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'user_id'  => Auth::id(),
            ]);
        }

        if (
            $order->payment_status === 'paid'
            && $order->razorpay_payment_id === $request->razorpay_payment_id
        ) {
            return response()->json([
                'ok' => $order->status === 'confirmed',
                'message' => $order->status === 'payment_review'
                    ? 'Payment is already captured for this order and is currently under manual review.'
                    : null,
                'redirect_url' => $order->status === 'confirmed'
                    ? route('checkout.thankyou', $order)
                    : route('orders.show', $order),
            ], $order->status === 'confirmed' ? 200 : 409);
        }

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

        //This code securely verifies the payment with Razorpay, ensures the payment belongs to the correct order, confirms the amount is correct, checks medicine stock availability, deducts stock, and prevents duplicate payment processing.

        try {
            DB::transaction(function () use ($api, $order, $request) {
                /** @var Order $lockedOrder */
                $lockedOrder = Order::query()
                    ->whereKey($order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $lockedOrder->payment_status === 'paid'
                    && $lockedOrder->razorpay_payment_id === $request->razorpay_payment_id
                ) {
                    return;
                }

                if (! in_array($lockedOrder->status, ['placed', 'payment_failed', 'payment_review'], true)) {
                    throw IlluminateValidationException::withMessages([
                        'order' => ['This order is no longer eligible for payment confirmation.'],
                    ]);
                }

                $payment = $api->payment->fetch($request->razorpay_payment_id);
                $paymentData = $payment->toArray();

                if (($paymentData['order_id'] ?? null) !== $lockedOrder->razorpay_order_id) {
                    throw IlluminateValidationException::withMessages([
                        'payment' => ['Payment does not belong to this order.'],
                    ]);
                }

                if ((int) ($paymentData['amount'] ?? 0) !== (int) $lockedOrder->total_paise) {
                    throw IlluminateValidationException::withMessages([
                        'payment' => ['Payment amount does not match the order total.'],
                    ]);
                }

                if (! in_array($paymentData['status'] ?? '', ['captured', 'authorized'], true)) {
                    throw IlluminateValidationException::withMessages([
                        'payment' => ['Payment is not in a capturable state.'],
                    ]);
                }

                $lockedOrder->loadMissing('items.medicine');

                foreach ($lockedOrder->items as $item) {
                    if (! $item->medicine) {
                        continue;
                    }

                    if (! $item->medicine->takeStock($item->quantity)) {
                        throw ValidationException::withMessages([
                            'stock' => ["{$item->medicine->name} is no longer available in the requested quantity."],
                        ]);
                    }
                }

                $lockedOrder->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature'  => $request->razorpay_signature,
                    'payment_status'      => 'paid',
                    'status'              => 'confirmed',
                ]);
            });

            
        } catch (ValidationException $e) {
            $messages = $e->errors();
            $stockConflict = array_key_exists('stock', $messages);

            Log::critical('Razorpay verification could not confirm order', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'errors' => $messages,
            ]);

            if ($stockConflict) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'payment_review',
                ]);

                return response()->json([
                    'ok' => false,
                    'message' => 'Payment was received, but stock changed before confirmation. Please contact support.',
                ], 409);
            }

            $order->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed',
            ]);

            return response()->json([
                'ok' => false,
                'message' => collect($messages)->flatten()->first() ?? 'Payment verification failed. Please contact support.',
            ], 422);
        }

        $order->refresh();
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
        $order->load('items.medicine');

        return view('checkout.thankyou', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helper - write order + items to DB
    // ─────────────────────────────────────────────────────────────────────────

    // ── Save or update a user address ────────────────────────────────────────
    private function saveAddress(array $data, PinCode $pin): void
    {
        $user  = Auth::user();
        $label = trim($data['address_label'] ?? 'Home') ?: 'Home';

        // Check if an identical address already exists - avoid duplicates
        $existing = $user->addresses()
            ->where('address_line1', $data['address_line1'])
            ->where('delivery_pin',  $data['delivery_pin'])
            ->where('customer_phone', $data['customer_phone'])
            ->first();

        if ($existing) {
            // Just bump the updated_at so it floats to the top
            $existing->touch();
            return;
        }

        // If this is the user's first address, make it default
        $isFirst = $user->addresses()->count() === 0;

        UserAddress::create([
            'user_id'        => $user->id,
            'label'          => $label,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'delivery_pin'   => $data['delivery_pin'],
            'delivery_area'  => $pin->area,
            'address_line1'  => $data['address_line1'],
            'address_line2'  => $data['address_line2'] ?? null,
            'is_default'     => $isFirst,
        ]);
    }

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

                // Decrement stock for COD immediately (order is confirmed intent)
                // For online payments, stock is decremented after payment verification
                if ($paymentMethod === 'cod') {
                    $medicine->decrementStock($qty);
                }
            }

            return $order;
        });
    }

    public function updateAddress(Request $request)
{
    $request->validate([
        'address_id'      => ['required', 'integer'],
        'customer_name'   => ['required', 'string', 'max:120'],
        'customer_phone'  => ['required', 'regex:/^[6-9][0-9]{9}$/'],
        'delivery_pin'    => ['required', 'regex:/^[0-9]{6}$/'],
        'address_line1'   => ['required', 'string', 'max:255'],
        'address_line2'   => ['nullable', 'string', 'max:255'],
    ]);

    $address = UserAddress::where('id', $request->address_id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $pin = PinCode::where('code', $request->delivery_pin)->first();
    if (! $pin) {
        return response()->json([
            'success' => false,
            'message' => 'Enter a valid Ahmedabad-area pin code.',
        ], 422);
    }

    $address->update([
        'customer_name'  => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'delivery_pin'   => $request->delivery_pin,
        'delivery_area'  => $pin->area,
        'address_line1'  => $request->address_line1,
        'address_line2'  => $request->address_line2,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Address updated successfully'
    ]);
}

    private function hasRazorpayCredentials(): bool
    {
        return filled(config('razorpay.key_id')) && filled(config('razorpay.key_secret'));
    }
}
