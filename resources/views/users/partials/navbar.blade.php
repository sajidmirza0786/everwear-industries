@php
    $ucategories   = ucategories();
@endphp

<!-- Main header -->
<header class="site-header" data-testid="site-header">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between" style="height: 78px;">

      <!-- Mobile menu toggle -->
      <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" data-testid="mobile-menu-btn">
        <i class="bi bi-list" style="font-size: 22px;"></i>
      </button>

      <!-- Brand -->
      <a href="{{ url('/') }}" class="brand" data-testid="brand-logo">
        <img src="{{ url('users/assets/images/logo.png') }}" width="80"/>
      </a>

      <!-- Desktop nav -->
      <nav class="d-none d-lg-flex align-items-center gap-1" data-testid="desktop-nav">

        <a href="{{ url('/') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Home</a>
        <a href="{{ url('about-us') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Company Profile</a>
        <a href="{{ url('collections') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Our Collections</a>
        <a href="{{ url('catalogue') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Our Catalogue</a>

        <!-- Shop dropdown -->
        {{-- @if($ucategories->isNotEmpty())
        <div class="nav-dropdown">
          <a href="#" class="nav-link-main nav-dropdown-trigger" data-testid="nav-shop">
            Products <i class="bi bi-chevron-down nav-chevron" style="font-size:10px;"></i>
          </a>
          <div class="nav-dropdown-menu">
            @foreach($ucategories as $cat)
            <a href="{{ route('listing', $cat->slug) }}" class="nav-dropdown-item">{{ str()->ucfirst($cat->name) }}</a>
            @endforeach
            <!-- <div class="nav-dropdown-divider"></div>
            <a href="/shop.html" class="nav-dropdown-item">All Products</a> -->
          </div>
        </div>
        @endif --}}
        <a href="{{ url('videos') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Videos</a>

        <a href="{{ url('contact-us') }}" class="nav-link-main" data-nav="about" data-testid="nav-about">Contact Us</a>
      </nav>

      <!-- Right utilities -->
      <div class="d-flex align-items-center gap-1">
        <button class="icon-btn" type="button" data-testid="search-trigger-btn" aria-label="Search">
          <i class="bi bi-search"></i>
        </button>
        <!-- <button class="icon-btn d-none d-md-inline-flex" type="button" data-testid="theme-toggle-btn" aria-label="Theme">
          <i class="theme-icon bi bi-moon-stars"></i>
        </button> -->
        <a href="{{ route('login') }}" class="icon-btn d-none d-md-inline-flex" data-testid="account-icon-btn" aria-label="Account">
          <i class="bi bi-person"></i>
        </a>
        {{-- <a href="/dashboard.html" class="icon-btn position-relative" data-testid="wishlist-icon-btn" aria-label="Wishlist">
          <i class="bi bi-heart"></i>
          <span class="badge" data-wish-count style="display:none">0</span>
        </a> --}}
        <a href="{{ url('cart') }}" class="icon-btn position-relative" data-testid="cart-icon-btn" aria-label="Cart">
          <i class="bi bi-bag"></i>
          <span class="badge" data-cart-count style="display:none">{{ cartItemCount() }}</span>
        </a>
      </div>
    </div>
  </div>
</header>


<!-- ============================================================
     CSS — replace old mega-menu styles with these
============================================================ -->
<style>
/* ── Sticky header ────────────────────────────────────────── */
.site-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--bg);
  border-bottom: 1px solid var(--line);
  backdrop-filter: saturate(140%) blur(10px);
  -webkit-backdrop-filter: saturate(140%) blur(10px);
  transition: box-shadow 0.3s ease;
}
.site-header.scrolled {
  box-shadow: 0 4px 24px -8px rgba(26,20,16,0.12);
}

/* ── Dropdown wrapper ─────────────────────────────────────── */
.nav-dropdown {
  position: relative;
}

/* ── Chevron rotate on open ───────────────────────────────── */
.nav-dropdown-trigger .nav-chevron {
  display: inline-block;
  transition: transform 0.22s ease;
}
.nav-dropdown.open .nav-chevron {
  transform: rotate(180deg);
}

/* ── Dropdown panel ───────────────────────────────────────── */
.nav-dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%) translateY(-6px);
  min-width: 200px;
  background: var(--bg);
  border: 1px solid var(--line);
  box-shadow: 0 16px 40px -12px rgba(26,20,16,0.16);
  padding: 8px 0;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0.22s;
  z-index: 200;

  /* subtle top accent line */
  border-top: 2px solid var(--accent);
}

/* small arrow notch pointing up */
.nav-dropdown-menu::before {
  content: '';
  position: absolute;
  top: -7px;
  left: 50%;
  transform: translateX(-50%);
  width: 12px;
  height: 12px;
  background: var(--bg);
  border-left: 1px solid var(--line);
  border-top: 1px solid var(--line);
  rotate: 45deg;
  /* hide the top border overlap with accent line */
  border-top-color: var(--accent);
  border-left-color: var(--accent);
}

