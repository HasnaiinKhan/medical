<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Refund Request Received</title>
<style>
  body{margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b}
  .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,138,.10)}
  .header{background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);padding:36px 40px;text-align:center}
  .header h1{margin:0;color:#fff;font-size:22px;font-weight:800}
  .header p{margin:6px 0 0;color:rgba(255,255,255,.85);font-size:14px}
  .badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;border-radius:99px;padding:4px 16px;font-size:13px;font-weight:700;margin-top:12px}
  .body{padding:36px 40px}
  .msg{font-size:14px;color:#475569;line-height:1.7;margin-bottom:20px}
  .box{background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:20px 24px;margin-bottom:20px}
  .box h3{margin:0 0 12px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#92400e}
  .row{display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px solid #fef3c7}
  .row:last-child{border-bottom:none}
  .row .label{color:#78350f}.row .val{font-weight:600;color:#1e293b}
  .timeline{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;margin-bottom:20px}
  .timeline h3{margin:0 0 14px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b}
  .step{display:flex;align-items:center;gap:12px;padding:6px 0}
  .dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
  .dot.done{background:#f59e0b;color:#fff}.dot.pending{background:#e2e8f0;color:#94a3b8}
  .step-text{font-size:13px}.step-text.active{font-weight:700;color:#92400e}
  .cta{text-align:center;margin:24px 0}
  .cta a{display:inline-block;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;text-decoration:none;padding:12px 28px;border-radius:12px;font-size:14px;font-weight:700}
  .footer{background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;font-size:12px;color:#94a3b8}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>↩ Refund Request Received</h1>
    <p>We've received your refund request and will review it shortly</p>
    <span class="badge">{{ $refund->refund_number }}</span>
  </div>
  <div class="body">
    <p class="msg">Hi <strong>{{ $refund->order->customer_name }}</strong>,<br><br>
    Your refund request for order <strong>{{ $refund->order->order_number }}</strong> has been received.
    Our team will review it within <strong>1–2 business days</strong>.
    @if($refund->type === 'cod_bank_transfer')
      Once approved, the amount will be transferred to your bank account within 5–7 business days.
    @else
      Once approved, the amount will be refunded to your original payment method within 5–7 business days.
    @endif
    </p>

    <div class="box">
      <h3>Refund Details</h3>
      <div class="row"><span class="label">Refund #</span><span class="val">{{ $refund->refund_number }}</span></div>
      <div class="row"><span class="label">Order #</span><span class="val">{{ $refund->order->order_number }}</span></div>
      <div class="row"><span class="label">Amount</span><span class="val">₹{{ number_format($refund->amountRupees(), 2) }}</span></div>
      <div class="row"><span class="label">Refund Method</span><span class="val">{{ $refund->type === 'gateway' ? '💳 Original Payment Method' : '🏦 Bank Transfer' }}</span></div>
      <div class="row"><span class="label">Your Reason</span><span class="val">{{ Str::limit($refund->reason, 80) }}</span></div>
    </div>

    <div class="timeline">
      <h3>What Happens Next</h3>
      <div class="step"><div class="dot done">✓</div><div class="step-text active">Refund request submitted</div></div>
      <div class="step"><div class="dot pending">2</div><div class="step-text">Admin reviews your request (1–2 days)</div></div>
      <div class="step"><div class="dot pending">3</div><div class="step-text">Refund approved &amp; initiated</div></div>
      <div class="step"><div class="dot pending">4</div><div class="step-text">Amount credited (5–7 business days)</div></div>
    </div>

    <div class="cta">
      <a href="{{ route('orders.show', $refund->order) }}">Track Refund Status →</a>
    </div>
  </div>
  <div class="footer">© {{ date('Y') }} Medikart, Ahmedabad · Questions? support@medikart.in</div>
</div>
</body>
</html>
