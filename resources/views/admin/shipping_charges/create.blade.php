@extends('admin.master')

@php
    // Determine if we are creating or editing
    $isEdit = isset($shippingcharge) && $shippingcharge->id;
    $formAction = $isEdit ? route('admin.shippingcharges.update', $shippingcharge->id) : route('admin.shippingcharges.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $pageTitle = $isEdit ? 'Edit Shipping Charge #' . $shippingcharge->id : 'Add New Shipping Charge';
    $breadcrumbTitle = $isEdit ? 'Edit Charge #' . $shippingcharge->id : 'Add New Charge';
@endphp

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.shippingcharges.index') }}">Shipping Charges</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $breadcrumbTitle }}</li>
@endsection
@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <h6 class="font-weight-bolder mb-0">
                {{ $pageTitle }}
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
                <div class="card-body">
                    
                    {{-- Added id="shippingChargeForm" to the form --}}
                    <form id="shippingChargeForm" action="{{ $formAction }}" method="POST">
                        @csrf
                        @method($formMethod)

                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="text-primary"><i class="bx bx-globe me-1"></i> Geographic Scope</h6>
                                <hr class="my-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="state_id" class="form-label">State (Optional)</label>
                                <select class="form-select @error('state_id') is-invalid @enderror" id="state_id" name="state_id">
                                    <option value="">Select State (All)</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id', $shippingcharge->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }} ({{ $state->country->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select a state to apply the charge to. If none selected, it applies to all states (globally).</small>
                            </div>

                            <div class="col-12 mt-4 mb-3">
                                <h6 class="text-primary"><i class="bx bx-box me-1"></i> Weight Range (in kg)</h6>
                                <hr class="my-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="min_weight" class="form-label">Min Weight <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('min_weight') is-invalid @enderror" id="min_weight" name="min_weight" value="{{ old('min_weight', $shippingcharge->min_weight ?? 0) }}" required min="0">
                                @error('min_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum weight of the order in kilograms.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_weight" class="form-label">Max Weight (Optional)</label>
                                <input type="number" step="0.01" class="form-control @error('max_weight') is-invalid @enderror" id="max_weight" name="max_weight" value="{{ old('max_weight', $shippingcharge->max_weight ?? '') }}" placeholder="Leave blank for no upper limit" min="0">
                                @error('max_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maximum weight of the order in kilograms. Must be greater than min weight.</small>
                            </div>

                            <div class="col-12 mt-4 mb-3">
                                <h6 class="text-primary"><i class="bx bx-purchase-tag-alt me-1"></i> Order Amount Range (in ₹)</h6>
                                <hr class="my-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="min_order_amount" class="form-label">Min Order Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $shippingcharge->min_order_amount ?? 0) }}" required min="0">
                                @error('min_order_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum order value required to apply this charge.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_order_amount" class="form-label">Max Order Amount (Optional)</label>
                                <input type="number" step="0.01" class="form-control @error('max_order_amount') is-invalid @enderror" id="max_order_amount" name="max_order_amount" value="{{ old('max_order_amount', $shippingcharge->max_order_amount ?? '') }}" placeholder="Leave blank for no upper limit" min="0">
                                @error('max_order_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maximum order value to apply this charge. Must be greater than min amount.</small>
                            </div>

                            <div class="col-12 mt-4 mb-3">
                                <h6 class="text-primary"><i class="bx bx-dollar me-1"></i> Charge Details</h6>
                                <hr class="my-2">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="charge" class="form-label">Shipping Charge (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('charge') is-invalid @enderror" id="charge" name="charge" value="{{ old('charge', $shippingcharge->charge ?? '') }}" required min="0">
                                @error('charge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">The actual shipping cost for this rule.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i> 
                                <span id="submitButtonText">{{ $isEdit ? 'Update Charge' : 'Create Charge' }}</span>
                            </button>
                            <a href="{{ route('admin.shippingcharges.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Cancel
                            </a>
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
        // Initialize Select2 on the category dropdown
        // Ensure the ID '#category_id' matches the select element's ID in your HTML
        $('#state_id').select2({
            placeholder: "Select Category", // Updated placeholder
            allowClear: true // Option to clear the selected value
        });
    });
</script>
@endsection