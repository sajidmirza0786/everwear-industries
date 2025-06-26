@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">Categories</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="font-weight-bolder mb-0">Category List</h6>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary ">
                    <i class="bx bx-plus-circle me-2"></i>Add New Category
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body px-0 pb-2">
            <div class="p-3">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="search" class="visually-hidden">Search by Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Search by category name..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bx bx-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary" title="Clear Search">
                                    <i class="bx bx-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 table-hover table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">HSN CODE</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Products</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                            <th class="text-secondary opacity-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 ps-3">
                                        {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                    </p>
                                </td>
                                <td>
                                    <h6 class="mb-0 text-sm">{{ $category->name }}</h6>
                                </td>
                                <td>
                                    <h6 class="mb-0 text-sm">{{ $category->hsn??'' }}</h6>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $category->products->count() }}</p>
                                </td>
                                <td>
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" class="avatar avatar-sm me-3" alt="{{ $category->name }}">
                                    @else
                                        <i class="bx bx-image text-muted bx-md me-3"></i>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-sm {{ $category->status == 'enable' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($category->status ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    {{-- <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-original-title="View category" title="View Details">
                                        <i class="bx bx-show-alt"></i>
                                    </a> --}}
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-original-title="Edit category" title="Edit Category">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    {{-- <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-original-title="Delete category" title="Delete Category">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No categories found
                                    @if(request('search'))
                                    matching "{{ request('search') }}".
                                    <a href="{{ route('admin.categories.index') }}">Clear search to see all categories.</a>
                                    @else
                                    . <a href="{{ route('admin.categories.create') }}">Click here to add one.</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center pt-4">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection