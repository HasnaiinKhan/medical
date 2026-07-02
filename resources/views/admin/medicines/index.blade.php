@extends('admin.layouts.admin')
@section('title', 'Medicines')
@section('page-title', 'Medicines')
@section('page-subtitle', $medicines->total() . ' medicines' . ($status !== 'all' ? ' · ' . ucfirst($status) : ' in catalogue'))

@section('content')

<style>
/* ── Medicine active-toggle button ─────────────────────────────────── */
.medicine-active-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.med-toggle-on  { background: #f0fdf4; color: #15803d; }
.med-toggle-on:hover  { background: #dcfce7; }
.med-toggle-off { background: #f1f5f9; color: #64748b; }
.med-toggle-off:hover { background: #e2e8f0; }

.toggle-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.med-toggle-on  .toggle-dot { background: #22c55e; }
.med-toggle-off .toggle-dot { background: #94a3b8; }
</style>

@php
    $outOfStockList = \App\Models\Medicine::where('stock', '<=', 0)->orderBy('name')->get(['id','name','slug','stock']);
    $lowStockList   = \App\Models\Medicine::where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get(['id','name','slug','stock']);
@endphp

{{-- Out-of-stock alert --}}
@if($outOfStockList->isNotEmpty())
<div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 relative" id="medicines-out-of-stock-alert" style="display: none;">
    {{-- Close button --}}
    <button type="button" 
            onclick="dismissMedicineAlert('medicines-out-of-stock-alert')" 
            class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 hover:bg-red-200 transition-all text-red-700 hover:text-red-900 shadow-sm hover:shadow-md"
            aria-label="Dismiss alert">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    
    <div class="flex items-start gap-3 pr-12">
        <span class="text-xl flex-shrink-0"><i class="fa-solid fa-circle-exclamation" style="color: rgb(255, 0, 0);"></i></span>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-900 mb-2">{{ $outOfStockList->count() }} out-of-stock - customers cannot buy these. Restock immediately.</p>
            <div class="flex flex-wrap gap-2">
                @foreach($outOfStockList->take(10) as $m)
                    <a href="{{ route('admin.medicines.edit', $m) }}"
                       class="inline-flex items-center gap-1 rounded-lg bg-red-100 border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-800 hover:bg-red-200 transition-colors">
                        {{ Str::limit($m->name, 25) }} <span class="bg-red-200 px-1 rounded font-bold">0</span>
                    </a>
                @endforeach
                @if($outOfStockList->count() > 10)
                    <a href="{{ route('admin.stock.alerts') }}" 
                       class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1 text-xs font-bold text-white hover:bg-red-700 transition-colors">
                        +{{ $outOfStockList->count() - 10 }} more
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Low-stock warning --}}
@if($lowStockList->isNotEmpty())
<div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 relative" id="medicines-low-stock-alert" style="display: none;">
    {{-- Close button --}}
    <button type="button" 
            onclick="dismissMedicineAlert('medicines-low-stock-alert')" 
            class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 hover:bg-amber-200 transition-all text-amber-700 hover:text-amber-900 shadow-sm hover:shadow-md"
            aria-label="Dismiss alert">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    
    <div class="flex items-start gap-3 pr-12">
        <span class="text-xl flex-shrink-0"><i class="fa-solid fa-arrow-trend-down" style="color: rgb(255, 0, 0);"></i></span>
        <div class="flex-1">
            <p class="text-sm font-bold text-amber-900 mb-2">{{ $lowStockList->count() }} running low (≤5 units) - consider restocking soon.</p>
            <div class="flex flex-wrap gap-2">
                @foreach($lowStockList->take(10) as $m)
                    <a href="{{ route('admin.medicines.edit', $m) }}"
                       class="inline-flex items-center gap-1 rounded-lg bg-amber-100 border border-amber-200 px-2.5 py-1 text-xs font-semibold text-amber-800 hover:bg-amber-200 transition-colors">
                        {{ Str::limit($m->name, 25) }} <span class="bg-amber-200 px-1 rounded font-bold">{{ $m->stock }}</span>
                    </a>
                @endforeach
                @if($lowStockList->count() > 10)
                    <a href="{{ route('admin.stock.alerts') }}" 
                       class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1 text-xs font-bold text-white hover:bg-amber-700 transition-colors">
                        +{{ $lowStockList->count() - 10 }} more
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
// Check if this is a hard refresh or first visit to page
(function() {
    const pageKey = 'medicines-page-visited';
    const wasVisited = sessionStorage.getItem(pageKey);
    
    ['medicines-out-of-stock-alert', 'medicines-low-stock-alert'].forEach(alertId => {
        const alert = document.getElementById(alertId);
        if (!alert) return;
        
        // Check if permanently dismissed (24 hours)
        const dismissedTime = localStorage.getItem(alertId + '_dismissed');
        if (dismissedTime) {
            const hoursSinceDismiss = (Date.now() - dismissedTime) / (1000 * 60 * 60);
            if (hoursSinceDismiss < 24) {
                // Keep hidden - dismissed within 24 hours
                return;
            } else {
                // Clear old dismissal
                localStorage.removeItem(alertId + '_dismissed');
            }
        }
        
        // Show alert only on hard refresh (first visit in this session)
        if (!wasVisited) {
            alert.style.display = 'block';
        }
    });
    
    // Mark page as visited in this session
    sessionStorage.setItem(pageKey, 'true');
})();

function dismissMedicineAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.3s, transform 0.3s';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alert.style.display = 'none';
            // Save dismissal in localStorage (24 hours)
            localStorage.setItem(alertId + '_dismissed', Date.now());
        }, 300);
    }
}
</script>

