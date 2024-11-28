@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

<style>
    body {
        background: url('{{ asset('img/background login web ohana .JPG') }}') no-repeat center center fixed; 
        background-size: cover; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh;
        margin: 0;
    }

    .login-container {
        position: relative;
        max-width: 400px;
        width: 100%;
    }

    .form-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 100%;
        margin-top: 30px;
        position: relative;
    }

    .logo-container img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: block;
        margin: 0 auto 20px auto;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

@section('content')
    <div class="login-container">
        <form class="form-container" action="{{ route('login.post') }}" method="POST">
            @csrf            
            <div class="logo-container">
                <img src="{{ asset('img/Ohana Pilates - Logo.png') }}" alt="Ohana Pilates">
            </div>
            <h2 class="text-left">Sign In</h2>
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

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>
            
            <button type="submit" class="btn w-100 btn-dark"><strong>Sign In</strong></button>

            <div class="my-3 text-center">
                <a class="link-dark text-decoration-none" href="{{ route('password.request') }}"><strong>Forgot Password?</strong></a>
            </div>

            <p class="text-center mt-4 mb-0">Doesn't have account?<a class="link-dark text-decoration-none" href="{{ route('register') }}"> <strong>Register Now</strong></a></p>

            <hr class="w-50 mx-auto my-4">

            <div class="container text-center">
                <a href="{{ route('redirectToProvider', ['provider' => 'google']) }}" class="btn btn-outline-dark btn-block social-btn">
                    <i class="fab fa-google"></i> Sign in with Google
                </a>
            </div>

        </form>
        <p class="text-center text-muted mt-3" style="color: white !important;">
            &copy;Ohana Pilates. All rights reserved.
        </p>
    </div>
@endsection
