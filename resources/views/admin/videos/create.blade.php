@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>{{ isset($video) ? 'Edit Video: ' . $video->title : 'Add New Video' }} | Admin Panel</title>
    <meta name="description" content="{{ isset($video) ? 'Edit details for video ' . $video->title : 'Add a new video' }} to the On Jewel admin panel.">
@endsection

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.videos.index') }}">Videos</a></li>
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    {{ isset($video) ? 'Edit' : 'Add New' }}
</li>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <h6 class="font-weight-bolder mb-0">
                    {{ isset($video) ? 'Edit Video: ' . ($video->title ?? 'N/A') : 'Create New Video' }}
                </h6>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8 col-md-10 mx-auto">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card p-3">
                    <div class="card-header pb-0 text-left bg-transparent">
                        <h5 class="font-weight-bolder text-info text-gradient">
                            {{ isset($video) ? 'Update Video Details' : 'Add New Video Details' }}
                        </h5>
                        <p class="mb-0">
                            {{ isset($video) ? 'Modify the information for this video.' : 'Fill in the required information for the new video.' }}
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($video) ? route('admin.videos.update', $video->id) : route('admin.videos.store') }}" method="POST">
                            @csrf
                            @if(isset($video))
                                @method('PUT') {{-- Use PUT method for update requests --}}
                            @endif

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="title" class="form-label">Video Title</label>
                                    <input type="text" class="form-control rounded @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $video->title ?? '') }}" placeholder="e.g., How to Use Our Product">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="url" class="form-label">Video URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control rounded @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $video->url ?? '') }}" placeholder="e.g., https://www.youtube.com/watch?v=yourvideo" required>
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control rounded @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="A brief description of the video content">{{ old('description', $video->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn {{ isset($video) ? 'btn-success' : 'btn-primary' }} btn-lg rounded-pill px-5 me-2">
                                    <i class="bx {{ isset($video) ? 'bx-sync' : 'bx-save' }} me-2"></i>
                                    {{ isset($video) ? 'Update Video' : 'Create Video' }}
                                </button>
                                <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary btn-lg rounded-pill px-5">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
