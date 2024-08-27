@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="w-100" style="max-width: 400px;">
            <form class="bg-white shadow-md rounded p-4">
                <h2 class="text-left mb-4">Sign In</h2>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Remember me</label>
                    </div>
                    <a class="link-dark text-decoration-none" href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="btn w-100 btn-dark"><strong>Sign In</strong></button>
                <p class="text-center mt-5 mb-0">Doesn't have account?<a class="link-dark text-decoration-none" href="#"> <strong>Register Now</strong></a></p>
            </form>
            <p class="text-center text-muted mt-3">
                &copy; 2024 Your Company. All rights reserved.
            </p>
        </div>
    </div>
@endsection
