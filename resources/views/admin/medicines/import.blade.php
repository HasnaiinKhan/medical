@extends('admin.layouts.admin')
@section('title', 'Import Medicines')
@section('page-title', 'Import Medicines')
@section('page-subtitle', 'Bulk upload medicines via CSV file')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">

    {{-- LEFT: Upload form + CSV guide --}}
    <div class="lg:col-span-2 space-y-6 min-w-0">

        {{-- Upload card --}}
        <div
            class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm ring-1 ring-black/5"
            x-data="{ dragging: false, fileName: '' }"
        >
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Upload CSV File</h3>
                    <p class="mt-1 text-xs text-slate-500">Upload a CSV to add or update medicines in bulk.</p>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-[11px] text-slate-500">
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-1 border border-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Live validation
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 py-1 border border-slate-200">
                        CSV (UTF-8)
                    </span>
                </div>
            </div>

            <form method="post" action="{{ route('admin.medicines.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <label
                    for="csv_file"
                    @dragover.prevent="dragging=true"
                    @dragleave.prevent="dragging=false"
                    @drop.prevent="
                        dragging=false;
                        fileName=$event.dataTransfer.files[0]?.name;
                        $refs.fileInput.files=$event.dataTransfer.files
                    "
                    :class="dragging ? 'border-blue-400 bg-blue-50/70 ring-2 ring-blue-200' : 'border-slate-200 bg-slate-50 hover:bg-slate-100/70'"
                    class="relative block rounded-2xl border-2 border-dashed p-6 sm:p-10 text-center transition-all cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 text-2xl shadow-inner">
                        📄
                    </div>
                    <p class="text-sm sm:text-base font-semibold text-slate-800" x-text="fileName || 'Tap to browse or drop CSV here'"></p>
                    <p class="mt-1 text-xs text-slate-500">Supports .csv files up to 2MB</p>

                    <input
                        id="csv_file"
                        name="csv_file"
                        type="file"
                        accept=".csv,.txt"
                        x-ref="fileInput"
                        @change="fileName = $event.target.files[0]?.name"
                        class="sr-only"
                    >
                </label>

                @error('csv_file')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror

                <div class="flex flex-col sm:flex-row gap-3 pt-1">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 flex-1"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Medicines
                    </button>

                    <a
                        href="{{ route('admin.medicines.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors shadow-sm"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- CSV format guide --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm ring-1 ring-black/5 min-w-0 overflow-hidden">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Required CSV Format</h3>
                <a
                    href="{{ route('admin.medicines.template') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors shadow-sm"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            {{-- Desktop: horizontally scrollable table --}}
            <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-200">
                <table class="text-xs whitespace-nowrap w-full">
                    <thead class="bg-slate-50/70 backdrop-blur-sm">
                        <tr>
                            @foreach(['name','manufacturer','category','mrp','price','prescription_required','stock','description','image_url'] as $col)
                                <th class="px-3 py-2 text-left font-bold text-slate-600 border-b border-slate-200 sticky top-0">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2 text-slate-800">Dolo 650 Tablet</td>
                            <td class="px-3 py-2 text-slate-800">Micro Labs</td>
                            <td class="px-3 py-2 text-slate-800">Fever &amp; Pain</td>
                            <td class="px-3 py-2 text-slate-800">45.00</td>
                            <td class="px-3 py-2 text-slate-800">38.00</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">false</span>
                            </td>
                            <td class="px-3 py-2 text-slate-800">200</td>
                            <td class="px-3 py-2 text-slate-500 italic">Optional…</td>
                            <td class="px-3 py-2 text-slate-500 italic">Optional URL…</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Mobile: stacked column cards --}}
            <div class="sm:hidden space-y-2">
                @foreach([
                    ['col'=>'name',                  'val'=>'Dolo 650 Tablet',  'req'=>true],
                    ['col'=>'manufacturer',          'val'=>'Micro Labs',       'req'=>true],
                    ['col'=>'category',              'val'=>'Fever & Pain',     'req'=>true],
                    ['col'=>'mrp',                   'val'=>'45.00',            'req'=>true],
                    ['col'=>'price',                 'val'=>'38.00',            'req'=>true],
                    ['col'=>'prescription_required', 'val'=>'true / false',     'req'=>false],
                    ['col'=>'stock',                 'val'=>'200',              'req'=>false],
                    ['col'=>'description',           'val'=>'Optional text…',   'req'=>false],
                    ['col'=>'image_url',             'val'=>'Optional URL…',    'req'=>false],
                ] as $row)
                <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 shadow-sm">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-slate-800">{{ $row['col'] }}</span>
                        @if($row['req'])
                            <span class="ml-1 text-[9px] font-bold text-red-600 uppercase tracking-wide">required</span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-600 italic truncate max-w-[55%] text-right">{{ $row['val'] }}</span>
                </div>
                @endforeach
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-700">
                <div class="rounded-xl bg-slate-50 p-3 border border-slate-200">
                    <p class="font-bold text-slate-900 mb-1">Required columns</p>
                    <p>name, manufacturer, category, mrp, price</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3 border border-slate-200">
                    <p class="font-bold text-slate-900 mb-1">Optional columns</p>
                    <p>prescription_required, stock, description, image_url</p>
                </div>
                <div class="rounded-xl bg-amber-50 p-3 border border-amber-200 sm:col-span-1 sm:col-start-3">
                    <p class="font-bold text-amber-900 mb-1">Notes</p>
                    <ul class="text-amber-800 space-y-1">
                        <li>• mrp and price must be in ₹ (e.g. 45.00) — not paise</li>
                        <li>• prescription_required: use <code class="bg-amber-100 px-1 rounded">true</code> or <code class="bg-amber-100 px-1 rounded">false</code></li>
                        <li>• Existing medicines (matched by name slug) will be updated, not duplicated</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Sidebar actions --}}
    <div class="flex flex-col gap-4">

        {{-- Download template --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 px-4 py-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 leading-tight">Download Template</p>
                    <p class="text-xs text-slate-500 mt-0.5">Pre-formatted CSV with example row</p>
                </div>
                <a href="{{ route('admin.medicines.template') }}"
                   class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>
        </div>

        {{-- Export current data --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 px-4 py-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 leading-tight">Export Current Data</p>
                    <p class="text-xs text-slate-500 mt-0.5">Download all medicines as CSV</p>
                </div>
                <a href="{{ route('admin.medicines.export') }}"
                  class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700 transition-colors shadow-sm " style="background-color:gray;">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>
            </div>
        </div>

        {{-- Tips --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-bold text-slate-900 mb-3">Import Tips</p>
            <ul class="space-y-2.5 text-xs text-slate-600">
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold mt-0.5 flex-shrink-0">✓</span>
                    Save your spreadsheet as CSV (UTF-8)
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold mt-0.5 flex-shrink-0">✓</span>
                    First row must be the header row
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold mt-0.5 flex-shrink-0">✓</span>
                    New categories are created automatically
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 font-bold mt-0.5 flex-shrink-0">✓</span>
                    Duplicate names are updated, not duplicated
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-amber-500 font-bold mt-0.5 flex-shrink-0">!</span>
                    Max file size: 2MB (~5,000 rows)
                </li>
            </ul>
        </div>

    </div>

</div>
@endsection
