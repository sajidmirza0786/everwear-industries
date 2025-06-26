@include('admin.partials.header')
<body>
   <!-- Layout wrapper -->
   <div class="layout-wrapper layout-content-navbar">
   <div class="layout-container">
   <!-- Menu -->
   @include('admin.partials.sidebar')
   <!-- / Menu -->
   <!-- Layout container -->
   <div class="layout-page">
   <!-- Navbar -->
   @include('admin.partials.navbar')
   <!-- / Navbar -->
   <!-- Content wrapper -->
   <div class="content-wrapper">
      <!-- Content -->
      <div class="container-xxl flex-grow-1 container-p-y">
         @yield('content')
      </div>
   </div>
   <!-- / Content -->
@include('admin.partials.footer')