@extends('layouts.shop')
@section('title', 'Request Refund')

@section('content')
<div class="mx-auto max-w-2xl">

    {{-- Breadcrumb --}}
    <nav class="mb-5 flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('orders.index') }}" class="hover:text-blue-700">My Orders</a>
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('orders.show', $order) }}" class="hover:text-blue-700">{{ $order->order_number }}</a>
        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 font-medium">Request Refund</span>
    </nav>

    {{-- Order summary --}}
    <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 text-center">
            <div><p class="text-xs text-slate-500">Order</p><p class="font-mono font-bold text-slate-900 text-sm">{{ $order->order_number }}</p></div>
            <div><p class="text-xs text-slate-500">Amount</p><p class="font-bold text-slate-900">₹{{ number_format($order->totalRupees(), 2) }}</p></div>
            <div><p class="text-xs text-slate-500">Payment</p><p class="font-semibold text-slate-700 text-sm">{{ $order->payment_method === 'online' ? '💳 Online' : '💵 COD' }}</p></div>
            <div>
                <p class="text-xs text-slate-500">Refund Window</p>
                @php $daysLeft = $order->refundWindowDaysLeft(); @endphp
                <p class="font-bold text-sm {{ $daysLeft <= 5 ? 'text-red-600' : 'text-green-700' }}">
                    {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }} left
                </p>
            </div>
        </div>
    </div>

    {{-- Dispatch warning --}}
    @if($order->is_dispatched)
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
            <span class="text-xl flex-shrink-0">⚠️</span>
            <div>
                <p class="text-sm font-bold text-amber-800">Order Already Dispatched</p>
                <p class="text-xs text-amber-700 mt-0.5">Your order has been dispatched. Refund requests for dispatched orders require manual admin review and may take 5–7 business days.</p>
            </div>
        </div>
    @endif

    {{-- Refund form --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-lg font-bold text-slate-900 mb-1">Request a Refund</h1>
        <p class="text-sm text-slate-500 mb-6">
            @if($order->isCOD())
                Since this was a COD order, refund will be processed via bank transfer or UPI.
            @else
                Refund will be credited back to your original payment method within 5–7 business days.
            @endif
        </p>

        <form method="POST" action="{{ route('refunds.store', $order) }}"
              id="refund-form"
              enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Reason --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Reason for Refund <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="4" required maxlength="1000"
                          placeholder="Please describe why you want a refund (damaged product, wrong item, etc.)…"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('reason') }}</textarea>
                @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Proof image upload --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Upload Proof / Photo
                    <span class="text-xs font-normal text-slate-400">(optional - damaged item, wrong product, etc.)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-3 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                    <svg class="h-5 w-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="text-sm text-slate-500" id="proof-label">Click to upload image (JPG, PNG - max 4MB)</span>
                    <input type="file" name="proof_image" accept="image/*" class="sr-only"
                           onchange="document.getElementById('proof-label').textContent = this.files[0]?.name || 'Click to upload'">
                </label>
                @error('proof_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- COD: Bank or UPI --}}
            @if($order->isCOD())
                <div x-data="{ method: 'bank' }" class="rounded-xl border border-blue-200 bg-blue-50 p-4 space-y-4">
                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wide">Refund Method</p>

                    {{-- Toggle --}}
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="refund_method_choice" value="bank" x-model="method" class="sr-only">
                            <div :class="method === 'bank' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700'"
                                 class="rounded-xl border-2 px-4 py-2.5 text-sm font-bold text-center transition-colors">
                                🏦 Bank Transfer
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="refund_method_choice" value="upi" x-model="method" class="sr-only">
                            <div :class="method === 'upi' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700'"
                                 class="rounded-xl border-2 px-4 py-2.5 text-sm font-bold text-center transition-colors">
                                📱 UPI
                            </div>
                        </label>
                    </div>

                    {{-- Bank fields --}}
                    <div x-show="method === 'bank'" class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Account Holder Name *</label>
                            <input name="bank_account_name" value="{{ old('bank_account_name') }}"
                                   :required="method === 'bank'"
                                   placeholder="As per bank records"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                            @error('bank_account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Account Number *</label>
                                <input name="bank_account_number" value="{{ old('bank_account_number') }}"
                                       :required="method === 'bank'"
                                       placeholder="9–18 digit number"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                                @error('bank_account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">IFSC Code *</label>
                                <input name="bank_ifsc" value="{{ old('bank_ifsc') }}"
                                       :required="method === 'bank'"
                                       placeholder="e.g. SBIN0001234" maxlength="11"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                                @error('bank_ifsc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- UPI field --}}
                    <div x-show="method === 'upi'">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">UPI ID *</label>
                        <input name="upi_id" value="{{ old('upi_id') }}"
                               :required="method === 'upi'"
                               placeholder="e.g. yourname@upi or 7600264090@paytm"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        @error('upi_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-500">Format: name@bank or phone@upi</p>
                    </div>
                </div>
            @endif

            {{-- Online: Bank or UPI for refund credit --}}
            @if(!$order->isCOD())
                <div x-data="{ method: 'bank' }" class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 space-y-4">
                    <p class="text-xs font-bold text-indigo-800 uppercase tracking-wide">Refund Credit Method</p>
                    <p class="text-xs text-indigo-700">Please provide where you'd like the refund credited. Our team will process it within 5–7 business days.</p>

                    {{-- Toggle --}}
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="refund_method_choice" value="bank" x-model="method" class="sr-only">
                            <div :class="method === 'bank' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-700'"
                                 class="rounded-xl border-2 px-4 py-2.5 text-sm font-bold text-center transition-colors">
                                🏦 Bank Transfer
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="refund_method_choice" value="upi" x-model="method" class="sr-only">
                            <div :class="method === 'upi' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-700'"
                                 class="rounded-xl border-2 px-4 py-2.5 text-sm font-bold text-center transition-colors">
                                📱 UPI
                            </div>
                        </label>
                    </div>

                    {{-- Bank fields --}}
                    <div x-show="method === 'bank'" class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Account Holder Name *</label>
                            <input name="bank_account_name" value="{{ old('bank_account_name') }}"
                                   :required="method === 'bank'"
                                   placeholder="As per bank records"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                            @error('bank_account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Account Number *</label>
                                <input name="bank_account_number" value="{{ old('bank_account_number') }}"
                                       :required="method === 'bank'"
                                       placeholder="9–18 digit number"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                                @error('bank_account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">IFSC Code *</label>
                                <input name="bank_ifsc" value="{{ old('bank_ifsc') }}"
                                       :required="method === 'bank'"
                                       placeholder="e.g. SBIN0001234" maxlength="11"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                                @error('bank_ifsc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- UPI field --}}
                    <div x-show="method === 'upi'">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">UPI ID *</label>
                        <input name="upi_id" value="{{ old('upi_id') }}"
                               :required="method === 'upi'"
                               placeholder="e.g. yourname@upi or 7600264090@paytm"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                        @error('upi_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-500">Format: name@bank or phone@upi</p>
                    </div>
                </div>
            @endif

            {{-- Timeline info --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                <p class="text-xs font-bold text-slate-600 mb-2">What happens next?</p>
                <div class="space-y-1.5 text-xs text-slate-500">
                    <p>1. We review your request within 1–2 business days</p>
                    <p>2. You'll receive an email when approved</p>
                    <p>3. Refund credited within 5–7 business days after approval</p>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" id="refund-submit-btn"
                        class="btn-primary flex-1 rounded-xl py-3 text-sm font-bold text-white shadow-md">
                    Submit Refund Request
                </button>
                <a href="{{ route('orders.show', $order) }}"
                   class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-slate-400">
        Refunds are subject to our return policy. Medicines once opened cannot be returned.
    </p>

    {{-- WhatsApp support --}}
    @php $waPhone = config('services.whatsapp.number', '917600264090'); @endphp
    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hi! I need help with a refund for order ' . $order->order_number . ' on Rx Plus 365.') }}"
       target="_blank"
       class="mt-3 flex items-center justify-center gap-2 rounded-xl border border-green-200 bg-green-50 py-3 text-sm font-semibold text-green-800 hover:bg-green-100 transition-colors">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/></svg>
        Need help with your refund? Chat on WhatsApp
    </a>
</div>
@endsection

@push('scripts')
<style>
    @keyframes rfl-spin { to { transform: rotate(360deg); } }
    #refund-page-loader {
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
    #refund-page-loader.active { display: flex; }
    .rfl-ring {
        width: 56px; height: 56px;
        border: 5px solid #dbeafe;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: rfl-spin .75s linear infinite;
    }
    .rfl-title { font-size: 15px; font-weight: 700; color: #1e40af; }
    .rfl-sub   { font-size: 12px; color: #64748b; margin-top: -10px; }
</style>

{{-- Full-page overlay --}}
<div id="refund-page-loader" role="status" aria-label="Submitting">
    <div class="rfl-ring"></div>
    <p class="rfl-title">Submitting your request…</p>
    <p class="rfl-sub">Please don't close or refresh this page</p>
</div>

<script>
(function () {
    var form   = document.getElementById('refund-form');
    var btn    = document.getElementById('refund-submit-btn');
    var loader = document.getElementById('refund-page-loader');
    if (!form || !btn) return;

    form.addEventListener('submit', function () {
        if (loader) loader.classList.add('active');
        btn.disabled      = true;
        btn.style.opacity = '0.75';
        btn.textContent   = 'Submitting...';
    });
})();
</script>
@endpush
