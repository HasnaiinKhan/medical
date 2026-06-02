@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')

{{-- Breadcrumb --}}
<nav class="mb-5 flex items-center gap-2 text-xs text-slate-500">
    <a href="{{ route('home') }}" class="hover:text-blue-700 transition-colors">Home</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('cart.index') }}" class="hover:text-blue-700 transition-colors">Cart</a>
    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-700">Checkout</span>
</nav>

{{-- Progress steps --}}
<div class="mb-8 flex items-center gap-0">
    <div class="flex items-center gap-2">
        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-700 text-xs font-bold text-white">✓</div>
        <span class="text-xs font-semibold text-slate-700">Cart</span>
    </div>
    <div class="mx-3 h-px flex-1 bg-blue-300"></div>
    <div class="flex items-center gap-2">
        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-700 text-xs font-bold text-white">2</div>
        <span class="text-xs font-semibold text-slate-700">Delivery & Payment</span>
    </div>
    <div class="mx-3 h-px flex-1 bg-slate-200"></div>
    <div class="flex items-center gap-2">
        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-500">3</div>
        <span class="text-xs font-medium text-slate-400">Confirmation</span>
    </div>
</div>

<div class="grid gap-8 lg:grid-cols-5">

{{-- ===== CHECKOUT FORM ===== --}}
<div class="lg:col-span-3">
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
<h1 class="text-xl font-bold text-slate-900 mb-6">Delivery Details</h1>

<form id="checkout-form" class="space-y-5">
@csrf
<input type="hidden" id="selected_address_id" name="address_id" value="">

{{-- ── Saved addresses picker ── --}}
@if($savedAddresses->isNotEmpty())
<div class="mb-2">
    <label class="block text-sm font-semibold text-slate-700 mb-2">📍 Saved Addresses</label>
    <div class="space-y-2" id="saved-address-list">
        @foreach($savedAddresses as $addr)
        <div class="saved-addr-card flex items-start gap-3 rounded-xl border-2 border-slate-200 bg-white p-3 cursor-pointer transition-all hover:border-blue-400 {{ $loop->first ? 'border-blue-500 bg-blue-50' : '' }}"
             data-id="{{ $addr->id }}"
             data-name="{{ $addr->customer_name }}"
             data-phone="{{ $addr->customer_phone }}"
             data-pin="{{ $addr->delivery_pin }}"
             data-area="{{ $addr->delivery_area }}"
             data-line1="{{ $addr->address_line1 }}"
             data-line2="{{ $addr->address_line2 ?? '' }}">
            <div class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border-2 addr-radio-dot transition-all {{ $loop->first ? 'border-blue-600 bg-blue-600' : 'border-slate-300' }}">
                @if($loop->first)
                    <div class="h-2 w-2 rounded-full bg-white"></div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">{{ $addr->label }}</span>
                    @if($addr->is_default)<span class="text-xs text-slate-400">Default</span>@endif
                </div>
                <p class="text-sm font-semibold text-slate-800 mt-1">{{ $addr->customer_name }} · {{ $addr->customer_phone }}</p>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $addr->summary() }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <button type="button" id="use-new-address-btn"
            class="mt-3 text-xs font-semibold text-blue-700 hover:underline flex items-center gap-1">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Use a different address
    </button>
</div>
@endif

