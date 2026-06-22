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
            'image' => asset('Images/box.png'),
            'color' => 'bg-blue-50'
        ],
        [
            'label' => 'Pending',
            'value' => $pendingOrders,
            'image' => asset('Images/shipping-and-delivery.png'),
            'color' => 'bg-amber-50'
        ],
        [
            'label' => "Today's Orders",
            'value' => $todayOrders,
            'image' => asset('Images/calendar.png'),
            'color' => 'bg-purple-50'
        ],
        [
            'label' => 'Total Revenue',
            'value' => '₹' . number_format($totalRevenue, 2),
            'image' => asset('Images/revenue.png'),
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
<style>
.order-filter-form {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    margin-bottom: 20px;
}
.order-filter-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    align-items: end;
}
.order-filter-search {
    grid-column: span 2;
    position: relative;
}
@media (max-width: 640px) {
    .order-filter-search { grid-column: span 1; }
}
.order-filter-search svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px; height: 16px;
    color: #94a3b8;
    pointer-events: none;
}
.order-filter-input,
.order-filter-select {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 13px;
    color: #1e293b;
    background: #fff;
    outline: none;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
}
.order-filter-input:focus,
.order-filter-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.order-filter-search .order-filter-input {
    padding-left: 36px;
}
.order-filter-date-group label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.order-filter-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    grid-column: 1 / -1;
    padding-top: 4px;
}
.order-filter-btn-apply {
    width: 120px;
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 9px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
}
.order-filter-btn-apply:hover { background: #1d4ed8; }
.order-filter-btn-reset {
    width: 120px;
    background: #fff;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 9px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    display: inline-block;
    transition: background .15s;
}
.order-filter-btn-reset:hover { background: #f8fafc; }

/* Active filter chips */
.order-filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.order-filter-chips-label {
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
}
.order-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
}
.order-filter-chip.chip-status   { background: #dbeafe; color: #1e40af; }
.order-filter-chip.chip-payment  { background: #e0e7ff; color: #3730a3; }
.order-filter-chip.chip-search   { background: #f1f5f9; color: #334155; }
.order-filter-chip.chip-date     { background: #f3e8ff; color: #6b21a8; }
</style>

<form method="GET" action="{{ route('admin.orders.index') }}" class="order-filter-form">
    <div class="order-filter-grid">

        {{-- Search --}}
        <div class="order-filter-search">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Order #, name, phone, pincode…"
                   class="order-filter-input">
        </div>

        {{-- Status --}}
        <select name="status" id="filter-status" class="order-filter-select">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
            @foreach(['placed','confirmed','shipped','delivered','cancelled','payment_failed','payment_review'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>

        {{-- Payment method --}}
        <select name="payment" id="filter-payment" class="order-filter-select">
            <option value="all" {{ request('payment', 'all') === 'all' ? 'selected' : '' }}>All Payments</option>
            <option value="online" {{ request('payment') === 'online' ? 'selected' : '' }}>Online</option>
            <option value="cod"    {{ request('payment') === 'cod'    ? 'selected' : '' }}>COD</option>
        </select>

        {{-- Date from --}}
        <div class="order-filter-date-group">
            <label for="filter-from">From</label>
            <input type="date" id="filter-from" name="from" value="{{ request('from') }}"
                   class="order-filter-input">
        </div>

        {{-- Date to --}}
        <div class="order-filter-date-group">
            <label for="filter-to">To</label>
            <input type="date" id="filter-to" name="to" value="{{ request('to') }}"
                   class="order-filter-input">
        </div>

        {{-- Buttons --}}
        <div class="order-filter-actions">
            <button type="submit" class="order-filter-btn-apply">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="order-filter-btn-reset">Reset</a>
        </div>

    </div>

    {{-- Active filter chips --}}
    @if(request('status') && request('status') !== 'all' || request('payment') && request('payment') !== 'all' || request('q') || request('from') || request('to'))
    <div class="order-filter-chips">
        <span class="order-filter-chips-label">Active filters:</span>
        @if(request('status') && request('status') !== 'all')
            <span class="order-filter-chip chip-status">Status: {{ ucfirst(request('status')) }}</span>
        @endif
        @if(request('payment') && request('payment') !== 'all')
            <span class="order-filter-chip chip-payment">Payment: {{ ucfirst(request('payment')) }}</span>
        @endif
        @if(request('q'))
            <span class="order-filter-chip chip-search">Search: "{{ request('q') }}"</span>
        @endif
        @if(request('from') || request('to'))
            <span class="order-filter-chip chip-date">📅 {{ request('from') ?: 'Start' }} → {{ request('to') ?: 'End' }}</span>
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
                    <option value="{{ $s }}"> {{ ucfirst($s) }}</option>
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
                'image' => asset('Images/hourglass.gif')
            ],
            'confirmed' => [
                'class' => 'bg-blue-100 text-blue-800',
                'image' => asset('Images/check.png')
            ],
            'shipped' => [
                'class' => 'bg-purple-100 text-purple-800',
                'image' => asset('Images/package.png')
            ],
            'delivered' => [
                'class' => 'bg-green-100 text-green-800',
                'image' => asset('Images/Deliver.png')
            ],
            'cancelled' => [
                'class' => 'bg-red-100 text-red-800',
                'image' => asset('Images/prohibition.png')
            ],
            'payment_failed' => [
                'class' => 'bg-red-100 text-red-800',
                'image' => asset('Images/sad.png')
            ],
            'payment_review' => [
                'class' => 'bg-amber-100 text-amber-800',
                'image' => asset('Images/credit-card.png')
            ],
            'refunded' => [
                'class' => 'bg-orange-100 text-orange-800',
                'image' => asset('Images/refund.png')
            ],
            'refund_initiated' => [
                'class' => 'bg-yellow-100 text-yellow-800',
                'image' => asset('Images/dollars.png')
            ],
            'Refund_requested' => [
                'class' => 'bg-amber-100 text-amber-800',
                'image' => asset('Images/hourglass.gif')
            ],
        ];

        $status = $statusCfg[$order->status] ?? [
            'class' => 'bg-slate-100 text-slate-700',
            'image' => asset('Images/box.png')
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
                        <td style="width:150px;">
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
                            @php
                                // Check if order has active refund request
                                $hasActiveRefund = $order->refunds && $order->refunds->whereIn('status', ['requested', 'approved', 'processing', 'processed'])->isNotEmpty();
                            @endphp
                            
                            @if($hasActiveRefund)
                                {{-- Order has refund request - show link to refunds page --}}
                                <a href="{{ route('admin.refunds.show', $order->refunds->first()) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 px-3 py-1.5 text-xs font-bold transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    View in Refunds
                                </a>
                            @else
                                {{-- Normal order - show view button and status dropdown --}}
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
                                        data-show-url="{{ route('admin.orders.show', $order) }}"
                                        data-payment-method="{{ $order->payment_method }}"
                                        data-payment-status="{{ $order->payment_status }}"
                                        data-current="{{ $order->status }}">
                                        @php
                                            $flow        = ['placed','confirmed','shipped','delivered'];
                                            $currentIdx  = array_search($order->status, $flow);
                                        @endphp
                                        {{-- Current status as disabled label --}}
                                        <option value="{{ $order->status }}" selected disabled>
                                            {{ ucfirst(str_replace('_',' ',$order->status)) }}
                                        </option>
                                        {{-- Only forward steps --}}
                                        @if($currentIdx !== false)
                                            @foreach(array_slice($flow, $currentIdx + 1) as $s)
                                                <option value="{{ $s }}"> {{ ucfirst($s) }}</option>
                                            @endforeach
                                        @endif
                                        @if(!in_array($order->status, ['cancelled','delivered']))
                                            <option value="cancelled" style="color:#ef4444">❌ Cancel</option>
                                        @endif
                                    </select>
                                </div>
                            @endif
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
        const status  = this.value;
        const current = this.dataset.current;

        // Cancel must be done on the order detail page (requires a reason)
        if (status === 'cancelled') {
            this.value = current;
            window.location.href = showUrl;
            return;
        }

        this.disabled = true;

        // Show confirmation modal, only proceed on confirm
        const confirmed = await showIndexStatusConfirm(status);
        if (!confirmed) {
            this.value = current;
            this.disabled = false;
            return;
        }

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
                    Refund_requested:  'bg-amber-100 text-amber-800',
                    payment_review:          'bg-amber-100 text-amber-800',
                };
                const images = {
                    placed:                  '{{ asset('Images/hourglass.gif') }}',
                    confirmed:               '{{ asset('Images/check.png') }}',
                    shipped:                 '{{ asset('Images/package.png') }}',
                    delivered:               '{{ asset('Images/confetti.png') }}',
                    cancelled:               '{{ asset('Images/letter-x.png') }}',
                    payment_failed:          '{{ asset('Images/sad.png') }}',
                    refunded:                '{{ asset('Images/refund.png') }}',
                    refund_initiated:        '{{ asset('Images/dollars.png') }}',
                    Refund_requested:        '{{ asset('Images/hourglass.gif') }}',
                    payment_review:          '{{ asset('Images/credit-card.png') }}',
                };

                // Update status badge
                if (badge) {
                    badge.className = 'badge status-badge ' + (colors[status] || 'bg-slate-100 text-slate-700');
                    const imgSrc = images[status] || '{{ asset('Images/box.png') }}';
                    badge.innerHTML = `<img src="${imgSrc}" alt="${status}" class="h-4 w-4 object-contain" style="margin-right:5px;">` + status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
                }

                // If COD order marked delivered → flip payment badge to Paid
                if (status === 'delivered' && this.dataset.paymentMethod === 'cod') {
                    const paymentBadges = row.querySelectorAll('.badge');
                    paymentBadges.forEach(b => {
                        if (b.textContent.trim() === 'Pending') {
                            b.className = 'badge bg-green-100 text-green-800 mt-1';
                            b.textContent = 'Paid';
                        }
                    });
                    this.dataset.paymentStatus = 'paid';
                }

                // Rebuild dropdown: only show forward steps from new status
                const flow = ['placed','confirmed','shipped','delivered'];
                const newIdx = flow.indexOf(status);
                this.dataset.current = status;
                this.innerHTML = `<option value="${status}" selected disabled>${status.charAt(0).toUpperCase() + status.slice(1)}</option>`;
                flow.slice(newIdx + 1).forEach(s => {
                    this.innerHTML += `<option value="${s}"> ${s.charAt(0).toUpperCase() + s.slice(1)}</option>`;
                });
                if (status !== 'delivered') {
                    this.innerHTML += `<option value="cancelled" style="color:#ef4444">❌ Cancel</option>`;
                }

            } else {
                alert('Failed to update status. Please try again.');
            }
        } catch (e) {
            alert('Network error. Please try again.');
        }

        this.disabled = false;
    });
});

