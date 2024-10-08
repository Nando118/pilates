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
                        @if($isCoach)
                            <strong>This Month's Lessons You Teach</strong> <!-- Judul untuk coach -->
                        @else
                            <strong>This Month's Lessons You Booked</strong> <!-- Judul untuk client -->
                        @endif
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($myLessons->isEmpty())
                            <li class="list-group-item">
                                @if($isCoach)
                                    You have no lessons to teach this month.
                                @else
                                    You have no lessons booked this month.
                                @endif
                            </li>
                        @else
                            @foreach ($myLessons as $myLesson)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <!-- Kolom Jam -->
                                    <div class="me-3">
                                        <span class="badge bg-primary">
                                            @if($isCoach)
                                                {{ date('H:i', strtotime($myLesson->timeSlot->start_time ?? 'N/A')) }} - 
                                                {{ date('H:i', strtotime($myLesson->timeSlot->end_time ?? 'N/A')) }}
                                            @else
                                                {{ date('H:i', strtotime($myLesson->lessonSchedule->timeSlot->start_time ?? 'N/A')) }} - 
                                                {{ date('H:i', strtotime($myLesson->lessonSchedule->timeSlot->end_time ?? 'N/A')) }}
                                            @endif
                                        </span>
                                        <br>
                                        <span style="font-size: 0.7rem">
                                            <strong>{{ \Carbon\Carbon::parse($myLesson->date)->translatedFormat('D, d-M-y') }}</strong><br>
                                        </span>
                                    </div>

                                    <!-- Kolom Detail -->
                                    <div class="flex-grow-1">
                                        @if($isCoach)
                                            <strong>{{ $myLesson->lesson->name ?? 'N/A' }}</strong> / 
                                            <span>{{ $myLesson->lessonType->name ?? 'N/A' }}</span><br>                                            
                                            Room: <strong>{{ $myLesson->room->name ?? 'N/A' }}</strong>
                                        @else
                                            <strong>{{ $myLesson->lessonSchedule->lesson->name ?? 'N/A' }}</strong> / 
                                            <span>{{ $myLesson->lessonSchedule->lessonType->name ?? 'N/A' }}</span><br>                                            
                                            <em>Instructor: <strong>{{ $myLesson->lessonSchedule->user->name ?? 'N/A' }}</strong></em><br>
                                            Room: <strong>{{ $myLesson->lessonSchedule->room->name ?? 'N/A' }}</strong>
                                        @endif
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
