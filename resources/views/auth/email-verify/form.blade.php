@extends('auth.layouts.main-layout')

@section('title_page', $title_page)

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <form class="bg-white shadow-md rounded p-4" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <h2 class="text-left mb-4">Verifiy Email Address</h2>
            Before proceeding, please check your email for a verification link. If you did not receive the email,
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to re-send verification link</button>.
        </form>
        <p class="text-center text-muted mt-3">
            &copy; 2024 Your Company. All rights reserved.
        </p>
    </div>
@endsection
