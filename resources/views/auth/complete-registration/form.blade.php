@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <form id="form_input" class="bg-white shadow-md rounded p-4" action="{{ route('complete-registration.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h2 class="text-left mb-4">Complete Profile</h2>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender<span style="color: red;">*</span></label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="" disabled selected>Select your gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone<span style="color: red;">*</span></label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" autocomplete="off" value="{{ old('phone') }}" required>
                @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" rows="3" name="address" autocomplete="off">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <label for="profile_picture" class="form-label @error('profile_picture') is-invalid @enderror">Profile Image</label>
            <div class="input-group mb-3">
                <label for="formFile" class="form-label @error('profile_picture') is-invalid @enderror" onchange="updateFileName(this)"></label>
                <input class="form-control" type="file" id="formFile" name="profile_picture">
            </div>
            @error('profile_picture')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            <button type="submit" id="btn_submit" class="btn w-100 btn-dark"><strong>Sign Up</strong></button>
        </form>
        <p class="text-center text-muted mt-3">
            &copy;Ohana Pilates. All rights reserved.
        </p>
    </div>
@endsection
