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
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <div class="p-3">
                    <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                        @csrf

                        <input type="hidden" name="id" value="{{ $lessonDetails->id }}">

                        <div class="card mb-4">
                            <div class="card-header text-bg-dark">
                                <strong>Lesson Details</strong>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ $lessonDetails->lesson->name }}</strong> with coach <strong>{{ $lessonDetails->user->name }}</strong></p>
                                <p>Lesson Code: {{ $lessonDetails->lesson_code }}</p>
                                <p>Lesson Type: {{ $lessonDetails->lessonType->name }}</p>
                                <p>Date: {{ date('d-m-Y', strtotime($lessonDetails->date)) }}</p>
                                <p>Time:  {{ date('H:i', strtotime($lessonDetails->timeSlot->start_time)) }} - {{ date('H:i', strtotime($lessonDetails->timeSlot->end_time)) }}</p>
                                <p>Duration: {{ $lessonDetails->timeSlot->duration }} Minute</p>                                
                                <p>Available Quota: {{ $lessonDetails->quota }} Person</p>
                                <p>Credit Price: {{ $lessonDetails->credit_price }}</p>
                            </div>
                        </div>                        
                        <div class="d-flex justify-content-around">                            
                            <button id="btn_submit" class="btn btn-success w-25" type="submit"><strong>Booking</strong></button>
                            <a class="btn btn-warning w-25" href="{{ route('user-lesson-schedules.index') }}" role="button"><strong>Cancel</strong></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

