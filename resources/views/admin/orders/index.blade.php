@extends('admin.layouts.admin')
@section('title', 'Orders')
@section('page-title', 'Order Management')
@section('page-subtitle', 'View, filter and manage all customer orders')

@section('content')

{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:grid-cols-4 mb-6">
    @foreach([
        [
            'label' => 'Total Orders',
            'value' => $totalOrders,
            'image' => asset('images/box.png'),
            'color' => 'bg-blue-50'
        ],
        [
            'label' => 'Pending',
            'value' => $pendingOrders,
            'image' => asset('images/shipping-and-delivery.png'),
            'color' => 'bg-amber-50'
        ],
        [
            'label' => "Today's Orders",
            'value' => $todayOrders,
            'image' => asset('images/calendar.png'),
            'color' => 'bg-purple-50'
        ],
        [
            'label' => 'Total Revenue',
            'value' => '₹' . number_format($totalRevenue, 2),
            'image' => asset('images/revenue.png'),
            'color' => 'bg-green-50'
        ],
    ] as $s)
    <div class="stat-card flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">{{ $s['label'] }}</span>

            <span class="flex h-12 w-12 items-center justify-center rounded-lg {{ $s['color'] }}">
                <img src="{{ $s['image'] }}"
                     alt="{{ $s['label'] }}"
                     class="h-24 w-24 object-contain">
            </span>
        </div>

        <p class="text-2xl font-extrabold text-slate-900">
            {{ $s['value'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- ── FILTERS ── --}}
<form method="GET" action="{{ route('admin.orders.index') }}"
      class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-7">

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
        <select name="status" id="filter-status"
                class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
            @foreach(['placed','confirmed','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>

        {{-- Payment method --}}
        <select name="payment" id="filter-payment"
                class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all" {{ request('payment', 'all') === 'all' ? 'selected' : '' }}>All Payments</option>
            <option value="online" {{ request('payment') === 'online' ? 'selected' : '' }}>Online</option>
            <option value="cod"    {{ request('payment') === 'cod'    ? 'selected' : '' }}>COD</option>
        </select>

        {{-- Date range: From --}}
        <div class="relative">
            <label class="text-xs font-semibold text-slate-500 block mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="w-full rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
        </div>

        {{-- Date range: To --}}
        <div class="relative">
            <label class="text-xs font-semibold text-slate-500 block mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="w-full rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
        </div>

        {{-- Buttons --}}
            <div class="flex gap-2 justify-center col-span-full">
            <button type="submit"
                    class="w-32 rounded-xl bg-blue-600 py-2 px-4 text-sm font-bold text-white hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.orders.index') }}"
               class="w-32 rounded-xl border border-slate-200 py-2 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors text-center">
               Reset
            </a>
        </div>
    </div>

    {{-- Active filter indicator --}}
    @if(request('status') && request('status') !== 'all' || request('payment') && request('payment') !== 'all' || request('q') || request('from') || request('to'))
    <div class="mt-3 flex flex-wrap gap-2 pt-3 border-t border-slate-100">
        <span class="text-xs text-slate-500 font-semibold self-center">Active filters:</span>
        @if(request('status') && request('status') !== 'all')
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800">
                Status: {{ ucfirst(request('status')) }}
            </span>
        @endif
        @if(request('payment') && request('payment') !== 'all')
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-bold text-indigo-800">
                Payment: {{ ucfirst(request('payment')) }}
            </span>
        @endif
        @if(request('q'))
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                Search: "{{ request('q') }}"
            </span>
        @endif
        @if(request('from') || request('to'))
            <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-800">
                📅 {{ request('from') ? request('from') : 'Start' }} → {{ request('to') ? request('to') : 'End' }}
            </span>
        @endif
    </div>
    @endif
</form>

{{-- ── BULK ACTION FORM ── --}}
<form method="POST" action="{{ route('admin.orders.bulkStatus') }}" id="bulk-form">
    @csrf

{{-- ── TABLE ── --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4">

    {{-- Bulk toolbar --}}
    <div class="flex items-center justify-between gap-3 px-5 py-3 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer">
                <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-slate-300 text-blue-600">
                Select all
            </label>
            <span class="text-xs text-slate-400" id="selected-count">0 selected</span>
        </div>
        <div class="flex items-center gap-3">
            <select name="status" id="bulk-status"
                    class="rounded-lg border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
                <option value="">Change status to</option>
                @foreach(['placed','confirmed','shipped','delivered'] as $s)
                    <option value="{{ $s }}">➜ {{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" id="bulk-apply"
                    class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                Apply
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full admin-table">
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
            'placed' => [
                'class' => 'bg-amber-100 text-amber-800',
                'image' => asset('images/hourglass.gif')
            ],
            'confirmed' => [
                'class' => 'bg-blue-100 text-blue-800',
                'image' => asset('images/check.png')
            ],
            'shipped' => [
                'class' => 'bg-purple-100 text-purple-800',
                'image' => asset('images/package.png')
            ],
            'delivered' => [
                'class' => 'bg-green-100 text-green-800',
                'image' => asset('images/confetti.png')
            ],
            'cancelled' => [
                'class' => 'bg-red-100 text-red-800',
                'image' => asset('images/letter-x.png')
            ],
            'payment_failed' => [
                'class' => 'bg-red-100 text-red-800',
                'image' => asset('images/sad.png')
            ],
            'refunded' => [
                'class' => 'bg-orange-100 text-orange-800',
                'image' => asset('images/refund.png')
            ],
            'refund_initiated' => [
                'class' => 'bg-yellow-100 text-yellow-800',
                'image' => asset('images/dollars.png')
            ],
            'cancellation_requested' => [
                'class' => 'bg-amber-100 text-amber-800',
                'image' => asset('images/hourglass.gif')
            ],
        ];

        $status = $statusCfg[$order->status] ?? [
            'class' => 'bg-slate-100 text-slate-700',
            'image' => asset('images/box.png')
        ];

        $sc = $status['class'];
        $image = $status['image'];
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
                                {{ $order->payment_method === 'online' ? 'Online' : 'COD' }}
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
                            <span class="badge status-badge {{ $sc }}"><img src="{{ $image }}"
     alt="{{ $order->status }}"
     class="h-4 w-4 object-contain" style="margin-right:5px;"> {{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="text-xs text-slate-500 whitespace-nowrap">
                            {{ $order->created_at->format('d M Y') }}<br>
                            <span class="text-[10px]">{{ $order->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn-sm bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                    View
                                </a>
                                {{-- Quick status change via JS fetch (avoids nested form issue) --}}
                                <select
                                    class="quick-status rounded-lg border border-slate-200 py-1 pl-2 pr-6 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-white cursor-pointer"
                                    style="width:110px"
                                    data-url="{{ route('admin.orders.updateStatus', $order) }}"
                                    data-order="{{ $order->order_number }}"
                                    data-show-url="{{ route('admin.orders.show', $order) }}">
                                    @foreach(['placed','confirmed','shipped','delivered'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                    @if($order->status === 'cancelled')
                                        <option value="cancelled" selected>Cancelled</option>
                                    @else
                                        <option value="cancelled" disabled style="color:#ef4444">❌ Cancel</option>
                                    @endif
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

/* ── Auto-submit filter dropdowns on change ── */
document.getElementById('filter-status').addEventListener('change', function () {
    this.closest('form').submit();
});
document.getElementById('filter-payment').addEventListener('change', function () {
    this.closest('form').submit();
});

/* ── Quick inline status change (fetch PATCH) ── */
document.querySelectorAll('.quick-status').forEach(function (sel) {
    sel.addEventListener('change', async function () {
        const url     = this.dataset.url;
        const showUrl = this.dataset.showUrl;
        const order   = this.dataset.order;
        const status  = this.value;
        const orig    = this.querySelector('[selected]')?.value || this.value;

        // Cancel must be done on the order detail page (requires a reason)
        if (status === 'cancelled') {
            this.value = orig;
            window.location.href = showUrl;
            return;
        }

        this.disabled = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({ _method: 'PATCH', status }),
            });

            if (res.ok || res.redirected) {
                const row    = this.closest('tr');
                const badge  = row.querySelector('.status-badge');
                const colors = {
                    placed:                  'bg-amber-100 text-amber-800',
                    confirmed:               'bg-blue-100 text-blue-800',
                    shipped:                 'bg-purple-100 text-purple-800',
                    delivered:               'bg-green-100 text-green-800',
                    cancelled:               'bg-red-100 text-red-800',
                    payment_failed:          'bg-red-100 text-red-800',
                    refunded:                'bg-orange-100 text-orange-800',
                    refund_initiated:        'bg-yellow-100 text-yellow-800',
                    cancellation_requested:  'bg-amber-100 text-amber-800',
                };
                const images = {
                    placed:                  '{{ asset('images/hourglass.gif') }}',
                    confirmed:               '{{ asset('images/check.png') }}',
                    shipped:                 '{{ asset('images/package.png') }}',
                    delivered:               '{{ asset('images/confetti.png') }}',
                    cancelled:               '{{ asset('images/letter-x.png') }}',
                    payment_failed:          '{{ asset('images/sad.png') }}',
                    refunded:                '{{ asset('images/refund.png') }}',
                    refund_initiated:        '{{ asset('images/dollars.png') }}',
                    cancellation_requested:  '{{ asset('images/hourglass.gif') }}',
                };
                if (badge) {
                    badge.className = 'badge status-badge ' + (colors[status] || 'bg-slate-100 text-slate-700');
                    const imgSrc = images[status] || '{{ asset('images/box.png') }}';
                    badge.innerHTML = `<img src="${imgSrc}" alt="${status}" class="h-4 w-4 object-contain" style="margin-right:5px;">` + status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
                }
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
    const bulkForm   = document.getElementById('bulk-form');

    function getCheckedCount() {
        return document.querySelectorAll('.order-checkbox:checked').length;
    }

    function updateUI() {
        const count  = getCheckedCount();
        const ready  = count > 0 && bulkStatus.value !== '';
        countLabel.textContent = count > 0 ? count + ' selected' : '0 selected';

        bulkApply.disabled = !ready;

        // Sync select-all checkbox state
        selectAll.indeterminate = count > 0 && count < checkboxes.length;
        selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateUI));
    bulkStatus.addEventListener('change', updateUI);

    bulkForm.addEventListener('submit', function (e) {
        const count = getCheckedCount();
        if (count === 0) {
            e.preventDefault();
            alert('Please select at least one order.');
            return;
        }
        if (!bulkStatus.value) {
            e.preventDefault();
            alert('Please choose a status to apply.');
            return;
        }
        if (!confirm('Change ' + count + ' order(s) to "' + bulkStatus.value + '"?')) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
