@extends('admin.master')

@section('seo')
    <title>Edit Order #{{ $order->uuid }} | Admin Panel</title>
    <meta name="description" content="Edit order {{ $order->uuid }}.">
@endsection

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════════════════════ */
:root {
    --ink:        #211c16;
    --ink-soft:   #6f6457;
    --ink-faint:  #a39a8c;
    --paper:      #faf8f4;
    --surface:    #ffffff;
    --line:       #e8e1d4;
    --line-soft:  #f0ebe0;
    --brass:      #a9803f;
    --brass-deep: #7e5d2c;
    --brass-tint: #f6efe1;
    --green:      #3c6e51;
    --green-tint: #eaf3ee;
    --red:        #a8443a;
    --red-tint:   #fbece9;
    --blue:       #4a6b88;
    --blue-tint:  #ecf1f5;
    --radius:     10px;
    --serif: 'Fraunces', Georgia, serif;
    --sans:  'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

/* ── Wrap ──────────────────────────────────────────── */
.oe-wrap {
    background: var(--paper);
    font-family: var(--sans);
    color: var(--ink);
    margin: -1.5rem -0.75rem -1.5rem;
    padding: 2.25rem 1.5rem 4rem;
}

/* ── Page header ───────────────────────────────────── */
.oe-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding-bottom: 1.5rem;
    margin-bottom: 1.75rem;
    border-bottom: 1px solid var(--line);
}
.oe-header .eyebrow {
    font-size: .72rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--brass-deep);
    font-weight: 600;
    margin-bottom: .35rem;
    display: block;
}
.oe-header h1 {
    font-family: var(--serif);
    font-weight: 600;
    font-size: 1.9rem;
    letter-spacing: -.01em;
    margin: 0;
    color: var(--ink);
}
.oe-header h1 .order-id { color: var(--brass); font-weight: 500; }

.btn-ghost-back {
    font-family: var(--sans);
    font-size: .85rem;
    font-weight: 600;
    color: var(--ink-soft);
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: .5rem 1.1rem;
    transition: all .15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
}
.btn-ghost-back:hover { border-color: var(--brass); color: var(--brass-deep); background: var(--brass-tint); }

