@extends('users.master')

@section('seo')
    <title>Shipping Policy</title>
    <meta name="description" content="Everwear Industries ships across India with 2–5 business day processing and 3–7 business day delivery. Complimentary shipping on select premium and bulk orders with secure tracking.">
    <meta name="keywords" content="Everwear Industries shipping policy, delivery timeline India, order processing, complimentary shipping, order tracking, premium shipping">
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
                    Shipping Policy
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
            POLICY
        </span>
        <h1 class="fw-semibold mb-2" style="font-size:1.6rem;">Shipping Policy</h1>
        <p class="text-muted mb-0" style="font-size:0.93rem;">
            Every creation from Everwear Industries is handled with the utmost care, ensuring it arrives in impeccable condition.
        </p>
    </div>

    <hr class="mb-4">

    {{-- Policy Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-clock text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Order Processing</span>
                </div>
                <p class="mb-1 fw-semibold" style="font-size:0.95rem;">2–5 business days</p>
                <p class="text-muted mb-0" style="font-size:0.78rem;">
                    (Custom-crafted pieces may require additional time to perfect)
                </p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-truck text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Delivery Timeline</span>
                </div>
                <p class="mb-1 fw-semibold" style="font-size:0.95rem;">3–7 business days across India</p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-bag-check text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Shipping</span>
                </div>
                <p class="mb-1 fw-semibold" style="font-size:0.95rem;">Complimentary on select premium and bulk orders</p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-geo-alt text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Tracking</span>
                </div>
                <p class="mb-1 fw-semibold" style="font-size:0.95rem;">Secure tracking details provided upon dispatch</p>
            </div>
        </div>

    </div>

    {{-- Footer note --}}
    <div class="border-start border-2 border-dark ps-3">
        <p class="text-muted mb-0" style="font-size:0.83rem;">
            Our logistics partners are carefully chosen to uphold the integrity and elegance of every piece we deliver.
        </p>
    </div>

</div>

@endsection