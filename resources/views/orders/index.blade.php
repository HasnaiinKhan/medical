@extends('layouts.shop')

@section('title', 'My Orders')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">My Orders</h1>
        <p class="mt-0.5 text-sm text-slate-500">Order history for {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('medicines.index') }}" class="text-sm font-medium text-blue-700 hover:underline">
        Shop more →
    </a>
</div>

@if ($orders->isEmpty())
    <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center shadow-sm">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-700">No orders yet</h2>
        <p class="mt-2 text-sm text-slate-500 max-w-xs">You haven't placed any orders. Browse our medicines and place your first order!</p>
        <a href="{{ route('medicines.index') }}"
           class="btn-primary mt-6 inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-md">
            Browse Medicines →
        </a>
    </div>

@else
    <div class="space-y-4">
        @foreach ($orders as $order)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:border-blue-200 transition-colors">
                {{-- Order header --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Order Number</p>
                            <p class="text-sm font-bold text-slate-900 tracking-wide">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Placed On</p>
                            <p class="text-sm font-medium text-slate-700">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Total</p>
                            <p class="text-sm font-bold text-blue-800">₹{{ number_format($order->totalRupees(), 2) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Status badge --}}
                        @php
                            $statusColors = [
                                'placed'                 => 'bg-blue-100 text-blue-800 ring-blue-200',
                                'confirmed'              => 'bg-blue-100 text-blue-900 ring-blue-200',
                                'shipped'                => 'bg-purple-100 text-purple-800 ring-purple-200',
                                'delivered'              => 'bg-green-100 text-green-900 ring-green-200',
                                'cancellation_requested' => 'bg-amber-100 text-amber-800 ring-amber-200',
                                'refund_initiated'       => 'bg-orange-100 text-orange-800 ring-orange-200',
                                'refunded'               => 'bg-green-100 text-green-800 ring-green-200',
                                'cancelled'              => 'bg-red-100 text-red-800 ring-red-200',
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-800 ring-slate-200';
                            $activeRefund = $order->refunds()->whereIn('status',['requested','approved','processing'])->first();
                        @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                        @if($activeRefund)
                            @php [$rb, $rl] = $activeRefund->statusBadge(); @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-bold {{ $rb }}">
                                {{ $rl }}
                            </span>
                        @endif
                        <a href="{{ route('orders.show', $order) }}"
                           class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-blue-300 transition-colors shadow-sm">
                            View Details →
                        </a>
                    </div>
                </div>

                {{-- Order items preview --}}
                <div class="px-5 py-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($order->items->take(4) as $item)
                            <div class="flex items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded bg-blue-100 text-xs font-bold text-blue-800">
                                    {{ strtoupper(substr($item->medicine_name_snapshot, 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-slate-700">{{ $item->medicine_name_snapshot }}</span>
                                <span class="text-xs text-slate-400">×{{ $item->quantity }}</span>
                            </div>
                        @endforeach
                        @if ($order->items->count() > 4)
                            <div class="flex items-center rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5">
                                <span class="text-xs text-slate-500">+{{ $order->items->count() - 4 }} more</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $order->delivery_area }} — {{ $order->delivery_pin }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Cash on Delivery
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($orders->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $orders->links() }}
        </div>
    @endif
@endif

@endsection
