@extends('layouts.shop')

@section('title', 'My Cart')

@section('content')

{{-- Stock warnings (out-of-stock items removed, quantities clamped) --}}
@if(!empty($stockWarnings))
<div id="cart-stock-warnings">
    @foreach($stockWarnings as $warning)
    <div class="mb-3 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 font-medium">
        <span class="flex-shrink-0 text-base">⚠️</span>
        <span>{{ $warning }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Cart header - animated gradient, no image --}}
<div class="relative mb-6 overflow-hidden rounded-2xl" style="min-height: 10%;">
    {{-- Animated flowing gradient background --}}
    <div class="absolute inset-0 cart-header-bg"></div>
    {{-- Floating orbs for depth --}}
    <div class="pointer-events-none absolute -top-6 -left-6 h-32 w-32 rounded-full opacity-20" style="background:radial-gradient(circle,#80c0e0,transparent 70%);"></div>
    <div class="pointer-events-none absolute -bottom-4 right-16 h-24 w-24 rounded-full opacity-15" style="background:radial-gradient(circle,#b0d0f0,transparent 70%);"></div>
    <div class="pointer-events-none absolute top-2 right-1/3 h-16 w-16 rounded-full opacity-10" style="background:radial-gradient(circle,#fff,transparent 70%);"></div>
    {{-- Content --}}
    <div class="relative flex items-center justify-between px-6 py-5">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">My Cart</h1>
            <p id="cart-items-count" class="text-sm text-blue-200 mt-0.5">{{ $lines->count() }} {{ $lines->count() === 1 ? 'item' : 'items' }} in your cart</p>
        </div>
        <a href="{{ route('medicines.index') }}" class="text-sm font-semibold text-white hover:text-blue-200 transition-colors">← Continue Shopping</a>
    </div>
</div>

