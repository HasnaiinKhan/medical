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
<input type="hidden" id="edit_address_id" name="edit_address_id">

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
                <button type="button"
        class="edit-address-btn text-xs text-blue-600 font-semibold hover:underline"
        data-id="{{ $addr->id }}"
        data-name="{{ $addr->customer_name }}"
        data-phone="{{ $addr->customer_phone }}"
        data-pin="{{ $addr->delivery_pin }}"
        data-line1="{{ $addr->address_line1 }}"
        data-line2="{{ $addr->address_line2 }}">
    Edit Address
</button>
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
                   placeholder="1234567890"
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
        @if($onlineEnabled)
        <label id="label-online" class="payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 {{ $codEnabled ? 'border-blue-600 bg-blue-50' : 'border-blue-600 bg-blue-50' }} p-4 transition-all">
            <input type="radio" name="payment_method" value="online"
                   {{ ! $codEnabled || $onlineEnabled ? 'checked' : '' }}
                   class="sr-only">
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
        @endif

        {{-- COD --}}
        @if($codEnabled)
        <label id="label-cod" class="payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 {{ $onlineEnabled ? 'border-slate-200 bg-white' : 'border-blue-600 bg-blue-50' }} p-4 transition-all">
            <input type="radio" name="payment_method" value="cod"
                   {{ ! $onlineEnabled ? 'checked' : '' }}
                   class="sr-only">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 overflow-hidden">
                        <img src="{{ asset('Images/CashonDeliveryicon.png') }}" alt="COD" class="h-7 w-7 object-contain">
                    </div>
                    <span class="text-sm font-bold text-slate-900">Cash on Delivery</span>
                </div>
                <div class="h-4 w-4 rounded-full border-2 {{ $onlineEnabled ? 'border-slate-300 bg-white' : 'border-blue-700 bg-blue-700 flex items-center justify-center' }}" id="dot-cod">
                    @unless($onlineEnabled)
                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                    @endunless
                </div>
            </div>
            <p class="text-xs text-slate-600">Pay in cash when your order arrives</p>
            <p class="text-xs text-amber-600 font-medium mt-1">No extra charges</p>
        </label>
        @endif

    </div>

    {{-- Disabled-method notice --}}
    @if(! $onlineEnabled)
        <p class="mt-2 text-xs text-slate-500">Online payment is currently unavailable.</p>
    @endif
    @if(! $codEnabled)
        <p class="mt-2 text-xs text-slate-500">Cash on Delivery is currently unavailable.</p>
    @endif
</div>

{{-- General error --}}
<div id="form-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

{{-- Submit --}}
<div id="edit-address-actions" style="display:none; margin-top:15px;">

    <button type="button"
            id="update-address-btn"
            style="
                width:100%;
                padding:14px;
                background:#16a34a;
                color:white;
                border:none;
                border-radius:10px;
                font-size:15px;
                font-weight:600;
                cursor:pointer;">
        Update Address
    </button>

</div>
<div class="pt-2">
    <button type="submit"
            id="place-order-btn"
            class="btn-primary w-full rounded-xl py-3.5 text-sm font-bold text-white shadow-md flex items-center justify-center gap-2">
        <svg id="btn-spinner" class="hidden animate-spin h-4 w-4 shrink-0 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg><span id="btn-text">Pay & Place Order</span>
    </button>

