@extends('home.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <!-- Sticky Navbar -->
        <nav class="navbar navbar-dark bg-dark sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Navbar</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Dropdown
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Scrollable Content Section with Cards -->
        <div class="scrollable-content p-3">
            <div class="container-fluid">
                <div class="card mb-3">
                    <div class="card-header">Card 1</div>
                    <div class="card-body">
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Card 2</div>
                    <div class="card-body">
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Card 3</div>
                    <div class="card-body">
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Card 4</div>
                    <div class="card-body">
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer Navigation -->
        <nav class="navbar navbar-dark bg-dark sticky-bottom">
            <div class="container-fluid justify-content-around">
                <a class="navbar-brand" href="#">Buy Package</a>
                <a class="navbar-brand" href="#">Schedule</a>
                <a class="navbar-brand" href="#">My Package</a>
                <a class="navbar-brand" href="#">Appointment</a>
            </div>
        </nav>
    </div>

    <style>
        /* Scrollable Content Section */
        .scrollable-content {
            height: calc(100vh - 120px); /* Sesuaikan 120px dengan tinggi navbar dan footer */
            overflow-y: auto;
        }
    </style>
@endsection
