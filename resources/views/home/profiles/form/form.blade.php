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
        <div class="scrollable-content py-3">
            <form id="form_input" action="{{ $action }}" method="{{ $method }}" enctype="multipart/form-data">
                @csrf

                @if(isset($userData))
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $userData->id }}">
                @endif
            
                <div class="mb-3">
                    <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($userData) ? $userData->name : "") }}" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender<span style="color: red;">*</span></label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="male" {{ old('gender') == "male" ? 'selected' : (isset($userData) && $userData->profile->gender == "male" ? 'selected' : '') }}>Male</option>
                        <option value="female" {{ old('gender') == "female" ? 'selected' : (isset($userData) && $userData->profile->gender == "female" ? 'selected' : '') }}>Female</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone<span style="color: red;">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" autocomplete="off" value="{{ old('phone') ?? (isset($userData) ? $userData->profile->phone : "") }}" required>
                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" rows="3" name="address" autocomplete="off">{{ old('address') ?? (isset($userData) ? $userData->profile->address : "") }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="formFile" class="form-label">Profile Image</label>
                    <input class="form-control @error('profile_picture') is-invalid @enderror" type="file" id="formFile" name="profile_picture">
                    @error('profile_picture')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="off">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="off">
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <button id="btn_submit" class="btn btn-success" type="submit"><strong>Update</strong></button>
            </form>
        </div>
    </div>
@endsection
