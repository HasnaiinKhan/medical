@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your MediCart store')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 lg:grid-cols-6 mb-6">
@foreach([

[
'label'=>'Medicines',
'value'=>$stats['medicines'],
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M128 176C128 149.5 149.5 128 176 128C202.5 128 224 149.5 224 176L224 288L128 288L128 176zM64 176L64 464C64 525.9 114.1 576 176 576C237.9 576 288 525.9 288 464L288 358.2L404.3 527.7C439.8 579.4 509.6 592 560.3 555.8C611 519.6 623.3 448.3 587.8 396.6L459.3 209.3C423.8 157.6 354 145 303.3 181.2C297.7 185.2 292.6 189.6 288 194.3L288 176C288 114.1 237.9 64 176 64C114.1 64 64 114.1 64 176zM328.6 304.2C312.6 280.9 318.6 248.9 340.5 233.2C361.7 218.1 391 222.9 406.5 245.4L473.5 343L393.6 398.9L328.6 304.1z"/></svg>',
'color'=>'bg-blue-50 text-blue-800'
],

[
'label'=>'Categories',
'value'=>$stats['categories'],
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M296.5 69.2C311.4 62.3 328.6 62.3 343.5 69.2L562.1 170.2C570.6 174.1 576 182.6 576 192C576 201.4 570.6 209.9 562.1 213.8L343.5 314.8C328.6 321.7 311.4 321.7 296.5 314.8L77.9 213.8C69.4 209.8 64 201.3 64 192C64 182.7 69.4 174.1 77.9 170.2L296.5 69.2zM112.1 282.4L276.4 358.3C304.1 371.1 336 371.1 363.7 358.3L528 282.4L562.1 298.2C570.6 302.1 576 310.6 576 320C576 329.4 570.6 337.9 562.1 341.8L343.5 442.8C328.6 449.7 311.4 449.7 296.5 442.8L77.9 341.8C69.4 337.8 64 329.3 64 320C64 310.7 69.4 302.1 77.9 298.2L112 282.4zM77.9 426.2L112 410.4L276.3 486.3C304 499.1 335.9 499.1 363.6 486.3L527.9 410.4L562 426.2C570.5 430.1 575.9 438.6 575.9 448C575.9 457.4 570.5 465.9 562 469.8L343.4 570.8C328.5 577.7 311.3 577.7 296.4 570.8L77.9 469.8C69.4 465.8 64 457.3 64 448C64 438.7 69.4 430.1 77.9 426.2z"/></svg>',
'color'=>'bg-blue-50 text-blue-700'
],

[
'label'=>'Total Orders',
'value'=>$stats['orders'],
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M0 72C0 58.7 10.7 48 24 48L69.3 48C96.4 48 119.6 67.4 124.4 94L124.8 96L312 96L312 198.1L281 167.1C271.6 157.7 256.4 157.7 247.1 167.1C237.8 176.5 237.7 191.7 247.1 201L319.1 273C328.5 282.4 343.7 282.4 353 273L425 201C434.4 191.6 434.4 176.4 425 167.1C415.6 157.8 400.4 157.7 391.1 167.1L360.1 198.1L360.1 96L537.5 96C557.5 96 572.6 114.2 568.9 133.9L537.8 299.8C532.1 330.1 505.7 352 474.9 352L171.3 352L176.4 380.3C178.5 391.7 188.4 400 200 400L456 400C469.3 400 480 410.7 480 424C480 437.3 469.3 448 456 448L200.1 448C165.3 448 135.5 423.1 129.3 388.9L77.2 102.6C76.5 98.8 73.2 96 69.3 96L24 96C10.7 96 0 85.3 0 72zM160 528C160 501.5 181.5 480 208 480C234.5 480 256 501.5 256 528C256 554.5 234.5 576 208 576C181.5 576 160 554.5 160 528zM384 528C384 501.5 405.5 480 432 480C458.5 480 480 501.5 480 528C480 554.5 458.5 576 432 576C405.5 576 384 554.5 384 528z"/></svg>',
'color'=>'bg-purple-50 text-purple-700'
],

[
'label'=>'Customers',
'value'=>$stats['users'],
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M96 192C96 130.1 146.1 80 208 80C269.9 80 320 130.1 320 192C320 253.9 269.9 304 208 304C146.1 304 96 253.9 96 192zM32 528C32 430.8 110.8 352 208 352C305.2 352 384 430.8 384 528L384 534C384 557.2 365.2 576 342 576L74 576C50.8 576 32 557.2 32 534L32 528zM464 128C517 128 560 171 560 224C560 277 517 320 464 320C411 320 368 277 368 224C368 171 411 128 464 128zM464 368C543.5 368 608 432.5 608 512L608 534.4C608 557.4 589.4 576 566.4 576L421.6 576C428.2 563.5 432 549.2 432 534L432 528C432 476.5 414.6 429.1 385.5 391.3C408.1 376.6 435.1 368 464 368z"/></svg>',
'color'=>'bg-amber-50 text-amber-700'
],

[
'label'=>'Revenue (₹)',
'value'=>number_format($stats['revenue'],2),
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M320 48C306.7 48 296 58.7 296 72L296 84L294.2 84C257.6 84 228 113.7 228 150.2C228 183.6 252.9 211.8 286 215.9L347 223.5C352.1 224.1 356 228.5 356 233.7C356 239.4 351.4 243.9 345.8 243.9L272 244C256.5 244 244 256.5 244 272C244 287.5 256.5 300 272 300L296 300L296 312C296 325.3 306.7 336 320 336C333.3 336 344 325.3 344 312L344 300L345.8 300C382.4 300 412 270.3 412 233.8C412 200.4 387.1 172.2 354 168.1L293 160.5C287.9 159.9 284 155.5 284 150.3C284 144.6 288.6 140.1 294.2 140.1L360 140C375.5 140 388 127.5 388 112C388 96.5 375.5 84 360 84L344 84L344 72C344 58.7 333.3 48 320 48zM141.3 405.5L98.7 448L64 448C46.3 448 32 462.3 32 480L32 544C32 561.7 46.3 576 64 576L384.5 576C413.5 576 441.8 566.7 465.2 549.5L591.8 456.2C609.6 443.1 613.4 418.1 600.3 400.3C587.2 382.5 562.2 378.7 544.4 391.8L424.6 480L312 480C298.7 480 288 469.3 288 456C288 442.7 298.7 432 312 432L384 432C401.7 432 416 417.7 416 400C416 382.3 401.7 368 384 368L231.8 368C197.9 368 165.3 381.5 141.3 405.5z"/></svg>',
'color'=>'bg-green-50 text-green-700'
],

[
'label'=>'Pending',
'value'=>$stats['pending'],
'icon'=>'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 48, 80)" d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM224 139L224 128L416 128L416 139C416 158 410.4 176.4 400 192L240 192C229.7 176.4 224 158 224 139zM240 448C243.5 442.7 247.6 437.7 252.1 433.1L320 365.2L387.9 433.1C392.5 437.7 396.5 442.7 400.1 448L240 448z"/></svg>',
'color'=>'bg-red-50 text-red-700'
]

] as $s)

