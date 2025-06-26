@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row gx-4 gy-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Product Images</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#imageUploadCollapse" aria-expanded="false" aria-controls="imageUploadCollapse">
                        <i class="bi bi-upload me-1"></i> Upload More
                    </button>
                </div>
                <div class="card-body text-center p-4">
                    <div class="mb-4 main-image-container">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow main-product-image" alt="{{ $product->name }}">
                        @else
                            <img src="https://via.placeholder.com/600x600?text=No+Main+Image" class="img-fluid rounded shadow main-product-image" alt="No Main Image">
                        @endif
                    </div>

                    @if($product->images->count() > 0)
                        <h6 class="text-muted border-bottom pb-2 mb-3">Additional Images</h6>
                        <div class="row g-2 justify-content-center additional-images-gallery">
                            @foreach($product->images as $image)
                                <div class="col-4 col-md-3 col-lg-2">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail thumbnail-img w-100" alt="Product thumbnail" data-full-size="{{ asset('storage/' . $image->image_path) }}">
                                        {{-- Add delete button for each additional image --}}
                                        <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" class="image-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-image-btn" title="Delete Image">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted fst-italic mt-3">No additional images uploaded.</p>
                    @endif

                    <div class="collapse mt-4" id="imageUploadCollapse">
                        <hr>
                        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data" class="text-start">
                            @csrf
                            <div class="mb-3">
                                <label for="additional_images" class="form-label fw-semibold">Select Images to Upload 
                                    <span class="text-danger">Image size (800*800px)</span></label>
                                </label>
                                <input type="file" name="image" id="additional_images" class="form-control" accept="image/*" required>
                                <small class="text-muted">You can select multiple image files.</small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload Images
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 text-white">Product Information</h5>
                </div>
                <div class="card-body p-4">
                    <h3 class="fw-bold text-dark">{{ $product->name }}</h3>
                    <p class="text-muted mb-3 fs-6">{{ $product->title ?? 'No product title provided.' }}</p>

                    <hr>

                    <dl class="row mb-0 product-details-list">
                        <dt class="col-sm-4 text-muted">Category:</dt>
                        <dd class="col-sm-8 fw-bold">{{ $product->category->name ?? 'Uncategorized' }}</dd>

                        <dt class="col-sm-4 text-muted">Product Code:</dt>
                        <dd class="col-sm-8">{{ $product->code }}</dd>

                        <dt class="col-sm-4 text-muted">Slug:</dt>
                        <dd class="col-sm-8"><code>{{ $product->slug }}</code></dd>

                        <dt class="col-sm-4 text-muted">MRP:</dt>
                        <dd class="col-sm-8 fs-5 text-decoration-line-through">₹{{ number_format($product->mrp, 2) }}</dd>

                        <dt class="col-sm-4 text-muted">Selling Price:</dt>
                        <dd class="col-sm-8 fs-4 fw-bolder text-success">₹{{ number_format($product->selling, 2) }}</dd>

                        <dt class="col-sm-4 text-muted">Product Stock:</dt>
                        <dd class="col-sm-8">{{ $product->stock??'' }}</dd>

                        <dt class="col-sm-4 text-muted">Product Weight:</dt>
                        <dd class="col-sm-8">{{ $product->gram_weight??'' }}</dd>

                        @if($product->size)
                            <dt class="col-sm-4 text-muted">Size:</dt>
                            <dd class="col-sm-8"><span class="badge bg-secondary fs-6">{{ $product->size }}</span></dd>
                        @endif

                        @if($product->color)
                            <dt class="col-sm-4 text-muted">Color:</dt>
                            <dd class="col-sm-8">
                                <span class="badge" style="background-color: {{ $product->color }}; color: {{ (str_starts_with($product->color, '#') && hexdec(substr($product->color,1,2)) + hexdec(substr($product->color,3,2)) + hexdec(substr($product->color,5,2)) > 381) ? '#333' : '#FFF' }};">
                                    {{ $product->color }}
                                </span>
                            </dd>
                        @endif

                        <dt class="col-sm-4 text-muted">Status:</dt>
                        <dd class="col-sm-8">
                            @if($product->status === 'enable')
                                <span class="badge bg-success fs-6">Enabled</span>
                            @else
                                <span class="badge bg-danger fs-6">Disabled</span>
                            @endif
                        </dd>

                        @if($product->keyword)
                            <dt class="col-sm-4 text-muted">Keywords:</dt>
                            <dd class="col-sm-8">{{ $product->keyword }}</dd>
                        @endif
                    </dl>

                    <hr class="my-4">

                    <h5>Description</h5>
                    @if($product->description)
                        <p class="mb-3">{{ $product->description }}</p>
                    @else
                        <p class="text-muted fst-italic mb-3">No short description available.</p>
                    @endif

                    @if($product->long_description)
                        <h6 class="mt-4 text-dark">Detailed Description:</h6>
                        <div class="bg-light p-3 rounded border">
                            <p class="mb-0 text-dark">{!! nl2br(e($product->long_description)) !!}</p>
                        </div>
                    @endif

                    <hr class="my-4">

                    <p class="text-muted small mb-1"><strong>Created At:</strong> {{ $product->created_at->format('d M Y, h:i A') }}</p>
                    <p class="text-muted small"><strong>Last Updated:</strong> {{ $product->updated_at->format('d M Y, h:i A') }}</p>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i> Edit Product
                        </a>
                        <button type="button" class="btn btn-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                            <i class="bi bi-trash3 me-1"></i> Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white m-0" id="deleteProductModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the product "<strong>{{ $product->name }}</strong>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    body {
        background-color: #f8f9fa; /* Light background for admin panel */
    }
    .card {
        border-radius: 0.75rem; /* Slightly more rounded corners */
        overflow: hidden; /* Ensures header corners are rounded */
    }
    .card-header {
        border-bottom: 0; /* Remove default border-bottom */
        padding: 1rem 1.5rem;
    }
    .main-image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 400px; /* Fixed height for consistency */
        background-color: #f0f2f5; /* Light background for image area */
        border-radius: 0.5rem;
        overflow: hidden; /* Hide overflowing parts of the image */
    }
    .main-product-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Ensures entire image is visible */
        border: 1px solid #e9ecef;
        padding: 5px;
        background-color: #fff;
    }
    .additional-images-gallery .thumbnail-img {
        height: 100px;
        object-fit: cover;
        cursor: pointer;
        border: 1px solid #dee2e6;
        transition: border-color 0.2s ease-in-out, transform 0.2s ease-in-out;
    }
    .additional-images-gallery .thumbnail-img:hover {
        border-color: #007bff; /* Primary blue on hover */
        transform: scale(1.05);
    }
    .additional-images-gallery .position-relative {
        position: relative;
    }
    .delete-image-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        z-index: 10;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.8rem;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .product-details-list dt {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .product-details-list dd {
        margin-bottom: 0.5rem;
    }
    .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainImage = document.querySelector('.main-product-image');
        const thumbnails = document.querySelectorAll('.thumbnail-img');

        // Main image swap functionality
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                if (mainImage) {
                    mainImage.src = this.dataset.fullSize || this.src; // Use data-full-size if available, else src
                }
            });
        });

        // Basic form submission for delete images (can be enhanced with AJAX)
        document.querySelectorAll('.image-delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this image?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection