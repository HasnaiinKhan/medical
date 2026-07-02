@if ($paginator->hasPages())
@php
    $currentPage = $paginator->currentPage();
    $lastPage    = $paginator->lastPage();

    // Build the page window: always show first 3, last 1, and ±1 around current
    $pages = collect();

    // First 3 pages
    for ($i = 1; $i <= min(3, $lastPage); $i++) {
        $pages->push($i);
    }

    // Pages around current
    for ($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++) {
        $pages->push($i);
    }

    // Last page
    $pages->push($lastPage);

    $pages = $pages->unique()->sort()->values();
@endphp

<nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1 flex-wrap py-2">

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed select-none text-sm">
            ‹
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           rel="prev"
           class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-400 transition-colors text-sm font-medium">
            ‹
        </a>
    @endif

    {{-- Page numbers with ellipsis --}}
    @php $prevPage = null; @endphp
    @foreach ($pages as $page)
        @if ($prevPage !== null && $page - $prevPage > 1)
            <span class="inline-flex h-8 items-center px-1 text-slate-400 text-sm select-none">…</span>
        @endif

        @if ($page == $currentPage)
            <span aria-current="page"
                  class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg border border-blue-600 bg-blue-600 px-2 text-sm font-bold text-white shadow-sm">
                {{ $page }}
            </span>
        @else
            <a href="{{ $paginator->url($page) }}"
               class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-blue-400 transition-colors">
                {{ $page }}
            </a>
        @endif

        @php $prevPage = $page; @endphp
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           rel="next"
           class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-blue-400 transition-colors text-sm font-medium">
            ›
        </a>
    @else
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-300 cursor-not-allowed select-none text-sm">
            ›
        </span>
    @endif

</nav>
@endif