<div class="stat-card flex flex-col gap-1">
    <div class="flex items-center justify-between">
        <span class="text-[10px] sm:text-xs font-semibold text-slate-500">
            {{ $s['label'] }}
        </span>

        <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $s['color'] }}">
            {!! $s['icon'] !!}
        </span>
    </div>

    <p class="text-xl sm:text-2xl font-extrabold text-slate-900">
        {{ $s['value'] }}
    </p>
</div>

@endforeach
</div>

{{-- ── Stock Alerts ── --}}
@if($outOfStockMedicines->isNotEmpty() || $lowStockMedicines->isNotEmpty())
<div class="mb-6 space-y-3">

    {{-- Out of stock --}}
    @if($outOfStockMedicines->isNotEmpty())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-xl"><i class="fa-solid fa-circle-exclamation" style="color: rgb(255, 0, 0);"></i></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-red-900 mb-1">
                    {{ $outOfStockMedicines->count() }} Medicine{{ $outOfStockMedicines->count() > 1 ? 's' : '' }} Out of Stock
                </p>
                <p class="text-xs text-red-700 mb-3">These products have 0 units remaining. Customers cannot purchase them. Please restock immediately.</p>
                
                @if($outOfStockMedicines->count() <= 5)
                    {{-- Show all if 5 or fewer --}}
                    <div class="flex flex-wrap gap-2">
                        @foreach($outOfStockMedicines as $m)
                            <a href="{{ route('admin.medicines.edit', $m) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-800 hover:bg-red-200 transition-colors">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                {{ $m->name }}
                                <span class="text-[10px] font-bold text-red-600 bg-red-200 px-1 rounded">0</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Show first 5 + View All button --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($outOfStockMedicines->take(5) as $m)
                            <a href="{{ route('admin.medicines.edit', $m) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-800 hover:bg-red-200 transition-colors">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                {{ Str::limit($m->name, 25) }}
                                <span class="text-[10px] font-bold text-red-600 bg-red-200 px-1 rounded">0</span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.stock.alerts') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition-colors shadow-sm">
                        View All {{ $outOfStockMedicines->count() }} Out of Stock Items
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Low stock --}}
    @if($lowStockMedicines->isNotEmpty())
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl">⚠️</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-amber-900 mb-1">
                    {{ $lowStockMedicines->count() }} Medicine{{ $lowStockMedicines->count() > 1 ? 's' : '' }} Running Low
                </p>
                <p class="text-xs text-amber-700 mb-3">These products have 5 or fewer units remaining. Consider restocking soon.</p>
                
                @if($lowStockMedicines->count() <= 5)
                    {{-- Show all if 5 or fewer --}}
                    <div class="flex flex-wrap gap-2">
                        @foreach($lowStockMedicines as $m)
                            <a href="{{ route('admin.medicines.edit', $m) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-200 transition-colors">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                {{ $m->name }}
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-200 px-1 rounded">{{ $m->stock }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Show first 5 + View All button --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($lowStockMedicines->take(5) as $m)
                            <a href="{{ route('admin.medicines.edit', $m) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-200 transition-colors">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                {{ Str::limit($m->name, 25) }}
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-200 px-1 rounded">{{ $m->stock }}</span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.stock.alerts') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-amber-700 transition-colors shadow-sm">
                        View All {{ $lowStockMedicines->count() }} Low Stock Items
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    
                @endif
            </div>
        </div>
    </div>
    @endif

