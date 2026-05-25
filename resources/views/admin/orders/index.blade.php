@extends('admin.layouts.admin')
@section('title', 'Orders')
@section('page-title', 'Order Management')
@section('page-subtitle', 'View, filter and manage all customer orders')

@section('content')

{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
    @foreach([
        ['label'=>'Total Orders',   'value'=>$totalOrders,                        'icon'=>'📦', 'color'=>'bg-blue-50 text-blue-800'],
        ['label'=>'Pending',        'value'=>$pendingOrders,                       'icon'=>'⏳', 'color'=>'bg-amber-50 text-amber-800'],
        ['label'=>'Today\'s Orders','value'=>$todayOrders,                         'icon'=>'📅', 'color'=>'bg-purple-50 text-purple-800'],
        ['label'=>'Total Revenue',  'value'=>'₹'.number_format($totalRevenue, 2),  'icon'=>'💰', 'color'=>'bg-green-50 text-green-800'],
    ] as $s)
    <div class="stat-card flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">{{ $s['label'] }}</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $s['color'] }} text-base">{{ $s['icon'] }}</span>
        </div>
        <p class="text-2xl font-extrabold text-slate-900">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── FILTERS ── --}}
<form method="GET" action="{{ route('admin.orders.index') }}"
      class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">

        {{-- Search --}}
        <div class="lg:col-span-2 relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Order #, name, phone, pincode…"
                   class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
        </div>

        {{-- Status --}}
        <select name="status" class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all" {{ request('status','all')==='all'?'selected':'' }}>All Statuses</option>
            @foreach(['placed','confirmed','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>

        {{-- Payment method --}}
        <select name="payment" class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all" {{ request('payment','all')==='all'?'selected':'' }}>All Payments</option>
            <option value="online" {{ request('payment')==='online'?'selected':'' }}>Online</option>
            <option value="cod"    {{ request('payment')==='cod'?'selected':'' }}>COD</option>
        </select>

        {{-- Date from --}}
        <input type="date" name="from" value="{{ request('from') }}"
               class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">

        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row gap-2">
            <button type="submit"
                    class="w-full sm:flex-1 rounded-xl bg-blue-600 py-2 px-4 text-sm font-bold text-white hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.orders.index') }}"
               class="w-full sm:w-auto rounded-xl border border-slate-200 py-2 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors text-center">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- ── BULK ACTION FORM ── --}}
<form method="POST" action="{{ route('admin.orders.bulkStatus') }}" id="bulk-form">
    @csrf

