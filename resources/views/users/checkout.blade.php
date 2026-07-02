@extends('users.master')

@section('seo')
    <title>Checkout | On Jewel</title>
    <meta name="description" content="Complete your purchase at On Jewel with our secure checkout process.">
@endsection

@section('content')

    {{-- Breadcrumb --}}
    <div class="border-bottom-soft py-2 mb-0">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:0.75rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-decoration-none text-soft">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('cart') }}" class="text-decoration-none text-soft">Cart</a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Checkout</li>
                </ol>
            </nav>
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
                {{ session('warnings') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Page Hero --}}
    <div class="page-hero" style="padding: 28px 0 22px;">
        <div class="container">
            <span class="section-eyebrow">Almost There</span>
            <h1 style="font-size: clamp(1.6rem, 4vw, 2.4rem); margin-bottom: 0;">Checkout</h1>
        </div>
    </div>

    {{-- Steps indicator --}}
    <div class="border-bottom-soft" style="background: var(--surface);">
        <div class="container">
            <div class="d-flex align-items-center"
                style="padding: 12px 0; gap: 0; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 500;">
                <span style="color: var(--soft-2);">
                    <i class="bi bi-check-circle-fill me-1" style="color: var(--accent);"></i> Cart
                </span>
                <span style="color: var(--line-strong); margin: 0 12px;">——</span>
                <span style="color: var(--ink);">
                    <i class="bi bi-circle-fill me-1"
                        style="color: var(--accent); font-size: 8px; vertical-align: 1px;"></i> Shipping
                </span>
                <span style="color: var(--line-strong); margin: 0 12px;">——</span>
                <span style="color: var(--soft-2);">Confirmation</span>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         Coupon-specific styles — uses the page's existing CSS variables
         (--accent, --accent-2, --line, --soft, --surface, --bg, --ink)
         so it matches the rest of the checkout design exactly.
    ════════════════════════════════════════════════════════════════════ --}}
    <style>
        .coupon-box {
            border: 1px dashed var(--line-strong);
            background: var(--bg);
            padding: 14px 16px;
            margin-bottom: 28px;
        }
        .coupon-box.applied {
            border-style: solid;
            border-color: var(--accent);
            background: var(--surface);
        }
        .coupon-row { display: flex; gap: 8px; }
        .coupon-input {
            flex: 1; height: 40px; border: 1px solid var(--line-strong);
            background: var(--bg); padding: 0 12px; font-size: 13px;
            text-transform: uppercase; letter-spacing: 0.04em;
        }
        .coupon-input:focus { outline: none; border-color: var(--accent); }
        .coupon-apply-btn {
            height: 40px; padding: 0 18px; border: 1px solid var(--ink);
            background: var(--ink); color: var(--bg);
            font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;
            font-weight: 600; cursor: pointer; white-space: nowrap;
        }
        .coupon-apply-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .coupon-browse-link {
            display: inline-flex; align-items: center; gap: 6px; margin-top: 10px;
            font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase;
            color: var(--accent-2); background: none; border: none; padding: 0;
            cursor: pointer; font-weight: 600;
        }
        .coupon-browse-link:hover { text-decoration: underline; }
        .coupon-msg { font-size: 12px; margin-top: 8px; display: none; }
        .coupon-msg.error   { color: #b3261e; }
        .coupon-msg.success { color: #1e7a4a; }

        .coupon-applied-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .coupon-applied-code { font-weight: 600; font-size: 13px; letter-spacing: 0.04em; color: var(--ink); }
        .coupon-applied-desc { font-size: 11px; color: var(--soft); margin-top: 2px; }
        .coupon-applied-save { font-size: 12px; font-weight: 600; color: #1e7a4a; margin-top: 4px; }
        .coupon-remove-btn {
            background: none; border: none; color: var(--soft); cursor: pointer;
            font-size: 16px; line-height: 1; padding: 2px;
        }
        .coupon-remove-btn:hover { color: #b3261e; }

        /* Summary discount row */
        .row-line.discount span:last-child { color: #1e7a4a; }

        /* Modal */
        .coupon-modal-overlay {
            position: fixed; inset: 0; background: rgba(26,20,16,.45);
            display: none; align-items: flex-end; justify-content: center; z-index: 1080;
        }
        .coupon-modal-overlay.open { display: flex; }
        .coupon-modal {
            background: var(--bg); width: 100%; max-width: 480px;
            max-height: 78vh; display: flex; flex-direction: column;
            border: 1px solid var(--line-strong);
        }
        @media (min-width: 768px) { .coupon-modal-overlay { align-items: center; } }
        .coupon-modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 18px; border-bottom: 1px solid var(--line);
        }
        .coupon-modal-title { font-size: 13px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600; }
        .coupon-modal-close { background: none; border: none; font-size: 18px; color: var(--soft); cursor: pointer; }
        .coupon-modal-body { overflow-y: auto; flex: 1; }
        .coupon-list-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 18px; border-bottom: 1px solid var(--line); gap: 12px;
        }
        .coupon-list-code { font-weight: 600; font-size: 13px; letter-spacing: 0.03em; }
        .coupon-list-desc { font-size: 11px; color: var(--soft); margin-top: 2px; }
        .coupon-list-meta { font-size: 10px; color: var(--soft-2); margin-top: 3px; }
        .coupon-list-badge {
            background: var(--surface); border: 1px solid var(--accent); color: var(--accent-2);
            font-size: 10px; font-weight: 700; letter-spacing: 0.04em; padding: 4px 8px;
        }
        .coupon-list-use-btn {
            font-size: 10px; letter-spacing: 0.06em; text-transform: uppercase; font-weight: 600;
            color: var(--ink); background: none; border: 1px solid var(--line-strong);
            padding: 6px 10px; cursor: pointer; white-space: nowrap;
        }
        .coupon-list-use-btn:hover { background: var(--ink); color: var(--bg); }
        .coupon-modal-empty, .coupon-modal-loading { padding: 40px 18px; text-align: center; color: var(--soft-2); font-size: 13px; }
    </style>

    <div class="container py-4">
        <div class="row g-4 align-items-start">

            {{-- LEFT: Checkout Form --}}
            <div class="col-12 col-lg-7">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="coupon_code" id="couponCodeHidden" value="{{ session('coupon.code') }}">

                    {{-- Auth / Email Block --}}
                    @guest
                        <div class="mb-4">
                            <div
                                style="font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--accent-2); font-weight: 600; margin-bottom: 14px;">
                                Contact
                            </div>
                            <div>
                                <label for="email"
                                    style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Email
                                    Address</label>
                                <input type="email" id="email" name="email" class="form-control" required
                                    placeholder="your@email.com">
                                <small style="font-size: 11px; color: var(--soft-2); margin-top: 5px; display: block;">We'll
                                    send your order confirmation to this address.</small>
                            </div>
                        </div>
                        <div style="border-top: 1px solid var(--line); margin-bottom: 28px;"></div>
                    @else
                        <div class="mb-4"
                            style="background: var(--surface); border: 1px solid var(--line); padding: 14px 16px;">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div
                                        style="font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--soft-2); margin-bottom: 3px;">
                                        Logged in as</div>
                                    <div style="font-weight: 500; font-size: 14px;">{{ auth()->user()->email }}</div>
                                    <div style="font-size: 11px; color: var(--soft); margin-top: 2px;">Order updates will be
                                        sent here.</div>
                                </div>
                                <button type="button"
                                    onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                                    style="background: transparent; border: 1px solid var(--line-strong); color: var(--soft); font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; padding: 7px 14px; cursor: pointer; white-space: nowrap; transition: all 0.2s;"
                                    onmouseover="this.style.background='var(--ink)'; this.style.color='var(--bg)'; this.style.borderColor='var(--ink)';"
                                    onmouseout="this.style.background='transparent'; this.style.color='var(--soft)'; this.style.borderColor='var(--line-strong)';">
                                    <i class="bi bi-box-arrow-right me-1"></i> Sign out
                                </button>
                            </div>
                        </div>
                    @endguest

                    {{-- Shipping Section --}}
                    <div
                        style="font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--accent-2); font-weight: 600; margin-bottom: 16px;">
                        Shipping Address
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="name"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Full
                                Name</label>
                            <input type="text" id="name" name="name" class="form-control" required
                                value="{{ old('name', Auth::user()->name ?? '') }}" placeholder="Your full name">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="mobile"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Mobile</label>
                            <input type="text" id="mobile" name="mobile" class="form-control" required
                                value="{{ old('mobile', auth()->user()->mobile ?? '') }}"
                                placeholder="10-digit mobile number">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="state"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">State</label>
                            <select id="state" name="state" class="form-control" required>
                                <option value="">Select State</option>
                                @foreach (App\Models\State::all() as $state)
                                    <option value="{{ $state->name }}"
                                        {{ Auth::check() && Auth::user()->state == $state->name ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="city"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">City</label>
                            <input type="text" id="city" name="city" class="form-control" required
                                value="{{ old('city', Auth::user()->city ?? '') }}" placeholder="City">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="zipcode"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Zipcode</label>
                            <input type="text" id="zipcode" name="zipcode" class="form-control" required
                                value="{{ old('zipcode', Auth::user()->zipcode ?? '') }}" placeholder="Pincode">
                        </div>
                        <div class="col-12">
                            <label for="locality"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Locality
                                / Area</label>
                            <input type="text" id="locality" name="locality" class="form-control" required
                                value="{{ old('locality', Auth::user()->locality ?? '') }}"
                                placeholder="Colony, area or landmark">
                        </div>
                        <div class="col-12">
                            <label for="address"
                                style="font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--soft); font-weight: 600; display: block; margin-bottom: 6px;">Full
                                Address</label>
                            <textarea id="address" name="address" rows="3" class="form-control" required
                                placeholder="House no., building name, street name…">{{ old('address', Auth::user()->address ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Payment Method (COD hidden input) --}}
                    <label for="pay_cod"
                        style="display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; border: 1px solid var(--line); background: var(--bg); cursor: pointer; margin-bottom: 10px; transition: border-color 0.2s;">
                        <input type="hidden" id="pay_cod" name="payment_method" value="cod" checked required
                            style="margin-top: 2px; accent-color: var(--accent);">
                    </label>

                    {{-- Submit — desktop only; mobile uses sticky bar --}}
                    <div class="d-none d-lg-block">
                        <button type="submit" class="btn btn-dark w-100"
                            style="letter-spacing: 0.1em; text-transform: uppercase; font-size: 13px; padding: 16px;">
                            Place Order <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                        <p
                            style="font-size: 11px; color: var(--soft-2); text-align: center; margin-top: 10px; letter-spacing: 0.06em;">
                            <i class="bi bi-lock me-1"></i> Secure checkout · All taxes included
                        </p>
                    </div>

                </form>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="col-12 col-lg-5">
                <div class="cart-summary d-none d-lg-block">
                    <h6 class="section-eyebrow mb-4">Order Summary</h6>

                    {{-- Items list --}}
                    @forelse($cartItems as $item)
                        @php
                            $product      = isset($item->product)
                                ? $item->product
                                : \App\Models\Product::find($item->product_id);
                            $atrId        = $item->product_attribute_id ?? null;
                            $atr          = $atrId ? \App\Models\ProductAttribute::find($atrId) : null;
                            $variantLabel = $atr ? $atr->size : null;
                            $itemTotal    = $item->quantity * $item->price; // GST-inclusive line total (excl. shipping)
                        @endphp
                        @if (!$product)
                            @continue
                        @endif
                        <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--line);">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $product->name }}
                                </div>
                                @if ($variantLabel)
                                    <div style="margin-top:3px;">
                                        <span style="font-size:10px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent-2);background:rgba(0,0,0,.05);border:1px solid var(--line-strong);padding:1px 7px;border-radius:2px;">
                                            {{ $variantLabel }}
                                        </span>
                                    </div>
                                @endif
                                <div style="font-size:11px;color:var(--soft);margin-top:3px;">
                                    Qty: {{ $item->quantity }}
                                    &nbsp;·&nbsp;
                                    @if (($item->gst_rate ?? 0) > 0)
                                        GST ({{ $item->gst_rate }}%): ₹{{ number_format($item->gst_amount, 2) }}
                                        &nbsp;·&nbsp;
                                    @endif
                                    {{-- Ship: ₹{{ number_format($item->shipping_charge, 2) }} --}}
                                </div>
                            </div>
                            <div style="font-size:13px;font-weight:600;white-space:nowrap;flex-shrink:0;">
                                ₹{{ number_format($itemTotal + $item->shipping_charge, 2) }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:24px 0;color:var(--soft);font-size:14px;">Your cart is empty.</div>
                    @endforelse

                    {{-- ── Coupon box (desktop) ── --}}
                    <div class="coupon-box mt-3" id="couponBoxDesktop" data-variant="desktop">
                        <div id="couponFormStateDesktop">
                            <div class="coupon-row">
                                <input type="text" class="coupon-input coupon-code-input" placeholder="Enter coupon code" maxlength="50">
                                <button type="button" class="coupon-apply-btn coupon-apply-btn-trigger">Apply</button>
                            </div>
                            <div class="coupon-msg coupon-msg-target"></div>
                            {{-- ── Browse-coupons link kept for later re-enable — logic untouched, just hidden ──
                            <button type="button" class="coupon-browse-link coupon-browse-trigger">
                                <i class="bi bi-tag"></i> View available coupons
                            </button> --}}
                        </div>
                        <div id="couponAppliedStateDesktop" style="display:none;">
                            <div class="coupon-applied-row">
                                <div>
                                    <div class="coupon-applied-code applied-code-target"></div>
                                    <div class="coupon-applied-desc applied-desc-target"></div>
                                    <div class="coupon-applied-save applied-save-target"></div>
                                </div>
                                <button type="button" class="coupon-remove-btn coupon-remove-trigger" title="Remove coupon">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Totals breakdown --}}
                    <div class="row-line mt-3">
                        <span style="color:var(--soft);font-size:14px;">
                            Subtotal <span style="font-size:11px;">(excl. GST)</span>
                        </span>
                        <span style="font-size:14px;" data-total="subtotalExGst">₹{{ number_format($totalExGst, 2) }}</span>
                    </div>

                    {{-- Discount row — hidden until a coupon is applied --}}
                    <div class="row-line discount" id="discountRowDesktop" style="display:none;">
                        <span style="color:var(--soft);font-size:14px;">
                            Discount <span class="discount-code-label-desktop" style="font-size:11px;"></span>
                        </span>
                        <span style="font-size:14px;" id="discountAmountDesktop">−₹0.00</span>
                    </div>

                    @if ($totalGst > 0)
                        <div class="row-line">
                            <span style="color:var(--soft);font-size:14px;">
                                GST
                                <i class="bi bi-info-circle" style="font-size:11px;cursor:help;"
                                   title="Goods & Services Tax included in your order"></i>
                            </span>
                            <span style="font-size:14px;" data-total="gst">₹{{ number_format($totalGst, 2) }}</span>
                        </div>
                    @endif

                    <div class="row-line">
                        <span style="color:var(--soft);font-size:14px;">Shipping</span>
                        <span style="font-size:14px;" data-total="shipping">₹{{ number_format($totalShipping, 2) }}</span>
                    </div>

                    <div style="border-top:1px solid var(--line-strong);margin:8px 0;"></div>

                    <div class="row-line total">
                        <span>Total <span style="font-size:11px;font-weight:400;color:var(--soft);">(incl. GST)</span></span>
                        <span id="grandTotalDesktop">₹{{ number_format($total + $totalShipping, 2) }}</span>
                    </div>

                    @if ($totalGst > 0)
                        <p style="font-size:11px;color:var(--soft-2);text-align:right;margin-top:4px;letter-spacing:.04em;">
                            ₹{{ number_format($totalGst, 2) }} GST included in total
                        </p>
                    @endif
                </div>

                {{-- Mobile inline summary (accordion) --}}
                <div class="d-lg-none" style="border:1px solid var(--line);background:var(--surface);">
                    <button type="button"
                        onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'block' : 'none'; this.querySelector('.toggle-icon').style.transform = this.nextElementSibling.style.display === 'none' ? 'rotate(0deg)' : 'rotate(180deg)';"
                        style="width:100%;background:transparent;border:none;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;font-size:13px;font-weight:500;letter-spacing:0.04em;">
                        <span>
                            <i class="bi bi-bag me-2" style="color:var(--accent);"></i>
                            Show order summary
                            <strong style="margin-left:8px;" id="mobileSummaryTotalLabel">₹{{ number_format($total + $totalShipping, 2) }}</strong>
                        </span>
                        <i class="bi bi-chevron-down toggle-icon"
                            style="transition:transform 0.2s;font-size:14px;color:var(--soft);"></i>
                    </button>

                    <div style="display:none;border-top:1px solid var(--line);padding:12px 16px;">

                        {{-- Items --}}
                        @forelse($cartItems as $item)
                            @php
                                $product      = isset($item->product)
                                    ? $item->product
                                    : \App\Models\Product::find($item->product_id);
                                $atrId        = $item->product_attribute_id ?? null;
                                $atr          = $atrId ? \App\Models\ProductAttribute::find($atrId) : null;
                                $variantLabel = $atr ? $atr->size : null;
                                $itemTotal    = $item->quantity * $item->price;
                            @endphp
                            @if (!$product)
                                @continue
                            @endif
                            <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid var(--line);font-size:13px;">
                                <div>
                                    <div style="font-weight:500;">{{ $product->name }}</div>
                                    @if ($variantLabel)
                                        <span style="font-size:10px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent-2);background:rgba(0,0,0,.05);border:1px solid var(--line-strong);padding:1px 7px;border-radius:2px;display:inline-block;margin-top:2px;">
                                            {{ $variantLabel }}
                                        </span>
                                    @endif
                                    <div style="color:var(--soft);font-size:11px;margin-top:3px;">
                                        Qty: {{ $item->quantity }}
                                        @if (($item->gst_rate ?? 0) > 0)
                                            · GST ({{ $item->gst_rate }}%): ₹{{ number_format($item->gst_amount, 2) }}
                                        @endif
                                        {{-- · Ship: ₹{{ number_format($item->shipping_charge, 2) }} --}}
                                    </div>
                                </div>
                                <div style="font-weight:600;white-space:nowrap;">
                                    ₹{{ number_format($itemTotal + $item->shipping_charge, 2) }}
                                </div>
                            </div>
                        @empty
                        @endforelse

                        {{-- ── Coupon box (mobile) ── --}}
                        <div class="coupon-box mt-3" id="couponBoxMobile" data-variant="mobile">
                            <div id="couponFormStateMobile">
                                <div class="coupon-row">
                                    <input type="text" class="coupon-input coupon-code-input" placeholder="Enter coupon code" maxlength="50">
                                    <button type="button" class="coupon-apply-btn coupon-apply-btn-trigger">Apply</button>
                                </div>
                                <div class="coupon-msg coupon-msg-target"></div>
                                {{-- ── Browse-coupons link kept for later re-enable — logic untouched, just hidden ──
                                     (matches the desktop box above — both commented the same way)
                                <button type="button" class="coupon-browse-link coupon-browse-trigger">
                                    <i class="bi bi-tag"></i> View available coupons
                                </button> --}}
                            </div>
                            <div id="couponAppliedStateMobile" style="display:none;">
                                <div class="coupon-applied-row">
                                    <div>
                                        <div class="coupon-applied-code applied-code-target"></div>
                                        <div class="coupon-applied-desc applied-desc-target"></div>
                                        <div class="coupon-applied-save applied-save-target"></div>
                                    </div>
                                    <button type="button" class="coupon-remove-btn coupon-remove-trigger" title="Remove coupon">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Subtotal (ex-GST) --}}
                        <div style="display:flex;justify-content:space-between;padding:10px 0 4px;font-size:13px;color:var(--soft);">
                            <span>Subtotal <span style="font-size:11px;">(excl. GST)</span></span>
                            <span data-total="subtotalExGst">₹{{ number_format($totalExGst, 2) }}</span>
                        </div>

                        {{-- GST row --}}
                        @if ($totalGst > 0)
                            <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:var(--soft);">
                                <span>GST</span>
                                <span data-total="gst">₹{{ number_format($totalGst, 2) }}</span>
                            </div>
                        @endif

                        {{-- Discount row (mobile) --}}
                        <div style="display:none;justify-content:space-between;padding:4px 0;font-size:13px;color:#1e7a4a;" id="discountRowMobile">
                            <span>Discount <span class="discount-code-label-mobile" style="font-size:11px;"></span></span>
                            <span id="discountAmountMobile">−₹0.00</span>
                        </div>

                        {{-- Shipping --}}
                        <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:var(--soft);">
                            <span>Shipping</span>
                            <span data-total="shipping">₹{{ number_format($totalShipping, 2) }}</span>
                        </div>

                        {{-- Grand Total --}}
                        <div style="display:flex;justify-content:space-between;padding:10px 0 4px;font-size:16px;font-weight:600;border-top:1px solid var(--line-strong);margin-top:6px;">
                            <span>Total <span style="font-size:11px;font-weight:400;color:var(--soft);">(incl. GST)</span></span>
                            <span id="grandTotalMobile">₹{{ number_format($total + $totalShipping, 2) }}</span>
                        </div>

                        @if ($totalGst > 0)
                            <p style="font-size:11px;color:var(--soft-2);text-align:right;margin-top:2px;">
                                ₹{{ number_format($totalGst, 2) }} GST included
                            </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MOBILE: spacer + sticky Place Order bar --}}
    <div class="d-lg-none" style="height:90px;" aria-hidden="true"></div>
    <div class="d-lg-none p-2"
        style="position:fixed;bottom:0;left:0;right:0;z-index:91;background:var(--bg);border-top:1px solid var(--line-strong);box-shadow:0 -2px 16px rgba(26,20,16,0.09);">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div style="line-height:1.25;">
                <div style="font-size:10px;letter-spacing:0.14em;text-transform:uppercase;color:var(--soft-2);">
                    Total <span style="text-transform:none;letter-spacing:0;">(incl. GST)</span>
                </div>
                <div style="font-family:var(--font-display);font-size:18px;font-weight:500;" id="stickyBarTotal">
                    ₹{{ number_format($total + $totalShipping, 2) }}
                </div>
                @if ($totalGst > 0)
                    <div style="font-size:11px;color:var(--soft);">
                        ₹{{ number_format($totalGst, 2) }} GST
                        @if ($totalShipping > 0) · @endif
                        @if ($totalShipping > 0) ₹{{ number_format($totalShipping, 2) }} ship @endif
                    </div>
                @endif
            </div>
            <button type="submit" form="checkoutForm" class="btn btn-dark flex-shrink-0"
                style="letter-spacing:0.1em;text-transform:uppercase;font-size:12px;padding:10px 20px;">
                Place Order <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>
    </div>

    {{-- Hidden logout form --}}
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    {{-- ── Coupon modal (shared by desktop + mobile triggers) ── --}}
    <div class="coupon-modal-overlay" id="couponModalOverlay">
        <div class="coupon-modal">
            <div class="coupon-modal-header">
                <span class="coupon-modal-title">Available Coupons</span>
                <button type="button" class="coupon-modal-close" id="couponModalClose"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="coupon-modal-body" id="couponModalBody">
                <div class="coupon-modal-loading">Loading coupons…</div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(function(r) {
                    r.closest('label').style.borderColor = 'var(--line)';
                    r.closest('label').style.background  = 'var(--bg)';
                });
                this.closest('label').style.borderColor = 'var(--accent)';
                this.closest('label').style.background  = 'var(--surface)';
            });
        });
    </script>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function() {
            const desktopBtn = this.querySelector('button[type="submit"]');
            const mobileBtn  = document.querySelector('button[form="checkoutForm"]');
            const loadingHtml = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Placing Order…`;
            if (desktopBtn) { desktopBtn.disabled = true; desktopBtn.innerHTML = loadingHtml; }
            if (mobileBtn)  { mobileBtn.disabled  = true; mobileBtn.innerHTML  = loadingHtml; }
        });
    </script>

    {{-- ════════════════════════════════════════════════════════════════════
         COUPON LOGIC — apply / remove / browse + live total recalculation.

         IMPORTANT: totals are NEVER recomputed client-side from the discount
         alone anymore. Discounting the ex-GST subtotal also changes the GST
         amount (GST is a % of the taxable value), so the only correct source
         for taxable_value / gst_amount / grand_total is the server response
         from /checkout/coupon/apply (and the session-restored values on
         reload). The JS below simply paints whatever numbers the server
         computed — it never re-derives them — so the preview always matches
         exactly what CheckoutController@store will actually charge.
    ════════════════════════════════════════════════════════════════════ --}}
    <script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value;

        // Base order figures rendered by the server (no discount applied)
        const baseSubtotalExGst = {{ (float) $totalExGst }};
        const baseGst           = {{ (float) $totalGst }};
        const baseShipping      = {{ (float) $totalShipping }};
        const baseTotal         = {{ (float) ($total + $totalShipping) }};

        const hiddenCouponInput = document.getElementById('couponCodeHidden');
        const modalOverlay = document.getElementById('couponModalOverlay');
        const modalBody = document.getElementById('couponModalBody');
        const modalClose = document.getElementById('couponModalClose');

        let couponsLoaded = false;

        // Current authoritative totals — start at the no-discount baseline,
        // and only ever get replaced wholesale by a server response.
        let appliedDiscount = 0;
        let appliedGst      = baseGst;
        let appliedGrand    = baseTotal;

        function formatRupee(n) {
            return '₹' + Number(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // ── Repaint every total on the page from the current state ──
        function recalcTotals() {
            document.querySelectorAll('[data-total="gst"]').forEach(function (el) {
                el.textContent = formatRupee(appliedGst);
            });

            document.getElementById('grandTotalDesktop').textContent = formatRupee(appliedGrand);
            document.getElementById('grandTotalMobile').textContent  = formatRupee(appliedGrand);
            document.getElementById('stickyBarTotal').textContent    = formatRupee(appliedGrand);
            document.getElementById('mobileSummaryTotalLabel').textContent = formatRupee(appliedGrand);

            const discountRowDesktop = document.getElementById('discountRowDesktop');
            const discountRowMobile  = document.getElementById('discountRowMobile');

            if (appliedDiscount > 0) {
                discountRowDesktop.style.display = 'flex';
                discountRowMobile.style.display  = 'flex';
                document.getElementById('discountAmountDesktop').textContent = '−' + formatRupee(appliedDiscount);
                document.getElementById('discountAmountMobile').textContent  = '−' + formatRupee(appliedDiscount);
            } else {
                discountRowDesktop.style.display = 'none';
                discountRowMobile.style.display  = 'none';
            }
        }

        // ── Wire up both coupon boxes (desktop + mobile) identically ──
        function wireCouponBox(boxId, formStateId, appliedStateId) {
            const box = document.getElementById(boxId);
            if (!box) return;

            const formState    = document.getElementById(formStateId);
            const appliedState = document.getElementById(appliedStateId);
            const input        = box.querySelector('.coupon-code-input');
            const applyBtn     = box.querySelector('.coupon-apply-btn-trigger');
            const msgEl        = box.querySelector('.coupon-msg-target');
            // Browse trigger is currently commented out in the markup by
            // request — this stays null-guarded so nothing throws, and will
            // "just work" again the moment that markup is re-enabled.
            const browseBtn    = box.querySelector('.coupon-browse-trigger');
            const removeBtn    = box.querySelector('.coupon-remove-trigger');

            function showMessage(text, type) {
                if (!msgEl) return;
                msgEl.textContent = text;
                msgEl.className = 'coupon-msg coupon-msg-target ' + type;
                msgEl.style.display = 'block';
            }
            function clearMessage() {
                if (!msgEl) return;
                msgEl.style.display = 'none';
            }

            function setAppliedUI(data) {
                box.querySelector('.applied-code-target').textContent = data.code;
                box.querySelector('.applied-desc-target').textContent = data.description || '';
                box.querySelector('.applied-save-target').textContent =
                    'You saved ' + formatRupee(data.discount_ex_gst);

                formState.style.display = 'none';
                appliedState.style.display = 'block';
                box.classList.add('applied');
                hiddenCouponInput.value = data.code;

                // Update the small "(CODE)" label next to "Discount" in both summaries
                document.querySelectorAll('.discount-code-label-desktop, .discount-code-label-mobile')
                    .forEach(el => el.textContent = '(' + data.code + ')');

                // Trust the server's fully-computed figures — do not re-derive
                // GST or grand total on the client, since GST depends on the
                // post-discount taxable value.
                appliedDiscount = Number(data.discount_ex_gst);
                appliedGst      = Number(data.gst_amount);
                appliedGrand    = Number(data.grand_total);
                recalcTotals();

                // Keep both boxes (desktop + mobile) in sync visually
                syncOtherBox(boxId, data);
            }

            function resetUI() {
                formState.style.display = 'block';
                appliedState.style.display = 'none';
                box.classList.remove('applied');
                hiddenCouponInput.value = '';
                input.value = '';
                clearMessage();

                appliedDiscount = 0;
                appliedGst      = baseGst;
                appliedGrand    = baseTotal;
                recalcTotals();

                syncOtherBoxReset(boxId);
            }

            async function applyCode(code) {
                if (!code) { showMessage('Please enter a coupon code.', 'error'); return; }

                applyBtn.disabled = true;
                applyBtn.textContent = 'Applying…';
                clearMessage();

                try {
                    const res = await fetch('{{ route("checkout.coupon.apply") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ code: code.toUpperCase() }),
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        showMessage(data.message || 'Could not apply this coupon.', 'error');
                        return;
                    }

                    setAppliedUI(data);
                    showMessage(data.message || 'Coupon applied successfully!', 'success');
                    closeModal();
                } catch (e) {
                    showMessage('Something went wrong. Please try again.', 'error');
                } finally {
                    applyBtn.disabled = false;
                    applyBtn.textContent = 'Apply';
                }
            }

            applyBtn.addEventListener('click', () => applyCode(input.value.trim()));
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); applyCode(input.value.trim()); }
            });

            if (removeBtn) {
                removeBtn.addEventListener('click', async () => {
                    removeBtn.disabled = true;
                    try {
                        await fetch('{{ route("checkout.coupon.remove") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        });
                    } catch (e) { /* ignore */ }
                    resetUI();
                    removeBtn.disabled = false;
                });
            }

            if (browseBtn) {
                browseBtn.addEventListener('click', openModal);
            }

            // Expose apply function so the modal's "Apply" buttons can trigger it
            box.__applyCode = applyCode;
            box.__setAppliedUI = setAppliedUI;
            box.__resetUI = resetUI;
        }

        function syncOtherBox(currentBoxId, data) {
            const otherId = currentBoxId === 'couponBoxDesktop' ? 'couponBoxMobile' : 'couponBoxDesktop';
            const otherBox = document.getElementById(otherId);
            if (otherBox && otherBox.__setAppliedUI) {
                const formStateId    = otherId === 'couponBoxDesktop' ? 'couponFormStateDesktop' : 'couponFormStateMobile';
                const appliedStateId = otherId === 'couponBoxDesktop' ? 'couponAppliedStateDesktop' : 'couponAppliedStateMobile';
                document.getElementById(formStateId).style.display = 'none';
                document.getElementById(appliedStateId).style.display = 'block';
                otherBox.classList.add('applied');
                otherBox.querySelector('.applied-code-target').textContent = data.code;
                otherBox.querySelector('.applied-desc-target').textContent = data.description || '';
                otherBox.querySelector('.applied-save-target').textContent = 'You saved ' + formatRupee(data.discount_ex_gst);
            }
        }

        function syncOtherBoxReset(currentBoxId) {
            const otherId = currentBoxId === 'couponBoxDesktop' ? 'couponBoxMobile' : 'couponBoxDesktop';
            const otherBox = document.getElementById(otherId);
            if (otherBox) {
                const formStateId    = otherId === 'couponBoxDesktop' ? 'couponFormStateDesktop' : 'couponFormStateMobile';
                const appliedStateId = otherId === 'couponBoxDesktop' ? 'couponAppliedStateDesktop' : 'couponAppliedStateMobile';
                document.getElementById(formStateId).style.display = 'block';
                document.getElementById(appliedStateId).style.display = 'none';
                otherBox.classList.remove('applied');
                const input = otherBox.querySelector('.coupon-code-input');
                if (input) input.value = '';
            }
        }

        wireCouponBox('couponBoxDesktop', 'couponFormStateDesktop', 'couponAppliedStateDesktop');
        wireCouponBox('couponBoxMobile', 'couponFormStateMobile', 'couponAppliedStateMobile');

        // ── Modal (fully intact — just currently unreachable since both
        //     browse-trigger buttons are commented out in the markup) ──
        function openModal() { modalOverlay.classList.add('open'); loadCoupons(); }
        function closeModal() { modalOverlay.classList.remove('open'); }

        modalClose.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        async function loadCoupons() {
            if (couponsLoaded) return;
            modalBody.innerHTML = '<div class="coupon-modal-loading">Loading coupons…</div>';

            try {
                const res = await fetch('{{ route("checkout.coupons.list") }}', { headers: { 'Accept': 'application/json' } });
                const coupons = await res.json();

                if (!coupons.length) {
                    modalBody.innerHTML = '<div class="coupon-modal-empty">No coupons available right now.</div>';
                    return;
                }

                modalBody.innerHTML = coupons.map(c => `
                    <div class="coupon-list-item">
                        <div>
                            <div class="coupon-list-code">${c.code}</div>
                            ${c.description ? `<div class="coupon-list-desc">${escapeHtml(c.description)}</div>` : ''}
                            <div class="coupon-list-meta">
                                ${c.min_order_amount ? `Min. order ₹${Number(c.min_order_amount).toLocaleString('en-IN')}` : 'No minimum order'}
                                ${c.expires_at ? ` · Expires ${c.expires_at}` : ''}
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
                            <span class="coupon-list-badge">${c.label}</span>
                            <button type="button" class="coupon-list-use-btn" data-code="${c.code}">Apply</button>
                        </div>
                    </div>
                `).join('');

                modalBody.querySelectorAll('.coupon-list-use-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Apply via the desktop box if visible, otherwise mobile
                        const visibleBox = window.innerWidth >= 992
                            ? document.getElementById('couponBoxDesktop')
                            : document.getElementById('couponBoxMobile');
                        if (visibleBox && visibleBox.__applyCode) {
                            visibleBox.__applyCode(btn.dataset.code);
                        }
                    });
                });

                couponsLoaded = true;
            } catch (e) {
                modalBody.innerHTML = '<div class="coupon-modal-empty">Could not load coupons. Please try again.</div>';
            }
        }

        // ── If a coupon is already in session (e.g. page reload after apply), restore UI ──
        @if (session('coupon'))
            (function restoreAppliedCoupon() {
                const data = {
                    code:            @json(session('coupon.code')),
                    description:     @json(session('coupon.description')),
                    discount_ex_gst: {{ (float) session('coupon.discount_ex_gst', 0) }},
                    gst_amount:      {{ (float) session('coupon.gst_amount', $totalGst) }},
                    grand_total:     {{ (float) session('coupon.grand_total', $total + $totalShipping) }},
                };

                appliedDiscount = data.discount_ex_gst;
                appliedGst      = data.gst_amount;
                appliedGrand    = data.grand_total;

                ['couponBoxDesktop', 'couponBoxMobile'].forEach(function (boxId) {
                    const box = document.getElementById(boxId);
                    if (!box) return;
                    const formStateId    = boxId === 'couponBoxDesktop' ? 'couponFormStateDesktop' : 'couponFormStateMobile';
                    const appliedStateId = boxId === 'couponBoxDesktop' ? 'couponAppliedStateDesktop' : 'couponAppliedStateMobile';
                    document.getElementById(formStateId).style.display = 'none';
                    document.getElementById(appliedStateId).style.display = 'block';
                    box.classList.add('applied');
                    box.querySelector('.applied-code-target').textContent = data.code;
                    box.querySelector('.applied-desc-target').textContent = data.description || '';
                    box.querySelector('.applied-save-target').textContent = 'You saved ' + formatRupee(data.discount_ex_gst);
                });

                document.querySelectorAll('.discount-code-label-desktop, .discount-code-label-mobile')
                    .forEach(el => el.textContent = '(' + data.code + ')');

                recalcTotals();
            })();
        @endif
    })();
    </script>

@endsection