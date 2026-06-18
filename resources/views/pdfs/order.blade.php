<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>
        Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
    </title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:13px;
            color:#1c1c1c;
            background:#ffffff;
            line-height:1.5;
            width:100%;
        }

        .header{
            background-color:#ffffff;
            width:100%;
            border-bottom:1px solid #dddddd;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
        }

        .header-table td{
            padding:36px 0;
            vertical-align:bottom;
        }

        .header-table td:first-child{
            padding-left:48px;
        }

        .header-table td:last-child{
            padding-right:48px;
            text-align:right;
        }

        .brand-name{
            font-size:24px;
            font-weight:bold;
            color:#111111;
            letter-spacing:2px;
            text-transform:uppercase;
        }

        .brand-sub{
            font-size:9px;
            color:#777777;
            letter-spacing:3px;
            text-transform:uppercase;
            margin-top:5px;
        }

        .inv-label{
            font-size:9px;
            color:#777777;
            letter-spacing:3px;
            text-transform:uppercase;
            margin-bottom:5px;
        }

        .inv-number{
            font-size:18px;
            font-weight:bold;
            color:#111111;
            letter-spacing:1px;
        }

        .gold-line{
            background-color:#dddddd;
            height:1px;
            font-size:0;
            line-height:0;
        }

        .meta-table{
            width:100%;
            border-collapse:collapse;
            background-color:#fafaf8;
            border-bottom:1px solid #e4e0d8;
        }

        .meta-table td{
            padding:24px 0;
            vertical-align:top;
            width:33.3%;
        }

        .meta-table td:first-child{
            padding-left:48px;
        }

        .meta-table td:last-child{
            padding-right:48px;
        }

        .meta-lbl{
            font-size:9px;
            font-weight:bold;
            letter-spacing:2px;
            text-transform:uppercase;
            color:#b0a898;
            margin-bottom:6px;
        }

        .meta-val{
            font-size:13px;
            color:#1c1c1c;
        }

        .badge{
            display:inline;
            background-color:#f3f3f3;
            color:#444444;
            font-size:9px;
            font-weight:bold;
            letter-spacing:1.5px;
            text-transform:uppercase;
            padding:2px 8px;
            border:1px solid #cccccc;
            border-radius:10px;
        }

        .body-wrap{
            padding:36px 48px 40px 48px;
        }

        .items-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom:28px;
        }

        .items-table th{
            font-size:9px;
            font-weight:bold;
            letter-spacing:2px;
            text-transform:uppercase;
            color:#999999;
            padding-bottom:10px;
            border-bottom:2px solid #1c1c1c;
            text-align:left;
        }

        .items-table th.r{
            text-align:right;
        }

        .items-table th.c{
            text-align:center;
        }

        .items-table td{
            padding:14px 0;
            font-size:13px;
            color:#1c1c1c;
            vertical-align:middle;
            border-bottom:1px solid #eeebe4;
        }

        .items-table tr:last-child td{
            border-bottom:2px solid #cccccc;
        }

        .items-table td.r{
            text-align:right;
        }

        .items-table td.c{
            text-align:center;
        }

        .item-name{
            font-weight:bold;
            font-size:13px;
        }

        .item-sku{
            font-size:10px;
            color:#aaaaaa;
            margin-top:3px;
        }

        .qty-pill{
            background-color:#eeebe4;
            border-radius:10px;
            padding:2px 10px;
            font-size:12px;
            font-weight:bold;
            color:#555555;
        }

        .gst-note{
            font-size:10px;
            color:#888888;
            margin-top:3px;
        }

        .totals-outer{
            width:100%;
            border-collapse:collapse;
        }

        .totals-outer td{
            padding:0;
            vertical-align:top;
        }

        .totals-spacer{
            width:52%;
        }

        .totals-box{
            width:48%;
        }

        .totals-inner{
            width:100%;
            border-collapse:collapse;
        }

        .totals-inner td{
            padding:8px 0;
            font-size:12px;
            border-bottom:1px solid #eeebe4;
            color:#666666;
        }

        .totals-inner td.val{
            text-align:right;
        }

        .totals-inner tr:last-child td{
            border-bottom:none;
        }

        .free-text{
            color:#2e7d32;
            font-weight:bold;
        }

        .grand-box{
            background-color:#f8f8f8;
            width:100%;
            border-collapse:collapse;
            margin-top:12px;
            border:1px solid #dddddd;
        }

        .grand-box td{
            padding:14px 18px;
            vertical-align:middle;
        }

        .grand-lbl{
            font-size:9px;
            letter-spacing:2.5px;
            text-transform:uppercase;
            color:#666666;
            font-weight:bold;
            text-align:left;
        }

        .grand-val{
            font-size:20px;
            font-weight:bold;
            color:#111111;
            text-align:right;
        }

        .grand-cur{
            font-size:12px;
            color:#111111;
            margin-right:2px;
        }

        .gst-incl-note{
            font-size:9px;
            color:#999999;
            text-align:right;
            margin-top:6px;
        }

        .footer-line{
            border-top:1px solid #e4e0d8;
        }

        .footer-table{
            width:100%;
            border-collapse:collapse;
        }

        .footer-table td{
            padding:22px 0;
            vertical-align:middle;
        }

        .footer-table td:first-child{
            padding-left:48px;
            width:60%;
        }

        .footer-table td:last-child{
            padding-right:48px;
            text-align:right;
            width:40%;
        }

        .footer-note{
            font-size:11px;
            color:#aaaaaa;
            line-height:1.6;
        }

        .footer-thanks{
            font-size:13px;
            color:#444444;
            font-weight:bold;
            letter-spacing:0.5px;
        }

        .bottom-bar{
            background-color:#dddddd;
            height:1px;
            font-size:0;
            line-height:0;
        }
    </style>
