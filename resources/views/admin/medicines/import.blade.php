@extends('admin.layouts.admin')
@section('title', 'Import Medicines')
@section('page-title', 'Import Medicines')
@section('page-subtitle', 'Bulk upload medicines via CSV file')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Upload form --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Drop zone --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
             x-data="{ dragging: false, fileName: '' }">
            <h3 class="text-sm font-bold text-slate-900 mb-5">Upload CSV File</h3>

            <form method="post" action="{{ route('admin.medicines.import') }}" enctype="multipart/form-data">
                @csrf

                <div @dragover.prevent="dragging=true"
                     @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; fileName=$event.dataTransfer.files[0]?.name; $refs.fileInput.files=$event.dataTransfer.files"
                     :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-slate-200 bg-slate-50'"
                     class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-10 text-center transition-all cursor-pointer"
                     onclick="document.getElementById('csv_file').click()">

                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 text-2xl">
                        📄
                    </div>
                    <p class="text-sm font-bold text-slate-800" x-text="fileName || 'Drop your CSV here or click to browse'"></p>
                    <p class="mt-1 text-xs text-slate-500">Supports .csv files up to 2MB</p>

                    <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt"
                           x-ref="fileInput"
                           @change="fileName = $event.target.files[0]?.name"
                           class="absolute inset-0 opacity-0 cursor-pointer">
                </div>

                @error('csv_file')
                    <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror

                <div class="mt-5 flex gap-3">
                    <button type="submit"
                            class="flex-1 rounded-xl bg-blue-700 py-3 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Medicines
                    </button>
                    <a href="{{ route('admin.medicines.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- CSV format guide --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Required CSV Format</h3>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            @foreach(['name','manufacturer','category','mrp','price','prescription_required','stock','description','image_url'] as $col)
                                <th class="px-3 py-2 text-left font-bold text-slate-600 border-b border-slate-200 whitespace-nowrap">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white">
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">Dolo 650 Tablet</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">Micro Labs</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">Fever &amp; Pain</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">45.00</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">38.00</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">false</td>
                            <td class="px-3 py-2 text-slate-700 border-b border-slate-100">200</td>
                            <td class="px-3 py-2 text-slate-500 border-b border-slate-100 italic">Optional text…</td>
                            <td class="px-3 py-2 text-slate-500 border-b border-slate-100 italic">Optional URL…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-slate-600">
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="font-bold text-slate-800 mb-1">Required columns</p>
                    <p>name, manufacturer, category, mrp, price</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="font-bold text-slate-800 mb-1">Optional columns</p>
                    <p>prescription_required, stock, description, image_url</p>
                </div>
                <div class="rounded-lg bg-amber-50 p-3 col-span-2">
                    <p class="font-bold text-amber-800 mb-1">⚠ Notes</p>
                    <p class="text-amber-700">• mrp and price must be in ₹ (e.g. 45.00) — not paise<br>
                    • prescription_required: use <code class="bg-amber-100 px-1 rounded">true</code> or <code class="bg-amber-100 px-1 rounded">false</code><br>
                    • Existing medicines (matched by name slug) will be updated, not duplicated</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar actions --}}
    <div class="space-y-4">
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white text-lg">↓</div>
                <div>
                    <p class="text-sm font-bold text-blue-900">Download Template</p>
                    <p class="text-xs text-blue-800">Pre-formatted CSV with example row</p>
                </div>
            </div>
            <a href="{{ route('admin.medicines.template') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Template
            </a>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white text-lg">↓</div>
                <div>
                    <p class="text-sm font-bold text-blue-900">Export Current Data</p>
                    <p class="text-xs text-blue-700">Download all medicines as CSV</p>
                </div>
            </div>
            <a href="{{ route('admin.medicines.export') }}"
               class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-2.5 text-sm font-bold text-white hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export All Medicines
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-700 mb-3">Import Tips</p>
            <ul class="space-y-2 text-xs text-slate-500">
                <li class="flex items-start gap-2"><span class="text-blue-600 mt-0.5">✓</span>Save your spreadsheet as CSV (UTF-8)</li>
                <li class="flex items-start gap-2"><span class="text-blue-600 mt-0.5">✓</span>First row must be the header row</li>
                <li class="flex items-start gap-2"><span class="text-blue-600 mt-0.5">✓</span>New categories are created automatically</li>
                <li class="flex items-start gap-2"><span class="text-blue-600 mt-0.5">✓</span>Duplicate names are updated, not duplicated</li>
                <li class="flex items-start gap-2"><span class="text-amber-500 mt-0.5">!</span>Max file size: 2MB (~5,000 rows)</li>
            </ul>
        </div>
    </div>
</div>
@endsection
