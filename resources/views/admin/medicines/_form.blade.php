{{-- Shared form partial for create & edit --}}
@php
    $existingImages = isset($medicine)
        ? array_values(array_filter(array_merge(
            [$medicine->image_url ?? ''],
            $medicine->extra_images ?? []
          )))
        : [];
    $initialImages = json_encode($existingImages ?: ['']);
@endphp

{{-- ═══════════════════════════════════════════════════════
     PHARMEASY AUTO-FILL PANEL  (pure vanilla JS, no Alpine)
═══════════════════════════════════════════════════════ --}}
<style>
.pe-panel        { display:none; margin-top:12px; border-radius:18px; border:1.5px solid #bfdbfe; background:linear-gradient(135deg,#eff6ff,#f5f3ff); padding:20px; box-shadow:0 4px 20px rgba(37,99,235,.10); }
.pe-panel.open   { display:block; }
.pe-search-row   { display:flex; gap:8px; margin-bottom:12px; }
.pe-input        { flex:1; border:1.5px solid #bfdbfe; border-radius:12px; padding:10px 14px 10px 38px; font-size:13px; outline:none; background:#fff; }
.pe-input:focus  { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.15); }
.pe-btn          { background:#2563eb; color:#fff; border:none; border-radius:12px; padding:10px 20px; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap; display:inline-flex; align-items:center; gap:6px; }
.pe-btn:hover    { background:#1d4ed8; }
.pe-btn:disabled { opacity:.5; cursor:not-allowed; }
.pe-error        { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:10px 14px; font-size:13px; color:#dc2626; margin-bottom:10px; display:none; }
.pe-meta         { font-size:11px; color:#94a3b8; margin-bottom:8px; display:none; }
.pe-grid         { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; max-height:400px; overflow-y:auto; padding-right:4px; }
@media(max-width:900px){ .pe-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:600px){ .pe-grid{ grid-template-columns:1fr; } }
.pe-card         { background:#fff; border:2px solid #e2e8f0; border-radius:14px; padding:12px; cursor:pointer; transition:border-color .2s,box-shadow .2s; position:relative; }
.pe-card:hover   { border-color:#3b82f6; box-shadow:0 4px 16px rgba(37,99,235,.12); }
.pe-card.active  { border-color:#2563eb; background:#eff6ff; }
.pe-card-img     { width:52px; height:52px; object-fit:contain; border-radius:8px; border:1px solid #e2e8f0; padding:4px; background:#fff; flex-shrink:0; }
.pe-card-initial { width:52px; height:52px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:900; color:#94a3b8; flex-shrink:0; }
.pe-card-top     { display:flex; gap:10px; align-items:flex-start; margin-bottom:8px; }
.pe-card-name    { font-size:12px; font-weight:700; color:#0f172a; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.pe-card-mfg     { font-size:10px; color:#64748b; margin-top:2px; }
.pe-card-bottom  { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.pe-price        { font-size:13px; font-weight:800; color:#0f172a; }
.pe-mrp          { font-size:10px; color:#94a3b8; text-decoration:line-through; }
.pe-badge        { border-radius:99px; padding:2px 7px; font-size:9px; font-weight:700; }
.pe-badge-rx     { background:#fef3c7; color:#92400e; }
.pe-badge-cat    { background:#f1f5f9; color:#475569; }
.pe-tick         { font-size:10px; color:#2563eb; font-weight:700; display:none; margin-top:6px; }
.pe-card.active .pe-tick { display:flex; align-items:center; gap:3px; }
.pe-overlay      { display:none; position:absolute; inset:0; border-radius:13px; background:rgba(255,255,255,.8); align-items:center; justify-content:center; z-index:5; }
.pe-card.loading .pe-overlay { display:flex; }
.pe-spinner      { width:22px; height:22px; border:3px solid #bfdbfe; border-top-color:#2563eb; border-radius:50%; animation:pe-spin .7s linear infinite; }
@keyframes pe-spin{ to{ transform:rotate(360deg); } }
.pe-toggle-btn   { display:inline-flex; align-items:center; gap:8px; border-radius:14px; padding:10px 20px; font-size:13px; font-weight:700; cursor:pointer; border:none; background:linear-gradient(135deg,#2563eb,#7c3aed); color:#fff; box-shadow:0 2px 10px rgba(37,99,235,.25); transition:opacity .2s; }
.pe-toggle-btn:hover { opacity:.9; }
.pe-toast        { position:fixed; bottom:24px; right:24px; z-index:9999; background:#2563eb; color:#fff; border-radius:14px; padding:12px 20px; font-size:13px; font-weight:700; box-shadow:0 8px 24px rgba(37,99,235,.3); display:flex; align-items:center; gap:8px; pointer-events:none; }
</style>

<div style="margin-bottom:24px;">

    {{-- Toggle button --}}
    <button type="button" class="pe-toggle-btn" id="pe-toggle">
        <span id="pe-toggle-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <span id="pe-toggle-label"> Auto-Fill Agent</span>
    </button>

    {{-- Panel --}}
    <div class="pe-panel" id="pe-panel">

        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:60px;height:60px;border-radius:12px;background:#2563eb;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:50px;flex-shrink:0;"><img src="{{ asset('Images/chatbot.gif') }}" alt="Loading" class="h-16 w-max"></div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">Rx Plus 365 Auto-Fill</p>
                <p style="font-size:11px;color:#64748b;margin:2px 0 0;">Search any medicine, face wash, diaper, supplement - click a result to fill all form fields.</p>
            </div>
        </div>

        {{-- Search --}}
        <div class="pe-search-row">
            <div style="position:relative;flex:1;">
                <svg style="position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="pe-query" class="pe-input"
                       placeholder="e.g. Dolo 650, Himalaya face wash, Pampers diaper…">
            </div>
            <button type="button" class="pe-btn" id="pe-search-btn">
                <span id="pe-btn-text">Search</span>
            </button>
        </div>

        {{-- Error --}}
        <div class="pe-error" id="pe-error"></div>

        {{-- Meta --}}
        <div class="pe-meta" id="pe-meta"></div>

        {{-- Results --}}
        <div class="pe-grid" id="pe-grid"></div>

    </div>
</div>


<div class="grid gap-6 lg:grid-cols-3">

    {{-- ===== LEFT: main fields ===== --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Basic info --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Basic Information</h3>
            <div class="grid gap-4 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Medicine Name *</label>
                    <input name="name" value="{{ old('name', $medicine->name ?? '') }}" required
                           placeholder="e.g. Dolo 650 Tablet"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Manufacturer *</label>
                    <input name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer ?? '') }}" required
                           placeholder="e.g. Micro Labs"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('manufacturer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Category: select existing OR create new --}}
                <div x-data="{ creating: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Category *</label>
                        <button type="button" @click="creating = !creating"
                                class="text-xs font-semibold text-blue-700 hover:underline"
                                x-text="creating ? '← Pick existing' : '+ Create new'"></button>
                    </div>
                    <div x-show="!creating">
                        <select name="category_id"
                                :required="!creating"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                            <option value="">- Select a category -</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $medicine->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div x-show="creating" x-cloak>
                        <input name="new_category_name"
                               placeholder="New category name…"
                               class="w-full rounded-xl border border-blue-300 bg-blue-50 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        <p class="mt-1 text-xs text-blue-700">Category will be created automatically on save.</p>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Brief description of the medicine…"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 resize-none">{{ old('description', $medicine->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Pricing & Stock --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Pricing & Stock</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">MRP (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">₹</span>
                        <input name="mrp" type="number" step="0.01" min="0.01"
                               value="{{ old('mrp', isset($medicine) ? number_format($medicine->mrp_paise/100,2,'.','') : '') }}"
                               required placeholder="0.00"
                               class="w-full rounded-xl border border-slate-200 pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    @error('mrp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Selling Price (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">₹</span>
                        <input name="price" type="number" step="0.01" min="0.01"
                               value="{{ old('price', isset($medicine) ? number_format($medicine->price_paise/100,2,'.','') : '') }}"
                               required placeholder="0.00"
                               class="w-full rounded-xl border border-slate-200 pl-7 pr-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    </div>
                    @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Stock *</label>
                    <input name="stock" type="number" min="0"
                           value="{{ old('stock', $medicine->stock ?? 100) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ===== PRODUCT IMAGES ===== --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Product Images</h3>
                    <p class="text-xs text-slate-500 mt-0.5">First image is the primary. Add up to 8 images.</p>
                </div>
                <button type="button" onclick="addImageSlot()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-800 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Image
                </button>
            </div>

            <div id="image-slots" class="space-y-4">
                @php
                    $slots = isset($medicine)
                        ? array_values(array_filter(array_merge(
                            [$medicine->image_url ?? ''],
                            $medicine->extra_images ?? []
                          )))
                        : [''];
                    if (empty($slots)) $slots = [''];
                @endphp

                @foreach($slots as $slotIdx => $slotUrl)
                <div class="image-slot rounded-xl border border-slate-200 bg-slate-50 p-4" data-slot="{{ $slotIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $slotIdx === 0 ? 'text-blue-700' : 'text-slate-400' }}">
                            {{ $slotIdx === 0 ? '★ Primary Image' : 'Extra Image ' . $slotIdx }}
                        </span>
                        @if($slotIdx > 0)
                        <button type="button" onclick="removeImageSlot(this)"
                                class="rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Preview --}}
                        <div class="img-preview h-20 w-20 flex-shrink-0 rounded-xl overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                            @if($slotUrl)
                                <img src="{{ $slotUrl }}" class="h-full w-full object-contain p-1" onerror="this.style.opacity='.15'">
                            @else
                                <svg class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 space-y-2">
                            {{-- URL input --}}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">🔗 Image URL</label>
                                <input type="text"
                                       name="{{ $slotIdx === 0 ? 'image_url' : 'extra_image_url[]' }}"
                                       value="{{ $slotUrl }}"
                                       placeholder="https://example.com/image.jpg"
                                       class="img-url-input w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20"
                                       oninput="updatePreview(this)">
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-px bg-slate-200"></div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">or</span>
                                <div class="flex-1 h-px bg-slate-200"></div>
                            </div>

                            {{-- File upload --}}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">📁 Upload from Device</label>
                                <label class="flex items-center gap-2 cursor-pointer rounded-lg border-2 border-dashed border-slate-300 bg-white px-3 py-2 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                    <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span class="text-xs text-slate-500 file-label">Click to choose image (JPG, PNG, WEBP - max 4MB)</span>
                                    <input type="file"
                                           name="{{ $slotIdx === 0 ? 'image_file' : 'extra_image_file[]' }}"
                                           accept="image/jpeg,image/png,image/webp,image/gif"
                                           class="sr-only"
                                           onchange="handleFileSelect(this)">
                                </label>
                                <p class="mt-1 text-[10px] text-slate-400">File takes priority over URL if both provided.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" onclick="addImageSlot()"
                    class="mt-4 w-full rounded-xl border-2 border-dashed border-slate-300 py-2.5 text-xs font-semibold text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-colors">
                + Add another image slot
            </button>
        </div>
    </div>

    {{-- ===== RIGHT: options + submit ===== --}}
    <div class="space-y-5">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Options</h3>
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="prescription_required" value="1"
                           {{ old('prescription_required', $medicine->prescription_required ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer-checked:bg-blue-700 transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Prescription Required</p>
                    <p class="text-xs text-slate-500">Show Rx badge on product</p>
                </div>
            </label>
        </div>

        

        <div class="flex flex-col sm:flex-row gap-2">
            <button type="submit"
                    class="p-4 ml-1 w-full sm:flex-1 rounded-xl bg-blue-700 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-md">
                {{ isset($medicine) ? 'Update Medicine' : 'Create Medicine' }}
            </button>
            <a href="{{ route('admin.medicines.create') }}"
               class="p-4 sm:w-auto rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Image slot helpers ────────────────────────────────────────────────────────
var _slotCount = document.querySelectorAll('.image-slot').length;

function addImageSlot() {
    var container = document.getElementById('image-slots');
    if (!container) return null;
    var total = container.querySelectorAll('.image-slot').length;
    if (total >= 8) { alert('Maximum 8 images allowed.'); return null; }

    var idx = _slotCount++;          // unique index for name attrs
    var isExtra = (idx > 0);         // slot 0 is always primary; new dynamic slots are extra

    var slot = document.createElement('div');
    slot.className = 'image-slot rounded-xl border border-slate-200 bg-slate-50 p-4';
    slot.dataset.slot = idx;
    slot.innerHTML =
        '<div class="flex items-center justify-between mb-3">' +
            '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Extra Image ' + total + '</span>' +
            '<button type="button" onclick="removeImageSlot(this)" ' +
                    'class="rounded-lg p-1 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">' +
                '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' +
                '</svg>' +
            '</button>' +
        '</div>' +
        '<div class="flex flex-col sm:flex-row gap-3">' +
            '<div class="img-preview h-20 w-20 flex-shrink-0 rounded-xl overflow-hidden bg-white border border-slate-200 flex items-center justify-center">' +
                '<svg class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>' +
                '</svg>' +
            '</div>' +
            '<div class="flex-1 space-y-2">' +
                '<div>' +
                    '<label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">🔗 Image URL</label>' +
                    '<input type="text" name="extra_image_url[]" value="" placeholder="https://example.com/image.jpg" ' +
                           'class="img-url-input w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20" ' +
                           'oninput="updatePreview(this)">' +
                '</div>' +
                '<div class="flex items-center gap-2">' +
                    '<div class="flex-1 h-px bg-slate-200"></div>' +
                    '<span class="text-[10px] font-bold text-slate-400 uppercase">or</span>' +
                    '<div class="flex-1 h-px bg-slate-200"></div>' +
                '</div>' +
                '<div>' +
                    '<label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1">📁 Upload from Device</label>' +
                    '<label class="flex items-center gap-2 cursor-pointer rounded-lg border-2 border-dashed border-slate-300 bg-white px-3 py-2 hover:border-blue-400 hover:bg-blue-50 transition-colors">' +
                        '<svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>' +
                        '</svg>' +
                        '<span class="text-xs text-slate-500 file-label">Click to choose image (JPG, PNG, WEBP - max 4MB)</span>' +
                        '<input type="file" name="extra_image_file[]" accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only" onchange="handleFileSelect(this)">' +
                    '</label>' +
                    '<p class="mt-1 text-[10px] text-slate-400">File takes priority over URL if both provided.</p>' +
                '</div>' +
            '</div>' +
        '</div>';

    container.appendChild(slot);
    return slot;
}

function removeImageSlot(btn) {
    var slot = btn.closest('.image-slot');
    if (slot) slot.remove();
}

function updatePreview(input) {
    var slot    = input.closest('.image-slot');
    if (!slot) return;
    var preview = slot.querySelector('.img-preview');
    var url     = input.value.trim();

    if (url) {
        preview.innerHTML = '<img src="' + url + '" class="h-full w-full object-contain p-1" onerror="this.style.opacity=\'.15\'">';
    } else {
        preview.innerHTML =
            '<svg class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>' +
            '</svg>';
    }
}

function handleFileSelect(input) {
    var slot    = input.closest('.image-slot');
    if (!slot) return;
    var preview = slot.querySelector('.img-preview');
    var label   = slot.querySelector('.file-label');
    var file    = input.files && input.files[0];

    if (!file) return;

    // Validate size (4 MB)
    if (file.size > 4 * 1024 * 1024) {
        alert('Image must be under 4 MB. Please choose a smaller file.');
        input.value = '';
        return;
    }

    // Update label text
    if (label) label.textContent = file.name;

    // Mark that a file was selected
    input.setAttribute('data-file-selected', 'true');

    // Show local preview via FileReader
    var reader = new FileReader();
    reader.onload = function (e) {
        preview.innerHTML = '<img src="' + e.target.result + '" class="h-full w-full object-contain p-1">';
        // Clear any URL input in this slot so the file takes priority
        var urlInput = slot.querySelector('.img-url-input');
        if (urlInput) {
            urlInput.value = '';
        }
    };
    reader.readAsDataURL(file);
}

// Before form submit, clear URL fields if file was selected
document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit handler: clearing URL fields where files are selected');
            
            // For each image slot, if there's a file selected, clear the URL field
            document.querySelectorAll('.image-slot').forEach(function(slot, idx) {
                var fileInput = slot.querySelector('input[type="file"]');
                var urlInput = slot.querySelector('.img-url-input');
                
                if (fileInput && urlInput) {
                    var hasFile = fileInput.files && fileInput.files.length > 0;
                    var urlValue = urlInput.value.trim();
                    
                    console.log('Slot ' + idx + ': hasFile=' + hasFile + ', urlValue=' + (urlValue ? 'present' : 'empty'));
                    
                    // If a file is selected in this slot, ALWAYS clear the URL field
                    // to ensure file upload takes priority
                    if (hasFile) {
                        console.log('  → Clearing URL field because file is selected');
                        urlInput.value = '';
                        urlInput.setAttribute('data-cleared-for-upload', 'true');
                    }
                }
            });
        }, false); // Use capture phase to ensure this runs before any other handlers
    }
});

</script>

<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var CATEGORY_MAP = {
        @foreach($categories as $cat)
        "{{ $cat->slug }}": "{{ $cat->id }}",
        @endforeach
    };

    var SEARCH_URL = '{{ route('admin.ai.medicine.generate') }}';
    var DETAIL_URL = '{{ route('admin.ai.medicine.detail') }}';
    var DESC_URL   = '{{ route('admin.ai.medicine.description') }}';

    var toggleBtn  = document.getElementById('pe-toggle');
    var toggleLbl  = document.getElementById('pe-toggle-label');
    var toggleIcon = document.getElementById('pe-toggle-icon');
    var panel      = document.getElementById('pe-panel');
    var queryInput = document.getElementById('pe-query');
    var searchBtn  = document.getElementById('pe-search-btn');
    var btnText    = document.getElementById('pe-btn-text');
    var errorBox   = document.getElementById('pe-error');
    var metaBox    = document.getElementById('pe-meta');
    var grid       = document.getElementById('pe-grid');

    var currentSource  = null;
    var currentResults = [];
    var selectedIdx    = null;

    var CROSS_ICON  = '<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    var SEARCH_ICON = '<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>';

    // Toggle panel open/close
    toggleBtn.addEventListener('click', function () {
        var isOpen = panel.classList.toggle('open');
        toggleLbl.textContent = isOpen ? ' Close' : ' Auto-Fill Agent';
        toggleIcon.innerHTML  = isOpen ? CROSS_ICON : SEARCH_ICON;
        if (isOpen) queryInput.focus();
    });

    // Enter key on input
    queryInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });

    searchBtn.addEventListener('click', doSearch);

    // ── Search ──────────────────────────────────────────────────────────────
    function doSearch() {
        var q = queryInput.value.trim();
        if (!q) return;

        setLoading(true);
        showError('');
        metaBox.style.display = 'none';
        grid.innerHTML = '';
        currentResults = [];
        selectedIdx    = null;

        fetch(SEARCH_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ name: q })
        })
        .then(function (res) { return res.json().then(function (j) { return { ok: res.ok, j: j }; }); })
        .then(function (r) {
            if (!r.ok || r.j.error) {
                showError(r.j.error || 'No results found. Try a different name.');
            } else {
                currentResults = r.j.results || [];
                currentSource  = r.j.source  || null;
                renderResults();
            }
        })
        .catch(function () { showError('Network error. Please check your connection.'); })
        .finally(function () { setLoading(false); });
    }

    // ── Render cards ─────────────────────────────────────────────────────────
    function renderResults() {
        grid.innerHTML = '';

        if (!currentResults.length) {
            showError('No results found. Try a more specific name.');
            return;
        }

        var srcLabel = currentSource === 'pharmeasy' ? '🛒 PharmEasy' : '🤖 AI Generated';
        metaBox.textContent = srcLabel + ' · ' + currentResults.length + ' result(s) found';
        metaBox.style.display = 'block';

        currentResults.forEach(function (item, idx) {
            var card = document.createElement('div');
            card.className = 'pe-card';
            card.dataset.idx = idx;

            var imgHtml = item.image_url
                ? '<img src="' + esc(item.image_url) + '" class="pe-card-img" onerror="this.style.display=\'none\'">'
                : '<div class="pe-card-initial">' + esc((item.name || '?')[0].toUpperCase()) + '</div>';

            var rxBadge  = item.prescription_required ? '<span class="pe-badge pe-badge-rx">Rx</span>' : '';
            var catBadge = item.category ? '<span class="pe-badge pe-badge-cat">' + esc(item.category) + '</span>' : '';
            var price    = item.price_suggestion ? '<span class="pe-price">₹' + parseFloat(item.price_suggestion).toFixed(2) + '</span>' : '';
            var mrp      = (item.mrp_suggestion && item.price_suggestion && parseFloat(item.mrp_suggestion) > parseFloat(item.price_suggestion))
                         ? '<span class="pe-mrp">₹' + parseFloat(item.mrp_suggestion).toFixed(2) + '</span>' : '';

            card.innerHTML =
                '<div class="pe-overlay"><div class="pe-spinner"></div></div>' +
                '<div class="pe-card-top">' + imgHtml +
                    '<div style="flex:1;min-width:0;">' +
                        '<div class="pe-card-name">' + esc(item.name || '') + '</div>' +
                        '<div class="pe-card-mfg">' + esc(item.manufacturer || '') + '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="pe-card-bottom">' + price + mrp + rxBadge + catBadge + '</div>' +
                '<div class="pe-tick">✔ Applied to form</div>';

            card.addEventListener('click', function () { selectCard(idx); });
            grid.appendChild(card);
        });
    }

    // ── Select card ───────────────────────────────────────────────────────────
    function selectCard(idx) {
        var cards = grid.querySelectorAll('.pe-card');
        cards.forEach(function (c) { c.classList.remove('active'); });
        var card = grid.querySelector('[data-idx="' + idx + '"]');
        if (!card) return;
        card.classList.add('active', 'loading');
        selectedIdx = idx;

        var data = currentResults[idx];

        // Always fetch full pharmacy detail when slug is available so gallery images are included.
        if (data.slug && (data.source_platform === 'PharmEasy' || data.source_platform === 'NetMeds' || currentSource === 'pharmeasy')) {
            fetch(DETAIL_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ slug: data.slug, platform: data.source_platform || '' })
            })
            .then(function (res) { return res.json().then(function (j) { return { ok: res.ok, j: j }; }); })
            .then(function (r) {
                if (r.ok && r.j.data) data = r.j.data;
                card.classList.remove('loading');
                applyToForm(data);
            })
            .catch(function () {
                card.classList.remove('loading');
                applyToForm(data);
            });
        } else {
            card.classList.remove('loading');
            applyToForm(data);
        }
    }

    // ── Apply data to form ────────────────────────────────────────────────────
    function applyToForm(d) {
        if (!d) return;

        function setVal(name, val) {
            var el = document.querySelector('[name="' + name + '"]');
            if (el && val != null && val !== '') el.value = val;
        }

        setVal('name',         d.name);
        setVal('manufacturer', d.manufacturer);

        // Category select
        if (d.category && CATEGORY_MAP[d.category]) {
            var sel = document.querySelector('select[name="category_id"]');
            if (sel) sel.value = CATEGORY_MAP[d.category];
        }

        // Pricing
        if (d.mrp_suggestion)   setVal('mrp',   parseFloat(d.mrp_suggestion).toFixed(2));
        if (d.price_suggestion) setVal('price', parseFloat(d.price_suggestion).toFixed(2));

        // Prescription checkbox
        var rx = document.querySelector('input[name="prescription_required"]');
        if (rx) rx.checked = !!d.prescription_required;

        // ── Description: ALWAYS generate via AI. Use any existing data as context. ──
        var descEl = document.querySelector('[name="description"]');

        // Build whatever context we have from the product data
        var ctxParts = [];
        if (d.description)           ctxParts.push(d.description);
        if (d.composition)           ctxParts.push('Composition: ' + d.composition + '.');
        if (d.uses && d.uses.length) ctxParts.push('Uses: ' + d.uses.join('; ') + '.');
        var baseDesc = ctxParts.join('\n\n');

        // Count words helper
        function wordCount(str) {
            return str ? str.trim().replace(/\s+/g, ' ').split(' ').length : 0;
        }

        // Always call AI - unless we already have a proper description of ≥50 words
        // (e.g. PharmEasy detail fetch already returned a full paragraph)
        var needsAI = wordCount(d.description || '') < 50;

        if (descEl) {
            if (needsAI) {
                // Show placeholder while AI generates
                descEl.value = '';
                descEl.placeholder = '✨ Generating AI description…';
                descEl.disabled = true;

                fetch(DESC_URL, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body:    JSON.stringify({
                        name:         d.name         || '',
                        manufacturer: d.manufacturer || '',
                        composition:  d.composition  || '',
                        uses:         d.uses         || [],
                        dosage_form:  d.dosage_form  || '',
                        category:     d.category     || '',
                        existing:     baseDesc        || '',
                    })
                })
                .then(function (res) { return res.json(); })
                .then(function (j) {
                    descEl.disabled    = false;
                    descEl.placeholder = '';
                    if (j.description && wordCount(j.description) >= 50) {
                        descEl.value = j.description;
                        showDescBadge('✨ AI description generated');
                    } else if (j.description) {
                        // AI returned something but it's short - append to base context
                        descEl.value = (baseDesc ? baseDesc + '\n\n' : '') + j.description;
                        showDescBadge('✨ AI description generated');
                    } else {
                        // AI failed - use base context as fallback
                        descEl.value = baseDesc;
                    }
                })
                .catch(function () {
                    descEl.disabled    = false;
                    descEl.placeholder = '';
                    descEl.value       = baseDesc;
                });
            } else {
                // Already have ≥50 word description from pharmacy data - use it directly
                descEl.value = baseDesc;
                showDescBadge('📋 Description from ' + (d.source_platform || 'pharmacy'));
            }
        }

        fillProductImages(d);

        // Toast
        var toast = document.createElement('div');
        toast.className = 'pe-toast';
        toast.innerHTML = '✔ Form filled - ' + (needsAI ? '✨ generating description…' : 'description ready!');
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 4000);
    }

    // Small badge that briefly appears next to the description field
    function showDescBadge(msg) {
        var descEl = document.querySelector('[name="description"]');
        if (!descEl) return;
        var badge = document.createElement('p');
        badge.style.cssText = 'font-size:11px;color:#2563eb;font-weight:600;margin-top:4px;';
        badge.textContent = msg;
        descEl.parentNode.appendChild(badge);
        setTimeout(function () { badge.remove(); }, 4000);
    }

    function uniqueImageUrls(urls) {
        var seen = {};
        var result = [];
        (urls || []).forEach(function (url) {
            if (!url) return;
            var key = String(url).split('?')[0];
            if (!seen[key]) {
                seen[key] = true;
                result.push(url);
            }
        });
        return result.slice(0, 8);
    }

    function resetImageSlots() {
        var container = document.getElementById('image-slots');
        if (!container) return;

        var slots = container.querySelectorAll('.image-slot');
        slots.forEach(function (slot, idx) {
            if (idx > 0) slot.remove();
        });

        var primaryInput = document.querySelector('input[name="image_url"]');
        if (primaryInput) {
            primaryInput.value = '';
            primaryInput.placeholder = 'https://example.com/image.jpg';
            primaryInput.dispatchEvent(new Event('input'));
        }
    }

    function ensureImageSlot(idx) {
        var container = document.getElementById('image-slots');
        if (!container) return null;

        while (container.querySelectorAll('.image-slot').length <= idx) {
            if (!addImageSlot()) break;
        }

        return container.querySelectorAll('.image-slot')[idx] || null;
    }

    function setSlotImage(idx, url) {
        var slot = ensureImageSlot(idx);
        if (!slot) return;

        var input = idx === 0
            ? slot.querySelector('input[name="image_url"]')
            : slot.querySelector('input[name="extra_image_url[]"]');

        if (!input) return;
        input.value = url;
        input.dispatchEvent(new Event('input'));
    }

    function fillProductImages(d) {
        var urls = uniqueImageUrls([d.image_url].concat(d.gallery_image_urls || []));
        if (!urls.length) return;

        resetImageSlots();
        urls.forEach(function (url, idx) {
            setSlotImage(idx, url);
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function setLoading(on) {
        searchBtn.disabled = on;
        btnText.textContent = on ? 'Searching…' : 'Search';
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.style.display = msg ? 'block' : 'none';
    }

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

})();
</script>
@endpush