/* ── Flash alerts ──────────────────────────────────── */
.oe-flash {
    border-radius: var(--radius);
    padding: .85rem 1.1rem;
    font-size: .88rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
}
.oe-flash.error   { background: var(--red-tint);   border: 1px solid #e6c4bd; color: var(--red); }
.oe-flash.success { background: var(--green-tint);  border: 1px solid #c7e0d2; color: var(--green); }
.oe-flash .btn-close { font-size: .7rem; }

/* ── Section card ──────────────────────────────────── */
.oe-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.oe-card-head {
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid var(--line-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
}
.oe-card-head .label {
    font-family: var(--serif);
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: .55rem;
}
.oe-card-head .label i { color: var(--brass); font-size: 1.15rem; }
.oe-card-body { padding: 1.4rem; }

/* ── Form controls ─────────────────────────────────── */
.oe-card-body label,
.field-label {
    font-size: .72rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--ink-soft);
    margin-bottom: .4rem;
    display: block;
}
.oe-card-body .form-control,
.oe-card-body .form-select {
    font-family: var(--sans);
    font-size: .92rem;
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: .55rem .8rem;
    background: var(--paper);
    color: var(--ink);
    transition: border-color .15s ease, box-shadow .15s ease;
}
.oe-card-body .form-control:disabled { color: var(--ink-faint); background: var(--line-soft); }
.oe-card-body .form-control:focus,
.oe-card-body .form-select:focus {
    border-color: var(--brass);
    box-shadow: 0 0 0 3px rgba(169,128,63,.14);
    background: #fff;
}
.oe-card-body .invalid-feedback { font-size: .78rem; }
.oe-card-body .is-invalid { border-color: var(--red) !important; }
.oe-card-body .text-danger { color: var(--red) !important; }

/* ── Add item button (card head) ───────────────────── */
.btn-add-item {
    font-size: .8rem;
    font-weight: 600;
    border: 1px solid var(--brass);
    color: var(--brass-deep);
    background: var(--brass-tint);
    border-radius: 999px;
    padding: .4rem .95rem;
    transition: all .15s ease;
}
.btn-add-item:hover { background: var(--brass); color: #fff; }

/* ══════════════════════════════════════════════════════
   ITEMS TABLE
══════════════════════════════════════════════════════ */
.items-table { width: 100%; border-collapse: collapse; }

.items-table thead th {
    font-size: .68rem;
    letter-spacing: .1em;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--ink-soft);
    padding: .75rem 1rem;
    border-bottom: 2px solid var(--brass);
    background: var(--brass-tint);
    text-align: left;
    white-space: nowrap;
}

/* Main product row */
.items-table .item-main-row td {
    padding: .9rem 1rem .4rem;
    font-size: .88rem;
    border-top: 1px solid var(--line-soft);
    vertical-align: middle;
    color: var(--ink);
}
.items-table .item-main-row:first-child td { border-top: none; }

/* Discount sub-row — visually tucked under its parent */
.items-table .item-discount-row td {
    padding: .25rem 1rem .9rem;
    vertical-align: top;
    border-bottom: 1px solid var(--line-soft);
}
.items-table .item-discount-row:last-child td { border-bottom: none; }

/* Marked-for-delete */
.items-table .item-main-row.deleted-row td,
.items-table .item-main-row.deleted-row + .item-discount-row td {
    opacity: .38;
}
.items-table .item-main-row.deleted-row .item-line-total { text-decoration: line-through; }

/* Misc cell helpers */
.cell-num { color: var(--ink-faint); font-size: .8rem; width: 28px; }
.cell-actions { width: 44px; text-align: center; }
.qty-input  { width: 64px;  text-align: center; padding: .4rem !important; }
.price-input { width: 100px; padding: .4rem .6rem !important; }
.item-line-total { font-family: var(--serif); font-weight: 600; font-size: .95rem; white-space: nowrap; }

/* Discount sub-row inner grid */
.discount-subrow {
    display: grid;
    grid-template-columns: 1fr 1fr 2fr auto;
    gap: .5rem;
    align-items: end;
}
.discount-subrow .ds-label {
    font-size: .68rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--ink-faint);
    margin-bottom: .3rem;
    display: block;
}
.discount-subrow .form-control,
.discount-subrow .form-select {
    font-family: var(--sans);
    font-size: .82rem;
    border: 1px solid var(--line);
    border-radius: 7px;
    padding: .4rem .65rem;
    background: var(--paper);
    color: var(--ink);
    transition: border-color .15s ease;
}
.discount-subrow .form-control:focus,
.discount-subrow .form-select:focus {
    border-color: var(--brass);
    box-shadow: 0 0 0 2px rgba(169,128,63,.12);
    outline: none;
}
.coupon-display {
    font-size: .78rem;
    font-weight: 600;
    color: var(--green);
    background: var(--green-tint);
    border: 1px solid #c7e0d2;
    border-radius: 6px;
    padding: .3rem .65rem;
    display: inline-block;
    margin-top: .25rem;
}

/* Badge helpers */
.badge-size {
    font-size: .62rem; letter-spacing: .04em; font-weight: 700;
    color: var(--brass-deep); background: var(--brass-tint);
    border-radius: 4px; padding: .1rem .4rem; margin-left: .35rem;
    text-transform: uppercase;
}
.badge-new {
    font-size: .62rem; letter-spacing: .04em; font-weight: 700;
    color: var(--green); background: var(--green-tint);
    border-radius: 4px; padding: .1rem .4rem; margin-left: .25rem;
    text-transform: uppercase;
}

/* Delete / undo button */
.btn-row-delete {
    border: 1px solid var(--line);
    background: var(--surface);
    color: var(--ink-faint);
    border-radius: 999px;
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all .15s ease;
    cursor: pointer;
}
.btn-row-delete:hover { border-color: var(--red); color: var(--red); background: var(--red-tint); }
.btn-row-delete.is-marked { border-color: var(--ink-faint); color: var(--ink-soft); background: var(--line-soft); }

.empty-row { text-align: center; color: var(--ink-faint); padding: 2.5rem 0; font-size: .88rem; }

/* ══════════════════════════════════════════════════════
   ORDER SUMMARY (right column)
══════════════════════════════════════════════════════ */
.oe-summary {
    background: var(--ink);
    color: #f4efe5;
    border-radius: var(--radius);
    padding: 1.5rem 1.5rem 1.3rem;
    margin-bottom: 1.5rem;
}
.oe-summary .summary-title {
    font-family: var(--serif);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.1rem;
    display: flex; align-items: center; gap: .5rem;
    color: #fff;
}
.oe-summary .summary-title i { color: var(--brass); }

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    font-size: .88rem;
    padding: .45rem 0;
    color: #d8d0c2;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.summary-row:last-of-type { border-bottom: none; }
.summary-row.s-taxable { color: #e8d9c0; font-weight: 600; }
.summary-row.s-gst     { color: #c9bfb0; }
.summary-row.s-total   {
    border-top: 2px solid rgba(255,255,255,.22) !important;
    margin-top: .6rem;
    padding-top: 1rem;
    font-family: var(--serif);
    font-size: 1.35rem;
    font-weight: 600;
    color: #fff;
    align-items: center;
    border-bottom: none !important;
}
.summary-row.s-total .s-amount { color: #e8c887; }
.summary-note {
    font-size: .75rem;
    color: rgba(255,255,255,.35);
    margin-top: .85rem;
    padding-top: .75rem;
    border-top: 1px dashed rgba(255,255,255,.12);
    text-align: center;
}

/* ── Action buttons ────────────────────────────────── */
.btn-save-order {
    background: var(--brass);
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    border-radius: 999px;
    padding: .8rem 1.5rem;
    transition: background .15s ease;
    display: flex; align-items: center; justify-content: center; gap: .4rem;
    width: 100%;
}
.btn-save-order:hover { background: var(--brass-deep); }
.btn-cancel-order {
    background: transparent;
    border: 1px solid var(--line);
    color: var(--ink-soft);
    font-weight: 600;
    font-size: .88rem;
    border-radius: 999px;
    padding: .65rem 1.5rem;
    text-decoration: none;
    text-align: center;
    display: block;
    transition: all .15s ease;
    margin-top: .6rem;
}
.btn-cancel-order:hover { border-color: var(--ink-faint); color: var(--ink); background: var(--line-soft); }

/* ══════════════════════════════════════════════════════
   MODAL
══════════════════════════════════════════════════════ */
.modal-content { border-radius: var(--radius); border: none; font-family: var(--sans); }
.modal-header  { border-bottom: 1px solid var(--line); padding: 1.1rem 1.4rem; }
.modal-header .modal-title { font-family: var(--serif); font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; gap: .5rem; }
.modal-header .modal-title i { color: var(--brass); }
.modal-body   { padding: 1.4rem; }
.modal-footer { border-top: 1px solid var(--line); padding: 1rem 1.4rem; }

/* Search results list */
#product-results-list { max-height: 260px; overflow-y: auto; border: 1px solid var(--line); border-radius: 8px; }
.result-item {
    padding: .7rem 1rem;
    border-bottom: 1px solid var(--line-soft);
    cursor: pointer;
    display: flex; justify-content: space-between; align-items: center;
    font-size: .88rem;
    transition: background .1s ease;
}
.result-item:last-child { border-bottom: none; }
.result-item:hover { background: var(--brass-tint); }
.result-item.is-selected { background: var(--brass-tint); }
.result-pill {
    font-size: .68rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase;
    color: var(--brass-deep); background: var(--brass-tint);
    border-radius: 4px; padding: .1rem .4rem; margin-top: .2rem; display: inline-block;
}

/* Attribute chips */
#attr-picker { display: flex; flex-wrap: wrap; gap: .5rem; }
.attr-chip {
    border: 1px solid var(--line); background: var(--surface);
    border-radius: 8px; padding: .5rem .9rem;
    cursor: pointer; font-size: .85rem; font-weight: 600;
    color: var(--ink); transition: all .15s ease; text-align: left;
}
.attr-chip small { display: block; font-weight: 500; color: var(--ink-faint); font-size: .72rem; margin-top: .1rem; }
.attr-chip:hover { border-color: var(--brass); background: var(--brass-tint); }
.attr-chip.selected { border-color: var(--brass); background: var(--brass); color: #fff; }
.attr-chip.selected small { color: rgba(255,255,255,.8); }
.attr-chip:disabled, .attr-chip.oos { opacity: .42; cursor: not-allowed; }
.attr-chip.oos:hover { border-color: var(--line); background: var(--surface); }

.btn-confirm-add {
    background: var(--ink); color: #fff; border: none;
    border-radius: 999px; font-weight: 600; font-size: .85rem;
    padding: .55rem 1.2rem; transition: background .15s ease;
}
.btn-confirm-add:hover:not(:disabled) { background: var(--brass-deep); }
.btn-confirm-add:disabled { opacity: .45; cursor: not-allowed; }
.btn-modal-close {
    background: transparent; border: 1px solid var(--line); color: var(--ink-soft);
    border-radius: 999px; font-size: .85rem; font-weight: 600; padding: .55rem 1.2rem;
}

/* Search spinner */
#search-spinner { display: none; }

/* Page submit spinner overlay */
#submit-spinner {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(33,28,22,.55);
    align-items: center; justify-content: center;
    backdrop-filter: blur(2px);
}
#submit-spinner .spinner-box {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 2rem 2.5rem;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,.18);
}
#submit-spinner .spinner-box p {
    margin-top: .85rem; margin-bottom: 0;
    font-family: var(--serif); font-size: 1rem;
    color: var(--ink-soft);
}

@media (max-width: 767px) {
    .oe-wrap { margin: -1rem -0.25rem; padding: 1.5rem 1rem 3rem; }
    .oe-header h1 { font-size: 1.45rem; }
    .discount-subrow { grid-template-columns: 1fr 1fr; }
    .discount-subrow .ds-note-col { grid-column: 1 / -1; }
}
</style>

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active fw-semibold" aria-current="page">Edit #{{ $order->uuid }}</li>
@endsection

@section('content')

{{-- Page-submit overlay spinner --}}
<div id="submit-spinner" role="status" aria-label="Saving order">
    <div class="spinner-box">
        <div class="spinner-border" style="color:var(--brass); width:2.4rem; height:2.4rem;" role="status"></div>
        <p>Saving order…</p>
    </div>
</div>

<div class="oe-wrap">

    {{-- ── Page header ──────────────────────────────────────────── --}}
    <div class="oe-header">
        <div>
            <span class="eyebrow">Order Management</span>
            <h1>Edit Order <span class="order-id">#{{ $order->uuid }}</span></h1>
        </div>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn-ghost-back">
            <i class="bx bx-arrow-back"></i> Back to Order
        </a>
    </div>

    {{-- ── Flash messages ───────────────────────────────────────── --}}
    @if (session('error'))
        <div class="oe-flash error" role="alert">
            <span><i class="bx bx-error-circle me-1"></i>{{ session('error') }}</span>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('success'))
        <div class="oe-flash success" role="alert">
            <span><i class="bx bx-check-circle me-1"></i>{{ session('success') }}</span>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation errors summary --}}
    @if ($errors->any())
        <div class="oe-flash error" role="alert">
            <div>
                <strong><i class="bx bx-error-circle me-1"></i>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 ps-3" style="font-size:.84rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.orders.update', $order) }}" method="POST"
          id="order-edit-form" novalidate>
        @csrf
        @method('PUT')

        {{-- ═══════════════ TOP ROW: details + summary ═══════════════ --}}
        <div class="row">

            {{-- ── LEFT: Status / Customer ──────────────────────── --}}
            <div class="col-lg-8">

                {{-- ── Status & Payment ──────────────────────────── --}}
                <div class="oe-card">
                    <div class="oe-card-head">
                        <span class="label"><i class="bx bx-cog"></i> Status &amp; Payment</span>
                    </div>
                    <div class="oe-card-body row g-3">
                        <div class="col-md-6">
                            <label for="status">Order Status <span class="text-danger">*</span></label>
                            <select id="status" name="status"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="in-transit" @selected(old('status', $order->status) === 'in-transit')>In-Transit</option>
                                <option value="completed"  @selected(old('status', $order->status) === 'completed') >Completed</option>
                                <option value="cancelled"  @selected(old('status', $order->status) === 'cancelled') >Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select id="payment_method" name="payment_method"
                                    class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="prepaid" @selected(old('payment_method', $order->payment_method) === 'prepaid')>Prepaid</option>
                                <option value="cod"     @selected(old('payment_method', $order->payment_method) === 'cod')    >Cash on Delivery</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Shipping charges (mirrors orders table columns) ── --}}
                        <div class="col-md-6">
                            <label for="shipping_charge">Shipping Charge (₹)</label>
                            <input type="number" id="shipping_charge" name="shipping_charge"
                                   class="form-control @error('shipping_charge') is-invalid @enderror"
                                   value="{{ old('shipping_charge', $order->shipping_charge ?? 0) }}"
                                   min="0" step="0.01">
                            @error('shipping_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="shipping_charge_gst">Shipping Charge GST (₹)</label>
                            <input type="number" id="shipping_charge_gst" name="shipping_charge_gst"
                                   class="form-control @error('shipping_charge_gst') is-invalid @enderror"
                                   value="{{ old('shipping_charge_gst', $order->shipping_charge_gst ?? 0) }}"
                                   min="0" step="0.01" readonly>
                            @error('shipping_charge_gst')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── Customer & Shipping ───────────────────────── --}}
                <div class="oe-card">
                    <div class="oe-card-head">
                        <span class="label"><i class="bx bx-user-pin"></i> Customer &amp; Shipping</span>
                    </div>
                    <div class="oe-card-body row g-3">
                        <div class="col-md-6">
                            <label for="name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $order->name) }}" required maxlength="255">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label>Customer Email</label>
                            <input type="email" class="form-control" value="{{ $order->email }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="mobile">Mobile <span class="text-danger">*</span></label>
                            <input type="tel" id="mobile" name="mobile"
                                   class="form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', $order->mobile) }}" required maxlength="20">
                            @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="zipcode">Zip Code</label>
                            <input type="text" id="zipcode" name="zipcode"
                                   class="form-control @error('zipcode') is-invalid @enderror"
                                   value="{{ old('zipcode', $order->zipcode) }}" maxlength="25">
                            @error('zipcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state"
                                   class="form-control @error('state') is-invalid @enderror"
                                   value="{{ old('state', $order->state) }}" maxlength="255">
                            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $order->city) }}" maxlength="255">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="locality">Locality / Area</label>
                            <input type="text" id="locality" name="locality"
                                   class="form-control @error('locality') is-invalid @enderror"
                                   value="{{ old('locality', $order->locality) }}" maxlength="255">
                            @error('locality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="address">Full Shipping Address <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      required maxlength="500">{{ old('address', $order->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

            </div>{{-- /col-lg-8 --}}

            {{-- ── RIGHT: Summary + actions ──────────────────────── --}}
            <div class="col-lg-4">

                {{-- ── Order Summary (server-computed values) ─── --}}
                <div class="oe-summary" aria-label="Current order totals">
                    <div class="summary-title">
                        <i class="bx bx-receipt"></i> Current Totals
                    </div>

                    <div class="summary-row">
                        <span>Subtotal <small style="opacity:.6; font-size:.75rem;">(ex. GST)</small></span>
                        <span>₹{{ number_format($order->subtotal_ex_gst ?? 0, 2) }}</span>
                    </div>

                    @if(($order->items->sum('coupon_discount')) > 0)
                    <div class="summary-row" style="color:#9fd3b4;">
                        <span><i class="bx bx-purchase-tag-alt" style="font-size:.82rem;"></i> Coupon Discounts</span>
                        <span>− ₹{{ number_format($order->items->sum('coupon_discount'), 2) }}</span>
                    </div>
                    @endif

                    @if(($order->items->sum('manual_discount_amount')) > 0)
                    <div class="summary-row" style="color:#9fd3b4;">
                        <span><i class="bx bx-scissors" style="font-size:.82rem;"></i> Manual Discounts</span>
                        <span>− ₹{{ number_format($order->items->sum('manual_discount_amount'), 2) }}</span>
                    </div>
                    @endif

                    <hr style="border:none; border-top:1px dashed rgba(255,255,255,.15); margin:.4rem 0;">

                    <div class="summary-row s-taxable">
                        <span>Taxable Value <small style="font-weight:400; font-size:.72rem; opacity:.65;">(GST base)</small></span>
                        <span>₹{{ number_format($order->taxable_value ?? 0, 2) }}</span>
                    </div>

                    @if(($order->tax_amount ?? 0) > 0)
                    <div class="summary-row s-gst">
                        <span>+ GST</span>
                        <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @endif

                    @if(($order->shipping_charge ?? 0) > 0)
                    <div class="summary-row">
                        <span><i class="bx bx-truck" style="font-size:.82rem;"></i> Shipping Charge</span>
                        <span>₹{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    @endif

                    @if(($order->shipping_charge_gst ?? 0) > 0)
                    <div class="summary-row s-gst">
                        <span>+ Shipping GST</span>
                        <span>₹{{ number_format($order->shipping_charge_gst, 2) }}</span>
                    </div>
                    @endif

                    <div class="summary-row s-total">
                        <span>Grand Total</span>
                        <span class="s-amount">₹{{ number_format($order->total ?? 0, 2) }}</span>
                    </div>

                    <p class="summary-note">
                        Totals update after saving.
                    </p>
                </div>

                {{-- ── Save / Cancel ─────────────────────────────── --}}
                <button type="submit" class="btn-save-order" form="order-edit-form" id="save-btn">
                    <i class="bx bx-check-circle"></i> Update Order
                </button>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn-cancel-order">
                    Cancel
                </a>

            </div>{{-- /col-lg-4 --}}

        </div>{{-- /top row --}}

        {{-- ═══════════════ FULL-WIDTH ROW: ORDER ITEMS ═══════════════ --}}
        <div class="row">
            <div class="col-lg-12">

                <div class="oe-card">
                    <div class="oe-card-head">
                        <span class="label"><i class="bx bx-package"></i> Order Items</span>
                        <button type="button" class="btn-add-item"
                                data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bx bx-plus"></i> Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="items-table" id="items-table" aria-label="Order items">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Unit Price (₹ incl. GST)</th>
                                    <th>Qty</th>
                                    <th>Line Total (₹)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">

                                @forelse($order->items as $idx => $item)
                                    {{-- ── Main product row ───────────────────── --}}
                                    <tr class="item-main-row"
                                        id="item-row-{{ $item->id }}"
                                        data-item-id="{{ $item->id }}">

                                        <td class="cell-num">{{ $loop->iteration }}</td>

                                        <td>
                                            {{-- Hidden inputs for this item --}}
                                            <input type="hidden"
                                                   name="items[{{ $idx }}][id]"
                                                   value="{{ $item->id }}">
                                            <input type="hidden"
                                                   name="items[{{ $idx }}][_delete]"
                                                   value="0"
                                                   class="delete-flag"
                                                   id="delete-flag-{{ $item->id }}">

                                            <span class="fw-semibold">{{ $item->product->name ?? 'N/A' }}</span>
                                            @if ($item->product?->code)
                                                <br><small style="color:var(--ink-faint);">
                                                    {{ $item->product->code }}
                                                </small>
                                            @endif
                                            @if ($item->productAttribute?->size)
                                                <span class="badge-size">{{ $item->productAttribute->size }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <input type="number"
                                                   name="items[{{ $idx }}][price]"
                                                   class="form-control form-control-sm price-input"
                                                   value="{{ old("items.{$idx}.price", $item->price) }}"
                                                   min="0" step="0.01" required
                                                   aria-label="Unit price for {{ $item->product->name ?? 'item' }}">
                                        </td>

                                        <td>
                                            <input type="number"
                                                   name="items[{{ $idx }}][quantity]"
                                                   class="form-control form-control-sm qty-input"
                                                   value="{{ old("items.{$idx}.quantity", $item->quantity) }}"
                                                   min="1" required
                                                   aria-label="Quantity for {{ $item->product->name ?? 'item' }}">
                                        </td>

                                        <td class="item-line-total">
                                            ₹{{ number_format($item->line_total, 2) }}
                                        </td>

                                        <td class="cell-actions">
                                            <button type="button"
                                                    class="btn-row-delete"
                                                    data-item-id="{{ $item->id }}"
                                                    aria-label="Mark item for removal"
                                                    title="Remove item">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- ── Discount sub-row (per-item) ──────────── --}}
                                    <tr class="item-discount-row"
                                        id="item-discount-{{ $item->id }}"
                                        data-item-id="{{ $item->id }}">
                                        <td></td>
                                        <td colspan="5">
                                            <div class="discount-subrow">

                                                {{-- Coupon code --}}
                                                <div>
                                                    <span class="ds-label">
                                                        <i class="bx bx-purchase-tag-alt"
                                                           style="font-size:.85rem; vertical-align:middle;"></i>
                                                        Coupon Code
                                                    </span>
                                                    <input type="text"
                                                           name="items[{{ $idx }}][coupon_code]"
                                                           class="form-control @error("items.{$idx}.coupon_code") is-invalid @enderror"
                                                           value="{{ old("items.{$idx}.coupon_code", $item->coupon?->code ?? '') }}"
                                                           placeholder="e.g. SAVE10"
                                                           maxlength="50"
                                                           style="text-transform:uppercase; letter-spacing:.04em; font-weight:600;">
                                                    @if($item->coupon)
                                                        <span class="coupon-display mt-1">
                                                            <i class="bx bx-check-circle"></i>
                                                            {{ $item->coupon->code }}
                                                            — saved ₹{{ number_format($item->coupon_discount, 2) }}
                                                        </span>
                                                    @endif
                                                    @error("items.{$idx}.coupon_code")
                                                        <div class="invalid-feedback d-block" style="font-size:.76rem;">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Manual discount type --}}
                                                <div>
                                                    <span class="ds-label">
                                                        <i class="bx bx-scissors"
                                                           style="font-size:.85rem; vertical-align:middle;"></i>
                                                        Manual Discount
                                                    </span>
                                                    <select name="items[{{ $idx }}][manual_discount_type]"
                                                            class="form-select">
                                                        <option value=""      @selected(old("items.{$idx}.manual_discount_type", $item->manual_discount_type) === null || old("items.{$idx}.manual_discount_type", $item->manual_discount_type) === '')>— None —</option>
                                                        <option value="flat"  @selected(old("items.{$idx}.manual_discount_type", $item->manual_discount_type) === 'flat')  >Flat (₹)</option>
                                                        <option value="percent" @selected(old("items.{$idx}.manual_discount_type", $item->manual_discount_type) === 'percent')>Percent (%)</option>
                                                    </select>
                                                </div>

                                                {{-- Manual discount value --}}
                                                <div>
                                                    <span class="ds-label">Value</span>
                                                    <input type="number"
                                                           name="items[{{ $idx }}][manual_discount_value]"
                                                           class="form-control @error("items.{$idx}.manual_discount_value") is-invalid @enderror"
                                                           value="{{ old("items.{$idx}.manual_discount_value", $item->manual_discount_value ?? 0) }}"
                                                           min="0" step="0.01" placeholder="0">
                                                    @if($item->manual_discount_amount > 0)
                                                        <small style="color:var(--ink-faint); font-size:.72rem; display:block; margin-top:.2rem;">
                                                            Applied: ₹{{ number_format($item->manual_discount_amount, 2) }}
                                                        </small>
                                                    @endif
                                                    @error("items.{$idx}.manual_discount_value")
                                                        <div class="invalid-feedback d-block" style="font-size:.76rem;">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Note --}}
                                                <div class="ds-note-col">
                                                    <span class="ds-label">Note</span>
                                                    <input type="text"
                                                           name="items[{{ $idx }}][manual_discount_note]"
                                                           class="form-control"
                                                           value="{{ old("items.{$idx}.manual_discount_note", $item->manual_discount_note) }}"
                                                           placeholder="e.g. Loyalty discount"
                                                           maxlength="255">
                                                </div>

                                            </div>{{-- /discount-subrow --}}
                                        </td>
                                    </tr>

                                @empty
                                    <tr id="no-items-row">
                                        <td colspan="6" class="empty-row">
                                            <i class="bx bx-package" style="font-size:1.5rem; display:block; margin-bottom:.4rem; color:var(--line);"></i>
                                            No items yet. Use "Add Item" to add products.
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- New items appended here by JS --}}

                            </tbody>
                        </table>
                    </div>
                </div>{{-- /items card --}}

            </div>{{-- /col-lg-12 --}}
        </div>{{-- /items row --}}

    </form>

</div>{{-- /oe-wrap --}}


{{-- ══════════════ ADD ITEM MODAL ══════════════ --}}
<div class="modal fade" id="addItemModal" tabindex="-1"
     aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">
                    <i class="bx bx-search-alt"></i> Add Product to Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Search box --}}
                <div class="input-group mb-3">
                    <span class="input-group-text" style="background:var(--paper); border-color:var(--line);">
                        <i class="bx bx-search" style="color:var(--ink-faint);"></i>
                    </span>
                    <input type="text" id="product-search-input"
                           class="form-control"
                           placeholder="Search by name or code…"
                           autocomplete="off"
                           style="border-left:none; background:var(--paper); border-color:var(--line);">
                </div>

                {{-- Spinner --}}
                <div id="search-spinner" class="text-center py-2" aria-live="polite">
                    <div class="spinner-border spinner-border-sm" style="color:var(--brass);" role="status">
                        <span class="visually-hidden">Searching…</span>
                    </div>
                </div>

                {{-- Results --}}
                <div id="product-results-list" style="display:none;" role="listbox" aria-label="Product search results">
                    {{-- Populated by JS --}}
                </div>

                <div id="search-empty" class="text-center py-4" style="color:var(--ink-faint); display:none;">
                    <i class="bx bx-package" style="font-size:1.8rem;"></i>
                    <p class="mb-0 mt-1" style="font-size:.85rem;">No products found.</p>
                </div>

                <div id="search-placeholder" class="text-center py-4" style="color:var(--ink-faint);">
                    <i class="bx bx-search-alt" style="font-size:1.8rem;"></i>
                    <p class="mb-0 mt-1" style="font-size:.85rem;">Type to search products</p>
                </div>

                {{-- Selected product form --}}
                <div id="selected-product-form" class="mt-3 p-3 d-none"
                     style="background:var(--paper); border:1px solid var(--line); border-radius:8px;">

                    <h6 class="fw-bold mb-3" style="font-family:var(--serif); color:var(--ink);">
                        <i class="bx bx-package me-1" style="color:var(--brass);"></i>
                        <span id="selected-product-name"></span>
                    </h6>

                    <input type="hidden" id="new-product-id">
                    <input type="hidden" id="new-attribute-id" value="">
                    <input type="hidden" id="new-product-gst" value="0">

                    {{-- Attribute / size picker --}}
                    <div id="attr-picker-wrap" class="mb-3 d-none">
                        <label class="field-label">
                            Select Size / Variant <span class="text-danger">*</span>
                        </label>
                        <div id="attr-picker"></div>
                        <p id="attr-required-msg" class="mb-0 mt-1"
                           style="font-size:.8rem; color:var(--red); display:none;">
                            Please select a size before adding.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label" for="new-price">Unit Price (₹ incl. GST)</label>
                            <input type="number" id="new-price" class="form-control"
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="new-qty">Quantity</label>
                            <input type="number" id="new-qty" class="form-control"
                                   value="1" min="1">
                        </div>

                        {{-- Per-item discount fields for new items --}}
                        <div class="col-md-6">
                            <label class="field-label" for="new-coupon">Coupon Code</label>
                            <input type="text" id="new-coupon" class="form-control"
                                   placeholder="e.g. SAVE10" maxlength="50"
                                   style="text-transform:uppercase; letter-spacing:.04em; font-weight:600;">
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="new-disc-type">Manual Discount Type</label>
                            <select id="new-disc-type" class="form-select">
                                <option value="">— None —</option>
                                <option value="flat">Flat (₹)</option>
                                <option value="percent">Percent (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="new-disc-value">Discount Value</label>
                            <input type="number" id="new-disc-value" class="form-control"
                                   min="0" step="0.01" placeholder="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="new-disc-note">Discount Note</label>
                            <input type="text" id="new-disc-note" class="form-control"
                                   placeholder="e.g. Staff order" maxlength="255">
                        </div>
                    </div>

                </div>{{-- /selected-product-form --}}

            </div>{{-- /modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-confirm-add" id="confirm-add-btn" disabled>
                    <i class="bx bx-plus-circle me-1"></i> Add to Order
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

{{-- ══════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════ --}}
@section('scripts')
<script>
(function () {
    'use strict';

    /* ── Config ───────────────────────────────────────────────────────── */
    const SEARCH_URL = "{{ route('admin.orders.products.search') }}";
    const CSRF       = "{{ csrf_token() }}";

    /* ── Utility: ₹ formatted like the server (comma separated) ────────── */
    function fmtMoney(n) {
        return Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* ── 1. Submit spinner ────────────────────────────────────────────── */
    const form    = document.getElementById('order-edit-form');
    const spinner = document.getElementById('submit-spinner');

    form.addEventListener('submit', function () {
        spinner.style.display = 'flex';
        document.getElementById('save-btn').disabled = true;
    });

    /* ── 2 & 3. Item delete toggle ────────────────────────────────────── */
    document.getElementById('items-tbody').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-row-delete');
        if (!btn) return;

        const mainRow     = btn.closest('.item-main-row');
        const deleteFlag  = mainRow ? mainRow.querySelector('.delete-flag') : null;

        if (deleteFlag) {
            /* Existing DB item — toggle mark-for-delete */
            const isDeleted = deleteFlag.value === '1';
            deleteFlag.value = isDeleted ? '0' : '1';
            mainRow.classList.toggle('deleted-row', !isDeleted);

            /* Also visually dim the sibling discount sub-row */
            const itemId     = mainRow.dataset.itemId;
            const discountRow = document.getElementById('item-discount-' + itemId);
            if (discountRow) discountRow.classList.toggle('deleted-row', !isDeleted);

            btn.innerHTML = isDeleted
                ? "<i class='bx bx-trash'></i>"
                : "<i class='bx bx-undo'></i>";
            btn.classList.toggle('is-marked', !isDeleted);
            btn.setAttribute('aria-label', isDeleted ? 'Mark item for removal' : 'Undo removal');
        } else {
            /* New (unsaved) item — remove both rows from DOM */
            const newRow     = btn.closest('tr[data-new-row]');
            const discountRow = newRow
                ? document.querySelector('tr[data-new-discount="' + newRow.dataset.newRow + '"]')
                : null;

            if (discountRow) discountRow.remove();
            if (newRow)      newRow.remove();

            renumberRows();
        }
    });

    /* ── 3b. Live line-total recalculation as price/qty change ─────────── */
    document.getElementById('items-tbody').addEventListener('input', function (e) {
        if (!e.target.classList.contains('price-input') && !e.target.classList.contains('qty-input')) return;

        const row = e.target.closest('tr');
        if (!row) return;

        const priceInput = row.querySelector('.price-input');
        const qtyInput    = row.querySelector('.qty-input');
        const totalCell   = row.querySelector('.item-line-total');
        if (!priceInput || !qtyInput || !totalCell) return;

        const price = parseFloat(priceInput.value) || 0;
        const qty   = parseInt(qtyInput.value, 10) || 0;
        totalCell.textContent = '₹' + fmtMoney(price * qty);
    });

    /* ── Renumber the leading "#" column after a new row is removed ────── */
    function renumberRows() {
        document.querySelectorAll('#items-tbody tr.item-main-row').forEach(function (row, i) {
            const cell = row.querySelector('.cell-num');
            if (cell) cell.textContent = i + 1;
        });
    }

    /* ── 4. Product search (modal) ────────────────────────────────────── */
    let searchTimer;
    let productCache = {};

    const searchInput    = document.getElementById('product-search-input');
    const resultsList    = document.getElementById('product-results-list');
    const searchEmpty    = document.getElementById('search-empty');
    const searchPH       = document.getElementById('search-placeholder');
    const searchSpin     = document.getElementById('search-spinner');
    const selectedForm   = document.getElementById('selected-product-form');
    const confirmBtn     = document.getElementById('confirm-add-btn');
    const attrWrap       = document.getElementById('attr-picker-wrap');
    const attrPicker     = document.getElementById('attr-picker');
    const attrRequiredMsg = document.getElementById('attr-required-msg');

    function resetSearchUI() {
        resultsList.style.display  = 'none';
        resultsList.innerHTML      = '';
        searchEmpty.style.display  = 'none';
        searchPH.style.display     = 'block';
        searchSpin.style.display   = 'none';
        selectedForm.classList.add('d-none');
        attrWrap.classList.add('d-none');
        attrPicker.innerHTML       = '';
        document.getElementById('new-product-id').value  = '';
        document.getElementById('new-attribute-id').value = '';
        document.getElementById('new-product-gst').value  = '0';
        document.getElementById('new-price').value        = '';
        document.getElementById('new-qty').value           = '1';
        document.getElementById('new-coupon').value        = '';
        document.getElementById('new-disc-type').value     = '';
        document.getElementById('new-disc-value').value    = '0';
        document.getElementById('new-disc-note').value     = '';
        confirmBtn.disabled = true;
        productCache = {};
    }

    /* Reset modal state when it closes */
    document.getElementById('addItemModal').addEventListener('hidden.bs.modal', function () {
        searchInput.value = '';
        resetSearchUI();
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            resetSearchUI();
            return;
        }

        searchPH.style.display    = 'none';
        resultsList.style.display = 'none';
        searchEmpty.style.display = 'none';
        searchSpin.style.display  = 'block';

        searchTimer = setTimeout(async function () {
            try {
                const res  = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                });
                const data = await res.json();
                searchSpin.style.display = 'none';

                if (!Array.isArray(data) || !data.length) {
                    searchEmpty.style.display = 'block';
                    return;
                }

                productCache = {};
                data.forEach(function (p) { productCache[p.id] = p; });

                resultsList.innerHTML = data.map(function (p) {
                    const hasAttrs = p.attributes && p.attributes.length > 0;
                    return '<div class="result-item" role="option" tabindex="0" data-id="' + p.id + '">' +
                        '<div>' +
                            '<span class="fw-semibold">' + escHtml(p.name) + '</span>' +
                            (p.code ? '<br><small style="color:var(--ink-faint);">' + escHtml(p.code) + '</small>' : '') +
                            (hasAttrs ? '<br><span class="result-pill">' + p.attributes.length + ' size' + (p.attributes.length > 1 ? 's' : '') + '</span>' : '') +
                        '</div>' +
                        '<span style="font-family:var(--serif); font-weight:600; color:var(--brass-deep);">' +
                            (hasAttrs ? 'From ' : '') + '₹' + fmtMoney(p.selling || 0) +
                        '</span>' +
                    '</div>';
                }).join('');

                resultsList.style.display = 'block';

            } catch (err) {
                searchSpin.style.display = 'none';
                resultsList.innerHTML    = '<div class="text-center py-3" style="color:var(--red); font-size:.85rem;">Search failed. Please try again.</div>';
                resultsList.style.display = 'block';
            }
        }, 320);
    });

    /* ── Select a product from results ───────────────────────────────── */
    resultsList.addEventListener('click', function (e) {
        const item    = e.target.closest('.result-item');
        if (!item) return;
        selectProduct(productCache[item.dataset.id]);
    });

    /* Keyboard accessibility on result items */
    resultsList.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            const item = e.target.closest('.result-item');
            if (item) selectProduct(productCache[item.dataset.id]);
        }
    });

    function selectProduct(product) {
        if (!product) return;

        document.querySelectorAll('.result-item').forEach(function (el) {
            el.classList.remove('is-selected');
        });
        document.querySelector('.result-item[data-id="' + product.id + '"]')
            ?.classList.add('is-selected');

        document.getElementById('new-product-id').value  = product.id;
        document.getElementById('new-attribute-id').value = '';
        document.getElementById('new-product-gst').value  = product.gst || 0;
        document.getElementById('selected-product-name').textContent = product.name;
        attrRequiredMsg.style.display = 'none';

        const hasAttrs = product.attributes && product.attributes.length > 0;

        if (hasAttrs) {
            /* Render size chips */
            attrPicker.innerHTML = product.attributes.map(function (a) {
                const oos = parseInt(a.stock, 10) <= 0;
                return '<button type="button" class="attr-chip' + (oos ? ' oos' : '') + '"' +
                    (oos ? ' disabled' : '') +
                    ' data-attr-id="' + a.id + '"' +
                    ' data-price="' + a.selling_price + '">' +
                    escHtml(a.size) +
                    '<small>₹' + fmtMoney(a.selling_price) +
                    (oos ? ' · Out of stock' : '') + '</small>' +
                '</button>';
            }).join('');
            attrWrap.classList.remove('d-none');
            document.getElementById('new-price').value = '';
            confirmBtn.disabled = true;
        } else {
            attrWrap.classList.add('d-none');
            attrPicker.innerHTML = '';
            document.getElementById('new-price').value = product.selling || '';
            confirmBtn.disabled = false;
        }

        selectedForm.classList.remove('d-none');
    }

    /* ── 5. Attribute chip selection ─────────────────────────────────── */
    attrPicker.addEventListener('click', function (e) {
        const chip = e.target.closest('.attr-chip');
        if (!chip || chip.disabled) return;

        document.querySelectorAll('.attr-chip').forEach(function (c) {
            c.classList.remove('selected');
        });
        chip.classList.add('selected');

        document.getElementById('new-attribute-id').value = chip.dataset.attrId;
        document.getElementById('new-price').value        = chip.dataset.price;
        attrRequiredMsg.style.display = 'none';
        confirmBtn.disabled = false;
    });

    /* ── 6. Confirm add → append rows that match the existing markup ──── */
    let newIdx = 0;

    confirmBtn.addEventListener('click', function () {
        const productId   = document.getElementById('new-product-id').value;
        const attributeId = document.getElementById('new-attribute-id').value;
        const product     = productCache[productId];
        const price       = parseFloat(document.getElementById('new-price').value)     || 0;
        const qty         = parseInt(document.getElementById('new-qty').value, 10)     || 1;
        const coupon      = document.getElementById('new-coupon').value.trim().toUpperCase();
        const discType    = document.getElementById('new-disc-type').value;
        const discValue   = parseFloat(document.getElementById('new-disc-value').value) || 0;
        const discNote    = document.getElementById('new-disc-note').value.trim();
        const gstRate     = parseFloat(document.getElementById('new-product-gst').value) || 0;

        /* Guard: variant product needs a size */
        if (product && product.attributes && product.attributes.length && !attributeId) {
            attrRequiredMsg.style.display = 'block';
            return;
        }

        if (!productId || price <= 0 || qty < 1) {
            return; /* silent guard — UI should prevent this */
        }

        const idx          = newIdx++;
        const selectedAttr = (product?.attributes || []).find(function (a) {
            return String(a.id) === String(attributeId);
        });
        const sizeLabel = selectedAttr ? selectedAttr.size : null;

        /* Remove "no items" placeholder */
        const noItemsRow = document.getElementById('no-items-row');
        if (noItemsRow) noItemsRow.remove();

        const rowCount = document.querySelectorAll(
            '#items-tbody tr.item-main-row'
        ).length + 1;

        const tbody = document.getElementById('items-tbody');

        /* ── Main row (visible) — same classes as a server-rendered row ── */
        const mainTr = document.createElement('tr');
        mainTr.classList.add('item-main-row');
        mainTr.setAttribute('data-new-row', idx);
        mainTr.dataset.gst = gstRate;
        mainTr.innerHTML =
            '<td class="cell-num">' + rowCount + '</td>' +
            '<td>' +
                '<span class="fw-semibold">' + escHtml(product?.name || '') + '</span>' +
                (sizeLabel ? '<span class="badge-size">' + escHtml(sizeLabel) + '</span>' : '') +
                '<span class="badge-new">New</span>' +
            '</td>' +
            '<td>' +
                '<input type="number" name="new_items[' + idx + '][price]"' +
                '       class="form-control form-control-sm price-input"' +
                '       value="' + price + '" min="0" step="0.01" required>' +
            '</td>' +
            '<td>' +
                '<input type="number" name="new_items[' + idx + '][quantity]"' +
                '       class="form-control form-control-sm qty-input"' +
                '       value="' + qty + '" min="1" required>' +
            '</td>' +
            '<td class="item-line-total">₹' + fmtMoney(qty * price) + '</td>' +
            '<td class="cell-actions">' +
                '<button type="button" class="btn-row-delete" title="Remove item" aria-label="Remove item">' +
                    '<i class="bx bx-trash"></i>' +
                '</button>' +
            '</td>';

        tbody.appendChild(mainTr);

        /* ── Discount sub-row (visible & editable, same as existing items) ── */
        const discTr = document.createElement('tr');
        discTr.classList.add('item-discount-row');
        discTr.setAttribute('data-new-discount', idx);
        discTr.innerHTML =
            '<td></td>' +
            '<td colspan="5">' +
                '<div class="discount-subrow">' +
                    '<div>' +
                        '<span class="ds-label"><i class="bx bx-purchase-tag-alt" style="font-size:.85rem; vertical-align:middle;"></i> Coupon Code</span>' +
                        '<input type="text" name="new_items[' + idx + '][coupon_code]" class="form-control" ' +
                               'value="' + escAttr(coupon) + '" placeholder="e.g. SAVE10" maxlength="50" ' +
                               'style="text-transform:uppercase; letter-spacing:.04em; font-weight:600;">' +
                    '</div>' +
                    '<div>' +
                        '<span class="ds-label"><i class="bx bx-scissors" style="font-size:.85rem; vertical-align:middle;"></i> Manual Discount</span>' +
                        '<select name="new_items[' + idx + '][manual_discount_type]" class="form-select">' +
                            '<option value=""' + (discType === '' ? ' selected' : '') + '>— None —</option>' +
                            '<option value="flat"' + (discType === 'flat' ? ' selected' : '') + '>Flat (₹)</option>' +
                            '<option value="percent"' + (discType === 'percent' ? ' selected' : '') + '>Percent (%)</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<span class="ds-label">Value</span>' +
                        '<input type="number" name="new_items[' + idx + '][manual_discount_value]" class="form-control" ' +
                               'value="' + discValue + '" min="0" step="0.01" placeholder="0">' +
                    '</div>' +
                    '<div class="ds-note-col">' +
                        '<span class="ds-label">Note</span>' +
                        '<input type="text" name="new_items[' + idx + '][manual_discount_note]" class="form-control" ' +
                               'value="' + escAttr(discNote) + '" placeholder="e.g. Loyalty discount" maxlength="255">' +
                    '</div>' +
                '</div>' +
                buildHidden('new_items[' + idx + '][product_id]', productId) +
                buildHidden('new_items[' + idx + '][product_attribute_id]', attributeId) +
            '</td>';

        tbody.appendChild(discTr);

        /* ── Close modal & reset ───────────────────────────────────── */
        bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
    });

    /* ── Utility: build a hidden input ───────────────────────────────── */
    function buildHidden(name, value) {
        return '<input type="hidden" name="' + escAttr(name) + '" value="' + escAttr(String(value ?? '')) + '">';
    }

    /* ── Utility: minimal HTML escaping ──────────────────────────────── */
    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
    function escAttr(str) {
        return String(str ?? '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

})();
</script>
@endsection