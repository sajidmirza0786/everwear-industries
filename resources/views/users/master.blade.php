<!DOCTYPE html>
<html lang="en">

<head>
    @yield('seo')
    @include('users.partials.header')
</head>

<body>
    <!-- Topbar Start -->
    @include('users.partials.top')
    <!-- Topbar End -->


    <!-- Navbar Start -->
    @include('users.partials.navbar')
    <!-- Navbar End -->


    @yield('content')

    <!-- Footer Start -->
    @include('users.partials.footer')
</body>

</html>