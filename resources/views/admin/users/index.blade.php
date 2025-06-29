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
                <table class="table table-hover table-bordered align-middle table-sm small">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Profile</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">State</th>
                            <th scope="col">City</th>
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
                                <td>{{ $user->state??'NA' }}</td>
                                <td>{{ $user->city??'NA' }}</td>
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
                                        {{-- <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-original-title="Delete user" title="Delete Category">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>
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