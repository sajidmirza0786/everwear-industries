@extends('admin.master')

@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">Shipping Charges</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="font-weight-bolder mb-0">Shipping Charges List</h6>
                <div>
                    <a href="{{ route('admin.shippingcharges.create') }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-plus-circle me-2"></i>Add New Shipping Charges
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
                <form action="{{ route('admin.shippingcharges.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="state_id_filter" class="visually-hidden">Filter by State</label>
                        <select class="form-select" id="state_id_filter" name="state_id">
                            <option value="">All State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }} ({{ $state->country->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bx bx-search"></i> Search / Filter
                        </button>
                        @if(request('search') || request('state_id'))
                            <a href="{{ route('admin.shippingcharges.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                <i class="bx bx-x"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive p-0"> {{-- Added p-0 for table alignment --}}
                <table class="table align-items-center mb-0 table-hover table-sm table-bordered"> {{-- Added align-items-center mb-0 and table-sm --}}
                    <thead class="bg-light"> {{-- Consistent with products table --}}
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th> {{-- Added classes for consistency --}}
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Country</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">State</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Min Weight (kg)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Max Weight (kg)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Min Order Amt</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Max Order Amt</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Charge (₹)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Created At</th>
                            <th class="text-secondary opacity-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingCharges as $charge)
                            <tr>
                                <td><p class="text-xs font-weight-bold mb-0 ps-3">{{ $charge->id }}</p></td> {{-- Added classes for consistency --}}
                                <td><p class="text-xs font-weight-bold mb-0">{{ $charge->country->name ?? 'All Countries' }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $charge->state->name ?? 'All States' }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ number_format($charge->min_weight, 2) }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $charge->max_weight ? number_format($charge->max_weight, 2) : '∞' }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">₹{{ number_format($charge->min_order_amount, 2) }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $charge->max_order_amount ? '₹' . number_format($charge->max_order_amount, 2) : '∞' }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">₹{{ number_format($charge->charge, 2) }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $charge->created_at->format('d M Y, h:i A') }}</p></td>
                                <td class="align-middle"> {{-- Added align-middle --}}
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.shippingcharges.edit', $charge->id) }}" class="btn btn-warning btn-sm" title="Edit Charge">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.shippingcharges.destroy', $charge) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to move this shippingcharges to delete?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-original-title="Move to Trash" title="Move to Trash">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No products found
                                    @if(request('search') || request('state_id'))
                                    matching your filters. <a href="{{ route('admin.shippingcharges.index') }}">Clear filters to see all shippingcharges.</a>
                                    @else
                                    . <a href="{{ route('admin.shippingcharges.create') }}">Click here to add one.</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center pt-4">
                {{ $shippingCharges->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the category filter dropdown
            $('#state_id_filter').select2({
                placeholder: "All State",
                allowClear: true // Option to clear the selected value
            });
        });
    </script>

@endsection