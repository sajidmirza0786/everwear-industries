@php
    use Illuminate\Support\Facades\Auth;
    $settings = settings();
@endphp

<!-- Navbar Container -->
<div class="navbar-container sticky-navbar">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container-fluid px-xl-5">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="navbar-brand">
                <img src="{{ url(\Storage::url($settings->logo??'')) }}" alt="On Jewel Logo" height="40">
            </a>

            <!-- Mobile Icons (Cart, User, Search Toggle) -->
            <div class="d-flex align-items-center d-lg-none">
                @guest
                <a href="{{ route('login') }}" class="nav-link text-muted mx-2 border"><i class="fa fa-sign-in"></i> Login</a>
                @else
                <a href="#" class="nav-link text-muted mx-2"><i class="fa fa-user"></i> {{ substr(auth()->user()->name, 0,10) }}</a>
                @endguest
                <a href="{{ route('cart.view') }}" class="nav-link text-muted mx-2 position-relative">
                    <i class="fa fa-shopping-cart"></i>
                     @if(cartItemCount() > 0)
                    <span class="cart-badge">{{ cartItemCount() }}</span>
                    @endif
                </a>
                <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Navbar Collapse -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link">About Us</a></li>
                    <li class="nav-item"><a href="{{ route('videos') }}" class="nav-link">Videos</a></li>
                    <li class="nav-item"><a href="{{ route('shipping_policy') }}" class="nav-link">Shipping Policy</a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Our Products</a>
                        <div class="dropdown-menu border-0 shadow-sm">
                            @if(ucategories()->count() > 0)
                                @foreach(ucategories() as $category)
                                    <a href="{{ route('listing', $category) }}" class="dropdown-item">{{ $category->name }}</a>
                                @endforeach
                            @endif
                        </div>
                    </li>
                    <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link">Contact</a></li>
                </ul>

                <!-- Search Form (Hidden on Mobile, Toggled) -->
                <form action="{{ route('listing') }}" method="GET" class="form-inline mx-auto my-2 my-lg-0 search-form d-none d-lg-flex">
                    <div class="input-group">
                        <input name="search" class="form-control border-right-0" type="text" placeholder="Search products..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <!-- Auth Links and Icons (Desktop) -->
                <ul class="navbar-nav align-items-center d-none d-lg-flex">
                    @guest
                        <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">
                            <i class="fa fa-sign-in"></i> Login</a></li>
                        <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">
                            <i class="fa fa-user-plus"></i> Register</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                            <div class="dropdown-menu dropdown-menu-right border-0 shadow-sm">
                                <a class="dropdown-item" href="">Profile</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </div>
                        </li>
                    @endguest
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link text-muted"><i class="fa fa-user"></i></a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a href="#" class="nav-link text-muted"><i class="fa fa-heart"></i></a>
                    </li> --}}
                    <li class="nav-item">
                        <a href="{{ route('cart.view') }}" class="nav-link text-muted position-relative">
                            <i class="fa fa-shopping-cart"></i>
                            @if(cartItemCount() > 0)
                            <span class="cart-badge">{{ cartItemCount() }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>


<style>
/* Navbar Styles */
.navbar-container {
    position: sticky;
    top: 0;
    z-index: 1030;
}

.navbar-light.bg-white {
    background-color: #ffffff !important;
    border-bottom: 1px solid #e9ecef;
}

.navbar-brand img {
    transition: transform 0.2s;
}

.navbar-brand img:hover {
    transform: scale(1.05);
}

.nav-link {
    color: #333 !important;
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    transition: color 0.2s;
}

.dropdown-menu {
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}

.dropdown-item {
    color: #333;
    padding: 0.5rem 1.5rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.form-control {
    border: 1px solid #ced4da;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    height:40px;
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #dc3545;
    color: #fff;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.navbar-toggler {
    border: none;
    padding: 0.5rem;
}

.navbar-toggler:focus {
    outline: none;
}

/* Search Form Styles */
.search-form {
    width: 300px;
}

.search-form-mobile {
    padding: 0.5rem 1rem;
}

.search-toggle {
    font-size: 1.2rem;
}

/* Responsive Styles */
@media (min-width: 992px) {
    /* Keep dropdown open on hover */
    .navbar-nav .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0; /* Optional: align perfectly under toggle */
    }

    /* Smooth transition (optional) */
    .dropdown-menu {
        transition: all 0.3s ease;
        visibility: hidden;
        opacity: 0;
        display: block;
    }

    .navbar-nav .dropdown:hover .dropdown-menu {
        visibility: visible;
        opacity: 1;
    }
}

</style>
