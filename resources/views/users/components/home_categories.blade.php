<div class="container-fluid py-5 bg-light">
    <div class="row px-xl-5">
        <div class="col-12 text-center mb-5">
            <h2 class="section-title position-relative d-inline-block mb-3">
                <span class="bg-light px-4 py-2 rounded shadow-sm">Shop by Category</span>
            </h2>
            <p class="text-muted mb-0">Browse our curated selection of categories and discover products tailored to your needs.</p>
        </div>

        @if(ucategories()->count() > 0)
            @foreach(ucategories() as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="cat-item d-flex flex-column border rounded bg-white shadow-sm h-100 p-3 text-center position-relative transition">
                    <div class="position-relative overflow-hidden mb-3 rounded cat-img">
                        <a href="{{ route('listing', $category) }}">
                            <img class="img-fluid rounded" src="{{ url(\Storage::url($category->image??'')) }}" alt="{{ $category->name ?? '' }}">
                        </a>
                    </div>
                    <h5 class="font-weight-semibold text-dark mb-2">{{ $category->name }}</h5>
                    <p class="text-muted small mb-3">{{ $category->products->where('status','enable')->count() }} Products</p>
                    <div class="mt-auto">
                        <a href="{{ route('listing', $category) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-arrow-right mr-1"></i> Explore
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <p class="lead text-muted mb-4">No categories available at the moment.</p>
                <a href="{{ url('/') }}" class="btn btn-primary px-4">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.cat-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.cat-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}
.cat-img img {
    transition: transform 0.3s ease;
}
.cat-img img:hover {
    transform: scale(1.05);
}
.section-title span {
    font-size: 1.5rem;
    font-weight: 600;
}
</style>
