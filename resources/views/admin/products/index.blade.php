@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">Products</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="font-weight-bolder mb-0">Product List</h6>
                <div>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-plus-circle me-2"></i>Add New Product
                    </a>
                    <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-trash me-2"></i>Trash
                        @if($trashedProductsCount > 0)
                            <span class="badge bg-danger ms-1">{{ $trashedProductsCount }}</span>
                        @endif
                    </a>
                </div>
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
                <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="search" class="visually-hidden">Search by Name/Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Search by product name or code..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category_id_filter" class="visually-hidden">Filter by Category</label>
                        <select class="form-select" id="category_id_filter" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bx bx-search"></i> Search / Filter
                        </button>
                        @if(request('search') || request('category_id'))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                <i class="bx bx-x"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 table-hover table-sm table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Code</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">MRP</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Selling</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stock</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">WT(GM)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                            <th class="text-secondary opacity-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 ps-3">
                                        {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}
                                    </p>
                                </td>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="avatar avatar-sm me-3" alt="{{ $product->name }}">
                                    @else
                                        <i class="bx bx-image text-muted bx-md me-3"></i>
                                    @endif
                                </td>
                                <td>
                                    <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $product->category->name ?? 'N/A' }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $product->code }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">₹{{ number_format($product->mrp, 2) }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">₹{{ number_format($product->selling, 2) }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $product->stock }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $product->gram_weight }}</p>
                                </td>
                                <td>
                                    <span class="badge badge-sm {{ $product->status == 'enable' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($product->status ?? 'N/A') }}
                                    </span>
                                </td>
                                @if($product->trashed())
                                <td class="align-middle">
                                    <form action="{{ route('admin.products.restore', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to move this product to recover?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-original-title="Move to recover" title="Move to recover">
                                            <i class="bx bx-sync"></i>
                                        </button>
                                    </form>
                                </td>
                                @else
                                <td class="align-middle">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-original-title="View product" title="View Details">
                                        <i class="bx bx-show-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-original-title="Edit product" title="Edit Product">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    {{-- Soft Delete button --}}
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to move this product to trash?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-original-title="Move to Trash" title="Move to Trash">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No products found
                                    @if(request('search') || request('category_id'))
                                    matching your filters. <a href="{{ route('admin.products.index') }}">Clear filters to see all products.</a>
                                    @else
                                    . <a href="{{ route('admin.products.create') }}">Click here to add one.</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center pt-4">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the category filter dropdown
            $('#category_id_filter').select2({
                placeholder: "All Categories",
                allowClear: true // Option to clear the selected value
            });
        });
    </script>

@endsection