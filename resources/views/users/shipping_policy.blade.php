@extends('users.master')

@section('seo')
    <title>Shipping Policy</title>
@endsection

@section('content')

<!-- Page Header Start -->
<div class="container-fluid bg-secondary text-white mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">
        <h1 class="display-4 font-weight-bold text-uppercase mb-3">Shipping Policy</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
            <p class="m-0 px-2">-</p>
            <p class="m-0 text-dark">Shipping Policy</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Shipping Policy Section Start -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title px-4">
            <span class="px-3 border-bottom border-primary">Our Shipping Guidelines</span>
        </h2>
        <p class="text-muted mt-3 w-75 mx-auto">
            Learn more about our order processing, delivery times, and payment methods.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="bg-light p-4 p-md-5 rounded shadow-sm">
                <ul class="list-unstyled mb-4">
                    <li class="mb-3">
                        <h5><i class="fa fa-credit-card text-primary mr-2"></i><strong>How can I pay for my order?</strong></h5>
                        <p class="mb-0 text-muted">You can pay for your orders via online net banking including credit card, debit card, UPI, or bank transfer.</p>
                    </li>

                    <li class="mb-3">
                        <h5><i class="fa fa-truck text-primary mr-2"></i><strong>When will I receive my order?</strong></h5>
                        <p class="mb-0 text-muted">We usually ship your order within <strong>4 working days</strong> of order placement. Delivery times may vary based on location.</p>
                    </li>
                </ul>

                <!-- Optional Additional Description from Settings -->
                @if(settings()->long_about_description)
                    <hr>
                    <div class="mt-4">
                        {!! settings()->long_about_description !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Shipping Policy Section End -->

@endsection
