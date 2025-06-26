<!-- Footer Start -->
<div class="container-fluid bg-secondary text-dark mt-5 pt-5">
    <div class="row px-xl-5 pt-5">
        <!-- Logo & Contact Info -->
        <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
            {{-- <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-3">
                <img src="{{ url(\Storage::url(settings()->logo??'')) }}" alt="On Jewel Logo" width="150px">
            </a> --}}
            <p>
                <strong>How to make an order?</strong><br>
                Send me a picture of whatever you want via WhatsApp / Facebook / Instagram<br>
                @if(settings()->mobile)
                <strong>Contact:</strong> {{ settings()->mobile??'' }}<br>
                @endif
                @if(settings()->alt_mobile)
                <strong>Contact:</strong> {{ settings()->alt_mobile??'' }}<br>
                @endif
                Free Delivery all over India<br>
                Payment through Paytm / Bank Transfer<br>
                Send address after confirming the order
            </p>
            @if(settings()->email)
            <p class="mb-2"><i class="fa fa-envelope text-primary mr-2"></i>{{ settings()->email }}</p>
            @endif
            @if(settings()->alt_email)
            <p class="mb-2"><i class="fa fa-envelope text-primary mr-2"></i>{{ settings()->alt_email }}</p>
            @endif
        </div>

        <!-- Links & Info -->
        <div class="col-lg-8 col-md-12">
            <div class="row">
                <!-- Quick Links -->
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Quick Links</h5>
                    <div class="d-flex flex-column">
                        <a class="text-dark mb-2" href="{{ url('/') }}"><i class="fa fa-angle-right mr-2"></i>Home</a>
                        <a class="text-dark mb-2" href="{{ route('about') }}"><i class="fa fa-angle-right mr-2"></i>Company Profile</a>
                        <a class="text-dark mb-2" href="{{ route('videos') }}"><i class="fa fa-angle-right mr-2"></i>Videos</a>
                        <a class="text-dark mb-2" href="{{ route('contact') }}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                        <a class="text-dark mb-2" href="{{ route('shipping_policy') }}"><i class="fa fa-angle-right mr-2"></i>Shipping Policy</a>
                    </div>
                </div>

                <!-- Product Categories -->
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Products</h5>
                    <div class="d-flex flex-column">
                        @if(ucategories()->count() > 0)
                            @foreach(ucategories() as $category)
                                <a class="text-dark mb-2" href="{{ route('listing', $category) }}">
                                    <i class="fa fa-angle-right mr-2"></i>{{ $category->name??'' }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if(settings()->short_about_description)
                <!-- Company Description -->
                <div class="col-md-4 mb-5">
                    <h5 class="font-weight-bold text-dark mb-4">Profile</h5>
                    <p>{{ settings()->short_about_description }}</p>
                    <div class="d-inline-flex align-items-center">
                        @if(settings()->facebook)
                        <a class="text-dark px-2" target="_blank" href="{{ settings()->facebook }}">
                           <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif
                        @if(settings()->twitter)
                        <a class="text-dark px-2" href="{{ settings()->twitter }}">
                           <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if(settings()->linkedin)
                        <a class="text-dark px-2" href="{{ settings()->linkedin }}">
                           <i class="fab fa-linkedin-in"></i>
                        </a>
                        @endif
                        @if(settings()->instagram)
                        <a class="text-dark px-2" target="_blank" href="{{ settings()->instagram }}">
                           <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if(settings()->youtube)
                        <a class="text-dark pl-2" target="_blank" href="{{ settings()->youtube }}">
                           <i class="fab fa-youtube"></i>
                        </a>
                        @endif
                     </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="row border-top border-light mx-xl-5 py-4">
        <div class="col-md-6 text-center text-md-left px-xl-0">
            <p class="mb-0 text-dark">
                <a class="text-dark font-weight-semi-bold" href="{{ url('/') }}"> {{ settings()->footer_text??'' }}
                <a class="text-dark font-weight-semi-bold" href="https://cypwebtech.com" target="_blank">www.cypwebtech.com</a>
            </p>
        </div>
        <div class="col-md-6 text-center text-md-right px-xl-0">
            <img class="img-fluid" src="{{ url('users/images/payments.png') }}" width="100" alt="Payment Methods">
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

<!-- WhatsApp Floating Button -->
<a href="https://api.whatsapp.com/send?phone=+918510047947&text=Hello" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
</a>

<!-- Styles -->
<style>
    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        left: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50%;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
    }

    .my-float {
        margin-top: 16px;
    }
</style>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('users/lib/easing/easing.min.js') }}"></script>
<script src="{{ url('users/lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ url('users/mail/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ url('users/mail/contact.js') }}"></script>
<script src="{{ url('users/js/main.js') }}"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