{{-- New address fields (hidden when saved address is selected) --}}
<div id="new-address-form" class="{{ $savedAddresses->isNotEmpty() ? 'hidden' : '' }} space-y-5">

    {{-- Name --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
        <input name="customer_name" id="customer_name"
               value="{{ old('customer_name', auth()->user()->name) }}" required
               placeholder="e.g. Rahul Sharma"
               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-colors">
        <p class="field-error hidden mt-1.5 text-xs text-red-600" id="err-customer_name"></p>
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mobile Number <span class="text-red-500">*</span></label>
        <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 transition-colors">
            <span class="flex items-center bg-slate-50 px-3 text-sm font-medium text-slate-600 border-r border-slate-200">+91</span>
            <input name="customer_phone" id="customer_phone"
                   value="{{ old('customer_phone') }}" required maxlength="10" inputmode="numeric"
                   placeholder="7600264090"
                   class="flex-1 px-4 py-3 text-sm focus:outline-none">
        </div>
        <p class="field-error hidden mt-1.5 text-xs text-red-600" id="err-customer_phone"></p>
    </div>

    {{-- Pincode --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Delivery Pincode <span class="text-red-500">*</span></label>
        <div class="flex gap-2">
            <input name="delivery_pin" id="delivery_pin"
                   value="{{ old('delivery_pin', session('delivery_pin')) }}"
                   required maxlength="6" inputmode="numeric" placeholder="e.g. 380028"
                   class="w-40 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-colors">
            <div id="checkout-pin-status" class="flex items-center text-sm"></div>
        </div>
        <p class="mt-1.5 text-xs text-slate-500">
            Try:
            <button type="button" onclick="fillPin('380028')" class="font-semibold text-blue-700 hover:underline">380028</button>,
            <button type="button" onclick="fillPin('380009')" class="font-semibold text-blue-700 hover:underline">380009</button>,
            <button type="button" onclick="fillPin('380015')" class="font-semibold text-blue-700 hover:underline">380015</button>
        </p>
        <p class="field-error hidden mt-1.5 text-xs text-red-600" id="err-delivery_pin"></p>
    </div>

    {{-- Address Line 1 --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Address Line 1 <span class="text-red-500">*</span></label>
        <input name="address_line1" id="address_line1"
               value="{{ old('address_line1') }}" required
               placeholder="House/Flat no., Building name, Street"
               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-colors">
        <p class="field-error hidden mt-1.5 text-xs text-red-600" id="err-address_line1"></p>
    </div>

    {{-- Address Line 2 --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Address Line 2 <span class="text-xs font-normal text-slate-400">(optional)</span></label>
        <input name="address_line2" id="address_line2"
               value="{{ old('address_line2') }}"
               placeholder="Area, Landmark (optional)"
               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-colors">
    </div>

    {{-- Save address checkbox --}}
    <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
        <input type="checkbox" id="save_address" name="save_address" value="1"
               class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
        <div class="flex-1">
            <label for="save_address" class="text-sm font-semibold text-slate-700 cursor-pointer">Save this address for next time</label>
            <div id="save-address-label-row" class="hidden mt-2">
                <label class="text-xs text-slate-500 mb-1 block">Label (e.g. Home, Work)</label>
                <div class="flex gap-2">
                    @foreach(['Home','Work','Other'] as $lbl)
                    <button type="button" onclick="setAddrLabel('{{ $lbl }}')"
                            class="addr-label-btn text-xs font-semibold px-3 py-1 rounded-full border border-slate-200 bg-white text-slate-600 hover:border-blue-500 hover:text-blue-700 transition-all">{{ $lbl }}</button>
                    @endforeach
                    <input type="text" id="address_label" name="address_label" value="Home"
                           maxlength="30" placeholder="Custom"
                           class="flex-1 rounded-lg border border-slate-200 px-2 py-1 text-xs focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>

</div>{{-- end #new-address-form --}}

{{-- ===== PAYMENT METHOD ===== --}}
<div class="pt-2">
    <label class="block text-sm font-semibold text-slate-700 mb-3">Payment Method <span class="text-red-500">*</span></label>
    <div class="grid grid-cols-2 gap-3">
        {{-- Online --}}
        <label id="label-online" class="payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 border-blue-600 bg-blue-50 p-4 transition-all">
            <input type="radio" name="payment_method" value="online" checked class="sr-only">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-700 text-white text-xs font-black">₹</div>
                    <span class="text-sm font-bold text-slate-900">Online Payment</span>
                </div>
                <div class="h-4 w-4 rounded-full border-2 border-blue-700 bg-blue-700 flex items-center justify-center" id="dot-online">
                    <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                </div>
            </div>
            <p class="text-xs text-slate-600">Pay securely via UPI, Cards, Net Banking</p>
            <div class="flex items-center gap-1.5 mt-1">
                <img src="https://razorpay.com/favicon.ico" alt="Razorpay" class="h-4 w-4 rounded">
                <span class="text-xs font-semibold text-slate-500">Powered by Razorpay</span>
            </div>
        </label>
        {{-- COD --}}
        <label id="label-cod" class="payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 border-slate-200 bg-white p-4 transition-all">
            <input type="radio" name="payment_method" value="cod" class="sr-only">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 overflow-hidden">
                        <img src="{{ asset('images/CashonDeliveryicon.png') }}" alt="COD" class="h-7 w-7 object-contain">
                    </div>
                    <span class="text-sm font-bold text-slate-900">Cash on Delivery</span>
                </div>
                <div class="h-4 w-4 rounded-full border-2 border-slate-300 bg-white" id="dot-cod"></div>
            </div>
            <p class="text-xs text-slate-600">Pay in cash when your order arrives</p>
            <p class="text-xs text-amber-600 font-medium mt-1">No extra charges</p>
        </label>
    </div>
</div>

{{-- General error --}}
<div id="form-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

{{-- Submit --}}
<div class="pt-2">
    <button type="submit" id="place-order-btn"
            class="btn-primary w-full rounded-xl py-3.5 text-sm font-bold text-white shadow-md flex items-center justify-center gap-2">
        <span id="btn-text">Pay & Place Order</span>
        <svg id="btn-spinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </button>
</div>

</form>
</div>{{-- end card --}}
</div>{{-- end lg:col-span-3 --}}

{{-- ===== ORDER SUMMARY ===== --}}
<div class="lg:col-span-2">
<div class="sticky top-36 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-900 mb-4">Order Summary</h2>
    <ul class="space-y-3 max-h-64 overflow-y-auto pr-1">
        @foreach ($lines as $line)
            @php($m = $line['medicine'])
            <li class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-black text-blue-700">
                    {{ strtoupper(substr($m->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 line-clamp-1">{{ $m->name }}</p>
                    <p class="text-xs text-slate-500">Qty: {{ $line['quantity'] }}</p>
                </div>
                <span class="text-xs font-bold text-slate-900 flex-shrink-0">
                    ₹{{ number_format($line['line_total_paise'] / 100, 2) }}
                </span>
            </li>
        @endforeach
    </ul>
    <div class="mt-4 border-t border-slate-200 pt-4 space-y-2 text-sm">
        <div class="flex justify-between text-slate-600">
            <span>Subtotal</span>
            <span class="font-medium text-slate-900">₹{{ number_format($subtotalPaise / 100, 2) }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
            <span>Delivery</span>
            @if ($deliveryFeePaise === 0)
                <span class="font-semibold text-blue-700">FREE</span>
            @else
                <span class="font-medium text-slate-900">₹{{ number_format($deliveryFeePaise / 100, 2) }}</span>
            @endif
        </div>
    </div>
    <div class="mt-3 border-t border-slate-200 pt-3">
        <div class="flex justify-between text-base font-bold text-slate-900">
            <span>Total</span>
            <span>₹{{ number_format($totalPaise / 100, 2) }}</span>
        </div>
    </div>
    <div id="summary-payment-badge" class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-3 flex items-center gap-2">
        <span class="text-lg">💳</span>
        <p class="text-xs font-medium text-slate-700">Secure online payment via Razorpay</p>
    </div>
</div>
</div>{{-- end lg:col-span-2 --}}

</div>{{-- end grid --}}

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const RZP_KEY = @json($razorpayKeyId);

// ── Saved address selection ───────────────────────────────────────────────────
const newAddressForm   = document.getElementById('new-address-form');
const useNewAddrBtn    = document.getElementById('use-new-address-btn');
const selectedAddrId   = document.getElementById('selected_address_id');

function selectSavedAddress(card) {
    // Highlight selected card
    document.querySelectorAll('.saved-addr-card').forEach(c => {
        c.classList.remove('border-blue-500', 'bg-blue-50');
        c.classList.add('border-slate-200', 'bg-white');
        const dot = c.querySelector('.addr-radio-dot');
        dot.classList.remove('border-blue-600', 'bg-blue-600');
        dot.classList.add('border-slate-300');
        dot.innerHTML = '';
    });
    card.classList.add('border-blue-500', 'bg-blue-50');
    card.classList.remove('border-slate-200', 'bg-white');
    const dot = card.querySelector('.addr-radio-dot');
    dot.classList.add('border-blue-600', 'bg-blue-600');
    dot.classList.remove('border-slate-300');
    dot.innerHTML = '<div class="h-2 w-2 rounded-full bg-white"></div>';

    // Fill hidden fields + form fields (used by submit)
    selectedAddrId.value = card.dataset.id;
    document.getElementById('customer_name').value  = card.dataset.name;
    document.getElementById('customer_phone').value = card.dataset.phone;
    document.getElementById('delivery_pin').value   = card.dataset.pin;
    document.getElementById('address_line1').value  = card.dataset.line1;
    document.getElementById('address_line2').value  = card.dataset.line2;
    checkoutPinLookup();

    // Hide new address form
    newAddressForm.classList.add('hidden');
}

// Auto-select first saved address on load
const firstCard = document.querySelector('.saved-addr-card');
if (firstCard) selectSavedAddress(firstCard);

document.querySelectorAll('.saved-addr-card').forEach(card => {
    card.addEventListener('click', () => selectSavedAddress(card));
});

if (useNewAddrBtn) {
    useNewAddrBtn.addEventListener('click', () => {
        // Deselect all saved cards
        document.querySelectorAll('.saved-addr-card').forEach(c => {
            c.classList.remove('border-blue-500', 'bg-blue-50');
            c.classList.add('border-slate-200', 'bg-white');
            const dot = c.querySelector('.addr-radio-dot');
            dot.classList.remove('border-blue-600', 'bg-blue-600');
            dot.classList.add('border-slate-300');
            dot.innerHTML = '';
        });
        selectedAddrId.value = '';
        // Clear fields
        document.getElementById('customer_name').value  = @json(auth()->user()->name);
        document.getElementById('customer_phone').value = '';
        document.getElementById('delivery_pin').value   = '';
        document.getElementById('address_line1').value  = '';
        document.getElementById('address_line2').value  = '';
        document.getElementById('checkout-pin-status').innerHTML = '';
        newAddressForm.classList.remove('hidden');
        newAddressForm.querySelector('input, textarea')?.focus();
    });
}

// ── Save address checkbox toggle ──────────────────────────────────────────────
const saveAddrCheck    = document.getElementById('save_address');
const saveAddrLabelRow = document.getElementById('save-address-label-row');
if (saveAddrCheck) {
    saveAddrCheck.addEventListener('change', () => {
        saveAddrLabelRow.classList.toggle('hidden', !saveAddrCheck.checked);
    });
}
function setAddrLabel(lbl) {
    document.getElementById('address_label').value = lbl;
    document.querySelectorAll('.addr-label-btn').forEach(b => {
        b.classList.toggle('border-blue-500', b.textContent.trim() === lbl);
        b.classList.toggle('text-blue-700', b.textContent.trim() === lbl);
    });
}


// ── Payment method toggle ────────────────────────────────────────────────────
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isOnline = this.value === 'online';
        document.getElementById('label-online').className =
            'payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 p-4 transition-all ' +
            (isOnline ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white');
        document.getElementById('label-cod').className =
            'payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 p-4 transition-all ' +
            (!isOnline ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white');
        document.getElementById('dot-online').className =
            'h-4 w-4 rounded-full border-2 flex items-center justify-center ' +
            (isOnline ? 'border-blue-700 bg-blue-700' : 'border-slate-300 bg-white');
        document.getElementById('dot-online').innerHTML =
            isOnline ? '<div class="h-1.5 w-1.5 rounded-full bg-white"></div>' : '';
        document.getElementById('dot-cod').className =
            'h-4 w-4 rounded-full border-2 flex items-center justify-center ' +
            (!isOnline ? 'border-blue-700 bg-blue-700' : 'border-slate-300 bg-white');
        document.getElementById('dot-cod').innerHTML =
            !isOnline ? '<div class="h-1.5 w-1.5 rounded-full bg-white"></div>' : '';
        document.getElementById('btn-text').textContent =
            isOnline ? 'Pay & Place Order' : 'Place Order (COD)';
        const badge = document.getElementById('summary-payment-badge');
        badge.innerHTML = isOnline
            ? '<span class="text-lg">💳</span><p class="text-xs font-medium text-slate-700">Secure online payment via Razorpay</p>'
            : '<span class="text-lg">💵</span><p class="text-xs font-medium text-amber-800">Pay cash when your order arrives</p>';
        badge.className = 'mt-4 rounded-xl border p-3 flex items-center gap-2 ' +
            (isOnline ? 'bg-blue-50 border-blue-100' : 'bg-amber-50 border-amber-100');
    });
});

// ── Pincode inline check ─────────────────────────────────────────────────────
async function checkoutPinLookup() {
    const input  = document.getElementById('delivery_pin');
    const status = document.getElementById('checkout-pin-status');
    const pin    = (input.value || '').replace(/\D/g, '');
    if (pin.length !== 6) return;
    status.innerHTML = '<span class="text-slate-400 text-xs">Checking…</span>';
    try {
        const url = new URL(@json(route('pincode.lookup', [], false)), window.location.origin);
        url.searchParams.set('pin', pin);
        const res  = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
        const data = await res.json();
        status.innerHTML = data.ok
            ? `<span class="text-blue-700 text-xs font-semibold">✓ ${data.area}</span>`
            : `<span class="text-red-500 text-xs">✗ Not serviceable</span>`;
    } catch { status.innerHTML = ''; }
}
function fillPin(pin) {
    document.getElementById('delivery_pin').value = pin;
    checkoutPinLookup();
}
document.getElementById('delivery_pin').addEventListener('input', function () {
    if (this.value.replace(/\D/g,'').length === 6) checkoutPinLookup();
});
if (document.getElementById('delivery_pin').value.length === 6) checkoutPinLookup();

// ── Helpers ───────────────────────────────────────────────────────────────────
function setLoading(on) {
    document.getElementById('place-order-btn').disabled = on;
    document.getElementById('btn-spinner').classList.toggle('hidden', !on);
}
function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
    document.getElementById('form-error').classList.add('hidden');
}
function showErrors(errors) {
    Object.entries(errors).forEach(([field, msgs]) => {
        const el = document.getElementById('err-' + field);
        if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
    });
}
function showGeneralError(msg) {
    const el = document.getElementById('form-error');
    el.textContent = msg;
    el.classList.remove('hidden');
}

// ── Form submit ───────────────────────────────────────────────────────────────
document.getElementById('checkout-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    clearErrors();
    setLoading(true);

    const saveAddr = document.getElementById('save_address');
    const formData = {
        customer_name:  document.getElementById('customer_name').value,
        customer_phone: document.getElementById('customer_phone').value,
        delivery_pin:   document.getElementById('delivery_pin').value,
        address_line1:  document.getElementById('address_line1').value,
        address_line2:  document.getElementById('address_line2').value,
        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
        address_id:     document.getElementById('selected_address_id').value || null,
        save_address:   saveAddr && saveAddr.checked ? 1 : 0,
        address_label:  document.getElementById('address_label')?.value || 'Home',
        _token:         CSRF,
    };

    let res, data;
    try {
        res  = await fetch(@json(route('checkout.order')), {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify(formData),
        });
        data = await res.json();
    } catch {
        showGeneralError('Network error. Please try again.');
        setLoading(false);
        return;
    }

    if (res.status === 422) {
        if (data.errors) showErrors(data.errors);
        else showGeneralError(data.error || 'Please fix the errors above.');
        setLoading(false);
        return;
    }
    if (!res.ok) {
        showGeneralError(data.error || 'Something went wrong. Please try again.');
        setLoading(false);
        return;
    }

    if (data.method === 'cod') {
        window.location.href = data.redirect_url;
        return;
    }

    const options = {
        key:         RZP_KEY,
        amount:      data.amount,
        currency:    data.currency,
        name:        data.name,
        description: data.description,
        order_id:    data.razorpay_order_id,
        prefill:     data.prefill,
        theme:       { color: '#1e3a8a' },
        modal: {
            ondismiss: function () {
                showGeneralError('Payment was cancelled. You can try again.');
                setLoading(false);
            }
        },
        handler: async function (response) {
            let vRes, vData;
            try {
                vRes  = await fetch(@json(route('checkout.verify')), {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body:    JSON.stringify({
                        razorpay_order_id:   response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature:  response.razorpay_signature,
                        order_id:            data.order_id,
                    }),
                });
                vData = await vRes.json();
            } catch {
                showGeneralError('Could not verify payment. Please contact support.');
                setLoading(false);
                return;
            }
            if (vData.ok) {
                window.location.href = vData.redirect_url;
            } else {
                showGeneralError(vData.message || 'Payment verification failed.');
                setLoading(false);
            }
        },
    };

    const rzp = new Razorpay(options);
    rzp.open();
    setLoading(false);
});
</script>
@endpush

@endsection
