@extends('admin.layouts.admin')
@section('title', 'Order ' . $order->order_number)
@section('page-title', 'Order #' . $order->order_number)
@section('page-subtitle', 'Full order details and status management')

@section('content')

{{-- ── BACK + QUICK ACTIONS ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors w-fit">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Orders
    </a>

<<<<<<< HEAD
    {{-- Status change form (non-cancel statuses only) --}}
    @if($order->status !== 'cancelled')
    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
          class="flex flex-wrap items-center gap-2">
        @csrf @method('PATCH')
        <select name="status"
                class="flex-1 min-w-0 rounded-xl border border-slate-200 py-2 px-3 text-sm font-semibold focus:outline-none focus:border-blue-500 bg-white shadow-sm">
            @foreach($statusFlow as $s)
                @if($s !== 'cancelled')
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endif
            @endforeach
        </select>
        <button type="submit"
                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-colors whitespace-nowrap">
            Update
        </button>
        <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-100 transition-colors whitespace-nowrap">
            ❌ Cancel
        </button>
    </form>
    @endif
=======
    {{-- Status change form --}}
    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
          class="flex items-center gap-2 flex-wrap">
        @csrf @method('PATCH')
        <select name="status"
                class="flex-1 sm:flex-none rounded-xl border border-slate-200 py-2 px-3 text-sm font-semibold focus:outline-none focus:border-blue-500 bg-white shadow-sm">
            @foreach($statusFlow as $s)
                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-colors whitespace-nowrap">
            Update Status
        </button>
    </form>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
</div>

{{-- ── STATUS TIMELINE ── --}}
@php
    $allStatuses = ['placed','confirmed','shipped','delivered'];
    $currentIdx  = array_search($order->status, $allStatuses);
    $isCancelled = $order->status === 'cancelled';
@endphp
<<<<<<< HEAD
<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Order Progress</h3>
    @if($isCancelled)
        <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
            <span class="text-xl mt-0.5">❌</span>
            <div>
                <p class="text-sm font-bold text-red-800">Order Cancelled</p>
                @if($order->cancellation_reason)
                    <p class="text-xs text-red-700 mt-1"><span class="font-semibold">Reason:</span> {{ $order->cancellation_reason }}</p>
                @endif
                @if($order->cancelled_by)
                    <p class="text-xs text-red-500 mt-0.5">Cancelled by: <span class="font-semibold capitalize">{{ $order->cancelled_by }}</span>
                        @if($order->cancelled_at) · {{ $order->cancelled_at->format('d M Y, h:i A') }} @endif
                    </p>
                @endif
            </div>
        </div>
    @else
        <div class="overflow-x-auto">
            <div class="flex items-center gap-0 min-w-[320px]">
                @foreach($allStatuses as $i => $step)
                    @php
                        $done    = $currentIdx !== false && $i <= $currentIdx;
                        $current = $currentIdx !== false && $i === $currentIdx;
                        $icons   = ['placed'=>'📋','confirmed'=>'✅','shipped'=>'🚚','delivered'=>'🎉'];
                    @endphp
                    <div class="flex flex-1 flex-col items-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-base
                                    {{ $current ? 'bg-blue-600 ring-4 ring-blue-100' : ($done ? 'bg-blue-500' : 'bg-slate-200') }}">
                            @if($done)<span>{{ $icons[$step] }}</span>
                            @else<span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>@endif
                        </div>
                        <p class="mt-1.5 text-[10px] sm:text-[11px] font-semibold {{ $done ? 'text-blue-700' : 'text-slate-400' }}">{{ ucfirst($step) }}</p>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 mb-5 {{ $currentIdx !== false && $i < $currentIdx ? 'bg-blue-500' : 'bg-slate-200' }}"></div>
                    @endif
                @endforeach
            </div>
=======
<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Order Progress</h3>
    @if($isCancelled)
        <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
            <span class="text-xl">❌</span>
            <div>
                <p class="text-sm font-bold text-red-800">Order Cancelled</p>
                <p class="text-xs text-red-600">This order has been cancelled.</p>
            </div>
        </div>
    @else
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-0 overflow-x-auto pb-1">
            @foreach($allStatuses as $i => $step)
                @php
                    $done    = $currentIdx !== false && $i <= $currentIdx;
                    $current = $currentIdx !== false && $i === $currentIdx;
                    $icons   = ['placed'=>'📋','confirmed'=>'✅','shipped'=>'🚚','delivered'=>'🎉'];
                @endphp
                <div class="flex flex-1 flex-col items-center">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-base
                                {{ $current ? 'bg-blue-600 ring-4 ring-blue-100' : ($done ? 'bg-blue-500' : 'bg-slate-200') }}">
                        @if($done)
                            <span>{{ $icons[$step] }}</span>
                        @else
                            <span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                        @endif
                    </div>
                    <p class="mt-1.5 text-[11px] font-semibold {{ $done ? 'text-blue-700' : 'text-slate-400' }}">
                        {{ ucfirst($step) }}
                    </p>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-0.5 mb-5 {{ $currentIdx !== false && $i < $currentIdx ? 'bg-blue-500' : 'bg-slate-200' }}"></div>
                @endif
            @endforeach
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        </div>
    @endif
</div>

{{-- ── MAIN GRID ── --}}
<div class="grid gap-5 lg:grid-cols-3">

    {{-- Left: Order items --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Items table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">
                    Order Items
                    <span class="ml-2 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-700">
                        {{ $order->items->count() }}
                    </span>
                </h3>
                <span class="text-xs text-slate-500">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-4 px-5 py-4">
                        {{-- Medicine initial avatar --}}
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-lg font-black text-blue-700">
                            {{ strtoupper(substr($item->medicine_name_snapshot, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ $item->medicine_name_snapshot }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                ₹{{ number_format($item->unit_price_paise / 100, 2) }} × {{ $item->quantity }}
                            </p>
                        </div>
                        <p class="text-sm font-bold text-slate-900 flex-shrink-0">
                            ₹{{ number_format($item->line_total_paise / 100, 2) }}
                        </p>
                    </div>
                @endforeach
            </div>
            {{-- Bill summary --}}
            <div class="border-t border-slate-200 px-5 py-4 bg-slate-50 space-y-2">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-medium">₹{{ number_format($order->subtotal_paise / 100, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Delivery fee</span>
                    @if($order->delivery_fee_paise === 0)
                        <span class="font-semibold text-green-700">FREE</span>
                    @else
                        <span class="font-medium">₹{{ number_format($order->delivery_fee_paise / 100, 2) }}</span>
                    @endif
                </div>
                <div class="flex justify-between text-base font-extrabold text-slate-900 border-t border-slate-200 pt-2 mt-2">
                    <span>Total</span>
                    <span>₹{{ number_format($order->totalRupees(), 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment info --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Payment Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Method</p>
                    <span class="badge {{ $order->payment_method === 'online' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }} text-sm px-3 py-1">
                        {{ $order->payment_method === 'online' ? '💳 Online (Razorpay)' : '💵 Cash on Delivery' }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Payment Status</p>
                    @php
                        $psBadge = match($order->payment_status) {
                            'paid'   => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            default  => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <span class="badge {{ $psBadge }} text-sm px-3 py-1">
                        {{ ucfirst($order->payment_status ?? 'pending') }}
                    </span>
                </div>
                @if($order->razorpay_order_id)
                    <div class="col-span-2">
                        <p class="text-xs text-slate-500 mb-0.5">Razorpay Order ID</p>
                        <p class="font-mono text-xs text-slate-700 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200">
                            {{ $order->razorpay_order_id }}
                        </p>
                    </div>
                @endif
                @if($order->razorpay_payment_id)
                    <div class="col-span-2">
                        <p class="text-xs text-slate-500 mb-0.5">Razorpay Payment ID</p>
                        <p class="font-mono text-xs text-slate-700 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200">
                            {{ $order->razorpay_payment_id }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Customer + Delivery --}}
    <div class="space-y-5">

        {{-- Order meta --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Order Info</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Order #</span>
                    <span class="font-mono font-bold text-slate-800">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Placed on</span>
                    <span class="font-medium text-slate-700">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Time</span>
                    <span class="font-medium text-slate-700">{{ $order->created_at->format('h:i A') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Status</span>
                    @php
<<<<<<< HEAD
                        $statusCfgShow = [
                            'placed'                 => ['bg-amber-100 text-amber-800',  asset('images/hourglass.gif')],
                            'confirmed'              => ['bg-blue-100 text-blue-800',    asset('images/check.png')],
                            'shipped'                => ['bg-purple-100 text-purple-800',asset('images/package.png')],
                            'delivered'              => ['bg-green-100 text-green-800',  asset('images/confetti.png')],
                            'cancelled'              => ['bg-red-100 text-red-800',      asset('images/letter-x.png')],
                            'payment_failed'         => ['bg-red-100 text-red-800',      asset('images/sad.png')],
                            'refunded'               => ['bg-orange-100 text-orange-800',asset('images/refund.png')],
                            'refund_initiated'       => ['bg-yellow-100 text-yellow-800',asset('images/dollars.png')],
                            'cancellation_requested' => ['bg-amber-100 text-amber-800',  asset('images/hourglass.gif')],
                        ];
                        [$showSc, $showImg] = $statusCfgShow[$order->status] ?? ['bg-slate-100 text-slate-700', asset('images/box.png')];
                    @endphp
                    <span class="badge {{ $showSc }} inline-flex items-center gap-1.5">
                        <img src="{{ $showImg }}" alt="{{ $order->status }}" class="h-4 w-4 object-contain flex-shrink-0">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
=======
                        $statusCfg = [
                            'placed'    => 'bg-amber-100 text-amber-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'shipped'   => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="badge {{ $statusCfg[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst($order->status) }}
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    </span>
                </div>
            </div>
        </div>

        {{-- Customer info --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Customer
            </h3>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs text-slate-500">Name</p>
                    <p class="font-semibold text-slate-800">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Phone</p>
                    <p class="font-semibold text-slate-800">+91 {{ $order->customer_phone }}</p>
                </div>
                @if($order->user)
                    <div>
                        <p class="text-xs text-slate-500">Account Email</p>
                        <p class="font-semibold text-blue-700">{{ $order->user->email }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Delivery address --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Delivery Address
            </h3>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm text-slate-700 space-y-0.5">
                <p class="font-semibold">{{ $order->address_line1 }}</p>
                @if($order->address_line2)
                    <p>{{ $order->address_line2 }}</p>
                @endif
                <p>{{ $order->delivery_area }}</p>
                <p class="font-bold text-slate-900">Pincode: {{ $order->delivery_pin }}</p>
                <p class="text-slate-500">Ahmedabad, Gujarat</p>
            </div>
        </div>

        {{-- Quick status change (sidebar) --}}
<<<<<<< HEAD
        @if($order->status !== 'cancelled')
=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <h3 class="text-sm font-bold text-blue-900 mb-3">Quick Status Update</h3>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div class="grid grid-cols-1 gap-2">
<<<<<<< HEAD
                    @foreach(['placed','confirmed','shipped','delivered'] as $s)
                        @php
                            $icons  = ['placed'=>'📋','confirmed'=>'✅','shipped'=>'🚚','delivered'=>'🎉'];
=======
                    @foreach(['placed','confirmed','shipped','delivered','cancelled'] as $s)
                        @php
                            $icons = ['placed'=>'📋','confirmed'=>'✅','shipped'=>'🚚','delivered'=>'🎉','cancelled'=>'❌'];
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                            $active = $order->status === $s;
                        @endphp
                        <button type="submit" name="status" value="{{ $s }}"
                                class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold transition-all
                                       {{ $active
                                           ? 'bg-blue-600 text-white shadow-md'
                                           : 'bg-white border border-slate-200 text-slate-700 hover:border-blue-400 hover:text-blue-700' }}">
                            <span>{{ $icons[$s] }}</span>
                            {{ ucfirst($s) }}
                            @if($active)
                                <span class="ml-auto text-[10px] bg-white/20 rounded px-1.5 py-0.5">Current</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </form>
<<<<<<< HEAD

            {{-- Cancel — separate, opens modal --}}
            <div class="mt-3 pt-3 border-t border-blue-200">
                <button type="button"
                        onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                        class="w-full flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-bold bg-white border border-red-200 text-red-700 hover:bg-red-50 transition-all">
                    ❌ Cancel this order
                </button>
            </div>
        </div>
        @else
        {{-- Cancellation details card --}}
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <h3 class="text-sm font-bold text-red-900 mb-3">Cancellation Details</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Reason</p>
                    <p class="text-red-800 mt-0.5">{{ $order->cancellation_reason ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Cancelled By</p>
                    <p class="text-red-800 mt-0.5 capitalize">{{ $order->cancelled_by ?? '—' }}</p>
                </div>
                @if($order->cancelled_at)
                <div>
                    <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Cancelled At</p>
                    <p class="text-red-800 mt-0.5">{{ $order->cancelled_at->format('d M Y, h:i A') }}</p>
                </div>
                @endif
                @if($order->payment_status === 'paid')
                <div class="mt-2 rounded-lg bg-red-100 border border-red-200 px-3 py-2">
                    <p class="text-xs font-bold text-red-800">⚠ Revenue Impact</p>
                    <p class="text-xs text-red-700 mt-0.5">₹{{ number_format($order->totalRupees(), 2) }} deducted from revenue.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
=======
        </div>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    </div>
</div>

@endsection
<<<<<<< HEAD

{{-- ── CANCEL ORDER MODAL ── --}}
@if($order->status !== 'cancelled')
<div id="cancel-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-xl">❌</div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Cancel Order</h3>
                <p class="text-xs text-slate-500">{{ $order->order_number }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Reason for cancellation <span class="text-red-500">*</span>
                </label>
                <textarea name="cancellation_reason" rows="3" required minlength="5" maxlength="500"
                          placeholder="e.g. Out of stock, customer requested, duplicate order…"
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-400/20 resize-none">{{ old('cancellation_reason') }}</textarea>
                @error('cancellation_reason')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($order->payment_status === 'paid')
            <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2.5 text-xs text-amber-800">
                ⚠ This order was <strong>paid (₹{{ number_format($order->totalRupees(), 2) }})</strong>.
                Cancelling will deduct this amount from revenue.
            </div>
            @endif

            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('cancel-modal').classList.add('hidden')"
                        class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Go Back
                </button>
                <button type="submit"
                        class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors" style="background-coloR:red;">
                    Confirm Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modal on backdrop click
document.getElementById('cancel-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
// Re-open if validation failed (cancellation_reason error present)
@error('cancellation_reason')
    document.getElementById('cancel-modal').classList.remove('hidden');
@enderror
</script>
@endif
=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
