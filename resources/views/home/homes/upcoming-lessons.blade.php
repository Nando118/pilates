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
                <div class="mb-4">                    
                    <p class="h3">Hi,</p>
                    <p class="h3"><em>{{ $user->name }}</em></p>
                </div>
                
                <div class="mb-4">
                    <p class="h3" style="font-size: 1rem;"><strong>Available Credits</strong></p>
                    <p class="h3"><strong style="font-size: 1.5rem; margin-right: 0.5rem;">{{ $user->credit_balance }}</strong><span style="font-size: 1rem;">Left</span></p>
                </div>

                <div class="mb-4">
                    <a class="btn btn-dark rounded-pill" href="{{ route('home') }}" role="button"><strong>Upcoming Lessons</strong></a>
                    <a class="btn btn-outline-dark rounded-pill" href="{{ route('pastLessons') }}" role="button"><strong>Past Lessons</strong></a>
                </div>
                
                <div class="card text-bg-dark">
                    <div class="card-header">
                        <strong>Upcoming lessons</strong>
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($myLessons->isEmpty())
                            <li class="list-group-item">
                                @if($isCoach)
                                    There are no lessons you teach this month.
                                @else
                                    There are no lessons you have booked this month.
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
                                            <em>Lesson Code: <br><strong>{{ $myLesson->lesson_code ?? 'N/A' }}</strong></em>
                                        @else
                                            <strong>{{ $myLesson->lessonSchedule->lesson->name ?? 'N/A' }}</strong> / 
                                            <span>{{ $myLesson->lessonSchedule->lessonType->name ?? 'N/A' }}</span><br>                                            
                                            <em>Instructor: <strong>{{ $myLesson->lessonSchedule->user->name ?? 'N/A' }}</strong></em><br>
                                            <em>Lesson Code: <br><strong>{{ $myLesson->lessonSchedule->lesson_code ?? 'N/A' }}</strong></em>
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
