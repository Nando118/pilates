<!-- resources/views/dashboard/reports/weekly.blade.php -->
@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Weekly Report</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Weekly Report</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.export.weekly') }}" method="GET">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <button type="submit" class="btn btn-success">Export Weekly Report</button>
                </form>

                <div class="mt-4">
                    <h4>Report from {{ \Carbon\Carbon::parse($startDate)->format('D, d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('D, d M Y') }}</h4>

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Lesson Code</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Coach</th>
                                <th>Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lessonSchedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->lesson_code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->format('D, d M Y') }}</td>
                                    <td>{{ date("H:i", strtotime($schedule->timeSlot->start_time)) }} - {{ date("H:i", strtotime($schedule->timeSlot->end_time)) }}</td>
                                    <td>{{ $schedule->coach ? $schedule->coach->name : 'N/A' }}</td>
                                    <td>
                                        @if ($schedule->bookings->isEmpty())
                                            No Participants
                                        @else
                                            @foreach ($schedule->bookings as $booking)
                                                {{ $booking->user ? $booking->user->name : $booking->booked_by_name }}<br>
                                            @endforeach
                                        @endif                                  
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