</head>

<body>

<div class="header">

    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="brand-name">{{ config('app.name') }}</div>
                <div class="brand-sub">The Legend of Award Products.</div>
            </td>
            <td>
                <div class="inv-label">Sales Quotation</div>
                {{-- <div class="inv-number">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div> --}}
                <div class="inv-number">#{{ $order->uuid }}</div>
            </td>
        </tr>
    </table>

</div>

<div class="gold-line">&nbsp;</div>

<table class="meta-table" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="meta-lbl">Date</div>
            <div class="meta-val">{{ $order->created_at->format('d M, Y') }}</div>
            <div style="margin-top:8px;">
                <span class="badge">{{ ucfirst($order->status) }}</span>
            </div>
        </td>
        <td>
            <div class="meta-lbl">Billed To</div>
            <div class="meta-val">
                {{ $order->name }}<br>
                {{ $order->email }}<br>
                {{ $order->mobile }}
            </div>
        </td>
        <td>
            <div class="meta-lbl">Shipping Address</div>
            <div class="meta-val">
                {{ $order->address }},
                {{ $order->locality }},
                {{ $order->city }},
                {{ $order->state }}
                - {{ $order->zipcode }}
            </div>
        </td>
    </tr>
</table>

<div class="body-wrap">

    <table class="items-table" cellpadding="0" cellspacing="0">

        <thead>
            <tr>
                <th style="width:30%">Item</th>
                <th class="c" style="width:8%">Qty</th>
                <th class="r" style="width:14%">Price<br><span style="font-size:8px;font-weight:normal;">(excl. GST)</span></th>
                <th class="r" style="width:14%">GST</th>
                <th class="r" style="width:18%">Unit Price<br><span style="font-size:8px;font-weight:normal;">(incl. GST)</span></th>
                <th class="r" style="width:16%">Subtotal</th>
            </tr>
        </thead>

        <tbody>

            @foreach($order->items as $item)
                @php
                    $gstRate   = (float) ($item->product?->gst ?? 0);
                    $exGstUnit = $gstRate > 0
                        ? round($item->price / (1 + $gstRate / 100), 2)
                        : $item->price;
                    $gstUnit   = round($item->price - $exGstUnit, 2);
                    $lineTotal = round($item->price * $item->quantity, 2);
                @endphp

                <tr>

                    <td>
                        <div class="item-name">{{ $item->product->name ?? 'Product Deleted' }}</div>
                        <div class="item-sku">
                            SKU: {{ $item->product->code ?? 'N/A' }}
                            @if($item->productAttribute)
                                | Size: {{ $item->productAttribute->size }}
                            @endif
                        </div>
                    </td>

                    <td class="c">
                        <span class="qty-pill">{{ $item->quantity }}</span>
                    </td>

                    <td class="r">
                        Rs. {{ number_format($exGstUnit, 2) }}
                    </td>

                    <td class="r">
                        @if($gstRate > 0)
                            Rs. {{ number_format($gstUnit, 2) }}
                            <div class="gst-note">{{ $gstRate }}%</div>
                        @else
                            —
                        @endif
                    </td>

                    <td class="r">
                        Rs. {{ number_format($item->price, 2) }}
                    </td>

                    <td class="r">
                        Rs. <strong>{{ number_format($lineTotal, 2) }}</strong>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    {{-- ── Compute order-level GST totals ── --}}
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

    <table class="totals-outer" cellpadding="0" cellspacing="0">
        <tr>
            <td class="totals-spacer"></td>
            <td class="totals-box">

                <table class="totals-inner" cellpadding="0" cellspacing="0">

                    <tr>
                        <td>Subtotal <span style="font-size:10px;">(excl. GST)</span></td>
                        <td class="val">Rs. {{ number_format($orderTotalExGst, 2) }}</td>
                    </tr>

                    @if($orderTotalGst > 0)
                        <tr>
                            <td>GST</td>
                            <td class="val">Rs. {{ number_format($orderTotalGst, 2) }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td>Shipping Charges</td>
                        <td class="val">
                            @if($order->shipping_charge > 0)
                                Rs. {{ number_format($order->shipping_charge, 2) }}
                            @else
                                <span class="free-text">FREE</span>
                            @endif
                        </td>
                    </tr>

                </table>

                <table class="grand-box" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="grand-lbl">Total Due <span style="font-size:8px;">(incl. GST)</span></td>
                        <td class="grand-val">
                            <span class="grand-cur">Rs.</span>
                            {{ number_format($order->total + $order->shipping_charge, 2) }}
                        </td>
                    </tr>
                </table>

                @if($orderTotalGst > 0)
                    <div class="gst-incl-note">
                        Includes Rs. {{ number_format($orderTotalGst, 2) }} GST
                    </div>
                @endif

            </td>
        </tr>
    </table>

</div>

<div class="footer-line"></div>

<table class="footer-table" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="footer-note">
                Thank you for shopping with us.
                For any queries regarding this invoice,
                please contact support.
            </div>
        </td>
        <td>
            <div class="footer-thanks">We appreciate your trust.</div>
        </td>
    </tr>
</table>

<div class="bottom-bar">&nbsp;</div>

</body>
</html>