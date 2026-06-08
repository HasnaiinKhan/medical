<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Cancelled - Admin Alert</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(30,58,138,.10); }
  .header { background:linear-gradient(135deg,#7f1d1d 0%,#dc2626 100%); padding:28px 40px; text-align:center; }
  .header h1 { margin:0; color:#fff; font-size:22px; font-weight:800; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.8); font-size:13px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); color:#fff; border-radius:99px; padding:3px 14px; font-size:12px; font-weight:700; margin-top:10px; }
  .body  { padding:32px 40px; }
  .alert-bar { background:#fef2f2; border-left:4px solid #dc2626; border-radius:0 8px 8px 0; padding:14px 18px; margin-bottom:24px; font-size:14px; color:#7f1d1d; font-weight:600; }
  .section { margin-bottom:24px; }
  .section h3 { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748b; margin:0 0 12px; padding-bottom:6px; border-bottom:1px solid #e2e8f0; }
  .row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; }
  .row .label { color:#64748b; }
  .row .val   { font-weight:600; color:#1e293b; }
  .reason-box { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; font-size:13px; color:#7f1d1d; line-height:1.6; margin-bottom:24px; }
  .items-table { width:100%; border-collapse:collapse; margin-bottom:12px; }
  .items-table th { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:6px 8px; background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left; }
  .items-table td { padding:8px; font-size:13px; border-bottom:1px solid #f1f5f9; }
  .items-table tr:last-child td { border-bottom:none; }
  .total-row { display:flex; justify-content:space-between; font-size:14px; font-weight:800; color:#dc2626; padding:10px 0 0; border-top:2px solid #fecaca; }
  .refund-alert { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 16px; font-size:13px; color:#92400e; margin-bottom:24px; }
  .cta { text-align:center; margin:20px 0 0; }
  .cta a { display:inline-block; background:#dc2626; color:#fff; text-decoration:none; padding:11px 28px; border-radius:10px; font-size:13px; font-weight:700; }
  .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:18px 40px; text-align:center; font-size:11px; color:#94a3b8; }
</style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>⚠️ Order Cancelled</h1>
    <p>Admin Alert - Medikart</p>
    <span class="badge">{{ $order->order_number }}</span>
  </div>

  <div class="body">

    <div class="alert-bar">
      Order <strong>{{ $order->order_number }}</strong> was cancelled
      by <strong>{{ ucfirst($order->cancelled_by ?? 'admin') }}</strong>
      on {{ $order->cancelled_at ? $order->cancelled_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}.
    </div>

    {{-- Cancellation reason --}}
    @if($order->cancellation_reason)
    <div class="reason-box">
      <strong>Reason:</strong> {{ $order->cancellation_reason }}
    </div>
    @endif

    {{-- Order info --}}
    <div class="section">
      <h3>Order Details</h3>
      <div class="row"><span class="label">Order Number</span><span class="val">{{ $order->order_number }}</span></div>
      <div class="row"><span class="label">Placed On</span><span class="val">{{ $order->created_at->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="label">Payment Method</span><span class="val">{{ $order->payment_method === 'online' ? 'Online (Razorpay)' : 'Cash on Delivery' }}</span></div>
      <div class="row"><span class="label">Payment Status</span><span class="val">{{ ucfirst($order->payment_status ?? 'pending') }}</span></div>
    </div>

    {{-- Customer info --}}
    <div class="section">
      <h3>Customer</h3>
      <div class="row"><span class="label">Name</span><span class="val">{{ $order->customer_name }}</span></div>
      <div class="row"><span class="label">Phone</span><span class="val">+91 {{ $order->customer_phone }}</span></div>
      @if($order->user)
      <div class="row"><span class="label">Email</span><span class="val">{{ $order->user->email }}</span></div>
      @endif
      <div class="row"><span class="label">Area</span><span class="val">{{ $order->delivery_area }}, {{ $order->delivery_pin }}</span></div>
    </div>

    {{-- Items --}}
    <div class="section">
      <h3>Items ({{ $order->items->count() }})</h3>
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
      <div class="total-row">
        <span>Lost Revenue</span>
        <span>₹{{ number_format($order->totalRupees(), 2) }}</span>
      </div>
    </div>

    {{-- Refund alert --}}
    @if($order->payment_status === 'paid' && $order->payment_method === 'online')
    <div class="refund-alert">
      💰 <strong>Refund Required:</strong> This order was paid online (₹{{ number_format($order->totalRupees(), 2) }}).
      Please ensure the refund is initiated via Razorpay or the refund management panel.
    </div>
    @endif

    <div class="cta">
      <a href="{{ route('admin.orders.show', $order) }}">View Order in Admin Panel →</a>
    </div>

  </div>

  <div class="footer">
    Medikart Admin Notification · {{ date('Y') }} · Do not reply to this email.
  </div>
</div>
</body>
</html>
