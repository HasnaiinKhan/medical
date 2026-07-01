@extends('layouts.shop')

@section('title', 'Order ' . $order->order_number)

@section('content')

{{-- Breadcrumb --}}
<nav class="mb-5 flex items-center gap-2 text-xs text-slate-500">
    <a href="{{ route('home') }}" class="hover:text-blue-700 transition-colors">Home</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('orders.index') }}" class="hover:text-blue-700 transition-colors">My Orders</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-700">{{ $order->order_number }}</span>
</nav>

<div class="mx-auto max-w-3xl">

    {{-- Status banner --}}
    @php
        $statusConfig = [
            'placed'    => ['color' => 'from-blue-500 to-blue-600',    'icon' => '📋', 'msg' => 'Order received and being processed.'],
            'confirmed' => ['color' => 'from-blue-600 to-blue-700',    'icon' => '✅', 'msg' => 'Order confirmed by the pharmacy.'],
            'shipped'   => ['color' => 'from-purple-500 to-purple-600','icon' => '🚚', 'msg' => 'Your order is on the way!'],
            'delivered' => ['color' => 'from-blue-600 to-blue-700','icon' => '🎉','msg' => 'Order delivered successfully.'],
            'cancelled' => ['color' => 'from-red-500 to-red-600',      'icon' => '❌', 'msg' => 'This order has been cancelled.'],
            'payment_failed' => ['color' => 'from-red-500 to-red-600', 'icon' => '⚠️', 'msg' => 'Payment could not be verified successfully.'],
            'payment_review' => ['color' => 'from-amber-500 to-orange-600', 'icon' => '💳', 'msg' => 'Payment was received, but the order needs manual review before fulfillment.'],
        ];
        $sc = $statusConfig[$order->status] ?? ['color' => 'from-slate-500 to-slate-600', 'icon' => '📦', 'msg' => ''];
    @endphp
    <div class="mb-6 rounded-2xl bg-gradient-to-r {{ $sc['color'] }} p-5 text-white shadow-lg">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xl">{{ $sc['icon'] }}</span>
                    <span class="text-sm font-semibold uppercase tracking-wider opacity-90">{{ ucfirst($order->status) }}</span>
                </div>
                <p class="text-lg font-bold">{{ $sc['msg'] }}</p>
                <p class="mt-0.5 text-sm opacity-80">Order {{ $order->order_number }} · {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs opacity-75 mb-0.5">Total to Pay</p>
                <p class="text-2xl font-extrabold">₹{{ number_format($order->totalRupees(), 2) }}</p>
                <p class="text-xs opacity-75">Cash on Delivery</p>
            </div>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">

        {{-- Delivery info --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 mb-4">
                <svg class="h-4 w-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>Delivery Details
            </h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-500">Customer</p>
                    <p class="font-semibold text-slate-800">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Mobile</p>
                    <p class="font-semibold text-slate-800">+91 {{ $order->customer_phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Address</p>
                    <p class="font-semibold text-slate-800">{{ $order->address_line1 }}</p>
                    @if($order->address_line2)
                        <p class="text-slate-600">{{ $order->address_line2 }}</p>
                    @endif
                    <p class="text-slate-600">{{ $order->delivery_area }}, Ahmedabad - {{ $order->delivery_pin }}</p>
                </div>
            </div>
        </div>

        {{-- Payment info --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 mb-4 ">
                <svg class="h-4 w-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Payment Summary
            </h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-medium text-slate-900">₹{{ number_format($order->subtotal_paise / 100, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Delivery fee</span>
                    @if ($order->delivery_fee_paise === 0)
                    <span class="text-blue-700">FREE</span>
                    @else
                        <span class="font-medium text-slate-900">₹{{ number_format($order->delivery_fee_paise / 100, 2) }}</span>
                    @endif
                </div>
                <div class="border-t border-slate-200 pt-2 flex justify-between font-bold text-slate-900">
                    <span>Total</span>
                    <span class="text-slate-900">₹{{ number_format($order->totalRupees(), 2) }}</span>
                </div>
            </div>
            <div class="mt-4 rounded-xl bg-amber-50 border border-amber-100 p-3 flex items-center gap-2">
                @if($order->payment_method === 'online')
                    <span class="text-lg">💳</span>
                    <div>
                        <p class="text-xs font-semibold text-slate-800">Paid Online via Razorpay</p>
                        @if($order->razorpay_payment_id)
                            <p class="text-xs text-slate-600 font-mono">{{ $order->razorpay_payment_id }}</p>
                        @endif
                    </div>
                @else
                    <span class="text-lg"><i class="fa-solid fa-indian-rupee-sign fa-l"></i></span>
                    <p class="text-xs font-medium text-amber-800">Pay ₹{{ number_format($order->totalRupees(), 2) }} in cash when your order arrives.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Order items --}}
    <div class="mt-5 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
            <h2 class="text-sm font-bold text-slate-900">
                Order Items ({{ $order->items->count() }})
            </h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach ($order->items as $item)
                <div class="flex items-center gap-4 px-5 py-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-lg font-black text-blue-700">
                        {{ strtoupper(substr($item->medicine_name_snapshot, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 line-clamp-1">
                            @if($item->medicine && $item->medicine->is_active)
                                <a href="{{ route('medicines.show', $item->medicine) }}"
                                   class="hover:text-blue-700 hover:underline transition-colors">{{ $item->medicine_name_snapshot }}</a>
                            @else
                                {{ $item->medicine_name_snapshot }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            ₹{{ number_format($item->unit_price_paise / 100, 2) }} each × {{ $item->quantity }}
                        </p>
                    </div>
                    <p class="text-sm font-bold text-slate-900 flex-shrink-0">
                        ₹{{ number_format($item->line_total_paise / 100, 2) }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('orders.index') }}"
           class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            ← Back to Orders
        </a>
        {{-- Order Again: POST reorder → fills cart then redirects to /cart --}}
        <form method="POST" action="{{ route('orders.reorder', $order) }}" id="reorder-form">
            @csrf
            <button type="submit" id="reorder-btn"
                    class="btn-primary flex items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold text-white shadow-md" style="width:400px;">
                <i class="fa-solid fa-rotate-right" style="color: rgba(255, 255, 255, 1);"></i> Order Again
            </button>
        </form>
        @if($order->canRequestRefund())
            <a href="{{ route('refunds.create', $order) }}"
               id="refund-request-link"
               class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 py-3 text-sm font-semibold text-red-700 hover:bg-red-100 transition-colors shadow-sm">
                ↩ Request Refund
            </a>
        @endif
        {{-- Cancel Order — visible only while still cancellable --}}
        @if($order->canBeCancelledByUser())
            <button type="button"
                    id="user-cancel-order-btn"
                    class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-red-300 bg-red-50 py-3 text-sm font-semibold text-red-700 hover:bg-red-100 transition-colors shadow-sm">
                ✕ Cancel Order
            </button>
        @endif
    </div>

    {{-- WhatsApp help --}}
    @php
        $waPhone  = config('services.whatsapp.number', '919737275558');
        $waMsg    = "Hi! I need help with my order {$order->order_number} on Rx Plus 365.";
    @endphp
    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMsg) }}" target="_blank"
       class="mt-4 flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 py-3 text-sm font-semibold text-green-800 hover:bg-green-100 transition-colors">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/></svg>
        Need help? Chat on WhatsApp
    </a>

    {{-- Refund status (if any) --}}
    @php $latestRefund = $order->refunds()->latest()->first(); @endphp
    @if($latestRefund)
        @php [$rbadge, $rlabel] = $latestRefund->statusBadge(); @endphp
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Refund Status</h3>
                <span class="badge {{ $rbadge }}">{{ $rlabel }}</span>
            </div>
            <div class="px-5 py-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Refund #</span><span class="font-mono font-bold text-slate-800">{{ $latestRefund->refund_number }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Amount</span><span class="font-bold text-slate-900">₹{{ number_format($latestRefund->amountRupees(), 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Requested</span><span class="text-slate-700">{{ $latestRefund->created_at->format('d M Y') }}</span></div>
                @if($latestRefund->processed_at)
                    <div class="flex justify-between"><span class="text-slate-500">Processed</span><span class="text-slate-700">{{ $latestRefund->processed_at->format('d M Y') }}</span></div>
                @endif
                @if($latestRefund->status === 'processed')
                    <div class="mt-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-xs text-green-800">
                        ✅ Your refund has been processed. Amount will reflect in your account within 5–7 business days.
                    </div>
                @elseif($latestRefund->status === 'rejected')
                    <div class="mt-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-800">
                        🚫 Your refund request was rejected.
                        @if($latestRefund->admin_notes) Reason: {{ $latestRefund->admin_notes }} @endif
                        <br><br>
                        If you think we made a mistake you can contact this number:   <a href="tel:+919737275558">+919737275558</a> 

                    </div>
                @elseif($latestRefund->status === 'failed')
                    <div class="mt-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-800">
                        ⚠️ Refund processing failed. Our team has been notified and will resolve this manually.
                    </div>
                @else
                    <div class="mt-3 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-xs text-blue-800">
                        ⏳ Your refund is being processed. Expected timeline: 5–7 business days after approval.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

{{-- ── CANCEL ORDER MODAL ── --}}
@if($order->canBeCancelledByUser())
@php $isOnlinePaid = ($order->payment_method === 'online' && $order->payment_status === 'paid'); @endphp
<div id="user-cancel-modal"
     style="display:none; position:fixed; inset:0; z-index:99999;
            background:rgba(15,23,42,0.6); backdrop-filter:blur(4px);
            align-items:flex-start; justify-content:center; padding:20px; overflow-y:auto;">
    <div style="background:#fff; border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,.25);
                padding:28px 24px; width:100%; max-width:500px; margin:auto;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
            <div style="width:48px; height:48px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:22px;">✕</div>
            <div>
                <p style="margin:0; font-size:16px; font-weight:700; color:#0f172a;">Cancel Order</p>
                <p style="margin:3px 0 0; font-size:12px; color:#64748b;">Order #{{ $order->order_number }} &middot; ₹{{ number_format($order->totalRupees(), 2) }}</p>
            </div>
        </div>

        @if($isOnlinePaid)
        {{-- Online paid notice --}}
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 14px; margin-bottom:16px;">
            <p style="margin:0; font-size:13px; font-weight:700; color:#1e40af;">💳 You paid online — refund required</p>
            <p style="margin:5px 0 0; font-size:12px; color:#3b82f6; line-height:1.5;">
                Since this order was paid online, please provide your bank or UPI details. Our team will process your refund within 5–7 business days after cancellation.
            </p>
        </div>
        @else
        <p style="font-size:13px; color:#475569; line-height:1.6; margin:0 0 16px;">
            Are you sure you want to cancel this order? Items will be restocked automatically.
        </p>
        @endif

        <form method="POST" action="{{ route('orders.cancel', $order) }}" id="user-cancel-form">
            @csrf

            {{-- Reason --}}
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">Reason <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                <textarea name="cancellation_reason" rows="2" placeholder="e.g. Ordered by mistake…"
                          style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; resize:none; outline:none; box-sizing:border-box;">{{ old('cancellation_reason') }}</textarea>
            </div>

            @if($isOnlinePaid)
            {{-- ── Refund method ── --}}
            <div style="margin-bottom:16px;">
                <p style="font-size:12px; font-weight:700; color:#1e293b; margin:0 0 10px; text-transform:uppercase; letter-spacing:.04em;">Refund Method</p>
                <div style="display:flex; gap:10px; margin-bottom:14px;" id="refund-method-tabs">
                    <button type="button" data-method="bank"
                            class="refund-tab refund-tab-active"
                            style="flex:1; border:2px solid #2563eb; background:#eff6ff; color:#1e40af; border-radius:12px; padding:10px; font-size:13px; font-weight:700; cursor:pointer;">
                        🏦 Bank Transfer
                    </button>
                    <button type="button" data-method="upi"
                            class="refund-tab"
                            style="flex:1; border:2px solid #e2e8f0; background:#fff; color:#475569; border-radius:12px; padding:10px; font-size:13px; font-weight:700; cursor:pointer;">
                        📱 UPI
                    </button>
                </div>
                <input type="hidden" name="refund_method_choice" id="refund_method_choice" value="bank">

                {{-- Bank fields --}}
                <div id="bank-fields">
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">Account Holder Name <span style="color:#ef4444;">*</span></label>
                        <input name="bank_account_name" type="text" placeholder="As per bank records"
                               value="{{ old('bank_account_name') }}"
                               style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; outline:none; box-sizing:border-box;">
                        @error('bank_account_name')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">Account Number <span style="color:#ef4444;">*</span></label>
                            <input name="bank_account_number" type="text" placeholder="9–18 digits"
                                   value="{{ old('bank_account_number') }}"
                                   style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; outline:none; box-sizing:border-box;">
                            @error('bank_account_number')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">IFSC Code <span style="color:#ef4444;">*</span></label>
                            <input name="bank_ifsc" type="text" placeholder="e.g. SBIN0001234" maxlength="11"
                                   value="{{ old('bank_ifsc') }}"
                                   style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; outline:none; box-sizing:border-box; text-transform:uppercase;">
                            @error('bank_ifsc')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- UPI field --}}
                <div id="upi-fields" style="display:none;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">UPI ID <span style="color:#ef4444;">*</span></label>
                    <input name="upi_id" type="text" placeholder="e.g. yourname@upi or 9876543210@paytm"
                           value="{{ old('upi_id') }}"
                           style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; outline:none; box-sizing:border-box;">
                    <p style="font-size:11px; color:#94a3b8; margin:4px 0 0;">Format: name@bank or phone@upi</p>
                    @error('upi_id')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
                </div>
            </div>
            @endif

            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" id="user-cancel-no"
                        style="flex:1; border:1px solid #e2e8f0; background:#fff; border-radius:12px; padding:11px; font-size:13px; font-weight:600; color:#475569; cursor:pointer;">
                    No, Keep Order
                </button>
                <button type="submit" id="user-cancel-yes"
                        style="flex:1; border:none; border-radius:12px; padding:11px; font-size:13px; font-weight:700; color:#fff; background:#dc2626; cursor:pointer;">
                    {{ $isOnlinePaid ? 'Cancel & Request Refund' : 'Yes, Cancel Order' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<style>
    @keyframes ors-spin { to { transform: rotate(360deg); } }
    #order-nav-loader {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15,23,42,0.92);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 18px;
    }
    #order-nav-loader.active { display: flex; }
    .ors-ring {
        width: 56px; height: 56px;
        border: 5px solid #dbeafe;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: ors-spin .75s linear infinite;
    }
    .ors-title { font-size: 15px; font-weight: 700; color: #1e40af; }
    .ors-sub   { font-size: 12px; color: #64748b; margin-top: -10px; }
</style>

<div id="order-nav-loader" role="status" aria-label="Loading">
    <div class="ors-ring"></div>
    <p class="ors-title" id="order-nav-loader-title">Just a moment...</p>
    <p class="ors-sub"   id="order-nav-loader-sub">Please wait</p>
</div>

<script>
(function () {
    var loader      = document.getElementById('order-nav-loader');
    var loaderTitle = document.getElementById('order-nav-loader-title');
    var loaderSub   = document.getElementById('order-nav-loader-sub');

    function showLoader(title, sub) {
        if (!loader) return;
        if (loaderTitle) loaderTitle.textContent = title;
        if (loaderSub)   loaderSub.textContent   = sub;
        loader.classList.add('active');
    }

    // Cancel Order modal
    var cancelBtn   = document.getElementById('user-cancel-order-btn');
    var cancelModal = document.getElementById('user-cancel-modal');
    var cancelNo    = document.getElementById('user-cancel-no');
    var cancelForm  = document.getElementById('user-cancel-form');
    var cancelYes   = document.getElementById('user-cancel-yes');

    if (cancelBtn && cancelModal) {
        cancelBtn.addEventListener('click', function () {
            cancelModal.style.display = 'flex';
        });
        cancelNo.addEventListener('click', function () {
            cancelModal.style.display = 'none';
        });
        cancelModal.addEventListener('click', function (e) {
            if (e.target === cancelModal) cancelModal.style.display = 'none';
        });
        // Prevent double-submit
        cancelForm.addEventListener('submit', function () {
            cancelYes.disabled     = true;
            cancelYes.textContent  = 'Cancelling...';
            showLoader('Cancelling your order...', 'Please wait');
        });

        // Bank / UPI tab switching
        var tabs        = document.querySelectorAll('.refund-tab');
        var methodInput = document.getElementById('refund_method_choice');
        var bankFields  = document.getElementById('bank-fields');
        var upiFields   = document.getElementById('upi-fields');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var method = this.dataset.method;
                if (methodInput) methodInput.value = method;

                tabs.forEach(function (t) {
                    t.style.borderColor = '#e2e8f0';
                    t.style.background  = '#fff';
                    t.style.color       = '#475569';
                });
                this.style.borderColor = '#2563eb';
                this.style.background  = '#eff6ff';
                this.style.color       = '#1e40af';

                if (bankFields) bankFields.style.display = method === 'bank' ? 'block' : 'none';
                if (upiFields)  upiFields.style.display  = method === 'upi'  ? 'block' : 'none';

                document.querySelectorAll('#bank-fields input').forEach(function (i) { i.required = (method === 'bank'); });
                var upiInput = document.querySelector('#upi-fields input[name="upi_id"]');
                if (upiInput) upiInput.required = (method === 'upi');
            });
        });
    }

    // Request Refund link
    var refundLink = document.getElementById('refund-request-link');
    if (refundLink) {
        refundLink.addEventListener('click', function (e) {
            if (e.ctrlKey || e.metaKey || e.shiftKey) return;
            showLoader('Opening refund request...', 'Just a moment');
            refundLink.style.pointerEvents = 'none';
            refundLink.style.opacity = '0.7';
        });
    }

    // Order Again form
    var reorderForm = document.getElementById('reorder-form');
    var reorderBtn  = document.getElementById('reorder-btn');
    if (reorderForm) {
        reorderForm.addEventListener('submit', function () {
            showLoader('Adding items to your cart...', 'Please wait');
            if (reorderBtn) {
                reorderBtn.disabled      = true;
                reorderBtn.style.opacity = '0.75';
            }
        });
    }
})();
</script>
@endpush
