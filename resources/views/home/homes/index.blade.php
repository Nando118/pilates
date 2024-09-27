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
    <div class="w-100" style="max-width: 400px;">
        <!-- Scrollable Content Section with Cards -->
        <div class="scrollable-content p-3">
            <div class="container-fluid">
                <div class="mb-5">
                    <!-- Tampilkan Nama Pengguna dan Tanggal -->
                    <h4>Hello, {{ '@' . $user->profile->username }}, today is the best day for exercise!</h4>
                    <p>{{ $currentDate }}</p>
                </div>

                <figure class="text-center">
                    <blockquote class="blockquote">
                        <p>&ldquo;{{ $randomQuote }}&rdquo;</p>
                    </blockquote>
                    <figcaption class="blockquote-footer">
                        <cite title="Source Title">Pilates Wisdom</cite>
                    </figcaption>
                </figure>           
            </div>
        </div>        
    </div>
@endsection