<style>
@keyframes gradientFlow {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.cart-header-bg {
    background: linear-gradient(135deg, #1e3a8a, #1e40af, #2563eb, #3b82f6, #1e40af);
    background-size: 300% 300%;
    animation: gradientFlow 6s ease infinite;
}
</style>

@if ($lines->isEmpty())
    {{-- Empty cart --}}
    <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center shadow-sm">
        <img src="{{ asset('Images/emptycart1.png') }}"
             alt="Empty cart"
             class="h-36 w-auto object-contain mb-4 opacity-80"
             loading="lazy">
        <h2 class="text-xl font-bold text-slate-700">Your cart is empty</h2>
        <p class="mt-2 text-sm text-slate-500 max-w-xs">Looks like you haven't added any medicines yet. Browse our catalogue to get started.</p>
        <a href="{{ route('medicines.index') }}"
           class="btn-primary mt-6 inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-md">
            Browse Medicines →
        </a>
    </div>

 @else
     <div id="cart-page-content" class="grid gap-6 lg:grid-cols-3">

        {{-- ===== CART ITEMS ===== --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($lines as $line)
                @php
                    $m = $line['medicine'];
                    $colors = ['from-blue-50 to-blue-100', 'from-blue-50 to-indigo-100', 'from-purple-50 to-violet-100', 'from-amber-50 to-orange-100', 'from-rose-50 to-pink-100'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp

                <div data-cart-row-id="{{ $m->id }}" class="flex gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-blue-200 transition-colors">
                    {{-- Thumbnail --}}
                    <div class="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-gradient-to-br {{ $color }}">
                        <img src="{{ $m->imageUrl() }}"
                             alt="{{ $m->name }}"
                             class="h-full w-full object-contain object-center p-1"
                             loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="absolute inset-0 hidden items-center justify-center">
                            <span class="text-2xl font-black text-slate-300/70">{{ strtoupper(substr($m->name, 0, 1)) }}</span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex flex-1 flex-col min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('medicines.show', $m) }}"
                                   class="text-sm font-semibold text-slate-900 hover:text-blue-800 transition-colors line-clamp-2 leading-snug">
                                    {{ $m->name }}
                                </a>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $m->manufacturer }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p data-cart-line-total-id="{{ $m->id }}" class="text-base font-bold text-slate-900">₹{{ number_format($line['line_total_paise'] / 100, 2) }}</p>
                                @if ($m->mrp_paise > $m->price_paise)
                                    <p class="text-xs text-slate-400 line-through">₹{{ number_format($m->mrpRupees() * $line['quantity'], 2) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            {{-- Unit price --}}
                            <span class="text-xs text-slate-500">₹{{ number_format($m->priceRupees(), 2) }} each</span>

                            {{-- Quantity update --}}
                            <form method="post" action="{{ route('cart.update', $m) }}"
                                  class="inline-flex items-center gap-0 rounded-lg border border-slate-200 overflow-hidden js-cart-update-form"
                                  data-cart-medicine-id="{{ $m->id }}"
                                  data-stock="{{ $m->stock }}">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                        class="js-card-qty-minus px-2.5 py-1.5 text-slate-600 hover:bg-slate-50 transition-colors font-bold text-sm leading-none">
                                    −
                                </button>
                                <input type="number"
                                       name="quantity"
                                       value="{{ $line['quantity'] }}"
                                       min="1"
                                       max="99"
                                       class="w-10 border-x border-slate-200 py-1.5 text-center text-sm font-semibold focus:outline-none" />
                                <button type="button"
                                        class="js-card-qty-plus px-2.5 py-1.5 text-slate-600 hover:bg-slate-50 transition-colors font-bold text-sm leading-none">
                                    +
                                </button>
                            </form>

                            {{-- Remove --}}
                            <form method="post" action="{{ route('cart.remove', $m) }}"
                                  class="js-cart-remove-form"
                                  data-cart-medicine-id="{{ $m->id }}"
                                  data-medicine-name="{{ $m->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="js-cart-remove-btn text-xs font-medium text-red-500 hover:text-red-700 transition-colors hover:underline">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== ORDER SUMMARY ===== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-36 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-4">Order Summary</h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal (<span id="cart-summary-items-count">{{ $lines->count() }}</span> <span id="cart-summary-items-label">{{ $lines->count() === 1 ? 'item' : 'items' }}</span>)</span>
                        <span id="cart-summary-subtotal" class="font-medium text-slate-900">₹{{ number_format($subtotalPaise / 100, 2) }}</span>
                    </div>
                    @php $deliveryFee = $subtotalPaise >= 50000 ? 0 : 4000; @endphp
                    <div class="flex justify-between text-slate-600">
                        <span>Delivery fee</span>
                        @if ($deliveryFee === 0)
                            <span id="cart-summary-delivery-fee" class="font-semibold text-blue-700">FREE</span>
                        @else
                            <span id="cart-summary-delivery-fee" class="font-medium text-slate-900">₹{{ number_format($deliveryFee / 100, 2) }}</span>
                        @endif
                    </div>
                    @if ($deliveryFee > 0)
                        <p id="cart-delivery-text" class="text-xs text-slate-500 bg-amber-50 rounded-lg px-3 py-2 border border-amber-100">
                            Add ₹{{ number_format((50000 - $subtotalPaise) / 100, 2) }} more for free delivery!
                        </p>
                    @else
                        <p id="cart-delivery-text" class="text-xs text-blue-800 bg-blue-50 rounded-lg px-3 py-2 border border-blue-100">
                            🎉 You qualify for free delivery!
                        </p>
                    @endif
                </div>

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex justify-between text-base font-bold text-slate-900">
                        <span>Total</span>
                        <span id="cart-total-amount">₹{{ number_format(($subtotalPaise + $deliveryFee) / 100, 2) }}</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Payment: Cash on Delivery</p>
                </div>

                <a href="{{ route('checkout.create') }}"
                   class="btn-primary mt-5 flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold text-white shadow-md">
                    Proceed to Checkout →
                </a>

                <div class="mt-4 space-y-2">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Secure checkout
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Ahmedabad delivery only
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Remove confirmation modal -->
<div id="remove-modal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-md p-6">
        <h3 class="text-xl font-bold text-slate-800">Remove Item?</h3>
        <p class="mt-3 text-sm text-slate-500">
            Are you sure you want to remove
            <span id="remove-item-name" class="font-semibold text-slate-800"></span>
            from your cart?
        </p>
        <div class="flex justify-end gap-3 mt-6">
            <button id="cancel-remove"
                    class="px-5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50">
                Cancel
            </button>
            <button id="confirm-remove"
                    class="px-5 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                Remove
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal       = document.getElementById('remove-modal');
    const itemNameEl  = document.getElementById('remove-item-name');
    const cancelBtn   = document.getElementById('cancel-remove');
    const confirmBtn  = document.getElementById('confirm-remove');
    let pendingForm   = null;

    // Open modal when Remove button clicked
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-cart-remove-btn');
        if (!btn) return;
        e.preventDefault();
        pendingForm  = btn.closest('.js-cart-remove-form');
        itemNameEl.textContent = pendingForm?.dataset.medicineName || 'this item';
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        pendingForm = null;
        window._pendingRemoveForm = null;
    });

    // Close on backdrop click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            pendingForm = null;
            window._pendingRemoveForm = null;
        }
    });

    confirmBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        const formToSubmit = pendingForm || window._pendingRemoveForm;
        if (formToSubmit) {
            formToSubmit.requestSubmit();
        }
        pendingForm = null;
        window._pendingRemoveForm = null;
    });
})();
</script>
@endpush

@endsection
