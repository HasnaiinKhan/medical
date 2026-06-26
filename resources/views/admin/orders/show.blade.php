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

    {{-- Status change form — hide Move to + Update when delivered, cancelled, or in any refund stage --}}
    @if(!in_array($order->status, ['cancelled', 'delivered', 'refunded', 'refund_requested', 'refund_initiated', 'refund_rejected']))
    @php
        $allSteps = ['placed', 'confirmed', 'shipped', 'delivered'];
        $currentStep   = array_search($order->status, $allSteps);
        $forwardSteps  = $currentStep !== false
            ? array_slice($allSteps, $currentStep + 1)
            : $allSteps;
    @endphp
    <div class="flex flex-wrap items-center gap-2">
        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}"
              id="status-top-form"
              class="flex flex-wrap items-center gap-2">
            @csrf @method('PATCH')
            <select name="status" id="status-top-select"
                    class="flex-1 min-w-0 rounded-xl border border-slate-200 py-2 px-3 text-sm font-semibold focus:outline-none focus:border-blue-500 bg-white shadow-sm">
                <option value="" disabled selected>Move to…</option>
                @foreach($forwardSteps as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="button"
                    onclick="openStatusModal(document.getElementById('status-top-select').value, 'status-top-form')"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-colors whitespace-nowrap">
                Update
            </button>
        </form>
        <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-100 transition-colors whitespace-nowrap">
            ❌ Cancel
        </button>
    </div>
    @endif
</div>

{{-- ── STATUS TIMELINE ── --}}
@php
    // Build path based on order status
    $allStatuses = ['placed', 'confirmed', 'shipped', 'delivered'];
    $isRefunding = in_array($order->status, ['refund_requested','refund_initiated', 'refunded', 'refund_rejected']);
    $isCancelled = $order->status === 'cancelled';
    
    if ($isRefunding) {
        $allStatuses = ['placed', 'confirmed', 'shipped', 'delivered', 'refund_requested', 'refund_initiated', 'refunded'];
        // For rejected path, fork at refund_requested
        if ($order->status === 'refund_rejected') {
            $allStatuses = ['placed', 'confirmed', 'shipped', 'delivered', 'refund_requested', 'refund_rejected'];
        }
    }
    
    $currentIdx = array_search($order->status, $allStatuses);
    
    $icons = [
        'placed' => '📋',
        'confirmed' => '✅', 
        'shipped' => '🚚',
        'delivered' => '🎉',
        'refund_initiated' => '💸',
        'refund_requested' => '↩️',
        'refunded' => '💰',
        'refund_rejected' => '🚫',
    ];
@endphp
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
    @elseif($isRefunding)
        <div class="overflow-x-auto">
            <div class="flex items-center gap-0 min-w-[400px]">
                @foreach($allStatuses as $i => $step)
                    @php
                        $done    = $currentIdx !== false && $i <= $currentIdx;
                        $current = $currentIdx !== false && $i === $currentIdx;
                    @endphp
                    <div class="flex flex-1 flex-col items-center mt-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-base
                                    {{ $current ? ($order->status === 'refund_rejected' ? 'bg-red-500 ring-4 ring-red-100' : 'bg-amber-500 ring-4 ring-amber-100') : ($done ? 'bg-green-500' : 'bg-slate-200') }}">
                            @if($done)<span>{{ $icons[$step] }}</span>
                            @else<span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>@endif
                        </div>
                        <p class="mt-1.5 text-[10px] sm:text-[11px] font-semibold {{ $done ? ($current ? ($order->status === 'refund_rejected' ? 'text-red-700' : 'text-amber-700') : 'text-green-700') : 'text-slate-400' }}">{{ ucfirst(str_replace('_', ' ', $step)) }}</p>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 mb-5 {{ $currentIdx !== false && $i < $currentIdx ? ($currentIdx === $i && $order->status === 'refund_requested' ? 'bg-amber-400' : 'bg-green-500') : 'bg-slate-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="overflow-x-auto">
            <div class="flex items-center gap-0 min-w-[320px]">
                @foreach($allStatuses as $i => $step)
                    @php
                        $done    = $currentIdx !== false && $i <= $currentIdx;
                        $current = $currentIdx !== false && $i === $currentIdx;
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
                        $statusCfgShow = [
                            'placed'                 => ['bg-amber-100 text-amber-800',  asset('Images/hourglass.gif')],
                            'confirmed'              => ['bg-blue-100 text-blue-800',    asset('Images/check.png')],
                            'shipped'                => ['bg-purple-100 text-purple-800',asset('Images/package.png')],
                            'delivered'              => ['bg-green-100 text-green-800',  asset('Images/Deliver.png')],
                            'cancelled'              => ['bg-red-100 text-red-800',      asset('Images/letter-x.png')],
                            'payment_failed'         => ['bg-red-100 text-red-800',      asset('Images/sad.png')],
                            'refunded'               => ['bg-orange-100 text-orange-800',asset('Images/refund.png')],
                            'refund_initiated'       => ['bg-yellow-100 text-yellow-800',asset('Images/dollars.png')],
                            'refund_requested'       => ['bg-amber-100 text-amber-800',  asset('Images/hourglass.gif')],
                            'refund_rejected'        => ['bg-red-100 text-red-800',       asset('Images/letter-x.png')],
                        ];
                        [$showSc, $showImg] = $statusCfgShow[$order->status] ?? ['bg-slate-100 text-slate-700', asset('Images/box.png')];
                    @endphp
                    <span class="badge {{ $showSc }} inline-flex items-center gap-1.5">
                        <img src="{{ $showImg }}" alt="{{ $order->status }}" class="h-4 w-4 object-contain flex-shrink-0">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
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
        @if(!in_array($order->status, ['cancelled', 'delivered', 'refunded', 'refund_requested', 'refund_initiated', 'refund_rejected']))
        @php
            $allSteps = ['placed', 'confirmed', 'shipped', 'delivered'];
            $currentStep  = array_search($order->status, $allSteps);
            $forwardSteps = $currentStep !== false
                ? array_slice($allSteps, $currentStep + 1)
                : $allSteps;
        @endphp
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <h3 class="text-sm font-bold text-blue-900 mb-1">Quick Status Update</h3>
            @php $icons = ['placed'=>'📋','confirmed'=>'✅','shipped'=>'🚚','delivered'=>'🎉','refund_requested'=>'↩️','refund_initiated'=>'💸','refunded'=>'💰']; @endphp

            {{-- Current status indicator --}}
            <p class="text-xs text-blue-700 mb-3">
                Current: <span class="font-bold">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
            </p>

            @if(count($forwardSteps))
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" id="status-sidebar-form" class="space-y-2">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" id="status-sidebar-input">
                    @foreach($forwardSteps as $s)
                        <button type="button"
                                onclick="openStatusModal('{{ $s }}', 'status-sidebar-form')"
                                class="flex items-center gap-2 w-full rounded-xl px-3 py-2 text-xs font-bold transition-all bg-white border border-slate-200 text-slate-700 hover:border-blue-400 hover:text-blue-700">
                            <span>{{ $icons[$s] }}</span>
                            {{ ucfirst($s) }}
                            <svg class="ml-auto h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endforeach
                </form>
            @elseif($order->status === 'delivered')
                <p class="text-xs text-slate-500 italic">Order has been delivered.</p>
            @else
                <p class="text-xs text-slate-500 italic">Order has reached final status.</p>
            @endif

            {{-- Cancel - always visible while not cancelled --}}
            <div class="mt-3 pt-3 border-t border-blue-200">
                <button type="button"
                        onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                        class="w-full flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-bold bg-white border border-red-200 text-red-700 hover:bg-red-50 transition-all">
                    ❌ Cancel this order
                </button>
            </div>
        </div>
        @elseif($order->status === 'cancelled')
        {{-- Cancellation details card — only shown for cancelled orders --}}
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <h3 class="text-sm font-bold text-red-900 mb-3">Cancellation Details</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Reason</p>
                    <p class="text-red-800 mt-0.5">{{ $order->cancellation_reason ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Cancelled By</p>
                    <p class="text-red-800 mt-0.5 capitalize">{{ $order->cancelled_by ?? '-' }}</p>
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
        @elseif(in_array($order->status, ['refund_requested', 'refund_initiated', 'refunded', 'refund_rejected']))
        {{-- Refund info card — shown for all refund-stage orders --}}
        @php
            $refundStageColors = [
                'refund_requested' => ['border-amber-200', 'bg-amber-50', 'text-amber-900', 'text-amber-700', 'bg-amber-100 border-amber-200', 'text-amber-800'],
                'refund_initiated'  => ['border-yellow-200', 'bg-yellow-50', 'text-yellow-900', 'text-yellow-700', 'bg-yellow-100 border-yellow-200', 'text-yellow-800'],
                'refunded'          => ['border-green-200', 'bg-green-50', 'text-green-900', 'text-green-700', 'bg-green-100 border-green-200', 'text-green-800'],
                'refund_rejected'   => ['border-red-200', 'bg-red-50', 'text-red-900', 'text-red-700', 'bg-red-100 border-red-200', 'text-red-800'],
            ];
            [$rBorder, $rBg, $rTitle, $rSub, $rBadgeBg, $rBadgeText] = $refundStageColors[$order->status];
            $refundLabels = [
                'refund_requested' => ['↩️', 'Refund Requested', 'The customer has requested a refund. Review and initiate the refund process.'],
                'refund_initiated'  => ['💸', 'Refund Initiated', 'The refund has been initiated and is being processed by the payment gateway.'],
                'refunded'          => ['💰', 'Refund Completed', 'The refund has been successfully processed and credited to the customer.'],
                'refund_rejected'   => ['🚫', 'Refund Rejected', 'The refund request was reviewed and rejected by the admin. The order remains delivered.'],
            ];
            [$rIcon, $rLabel, $rDesc] = $refundLabels[$order->status];
        @endphp
        <div class="rounded-2xl border {{ $rBorder }} {{ $rBg }} p-5">
            <h3 class="text-sm font-bold {{ $rTitle }} mb-3 flex items-center gap-2">
                <span>{{ $rIcon }}</span> {{ $rLabel }}
            </h3>
            <p class="text-xs {{ $rSub }} mb-3">{{ $rDesc }}</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-xs {{ $rSub }}">Order Total</span>
                    <span class="font-bold {{ $rTitle }}">₹{{ number_format($order->totalRupees(), 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs {{ $rSub }}">Payment Method</span>
                    <span class="font-semibold {{ $rTitle }} capitalize">{{ $order->payment_method }}</span>
                </div>
                <div class="mt-2 rounded-lg {{ $rBadgeBg }} border px-3 py-2">
                    <p class="text-xs font-bold {{ $rBadgeText }}">Status: {{ ucwords(str_replace('_', ' ', $order->status)) }}</p>
                    <p class="text-xs {{ $rSub }} mt-0.5">This order is in a final stage. No further status changes are allowed.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

{{-- ── STATUS CONFIRM MODAL ── --}}
@if(!in_array($order->status, ['cancelled', 'delivered', 'refunded', 'refund_requested', 'refund_initiated', 'refund_rejected']))
<div id="status-confirm-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);">
    <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div id="scm-icon" class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xl">📦</div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Confirm Status Change</h3>
                <p id="scm-subtitle" class="text-xs text-slate-500 mt-0.5"></p>
            </div>
        </div>
        <p id="scm-body" class="text-sm text-slate-600 mb-5"></p>
        <div class="flex gap-3">
            <button type="button"
                    onclick="document.getElementById('status-confirm-modal').classList.add('hidden')"
                    class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button type="button" id="scm-confirm-btn"
                    class="flex-1 rounded-xl bg-blue-600 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors">
                Yes, Update
            </button>
        </div>
    </div>
</div>

<script>
const statusIcons  = { placed:'📋', confirmed:'✅', shipped:'🚚', delivered:'🎉', refund_requested:'↩️', refunded:'💰' };
const statusColors = { placed:'#d97706', confirmed:'#2563eb', shipped:'#7c3aed', delivered:'#16a34a', refund_requested:'#f59e0b', refunded:'#ea580c' };

function openStatusModal(newStatus, formId) {
    if (!newStatus) { alert('Please select a status first.'); return; }
    const modal    = document.getElementById('status-confirm-modal');
    const icon     = document.getElementById('scm-icon');
    const subtitle = document.getElementById('scm-subtitle');
    const body     = document.getElementById('scm-body');
    const confirmBtn = document.getElementById('scm-confirm-btn');

    icon.textContent = statusIcons[newStatus] || '📦';
    icon.style.background = '#eff6ff';
    subtitle.textContent = `Move order to: ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
    body.textContent = `Are you sure you want to mark this order as "${newStatus}"? ${newStatus === 'delivered' ? 'This will also mark payment as Paid for COD orders.' : 'A notification email will be sent to the customer.'}`;
    confirmBtn.style.background = statusColors[newStatus] || '#2563eb';

    confirmBtn.onclick = function () {
        // Set the hidden input for sidebar form, or select value for top form
        if (formId === 'status-sidebar-form') {
            document.getElementById('status-sidebar-input').value = newStatus;
        } else {
            // top form: the select already has the value from when button was clicked
            const sel = document.getElementById('status-top-select');
            if (sel) sel.value = newStatus;
        }
        modal.classList.add('hidden');
        document.getElementById(formId).submit();
    };

    modal.classList.remove('hidden');
}

document.getElementById('status-confirm-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') document.getElementById('status-confirm-modal')?.classList.add('hidden');
});
</script>
@endif

{{-- ── CANCEL ORDER MODAL ── --}}
@if(!in_array($order->status, ['cancelled', 'delivered', 'refunded', 'refund_requested', 'refund_initiated', 'refund_rejected']))
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
