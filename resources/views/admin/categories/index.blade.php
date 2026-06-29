d@extends('admin.layouts.admin')
@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', $categories->count() . ' categories')

@section('content')

<div class="grid gap-6 grid-cols-1 lg:grid-cols-5">

    {{-- ===== CREATE FORM ===== --}}
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sticky top-24 lg:top-24" style="z-index:1;">
            <h2 class="text-sm font-bold text-slate-900 mb-5 flex items-center gap-2">
                <svg class="h-4 w-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Category
            </h2>

            <form method="post" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Category Name *
                    </label>
                    <input name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Vitamins & Supplements"
                           autofocus
                           class="w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">Slug is generated automatically from the name.</p>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-blue-700 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-md">
                    Create Category
                </button>
            </form>
        </div>
    </div>

    {{-- ===== CATEGORIES LIST ===== --}}
    <div class="lg:col-span-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                <h2 class="text-sm font-bold text-slate-900">All Categories</h2>
            </div>

            @if($categories->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <span class="text-4xl mb-3">🗂️</span>
                    <p class="text-sm font-semibold text-slate-600">No categories yet</p>
                    <p class="text-xs text-slate-400 mt-1">Create your first category using the form.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($categories as $cat)
                        <div class="flex items-center justify-between px-4 sm:px-5 py-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700 font-black text-sm">
                                    {{ strtoupper(substr($cat->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $cat->name }}</p>
                                    <p class="text-xs text-slate-500">
                                        <code class="bg-slate-100 px-1 rounded text-slate-600 text-[10px]">{{ $cat->slug }}</code>
                                        &nbsp;·&nbsp;
                                        <span class="{{ $cat->medicines_count > 0 ? 'text-blue-700 font-semibold' : 'text-slate-400' }}">
                                            {{ $cat->medicines_count }} med.
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                <a href="{{ route('admin.medicines.index', ['category' => $cat->slug]) }}"
                                   class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-blue-300 hover:text-blue-800 transition-colors">
                                    View Medicines
                                </a>

                                @if($cat->medicines_count === 0)
                                    <form method="post" action="{{ route('admin.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-400 cursor-not-allowed"
                                          title="Cannot delete - has medicines">
                                        Delete
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection