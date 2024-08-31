@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <form class="bg-white shadow-md rounded p-4" action="{{ route('password.update') }}" method="POST">
            @csrf
            <h2 class="text-left mb-4">Reset Password</h2>

            <input type="hidden" class="form-control" id="token" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label @error('email') is-invalid @enderror">Email address</label>
                <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email" autocomplete="off" value="{{ $email }}" required readonly>
                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="off" required>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="off" required>
                @error('password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn w-100 btn-dark"><strong>Sign Up</strong></button>

            <p class="text-center mt-4 mb-0">Already have an account?<a class="link-dark text-decoration-none" href="{{ route('login') }}"> <strong>Sign In</strong></a></p>
        </form>
        <p class="text-center text-muted mt-3">
            &copy; 2024 Your Company. All rights reserved.
        </p>
    </div>
@endsection
