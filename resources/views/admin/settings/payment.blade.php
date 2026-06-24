@extends('admin.layouts.admin')
@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')
@section('page-subtitle', 'Control which payment methods are available to customers at checkout')

@section('content')

<style>
    .ps-wrap {
        max-width: 720px;
    }

    /* ── Form spacing ── */
    .ps-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ── Error banner ── */
    .ps-error-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        border-radius: 12px;
        padding: 12px 16px;
    }
    .ps-error-banner .ps-error-icon {
        flex-shrink: 0;
        font-size: 16px;
        color: #ef4444;
    }
    .ps-error-banner p {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #b91c1c;
    }

    /* ── Card ── */
    .ps-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: box-shadow .2s;
    }
    .ps-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,.09);
    }

    /* ── Card header row ── */
    .ps-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .ps-card-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ps-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .ps-card-icon.green  { background: #dcfce7; }
    .ps-card-icon.blue   { background: #dbeafe; }

    .ps-card-title-group h3 {
        margin: 0 0 2px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }
    .ps-card-title-group p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
    }

    /* ── Info list inside card ── */
    .ps-info-list {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px 16px;
    }
    .ps-info-list p {
        margin: 0 0 4px;
        font-size: 12px;
        color: #475569;
    }
    .ps-info-list p:last-child { margin-bottom: 0; }
    .ps-info-list code {
        background: #e2e8f0;
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 11px;
    }

    /* ── Toggle switch ── */
    .ps-toggle-label {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }
    .ps-toggle-input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }
    .ps-toggle-track {
        width: 44px;
        height: 24px;
        border-radius: 999px;
        background: #cbd5e1;
        position: relative;
        transition: background .25s;
        flex-shrink: 0;
    }
    .ps-toggle-track.on-green  { background: #22c55e; }
    .ps-toggle-track.on-blue   { background: #2563eb; }
    .ps-toggle-thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
        transition: transform .25s;
    }
    .ps-toggle-track .ps-toggle-thumb.active {
        transform: translateX(20px);
    }

    /* ── Warning banner ── */
    .ps-warning {
        display: none;
        align-items: center;
        gap: 12px;
        border: 1px solid #fde68a;
        background: #fffbeb;
        border-radius: 12px;
        padding: 12px 16px;
    }
    .ps-warning.visible { display: flex; }
    .ps-warning-icon {
        flex-shrink: 0;
        font-size: 16px;
        color: #d97706;
    }
    .ps-warning p {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
        color: #92400e;
    }

    /* ── Save row ── */
    .ps-save-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-top: 8px;
    }
    .ps-save-btn {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 11px 32px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(37,99,235,.3);
        transition: background .18s, box-shadow .18s, opacity .18s;
    }
    .ps-save-btn:hover  { background: #1d4ed8; box-shadow: 0 4px 14px rgba(37,99,235,.35); }
    .ps-save-btn:active { box-shadow: 0 1px 4px rgba(37,99,235,.25); }
    .ps-save-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
        box-shadow: none;
    }
    .ps-save-hint {
        margin: 0;
        font-size: 12px;
        color: #94a3b8;
    }
</style>

<div class="ps-wrap">