</div>
@endif

{{-- Quick actions --}}
<div class="mb-6 grid gap-3 grid-cols-2 lg:grid-cols-4">
    <a href="{{ route('admin.medicines.create') }}"
       class="flex items-center gap-2 sm:gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-3 sm:p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-700 text-white text-lg group-hover:scale-110 transition-transform">+</div>
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-bold text-blue-950 truncate">Add Medicine</p>
            <p class="text-[10px] sm:text-xs text-blue-700 hidden sm:block">Create a new product</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.import.form') }}"
       class="flex items-center gap-2 sm:gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-3 sm:p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white text-lg group-hover:scale-110 transition-transform">↑</div>
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-bold text-blue-900 truncate">Import CSV</p>
            <p class="text-[10px] sm:text-xs text-blue-600 hidden sm:block">Bulk upload medicines</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.export') }}"
       class="flex items-center gap-2 sm:gap-3 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-3 sm:p-4 hover:border-blue-400 hover:bg-blue-100 transition-all group">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-700 text-white text-lg group-hover:scale-110 transition-transform">↓</div>
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-bold text-blue-900 truncate">Export CSV</p>
            <p class="text-[10px] sm:text-xs text-blue-700 hidden sm:block">Download all medicines</p>
        </div>
    </a>
    <a href="{{ route('admin.medicines.index') }}"
       class="flex items-center gap-2 sm:gap-3 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50 p-3 sm:p-4 hover:border-purple-400 hover:bg-purple-100 transition-all group">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 flex-shrink-0 items-center justify-center rounded-xl bg-purple-600 text-white text-lg group-hover:scale-110 transition-transform">☰</div>
        <div class="min-w-0">
            <p class="text-xs sm:text-sm font-bold text-purple-900 truncate">All Medicines</p>
            <p class="text-[10px] sm:text-xs text-purple-600 hidden sm:block">Browse & manage</p>
        </div>
    </a>
</div>

{{-- WhatsApp Settings --}}
@php
    $waEnabled = config('services.whatsapp.enabled', true);
    $waPhone   = config('services.whatsapp.number', '917600264090');
@endphp

