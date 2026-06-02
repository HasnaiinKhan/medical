@extends('layouts.shop')

@section('title', 'Medicines')

@section('content')

<<<<<<< HEAD
<style>
    /* ── Mobile-only filter drawer ── */
    #mob-filter-drawer {
        display: none; /* hidden by default on all screens */
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

    /* On desktop: hide the mobile drawer entirely */
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

    {{-- Filter icon button — mobile only --}}
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

{{-- ── Mobile filter drawer (outside flex row, fixed overlay) ── --}}
<div id="mob-filter-drawer">
    {{-- Drawer header --}}
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
    {{-- Filters content (mobile copy) --}}
    <div id="mob-filters-container">
        @include('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))
    </div>
</div>

{{-- ── Main layout: desktop sidebar + results ── --}}
<div class="flex gap-6">

    {{-- Desktop sidebar — hidden on mobile, shown on lg+ --}}
    <aside class="hidden lg:block w-72 max-w-[18rem] flex-shrink-0">
        <div id="medicines-filters-container"
             class="w-full rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-36">
=======
<div id="medicines-page-summary" class="mb-6">
    @include('medicines._summary', ['medicines' => $medicines, 'categories' => $categories, 'q' => $q])
</div>

<div class="flex flex-col gap-6 lg:flex-row">
    <aside class="w-64 lg:w-72 lg:max-w-[18rem] flex-shrink-0 min-w-0">
        <div id="medicines-filters-container" class="w-full rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-36">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            @include('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))
        </div>
    </aside>

<<<<<<< HEAD
    {{-- Results --}}
    <section class="min-w-0 flex-1">
        <div id="medicines-results-container" class="space-y-4 lg:space-y-6">
            @include('medicines._results', compact('medicines', 'categories', 'q'))
        </div>
    </section>

=======
    <section class="min-w-0 flex-1">
        <div id="medicines-results-container" class="space-y-6">
            @include('medicines._results', compact('medicines', 'categories', 'q'))
        </div>
    </section>
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
</div>

@endsection

@push('scripts')
<script>
(function () {
<<<<<<< HEAD
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

    /* Toggle collapsible sections inside filters */
=======
    const resultsContainer = document.getElementById('medicines-results-container');
    const filtersContainer = document.getElementById('medicines-filters-container');
    const summaryContainer = document.getElementById('medicines-page-summary');

    if (!resultsContainer || !summaryContainer || !filtersContainer) return;

    async function loadResults(url, pushState = true) {
        resultsContainer.classList.add('opacity-60', 'pointer-events-none');
        filtersContainer.classList.add('opacity-60', 'pointer-events-none');

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Could not load medicines.');
            }

            resultsContainer.innerHTML = data.resultsHtml || '';
            summaryContainer.innerHTML = data.headingHtml || '';
            filtersContainer.innerHTML = data.filtersHtml || filtersContainer.innerHTML;

            if (pushState) {
                window.history.pushState({ url }, '', url);
            }
        } catch (error) {
            window.location.href = url;
        } finally {
            resultsContainer.classList.remove('opacity-60', 'pointer-events-none');
            filtersContainer.classList.remove('opacity-60', 'pointer-events-none');
        }
    }

    function buildUrlFromForm(form) {
        const formData = new FormData(form);
        const url = new URL(form.action, window.location.origin);

        for (const [key, value] of formData.entries()) {
            const trimmedValue = String(value).trim();
            if (trimmedValue !== '') {
                if (key.endsWith('[]')) {
                    url.searchParams.append(key, trimmedValue);
                } else {
                    url.searchParams.set(key, trimmedValue);
                }
            }
        }

        return url.toString();
    }

    function isMedicineResultsForm(form) {
        return form instanceof HTMLFormElement && (form.id === 'medicine-search-form' || form.matches('[data-medicine-results-form]'));
    }

>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    function toggleSection(sectionId, chevronId) {
        const section = document.getElementById(sectionId);
        const chevron = document.getElementById(chevronId);
        if (!section) return;
<<<<<<< HEAD
        const hidden = section.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('-rotate-180', !hidden);
    }

    function toggleSeeMore(itemClass, btnId) {
        const items  = Array.from(document.querySelectorAll('.' + itemClass));
        const btn    = document.getElementById(btnId);
        if (!btn || !items.length) return;
        const expand = btn.dataset.expanded !== 'true';
        items.forEach(el => el.classList.toggle('hidden', !expand && el.dataset.defaultHidden === 'true'));
        btn.dataset.expanded = expand ? 'true' : 'false';
        const lbl = btn.querySelector('.btn-label');
        const ico = btn.querySelector('.btn-icon');
        if (lbl) lbl.textContent = expand ? 'Show less' : 'See ' + Math.max(items.length - 5, 0) + ' more';
        if (ico) ico.classList.toggle('rotate-180', expand);
=======

        const isHidden = section.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('-rotate-180', !isHidden);
        }
    }

    function toggleSeeMore(itemClass, btnId) {
        const items = Array.from(document.querySelectorAll(`.${itemClass}`));
        const button = document.getElementById(btnId);
        if (!button || !items.length) return;

        const expanded = button.dataset.expanded === 'true';
        const showAll = !expanded;
        const itemCount = items.length;
        const label = button.querySelector('.btn-label');
        const icon = button.querySelector('.btn-icon');

        items.forEach((item) => {
            const defaultHidden = item.dataset.defaultHidden === 'true';
            if (showAll) {
                item.classList.remove('hidden');
            } else {
                item.classList.toggle('hidden', defaultHidden);
            }
        });

        button.dataset.expanded = showAll ? 'true' : 'false';
        if (label) {
            label.textContent = showAll ? 'Show less' : `See ${Math.max(itemCount - 5, 0)} more`;
        }
        if (icon) {
            icon.classList.toggle('rotate-180', showAll);
        }
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    }

    window.toggleSection = toggleSection;
    window.toggleSeeMore = toggleSeeMore;

<<<<<<< HEAD
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
=======
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!isMedicineResultsForm(form)) {
            return;
        }

        event.preventDefault();
        loadResults(buildUrlFromForm(form));
    });

    document.addEventListener('change', function (event) {
        const input = event.target;
        if (!(input instanceof HTMLInputElement)) return;

        const form = input.closest('form[data-medicine-results-form]');
        if (!form || form.id !== 'brand-filter-form') return;

        loadResults(buildUrlFromForm(form));
    });

    document.addEventListener('input', function (event) {
        if (!(event.target instanceof HTMLInputElement) || event.target.id !== 'brand-search-input') return;

        const query = event.target.value.trim().toLowerCase();
        const items = document.querySelectorAll('#brand-filter-list .brand-filter-item');

        items.forEach((item, index) => {
            const name = (item.dataset.brandName || item.textContent || '').toLowerCase();
            const matches = name.includes(query);
            const defaultHidden = item.dataset.defaultHidden === 'true';

            if (query === '') {
                item.classList.toggle('hidden', defaultHidden);
            } else {
                item.classList.toggle('hidden', !matches);
            }
        });
    });

    document.addEventListener('click', function (event) {
        const link = event.target.closest('.js-medicine-results-link, .js-medicine-filter-link, #medicines-results-container nav[aria-label="pagination"] a');
        if (!link) return;

        const url = link.getAttribute('href');
        if (!url) return;

        event.preventDefault();
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        loadResults(url);
    });

    window.addEventListener('popstate', function () {
        loadResults(window.location.href, false);
    });
})();
</script>
@endpush
