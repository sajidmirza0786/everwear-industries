@extends('users.master')

@section('seo')
    <title>Shopping Cart | On Jewel</title>
    <meta name="description" content="View and manage your shopping cart. Securely proceed to checkout with your selected items.">
@endsection

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary text-white mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
            <h1 class="display-4 font-weight-bold text-uppercase mb-3">Cart</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
                <p class="m-0 px-2 text-dark">-</p>
                <p class="m-0 text-dark">Cart</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Notifications -->
    @if (session('success'))
        <div class="container-xl py-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="container-xl py-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif
    @if (session('warnings'))
        <div class="container-xl py-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warnings') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Desktop Cart Content --}}
    <div class="container py-2 d-none d-lg-block">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <h5 class="mb-0 fw-bold small">Cart Items ({{ count($cartItems) }})</h5>
                        <h5 class="mb-0 fw-bold small text-end">
                            Total: <strong>₹{{ number_format($total + $totalShipping, 2) }}</strong>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($cartItems))
                            <div class="table-responsive">
                                <table class="table align-middle small">
                                    <thead class="bg-light text-uppercase text-muted small">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Shipping</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                            @php
                                            $product = App\Models\Product::whereId($item->product_id)->first();
                                            @endphp
                                            <tr>
                                                <td class="d-flex align-items-center gap-2">
                                                    <img src="{{ url(Storage::url($product->image ?? '')) }}" class="rounded" width="50" height="50" style="object-fit: cover;"> 
                                                    <a href="{{ route('listing', $product) }}" class="text-dark text-decoration-none px-2">
                                                        {{ $product->name }}
                                                    </a>
                                                </td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('cart.update') }}">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <div class="input-group input-group-sm flex-nowrap" style="width: 150px;">
                                                            <button type="button" class="btn btn-outline-dark btn-sm px-2" onclick="updateQty(this, 'down')">−</button>
                                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm text-center border-start-0 border-end-0" readonly>
                                                            <button type="button" class="btn btn-outline-dark btn-sm px-2" onclick="updateQty(this, 'up')">+</button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td>₹{{ number_format($item->shipping_charge, 2) }}</td>
                                                {{-- <td>₹{{ number_format(($item->quantity * $item->price) + $item->shipping_charge, 2) }}</td> --}}
                                                <td>
                                                    <form method="POST" action="{{ route('cart.remove') }}">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-2">
                                <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                                <p class="text-muted">Your cart is empty.</p>
                                <a href="{{ url('/') }}" class="btn btn-primary rounded">Continue Shopping</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm rounded-3 sticky-top" style="top: 80px;">
                    <div class="card-header bg-light">
                        <h5 class="fw-bold mb-0 small">Summary</h5>
                    </div>
                    <div class="card-body small">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Subtotal</strong>
                            <strong>₹{{ number_format($total, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Shipping</strong>
                            <span>₹{{ number_format($totalShipping, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fw-bold">
                            <strong>Total</strong>
                            <strong class="text-primary">₹{{ number_format($total + $totalShipping, 2) }}</strong>
                        </div>
                        <a href="{{ route('checkout') }}" class="btn btn-primary w-100 py-2 small {{ count($cartItems) ? '' : 'disabled' }}">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Cart View --}}
    <div class="container py-2 d-lg-none">
        @if(count($cartItems))
            @foreach($cartItems as $item)
                <div class="card shadow-sm mb-3">
                    <div class="card-body d-flex gap-3 p-2">
                        <img src="{{ url(Storage::url($product->image ?? '')) }}" class="rounded" width="80" height="80" style="object-fit: cover;">
                        <div class="flex-grow-1 px-2">
                            <h6 class="mb-1 small">{{ $product->name }}</h6>
                            <div class="d-flex justify-content-between small">
                                <strong>₹{{ number_format($item->price, 2) }}</strong>
                                <span>Shipping: ₹{{ number_format($item->shipping_charge, 2) }}</span>
                            </div>
                            <div class="mt-2">
                                <form method="POST" action="{{ route('cart.update') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="updateQty(this, 'down')">-</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control text-center" readonly>
                                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="updateQty(this, 'up')">+</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('cart.remove') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-trash me-1"></i> Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-2">
                <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                <p class="text-muted">Your cart is empty.</p>
                <a href="{{ url('/') }}" class="btn btn-primary rounded">Continue Shopping</a>
            </div>
        @endif
    </div>

    {{-- Mobile Footer --}}
    <div class="mobile-cart-footer d-lg-none fixed-bottom bg-white shadow py-1 border-top">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold">
                    <strong>₹{{ number_format($total + $totalShipping, 2) }}</strong>
                </div>
                <small class="text-muted">{{ count($cartItems) }} item{{ count($cartItems) !== 1 ? 's' : '' }}</small>
            </div>
            <a href="{{ route('checkout') }}" class="btn btn-primary px-4 {{ count($cartItems) ? '' : 'disabled' }}">Checkout</a>
        </div>
    </div>

    <script>
        function updateQty(btn, action) {
            const input = btn.parentNode.querySelector('input[name="quantity"]');
            if (action === 'up') input.stepUp();
            else input.stepDown();
            btn.closest('form').submit();
        }
    </script>
@endsection
