@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    {{ isset($product) ? 'Edit' : 'Add New' }}
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <h6 class="font-weight-bolder mb-0">
                {{ isset($product) ? 'Edit Product: ' . $product->name : 'Create New Product' }}
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
                        {{ isset($product) ? 'Update Product Details' : 'Add Product Details' }}
                    </h5>
                    <p class="mb-0">
                        {{ isset($product) ? 'Modify the information for this product.' : 'Fill in the required information for the new product.' }}
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($product))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                {{-- Ensure this select has the ID 'category_id' --}}
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="e.g., Smart TV, Running Shoes" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="code" class="form-label">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $product->code ?? '') }}" placeholder="e.g., PROD001, EL005" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mrp" class="form-label">MRP (Maximum Retail Price) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('mrp') is-invalid @enderror" id="mrp" name="mrp" value="{{ old('mrp', $product->mrp ?? '') }}" placeholder="e.g., 999.99" required>
                                @error('mrp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="selling" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('selling') is-invalid @enderror" id="selling" name="selling" value="{{ old('selling', $product->selling ?? '') }}" placeholder="e.g., 899.50" required>
                                @error('selling')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="gram_weight" class="form-label">Product Weight (Gram) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('gram_weight') is-invalid @enderror" id="gram_weight" name="gram_weight" value="{{ old('gram_weight', $product->gram_weight ?? '') }}" placeholder="e.g., 0,12,155" required>
                                @error('gram_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Product Stock Qty <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock ?? '') }}" placeholder="e.g., 0,12,155" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="size" class="form-label">Product Size (Optional)</label>
                                <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size', $product->size ?? '') }}" placeholder="e.g., 1 inch etc">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="color" class="form-label">Product Color (Optional)</label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $product->color ?? '') }}" placeholder="e.g., Blue, White etc">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="enable" {{ old('status', $product->status ?? 'enable') == 'enable' ? 'selected' : '' }}>Enable</option>
                                    <option value="disable" {{ old('status', $product->status ?? 'enable') == 'disable' ? 'selected' : '' }}>Disable</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Product Image (Optional) 
                                    <span class="text-danger">Image size (800*800px)</span></label>
                                </label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (isset($product) && $product->image)
                                    <div class="mt-2">
                                        Current Image: <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3 font-weight-bolder text-info text-gradient">SEO Information (Optional)</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $product->title ?? '') }}" placeholder="Optimize for search engines">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="keyword" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('keyword') is-invalid @enderror" id="keyword" name="keyword" value="{{ old('keyword', $product->keyword ?? '') }}" placeholder="comma-separated keywords">
                                @error('keyword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $product->description ?? '') }}" placeholder="A brief summary for search results">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="long_description" class="form-label">Long Description (for product page content)</label>
                                {{-- Ensure this textarea has the ID 'long_description' --}}
                                <textarea class="form-control @error('long_description') is-invalid @enderror" id="long_description" name="long_description" rows="5" placeholder="Detailed description of the product">{{ old('long_description', $product->long_description ?? '') }}</textarea>
                                @error('long_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn {{ isset($product) ? 'btn-warning' : 'btn-primary' }} mt-4 mb-0">
                                <i class="bx {{ isset($product) ? 'bx-sync' : 'bx-save' }} me-2"></i>
                                {{ isset($product) ? 'Update Product' : 'Create Product' }}
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-4 mb-0 ms-2">Cancel</a>
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

        // Initialize Select2 on the category dropdown
        // Ensure the ID '#category_id' matches the select element's ID in your HTML
        $('#category_id').select2({
            placeholder: "Select Category", // Updated placeholder
            allowClear: true // Option to clear the selected value
        });
    });
</script>
@endsection