@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your MediCart store')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6 mb-8">
    @foreach([
        ['label'=>'Medicines',   'value'=>$stats['medicines'],              'icon'=>'💊', 'color'=>'bg-blue-50 text-blue-800'],
        ['label'=>'Categories',  'value'=>$stats['categories'],             'icon'=>'🗂️', 'color'=>'bg-blue-50 text-blue-700'],
        ['label'=>'Total Orders','value'=>$stats['orders'],                 'icon'=>'📦', 'color'=>'bg-purple-50 text-purple-700'],
        ['label'=>'Customers',   'value'=>$stats['users'],                  'icon'=>'👥', 'color'=>'bg-amber-50 text-amber-700'],
        ['label'=>'Revenue (₹)', 'value'=>number_format($stats['revenue'],2),'icon'=>'💰','color'=>'bg-blue-50 text-blue-800'],
        ['label'=>'Pending',     'value'=>$stats['pending'],                'icon'=>'⏳', 'color'=>'bg-red-50 text-red-700'],
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

{{-- Quick actions --}}
<div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('admin.medicines.create') }}"
       class="flex items-center gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white text-lg group-hover:scale-110 transition-transform">+</div>
        <div>
            <p class="text-sm font-bold text-blue-950">Add Medicine</p>
            <p class="text-xs text-blue-700">Create a new product</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.import.form') }}"
       class="flex items-center gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white text-lg group-hover:scale-110 transition-transform">↑</div>
        <div>
            <p class="text-sm font-bold text-blue-900">Import CSV</p>
            <p class="text-xs text-blue-600">Bulk upload medicines</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.export') }}"
       class="flex items-center gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white text-lg group-hover:scale-110 transition-transform">↓</div>
        <div>
            <p class="text-sm font-bold text-blue-900">Export CSV</p>
            <p class="text-xs text-blue-700">Download all medicines</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.index') }}"
       class="flex items-center gap-3 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50 p-4 hover:border-purple-400 hover:bg-purple-100 transition-all group">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-600 text-white text-lg group-hover:scale-110 transition-transform">☰</div>
        <div>
            <p class="text-sm font-bold text-purple-900">All Medicines</p>
            <p class="text-xs text-purple-600">Browse & manage</p>
        </div>
    </a>
</div>

{{-- WhatsApp Settings --}}
@php
    $waEnabled = config('services.whatsapp.enabled', true);
    $waPhone   = config('services.whatsapp.number', '917600264090');
@endphp
<div class="mb-8 rounded-2xl border border-green-200 bg-green-50 p-5">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white text-lg" style="background-color:green;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-green-900">WhatsApp Integration</p>
                <p class="text-xs text-green-700">
                    Status: <strong>{{ $waEnabled ? 'Enabled ✓' : 'Disabled ✗' }}</strong> ·
                    Number: <strong>+{{ $waPhone }}</strong>
                </p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 text-xs text-green-800 w-full sm:w-auto">
            <span class="rounded-full bg-green-100 px-3 py-1 font-semibold text-[11px] leading-relaxed">
                Update <code class="font-mono bg-green-200 px-1 rounded">WHATSAPP_NUMBER</code> &amp; <code class="font-mono bg-green-200 px-1 rounded">WHATSAPP_ENABLED</code> in <code class="font-mono bg-green-200 px-1 rounded">.env</code>
            </span>
            <a href="https://wa.me/{{ $waPhone }}" target="_blank"
               class="rounded-xl px-4 py-2 font-bold text-white hover:bg-green-600 transition-colors flex-shrink-0" style="background-color:green;">
                Test Chat
            </a>
        </div>
    </div>
</div>
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-900">Recent Orders</h2>
        <span class="text-xs text-slate-500">Last 8 orders</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] admin-table">
            <thead>
                <tr>
                    <th class="text-left">Order #</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Amount</th>
                    <th class="text-left">Payment</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    @php
                        $statusColors = [
                            'placed'         => 'bg-blue-100 text-blue-800',
                            'confirmed'      => 'bg-blue-100 text-blue-900',
                            'shipped'        => 'bg-purple-100 text-purple-800',
                            'delivered'      => 'bg-blue-100 text-blue-900',
                            'cancelled'      => 'bg-red-100 text-red-800',
                            'payment_failed' => 'bg-red-100 text-red-800',
                        ];
                        $sc = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <tr>
                        <td class="font-mono font-semibold text-slate-800 text-xs">{{ $order->order_number }}</td>
                        <td>
                            <p class="font-semibold text-slate-800">{{ $order->customer_name }}</p>
                            <p class="text-xs text-slate-500">{{ $order->customer_phone }}</p>
                        </td>
                        <td class="font-bold text-slate-900">₹{{ number_format($order->totalRupees(), 2) }}</td>
                        <td>
                            <span class="badge {{ $order->payment_method === 'online' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $order->payment_method === 'online' ? 'Online' : 'COD' }}
                            </span>
                        </td>
                        <td><span class="badge {{ $sc }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="text-slate-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-slate-400 py-8">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
