@extends('admin.layouts.admin')
@section('title', 'Export Medicines')
@section('page-title', 'Export Medicines')
@section('page-subtitle', 'Download medicines with custom filters')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">

        {{-- ── The form uses GET so filters persist in URL and download works correctly ── --}}
        <form id="export-form"
              method="get"
              action="{{ route('admin.medicines.export') }}"
              class="space-y-6"
              data-no-loader>

            {{-- Live search to filter the checkboxes on screen (not submitted) --}}
            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">
                    Filter list below
                    <span class="text-xs font-normal text-slate-400 ml-1">(narrows categories &amp; manufacturers shown)</span>
                </label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="list-filter"
                           placeholder="Type to filter categories &amp; manufacturers…"
                           autocomplete="off"
                           class="rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
                </div>
            </div>

            {{-- Export keyword search (goes into CSV filter) --}}
            <div>
                <label for="q" class="block text-sm font-semibold text-slate-900 mb-2">
                    Search exported medicines
                    <span class="text-xs font-normal text-slate-400 ml-1">(filters which rows are exported)</span>
                </label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="q" id="q"
                           value="{{ request('q') }}"
                           placeholder="e.g. Dolo, Himalaya…"
                           class="rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
                </div>
            </div>

            {{-- Categories --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-slate-900">Categories</label>
                    <button type="button" id="clear-cats"
                            class="text-xs text-slate-400 hover:text-red-500 transition-colors">Clear</button>
                </div>
                <div class="categories-container border border-slate-200 rounded-xl p-3 space-y-2"
                     style="max-height:220px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(15,23,42,.25) transparent;">
                    @forelse($categories as $category)
                        <label class="flex items-center gap-2 cursor-pointer category-item select-none">
                            <input type="checkbox" name="categories[]"
                                   value="{{ $category->slug }}"
                                   {{ in_array($category->slug, request('categories', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">{{ $category->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No categories found.</p>
                    @endforelse
                </div>
                <p class="text-xs text-slate-400 mt-1">Leave all unchecked to export every category.</p>
            </div>

            {{-- Manufacturers --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-slate-900">Manufacturers / Brands</label>
                    <button type="button" id="clear-mfrs"
                            class="text-xs text-slate-400 hover:text-red-500 transition-colors">Clear</button>
                </div>
                <div class="manufacturers-container border border-slate-200 rounded-xl p-3 space-y-2"
                     style="max-height:220px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(15,23,42,.25) transparent;">
                    @forelse($manufacturers as $manufacturer)
                        <label class="flex items-center gap-2 cursor-pointer manufacturer-item select-none">
                            <input type="checkbox" name="manufacturer[]"
                                   value="{{ $manufacturer }}"
                                   {{ in_array($manufacturer, request('manufacturer', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700">{{ $manufacturer }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No manufacturers found.</p>
                    @endforelse
                </div>
                <p class="text-xs text-slate-400 mt-1">Leave all unchecked to export every manufacturer.</p>
            </div>

            {{-- Stock Status --}}
            <div>
                <label for="stock_status" class="block text-sm font-semibold text-slate-900 mb-2">Stock Status</label>
                <select name="stock_status" id="stock_status"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
                    <option value="">All Stock Levels</option>
                    <option value="in_stock"     {{ request('stock_status') === 'in_stock'     ? 'selected' : '' }}>In Stock (> 5 units)</option>
                    <option value="low_stock"    {{ request('stock_status') === 'low_stock'    ? 'selected' : '' }}>Low Stock (1–5 units)</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            {{-- Prescription --}}
            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">Prescription Required</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="prescription" value=""
                               {{ request('prescription', '') === '' ? 'checked' : '' }}
                               class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">All medicines</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="prescription" value="1"
                               {{ request('prescription') === '1' ? 'checked' : '' }}
                               class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">Rx required only</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="prescription" value="0"
                               {{ request('prescription') === '0' ? 'checked' : '' }}
                               class="w-4 h-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">OTC only (no Rx required)</span>
                    </label>
                </div>
            </div>

            {{-- Price Range --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price_min" class="block text-sm font-semibold text-slate-900 mb-2">Min Price (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="price_min" id="price_min"
                               value="{{ request('price_min') }}"
                               placeholder="0.00" step="0.01" min="0"
                               class="rounded-xl border border-slate-200 bg-white pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
                    </div>
                </div>
                <div>
                    <label for="price_max" class="block text-sm font-semibold text-slate-900 mb-2">Max Price (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                        <input type="number" name="price_max" id="price_max"
                               value="{{ request('price_max') }}"
                               placeholder="10000.00" step="0.01" min="0"
                               class="rounded-xl border border-slate-200 bg-white pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
                    </div>
                </div>
            </div>

            {{-- Active Filters Summary (works because form is GET) --}}
            @php
                $activeFilters = [];
                if (request()->filled('q'))           $activeFilters[] = 'Search: "' . request('q') . '"';
                if (count(request('categories', []))) $activeFilters[] = count(request('categories')) . ' categor' . (count(request('categories')) > 1 ? 'ies' : 'y');
                if (count(request('manufacturer', []))) $activeFilters[] = count(request('manufacturer')) . ' manufacturer' . (count(request('manufacturer')) > 1 ? 's' : '');
                if (request()->filled('stock_status')) $activeFilters[] = ucfirst(str_replace('_', ' ', request('stock_status')));
                if (request()->filled('prescription'))  $activeFilters[] = request('prescription') === '1' ? 'Rx only' : 'OTC only';
                if (request()->filled('price_min'))    $activeFilters[] = 'Min ₹' . request('price_min');
                if (request()->filled('price_max'))    $activeFilters[] = 'Max ₹' . request('price_max');
            @endphp

            @if(count($activeFilters))
                <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                    <p class="text-xs font-bold text-blue-900 mb-2">Active export filters:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($activeFilters as $f)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-lg">{{ $f }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.medicines.index') }}"
                   class="btn-sm inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl flex-1 justify-center" style="align-item:center;">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Cancel
                </a>
                <a href="{{ route('admin.medicines.export.form') }}"
                   class="btn-sm inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl justify-center px-4">
                    Reset
                </a>
                <button type="submit" id="export-submit-btn"
                        class="btn-sm inline-flex items-center gap-2 bg-blue-600 text-white hover:bg-blue-700 rounded-xl flex-1 justify-center">
                    <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span id="export-btn-text">Export CSV</span>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 p-4">
        <p class="text-sm text-green-900">
            <strong>Tip:</strong> Select your filters then click Export CSV. The file downloads directly — no page reload.
            All filters are optional; leave them blank to export everything.
        </p>
    </div>
</div>

{{-- ── Screen freeze overlay (shown for 3s while download starts) ── --}}
<div id="export-freeze" style="
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
">
    <div style="
        background: #fff;
        border-radius: 20px;
        padding: 32px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        box-shadow: 0 24px 64px rgba(0,0,0,.22);
        min-width: 200px;
        text-align: center;
    ">
        <div style="
            width: 48px; height: 48px;
            border: 4px solid #dbeafe;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: adminSpin .75s linear infinite;
        "></div>
        <div>
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 4px;">Preparing CSV…</p>
            <p style="font-size:12px;color:#64748b;margin:0;">Your download will start automatically</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Kill the global loader the instant this script runs (before DOMContentLoaded)
(function () {
    var g = document.getElementById('admin-loader');
    if (g) g.style.display = 'none';
})();

document.addEventListener('DOMContentLoaded', function () {

    // Hide the global admin loader immediately — this page manages its own freeze
    var globalLoader = document.getElementById('admin-loader');
    if (globalLoader) globalLoader.style.display = 'none';
    const listFilter = document.getElementById('list-filter');
    if (listFilter) {
        listFilter.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.category-item, .manufacturer-item').forEach(item => {
                item.style.display = (!q || item.textContent.toLowerCase().includes(q)) ? '' : 'none';
            });
        });
    }

    // ── 2. Clear buttons ──────────────────────────────────────────────────────
    document.getElementById('clear-cats')?.addEventListener('click', function () {
        document.querySelectorAll('.category-item input[type="checkbox"]').forEach(cb => cb.checked = false);
    });
    document.getElementById('clear-mfrs')?.addEventListener('click', function () {
        document.querySelectorAll('.manufacturer-item input[type="checkbox"]').forEach(cb => cb.checked = false);
    });

    // ── 3. Export download ────────────────────────────────────────────────────
    const form    = document.getElementById('export-form');
    const btn     = document.getElementById('export-submit-btn');
    const btnText = document.getElementById('export-btn-text');
    const freeze  = document.getElementById('export-freeze');

    // Flag so the global beforeunload loader doesn't fire during a download
    let downloadInProgress = false;

    // Suppress global loader while download is happening
    window.addEventListener('beforeunload', function (e) {
        if (downloadInProgress) {
            // Cancel the beforeunload so the global loader can't intercept it
            // (downloads don't unload the page, but just in case)
            e.stopImmediatePropagation();
        }
    }, true); // capture phase — runs before the layout's listener

    if (form && btn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation(); // prevent global submit listener from showing loader

            // Build GET query string (handles duplicate keys e.g. categories[])
            const manual = new URLSearchParams();
            for (const [k, v] of new FormData(form).entries()) {
                manual.append(k, v);
            }
            const downloadUrl = form.action + '?' + manual.toString();

            // ── Button: loading state ─────────────────────────────────────
            btn.disabled = true;
            btnText.innerHTML =
                '<span style="width:14px;height:14px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:adminSpin .65s linear infinite;display:inline-block;vertical-align:middle;margin-right:6px;"></span>' +
                'Preparing…';

            // ── Show export freeze overlay ────────────────────────────────
            freeze.style.display = 'flex';
            downloadInProgress   = true;

            // Hide the global admin loader in case it somehow appeared
            const globalLoader = document.getElementById('admin-loader');
            if (globalLoader) globalLoader.style.display = 'none';

            // ── Trigger the actual download ───────────────────────────────
            const a = document.createElement('a');
            a.href          = downloadUrl;
            a.download      = '';
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            // ── After 3 s: unfreeze, restore button, show toast ───────────
            setTimeout(function () {
                downloadInProgress      = false;
                freeze.style.display    = 'none';
                btn.disabled            = false;
                btnText.innerHTML =
                    '<svg style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>' +
                    '</svg>Export CSV';
                showToast('✅ CSV downloaded successfully!');
            }, 3000);
        });
    }

    // ── 4. Toast ──────────────────────────────────────────────────────────────
    function showToast(msg) {
        const t = document.createElement('div');
        t.style.cssText =
            'position:fixed;bottom:24px;right:24px;' +
            'background:linear-gradient(135deg,#10b981,#059669);' +
            'color:#fff;padding:13px 20px;border-radius:12px;' +
            'box-shadow:0 8px 24px rgba(0,0,0,.18);' +
            'font-size:14px;font-weight:600;z-index:100000;' +
            'display:flex;align-items:center;gap:10px;' +
            'transform:translateX(130%);transition:transform .3s ease;';
        t.textContent = msg;
        document.body.appendChild(t);
        requestAnimationFrame(() => { t.style.transform = 'translateX(0)'; });
        setTimeout(() => {
            t.style.transform = 'translateX(130%)';
            setTimeout(() => t.remove(), 350);
        }, 4000);
    }
});
</script>

<style>
/* Thin scrollbars for category/manufacturer lists */
.categories-container::-webkit-scrollbar,
.manufacturers-container::-webkit-scrollbar   { width: 6px; }
.categories-container::-webkit-scrollbar-track,
.manufacturers-container::-webkit-scrollbar-track { background: transparent; }
.categories-container::-webkit-scrollbar-thumb,
.manufacturers-container::-webkit-scrollbar-thumb { background: rgba(15,23,42,.2); border-radius: 99px; }
.categories-container::-webkit-scrollbar-thumb:hover,
.manufacturers-container::-webkit-scrollbar-thumb:hover { background: rgba(15,23,42,.4); }
</style>
@endpush
