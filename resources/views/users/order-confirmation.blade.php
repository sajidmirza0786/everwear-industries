@extends('users.master')

@section('seo')
    <title>Order Confirmed #{{ $order->id }} | On Jewel</title>
    <meta name="description" content="Thank you for your order at On Jewel. View your order details.">
@endsection

@section('content')
    @if (session('success'))
        <div class="container pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <section style="padding: 36px 0 64px;">
        <div class="container">
            <div style="max-width: 680px; margin: 0 auto;">

                {{-- ── Success Banner ── --}}
                <div style="display:flex;align-items:center;gap:16px;padding:22px 24px;background:var(--surface);border:1px solid var(--line);margin-bottom:24px;">
                    <div style="width:42px;height:42px;flex-shrink:0;background:var(--ink);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-lg" style="color:#fff;font-size:18px;"></i>
                    </div>
                    <div>
                        <div style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--soft-2);margin-bottom:3px;">
                            Order Confirmed</div>
                        <div style="font-family:var(--font-display);font-size:clamp(1.1rem,2.5vw,1.4rem);font-weight:500;color:var(--ink);line-height:1.2;">
                            Thank you, {{ Str::before($order->name, ' ') }}!
                        </div>
                        <div style="font-size:12px;color:var(--soft);margin-top:3px;">
                            Order <strong style="color:var(--ink);">#{{ $order->uuid }}</strong> placed · Confirmation sent
                            to <strong style="color:var(--ink);">{{ $order->email }}</strong>
                        </div>
                    </div>
                </div>

                {{-- ── Two column grid ── --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div class="oc-meta-card">
                        <div class="oc-meta-label">Order ID</div>
                        <div class="oc-meta-value">#{{ $order->uuid }}</div>
                    </div>
                    <div class="oc-meta-card">
                        <div class="oc-meta-label">Status</div>
                        <div class="oc-meta-value">
                            <span class="oc-status-dot"></span> Pending
                        </div>
                    </div>
                    <div class="oc-meta-card">
                        <div class="oc-meta-label">Payment</div>
                        <div class="oc-meta-value" style="text-transform:capitalize;">Pending</div>
                    </div>
                    <div class="oc-meta-card">
                        <div class="oc-meta-label">Date</div>
                        <div class="oc-meta-value">{{ $order->created_at->format('d M Y') }}</div>
                    </div>
                </div>

                {{-- ── Order Items ── --}}
                <div class="oc-section" style="margin-bottom:12px;">
                    <div class="oc-section-head">
                        <i class="bi bi-bag" style="font-size:11px;"></i> Items Ordered
                    </div>
                    <div>
                        @foreach ($order->items as $item)
                            @php
                                $atr          = $item->productAttribute ?? null;
                                $variantLabel = $atr ? $atr->size : null;
                                $gstRate      = (float) ($item->product?->gst ?? 0);
                                $exGstUnit    = $gstRate > 0
                                    ? round($item->price / (1 + $gstRate / 100), 2)
                                    : $item->price;
                                $gstUnit      = round($item->price - $exGstUnit, 2);
                            @endphp
                            <div class="oc-item-row {{ !$loop->last ? 'oc-item-border' : '' }}">
                                {{-- Thumb --}}
                                <div style="width:48px;height:58px;flex-shrink:0;overflow:hidden;background:var(--bg-2);">
                                    @if ($item->product?->image)
                                        <img src="{{ url(\Storage::url($item->product->image)) }}"
                                            style="width:100%;height:100%;object-fit:cover;"
                                            alt="{{ $item->product->name }}">
                                    @else
                                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-image" style="color:var(--soft-2);font-size:16px;"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;font-weight:500;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $item->product->name ?? '—' }}
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-top:4px;flex-wrap:wrap;">
                                        @if ($variantLabel)
                                            <span class="oc-variant-badge">{{ $variantLabel }}</span>
                                        @endif
                                        <span style="font-size:11px;color:var(--soft-2);">Qty: {{ $item->quantity }}</span>
                                        <span style="font-size:11px;color:var(--soft-2);">
                                            ₹{{ number_format($exGstUnit, 2) }}
                                            @if ($gstRate > 0)
                                                + ₹{{ number_format($gstUnit, 2) }} GST ({{ $gstRate }}%)
                                            @endif
                                            /unit
                                        </span>
                                    </div>
                                </div>

                                {{-- Line total (GST-inclusive) --}}
                                <div style="font-size:13px;font-weight:600;color:var(--ink);white-space:nowrap;flex-shrink:0;">
                                    ₹{{ number_format($item->quantity * $item->price, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Billing + Address side by side ── --}}
                @php
                    // Compute GST breakdown from order items
                    $orderTotalExGst = 0;
                    $orderTotalGst   = 0;
                    foreach ($order->items as $item) {
                        $rate         = (float) ($item->product?->gst ?? 0);
                        $exGstUnit    = $rate > 0 ? round($item->price / (1 + $rate / 100), 2) : $item->price;
                        $orderTotalExGst += round($exGstUnit * $item->quantity, 2);
                        $orderTotalGst   += round(($item->price - $exGstUnit) * $item->quantity, 2);
                    }
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">

                    {{-- Bill --}}
                    <div class="oc-section">
                        <div class="oc-section-head">
                            <i class="bi bi-receipt" style="font-size:11px;"></i> Bill
                        </div>
                        <div class="oc-bill-row">
                            <span>Subtotal <span style="font-size:10px;">(excl. GST)</span></span>
                            <span>₹{{ number_format($orderTotalExGst, 2) }}</span>
                        </div>
                        @if ($orderTotalGst > 0)
                            <div class="oc-bill-row">
                                <span>GST</span>
                                <span>₹{{ number_format($orderTotalGst, 2) }}</span>
                            </div>
                        @endif
                        <div class="oc-bill-row">
                            <span>Shipping</span>
                            <span>₹{{ number_format($order->shipping_charge, 2) }}</span>
                        </div>
                        <div class="oc-bill-row oc-bill-total">
                            <span>Total <span style="font-size:10px;font-weight:400;color:var(--soft);">(incl. GST)</span></span>
                            <span>₹{{ number_format($order->total + $order->shipping_charge, 2) }}</span>
                        </div>
                        @if ($orderTotalGst > 0)
                            <div style="padding:4px 16px 10px;font-size:11px;color:var(--soft-2);text-align:right;">
                                ₹{{ number_format($orderTotalGst, 2) }} GST included in total
                            </div>
                        @endif
                    </div>

                    {{-- Address --}}
                    <div class="oc-section">
                        <div class="oc-section-head">
                            <i class="bi bi-geo-alt" style="font-size:11px;"></i> Delivery To
                        </div>
                        <div style="padding:12px 16px;">
                            <div style="font-size:13px;font-weight:500;color:var(--ink);margin-bottom:4px;">
                                {{ $order->name }}
                            </div>
                            <div style="font-size:12px;color:var(--soft);line-height:1.8;">
                                {{ $order->address }}, {{ $order->locality }}<br>
                                {{ $order->city }}, {{ $order->state }} – {{ $order->zipcode }}
                            </div>
                            <div style="font-size:12px;color:var(--soft);margin-top:6px;">
                                <i class="bi bi-phone me-1" style="font-size:10px;"></i>{{ $order->mobile }}
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── CTA ── --}}
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="{{ url('/') }}" class="btn btn-dark"
                        style="flex:1;text-align:center;letter-spacing:.1em;text-transform:uppercase;font-size:12px;padding:13px;">
                        <i class="bi bi-bag me-2"></i>Continue Shopping
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost"
                            style="flex:1;text-align:center;letter-spacing:.1em;text-transform:uppercase;font-size:12px;padding:13px;">
                            <i class="bi bi-list-ul me-2"></i>My Orders
                        </a>
                    @endauth
                </div>

                {{-- Footer note --}}
                <div class="border-start border-2 border-dark ps-3 pt-5">
                    <p class="text-muted mb-0" style="font-size:0.83rem;">
                        To initiate a request, kindly write to us at
                        <a href="mailto:order@everwearindustries.com"
                            class="text-dark fw-medium">order@everwearindustries.com</a>
                        with relevant details and images. Each case is reviewed with care to ensure a fair and prompt
                        resolution.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <style>
        .oc-meta-card {
            background: var(--surface);
            border: 1px solid var(--line);
            padding: 12px 16px;
        }
        .oc-meta-label {
            font-size: 10px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--soft-2);
            margin-bottom: 4px;
        }
        .oc-meta-value {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .oc-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #e6a817;
            display: inline-block;
            flex-shrink: 0;
        }
        .oc-section {
            background: var(--surface);
            border: 1px solid var(--line);
        }
        .oc-section-head {
            font-size: 10px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--soft-2);
            font-weight: 600;
            padding: 10px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .oc-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
        }
        .oc-item-border {
            border-bottom: 1px solid var(--line);
        }
        .oc-variant-badge {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--accent-2);
            background: rgba(0, 0, 0, .05);
            border: 1px solid var(--line-strong);
            padding: 1px 7px;
            border-radius: 2px;
            white-space: nowrap;
        }
        .oc-bill-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 16px;
            font-size: 13px;
            color: var(--soft);
            border-bottom: 1px solid var(--line);
        }
        .oc-bill-row:last-child {
            border-bottom: none;
        }
        .oc-bill-total {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            padding-top: 10px;
            padding-bottom: 10px;
        }
        @media (max-width: 575.98px) {
            .oc-section-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection