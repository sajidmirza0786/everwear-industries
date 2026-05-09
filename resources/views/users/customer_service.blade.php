@extends('users.master')

@section('seo')
    <title>Customer Service</title>
    <meta name="description" content="Everwear Industries customer service is available Monday to Saturday, 11 AM – 7 PM, with a 24-hour response commitment. Get support for custom designs, bulk orders, and personalized consultations.">
    <meta name="keywords" content="Everwear Industries customer service, support team, custom design consultation, bulk order support, corporate orders, engraving support, contact Everwear">
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="border-bottom py-2 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.78rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('welcome') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">
                    Customer Service
                </li>
            </ol>
        </nav>
    </div>
</div>

{{-- Page Content --}}
<div class="container pb-5" style="max-width:780px;">

    {{-- Header --}}
    <div class="mb-4">
        <span class="badge bg-dark text-white fw-normal mb-2" style="font-size:0.7rem;letter-spacing:0.08em;">
            SUPPORT
        </span>
        <h1 class="fw-semibold mb-2" style="font-size:1.6rem;">Customer Service</h1>
        <p class="text-muted mb-2" style="font-size:0.93rem;">
            At Everwear Industries, excellence extends beyond our products—it defines every interaction. Our dedicated customer service team ensures a seamless, refined experience from initial inquiry to final delivery.
        </p>
        <p class="text-muted mb-0" style="font-size:0.93rem;">
            Whether you seek bespoke award designs, curated recommendations, or assistance with large-scale corporate requirements, our specialists are here to serve with precision and discretion.
        </p>
    </div>

    <hr class="mb-4">

    {{-- Top two cards --}}
    <div class="row g-3 mb-3">

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-clock text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Availability</span>
                </div>
                <p class="mb-0 fw-semibold" style="font-size:0.95rem;">Monday – Saturday, 11:00 AM – 7:00 PM</p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-reply text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Response Commitment</span>
                </div>
                <p class="mb-0 fw-semibold" style="font-size:0.95rem;">Within 24 hours</p>
            </div>
        </div>

    </div>

    {{-- Services card full width --}}
    <div class="border rounded-3 p-3 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="bg-light rounded-2 p-2 lh-1">
                <i class="bi bi-stars text-dark" style="font-size:1rem;"></i>
            </span>
            <span class="fw-medium" style="font-size:0.88rem;">Services Include</span>
        </div>
        <div class="row g-2">
            <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check2 text-dark" style="font-size:0.9rem;"></i>
                    <span style="font-size:0.85rem;">Personalized product consultation</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check2 text-dark" style="font-size:0.9rem;"></i>
                    <span style="font-size:0.85rem;">Custom design & engraving support</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check2 text-dark" style="font-size:0.9rem;"></i>
                    <span style="font-size:0.85rem;">Corporate & bulk order management</span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check2 text-dark" style="font-size:0.9rem;"></i>
                    <span style="font-size:0.85rem;">Real-time order assistance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer note --}}
    <div class="border-start border-2 border-dark ps-3">
        <p class="text-muted mb-0" style="font-size:0.83rem;">
            We pride ourselves on delivering not just products, but a service experience worthy of distinction.
        </p>
    </div>

</div>

@endsection