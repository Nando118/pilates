<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title_page', 'My Laravel App')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.css') }}">
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        @yield('content')
    </div>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
@stack('scripts')
@include('sweetalert::alert')
</body>
</html>
