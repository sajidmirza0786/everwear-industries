@extends('users.master')

@section('seo')
    @if(request()->has('search'))
        <title>Search results for "{{ request('search') }}" | On Jewel</title>
    @else
        <title>{{ $category->name }} | On Jewel</title>
        <meta name="keywords" content="{{ $category->keywords??'' }}">
        <meta name="description" content="{{ $category->description??'' }}">
    @endif
@endsection

@section('content')

<!-- Page Header -->
<div class="container-fluid bg-secondary text-white mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
        <h1 class="display-5 text-uppercase font-weight-bold">
            {{ request('search') ?? $category->name }}
        </h1>
        <nav>
            <ol class="breadcrumb justify-content-center bg-transparent">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-dark">Home</a></li>
                <li class="breadcrumb-item text-dark active">
                    {{ request('search') ?? $category->name }}
                </li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Shop Section -->
<div class="container-fluid pb-5">
    <div class="row px-xl-5">
        
        <!-- Sidebar -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="mb-5">
                <h5 class="font-weight-bold mb-4">Categories</h5>
                <ul class="list-group list-group-flush">
                    @foreach(ucategories() as $cat)
                        <li class="list-group-item px-0 border-0">
                            <a href="{{ route('listing', $cat) }}" class="text-dark">
                                <i class="fas fa-angle-right mr-2 text-primary"></i>{{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Products -->
        <div class="col-lg-9">
            <div class="row">
                @forelse($products as $product)
                    @include('users.components.products')
                @empty
                    <div class="col-12 text-center">
                        <h5 class="text-muted">No products found.</h5>
                    </div>
                @endforelse
            </div>

            {{-- Pagination (if available) --}}
            {{-- <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div> --}}
        </div>
        <!-- Products End -->
    </div>
</div>
<!-- Shop Section End -->

@endsection
