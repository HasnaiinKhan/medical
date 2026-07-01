<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Order is On the Way - {{ $order->order_number }}</title>
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
    .step-td { padding:6px 4px !important; font-size:10px !important; }
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
          <td class="hpad" align="center" style="background-color:#553c9a;padding:36px 40px;">
            <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.7);font-family:Arial,Helvetica,sans-serif;">Medikart</p>
            <h1 class="h1" style="margin:0 0 8px 0;font-size:24px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Your package is heading your way</h1>
            <p style="margin:0 0 16px 0;font-size:14px;color:rgba(255,255,255,0.8);font-family:Arial,Helvetica,sans-serif;">Your medicines are being delivered to you now.</p>
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
              Your order <strong style="color:#1a202c;">{{ $order->order_number }}</strong> has been shipped and is on its way to <strong style="color:#1a202c;">{{ $order->delivery_area }}</strong>.
              @if($order->payment_method === 'cod')
                Please keep <strong style="color:#1a202c;">Rs. {{ number_format($order->totalRupees(), 2) }}</strong> ready for cash on delivery.
              @endif
            </p>

            <!-- PROGRESS TRACKER -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:28px;">
              <tr>
                <!-- Step 1: Placed -->
                <td class="step-td" align="center" style="padding:8px 4px;width:25%;">
                  <table cellpadding="0" cellspacing="0" align="center"><tr>
                    <td align="center" style="width:32px;height:32px;background-color:#1a56db;border-radius:16px;font-size:13px;color:#ffffff;font-weight:700;font-family:Arial,Helvetica,sans-serif;">&#10003;</td>
                  </tr></table>
                  <p style="margin:5px 0 0;font-size:11px;font-weight:600;color:#1a56db;font-family:Arial,Helvetica,sans-serif;">Placed</p>
                </td>
                <td style="padding-bottom:18px;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="height:2px;background-color:#1a56db;font-size:0;line-height:0;">&nbsp;</td></tr></table></td>
                <!-- Step 2: Confirmed -->
                <td class="step-td" align="center" style="padding:8px 4px;width:25%;">
                  <table cellpadding="0" cellspacing="0" align="center"><tr>
                    <td align="center" style="width:32px;height:32px;background-color:#1a56db;border-radius:16px;font-size:13px;color:#ffffff;font-weight:700;font-family:Arial,Helvetica,sans-serif;">&#10003;</td>
                  </tr></table>
                  <p style="margin:5px 0 0;font-size:11px;font-weight:600;color:#1a56db;font-family:Arial,Helvetica,sans-serif;">Confirmed</p>
                </td>
                <td style="padding-bottom:18px;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="height:2px;background-color:#1a56db;font-size:0;line-height:0;">&nbsp;</td></tr></table></td>
                <!-- Step 3: Shipped (active) -->
                <td class="step-td" align="center" style="padding:8px 4px;width:25%;">
                  <table cellpadding="0" cellspacing="0" align="center"><tr>
                    <td align="center" style="width:32px;height:32px;background-color:#553c9a;border-radius:16px;font-size:13px;color:#ffffff;font-weight:700;font-family:Arial,Helvetica,sans-serif;border:3px solid #c4b5fd;">3</td>
                  </tr></table>
                  <p style="margin:5px 0 0;font-size:11px;font-weight:700;color:#553c9a;font-family:Arial,Helvetica,sans-serif;">Shipped</p>
                </td>
                <td style="padding-bottom:18px;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="height:2px;background-color:#e2e8f0;font-size:0;line-height:0;">&nbsp;</td></tr></table></td>
                <!-- Step 4: Delivered -->
                <td class="step-td" align="center" style="padding:8px 4px;width:25%;">
                  <table cellpadding="0" cellspacing="0" align="center"><tr>
                    <td align="center" style="width:32px;height:32px;background-color:#e2e8f0;border-radius:16px;font-size:13px;color:#a0aec0;font-weight:700;font-family:Arial,Helvetica,sans-serif;">4</td>
                  </tr></table>
                  <p style="margin:5px 0 0;font-size:11px;font-weight:600;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Delivered</p>
                </td>
              </tr>
            </table>

            <!-- ORDER DETAILS -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Details</p>
              </td></tr>
              <tr><td style="padding:0 16px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Number</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Ordered On</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('d M Y') }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Total</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($order->totalRupees(), 2) }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Payment</td>
                    <td style="padding:9px 0;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->payment_method === 'online' ? 'Paid Online' : 'Cash on Delivery' }}</td>
                  </tr>
                </table>
              </td></tr>
            </table>

            <!-- DELIVERY ADDRESS -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Delivering To</p>
              </td></tr>
              <tr><td style="padding:12px 16px;font-size:13px;color:#2d3748;line-height:1.8;font-family:Arial,Helvetica,sans-serif;">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->address_line1 }}<br>
                @if($order->address_line2){{ $order->address_line2 }}<br>@endif
                {{ $order->delivery_area }}, Ahmedabad - {{ $order->delivery_pin }}<br>
                +91 {{ $order->customer_phone }}
              </td></tr>
            </table>

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr><td align="center" style="padding:8px 0 4px 0;">
                <a href="{{ route('orders.show', $order) }}"
                   style="display:inline-block;background-color:#553c9a;color:#ffffff;text-decoration:none;padding:13px 34px;border-radius:6px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">Track My Order</a>
              </td></tr>
            </table>

          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td class="fpad" align="center" style="background-color:#f7fafc;border-top:1px solid #e2e8f0;padding:20px 40px;">
            <p style="margin:0 0 4px 0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">&copy; {{ date('Y') }} Medikart, Ahmedabad.</p>
            <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Questions? <a href="mailto:support@medikart.in" style="color:#718096;text-decoration:underline;">support@medikart.in</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>

