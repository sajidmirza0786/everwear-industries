@extends('users.master')

@section('seo')
    <title>{{ config('app.name') }} · Trophies, Awards & Corporate Mementos</title>
    <meta name="description" content="Shop bespoke trophies, awards, medals and corporate mementos crafted in India. Bulk orders welcome. Free engraving included.">
@endsection

@section('content')
@php
    use App\Models\Category;
    use App\Models\Product;

    $categories   = Category::where('status', 'enable')
                        ->whereNull('parent_id')
                        ->withCount('products')
                        ->orderByDesc('id')
                        ->latest()
                        ->take(8)
                        ->get();

    $trendingAll      = Product::where('status', 'enable')->latest()->take(8)->get();
    $trendingTrophies = Product::where('status', 'enable')
                            ->whereHas('category', fn($q) => $q->where('name', 'like', '%trophy%')->orWhere('name', 'like', '%trophies%'))
                            ->latest()->take(8)->get();
    $trendingMementos = Product::where('status', 'enable')
                            ->whereHas('category', fn($q) => $q->where('name', 'like', '%medal%'))
                            ->latest()->take(8)->get();

    $flashSale    = Product::where('status', 'enable')
                        ->whereRaw('selling < mrp')
                        ->latest()
                        ->take(4)
                        ->get();

    $bestSellers  = Product::where('status', 'enable')
                        ->orderByDesc('stock')
                        ->take(4)
                        ->get();
