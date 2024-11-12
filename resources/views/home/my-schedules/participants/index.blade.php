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
        <!-- Scrollable Content Section -->
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <div class="mb-5 px-3 pt-3">
                    <h3>{{ $lessonSchedule->lesson->name ?? 'Lesson' }} - {{ $lessonSchedule->lessonType->name ?? 'Type' }}</h3>
                    <p><strong>Lesson Code:</strong> {{ $lessonSchedule->lesson_code }}</p>
                    <p><strong>Date:</strong> {{ $lessonSchedule->date }}</p>
                    <p><strong>Time:</strong> {{ date('H:i', strtotime($lessonSchedule->timeSlot->start_time)) }}</p>
                    <p><strong>Coach:</strong> {{ $lessonSchedule->user->name }}</p>
                </div>
                
                <!-- List of Participants -->
                <div class="container-fluid">
                    <h5>List Participants</h5>
                    @if($bookings->isEmpty())
                        <p>No participants have booked for this lesson schedule.</p>
                    @else
                        <ul class="list-group">
                            @foreach($bookings as $booking)
                                <li class="list-group-item">
                                    <strong>{{ $booking->user->name }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="d-flex justify-content-around">                    
                    <a class="btn btn-warning w-25" href="{{ route('my-schedules.index') }}" role="button"><strong>Back</strong></a>
                </div>
            </div>                    
        </div>  
    </div>
@endsection
