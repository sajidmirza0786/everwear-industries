@extends('users.master')

@section('seo')
    <title>{{ $product->name }} | On Jewel</title>
    <meta name="keywords" content="{{ $product->keyword??'' }}">
    <meta name="description" content="{{ $product->description??'' }}">
@endsection

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary text-white mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">
            <h1 class="display-4 font-weight-bold text-uppercase mb-3 d-none d-md-block">{{ $product->name }}</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
                <p class="m-0 px-2 text-dark"> / </p>
                <p class="m-0 text-dark">{{ $product->name }}</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Shop Detail Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">
            <!-- Product Images -->
            <div class="col-lg-6 pb-5">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner border rounded-lg shadow-sm">
                        <div class="carousel-item active">
                            <img class="w-100 h-100" src="{{ url(\Storage::url($product->image??'')) }}" alt="{{ $product->name }}" width="100%">
                        </div>
                        @if($product->images->count() > 0)
                            @foreach($product->images as $image)
                                <div class="carousel-item">
                                    <img class="w-100 h-100" src="{{ url(\Storage::url($image->image_path??'')) }}" alt="{{ $product->name }}" width="100%">
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <i class="fas fa-angle-left fa-2x text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <i class="fas fa-angle-right fa-2x text-dark"></i>
                    </a>
                </div>
                <!-- Thumbnail Previews -->
                @if($product->images->count() > 0 || $product->image)
                    <div class="d-flex mt-3 justify-content-center flex-wrap">
                        <img class="img-thumbnail mx-1 mb-2" src="{{ url(\Storage::url($product->image??'')) }}" alt="Thumbnail {{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid lightgrey;" data-target="#product-carousel" data-slide-to="0">
                        @foreach($product->images as $index => $image)
                            <img class="img-thumbnail mx-1 mb-2" src="{{ url(\Storage::url($image->image_path??'')) }}" alt="Thumbnail {{ $product->name }}" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid lightgrey;" data-target="#product-carousel" data-slide-to="{{ $index + 1 }}">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="col-lg-6 pb-5">
                <h3 class="font-weight-bold mb-3">{{ $product->name }}</h3>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-primary mr-2">
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star"></small>
                        <small class="fas fa-star-half-alt"></small>
                        <small class="far fa-star"></small>
                    </div>
                    <small class="text-muted">(0 Reviews)</small>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <h3 class="font-weight-bold text-primary mr-3">₹{{ $product->selling }}</h3>
                    @if($product->mrp > $product->selling)
                        <small class="text-muted"><del>₹{{ $product->mrp }}</del></small>
                        <span class="badge badge-success ml-2">
                            {{ round((($product->mrp - $product->selling) / $product->mrp) * 100) }}% OFF
                        </span>
                    @endif
                </div>
                <div class="mb-4">
                    <p class="mb-2"><strong>Product Code:</strong> {{ $product->code }}</p>
                    @if($product->size)
                        <p class="mb-2"><strong>Size:</strong> {{ $product->size }}</p>
                    @endif
                    @if($product->color)
                        <p class="mb-2"><strong>Color:</strong> {{ $product->color }}</p>
                    @endif
                    @if($product->gram_weight)
                        <p class="mb-2"><strong>Weight:</strong> {{ $product->gram_weight }}g</p>
                    @endif
                    <p class="mb-2"><strong>Availability:</strong> 
                        <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock' }}
                        </span>
                    </p>
                    @if($product->short_description)
                    {{-- Short Description / Availability --}}
                    <p class="mb-4 text-muted">
                        {{ $product->short_description }}
                    </p>
                    @endif
                </div>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-4 d-flex align-items-center">
                        <label for="quantity" class="font-weight-semibold mr-3">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" class="form-control rounded" value="1" min="1" max="{{ $product->stock }}" style="width: 100px;" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                        <button type="submit" class="btn btn-primary btn-block btn-lg mb-2 mb-sm-0 mr-sm-3 rounded {{ $product->stock <= 0 ? 'disabled' : '' }}">
                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                        </button>
                        <a href="https://api.whatsapp.com/send?phone=+918510047947&text={{ urlencode('Interested in ' . $product->name . ' (Code: ' . $product->code . ') - ' . url()->full()) }}" class="btn btn-success btn-block btn-lg rounded">
                            <i class="fab fa-whatsapp mr-2"></i>Order on WhatsApp
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Description and Reviews -->
        <div class="row px-xl-5">
            <div class="col">
                <div class="nav nav-tabs justify-content-center border-0 mb-4">
                    <a class="nav-item nav-link active font-weight-semibold text-primary" data-toggle="tab" href="#tab-pane-1">Description</a>
                    <a class="nav-item nav-link font-weight-semibold text-primary" data-toggle="tab" href="#tab-pane-2">Write a Review</a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-pane-1">
                        <h4 class="font-weight-bold mb-3">Product Description</h4>
                        <div class="text-muted">{!! $product->long_description??'No description available.' !!}</div>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-2">
                        <h4 class="font-weight-bold mb-4">Write a Review</h4>
                        <form action="" method="POST" class="bg-light p-4 rounded shadow-sm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="form-group mb-4">
                                <label for="rating" class="font-weight-semibold mb-2">Your Rating</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" class="fas fa-star"></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" class="fas fa-star"></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" class="fas fa-star"></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" class="fas fa-star"></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" class="fas fa-star"></label>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="review_title" class="font-weight-semibold mb-2">Review Title</label>
                                <input type="text" class="form-control rounded" id="review_title" name="review_title" placeholder="Enter a title for your review" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="review_text" class="font-weight-semibold mb-2">Your Review</label>
                                <textarea class="form-control rounded" id="review_text" name="review_text" rows="5" placeholder="Share your experience with this product" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary px-5 rounded">
                                <i class="fas fa-star mr-2"></i>Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Products -->
        <div class="text-center mt-5 mb-5">
            <h2 class="section-title px-4">
                <span class="px-3 border-bottom">Explore Similar Products</span>
            </h2>
        </div>
        <div class="row px-xl-5">
            <div class="col-lg-12 col-md-12">
                <div class="row pb-3">
                    @if($similarProducts->count() > 0)
                        @foreach($similarProducts as $product)
                            @include('users.components.product_3')
                        @endforeach
                    @else
                        <div class="col-12 text-center">
                            <p class="text-muted">No similar products available at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Detail End -->

    <style>
        .img-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            transition: border-color 0.3s ease, opacity 0.3s ease;
        }
        .img-thumbnail:hover {
            border-color: #007bff;
            opacity: 0.9;
        }
        .img-thumbnail.active {
            border-color: #007bff;
        }
        .btn-primary:hover, .btn-success:hover {
            transform: translateY(-2px);
        }
        .btn-primary.disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            width: 60px;
            height: 3px;
            background: #007bff;
            transform: translateX(-50%);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "-";
            color: #fff;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .badge-success {
            font-size: 0.9rem;
            padding: 0.4em 0.8em;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            color: #ccc;
            cursor: pointer;
            font-size: 1.5rem;
            padding: 0 5px;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f8d64e;
        }
        @media (max-width: 767px) {
            .carousel-inner {
                max-height: 300px;
            }
            .img-thumbnail {
                width: 60px;
                height: 60px;
            }
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            .col-lg-6.pb-5 {
                margin-bottom: 2rem;
            }
            .d-flex.flex-sm-row {
                flex-direction: column !important;
            }
            .d-flex.flex-sm-row .btn {
                margin-bottom: 1rem;
            }
        }
    </style>
    <script>
        // Thumbnail click to slide and highlight active thumbnail
        document.querySelectorAll('.img-thumbnail').forEach((thumb, index) => {
            thumb.addEventListener('click', function() {
                $('#product-carousel').carousel(index);
                document.querySelectorAll('.img-thumbnail').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Highlight first thumbnail by default
        document.querySelector('.img-thumbnail')?.classList.add('active');

        // Update active thumbnail on carousel slide
        $('#product-carousel').on('slide.bs.carousel', function (e) {
            document.querySelectorAll('.img-thumbnail').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.img-thumbnail')[e.to]?.classList.add('active');
        });
    </script>
@endsection