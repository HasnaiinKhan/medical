@extends('admin.layouts.admin')
@section('title', 'Order Settings')
@section('page-title', 'Order Settings')
@section('page-subtitle', 'Configure refund window and order policies')

@section('content')

<div class="max-w-3xl">

<form method="POST" action="{{ route('admin.settings.orders.save') }}" class="space-y-6">
    @csrf

    {{-- ── Refund Window ── --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-xl">↩️</div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Refund Window</h3>
                <p class="text-xs text-slate-500 mt-0.5">Number of days after order placement that customers can request a refund</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                    Refund Window (Days)
                </label>
                <div class="flex items-center gap-3">
                    <input type="number"
                           name="refund_window_days"
                           value="{{ old('refund_window_days', $settings['refund_window_days']) }}"
                           min="1" max="365" required
                           class="w-32 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-mono font-bold text-slate-900 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all text-center">
                    <span class="text-sm text-slate-500 font-medium">days from order date</span>
                </div>
                <p class="mt-1.5 text-xs text-slate-500">
                    Customers can request a refund within this many days of placing their order.
                    Currently set to <strong>{{ $settings['refund_window_days'] }} days</strong>.
                </p>
                @error('refund_window_days')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Visual presets --}}
            <div>
                <p class="text-xs text-slate-500 mb-2">Quick presets:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([7, 14, 30, 45, 60, 90] as $preset)
                        <button type="button"
                                onclick="document.querySelector('[name=refund_window_days]').value = {{ $preset }}"
                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            {{ $preset }} days
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Info box --}}
            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 flex items-start gap-3">
                <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">ℹ️</span>
                <div class="text-xs text-blue-800 space-y-1">
                    <p>This setting affects all future refund eligibility checks. Existing orders already past the window will not be affected retroactively.</p>
                    <p>The refund button on the order detail page and chatbot also use this value.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white hover:bg-blue-700 transition-all shadow-md hover:shadow-lg active:shadow-md">
            ✓ Save Settings
        </button>
        <p class="text-xs text-slate-500">Changes take effect immediately.</p>
    </div>

</form>

</div>

@endsection