{{-- ── Charts ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue + Orders charts with filter toggle (spans 2 cols) --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Filter toggle --}}
        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white shadow-sm px-4 py-3">
            <span class="text-xs font-bold text-slate-500 mr-1">View by:</span>
            @foreach([
                ['today', 'Today',      'Hourly'],
                ['week',  'This Week',  'Daily'],
                ['month', 'This Month', 'Daily'],
                ['year',  'This Year',  'Monthly'],
            ] as [$key, $label, $sub])
                <button type="button"
                        onclick="setChartFilter('{{ $key }}')"
                        id="filter-btn-{{ $key }}"
                        class="chart-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition-all
                               {{ $key === 'month' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    {{ $label }}
                    <span class="text-[10px] font-normal opacity-70 ml-0.5">{{ $sub }}</span>
                </button>
            @endforeach
        </div>

        {{-- Revenue bar chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Revenue Overview</h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="revenue-subtitle">This month · daily · paid orders only</p>
                </div>
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">₹ Revenue</span>
            </div>
            <div class="p-4 sm:p-5">
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </div>

        {{-- Orders line chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Orders Trend</h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="orders-subtitle">This month · daily · paid orders only</p>
                </div>
                <span class="text-xs font-semibold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg"># Orders</span>
            </div>
            <div class="p-4 sm:p-5">
                <canvas id="ordersChart" height="80"></canvas>
            </div>
        </div>
    </div>

    {{-- Order status doughnut --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Order Status</h2>
                <p class="text-xs text-slate-500 mt-0.5" id="status-subtitle">This month breakdown</p>
            </div>
        </div>
        <div class="p-4 sm:p-5 flex flex-col items-center">
            <canvas id="statusChart" height="180" style="max-width:220px"></canvas>
            <div class="mt-4 w-full grid grid-cols-2 gap-x-4 gap-y-1.5" id="status-legend">
                {{-- This will be populated dynamically --}}
            </div>
        </div>
    </div>

</div>

{{-- Recent Orders --}}
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
                        $statusCfg = [
                            'placed'                 => ['bg-amber-100 text-amber-800',  asset('images/hourglass.gif')],
                            'confirmed'              => ['bg-blue-100 text-blue-800',    asset('images/check.png')],
                            'shipped'                => ['bg-purple-100 text-purple-800',asset('images/package.png')],
                            'delivered'              => ['bg-green-100 text-green-800',  asset('images/confetti.png')],
                            'cancelled'              => ['bg-red-100 text-red-800',      asset('images/letter-x.png')],
                            'payment_failed'         => ['bg-red-100 text-red-800',      asset('images/sad.png')],
                            'payment_review'         => ['bg-amber-100 text-amber-800',  asset('images/credit-card.png')],
                            'refunded'               => ['bg-orange-100 text-orange-800',asset('images/refund.png')],
                            'refund_initiated'       => ['bg-yellow-100 text-yellow-800',asset('images/dollars.png')],
                            'cancellation_requested' => ['bg-amber-100 text-amber-800',  asset('images/hourglass.gif')],
                        ];
                        [$sc, $sImg] = $statusCfg[$order->status] ?? ['bg-slate-100 text-slate-700', asset('images/box.png')];
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
                        <td>
                            <span class="badge {{ $sc }} inline-flex items-center gap-1.5">
                                <img src="{{ $sImg }}" alt="{{ $order->status }}" class="h-4 w-4 object-contain flex-shrink-0">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="text-slate-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-slate-400 py-8">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    // ── All data sets from backend ─────────────────────────────────────
    const allDataSets = {
        today: @json($todayData),
        week:  @json($weekData),
        month: @json($monthData),
        year:  @json($yearData),
    };

    // Status breakdown for each time period
    const statusBreakdownData = {
        today: @json($statusBreakdownToday),
        week:  @json($statusBreakdownWeek),
        month: @json($statusBreakdownMonth),
        year:  @json($statusBreakdownYear),
    };

    // Chart metadata for each filter
    const chartMeta = {
        today: { title: 'Today', subtitle: 'hourly · paid orders only', periodType: 'hour' },
        week:  { title: 'This Week', subtitle: 'daily · paid orders only', periodType: 'day' },
        month: { title: 'This Month', subtitle: 'daily · paid orders only', periodType: 'day' },
        year:  { title: 'This Year', subtitle: 'monthly · paid orders only', periodType: 'month' },
    };

    let currentFilter = 'month'; // default
    let revenueChartInstance = null;
    let ordersChartInstance = null;
    let statusChartInstance = null;

    const fontFamily = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.family = fontFamily;
    Chart.defaults.color = '#64748b';

    // ── Build & render charts ──────────────────────────────────────────
    function renderCharts(filterKey) {
        const data = allDataSets[filterKey];
        const meta = chartMeta[filterKey];

        const labels  = data.map(r => r.label);
        const revenue = data.map(r => r.revenue);
        const orders  = data.map(r => r.orders);

        // Highlight last bar (today/current period)
        const lastIdx = labels.length - 1;

        // Revenue bar colors
        const revBg = labels.map((_, i) =>
            i === lastIdx ? '#2563eb' : 'rgba(37,99,235,0.35)'
        );
        const revBorder = labels.map((_, i) =>
            i === lastIdx ? '#1d4ed8' : '#3b82f6'
        );

        // Orders line colors
        const ordPointBg = labels.map((_, i) =>
            i === lastIdx ? '#7c3aed' : '#a78bfa'
        );
        const ordPointRadius = labels.map((_, i) =>
            i === lastIdx ? 6 : 4
        );

        // Update subtitles
        document.getElementById('revenue-subtitle').textContent = meta.subtitle;
        document.getElementById('orders-subtitle').textContent = meta.subtitle;

        // ── Revenue bar chart ──────────────────────────────────────────
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueChartInstance) revenueChartInstance.destroy();
        revenueChartInstance = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: revenue,
                    backgroundColor: revBg,
                    borderColor: revBorder,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: ctx => labels[ctx[0].dataIndex] + ' (' + meta.periodType + ')',
                            label: ctx => ' ₹' + Number(ctx.parsed.y).toLocaleString('en-IN', { minimumFractionDigits: 2 })
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            font: { size: 10 },
                            color: '#64748b',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: v => '₹' + (v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v)
                        }
                    }
                }
            }
        });

        // ── Orders line chart ──────────────────────────────────────────
        const ordersCtx = document.getElementById('ordersChart');
        if (ordersChartInstance) ordersChartInstance.destroy();
        ordersChartInstance = new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data: orders,
                    backgroundColor: 'rgba(124,58,237,0.08)',
                    borderColor: '#7c3aed',
                    borderWidth: 2.5,
                    pointBackgroundColor: ordPointBg,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5,
                    pointRadius: ordPointRadius,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: ctx => labels[ctx[0].dataIndex] + ' (' + meta.periodType + ')',
                            label: ctx => ' ' + ctx.parsed.y + ' order' + (ctx.parsed.y !== 1 ? 's' : '')
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            font: { size: 10 },
                            color: '#64748b',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // ── Status doughnut chart ──────────────────────────────────────
        renderStatusChart(filterKey);
    }

    // ── Render status doughnut chart ───────────────────────────────────
    function renderStatusChart(filterKey) {
        const statusData = statusBreakdownData[filterKey];
        const statusLabels = Object.keys(statusData);
        const statusCounts = Object.values(statusData);

        // Update subtitle
        const subtitles = {
            today: 'Today breakdown',
            week: 'This week breakdown',
            month: 'This month breakdown',
            year: 'This year breakdown'
        };
        document.getElementById('status-subtitle').textContent = subtitles[filterKey];

        const statusColorMap = {
            placed:                  '#3b82f6',
            confirmed:               '#6366f1',
            shipped:                 '#8b5cf6',
            delivered:               '#10b981',
            cancelled:               '#ef4444',
            payment_failed:          '#f97316',
            payment_review:          '#f59e0b',
            cancellation_requested:  '#f59e0b',
            refund_initiated:        '#06b6d4',
            refunded:                '#14b8a6',
        };

        const statusLabelMap = {
            placed:                  'Placed',
            confirmed:               'Confirmed',
            shipped:                 'Shipped',
            delivered:               'Delivered',
            cancelled:               'Cancelled',
            payment_failed:          'Pay Failed',
            payment_review:          'Pay Review',
            cancellation_requested:  'Cancel Req',
            refund_initiated:        'Refund Init',
            refunded:                'Refunded',
        };

        const pieColors = statusLabels.map(s => statusColorMap[s] || '#94a3b8');

        const statusCtx = document.getElementById('statusChart');
        if (statusChartInstance) statusChartInstance.destroy();
        statusChartInstance = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.map(s => statusLabelMap[s] || s.replace(/_/g, ' ')),
                datasets: [{
                    data: statusCounts,
                    backgroundColor: pieColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ': ' + ctx.parsed
                        }
                    }
                }
            }
        });

        // Update legend below chart
        const legendHtml = statusLabels.map(status => {
            const count = statusData[status];
            const label = statusLabelMap[status] || status.replace(/_/g, ' ');
            const color = statusColorMap[status] || '#94a3b8';
            return `
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full flex-shrink-0" style="background:${color}"></span>
                    <span class="text-[11px] text-slate-600 truncate">${label} <strong>${count}</strong></span>
                </div>
            `;
        }).join('');
        document.getElementById('status-legend').innerHTML = legendHtml;
    }

    // ── Filter switcher ────────────────────────────────────────────────
    window.setChartFilter = function(filterKey) {
        if (currentFilter === filterKey) return;
        currentFilter = filterKey;

        // Update button styles
        document.querySelectorAll('.chart-filter-btn').forEach(btn => {
            const isActive = btn.id === 'filter-btn-' + filterKey;
            btn.className = 'chart-filter-btn rounded-xl px-3.5 py-1.5 text-xs font-bold transition-all ' +
                (isActive
                    ? 'bg-blue-600 text-white shadow-sm'
                    : 'text-slate-500 hover:bg-slate-100');
        });

        // Re-render charts
        renderCharts(filterKey);
    };

    // ── Initial render (month by default) ─────────────────────────────
    renderCharts(currentFilter);
})();
</script>
@endpush

@endsection
