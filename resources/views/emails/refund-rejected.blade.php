<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Refund Request Rejected - {{ $refund->refund_number }}</title>
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
          <td class="hpad" align="center" style="background-color:#c53030;padding:36px 40px;">
            <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.7);font-family:Arial,Helvetica,sans-serif;">Medikart</p>
            <h1 class="h1" style="margin:0 0 8px 0;font-size:22px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Request Rejected</h1>
            <p style="margin:0 0 16px 0;font-size:14px;color:rgba(255,255,255,0.85);font-family:Arial,Helvetica,sans-serif;">Unfortunately your refund request could not be approved.</p>
            <table cellpadding="0" cellspacing="0" role="presentation" align="center">
              <tr><td style="background-color:rgba(255,255,255,0.15);border-radius:4px;padding:6px 18px;">
                <span style="font-size:13px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">{{ $refund->refund_number }}</span>
              </td></tr>
            </table>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td class="bpad" style="padding:32px 40px;">

            <p style="margin:0 0 20px 0;font-size:14px;color:#4a5568;line-height:1.7;font-family:Arial,Helvetica,sans-serif;">
              Hi <strong style="color:#1a202c;">{{ $refund->order->customer_name }}</strong>,<br><br>
              We have reviewed your refund request for order <strong style="color:#1a202c;">{{ $refund->order->order_number }}</strong> and unfortunately we are unable to approve it at this time.
            </p>

            <!-- REJECTION NOTICE -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
              <tr><td style="background-color:#fff5f5;border:1px solid #fed7d7;border-left:4px solid #c53030;border-radius:4px;padding:14px 16px;">
                <p style="margin:0 0 4px 0;font-size:13px;font-weight:700;color:#c53030;font-family:Arial,Helvetica,sans-serif;">Reason for rejection:</p>
                <p style="margin:0;font-size:13px;color:#742a2a;font-family:Arial,Helvetica,sans-serif;">{{ $refund->admin_notes ?? 'Please contact our support team for more information.' }}</p>
              </td></tr>
            </table>

            <!-- REFUND DETAILS -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Request Details</p>
              </td></tr>
              <tr><td style="padding:0 16px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Refund No.</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $refund->refund_number }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order No.</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $refund->order->order_number }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Requested Amount</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:14px;font-weight:700;color:#c53030;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($refund->amountRupees(), 2) }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Status</td>
                    <td style="padding:9px 0;font-size:13px;font-weight:700;color:#c53030;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rejected</td>
                  </tr>
                </table>
              </td></tr>
            </table>

            <!-- WHAT TO DO NEXT -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">What you can do</p>
              </td></tr>
              <tr><td style="padding:16px;">
                <p style="margin:0 0 8px 0;font-size:13px;color:#4a5568;line-height:1.6;font-family:Arial,Helvetica,sans-serif;">
                  If you believe this rejection is incorrect or have additional information to provide, please contact our support team with your order number and refund reference.
                </p>
                <p style="margin:0;font-size:13px;color:#4a5568;font-family:Arial,Helvetica,sans-serif;">
                  📧 <a href="mailto:support@medikart.in" style="color:#c53030;text-decoration:underline;">support@medikart.in</a>
                </p>
              </td></tr>
            </table>

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr><td align="center" style="padding:8px 0 4px 0;">
                <a href="{{ route('orders.show', $refund->order) }}"
                   style="display:inline-block;background-color:#c53030;color:#ffffff;text-decoration:none;padding:13px 34px;border-radius:6px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">View Order Details</a>
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

