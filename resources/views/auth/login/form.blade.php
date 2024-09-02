@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <form class="bg-white shadow-md rounded p-4" action="{{ route('login.post') }}" method="POST">
            @csrf
            <h2 class="text-left mb-4">Sign In</h2>
            <div class="mb-3">
                <label for="email" class="form-label @error('email') is-invalid @enderror">Email address</label>
                <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email" autocomplete="off" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label @error('password') is-invalid @enderror">Password</label>
                <input type="password" class="form-control" id="password" name="password" autocomplete="off" required>
                @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a class="link-dark text-decoration-none" href="{{ route('password.request') }}">Forgot Password?</a>
            </div>

            <button type="submit" class="btn w-100 btn-dark"><strong>Sign In</strong></button>

            <p class="text-center mt-4 mb-0">Doesn't have account?<a class="link-dark text-decoration-none" href="{{ route('register') }}"> <strong>Register Now</strong></a></p>

            <hr class="w-50 mx-auto my-4">

            <div class="container text-center">
                <a href="{{ route('redirectToProvider', ['provider' => 'google']) }}" class="btn btn-outline-dark btn-block social-btn">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
            </div>

        </form>
        <p class="text-center text-muted mt-3">
            &copy; 2024 Your Company. All rights reserved.
        </p>
    </div>
@endsection

@push('scripts')
    Swal.fire({
    title: 'Error!',
    text: 'Do you want to continue',
    icon: 'error',
    confirmButtonText: 'Cool'
    })
@endpush