{{-- ── TABLE ── --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4">

    {{-- Bulk toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-slate-300 text-blue-600">
                Select all
            </label>
            <span class="text-xs text-slate-400" id="selected-count">0 selected</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <select name="status" id="bulk-status"
                    class="rounded-lg border border-slate-200 py-1.5 px-2 text-xs font-semibold focus:outline-none focus:border-blue-500">
                <option value="">Bulk change status…</option>
                @foreach(['placed','confirmed','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}">→ {{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" id="bulk-apply"
                    class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition-colors disabled:opacity-40"
                    disabled>
                Apply
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] admin-table">
            <thead>
                <tr>
                    <th class="w-8"></th>
                    <th class="text-left">Order</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Delivery</th>
                    <th class="text-right">Amount</th>
                    <th class="text-left">Payment</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusCfg = [
                            'placed'         => ['bg-amber-100 text-amber-800',  '⏳'],
                            'confirmed'      => ['bg-blue-100 text-blue-800',    '✅'],
                            'shipped'        => ['bg-purple-100 text-purple-800','🚚'],
                            'delivered'      => ['bg-green-100 text-green-800',  '🎉'],
                            'cancelled'      => ['bg-red-100 text-red-800',      '❌'],
                            'payment_failed' => ['bg-red-100 text-red-800',      '💳'],
                        ];
                        [$sc, $icon] = $statusCfg[$order->status] ?? ['bg-slate-100 text-slate-700', '📦'];
                    @endphp
                    <tr class="group">
                        <td>
                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                   class="order-checkbox h-4 w-4 rounded border-slate-300 text-blue-600">
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="font-mono text-xs font-bold text-blue-700 hover:underline">
                                {{ $order->order_number }}
                            </a>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $order->items_count ?? $order->items->count() }} item(s)
                            </p>
                        </td>
                        <td>
                            <p class="font-semibold text-slate-800 text-xs">{{ $order->customer_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $order->customer_phone }}</p>
                            @if($order->user)
                                <p class="text-[10px] text-blue-500">{{ $order->user->email }}</p>
                            @endif
                        </td>
                        <td>
                            <p class="text-xs font-semibold text-slate-700">{{ $order->delivery_area }}</p>
                            <p class="text-[10px] text-slate-400">{{ $order->delivery_pin }}</p>
                        </td>
                        <td class="text-right font-bold text-slate-900 text-sm">
                            ₹{{ number_format($order->totalRupees(), 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $order->payment_method === 'online' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $order->payment_method === 'online' ? '💳 Online' : '💵 COD' }}
                            </span>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-green-100 text-green-800 mt-1">Paid</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-red-100 text-red-800 mt-1">Failed</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-600 mt-1">Pending</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge status-badge {{ $sc }}">{{ $icon }} {{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="text-xs text-slate-500 whitespace-nowrap">
                            {{ $order->created_at->format('d M Y') }}<br>
                            <span class="text-[10px]">{{ $order->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn-sm w-full sm:w-auto bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-center">
                                    View
                                </a>
                                {{-- Quick status change via JS fetch (avoids nested form issue) --}}
                                <select
                                    class="quick-status rounded-lg border border-slate-200 py-1 px-2 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-white cursor-pointer w-full sm:w-auto"
                                    data-url="{{ route('admin.orders.updateStatus', $order) }}"
                                    data-order="{{ $order->order_number }}">
                                    @foreach(['placed','confirmed','shipped','delivered','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <p class="text-4xl mb-3">📭</p>
                            <p class="text-sm font-semibold text-slate-600">No orders found</p>
                            <p class="text-xs text-slate-400 mt-1">Try adjusting your filters</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</form>{{-- end bulk form --}}

{{-- Pagination --}}
@if($orders->hasPages())
    <div class="flex justify-center">
        {{ $orders->links() }}
    </div>
@endif

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ── Quick inline status change (fetch PATCH) ── */
document.querySelectorAll('.quick-status').forEach(function (sel) {
    sel.addEventListener('change', async function () {
        const url    = this.dataset.url;
        const order  = this.dataset.order;
        const status = this.value;
        const orig   = this.querySelector('[selected]')?.value || this.value;

        this.disabled = true;

        try {
            const res = await fetch(url, {
                method: 'POST',          // Laravel tunnels PATCH via _method
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({ _method: 'PATCH', status }),
            });

            if (res.ok || res.redirected) {
                // Update the badge in the same row
                const row    = this.closest('tr');
                const badge  = row.querySelector('.status-badge');
                const colors = {
                    placed:    'bg-amber-100 text-amber-800',
                    confirmed: 'bg-blue-100 text-blue-800',
                    shipped:   'bg-purple-100 text-purple-800',
                    delivered: 'bg-green-100 text-green-800',
                    cancelled: 'bg-red-100 text-red-800',
                };
                const icons = { placed:'⏳', confirmed:'✅', shipped:'🚚', delivered:'🎉', cancelled:'❌' };
                if (badge) {
                    badge.className = 'badge status-badge ' + (colors[status] || 'bg-slate-100 text-slate-700');
                    badge.textContent = (icons[status] || '📦') + ' ' + status.charAt(0).toUpperCase() + status.slice(1);
                }
                // Mark new selected
                this.querySelectorAll('option').forEach(o => o.removeAttribute('selected'));
                this.querySelector(`option[value="${status}"]`)?.setAttribute('selected', 'selected');
            } else {
                alert('Failed to update status. Please try again.');
                this.value = orig;
            }
        } catch (e) {
            alert('Network error. Please try again.');
            this.value = orig;
        }

        this.disabled = false;
    });
});

/* ── Bulk action ── */
(function () {
    const selectAll  = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const countLabel = document.getElementById('selected-count');
    const bulkApply  = document.getElementById('bulk-apply');
    const bulkStatus = document.getElementById('bulk-status');

    function updateCount() {
        const checked = document.querySelectorAll('.order-checkbox:checked').length;
        countLabel.textContent = checked + ' selected';
        bulkApply.disabled = checked === 0 || bulkStatus.value === '';
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    bulkStatus.addEventListener('change', updateCount);

    document.getElementById('bulk-form').addEventListener('submit', function (e) {
        const checked = document.querySelectorAll('.order-checkbox:checked').length;
        if (checked === 0 || bulkStatus.value === '') { e.preventDefault(); return; }
        if (!confirm(`Change ${checked} order(s) to "${bulkStatus.value}"?`)) e.preventDefault();
    });
})();
</script>
@endpush
