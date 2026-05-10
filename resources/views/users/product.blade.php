@extends('users.master')

@section('seo')
    <title>{{ $product->name }} | On Jewel</title>
    <meta name="keywords" content="{{ $product->keyword ?? '' }}">
    <meta name="description" content="{{ $product->description ?? '' }}">
@endsection

@section('content')

    {{-- ── Compact Breadcrumb ── --}}
    <div class="pd-breadcrumb">
        <div class="container">
            <nav class="pd-crumbs" aria-label="breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <a href="{{ route('listing', $product->category) }}">{{ strtolower($product->category->name ?? 'Home') }}</a>
                <span>/</span>
                <span class="pd-crumb-active">{{ Str::limit(strtolower($product->name), 40) }}</span>
            </nav>
        </div>
    </div>

    {{-- ── Product Detail ── --}}
    <section class="pd-section">
        <div class="container">
            <div class="pd-grid">

                {{-- ── LEFT: Gallery ── --}}
                <div class="pd-gallery">
                    @php
                        $allImages = collect();
                        $allImages->push(['type' => 'image', 'src' => url(\Storage::url($product->image ?? ''))]);
                        foreach ($product->images as $img) {
                            $allImages->push(['type' => 'image', 'src' => url(\Storage::url($img->image_path ?? ''))]);
                        }
                        // Add YouTube video if set
                        // if ($product->video_url) {
                        //     preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $product->video_url, $m);
                        //     $videoId = $m[1] ?? null;
                        //     if ($videoId) {
                        //         $allImages->push(['type' => 'video', 'video_id' => $videoId]);
                        //     }
                        // }
                        if ($product->video_url) {
                            preg_match('/(?:v=|youtu\.be\/|shorts\/)([a-zA-Z0-9_-]{11})/', $product->video_url, $m);
                            $videoId = $m[1] ?? null;
                            if ($videoId) {
                                $allImages->push(['type' => 'video', 'video_id' => $videoId]);
                            }
                        }
                    @endphp

                    {{-- Carousel --}}
                    <div class="pd-carousel-wrap" id="pdCarouselWrap">

                        @if ($product->mrp > $product->selling)
                            <div class="pd-badge-off">
                                {{ round((($product->mrp - $product->selling) / $product->mrp) * 100) }}% OFF</div>
                        @endif

                        @if ($product->stock <= 0)
                            <div class="pd-oos-overlay">Out of Stock</div>
                        @endif

                        {{-- Slides container --}}
                        <div class="pd-slides-track" id="pdTrack">
                            @foreach ($allImages as $i => $item)
                                <div class="pd-slide" data-index="{{ $i }}" data-type="{{ $item['type'] }}"
                                    @if ($item['type'] === 'video') data-video-id="{{ $item['video_id'] }}" @endif>
                                    @if ($item['type'] === 'video')
                                        <div class="pd-video-slide" style="height:var(--carousel-h,420px)">
                                            <img class="yt-poster"
                                                src="https://img.youtube.com/vi/{{ $item['video_id'] }}/hqdefault.jpg"
                                                alt="Product video">
                                            <div class="pd-play-btn">
                                                <svg viewBox="0 0 24 24" fill="white">
                                                    <polygon points="6,4 20,12 6,20" />
                                                </svg>
                                            </div>
                                            <div class="pd-video-label">Watch product video</div>
                                        </div>
                                    @else
                                        <img src="{{ $item['src'] }}" alt="{{ $product->name }}"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($allImages->count() > 1)
                            <button class="pd-arrow pd-arrow-prev" id="pdPrev" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="pd-arrow pd-arrow-next" id="pdNext" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <div class="pd-dots" id="pdDots">
                                @foreach ($allImages as $i => $src)
                                    <button class="pd-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"
                                        aria-label="Image {{ $i + 1 }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    @if ($allImages->count() > 1)
                        <div class="pd-thumbs">
                            @foreach ($allImages as $i => $item)
                                <button class="pd-thumb {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"
                                    type="button">
                                    @if ($item['type'] === 'video')
                                        <img src="https://img.youtube.com/vi/{{ $item['video_id'] }}/mqdefault.jpg"
                                            alt="Video">
                                        <div class="pd-thumb-video-badge">
                                            <svg viewBox="0 0 24 24">
                                                <rect x="2" y="5" width="20" height="14" rx="3"
                                                    fill="rgba(220,0,0,.85)" />
                                                <polygon points="10,9 16,12 10,15" fill="white" />
                                            </svg>
                                        </div>
                                    @else
                                        <img src="{{ $item['src'] }}" alt="{{ $product->name }}">
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── RIGHT: Info ── --}}
                <div class="pd-info">

                    <h1 class="pd-title">{{ $product->name }}</h1>

                    {{-- Pricing — updated dynamically by JS when an attribute is selected --}}
                    <div class="pd-pricing" id="pdPricing">
                        <span class="pd-price" id="pdPrice">₹{{ number_format($product->selling) }}</span>
                        @if ($product->mrp > $product->selling)
                            <span class="pd-mrp" id="pdMrp">₹{{ number_format($product->mrp) }}</span>
                            <span class="pd-save" id="pdSave">Save
                                ₹{{ number_format($product->mrp - $product->selling) }}</span>
                        @else
                            <span class="pd-mrp" id="pdMrp" style="display:none"></span>
                            <span class="pd-save" id="pdSave" style="display:none"></span>
                        @endif
                    </div>

                    <div class="pd-divider"></div>

                    <dl class="pd-meta">
                        <div class="pd-meta-row">
                            <dt>Code</dt>
                            <dd>{{ $product->code }}</dd>
                        </div>
                        @if ($product->size)
                            <div class="pd-meta-row" id="pdSizeRow">
                                <dt>Size</dt>
                                <dd id="pdSizeMeta">{{ $product->size }}</dd>
                            </div>
                        @endif
                        @if ($product->color)
                            <div class="pd-meta-row">
                                <dt>Colour</dt>
                                <dd>{{ $product->color }}</dd>
                            </div>
                        @endif
                        @if ($product->gram_weight)
                            <div class="pd-meta-row">
                                <dt>Weight</dt>
                                <dd>{{ $product->gram_weight }}g</dd>
                            </div>
                        @endif
                        <div class="pd-meta-row">
                            <dt>Stock</dt>
                            <dd id="pdStockMeta" class="{{ $product->stock > 0 ? 'pd-instock' : 'pd-outstock' }}">
                                <span
                                    class="pd-stock-dot {{ $product->stock > 0 ? 'pd-stock-dot--in' : 'pd-stock-dot--out' }}"
                                    id="pdStockDot"></span>
                                <span id="pdStockText">
                                    @if ($product->stock > 0)
                                        In Stock ({{ $product->stock }} units)
                                    @else
                                        Out of Stock
                                    @endif
                                </span>
                            </dd>
                        </div>
                    </dl>

                    @if ($product->short_description)
                        <p class="pd-short-desc">{{ $product->short_description }}</p>
                    @endif

                    <div class="pd-divider"></div>

                    {{-- ── Product Attributes (Sizes/Variants) ── --}}
                    @php $attributes = $product->attributes ?? collect(); @endphp
                    @if ($attributes->count() > 0)
                        <div class="pd-variants-wrap" id="pdVariantsWrap">
                            <div class="pd-label">Select Size / Variant</div>
                            <div class="pd-variant-chips" id="pdVariantChips">
                                @foreach ($attributes as $atr)
                                    <button type="button"
                                        class="pd-variant-chip {{ $atr->stock <= 0 ? 'pd-variant-chip--oos' : '' }}"
                                        data-atr-id="{{ $atr->id }}" data-mrp="{{ $atr->mrp }}"
                                        data-selling="{{ $atr->selling_price }}" data-stock="{{ $atr->stock }}"
                                        data-size="{{ $atr->size }}" data-desc="{{ $atr->description }}"
                                        {{ $atr->stock <= 0 ? 'title=Out of Stock' : '' }}>
                                        {{ $atr->size }}
                                        @if ($atr->stock <= 0)
                                            <span class="pd-chip-oos-line"></span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            @if ($attributes->where('description', '!=', null)->count() > 0)
                                <p class="pd-variant-desc" id="pdVariantDesc" style="display:none"></p>
                            @endif
                        </div>
                        <div class="pd-divider"></div>
                    @endif

                    @php
                        $colorsvrs = App\Models\Product::where('color_group_id', $product->color_group_id)
                            ->where('id', '!=', $product->id)
                            ->get();
                    @endphp

                    @if ($colorsvrs->isNotEmpty())
                        <div class="pd-color-variants-wrap">
                            <div class="pd-label">Color Variants</div>
                            <div class="pd-color-swatches">
                                @foreach ($colorsvrs as $colorVariant)
                                    <a href="{{ route('listing', $colorVariant) }}" class="pd-color-swatch"
                                        title="{{ $colorVariant->color ?? $colorVariant->name }}">
                                        <span class="pd-color-swatch-img">
                                            <img src="{{ \Storage::url($colorVariant->image) }}"
                                                alt="{{ $colorVariant->name }}" loading="lazy">
                                        </span>
                                        @if ($colorVariant->color)
                                            <span class="pd-color-swatch-label">{{ $colorVariant->color }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="pd-divider"></div>
                    @endif

                    {{-- Cart Form --}}
                    <form action="{{ route('cart.add') }}" method="POST" id="pdCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_attribute_id" id="pdAtrInput" value="">
                        <div class="pd-qty-row">
                            <span class="pd-label">Quantity</span>
                            <div class="qty-stepper">
                                <button type="button" id="qtyMinus">−</button>
                                <input type="number" id="pd-qty" name="quantity" value="1" min="1"
                                    max="{{ $product->stock }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <button type="button" id="qtyPlus">+</button>
                            </div>
                        </div>

                        {{-- Show variant-required message if attributes exist and none selected --}}
                        @if ($attributes->count() > 0)
                            <p class="pd-variant-required" id="pdVariantRequired" style="display:none">
                                <i class="bi bi-exclamation-circle"></i> Please select a size/variant before adding to
                                cart.
                            </p>
                        @endif

                        <button type="submit" id="pdAddBtn"
                            class="btn btn-dark btn-lg pd-add-btn {{ $product->stock <= 0 && $attributes->count() === 0 ? 'pd-btn-disabled' : '' }}"
                            {{ $product->stock <= 0 && $attributes->count() === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-bag"></i>
                            <span id="pdAddBtnText">
                                @if ($attributes->count() > 0)
                                    Select a Variant
                                @elseif ($product->stock <= 0)
                                    Out of Stock
                                @else
                                    Add to Cart
                                @endif
                            </span>
                        </button>
                    </form>

                    <div class="pd-divider"></div>

                    {{-- Static Service Badges --}}
                    <div class="pd-services">
                        <div class="pd-service-item">
                            <div class="pd-service-icon"><i class="bi bi-receipt"></i></div>
                            <div class="pd-service-body">
                                <strong>GST Invoice</strong>
                                <span>Claim your GST input on every purchase</span>
                            </div>
                        </div>
                        <div class="pd-service-item">
                            <div class="pd-service-icon"><i class="bi bi-brush"></i></div>
                            <div class="pd-service-body">
                                <strong>Free Personalisation</strong>
                                <span>We offer standard free printing on all products</span>
                            </div>
                        </div>
                        <div class="pd-service-item">
                            <div class="pd-service-icon"><i class="bi bi-truck"></i></div>
                            <div class="pd-service-body">
                                <strong>Delivery across India</strong>
                                <span>We deliver to every pin code in India</span>
                            </div>
                        </div>
                    </div>

                </div>{{-- /pd-info --}}
            </div>{{-- /pd-grid --}}

            {{-- Description --}}
            @if ($product->long_description)
                <div class="pd-desc-wrap">
                    <div class="accordion-clean">
                        <div class="acc-item open">
                            <div class="acc-head" onclick="this.parentElement.classList.toggle('open')">
                                <span>Product Description</span>
                                <i class="bi bi-plus" style="font-size:14px;color:var(--accent-2)"></i>
                            </div>
                            <div class="acc-body">
                                <div class="pd-long-desc">{!! $product->long_description !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- Similar Products --}}
    @if ($similarProducts->count() > 0)
        <section class="section-tight bg-surface border-top-soft">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="section-eyebrow">More Like This</span>
                    <h2 class="section-title" style="font-size:clamp(1.5rem,3vw,2.2rem);margin-top:4px">Similar Products
                    </h2>
                </div>
                <div class="row g-3">
                    @foreach ($similarProducts as $simProduct)
                        @include('users.components.product_3', ['product' => $simProduct])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <style>
        /* ── Breadcrumb ── */
        .pd-breadcrumb {
            background: var(--bg-2);
            border-bottom: 1px solid var(--line);
            padding: 9px 0;
        }

        .pd-crumbs {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            letter-spacing: 0.05em;
            flex-wrap: wrap;
            margin: 0;
        }

        .pd-crumbs a {
            color: var(--soft);
        }

        .pd-crumbs a:hover {
            color: var(--accent-2);
        }

        .pd-crumbs>span {
            color: var(--soft-2);
        }

        .pd-crumb-active {
            color: var(--ink);
            font-weight: 500;
        }

        /* ── Section + Grid ── */
        .pd-section {
            padding: 36px 0 56px;
        }

        .pd-grid {
            display: grid;
            grid-template-columns: 52% 1fr;
            gap: 44px;
            align-items: start;
        }

        /* ── Carousel ── */
        .pd-carousel-wrap {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid var(--line);
            touch-action: pan-y;
            user-select: none;
            -webkit-user-select: none;
        }

        .pd-slides-track {
            display: flex;
            width: 100%;
            transition: transform 0.38s cubic-bezier(0.25, 0.8, 0.25, 1);
            will-change: transform;
            align-items: flex-start;
        }

        .pd-slide {
            min-width: 100%;
            flex-shrink: 0;
            overflow: hidden;
        }

        .pd-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: var(--surface);
        }

        /* Discount badge */
        .pd-badge-off {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 5;
            background: var(--ink);
            color: var(--bg);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 5px 10px;
        }

        .pd-oos-overlay {
            position: absolute;
            inset: 0;
            z-index: 5;
            background: rgba(26, 20, 16, 0.52);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-size: 24px;
            color: #fff;
            font-style: italic;
        }

        /* Arrows */
        .pd-arrow {
            position: absolute;
            top: 50%;
            z-index: 6;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s, color 0.2s;
        }

        .pd-arrow:hover {
            background: var(--ink);
            color: #fff;
        }

        .pd-arrow-prev {
            left: 10px;
        }

        .pd-arrow-next {
            right: 10px;
        }

        /* Dots */
        .pd-dots {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 6;
        }

        .pd-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.45);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: background 0.25s, transform 0.25s;
        }

        .pd-dot.active {
            background: #fff;
            transform: scale(1.35);
        }

        /* Thumbnails */
        .pd-thumbs {
            display: flex;
            gap: 6px;
            margin-top: 7px;
            flex-wrap: wrap;
        }

        .pd-thumb {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            border: 1px solid var(--line-strong);
            background: var(--surface);
            overflow: hidden;
            cursor: pointer;
            padding: 0;
            transition: border-color 0.2s, opacity 0.2s;
            opacity: 0.65;
        }

        .pd-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pd-thumb.active {
            border-color: var(--accent);
            border-width: 2px;
            opacity: 1;
        }

        .pd-thumb:hover {
            opacity: 1;
        }

        /* ── Info ── */
        .pd-title {
            font-family: var(--font-display);
            font-size: clamp(1.45rem, 2.6vw, 2.1rem);
            font-weight: 500;
            color: var(--ink);
            margin: 0 0 14px;
            line-height: 1.1;
        }

        .pd-pricing {
            display: flex;
            align-items: baseline;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pd-price {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--ink);
            line-height: 1;
        }

        .pd-mrp {
            font-size: 0.9rem;
            color: var(--soft-2);
            text-decoration: line-through;
        }

        .pd-save {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #3d7a2a;
            background: rgba(61, 122, 42, 0.1);
            padding: 3px 9px;
            border-radius: 999px;
        }

        .pd-divider {
            height: 1px;
            background: var(--line);
            margin: 18px 0;
            border: none;
        }

        /* Meta */
        .pd-meta {
            margin: 0 0 14px;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .pd-meta-row {
            display: flex;
            align-items: baseline;
            gap: 12px;
            font-size: 13.5px;
        }

        .pd-meta-row dt {
            min-width: 68px;
            flex-shrink: 0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--soft-2);
        }

        .pd-meta-row dd {
            margin: 0;
            color: var(--ink);
            font-weight: 500;
        }

        .pd-instock {
            color: #3d7a2a;
        }

        .pd-outstock {
            color: #a8412c;
        }

        .pd-stock-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
            position: relative;
            top: -1px;
        }

        .pd-stock-dot--in {
            background: #3d7a2a;
        }

        .pd-stock-dot--out {
            background: #a8412c;
        }

        .pd-short-desc {
            font-size: 13.5px;
            color: var(--soft);
            line-height: 1.75;
            margin: 0;
        }

        /* Qty + CTA */
        .pd-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--soft-2);
            display: block;
            margin-bottom: 8px;
        }

        .pd-qty-row {
            margin-bottom: 14px;
        }

        .pd-add-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .pd-btn-disabled {
            opacity: 0.42;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Service Badges ── */
        .pd-services {
            display: flex;
            flex-direction: column;
        }

        .pd-service-item {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .pd-service-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .pd-service-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border: 1px solid var(--line-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-2);
            font-size: 14px;
            background: var(--surface);
        }

        .pd-service-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pd-service-body strong {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }

        .pd-service-body span {
            font-size: 12px;
            color: var(--soft);
            line-height: 1.5;
        }

        /* Description */
        .pd-desc-wrap {
            margin-top: 36px;
            max-width: 860px;
        }

        .pd-long-desc {
            font-size: 14px;
            color: var(--soft);
            line-height: 1.8;
        }

        .pd-long-desc p {
            margin-bottom: 10px;
        }

        .pd-long-desc ul,
        .pd-long-desc ol {
            padding-left: 18px;
            margin-bottom: 10px;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .pd-grid {
                grid-template-columns: 1fr;
                gap: 22px;
            }

            .pd-section {
                padding: 24px 0 44px;
            }
        }

        @media (max-width: 575.98px) {
            .pd-thumb {
                width: 54px;
                height: 54px;
            }

            .pd-title {
                font-size: 1.4rem;
            }

            .pd-price {
                font-size: 1.45rem;
            }

            .pd-section {
                padding: 16px 0 36px;
            }

            .pd-arrow {
                width: 30px;
                height: 30px;
                font-size: 10px;
            }

            .pd-desc-wrap {
                margin-top: 24px;
            }
        }

        .pd-video-slide {
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .pd-video-slide img.yt-poster {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pd-play-btn {
            position: absolute;
            z-index: 4;
            width: 64px;
            height: 64px;
            background: rgba(255, 0, 0, .88);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, transform .2s;
        }

        .pd-play-btn:hover {
            background: #f00;
            transform: scale(1.08);
        }

        .pd-play-btn svg {
            width: 28px;
            height: 28px;
            margin-left: 4px;
        }

        .pd-video-label {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 11px;
            letter-spacing: .08em;
            padding: 3px 10px;
            white-space: nowrap;
            z-index: 5;
        }

        .pd-iframe-wrap {
            position: absolute;
            inset: 0;
            z-index: 20;
            background: #000;
        }

        .pd-iframe-wrap iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .pd-iframe-close {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 30;
            background: rgba(0, 0, 0, .6);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 14px;
            cursor: pointer;
        }

        .pd-thumb {
            position: relative;
        }

        .pd-thumb-video-badge {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, .25);
        }

        .pd-thumb-video-badge svg {
            width: 22px;
            height: 22px;
        }

        /* ── Variant Chips ── */
        .pd-variants-wrap {
            margin-bottom: 4px;
        }

        .pd-variant-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
            margin-bottom: 6px;
        }

        .pd-variant-chip {
            position: relative;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.06em;
            border: 1.5px solid var(--line-strong);
            background: var(--surface);
            color: var(--ink);
            cursor: pointer;
            transition: border-color 0.18s, background 0.18s, color 0.18s, opacity 0.18s;
            user-select: none;
            -webkit-user-select: none;
            overflow: hidden;
        }

        .pd-variant-chip:hover:not(.pd-variant-chip--oos) {
            border-color: var(--ink);
            background: var(--bg-2);
        }

        .pd-variant-chip.active {
            border-color: var(--ink);
            border-width: 2px;
            background: var(--ink);
            color: var(--bg);
        }

        .pd-variant-chip--oos {
            opacity: 0.42;
            cursor: not-allowed;
            color: var(--soft-2);
        }

        /* Diagonal strikethrough line for OOS chips */
        .pd-chip-oos-line {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom right,
                    transparent calc(50% - 0.5px),
                    var(--soft-2) calc(50% - 0.5px),
                    var(--soft-2) calc(50% + 0.5px),
                    transparent calc(50% + 0.5px));
            pointer-events: none;
        }

        .pd-variant-desc {
            font-size: 12.5px;
            color: var(--soft);
            margin: 6px 0 0;
            line-height: 1.5;
        }

        .pd-variant-required {
            font-size: 12.5px;
            color: #a8412c;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ── Color Variants ── */
        .pd-color-variants-wrap {
            margin-bottom: 4px;
        }

        .pd-color-swatches {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .pd-color-swatch {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .pd-color-swatch-img {
            display: block;
            width: 64px;
            height: 64px;
            border: 1.5px solid var(--line-strong);
            overflow: hidden;
            background: var(--surface);
            transition: border-color 0.18s, opacity 0.18s;
            opacity: 0.72;
        }

        .pd-color-swatch-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pd-color-swatch:hover .pd-color-swatch-img {
            border-color: var(--ink);
            opacity: 1;
        }

        .pd-color-swatch-label {
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--soft-2);
            text-align: center;
            max-width: 64px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.18s;
        }

        .pd-color-swatch:hover .pd-color-swatch-label {
            color: var(--ink);
        }
    </style>

<script>
(function () {
    /* ════════════════════════════════════════
       CAROUSEL
    ════════════════════════════════════════ */
    var track   = document.getElementById('pdTrack');
    var wrap    = document.getElementById('pdCarouselWrap');
    var thumbs  = Array.from(document.querySelectorAll('.pd-thumb'));
    var dots    = Array.from(document.querySelectorAll('.pd-dot'));
    var btnPrev = document.getElementById('pdPrev');
    var btnNext = document.getElementById('pdNext');

    if (track) {
        var slides    = Array.from(track.children);
        var total     = slides.length;
        var current   = 0;
        var animating = false;

        function syncHeight() {
            var img = slides[current].querySelector('img:not(.yt-poster)') ||
                      slides[current].querySelector('img');
            if (!img) return;
            if (img.complete && img.naturalHeight > 0) {
                var w = wrap.offsetWidth;
                var h = Math.round(w * img.naturalHeight / img.naturalWidth);
                wrap.style.height = h + 'px';
                slides.forEach(function (s) { s.style.height = h + 'px'; });
            } else {
                img.addEventListener('load', syncHeight, { once: true });
            }
        }
        window.addEventListener('load', syncHeight);
        window.addEventListener('resize', syncHeight);
        syncHeight();

        function removeIframe() {
            var existing = wrap.querySelector('.pd-iframe-wrap');
            if (existing) existing.remove();
        }

        function setSlide(index) {
            if (animating || index === current) return;
            removeIframe();
            animating = true;
            current   = index;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            dots.forEach(function (d, i)   { d.classList.toggle('active', i === current); });
            thumbs.forEach(function (t, i) { t.classList.toggle('active', i === current); });
            setTimeout(function () { syncHeight(); animating = false; }, 400);
        }

        if (total > 1) {
            btnPrev && btnPrev.addEventListener('click', function () {
                setSlide((current - 1 + total) % total);
            });
            btnNext && btnNext.addEventListener('click', function () {
                setSlide((current + 1) % total);
            });
            dots.forEach(function (dot, i) {
                dot.addEventListener('click', function () { setSlide(i); });
            });
            thumbs.forEach(function (thumb, i) {
                thumb.addEventListener('click', function () { setSlide(i); });
            });

            /* Swipe */
            var tx = 0, ty = 0;
            wrap.addEventListener('touchstart', function (e) {
                tx = e.changedTouches[0].clientX;
                ty = e.changedTouches[0].clientY;
            }, { passive: true });
            wrap.addEventListener('touchend', function (e) {
                var dx = e.changedTouches[0].clientX - tx;
                var dy = e.changedTouches[0].clientY - ty;
                if (Math.abs(dx) > 36 && Math.abs(dx) > Math.abs(dy)) {
                    dx < 0 ? setSlide((current + 1) % total)
                           : setSlide((current - 1 + total) % total);
                }
            }, { passive: true });

            /* Keyboard */
            document.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft')  setSlide((current - 1 + total) % total);
                if (e.key === 'ArrowRight') setSlide((current + 1) % total);
            });
        }

        /* Video slides */
        slides.forEach(function (slide) {
            if (slide.dataset.type !== 'video') return;
            var videoId  = slide.dataset.videoId;
            var videoDiv = slide.querySelector('.pd-video-slide');
            if (!videoDiv) return;

            videoDiv.addEventListener('click', function () {
                removeIframe();
                var iw  = document.createElement('div');
                iw.className = 'pd-iframe-wrap';

                var ifr = document.createElement('iframe');
                ifr.src = 'https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0';
                ifr.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                ifr.allowFullscreen = true;

                /* postMessage error → open in new tab */
                window.addEventListener('message', function onMsg(e) {
                    try {
                        var data = typeof e.data === 'string' ? JSON.parse(e.data) : e.data;
                        if (data && ((data.event === 'infoDelivery' && data.info && data.info.error) || data.event === 'onError')) {
                            removeIframe();
                            window.open('https://www.youtube.com/watch?v=' + videoId, '_blank');
                            window.removeEventListener('message', onMsg);
                        }
                    } catch (err) {}
                });

                var cb = document.createElement('button');
                cb.className = 'pd-iframe-close';
                cb.innerHTML = '✕';
                cb.onclick = function (e) { e.stopPropagation(); removeIframe(); };

                iw.appendChild(ifr);
                iw.appendChild(cb);
                wrap.appendChild(iw);
            });
        });
    } // end carousel block


    /* ════════════════════════════════════════
       QTY STEPPER  — completely independent
    ════════════════════════════════════════ */
    var qtyInput = document.getElementById('pd-qty');
    if (qtyInput) {
        var qtyMinus = document.getElementById('qtyMinus');
        var qtyPlus  = document.getElementById('qtyPlus');

        qtyMinus && qtyMinus.addEventListener('click', function () {
            var v = parseInt(qtyInput.value) || 1;
            if (v > 1) qtyInput.value = v - 1;
        });
        qtyPlus && qtyPlus.addEventListener('click', function () {
            var v   = parseInt(qtyInput.value) || 1;
            var max = parseInt(qtyInput.getAttribute('max')) || 9999;
            if (v < max) qtyInput.value = v + 1;
        });
    }


    /* ════════════════════════════════════════
       VARIANT CHIPS  — completely independent
    ════════════════════════════════════════ */
    var chips = Array.from(document.querySelectorAll('.pd-variant-chip'));

    if (chips.length) {
        var atrInput      = document.getElementById('pdAtrInput');
        var addBtn        = document.getElementById('pdAddBtn');
        var addBtnText    = document.getElementById('pdAddBtnText');
        var priceEl       = document.getElementById('pdPrice');
        var mrpEl         = document.getElementById('pdMrp');
        var saveEl        = document.getElementById('pdSave');
        var stockMeta     = document.getElementById('pdStockMeta');
        var stockDot      = document.getElementById('pdStockDot');
        var stockText     = document.getElementById('pdStockText');
        var variantDesc   = document.getElementById('pdVariantDesc');
        var variantReq    = document.getElementById('pdVariantRequired');
        var qtyField      = document.getElementById('pd-qty');
        var qtyMinusBtn   = document.getElementById('qtyMinus');
        var qtyPlusBtn    = document.getElementById('qtyPlus');

        function fmt(n) {
            return '₹' + parseFloat(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
        }

        function updateStock(stock) {
            var inStock = stock > 0;
            if (stockMeta) stockMeta.className = inStock ? 'pd-instock' : 'pd-outstock';
            if (stockDot)  stockDot.className  = 'pd-stock-dot ' + (inStock ? 'pd-stock-dot--in' : 'pd-stock-dot--out');
            if (stockText) stockText.textContent = inStock ? 'In Stock (' + stock + ' units)' : 'Out of Stock';

            if (qtyField) {
                qtyField.max      = inStock ? stock : 1;
                qtyField.disabled = !inStock;
                qtyField.value    = 1;
            }
            if (qtyMinusBtn) qtyMinusBtn.disabled = !inStock;
            if (qtyPlusBtn)  qtyPlusBtn.disabled  = !inStock;
        }

        function updateBtn(state) {
            // state: 'select' | 'instock' | 'outofstock'
            var enabled = (state === 'instock');
            if (addBtn) {
                addBtn.disabled = !enabled;
                addBtn.classList.toggle('pd-btn-disabled', !enabled);
            }
            if (addBtnText) {
                addBtnText.textContent =
                    state === 'select'     ? 'Select a Variant' :
                    state === 'outofstock' ? 'Out of Stock'      : 'Add to Cart';
            }
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                // Block OOS chips
                if (chip.classList.contains('pd-variant-chip--oos')) return;

                // Deselect all → select clicked
                chips.forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');

                var atrId   = chip.dataset.atrId;
                var mrp     = parseFloat(chip.dataset.mrp)     || 0;
                var selling = parseFloat(chip.dataset.selling)  || 0;
                var stock   = parseInt(chip.dataset.stock)      || 0;
                var desc    = chip.dataset.desc                 || '';

                // Hidden input
                if (atrInput) atrInput.value = atrId;

                // Price
                if (priceEl) priceEl.textContent = fmt(selling);
                if (mrp > selling) {
                    if (mrpEl)  { mrpEl.textContent  = fmt(mrp);                    mrpEl.style.display  = ''; }
                    if (saveEl) { saveEl.textContent = 'Save ' + fmt(mrp - selling); saveEl.style.display = ''; }
                } else {
                    if (mrpEl)  mrpEl.style.display  = 'none';
                    if (saveEl) saveEl.style.display = 'none';
                }

                // Stock + qty
                updateStock(stock);

                // Variant description
                if (variantDesc) {
                    variantDesc.textContent  = desc;
                    variantDesc.style.display = desc ? '' : 'none';
                }

                // Hide warning
                if (variantReq) variantReq.style.display = 'none';

                // Button state
                updateBtn(stock > 0 ? 'instock' : 'outofstock');
            });
        });

        /* Guard: block submit if no variant chosen */
        var cartForm = document.getElementById('pdCartForm');
        if (cartForm) {
            cartForm.addEventListener('submit', function (e) {
                if (!atrInput || !atrInput.value) {
                    e.preventDefault();
                    if (variantReq) {
                        variantReq.style.display = 'flex';
                        variantReq.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
    } // end chips block

})();
</script>
@endsection
