@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Dashboard | Admin Panel</title>
    <meta name="description" content="E-commerce admin dashboard for On Jewel. Overview of sales, orders, and customer data.">
@endsection

@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    Overview
</li>
@endsection

@section('content')
    <style>
        .metric-card .card-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .metric-card .icon-box {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary);
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
        }
        .metric-card.bg-success-light .icon-box {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: var(--bs-success);
        }
        .metric-card.bg-warning-light .icon-box {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
            color: var(--bs-warning);
        }
        .metric-card.bg-danger-light .icon-box {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: var(--bs-danger);
        }
        .metric-card.bg-info-light .icon-box {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            color: var(--bs-info);
        }
        .metric-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .metric-card .label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .table-custom-small th, .table-custom-small td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        .table-custom-small th {
            white-space: nowrap; /* Prevent header wrapping */
        }
        .status-badge {
            padding: .3em .5em;
            border-radius: .25rem;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
        }
        .status-pending { background-color: #ffc107; color: #343a40; } /* Yellow */
        .status-completed { background-color: #28a745; color: #fff; } /* Green */
        .status-cancelled { background-color: #dc3545; color: #fff; } /* Red */
        .status-in-transit { background-color: #17a2b8; color: #fff; } /* Info/Cyan */
        .payment-method-tag {
            padding: .2em .4em;
            border-radius: .25rem;
            font-size: 0.7em;
            background-color: #6c757d; /* Gray */
            color: #fff;
            text-transform: uppercase;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <h3 class="h4 mb-4 text-gray-800">Dashboard Overview</h3>
            </div>
        </div>

        {{-- Top Metric Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card shadow rounded-3 metric-card">
                    <div class="card-body">
                        <div>
                            <p class="label mb-0">Total Orders</p>
                            <h5 class="value text-primary mb-0">
                                {{ number_format($totalOrders) }}
                            </h5>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-cart-alt'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow rounded-3 metric-card bg-success-light">
                    <div class="card-body">
                        <div>
                            <p class="label mb-0">Total Revenue</p>
                            <h5 class="value text-success mb-0">
                                ₹{{ number_format($totalRevenue) }}
                            </h5>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-rupee'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow rounded-3 metric-card bg-warning-light">
                    <div class="card-body">
                        <div>
                            <p class="label mb-0">Customers (30 Days)</p>
                            <h5 class="value text-warning mb-0">
                                {{ number_format($newCustomers) }}
                            </h5>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-user-plus'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow rounded-3 metric-card bg-danger-light">
                    <div class="card-body">
                        <div>
                            <p class="label mb-0">Pending Orders</p>
                            <h5 class="value text-danger mb-0">
                                {{ number_format($pendingOrders) }}
                            </h5>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-time-five'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts / Visualizations Section --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card shadow rounded-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-primary">Sales Over Time (Last 7 Days)</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="salesChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow rounded-3 h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold text-primary">Order Status Distribution</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="statusChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow rounded-3 h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">Recent Orders</h6>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered small table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none text-primary fw-semibold">#{{ $order->id }}</a></td>
                                        <td>{{ $order->name }}</td>
                                        <td>₹{{ number_format($order->total, 2) }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No recent orders.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow rounded-3 h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">Recent Enquiries</h6>
                        <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered small table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Subject</th>
                                        <th>Received At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentEnquiries as $enquiry)
                                    <tr>
                                        <td>{{ $enquiry->name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.enquiries.show', $enquiry->id) }}">
                                                {{ $enquiry->mobile ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($enquiry->subject, 30, '...') ?? 'N/A' }}</td>
                                        <td>{{ $enquiry->created_at->diffForHumans() }}</td> {{-- Showing relative time --}}
                                        {{-- <td class="text-center">
                                            <a href="{{ route('admin.enquiries.show', $enquiry->id) }}" class="btn btn-sm btn-info text-white rounded" title="View Enquiry"><i class='bx bx-show'></i></a>
                                        </td> --}}
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No recent enquiries.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from Laravel controller
            const salesLabels = @json($salesLabels);
            const salesValues = @json($salesValues);
            const statusLabels = @json($statusLabels);
            const statusData = @json($statusData);
            const statusBackgroundColors = @json($statusBackgroundColors);

            // --- Sales Over Time Chart (Line Chart) ---
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Sales Revenue (₹)',
                        data: salesValues,
                        borderColor: 'rgb(75, 192, 192)', // Primary color for line
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Light fill below line
                        borderWidth: 2,
                        tension: 0.3, // Smooth line
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allow custom height for canvas
                    plugins: {
                        legend: {
                            display: false // Hide dataset label
                        },
                        title: {
                            display: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            grid: {
                                display: false // Hide vertical grid lines
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Revenue (₹)'
                            },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, ticks) {
                                    return '₹' + value.toLocaleString(); // Format Y-axis labels as currency
                                }
                            }
                        }
                    }
                }
            });

            // --- Order Status Distribution Chart (Doughnut Chart) ---
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)), // Capitalize labels
                    datasets: [{
                        label: 'Order Status',
                        data: statusData,
                        backgroundColor: statusBackgroundColors, // Colors from controller
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Allow custom height for canvas
                    plugins: {
                        legend: {
                            position: 'right', // Place legend on the right for better space usage
                            labels: {
                                boxWidth: 15, // Smaller legend boxes
                                padding: 10 // Padding between legend items
                            }
                        },
                        title: {
                            display: false,
                        }
                    }
                }
            });
        });
    </script>
@endsection
