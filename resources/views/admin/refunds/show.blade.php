@extends('admin.layouts.admin')
@section('title', 'Refund ' . $refund->refund_number)
@section('page-title', 'Refund ' . $refund->refund_number)
@section('page-subtitle', 'Review and take action on this refund request')

@section('content')

@php [$badgeClass, $badgeLabel] = $refund->statusBadge(); @endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.refunds.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Refunds
    </a>
    <span class="badge {{ $badgeClass }} text-sm px-4 py-1.5">{{ $badgeLabel }}</span>
</div>

<div class="grid gap-5 lg:grid-cols-3">

    {{-- Left: main info --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Refund details --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Refund Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-xs text-slate-500">Refund #</p><p class="font-mono font-bold text-slate-800">{{ $refund->refund_number }}</p></div>
                <div><p class="text-xs text-slate-500">Amount</p><p class="font-bold text-slate-900 text-lg">₹{{ number_format($refund->amountRupees(), 2) }}</p></div>
                <div><p class="text-xs text-slate-500">Type</p>
                    @php
                        $typeBadge = match($refund->type) {
                            'gateway'              => ['bg-indigo-100 text-indigo-800',  '💳 Gateway (Auto)'],
                            'online_bank_transfer' => ['bg-blue-100 text-blue-800',      '🏦 Online Bank Transfer'],
                            'online_upi'           => ['bg-violet-100 text-violet-800',  '📱 Online UPI'],
                            'cod_upi'              => ['bg-purple-100 text-purple-800',  '📱 COD UPI'],
                            default                => ['bg-amber-100 text-amber-800',    '🏦 COD Bank Transfer'],
                        };
                    @endphp
                    <span class="badge {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
                </div>
                <div><p class="text-xs text-slate-500">Requested</p><p class="font-medium text-slate-700">{{ $refund->created_at->format('d M Y, h:i A') }}</p></div>
                @if($refund->processed_at)
                    <div><p class="text-xs text-slate-500">Processed</p><p class="font-medium text-slate-700">{{ $refund->processed_at->format('d M Y, h:i A') }}</p></div>
                @endif
                @if($refund->refund_id_gateway)
                    <div class="col-span-2"><p class="text-xs text-slate-500">Gateway Refund ID</p><p class="font-mono text-xs text-slate-700 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200">{{ $refund->refund_id_gateway }}</p></div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Customer Reason</p>
                <p class="text-sm text-slate-700 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200">{{ $refund->reason }}</p>
            </div>

            @if($refund->admin_notes)
                <div class="mt-3">
                    <p class="text-xs text-slate-500 mb-1">Admin Notes</p>
                    <p class="text-sm text-slate-700 bg-amber-50 rounded-xl px-4 py-3 border border-amber-200">{{ $refund->admin_notes }}</p>
                </div>
            @endif

            @if($refund->proof_image_path)
                <div class="mt-3">
                    <p class="text-xs text-slate-500 mb-2">Customer Proof Image</p>
                    <a href="{{ asset('storage/' . $refund->proof_image_path) }}" target="_blank" class="block">
                        <img src="{{ asset('storage/' . $refund->proof_image_path) }}"
                             alt="Refund Proof"
                             class="max-w-full max-h-64 rounded-xl border-2 border-slate-200 object-contain hover:opacity-90 hover:border-blue-400 transition-all shadow-sm">
                    </a>
                    <p class="text-[10px] text-slate-400 mt-1">Click to view full size</p>
                </div>
            @endif
        </div>

        {{-- Bank details (COD bank transfer) --}}
        @if($refund->type === 'cod_bank_transfer' && $refund->bank_account_number)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-amber-900 mb-4">🏦 Bank Transfer Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><p class="text-xs text-amber-700">Account Name</p><p class="font-bold text-slate-800">{{ $refund->bank_account_name }}</p></div>
                    <div><p class="text-xs text-amber-700">Account Number</p><p class="font-mono font-bold text-slate-800">{{ $refund->bank_account_number }}</p></div>
                    <div><p class="text-xs text-amber-700">IFSC Code</p><p class="font-mono font-bold text-slate-800">{{ $refund->bank_ifsc }}</p></div>
                </div>
            </div>
        @endif

        {{-- UPI details (COD UPI) --}}
        @if($refund->type === 'cod_upi' && $refund->upi_id)
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-purple-900 mb-4">📱 UPI Refund Details</h3>
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-xl flex-shrink-0">📱</div>
                    <div>
                        <p class="text-xs text-purple-700 mb-0.5">UPI ID</p>
                        <p class="font-mono font-bold text-slate-900 text-base">{{ $refund->upi_id }}</p>
                        <p class="text-xs text-purple-600 mt-0.5">Transfer the refund amount directly to this UPI ID</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Bank details (Online bank transfer) --}}
        @if($refund->type === 'online_bank_transfer' && $refund->bank_account_number)
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-blue-900 mb-4">🏦 Online Refund — Bank Transfer Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><p class="text-xs text-blue-700">Account Name</p><p class="font-bold text-slate-800">{{ $refund->bank_account_name }}</p></div>
                    <div><p class="text-xs text-blue-700">Account Number</p><p class="font-mono font-bold text-slate-800">{{ $refund->bank_account_number }}</p></div>
                    <div><p class="text-xs text-blue-700">IFSC Code</p><p class="font-mono font-bold text-slate-800">{{ $refund->bank_ifsc }}</p></div>
                </div>
            </div>
        @endif

        {{-- UPI details (Online UPI) --}}
        @if($refund->type === 'online_upi' && $refund->upi_id)
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
                <h3 class="text-sm font-bold text-violet-900 mb-4">📱 Online Refund — UPI Details</h3>
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-xl flex-shrink-0">📱</div>
                    <div>
                        <p class="text-xs text-violet-700 mb-0.5">UPI ID</p>
                        <p class="font-mono font-bold text-slate-900 text-base">{{ $refund->upi_id }}</p>
                        <p class="text-xs text-violet-600 mt-0.5">Transfer the refund amount to this UPI ID</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Order items --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-bold text-slate-900">Order Items</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($refund->order->items as $item)
                    <div class="flex items-center gap-4 px-5 py-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-black text-blue-700">
                            {{ strtoupper(substr($item->medicine_name_snapshot, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $item->medicine_name_snapshot }}</p>
                            <p class="text-xs text-slate-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price_paise/100, 2) }}</p>
                        </div>
                        <p class="text-sm font-bold text-slate-900">₹{{ number_format($item->line_total_paise/100, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-between text-sm font-bold text-slate-900">
                <span>Total Refund Amount</span>
                <span>₹{{ number_format($refund->amountRupees(), 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Right: actions --}}
    <div class="space-y-5">

        {{-- Order info --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Order Info</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Order #</span><a href="{{ route('admin.orders.show', $refund->order) }}" class="font-mono font-bold text-blue-700 hover:underline">{{ $refund->order->order_number }}</a></div>
                <div class="flex justify-between"><span class="text-slate-500">Customer</span><span class="font-semibold">{{ $refund->order->customer_name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Payment</span><span class="font-semibold">{{ $refund->order->payment_method === 'online' ? '💳 Online' : '💵 COD' }}</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Dispatched?</span>
                    <form method="POST" action="{{ route('admin.orders.toggleDispatched', $refund->order) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="rounded-full px-3 py-1 text-xs font-bold transition-colors
                                       {{ $refund->order->is_dispatched ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            {{ $refund->order->is_dispatched ? '✓ Dispatched' : '✗ Not Dispatched' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Action buttons --}}
        @if($refund->status === 'requested')
            {{-- Approve --}}
            <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
                <h3 class="text-sm font-bold text-green-900 mb-3">✅ Approve Refund</h3>
                <p class="text-xs text-green-700 mb-3">
                    @if(in_array($refund->type, ['gateway']))
                        This will trigger an automatic refund via Razorpay.
                    @else
                        This will mark the refund as approved. Process the transfer manually.
                    @endif
                </p>
                <form method="POST" action="{{ route('admin.refunds.approve', $refund) }}" id="form-approve">
                    @csrf
                    <button type="button"
                            onclick="openConfirmModal('approve')"
                            class="w-full rounded-xl py-2 text-sm font-bold text-white hover:opacity-80 transition-opacity" style="background-color:green">
                        Approve & Process
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div class="rounded-2xl border border-red-500 bg-red-50 p-5">
                <h3 class="text-sm font-bold text-red-900 mb-3">🚫 Reject Refund</h3>
                <form method="POST" action="{{ route('admin.refunds.reject', $refund) }}" id="form-reject" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" id="reject-notes" rows="3" required
                              placeholder="Reason for rejection (visible to admin only)…"
                              class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-xs focus:outline-none focus:border-red-400 resize-none"></textarea>
                    <button type="button"
                            onclick="openConfirmModal('reject')"
                            class="w-full rounded-xl py-2.5 text-sm font-bold text-white transition-colors hover:opacity-80" style="background-color:red">
                        Reject Request
                    </button>
                </form>
            </div>
        @endif

        {{-- Mark processed (COD bank transfer approved) --}}
        @if(in_array($refund->status, ['approved', 'processing']) && $refund->type === 'cod_bank_transfer')
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <h3 class="text-sm font-bold text-blue-900 mb-3">💸 Mark Bank Transfer Done</h3>
                <form method="POST" action="{{ route('admin.refunds.markProcessed', $refund) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" rows="3" required
                              placeholder="Transfer reference / UTR number…"
                              class="w-full rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs focus:outline-none focus:border-blue-400 resize-none"></textarea>
                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors">
                        Mark as Processed
                    </button>
                </form>
            </div>
        @endif

        {{-- Mark processed (COD UPI approved) --}}
        @if(in_array($refund->status, ['approved', 'processing']) && $refund->type === 'cod_upi')
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-5">
                <h3 class="text-sm font-bold text-purple-900 mb-1">📱 Mark UPI Transfer Done</h3>
                <p class="text-xs text-purple-700 mb-3">UPI ID: <span class="font-mono font-bold">{{ $refund->upi_id }}</span></p>
                <form method="POST" action="{{ route('admin.refunds.markProcessed', $refund) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" rows="3" required
                              placeholder="UPI transaction reference / UTR number…"
                              class="w-full rounded-xl border border-purple-200 bg-white px-3 py-2 text-xs focus:outline-none focus:border-purple-400 resize-none"></textarea>
                    <button type="submit"
                            class="w-full rounded-xl bg-purple-600 py-2.5 text-sm font-bold text-white hover:bg-purple-700 transition-colors">
                        Mark as Processed
                    </button>
                </form>
            </div>
        @endif

        {{-- Mark processed (Online bank transfer approved) --}}
        @if(in_array($refund->status, ['approved', 'processing']) && $refund->type === 'online_bank_transfer')
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <h3 class="text-sm font-bold text-blue-900 mb-1">🏦 Mark Online Bank Transfer Done</h3>
                <p class="text-xs text-blue-700 mb-3">
                    Account: <span class="font-mono font-bold">{{ $refund->bank_account_number }}</span> · IFSC: <span class="font-mono font-bold">{{ $refund->bank_ifsc }}</span>
                </p>
                <form method="POST" action="{{ route('admin.refunds.markProcessed', $refund) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" rows="3" required
                              placeholder="Transfer reference / UTR number…"
                              class="w-full rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs focus:outline-none focus:border-blue-400 resize-none"></textarea>
                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors">
                        Mark as Processed
                    </button>
                </form>
            </div>
        @endif

        {{-- Mark processed (Online UPI approved) --}}
        @if(in_array($refund->status, ['approved', 'processing']) && $refund->type === 'online_upi')
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">
                <h3 class="text-sm font-bold text-violet-900 mb-1">📱 Mark Online UPI Transfer Done</h3>
                <p class="text-xs text-violet-700 mb-3">UPI ID: <span class="font-mono font-bold">{{ $refund->upi_id }}</span></p>
                <form method="POST" action="{{ route('admin.refunds.markProcessed', $refund) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_notes" rows="3" required
                              placeholder="UPI transaction reference / UTR number…"
                              class="w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-xs focus:outline-none focus:border-violet-400 resize-none"></textarea>
                    <button type="submit"
                            class="w-full rounded-xl bg-violet-600 py-2.5 text-sm font-bold text-white hover:bg-violet-700 transition-colors">
                        Mark as Processed
                    </button>
                </form>
            </div>
        @endif

        {{-- Gateway metadata --}}
        @if($refund->metadata)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Gateway Response</h3>
                <pre class="text-[10px] text-slate-600 bg-slate-50 rounded-xl p-3 overflow-x-auto border border-slate-200 max-h-48">{{ json_encode($refund->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
</div>

{{-- Audit Log --}}
@if($refund->auditLogs->isNotEmpty())
<div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-900">📋 Audit Log</h3>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($refund->auditLogs->sortByDesc('created_at') as $log)
            <div class="flex items-start gap-4 px-5 py-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold
                    {{ $log->actor_type === 'admin' ? 'bg-blue-100 text-blue-700' : ($log->actor_type === 'webhook' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ strtoupper(substr($log->actor_type, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-slate-800">{{ ucfirst($log->action) }}</span>
                        @if($log->from_status && $log->to_status)
                            <span class="text-[10px] text-slate-400">{{ $log->from_status }} → {{ $log->to_status }}</span>
                        @endif
                        <span class="text-[10px] font-semibold rounded-full px-2 py-0.5
                            {{ $log->actor_type === 'admin' ? 'bg-blue-50 text-blue-600' : ($log->actor_type === 'webhook' ? 'bg-purple-50 text-purple-600' : 'bg-slate-50 text-slate-500') }}">
                            {{ ucfirst($log->actor_type) }}
                        </span>
                    </div>
                    @if($log->notes)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $log->notes }}</p>
                    @endif
                </div>
                <span class="text-[10px] text-slate-400 flex-shrink-0">{{ $log->created_at->format('d M Y, h:i A') }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
{{-- ── Confirmation Modal ─────────────────────────────────────────────────── --}}
<div id="confirm-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display:none !important;">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeConfirmModal()"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6 z-10">
        <div id="modal-icon" class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full text-2xl"></div>
        <h3 id="modal-title" class="text-center text-base font-bold text-slate-900 mb-1"></h3>
        <p  id="modal-body"  class="text-center text-sm text-slate-500 mb-6"></p>
        <div class="flex gap-3">
            <button type="button"
                    onclick="closeConfirmModal()"
                    class="flex-1 rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button type="button"
                    id="modal-confirm-btn"
                    class="flex-1 rounded-xl py-2.5 text-sm font-bold text-white transition-colors">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let _pendingAction = null;

function openConfirmModal(action) {
    _pendingAction = action;

    const icon  = document.getElementById('modal-icon');
    const title = document.getElementById('modal-title');
    const body  = document.getElementById('modal-body');
    const btn   = document.getElementById('modal-confirm-btn');

    if (action === 'approve') {
        icon.textContent  = '✅';
        icon.className    = 'mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full text-2xl bg-green-100';
        title.textContent = 'Approve this refund?';
        body.textContent  = 'This will process the refund. This action cannot be undone.';
        btn.style.backgroundColor = 'green';
        btn.className     = 'flex-1 rounded-xl py-2.5 text-sm font-bold text-white hover:opacity-80 transition-opacity';
    } else {
        const notes = document.getElementById('reject-notes');
        if (!notes || !notes.value.trim()) {
            showAdminToast('Please enter a rejection reason before proceeding.', 'error');
            return;
        }
        icon.textContent  = '🚫';
        icon.className    = 'mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full text-2xl bg-red-100';
        title.textContent = 'Reject this refund?';
        body.textContent  = 'The customer will be notified that their refund was rejected.';
        btn.style.backgroundColor = 'red';
        btn.className     = 'flex-1 rounded-xl py-2.5 text-sm font-bold text-white hover:opacity-80 transition-opacity';
    }

    btn.onclick = confirmAction;

    const modal = document.getElementById('confirm-modal');
    modal.style.removeProperty('display');
    modal.style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirm-modal').style.display = 'none';
    _pendingAction = null;
}

function confirmAction() {
    const action = _pendingAction;
    closeConfirmModal();
    if (action === 'approve') {
        document.getElementById('form-approve').submit();
    } else if (action === 'reject') {
        document.getElementById('form-reject').submit();
    }
}

// Close on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeConfirmModal();
});

function showAdminToast(message, type) {
    // Reuse the admin flash style inline
    const existing = document.getElementById('admin-inline-toast');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.id = 'admin-inline-toast';
    div.style.cssText = `
        position:fixed; top:20px; right:20px; z-index:9999;
        padding:14px 20px; border-radius:12px; font-size:13px; font-weight:600;
        color:#fff; max-width:340px; box-shadow:0 8px 24px rgba(0,0,0,.18);
        background: ${type === 'error'
            ? 'linear-gradient(135deg,#dc2626,#ef4444)'
            : 'linear-gradient(135deg,#16a34a,#22c55e)'};
        animation: slideInRight .25s ease;
    `;
    div.textContent = message;

    document.body.appendChild(div);
    setTimeout(() => { div.style.opacity = '0'; div.style.transition = 'opacity .3s'; }, 3500);
    setTimeout(() => div.remove(), 3800);
}
</script>

<style>
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>
@endpush