.nav-dropdown.open .nav-dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(0);
  transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0s;
}

/* ── Dropdown items ───────────────────────────────────────── */
.nav-dropdown-item {
  display: flex;
  align-items: center;
  padding: 9px 20px;
  font-size: 13.5px;
  font-weight: 500;
  color: var(--ink);
  letter-spacing: 0.01em;
  transition: background 0.15s, color 0.15s, padding-left 0.15s;
  white-space: nowrap;
}
.nav-dropdown-item:hover {
  background: var(--surface);
  color: var(--accent-2);
  padding-left: 26px;
}

/* thin divider */
.nav-dropdown-divider {
  height: 1px;
  background: var(--line);
  margin: 6px 14px;
}
</style>


<!-- ============================================================
     JS — hover + keyboard accessible dropdown
============================================================ -->
<script>
(function () {
  var dropdowns = document.querySelectorAll('.nav-dropdown');
  var timers = new WeakMap();

  dropdowns.forEach(function (dd) {
    var trigger = dd.querySelector('.nav-dropdown-trigger');
    var menu    = dd.querySelector('.nav-dropdown-menu');

    function open() {
      // close all others first
      dropdowns.forEach(function (other) {
        if (other !== dd) other.classList.remove('open');
      });
      clearTimeout(timers.get(dd));
      dd.classList.add('open');
    }

    function scheduleClose() {
      timers.set(dd, setTimeout(function () {
        dd.classList.remove('open');
      }, 120));
    }

    // hover
    dd.addEventListener('mouseenter', open);
    dd.addEventListener('mouseleave', scheduleClose);

    // keyboard: Enter / Space toggles, Escape closes
    trigger.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        dd.classList.toggle('open');
      }
      if (e.key === 'Escape') dd.classList.remove('open');
    });
  });

  // click outside closes all
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.nav-dropdown')) {
      dropdowns.forEach(function (dd) { dd.classList.remove('open'); });
    }
  });

  // sticky shadow on scroll
  var header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });
  }
})();
</script>


<!-- ============================================================
     Mobile offcanvas (unchanged)
============================================================ -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" data-testid="mobile-offcanvas">
  <div class="offcanvas-header">
    <!-- <span class="brand">Everwear Industries</span> -->
    <img src="{{ url('users/assets/images/logo.png') }}" width="90"/>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="p-3 border-bottom-soft">
      <div class="position-relative">
        <form method="GET" action="{{ route('listing') }}">
          @csrf
          <i class="bi bi-search position-absolute" style="left: 14px; top: 14px; color: var(--text-soft);"></i>
          <input type="search" name="search" class="form-control" placeholder="Search" style="padding-left: 40px;" data-testid="mobile-search-input">
        </form>
        </div>
    </div>
    <ul class="list-unstyled m-0">
      <li><a href="{{ url('/') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Home <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('about-us') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">About Us <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('collections') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Our Collections <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('catalogue') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Our Catalogue <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('videos') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Videos <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('contact-us') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Contact Us <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('customer-service') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Customer Service <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('shipping-policy') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Shipping Policy <i class="bi bi-chevron-right"></i></a></li>
      <li><a href="{{ url('return-exchange-policy') }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom-soft text-reset">Return & Exchanges <i class="bi bi-chevron-right"></i></a></li>
    </ul>
    <div class="p-3">
      <a href="{{ route('login') }}" class="btn btn-outline-dark w-100 mb-2" data-testid="mobile-login-btn">Sign in</a>
      <a href="{{ route('register') }}" class="btn btn-dark w-100" data-testid="mobile-register-btn">Create account</a>
    </div>
    <!-- <div class="p-3 border-top-soft d-flex justify-content-between align-items-center">
      <small class="text-soft">Theme</small>
      <button class="btn btn-sm btn-outline-dark" data-testid="theme-toggle-btn"><i class="theme-icon bi bi-moon-stars"></i></button>
    </div> -->
  </div>
</div>

<!-- Search overlay (unchanged) -->
<div class="search-overlay" id="searchOverlay" data-testid="search-overlay">
  <div class="search-modal" data-testid="search-modal">
    <form method="GET" action="{{ route('listing') }}">
      @csrf
      <div class="search-input-wrap">
        <i class="bi bi-search search-icon"></i>
        <input
          type="search"
          name="search"
          placeholder="Search products…"
          data-testid="search-input"
          autocomplete="off"
          spellcheck="false"
        >
        <button type="button" class="search-close-btn" data-testid="search-close-btn" aria-label="Close search">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     Extra JS — wire the × button (add inside initSearch or
     alongside your main.js boot)
============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var closeBtn = document.querySelector('[data-testid="search-close-btn"]');
  var overlay  = document.getElementById('searchOverlay');
  if (closeBtn && overlay) {
    closeBtn.addEventListener('click', function () {
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    });
  }
});
</script>