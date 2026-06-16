@extends('admin.master')

@section('seo')
    <title>Order #{{ $order->uuid }} | Admin</title>
@endsection

@push('styles')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* ── Stat cards ── */
        .stat-card          { border:none; border-radius:16px; background:#f8f9fa; }
        .stat-icon          { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }

        /* ── Section cards ── */
        .od-card            { border:1px solid #eef0f4; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
        .od-card .card-header { background:transparent; border-bottom:1px solid #eef0f4; padding:1rem 1.25rem; }
        .od-card .card-body   { padding:1.25rem; }

        /* ── Meta rows inside cards ── */
        .meta-row           { display:flex; justify-content:space-between; align-items:center;
                              padding:.55rem 0; border-bottom:1px solid #f3f4f6; font-size:.875rem; }
        .meta-row:last-child{ border-bottom:none; }
        .meta-row .mk       { color:#8a92a4; font-weight:500; }
        .meta-row .mv       { color:#1a1d23; font-weight:500; text-align:right; }

        /* ── Status badges ── */
        .s-pending          { background:#fff8e6; color:#b07800; }
        .s-completed        { background:#e6f9f0; color:#1a7a4a; }
        .s-cancelled        { background:#fdecea; color:#c0392b; }
        .s-processing       { background:#e8f0fe; color:#1a56db; }
        .status-pill        { padding:.3em .85em; border-radius:50px; font-size:.75rem; font-weight:700; letter-spacing:.03em; text-transform:capitalize; }

        /* ── Items table ── */
        .items-tbl th       { font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
                              color:#8a92a4; border-top:none; border-bottom:1px solid #eef0f4 !important; padding:.75rem 1rem; white-space:nowrap; }
        .items-tbl td       { font-size:.875rem; color:#1a1d23; padding:.85rem 1rem; vertical-align:middle; border-color:#f3f4f6; }
        .items-tbl tbody tr:last-child td { border-bottom:none; }
        .items-tbl tbody tr:hover { background:#fafbfc; }

        /* ── GST badge inline ── */
        .gst-pill           { font-size:.68rem; font-weight:700; background:#eef0f4; color:#6b7280;
                              padding:1px 7px; border-radius:50px; margin-left:4px; letter-spacing:.02em; }

        /* ── Summary block ── */
        .summary-row        { display:flex; justify-content:space-between; padding:.5rem 0;
                              font-size:.875rem; border-bottom:1px solid #f3f4f6; }
        .summary-row:last-child { border-bottom:none; }
        .summary-row .sk    { color:#8a92a4; font-weight:500; }
        .summary-row .sv    { color:#1a1d23; font-weight:600; }
        .summary-grand      { font-size:1.05rem; font-weight:700; color:#1a1d23; padding-top:.85rem !important; border-top:2px solid #eef0f4 !important; border-bottom:none !important; }

        /* ── History timeline ── */
        .tl-dot             { width:10px; height:10px; border-radius:50%; background:#dee2e6; flex-shrink:0; margin-top:5px; }
        .tl-line            { width:1px; background:#eef0f4; flex:1; margin:.25rem auto 0; }
    </style>
@endpush

@section('content')

@php
    /* ── GST totals computed once ── */
    $orderTotalExGst = 0;
    $orderTotalGst   = 0;
    foreach ($order->items as $item) {
        $r            = (float) ($item->product?->gst ?? 0);
        $ex           = $r > 0 ? round($item->price / (1 + $r / 100), 2) : $item->price;
        $orderTotalExGst += round($ex * $item->quantity, 2);
        $orderTotalGst   += round(($item->price - $ex) * $item->quantity, 2);
    }
    $grandTotal = $order->total + $order->shipping_charge;
@endphp

<div class="container-fluid px-4 py-4" style="max-width:1280px;">

    {{-- ── Page header ── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="text-muted mb-0" style="font-size:.8rem; letter-spacing:.06em; text-transform:uppercase;">Order</p>
            <h4 class="fw-bold mb-0" style="letter-spacing:-.3px;">#{{ $order->uuid }}</h4>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.orders.index') }}"
               class="btn btn-sm btn-light border fw-semibold px-3" style="border-radius:10px;">
                <i class='bx bx-arrow-back me-1'></i> Orders
            </a>
            <a href="{{ route('admin.orders.pdf', $order) }}" target="_blank"
               class="btn btn-sm fw-semibold px-3" style="border-radius:10px; background:#fdecea; color:#c0392b; border:1px solid #f5c6c2;">
                <i class='bx bx-file-pdf me-1'></i> Download PDF
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}"
               class="btn btn-sm btn-dark fw-semibold px-3" style="border-radius:10px;">
                <i class='bx bx-edit me-1'></i> Edit Order
            </a>
        </div>
    </div>

    {{-- ── Stat strip ── --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f0fe; color:#1a56db;">
                    <i class='bx bx-calendar'></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Placed on</p>
                    <p class="mb-0 fw-bold" style="font-size:.9rem;">{{ $order->created_at->format('d M Y') }}</p>
                    <p class="mb-0 text-muted" style="font-size:.75rem;">{{ $order->created_at->format('H:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff8e6; color:#b07800;">
                    <i class='bx bx-package'></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Status</p>
                    <span class="status-pill s-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;">
                    <i class='bx bx-credit-card'></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Payment</p>
                    <p class="mb-0 fw-bold" style="font-size:.9rem;">{{ strtoupper($order->payment_method) }}</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f3e8ff; color:#7c3aed;">
                    <i class='bx bx-rupee'></i>
                </div>
                <div>
                    <p class="mb-0 text-muted" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Grand Total</p>
                    <p class="mb-0 fw-bold" style="font-size:1.05rem;">₹{{ number_format($grandTotal, 2) }}</p>
                    @if($orderTotalGst > 0)
                        <p class="mb-0 text-muted" style="font-size:.72rem;">incl. ₹{{ number_format($orderTotalGst,2) }} GST</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-3">

        {{-- ── Customer ── --}}
        <div class="col-md-6">
            <div class="card od-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="stat-icon" style="width:32px;height:32px;font-size:16px;background:#e8f0fe;color:#1a56db;">
                        <i class='bx bx-user'></i>
                    </div>
                    <span class="fw-bold" style="font-size:.9rem;">Customer</span>
                </div>
                <div class="card-body">
                    <div class="meta-row">
                        <span class="mk">Name</span>
                        <span class="mv">{{ $order->name }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="mk">Email</span>
                        <span class="mv">{{ $order->email }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="mk">Mobile</span>
                        <span class="mv">{{ $order->mobile }}</span>
                    </div>
                    @if($order->user_id)
                        <div class="meta-row">
                            <span class="mk">Account</span>
                            <span class="mv">
                                <a href="{{ route('admin.users.show', $order->user_id) }}" class="text-primary text-decoration-none fw-semibold">
                                    {{ $order->user->name ?? 'User #'.$order->user_id }}
                                </a>
                            </span>
                        </div>
                    @endif
                    <div class="meta-row">
                        <span class="mk">IP Address</span>
                        <span class="mv text-muted">{{ $order->ip_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Shipping Address ── --}}
        <div class="col-md-6">
            <div class="card od-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="stat-icon" style="width:32px;height:32px;font-size:16px;background:#fef3c7;color:#b45309;">
                        <i class='bx bx-map-pin'></i>
                    </div>
                    <span class="fw-bold" style="font-size:.9rem;">Delivery Address</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between gap-3">
                    <address class="mb-0" style="font-size:.9rem; line-height:1.9; color:#1a1d23;">
                        <strong>{{ $order->name }}</strong><br>
                        {{ $order->address }},<br>
                        {{ $order->locality ? $order->locality.', ' : '' }}{{ $order->city }}<br>
                        {{ $order->state }} – {{ $order->zipcode }}
                    </address>
                    <div>
                        <span class="badge bg-light text-secondary border" style="font-size:.75rem; font-weight:500; border-radius:8px;">
                            <i class='bx bx-phone me-1'></i>{{ $order->mobile }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Order Items ── --}}
    <div class="card od-card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <div class="stat-icon" style="width:32px;height:32px;font-size:16px;background:#e6f9f0;color:#1a7a4a;">
                <i class='bx bx-shopping-bag'></i>
            </div>
            <span class="fw-bold" style="font-size:.9rem;">Order Items</span>
            <span class="ms-auto badge bg-light text-secondary border" style="font-size:.75rem; border-radius:8px;">
                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table items-tbl mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Size</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price <span style="font-weight:400;">(ex. GST)</span></th>
                            <th class="text-end">GST</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Shipping</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                            @php
                                $gstRate   = (float) ($item->product?->gst ?? 0);
                                $exGstUnit = $gstRate > 0
                                    ? round($item->price / (1 + $gstRate / 100), 2)
                                    : $item->price;
                                $gstUnit   = round($item->price - $exGstUnit, 2);
                                $lineTotal = round($item->price * $item->quantity, 2);
                                $shipping  = $item->shipping_charge ?? 0;
                            @endphp
                            <tr>
                                <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('admin.products.show', $item->product) }}"
                                       class="text-decoration-none fw-semibold" style="color:#1a1d23;">
                                        {{ $item->product->name ?? 'Product Not Found' }}
                                    </a>
                                    @if($item->product?->sku)
                                        <br><span class="text-muted" style="font-size:.75rem;">SKU: {{ $item->product->sku }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $item->product->code ?? '—' }}</td>
                                <td>
                                    @if($item->productAttribute?->size)
                                        <span class="badge bg-light text-secondary border" style="border-radius:6px; font-weight:600;">
                                            {{ $item->productAttribute->size }}
                                        </span>
                                    @else —
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold" style="border-radius:6px; font-size:.8rem;">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="text-end">₹{{ number_format($exGstUnit, 2) }}</td>
                                <td class="text-end">
                                    @if($gstRate > 0)
                                        ₹{{ number_format($gstUnit, 2) }}
                                        (<small class="gst-pill">{{ $gstRate }}%</small>)
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-end text-muted">₹{{ number_format($shipping, 2) }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($lineTotal + $shipping, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class='bx bx-package' style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.3;"></i>
                                    No items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Financial Summary ── --}}
    <div class="card od-card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <div class="stat-icon" style="width:32px;height:32px;font-size:16px;background:#f3e8ff;color:#7c3aed;">
                <i class='bx bx-receipt'></i>
            </div>
            <span class="fw-bold" style="font-size:.9rem;">Financial Summary</span>
        </div>
        <div class="card-body">
            <div style="max-width:400px; margin-left:auto;">
                <div class="summary-row">
                    <span class="sk">Subtotal <small>(excl. GST)</small></span>
                    <span class="sv">₹{{ number_format($orderTotalExGst, 2) }}</span>
                </div>
                @if($orderTotalGst > 0)
                    <div class="summary-row">
                        <span class="sk">GST</span>
                        <span class="sv">₹{{ number_format($orderTotalGst, 2) }}</span>
                    </div>
                @endif
                <div class="summary-row">
                    <span class="sk">Shipping</span>
                    <span class="sv">
                        @if($order->shipping_charge > 0)
                            ₹{{ number_format($order->shipping_charge, 2) }}
                        @else
                            <span class="text-success fw-bold">FREE</span>
                        @endif
                    </span>
                </div>
                <div class="summary-row summary-grand">
                    <span>Grand Total <small class="fw-normal text-muted" style="font-size:.75rem;">(incl. GST)</small></span>
                    <span>₹{{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Order History ── --}}
    <div class="card od-card">
        <div class="card-header d-flex align-items-center gap-2">
            <div class="stat-icon" style="width:32px;height:32px;font-size:16px;background:#fef3c7;color:#b45309;">
                <i class='bx bx-history'></i>
            </div>
            <span class="fw-bold" style="font-size:.9rem;">Order History</span>
        </div>
        <div class="card-body">
            @if($order_logs->count() > 0)
                @foreach($order_logs as $log)
                    <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                        <div class="d-flex flex-column align-items-center" style="width:20px;">
                            <div class="tl-dot"></div>
                            @if(!$loop->last)<div class="tl-line"></div>@endif
                        </div>
                        <div class="pb-3" style="{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }} flex:1; width:100%;">
                            <p class="mb-0 text-muted" style="font-size:.75rem;">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </p>
                            <p class="mb-0 fw-medium" style="font-size:.875rem; color:#1a1d23;">
                                {{ $log->description ?? '—' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class='bx bx-history' style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.3;"></i>
                    No history yet.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection