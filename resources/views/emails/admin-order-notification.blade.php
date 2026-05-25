<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>New Order — Admin</title>
<style>
  body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b}
  .wrap{max-width:620px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,.10)}
  .header{background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:28px 36px;display:flex;align-items:center;gap:16px}
  .header-icon{width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
  .header-text h1{margin:0;color:#fff;font-size:18px;font-weight:800}
  .header-text p{margin:4px 0 0;color:rgba(255,255,255,.75);font-size:13px}
  .body{padding:28px 36px}
  .alert{background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px}
  .alert-dot{width:10px;height:10px;background:#2563eb;border-radius:50%;flex-shrink:0;animation:pulse 1.5s ease-in-out infinite}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
  .section{margin-bottom:20px}
  .section h3{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin:0 0 10px}
  .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  .info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px}
  .info-box .label{font-size:11px;color:#94a3b8;margin:0 0 3px}
  .info-box .val{font-size:13px;font-weight:700;color:#1e293b;margin:0}
  .items-table{width:100%;border-collapse:collapse}
  .items-table th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;padding:8px 12px;background:#f8fafc;border-bottom:2px solid #e2e8f0;text-align:left}
  .items-table td{padding:10px 12px;font-size:13px;border-bottom:1px solid #f1f5f9}
  .items-table tr:last-child td{border-bottom:none}
  .total-row{display:flex;justify-content:space-between;align-items:center;padding:14px 0 0;border-top:2px solid #1e3a8a;margin-top:8px}
  .total-row .label{font-size:14px;font-weight:700;color:#1e293b}
  .total-row .amount{font-size:20px;font-weight:900;color:#1e3a8a}
  .payment-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700}
  .cta{text-align:center;margin:28px 0 8px}
  .cta a{display:inline-block;background:linear-gradient(135deg,#1e40af,#2563eb);color:#fff;text-decoration:none;padding:13px 32px;border-radius:12px;font-size:14px;font-weight:700;box-shadow:0 4px 14px rgba(37,99,235,.4)}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:16px 36px;text-align:center;font-size:12px;color:#94a3b8}
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div class="header-icon">🛒</div>
    <div class="header-text">
      <h1>New Order Received!</h1>
      <p>{{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="body">

    <div class="alert">
      <div class="alert-dot"></div>
      <p style="margin:0;font-size:13px;font-weight:600;color:#1e40af;">
        Order <strong>#{{ $order->order_number }}</strong> needs your attention.
        @if($order->payment_method === 'cod') Customer will pay cash on delivery. @else Payment received online. @endif
      </p>
    </div>

    {{-- Customer info --}}
    <div class="section">
      <h3>Customer Details</h3>
      <div class="info-grid">
        <div class="info-box"><p class="label">Name</p><p class="val">{{ $order->customer_name }}</p></div>
        <div class="info-box"><p class="label">Phone</p><p class="val">+91 {{ $order->customer_phone }}</p></div>
        <div class="info-box" style="grid-column:1/-1"><p class="label">Delivery Address</p><p class="val">{{ $order->address_line1 }}@if($order->address_line2), {{ $order->address_line2 }}@endif, {{ $order->delivery_area }} — {{ $order->delivery_pin }}</p></div>
      </div>
    </div>

    {{-- Items --}}
    <div class="section">
      <h3>Order Items</h3>
      <table class="items-table">
        <thead>
          <tr>
            <th>Medicine</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>{{ $item->medicine_name_snapshot }}</td>
            <td style="text-align:center;font-weight:700">{{ $item->quantity }}</td>
            <td style="text-align:right;font-weight:700">₹{{ number_format($item->line_total_paise/100,2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div style="padding:10px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0 0 10px 10px;display:flex;justify-content:space-between;font-size:13px;color:#64748b">
        <span>Delivery fee</span>
        <span>{{ $order->delivery_fee_paise === 0 ? 'FREE' : '₹'.number_format($order->delivery_fee_paise/100,2) }}</span>
      </div>

      <div class="total-row">
        <span class="label">Total</span>
        <span class="amount">₹{{ number_format($order->totalRupees(),2) }}</span>
      </div>
    </div>

    {{-- Payment --}}
    <div class="section">
      <h3>Payment</h3>
      @if($order->payment_method === 'online')
        <span class="payment-badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe">💳 Online — Razorpay</span>
        @if($order->razorpay_payment_id)
          <p style="margin:8px 0 0;font-size:12px;color:#64748b;font-family:monospace">ID: {{ $order->razorpay_payment_id }}</p>
        @endif
      @else
        <span class="payment-badge" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a">💵 Cash on Delivery</span>
      @endif
    </div>

    <div class="cta">
      <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}">View Order in Admin Panel →</a>
    </div>
  </div>

  <div class="footer">
    Medikart Admin · {{ config('app.url') }} · This is an automated notification.
  </div>
</div>
</body>
</html>