@endphp

    @include('users.partials.slider')

    {{-- ── Categories ── --}}
    <section class="section" data-testid="categories-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4 mb-lg-5 reveal">
                <div>
                    <span class="section-eyebrow">Shop by category</span>
                    <h2 class="section-title">A trophy for <em>every story.</em></h2>
                </div>
                <a href="{{ url('collections') }}" class="btn btn-ghost">
                    See all <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if($categories->isEmpty())
                <p class="text-muted-2">No categories available.</p>
            @else
                <div class="row g-3 g-lg-4">
                    @foreach($categories as $cat)
                        <div class="col-6 col-lg-3 reveal">
                            <a href="{{ route('listing', $cat->slug) }}" class="cat-card">
                                @if($cat->image)
                                    <img src="{{ asset('storage/' . $cat->image) }}"
                                         alt="{{ $cat->name }}" loading="lazy">
                                @else
                                    <img src="https://images.pexels.com/photos/7005034/pexels-photo-7005034.jpeg?auto=compress&cs=tinysrgb&w=900"
                                         alt="{{ $cat->name }}" loading="lazy">
                                @endif
                                <div class="cat-card-arrow"><i class="bi bi-arrow-up-right"></i></div>
                                <div class="cat-card-body">
                                    <span class="text-dark">{{ $cat->products_count }} designs</span>
                                    <h5 class="text-dark">{{ $cat->name }}</h5>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ── Trending ── --}}
    <section class="section bg-surface" data-testid="trending-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4 mb-lg-5 reveal">
                <div>
                    <span class="section-eyebrow">Trending now</span>
                    <h2 class="section-title">Loved by clubs &amp; <em>boardrooms.</em></h2>
                </div>
                <ul class="nav nav-tabs-clean d-none d-md-flex" id="trendTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#all">All</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#wmn">Trophies</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mn">Medals</button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">

                {{-- All --}}
                <div class="tab-pane fade show active" id="all">
                    @if($trendingAll->isEmpty())
                        <p class="text-muted-2">No products available.</p>
                    @else
                        <div class="row g-3 g-lg-4">
                            @foreach($trendingAll as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('users.partials.product-card', ['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Trophies --}}
                <div class="tab-pane fade" id="wmn">
                    @if($trendingTrophies->isEmpty())
                        <p class="text-muted-2">No trophies available.</p>
                    @else
                        <div class="row g-3 g-lg-4">
                            @foreach($trendingTrophies as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('users.partials.product-card', ['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Mementos --}}
                <div class="tab-pane fade" id="mn">
                    @if($trendingMementos->isEmpty())
                        <p class="text-muted-2">No mementos available.</p>
                    @else
                        <div class="row g-3 g-lg-4">
                            @foreach($trendingMementos as $product)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @include('users.partials.product-card', ['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- ── Marquee ── --}}
    <div class="marquee" data-testid="marquee-strip">
        <div class="marquee-track">
            <span>Crafted in India <span class="dot">·</span> Free engraving included <span class="dot">·</span>
                7-day pan-India delivery <span class="dot">·</span> 1,200+ corporates served <span class="dot">·</span>
                Bulk orders welcome <span class="dot">·</span></span>
            <span>Crafted in India <span class="dot">·</span> Free engraving included <span class="dot">·</span>
                7-day pan-India delivery <span class="dot">·</span> 1,200+ corporates served <span class="dot">·</span>
                Bulk orders welcome <span class="dot">·</span></span>
        </div>
    </div>

    {{-- ── Flash Sale ── --}}
    @if($flashSale->isNotEmpty())
    <section class="section" data-testid="flash-sale-section">
        <div class="container">
            <div class="row g-4 align-items-center">

                <div class="col-lg-5 reveal">
                    <div class="flash-banner">
                        <span class="hero-eyebrow" style="margin-bottom:.5rem;display:inline-block;">
                            Year-end Drop · 48h
                        </span>
                        <h2 class="display-title" style="font-size:2.4rem;line-height:1.05;">
                            Up to <em>40% off</em> select awards.
                        </h2>
                        <p style="opacity:.85;color:#dadada;">
                            Last-season designs, new-season finish. While stocks last.
                        </p>
                        <div class="countdown" data-countdown>
                            <div class="countdown-box">
                                <div class="num" data-d>02</div>
                                <div class="label">Days</div>
                            </div>
                            <div class="countdown-box">
                                <div class="num" data-h>08</div>
                                <div class="label">Hrs</div>
                            </div>
                            <div class="countdown-box">
                                <div class="num" data-m>14</div>
                                <div class="label">Min</div>
                            </div>
                            <div class="countdown-box">
                                <div class="num" data-s>32</div>
                                <div class="label">Sec</div>
                            </div>
                        </div>
                        <a href="{{ route('listing') }}" class="btn btn-accent mt-4" data-testid="flash-shop-btn">
                            Shop the sale <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-7 reveal">
                    <div class="row g-3">
                        @foreach($flashSale as $product)
                            @php
                                $discount = $product->mrp > 0
                                    ? round((($product->mrp - $product->selling) / $product->mrp) * 100)
                                    : 0;
                            @endphp
                            <div class="col-6">
                                <div class="product-card"
                                     data-product='{"id":"{{ $product->id }}","title":"{{ addslashes($product->name) }}","price":{{ $product->selling }}}'>
                                    <div class="product-media">
                                        @if($discount > 0)
                                            <span class="product-tag sale">-{{ $discount }}%</span>
                                        @endif
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.pexels.com/photos/164005/pexels-photo-164005.jpeg?auto=compress&cs=tinysrgb&w=600' }}"
                                             alt="{{ $product->name }}" loading="lazy">
                                        <div class="product-quick-cta">
                                            <a href="{{ route('listing', $product->slug) }}" class="btn">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                    <h6 class="product-title">{{ $product->name }}</h6>
                                    <div class="product-meta">
                                        <span class="product-price">&#8377; {{ number_format($product->selling, 2) }}</span>
                                        @if($product->mrp > $product->selling)
                                            <span class="product-price-old">&#8377; {{ number_format($product->mrp, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

    {{-- ── Promo Banners ── --}}
    @php
        $promoCats = Category::where('status', 'enable')->whereNotNull('image')->take(2)->get();
    @endphp
    @if($promoCats->count() >= 2)
    <section class="section-tight" data-testid="promo-banners">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-8 reveal">
                    <a href="{{ route('listing', $promoCats[0]->slug) }}" class="promo-banner d-block"
                       style="background-image:url('{{ asset('storage/' . $promoCats[0]->image) }}')">
                        <div class="promo-banner-body">
                            <span class="hero-eyebrow" style="opacity:.95;">Editorial</span>
                            <h3>{{ $promoCats[0]->name }}</h3>
                            @if($promoCats[0]->description)
                                <p class="mb-3" style="max-width:380px;opacity:.92;font-size:14px;">
                                    {{ $promoCats[0]->description }}
                                </p>
                            @endif
                            <span class="btn btn-light btn-sm">
                                Discover <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 reveal">
                    <a href="{{ route('listing', $promoCats[1]->slug) }}"
                       class="promo-banner d-block h-100"
                       style="aspect-ratio:auto;min-height:100%;background-image:url('{{ asset('storage/' . $promoCats[1]->image) }}')">
                        <div class="promo-banner-body">
                            <span class="hero-eyebrow" style="opacity:.95;">For Boardrooms</span>
                            <h3>{{ $promoCats[1]->name }}</h3>
                            <span class="btn btn-light btn-sm">Shop now</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ── Best Sellers ── --}}
    <section class="section bg-surface" data-testid="best-sellers">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4 mb-lg-5 reveal">
                <div>
                    <span class="section-eyebrow">Best sellers</span>
                    <h2 class="section-title">Awarded by <em>thousands</em>.</h2>
                </div>
                <a href="{{ route('listing') }}" class="btn btn-ghost">
                    Shop all <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if($bestSellers->isEmpty())
                <p class="text-muted-2">No products available.</p>
            @else
                <div class="row g-3 g-lg-4">
                    @foreach($bestSellers as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('users.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ── Testimonials ── --}}
    <section class="section" data-testid="testimonials-section">
        <div class="container">
            <div class="row mb-4 mb-lg-5 reveal">
                <div class="col-lg-7">
                    <span class="section-eyebrow">What clients say</span>
                    <h2 class="section-title">Trusted across <em>India.</em></h2>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <blockquote>"500 medals in 6 days for our city marathon — pristine engraving, perfect packing. Everwear is on speed-dial now."</blockquote>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/762020/pexels-photo-762020.jpeg?auto=compress&cs=tinysrgb&w=200" alt="">
                            <div><strong>Aarav S.</strong><small>Mumbai Run Club · Verified</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <blockquote>"The Royal Series cup we ordered for our 25-year client felt like an heirloom. Quality you can feel in your hand."</blockquote>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=200" alt="">
                            <div><strong>Priya N.</strong><small>HR Lead, Infotech · Bangalore</small></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <blockquote>"They made design proofs for free, kept revising till our logo sat just right. Old-school customer care."</blockquote>
                        <div class="testimonial-author">
                            <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=200" alt="">
                            <div><strong>Rohan K.</strong><small>Principal, DPS · Delhi</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Brand Strip ── --}}
    <section class="section-tight border-top-soft border-bottom-soft" data-testid="brand-strip-section">
        <div class="container">
            <div class="brand-strip">
                <span class="b">Tata</span>
                <span class="b">Infosys</span>
                <span class="b">HDFC</span>
                <span class="b">DPS</span>
                <span class="b">Reliance</span>
                <span class="b">Wipro</span>
                <span class="b">IIT-M</span>
            </div>
        </div>
    </section>

    {{-- ── Newsletter ── --}}
    <section class="section" data-testid="newsletter-section">
        <div class="container">
            <div class="newsletter reveal">
                <span class="hero-eyebrow" style="opacity:.85;color:#e7d6b3;">The Everwear Letter</span>
                <h2 class="display-title" style="font-size:2.4rem;max-width:680px;margin:.5rem auto 1rem;">
                    Get <em>10% off</em> your first order, plus new-arrival previews.
                </h2>
                <p style="color:#cdc4b1;max-width:520px;margin:0 auto 1.75rem;font-size:14px;">
                    A short note twice a month — new designs, gift ideas, and bulk-order offers.
                </p>
                <form class="d-flex flex-column flex-sm-row gap-2 justify-content-center"
                      style="max-width:480px;margin:0 auto;"
                      onsubmit="event.preventDefault(); window.toast('Welcome to the Everwear Letter','envelope-paper');">
                    <input type="email" class="form-control" placeholder="your@email.com"
                           required data-testid="newsletter-email-input">
                    <button class="btn btn-accent" type="submit" data-testid="newsletter-submit-btn">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

@endsection