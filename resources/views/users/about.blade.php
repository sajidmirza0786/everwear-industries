@extends('users.master')

@section('seo')
    <title>About Us · Everwear Industries</title>
    <meta name="description" content="Everwear Industries is a premier manufacturer of bespoke awards, trophies, and corporate gifting solutions. Discover our story, craftsmanship philosophy, and commitment to excellence.">
    <meta name="keywords" content="Everwear Industries, about us, bespoke awards, trophy manufacturer, corporate gifts India, custom engraving, premium awards, craftsmanship, company story">
@endsection

@section('content')
    <section class="about-hero"
        style="background-image:url('https://images.pexels.com/photos/12915232/pexels-photo-12915232.jpeg?auto=compress&cs=tinysrgb&w=2000')"
        data-testid="about-hero">
        <div class="container">
            <div class="hero-eyebrow" style="color:#e7d6b3;border-color:rgba(255,255,255,0.4)">Since 2003</div>
            <h1>The Legend of<br> Award Products.</h1>
            <p style="max-width:480px; color:rgba(255,255,255,0.85);">At Everwear Industries, we craft more than trophies—we
                create enduring symbols of excellence.</p>
        </div>
    </section>

    <section class="section" data-testid="about-story">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 reveal">
                    <span class="section-eyebrow">Our story</span>
                    <h2 class="section-title">The Legend of <em>Award Products.</em></h2>
                    <p class="text-soft" style="font-size:16px;">With a legacy built on precision, artistry, and
                        uncompromising quality, we specialize in premium trophies, medals, and bespoke awards designed to
                        honor achievement in its finest form. Each piece is thoughtfully created to embody prestige, making
                        every recognition moment truly unforgettable.</p>
                    <p class="text-soft" style="font-size:16px;">Trusted by leading corporates, elite institutions, and
                        distinguished events, our work reflects a perfect harmony of craftsmanship and contemporary design.
                    </p>

                    <h3>Why Everwear Industries</h3>

                    <ul class="text-soft" style="font-size:16px; line-height:1.9;">
                        <li>Exquisite craftsmanship with premium materials</li>
                        <li>Bespoke design tailored to your vision</li>
                        <li>Attention to detail at every stage</li>
                        <li>Trusted delivery with uncompromised quality</li>
                    </ul>

                    <p class="text-soft" style="font-size:16px;">
                        At Everwear Industries, every award tells a story—of success,
                        ambition, and legacy.
                    </p>

                    <a href="#" class="btn btn-dark mt-3">Browse the catalogue <i
                            class="bi bi-arrow-right ms-2"></i></a>
                </div>
                <div class="col-lg-6 reveal">
                    <img src="{{ url('users/assets/images/about.jpg') }}"
                        alt="Crystal cut glass" loading="lazy" style="width:100%; aspect-ratio:4/5; object-fit:cover;">
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-surface" data-testid="about-stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 stat-block reveal">
                    <div class="num">4M+</div>
                    <div class="label">Awards delivered</div>
                </div>
                <div class="col-md-3 col-6 stat-block reveal">
                    <div class="num">1,200+</div>
                    <div class="label">Corporate clients</div>
                </div>
                <div class="col-md-3 col-6 stat-block reveal">
                    <div class="num">23</div>
                    <div class="label">Years of craft</div>
                </div>
                <div class="col-md-3 col-6 stat-block reveal">
                    <div class="num">8</div>
                    <div class="label">Materials in studio</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" data-testid="about-values">
        <div class="container">
            <div class="row mb-5 reveal">
                <div class="col-lg-7"><span class="section-eyebrow">Our values</span>
                    <h2 class="section-title">What we keep <em>uncompromised.</em></h2>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div style="border-top:1px solid var(--line-strong); padding-top:18px;">
                        <i class="bi bi-gem" style="color:var(--accent-2); font-size:26px;"></i>
                        <h4 style="margin:14px 0 8px;">Material honesty</h4>
                        <p class="text-soft" style="font-size:14px;">Optical-grade crystal is always optical-grade. Solid
                            brass is always solid. We label everything, transparently.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div style="border-top:1px solid var(--line-strong); padding-top:18px;">
                        <i class="bi bi-tools" style="color:var(--accent-2); font-size:26px;"></i>
                        <h4 style="margin:14px 0 8px;">Hand-finished detail</h4>
                        <p class="text-soft" style="font-size:14px;">Every Royal Series cup is buffed, signed and numbered
                            by our master finisher before it leaves the studio.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div style="border-top:1px solid var(--line-strong); padding-top:18px;">
                        <i class="bi bi-truck" style="color:var(--accent-2); font-size:26px;"></i>
                        <h4 style="margin:14px 0 8px;">On-time, on-spec</h4>
                        <p class="text-soft" style="font-size:14px;">95.4% of orders ship on or before the promised date.
                            The other 4.6% — we make it right.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
