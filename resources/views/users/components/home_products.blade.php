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
            @include('users.components.product_3')
            @endforeach
        @endif
    </div>
</div>
