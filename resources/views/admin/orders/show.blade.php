@extends('admin.master')

@section('seo')
    <title>Order #{{ $order->uuid }} · Admin</title>
@endsection

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
/* ── Reset ── */
.ow *{box-sizing:border-box;margin:0;padding:0;}
.ow{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#0f172a;font-size:13px;line-height:1.5;}

/* ── Layout ── */
.ow-page{max-width:1200px;margin:0 auto;padding:20px 16px 40px;}

/* ── Topbar ── */
.ow-topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:20px;}
.ow-crumb{font-size:10.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;}
.ow-crumb a{color:#94a3b8;text-decoration:none;}
.ow-crumb a:hover{color:#0f172a;}
.ow-h1{font-size:18px;font-weight:800;letter-spacing:-.4px;color:#0f172a;line-height:1;}
.ow-meta{font-size:11px;color:#94a3b8;margin-top:4px;font-family:'SF Mono','Fira Code',monospace;}
.ow-actions{display:flex;gap:6px;flex-wrap:wrap;align-items:center;}
.btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:7px;font-size:11.5px;font-weight:600;text-decoration:none;border:1.5px solid transparent;cursor:pointer;transition:all .14s;white-space:nowrap;line-height:1;}
.btn:hover{text-decoration:none;transform:translateY(-1px);}
.btn-outline{background:#fff;color:#475569;border-color:#e2e8f0;}
.btn-outline:hover{background:#f8fafc;color:#0f172a;}
.btn-pdf{background:#fff5f5;color:#dc2626;border-color:#fecaca;}
.btn-pdf:hover{background:#fee2e2;}
.btn-edit{background:#0f172a;color:#fff;border-color:#0f172a;}
.btn-edit:hover{background:#1e293b;}

/* ── Stat strip ── */
.stat-row{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:14px;}
@media(max-width:900px){.stat-row{grid-template-columns:repeat(3,1fr);}}
@media(max-width:560px){.stat-row{grid-template-columns:repeat(2,1fr);}}
.stat{background:#fff;border:1.5px solid #f1f5f9;border-radius:10px;padding:11px 13px;display:flex;flex-direction:column;gap:3px;}
.stat-label{font-size:9.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;}
.stat-value{font-size:14px;font-weight:800;color:#0f172a;line-height:1.2;}
.stat-sub{font-size:10px;color:#94a3b8;}

/* ── Pills ── */
.pill{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.04em;}
.pill::before{content:'';width:5px;height:5px;border-radius:50%;}
.p-completed{background:#dcfce7;color:#14532d;}.p-completed::before{background:#16a34a;}
.p-cancelled{background:#fee2e2;color:#7f1d1d;}.p-cancelled::before{background:#dc2626;}
.p-in-transit{background:#dbeafe;color:#1e3a5f;}.p-in-transit::before{background:#2563eb;}
.p-pending{background:#fef9c3;color:#713f12;}.p-pending::before{background:#ca8a04;}
.p-processing{background:#ede9fe;color:#3b0764;}.p-processing::before{background:#7c3aed;}
.p-prepaid{background:#dcfce7;color:#14532d;}.p-prepaid::before{background:#16a34a;}
.p-cod{background:#fef9c3;color:#713f12;}.p-cod::before{background:#d97706;}

/* ── Grid rows ── */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
@media(max-width:680px){.two-col{grid-template-columns:1fr;}}

/* ── Card ── */
.card{background:#fff;border:1.5px solid #f1f5f9;border-radius:12px;overflow:hidden;margin-bottom:8px;}
.card-head{display:flex;align-items:center;gap:8px;padding:10px 14px;border-bottom:1.5px solid #f8fafc;background:#fafbfc;}
.ch-icon{width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;}
.ch-title{font-size:12px;font-weight:700;color:#0f172a;letter-spacing:-.1px;}
.ch-badge{margin-left:auto;background:#f1f5f9;color:#64748b;font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px;border:1px solid #e2e8f0;letter-spacing:.03em;}
.card-body{padding:14px;}

/* ── Field rows ── */
.fr{display:flex;justify-content:space-between;align-items:flex-start;padding:5px 0;border-bottom:1px solid #f8fafc;gap:10px;}
.fr:last-child{border-bottom:none;}
.fk{font-size:11px;font-weight:500;color:#94a3b8;flex-shrink:0;padding-top:1px;}
.fv{font-size:11.5px;font-weight:600;color:#0f172a;text-align:right;word-break:break-word;}
.fv.mono{font-family:'SF Mono','Fira Code',monospace;font-size:10.5px;}
.fv.muted{color:#64748b;font-weight:500;}
.fv a{color:#2563eb;text-decoration:none;}
.fv a:hover{text-decoration:underline;}

/* ── Avatar ── */
.av{width:34px;height:34px;border-radius:50%;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:11px;flex-shrink:0;}

/* ── Address block ── */
.addr{background:#f8fafc;border:1.5px solid #f1f5f9;border-radius:8px;padding:10px 12px;font-size:12px;line-height:1.9;color:#475569;margin-bottom:10px;}
.addr strong{color:#0f172a;font-weight:700;}

/* ── Items table ── */
.itbl{width:100%;border-collapse:collapse;}
.itbl thead th{font-size:9.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;padding:8px 11px;border-bottom:2px solid #f1f5f9;white-space:nowrap;background:#fafbfc;}
.itbl tbody td{font-size:11.5px;color:#475569;padding:10px 11px;border-bottom:1px solid #f8fafc;vertical-align:middle;}
.itbl tbody tr:last-child td{border-bottom:none;}
.itbl tbody tr:hover td{background:#fafbfc;}
.itbl tfoot td{font-size:11px;padding:8px 11px;border-top:2px solid #f1f5f9;color:#64748b;}

.pname{font-weight:700;color:#0f172a;font-size:12px;}
.pmeta{font-size:10px;color:#94a3b8;margin-top:2px;display:flex;gap:6px;flex-wrap:wrap;}
.sku{font-family:'SF Mono','Fira Code',monospace;}
.sbadge{display:inline-block;background:#f1f5f9;color:#374151;font-size:10px;font-weight:700;padding:1px 7px;border-radius:4px;border:1.5px solid #e2e8f0;}
.qbadge{display:inline-block;background:#0f172a;color:#fff;font-size:10.5px;font-weight:700;padding:1px 8px;border-radius:5px;min-width:24px;text-align:center;}
.grate{display:inline-block;background:#ede9fe;color:#5b21b6;font-size:9.5px;font-weight:700;padding:1px 5px;border-radius:3px;margin-left:3px;}

/* ── Discount tags ── */
.ctag{display:inline-flex;align-items:center;gap:3px;background:#f0fdf4;color:#15803d;font-size:9.5px;font-weight:700;padding:1px 7px;border-radius:4px;border:1.5px solid #bbf7d0;}
.mtag{display:inline-flex;align-items:center;gap:3px;background:#fffbeb;color:#92400e;font-size:9.5px;font-weight:700;padding:1px 7px;border-radius:4px;border:1.5px solid #fde68a;}

/* ── Per-item discount chips ── */
.item-disc-wrap{display:flex;flex-direction:column;gap:3px;font-size:10px;}
.disc-line{color:#16a34a;font-weight:600;}
.disc-note{color:#94a3b8;font-style:italic;}

/* ── Financial summary ── */
.fin-wrap{max-width:380px;margin-left:auto;}
.srow{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid #f1f5f9;gap:10px;}
.srow:last-child{border-bottom:none;}
.slabel{font-size:11.5px;color:#64748b;font-weight:500;display:flex;align-items:center;gap:5px;flex-wrap:wrap;}
.svalue{font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;}
.s-disc{color:#16a34a;}
.s-muted{color:#94a3b8;font-weight:500;font-size:11px;}
.s-free{color:#16a34a;font-weight:700;}
.s-grand{border-top:2px solid #0f172a !important;margin-top:6px;padding-top:10px !important;}
.s-grand .slabel{font-size:13px;font-weight:700;color:#0f172a;}
.s-grand .svalue{font-size:15px;font-weight:800;}
.srow-dashed{border-bottom-style:dashed !important;}

/* ── Log tables ── */
.ltbl{width:100%;border-collapse:collapse;}
.ltbl thead th{font-size:9.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;padding:7px 11px;border-bottom:2px solid #f1f5f9;background:#fafbfc;white-space:nowrap;}
.ltbl tbody td{font-size:11px;color:#475569;padding:9px 11px;border-bottom:1px solid #f8fafc;vertical-align:top;}
.ltbl tbody tr:last-child td{border-bottom:none;}
.ltbl tbody tr:hover td{background:#fafbfc;}
.abadge{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700;}
.ab-create{background:#dcfce7;color:#14532d;}
.ab-update{background:#dbeafe;color:#1e3a5f;}
.ab-delete{background:#fee2e2;color:#7f1d1d;}
.log-desc{max-width:380px;word-break:break-word;line-height:1.6;}
.log-mid{font-family:'SF Mono','Fira Code',monospace;font-size:9.5px;color:#94a3b8;}

/* ── Pagination ── */
.ow-pg .pagination{margin:0;}
.ow-pg .page-link{border-radius:6px !important;font-size:11px;padding:4px 9px;border-color:#e2e8f0;color:#475569;}
.ow-pg .page-item.active .page-link{background:#0f172a;border-color:#0f172a;color:#fff;}
.ow-pg .page-item.disabled .page-link{opacity:.35;}

/* ── Empty ── */
.empty{text-align:center;padding:28px 16px;color:#94a3b8;}
.empty i{font-size:1.8rem;display:block;margin-bottom:5px;opacity:.3;}
.empty p{font-size:11px;margin:0;}
</style>

@php
    /* ── Per-item display figures (table only — not stored aggregates) ── */
    $dispExGst  = 0;
    $dispGst    = 0;
    $dispRaw    = 0;
    foreach ($order->items as $item) {
        $r          = (float)($item->product?->gst ?? 0);
        $exUnit     = $r > 0 ? round($item->price / (1 + $r / 100), 2) : (float)$item->price;
        $dispExGst += round($exUnit * $item->quantity, 2);
        $dispGst   += round(($item->price - $exUnit) * $item->quantity, 2);
        $dispRaw   += round((float)$item->price * $item->quantity, 2);
    }

    /* ── Stored order-level aggregates (from controller update()) ── */
    // subtotal_ex_gst  = Σ ex-GST line totals BEFORE discount
    // taxable_value    = Σ taxable_price × qty  (ex-GST after all discounts) — the GST base
    // tax_amount       = GST on taxable_value
    // total            = taxable_value + tax_amount  (post-discount, pre-shipping)
    // grand_total      = total + shipping_charge  (if column exists)

    $subtotalExGst  = (float)($order->subtotal_ex_gst ?? 0);
    $taxableValue   = (float)($order->taxable_value   ?? 0);
    $taxAmount      = (float)($order->tax_amount      ?? 0);
    $orderTotal     = (float)($order->items->sum('line_total')) ?? 0;
    $shippingCharge = (float)($order->shipping_charge ?? 0);
    $shippingChargeGst = (float)($order->shipping_charge_gst ?? 0);
    $grandTotal     = (float)($order->total);

    /* ── Item-level discount aggregates for the summary ── */
    $totalCouponDisc  = (float)$order->items->sum('coupon_discount');   // incl-GST
    $totalManualDisc  = (float)$order->items->sum('manual_discount_amount'); // incl-GST
    $totalDiscountAll = $totalCouponDisc + $totalManualDisc;

    // Raw incl-GST subtotal (display: price × qty for each item, sum)
    $rawSubtotal = round($dispRaw, 2);

    $statusSlug = $order->status ?? 'pending';
@endphp

@section('content')
<div class="ow">
<div class="ow-page">

    {{-- ── Topbar ── --}}
    <div class="ow-topbar">
        <div>
            <div class="ow-crumb">
                <a href="{{ route('admin.orders.index') }}">Orders</a> / Detail
            </div>
            <h1 class="ow-h1">Order #{{ $order->uuid }}</h1>
            <div class="ow-meta">
                Placed {{ $order->created_at->format('d M Y, H:i') }} &middot; {{ $order->created_at->diffForHumans() }}
            </div>
        </div>
        <div class="ow-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <a href="{{ route('admin.orders.pdf', $order) }}" target="_blank" class="btn btn-pdf">
                <i class='bx bxs-file-pdf'></i> PDF
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-edit">
                <i class='bx bx-edit-alt'></i> Edit
            </a>
        </div>
    </div>

    {{-- ── Stat strip ── --}}
    <div class="stat-row">
        <div class="stat">
            <div class="stat-label">Placed</div>
            <div class="stat-value">{{ $order->created_at->format('d M Y') }}</div>
            <div class="stat-sub">{{ $order->created_at->format('h:i A') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Status</div>
            <div class="stat-value" style="font-size:12px;margin-top:2px;">
                <span class="pill p-{{ $statusSlug }}">{{ ucfirst($statusSlug) }}</span>
            </div>
        </div>
        <div class="stat">
            <div class="stat-label">Payment</div>
            <div class="stat-value" style="font-size:12px;margin-top:2px;">
                <span class="pill p-{{ $order->payment_method }}">{{ strtoupper($order->payment_method) }}</span>
            </div>
        </div>
        <div class="stat">
            <div class="stat-label">Items / Qty</div>
            <div class="stat-value">{{ $order->items->count() }}</div>
            <div class="stat-sub">Qty {{ $order->items->sum('quantity') }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Grand Total</div>
            <div class="stat-value">₹{{ number_format($grandTotal, 2) }}</div>
            @if($taxAmount > 0)
                <div class="stat-sub">incl. ₹{{ number_format($taxAmount,2) }} GST</div>
            @endif
        </div>
    </div>

    {{-- ── Customer + Address ── --}}
    <div class="two-col">

        {{-- Customer --}}
        <div class="card">
            <div class="card-head">
                <div class="ch-icon" style="background:#eff6ff;color:#1d4ed8;"><i class='bx bx-user'></i></div>
                <span class="ch-title">Customer</span>
                <span class="ch-badge" style="{{ $order->user_id ? 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;' : '' }}">
                    {{ $order->user_id ? 'Registered' : 'Guest' }}
                </span>
            </div>
            <div class="card-body">
                @if($order->user_id)
                <div style="display:flex;align-items:center;gap:9px;padding-bottom:10px;margin-bottom:2px;border-bottom:1px solid #f1f5f9;">
                    <div class="av">{{ strtoupper(substr($order->name,0,2)) }}</div>
                    <div>
                        <div style="font-weight:700;font-size:12px;color:#0f172a;">{{ $order->user->name ?? $order->name }}</div>
                        <a href="{{ route('admin.users.show', $order->user_id) }}" style="font-size:10.5px;color:#2563eb;text-decoration:none;font-weight:600;">View Account →</a>
                    </div>
                </div>
                @endif
                <div class="fr"><span class="fk">Name</span><span class="fv">{{ $order->name }}</span></div>
                <div class="fr"><span class="fk">Email</span><span class="fv"><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></span></div>
                <div class="fr"><span class="fk">Mobile</span><span class="fv"><a href="tel:{{ $order->mobile }}">{{ $order->mobile }}</a></span></div>
                <div class="fr"><span class="fk">IP Address</span><span class="fv mono muted">{{ $order->ip_address ?? 'N/A' }}</span></div>
                <div class="fr"><span class="fk">User ID</span><span class="fv mono muted">{{ $order->user_id ?? 'Guest' }}</span></div>
            </div>
        </div>

        {{-- Address --}}
        <div class="card">
            <div class="card-head">
                <div class="ch-icon" style="background:#fff7ed;color:#c2410c;"><i class='bx bx-map-pin'></i></div>
                <span class="ch-title">Delivery Address</span>
            </div>
            <div class="card-body">
                <div class="addr">
                    <strong>{{ $order->name }}</strong><br>
                    {{ $order->address }}<br>
                    @if($order->locality){{ $order->locality }}, @endif{{ $order->city }}<br>
                    {{ $order->state }} – {{ $order->zipcode }}
                </div>
                <div class="fr"><span class="fk">City</span><span class="fv">{{ $order->city }}</span></div>
                <div class="fr"><span class="fk">State</span><span class="fv">{{ $order->state }}</span></div>
                <div class="fr"><span class="fk">Pincode</span><span class="fv mono">{{ $order->zipcode }}</span></div>
                @if($order->locality)
                <div class="fr"><span class="fk">Locality</span><span class="fv">{{ $order->locality }}</span></div>
                @endif
                <div class="fr"><span class="fk">Mobile</span><span class="fv"><a href="tel:{{ $order->mobile }}">{{ $order->mobile }}</a></span></div>
            </div>
        </div>

    </div>

    {{-- ── Payment + Shipping ── --}}
    <div class="two-col">

        {{-- Payment --}}
        <div class="card">
            <div class="card-head">
                <div class="ch-icon" style="background:#f0fdf4;color:#15803d;"><i class='bx bx-credit-card'></i></div>
                <span class="ch-title">Payment</span>
                <span class="ch-badge" style="{{ $order->payment_method === 'prepaid' ? 'background:#d1fae5;color:#065f46;border-color:#a7f3d0;' : 'background:#fef9c3;color:#78350f;border-color:#fde68a;' }}">
                    {{ strtoupper($order->payment_method) }}
                </span>
            </div>
            <div class="card-body">
                <div class="fr"><span class="fk">Method</span><span class="fv"><span class="pill p-{{ $order->payment_method }}">{{ strtoupper($order->payment_method) }}</span></span></div>
                <div class="fr"><span class="fk">Status</span><span class="fv"><span class="pill p-{{ $statusSlug }}">{{ ucfirst($order->status) }}</span></span></div>
                <div class="fr"><span class="fk">Raw Subtotal <span style="font-size:9px;color:#94a3b8;">(incl. GST)</span></span><span class="fv">₹{{ number_format($rawSubtotal, 2) }}</span></div>
                @if($totalCouponDisc > 0)
                <div class="fr">
                    <span class="fk">Coupon Discount</span>
                    <span class="fv" style="color:#16a34a;">−₹{{ number_format($totalCouponDisc, 2) }}</span>
                </div>
                @endif
                @if($totalManualDisc > 0)
                <div class="fr">
                    <span class="fk">Manual Discount</span>
                    <span class="fv" style="color:#16a34a;">−₹{{ number_format($totalManualDisc, 2) }}</span>
                </div>
                @endif
                <div class="fr"><span class="fk">Order Total <span style="font-size:9px;color:#94a3b8;">(post-discount)</span></span><span class="fv" style="color:#7c3aed;">₹{{ number_format($orderTotal, 2) }}</span></div>
            </div>
        </div>

        {{-- Shipping & GST ── --}}
        <div class="card">
            <div class="card-head">
                <div class="ch-icon" style="background:#fdf4ff;color:#7e22ce;"><i class='bx bx-car'></i></div>
                <span class="ch-title">Shipping &amp; Tax</span>
            </div>
            <div class="card-body">
                <div class="fr">
                    <span class="fk">Shipping</span>
                    <span class="fv">
                        @if($shippingCharge > 0) ₹{{ number_format($shippingCharge + $shippingChargeGst, 2) }}
                        @else <span class="s-free">FREE</span> @endif
                    </span>
                </div>
                <div class="fr"><span class="fk">Total Weight</span><span class="fv">{{ $order->total_weight > 0 ? number_format($order->total_weight,2).' kg' : '—' }}</span></div>
                <div class="fr">
                    <span class="fk">Subtotal ex-GST <span style="font-size:9px;color:#94a3b8;">(before discount)</span></span>
                    <span class="fv mono">₹{{ number_format($subtotalExGst, 2) }}</span>
                </div>
                <div class="fr">
                    <span class="fk">Taxable Value <span style="font-size:9px;color:#94a3b8;">(ex-GST after discount)</span></span>
                    <span class="fv mono">₹{{ number_format($taxableValue, 2) }}</span>
                </div>
                <div class="fr"><span class="fk">GST on Taxable Value</span><span class="fv mono">₹{{ number_format($taxAmount, 2) }}</span></div>
                <div class="fr"><span class="fk">Last Updated</span><span class="fv muted">{{ $order->updated_at->format('d M Y, h:i A') }}</span></div>
            </div>
        </div>

    </div>

    {{-- ── Items Table ── --}}
    <div class="card">
        <div class="card-head">
            <div class="ch-icon" style="background:#f0fdf4;color:#15803d;"><i class='bx bx-shopping-bag'></i></div>
            <span class="ch-title">Order Items</span>
            <span class="ch-badge">
                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                · Qty {{ $order->items->sum('quantity') }}
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="itbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Size</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price <span style="font-weight:400;">(incl. GST)</span></th>
                        <th class="text-end">Ex-GST Unit</th>
                        <th class="text-end">Discounts</th>
                        <th class="text-end">Taxable Price <span style="font-weight:400;">(ex-GST/unit)</span></th>
                        <th class="text-end">GST Amt</th>
                        <th class="text-end">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        @php
                            $gstRate    = (float)($item->product?->gst ?? 0);
                            $exUnit     = $gstRate > 0 ? round($item->price / (1 + $gstRate / 100), 2) : (float)$item->price;
                            $couponDisc = (float)($item->coupon_discount ?? 0);       // incl-GST
                            $manualDisc = (float)($item->manual_discount_amount ?? 0); // incl-GST
                        @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:10px;font-weight:600;">{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.products.show', $item->product) }}" class="text-decoration-none">
                                    <span class="pname">{{ $item->product->name ?? 'Deleted' }}</span>
                                </a>
                                <div class="pmeta">
                                    @if($item->product?->code)<span class="sku">{{ $item->product->code }}</span>@endif
                                    @if($item->product?->id)<span>#{{ $item->product->id }}</span>@endif
                                </div>
                            </td>
                            <td>
                                @if($item->productAttribute?->size)
                                    <span class="sbadge">{{ $item->productAttribute->size }}</span>
                                @else <span style="color:#e2e8f0;">—</span> @endif
                            </td>
                            <td class="text-center"><span class="qbadge">{{ $item->quantity }}</span></td>
                            <td class="text-end" style="font-weight:600;">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-end">₹{{ number_format($exUnit, 2) }}</td>
                            <td class="text-end">
                                @if($couponDisc > 0 || $manualDisc > 0)
                                    <div class="item-disc-wrap">
                                        @if($couponDisc > 0)
                                            <span class="disc-line">
                                                <span class="ctag"><i class='bx bx-purchase-tag' style="font-size:9px;"></i>
                                                    {{ $item->coupon?->code ?? 'Coupon' }}
                                                </span>
                                                −₹{{ number_format($couponDisc, 2) }}
                                            </span>
                                        @endif
                                        @if($manualDisc > 0)
                                            <span class="disc-line">
                                                <span class="mtag">
                                                    {{ $item->manual_discount_type === 'percent'
                                                        ? $item->manual_discount_value.'%'
                                                        : 'flat' }}
                                                </span>
                                                −₹{{ number_format($manualDisc, 2) }}
                                            </span>
                                            @if($item->manual_discount_note)
                                                <span class="disc-note">{{ $item->manual_discount_note }}</span>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <span style="color:#e2e8f0;">—</span>
                                @endif
                            </td>
                            {{-- taxable_price is the ex-GST per-unit price after discount (stored to 4dp) --}}
                            <td class="text-end" style="font-weight:600;">₹{{ number_format($item->taxable_price, 4) }}</td>
                            <td class="text-end">
                                @if($gstRate > 0)
                                    <span style="font-weight:600;">₹{{ number_format($item->tax_amount, 2) }}</span>
                                    <span class="grate">{{ $gstRate }}%</span>
                                @else <span style="color:#e2e8f0;">—</span> @endif
                            </td>
                            <td class="text-end" style="font-weight:800;color:#0f172a;">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10">
                            <div class="empty"><i class='bx bx-package'></i><p>No items in this order.</p></div>
                        </td></tr>
                    @endforelse
                </tbody>
                @if($order->items->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-end" style="font-weight:600;">₹{{ number_format($dispExGst, 2) }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end" style="font-weight:600;">₹{{ number_format($taxAmount, 2) }}</td>
                        <td class="text-end" style="font-weight:800;color:#7c3aed;font-size:12px;">₹{{ number_format($orderTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ── Financial Summary ── --}}
    <div class="card">
        <div class="card-head">
            <div class="ch-icon" style="background:#f5f3ff;color:#6d28d9;"><i class='bx bx-receipt'></i></div>
            <span class="ch-title">Financial Summary</span>
        </div>
        <div class="card-body">
            <div class="fin-wrap">

                {{-- Raw incl-GST subtotal (price × qty, no discounts) --}}
                <div class="srow">
                    <span class="slabel">Items Subtotal <span class="s-muted">(incl. GST, before discount)</span></span>
                    <span class="svalue">₹{{ number_format($rawSubtotal, 2) }}</span>
                </div>

                {{-- GST contained in raw subtotal --}}
                <div class="srow">
                    <span class="slabel">GST in subtotal</span>
                    <span class="svalue s-muted">₹{{ number_format($dispGst, 2) }}</span>
                </div>

                {{-- Show GST base breakdown when any discount exists --}}
                @if($totalDiscountAll > 0)
                <div class="srow srow-dashed">
                    <span class="slabel s-muted">Subtotal ex-GST (before discount)</span>
                    <span class="svalue">₹{{ number_format($subtotalExGst, 2) }}</span>
                </div>

                {{-- Manual discount (sum of item-level manual_discount_amount) --}}
                @if($totalManualDisc > 0)
                <div class="srow">
                    <span class="slabel">
                        <i class='bx bx-tag' style="color:#d97706;font-size:13px;"></i>
                        Manual Discount
                    </span>
                    <span class="svalue s-disc">−₹{{ number_format($totalManualDisc, 2) }}</span>
                </div>
                @endif

                {{-- Coupon discount (sum of item-level coupon_discount) --}}
                @if($totalCouponDisc > 0)
                <div class="srow">
                    <span class="slabel">
                        <i class='bx bx-purchase-tag' style="color:#16a34a;font-size:13px;"></i>
                        Coupon Discount
                    </span>
                    <span class="svalue s-disc">−₹{{ number_format($totalCouponDisc, 2) }}</span>
                </div>
                @endif

                <div class="srow srow-dashed">
                    <span class="slabel s-muted">Taxable Value (ex-GST after discount)</span>
                    <span class="svalue s-muted">₹{{ number_format($taxableValue, 2) }}</span>
                </div>
                <div class="srow srow-dashed">
                    <span class="slabel s-muted">GST on Taxable Value</span>
                    <span class="svalue s-muted">₹{{ number_format($taxAmount, 2) }}</span>
                </div>
                @endif

                {{-- Shipping --}}
                <div class="srow">
                    <span class="slabel">Shipping</span>
                    <span class="svalue">
                        @if($shippingCharge > 0) ₹{{ number_format($shippingCharge, 2) }}
                        @else <span class="s-free">FREE</span> @endif
                    </span>
                </div>
                <div class="srow">
                    <span class="slabel">GST in shipping</span>
                    <span class="svalue s-muted">₹{{ number_format($shippingChargeGst, 2) }}</span>
                </div>

                {{-- Grand Total --}}
                <div class="srow s-grand">
                    <span class="slabel">Grand Total <span class="s-muted" style="font-size:10px;">(incl. GST + shipping)</span></span>
                    <span class="svalue">₹{{ number_format($grandTotal, 2) }}</span>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Order Audit Log ── --}}
    <div class="card">
        <div class="card-head">
            <div class="ch-icon" style="background:#fff7ed;color:#c2410c;"><i class='bx bx-history'></i></div>
            <span class="ch-title">Order Audit Log</span>
            <span class="ch-badge">{{ $order_logs->total() }} {{ Str::plural('entry', $order_logs->total()) }}</span>
        </div>
        @if($order_logs->count() > 0)
            <div style="overflow-x:auto;">
                <table class="ltbl">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th style="width:140px;">Date &amp; Time</th>
                            <th style="width:80px;">Action</th>
                            <th>Description</th>
                            <th style="width:70px;">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order_logs as $log)
                        @php $act = strtolower($log->action ?? 'update'); @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:10px;font-weight:600;">
                                {{ ($order_logs->currentPage()-1)*$order_logs->perPage()+$loop->iteration }}
                            </td>
                            <td>
                                <div style="font-size:11px;font-weight:600;color:#374151;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                                <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i:s A') }}</div>
                            </td>
                            <td>
                                <span class="abadge {{ $act==='create'?'ab-create':($act==='delete'?'ab-delete':'ab-update') }}">
                                    <i class='bx {{ $act==="create"?"bx-plus":($act==="delete"?"bx-trash":"bx-edit") }}'></i>
                                    {{ ucfirst($act) }}
                                </span>
                            </td>
                            <td><div class="log-desc">{{ $log->description ?? '—' }}</div></td>
                            <td style="font-size:10.5px;color:#64748b;font-weight:600;">
                                {{ $log->user_id ? '#'.$log->user_id : 'System' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($order_logs->hasPages())
            <div class="ow-pg" style="padding:10px 14px;border-top:1.5px solid #f1f5f9;">
                {{ $order_logs->appends(['item_page'=>$item_logs->currentPage()])->links() }}
            </div>
            @endif
        @else
            <div class="empty"><i class='bx bx-history'></i><p>No order activity recorded yet.</p></div>
        @endif
    </div>

    {{-- ── Item Audit Log ── --}}
    <div class="card">
        <div class="card-head">
            <div class="ch-icon" style="background:#eff6ff;color:#1d4ed8;"><i class='bx bx-list-ul'></i></div>
            <span class="ch-title">Item Audit Log</span>
            <span class="ch-badge">{{ $item_logs->total() }} {{ Str::plural('entry', $item_logs->total()) }}</span>
        </div>
        @if($item_logs->count() > 0)
            <div style="overflow-x:auto;">
                <table class="ltbl">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th style="width:140px;">Date &amp; Time</th>
                            <th style="width:80px;">Action</th>
                            <th style="width:80px;">Item</th>
                            <th>Description</th>
                            <th style="width:70px;">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item_logs as $log)
                        @php $act = strtolower($log->action ?? 'update'); @endphp
                        <tr>
                            <td style="color:#94a3b8;font-size:10px;font-weight:600;">
                                {{ ($item_logs->currentPage()-1)*$item_logs->perPage()+$loop->iteration }}
                            </td>
                            <td>
                                <div style="font-size:11px;font-weight:600;color:#374151;">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                                <div style="font-size:10px;color:#94a3b8;">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i:s A') }}</div>
                            </td>
                            <td>
                                <span class="abadge {{ $act==='create'?'ab-create':($act==='delete'?'ab-delete':'ab-update') }}">
                                    <i class='bx {{ $act==="create"?"bx-plus":($act==="delete"?"bx-trash":"bx-edit") }}'></i>
                                    {{ ucfirst($act) }}
                                </span>
                            </td>
                            <td><span class="log-mid">Item #{{ $log->model_id }}</span></td>
                            <td><div class="log-desc">{{ $log->description ?? '—' }}</div></td>
                            <td style="font-size:10.5px;color:#64748b;font-weight:600;">
                                {{ $log->user_id ? '#'.$log->user_id : 'System' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($item_logs->hasPages())
            <div class="ow-pg" style="padding:10px 14px;border-top:1.5px solid #f1f5f9;">
                {{ $item_logs->appends(['order_page'=>$order_logs->currentPage()])->links() }}
            </div>
            @endif
        @else
            <div class="empty"><i class='bx bx-list-ul'></i><p>No item changes logged yet.</p></div>
        @endif
    </div>

</div>
</div>
@endsection

@section('scripts')
@endsection