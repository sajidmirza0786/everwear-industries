<div class="container-fluid">
   <div class="row bg-secondary py-2 px-xl-5">
      <div class="col-lg-6 d-none d-lg-block">
         <div class="d-inline-flex align-items-center">
            <a class="text-dark" href="{{ route('contact') }}">Contact</a>
            <!-- <span class="text-muted px-2">|</span>
               <a class="text-dark" href="">Help</a> -->
            <span class="text-muted px-2">|</span>
            <a class="text-dark" href="{{ route('shipping_policy') }}">Shipping Policy</a>
         </div>
      </div>
      <div class="col-lg-6 text-center text-lg-right">
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
   </div>
   <div class="row align-items-center px-xl-5">
      <div class="col-lg-6 col-12 text-left d-block d-md-none">
         <form action="{{ route('listing') }}" method="GET" class="form-inline mx-auto my-2 my-lg-0 search-form">
            <div class="input-group">
               <input name="search" class="form-control border-right-0" type="text" placeholder="Search products..." aria-label="Search">
               <div class="input-group-append">
                  <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>