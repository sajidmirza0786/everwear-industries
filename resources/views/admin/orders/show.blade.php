@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Order Details #{{ $order->uuid }} | Admin Panel</title>
    <meta name="description" content="Detailed view of order {{ $order->uuid }} on the On Jewel admin panel.">
@endsection

@push('styles')
    {{-- Boxicons CSS for professional icons --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .detail-row {
            padding: 0.5rem 0;
            border-bottom: 1px dashed #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            color: #343a40;
        }
        .status-badge {
            padding: .4em .6em;
            border-radius: .25rem;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .status-pending { background-color: #ffc107; color: #343a40; } /* Yellow */
        .status-completed { background-color: #28a745; color: #fff; } /* Green */
        .status-cancelled { background-color: #dc3545; color: #fff; } /* Red */

        .payment-method-tag {
            padding: .3em .5em;
            border-radius: .25rem;
            font-size: 0.8em;
            background-color: #6c757d; /* Gray */
            color: #fff;
            text-transform: uppercase;
        }

        .order-items-table th, .order-items-table td {
            vertical-align: middle;
            padding: 0.85rem;
        }
        .order-items-table thead th {
            border-bottom: 2px solid #dee2e6;
        }
        .order-items-table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Order Details <span class="text-primary">#{{ $order->uuid }}</span></h2>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                    <i class='bx bx-arrow-back me-1'></i> Back to Orders
                </a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning rounded-pill">
                    <i class='bx bx-edit me-1'></i> Edit Order
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Order Status & Basic Info --}}
            <div class="col-lg-12">
                <div class="card shadow mb-4 rounded-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Order Placed On</p>
                            <h5 class="fw-bold mb-0">{{ $order->created_at->format('M d, Y H:i A') }}</h5>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small">Current Status</p>
                            <span class="status-badge status-{{ $order->status }} py-2 px-3">
                                <i class='bx bx-check-circle me-1' ></i> {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small">Payment Method</p>
                            <span class="payment-method-tag py-2 px-3">
                                <i class='bx bx-credit-card-alt me-1'></i> {{ strtoupper($order->payment_method) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small">Order Total</p>
                            <h4 class="fw-bold text-primary mb-0">₹{{ number_format($order->total, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Details --}}
            <div class="col-md-6">
                <div class="card shadow mb-4 rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-user fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">{{ $order->name }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $order->email }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Mobile:</span>
                            <span class="detail-value">{{ $order->mobile }}</span>
                        </div>
                        @if($order->user_id)
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Registered User:</span>
                            <span class="detail-value">
                                <a href="{{ route('admin.users.show', $order->user_id) }}" class="text-decoration-none text-primary">
                                    {{ $order->user->name ?? 'User ID: ' . $order->user_id }}
                                </a>
                            </span>
                        </div>
                        @endif
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">IP Address:</span>
                            <span class="detail-value">{{ $order->ip_address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="col-md-6">
                <div class="card shadow mb-4 rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-map fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <address class="mb-0 detail-value">
                            {{ $order->address }}<br>
                            {{ $order->locality ? $order->locality . ', ' : '' }}
                            {{ $order->city ? $order->city . ', ' : '' }}
                            {{ $order->state ? $order->state . ' - ' : '' }}
                            {{ $order->zipcode }}
                        </address>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="col-lg-12">
                <div class="card shadow mb-4 rounded-3">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-package fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Order Items</h5>
                    </div>
                    <div class="card-body p-0"> {{-- No padding here, handled by table cells --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover order-items-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Item Shipping</th>
                                        <th scope="col">Item Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $item->product) }}" class="text-primary fw-semibold text-decoration-none">
                                                    {{ $item->product->name ?? 'Product Not Found' }}
                                                </a>
                                                @if($item->product && $item->product->sku)
                                                    <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->product->code ?? '' }}</td>
                                            <td>{{ $item->productAttribute->size ?? '' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>₹{{ number_format($item->price, 2) }}</td>
                                            <td>₹{{ number_format($item->shipping_charge ?? 0, 2) }}</td> {{-- Assuming shipping_charge is on order_items --}}
                                            <td>₹{{ number_format(($item->quantity * $item->price) + ($item->shipping_charge ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No items found for this order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Totals Summary --}}
            <div class="col-lg-12">
                <div class="card shadow mb-4 rounded-3">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-calculator fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Order Financial Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between detail-row">
                            <span class="detail-label">Subtotal:</span>
                            <span class="detail-value">₹{{ number_format($order->total - $order->shipping_charge, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between detail-row">
                            <span class="detail-label">Shipping Charge:</span>
                            <span class="detail-value">₹{{ number_format($order->shipping_charge, 2) }}</span>
                        </div>
                        {{-- <div class="d-flex justify-content-between detail-row">
                            <span class="detail-label">Total Weight:</span>
                            <span class="detail-value">{{ number_format($order->total_weight, 2) }} kg</span>
                        </div> --}}
                        <div class="d-flex justify-content-between pt-3 fs-5 fw-bold text-primary">
                            <span>Grand Total:</span>
                            <span>₹{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Optional: Order History/Timeline (Future enhancement) --}}
            <div class="col-lg-12">
                <div class="card shadow mb-4 rounded-3">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-history fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Order History</h5>
                    </div>
                    <div class="card-body">
                        @if($order_logs->count()>0)
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Edited on</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order_logs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                    <td>{{ $log->description??'' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">No history available yet. (Implement order history/timeline here)</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
