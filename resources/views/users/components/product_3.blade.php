<div class="col-lg-3 col-md-6 col-sm-12 pb-1">
    <div class="card product-item border-0 mb-4 h-100">
        <!-- Product Image -->
        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            <img class="img-fluid w-100" src="{{ url(Storage::url($product->image ?? '')) }}" alt="{{ $product->name }}">
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

        <!-- Footer: Actions -->
        <div class="card-footer bg-light d-flex justify-content-between align-items-center px-3 py-1 border-top">
            <!-- View Detail -->
            <a href="{{ route('listing', $product) }}" class="btn btn-sm text-dark d-flex align-items-center">
                <i class="fas fa-eye text-primary mr-2"></i>View Detail
            </a>

            <!-- Add to Cart -->
            <form action="{{ route('cart.add') }}" method="POST" class="mb-0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                @if($product->stock === 0)
                <a href="{{ route('listing', $product) }}" class="btn btn-sm text-danger d-flex align-items-center">
                    Out of Stock
                </a>
                @else
                <button type="submit" class="btn btn-sm text-dark d-flex align-items-center">
                    <i class="fas fa-shopping-cart text-primary mr-2"></i>Add To Cart
                </button>
                @endif
            </form>
        </div>
    </div>
</div>
