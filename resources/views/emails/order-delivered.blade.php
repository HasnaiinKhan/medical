<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Delivered - {{ $order->order_number }}</title>
<style type="text/css">
  body,table,td,p,a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
  table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
  body { margin:0; padding:0; background-color:#f0f2f5; font-family:Arial,Helvetica,sans-serif; color:#1a202c; }
  a { color:#1a56db; text-decoration:none; }
  @media only screen and (max-width:600px) {
    .outer { padding:12px 0 !important; }
    .card  { width:100% !important; border-radius:0 !important; }
    .hpad  { padding:28px 20px !important; }
    .bpad  { padding:28px 20px !important; }
    .fpad  { padding:16px 20px !important; }
    .th, .td { padding:8px 10px !important; font-size:12px !important; }
    .h1 { font-size:20px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
  <tr>
    <td class="outer" align="center" style="padding:32px 16px;background-color:#f0f2f5;">
      <table class="card" width="580" cellpadding="0" cellspacing="0" role="presentation"
             style="background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;">

        <!-- HEADER -->
        <tr>
          <td class="hpad" align="center" style="background-color:#276749;padding:36px 40px;">
            <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.7);font-family:Arial,Helvetica,sans-serif;">Delivery Confirmed</p>
            <h1 class="h1" style="margin:0 0 8px 0;font-size:24px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Order Delivered Successfully!</h1>
            <p style="margin:0 0 16px 0;font-size:14px;color:rgba(255,255,255,0.8);font-family:Arial,Helvetica,sans-serif;">Your medicines have arrived safely.</p>
            <table cellpadding="0" cellspacing="0" role="presentation" align="center">
              <tr><td style="background-color:rgba(255,255,255,0.15);border-radius:4px;padding:6px 18px;">
                <span style="font-size:13px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</span>
              </td></tr>
            </table>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td class="bpad" style="padding:32px 40px;">

            <p style="margin:0 0 6px 0;font-size:16px;font-weight:700;color:#1a202c;font-family:Arial,Helvetica,sans-serif;">Hi {{ $order->customer_name }},</p>
            <p style="margin:0 0 24px 0;font-size:14px;color:#4a5568;line-height:1.7;font-family:Arial,Helvetica,sans-serif;">
              Your order <strong style="color:#1a202c;">{{ $order->order_number }}</strong> has been delivered successfully. We hope you are satisfied with your purchase. Your invoice is attached for your records.
            </p>

            <!-- DELIVERY CONFIRMATION BANNER -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
              <tr>
                <td align="center" style="background-color:#f0fff4;border:1px solid #9ae6b4;border-radius:6px;padding:22px 20px;">
                  <p style="margin:0 0 4px 0;font-size:16px;font-weight:700;color:#276749;font-family:Arial,Helvetica,sans-serif;">Delivered on {{ now()->format('d M Y') }}</p>
                  <p style="margin:0;font-size:13px;color:#38a169;font-family:Arial,Helvetica,sans-serif;">To {{ $order->delivery_area }}, Ahmedabad - {{ $order->delivery_pin }}</p>
                </td>
              </tr>
            </table>

            <!-- ORDER SUMMARY -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Summary</p>
              </td></tr>
              <tr><td style="padding:0 16px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Number</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Date</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('d M Y') }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Payment</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->payment_method === 'online' ? 'Paid Online' : 'Cash on Delivery' }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Total Paid</td>
                    <td style="padding:9px 0;font-size:14px;font-weight:700;color:#276749;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($order->totalRupees(), 2) }}</td>
                  </tr>
                </table>
              </td></tr>
            </table>

            <!-- ITEMS -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr style="background-color:#f7fafc;">
                <th class="th" align="left" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;">Medicine</th>
                <th class="th" align="center" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;width:50px;">Qty</th>
                <th class="th" align="right" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;width:90px;">Amount</th>
              </tr>
              @foreach($order->items as $item)
              <tr>
                <td class="td" style="padding:10px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">{{ $item->medicine_name_snapshot }}</td>
                <td class="td" align="center" style="padding:10px 14px;font-size:13px;font-weight:700;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">{{ $item->quantity }}</td>
                <td class="td" align="right" style="padding:10px 14px;font-size:13px;font-weight:700;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($item->line_total_paise / 100, 2) }}</td>
              </tr>
              @endforeach
            </table>

            <!-- ISSUE NOTE -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
              <tr>
                <td style="background-color:#fffbeb;border:1px solid #f6e05e;border-radius:6px;padding:14px 16px;">
                  <p style="margin:0 0 4px 0;font-size:12px;font-weight:700;color:#92400e;font-family:Arial,Helvetica,sans-serif;">Issue with your order?</p>
                  <p style="margin:0;font-size:13px;color:#78350f;line-height:1.6;font-family:Arial,Helvetica,sans-serif;">If you received damaged or incorrect medicines, you can raise a refund request from your order history within the refund window.</p>
                </td>
              </tr>
            </table>

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr><td align="center" style="padding:8px 0 4px 0;">
                <a href="{{ route('orders.show', $order) }}"
                   style="display:inline-block;background-color:#276749;color:#ffffff;text-decoration:none;padding:13px 34px;border-radius:6px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">View Order Details</a>
              </td></tr>
            </table>

          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td class="fpad" align="center" style="background-color:#f7fafc;border-top:1px solid #e2e8f0;padding:20px 40px;">
            <p style="margin:0 0 4px 0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">&copy; {{ date('Y') }} Rx Plus 365, Ahmedabad. This is an automated email, please do not reply.</p>
            <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Questions? <a href="mailto:support@medikart.in" style="color:#718096;text-decoration:underline;">support@medikart.in</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
