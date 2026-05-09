@php
    $discount = ($product->mrp > 0 && $product->mrp > $product->selling)
        ? round((($product->mrp - $product->selling) / $product->mrp) * 100)
        : 0;
@endphp

<div class="product-card reveal"
     data-product='{"id":"{{ $product->id }}","title":"{{ addslashes($product->name) }}","price":{{ $product->selling }}}'>

    <div class="product-media">

        @if($discount > 0)
            <span class="product-tag sale">-{{ $discount }}%</span>
        @elseif($product->created_at->diffInDays() < 30)
            <span class="product-tag new">New</span>
        @endif

        <img class="img-main"
             src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.pexels.com/photos/9324302/pexels-photo-9324302.jpeg?auto=compress&cs=tinysrgb&w=600' }}"
             alt="{{ $product->name }}" loading="lazy">

        <div class="product-actions">
            <button data-add-to-wishlist title="Wishlist">
                <i class="bi bi-heart"></i>
            </button>
        </div>

        <div class="product-quick-cta">
            <a href="{{ route('listing', $product->slug) }}" class="btn">
                View Details
            </a>
        </div>
    </div>

    <a href="{{ route('listing', $product->slug) }}">
        <h6 class="product-title">{{ $product->name }}</h6>
    </a>

    <div class="product-meta">
        <span class="product-price">&#8377; {{ number_format($product->selling, 2) }}</span>
        @if($discount > 0)
            <span class="product-price-old">&#8377; {{ number_format($product->mrp, 2) }}</span>
        @endif
    </div>

    @if($product->color)
        <div class="swatches">
            @foreach(explode(',', $product->color) as $color)
                <span class="swatch" style="background:{{ trim($color) }};"></span>
            @endforeach
        </div>
    @endif

</div>