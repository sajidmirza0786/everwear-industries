<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
</head>

<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;">

<div style="max-width:700px; margin:30px auto; background:#ffffff; padding:40px;">

    <h1 style="margin:0 0 25px; color:#111;">
        Sales Quotation for Order #{{ $order->uuid }}
    </h1>

    <p style="font-size:16px; color:#444;">
        <strong>Hi {{ $order->name }},</strong><br><br>
        Thank you for your order. Below are your order details:
    </p>

    <div style="background:#f3f4f6; padding:20px; margin:25px 0; border-left:4px solid #111827;">

        <p style="margin:0 0 10px;">
            <strong>Order ID:</strong> {{ $order->uuid }}
        </p>

        <p style="margin:0 0 10px;">
            <strong>Date:</strong> {{ $order->created_at->format('d M, Y') }}
        </p>

        <p style="margin:0;">
            <strong>Status:</strong> {{ ucfirst($order->status) }}
        </p>

    </div>

    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-top:20px;">

        <thead>
            <tr style="background:#f9fafb;">
                <th align="left"   style="padding:12px; border:1px solid #e5e7eb;">Product</th>
                <th align="center" style="padding:12px; border:1px solid #e5e7eb;">Qty</th>
                <th align="right"  style="padding:12px; border:1px solid #e5e7eb;">Unit Price (excl. GST)</th>
                <th align="right"  style="padding:12px; border:1px solid #e5e7eb;">GST</th>
                <th align="right"  style="padding:12px; border:1px solid #e5e7eb;">Unit Price (incl. GST)</th>
                <th align="right"  style="padding:12px; border:1px solid #e5e7eb;">Subtotal</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($order->items as $item)
                @php
                    $gstRate   = (float) ($item->product?->gst ?? 0);
                    $exGstUnit = $gstRate > 0
                        ? round($item->price / (1 + $gstRate / 100), 2)
                        : $item->price;
                    $gstUnit   = round($item->price - $exGstUnit, 2);
                @endphp

                <tr>

                    <td style="padding:12px; border:1px solid #e5e7eb;">
                        <strong>{{ $item->product->name ?? 'Product Deleted' }}</strong>
                        <br>
                        <span style="font-size:12px; color:#666;">
                            SKU: {{ $item->product->code ?? 'N/A' }}
                            @if ($item->productAttribute && $item->productAttribute->size)
                                | Size: {{ $item->productAttribute->size }}
                            @endif
                        </span>
                    </td>

                    <td align="center" style="padding:12px; border:1px solid #e5e7eb;">
                        {{ $item->quantity }}
                    </td>

                    <td align="right" style="padding:12px; border:1px solid #e5e7eb;">
                        ₹{{ number_format($exGstUnit, 2) }}
                    </td>

                    <td align="right" style="padding:12px; border:1px solid #e5e7eb;">
                        @if ($gstRate > 0)
                            ₹{{ number_format($gstUnit, 2) }}
                            <span style="font-size:11px; color:#666;">({{ $gstRate }}%)</span>
                        @else
                            —
                        @endif
                    </td>

                    <td align="right" style="padding:12px; border:1px solid #e5e7eb;">
                        ₹{{ number_format($item->price, 2) }}
                    </td>

                    <td align="right" style="padding:12px; border:1px solid #e5e7eb;">
                        ₹{{ number_format($item->price * $item->quantity, 2) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    {{-- GST Summary --}}
    @php
        $orderTotalExGst = 0;
        $orderTotalGst   = 0;
        foreach ($order->items as $item) {
            $rate         = (float) ($item->product?->gst ?? 0);
            $exGstUnit    = $rate > 0 ? round($item->price / (1 + $rate / 100), 2) : $item->price;
            $orderTotalExGst += round($exGstUnit * $item->quantity, 2);
            $orderTotalGst   += round(($item->price - $exGstUnit) * $item->quantity, 2);
        }
    @endphp

    <div style="margin-top:30px; text-align:right;">

        <p style="margin:5px 0; color:#444;">
            <strong>Subtotal (excl. GST):</strong>
            ₹{{ number_format($orderTotalExGst, 2) }}
        </p>

        @if ($orderTotalGst > 0)
            <p style="margin:5px 0; color:#444;">
                <strong>GST:</strong>
                ₹{{ number_format($orderTotalGst, 2) }}
            </p>
        @endif

        <p style="margin:5px 0; color:#444;">
            <strong>Shipping:</strong>
            ₹{{ number_format($order->shipping_charge, 2) }}
        </p>

        <p style="margin:10px 0 5px; font-size:20px; border-top:2px solid #e5e7eb; padding-top:10px;">
            <strong>Total (incl. GST):</strong>
            ₹{{ number_format($order->total + $order->shipping_charge, 2) }}
        </p>

        @if ($orderTotalGst > 0)
            <p style="margin:4px 0; font-size:12px; color:#666;">
                (Includes ₹{{ number_format($orderTotalGst, 2) }} GST)
            </p>
        @endif

    </div>

    <div style="margin-top:35px; text-align:center;">

        <a href="{{ route('order.confirmation', $order) }}"
           style="display:inline-block;
                  background:#111827;
                  color:#ffffff;
                  padding:14px 30px;
                  text-decoration:none;
                  border-radius:6px;
                  font-weight:bold;">
            View Order
        </a>

    </div>

    <p style="margin-top:40px; color:#666; line-height:1.7;">
        If you have any questions, feel free to reach out.
    </p>

    <p style="margin-top:25px; color:#111;">
        Thanks,<br>
        {{ config('app.name') }}<br>
        <a href="mailto:order@everwearindustries.com">order@everwearindustries.com</a>
    </p>

</div>

</body>
</html>