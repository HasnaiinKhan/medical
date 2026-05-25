@extends('layouts.shop')

@section('title', 'Medicines')

@section('content')

<div id="medicines-page-summary" class="mb-6">
    @include('medicines._summary', ['medicines' => $medicines, 'categories' => $categories, 'q' => $q])
</div>

<div class="flex flex-col gap-6 lg:flex-row">
    <aside class="w-64 lg:w-72 lg:max-w-[18rem] flex-shrink-0 min-w-0">
        <div id="medicines-filters-container" class="w-full rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-36">
            @include('medicines._filters', compact('categories', 'brands', 'brandFilters', 'q'))
        </div>
    </aside>

    <section class="min-w-0 flex-1">
        <div id="medicines-results-container" class="space-y-6">
            @include('medicines._results', compact('medicines', 'categories', 'q'))
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
(function () {
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

    function toggleSection(sectionId, chevronId) {
        const section = document.getElementById(sectionId);
        const chevron = document.getElementById(chevronId);
        if (!section) return;

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
    }

    window.toggleSection = toggleSection;
    window.toggleSeeMore = toggleSeeMore;

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
        loadResults(url);
    });

    window.addEventListener('popstate', function () {
        loadResults(window.location.href, false);
    });
})();
</script>
@endpush
