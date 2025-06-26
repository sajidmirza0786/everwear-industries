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
            <a class="text-dark px-2" target="_blank" href="https://www.facebook.com/jewelonjewel?mibextid=ZbWKwL">
            <i class="fab fa-facebook-f"></i>
            </a>
            <!--<a class="text-dark px-2" href="">
               <i class="fab fa-twitter"></i>
               </a>
               <a class="text-dark px-2" href="">
               <i class="fab fa-linkedin-in"></i>
               </a>-->
            <a class="text-dark px-2" target="_blank" href="https://www.instagram.com/theonjewel_/">
            <i class="fab fa-instagram"></i>
            </a>
            <a class="text-dark pl-2" target="_blank" href="https://www.youtube.com/@onjewel2051">
            <i class="fab fa-youtube"></i>
            </a>
         </div>
      </div>
   </div>
   <div class="row align-items-center px-xl-5">
      {{-- <div class="col-lg-3 d-none d-lg-block">
         <a href="{{ url('/') }}" class="text-decoration-none">
            <h1 class="m-0 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border px-3 mr-1">The</span>Onjewel</h1>
            <img src="{{ url(\Storage::url(settings()->logo??'')) }}" width="70"/>
         </a>
      </div> --}}
      <div class="col-lg-6 col-12 text-left d-block d-md-none">
         <form action="{{ route('search') }}" method="GET">
            @csrf
            <div class="input-group">
               <input type="text" name="query" class="form-control" placeholder="Search for products">
               <div class="input-group-append">
                  <button class="input-group-text bg-transparent text-primary" type="submit">
                  <i class="fa fa-search"></i>
                  </button>
               </div>
            </div>
         </form>
      </div>
      {{-- <div class="col-lg-3 col-6 text-right">
         <a href="" class="btn border">
         <i class="fas fa-heart text-primary"></i>
         <span class="badge">0</span>
         </a>
         <a href="" class="btn border">
         <i class="fas fa-shopping-cart text-primary"></i>
         <span class="badge">0</span>
         </a>
      </div> --}}
   </div>
</div>