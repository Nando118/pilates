@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <form class="bg-white shadow-md rounded p-4" action="{{ route('password.email') }}" method="POST">
            @csrf
            <h2 class="text-left mb-4">Reset Password</h2>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" autocomplete="off" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn w-100 btn-dark"><strong>Reset Password</strong></button>

            <p class="text-center mt-4 mb-0">Already have an account?<a class="link-dark text-decoration-none" href="{{ route('login') }}"> <strong>Sign In</strong></a></p>
        </form>
        <p class="text-center text-muted mt-3">
            &copy; 2024 Your Company. All rights reserved.
        </p>
    </div>
@endsection
