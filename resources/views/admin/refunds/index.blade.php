@extends('admin.layouts.admin')
@section('title', 'Refunds')
@section('page-title', 'Refund Management')
@section('page-subtitle', 'Review and process customer refund requests')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-6">
    @foreach([
        ['label'=>'Total',      'value'=>$stats['total'],      'color'=>'bg-slate-50 text-slate-700'],
        ['label'=>'Requested',  'value'=>$stats['requested'],  'color'=>'bg-amber-50 text-amber-800'],
        ['label'=>'Processing', 'value'=>$stats['processing'], 'color'=>'bg-blue-50 text-blue-800'],
        ['label'=>'Processed',  'value'=>$stats['processed'],  'color'=>'bg-green-50 text-green-800'],
        ['label'=>'Failed',     'value'=>$stats['failed'],     'color'=>'bg-red-50 text-red-800'],
    ] as $s)
    <div class="stat-card flex flex-col gap-1">
        <span class="text-xs font-semibold text-slate-500">{{ $s['label'] }}</span>
        <p class="text-2xl font-extrabold {{ $s['color'] }} rounded-lg px-2 py-0.5 w-fit">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.refunds.index') }}"
      class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Order #, refund #, customer…"
                   class="w-full rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none">
        </div>
        <select name="status" class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all">All Statuses</option>
            @foreach(['requested','approved','processing','processed','failed','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="type" class="rounded-xl border border-slate-200 py-2 px-3 text-sm focus:border-blue-500 focus:outline-none">
            <option value="all">All Types</option>
            <option value="gateway" {{ request('type')==='gateway'?'selected':'' }}>Gateway (Online)</option>
            <option value="cod_bank_transfer" {{ request('type')==='cod_bank_transfer'?'selected':'' }}>COD Bank Transfer</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-xl bg-blue-600 py-2 px-4 text-sm font-bold text-white hover:bg-blue-700 transition-colors">Filter</button>
            <a href="{{ route('admin.refunds.index') }}" class="flex-1 rounded-xl border border-slate-200 py-2 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50 text-center">Reset</a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] admin-table">
            <thead>
                <tr>
                    <th class="text-left">Refund #</th>
                    <th class="text-left">Order</th>
                    <th class="text-left">Customer</th>
                    <th class="text-right">Amount</th>
                    <th class="text-left">Type</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Requested</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($refunds as $refund)
                    @php [$badgeClass, $badgeLabel] = $refund->statusBadge(); @endphp
                    <tr>
                        <td class="font-mono text-xs font-bold text-slate-700">{{ $refund->refund_number }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $refund->order) }}"
                               class="font-mono text-xs font-bold text-blue-700 hover:underline">
                                {{ $refund->order->order_number }}
                            </a>
                        </td>
                        <td>
                            <p class="text-xs font-semibold text-slate-800">{{ $refund->order->customer_name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $refund->order->customer_phone }}</p>
                        </td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($refund->amountRupees(), 2) }}</td>
                        <td>
                            <span class="badge {{ $refund->type === 'gateway' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $refund->type === 'gateway' ? '💳 Gateway' : '🏦 Bank Transfer' }}
                            </span>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                        <td class="text-xs text-slate-500">{{ $refund->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.refunds.show', $refund) }}"
                               class="btn-sm bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center">
                            <p class="text-3xl mb-2">🎉</p>
                            <p class="text-sm font-semibold text-slate-600">No refund requests</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($refunds->hasPages())
    <div class="flex justify-center">{{ $refunds->links() }}</div>
@endif

@endsection
