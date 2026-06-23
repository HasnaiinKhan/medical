@extends('layouts.shop')

@section('title', 'Medicines')

@section('content')

<style>
    /* ── Mobile-only filter drawer ── */
    #mob-filter-drawer {
        display: none;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: 80%;
        max-width: 300px;
        background: #fff;
        z-index: 9999;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.28s ease;
        box-shadow: 4px 0 20px rgba(0,0,0,0.15);
    }
    #mob-filter-drawer.is-open {
        display: block;
        transform: translateX(0);
    }
    #mob-filter-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 9998;
    }
    #mob-filter-backdrop.is-open {
        display: block;
    }
    @media (min-width: 1024px) {
        #mob-filter-drawer,
        #mob-filter-backdrop,
        #mob-filter-toggle { display: none !important; }
    }
</style>

{{-- ── Summary row with filter button top-right on mobile ── --}}
<div class="mb-4 flex items-start justify-between gap-3">
    <div id="medicines-page-summary" class="flex-1 min-w-0">
        @include('medicines._summary', ['medicines' => $medicines, 'categories' => $categories, 'q' => $q])
    </div>

    {{-- Filter icon button - mobile only --}}
    <button id="mob-filter-toggle"
            class="lg:hidden flex-shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white shadow-sm text-blue-600 hover:bg-blue-50 transition-colors"
            aria-label="Open filters">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
        </svg>
    </button>
</div>

{{-- ── Mobile filter backdrop ── --}}
<div id="mob-filter-backdrop"></div>

{{-- ── Mobile filter drawer ── --}}
<div id="mob-filter-drawer">
    <div class="flex items-center justify-between px-4 py-4 border-b border-white/20 flex-shrink-0"
         style="background: linear-gradient(135deg,#1e3a8a,#2563eb);">
        <span class="text-sm font-bold text-white uppercase tracking-wider">Filters</span>
        <button id="mob-filter-close"
                class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/15 text-white hover:bg-white/25 transition-colors"
                aria-label="Close filters">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div id="mob-filters-container">
        @include('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))
    </div>
</div>

{{-- ── Main layout: desktop sidebar + results ── --}}
<div class="flex gap-6">

    {{-- Desktop sidebar - hidden on mobile, shown on lg+ --}}
    <aside class="hidden lg:block w-72 max-w-[18rem] flex-shrink-0">
        <div id="medicines-filters-container"
             class="w-full rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-36">
            @include('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))
        </div>
    </aside>

    {{-- Results --}}
    <section class="min-w-0 flex-1">
        <div id="medicines-results-container" class="space-y-4 lg:space-y-6">
            @include('medicines._results', compact('medicines', 'categories', 'q'))
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Mobile drawer open / close ── */
    const toggleBtn = document.getElementById('mob-filter-toggle');
    const closeBtn  = document.getElementById('mob-filter-close');
    const backdrop  = document.getElementById('mob-filter-backdrop');
    const drawer    = document.getElementById('mob-filter-drawer');

    function openDrawer() {
        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
    if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
    if (backdrop)  backdrop.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });

    /* ── AJAX results loading ── */
    const resultsContainer = document.getElementById('medicines-results-container');
    const summaryContainer = document.getElementById('medicines-page-summary');

    if (!resultsContainer) return;

    async function loadResults(url, pushState = true) {
        resultsContainer.classList.add('opacity-50', 'pointer-events-none');

        try {
            const res  = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error');

            resultsContainer.innerHTML = data.resultsHtml || '';
            if (summaryContainer) summaryContainer.innerHTML = data.headingHtml || '';

            // Refresh both desktop and mobile filter containers
            if (data.filtersHtml) {
                const desk = document.getElementById('medicines-filters-container');
                const mob  = document.getElementById('mob-filters-container');
                if (desk) desk.innerHTML = data.filtersHtml;
                if (mob)  mob.innerHTML  = data.filtersHtml;
            }

            if (pushState) window.history.pushState({ url }, '', url);
            closeDrawer();
        } catch {
            window.location.href = url;
        } finally {
            resultsContainer.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    function buildUrl(form) {
        const fd  = new FormData(form);
        const url = new URL(form.action, window.location.origin);
        for (const [k, v] of fd.entries()) {
            const val = String(v).trim();
            if (val) k.endsWith('[]') ? url.searchParams.append(k, val) : url.searchParams.set(k, val);
        }
        return url.toString();
    }

    function isResultsForm(form) {
        return form instanceof HTMLFormElement &&
            (form.id === 'medicine-search-form' || form.matches('[data-medicine-results-form]'));
    }

    function toggleSection(sectionId, chevronId) {
        const section = document.getElementById(sectionId);
        const chevron = document.getElementById(chevronId);
        if (!section) return;
        const hidden = section.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('-rotate-180', !hidden);
    }

   function toggleSeeMore(itemClass, button) {

    const container = button.closest('#cat-section, #brand-section');

    const items = container.querySelectorAll('.' + itemClass);

    const expanded = button.dataset.expanded === 'true';

    const hiddenItems = [...items].filter(
        item => item.dataset.defaultHidden === 'true'
    );

    const list =
        itemClass === 'cat-item'
        ? container.querySelector('#cat-list')
        : container.querySelector('#brand-filter-list');

    if (!expanded) {

        hiddenItems.forEach(item => {
            item.classList.remove('hidden');
        });

        list.classList.add('scroll-enabled');

    } else {

        hiddenItems.forEach(item => {
            item.classList.add('hidden');
        });

        list.scrollTop = 0;

        list.classList.remove('scroll-enabled');

    }

    button.dataset.expanded = (!expanded).toString();

    const label = button.querySelector('.btn-label');

    if (!expanded) {

        label.textContent = 'See Less';

    } else {

        label.textContent =
            `See ${button.dataset.count} More`;

    }

    button.querySelector('.btn-icon')
        .classList.toggle('rotate-180', !expanded);
}

    window.toggleSection = toggleSection;
    window.toggleSeeMore = toggleSeeMore;

   document.addEventListener('wheel', function (e) {

    const list = e.target.closest('.filter-list.scroll-enabled');

    if (!list) return;

    e.preventDefault();
    e.stopPropagation();

    list.scrollBy({
        top: e.deltaY,
        behavior: 'smooth'
    });

}, { passive: false });

    document.addEventListener('submit', function (e) {
        if (!isResultsForm(e.target)) return;
        e.preventDefault();
        loadResults(buildUrl(e.target));
    });

    document.addEventListener('change', function (e) {
        if (!(e.target instanceof HTMLInputElement)) return;
        const form = e.target.closest('form[data-medicine-results-form]');
        if (!form || form.id !== 'brand-filter-form') return;
        loadResults(buildUrl(form));
    });

    document.addEventListener('input', function (e) {
        if (e.target.id !== 'brand-search-input') return;
        const q = e.target.value.trim().toLowerCase();
        document.querySelectorAll('#brand-filter-list .brand-filter-item').forEach(item => {
            const name = (item.dataset.brandName || '').toLowerCase();
            item.classList.toggle('hidden', q ? !name.includes(q) : item.dataset.defaultHidden === 'true');
        });
    });

    document.addEventListener('click', function (e) {
        const link = e.target.closest(
            '.js-medicine-results-link, .js-medicine-filter-link, #medicines-results-container nav[aria-label="pagination"] a'
        );
        if (!link) return;
        const url = link.getAttribute('href');
        if (!url) return;
        e.preventDefault();
        loadResults(url);
    });

    window.addEventListener('popstate', function () {
        loadResults(window.location.href, false);
    });
})();
</script>
@endpush
