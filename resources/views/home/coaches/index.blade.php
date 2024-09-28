@extends('home.layouts.main-layout')

@section('title_page', $title_page)

@push('styles')
    <style>
        /* Scrollable Content Section */
        .scrollable-content {
            height: 100vh;
            overflow-y: auto;

            /* Menyembunyikan scrollbar */
            scrollbar-width: none; /* Untuk Firefox */
            -ms-overflow-style: none; /* Untuk Internet Explorer dan Edge lama */
        }

        .scrollable-content::-webkit-scrollbar {
            display: none; /* Untuk Chrome, Safari, dan Opera */
        }

        .certification-header {
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
        }

        .certification-item {
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <!-- Scrollable Content Section with Cards -->
        <div class="scrollable-content p-3">
            <div class="container-fluid d-flex flex-column align-items-center">
                @foreach($coaches as $coach)
                    <div class="card mb-5" style="width: 100%;">
                        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                            @if(isset($coach->profile->profile_picture))
                                <img class="card-img-top img-fluid" src="{{ asset('storage/' . $coach->profile->profile_picture) }}" alt="User Avatar" style="object-fit: cover; max-width: 100%; max-height: 100%;">
                            @else
                                <img class="card-img-top img-fluid" src="{{ asset('storage/images/profile_default/profile_default.jpg') }}" alt="User Avatar" style="object-fit: cover; max-width: 100%; max-height: 100%;">
                            @endif
                        </div>
                        <div class="card-body text-center pb-0">
                            <h5 class="card-title">{{ $coach->name }}</h5>
                        </div>
                        <div class="card-body pt-0 pb-2 px-4">
                            <div class="certification-header">Certifications:</div>
                            <ul class="list-group list-group-flush p-3">
                                @foreach($coach->coachCertifications as $certification)
                                    <li class="certification-item">
                                        <strong>{{ $certification->certification_name }}</strong>
                                        <br>
                                        @isset($certification->issuing_organization)
                                            <em>{{ $certification->issuing_organization }} - {{ date('Y', strtotime($certification->date_received ?? 'N/A')) }}</em>
                                        @endisset
                                    </li>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