</div>
<div id="toast-message"
     style="
        display:none;
        position:fixed;
        top:20px;
        right:20px;
        z-index:9999;
        background:linear-gradient(135deg,#2563eb,#1d4ed8,#1e40af);
        color:#fff;
        padding:14px 22px;
        border-radius:12px;
        font-size:14px;
        font-weight:600;
        box-shadow:0 10px 25px rgba(37,99,235,.35);
        min-width:250px;
        animation:slideIn .3s ease;
     ">
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

//Edit adress Button logic

document.querySelectorAll('.edit-address-btn').forEach(btn => {

    btn.addEventListener('click', function(e) {

        e.stopPropagation();

        newAddressForm.classList.remove('hidden');

        document.getElementById('edit_address_id').value = this.dataset.id;

        document.getElementById('customer_name').value = this.dataset.name;
        document.getElementById('customer_phone').value = this.dataset.phone;
        document.getElementById('delivery_pin').value = this.dataset.pin;
        document.getElementById('address_line1').value = this.dataset.line1;
        document.getElementById('address_line2').value = this.dataset.line2;

        document.getElementById('edit-address-actions').style.display = 'block';

document.getElementById('place-order-btn').style.display = 'none';

        window.scrollTo({
            top: newAddressForm.offsetTop - 100,
            behavior: 'smooth'
        });
    });

});


// Auto-select first saved address on load
const firstCard = document.querySelector('.saved-addr-card');
if (firstCard) selectSavedAddress(firstCard);

document.querySelectorAll('.saved-addr-card').forEach(card => {
    card.addEventListener('click', () => selectSavedAddress(card));
});

if (useNewAddrBtn) {
    useNewAddrBtn.addEventListener('click', () => {

        selectedAddrId.value = '';
        document.getElementById('edit_address_id').value = '';
document.getElementById('edit-address-actions').style.display = 'none';
document.getElementById('place-order-btn').style.display = 'block';
        document.getElementById('customer_name').value  = @json(auth()->user()->name);
        document.getElementById('customer_phone').value = '';
        document.getElementById('delivery_pin').value   = '';
        document.getElementById('address_line1').value  = '';
        document.getElementById('address_line2').value  = '';

        // Restore checkout button text
        document.getElementById('btn-text').textContent = 'Pay & Place Order';

        newAddressForm.classList.remove('hidden');
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
const initialMethod = (document.querySelector('input[name="payment_method"]:checked') || {}).value || 'online';

// Set initial button text and badge based on which method is pre-selected
(function initPaymentUI() {
    const isOnline = initialMethod === 'online';
    document.getElementById('btn-text').textContent = isOnline ? 'Pay & Place Order' : 'Place Order (COD)';
    const badge = document.getElementById('summary-payment-badge');
    if (badge) {
        badge.innerHTML = isOnline
            ? '<span class="text-lg">💳</span><p class="text-xs font-medium text-slate-700">Secure online payment via Razorpay</p>'
            : '<span class="text-lg">💵</span><p class="text-xs font-medium text-amber-800">Pay cash when your order arrives</p>';
        badge.className = 'mt-4 rounded-xl border p-3 flex items-center gap-2 ' +
            (isOnline ? 'bg-blue-50 border-blue-100' : 'bg-amber-50 border-amber-100');
    }
})();

document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isOnline = this.value === 'online';
        const onlineLabel = document.getElementById('label-online');
        const codLabel    = document.getElementById('label-cod');
        if (onlineLabel) onlineLabel.className =
            'payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 p-4 transition-all ' +
            (isOnline ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white');
        if (codLabel) codLabel.className =
            'payment-option relative flex cursor-pointer flex-col gap-2 rounded-xl border-2 p-4 transition-all ' +
            (!isOnline ? 'border-blue-600 bg-blue-50' : 'border-slate-200 bg-white');
        const dotOnline = document.getElementById('dot-online');
        const dotCod    = document.getElementById('dot-cod');
        if (dotOnline) {
            dotOnline.className = 'h-4 w-4 rounded-full border-2 flex items-center justify-center ' +
                (isOnline ? 'border-blue-700 bg-blue-700' : 'border-slate-300 bg-white');
            dotOnline.innerHTML = isOnline ? '<div class="h-1.5 w-1.5 rounded-full bg-white"></div>' : '';
        }
        if (dotCod) {
            dotCod.className = 'h-4 w-4 rounded-full border-2 flex items-center justify-center ' +
                (!isOnline ? 'border-blue-700 bg-blue-700' : 'border-slate-300 bg-white');
            dotCod.innerHTML = !isOnline ? '<div class="h-1.5 w-1.5 rounded-full bg-white"></div>' : '';
        }
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
    const spinner = document.getElementById('btn-spinner');
    spinner.style.display = on ? 'inline-block' : 'none';
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

function showToast(message, type = 'success')
{
    const toast = document.getElementById('toast-message');

    toast.innerText = message;

    if(type === 'success'){
    toast.style.background = 'linear-gradient(135deg, #2563eb, #1d4ed8, #1e40af)';
}else{
    toast.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
}

    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

document.getElementById('update-address-btn').addEventListener('click', async function () {

    const btn = this;
    const addressId = document.getElementById('edit_address_id').value;

    btn.disabled = true;
    btn.innerText = 'Updating...';

    const newName  = document.getElementById('customer_name').value;
    const newPhone = document.getElementById('customer_phone').value;
    const newPin   = document.getElementById('delivery_pin').value;
    const newLine1 = document.getElementById('address_line1').value;
    const newLine2 = document.getElementById('address_line2').value;

    try {

        const response = await fetch("{{ route('address.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                address_id:     addressId,
                customer_name:  newName,
                customer_phone: newPhone,
                delivery_pin:   newPin,
                address_line1:  newLine1,
                address_line2:  newLine2
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            showToast(data.message || 'Failed to update address', 'error');
            btn.disabled = false;
            btn.innerText = 'Update Address';
            return;
        }

        // ── Refresh the saved address card in the DOM ──────────────────────
        const card = document.querySelector(`.saved-addr-card[data-id="${addressId}"]`);
        if (card) {
            // Update data attributes so selectSavedAddress() picks up new values
            card.dataset.name  = newName;
            card.dataset.phone = newPhone;
            card.dataset.pin   = newPin;
            card.dataset.line1 = newLine1;
            card.dataset.line2 = newLine2;

            // Also update the edit button's data attributes
            const editBtn = card.querySelector('.edit-address-btn');
            if (editBtn) {
                editBtn.dataset.name  = newName;
                editBtn.dataset.phone = newPhone;
                editBtn.dataset.pin   = newPin;
                editBtn.dataset.line1 = newLine1;
                editBtn.dataset.line2 = newLine2;
            }

            // Refresh the visible card text
            const namePhoneEl = card.querySelector('p.text-sm');
            if (namePhoneEl) namePhoneEl.textContent = `${newName} · ${newPhone}`;

            const summaryParts = [newLine1, newLine2, newPin].filter(Boolean);
            const summaryEl = card.querySelector('p.text-xs.text-slate-500');
            if (summaryEl) summaryEl.textContent = summaryParts.join(', ');

            // Auto-select the updated card so form fields are correct
            selectSavedAddress(card);
            selectedAddrId.value = addressId;
        }

        showToast(data.message, 'success');

        document.getElementById('edit-address-actions').style.display = 'none';
        document.getElementById('place-order-btn').style.display = 'block';
        document.getElementById('edit_address_id').value = '';
        newAddressForm.classList.add('hidden');

    } catch (error) {

        showToast('Network error. Please try again.', 'error');

    }

    btn.disabled = false;
    btn.innerText = 'Update Address';
});




// ── Full-screen overlay ───────────────────────────────────────────────────────
function showOverlay() {
    document.getElementById('checkout-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function hideOverlay() {
    document.getElementById('checkout-overlay').style.display = 'none';
    document.body.style.overflow = '';
}

// ── Form submit ───────────────────────────────────────────────────────────────
document.getElementById('checkout-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Show overlay IMMEDIATELY - before anything else
    showOverlay();
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
        edit_address_id: document.getElementById('edit_address_id').value || null,
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
        hideOverlay();
        showGeneralError('Network error. Please try again.');
        setLoading(false);
        return;
    }

    if (res.status === 422) {
        hideOverlay();
        if (data.errors) showErrors(data.errors);
        else showGeneralError(data.error || 'Please fix the errors above.');
        setLoading(false);
        return;
    }
    if (!res.ok) {
        hideOverlay();
        showGeneralError(data.error || 'Something went wrong. Please try again.');
        setLoading(false);
        return;
    }

    // COD - overlay stays, redirect immediately
    if (data.method === 'cod') {
        window.location.href = data.redirect_url;
        return;
    }

    // Online - overlay stays behind Razorpay modal
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
                hideOverlay();
                showGeneralError('Payment was cancelled. You can try again.');
                setLoading(false);
            }
        },
        handler: async function (response) {
            // Overlay already visible - update message during verification
            const overlayMsg = document.getElementById('overlay-msg');
            if (overlayMsg) overlayMsg.textContent = 'Verifying payment…';

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
                hideOverlay();
                showGeneralError('Could not verify payment. Please contact support.');
                setLoading(false);
                return;
            }
            if (vData.ok) {
                // Overlay stays visible until confirmation page loads
                window.location.href = vData.redirect_url;
            } else {
                hideOverlay();
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

{{-- ── Full-screen loading overlay ─────────────────────────────────────────── --}}
<div id="checkout-overlay"
     style="display:none; position:fixed; inset:0; z-index:99999;
            background:rgba(15,23,42,0.92); backdrop-filter:blur(6px);
            flex-direction:column; align-items:center; justify-content:center; gap:20px;">

    {{-- Spinner --}}
    <svg width="56" height="56" viewBox="0 0 56 56" fill="none"
         style="animation:co-spin 0.9s linear infinite; flex-shrink:0;">
        <circle cx="28" cy="28" r="22" stroke="#1e3a8a" stroke-width="5" opacity="0.2"/>
        <path d="M28 6 A22 22 0 0 1 50 28" stroke="#3b82f6" stroke-width="5"
              stroke-linecap="round"/>
    </svg>

    <div style="text-align:center;">
        <p style="color:#f1f5f9; font-size:1.1rem; font-weight:700; margin:0 0 6px;">
            Processing your order…
        </p>
        <p id="overlay-msg"
           style="color:#94a3b8; font-size:0.85rem; margin:0;">
            Please wait, do not close this page
        </p>
    </div>
</div>

<style>
@keyframes co-spin { to { transform: rotate(360deg); } }
</style>

@endpush

@endsection
