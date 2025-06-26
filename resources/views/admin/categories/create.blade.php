@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    {{ isset($category) ? 'Edit' : 'Add New' }}
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <h6 class="font-weight-bolder mb-0">
                {{ isset($category) ? 'Edit Category: ' . $category->name : 'Create New Category' }}
            </h6>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-10 col-md-12 mx-auto">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card p-3">
                <div class="card-header pb-0 text-left bg-transparent">
                    <h5 class="font-weight-bolder text-info text-gradient">
                        {{ isset($category) ? 'Update Category Details' : 'Add Category Details' }}
                    </h5>
                    <p class="mb-0">
                        {{ isset($category) ? 'Modify the information for this category.' : 'Fill in the required information for the new category.' }}
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" placeholder="e.g., Electronics, Fashion" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="hsn" class="form-label">Category HSN (Optional)</label>
                                <input type="text" class="form-control @error('hsn') is-invalid @enderror" id="hsn" name="hsn" value="{{ old('hsn', $category->hsn ?? '') }}" placeholder="HSN Code">
                                @error('hsn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Category Image (Optional)
                                    <span class="text-danger">Image size (800*800px)</span></label>
                                </label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (isset($category) && $category->image)
                                    <div class="mt-2">
                                        Current Image: <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                 <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="enable" {{ old('status', $category->status ?? 'enable') == 'enable' ? 'selected' : '' }}>Enable</option>
                                        <option value="disable" {{ old('status', $category->status ?? 'enable') == 'disable' ? 'selected' : '' }}>Disable</option>
                                    </select>
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3 font-weight-bolder text-info text-gradient">SEO Information (Optional)</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $category->title ?? '') }}" placeholder="Optimize for search engines">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="keyword" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('keyword') is-invalid @enderror" id="keyword" name="keyword" value="{{ old('keyword', $category->keyword ?? '') }}" placeholder="comma-separated keywords">
                                @error('keyword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $category->description ?? '') }}" placeholder="A brief summary for search results">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="long_description" class="form-label">Long Description (for category page content)</label>
                                <textarea class="form-control @error('long_description') is-invalid @enderror" id="long_description" name="long_description" rows="5" placeholder="Detailed description of the category">{{ old('long_description', $category->long_description ?? '') }}</textarea>
                                @error('long_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn {{ isset($category) ? 'btn-success' : 'btn-primary' }} mt-4 mb-0">
                                <i class="bx {{ isset($category) ? 'bx-sync' : 'bx-save' }} me-2"></i>
                                {{ isset($category) ? 'Update Category' : 'Create Category' }}
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary mt-4 mb-0 ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor
        // Ensure the ID '#long_description' matches the textarea element's ID in your HTML
        ClassicEditor
            .create( document.querySelector( '#long_description' ), {
                // Optional: Add more configuration options here if needed
                // toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
                // heading: {
                //     options: [
                //         { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                //         { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                //         { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                //     ]
                // }
            })
            .catch( error => {
                console.error( "CKEditor error:", error ); // Log specific CKEditor errors
            });
    });
</script>
@endsection