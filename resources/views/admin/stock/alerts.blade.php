@extends('admin.layouts.admin')
@section('title', 'Stock Alerts')
@section('page-title', 'Stock Management')
@section('page-subtitle', 'Monitor and manage out-of-stock and low-stock medicines')

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    {{-- Out of Stock Summary --}}
    <div class="rounded-2xl border-2 border-red-200 bg-red-50 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-red-600 uppercase tracking-wide">Out of Stock</p>
                <p class="text-4xl font-black text-red-900 mt-2">{{ $outOfStockMedicines->total() }}</p>
                <p class="text-xs text-red-700 mt-1">Products unavailable for sale</p>
            </div>
            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-red-100">
                <i class="fa-solid fa-circle-exclamation text-3xl" style="color: rgb(194, 0, 0);"></i>
            </div>
        </div>
    </div>

    {{-- Low Stock Summary --}}
    <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-amber-600 uppercase tracking-wide">Low Stock</p>
                <p class="text-4xl font-black text-amber-900 mt-2">{{ $lowStockMedicines->total() }}</p>
                <p class="text-xs text-amber-700 mt-1">Products with 5 or fewer units</p>
            </div>
            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-amber-100 text-3xl">
               <i class="fa-solid fa-arrow-trend-down" style="color: rgb(255, 0, 0);"></i>
            </div>
        </div>
    </div>
</div>

{{-- Out of Stock Medicines Table --}}
@if($outOfStockMedicines->isNotEmpty())
<div class="rounded-2xl border border-red-200 bg-white shadow-sm overflow-hidden mb-6">
    <div class="flex items-center justify-between px-5 py-4 border-b border-red-100 bg-red-50">
        <div>
            <h2 class="text-sm font-bold text-red-900">Out of Stock Medicines</h2>
            <p class="text-xs text-red-700 mt-0.5">These products are currently unavailable for purchase</p>
        </div>
        <span class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-black text-white">
            {{ $outOfStockMedicines->total() }} Total
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] admin-table">
            <thead>
                <tr>
                    <th class="text-left">Medicine</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">Manufacturer</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outOfStockMedicines as $m)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($m->image)
                                    <img src="{{ $m->imageUrl() }}" 
                                         alt="{{ $m->name }}" 
                                         class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-bold">
                                        {{ strtoupper(substr($m->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $m->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $m->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                {{ $m->category->name }}
                            </span>
                        </td>
                        <td class="text-slate-600 text-sm">{{ $m->manufacturer }}</td>
                        <td class="text-center">
                            <span class="inline-flex items-center justify-center rounded-lg bg-red-100 px-3 py-1.5 text-sm font-black text-red-800 border-2 border-red-200">
                                0
                            </span>
                        </td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($m->priceRupees(), 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.medicines.edit', $m) }}" 
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition-colors">
                                <i class="fa-solid fa-edit"></i>
                                Update Stock
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($outOfStockMedicines->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $outOfStockMedicines->links() }}
        </div>
    @endif
</div>
@else
    <div class="rounded-2xl border border-green-200 bg-green-50 p-8 text-center mb-6">
        <div class="flex justify-center mb-3">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-3xl">
                ✅
            </div>
        </div>
        <p class="text-sm font-bold text-green-900">All Products in Stock!</p>
        <p class="text-xs text-green-700 mt-1">No out-of-stock medicines found.</p>
    </div>
@endif

{{-- Low Stock Medicines Table --}}
@if($lowStockMedicines->isNotEmpty())
<div class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-amber-100 bg-amber-50">
        <div>
            <h2 class="text-sm font-bold text-amber-900">Low Stock Medicines</h2>
            <p class="text-xs text-amber-700 mt-0.5">These products have 5 or fewer units remaining</p>
        </div>
        <span class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-black text-white">
            {{ $lowStockMedicines->total() }} Total
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] admin-table">
            <thead>
                <tr>
                    <th class="text-left">Medicine</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">Manufacturer</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockMedicines as $m)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($m->image)
                                    <img src="{{ $m->imageUrl() }}" 
                                         alt="{{ $m->name }}" 
                                         class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-bold">
                                        {{ strtoupper(substr($m->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $m->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $m->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                {{ $m->category->name }}
                            </span>
                        </td>
                        <td class="text-slate-600 text-sm">{{ $m->manufacturer }}</td>
                        <td class="text-center">
                            <span class="inline-flex items-center justify-center rounded-lg 
                                {{ $m->stock <= 2 ? 'bg-red-100 border-red-200 text-red-800' : 'bg-amber-100 border-amber-200 text-amber-800' }} 
                                px-3 py-1.5 text-sm font-black border-2">
                                {{ $m->stock }}
                            </span>
                        </td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($m->priceRupees(), 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.medicines.edit', $m) }}" 
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 transition-colors">
                                <i class="fa-solid fa-edit"></i>
                                Update Stock
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($lowStockMedicines->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $lowStockMedicines->links() }}
        </div>
    @endif
</div>
@else
    <div class="rounded-2xl border border-green-200 bg-green-50 p-8 text-center">
        <div class="flex justify-center mb-3">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-3xl">
                ✅
            </div>
        </div>
        <p class="text-sm font-bold text-green-900">Healthy Stock Levels!</p>
        <p class="text-xs text-green-700 mt-1">All products have sufficient stock (more than 5 units).</p>
    </div>
@endif

@endsection
