<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title_page', 'My Laravel App')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2-bootstrap4.min.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css") }}" rel="stylesheet" />
    @stack('styles')
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="w-100" style="max-width: 400px;">
            <!-- Sticky Navbar -->
            <nav class="navbar navbar-light bg-light sticky-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#"><strong>Pilates</strong></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">My Lessons</a>
                            </li>
                            <li class="nav-item">
                                <!-- Form Logout -->
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <div class="my-2" style="background-color: #FAF7F0;">
                @yield('content')
            </div>

            <!-- Sticky Footer Navigation -->
            <nav class="navbar navbar-light bg-light sticky-bottom">
                <div class="container-fluid justify-content-around">
                    <a class="navbar-brand" href="{{ route('home') }}"><i class="fas fa-home"></i></a>
                    <a class="navbar-brand" href="{{ route('user-lesson-schedules.index') }}"><i class="fas fa-calendar"></i></a>
                    <a class="navbar-brand" href="#"><i class="fas fa-clock"></i></a>
                </div>
            </nav>
        </div>
    </div>

    @include('sweetalert::alert')
    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset("/libs/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js") }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
