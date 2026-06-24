<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Refund Request Received - {{ $refund->refund_number }}</title>
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
          <td class="hpad" align="center" style="background-color:#b7791f;padding:36px 40px;">
            <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.7);font-family:Arial,Helvetica,sans-serif;">Refund Request</p>
            <h1 class="h1" style="margin:0 0 8px 0;font-size:22px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Request Received</h1>
            <p style="margin:0 0 16px 0;font-size:14px;color:rgba(255,255,255,0.8);font-family:Arial,Helvetica,sans-serif;">We will review your request within 1-2 business days.</p>
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
              Your refund request for order <strong style="color:#1a202c;">{{ $refund->order->order_number }}</strong> has been received. Our team will review it within <strong>1-2 business days</strong>.
              @if($refund->type === 'cod_bank_transfer')
                Once approved, the amount will be transferred to your bank account within 5-7 business days.
              @else
                Once approved, the amount will be refunded to your original payment method within 5-7 business days.
              @endif
            </p>

            <!-- CONFIRMATION NOTICE -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
              <tr><td style="background-color:#fffbeb;border:1px solid #f6e05e;border-left:4px solid #b7791f;border-radius:4px;padding:14px 16px;">
                <p style="margin:0;font-size:13px;font-weight:700;color:#92400e;font-family:Arial,Helvetica,sans-serif;">Your refund request has been logged. We will notify you once it is reviewed.</p>
              </td></tr>
            </table>

            <!-- REFUND DETAILS -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Refund Details</p>
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
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Refund Amount</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:14px;font-weight:700;color:#276749;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($refund->amountRupees(), 2) }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Refund Method</td>
                    <td style="padding:9px 0;border-bottom:1px solid #edf2f7;font-size:13px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $refund->type === 'gateway' ? 'Original Payment Method' : 'Bank Transfer' }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 0;font-size:13px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Your Reason</td>
                    <td style="padding:9px 0;font-size:13px;font-weight:600;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ Str::limit($refund->reason, 80) }}</td>
                  </tr>
                </table>
              </td></tr>
            </table>

            <!-- WHAT HAPPENS NEXT -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:24px;">
              <tr><td style="background-color:#f7fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">What Happens Next</p>
              </td></tr>
              <tr><td style="padding:14px 16px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="width:28px;vertical-align:top;padding-top:1px;">
                      <table cellpadding="0" cellspacing="0"><tr><td align="center" style="width:24px;height:24px;background-color:#b7791f;border-radius:12px;font-size:12px;color:#ffffff;font-weight:700;font-family:Arial,Helvetica,sans-serif;">1</td></tr></table>
                    </td>
                    <td style="padding:0 0 12px 10px;border-bottom:1px solid #edf2f7;">
                      <p style="margin:0 0 2px 0;font-size:13px;font-weight:700;color:#b7791f;font-family:Arial,Helvetica,sans-serif;">Request submitted</p>
                      <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Done - we have your request</p>
                    </td>
                  </tr>
                  <tr><td colspan="2" style="height:12px;"></td></tr>
                  <tr>
                    <td style="width:28px;vertical-align:top;padding-top:1px;">
                      <table cellpadding="0" cellspacing="0"><tr><td align="center" style="width:24px;height:24px;background-color:#e2e8f0;border-radius:12px;font-size:12px;color:#718096;font-weight:700;font-family:Arial,Helvetica,sans-serif;">2</td></tr></table>
                    </td>
                    <td style="padding:0 0 12px 10px;border-bottom:1px solid #edf2f7;">
                      <p style="margin:0 0 2px 0;font-size:13px;font-weight:700;color:#2d3748;font-family:Arial,Helvetica,sans-serif;">Admin review</p>
                      <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Within 1-2 business days</p>
                    </td>
                  </tr>
                  <tr><td colspan="2" style="height:12px;"></td></tr>
                  <tr>
                    <td style="width:28px;vertical-align:top;padding-top:1px;">
                      <table cellpadding="0" cellspacing="0"><tr><td align="center" style="width:24px;height:24px;background-color:#e2e8f0;border-radius:12px;font-size:12px;color:#718096;font-weight:700;font-family:Arial,Helvetica,sans-serif;">3</td></tr></table>
                    </td>
                    <td style="padding:0 0 12px 10px;border-bottom:1px solid #edf2f7;">
                      <p style="margin:0 0 2px 0;font-size:13px;font-weight:700;color:#2d3748;font-family:Arial,Helvetica,sans-serif;">Refund approved and initiated</p>
                      <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">You will receive a confirmation email</p>
                    </td>
                  </tr>
                  <tr><td colspan="2" style="height:12px;"></td></tr>
                  <tr>
                    <td style="width:28px;vertical-align:top;padding-top:1px;">
                      <table cellpadding="0" cellspacing="0"><tr><td align="center" style="width:24px;height:24px;background-color:#e2e8f0;border-radius:12px;font-size:12px;color:#718096;font-weight:700;font-family:Arial,Helvetica,sans-serif;">4</td></tr></table>
                    </td>
                    <td style="padding:0 0 0 10px;">
                      <p style="margin:0 0 2px 0;font-size:13px;font-weight:700;color:#2d3748;font-family:Arial,Helvetica,sans-serif;">Amount credited to your account</p>
                      <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">5-7 business days after approval</p>
                    </td>
                  </tr>
                </table>
              </td></tr>
            </table>

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr><td align="center" style="padding:8px 0 4px 0;">
                <a href="{{ route('orders.show', $refund->order) }}"
                   style="display:inline-block;background-color:#b7791f;color:#ffffff;text-decoration:none;padding:13px 34px;border-radius:6px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">Track Refund Status</a>
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
