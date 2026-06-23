<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Cancelled - Admin - {{ $order->order_number }}</title>
<style type="text/css">
  body,table,td,p,a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
  table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
  body { margin:0; padding:0; background-color:#f0f2f5; font-family:Arial,Helvetica,sans-serif; color:#1a202c; }
  a { color:#1a56db; text-decoration:none; }
  @media only screen and (max-width:600px) {
    .outer { padding:12px 0 !important; }
    .card  { width:100% !important; border-radius:0 !important; }
    .hpad  { padding:24px 20px !important; }
    .bpad  { padding:24px 20px !important; }
    .fpad  { padding:16px 20px !important; }
    .th, .td { padding:8px 10px !important; font-size:12px !important; }
    .grid-td { display:block !important; width:100% !important; margin-bottom:8px !important; }
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
          <td class="hpad" style="background-color:#742a2a;padding:24px 32px;">
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
                <td>
                  <p style="margin:0 0 2px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.5);font-family:Arial,Helvetica,sans-serif;">Admin Alert</p>
                  <p style="margin:0;font-size:18px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Order Cancelled</p>
                </td>
                <td align="right">
                  <span style="display:inline-block;background-color:rgba(255,255,255,0.2);color:#ffffff;border-radius:4px;padding:5px 14px;font-size:12px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</span>
                  <p style="margin:4px 0 0;font-size:11px;color:rgba(255,255,255,0.5);text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->cancelled_at ? $order->cancelled_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- ALERT BANNER -->
        <tr>
          <td style="background-color:#fff5f5;border-bottom:1px solid #feb2b2;padding:12px 32px;">
            <p style="margin:0;font-size:13px;font-weight:600;color:#c53030;font-family:Arial,Helvetica,sans-serif;">
              Order <strong>#{{ $order->order_number }}</strong> was cancelled by <strong>{{ ucfirst($order->cancelled_by ?? 'admin') }}</strong>.
            </p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td class="bpad" style="padding:28px 32px;">

            @if($order->cancellation_reason)
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:20px;">
              <tr><td style="background-color:#fff5f5;border:1px solid #feb2b2;border-left:4px solid #c0392b;border-radius:4px;padding:12px 14px;">
                <p style="margin:0 0 3px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#c0392b;font-family:Arial,Helvetica,sans-serif;">Cancellation Reason</p>
                <p style="margin:0;font-size:13px;color:#742a2a;line-height:1.6;font-family:Arial,Helvetica,sans-serif;">{{ $order->cancellation_reason }}</p>
              </td></tr>
            </table>
            @endif

            <!-- CUSTOMER + ORDER INFO -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:20px;">
              <tr>
                <td class="grid-td" style="width:49%;vertical-align:top;padding-right:12px;">
                  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid #e2e8f0;border-radius:6px;">
                    <tr><td style="background-color:#f7fafc;padding:9px 14px;border-bottom:1px solid #e2e8f0;">
                      <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Customer</p>
                    </td></tr>
                    <tr><td style="padding:12px 14px;">
                      <p style="margin:0 0 3px 0;font-size:14px;font-weight:700;color:#1a202c;font-family:Arial,Helvetica,sans-serif;">{{ $order->customer_name }}</p>
                      <p style="margin:0 0 3px 0;font-size:13px;color:#4a5568;font-family:Arial,Helvetica,sans-serif;">+91 {{ $order->customer_phone }}</p>
                      @if($order->user)<p style="margin:0 0 3px 0;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">{{ $order->user->email }}</p>@endif
                      <p style="margin:0;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">{{ $order->delivery_area }} - {{ $order->delivery_pin }}</p>
                    </td></tr>
                  </table>
                </td>
                <td class="grid-td" style="width:2%;"></td>
                <td class="grid-td" style="width:49%;vertical-align:top;">
                  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid #e2e8f0;border-radius:6px;">
                    <tr><td style="background-color:#f7fafc;padding:9px 14px;border-bottom:1px solid #e2e8f0;">
                      <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Info</p>
                    </td></tr>
                    <tr><td style="padding:0 14px;">
                      <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Order Date</td>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Payment</td>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->payment_method === 'online' ? 'Online' : 'COD' }}</td>
                        </tr>
                        <tr>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Pay Status</td>
                          <td style="padding:7px 0;border-bottom:1px solid #edf2f7;font-size:12px;font-weight:700;color:#1a202c;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ ucfirst($order->payment_status ?? 'pending') }}</td>
                        </tr>
                        <tr>
                          <td style="padding:7px 0;font-size:12px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Total</td>
                          <td style="padding:7px 0;font-size:13px;font-weight:700;color:#c0392b;text-align:right;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($order->totalRupees(), 2) }}</td>
                        </tr>
                      </table>
                    </td></tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- ITEMS -->
            <p style="margin:0 0 10px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;font-family:Arial,Helvetica,sans-serif;">Items ({{ $order->items->count() }})</p>
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                   style="border:1px solid #e2e8f0;border-radius:6px;margin-bottom:20px;">
              <tr style="background-color:#f7fafc;">
                <th class="th" align="left" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;">Medicine</th>
                <th class="th" align="center" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;width:50px;">Qty</th>
                <th class="th" align="right" style="padding:9px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#718096;border-bottom:2px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;width:90px;">Amount</th>
              </tr>
              @foreach($order->items as $item)
              <tr>
                <td class="td" style="padding:10px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">{{ $item->medicine_name_snapshot }}</td>
                <td class="td" align="center" style="padding:10px 14px;font-size:13px;font-weight:700;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">{{ $item->quantity }}</td>
                <td class="td" align="right" style="padding:10px 14px;font-size:13px;font-weight:700;color:#2d3748;border-bottom:1px solid #edf2f7;font-family:Arial,Helvetica,sans-serif;">Rs. {{ number_format($item->line_total_paise/100,2) }}</td>
              </tr>
              @endforeach
            </table>

            @if($order->payment_status === 'paid' && $order->payment_method === 'online')
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:20px;">
              <tr><td style="background-color:#fffbeb;border:1px solid #f6e05e;border-radius:6px;padding:12px 14px;">
                <p style="margin:0 0 3px 0;font-size:12px;font-weight:700;color:#92400e;font-family:Arial,Helvetica,sans-serif;">Refund Required</p>
                <p style="margin:0;font-size:13px;color:#78350f;font-family:Arial,Helvetica,sans-serif;">This order was paid online (Rs. {{ number_format($order->totalRupees(), 2) }}). Please initiate the refund via Razorpay or the refund management panel.</p>
              </td></tr>
            </table>
            @endif

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr><td align="center" style="padding:8px 0 4px 0;">
                <a href="{{ route('admin.orders.show', $order) }}"
                   style="display:inline-block;background-color:#742a2a;color:#ffffff;text-decoration:none;padding:13px 34px;border-radius:6px;font-size:14px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">View Order in Admin Panel</a>
              </td></tr>
            </table>

          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td class="fpad" align="center" style="background-color:#f7fafc;border-top:1px solid #e2e8f0;padding:16px 32px;">
            <p style="margin:0;font-size:12px;color:#a0aec0;font-family:Arial,Helvetica,sans-serif;">Rx Plus 365 Admin Alert &mdash; {{ date('Y') }} &mdash; Do not reply to this email.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
