@extends('users.master')

@section('seo')
    <title>Order Confirmation | On Jewel</title>
    <meta name="description" content="Thank you for your order at On Jewel. View your order details.">
@endsection

@section('content')
    <!-- Page Header -->
    <div class="container-fluid bg-secondary text-dark mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
            <h1 class="display-5 text-uppercase font-weight-bold">Order Confirmation</h1>
            <nav>
                <ol class="breadcrumb justify-content-center bg-transparent">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-dark">Home</a></li>
                    <li class="breadcrumb-item text-dark active">Order Confirmation</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div class="container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        </div>
    @endif

    <!-- Order Confirmation -->
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12">
                <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success display-4 mb-2"></i>
                        <h3 class="font-weight-bold">Thank You for Your Order!</h3>
                        <p class="text-muted">Your order <strong>#{{ $order->id }}</strong> has been placed successfully.</p>
                    </div>

                    <h5 class="mb-3">Order Summary</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-center table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->quantity * $item->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>₹{{ number_format($order->total, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <strong>₹{{ number_format($order->shipping_charge, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mb-0">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0">₹{{ number_format($order->total + $order->shipping_charge, 2) }}</h5>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Shipping Address</h5>
                    <p class="mb-1"><strong>{{ $order->name }}</strong></p>
                    <p class="mb-1">{{ $order->address }}, {{ $order->locality }}</p>
                    <p class="mb-1">{{ $order->city }}, {{ $order->state }} - {{ $order->zipcode }}</p>
                    <p class="mb-1">Mobile: {{ $order->mobile }}</p>
                    <p class="mb-3">Email: {{ $order->email }}</p>

                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow">
                            <i class="fas fa-shopping-bag mr-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .breadcrumb-item + .breadcrumb-item::before {
            content: "-";
            color: #fff;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        @media (max-width: 767px) {
            .table {
                font-size: 0.9rem;
            }
            .btn {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
@endsection
