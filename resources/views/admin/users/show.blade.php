@extends('admin.master') {{-- Assumes you have an admin master layout --}}

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $user->name }}'s Profile</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row gx-4 gy-4">
        {{-- Left Column: Profile Card & Quick Actions --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 user-profile-card h-100">
                <div class="card-body p-4 text-center">
                    <div class="profile-picture-container mb-3">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="rounded-circle profile-picture shadow-sm">
                        @else
                            <div class="profile-picture-placeholder rounded-circle bg-light d-flex justify-content-center align-items-center text-muted shadow-sm">
                                <i class="bx bx-user-circle fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <p class="text-muted fs-6">{{ $user->email }}</p>
                    <p class="text-muted fs-6">{{ $user->uuid }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'employee' ? 'info' : 'primary') }} fs-6 px-3 py-2 rounded-pill">
                            <i class="bx bx-shield-alt me-1"></i> {{ ucfirst($user->user_type) }}
                        </span>
                        <span class="badge bg-{{ $user->status == 'enable' ? 'success' : 'danger' }} fs-6 px-3 py-2 rounded-pill">
                            <i class="bx bx-power-off me-1"></i> {{ ucfirst($user->status) }}
                        </span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-lg">
                            <i class="bx bx-edit-alt me-2"></i> Edit Profile
                        </a>
                        {{-- Example: Impersonate user (requires custom logic/package) --}}
                        {{-- <a href="#" class="btn btn-secondary btn-lg">
                            <i class="bx bx-user-pin me-2"></i> Impersonate
                        </a> --}}
                        {{-- <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="bx bx-trash me-2"></i> Delete User
                        </button> --}}
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bx bx-arrow-back me-2"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Detailed Information Sections --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary p-3">
                    <h5 class="mb-0 text-white">Detailed Information</h5>
                </div>
                <div class="card-body p-4">

                    {{-- Personal & Contact Information --}}
                    <h5 class="mb-3 text-primary"><i class="bx bx-info-circle me-2"></i> Personal & Contact Information</h5>
                    <dl class="row mb-4 user-detail-list">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8 fw-bold">{{ $user->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $user->email }}
                            @if($user->email_verified_at)
                                <span class="badge bg-success ms-2"><i class="bx bx-check-circle me-1"></i> Verified</span>
                            @else
                                <span class="badge bg-warning text-dark ms-2"><i class="bx bx-x-circle me-1"></i> Not Verified</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Mobile:</dt>
                        <dd class="col-sm-8"><i class="bx bx-phone me-1"></i> {{ $user->mobile ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">UUID:</dt>
                        <dd class="col-sm-8"><code>{{ $user->uuid }}</code></dd>
                    </dl>

                    <hr class="my-4">

                    {{-- Address Information --}}
                    <h5 class="mb-3 text-primary"><i class="bx bx-map me-2"></i> Address Details</h5>
                    <dl class="row mb-4 user-detail-list">
                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $user->address ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Locality:</dt>
                        <dd class="col-sm-8">{{ $user->locality ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">City:</dt>
                        <dd class="col-sm-8">{{ $user->city ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">State:</dt>
                        <dd class="col-sm-8">{{ $user->state ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Zipcode:</dt>
                        <dd class="col-sm-8">{{ $user->zipcode ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Country Code:</dt>
                        <dd class="col-sm-8">{{ $user->country_code ?? 'N/A' }}</dd>
                    </dl>

                    <hr class="my-4">

                    {{-- Account & System Information --}}
                    <h5 class="mb-3 text-primary"><i class="bx bx-cog me-2"></i> Account & System Information</h5>
                    <dl class="row mb-4 user-detail-list">
                        <dt class="col-sm-4">Account Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $user->status == 'enable' ? 'success' : 'danger' }} fs-6">
                                {{ ucfirst($user->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">User Type:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'employee' ? 'info' : 'primary') }} fs-6">
                                {{ ucfirst($user->user_type) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Password Set:</dt>
                        <dd class="col-sm-8">
                            @if($user->password) {{-- Check if password hash exists --}}
                                <span class="badge bg-success"><i class="bx bx-lock-alt me-1"></i> Yes</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bx bx-lock-open-alt me-1"></i> No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Local Password:</dt> {{-- Assuming 'local_password' might be for a different auth method --}}
                        <dd class="col-sm-8">{{ $user->local_password ? 'Set' : 'Not Set' }}</dd>

                        <dt class="col-sm-4">IP Address:</dt>
                        <dd class="col-sm-8">{{ $user->ip_address ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Created At:</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('d M Y, h:i A') }}</dd>

                        <dt class="col-sm-4">Last Updated:</dt>
                        <dd class="col-sm-8">{{ $user->updated_at->format('d M Y, h:i A') }}</dd>
                    </dl>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete user "<strong>{{ $user->name }} ({{ $user->email }})</strong>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete User</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
{{-- Boxicons CDN should be in your admin.master --}}
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
    .user-profile-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .profile-picture-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #fff; /* White border around picture */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Subtle shadow */
        background-color: #f0f2f5; /* Background for placeholder */
    }
    .profile-picture {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-picture-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 5rem; /* Larger icon for placeholder */
        color: #adb5bd; /* Lighter gray for placeholder icon */
    }
    .user-detail-list dt {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .user-detail-list dd {
        margin-bottom: 0.5rem;
    }
    .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>
@endsection

@section('scripts')
    {{-- No specific JavaScript needed for this static display --}}
@endsection