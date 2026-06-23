<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Order {{ $order->order_number }} - Rx Plus 365 Admin</title>
<style type="text/css">
  body, table, td, p, a, span { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  body  { margin: 0; padding: 0; background-color: #edf0f5; font-family: Arial, Helvetica, sans-serif; color: #1a202c; }
  a     { color: #1a56db; text-decoration: none; }
  img   { border: 0; outline: none; }

  /* ── Mobile ── */
  @media only screen and (max-width: 620px) {
    .outer   { padding: 0 !important; }
    .card    { width: 100% !important; border-radius: 0 !important; }
    .hpad    { padding: 20px 18px !important; }
    .bpad    { padding: 20px 18px !important; }
    .fpad    { padding: 14px 18px !important; }
    .col-l, .col-r { display: block !important; width: 100% !important; padding-right: 0 !important; padding-left: 0 !important; }
    .col-gap { display: none !important; }
    .col-l   { padding-bottom: 12px !important; }
    .th, .td { padding: 8px 10px !important; font-size: 12px !important; }
    .h-title { font-size: 17px !important; }
    .total-amount { font-size: 20px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#edf0f5;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#edf0f5;">
<tr>
  <td class="outer" align="center" style="padding:28px 16px;">

    <table class="card" width="600" cellpadding="0" cellspacing="0" role="presentation"
           style="background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #d1d9e6;">

      {{-- ═══════════════════════════════════════
           HEADER — dark with order badge + amount
           ═══════════════════════════════════════ --}}
      <tr>
        <td class="hpad" style="background-color:#0f172a;padding:22px 32px;">
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td style="vertical-align:middle;">
                {{-- Brand line --}}
                <p style="margin:0 0 3px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,0.4);font-family:Arial,Helvetica,sans-serif;">Rx Plus 365 &mdash; Admin</p>
                <p class="h-title" style="margin:0 0 10px 0;font-size:19px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">New Order Received</p>
                {{-- Order number badge --}}
                <table cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="background-color:#1a56db;border-radius:4px;padding:4px 14px;">
                      <span style="font-size:12px;font-weight:700;color:#ffffff;letter-spacing:0.6px;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</span>
                    </td>
                    <td style="width:8px;"></td>
                    {{-- Payment method badge --}}
                    @if($order->payment_method === 'online')
                    <td style="background-color:#065f46;border-radius:4px;padding:4px 12px;">
                      <span style="font-size:11px;font-weight:700;color:#6ee7b7;font-family:Arial,Helvetica,sans-serif;">PAID ONLINE</span>
                    </td>
                    @else
                    <td style="background-color:#78350f;border-radius:4px;padding:4px 12px;">
                      <span style="font-size:11px;font-weight:700;color:#fcd34d;font-family:Arial,Helvetica,sans-serif;">CASH ON DELIVERY</span>
                    </td>
                    @endif
                  </tr>
                </table>
              </td>
              <td style="vertical-align:middle;text-align:right;padding-left:16px;">
                <p style="margin:0 0 2px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.4);font-family:Arial,Helvetica,sans-serif;">Order Total</p>
                <p class="total-amount" style="margin:0 0 4px 0;font-size:24px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Rs.&nbsp;{{ number_format($order->totalRupees(), 2) }}</p>
                <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.4);font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('d M Y, h:i A') }}</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      {{-- ═════════════════════════════
           ALERT STRIP
           ═════════════════════════════ --}}
      <tr>
        <td style="padding:0;">
          @if($order->payment_method === 'online')
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td style="background-color:#ecfdf5;border-top:3px solid #10b981;border-bottom:1px solid #a7f3d0;padding:11px 32px;">
                <p style="margin:0;font-size:13px;font-weight:600;color:#065f46;font-family:Arial,Helvetica,sans-serif;">
                  Payment confirmed via Razorpay. Order is ready to process.
                </p>
              </td>
            </tr>
          </table>
          @else
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td style="background-color:#fffbeb;border-top:3px solid #f59e0b;border-bottom:1px solid #fde68a;padding:11px 32px;">
                <p style="margin:0;font-size:13px;font-weight:600;color:#92400e;font-family:Arial,Helvetica,sans-serif;">
                  Cash on Delivery order. Customer will pay Rs.&nbsp;{{ number_format($order->totalRupees(), 2) }} on arrival.
                </p>
              </td>
            </tr>
          </table>
          @endif
        </td>
      </tr>

      {{-- ═════════════════════════════
           BODY
           ═════════════════════════════ --}}
      <tr>
        <td class="bpad" style="padding:28px 32px;">

          {{-- ── Section label ── --}}
          <p style="margin:0 0 12px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;">Customer &amp; Order Info</p>

          {{-- ── Two-column info cards ── --}}
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:26px;">
            <tr>
              {{-- Left: Customer --}}
              <td class="col-l" style="width:48%;vertical-align:top;padding-right:8px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                       style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                  <tr>
                    <td style="background-color:#f8fafc;padding:9px 14px;border-bottom:1px solid #e2e8f0;">
                      <p style="margin:0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.7px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Customer</p>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:14px;">
                      <p style="margin:0 0 4px 0;font-size:14px;font-weight:700;color:#0f172a;font-family:Arial,Helvetica,sans-serif;">{{ $order->customer_name }}</p>
                      <p style="margin:0 0 4px 0;font-size:13px;color:#334155;font-family:Arial,Helvetica,sans-serif;">+91&nbsp;{{ $order->customer_phone }}</p>
                      @if($order->user)
                      <p style="margin:0 0 8px 0;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">{{ $order->user->email }}</p>
                      @else
                      <p style="margin:0 0 8px 0;font-size:12px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;">No account email</p>
                      @endif
                      <p style="margin:0;font-size:12px;color:#64748b;line-height:1.65;font-family:Arial,Helvetica,sans-serif;">
                        {{ $order->address_line1 }}
                        @if($order->address_line2)<br>{{ $order->address_line2 }}@endif<br>
                        {{ $order->delivery_area }},&nbsp;Ahmedabad&nbsp;-&nbsp;{{ $order->delivery_pin }}
                      </p>
                    </td>
                  </tr>
                </table>
              </td>

              <td class="col-gap" style="width:4%;"></td>

              {{-- Right: Order info --}}
              <td class="col-r" style="width:48%;vertical-align:top;padding-left:8px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                       style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                  <tr>
                    <td style="background-color:#f8fafc;padding:9px 14px;border-bottom:1px solid #e2e8f0;">
                      <p style="margin:0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.7px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Order Info</p>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:0 14px;">
                      <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Order Number</td>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;font-weight:700;color:#0f172a;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Placed On</td>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;font-weight:700;color:#0f172a;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Time</td>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;font-weight:700;color:#0f172a;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->created_at->format('h:i A') }}</td>
                        </tr>
                        <tr>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Payment</td>
                          <td style="padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:12px;font-weight:700;color:#0f172a;text-align:right;font-family:Arial,Helvetica,sans-serif;">{{ $order->payment_method === 'online' ? 'Online (Razorpay)' : 'Cash on Delivery' }}</td>
                        </tr>
                        <tr>
                          <td style="padding:9px 0;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Status</td>
                          <td style="padding:9px 0;text-align:right;font-family:Arial,Helvetica,sans-serif;">
                            <span style="display:inline-block;background-color:#dbeafe;color:#1e40af;border-radius:99px;padding:2px 10px;font-size:11px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">{{ ucfirst($order->status) }}</span>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          {{-- ── Order Items ── --}}
          <p style="margin:0 0 12px 0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;">Order Items ({{ $order->items->count() }})</p>

          <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                 style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;margin-bottom:8px;">
            {{-- Header row --}}
            <tr style="background-color:#0f172a;">
              <th class="th" align="left"   style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;border-bottom:0;">#</th>
              <th class="th" align="left"   style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;border-bottom:0;">Medicine</th>
              <th class="th" align="center" style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;border-bottom:0;width:46px;">Qty</th>
              <th class="th" align="right"  style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;border-bottom:0;width:80px;">Unit</th>
              <th class="th" align="right"  style="padding:10px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;border-bottom:0;width:90px;">Amount</th>
            </tr>
            {{-- Item rows --}}
            @foreach($order->items as $i => $item)
            <tr style="background-color:{{ $loop->odd ? '#ffffff' : '#f8fafc' }};">
              <td class="td" style="padding:10px 14px;font-size:12px;color:#94a3b8;border-bottom:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;vertical-align:middle;">{{ $i + 1 }}</td>
              <td class="td" style="padding:10px 14px;font-size:13px;color:#0f172a;font-weight:600;border-bottom:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;vertical-align:middle;">{{ $item->medicine_name_snapshot }}</td>
              <td class="td" align="center" style="padding:10px 14px;font-size:13px;font-weight:700;color:#334155;border-bottom:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;vertical-align:middle;">{{ $item->quantity }}</td>
              <td class="td" align="right"  style="padding:10px 14px;font-size:12px;color:#64748b;border-bottom:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;vertical-align:middle;">Rs.&nbsp;{{ number_format($item->unit_price_paise / 100, 2) }}</td>
              <td class="td" align="right"  style="padding:10px 14px;font-size:13px;font-weight:700;color:#0f172a;border-bottom:1px solid #f1f5f9;font-family:Arial,Helvetica,sans-serif;vertical-align:middle;">Rs.&nbsp;{{ number_format($item->line_total_paise / 100, 2) }}</td>
            </tr>
            @endforeach
            {{-- Subtotal --}}
            <tr>
              <td colspan="4" style="padding:9px 14px;font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;">Subtotal</td>
              <td align="right" style="padding:9px 14px;font-size:12px;font-weight:600;color:#334155;border-top:1px solid #e2e8f0;font-family:Arial,Helvetica,sans-serif;">Rs.&nbsp;{{ number_format($order->subtotal_paise / 100, 2) }}</td>
            </tr>
            {{-- Delivery --}}
            <tr>
              <td colspan="4" style="padding:9px 14px;font-size:12px;color:#64748b;font-family:Arial,Helvetica,sans-serif;">Delivery Fee</td>
              <td align="right" style="padding:9px 14px;font-size:12px;font-weight:600;{{ $order->delivery_fee_paise === 0 ? 'color:#059669;' : 'color:#334155;' }}font-family:Arial,Helvetica,sans-serif;">{{ $order->delivery_fee_paise === 0 ? 'FREE' : 'Rs. '.number_format($order->delivery_fee_paise / 100, 2) }}</td>
            </tr>
            {{-- Grand total --}}
            <tr style="background-color:#0f172a;">
              <td colspan="4" style="padding:12px 14px;font-size:13px;font-weight:700;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">Grand Total</td>
              <td align="right" style="padding:12px 14px;font-size:15px;font-weight:700;color:#60a5fa;font-family:Arial,Helvetica,sans-serif;">Rs.&nbsp;{{ number_format($order->totalRupees(), 2) }}</td>
            </tr>
          </table>

          {{-- ── Razorpay ID (online orders only) ── --}}
          @if($order->payment_method === 'online' && $order->razorpay_payment_id)
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px;margin-bottom:0;">
            <tr>
              <td style="background-color:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:11px 14px;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#16a34a;font-family:Arial,Helvetica,sans-serif;">Payment Verified</td>
                    <td align="right" style="font-size:12px;font-weight:700;color:#15803d;font-family:monospace,Arial,sans-serif;">{{ $order->razorpay_payment_id }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          @endif

          {{-- ── Divider ── --}}
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:22px 0;">
            <tr><td style="height:1px;background-color:#e2e8f0;font-size:0;line-height:0;">&nbsp;</td></tr>
          </table>

          {{-- ── CTA button ── --}}
          <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
              <td align="center" style="padding:4px 0;">
                <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}"
                   style="display:inline-block;background-color:#1a56db;color:#ffffff;text-decoration:none;padding:13px 36px;border-radius:6px;font-size:14px;font-weight:700;letter-spacing:0.2px;font-family:Arial,Helvetica,sans-serif;">View Order in Admin Panel</a>
              </td>
            </tr>
          </table>

        </td>
      </tr>

      {{-- ═════════════════════════════
           FOOTER
           ═════════════════════════════ --}}
      <tr>
        <td class="fpad" align="center"
            style="background-color:#0f172a;border-top:1px solid #1e293b;padding:18px 32px;">
          <p style="margin:0 0 3px 0;font-size:12px;font-weight:600;color:rgba(255,255,255,0.5);font-family:Arial,Helvetica,sans-serif;">Rx Plus 365 Admin Alert &mdash; {{ date('Y') }}</p>
          <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.3);font-family:Arial,Helvetica,sans-serif;">Automated notification. Do not reply to this email.</p>
        </td>
      </tr>

    </table>
    {{-- /card --}}

  </td>
</tr>
</table>

</body>
</html>
