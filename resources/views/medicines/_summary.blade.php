<h1 class="text-2xl font-bold text-slate-900">All Medicines</h1>
<p class="text-sm text-slate-500 mt-0.5">
    @if($q)
        Showing results for "<strong class="text-slate-700">{{ $q }}</strong>"
    @elseif(request('category'))
        Browsing <strong class="text-slate-700">{{ $categories->firstWhere('slug', request('category'))?->name ?? 'Category' }}</strong>
    @else
        Browse our complete catalogue of {{ $medicines->total() }} medicines
    @endif
</p>
