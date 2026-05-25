<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Confirmed</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(30,58,138,.10); }
  .header { background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); padding:36px 40px; text-align:center; }
  .header h1 { margin:0; color:#fff; font-size:24px; font-weight:800; letter-spacing:-.5px; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.8); font-size:14px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; border-radius:99px; padding:4px 16px; font-size:13px; font-weight:700; margin-top:12px; }
  .body  { padding:36px 40px; }
  .greeting { font-size:16px; font-weight:600; margin-bottom:8px; }
  .msg { font-size:14px; color:#475569; line-height:1.7; margin-bottom:24px; }
  .order-box { background:#f8faff; border:1px solid #bfdbfe; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .order-box h3 { margin:0 0 14px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
  .row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; border-bottom:1px solid #e2e8f0; }
  .row:last-child { border-bottom:none; }
  .row .label { color:#64748b; }
  .row .val   { font-weight:600; color:#1e293b; }
  .items-table { width:100%; border-collapse:collapse; margin-bottom:20px; }
  .items-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:8px 10px; background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left; }
  .items-table td { padding:10px; font-size:13px; border-bottom:1px solid #f1f5f9; }
  .items-table tr:last-child td { border-bottom:none; }
  .total-row { display:flex; justify-content:space-between; font-size:15px; font-weight:800; color:#1e3a8a; padding:14px 0 0; border-top:2px solid #1e3a8a; margin-top:8px; }
  .cta { text-align:center; margin:28px 0; }
  .cta a { display:inline-block; background:linear-gradient(135deg,#1e40af,#2563eb); color:#fff; text-decoration:none; padding:13px 32px; border-radius:12px; font-size:14px; font-weight:700; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:12px; color:#94a3b8; }
  .status-pill { display:inline-block; background:#dbeafe; color:#1e40af; border-radius:99px; padding:3px 12px; font-size:12px; font-weight:700; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <h1>✅ Order Confirmed!</h1>
    <p>Thank you for shopping with Medikart</p>
    <span class="badge">{{ $order->order_number }}</span>
  </div>

  {{-- Body --}}
  <div class="body">
    <p class="greeting">Hi {{ $order->customer_name }},</p>
    <p class="msg">
      We've received your order and it's being processed. You'll get another email once your order is shipped.
      @if($order->payment_method === 'cod')
        Please keep <strong>₹{{ number_format($order->totalRupees(), 2) }}</strong> ready for cash on delivery.
      @else
        Your online payment of <strong>₹{{ number_format($order->totalRupees(), 2) }}</strong> was successful.
      @endif
    </p>

    {{-- Order summary --}}
    <div class="order-box">
      <h3>Order Summary</h3>
      <div class="row"><span class="label">Order Number</span><span class="val">{{ $order->order_number }}</span></div>
      <div class="row"><span class="label">Order Date</span><span class="val">{{ $order->created_at->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="label">Payment</span><span class="val">{{ $order->payment_method === 'online' ? '💳 Online (Razorpay)' : '💵 Cash on Delivery' }}</span></div>
      <div class="row"><span class="label">Status</span><span class="val"><span class="status-pill">{{ ucfirst($order->status) }}</span></span></div>
    </div>

    {{-- Items --}}
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
          <td style="text-align:center">{{ $item->quantity }}</td>
          <td style="text-align:right;font-weight:600">₹{{ number_format($item->line_total_paise / 100, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="row"><span class="label">Subtotal</span><span class="val">₹{{ number_format($order->subtotal_paise / 100, 2) }}</span></div>
    <div class="row"><span class="label">Delivery</span><span class="val">{{ $order->delivery_fee_paise === 0 ? 'FREE' : '₹'.number_format($order->delivery_fee_paise/100,2) }}</span></div>
    <div class="total-row"><span>Total</span><span>₹{{ number_format($order->totalRupees(), 2) }}</span></div>

    {{-- Delivery address --}}
    <div class="order-box" style="margin-top:24px;">
      <h3>📍 Delivery Address</h3>
      <p style="margin:0;font-size:13px;line-height:1.7;color:#334155;">
        {{ $order->address_line1 }}<br>
        @if($order->address_line2){{ $order->address_line2 }}<br>@endif
        {{ $order->delivery_area }}, Ahmedabad — {{ $order->delivery_pin }}
      </p>
    </div>

    <div class="cta">
      <a href="{{ route('orders.show', $order) }}">View My Order →</a>
    </div>
  </div>

  <div class="footer">
    © {{ date('Y') }} Medikart, Ahmedabad · This is an automated email, please do not reply.<br>
    <span style="color:#cbd5e1">Questions? Contact us at support@medikart.in</span>
  </div>
</div>
</body>
</html>
