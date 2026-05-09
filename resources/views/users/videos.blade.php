@extends('users.master')

@section('seo')
    <title>Videos · Everwear Industries</title>
    <meta name="description" content="Watch our company videos to learn more about our mission, services, and achievements.">
    <meta name="keywords" content="Everwear Industries videos, company profile, product showcase, corporate videos, award videos, trophy videos">
@endsection

@section('content')
@php
    $videos = App\Models\Video::orderByDesc('id')->get();
@endphp

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
                    Videos
                </li>
            </ol>
        </nav>
    </div>
</div>

{{-- Page Content --}}
<div class="container pb-5" style="max-width:1240px;">

    {{-- Header --}}
    <div class="mb-4">
        <span class="badge bg-dark text-white fw-normal mb-2" style="font-size:0.7rem;letter-spacing:0.08em;">
            MEDIA
        </span>
        <h1 class="fw-semibold mb-2" style="font-size:1.6rem;font-family:var(--font-display);">Our Videos</h1>
        <p class="mb-0" style="color:var(--soft);font-size:0.93rem;">
            Get to know us better through our corporate videos, product showcases, and customer stories.
        </p>
    </div>

    <hr class="mb-4" style="border-color:var(--line);">

    {{-- Videos Grid --}}
    @if($videos->isEmpty())
        <div class="text-center py-5" style="color:var(--soft);">
            <i class="bi bi-camera-video" style="font-size:2.5rem;color:var(--accent);display:block;margin-bottom:1rem;"></i>
            <p class="mb-0" style="font-size:0.93rem;">No videos available at the moment.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($videos as $video)
                <div class="col-lg-4 col-md-6">
                    <div style="background:var(--bg);border:1px solid var(--line);transition:box-shadow 0.3s ease,transform 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(26,20,16,0.10)'"
                         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">

                        {{-- Video Embed --}}
                        <div class="ratio ratio-16x9">
                            <iframe
                                src="{{ $video->url }}"
                                title="{{ $video->title }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>
                        </div>

                        {{-- Title --}}
                        <div style="padding:14px 16px;border-top:1px solid var(--line);">
                            <p class="mb-0 fw-medium" style="font-size:0.88rem;color:var(--ink);letter-spacing:0.01em;">
                                {{ $video->title }}
                            </p>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection