@extends('users.master')

@section('seo')
    <title>Returns & Exchanges</title>
    <meta name="description" content="Everwear Industries accepts returns within 7 days of delivery for damaged or incorrect items. Learn about our fair and prompt returns and exchanges process.">
    <meta name="keywords" content="Everwear Industries returns policy, exchange policy, return period, damaged items, incorrect product, refund policy India">
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
                    Returns & Exchanges
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
        <h1 class="fw-semibold mb-2" style="font-size:1.6rem;">Returns & Exchanges</h1>
        <p class="text-muted mb-0" style="font-size:0.93rem;">
            We uphold the highest standards of craftsmanship. Should your experience fall short of expectation, we are committed to resolving it with grace and efficiency.
        </p>
    </div>

    <hr class="mb-4">

    {{-- Policy Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-4">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-calendar-check text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Return Period</span>
                </div>
                <p class="mb-0 fw-semibold" style="font-size:0.95rem;">Within 7 days of delivery</p>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-check2-circle text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Eligible Requests</span>
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.82rem;">
                    <li class="mb-1">Items received in damaged condition</li>
                    <li>Incorrect product delivered</li>
                </ul>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="bg-light rounded-2 p-2 lh-1">
                        <i class="bi bi-x-circle text-dark" style="font-size:1rem;"></i>
                    </span>
                    <span class="fw-medium" style="font-size:0.88rem;">Exclusions</span>
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.82rem;">
                    <li>Personalized or engraved pieces (unless defective or incorrect)</li>
                </ul>
            </div>
        </div>

    </div>

    {{-- Footer note --}}
    <div class="border-start border-2 border-dark ps-3">
        <p class="text-muted mb-0" style="font-size:0.83rem;">
            To initiate a request, kindly connect with our support team along with relevant details and images. Each case is reviewed with care to ensure a fair and prompt resolution.
        </p>
    </div>

</div>

@endsection