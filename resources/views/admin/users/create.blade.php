@extends('admin.master')

{{-- Determine if we are creating or editing --}}
@php
    $isEdit = isset($user) && $user->id;
    $formAction = $isEdit ? route('admin.users.update', $user->id) : route('admin.users.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $pageTitle = $isEdit ? 'Edit User: ' . $user->name : 'Add New User';
    $breadcrumbTitle = $isEdit ? 'Edit ' . $user->name : 'Add New User';
@endphp

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $breadcrumbTitle }}</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-white">{{ $pageTitle }}</h5>
        </div>
        <div class="card-body p-4">
            {{-- Session Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($formMethod)

                <div class="row">
                    {{-- Personal Information Section --}}
                    <div class="col-12 mb-3">
                        <h6 class="text-primary"><i class="bx bx-user me-1"></i> Personal Information</h6>
                        <hr class="my-2">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Enter user's full name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Enter user's email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" placeholder="Enter user's mobile number" required>
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input class="form-control @error('profile_picture') is-invalid @enderror" type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        @error('profile_picture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($isEdit && $user->profile_picture)
                            <div class="mt-2 d-flex align-items-center">
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Current Profile Picture" class="img-thumbnail me-2" style="width: 80px; height: 80px; object-fit: cover;">
                                {{-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remove_profile_picture" name="remove_profile_picture" value="1">
                                    <label class="form-check-label" for="remove_profile_picture">Remove current picture</label>
                                </div> --}}
                            </div>
                        @endif
                    </div>

                    {{-- Account Settings Section --}}
                    <div class="col-12 mt-4 mb-3">
                        <h6 class="text-primary"><i class="bx bx-cog me-1"></i> Account Settings</h6>
                        <hr class="my-2">
                    </div>

                    {{-- <div class="col-md-6 mb-3">
                        <label for="user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('user_type') is-invalid @enderror" id="user_type" name="user_type" required>
                            <option value="">Select User Type</option>
                            @foreach(['admin', 'customer', 'employee'] as $type)
                                <option value="{{ $type }}" {{ old('user_type', $user->user_type ?? '') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Account Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="enable" {{ old('status', $user->status ?? '') == 'enable' ? 'selected' : '' }}>Enable</option>
                            <option value="disable" {{ old('status', $user->status ?? '') == 'disable' ? 'selected' : '' }}>Disable</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password @if(!$isEdit)<span class="text-danger">*</span>@else <small class="text-muted">(Leave blank to keep current)</small> @endif</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ $isEdit ? 'Enter new password' : 'Enter password' }}" {{ !$isEdit ? 'required' : '' }}>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ $isEdit ? 'Confirm new password' : 'Confirm password' }}" {{ !$isEdit ? 'required' : '' }}>
                    </div>

                    {{-- Address Information Section --}}
                    <div class="col-12 mt-4 mb-3">
                        <h6 class="text-primary"><i class="bx bx-map me-1"></i> Address Information (Optional)</h6>
                        <hr class="my-2">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Address Line</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address ?? '') }}" placeholder="E.g., 123 Main St.">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="locality" class="form-label">Locality</label>
                        <input type="text" class="form-control @error('locality') is-invalid @enderror" id="locality" name="locality" value="{{ old('locality', $user->locality ?? '') }}" placeholder="E.g., Downtown">
                        @error('locality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city ?? '') }}" placeholder="E.g., New York">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $user->state ?? '') }}" placeholder="E.g., NY">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="zipcode" class="form-label">Zip/Postal Code</label>
                        <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode" name="zipcode" value="{{ old('zipcode', $user->zipcode ?? '') }}" placeholder="E.g., 10001">
                        @error('zipcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="country_code" class="form-label">Country Code</label>
                        <input type="text" class="form-control @error('country_code') is-invalid @enderror" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}" placeholder="E.g., US">
                        @error('country_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bx bx-save me-1"></i> {{ $isEdit ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 0;
        padding: 1rem 1.5rem;
    }
    .text-primary {
        color: #007bff !important; /* Ensure primary color for section headers */
    }
</style>
@endsection

@section('scripts')
    {{-- Any JavaScript for this page can go here --}}
@endsection