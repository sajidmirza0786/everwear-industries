@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Manage Enquiries | Admin Panel</title>
    <meta name="description" content="View and manage all customer enquiries on the On Jewel admin panel.">
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    Enquiries
</li>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Enquiry Management</h2>
        </div>

        {{-- Search and Filter Section --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-body">
                <form action="{{ route('admin.enquiries.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label mb-1">Search</label>
                        <input type="text" class="form-control rounded" id="search" name="search" placeholder="Search by Name, Email, Mobile, Subject..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="reason_filter" class="form-label mb-1">Reason</label>
                        <input type="text" class="form-control rounded" id="reason_filter" name="reason" placeholder="Filter by reason" value="{{ request('reason') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary rounded w-100"><i class='bx bx-filter-alt me-1'></i> Filter</button>
                    </div>
                    @if(request('search') || request('reason'))
                        <div class="col-md-2">
                            <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary rounded w-100"><i class='bx bx-refresh me-1'></i> Reset</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Enquiries Table --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Enquiries</h6>
                @if($enquiries->total() > 0)
                    <span class="small text-muted">Showing {{ $enquiries->firstItem() }} to {{ $enquiries->lastItem() }} of {{ $enquiries->total() }} entries</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered small table-sm table-responsive-stack" id="enquiriesTable">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Contact Info</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Page URL</th>
                                <th scope="col">Received At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enquiries as $enquiry)
                                <tr>
                                    <td data-label="#" class="text-center">{{ $loop->iteration + ($enquiries->currentPage() - 1) * $enquiries->perPage() }}</td>
                                    <td data-label="Name">{{ $enquiry->name ?? 'N/A' }}</td>
                                    <td data-label="Contact Info">
                                        {{ $enquiry->email ?? 'N/A' }}<br>
                                        <span class="text-muted small">{{ $enquiry->mobile ?? 'N/A' }}</span>
                                    </td>
                                    <td data-label="Subject">{{ $enquiry->subject ?? 'N/A' }}</td>
                                    <td data-label="Reason">{{ $enquiry->reason ?? 'N/A' }}</td>
                                    <td data-label="Page URL">
                                        @if($enquiry->page_url)
                                            <a href="{{ $enquiry->page_url }}" target="_blank" class="text-primary text-decoration-none">
                                                {{ Str::limit($enquiry->page_url, 30, '...') }} <i class='bx bx-link-external small'></i>
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Received At">{{ $enquiry->created_at->format('M d, Y H:i') }}</td>
                                    <td data-label="Actions" class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.enquiries.show', $enquiry->id) }}" class="btn btn-sm btn-info text-white rounded" title="View Details">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            <form action="{{ route('admin.enquiries.destroy', $enquiry->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this enquiry? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded" title="Delete Enquiry">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No enquiries found matching your criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $enquiries->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
