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
                <div class="mt-4" style="font-size: 0.9rem;">
                    <div class="mb-3">
                        {{-- <i class="fas fa-envelope"></i> --}}
                        <strong><em>Email Address</em></strong>
                        <div>{{ ucfirst($userData->email) }}</div>
                    </div>
                    <div class="mb-3">
                        {{-- <i class="fas fa-venus-mars"></i> --}}
                        <strong><em>Gender</em></strong>
                        <div>{{ ucfirst($userData->profile->gender) }}</div>
                    </div>
                    <div class="mb-3">
                        {{-- <i class="fas fa-phone"></i> --}}
                        <strong><em>Phone Number</em></strong>
                        <div>{{ $userData->profile->phone }}</div>
                    </div>
                    <div class="mb-3">
                        {{-- <i class="fas fa-home"></i> --}}
                        <strong><em>Address</em></strong>
                        <div>{{ ucfirst($userData->profile->address) }}</div>
                    </div>
                    <a class="btn btn-warning mt-3" href="{{ route('my-profile.edit') }}" role="button"><strong>Edit Profile</strong></a>
                </div>
            </div>
        </div>
    </div>
@endsection
