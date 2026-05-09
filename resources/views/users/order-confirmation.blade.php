@extends('users.master')

@section('seo')
    <title>Order Confirmation | On Jewel</title>
    <meta name="description" content="Thank you for your order at On Jewel. View your order details.">
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-hero text-center">
    <div class="container">
        <p class="crumbs mb-2">
            <a href="{{ url('/') }}" class="text-soft">Home</a>
            <span class="mx-2 text-soft">—</span>
            <span>Order Confirmation</span>
        </p>
        <h1 class="section-title mb-0">Order Confirmation</h1>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="container pt-4">
    <div class="alert alert-success alert-dismissible fade show rounded-0 border-0 border-start border-4 border-success" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif
@if(session('error'))
<div class="container pt-4">
    <div class="alert alert-danger alert-dismissible fade show rounded-0 border-0 border-start border-4 border-danger" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

{{-- Main Content --}}
<section class="section">
    <div class="container">
        <div class="row justify-content-center g-0">
            <div class="col-12 col-lg-9 col-xl-8">

                {{-- Success Banner --}}
                <div class="oc-success-banner d-flex flex-column flex-sm-row align-items-center gap-4 p-4 p-md-5 mb-4 mb-md-5">
                    <div class="oc-check-circle flex-shrink-0">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="text-center text-sm-start">
                        <p class="section-eyebrow mb-1">Order Confirmed</p>
                        <h2 class="mb-1" style="font-size: clamp(1.6rem, 3vw, 2.2rem);">Thank You for Your Order!</h2>
                        <p class="text-soft mb-0" style="font-size: 14px;">
                            Order <strong class="text-ink">#{{ $order->id }}</strong> has been placed successfully. We'll send updates to <strong class="text-ink">{{ $order->email }}</strong>.
                        </p>
                    </div>
                </div>

                <div class="row g-4 align-items-start">

                    {{-- Left col: Items + Address --}}
                    <div class="col-12 col-md-7">

                        {{-- Order Items --}}
                        <div class="dash-card mb-4">
                            <p class="section-eyebrow mb-3">Order Summary</p>
                            <div class="oc-item-list">
                                @foreach($order->items as $item)
                                <div class="oc-item-row">
                                    <div class="oc-item-info">
                                        <span class="oc-fw-500 d-block" style="font-size: 14px; color: var(--ink);">{{ $item->product->name }}</span>
                                        <span class="text-soft" style="font-size: 12px;">Qty: {{ $item->quantity }}</span>
                                    </div>
                                    <span class="oc-fw-500" style="font-size: 14px; white-space: nowrap;">₹{{ number_format($item->quantity * $item->price, 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="dash-card">
                            <p class="section-eyebrow mb-3">Delivery Address</p>
                            <p class="mb-1 oc-fw-500" style="font-size: 14px;">{{ $order->name }}</p>
                            <p class="text-soft mb-1" style="font-size: 13px; line-height: 1.8;">
                                {{ $order->address }}, {{ $order->locality }}<br>
                                {{ $order->city }}, {{ $order->state }} – {{ $order->zipcode }}
                            </p>
                            <p class="text-soft mb-0" style="font-size: 13px;">
                                <i class="bi bi-phone me-1"></i>{{ $order->mobile }}
                            </p>
                        </div>

                    </div>

                    {{-- Right col: Bill + CTA --}}
                    <div class="col-12 col-md-5">
                        <div class="cart-summary">
                            <p class="section-eyebrow mb-3">Bill</p>

                            <div class="row-line">
                                <span class="text-soft">Subtotal</span>
                                <span>₹{{ number_format($order->total, 2) }}</span>
                            </div>
                            <div class="row-line">
                                <span class="text-soft">Shipping</span>
                                <span>₹{{ number_format($order->shipping_charge, 2) }}</span>
                            </div>
                            <div class="row-line total">
                                <span>Total</span>
                                <span>₹{{ number_format($order->total + $order->shipping_charge, 2) }}</span>
                            </div>

                            <a href="{{ url('/') }}" class="btn btn-dark w-100 mt-4" style="letter-spacing: 0.1em; text-transform: uppercase; font-size: 12px; padding: 14px;">
                                <i class="bi bi-bag me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection