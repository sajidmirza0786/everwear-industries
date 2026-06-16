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

    <div class="container py-4">
        <div class="row g-4 align-items-start">

            {{-- LEFT: Checkout Form --}}
            <div class="col-12 col-lg-7">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf

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
                                    Ship: ₹{{ number_format($item->shipping_charge, 2) }}
                                </div>
                            </div>
                            <div style="font-size:13px;font-weight:600;white-space:nowrap;flex-shrink:0;">
                                ₹{{ number_format($itemTotal + $item->shipping_charge, 2) }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:24px 0;color:var(--soft);font-size:14px;">Your cart is empty.</div>
                    @endforelse

                    {{-- Totals breakdown --}}
                    <div class="row-line mt-3">
                        <span style="color:var(--soft);font-size:14px;">
                            Subtotal <span style="font-size:11px;">(excl. GST)</span>
                        </span>
                        <span style="font-size:14px;">₹{{ number_format($totalExGst, 2) }}</span>
                    </div>

                    @if ($totalGst > 0)
                        <div class="row-line">
                            <span style="color:var(--soft);font-size:14px;">
                                GST
                                <i class="bi bi-info-circle" style="font-size:11px;cursor:help;"
                                   title="Goods & Services Tax included in your order"></i>
                            </span>
                            <span style="font-size:14px;">₹{{ number_format($totalGst, 2) }}</span>
                        </div>
                    @endif

                    <div class="row-line">
                        <span style="color:var(--soft);font-size:14px;">Shipping</span>
                        <span style="font-size:14px;">₹{{ number_format($totalShipping, 2) }}</span>
                    </div>

                    <div style="border-top:1px solid var(--line-strong);margin:8px 0;"></div>

                    <div class="row-line total">
                        <span>Total <span style="font-size:11px;font-weight:400;color:var(--soft);">(incl. GST)</span></span>
                        <span>₹{{ number_format($total + $totalShipping, 2) }}</span>
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
                            <strong style="margin-left:8px;">₹{{ number_format($total + $totalShipping, 2) }}</strong>
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
                                        · Ship: ₹{{ number_format($item->shipping_charge, 2) }}
                                    </div>
                                </div>
                                <div style="font-weight:600;white-space:nowrap;">
                                    ₹{{ number_format($itemTotal + $item->shipping_charge, 2) }}
                                </div>
                            </div>
                        @empty
                        @endforelse

                        {{-- Subtotal (ex-GST) --}}
                        <div style="display:flex;justify-content:space-between;padding:10px 0 4px;font-size:13px;color:var(--soft);">
                            <span>Subtotal <span style="font-size:11px;">(excl. GST)</span></span>
                            <span>₹{{ number_format($totalExGst, 2) }}</span>
                        </div>

                        {{-- GST row --}}
                        @if ($totalGst > 0)
                            <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:var(--soft);">
                                <span>GST</span>
                                <span>₹{{ number_format($totalGst, 2) }}</span>
                            </div>
                        @endif

                        {{-- Shipping --}}
                        <div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:var(--soft);">
                            <span>Shipping</span>
                            <span>₹{{ number_format($totalShipping, 2) }}</span>
                        </div>

                        {{-- Grand Total --}}
                        <div style="display:flex;justify-content:space-between;padding:10px 0 4px;font-size:16px;font-weight:600;border-top:1px solid var(--line-strong);margin-top:6px;">
                            <span>Total <span style="font-size:11px;font-weight:400;color:var(--soft);">(incl. GST)</span></span>
                            <span>₹{{ number_format($total + $totalShipping, 2) }}</span>
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
                <div style="font-family:var(--font-display);font-size:18px;font-weight:500;">
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

@endsection