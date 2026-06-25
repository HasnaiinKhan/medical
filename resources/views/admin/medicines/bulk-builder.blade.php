@extends('admin.layouts.admin')
@section('title', 'Bulk Import Builder')
@section('page-title', 'Bulk Import Builder')
@section('page-subtitle', 'Search any brand, select products, download CSV, then import')

@php
    $bulkUrl         = route('admin.ai.medicine.bulk-search');
    $imgUrl          = route('admin.ai.medicine.image');
    $galleryUrl      = route('admin.ai.medicine.gallery');
    $batchImagesUrl  = route('admin.ai.medicine.batch-images');
    $descUrl         = route('admin.ai.medicine.description');
    $importUrl       = route('admin.medicines.import.form');
    $quickTags = ['Himalaya', 'Dabur', 'Cipla', 'Dolo', 'Pampers', 'Cetaphil'];
@endphp

@section('content')
<style>
/* ── Bulk Builder Page Styles ─────────────────────────────────── */

.bb-wrap          { font-family: 'Plus Jakarta Sans', sans-serif; }

/* Top bar */
.bb-topbar        { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; }
.bb-search-row    { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.bb-search-box    { position: relative; flex: 1; max-width: 420px; }
.bb-search-box svg{ position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; pointer-events: none; }
.bb-search-input  { width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; padding: 10px 14px 10px 38px; font-size: 13px; outline: none; box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
.bb-search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.15); }

