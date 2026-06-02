<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Refund Processed</title>
<style>
  body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b}
  .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,.10)}
  .header{background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);padding:36px 40px;text-align:center}
  .header h1{margin:0;color:#fff;font-size:22px;font-weight:800}
  .header p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:14px}
  .badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;border-radius:99px;padding:4px 16px;font-size:13px;font-weight:700;margin-top:12px}
  .body{padding:36px 40px}
  .msg{font-size:14px;color:#475569;line-height:1.7;margin-bottom:20px}
  .success-box{background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:24px;text-align:center;margin-bottom:20px}
  .success-box .amount{font-size:32px;font-weight:900;color:#15803d}
  .success-box p{margin:4px 0 0;font-size:13px;color:#166534}
  .box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;margin-bottom:20px}
  .box h3{margin:0 0 12px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b}
  .row{display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px solid #f1f5f9}
  .row:last-child{border-bottom:none}
  .row .label{color:#64748b}.row .val{font-weight:600;color:#1e293b}
  .cta{text-align:center;margin:24px 0}
  .cta a{display:inline-block;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;text-decoration:none;padding:12px 28px;border-radius:12px;font-size:14px;font-weight:700}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;font-size:12px;color:#94a3b8}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Refund Processed!</h1>
    <p>Your refund has been successfully processed</p>
    <span class="badge">{{ $refund->refund_number }}</span>
  </div>
  <div class="body">
    <p class="msg">Hi <strong>{{ $refund->order->customer_name }}</strong>,<br><br>
    Great news! Your refund for order <strong>{{ $refund->order->order_number }}</strong> has been processed.
    @if($refund->type === 'gateway')
      The amount will appear in your account within 5–7 business days depending on your bank.
    @else
      The amount has been transferred to your bank account. Please check your account in 1–2 business days.
    @endif
    </p>

    <div class="success-box">
      <div class="amount">₹{{ number_format($refund->amountRupees(), 2) }}</div>
      <p>Refund Amount</p>
    </div>

    <div class="box">
      <h3>Refund Summary</h3>
      <div class="row"><span class="label">Refund #</span><span class="val">{{ $refund->refund_number }}</span></div>
      <div class="row"><span class="label">Order #</span><span class="val">{{ $refund->order->order_number }}</span></div>
      <div class="row"><span class="label">Processed On</span><span class="val">{{ $refund->processed_at?->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="label">Refund Method</span><span class="val">{{ $refund->type === 'gateway' ? '💳 Original Payment Method' : '🏦 Bank Transfer' }}</span></div>
      @if($refund->refund_id_gateway)
        <div class="row"><span class="label">Gateway Ref ID</span><span class="val" style="font-family:monospace;font-size:11px">{{ $refund->refund_id_gateway }}</span></div>
      @endif
      @if($refund->admin_notes && $refund->type === 'cod_bank_transfer')
        <div class="row"><span class="label">UTR / Reference</span><span class="val">{{ $refund->admin_notes }}</span></div>
      @endif
    </div>

    <div class="cta">
      <a href="{{ route('orders.show', $refund->order) }}">View Order Details →</a>
    </div>
  </div>
  <div class="footer">© {{ date('Y') }} Medikart, Ahmedabad · Questions? support@medikart.in</div>
</div>
</body>
</html>
