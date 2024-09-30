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
                    <p class="h5">Hi <em>{{ '@' . $user->profile->username }}</em>, today is the best day for exercise!</p>
                    <p class="lead">{{ $currentDate }}</p>
                </div>

                <figure class="text-center mb-5">
                    <blockquote class="blockquote">
                        <p>&ldquo;{{ $randomQuote }}&rdquo;</p>
                    </blockquote>
                    <figcaption class="blockquote-footer">
                        <cite title="Source Title">Pilates Wisdom</cite>
                    </figcaption>
                </figure>    
                
                <div class="card text-bg-dark">
                    <div class="card-header">
                        <strong>Today Lessons</strong>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($myBookings->isEmpty())
                            <li class="list-group-item">There is no lesson today</li>                            
                        @else
                            @foreach ($myBookings as $myBooking)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <!-- Kolom Jam -->
                                    <div class="me-3">
                                        <span class="badge bg-primary">
                                            {{ date('H:i', strtotime($myBooking->lessonSchedule->timeSlot->start_time ?? 'N/A')) }} - 
                                            {{ date('H:i', strtotime($myBooking->lessonSchedule->timeSlot->end_time ?? 'N/A')) }}
                                        </span>
                                    </div>

                                    <!-- Kolom Detail -->
                                    <div class="flex-grow-1">
                                        <strong>{{ $myBooking->lessonSchedule->lesson->name ?? 'N/A' }}</strong> / 
                                        <span>{{ $myBooking->lessonSchedule->lessonType->name ?? 'N/A' }}</span><br>
                                        <em>Instructor: <strong>{{ $myBooking->lessonSchedule->user->name ?? 'N/A' }}</strong></em><br>
                                        Room: <strong>{{ $myBooking->lessonSchedule->room->name ?? 'N/A' }}</strong>
                                    </div>
                                </li>
                            @endforeach
                        @endif                                    
                    </ul>
                </div>
            </div>
        </div>        
    </div>
@endsection
