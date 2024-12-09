<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title_page', 'My Laravel App')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset("libs/dataTables/css/dataTables-bootstrap4-min.css") }}" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2-bootstrap4.min.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css") }}" rel="stylesheet" />
    @stack('styles')
</head>
<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="w-100" style="max-width: 430px;">
            <!-- Sticky Navbar -->
            <nav class="navbar sticky-top" style="background-color: #FAF7F0;">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <!-- Left Side: Navbar Brand -->
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('img/logo ohana - navbar brand.png') }}" alt="Bootstrap" height="30">
                    </a>

                    <!-- Right Side: Location and Burger Button -->
                    <div class="d-flex align-items-center">
                        {{-- Location --}}
                        <p class="mb-0 me-3">
                            <a href="https://www.google.com/search?q=Ohana+Pilates+Gading+Serpong" target="_blank" style="text-decoration: none; color: inherit;">
                                <i class="fas fa-map-pin"></i> <strong>Gading Serpong</strong>
                            </a>
                        </p>
                        <!-- Burger Button -->
                        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    <!-- Collapsible Navbar Content -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item active">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('my-profile.index') }}">My Profile</a>
                            </li>
                            @can("access-client-menu")
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('my-lesson-schedules.index') }}">My Lessons</a>
                                </li>
                            @endcan
                            @can("access-client-menu")
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('my-transactions.index') }}">My Transactions</a>
                                </li>
                            @endcan
                            @can("access-client-menu")
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('coaches.index') }}">Coaches</a>
                                </li>
                            @endcan
                            @can("access-coach-menu")
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('my-schedules.index') }}">My Schedule</a>
                                </li>
                            @endcan
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


            <div class="py-2 d-flex align-items-center justify-content-center" style="background-color: #FAF7F0;">
                @yield('content')
            </div>

            <!-- Sticky Footer Navigation -->
            <nav class="navbar sticky-bottom bg-dark" data-bs-theme="dark">
                <div class="container-fluid justify-content-around">
                    <a class="navbar-brand" href="{{ route('home') }}"><i class="fas fa-home"></i></a>
                    <a class="navbar-brand" href="{{ route('user-lesson-schedules.index') }}"><i class="fas fa-calendar"></i></a>
                    @can("access-client-menu")
                        <a class="navbar-brand" href="{{ route('my-lesson-schedules.index') }}"><i class="fas fa-clock"></i></a>
                    @endcan
                    @can("access-coach-menu")
                        <a class="navbar-brand" href="{{ route('my-schedules.index') }}"><i class="fas fa-calendar-check"></i></a>
                    @endcan
                </div>
            </nav>
        </div>
    </div>

    @include('sweetalert::alert')
    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset("libs/dataTables/js/jquery-dataTables-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/dataTables-bootstrap4-min.js") }}"></script>
    <script src="{{ asset("/libs/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js") }}"></script>
    <script src="{{ asset("libs/moment/moment.js") }}"></script>
    <script src="{{ asset("libs/moment/moment-time-zone-with-data.js") }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
