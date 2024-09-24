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
                <div class="card mb-3">
                    <div class="card-header">HOMES</div>
                    <div class="card-body">
                        <p>{{ $users->name }}</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                        <p>This is some text within a card body.</p>
                    </div>
                </div>                
            </div>
        </div>        
    </div>
@endsection
