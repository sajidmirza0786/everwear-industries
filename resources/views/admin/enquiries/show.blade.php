@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Enquiry Details #{{ $enquiry->id }} | Admin Panel</title>
    <meta name="description" content="Detailed view of enquiry #{{ $enquiry->id }} from {{ $enquiry->name }} on the On Jewel admin panel.">
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
    </style>


@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.enquiries.index') }}">Enquiries</a></li>
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    Details
</li>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800"><i class='bx bx-message-square-dots me-2'></i> Enquiry Details <span class="text-primary">#{{ $enquiry->id }}</span></h2>
            <div>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                    <i class='bx bx-arrow-back me-1'></i> Back to Enquiries
                </a>
                <form action="{{ route('admin.enquiries.destroy', $enquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this enquiry? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class='bx bx-trash me-1'></i> Delete Enquiry
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            {{-- Contact Information --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4 rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-user fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">{{ $enquiry->name ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $enquiry->email ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Mobile:</span>
                            <span class="detail-value">{{ $enquiry->mobile ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Received At:</span>
                            <span class="detail-value">{{ $enquiry->created_at->format('M d, Y H:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enquiry Details --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4 rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-info-circle fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Enquiry Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Subject:</span>
                            <span class="detail-value">{{ $enquiry->subject ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Reason:</span>
                            <span class="detail-value">{{ $enquiry->reason ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Page URL:</span>
                            <span class="detail-value">
                                @if($enquiry->page_url)
                                    <a href="{{ $enquiry->page_url }}" target="_blank" class="text-primary text-decoration-none">
                                        {{ Str::limit($enquiry->page_url, 40, '...') }} <i class='bx bx-link-external small'></i>
                                    </a>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label d-block mb-1">Message:</span>
                            <p class="detail-value text-break">{{ $enquiry->message ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technical Information --}}
            <div class="col-lg-12">
                <div class="card shadow mb-4 rounded-3">
                    <div class="card-header bg-light d-flex align-items-center">
                        <i class='bx bx-desktop fs-4 me-2 text-primary'></i>
                        <h5 class="mb-0 fw-bold">Technical Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">IP Address:</span>
                            <span class="detail-value">{{ $enquiry->ip_address ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between">
                            <span class="detail-label">Last Updated:</span>
                            <span class="detail-value">{{ $enquiry->updated_at->format('M d, Y H:i A') }}</span>
                        </div>
                        {{-- Add other technical details like User Agent if you store them --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
