@extends('admin.master') {{-- Assuming you have an admin master layout --}}

@section('seo')
    <title>Manage Videos | Admin Panel</title>
    <meta name="description" content="View and manage all video content on the On Jewel admin panel.">
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item active fw-semibold" aria-current="page">
    Videos
</li>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Video Management</h2>
            <a href="{{ route('admin.videos.create') }}" class="btn btn-primary rounded-pill">
                <i class='bx bx-plus-circle me-1'></i> Add New Video
            </a>
        </div>

        {{-- Search Section --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-body">
                <form action="{{ route('admin.videos.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="search" class="form-label mb-1">Search</label>
                        <input type="text" class="form-control rounded" id="search" name="search" placeholder="Search by title, description, URL..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary rounded w-100"><i class='bx bx-search me-1'></i> Search</button>
                    </div>
                    @if(request('search'))
                        <div class="col-md-2">
                            <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary rounded w-100"><i class='bx bx-refresh me-1'></i> Reset</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Videos Table --}}
        <div class="card shadow mb-4 rounded-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Videos</h6>
                @if($videos->total() > 0)
                    <span class="small text-muted">Showing {{ $videos->firstItem() }} to {{ $videos->lastItem() }} of {{ $videos->total() }} entries</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-responsive-stack small" id="videosTable">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">URL</th>
                                <th scope="col">Description</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($videos as $video)
                                <tr>
                                    <td data-label="#" class="text-center">{{ $loop->iteration + ($videos->currentPage() - 1) * $videos->perPage() }}</td>
                                    <td data-label="Title">{{ $video->title ?? 'N/A' }}</td>
                                    <td data-label="URL">
                                        <a href="{{ $video->url }}" target="_blank" class="text-primary text-decoration-none">
                                            {{ Str::limit($video->url, 40, '...') }} <i class='bx bx-link-external small'></i>
                                        </a>
                                    </td>
                                    <td data-label="Description">
                                        {{ Str::limit($video->description, 50, '...') ?? 'N/A' }}
                                    </td>
                                    <td data-label="Created At">{{ $video->created_at->format('M d, Y H:i') }}</td>
                                    <td data-label="Actions" class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-warning rounded" title="Edit Video">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <form action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded" title="Delete Video">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No videos found. Click "Add New Video" to get started!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $videos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
