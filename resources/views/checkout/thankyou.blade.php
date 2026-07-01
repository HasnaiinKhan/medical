@extends('layouts.shop')

@section('title', 'Order Placed!')

@section('content')

<div class="mx-auto max-w-2xl">

    {{-- ===== SUCCESS HEADER ===== --}}
    <div class="mb-6 relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 p-8 text-center text-white shadow-xl">
        {{-- Delivery boy image --}}
        <div class="pointer-events-none absolute bottom-0 right-4 hidden sm:block">
            <img src="{{ asset('Images/MedicalDelhiveryboy.png') }}"
                 alt="" class="h-32 w-auto object-contain object-bottom opacity-30" draggable="false">
        </div>
        <div class="relative" style="margin-bottom:10px;">
                <i class="fa-regular fa-face-grin-wide fa-2xl"></i>
            </div>
            <p class="text-sm font-semibold uppercase tracking-widest text-blue-200 mb-1">Order Confirmed</p>
            <h1 class="text-3xl font-extrabold">Thank You!</h1>
            <p class="mt-2 text-blue-100">Your order has been placed successfully.</p>
            <div class="mt-5 inline-block rounded-xl bg-white/15 px-5 py-3 backdrop-blur-sm">
                <p class="text-xs font-medium text-blue-200 mb-0.5">Order Number</p>
                <p class="text-xl font-extrabold tracking-wider">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    {{-- Payment status banner --}}
    @if($order->payment_method === 'online')
        <div class="mb-4 flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 shadow-sm">
            <svg class="h-5 w-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <div>
                <p class="text-sm font-semibold text-slate-800">Payment Successful</p>
                <p class="text-xs text-slate-600">Paid online via Razorpay · ID: <span class="font-mono font-semibold">{{ $order->razorpay_payment_id }}</span></p>
            </div>
        </div>
    @endif

    {{-- ===== DELIVERY INFO ===== --}}
    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
            <p class="text-lg mb-1"><i class="fa-regular fa-user fa-lg" style="color: rgb(4, 22, 122);"></i></p>
            <p class="text-xs text-slate-500 mb-0.5">Customer</p>
            <p class="text-xs font-semibold text-slate-800">{{ $order->customer_name }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
            <p class="text-lg mb-1"><i class="fa-solid fa-mobile-button fa-lg" style="color: rgb(4, 22, 122);"></i></p>
            <p class="text-xs text-slate-500 mb-0.5">Mobile</p>
            <p class="text-xs font-semibold text-slate-800">+91 {{ $order->customer_phone }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
            <p class="text-lg mb-1"><i class="fa-solid fa-location-dot fa-lg"></i></p>
            <p class="text-xs text-slate-500 mb-0.5">Pincode</p>
            <p class="text-xs font-semibold text-slate-800">{{ $order->delivery_pin }}</p>
            <p class="text-xs text-slate-500 truncate">{{ $order->delivery_area }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm">
            <p class="text-lg mb-1"><i class="fa-solid fa-indian-rupee-sign fa-lg"></i></p>
            <p class="text-xs text-slate-500 mb-0.5">Payment</p>
            <p class="text-xs font-semibold text-slate-800">Cash on Delivery</p>
        </div>
    </div>

    {{-- ===== BILL DETAILS ===== --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Bill Details</h2>

        <div class="space-y-3">
            @foreach ($order->items as $item)
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-sm font-black text-blue-700">
                        {{ strtoupper(substr($item->medicine_name_snapshot, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 line-clamp-1">
                            @if($item->medicine && $item->medicine->is_active)
                                <a href="{{ route('medicines.show', $item->medicine) }}"
                                   class="hover:text-blue-700 hover:underline transition-colors">{{ $item->medicine_name_snapshot }}</a>
                            @else
                                {{ $item->medicine_name_snapshot }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price_paise / 100, 2) }}</p>
                    </div>
                    <span class="text-sm font-bold text-slate-900 flex-shrink-0">
                        ₹{{ number_format($item->line_total_paise / 100, 2) }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-5 border-t border-slate-200 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span class="font-medium text-slate-900">₹{{ number_format($order->subtotal_paise / 100, 2) }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Delivery fee</span>
                @if ($order->delivery_fee_paise === 0)
                    <span class="font-semibold text-blue-700">FREE</span>
                @else
                    <span class="font-medium text-slate-900">₹{{ number_format($order->delivery_fee_paise / 100, 2) }}</span>
                @endif
            </div>
        </div>

        <div class="mt-3 border-t-2 border-slate-900 pt-3">
            <div class="flex justify-between text-lg font-extrabold text-slate-900">
                @if($order->payment_method === 'online')
                    <span>Total Paid Online</span>
                    <span class="text-blue-700">₹{{ number_format($order->totalRupees(), 2) }}</span>
                @else
                    <span>Total to Pay (COD)</span>
                    <span class="text-slate-900">₹{{ number_format($order->totalRupees(), 2) }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== DELIVERY ADDRESS ===== --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 flex-shrink-0 text-blue-700 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div>
                <p class="text-xs font-semibold text-slate-700 mb-0.5">Delivery Address</p>
                <p class="text-sm text-slate-600">{{ $order->address_line1 }}</p>
                @if($order->address_line2)
                    <p class="text-sm text-slate-600">{{ $order->address_line2 }}</p>
                @endif
                <p class="text-sm text-slate-600">{{ $order->delivery_area }}, Ahmedabad - {{ $order->delivery_pin }}</p>
            </div>
        </div>
    </div>

    {{-- ===== ACTIONS ===== --}}
    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('medicines.index') }}"
           class="btn-primary flex-1 flex items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold text-white shadow-md">
            <i class="fa-solid fa-bag-shopping" style="color: rgb(255, 255, 255);"></i> Continue Shopping
        </a>
        <a href="{{ route('orders.index') }}"
           class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <i class="fa-regular fa-clipboard" style="color: rgb(66, 0, 255);"></i> View My Orders
        </a>
    </div>

    {{-- WhatsApp share --}}
    @php
        $waPhone = config('services.whatsapp.number', '919737275558');
        $waMsg   = "Hi! I just placed an order on Rx Plus 365 🎉\nOrder: {$order->order_number}\nAmount: ₹" . number_format($order->totalRupees(), 2) . "\nThank you Rx Plus 365!";
    @endphp
    <div class="mt-4 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#25d366" class="flex-shrink-0">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-green-900">    Share your order on WhatsApp</p>
            <p class="text-xs text-green-700"></p>Let your family know your order is placed</p>
        </div>
        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMsg) }}" target="_blank"
           class="rounded-xl px-4 py-2 text-xs font-bold text-white hover:bg-green-600 transition-colors flex-shrink-0" style="background-color: green;">
            Share
        </a>
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
        Always consult a doctor before taking medication.
    </p>
</div>

@endsection
