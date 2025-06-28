@extends('users.master')

@section('seo')
    <title>Checkout | On Jewel</title>
    <meta name="description" content="Complete your purchase at On Jewel with our secure checkout process.">
@endsection

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary text-white mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
            <h1 class="display-4 font-weight-bold text-uppercase mb-3">Checkout</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
                <p class="m-0 px-2 text-dark">-</p>
                <p class="m-0"><a href="{{ url('cart') }}" class="text-dark">Cart</a></p>
                <p class="m-0 px-2 text-dark">-</p>
                <p class="m-0 text-dark">Checkout</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
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
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container py-2">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 fw-bold">Shipping & Billing Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                @guest
                                    <div class="col-md-12 form-group">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" id="email" name="email" class="form-control rounded" required placeholder="Enter your email">
                                        <small class="form-text text-muted">
                                            We'll use this email to send your order confirmation and updates.
                                        </small>
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <div class="mb-4 rounded p-3 bg-light d-flex justify-content-between align-items-center border">
                                            <div>
                                                <span class="fw-semibold text-dark">You're logged in as:</span>
                                                <span class="ms-2 text-primary">{{ auth()->user()->email }}</span>
                                                <small class="text-muted d-block mt-1">This email will be used for order communication.</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                                                title="Logout from your account">
                                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                                            </button>
                                        </div>
                                    </div>
                                @endguest

                                <div class="col-md-6 form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control rounded" required value="{{ old('name', Auth::user()->name ?? '') }}">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control rounded" required value="{{ old('mobile', auth()->user()->mobile ?? '') }}">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="state" class="form-label">State</label>
                                    <select id="state" name="state" class="form-control rounded" required>
                                        <option value="">Select State</option>
                                        @foreach(App\Models\State::all() as $state)
                                            <option value="{{ $state->name }}" {{ Auth::check() && Auth::user()->state == $state->name ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" id="city" name="city" class="form-control rounded" required value="{{ old('city', Auth::user()->city ?? '') }}">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="zipcode" class="form-label">Zipcode</label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control rounded" required value="{{ old('zipcode', Auth::user()->zipcode ?? '') }}">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for="locality" class="form-label">Locality</label>
                                    <input type="text" id="locality" name="locality" class="form-control rounded" required value="{{ old('locality', Auth::user()->locality ?? '') }}">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for="address" class="form-label">Delivery Address</label>
                                    <textarea id="address" name="address" rows="3" class="form-control rounded" placeholder="Enter delivery address" required>{{ old('address', Auth::user()->address ?? '') }}</textarea>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control rounded" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cod">Cash on Delivery</option>
                                        <option value="prepaid">Online Payment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded">Place Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm rounded-3 sticky-top" style="top: 80px;">
                    <div class="card-header"> 
                        <h5 class="mb-0 fw-bold">Order Summary</h5>
                    </div>
                    <div class="card-body"> 
                        {{-- Product Items --}}
                        @forelse($cartItems as $item)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom"> 
                                <div class="flex-grow-1 me-3">
                                    <h6 class="mb-1 text-dark small">{{ $item->product->name ?? $item->name }}</h6> 
                                    <p class="mb-0 text-muted small">
                                        Qty: {{ $item->quantity }} | 
                                        Ship: ₹{{ number_format($item->shipping_charge, 2) }}
                                    </p> 
                                </div>
                                <span class="fw-semibold text-dark">₹{{ number_format(($item->quantity * $item->price) + $item->shipping_charge, 2) }}</span> {{-- Price bolded --}}
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">Your cart is empty. Add items to see your summary!</div>
                        @endforelse

                        {{-- Summary Totals --}}
                        <div class="d-flex justify-content-between mt-3">
                            <span class="text-dark">Subtotal</span>
                            <span class="fw-semibold text-dark">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-dark">Shipping</span>
                            <span class="fw-semibold text-dark">₹{{ number_format($totalShipping, 2) }}</span>
                        </div>

                        <hr class="my-3"> {{-- More vertical space for the separator --}}

                        <div class="d-flex justify-content-between align-items-baseline fs-5 text-primary"> {{-- Final total highlighted --}}
                            <strong class="fw-bold">Total</strong>
                            <strong class="fw-bold">₹{{ number_format($total + $totalShipping, 2) }}</strong>
                        </div>

                        {{-- Optional: Coupon Code Section (as included in previous modification) --}}
                        {{-- <div class="mt-4">
                            <h6 class="mb-2 fw-bold text-dark">Have a Coupon Code?</h6>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start" placeholder="Enter coupon code">
                                <button class="btn btn-outline-secondary rounded-end" type="button">Apply</button>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ✅ Hidden logout form --}}
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection
