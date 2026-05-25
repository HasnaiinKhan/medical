@extends('admin.layouts.admin')
@section('title', 'Notification Settings')
@section('page-title', 'Notification Settings')
@section('page-subtitle', 'Configure how you receive new order alerts')

@section('content')

<div class="max-w-3xl">

<form method="POST" action="{{ route('admin.settings.notifications.save') }}" class="space-y-6">
    @csrf

    {{-- ── Email Notifications ── --}}
    <div class="p-4 mb-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow" style="width:100%;">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-xl">📧</div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Email Notifications</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Receive new order alerts via Gmail</p>
                </div>
            </div>
            {{-- Toggle --}}
          <label class="relative inline-flex cursor-pointer items-center">
    <input type="checkbox" name="admin_email_notifications" value="1"
           {{ $settings['admin_email_notifications'] ? 'checked' : '' }}
           class="sr-only toggle-checkbox">
    <div class="toggle-track w-11 h-6 rounded-full transition-colors duration-300 relative bg-slate-200">
        <div class="toggle-thumb absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 shadow transition-transform duration-300"></div>
    </div>
</label>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                    Admin Email Address
                </label>
                <input type="email" name="admin_email"
                       value="{{ $settings['admin_email'] }}"
                       placeholder="admin@medikart.in"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all">
                <p class="mt-1.5 text-xs text-slate-500">New order emails will be sent to this address.</p>
                @error('admin_email')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── WhatsApp Notifications ── --}}
    <div class="p-4 mb-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#25d366">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">WhatsApp Notifications</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Receive new order alerts via WhatsApp Cloud API</p>
                </div>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
    <input type="checkbox" name="admin_whatsapp_notifications" value="1"
           {{ $settings['admin_whatsapp_notifications'] ? 'checked' : '' }}
           class="sr-only toggle-checkbox">
    <div class="toggle-track w-11 h-6 rounded-full transition-colors duration-300 relative bg-blue-600">
        <div class="toggle-thumb absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 shadow transition-transform duration-300"></div>
    </div>
</label>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                    Admin WhatsApp Number <span class="text-slate-500 font-normal normal-case">(with country code)</span>
                </label>
                <input type="text" name="admin_whatsapp_number"
                       value="{{ $settings['admin_whatsapp_number'] }}"
                       placeholder="919876543210"
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 transition-all">
                <p class="mt-1.5 text-xs text-slate-500">e.g. 919876543210 (91 = India country code)</p>
                @error('admin_whatsapp_number')<p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3">
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wide">⚙️ WhatsApp Cloud API Credentials</p>
                <p class="text-xs text-amber-800">Get these from <a href="https://developers.facebook.com/apps" target="_blank" class="underline font-semibold hover:text-amber-900">Meta for Developers</a> → Your App → WhatsApp → API Setup</p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">API Token <span class="text-slate-500 font-normal">(Permanent Access Token)</span></label>
                    <input type="password" name="whatsapp_api_token"
                           value="{{ $settings['whatsapp_api_token'] }}"
                           placeholder="EAAxxxxxxxxxxxxxxx"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Phone Number ID</label>
                    <input type="text" name="whatsapp_phone_number_id"
                           value="{{ $settings['whatsapp_phone_number_id'] }}"
                           placeholder="1234567890123456"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 bg-white transition-all">
                    <p class="mt-1.5 text-xs text-slate-500">Found in Meta Developer Console → WhatsApp → API Setup → Phone Number ID</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Save --}}
    <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="p-4 rounded-xl bg-blue-600 px-8 py-3 text-sm font-bold text-white hover:bg-blue-700 transition-all shadow-md hover:shadow-lg active:shadow-md">
            ✓ Save Settings
        </button>
        <p class="text-xs text-slate-500">Changes take effect immediately for new orders.</p>
    </div>

</form>

{{-- Test section --}}
<div class="mt-8 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <h3 class="text-sm font-bold text-slate-800 mb-2">🧪 Test Notifications</h3>
    <p class="text-xs text-slate-600 mb-4">Send a test notification to verify your settings are working correctly.</p>
    <div class="flex gap-3">
        <a href="{{ route('admin.settings.notifications.test', 'email') }}"
           class="rounded-xl bg-blue-50 border border-blue-200 px-5 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm">
            📧 Test Email
        </a>
        <a href="{{ route('admin.settings.notifications.test', 'whatsapp') }}"
            class="rounded-xl bg-blue-50 border border-blue-200 px-5 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm">
            💬 Test WhatsApp
        </a>
    </div>
</div>

</div>

<script>
document.querySelectorAll('.toggle-checkbox').forEach(function(checkbox) {
    const track = checkbox.nextElementSibling;
    const thumb = track.querySelector('.toggle-thumb');
    const isEmail = checkbox.name === 'admin_email_notifications';
    function updateToggle() {
        if (checkbox.checked) {
            track.classList.remove('bg-slate-200');
            track.classList.add(isEmail ? 'bg-blue-600' : 'bg-green-500');
            thumb.style.transform = 'translateX(20px)';
        } else {
            track.classList.add('bg-slate-200');
            track.classList.remove(isEmail ? 'bg-blue-600' : 'bg-green-500');
            thumb.style.transform = 'translateX(0px)';
        }
    }
    updateToggle(); // set correct state on page load
    checkbox.addEventListener('change', updateToggle);
});
</script>

@endsection
