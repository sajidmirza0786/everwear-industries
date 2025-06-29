@extends('users.master')

@section('seo')
    <title>Videos | Company Profile</title>
    <meta name="description" content="Watch our company videos to learn more about our mission, services, and achievements.">
@endsection

@section('content')
@php
    $videos = App\Models\Video::orderByDesc('id')->get();
@endphp
<!-- Page Header Start -->
<div class="container-fluid bg-secondary mb-5 py-5">
    <div class="d-flex flex-column align-items-center justify-content-center text-dark">
        <h1 class="display-5 font-weight-bold text-uppercase mb-3">Videos</h1>
        <nav>
            <ol class="breadcrumb justify-content-center bg-transparent mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-dark">Home</a></li>
                <li class="breadcrumb-item text-dark active" aria-current="page">Videos</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Videos Section Start -->
<div class="container py-2">
    <div class="text-center mb-5">
        <h2 class="section-title px-3"><span class="bg-light px-2">Our Videos</span></h2>
        <p class="text-muted">Get to know us better through our corporate videos, product showcases, and customer stories.</p>
    </div>

    <div class="row g-4">
        @forelse($videos as $video)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 video-card">
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="{{ $video->url }}" 
                            title="{{ $video->title }}" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen width="100%">
                        </iframe>
                    </div>
                    <div class="card-body py-2">
                        <h5 class="card-title text-center text-dark">{{ $video->title }}</h5>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">No videos available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
<!-- Videos Section End -->

<style>
    .video-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 10px;
    }
    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
</style>

@endsection
