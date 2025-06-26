<div class="container-fluid pt-5">
    <div class="text-center mb-5">
        <h2 class="section-title px-5">
            <span class="px-3 py-2 rounded bg-white shadow-sm">Trendy Products</span>
        </h2>
        <p class="text-muted mb-0">
            Check out the latest picks loved by our customers. Shop now before they're gone!
        </p>
    </div>

    <div class="row px-xl-5 pb-3">
        @if(randomProducts()->count() > 0)
            @foreach(randomProducts() as $product)
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="card product-item border-0 mb-4">
                        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                            <img class="img-fluid w-100" src="{{ url(\Storage::url($product->image ?? '')) }}" alt="" width="100%">
                        </div>
                        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                            <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
                            <div class="d-flex justify-content-center">
                                <h6>₹{{ $product->selling }}</h6>
                                <h6 class="text-muted ml-2"><del>₹{{ $product->mrp ?? 0 }}</del></h6>
                            </div>
                            @if($product->size || $product->color)
                                <div class="d-flex justify-content-center">
                                    @if($product->size)
                                        <h6>{{ $product->size }}</h6>
                                    @endif
                                    @if($product->color)
                                        <h6 class="text-muted ml-2">{{ $product->color }}</h6>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light border">
                            <a href="" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-eye text-primary mr-1"></i>View Detail
                            </a>
                            <a href="" class="btn btn-sm text-dark p-0">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
