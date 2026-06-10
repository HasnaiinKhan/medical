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

<style>
    /* Filter section pure CSS toggles */
    .filter-more-toggle {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        border: 0;
        padding: 0;
        clip: rect(0 0 0 0);
        overflow: hidden;
        white-space: nowrap;
    }

    #cat-more-toggle:not(:checked) ~ #cat-list .cat-item.hidden,
    #brand-more-toggle:not(:checked) ~ #brand-filter-list .brand-filter-item.hidden {
        display: none;
    }

    #cat-more-toggle:checked ~ .see-more-btn .btn-label::before,
    #brand-more-toggle:checked ~ .see-more-btn .btn-label::before {
        content: attr(data-expanded);
    }

    #cat-more-toggle:not(:checked) ~ .see-more-btn .btn-label::before,
    #brand-more-toggle:not(:checked) ~ .see-more-btn .btn-label::before {
        content: attr(data-collapsed);
    }

    #cat-more-toggle:checked ~ .see-more-btn .btn-icon,
    #brand-more-toggle:checked ~ .see-more-btn .btn-icon {
        transform: rotate(180deg);
    }

    .see-more-btn {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        border-radius: 0.75rem;
        padding: 0.375rem 0;
        font-size: 0.75rem;
        font-weight: 700;
        color: #2563eb;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
        text-align: center;
    }

    .see-more-btn:hover {
        background: #eff6ff;
    }

    .see-more-btn .btn-icon {
        transition: transform 0.2s ease;
    }

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
        scrollbar-color: #2563eb #f1f5f9;
        scrollbar-width: thin;
    }

    .see-more-btn{
    width:100%;
    margin-top:10px;

    display:flex;
    justify-content:center;
    align-items:center;
    gap:6px;

    padding:8px;

    border:none;
    background:transparent;

    color:#2563eb;
    font-size:13px;
    font-weight:600;

    cursor:pointer;

    transition:.3s;
}

.see-more-btn:hover{
    background:#eff6ff;
    border-radius:8px;
}

.btn-icon{
    width:15px;
    height:15px;

    transition:transform .3s ease;
}

.btn-icon.rotate-180{
    transform:rotate(180deg);
}   


.filter-list{
    max-height:250px;
    overflow:hidden;
    transition:max-height .3s ease;
}

.filter-list.scroll-enabled{
    overflow-y:auto;
    overflow-x:hidden;

    overscroll-behavior: contain;

    scroll-behavior:smooth;

    scrollbar-width: thin;
}

/* Beautiful scrollbar */

.filter-list::-webkit-scrollbar{
    width:8px;
}

.filter-list::-webkit-scrollbar-track{
    background:#f1f5f9;
    border-radius:10px;
}

.filter-list::-webkit-scrollbar-thumb{
    background:linear-gradient(
        180deg,
        #1e3a8a,
        #2563eb,
        #3b82f6
    );

    border-radius:10px;
}

.filter-list::-webkit-scrollbar-thumb:hover{
    background:linear-gradient(
        180deg,
        #1e40af,
        #1d4ed8,
        #2563eb
    );
}
</style>

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
            <input type="checkbox" id="cat-more-toggle" class="filter-more-toggle">
            <ul class="space-y-0.5 pr-2 filter-list" id="cat-list">
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
<button
type="button"
onclick="toggleSeeMore('cat-item', this)"
id="cat-more-btn"
class="see-more-btn"
data-count="{{ $categories->count()-5 }}">

    <span class="btn-label">
        See {{ $categories->count() - 5 }} More
    </span>

    <svg class="btn-icon"
         fill="none"
         stroke="currentColor"
         viewBox="0 0 24 24">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 9l-7 7-7-7"/>
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

                <input type="checkbox" id="brand-more-toggle" class="filter-more-toggle">
                <div id="brand-filter-list" class="space-y-1.5 pr-2 filter-list">
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
<button
type="button"
onclick="toggleSeeMore('brand-filter-item', this)"
id="brand-more-btn"
class="see-more-btn"
data-count="{{ $brands->count()-5 }}">

    <span class="btn-label">
        See {{ $brands->count() - 5 }} More
    </span>

    <svg class="btn-icon"
         fill="none"
         stroke="currentColor"
         viewBox="0 0 24 24">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 9l-7 7-7-7"/>
    </svg>

</button>
@endif
            </form>
        </div>
    </div>
