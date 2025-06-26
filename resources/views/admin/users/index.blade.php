@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('breadcrumbs')
<li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-white">User Management</h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
                <i class="bx bx-plus-circle me-1"></i> Add New User
            </a>
        </div>
        <div class="card-body p-4">
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
            
            {{-- Search Box --}}
            <div class="row mb-4">
                <div class="col-md-6 offset-md-6 col-lg-4 offset-lg-8">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or mobile..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bx bx-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Clear Search">
                                    <i class="bx bx-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle table-sm">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Profile</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">User Type</th>
                            <th scope="col">Status</th>
                            <th scope="col">CreatedAt</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center text-muted" style="width: 40px; height: 40px;">
                                            <i class="bx bx-user fs-5"></i> {{-- Changed from bi-person-fill to bx-user --}}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'employee' ? 'info' : 'primary') }}">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->status == 'enable')
                                        <span class="badge bg-success">Enabled</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $user->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm" title="View Details">
                                            <i class="bx bx-show-alt"></i> {{-- Changed from bi-eye to bx-show-alt --}}
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit User">
                                            <i class="bx bx-edit"></i> 
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal-{{ $user->id }}" title="Delete User">
                                            <i class="bx bx-trash"></i> 
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Delete Confirmation Modal for each user --}}
                            <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel-{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="modal-content">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteUserModalLabel-{{ $user->id }}">Confirm Deletion</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete user "<strong>{{ $user->name }}</strong>"? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-4"> {{-- Changed pt-4 to mt-4 for consistency --}}
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
{{-- Removed Bootstrap Icons CDN link as requested --}}
<style>
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 0;
        padding: 1rem 1.5rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f2f2f2;
    }
    /* Style for profile picture placeholder */
    .bg-light.rounded-circle {
        border: 1px solid #dee2e6;
    }
    .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>
@endsection

@section('scripts')
    {{-- No specific JavaScript for this table, but you can add custom scripts here --}}
@endsection