{{-- Toolbar --}}
<div class="mb-5 flex flex-col gap-3">

    {{-- Status filter tabs --}}
    <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-1 shadow-sm w-fit">
        @php
            $tabs = [
                'all'      => ['label' => 'All',      'count' => $totalCount,    'icon' => '📋'],
                'active'   => ['label' => 'Live',     'count' => $activeCount,   'icon' => '🟢'],
                'inactive' => ['label' => 'Inactive', 'count' => $inactiveCount, 'icon' => '⚫'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <a href="{{ route('admin.medicines.index', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $q ?: null, 'category' => request('category') ?: null])) }}"
               class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all
                      {{ $status === $key ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
                <span>{{ $tab['icon'] }}</span>
                {{ $tab['label'] }}
                <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold
                             {{ $status === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <form action="{{ route('admin.medicines.index') }}" method="get" class="flex gap-2 flex-wrap items-center">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        @if($status !== 'all')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div class="relative flex-1 min-w-[160px]">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" value="{{ $q }}" placeholder="Search medicines…"
                   class="rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
        </div>
        <select name="category" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-blue-600 focus:outline-none flex-1 min-w-[140px]">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.medicines.import.form') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-600 text-white hover:bg-blue-700 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import
        </a>
        <a href="{{ route('admin.medicines.export.form') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-700 text-white hover:bg-blue-800 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </a>
        <a href="{{ route('admin.medicines.create') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-700 text-white hover:bg-blue-800 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Medicine
        </a>
    </div>
</div>

{{-- Table --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] admin-table">
            <thead>
                <tr>
                    <th class="text-left w-12">Sr. No.</th>
                    <th class="text-left">Medicine</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">MRP</th>
                    <th class="text-left">Price</th>
                    <th class="text-left">Stock</th>
                    <th class="text-left">Rx</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $m)
                    <tr id="medicine-row-{{ $m->id }}" style="{{ $m->is_active ? '' : 'opacity:.45; background:#f8fafc;' }}">
                        <td class="text-slate-400 text-xs">{{ $m->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 flex-shrink-0 rounded-lg overflow-hidden bg-blue-50">
                                    <img src="{{ $m->imageUrl() }}" alt="{{ $m->name }}"
                                         class="h-full w-full object-contain p-1"
                                         onerror="this.style.display='none'">
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 text-sm">{{ $m->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $m->manufacturer }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="width:180px;">
                            <span class="badge bg-blue-50 text-blue-800 ring-1 ring-blue-200">{{ $m->category->name }}</span>
                        </td>
                        <td class="text-slate-500 text-xs line-through">₹{{ number_format($m->mrpRupees(), 2) }}</td>
                        <td class="font-bold text-slate-900">₹{{ number_format($m->priceRupees(), 2) }}</td>
                        <td>
                            <span class="font-semibold {{ $m->stock <= 0 ? 'text-red-600' : ($m->stock <= 5 ? 'text-amber-600' : 'text-slate-700') }}">
                                {{ $m->stock <= 0 ? '⚠ Out of Stock' : $m->stock }}
                            </span>
                        </td>
                        <td>
                            @if($m->prescription_required)
                                <span class="badge bg-amber-100 text-amber-800">Rx</span>
                            @else
                                <span class="text-slate-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="display:inline-flex;align-items:center;gap:5px;">
                                {{-- Live/Hidden badge --}}
                                <button type="button"
                                        data-toggle-url="{{ route('admin.medicines.toggleActive', $m) }}"
                                        data-id="{{ $m->id }}"
                                        data-name="{{ addslashes($m->name) }}"
                                        data-active="{{ $m->is_active ? '1' : '0' }}"
                                        class="medicine-active-toggle {{ $m->is_active ? 'med-toggle-on' : 'med-toggle-off' }}">
                                    <span class="toggle-dot"></span>
                                    <span class="toggle-label">{{ $m->is_active ? 'Live' : 'Inactive' }}</span>
                                </button>
                                {{-- Edit --}}
                                <a href="{{ route('admin.medicines.edit', $m) }}" class="btn-sm" style="background:#f1f5f9;color:#475569;">Edit</a>
                                {{-- Delete --}}
                                <form method="post" action="{{ route('admin.medicines.destroy', $m) }}" id="delete-form-{{ $m->id }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="openDeleteModal({{ $m->id }}, '{{ addslashes($m->name) }}')"
                                            class="btn-sm" style="background:#fef2f2;color:#dc2626;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-400 py-12">
                            No medicines found.
                            <a href="{{ route('admin.medicines.create') }}" class="text-blue-700 font-semibold hover:underline ml-1">Add one →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($medicines->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $medicines->links() }}
        </div>
    @endif
</div>

{{-- ── STATUS TOGGLE CONFIRM MODAL ── --}}
<div id="status-toggle-modal"
     style="display:none; position:fixed; inset:0; z-index:9999;
            background:rgba(15,23,42,0.55); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,.25);
                padding:28px 24px; width:100%; max-width:400px;">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
            <div style="width:48px; height:48px; border-radius:50%; background:#eff6ff;
                        display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:22px;">
                <i class="fa-solid fa-arrows-rotate" style="color: rgba(0, 4, 255, 1);"></i>    
            </div>
            <div>
                <p style="margin:0; font-size:16px; font-weight:700; color:#0f172a;">Change Selling Status</p>
                <p style="margin:3px 0 0; font-size:12px; color:#64748b;">This will update product visibility immediately</p>
            </div>
        </div>
        <p style="font-size:13px; color:#475569; margin:0 0 8px;">
            Are you sure you want to
            <strong id="stm-action-label" style="color:#1e293b;">change status</strong>
            for:
        </p>
        <p id="stm-product-name"
           style="font-size:14px; font-weight:700; color:#2563eb; background:#eff6ff;
                  border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px;
                  margin:0 0 22px; word-break:break-word;"></p>
        <div style="display:flex; gap:10px;">
            <button type="button" id="stm-cancel-btn"
                    style="flex:1; border:1px solid #e2e8f0; background:#fff; border-radius:12px;
                           padding:11px; font-size:13px; font-weight:600; color:#475569; cursor:pointer;"
                    onmouseover="this.style.background='#f8fafc'"
                    onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button type="button" id="stm-confirm-btn"
                    style="flex:1; border:none; border-radius:12px; padding:11px; font-size:13px;
                           font-weight:700; color:#fff; background:#16a34a; cursor:pointer;"
                    onmouseover="this.style.filter='brightness(.9)'"
                    onmouseout="this.style.filter='none'">
                Yes, Enable
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var CSRF  = document.querySelector('meta[name="csrf-token"]').content;
    var modal = document.getElementById('status-toggle-modal');

    document.querySelectorAll('.medicine-active-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var isActive = this.dataset.active === '1';

            document.getElementById('stm-product-name').textContent = this.dataset.name || 'this product';
            document.getElementById('stm-action-label').textContent = isActive
                ? 'disable selling (hide from customers)'
                : 'enable selling (show to customers)';

            var confirmBtn = document.getElementById('stm-confirm-btn');
            confirmBtn.style.background = isActive ? '#dc2626' : '#16a34a';
            confirmBtn.textContent      = isActive ? 'Yes, Disable' : 'Yes, Enable';

            modal._pendingBtn   = this;
            modal.style.display = 'flex';
        });
    });

    document.getElementById('stm-confirm-btn').addEventListener('click', function () {
        var btn = modal._pendingBtn;
        modal.style.display = 'none';
        if (!btn) return;

        var url       = btn.dataset.toggleUrl;
        var isActive  = btn.dataset.active === '1';
        var nowActive = !isActive;
        var row       = document.getElementById('medicine-row-' + btn.dataset.id);
        var label     = btn.querySelector('.toggle-label');

        btn.dataset.active = nowActive ? '1' : '0';
        label.textContent  = nowActive ? 'Live' : 'Inactive';
        btn.classList.toggle('med-toggle-on',  nowActive);
        btn.classList.toggle('med-toggle-off', !nowActive);
        if (row) { row.style.opacity = nowActive ? '' : '0.45'; row.style.background = nowActive ? '' : '#f8fafc'; }

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: '_method=PATCH',
        })
        .then(function (r) { return r.json(); })
        .then(function (d) { if (!d.ok) revert(); })
        .catch(revert);

        function revert() {
            btn.dataset.active = isActive ? '1' : '0';
            label.textContent  = isActive ? 'Live' : 'Inactive';
            btn.classList.toggle('med-toggle-on',  isActive);
            btn.classList.toggle('med-toggle-off', !isActive);
            if (row) { row.style.opacity = isActive ? '' : '0.45'; row.style.background = isActive ? '' : '#f8fafc'; }
        }
    });

    document.getElementById('stm-cancel-btn').addEventListener('click', function () { modal.style.display = 'none'; });
    modal.addEventListener('click', function (e) { if (e.target === this) this.style.display = 'none'; });
})();
</script>
@endpush
<div id="delete-medicine-modal"
     style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,.25); padding:28px 24px; width:100%; max-width:400px;">
        {{-- Icon + title --}}
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
            <div style="width:48px; height:48px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg style="width:22px;height:22px;color:#dc2626;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <p style="margin:0; font-size:16px; font-weight:700; color:#0f172a;">Delete Medicine</p>
                <p style="margin:3px 0 0; font-size:12px; color:#64748b;">This action cannot be undone</p>
            </div>
        </div>

        {{-- Body --}}
        <p style="font-size:13px; color:#475569; line-height:1.6; margin:0 0 6px;">
            Are you sure you want to permanently delete:
        </p>
        <p id="delete-medicine-name"
           style="font-size:14px; font-weight:700; color:#dc2626; background:#fff5f5; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; margin:0 0 22px; word-break:break-word;">
        </p>
        <p style="font-size:12px; color:#94a3b8; margin:0 0 22px;">
            All associated data (images, order history references) will be unlinked.
        </p>

        {{-- Buttons --}}
        <div style="display:flex; gap:10px;">
            <button type="button"
                    id="delete-modal-cancel"
                    style="flex:1; border:1px solid #e2e8f0; background:#fff; border-radius:12px; padding:11px; font-size:13px; font-weight:600; color:#475569; cursor:pointer; transition:background .15s;"
                    onmouseover="this.style.background='#f8fafc'"
                    onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button type="button"
                    id="delete-modal-confirm"
                    style="flex:1; border:none; border-radius:12px; padding:11px; font-size:13px; font-weight:700; color:#fff; background:#dc2626; cursor:pointer; transition:background .15s;"
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal      = document.getElementById('delete-medicine-modal');
    const nameEl     = document.getElementById('delete-medicine-name');
    const cancelBtn  = document.getElementById('delete-modal-cancel');
    const confirmBtn = document.getElementById('delete-modal-confirm');
    let   pendingFormId = null;

    window.openDeleteModal = function (id, name) {
        pendingFormId = 'delete-form-' + id;
        nameEl.textContent = name;
        modal.style.display = 'flex';
    };

    function closeModal() {
        modal.style.display = 'none';
        pendingFormId = null;
    }

    cancelBtn.addEventListener('click', closeModal);

    confirmBtn.addEventListener('click', function () {
        if (pendingFormId) {
            // Show full-page loader if available
            if (window.adminLoader && typeof window.adminLoader.show === 'function') {
                window.adminLoader.show();
            }
            document.getElementById(pendingFormId).submit();
        }
        closeModal();
    });

    // Close on backdrop click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
    });
})();
</script>
