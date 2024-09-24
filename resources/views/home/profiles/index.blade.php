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
    </style>
@endpush

@section('content')
    <div class="w-100 d-flex justify-content-center" style="max-width: 400px; margin: auto;">
        <!-- Scrollable Content Section with Cards -->
        <div class="scrollable-content p-3">
            <div class="container-fluid text-center">
                <!-- Foto Profil -->
                @if(isset($userData->profile->profile_picture))
                    <img class="rounded-circle mb-3" src="{{ asset('storage/' . $userData->profile->profile_picture) }}" alt="User Avatar" width="100" height="100">
                @else
                    <img class="rounded-circle mb-3" src="{{ asset('storage/images/profile_default/profile_default.jpg') }}" alt="User Avatar" width="100" height="100">
                @endif
                
                <!-- Nama dan Username -->
                <h3 class="card-title mb-0">{{ $userData->name }}</h3>
                <h6 class="card-subtitle mb-3"><em>{{ '@' . $userData->profile->username }}</em></h6>

                <!-- Jarak Sebelum Detail Profil -->
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-venus-mars"></i>
                        <span>{{ ucfirst($userData->profile->gender) }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone mr-2"></i>
                        <span>{{ $userData->profile->phone }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-home mr-2"></i>
                        <span>{{ ucfirst($userData->profile->address) }}</span>
                    </div>
                </div>
                <a class="btn btn-warning mt-3" href="#" role="button"><strong>Edit Profile</strong></a>
            </div>
        </div>
    </div>
@endsection