.bb-btn           { display: inline-flex; align-items: center; gap: 8px; border: none; border-radius: 12px; padding: 10px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background .15s, opacity .15s; white-space: nowrap; }
.bb-btn svg       { width: 16px; height: 16px; flex-shrink: 0; }
.bb-btn-primary   { background: #1d4ed8; color: #fff; }
.bb-btn-primary:hover:not(:disabled) { background: #1e40af; }
.bb-btn-success   { background: #059669; color: #fff; }
.bb-btn-success:hover:not(:disabled) { background: #047857; }
.bb-btn-outline   { background: #fff; color: #475569; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,.05); text-decoration: none; }
.bb-btn-outline:hover { background: #f8fafc; }
.bb-btn:disabled  { opacity: .45; cursor: not-allowed; }

/* Spin animation */
@keyframes bb-spin { to { transform: rotate(360deg); } }
.bb-spin          { animation: bb-spin .75s linear infinite; }

/* Source badges */
.bb-badges        { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.bb-badge         { display: inline-flex; align-items: center; gap: 6px; border-radius: 99px; border: 1px solid; padding: 4px 10px; font-size: 11px; font-weight: 600; }
.bb-badge-blue    { border-color: #bfdbfe; background: #eff6ff; color: #1d4ed8; }
.bb-badge-red     { border-color: #fecaca; background: #fef2f2; color: #b91c1c; }
.bb-badge-count   { border-radius: 99px; padding: 1px 6px; font-size: 10px; font-weight: 700; }
.bb-badge-count-blue { background: #dbeafe; color: #1e40af; }
.bb-badge-count-red  { background: #fee2e2; color: #b91c1c; }

/* Error banner */
.bb-error-banner  { margin-bottom: 16px; border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; padding: 12px 16px; font-size: 13px; color: #b91c1c; font-weight: 500; }

/* Action bar */
.bb-actionbar     { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.05); }
.bb-actionbar-left  { display: flex; align-items: center; gap: 12px; }
.bb-actionbar-right { display: flex; align-items: center; gap: 8px; }
.bb-select-label  { display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; font-size: 13px; font-weight: 600; color: #374151; }
.bb-select-label input { width: 15px; height: 15px; accent-color: #2563eb; cursor: pointer; }
.bb-select-count  { border-radius: 99px; background: #dbeafe; color: #1e3a8a; padding: 2px 8px; font-size: 11px; font-weight: 700; }

/* Results grid */
.bb-grid          { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }

/* Product card */
.bb-card          { position: relative; display: flex; flex-direction: column; border: 2px solid #e2e8f0; border-radius: 16px; background: #fff; cursor: pointer; overflow: hidden; transition: border-color .15s, box-shadow .15s; }
.bb-card:hover    { border-color: #93c5fd; }
.bb-card.is-selected { border-color: #2563eb; box-shadow: 0 4px 16px rgba(37,99,235,.15); }

/* Card checkbox */
.bb-card-cb-wrap  { position: absolute; top: 10px; left: 10px; z-index: 10; }
.bb-card-cb-wrap input { width: 15px; height: 15px; accent-color: #2563eb; cursor: pointer; }

/* Source badge on card */
.bb-card-src      { position: absolute; top: 10px; right: 10px; z-index: 10; border-radius: 99px; padding: 2px 7px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
.bb-src-pharmeasy { background: #fff7ed; color: #c2410c; }
.bb-src-netmeds   { background: #f0fdfa; color: #0f766e; }
.bb-src-apollo    { background: #eff6ff; color: #1d4ed8; }
.bb-src-other     { background: #f1f5f9; color: #475569; }

/* Card image */
.bb-card-img      { position: relative; height: 112px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.bb-card-img img  { width: 100%; height: 100%; object-fit: contain; padding: 8px; box-sizing: border-box; }
.bb-card-img-placeholder { font-size: 42px; font-weight: 900; color: #e2e8f0; user-select: none; }
.bb-img-loader    { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.8); }
.bb-img-loader svg { width: 20px; height: 20px; color: #3b82f6; }

/* Card body */
.bb-card-body     { display: flex; flex-direction: column; flex: 1; padding: 12px; gap: 4px; }
.bb-card-name     { font-size: 12px; font-weight: 700; color: #0f172a; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.bb-card-mfr      { font-size: 10px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bb-card-price    { display: flex; align-items: center; gap: 6px; margin-top: 2px; }
.bb-card-price-val { font-size: 14px; font-weight: 900; color: #0f172a; }
.bb-card-price-mrp { font-size: 10px; color: #cbd5e1; text-decoration: line-through; }
.bb-card-tags     { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
.bb-tag           { border-radius: 99px; padding: 2px 7px; font-size: 9px; font-weight: 700; }
.bb-tag-cat       { background: #eff6ff; color: #1d4ed8; }
.bb-tag-rx        { background: #fffbeb; color: #b45309; }
.bb-card-desc-status { margin-top: 4px; font-size: 10px; }
.bb-desc-generating { color: #3b82f6; font-weight: 500; }
.bb-desc-ready      { color: #059669; font-weight: 500; }
.bb-desc-source     { color: #94a3b8; }
.bb-desc-none       { color: #cbd5e1; }

/* Empty states */
.bb-empty         { display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fff; border: 2px dashed #e2e8f0; border-radius: 16px; padding: 64px 24px; text-align: center; }
.bb-empty-icon    { font-size: 52px; margin-bottom: 16px; }
.bb-empty-title   { font-size: 15px; font-weight: 700; color: #374151; margin: 0 0 6px; }
.bb-empty-sub     { font-size: 13px; color: #94a3b8; margin: 0 0 20px; max-width: 340px; }
.bb-quick-tags    { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
.bb-quick-tag     { border: 1px solid #e2e8f0; border-radius: 99px; background: #fff; padding: 5px 14px; font-size: 12px; font-weight: 600; color: #475569; cursor: pointer; transition: border-color .15s, color .15s; }
.bb-quick-tag:hover { border-color: #60a5fa; color: #1d4ed8; }

/* Toast notification */
.bb-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #0f172a;
    color: #fff;
    border-radius: 12px;
    padding: 13px 18px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(0,0,0,.22);
    pointer-events: none;
    opacity: 0;
    transform: translateY(12px);
    transition: opacity .25s ease, transform .25s ease;
}
.bb-toast.bb-toast-show {
    opacity: 1;
    transform: translateY(0);
}
.bb-toast-icon {
    width: 18px;
    height: 18px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bb-toast-icon svg { width: 11px; height: 11px; }

/* Responsive */
@media (max-width: 600px) {
    .bb-grid        { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .bb-topbar      { flex-direction: column; align-items: stretch; }
    .bb-search-row  { flex-wrap: wrap; }
    .bb-search-box  { max-width: 100%; }
    .bb-actionbar   { flex-direction: column; align-items: stretch; }
    .bb-actionbar-right { flex-wrap: wrap; }
}
@media (max-width: 380px) {
    .bb-grid        { grid-template-columns: 1fr; }
}
</style>

<div x-data="bulkBuilder()" x-init="init()" class="bb-wrap">

    {{-- ── Top action bar ───────────────────────────────────────────── --}}
    <div class="bb-topbar">
        <div class="bb-search-row">
            <div class="bb-search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input x-model="query"
                       @keydown.enter.prevent="doSearch"
                       type="text"
                       placeholder="Search brand or product - e.g. Himalaya, Dolo…"
                       class="bb-search-input">
            </div>
            <button @click="doSearch"
                    :disabled="loading || query.trim().length < 2"
                    class="bb-btn bb-btn-primary">
                <svg x-show="!loading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg x-show="loading" class="bb-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"/>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75"/>
                </svg>
                <span x-text="loading ? 'Searching…' : 'Search'"></span>
            </button>
        </div>

        {{-- Source badges --}}
        <div x-show="searchLog && Object.keys(searchLog).length" class="bb-badges">
            <template x-for="[src, count] in Object.entries(searchLog)" :key="src">
                <span class="bb-badge"
                      :class="count === 'error' ? 'bb-badge-red' : 'bb-badge-blue'">
                    <span x-text="src"></span>
                    <span x-show="count !== 'error'" class="bb-badge-count bb-badge-count-blue"
                          x-text="count + ' results'"></span>
                    <span x-show="count === 'error'" class="bb-badge-count bb-badge-count-red">failed</span>
                </span>
            </template>
        </div>
    </div>

    {{-- ── Error banner ─────────────────────────────────────────────── --}}
    <div x-show="error" x-transition class="bb-error-banner">
        <span x-text="error"></span>
    </div>

    {{-- ── Bulk action bar ───────────────────────────────────────────── --}}
    <div x-show="results.length" x-transition class="bb-actionbar">
        <div class="bb-actionbar-left">
            <label class="bb-select-label">
                <input type="checkbox"
                       :checked="allSelected"
                       :indeterminate="someSelected && !allSelected"
                       @change="toggleAll($event.target.checked)">
                Select all
                <span style="font-weight:400;color:#94a3b8;" x-text="'(' + results.length + ' products)'"></span>
            </label>
            <span x-show="selectedCount > 0" class="bb-select-count"
                  x-text="selectedCount + ' selected'"></span>
        </div>

        <div class="bb-actionbar-right">
            <button @click="downloadCsv"
                    :disabled="selectedCount === 0 || downloading_all"
                    class="bb-btn bb-btn-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span x-text="downloading_all ? 'Downloading…' : (selectedCount === 0 ? 'Download CSV' : 'Download CSV (' + selectedCount + ')')"></span>
            </button>

            <a href="{{ $importUrl }}" data-no-loader class="bb-btn bb-btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </a>
        </div>
    </div>

    {{-- ── Results grid ──────────────────────────────────────────────── --}}
    <div x-show="results.length" class="bb-grid">
        <template x-for="(item, idx) in results" :key="idx">
            <label :for="'cb-' + idx"
                   class="bb-card"
                   :class="item.selected ? 'is-selected' : ''">

                {{-- Checkbox --}}
                <div class="bb-card-cb-wrap">
                    <input :id="'cb-' + idx" type="checkbox" x-model="item.selected">
                </div>

                {{-- Source badge --}}
                <div class="bb-card-src"
                     :class="{
                         'bb-src-pharmeasy': item.source_platform === 'PharmEasy',
                         'bb-src-netmeds':   item.source_platform === 'NetMeds',
                         'bb-src-apollo':    item.source_platform === 'Apollo Pharmacy',
                         'bb-src-other':    !['PharmEasy','NetMeds','Apollo Pharmacy'].includes(item.source_platform)
                     }"
                     x-text="item.source_platform || 'AI'">
                </div>

                {{-- Image --}}
                <div class="bb-card-img">
                    <template x-if="item.local_image_url || item.image_url">
                        <img :src="item.local_image_url || item.image_url"
                             loading="lazy"
                             x-on:error="$el.style.display='none'">
                    </template>
                    <template x-if="!item.local_image_url && !item.image_url">
                        <span class="bb-card-img-placeholder"
                              x-text="(item.name || '?')[0].toUpperCase()"></span>
                    </template>
                    <div x-show="item.downloading_img" class="bb-img-loader">
                        <svg class="bb-spin" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"/>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75"/>
                        </svg>
                    </div>
                </div>

                {{-- Info --}}
                <div class="bb-card-body">
                    <p class="bb-card-name" x-text="item.name"></p>
                    <p class="bb-card-mfr"  x-text="item.manufacturer || '-'"></p>

                    <div class="bb-card-price">
                        <span class="bb-card-price-val"
                              x-text="item.price_suggestion ? '₹' + parseFloat(item.price_suggestion).toFixed(2) : '-'"></span>
                        <span x-show="item.mrp_suggestion && item.mrp_suggestion > item.price_suggestion"
                              class="bb-card-price-mrp"
                              x-text="'₹' + parseFloat(item.mrp_suggestion).toFixed(2)"></span>
                    </div>

                    <div class="bb-card-tags">
                        <span x-show="item.category" class="bb-tag bb-tag-cat" x-text="item.category"></span>
                        <span x-show="item.prescription_required" class="bb-tag bb-tag-rx">Rx</span>
                    </div>

                    <div class="bb-card-desc-status">
                        <span x-show="item.generating_desc" class="bb-desc-generating">✨ Generating description…</span>
                        <span x-show="!item.generating_desc && item.ai_description" class="bb-desc-ready">✓ AI description ready</span>
                        <span x-show="!item.generating_desc && !item.ai_description && item.description" class="bb-desc-source">Description from source</span>
                        <span x-show="!item.generating_desc && !item.ai_description && !item.description" class="bb-desc-none">No description yet</span>
                    </div>
                </div>
            </label>
        </template>
    </div>

    {{-- ── No results after search ───────────────────────────────────── --}}
    <div x-show="!loading && !results.length && searched" class="bb-empty">
        <div class="bb-empty-icon">🔍</div>
        <p class="bb-empty-title">No results found</p>
        <p class="bb-empty-sub">Try a different brand name like "Himalaya", "Dabur"</p>
    </div>

    {{-- ── Initial state (before any search) ──────────────────────────── --}}
    <div x-show="!loading && !results.length && !searched" class="bb-empty">
        <div class="bb-empty-icon" style="width:50px;"><img src="{{ asset('Images/medical-history.png') }}" alt="Medical History"></div>
        <p class="bb-empty-title">Search for any brand or product</p>
        <p class="bb-empty-sub">
            Results from PharmEasy &amp; NetMeds shown together &mdash; duplicates removed.
            Select what you want and download a ready-to-import CSV.
        </p>
        <div class="bb-quick-tags">
            @foreach($quickTags as $tag)
            <button type="button"
                    @click="query = '{{ $tag }}'; doSearch()"
                    class="bb-quick-tag">
                {{ $tag }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ── Toast notification ────────────────────────────────────────── --}}
    <div id="bb-toast" class="bb-toast">
        <span class="bb-toast-icon">
            <svg fill="none" stroke="#fff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </span>
        <span id="bb-toast-msg">CSV downloaded successfully!</span>
    </div>

</div>{{-- end x-data --}}
@endsection


@push('scripts')
<script>
(function () {
    var BULK_URL         = {{ Js::from($bulkUrl) }};
    var BATCH_IMAGES_URL = {{ Js::from($batchImagesUrl) }};
    var DESC_URL         = {{ Js::from($descUrl) }};
    var CSRF             = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    function bulkBuilder() {
        return {
            query:           '',
            loading:         false,
            searched:        false,
            results:         [],
            searchLog:       {},
            error:           '',
            downloading_all: false,

            get selectedCount() { return this.results.filter(function(r) { return r.selected; }).length; },
            get allSelected()   { return this.results.length > 0 && this.results.every(function(r) { return r.selected; }); },
            get someSelected()  { return this.results.some(function(r)  { return r.selected; }); },

            init: function () {},

            /* ── All unique image URLs for one item ────────────────────── */
            imageUrlsForItem: function (item) {
                var seen = {};
                return [item.image_url].concat(item.gallery_image_urls || []).filter(Boolean).filter(function(u) {
                    var k = String(u).split('?')[0];
                    if (seen[k]) return false;
                    seen[k] = true;
                    return true;
                }).slice(0, 8);
            },

            /* ── Search ─────────────────────────────────────────────────── */
            doSearch: async function () {
                var q = this.query.trim();
                if (q.length < 2 || this.loading) return;
                this.loading = true; this.error = ''; this.results = []; this.searched = false;
                try {
                    var res  = await fetch(BULK_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ name: q }),
                    });
                    var data = await res.json();
                    if (!res.ok || data.error) {
                        this.error = data.error || 'No results found. Try a different search.';
                    } else {
                        this.searchLog = data.search_log || {};
                        this.results = (data.results || []).map(function(item) {
                            return Object.assign({}, item, {
                                selected: false, local_image_url: null,
                                local_gallery_images: [], downloading_img: false,
                                ai_description: null, generating_desc: false,
                            });
                        });
                        this.runDescriptionQueue();
                    }
                } catch (e) {
                    this.error = 'Network error. Please check your connection.';
                } finally {
                    this.loading = false; this.searched = true;
                }
            },

            /* ── AI descriptions — 3 concurrent workers ─────────────────── */
            generateDescription: async function (idx) {
                var item = this.results[idx];
                if (!item) return;
                this.results[idx].generating_desc = true;
                try {
                    var res  = await fetch(DESC_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({
                            name: item.name||'', manufacturer: item.manufacturer||'',
                            composition: item.composition||'', uses: item.uses||[],
                            dosage_form: item.dosage_form||'', category: item.category||'',
                            existing: item.description||'', slug: item.slug||'',
                            source_platform: item.source_platform||'',
                        }),
                    });
                    var data = await res.json();
                    if (data.description) this.results[idx].ai_description = data.description;
                } catch (_) {}
                this.results[idx].generating_desc = false;
            },

            runDescriptionQueue: async function () {
                var CONCURRENCY = 3, idx = 0, total = this.results.length, self = this;
                var worker = async function () {
                    while (idx < total) { var i = idx++; await self.generateDescription(i); }
                };
                var workers = [];
                for (var w = 0; w < Math.min(CONCURRENCY, total); w++) workers.push(worker());
                await Promise.all(workers);
            },

            toggleAll: function (checked) {
                this.results.forEach(function(item) { item.selected = checked; });
            },

            /* ── Download CSV ─────────────────────────────────────────────
               Single POST to batchImages → PHP curl_multi downloads
               everything in parallel (one Apache thread, not N×M).      */
            downloadCsv: async function () {
                var selectedItems = this.results.filter(function(r) { return r.selected; });
                if (!selectedItems.length) return;
                this.downloading_all = true;

                /* 1. Batch-download all images — max 4 per product */
                var batchPayload = selectedItems.map(function(item) {
                    var seen = {};
                    var urls = [item.image_url].concat(item.gallery_image_urls||[]).filter(Boolean).filter(function(u) {
                        var k = String(u).split('?')[0]; if (seen[k]) return false; seen[k]=true; return true;
                    }).slice(0, 4);   // ← hard cap: 4 images max per product
                    return { urls: urls, platform: item.source_platform||'' };
                });

                var batchResults = {};
                try {
                    var res  = await fetch(BATCH_IMAGES_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ items: batchPayload }),
                    });
                    var data = await res.json();
                    (data.results || []).forEach(function(r) { batchResults[r.index] = r.images || []; });
                } catch (_) {}

                /* 2. Build CSV — separate column per image (image_url, image_url_2, image_url_3, image_url_4) */
                var catMap = {
                    'fever-pain':'Fever & Pain','vitamins':'Vitamins','digestive':'Digestive',
                    'diabetes':'Diabetes','heart-bp':'Heart & BP','skin':'Skin Care',
                    'cold-allergy':'Cold & Allergy','eye-ear':'Eye & Ear',
                    'bone-joint':'Bone & Joint','immunity':'Immunity',
                };
                var esc = function(v) {
                    var s = String(v==null?'':v).replace(/\r?\n|\r/g,' ');
                    return (s.indexOf(',')!==-1||s.indexOf('"')!==-1) ? '"'+s.replace(/"/g,'""')+'"' : s;
                };
                var autoDesc = function(item) {
                    var cat = catMap[item.category]||item.category||'healthcare';
                    var p = [(item.name||'')+(item.manufacturer?' by '+item.manufacturer:'')+
                             ' is a trusted '+cat.toLowerCase()+' product available at your online pharmacy.'];
                    if (item.composition)
                        p.push('It contains '+item.composition+' as the key active ingredient'+(item.dosage_form?', available as a '+item.dosage_form.toLowerCase():'')+' .');
                    else if (item.dosage_form) p.push('Available in '+item.dosage_form.toLowerCase()+' form for convenient use.');
                    p.push(item.prescription_required
                        ? 'This product requires a valid prescription from a licensed healthcare professional.'
                        : 'Available over the counter without a prescription.');
                    p.push('Order now for fast delivery across India.');
                    return p.join(' ');
                };

                // Header: fixed columns + 4 separate image columns
                var rows = [['name','manufacturer','category','mrp','price',
                             'prescription_required','stock','description',
                             'image_url','image_url_2','image_url_3','image_url_4'].join(',')];

                selectedItems.forEach(function(item, selIdx) {
                    var catName = catMap[item.category]||item.category||'General';
                    var desc    = (item.ai_description&&item.ai_description.trim()) ? item.ai_description
                                : (item.description&&item.description.trim())       ? item.description
                                : autoDesc(item);

                    // Get up to 4 locally-downloaded images, fall back to remote URLs
                    var localImages = batchResults[selIdx]||[];
                    var allImages;
                    if (localImages.length) {
                        allImages = localImages.slice(0, 4);
                    } else {
                        // Fallback: use remote URLs (no batch result)
                        var seen = {};
                        allImages = [item.image_url].concat(item.gallery_image_urls||[])
                            .filter(Boolean)
                            .filter(function(u){
                                var k = String(u).split('?')[0];
                                if (seen[k]) return false; seen[k]=true; return true;
                            })
                            .slice(0, 4);
                    }

                    // Pad to exactly 4 slots (empty string if fewer images available)
                    while (allImages.length < 4) allImages.push('');

                    rows.push([
                        esc(item.name),
                        esc(item.manufacturer||''),
                        esc(catName),
                        esc(item.mrp_suggestion||''),
                        esc(item.price_suggestion||''),
                        esc(item.prescription_required?'true':'false'),
                        esc('100'),
                        esc(desc),
                        esc(allImages[0]),
                        esc(allImages[1]),
                        esc(allImages[2]),
                        esc(allImages[3]),
                    ].join(','));
                });

                var blob = new Blob(['\uFEFF'+rows.join('\r\n')], {type:'text/csv;charset=utf-8;'});
                var bUrl = URL.createObjectURL(blob);
                var a    = document.createElement('a');
                a.href   = bUrl;
                a.download = this.query.trim().toLowerCase().replace(/[^a-z0-9]+/g,'_').replace(/^_+|_+$/g,'')
                           + '_' + new Date().toISOString().slice(0,10) + '.csv';
                document.body.appendChild(a); a.click(); document.body.removeChild(a);
                URL.revokeObjectURL(bUrl);
                this.downloading_all = false;
                showToast('CSV downloaded! ('+selectedItems.length+' medicines, images saved locally)');
            },
        };
    }

    /* ── Toast ───────────────────────────────────────────────────────── */
    function showToast(msg) {
        var t = document.getElementById('bb-toast'), l = document.getElementById('bb-toast-msg');
        if (!t) return;
        if (l) l.textContent = msg;
        if (showToast._t) clearTimeout(showToast._t);
        t.classList.add('bb-toast-show');
        showToast._t = setTimeout(function(){ t.classList.remove('bb-toast-show'); }, 3500);
    }

    window.bulkBuilder = bulkBuilder;
}());
</script>
@endpush
