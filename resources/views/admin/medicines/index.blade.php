@extends('admin.layouts.admin')
@section('title', 'Medicines')
@section('page-title', 'Medicines')
@section('page-subtitle', $medicines->total() . ' medicines in catalogue')

@section('content')

{{-- Toolbar --}}
<<<<<<< HEAD
<div class="mb-5 flex flex-col gap-3">
    <form action="{{ route('admin.medicines.index') }}" method="get" class="flex gap-2 flex-wrap items-center">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <div class="relative flex-1 min-w-[160px]">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" value="{{ $q }}" placeholder="Search medicines…"
                   class="rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full">
        </div>
        <select name="category" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-blue-600 focus:outline-none flex-1 min-w-[140px]">
=======
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form action="{{ route('admin.medicines.index') }}" method="get" class="flex gap-2 flex-1 flex-wrap sm:flex-nowrap items-center">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                 <input type="search" name="q" value="{{ $q }}" placeholder="Search medicines…"
                     class="rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 w-full sm:w-64">
        </div>
        <select name="category" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-blue-600 focus:outline-none w-full sm:w-auto">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </form>

<<<<<<< HEAD
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.medicines.import.form') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-600 text-white hover:bg-blue-700 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import
=======
    <div class="flex gap-2">
        <a href="{{ route('admin.medicines.import.form') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-600 text-white hover:bg-blue-700 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import CSV
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
        </a>
        <a href="{{ route('admin.medicines.export') }}"
           class="btn-sm inline-flex items-center gap-1.5 bg-blue-700 text-white hover:bg-blue-800 rounded-xl">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
<<<<<<< HEAD
            Export
=======
            Export CSV
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
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
                    <th class="text-left w-12">#</th>
                    <th class="text-left">Medicine</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">MRP</th>
                    <th class="text-left">Price</th>
                    <th class="text-left">Stock</th>
                    <th class="text-left">Rx</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $m)
                    <tr>
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
                        <td>
                            <span class="badge bg-blue-50 text-blue-800 ring-1 ring-blue-200">{{ $m->category->name }}</span>
                        </td>
                        <td class="text-slate-500 text-xs line-through">₹{{ number_format($m->mrpRupees(), 2) }}</td>
                        <td class="font-bold text-slate-900">₹{{ number_format($m->priceRupees(), 2) }}</td>
                        <td>
                            <span class="font-semibold {{ $m->stock < 20 ? 'text-red-600' : 'text-slate-700' }}">
                                {{ $m->stock }}
                            </span>
                        </td>
                        <td>
                            @if($m->prescription_required)
                                <span class="badge bg-amber-100 text-amber-800">Rx</span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap sm:flex-nowrap">
                                <a href="{{ route('admin.medicines.edit', $m) }}"
<<<<<<< HEAD
                                   class="btn-sm bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-800 w-full sm:w-auto" style="text-align:center;">
=======
                                   class="btn-sm bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-800 w-full sm:w-auto">
>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
                                    Edit
                                </a>
                                <form method="post" action="{{ route('admin.medicines.destroy', $m) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($m->name) }}?')" class="w-full sm:w-auto">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-sm bg-red-50 text-red-600 hover:bg-red-100 w-full">
                                        Delete
                                    </button>
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

@endsection