<form method="POST" action="{{ route('admin.settings.payment.save') }}"
      class="ps-form" id="payment-settings-form">
    @csrf

    {{-- ── Server-side error ── --}}
    @error('payment')
        <div class="ps-error-banner">
            <span class="ps-error-icon"><i class="fa-solid fa-circle-exclamation" style="color: rgb(230, 179, 0);"></i></span>
            <p>{{ $message }}</p>
        </div>
    @enderror

    {{-- ── Cash on Delivery ── --}}
    <div class="ps-card">
        <div class="ps-card-header">
            <div class="ps-card-title-group">
                <div class="ps-card-icon green"><i class="fa-solid fa-sack-dollar" style="color: rgb(0, 209, 5);"></i></div>
                <div>
                    <h3>Cash on Delivery (COD)</h3>
                    <p>Customer pays in cash when the order is delivered</p>
                </div>
            </div>
            <label class="ps-toggle-label" aria-label="Enable Cash on Delivery">
                <input type="checkbox" name="payment_cod_enabled" value="1"
                       id="cod-toggle" class="ps-toggle-input"
                       {{ $settings['payment_cod_enabled'] ? 'checked' : '' }}>
                <div class="ps-toggle-track {{ $settings['payment_cod_enabled'] ? 'on-green' : '' }}" id="cod-track">
                    <div class="ps-toggle-thumb {{ $settings['payment_cod_enabled'] ? 'active' : '' }}" id="cod-thumb"></div>
                </div>
            </label>
        </div>
        <div class="ps-info-list">
            <p>• No payment gateway required — zero transaction fees</p>
            <p>• Suitable for customers who prefer to pay in cash</p>
            <p>• Order is placed immediately; payment collected on delivery</p>
        </div>
    </div>

    {{-- ── Online Payment ── --}}
    <div class="ps-card">
        <div class="ps-card-header">
            <div class="ps-card-title-group">
                <div class="ps-card-icon blue"><i class="fa-solid fa-credit-card" style="color: rgb(0, 31, 209);"></i></div>
                <div>
                    <h3>Online Payment (Razorpay)</h3>
                    <p>UPI, cards, net banking, wallets via Razorpay gateway</p>
                </div>
            </div>
            <label class="ps-toggle-label" aria-label="Enable Online Payment">
                <input type="checkbox" name="payment_online_enabled" value="1"
                       id="online-toggle" class="ps-toggle-input"
                       {{ $settings['payment_online_enabled'] ? 'checked' : '' }}>
                <div class="ps-toggle-track {{ $settings['payment_online_enabled'] ? 'on-blue' : '' }}" id="online-track">
                    <div class="ps-toggle-thumb {{ $settings['payment_online_enabled'] ? 'active' : '' }}" id="online-thumb"></div>
                </div>
            </label>
        </div>
        <div class="ps-info-list">
            <p>• Payment collected upfront before order is confirmed</p>
            <p>• Supports UPI, credit/debit cards, net banking, and wallets</p>
            <p>• Requires valid Razorpay API keys configured in <code>.env</code></p>
        </div>
    </div>

    {{-- ── Warning: both disabled ── --}}
    <div class="ps-warning" id="last-enabled-warning">
        <span class="ps-warning-icon"><i class="fa-solid fa-circle-exclamation" style="color: rgb(230, 179, 0);"></i></span>
        <p>At least one payment method must remain enabled. You cannot disable both.</p>
    </div>

    {{-- ── Save ── --}}
    <div class="ps-save-row">
        <button type="submit" id="save-btn" class="ps-save-btn">✓ Save Settings</button>
        <p class="ps-save-hint">Changes take effect immediately for new checkout sessions.</p>
    </div>

</form>

</div>

<script>
(function () {
    var cod     = document.getElementById('cod-toggle');
    var online  = document.getElementById('online-toggle');
    var warning = document.getElementById('last-enabled-warning');
    var saveBtn = document.getElementById('save-btn');

    var config = {
        cod:    { track: document.getElementById('cod-track'),    thumb: document.getElementById('cod-thumb'),    activeClass: 'on-green' },
        online: { track: document.getElementById('online-track'), thumb: document.getElementById('online-thumb'), activeClass: 'on-blue'  },
    };

    function syncUI(id, checked) {
        var t = config[id];
        if (checked) {
            t.track.classList.add(t.activeClass);
            t.thumb.classList.add('active');
        } else {
            t.track.classList.remove(t.activeClass);
            t.thumb.classList.remove('active');
        }
    }

    function validate() {
        var bothOff = !cod.checked && !online.checked;
        if (bothOff) {
            warning.classList.add('visible');
        } else {
            warning.classList.remove('visible');
        }
        saveBtn.disabled = bothOff;
    }

    cod.addEventListener('change', function () { syncUI('cod', this.checked); validate(); });
    online.addEventListener('change', function () { syncUI('online', this.checked); validate(); });

    // Sync initial state (handles page-reload with server-side values)
    syncUI('cod', cod.checked);
    syncUI('online', online.checked);
    validate();
})();
</script>

@endsection
