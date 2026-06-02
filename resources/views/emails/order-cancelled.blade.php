<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Cancelled</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(30,58,138,.10); }
  .header { background:linear-gradient(135deg,#991b1b 0%,#dc2626 100%); padding:36px 40px; text-align:center; }
  .header h1 { margin:0; color:#fff; font-size:24px; font-weight:800; letter-spacing:-.5px; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.85); font-size:14px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; border-radius:99px; padding:4px 16px; font-size:13px; font-weight:700; margin-top:12px; }
  .body  { padding:36px 40px; }
  .greeting { font-size:16px; font-weight:600; margin-bottom:8px; }
  .msg { font-size:14px; color:#475569; line-height:1.7; margin-bottom:24px; }
  .cancel-box { background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .cancel-box h3 { margin:0 0 10px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#b91c1c; }
  .cancel-box p { margin:0; font-size:14px; color:#7f1d1d; line-height:1.6; }
  .order-box { background:#f8faff; border:1px solid #bfdbfe; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .order-box h3 { margin:0 0 14px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
  .row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; border-bottom:1px solid #e2e8f0; }
  .row:last-child { border-bottom:none; }
  .row .label { color:#64748b; }
  .row .val   { font-weight:600; color:#1e293b; }
  .items-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  .items-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:8px 10px; background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left; }
  .items-table td { padding:10px; font-size:13px; border-bottom:1px solid #f1f5f9; color:#94a3b8; text-decoration:line-through; }
  .items-table tr:last-child td { border-bottom:none; }
  .refund-box { background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:20px 24px; margin-bottom:24px; }
  .refund-box h3 { margin:0 0 8px; font-size:13px; font-weight:700; color:#92400e; }
  .refund-box p { margin:0; font-size:13px; color:#78350f; line-height:1.6; }
  .cta { text-align:center; margin:28px 0 0; }
  .cta a { display:inline-block; background:linear-gradient(135deg,#1e40af,#2563eb); color:#fff; text-decoration:none; padding:13px 32px; border-radius:12px; font-size:14px; font-weight:700; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <h1>❌ Order Cancelled</h1>
    <p>We're sorry to see this order go</p>
    <span class="badge">{{ $order->order_number }}</span>
  </div>

  {{-- Body --}}
  <div class="body">
    <p class="greeting">Hi {{ $order->customer_name }},</p>
    <p class="msg">
      Your order <strong>{{ $order->order_number }}</strong> has been cancelled.
      @if($order->cancelled_by === 'admin')
        This order was cancelled by our team.
      @else
        Your cancellation request has been processed.
      @endif
    </p>

    {{-- Cancellation reason --}}
    @if($order->cancellation_reason)
    <div class="cancel-box">
      <h3>❗ Reason for Cancellation</h3>
      <p>{{ $order->cancellation_reason }}</p>
    </div>
    @endif

    {{-- Order details --}}
    <div class="order-box">
      <h3>Cancelled Order Details</h3>
      <div class="row"><span class="label">Order Number</span><span class="val">{{ $order->order_number }}</span></div>
      <div class="row"><span class="label">Order Date</span><span class="val">{{ $order->created_at->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="label">Cancelled On</span><span class="val">{{ $order->cancelled_at ? $order->cancelled_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="label">Payment Method</span><span class="val">{{ $order->payment_method === 'online' ? '💳 Online (Razorpay)' : '💵 Cash on Delivery' }}</span></div>
      <div class="row"><span class="label">Order Total</span><span class="val">₹{{ number_format($order->totalRupees(), 2) }}</span></div>
    </div>

    {{-- Items (struck through) --}}
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
          <td style="text-align:right">₹{{ number_format($item->line_total_paise / 100, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Refund info --}}
    @if($order->payment_status === 'paid' && $order->payment_method === 'online')
    <div class="refund-box">
      <h3>💰 Refund Information</h3>
      <p>
        Since you paid online, a refund of <strong>₹{{ number_format($order->totalRupees(), 2) }}</strong>
        will be initiated to your original payment method within <strong>5–7 business days</strong>.
        You'll receive a separate email once the refund is processed.
      </p>
    </div>
    @elseif($order->payment_method === 'cod')
    <div class="refund-box">
      <h3>ℹ️ No Payment to Refund</h3>
      <p>This was a Cash on Delivery order. Since no payment was collected, no refund is needed.</p>
    </div>
    @endif

    <div class="cta">
      <a href="{{ route('home') }}">Continue Shopping →</a>
    </div>
  </div>

  <div class="footer">
    © {{ date('Y') }} Medikart, Ahmedabad · This is an automated email, please do not reply.<br>
    <span style="color:#cbd5e1">Questions? Contact us at support@medikart.in</span>
  </div>
</div>
</body>
</html>
