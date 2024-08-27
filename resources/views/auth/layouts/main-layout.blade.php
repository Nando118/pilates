<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title_page', 'My Laravel App')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>

@yield('content')

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
