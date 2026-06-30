<!DOCTYPE html>
<html lang="en">

<head>
    @yield('seo')
    @include('users.partials.header')
</head>

<body data-page="home">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>

    <!-- Topbar Start -->
    @include('users.partials.top')
    <!-- Topbar End -->


    <!-- Navbar Start -->
    @include('users.partials.navbar')
    <!-- Navbar End -->


    @yield('content')

    <!-- Footer Start -->
    @include('users.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="{{ url('users/assets/js/main.js') }}"></script>

    @yield('scripts')
</body>

</html>
