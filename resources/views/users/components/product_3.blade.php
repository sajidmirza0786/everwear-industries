{{-- users/components/product_3.blade.php --}}
<div class="col-lg-3 col-md-4 col-6">
    <div class="product-card">
        {{-- Image --}}
        <div class="product-media">
            <a href="{{ route('listing', $product) }}" class="d-block" style="height:100%">
                <img class="img-main"
                     src="{{ url(Storage::url($product->image ?? '')) }}"
                     alt="{{ $product->name }}"
                     loading="lazy">
            </a>

            {{-- Badges --}}
            @if($product->mrp > $product->selling)
                <span class="product-tag sale">
                    {{ round((($product->mrp - $product->selling) / $product->mrp) * 100) }}% OFF
                </span>
            @endif
            @if($product->stock <= 0)
                <span class="product-tag" style="background:var(--danger,#a8412c);color:#fff;top:auto;bottom:12px">
                    Out of Stock
                </span>
            @endif

            {{-- Quick CTA --}}
            <div class="product-quick-cta">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn">
                            <i class="bi bi-bag" style="margin-right:6px;font-size:11px"></i>
                            Add to Cart
                        </button>
                    </form>
                @else
                    <a href="{{ route('listing', $product) }}" class="btn">View Details</a>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="pc-body">
            <h6 class="product-title">
                <a href="{{ route('listing', $product) }}" class="pc-name">{{ $product->name }}</a>
            </h6>
            <div class="product-meta">
                <span class="product-price">₹{{ number_format($product->selling) }}</span>
                @if($product->mrp > $product->selling)
                    <span class="product-price-old">₹{{ number_format($product->mrp) }}</span>
                @endif
            </div>
            @if($product->size || $product->color)
                <div class="pc-attrs">
                    @if($product->size)<span>{{ $product->size }}</span>@endif
                    @if($product->size && $product->color)<span class="pc-dot">·</span>@endif
                    @if($product->color)<span>{{ $product->color }}</span>@endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Scoped card overrides (complement global product-card styles) */
.pc-body {
    padding: 10px 2px 4px;
}
.pc-name {
    color: var(--ink);
    font-size: 13.5px;
    font-weight: 500;
    letter-spacing: 0.01em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pc-name:hover { color: var(--accent-2); }
.pc-attrs {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: var(--soft-2);
    margin-top: 5px;
}
.pc-dot { color: var(--line-strong); }
</style>