@extends('admin.master')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="card shadow p-4">
                @csrf
                @method('PUT')
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <h4 class="mb-4 text-primary">Homepage Settings</h4>

                <div class="row g-3">
                    {{-- Title, Keywords, Description --}}
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" value="{{ old('title', $settings->title) }}" class="form-control" placeholder="Website Title">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keywords</label>
                        <input type="text" name="keywords" value="{{ old('keywords', $settings->keywords) }}" class="form-control" placeholder="e.g. shop, ecommerce">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <input type="text" name="description" value="{{ old('description', $settings->description) }}" class="form-control" placeholder="Short description for SEO">
                    </div>

                    {{-- Logo and Favicon --}}
                    <div class="col-md-6">
                        <label class="form-label">Logo 
                            <span class="text-danger">Image size (300*200px)</span>
                        </label>
                        <input type="file" name="logo" class="form-control">
                        @if($settings->logo)
                            <img src="{{ asset('storage/' . $settings->logo) }}" class="img-thumbnail mt-2" style="height: 60px;">
                        @endif
                        @error('logo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon
                            <span class="text-danger">Image size (300*200px)</span>
                        </label>
                        <input type="file" name="favicon" class="form-control">
                        @if($settings->favicon)
                            <img src="{{ asset('storage/' . $settings->favicon) }}" class="img-thumbnail mt-2" style="height: 30px;">
                        @endif
                        @error('favicon')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Contact --}}
                    <div class="col-md-6">
                        <label class="form-label">Primary Mobile</label>
                        <input type="text" name="mobile" value="{{ old('mobile', $settings->mobile) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alt Mobile</label>
                        <input type="text" name="alt_mobile" value="{{ old('alt_mobile', $settings->alt_mobile) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Primary Email</label>
                        <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alt Email</label>
                        <input type="email" name="alt_email" value="{{ old('alt_email', $settings->alt_email) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" value="{{ old('address', $settings->address) }}" class="form-control">
                    </div>

                    {{-- About Section --}}
                    <div class="col-12">
                        <label class="form-label">Short About Description</label>
                        <textarea name="short_about_description" class="form-control" rows="2">{{ old('short_about_description', $settings->short_about_description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Long About Description</label>
                        <textarea name="long_about_description" id="long_description" class="form-control">{{ old('long_about_description', $settings->long_about_description) }}</textarea>
                    </div>

                    {{-- Banner Images --}}
                    <div class="col-md-4">
                        <label class="form-label">Banner Image 1
                            <span class="text-danger">Image size (1800*800px)</span>
                        </label>
                        <input type="file" name="banner_image_1" class="form-control">
                        @if($settings->banner_image_1)
                            <img src="{{ asset('storage/' . $settings->banner_image_1) }}" class="img-thumbnail mt-2" style="height: 100px;">
                        @endif
                        @error('banner_image_1')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Banner Image 2
                            <span class="text-danger">Image size (1800*800px)</span>
                        </label>
                        <input type="file" name="banner_image_2" class="form-control">
                        @if($settings->banner_image_2)
                            <img src="{{ asset('storage/' . $settings->banner_image_2) }}" class="img-thumbnail mt-2" style="height: 100px;">
                        @endif
                        @error('banner_image_2')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Banner Image 3
                            <span class="text-danger">Image size (1800*800px)</span>
                        </label>
                        <input type="file" name="banner_image_3" class="form-control">
                        @if($settings->banner_image_3)
                            <img src="{{ asset('storage/' . $settings->banner_image_3) }}" class="img-thumbnail mt-2" style="height: 100px;">
                        @endif
                        @error('banner_image_3')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Other Images --}}
                    <div class="col-md-6">
                        <label class="form-label">Other Image 1
                            <span class="text-danger">Image size (1800*800px)</span>
                        </label>
                        <input type="file" name="other_image_1" class="form-control">
                        @if($settings->other_image_1)
                            <img src="{{ asset('storage/' . $settings->other_image_1) }}" class="img-thumbnail mt-2" style="height: 80px;">
                        @endif
                        @error('other_image_1')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other Image 2
                            <span class="text-danger">Image size (1800*800px)</span>
                        </label>
                        <input type="file" name="other_image_2" class="form-control">
                        @if($settings->other_image_2)
                            <img src="{{ asset('storage/' . $settings->other_image_2) }}" class="img-thumbnail mt-2" style="height: 80px;">
                        @endif
                        @error('other_image_2')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Section Toggles --}}
                    <div class="col-12">
                        <label class="form-check-label d-block mb-2">Toggle Sections</label>
                        <div class="form-check form-switch d-inline-block me-4">
                            <input class="form-check-input" type="checkbox" name="show_banners" value="1" {{ $settings->show_banners ? 'checked' : '' }}>
                            <label class="form-check-label">Show Banners</label>
                        </div>
                        <div class="form-check form-switch d-inline-block me-4">
                            <input class="form-check-input" type="checkbox" name="show_about_section" value="1" {{ $settings->show_about_section ? 'checked' : '' }}>
                            <label class="form-check-label">Show About</label>
                        </div>
                        {{-- <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" name="show_testimonials" value="1" {{ $settings->show_testimonials ? 'checked' : '' }}>
                            <label class="form-check-label">Show Testimonials</label>
                        </div> --}}
                    </div>

                    {{-- Social Links --}}
                    <div class="col-md-4">
                        <label class="form-label">Facebook</label>
                        <input type="text" name="facebook" value="{{ old('facebook', $settings->facebook) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Instagram</label>
                        <input type="text" name="instagram" value="{{ old('instagram', $settings->instagram) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">LinkedIn</label>
                        <input type="text" name="linkedin" value="{{ old('linkedin', $settings->linkedin) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Twitter</label>
                        <input type="text" name="twitter" value="{{ old('twitter', $settings->twitter) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">YouTube</label>
                        <input type="text" name="youtube" value="{{ old('youtube', $settings->youtube) }}" class="form-control">
                    </div>

                    {{-- Footer --}}
                    <div class="col-12">
                        <label class="form-label">Footer Text</label>
                        <textarea name="footer_text" class="form-control" rows="2">{{ old('footer_text', $settings->footer_text) }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary px-4" type="submit">Save Settings</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>

            </form>
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
