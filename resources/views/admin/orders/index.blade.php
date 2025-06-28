@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Manage Orders | Admin Panel</title>
    <meta name="description" content="View and manage all customer orders on the On Jewel admin panel.">
@endsection


    {{-- Boxicons CSS for professional icons --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .table-responsive-stack tbody tr {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.5rem;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef; /* Light border between grid rows */
        }
        .table-responsive-stack tbody td {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 0.25rem 0;
        }
        .table-responsive-stack tbody td::before {
            content: attr(data-label);
            font-weight: bold;
            color: #6c757d; /* Muted color for labels */
            margin-bottom: 0.25rem;
        }

        /* Hide table headers on small screens for stacked layout */
        @media (max-width: 767.98px) {
            .table-responsive-stack thead {
                display: none;
            }
        }

        /* Ensure standard table behavior on larger screens */
        @media (min-width: 768px) {
            .table-responsive-stack tbody tr {
                display: table-row; /* Revert to table row */
                border-bottom: none;
                padding: 0;
            }
            .table-responsive-stack tbody td {
                display: table-cell; /* Revert to table cell */
                padding: 0.75rem; /* Standard table cell padding */
            }
            .table-responsive-stack tbody td::before {
                content: none; /* Hide data-label on larger screens */
            }
        }

        .status-badge {
            padding: .4em .6em;
            border-radius: .25rem;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        .status-pending { background-color: #ffc107; color: #343a40; } /* Yellow */
        .status-completed { background-color: #28a745; color: #fff; } /* Green */
        .status-cancelled { background-color: #dc3545; color: #fff; } /* Red */

        .payment-method-tag {
            padding: .3em .5em;
            border-radius: .25rem;
            font-size: 0.75em;
            background-color: #6c757d; /* Gray */
            color: #fff;
            text-transform: uppercase;
        }
    </style>
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Order Management</h2>
        </div>

        {{-- Search and Filter Section --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label mb-1">Search</label>
                        <input type="text" class="form-control rounded" id="search" name="search" placeholder="Search by UUID, Name, Email, Mobile..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label mb-1">Status</label>
                        <select class="form-select rounded" id="status_filter" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_method_filter" class="form-label mb-1">Payment Method</label>
                        <select class="form-select rounded" id="payment_method_filter" name="payment_method">
                            <option value="">All Methods</option>
                            <option value="prepaid" {{ request('payment_method') == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                            <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary rounded w-100"><i class='bx bx-filter-alt me-1'></i> Filter</button>
                    </div>
                    @if(request('search') || request('status') || request('payment_method'))
                        <div class="col-md-2 offset-md-10 mt-2 mt-md-0">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded w-100"><i class='bx bx-refresh me-1'></i> Reset</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
                {{-- Displaying current page info if orders are paginated --}}
                @if($orders->total() > 0)
                    <span class="small text-muted">Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-responsive-stack small" id="ordersTable"> 
                        <thead>
                            <tr class="text-nowrap"> {{-- Prevents header wrapping on small screens --}}
                                <th scope="col">#</th>
                                <th scope="col">OD-ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Products</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Payment</th>
                                <th scope="col">Ordered On</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td data-label="#">{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td data-label="Order ID">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary fw-semibold text-decoration-none">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td data-label="Customer">
                                        <strong>{{ $order->name }}</strong><br>
                                        <span class="text-muted small">{{ $order->email }}</span>
                                    </td>
                                    <td data-label="Contact">{{ $order->mobile }}</td>
                                    <td data-label="Contact">Items: {{ $order->items->count() }}</td>
                                    <td data-label="Amount">₹{{ number_format($order->total, 2) }}</td>
                                    <td data-label="Status">
                                        <span class="status-badge status-{{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td data-label="Payment">
                                        <span class="payment-method-tag">
                                            {{ strtoupper($order->payment_method) }}
                                        </span>
                                    </td>
                                    <td data-label="Ordered On">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td data-label="Actions">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info text-white rounded" title="View Details">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-warning rounded" title="Edit Order">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            {{-- Example Delete Button (requires form for POST/DELETE request) --}}
                                            {{-- <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded" title="Delete Order">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">No orders found matching your criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links('pagination::bootstrap-5') }} {{-- Using Bootstrap 5 pagination theme --}}
                </div>
            </div>
        </div>
    </div>
@endsection