@extends('users.master')

@section('seo')
    <title>Our Collections – Everwear Industries | Trophies & Awards for Every Occasion</title>
    <meta name="description"
        content="Browse the complete Everwear Industries collections — from corporate trophies and crystal awards to personalised medals and custom engravings. Find the perfect award for every story.">
    <meta name="keywords"
        content="Everwear Industries collections, trophy categories, award collections, corporate trophies, crystal awards, custom medals, engraved awards, bulk order trophies">
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="border-bottom py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.78rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('welcome') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">
                    Our Collections
                </li>
            </ol>
        </nav>
    </div>
</div>

{{-- ── Categories Grid ── --}}
<section class="section">
    <div class="container">
        @if ($categories->isEmpty())
            <p class="text-muted-2">No collections available yet. Check back soon!</p>
        @else
            <div class="col-card-grid">
                @foreach ($categories as $cat)
                    <div class="reveal" style="transition-delay: {{ $loop->index * 60 }}ms">
                        <a href="{{ route('listing', $cat->slug) }}" class="col-card">

                            {{-- Image --}}
                            <div class="col-card-media">
                                @if ($cat->image)
                                    <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}" loading="lazy">
                                @else
                                    <img src="https://images.pexels.com/photos/7005034/pexels-photo-7005034.jpeg?auto=compress&cs=tinysrgb&w=900"
                                        alt="{{ $cat->name }}" loading="lazy">
                                @endif
                                <div class="col-card-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </div>
                            </div>

                            {{-- Info — fully below image, no overlap --}}
                            <div class="col-card-body">
                                <span class="col-card-count">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                    {{ $cat->products_count }} designs
                                </span>
                                <h3 class="col-card-name">{{ $cat->name }}</h3>
                                <p class="col-card-desc">
                                    @if (!empty($cat->long_description))
                                        {!! Str::limit($cat->long_description, 90) !!}
                                    @else
                                        Premium {{ strtolower($cat->name) }} crafted for every occasion and milestone.
                                    @endif
                                </p>
                                <span class="col-card-cta">
                                    Browse Collection <i class="bi bi-arrow-right ms-1"></i>
                                </span>
                            </div>

                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection