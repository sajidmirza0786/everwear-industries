<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222222;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ── HEADER ── */
        .header {
            border-bottom: 3px solid #f0a500;
            padding: 20px 32px 16px;
        }
        .header-table { width:100%; border-collapse:collapse; }
        .header-table td { vertical-align: bottom; }

        .brand { font-size:18px; font-weight:bold; color:#1a1a1a; letter-spacing:1px; text-transform:uppercase; }
        .brand-sub { font-size:8px; color:black; letter-spacing:2.5px; text-transform:uppercase; margin-top:3px; }

        .inv-right { text-align:right; }
        .inv-type { font-size:8px; color:black; letter-spacing:2px; text-transform:uppercase; margin-bottom:3px; }
        .inv-num { font-size:13px; font-weight:bold; color:#1a1a1a; }

        /* ── META BAND ── */
        .meta-band {
            border-bottom: 1px solid #e5e5e5;
            padding: 14px 32px;
        }
        .meta-table { width:100%; border-collapse:collapse; }
        .meta-table td { width:33.3%; vertical-align:top; padding-right:16px; }
        .meta-lbl { font-size:7px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; color:#aaaaaa; margin-bottom:4px; }
        .meta-val { font-size:10px; color:#1a1a1a; }

        .badge {
            display:inline;
            font-size:7px;
            font-weight:bold;
            letter-spacing:1px;
            text-transform:uppercase;
            padding:2px 6px;
            border-radius:2px;
        }
        .b-completed { border:1px solid #155724; color:#155724; }
        .b-cancelled  { border:1px solid #721c24; color:#721c24; }
        .b-transit    { border:1px solid #856404; color:#856404; }
        .b-prepaid    { border:1px solid #004085; color:#004085; }
        .b-cod        { border:1px solid #383d41; color:#383d41; }

        /* ── BODY ── */
        .body { padding: 20px 32px 28px; }

        .sec-label {
            font-size:7px;
            font-weight:bold;
            letter-spacing:2px;
            text-transform:uppercase;
            color:#aaaaaa;
            border-bottom:1px solid #e5e5e5;
            padding-bottom:5px;
            margin-bottom:10px;
        }

        /* ── ITEMS TABLE ── */
        .tbl { width:100%; border-collapse:collapse; margin-bottom:20px; }

        .tbl thead tr { border-bottom:2px solid #1a1a1a; }
        .tbl th {
            font-size:7px;
            font-weight:bold;
            letter-spacing:1.5px;
            text-transform:uppercase;
            color:#1a1a1a;
            padding:7px 10px;
            text-align:left;
            white-space:nowrap;
        }
        .tbl th.r { text-align:right; }
        .tbl th.c { text-align:center; }

        .tbl td {
            padding:9px 10px;
            font-size:10px;
            color:#222222;
            border-bottom:1px solid #f0f0f0;
            vertical-align:middle;
        }
        .tbl tr:last-child td { border-bottom:2px solid #dddddd; }
        .tbl td.r { text-align:right; }
        .tbl td.c { text-align:center; }

        .i-name { font-weight:bold; font-size:10px; }
        .i-sub  { font-size:8px; color:#888888; margin-top:2px; }
        .i-disc { font-size:8px; color:#c0392b; margin-top:2px; }
        .i-adm  { font-size:8px; color:#7b2fbe; margin-top:1px; }

        .qty-pill {
            display:inline-block;
            border:1px solid #cccccc;
            border-radius:2px;
            padding:1px 7px;
            font-size:10px;
            font-weight:bold;
        }

        .red  { color:#c0392b; font-weight:bold; }
        .green{ color:#27ae60; font-weight:bold; }

        /* ── TOTALS ── */
        .totals-wrap { width:100%; border-collapse:collapse; }
        .totals-wrap > tbody > tr > td { padding:0; vertical-align:top; }
        .t-spacer { width:52%; }
        .t-box    { width:48%; }

        .t-inner { width:100%; border-collapse:collapse; }
        .t-inner td {
            padding:6px 10px;
            font-size:10px;
            color:#444444;
            border-bottom:1px solid #f0f0f0;
        }
        .t-inner td.v { text-align:right; font-weight:bold; color:#1a1a1a; }
        .t-inner tr:last-child td { border-bottom:none; }
        .t-inner .disc td   { color:#c0392b; }
        .t-inner .disc td.v { color:#c0392b; }
        .t-inner .free td.v { color:#27ae60; }

        .grand-wrap {
            border-top:2px solid #1a1a1a;
            border-bottom:2px solid #1a1a1a;
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }
        .grand-wrap td { padding:11px 12px; }
        .g-lbl {
            font-size:8px;
            letter-spacing:2px;
            text-transform:uppercase;
            color:#666666;
            text-align:left;
        }
        .g-val {
            font-size:15px;
            font-weight:bold;
            color:#1a1a1a;
            text-align:right;
        }
        .gst-note {
            font-size:8px;
            color:#999999;
            text-align:right;
            margin-top:5px;
            padding-right:10px;
        }

        /* ── FOOTER ── */
        .footer {
            border-top:1px solid #e5e5e5;
            padding:12px 32px;
        }
        .footer-tbl { width:100%; border-collapse:collapse; }
        .footer-tbl td { vertical-align:middle; }
        .f-note   { font-size:8px; color:#aaaaaa; line-height:1.7; }
        .f-thanks { font-size:10px; font-weight:bold; color:#555555; text-align:right; }

        .accent-bar { background:#f0a500; height:3px; font-size:0; line-height:0; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="brand">EVERWEAR INDUSTRIES</div>
                <div class="brand-sub"><strong>The Legend of Award Products</strong></div>
            </td>
            <td class="inv-right">
                <div class="inv-type"><strong>Sales Quotation</strong></div>
                <div class="inv-num">#{{ $order->uuid }}</div>
            </td>
        </tr>
    </table>
</div>
<div class="accent-bar">&nbsp;</div>

{{-- META BAND --}}
<div class="meta-band">
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="meta-lbl">Order Date</div>
                <div class="meta-val" style="font-weight:bold;">{{ $order->created_at->format('d M Y') }}</div>
                <div style="margin-top:5px;">
                    @php
                        $sc = match($order->status) {
                            'completed'  => 'b-completed',
                            'cancelled'  => 'b-cancelled',
                            default      => 'b-transit',
                        };
                        $pc = $order->payment_method === 'prepaid' ? 'b-prepaid' : 'b-cod';
                    @endphp
                    <span class="badge {{ $sc }}">{{ ucfirst($order->status) }}</span>
                    &nbsp;
                    {{-- <span class="badge {{ $pc }}">{{ strtoupper($order->payment_method) }}</span> --}}
                </div>
            </td>
            <td>
                <div class="meta-lbl">Billed To</div>
                <div class="meta-val">
                    <strong>{{ $order->name }}</strong><br>
                    @if($order->email){{ $order->email }}<br>@endif
                    {{ $order->mobile }}
                </div>
            </td>
            <td style="padding-right:0;">
                <div class="meta-lbl">Shipping Address</div>
                <div class="meta-val">
                    {{ $order->address }}@if($order->locality), {{ $order->locality }}@endif<br>
                    @if($order->city){{ $order->city }},@endif
                    @if($order->state) {{ $order->state }}@endif
                    @if($order->zipcode) &ndash; {{ $order->zipcode }}@endif
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- BODY --}}
<div class="body">

    <div class="sec-label">Order Items</div>

    {{-- ITEMS TABLE --}}
    <table class="tbl" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:40%">Item</th>
                <th class="c" style="width:7%">Qty</th>
                <th class="r" style="width:13%">Unit Price</th>
                <th class="r" style="width:14%">Discount</th>
                <th class="r" style="width:13%">GST</th>
                <th class="r" style="width:13%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                @php
                    $gstRate     = (float)($item->tax_rate ?? $item->product?->gst ?? 0);
                    $lineDisc    = (float)$item->discount_amount;
                    $taxAmt      = (float)$item->tax_amount;
                    $lineTotal   = (float)$item->line_total;
                    $unitPrice   = (float)$item->price;
                @endphp
                <tr>
                    <td>
                        <div class="i-name">{{ $item->product->name ?? 'Product Deleted' }}</div>
                        <div class="i-sub">
                            SKU: {{ $item->product->code ?? 'N/A' }}
                            @if($item->productAttribute)
                                &nbsp;| Size: {{ $item->productAttribute->size }}
                            @endif
                        </div>
                        @if($item->coupon_id && $item->coupon_discount > 0)
                            <div class="i-disc">Coupon: {{ $item->coupon?->code }} (&minus;Rs.&nbsp;{{ number_format($item->coupon_discount, 2) }})</div>
                        @endif
                        @if($item->manual_discount_amount > 0)
                            <div class="i-adm">
                                Manual Discount:
                                @if($item->manual_discount_type === 'percent')
                                    {{ $item->manual_discount_value }}% off
                                @else
                                    Flat Rs.&nbsp;{{ number_format($item->manual_discount_value, 2) }}
                                @endif
                                @if($item->manual_discount_note) &ndash; {{ $item->manual_discount_note }}@endif
                            </div>
                        @endif
                    </td>
                    <td class="c"><span class="qty-pill">{{ $item->quantity }}</span></td>
                    <td class="r">Rs.&nbsp;{{ number_format($unitPrice, 2) }}</td>
                    <td class="r">
                        @if($lineDisc > 0)
                            <span class="red">&minus;Rs.&nbsp;{{ number_format($lineDisc, 2) }}</span>
                        @else
                            &mdash;
                        @endif
                    </td>
                    <td class="r">
                        @if($taxAmt > 0)
                            Rs.&nbsp;{{ number_format($taxAmt, 2) }}
                        @else
                            &mdash;
                        @endif
                    </td>
                    <td class="r"><strong>Rs.&nbsp;{{ number_format($lineTotal, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS --}}
    @php
        $subtotalExGST = (float)$order->subtotal_ex_gst;
        $couponDiscount  = (float)$order->coupon_discount;
        $manualDiscount  = (float)$order->manual_discount;
        $taxableValue    = (float)$order->taxable_value;
        $taxAmount       = (float)$order->tax_amount;
        $shippingCharge  = (float)$order->shipping_charge;
        $shippingGst     = (float)$order->shipping_charge_gst;
        $itemsTotal      = (float)$order->total;
        $grandTotal      = $order->total;
        $totalGst        = round($taxAmount + $shippingGst, 2);
    @endphp

    <table class="totals-wrap" cellpadding="0" cellspacing="0">
        <tbody><tr>
            <td class="t-spacer"></td>
            <td class="t-box">
                <table class="t-inner" cellpadding="0" cellspacing="0">

                    <tr>
                        <td>Subtotal <span style="font-size:8px;color:#aaaaaa;">(before discounts)</span></td>
                        <td class="v">Rs. {{ number_format($subtotalExGST, 2) }}</td>
                    </tr>

                    @if($couponDiscount > 0)
                    <tr class="disc">
                        <td>Coupon Discount</td>
                        <td class="v">&minus;Rs. {{ number_format($couponDiscount, 2) }}</td>
                    </tr>
                    @endif

                    @if($manualDiscount > 0)
                    <tr class="disc">
                        <td>Manual Discount</td>
                        <td class="v">&minus;Rs. {{ number_format($manualDiscount, 2) }}</td>
                    </tr>
                    @endif

                    @if($taxAmount > 0)
                    <tr>
                        <td>GST <span style="font-size:8px;color:#aaaaaa;">(on taxable value Rs.{{ number_format($taxableValue,2) }})</span></td>
                        <td class="v">Rs. {{ number_format($taxAmount, 2) }}</td>
                    </tr>
                    @endif

                    <tr @if($shippingCharge == 0) class="free" @endif>
                        <td>Shipping Charges
                            @if($shippingCharge > 0 && $shippingGst > 0)
                                <span style="font-size:8px;color:#aaaaaa;">(incl. GST Rs.{{ number_format($shippingGst,2) }})</span>
                            @endif
                        </td>
                        <td class="v">
                            @if($shippingCharge > 0)
                                Rs. {{ number_format($shippingCharge + $shippingGst, 2) }}
                            @else
                                <span class="green">FREE</span>
                            @endif
                        </td>
                    </tr>

                </table>

                {{-- GRAND TOTAL --}}
                <table class="grand-wrap" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="g-lbl">Grand Total <span style="font-size:7px;">(incl. GST)</span></td>
                        <td class="g-val">Rs. {{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>

                @if($totalGst > 0)
                    <div class="gst-note">* Inclusive of Rs.&nbsp;{{ number_format($totalGst, 2) }} GST</div>
                @endif

            </td>
        </tr></tbody>
    </table>

</div>

{{-- FOOTER --}}
<div class="footer">
    <table class="footer-tbl" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="f-note">
                    This is a computer-generated document. No signature required.<br>
                    For queries, contact support with order #{{ $order->uuid }}.
                </div>
            </td>
            <td>
                <div class="f-thanks">Thank you for your order!</div>
            </td>
        </tr>
    </table>
</div>
<div class="accent-bar">&nbsp;</div>

</body>
</html>