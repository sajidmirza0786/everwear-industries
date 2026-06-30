@extends('admin.master')

@section('seo')
    <title>{{ isset($coupon) ? 'Edit Coupon' : 'New Coupon' }} | Admin Panel</title>
    <meta name="description" content="{{ isset($coupon) ? 'Edit an existing discount coupon.' : 'Create a new discount coupon.' }}">
@endsection

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Page Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-semibold">{{ isset($coupon) ? 'Edit Coupon' : 'New Coupon' }}</h4>
            <small class="text-muted">
                {{ isset($coupon) ? 'Update the coupon settings below.' : 'Fill in the details to create a new discount code.' }}
            </small>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- ── Validation errors ── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show d-flex gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
          method="POST" id="couponForm" novalidate>
        @csrf
        @isset($coupon) @method('PUT') @endisset

        <div class="row g-4">

            {{-- ════════════════════════════════════════
                 LEFT COLUMN — main settings
            ════════════════════════════════════════ --}}
            <div class="col-xl-8">

                {{-- ── 1. Identity ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-primary bg-opacity-10 text-primary p-1 lh-1">
                            <i class="bi bi-ticket-perforated"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Coupon Identity</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="code" class="form-label fw-medium">
                                    Coupon Code <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-hash text-muted"></i></span>
                                    <input type="text"
                                           name="code"
                                           id="code"
                                           class="form-control font-monospace text-uppercase @error('code') is-invalid @enderror"
                                           value="{{ old('code', $coupon->code ?? '') }}"
                                           maxlength="50"
                                           placeholder="e.g. SAVE20"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Automatically saved in UPPERCASE.</div>
                            </div>

                            <div class="col-md-6 d-flex flex-column justify-content-end">
                                <label class="form-label fw-medium d-block">Status</label>
                                <div class="d-flex align-items-center gap-3 mt-1">
                                    <input type="hidden" name="is_active" value="0">
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="is_active"
                                               id="is_active"
                                               value="1"
                                               {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                                               role="switch">
                                        <label class="form-check-label" for="is_active" id="statusLabel">
                                            {{ old('is_active', $coupon->is_active ?? true) ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">Description</label>
                                <input type="text"
                                       name="description"
                                       id="description"
                                       class="form-control @error('description') is-invalid @enderror"
                                       value="{{ old('description', $coupon->description ?? '') }}"
                                       maxlength="255"
                                       placeholder="Internal note, e.g. Diwali sale – 20% off sitewide">
                                <div class="form-text">Shown only in the admin panel.</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── 2. Discount Settings ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-success bg-opacity-10 text-success p-1 lh-1">
                            <i class="bi bi-percent"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Discount Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="discount_type" class="form-label fw-medium">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select name="discount_type" id="discount_type"
                                        class="form-select @error('discount_type') is-invalid @enderror" required>
                                    @php $discountType = old('discount_type', $coupon->discount_type ?? 'flat'); @endphp
                                    <option value="flat"    {{ $discountType === 'flat'    ? 'selected' : '' }}>Flat Amount (₹)</option>
                                    <option value="percent" {{ $discountType === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount_value" class="form-label fw-medium">
                                    Value <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white" id="discountPrefix">₹</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0.01"
                                           name="discount_value"
                                           id="discount_value"
                                           class="form-control @error('discount_value') is-invalid @enderror"
                                           value="{{ old('discount_value', $coupon->discount_value ?? '') }}"
                                           required>
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4" id="max_discount_wrapper">
                                <label for="max_discount_amount" class="form-label fw-medium">
                                    Max Discount Cap (₹)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₹</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="max_discount_amount"
                                           id="max_discount_amount"
                                           class="form-control @error('max_discount_amount') is-invalid @enderror"
                                           value="{{ old('max_discount_amount', $coupon->max_discount_amount ?? '') }}"
                                           placeholder="No cap">
                                    @error('max_discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Maximum ₹ discount allowed for % coupons.</div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── 3. Order Constraints ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-warning bg-opacity-10 text-warning p-1 lh-1">
                            <i class="bi bi-cart-check"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Order Constraints</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="min_order_amount" class="form-label fw-medium">Minimum Order Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₹</span>
                                    <input type="number" step="0.01" min="0"
                                           name="min_order_amount" id="min_order_amount"
                                           class="form-control @error('min_order_amount') is-invalid @enderror"
                                           value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
                                           placeholder="No minimum">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="max_order_amount" class="form-label fw-medium">Maximum Order Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₹</span>
                                    <input type="number" step="0.01" min="0"
                                           name="max_order_amount" id="max_order_amount"
                                           class="form-control @error('max_order_amount') is-invalid @enderror"
                                           value="{{ old('max_order_amount', $coupon->max_order_amount ?? '') }}"
                                           placeholder="No maximum">
                                    @error('max_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="max_uses" class="form-label fw-medium">Max Total Uses</label>
                                <input type="number" min="1"
                                       name="max_uses" id="max_uses"
                                       class="form-control @error('max_uses') is-invalid @enderror"
                                       value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                                       placeholder="Unlimited">
                                <div class="form-text">Leave blank for unlimited redemptions.</div>
                                @error('max_uses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="max_uses_per_user" class="form-label fw-medium">Max Uses Per User</label>
                                <input type="number" min="1"
                                       name="max_uses_per_user" id="max_uses_per_user"
                                       class="form-control @error('max_uses_per_user') is-invalid @enderror"
                                       value="{{ old('max_uses_per_user', $coupon->max_uses_per_user ?? '') }}"
                                       placeholder="Unlimited">
                                <div class="form-text">Leave blank for unlimited per-user usage.</div>
                                @error('max_uses_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── 4. Item Targeting ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-info bg-opacity-10 text-info p-1 lh-1">
                            <i class="bi bi-box-seam"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Item Targeting</h6>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-auto small fw-normal  text-white">Optional</span>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-light border d-flex gap-2 py-2 mb-3" role="alert">
                            <i class="bi bi-info-circle text-muted mt-1 flex-shrink-0"></i>
                            <div class="small text-muted">
                                Leave both fields empty to apply this coupon to <strong>all items</strong>.
                                If you specify products or categories, the discount applies only to matching items.
                                Items qualify if they match <em>either</em> list (OR logic).
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="applicable_products_input" class="form-label fw-medium">
                                    Applicable Products
                                    <span class="text-muted fw-normal">(IDs)</span>
                                </label>
                                <input type="text"
                                       id="applicable_products_input"
                                       class="form-control @error('applicable_products') is-invalid @enderror"
                                       placeholder="e.g. 1, 5, 12"
                                       value="{{ old('applicable_products_raw', isset($coupon) && $coupon->applicable_products
                                            ? implode(', ', $coupon->applicable_products) : '') }}">
                                {{-- Hidden JSON field populated by JS --}}
                                <input type="hidden" name="applicable_products" id="applicable_products_json"
                                       value="{{ old('applicable_products', isset($coupon) && $coupon->applicable_products
                                            ? json_encode($coupon->applicable_products) : '') }}">
                                <div class="form-text">Comma-separated product IDs.</div>
                                @error('applicable_products')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="applicable_categories_input" class="form-label fw-medium">
                                    Applicable Categories
                                    <span class="text-muted fw-normal">(IDs)</span>
                                </label>
                                <input type="text"
                                       id="applicable_categories_input"
                                       class="form-control @error('applicable_categories') is-invalid @enderror"
                                       placeholder="e.g. 3, 7"
                                       value="{{ old('applicable_categories_raw', isset($coupon) && $coupon->applicable_categories
                                            ? implode(', ', $coupon->applicable_categories) : '') }}">
                                <input type="hidden" name="applicable_categories" id="applicable_categories_json"
                                       value="{{ old('applicable_categories', isset($coupon) && $coupon->applicable_categories
                                            ? json_encode($coupon->applicable_categories) : '') }}">
                                <div class="form-text">Comma-separated category IDs.</div>
                                @error('applicable_categories')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- /col-xl-8 --}}

            {{-- ════════════════════════════════════════
                 RIGHT COLUMN — validity & summary
            ════════════════════════════════════════ --}}
            <div class="col-xl-4">

                {{-- ── Validity ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-danger bg-opacity-10 text-danger p-1 lh-1">
                            <i class="bi bi-calendar-range"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Validity Window</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="starts_at" class="form-label fw-medium">Starts At</label>
                            <input type="datetime-local"
                                   name="starts_at"
                                   id="starts_at"
                                   class="form-control @error('starts_at') is-invalid @enderror"
                                   value="{{ old('starts_at', isset($coupon->starts_at) ? \Carbon\Carbon::parse($coupon->starts_at)->format('Y-m-d\TH:i') : '') }}">
                            <div class="form-text">Leave blank to activate immediately.</div>
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="expires_at" class="form-label fw-medium">Expires At</label>
                            <input type="datetime-local"
                                   name="expires_at"
                                   id="expires_at"
                                   class="form-control @error('expires_at') is-invalid @enderror"
                                   value="{{ old('expires_at', isset($coupon->expires_at) ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d\TH:i') : '') }}">
                            <div class="form-text">Leave blank for no expiry.</div>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Live Preview ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-secondary bg-opacity-10 text-secondary p-1 lh-1">
                            <i class="bi bi-eye"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Live Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="border rounded-3 p-3 bg-light text-center">
                            <div class="font-monospace fw-bold fs-4 text-dark mb-1" id="preview_code">
                                {{ strtoupper(old('code', $coupon->code ?? 'CODE')) }}
                            </div>
                            <div class="text-muted small mb-2" id="preview_desc">
                                {{ old('description', $coupon->description ?? 'Your coupon description') }}
                            </div>
                            <span class="badge bg-success fs-6 px-3 py-2" id="preview_discount">
                                @php
                                    $pv = old('discount_value', $coupon->discount_value ?? '—');
                                    $pt = old('discount_type',  $coupon->discount_type  ?? 'flat');
                                @endphp
                                {{ $pt === 'flat' ? '₹' . $pv . ' off' : $pv . '% off' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── Usage stats (edit only) ── --}}
                @isset($coupon)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                        <span class="rounded-2 bg-warning bg-opacity-10 text-warning p-1 lh-1">
                            <i class="bi bi-bar-chart-line"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Usage Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Total Redemptions</span>
                            <span class="fw-semibold">{{ number_format($coupon->used_count) }}</span>
                        </div>
                        @if($coupon->max_uses)
                            @php $usePct = min(100, round($coupon->used_count / $coupon->max_uses * 100)); @endphp
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Limit</span>
                                <span class="fw-semibold small">{{ $usePct }}% used</span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar {{ $usePct >= 90 ? 'bg-danger' : ($usePct >= 60 ? 'bg-warning' : 'bg-success') }}"
                                     style="width:{{ $usePct }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted" style="font-size:11px">0</span>
                                <span class="text-muted" style="font-size:11px">{{ number_format($coupon->max_uses) }}</span>
                            </div>
                        @else
                            <div class="text-muted small">No usage limit set.</div>
                        @endif
                        <hr class="my-2">
                        <div class="text-muted small">
                            Created {{ $coupon->created_at->diffForHumans() }}
                            &bull; Updated {{ $coupon->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @endisset

                {{-- ── Form actions ── --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi {{ isset($coupon) ? 'bi-check-lg' : 'bi-plus-lg' }} me-2"></i>
                        {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </div>{{-- /col-xl-4 --}}
        </div>{{-- /row --}}
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Refs ──────────────────────────────────────────────────────────────
    const typeSelect        = document.getElementById('discount_type');
    const discountValue     = document.getElementById('discount_value');
    const maxDiscWrapper    = document.getElementById('max_discount_wrapper');
    const discountPrefix    = document.getElementById('discountPrefix');
    const codeInput         = document.getElementById('code');
    const descInput         = document.getElementById('description');
    const isActiveToggle    = document.getElementById('is_active');
    const statusLabel       = document.getElementById('statusLabel');

    const previewCode       = document.getElementById('preview_code');
    const previewDesc       = document.getElementById('preview_desc');
    const previewDiscount   = document.getElementById('preview_discount');

    const productsInput     = document.getElementById('applicable_products_input');
    const productsJson      = document.getElementById('applicable_products_json');
    const categoriesInput   = document.getElementById('applicable_categories_input');
    const categoriesJson    = document.getElementById('applicable_categories_json');

    // ── Discount type toggle ───────────────────────────────────────────────
    function syncDiscountType() {
        const isPercent = typeSelect.value === 'percent';
        maxDiscWrapper.style.display = isPercent ? '' : 'none';
        discountPrefix.textContent   = isPercent ? '%' : '₹';
        updatePreview();
    }
    typeSelect.addEventListener('change', syncDiscountType);
    syncDiscountType();

    // ── Uppercase code ────────────────────────────────────────────────────
    codeInput.addEventListener('input', function () {
        const s = this.selectionStart, e = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(s, e);
        updatePreview();
    });

    // ── Live preview ──────────────────────────────────────────────────────
    function updatePreview() {
        const code = codeInput.value.trim() || 'CODE';
        const desc = descInput.value.trim() || 'Your coupon description';
        const val  = discountValue.value   || '—';
        const type = typeSelect.value;
        previewCode.textContent     = code;
        previewDesc.textContent     = desc;
        previewDiscount.textContent = type === 'flat' ? '₹' + val + ' off' : val + '% off';
    }
    descInput.addEventListener('input', updatePreview);
    discountValue.addEventListener('input', updatePreview);

    // ── Status label ──────────────────────────────────────────────────────
    isActiveToggle.addEventListener('change', function () {
        statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
    });

    // ── JSON serialisation for targeting fields ───────────────────────────
    function parseIds(raw) {
        return raw.split(',')
                  .map(s => s.trim())
                  .filter(s => /^\d+$/.test(s))
                  .map(Number);
    }

    function syncJson(inputEl, jsonEl) {
        const ids = parseIds(inputEl.value);
        jsonEl.value = ids.length ? JSON.stringify(ids) : '';
    }

    productsInput.addEventListener('input',    () => syncJson(productsInput,   productsJson));
    categoriesInput.addEventListener('input',  () => syncJson(categoriesInput, categoriesJson));

    // Seed JSON fields on page load (handles old() repopulation)
    syncJson(productsInput,   productsJson);
    syncJson(categoriesInput, categoriesJson);

    // ── Pre-submit guard ──────────────────────────────────────────────────
    document.getElementById('couponForm').addEventListener('submit', function () {
        syncJson(productsInput,   productsJson);
        syncJson(categoriesInput, categoriesJson);
    });
});
</script>
@endsection