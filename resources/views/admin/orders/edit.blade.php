@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Edit Order #{{ $order->uuid }} | Admin Panel</title>
    <meta name="description" content="Edit details for order {{ $order->uuid }} on the On Jewel admin panel.">
@endsection

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

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    Edit
</li>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <h6 class="font-weight-bolder mb-0">
                    Edit Order: #{{ $order->uuid }}
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
                    <div class="card-header pb-0 text-left bg-transparent">
                        <h5 class="font-weight-bolder text-info text-gradient">
                            Update Order Details
                        </h5>
                        <p class="mb-3">
                            Modify the information for this order.
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- Order Status & Payment Method --}}
                                <div class="col-lg-12">
                                    <div class="card shadow mb-4 rounded-3">
                                        <div class="card-header bg-light d-flex align-items-center">
                                            <i class='bx bx-cog fs-4 me-2 text-primary'></i>
                                            <h5 class="mb-0 fw-bold">Order Status & Payment</h5>
                                        </div>
                                        <div class="card-body row g-3">
                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Order Status <span class="text-danger">*</span></label>
                                                <select class="form-select rounded @error('status') is-invalid @enderror" id="status" name="status" required>
                                                    <option value="in-transit" {{ (old('status', $order->status) == 'in-transit') ? 'selected' : '' }}>In-transit</option>
                                                    <option value="completed" {{ (old('status', $order->status) == 'completed') ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ (old('status', $order->status) == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                                <select class="form-select rounded @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                                    <option value="prepaid" {{ (old('payment_method', $order->payment_method) == 'prepaid') ? 'selected' : '' }}>Prepaid</option>
                                                    <option value="cod" {{ (old('payment_method', $order->payment_method) == 'cod') ? 'selected' : '' }}>Cash on Delivery</option>
                                                </select>
                                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Customer & Shipping Information --}}
                                <div class="col-lg-12">
                                    <div class="card shadow mb-4 rounded-3">
                                        <div class="card-header bg-light d-flex align-items-center">
                                            <i class='bx bx-user-pin fs-4 me-2 text-primary'></i>
                                            <h5 class="mb-0 fw-bold">Customer & Shipping Information</h5>
                                        </div>
                                        <div class="card-body row g-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                                <input type="text" id="name" name="name" class="form-control rounded @error('name') is-invalid @enderror" required value="{{ old('name', $order->name) }}">
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Customer Email <span class="text-danger">*</span></label>
                                                <input type="email" id="email" name="email" class="form-control rounded @error('email') is-invalid @enderror" required value="{{ old('email', $order->email) }}" disabled>
                                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                                <input type="tel" id="mobile" name="mobile" class="form-control rounded @error('mobile') is-invalid @enderror" required value="{{ old('mobile', $order->mobile) }}">
                                                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="zipcode" class="form-label">Zip Code <span class="text-danger">*</span></label>
                                                <input type="text" id="zipcode" name="zipcode" class="form-control rounded @error('zipcode') is-invalid @enderror" required value="{{ old('zipcode', $order->zipcode) }}">
                                                @error('zipcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                                <input type="text" id="state" name="state" class="form-control rounded @error('state') is-invalid @enderror" required value="{{ old('state', $order->state) }}">
                                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                                <input type="text" id="city" name="city" class="form-control rounded @error('city') is-invalid @enderror" required value="{{ old('city', $order->city) }}">
                                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="locality" class="form-label">Locality / Area <span class="text-danger">*</span></label>
                                                <input type="text" id="locality" name="locality" class="form-control rounded @error('locality') is-invalid @enderror" required value="{{ old('locality', $order->locality) }}">
                                                @error('locality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="address" class="form-label">Full Shipping Address <span class="text-danger">*</span></label>
                                                <textarea id="address" name="address" rows="3" class="form-control rounded @error('address') is-invalid @enderror" required>{{ old('address', $order->address) }}</textarea>
                                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Order Items (Read-only for Edit, or implement complex item editing) --}}
                                <div class="col-lg-12">
                                    <div class="card shadow mb-4 rounded-3">
                                        <div class="card-header bg-light d-flex align-items-center">
                                            <i class='bx bx-package fs-4 me-2 text-primary'></i>
                                            <h5 class="mb-0 fw-bold">Order Items (View Only)</h5>
                                            {{-- Consider adding an "Add/Remove Item" button here if item editing is required --}}
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover order-items-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Product</th>
                                                            <th scope="col">Qty</th>
                                                            <th scope="col">Unit Price</th>
                                                            {{-- <th scope="col">Item Shipping</th> --}}
                                                            <th scope="col">Item Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($order->items as $item)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.products.edit', $item->product_id) }}" class="text-primary fw-semibold text-decoration-none">
                                                                        {{ $item->product->name ?? 'Product Not Found' }}
                                                                    </a>
                                                                    @if($item->product && $item->product->sku)
                                                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                                {{-- <td>₹{{ number_format($item->shipping_charge ?? 0, 2) }}</td> --}}
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

                                {{-- Save Changes Button --}}
                                <div class="col-12">
                                    <div class="d-flex justify-content-end mt-4"> {{-- Adjusted margin-top --}}
                                        <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 me-2"> {{-- Added me-2 for spacing with cancel --}}
                                            <i class='bx bx-sync me-2'></i> Update Order {{-- Changed icon and text --}}
                                        </button>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-lg rounded-pill px-5">Cancel</a> {{-- Added cancel button --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
