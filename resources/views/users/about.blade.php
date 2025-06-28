@extends('users.master')

@section('seo')
    <title>About Us | Company Profile</title>
@endsection

@section('content')

<!-- Page Header Start -->
<div class="container-fluid bg-secondary text-white mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">
        <h1 class="display-4 font-weight-bold text-uppercase mb-3">Company Profile</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
            <p class="m-0 px-2 text-dark">-</p>
            <p class="m-0 text-dark">Company Profile</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- About Section Start -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title px-4">
            <span class="px-3 border-bottom border-primary">The Onjewel</span>
        </h2>
        <h3><span class="px-3 border-bottom border-primary">{{ settings()->heading??'' }}</span></h3>
        <p class="text-muted mt-3 w-75 mx-auto">
            Discover our journey, values, and commitment to delivering stylish and quality artificial jewelry.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="bg-light p-4 p-md-5 rounded shadow-sm">
                {!! settings()->long_about_description !!}
            </div>
        </div>
    </div>
</div>
<!-- About Section End -->

@endsection
