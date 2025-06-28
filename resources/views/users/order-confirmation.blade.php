@extends('users.master')

@section('seo')
    <title>Order Confirmation | On Jewel</title>
    <meta name="description" content="Thank you for your order at On Jewel. View your order details.">
@endsection

@section('content')
    <!-- Notifications -->
    @if (session('success'))
        <div class="container-fluid py-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="container-fluid py-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Page Header Start -->
    <div class="container-fluid bg-dark text-light mb-5" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('{{ url('images/order-bg.jpg') }}') center/cover no-repeat;">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
            <h1 class="font-weight-bold text-uppercase mb-3 text-light">Order Confirmation</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-light">Home</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Order Confirmation</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Order Confirmation Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">
            <div class="col-lg-8 col-md-12 mx-auto">
                <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                    <h4 class="font-weight-bold mb-4">Thank You for Your Order!</h4>
                    <p class="text-muted mb-4">Your order #{{ $order->id }} has been placed successfully. We'll send you a confirmation email soon.</p>
                    <h5 class="font-weight-bold mb-3">Order Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ $item->quantity * $item->price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Subtotal</h6>
                        <h6>₹{{ $order->total }}</h6>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <h6>Shipping</h6>
                        <h6>₹{{ $order->shipping_charge }}</h6>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="font-weight-bold">Total</h5>
                        <h5 class="font-weight-bold">₹{{ $order->total + $order->shipping_charge }}</h5>
                    </div>
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Confirmation End -->

    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "-";
            color: #fff;
        }
        @media (max-width: 767px) {
            .table {
                font-size: 0.9rem;
            }
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
    </style>

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
@endsection