/* ── Index status confirm modal ── */
function showIndexStatusConfirm(status) {
    return new Promise(function (resolve) {
        const icons   = { placed:'📋', confirmed:'✅', shipped:'🚚', delivered:'🎉' };
        const colors  = { placed:'#d97706', confirmed:'#2563eb', shipped:'#7c3aed', delivered:'#16a34a' };
        const labels  = { placed:'Placed', confirmed:'Confirmed', shipped:'Shipped', delivered:'Delivered' };
        const extra   = status === 'delivered'
            ? ' COD payment will be marked as Paid.'
            : ' A notification email will be sent to the customer.';

        document.getElementById('iscm-icon').textContent   = icons[status] || '📦';
        document.getElementById('iscm-status').textContent = labels[status] || status;
        document.getElementById('iscm-body').textContent   = `Are you sure you want to mark this order as "${labels[status] || status}"?${extra}`;
        const btn = document.getElementById('iscm-confirm-btn');
        btn.style.background = colors[status] || '#2563eb';

        const modal = document.getElementById('index-status-modal');
        modal.style.display = 'flex';

        btn.onclick = function () { modal.style.display = 'none'; resolve(true); };
        document.getElementById('iscm-cancel-btn').onclick = function () { modal.style.display = 'none'; resolve(false); };
    });
}

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
        // Use custom modal for bulk confirm too
        e.preventDefault();
        const s = bulkStatus.value;
        showBulkConfirm(count, s).then(ok => { if (ok) bulkForm.submit(); });
    });

    function showBulkConfirm(count, status) {
        return new Promise(function (resolve) {
            const labels = { placed:'Placed', confirmed:'Confirmed', shipped:'Shipped', delivered:'Delivered' };
            document.getElementById('iscm-icon').textContent   = '📦';
            document.getElementById('iscm-status').textContent = labels[status] || status;
            document.getElementById('iscm-body').textContent   =
                `Change ${count} order(s) to "${labels[status] || status}"? This cannot be undone.`;
            const btn = document.getElementById('iscm-confirm-btn');
            btn.style.background = '#2563eb';

            const modal = document.getElementById('index-status-modal');
            modal.style.display = 'flex';

            btn.onclick = function () { modal.style.display = 'none'; resolve(true); };
            document.getElementById('iscm-cancel-btn').onclick = function () { modal.style.display = 'none'; resolve(false); };
        });
    }
})();
</script>

{{-- ── Index Status Confirm Modal ── --}}
<div id="index-status-modal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,.25); padding:28px 24px; width:100%; max-width:380px;">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
            <div id="iscm-icon"
                 style="width:44px; height:44px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
                📦
            </div>
            <div>
                <p style="font-size:15px; font-weight:700; color:#0f172a; margin:0;">Confirm Status Change</p>
                <p style="font-size:12px; color:#64748b; margin:2px 0 0;">
                    Move to: <strong id="iscm-status"></strong>
                </p>
            </div>
        </div>
        <p id="iscm-body" style="font-size:13px; color:#475569; margin:0 0 22px; line-height:1.6;"></p>
        <div style="display:flex; gap:10px;">
            <button id="iscm-cancel-btn"
                    style="flex:1; border:1px solid #e2e8f0; background:#fff; border-radius:12px; padding:10px; font-size:13px; font-weight:600; color:#475569; cursor:pointer;">
                Cancel
            </button>
            <button id="iscm-confirm-btn"
                    style="flex:1; border:none; border-radius:12px; padding:10px; font-size:13px; font-weight:700; color:#fff; cursor:pointer; background:#2563eb;">
                Yes, Update
            </button>
        </div>
    </div>
</div>

@endpush
