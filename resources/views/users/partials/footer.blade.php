@php
    $ucategories   = ucategories();
@endphp
<footer class="site-footer" data-testid="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-12">
        <a href="{{ url('/') }}">
          <img src="{{ url('users/assets/images/logo.png') }}" width="110"/>
        </a>
        <a href="{{ url('/') }}" class="brand mb-3 d-inline-block">Everwear Industries</a>
        <p class="text-muted-2" style="max-width: 320px; font-size: 14px;">
         At Everwear Industries, we craft more than trophies—we create enduring symbols of excellence.
        </p>
        <div class="social-icons mt-3" data-testid="social-icons">
          @if(!empty(settings()->instagram))
          <a href="{{ settings()->instagram }}" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          @endif

          @if(!empty(settings()->facebook))
          <a href="{{ settings()->facebook }}" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          @endif

          @if(!empty(settings()->youtube))
          <a href="{{ settings()->youtube }}" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          @endif

          @if(!empty(settings()->twitter))
          <a href="{{ settings()->twitter }}" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
          @endif
        </div>
      </div>

      <div class="col-lg-2 col-6 footer-col">
      @if($ucategories->isNotEmpty())
        <h6>Shop</h6>
        <ul>
          @foreach($ucategories as $cat)
            <li><a href="{{ route('listing', $cat->slug) }}">{{ str()->ucfirst($cat->name) }}</a></li>
          @endforeach
        </ul>
      @endif
      </div>

      <div class="col-lg-2 col-6 footer-col">
        <h6>Help</h6>
        <ul>
          <li><a href="{{ url('customer-service') }}">Customer Service</a></li>
          <li><a href="{{ url('shipping-policy') }}">Shipping Policy</a></li>
          <li><a href="{{ url('return-exchange-policy') }}">Returns &amp; Exchanges</a></li>
          {{-- <li><a href="#">Size Guide</a></li>
          <li><a href="#">Track Order</a></li> --}}
        </ul>
      </div>

      <div class="col-lg-2 col-6 footer-col">
        <h6>Company</h6>
        <ul>
          <li><a href="{{ url('/') }}">Home</a></li>
          <li><a href="{{ url('about-us') }}">About Us</a></li>
          <li><a href="{{ url('contact-us') }}">Contact Us</a></li>
          <li><a href="{{ url('videos') }}">Videos</a></li>
          <li><a href="{{ url('contact-us') }}">Stockists</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-6 footer-col">
        <h6>Contact</h6>
        <ul>
          <li>{{ settings()->address }}</li>
          <li><a href="mailto:{{ settings()->email }}">{{ settings()->email }}</a></li>
          <li>{{ settings()->mobile }}</li>
        </ul>
      </div>
    </div>

    <hr class="divider-line my-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <small class="text-soft">© 2026 Everwear Industries · All rights reserved</small>
      <div class="payment-icons" data-testid="payment-icons">
        <span>VISA</span>
        <span>MASTERCARD</span>
        <span>AMEX</span>
        <span>PAYPAL</span>
        <span>APPLE PAY</span>
        <span>KLARNA</span>
      </div>
    </div>
  </div>
</footer>