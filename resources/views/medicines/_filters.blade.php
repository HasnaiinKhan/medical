@php
    $selectedBrands = $brandFilters ?? [];
    if (! $selectedBrands) {
        foreach ((array) request('brand') as $brandItem) {
            $brandItem = trim((string) $brandItem);
            if ($brandItem !== '') {
                $selectedBrands[] = $brandItem;
            }
        }
    }
@endphp

<<<<<<< HEAD
<style>
    /* Custom scrollbar styling with blue gradient */
    #cat-list::-webkit-scrollbar,
    #brand-filter-list::-webkit-scrollbar {
        width: 8px;
    }

    #cat-list::-webkit-scrollbar-track,
    #brand-filter-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    #cat-list::-webkit-scrollbar-thumb,
    #brand-filter-list::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
        border-radius: 10px;
        border: 2px solid #f1f5f9;
    }

    #cat-list::-webkit-scrollbar-thumb:hover,
    #brand-filter-list::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
    }

    /* Firefox scrollbar */
    #cat-list,
    #brand-filter-list {
        scrollbar-color: linear-gradient(180deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%) #f1f5f9;
        scrollbar-width: thin;
    }
</style>

=======
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
{{-- Filters Header --}}
<div class="flex items-center justify-between px-4 py-3 border-b border-slate-100"
     style="background: linear-gradient(135deg,#1e3a8a,#2563eb);">
    <div class="flex items-center gap-2">
        <i class="fa-solid fa-sliders text-white text-sm"></i>
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Filters</h3>
    </div>
    @if(request('category') || request('brand'))
        <a href="{{ route('medicines.index', array_filter(['q' => $q])) }}"
           class="js-medicine-filter-link text-xs font-semibold text-blue-200 hover:text-white transition-colors">
            Clear filters
        </a>
    @endif
</div>

    {{-- Shop by Category --}}
    <div class="border-b border-slate-100">
        <button type="button" onclick="toggleSection('cat-section', 'cat-chevron')"
                class="flex w-full items-center justify-between px-4 py-3 text-left hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600">
                <i class="fa-solid fa-table-cells-large text-blue-500 text-xs"></i>
                Shop by Category
            </span>
            <svg id="cat-chevron" class="h-4 w-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="cat-section" class="px-3 pb-3">
<<<<<<< HEAD
            <ul class="space-y-0.5 max-h-64 overflow-y-auto pr-2" id="cat-list">
=======
            <ul class="space-y-0.5" id="cat-list">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                <li>
                    <a href="{{ route('medicines.index', array_filter(['q' => $q, 'brand' => request('brand')])) }}"
                       class="js-medicine-filter-link flex items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ !request('category') ? 'bg-blue-700 font-semibold text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>All Medicines</span>
                        @if(!request('category'))
                            <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        @endif
                    </a>
                </li>
                @foreach ($categories as $i => $cat)
                    <li class="cat-item {{ $i >= 5 && request('category') !== $cat->slug ? 'hidden' : '' }}"
                        data-default-hidden="{{ $i >= 5 && request('category') !== $cat->slug ? 'true' : 'false' }}">
                        <a href="{{ route('medicines.index', array_filter(['q' => $q, 'category' => $cat->slug, 'brand' => request('brand')])) }}"
                           class="js-medicine-filter-link flex items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ request('category') === $cat->slug ? 'bg-blue-700 font-semibold text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>{{ $cat->name }}</span>
                            @if(request('category') === $cat->slug)
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            @if($categories->count() > 5)
                <button type="button" onclick="toggleSeeMore('cat-item', 'cat-more-btn')"
                        id="cat-more-btn"
                        class="mt-2 flex w-full items-center justify-center gap-1 rounded-lg py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 transition-colors">
                    <span class="btn-label">See {{ $categories->count() - 5 }} more</span>
                    <svg class="btn-icon h-3.5 w-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Shop by Brand --}}
    <div>
        <button type="button" onclick="toggleSection('brand-section', 'brand-chevron')"
                class="flex w-full items-center justify-between px-4 py-3 text-left hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600">
                <i class="fa-solid fa-tag text-blue-500 text-xs"></i>
                Shop by Brand
            </span>
            <svg id="brand-chevron" class="h-4 w-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="brand-section" class="px-3 pb-3">
            <div class="relative mb-3">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="brand-search-input" type="search"
                       placeholder="Search brands..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-600 focus:outline-none">
            </div>

            <form id="brand-filter-form" action="{{ route('medicines.index') }}" method="get" data-medicine-results-form>
                <input type="hidden" name="q" value="{{ $q }}">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

<<<<<<< HEAD
                <div id="brand-filter-list" class="space-y-1.5 max-h-64 overflow-y-auto pr-2">
=======
                <div id="brand-filter-list" class="space-y-1.5">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                    @forelse ($brands as $i => $brandItem)
                        <label class="brand-filter-item {{ $i >= 5 && !in_array($brandItem->name, $selectedBrands) ? 'hidden' : '' }} flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm transition-colors hover:border-blue-300 hover:bg-slate-50 cursor-pointer"
                               data-brand-name="{{ strtolower($brandItem->name) }}"
                               data-default-hidden="{{ $i >= 5 && !in_array($brandItem->name, $selectedBrands) ? 'true' : 'false' }}">
                            <span class="flex min-w-0 items-center gap-2">
                                <input type="checkbox"
                                       name="brand[]"
                                       value="{{ $brandItem->name }}"
                                       class="h-4 w-4 flex-shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                       {{ in_array($brandItem->name, $selectedBrands) ? 'checked' : '' }}>
                                <span class="truncate">{{ $brandItem->name }}</span>
                            </span>
                            <span class="text-xs text-slate-400">{{ $brandItem->count }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">No brands found.</p>
                    @endforelse
                </div>

                @if($brands->count() > 5)
                    <button type="button" onclick="toggleSeeMore('brand-filter-item', 'brand-more-btn')"
                            id="brand-more-btn"
                            class="mt-2 flex w-full items-center justify-center gap-1 rounded-lg py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 transition-colors">
                        <span class="btn-label">See {{ $brands->count() - 5 }} more</span>
                        <svg class="btn-icon h-3.5 w-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                @endif
            </form>
        </div>
    </div>
