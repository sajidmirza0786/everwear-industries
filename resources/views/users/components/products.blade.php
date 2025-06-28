<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="card h-100 product-item border-0 shadow-sm">
        <!-- Product Image -->
        <div class="product-img position-relative overflow-hidden bg-white border-bottom p-0">
            <a href="{{ route('listing', $product) }}">
                <img src="{{ url(\Storage::url($product->image ?? '')) }}" alt="{{ $product->name }}" class="img-fluid w-100">
            </a>
        </div>

        <!-- Product Details -->
        <div class="card-body text-center py-3 px-2">
            <h6 class="text-truncate mb-2">{{ $product->name }}</h6>
            <div class="d-flex justify-content-center align-items-center mb-2">
                <h6 class="mb-0 text-primary">₹{{ $product->selling }}</h6>
                @if($product->mrp)
                    <h6 class="mb-0 text-muted ml-2"><del>₹{{ $product->mrp }}</del></h6>
                @endif
            </div>

            @if($product->size || $product->color)
                <div class="d-flex justify-content-center text-muted small">
                    @if($product->size)
                        <span class="mr-2">Size: {{ $product->size }}</span>
                    @endif
                    @if($product->color)
                        <span>Color: {{ $product->color }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="card-footer bg-light d-flex justify-content-between align-items-center px-3 py-2 border-top">
            <a href="{{ route('listing', $product) }}" class="btn btn-sm text-dark d-flex align-items-center">
                <i class="fas fa-eye text-primary mr-2"></i>View Detail
            </a>
            <a href="{{ route('listing', $product) }}" class="btn btn-sm text-dark d-flex align-items-center">
                <i class="fas fa-shopping-cart text-primary mr-2"></i>Add To Cart
            </a>
        </div>
    </div>
</div>
