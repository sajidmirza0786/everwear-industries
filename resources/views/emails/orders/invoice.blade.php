<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation</title>
</head>

<body style="margin:0; padding:0; background:#eef0f2; font-family:Arial, Helvetica, sans-serif;">

<div style="max-width:640px; margin:0 auto; background:#ffffff;">

    {{-- ── Header ── --}}
    <div style="background:#0f1111; padding:20px 30px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:0.5px;">
                    {{ config('app.name') }}
                </td>
                <td align="right" style="color:#cccccc; font-size:12px;">
                    Order Confirmed
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Confirmation banner ── --}}
    <div style="background:#067d62; padding:18px 30px;">
        <p style="margin:0; color:#ffffff; font-size:16px; font-weight:bold;">
            ✓ Thank you, {{ $order->name }}! Your order has been placed.
        </p>
        <p style="margin:4px 0 0; color:#d9f2ec; font-size:13px;">
            We'll notify you once it ships.
        </p>
    </div>

    {{-- ── Order meta strip ── --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #e5e7eb;">
        <tr>
            <td style="padding:18px 30px; width:33%;">
                <p style="margin:0; font-size:11px; color:#767676; text-transform:uppercase;">Order Number</p>
                <p style="margin:3px 0 0; font-size:13px; color:#111; font-weight:bold;">{{ $order->uuid }}</p>
            </td>
            <td style="padding:18px 0; width:33%;">
                <p style="margin:0; font-size:11px; color:#767676; text-transform:uppercase;">Order Date</p>
                <p style="margin:3px 0 0; font-size:13px; color:#111; font-weight:bold;">{{ $order->created_at->format('d M, Y') }}</p>
            </td>
            <td style="padding:18px 30px; width:33%;">
                <p style="margin:0; font-size:11px; color:#767676; text-transform:uppercase;">Status</p>
                <p style="margin:3px 0 0; font-size:13px; color:#111; font-weight:bold;">{{ ucfirst($order->status) }}</p>
            </td>
        </tr>
    </table>

    {{-- ── Items ── --}}
    <div style="padding:25px 30px 10px;">
        <p style="margin:0 0 15px; font-size:14px; color:#111; font-weight:bold;">Order Summary</p>

        @foreach ($order->items as $item)
            <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #f0f0f0; padding-bottom:14px; margin-bottom:14px;">
                <tr>
                    <td style="vertical-align:top; padding-bottom:14px;">
                        <p style="margin:0; font-size:14px; color:#111; font-weight:bold; line-height:1.4;">
                            {{ $item->product->name ?? 'Product Deleted' }}
                        </p>
                        <p style="margin:4px 0 0; font-size:12px; color:#767676;">
                            SKU: {{ $item->product->code ?? 'N/A' }}
                            @if ($item->productAttribute && $item->productAttribute->size)
                                &nbsp;|&nbsp; Size: {{ $item->productAttribute->size }}
                            @endif
                        </p>
                        <p style="margin:4px 0 0; font-size:12px; color:#767676;">
                            Qty: {{ $item->quantity }} &nbsp;×&nbsp; ₹{{ number_format($item->price, 2) }}
                        </p>

                        @if ($item->coupon_discount > 0 || $item->manual_discount_amount > 0)
                            <p style="margin:6px 0 0; font-size:12px; color:#067d62;">
                                @if ($item->coupon_discount > 0)
                                    Coupon{{ $item->coupon ? ' ('.$item->coupon->code.')' : '' }} applied: −₹{{ number_format($item->coupon_discount, 2) }}<br>
                                @endif
                                @if ($item->manual_discount_amount > 0)
                                    Discount applied: −₹{{ number_format($item->manual_discount_amount, 2) }}
                                @endif
                            </p>
                        @endif
                    </td>
                    <td align="right" style="vertical-align:top; padding-bottom:14px; white-space:nowrap;">
                        <p style="margin:0; font-size:14px; color:#111; font-weight:bold;">
                            ₹{{ number_format($item->line_total, 2) }}
                        </p>
                        @if ($item->tax_amount > 0)
                            <p style="margin:3px 0 0; font-size:11px; color:#767676;">
                                incl. ₹{{ number_format($item->tax_amount, 2) }} GST
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        @endforeach
    </div>

    {{-- ── Price breakdown ── --}}
    @php
        $itemsTotal = $order->items->sum('line_total');
    @endphp

    <div style="padding:5px 30px 25px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f8f8; border-radius:6px; padding:18px;">
            <tr><td colspan="2" style="padding:18px 18px 0;"></td></tr>

            <tr>
                <td style="padding:0 18px 8px; font-size:13px; color:#444;">Subtotal (excl. GST)</td>
                <td align="right" style="padding:0 18px 8px; font-size:13px; color:#444;">₹{{ number_format($order->subtotal_ex_gst, 2) }}</td>
            </tr>

            @if ($order->coupon_discount > 0)
                <tr>
                    <td style="padding:0 18px 8px; font-size:13px; color:#067d62;">Coupon Discount</td>
                    <td align="right" style="padding:0 18px 8px; font-size:13px; color:#067d62;">−₹{{ number_format($order->coupon_discount, 2) }}</td>
                </tr>
            @endif

            @if ($order->manual_discount > 0)
                <tr>
                    <td style="padding:0 18px 8px; font-size:13px; color:#067d62;">Manual Discount</td>
                    <td align="right" style="padding:0 18px 8px; font-size:13px; color:#067d62;">−₹{{ number_format($order->manual_discount, 2) }}</td>
                </tr>
            @endif

            <tr>
                <td style="padding:0 18px 8px; font-size:13px; color:#444;">Taxable Value</td>
                <td align="right" style="padding:0 18px 8px; font-size:13px; color:#444;">₹{{ number_format($order->taxable_value, 2) }}</td>
            </tr>

            <tr>
                <td style="padding:0 18px 8px; font-size:13px; color:#444;">GST</td>
                <td align="right" style="padding:0 18px 8px; font-size:13px; color:#444;">₹{{ number_format($order->tax_amount, 2) }}</td>
            </tr>

            <tr>
                <td style="padding:0 18px 8px; font-size:13px; color:#444;">
                    Shipping
                    @if ($order->shipping_charge_gst > 0)
                        <span style="color:#999;">(incl. ₹{{ number_format($order->shipping_charge_gst, 2) }} GST)</span>
                    @endif
                </td>
                <td align="right" style="padding:0 18px 8px; font-size:13px; color:#444;">₹{{ number_format($order->shipping_charge + $order->shipping_charge_gst, 2) }}</td>
            </tr>

            <tr>
                <td colspan="2" style="padding:10px 18px 0; border-top:1px solid #e0e0e0;"></td>
            </tr>

            <tr>
                <td style="padding:10px 18px 18px; font-size:16px; color:#111; font-weight:bold;">Order Total</td>
                <td align="right" style="padding:10px 18px 18px; font-size:16px; color:#111; font-weight:bold;">₹{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- ── Delivery address ── --}}
    <div style="padding:0 30px 25px;">
        <p style="margin:0 0 8px; font-size:14px; color:#111; font-weight:bold;">Delivery Address</p>
        <p style="margin:0; font-size:13px; color:#444; line-height:1.6;">
            {{ $order->name }}<br>
            {{ $order->address }}@if($order->locality), {{ $order->locality }}@endif<br>
            @if($order->city || $order->state || $order->zipcode)
                {{ $order->city }}@if($order->city && $order->state), @endif{{ $order->state }} {{ $order->zipcode }}<br>
            @endif
            Mobile: {{ $order->mobile }}
        </p>
    </div>

    {{-- ── CTA ── --}}
    <div style="padding:0 30px 35px; text-align:center;">
        <a href="{{ route('order.confirmation', $order) }}"
           style="display:inline-block;
                  background:#0f1111;
                  color:#ffffff;
                  padding:13px 36px;
                  text-decoration:none;
                  border-radius:6px;
                  font-size:14px;
                  font-weight:bold;">
            View Order Details
        </a>
    </div>

    {{-- ── Footer ── --}}
    <div style="background:#f3f4f6; padding:22px 30px; border-top:1px solid #e5e7eb;">
        <p style="margin:0 0 6px; font-size:12px; color:#767676; line-height:1.6;">
            Questions about your order? Contact us anytime.
        </p>
        <p style="margin:0; font-size:12px; color:#111;">
            <a href="mailto:order@everwearindustries.com" style="color:#0f1111; text-decoration:none; font-weight:bold;">
                order@everwearindustries.com
            </a>
        </p>
        <p style="margin:14px 0 0; font-size:11px; color:#999;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</div>

</body>
</html>