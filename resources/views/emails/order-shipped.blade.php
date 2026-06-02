<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Your Order is Shipped</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(30,58,138,.10); }
  .header { background:linear-gradient(135deg,#7c3aed 0%,#2563eb 100%); padding:36px 40px; text-align:center; }
  .header h1 { margin:0; color:#fff; font-size:24px; font-weight:800; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.8); font-size:14px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; border-radius:99px; padding:4px 16px; font-size:13px; font-weight:700; margin-top:12px; }
  .body  { padding:36px 40px; }
  .greeting { font-size:16px; font-weight:600; margin-bottom:8px; }
  .msg { font-size:14px; color:#475569; line-height:1.7; margin-bottom:24px; }
  .track-box { background:linear-gradient(135deg,#ede9fe,#dbeafe); border:1px solid #c4b5fd; border-radius:12px; padding:24px; text-align:center; margin-bottom:24px; }
  .track-box .icon { font-size:48px; margin-bottom:8px; }
  .track-box h2 { margin:0 0 6px; font-size:20px; font-weight:800; color:#1e3a8a; }
  .track-box p  { margin:0; font-size:13px; color:#475569; }
  .order-box { background:#f8faff; border:1px solid #bfdbfe; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .order-box h3 { margin:0 0 14px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
  .row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; border-bottom:1px solid #e2e8f0; }
  .row:last-child { border-bottom:none; }
  .row .label { color:#64748b; }
  .row .val   { font-weight:600; color:#1e293b; }
  .steps { display:flex; justify-content:space-between; margin:24px 0; }
  .step { flex:1; text-align:center; position:relative; }
  .step::after { content:''; position:absolute; top:16px; left:50%; width:100%; height:2px; background:#e2e8f0; z-index:0; }
  .step:last-child::after { display:none; }
  .step-dot { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 6px; font-size:14px; position:relative; z-index:1; }
  .step-dot.done { background:#2563eb; color:#fff; }
  .step-dot.active { background:#7c3aed; color:#fff; box-shadow:0 0 0 4px #ede9fe; }
  .step-dot.pending { background:#e2e8f0; color:#94a3b8; }
  .step-label { font-size:11px; font-weight:600; color:#64748b; }
  .step-label.active { color:#7c3aed; font-weight:700; }
  .cta { text-align:center; margin:28px 0; }
  .cta a { display:inline-block; background:linear-gradient(135deg,#7c3aed,#2563eb); color:#fff; text-decoration:none; padding:13px 32px; border-radius:12px; font-size:14px; font-weight:700; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <h1>🚚 Your Order is On the Way!</h1>
    <p>Sit tight — your medicines are heading to you</p>
    <span class="badge">{{ $order->order_number }}</span>
  </div>

  {{-- Body --}}
  <div class="body">
    <p class="greeting">Hi {{ $order->customer_name }},</p>
    <p class="msg">
      Great news! Your order <strong>{{ $order->order_number }}</strong> has been shipped and is on its way to your address in <strong>{{ $order->delivery_area }}</strong>.
      @if($order->payment_method === 'cod')
        Please keep <strong>₹{{ number_format($order->totalRupees(), 2) }}</strong> ready for cash on delivery.
      @endif
    </p>

    {{-- Shipping animation box --}}
    <div class="track-box">
      <div class="icon">📦</div>
      <h2>Package Dispatched!</h2>
      <p>Expected delivery within 1–3 business days to {{ $order->delivery_area }}, {{ $order->delivery_pin }}</p>
    </div>

<<<<<<< HEAD
=======
    {{-- Progress steps --}}
    <div class="steps">
      <div class="step">
        <div class="step-dot done">✓</div>
        <div class="step-label">Placed</div>
      </div>
      <div class="step">
        <div class="step-dot done">✓</div>
        <div class="step-label">Confirmed</div>
      </div>
      <div class="step">
        <div class="step-dot active">🚚</div>
        <div class="step-label active">Shipped</div>
      </div>
      <div class="step">
        <div class="step-dot pending">🎉</div>
        <div class="step-label">Delivered</div>
      </div>
    </div>

>>>>>>> 790fbb57cd8a67fb90eb8f1a6093c048cf5a90eb
    {{-- Order details --}}
    <div class="order-box">
      <h3>Order Details</h3>
      <div class="row"><span class="label">Order Number</span><span class="val">{{ $order->order_number }}</span></div>
      <div class="row"><span class="label">Ordered On</span><span class="val">{{ $order->created_at->format('d M Y') }}</span></div>
      <div class="row"><span class="label">Total Amount</span><span class="val">₹{{ number_format($order->totalRupees(), 2) }}</span></div>
      <div class="row"><span class="label">Payment</span><span class="val">{{ $order->payment_method === 'online' ? '💳 Paid Online' : '💵 Cash on Delivery' }}</span></div>
    </div>

    {{-- Delivery address --}}
    <div class="order-box">
      <h3>📍 Delivering To</h3>
      <p style="margin:0;font-size:13px;line-height:1.7;color:#334155;">
        <strong>{{ $order->customer_name }}</strong><br>
        {{ $order->address_line1 }}<br>
        @if($order->address_line2){{ $order->address_line2 }}<br>@endif
        {{ $order->delivery_area }}, Ahmedabad — {{ $order->delivery_pin }}<br>
        📞 +91 {{ $order->customer_phone }}
      </p>
    </div>

    <div class="cta">
      <a href="{{ route('orders.show', $order) }}">Track My Order →</a>
    </div>
  </div>

  <div class="footer">
    © {{ date('Y') }} Medikart, Ahmedabad · This is an automated email, please do not reply.<br>
    <span style="color:#cbd5e1">Questions? Contact us at support@medikart.in</span>
  </div>
</div>
</body>
</html>
