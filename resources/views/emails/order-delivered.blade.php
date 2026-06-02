<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Delivered</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(30,58,138,.10); }
  .header { background:linear-gradient(135deg,#059669 0%,#10b981 100%); padding:36px 40px; text-align:center; }
  .header h1 { margin:0; color:#fff; font-size:24px; font-weight:800; letter-spacing:-.5px; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.85); font-size:14px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; border-radius:99px; padding:4px 16px; font-size:13px; font-weight:700; margin-top:12px; }
  .body  { padding:36px 40px; }
  .greeting { font-size:16px; font-weight:600; margin-bottom:8px; }
  .msg { font-size:14px; color:#475569; line-height:1.7; margin-bottom:24px; }
  .delivered-box { background:linear-gradient(135deg,#d1fae5,#a7f3d0); border:1px solid #6ee7b7; border-radius:12px; padding:28px; text-align:center; margin-bottom:24px; }
  .delivered-box .icon { font-size:56px; margin-bottom:8px; }
  .delivered-box h2 { margin:0 0 6px; font-size:22px; font-weight:800; color:#065f46; }
  .delivered-box p  { margin:0; font-size:13px; color:#047857; }
  .order-box { background:#f8faff; border:1px solid #bfdbfe; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .order-box h3 { margin:0 0 14px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
  .row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; border-bottom:1px solid #e2e8f0; }
  .row:last-child { border-bottom:none; }
  .row .label { color:#64748b; }
  .row .val   { font-weight:600; color:#1e293b; }
  .items-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  .items-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:8px 10px; background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left; }
  .items-table td { padding:10px; font-size:13px; border-bottom:1px solid #f1f5f9; }
  .items-table tr:last-child td { border-bottom:none; }
  .total-row { display:flex; justify-content:space-between; font-size:15px; font-weight:800; color:#065f46; padding:14px 0 0; border-top:2px solid #059669; margin-top:8px; }
  .feedback-box { background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:20px 24px; margin-bottom:24px; text-align:center; }
  .feedback-box p { margin:0 0 12px; font-size:14px; color:#92400e; font-weight:600; }
  .cta { text-align:center; margin:28px 0 0; }
  .cta a { display:inline-block; background:linear-gradient(135deg,#059669,#10b981); color:#fff; text-decoration:none; padding:13px 32px; border-radius:12px; font-size:14px; font-weight:700; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <h1>🎉 Order Delivered!</h1>
    <p>Your medicines have arrived safely</p>
    <span class="badge">{{ $order->order_number }}</span>
  </div>

  {{-- Body --}}
  <div class="body">
    <p class="greeting">Hi {{ $order->customer_name }},</p>
    <p class="msg">
      Your order <strong>{{ $order->order_number }}</strong> has been successfully delivered to your address.
      We hope you're happy with your purchase from Medikart!
    </p>

    {{-- Delivery celebration --}}
    <div class="delivered-box">
      <div class="icon">🎊</div>
      <h2>Successfully Delivered!</h2>
      <p>Delivered on {{ now()->format('d M Y') }} to {{ $order->delivery_area }}, {{ $order->delivery_pin }}</p>
    </div>

    {{-- Order summary --}}
    <div class="order-box">
      <h3>Order Summary</h3>
      <div class="row"><span class="label">Order Number</span><span class="val">{{ $order->order_number }}</span></div>
      <div class="row"><span class="label">Order Date</span><span class="val">{{ $order->created_at->format('d M Y') }}</span></div>
      <div class="row"><span class="label">Payment</span><span class="val">{{ $order->payment_method === 'online' ? '💳 Paid Online' : '💵 Cash on Delivery' }}</span></div>
      <div class="row"><span class="label">Delivered To</span><span class="val">{{ $order->delivery_area }}</span></div>
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
    <div class="total-row"><span>Total Paid</span><span>₹{{ number_format($order->totalRupees(), 2) }}</span></div>

    {{-- Refund note --}}
    <div class="feedback-box" style="margin-top:24px;">
      <p>Something wrong with your order?</p>
      <p style="margin:0;font-size:13px;color:#78350f;">If you received damaged or incorrect medicines, you can raise a refund request from your order history page within the refund window.</p>
    </div>

    <div class="cta">
      <a href="{{ route('orders.show', $order) }}">View Order Details →</a>
    </div>
  </div>

  <div class="footer">
    © {{ date('Y') }} Medikart, Ahmedabad · This is an automated email, please do not reply.<br>
    <span style="color:#cbd5e1">Questions? Contact us at support@medikart.in</span>
  </div>
</div>
</body>
</html>
