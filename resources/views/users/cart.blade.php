@extends('users.master')

@section('seo')
    <title>Shopping Cart | On Jewel</title>
    <meta name="description"
        content="View and manage your shopping cart. Securely proceed to checkout with your selected items.">
@endsection

@section('content')

    <style>
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 56px!important;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="border-bottom-soft py-2 mb-0">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.75rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('welcome') }}" class="text-decoration-none text-soft">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Cart</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Page Hero --}}
    <div class="page-hero" style="padding: 28px 0 22px;">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Your Selection</span>
                <h1 style="font-size: clamp(1.6rem, 4vw, 2.4rem); margin-bottom: 0;">
                    Shopping Cart
                    @if (count($cartItems))
                        <span style="font-family: var(--font-body); font-size: 14px; font-weight: 500; color: var(--soft); margin-left: 10px;">
                            ({{ count($cartItems) }} item{{ count($cartItems) !== 1 ? 's' : '' }})
                        </span>
                    @endif
                </h1>
            </div>
            <a href="{{ url('/') }}" class="btn btn-ghost btn-sm">
                <i class="bi bi-arrow-left me-2"></i> Continue Shopping
            </a>
        </div>
    </div>

    {{-- Notifications --}}
    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if (session('warnings'))
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                @foreach (session('warnings') as $warning)
                    <div>{{ $warning }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- EMPTY STATE --}}
    @if (!count($cartItems))
        <div class="container section-tight text-center">
            <div style="max-width: 380px; margin: 0 auto;">
                <i class="bi bi-bag-x"
                    style="font-size: 3rem; color: var(--soft-2); display: block; margin-bottom: 16px;"></i>
                <h4 style="font-family: var(--font-display);">Your cart is empty</h4>
                <p class="text-soft" style="font-size: 14px;">Looks like you haven't added anything yet. Explore our
                    collection and find something you love.</p>
                <a href="{{ url('/') }}" class="btn btn-dark mt-3">Browse Collection</a>
            </div>
        </div>
    @else
        {{-- MAIN CART CONTENT --}}
        <div class="container py-4">
            <div class="row g-4 align-items-start">

                {{-- LEFT: Cart Items --}}
                <div class="col-12 col-lg-8">

                    {{-- DESKTOP TABLE --}}
                    <div class="d-none d-md-block">
                        <table class="table align-middle" style="font-size: 13px;">
                            <thead style="border-bottom: 1px solid var(--line-strong);">
                                <tr style="font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--soft);">
                                    <th class="fw-normal py-3 ps-0" style="width: 45%;">Product</th>
                                    <th class="fw-normal py-3 text-end">Price</th>
                                    <th class="fw-normal py-3 text-center">Qty</th>
                                    <th class="fw-normal py-3 text-end">Shipping</th>
                                    <th class="fw-normal py-3 text-end pe-0"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    @php
                                        // Support both DB model (Auth) and session object (guest)
                                        $product   = isset($item->product)
                                            ? $item->product
                                            : \App\Models\Product::find($item->product_id);

                                        $atrId     = $item->product_attribute_id ?? null;
                                        $atr       = $atrId
                                            ? \App\Models\ProductAttribute::find($atrId)
                                            : null;

                                        // Variant label: size + optional description
                                        $variantLabel = $atr ? $atr->size : null;
                                        $subtotal  = $item->quantity * $item->price;
                                    @endphp

                                    @if (!$product) @continue @endif

                                    <tr style="border-bottom: 1px solid var(--line);">
                                        {{-- Product Info --}}
                                        <td class="py-4 ps-0">
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="{{ route('listing', $product) }}" class="flex-shrink-0"
                                                    style="display:block;width:72px;height:90px;overflow:hidden;background:var(--surface);">
                                                    <img src="{{ url(\Storage::url($product->image ?? '')) }}"
                                                        style="width:100%;height:100%;object-fit:cover;transition:transform .6s ease;"
                                                        onmouseover="this.style.transform='scale(1.05)'"
                                                        onmouseout="this.style.transform='scale(1)'"
                                                        alt="{{ $product->name }}">
                                                </a>
                                                <div>
                                                    <a href="{{ route('listing', $product) }}"
                                                        class="product-title d-block mb-1">{{ $product->name }}</a>

                                                    {{-- Variant badge --}}
                                                    @if ($variantLabel)
                                                        <span class="cart-variant-badge">
                                                            <i class="bi bi-tag" style="font-size:9px;"></i>
                                                            {{ $variantLabel }}
                                                        </span>
                                                    @endif

                                                    <span style="font-size:11px;color:var(--soft-2);letter-spacing:.08em;text-transform:uppercase;display:block;margin-top:4px;">
                                                        SKU #{{ $product->code ?? $product->id }}
                                                    </span>

                                                    {{-- Subtotal on desktop --}}
                                                    <span style="font-size:12px;color:var(--soft);margin-top:3px;display:block;">
                                                        Subtotal: <strong style="color:var(--ink);">₹{{ number_format($subtotal, 2) }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Unit Price --}}
                                        <td class="text-end" style="font-weight:600;white-space:nowrap;">
                                            ₹{{ number_format($item->price, 2) }}
                                        </td>

                                        {{-- Qty stepper --}}
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('cart.update') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_attribute_id" value="{{ $atrId ?? '' }}">
                                                <div class="qty-stepper mx-auto" style="width:110px;">
                                                    <button type="button" onclick="updateQty(this,'down')" aria-label="Decrease">−</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                        max="{{ $atr ? $atr->stock : $product->stock }}" readonly>
                                                    <button type="button" onclick="updateQty(this,'up')" aria-label="Increase">+</button>
                                                </div>
                                            </form>
                                        </td>

                                        {{-- Shipping --}}
                                        <td class="text-end" style="color:var(--soft);white-space:nowrap;">
                                            ₹{{ number_format($item->shipping_charge, 2) }}
                                        </td>

                                        {{-- Remove --}}
                                        <td class="text-end pe-0">
                                            <form method="POST" action="{{ route('cart.remove') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_attribute_id" value="{{ $atrId ?? '' }}">
                                                <button type="submit" class="icon-btn"
                                                    style="width:36px;height:36px;font-size:15px;" title="Remove item">
                                                    <i class="bi bi-trash" style="color:var(--soft);"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-md-none d-flex flex-column gap-3">
                        @foreach ($cartItems as $item)
                            @php
                                $product  = isset($item->product)
                                    ? $item->product
                                    : \App\Models\Product::find($item->product_id);

                                $atrId    = $item->product_attribute_id ?? null;
                                $atr      = $atrId
                                    ? \App\Models\ProductAttribute::find($atrId)
                                    : null;

                                $variantLabel = $atr ? $atr->size : null;
                                $subtotal = $item->quantity * $item->price;
                            @endphp

                            @if (!$product) @continue @endif

                            <div style="background:var(--bg);border:1px solid var(--line);padding:14px;">
                                <div class="d-flex gap-3">
                                    {{-- Image --}}
                                    <a href="{{ route('listing', $product) }}" class="flex-shrink-0"
                                        style="display:block;width:80px;height:100px;overflow:hidden;background:var(--surface);">
                                        <img src="{{ url(\Storage::url($product->image ?? '')) }}"
                                            style="width:100%;height:100%;object-fit:cover;"
                                            alt="{{ $product->name }}">
                                    </a>

                                    {{-- Details --}}
                                    <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                        <div>
                                            <a href="{{ route('listing', $product) }}"
                                                class="product-title d-block mb-1"
                                                style="font-size:13px;">{{ $product->name }}</a>

                                            {{-- Variant badge --}}
                                            @if ($variantLabel)
                                                <span class="cart-variant-badge mb-1">
                                                    <i class="bi bi-tag" style="font-size:9px;"></i>
                                                    {{ $variantLabel }}
                                                </span>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span style="font-size:14px;font-weight:600;">
                                                    ₹{{ number_format($item->price, 2) }}
                                                    <span style="font-size:11px;font-weight:400;color:var(--soft);">/ unit</span>
                                                </span>
                                                <span style="font-size:12px;color:var(--soft);">
                                                    +₹{{ number_format($item->shipping_charge, 2) }} ship
                                                </span>
                                            </div>

                                            <div style="font-size:12px;color:var(--soft);margin-top:2px;">
                                                Subtotal: <strong style="color:var(--ink);">₹{{ number_format($subtotal, 2) }}</strong>
                                            </div>
                                        </div>

                                        {{-- Qty + Remove --}}
                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <form method="POST" action="{{ route('cart.update') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_attribute_id" value="{{ $atrId ?? '' }}">
                                                <div class="qty-stepper">
                                                    <button type="button" onclick="updateQty(this,'down')">−</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                        min="1" max="{{ $atr ? $atr->stock : $product->stock }}" readonly>
                                                    <button type="button" onclick="updateQty(this,'up')">+</button>
                                                </div>
                                            </form>

                                            <form method="POST" action="{{ route('cart.remove') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_attribute_id" value="{{ $atrId ?? '' }}">
                                                <button type="submit"
                                                    style="background:transparent;border:none;font-size:12px;color:var(--soft);letter-spacing:.06em;text-transform:uppercase;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:5px;">
                                                    <i class="bi bi-trash" style="font-size:14px;"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- RIGHT: Order Summary --}}
                <div class="col-12 col-lg-4">
                    <div class="cart-summary d-none d-lg-block">
                        <h6 class="section-eyebrow mb-4">Order Summary</h6>

                        <div class="row-line">
                            <span style="color:var(--soft);font-size:14px;">Subtotal</span>
                            <span style="font-size:14px;">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="row-line">
                            <span style="color:var(--soft);font-size:14px;">Shipping</span>
                            <span style="font-size:14px;">₹{{ number_format($totalShipping, 2) }}</span>
                        </div>
                        <div class="row-line total">
                            <span>Total</span>
                            <span>₹{{ number_format($total + $totalShipping, 2) }}</span>
                        </div>

                        <a href="{{ route('checkout') }}" class="btn btn-dark w-100 mt-4"
                            style="letter-spacing:.1em;text-transform:uppercase;font-size:13px;padding:16px;">
                            Proceed to Checkout
                        </a>
                        <p style="font-size:11px;color:var(--soft-2);text-align:center;margin-top:12px;letter-spacing:.06em;">
                            Secure checkout · All taxes included
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <div class="d-lg-none" style="height:130px;" aria-hidden="true"></div>

        {{-- MOBILE STICKY FOOTER --}}
        <div class="d-lg-none px-3"
            style="position:fixed;bottom:0;left:0;right:0;z-index:91;background:var(--bg);border-top:1px solid var(--line-strong);box-shadow:0 -2px 16px rgba(26,20,16,.09);">
            <div class="d-flex align-items-center justify-content-between gap-3" style="padding:10px 0;">
                <div>
                    <div style="font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--soft-2);margin-bottom:1px;">Total</div>
                    <div style="font-family:var(--font-display);font-size:22px;font-weight:500;line-height:1.1;">
                        ₹{{ number_format($total + $totalShipping, 2) }}</div>
                    <div style="font-size:11px;color:var(--soft);margin-top:1px;">
                        incl. ₹{{ number_format($totalShipping, 2) }} shipping
                    </div>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-dark flex-shrink-0"
                    style="letter-spacing:.1em;text-transform:uppercase;font-size:12px;padding:14px 22px;">
                    Checkout <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    @endif

    <style>
        /* Variant badge pill */
        .cart-variant-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent-2);
            background: rgba(0,0,0,0.05);
            border: 1px solid var(--line-strong);
            padding: 2px 8px;
            border-radius: 2px;
            white-space: nowrap;
        }
    </style>

    <script>
        function updateQty(btn, action) {
            const input = btn.parentNode.querySelector('input[name="quantity"]');
            const max   = parseInt(input.getAttribute('max')) || 9999;
            if (action === 'up') {
                if (parseInt(input.value) < max) input.stepUp();
                else return; // silently block beyond stock
            } else {
                if (input.value > 1) input.stepDown();
                else return;
            }
            btn.closest('form').submit();
        }
    </script>
@endsection