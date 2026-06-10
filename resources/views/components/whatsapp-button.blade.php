@php
    $enabled = config('services.whatsapp.enabled', true);
    $phone   = config('services.whatsapp.number', '917600264090');
@endphp

@if($enabled)
<div id="wa-widget" style="position:fixed;bottom:24px;right:24px;z-index:99998;font-family:'Plus Jakarta Sans',sans-serif;">

    {{-- ── Chat Panel ── --}}
    <div id="wa-panel"
         style="display:none;position:absolute;bottom:72px;right:0;width:320px;
                background:#fff;border-radius:20px;overflow:hidden;
                box-shadow:0 20px 60px rgba(0,0,0,.18);
                animation:waPanelIn .25s cubic-bezier(.4,0,.2,1);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#075e54,#128c7e);padding:16px 18px;display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="color:#fff;font-size:14px;font-weight:700;margin:0;line-height:1.2;">Medikart Support</p>
                <p style="color:rgba(255,255,255,.8);font-size:12px;margin:3px 0 0;display:flex;align-items:center;gap:5px;">
                    <span style="width:7px;height:7px;background:#4ade80;border-radius:50%;display:inline-block;"></span>
                    Typically replies within minutes
                </p>
            </div>
            <button onclick="toggleWA()" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.7);padding:4px;line-height:1;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Chat bubble --}}
        <div style="background:#e5ddd5;padding:16px 14px;">
            <div style="background:#fff;border-radius:0 12px 12px 12px;padding:12px 14px;max-width:85%;box-shadow:0 1px 2px rgba(0,0,0,.1);">
                <p style="font-size:13px;color:#1a1a1a;line-height:1.5;margin:0;">
                    👋 Hi! Welcome to <strong>Medikart</strong>.<br>
                    How can we help you today?
                </p>
                <p style="font-size:10px;color:#94a3b8;margin:6px 0 0;text-align:right;">{{ now()->format('h:i A') }}</p>
            </div>
        </div>

        {{-- Quick messages --}}
        <div style="background:#f8fafc;padding:12px 14px;border-top:1px solid #f1f5f9;">
            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 8px;">Quick Messages</p>
            <div style="display:flex;flex-direction:column;gap:6px;" id="wa-quick-btns">
                @foreach([
                    'I need help with my order',
                    'Check medicine availability',
                    'Ask about a prescription',
                    'Refund / cancellation help',
                ] as $msg)
                    <button onclick="selectQuick(this)"
                            data-msg="{{ $msg }}"
                            style="text-align:left;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 12px;font-size:12px;font-weight:600;color:#374151;cursor:pointer;transition:all .15s;font-family:inherit;"
                            onmouseover="this.style.borderColor='#25d366';this.style.color='#075e54'"
                            onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
                        {{ $msg }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Input + Send --}}
        <div style="padding:12px 14px;background:#fff;border-top:1px solid #f1f5f9;">
            <div style="display:flex;gap:8px;align-items:flex-end;">
                <textarea id="wa-msg-input"
                          placeholder="Type a message…"
                          rows="2"
                          style="flex:1;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 12px;font-size:13px;font-family:inherit;resize:none;outline:none;transition:border-color .2s;line-height:1.4;"
                          onfocus="this.style.borderColor='#25d366'"
                          onblur="this.style.borderColor='#e2e8f0'"></textarea>
                <button onclick="startChat()"
                        style="background:#25d366;border:none;border-radius:12px;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:background .2s;"
                        onmouseover="this.style.background='#128c7e'"
                        onmouseout="this.style.background='#25d366'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
            <p style="font-size:10px;color:#94a3b8;margin:8px 0 0;text-align:center;">
                Opens WhatsApp · <a href="https://wa.me/{{ $phone }}" target="_blank" style="color:#25d366;text-decoration:none;">+{{ $phone }}</a>
            </p>
        </div>
    </div>

    {{-- ── Floating Button ── --}}
    <button id="wa-fab" class="wa-widget-root"
            onclick="toggleWA()"
            aria-label="Chat on WhatsApp"
            style="width:58px;height:58px;background:#25d366;border:none;border-radius:50%;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;
                   box-shadow:0 4px 20px rgba(37,211,102,.5);
                   transition:transform .2s,box-shadow .2s;
                   position:relative;"
            onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 28px rgba(37,211,102,.65)'"
            onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(37,211,102,.5)'">

        {{-- Pulse rings --}}
        <span style="position:absolute;inset:0;border-radius:50%;background:#25d366;animation:waPulse 2s ease-out infinite;"></span>
        <span style="position:absolute;inset:0;border-radius:50%;background:#25d366;animation:waPulse 2s ease-out infinite .6s;"></span>

        {{-- WhatsApp icon --}}
        <span id="wa-fab-icon" style="position:relative;z-index:1;">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/>
    </svg>
</span>
    </button>
</div>

<style>
.wa-widget-root { bottom: 24px; }
@media (max-width: 639px) {
    .wa-widget-root { bottom: calc(72px + env(safe-area-inset-bottom, 0px)); }
}
@keyframes waPulse {
    0%   { transform: scale(1);   opacity: .6; }
    100% { transform: scale(1.8); opacity: 0; }
}
@keyframes waPanelIn {
    from { opacity: 0; transform: translateY(12px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<script>
(function () {
    const PHONE = '{{ $phone }}';

   window.toggleWA = function () {

    const panel = document.getElementById('wa-panel');
    const icon = document.getElementById('wa-fab-icon');

    const isOpen = panel.style.display !== 'none';

    if (isOpen) {

        panel.style.display = 'none';

        icon.innerHTML = `
        <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L0 24l6.335-1.508A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.213-3.727.977.994-3.634-.234-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>`;
    }
    else {

        panel.style.display = 'block';

        panel.style.animation = 'none';
        panel.offsetHeight;
        panel.style.animation = '';

        icon.innerHTML = `
            <i class="fas fa-times" style="font-size:28px;color:white;"></i>
        `;
    }
};

    window.selectQuick = function (btn) {
        document.getElementById('wa-msg-input').value = btn.dataset.msg;
        // highlight selected
        document.querySelectorAll('#wa-quick-btns button').forEach(b => {
            b.style.borderColor = '#e2e8f0';
            b.style.color = '#374151';
            b.style.background = '#fff';
        });
        btn.style.borderColor = '#25d366';
        btn.style.color = '#075e54';
        btn.style.background = '#f0fdf4';
    };

    window.startChat = function () {
        const msg = document.getElementById('wa-msg-input').value.trim()
                    || 'Hello! I need help with Medikart.';
        const url = 'https://wa.me/' + PHONE + '?text=' + encodeURIComponent(msg);
        window.open(url, '_blank');
    };

    // Allow Enter key to send (Shift+Enter for newline)
    document.getElementById('wa-msg-input')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            window.startChat();
        }
    });
})();
</script>
@endif
