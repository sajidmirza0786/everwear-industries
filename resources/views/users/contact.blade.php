@extends('users.master')

@section('seo')
    <title>Contact Us | On Jewel</title>
    <meta name="description" content="Get in touch with On Jewel for any queries about our fine artificial jewelry collection. Reach us via email, phone, or our contact form.">
@endsection

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary text-white mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px">
            <h1 class="display-4 font-weight-bold text-uppercase mb-3">Contact Us</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}" class="text-dark">Home</a></p>
                <p class="m-0 px-2 text-dark">-</p>
                <p class="m-0 text-dark">Contact Us</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Contact Start -->
    <div class="container-fluid py-5">
        <div class="text-center mb-5">
            <h2 class="section-title px-4">
                <span class="px-3 border-bottom border-primary">Get In Touch</span>
            </h2>
            <p class="text-muted w-75 mx-auto">Have questions about our jewelry or need help with your order? We're here to assist you every step of the way.</p>
        </div>

        <div class="row px-xl-5 justify-content-center">
            <!-- Contact Form --><div class="col-lg-6 mb-5">
            <div class="bg-white p-4 rounded-lg shadow-sm">

                {{-- Session error message --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">×</button>
                    </div>
                @endif

                {{-- Session success message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">×</button>
                    </div>
                @endif

                {{-- Validation errors summary --}}
                {{-- @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> Please fix the following issues:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <form action="{{ route('storeEnquiry') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group mb-4">
                        <label for="name" class="form-label font-weight-semibold">Your Name</label>
                        <input type="text" class="form-control rounded @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="mobile" class="form-label font-weight-semibold">Your Mobile</label>
                        <input type="text" class="form-control rounded @error('mobile') is-invalid @enderror" 
                               id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="Enter your mobile number" required>
                        @error('mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="email" class="form-label font-weight-semibold">Your Email</label>
                        <input type="email" class="form-control rounded @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="subject" class="form-label font-weight-semibold">Subject</label>
                        <input type="text" class="form-control rounded @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" placeholder="Enter subject" required>
                        @error('subject')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="message" class="form-label font-weight-semibold">Message</label>
                        <textarea class="form-control rounded @error('message') is-invalid @enderror" 
                                  id="message" name="message" rows="5" placeholder="Your message" required>{{ old('message') }}</textarea>
                        @error('message')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-5 rounded">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>

            <!-- Contact Info -->
            <div class="col-lg-6 mb-5">
                <div class="bg-white p-4 rounded-lg shadow-sm h-100">
                    <h5 class="font-weight-bold mb-4">Reach Out to On Jewel</h5>
                    @if(settings()->short_about_description)
                    <p class="text-muted mb-4">{{ settings()->short_about_description }}</p>
                    @endif

                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-3">Contact Details</h6>
                        @if(settings()->email)
                        <p class="mb-2"><i class="fas fa-envelope text-primary mr-2"></i> {{ settings()->email }}</p>
                        @endif
                        @if(settings()->alt_email)
                        <p class="mb-2"><i class="fas fa-envelope text-primary mr-2"></i> {{ settings()->alt_email }}</p>
                        @endif
                        @if(settings()->mobile)
                        <p class="mb-2"><i class="fas fa-phone-alt text-primary mr-2"></i> {{ settings()->mobile }}</p>
                        @endif
                        @if(settings()->alt_mobile)
                        <p class="mb-2"><i class="fas fa-phone-alt text-primary mr-2"></i> {{ settings()->alt_mobile }}</p>
                        @endif
                        <div class="d-flex mt-3">
                            <a href="https://api.whatsapp.com/send?phone=+918510047947&text=Hello" class="text-primary mr-3" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a>
                            @if(settings()->facebook)
                            <a class="text-primary mr-3" target="_blank" href="{{ settings()->facebook }}">
                               <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                            @endif
                            @if(settings()->twitter)
                            <a class="text-primary mr-3" href="{{ settings()->twitter }}">
                               <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            @endif
                            @if(settings()->linkedin)
                            <a class="text-primary mr-3" href="{{ settings()->linkedin }}">
                               <i class="fab fa-linkedin-in fa-lg"></i>
                            </a>
                            @endif
                            @if(settings()->instagram)
                            <a class="text-primary mr-3" target="_blank" href="{{ settings()->instagram }}">
                               <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            @endif
                            @if(settings()->youtube)
                            <a class="text-primary mr-3" target="_blank" href="{{ settings()->youtube }}">
                               <i class="fab fa-youtube fa-lg"></i>
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-3">Paytm UPI</h6>
                        <p class="mb-1">Name: Manpreet Kaur</p>
                        <p class="mb-1">UPI: 8510047947@paytm</p>
                        <p><a href="https://p.paytm.me/xCTH/0lpowpmg" target="_blank" class="text-primary">Pay via Paytm</a></p>
                    </div>

                    <div>
                        <h6 class="font-weight-bold mb-3">Bank Details</h6>
                        <p class="mb-1">Account Name: ON JEWEL</p>
                        <p class="mb-1">Account Number: 4111606793</p>
                        <p class="mb-1">IFSC: KKBK0000207</p>
                        <p>Account Type: Current</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection