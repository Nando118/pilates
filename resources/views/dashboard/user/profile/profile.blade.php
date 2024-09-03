@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Details</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-dark text-white">
            <div class="d-flex align-items-center">
            <img class="rounded-circle mr-3" src="{{ asset('img/profile/profile_default.jpg') }}" alt="User Avatar" width="50" height="50">
            <div>                
                <h3 class="card-title mb-0">{{ $user_data->name }}</h3>
                <h6 class="card-subtitle">{{ $user_role }}</h6>
            </div>
            </div>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex align-items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>{{ ucfirst($user_data->profile->branch) }}
            </li>
            <li class="list-group-item d-flex align-items-center">
                <i class="fas fa-venus-mars mr-2"></i>{{ ucfirst($user_data->profile->gender) }}
            </li>
            <li class="list-group-item d-flex align-items-center">
                <i class="fas fa-phone mr-2"></i>{{ ucfirst($user_data->profile->phone) }}
            </li>
            <li class="list-group-item d-flex align-items-center">
                <i class="fas fa-home mr-2"></i>{{ ucfirst($user_data->profile->address) }}
            </li>
            </ul>
        </div>
    </div>
@endsection