<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice {{ $order->order_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 13px;
    color: #1a202c;
    background: #ffffff;
    padding: 32px 36px;
  }

  /* ── Header ── */
  .inv-header {
    border-bottom: 3px solid #1a56db;
    padding-bottom: 18px;
    margin-bottom: 22px;
  }
  .inv-header table { width: 100%; }
  .brand-name {
    font-size: 22px;
    font-weight: 700;
    color: #1a56db;
    letter-spacing: -0.5px;
  }
  .brand-sub {
    font-size: 11px;
    color: #718096;
    margin-top: 2px;
  }
  .inv-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a202c;
    text-align: right;
  }
  .inv-number {
    font-size: 12px;
    color: #718096;
    text-align: right;
    margin-top: 3px;
  }

  /* ── Meta grid ── */
  .meta-section {
    margin-bottom: 22px;
  }
  .meta-section table { width: 100%; }
  .meta-box {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    padding: 12px 14px;
    vertical-align: top;
    width: 33.33%;
  }
  .meta-box + .meta-box { padding-left: 10px; }
  .meta-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #718096;
    margin-bottom: 5px;
  }
  .meta-value {
    font-size: 12px;
    font-weight: 600;
    color: #1a202c;
    line-height: 1.5;
  }
  .meta-value-sm {
    font-size: 11px;
    color: #4a5568;
    line-height: 1.6;
    font-weight: 400;
  }

  /* ── Status badge ── */
  .status-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
  }
  .status-paid    { background: #c6f6d5; color: #276749; }
  .status-pending { background: #fef3c7; color: #92400e; }
  .status-cod     { background: #ebf8ff; color: #2b6cb0; }

  /* ── Items table ── */
  .items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
  }
  .items-table thead tr {
    background: #1a56db;
    color: #ffffff;
  }
  .items-table th {
    padding: 9px 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
  }
  .items-table th.right { text-align: right; }
  .items-table th.center { text-align: center; }
  .items-table tbody tr { border-bottom: 1px solid #edf2f7; }
  .items-table tbody tr:nth-child(even) { background: #f7fafc; }
  .items-table td {
    padding: 9px 12px;
    font-size: 12px;
    color: #2d3748;
    vertical-align: middle;
  }
  .items-table td.right  { text-align: right; font-weight: 600; }
  .items-table td.center { text-align: center; }
  .sno { color: #a0aec0; font-size: 11px; }

  /* ── Totals ── */
  .totals-section {
    margin-top: 0;
    border-top: 2px solid #e2e8f0;
    padding-top: 10px;
  }
  .totals-table { width: 100%; }
  .totals-table td { padding: 4px 12px; font-size: 12px; }
  .totals-table td.lbl { color: #718096; }
  .totals-table td.val { text-align: right; font-weight: 600; color: #1a202c; }
  .totals-table .total-final td {
    padding-top: 8px;
    border-top: 2px solid #1a56db;
    font-size: 14px;
    font-weight: 700;
  }
  .totals-table .total-final td.lbl { color: #1a202c; }
  .totals-table .total-final td.val { color: #1a56db; font-size: 15px; }
  .free-delivery { color: #276749; font-weight: 700; }

  /* ── Payment info ── */
  .payment-section {
    margin-top: 20px;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    padding: 12px 14px;
  }
  .payment-section table { width: 100%; }
  .payment-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #718096;
    margin-bottom: 6px;
  }
  .payment-row td { font-size: 12px; padding: 2px 0; color: #2d3748; }
  .payment-row td.pl { color: #718096; width: 130px; }

  /* ── Footer ── */
  .inv-footer {
    margin-top: 30px;
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
    text-align: center;
    font-size: 10px;
    color: #a0aec0;
    line-height: 1.7;
  }
  .inv-footer strong { color: #718096; }

  /* ── Note ── */
  .note-box {
    margin-top: 16px;
    background: #fffbeb;
    border: 1px solid #f6e05e;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 11px;
    color: #78350f;
  }
</style>
</head>
<body>

<!-- Header -->
<div class="inv-header">
  <table>
    <tr>
      <td>
        <img src="{{ public_path('Images/company logo/RxPlus.png') }}"
             alt="Medikart"
             style="height:48px;width:auto;object-fit:contain;display:block;">
        <div class="brand-sub">Your Trusted Online Pharmacy, Ahmedabad</div>
      </td>
      <td style="text-align:right;">
        <div class="inv-title">INVOICE</div>
        <div class="inv-number">{{ $order->order_number }}</div>
      </td>
    </tr>
  </table>
</div>

<!-- Meta: Invoice Date | Bill To | Order Status -->
<div class="meta-section">
  <table>
    <tr>
      <td class="meta-box">
        <div class="meta-label">Invoice Date</div>
        <div class="meta-value">{{ $order->created_at->format('d M Y') }}</div>
        <div class="meta-value-sm">{{ $order->created_at->format('h:i A') }}</div>
      </td>
      <td style="width:4%;"></td>
      <td class="meta-box">
        <div class="meta-label">Bill To</div>
        <div class="meta-value">{{ $order->customer_name }}</div>
        <div class="meta-value-sm">
          +91 {{ $order->customer_phone }}<br>
          {{ $order->address_line1 }}<br>
          @if($order->address_line2){{ $order->address_line2 }}<br>@endif
          {{ $order->delivery_area }}, Ahmedabad - {{ $order->delivery_pin }}
        </div>
      </td>
      <td style="width:4%;"></td>
      <td class="meta-box">
        <div class="meta-label">Payment</div>
        @if($order->payment_method === 'online')
          <div class="meta-value">Online (Razorpay)</div>
          <div class="meta-value-sm">
            Status: <span class="status-badge status-paid">Paid</span><br>
            @if($order->razorpay_payment_id)
              ID: {{ $order->razorpay_payment_id }}
            @endif
          </div>
        @else
          <div class="meta-value">Cash on Delivery</div>
          <div class="meta-value-sm">
            @if($order->payment_status === 'paid')
              Status: <span class="status-badge status-paid">Paid</span>
            @else
              Status: <span class="status-badge status-cod">Pay on Delivery</span>
            @endif
          </div>
        @endif
      </td>
    </tr>
  </table>
</div>

<!-- Items table -->
<table class="items-table">
  <thead>
    <tr>
      <th style="width:32px;">#</th>
      <th>Medicine / Product</th>
      <th class="center" style="width:70px;">Qty</th>
      <th class="right" style="width:100px;">Unit Price</th>
      <th class="right" style="width:100px;">Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($order->items as $i => $item)
    <tr>
      <td class="sno">{{ $i + 1 }}</td>
      <td>{{ $item->medicine_name_snapshot }}</td>
      <td class="center">{{ $item->quantity }}</td>
      <td class="right">Rs. {{ number_format($item->unit_price_paise / 100, 2) }}</td>
      <td class="right">Rs. {{ number_format($item->line_total_paise / 100, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<!-- Totals -->
<div class="totals-section">
  <table>
    <tr>
      <td style="width:55%;vertical-align:top;padding-top:10px;">
        @if($order->payment_method === 'cod' && $order->payment_status !== 'paid')
        <div class="note-box">
          Please keep Rs. {{ number_format($order->totalRupees(), 2) }} ready in cash when the delivery arrives.
        </div>
        @endif
      </td>
      <td style="width:45%;vertical-align:top;">
        <table class="totals-table">
          <tr>
            <td class="lbl">Subtotal</td>
            <td class="val">Rs. {{ number_format($order->subtotal_paise / 100, 2) }}</td>
          </tr>
          <tr>
            <td class="lbl">Delivery Fee</td>
            <td class="val">
              @if($order->delivery_fee_paise === 0)
                <span class="free-delivery">FREE</span>
              @else
                Rs. {{ number_format($order->delivery_fee_paise / 100, 2) }}
              @endif
            </td>
          </tr>
          <tr class="total-final">
            <td class="lbl">Total</td>
            <td class="val">Rs. {{ number_format($order->totalRupees(), 2) }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>

<!-- Payment detail row -->
@if($order->payment_method === 'online' && $order->razorpay_payment_id)
<div class="payment-section">
  <div class="payment-label">Payment Reference</div>
  <table>
    <tr class="payment-row">
      <td class="pl">Payment Method</td>
      <td>Online via Razorpay</td>
      <td class="pl">Payment ID</td>
      <td>{{ $order->razorpay_payment_id }}</td>
    </tr>
  </table>
</div>
@endif

<!-- Footer -->
<div class="inv-footer">
  <strong>Medikart</strong> - Your Trusted Online Pharmacy, Ahmedabad, Gujarat<br>
  support@medikart.in &nbsp;|&nbsp; This is a computer-generated invoice and does not require a signature.<br>
  Thank you for choosing Medikart. Always consult a doctor before taking any medication.
</div>

</body>
</html>

