@extends('admin.master')

@section('seo')
    <title>Coupons | Admin Panel</title>
    <meta name="description" content="Manage discount coupons for your store.">
@endsection

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Page Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-semibold">Coupons</h4>
            <small class="text-muted">Manage discount codes and their rules</small>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bx bx-plus-lg"></i> New Coupon
        </a>
    </div>

    {{-- ── Flash messages ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2" role="alert">
            <i class="bx bx-check-circle-fill text-success"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Stats row ── --}}
    <div class="row g-3 mb-4">
        @php
            $total   = $coupons->total();
            $active  = $activeCouponsCount  ?? 0;
            $expired = $expiredCouponsCount ?? 0;
        @endphp
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bx bx-ticket-perforated fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $total }}</div>
                        <div class="text-muted small mt-1">Total Coupons</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $active }}</div>
                        <div class="text-muted small mt-1">Active</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger">
                        <i class="bx bx-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $expired }}</div>
                        <div class="text-muted small mt-1">Expired</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bx bx-bar-chart-line fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $coupons->sum('used_count') }}</div>
                        <div class="text-muted small mt-1">Total Redemptions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Search / Filter bar ── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Search Code / Description</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="e.g. SAVE20" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired"  {{ request('status') === 'expired'  ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="flat"    {{ request('type') === 'flat'    ? 'selected' : '' }}>Flat (₹)</option>
                        <option value="percent" {{ request('type') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['search','status','type']))
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($coupons->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-ticket-perforated fs-1 text-muted opacity-50"></i>
                    <p class="mt-3 text-muted">No coupons found. <a href="{{ route('admin.coupons.create') }}">Create your first one.</a></p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Code</th>
                                <th>Discount</th>
                                <th>Order Range (₹)</th>
                                <th>Targeting</th>
                                <th>Validity</th>
                                <th>Usage</th>
                                <th class="text-center">Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                            @php
                                $now     = now();
                                $started = !$coupon->starts_at  || $coupon->starts_at->lte($now);
                                $notExp  = !$coupon->expires_at || $coupon->expires_at->gte($now);
                                $live    = $coupon->is_active && $started && $notExp;

                                $hasProducts   = !empty($coupon->applicable_products);
                                $hasCategories = !empty($coupon->applicable_categories);
                            @endphp
                            <tr>
                                {{-- Code + description --}}
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark rounded-2 font-monospace fs-6 fw-semibold px-2 py-1">
                                            {{ $coupon->code }}
                                        </span>
                                        <button class="btn btn-link btn-sm p-0 text-muted copy-btn" title="Copy code"
                                                data-code="{{ $coupon->code }}">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                    @if($coupon->description)
                                        <div class="text-muted small mt-1 text-truncate" style="max-width:180px"
                                             title="{{ $coupon->description }}">
                                            {{ $coupon->description }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Discount --}}
                                <td>
                                    @if($coupon->discount_type === 'flat')
                                        <span class="fw-semibold text-success">₹{{ number_format($coupon->discount_value, 2) }}</span>
                                        <div class="text-muted small">Flat</div>
                                    @else
                                        <span class="fw-semibold text-info">{{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}%</span>
                                        @if($coupon->max_discount_amount)
                                            <div class="text-muted small">Max ₹{{ number_format($coupon->max_discount_amount, 2) }}</div>
                                        @else
                                            <div class="text-muted small">No cap</div>
                                        @endif
                                    @endif
                                </td>

                                {{-- Order range --}}
                                <td>
                                    @if($coupon->min_order_amount || $coupon->max_order_amount)
                                        <div class="small">
                                            @if($coupon->min_order_amount)
                                                <span class="text-muted">Min:</span> ₹{{ number_format($coupon->min_order_amount, 0) }}
                                            @endif
                                            @if($coupon->max_order_amount)
                                                <br><span class="text-muted">Max:</span> ₹{{ number_format($coupon->max_order_amount, 0) }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Targeting --}}
                                <td>
                                    @if($hasProducts || $hasCategories)
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($hasProducts)
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">
                                                    <i class="bx bx-box-seam me-1"></i>{{ count($coupon->applicable_products) }} product{{ count($coupon->applicable_products) !== 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                            @if($hasCategories)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill small">
                                                    <i class="bx bx-tag me-1"></i>{{ count($coupon->applicable_categories) }} cat.
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill small">All items</span>
                                    @endif
                                </td>

                                {{-- Validity --}}
                                <td>
                                    <div class="small">
                                        @if($coupon->starts_at)
                                            <div><i class="bx bx-calendar-check me-1 text-muted"></i>{{ $coupon->starts_at->format('d M Y') }}</div>
                                        @else
                                            <div class="text-muted">Any time</div>
                                        @endif
                                        @if($coupon->expires_at)
                                            <div class="{{ $coupon->expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                                <i class="bx bx-calendar-x me-1"></i>{{ $coupon->expires_at->format('d M Y') }}
                                                @if($coupon->expires_at->isPast())
                                                    <span class="text-danger">(expired)</span>
                                                @elseif($coupon->expires_at->diffInDays(now()) <= 7)
                                                    <span class="text-warning">({{ $coupon->expires_at->diffForHumans() }})</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-muted">No expiry</div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Usage --}}
                                <td>
                                    <div class="small fw-medium">
                                        {{ number_format($coupon->used_count) }}
                                        @if($coupon->max_uses)
                                            / {{ number_format($coupon->max_uses) }}
                                        @else
                                            <span class="text-muted">/ ∞</span>
                                        @endif
                                    </div>
                                    @if($coupon->max_uses)
                                        @php $pct = min(100, round($coupon->used_count / $coupon->max_uses * 100)); @endphp
                                        <div class="progress mt-1" style="height:4px;width:80px">
                                            <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 60 ? 'bg-warning' : 'bg-success') }}"
                                                 style="width:{{ $pct }}%"></div>
                                        </div>
                                    @endif
                                    @if($coupon->max_uses_per_user)
                                        <div class="text-muted" style="font-size:11px">{{ $coupon->max_uses_per_user }}x/user</div>
                                    @endif
                                </td>

                                {{-- Status badge --}}
                                <td class="text-center">
                                    @if(!$coupon->is_active)
                                        <span class="badge bg-secondary rounded-pill px-3">Inactive</span>
                                    @elseif(!$started)
                                        <span class="badge bg-info text-dark rounded-pill px-3">Scheduled</span>
                                    @elseif(!$notExp)
                                        <span class="badge bg-danger rounded-pill px-3">Expired</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3">Active</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.toggle', $coupon) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $coupon->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bx {{ $coupon->is_active ? 'bx-pause-circle' : 'bx-play-circle' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                              class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($coupons->hasPages())
                    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                        <div class="text-muted small">
                            Showing {{ $coupons->firstItem() }}–{{ $coupons->lastItem() }} of {{ $coupons->total() }} coupons
                        </div>
                        {{ $coupons->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Copy code to clipboard ──
    document.querySelectorAll('.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            navigator.clipboard.writeText(this.dataset.code).then(() => {
                const icon = this.querySelector('i');
                icon.classList.replace('bi-copy', 'bi-check2');
                setTimeout(() => icon.classList.replace('bi-check2', 'bi-copy'), 1500);
            });
        });
    });

    // ── Delete confirmation ──
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Delete this coupon? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection