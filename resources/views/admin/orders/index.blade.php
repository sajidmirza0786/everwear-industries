@extends('admin.master')

@section('seo')
    <title>Manage Orders | Admin Panel</title>
    <meta name="description" content="View and manage all customer orders on the On Jewel admin panel.">
@endsection

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>
    .orders-page { padding: 1.5rem; }

    /* ── Top bar ── */
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
    .page-title  { font-size: 1.1rem; font-weight: 600; color: #111; margin: 0; }
    .page-sub    { font-size: 0.75rem; color: #888; margin-top: 2px; }

    /* ── Filter card ── */
    .filter-card { background: #fff; border: 1px solid #e9e9e9; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; }
    .filter-card .form-control,
    .filter-card .form-select { font-size: 0.8rem; border-color: #e0e0e0; border-radius: 7px; height: 34px; padding: 0 10px; }
    .filter-card .form-label  { font-size: 0.72rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
    .btn-filter  { height: 34px; font-size: 0.8rem; border-radius: 7px; padding: 0 14px; }
    .btn-reset   { height: 34px; font-size: 0.8rem; border-radius: 7px; padding: 0 14px; }

    /* ── Table card ── */
    .table-card  { background: #fff; border: 1px solid #e9e9e9; border-radius: 10px; overflow: hidden; }
    .table-card-header { display: flex; justify-content: space-between; align-items: center; padding: .75rem 1.25rem; border-bottom: 1px solid #f0f0f0; }
    .table-card-title  { font-size: 0.8rem; font-weight: 600; color: #444; margin: 0; }
    .table-entries     { font-size: 0.72rem; color: #aaa; }

    /* ── Table ── */
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table thead th {
        font-size: 0.68rem; font-weight: 600; color: #aaa; text-transform: uppercase;
        letter-spacing: .05em; padding: 9px 12px; background: #fafafa;
        border-bottom: 1px solid #f0f0f0; white-space: nowrap;
    }
    .orders-table tbody td { padding: 11px 12px; font-size: 0.78rem; color: #333; border-bottom: 1px solid #f7f7f7; vertical-align: middle; }
    .orders-table tbody tr:last-child td { border-bottom: none; }
    .orders-table tbody tr:hover td { background: #fafafa; }

    /* Order ID */
    .order-id-link { font-weight: 600; color: #3b82f6; text-decoration: none; font-size: 0.78rem; }
    .order-id-link:hover { color: #2563eb; }

    /* Customer cell */
    .customer-name  { font-weight: 600; color: #222; line-height: 1.2; }
    .customer-email { font-size: 0.7rem; color: #aaa; margin-top: 1px; }

    /* Location */
    .location-text { font-size: 0.7rem; color: #aaa; margin-top: 1px; }

    /* Items */
    .items-count { font-weight: 500; }
    .items-weight { font-size: 0.7rem; color: #aaa; margin-top: 1px; }

    /* Amount */
    .amount-main     { font-weight: 600; color: #111; }
    .amount-discount { font-size: 0.7rem; color: #10b981; margin-top: 1px; }

    /* Status badges */
    .status-badge {
        display: inline-block; padding: 3px 9px; border-radius: 20px;
        font-size: 0.68rem; font-weight: 600; white-space: nowrap;
    }
    .status-pending   { background: #fef9c3; color: #854d0e; }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .status-in-transit{ background: #dbeafe; color: #1e40af; }

    /* Payment tags */
    .pay-tag { display: inline-block; padding: 3px 9px; border-radius: 5px; font-size: 0.68rem; font-weight: 600; letter-spacing: .03em; }
    .pay-prepaid { background: #eff6ff; color: #3b82f6; }
    .pay-cod     { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

    /* Date */
    .date-main { color: #444; }
    .date-time { font-size: 0.7rem; color: #bbb; margin-top: 1px; }

    /* Action buttons */
    .action-btn {
        width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px; border: 1px solid #e5e7eb; background: transparent;
        color: #9ca3af; text-decoration: none; font-size: 0.85rem; transition: all .15s;
    }
    .action-btn:hover         { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
    .action-btn.view:hover    { color: #3b82f6; border-color: #bfdbfe; background: #eff6ff; }
    .action-btn.edit:hover    { color: #f59e0b; border-color: #fde68a; background: #fffbeb; }

    /* Empty state */
    .empty-state { padding: 48px 16px; text-align: center; color: #bbb; font-size: 0.82rem; }

    /* Pagination */
    .pagination-wrap { padding: .75rem 1.25rem; border-top: 1px solid #f0f0f0; }

    /* Responsive: stack on mobile */
    @media (max-width: 767px) {
        .orders-table thead { display: none; }
        .orders-table tbody tr { display: block; padding: .75rem 1rem; border-bottom: 1px solid #f0f0f0; }
        .orders-table tbody td { display: flex; justify-content: space-between; padding: 4px 0; border: none; font-size: 0.78rem; }
        .orders-table tbody td::before { content: attr(data-label); font-weight: 600; color: #aaa; font-size: 0.7rem; }
    }
</style>

@section('content')
<div class="orders-page">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-sub">{{ $orders->total() }} total orders</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form action="{{ route('admin.orders.index') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search"
                        placeholder="Order ID, name, email, mobile…"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All statuses</option>
                        <option value="pending"    {{ request('status') == 'pending'    ? 'selected' : '' }}>Pending</option>
                        <option value="in-transit" {{ request('status') == 'in-transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="completed"  {{ request('status') == 'completed'  ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled"  {{ request('status') == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment</label>
                    <select class="form-select" name="payment_method">
                        <option value="">All methods</option>
                        <option value="prepaid" {{ request('payment_method') == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                        <option value="cod"     {{ request('payment_method') == 'cod'     ? 'selected' : '' }}>COD</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-filter w-100">
                        <i class='bx bx-filter-alt'></i>
                    </button>
                </div>
                @if(request('search') || request('status') || request('payment_method'))
                <div class="col-md-1">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-reset w-100">
                        <i class='bx bx-x'></i>
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <span class="table-card-title">All Orders</span>
            @if($orders->total() > 0)
                <span class="table-entries">
                    {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}
                </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    @php
                        $totalDiscount = $order->coupon_discount + $order->manual_discount;
                    @endphp
                    <tr>
                        <td data-label="#">
                            {{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}
                        </td>

                        <td data-label="Order">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="order-id-link">
                                #{{ $order->id }}
                            </a>
                        </td>

                        <td data-label="Customer">
                            <div class="customer-name">{{ $order->name }}</div>
                            <div class="customer-email">{{ $order->email }}</div>
                        </td>

                        <td data-label="Location">
                            <div>{{ $order->mobile }}</div>
                            @if($order->city || $order->state)
                                <div class="location-text">{{ implode(', ', array_filter([$order->city, $order->state])) }}</div>
                            @endif
                        </td>

                        <td data-label="Items">
                            <div class="items-count">{{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }}</div>
                            @if($order->total_weight > 0)
                                <div class="items-weight">{{ $order->total_weight }}g</div>
                            @endif
                        </td>

                        <td data-label="Amount">
                            <div class="amount-main">₹{{ number_format($order->total, 2) }}</div>
                            @if($totalDiscount > 0)
                                <div class="amount-discount">−₹{{ number_format($totalDiscount, 2) }} off</div>
                            @endif
                        </td>

                        <td data-label="Status">
                            <span class="status-badge status-{{ str_replace(' ', '-', $order->status) }}">
                                {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                            </span>
                        </td>

                        <td data-label="Payment">
                            <span class="pay-tag pay-{{ $order->payment_method }}">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </td>

                        <td data-label="Date">
                            <div class="date-main">{{ $order->created_at->format('M d, Y') }}</div>
                            <div class="date-time">{{ $order->created_at->format('h:i A') }}</div>
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="action-btn view" title="View">
                                    <i class='bx bx-show'></i>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order) }}"
                                   class="action-btn edit" title="Edit">
                                    <i class='bx bx-edit'></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class='bx bx-package' style="font-size:2rem;display:block;margin-bottom:8px;color:#ddd"></i>
                                No orders found matching your filters.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="pagination-wrap">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection