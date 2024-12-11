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

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Lesson Code</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Coach</th>
                                <th>Participants</th>
                                <th>Total Participants</th>
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
                                    <td>{{ $schedule->participants_count }}</td> <!-- Tampilkan jumlah peserta -->
                                </tr>
                            @endforeach
                        </tbody>

                        {{-- <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">Time/Date</th>
                                @php
                                    $processedDates = [];
                                @endphp

                                @foreach ($lessonSchedules as $schedule)
                                    @php
                                        $day = \Carbon\Carbon::parse($schedule->date)->format('D');
                                    @endphp

                                    @if (!in_array($schedule->date, $processedDates))
                                        <th>{{ $day }}</th>
                                        @php
                                            $processedDates[] = $schedule->date;
                                        @endphp
                                    @endif
                                @endforeach
                            </tr>
                            <tr>                                
                                @foreach ($processedDates as $date)
                                    <th>{{ \Carbon\Carbon::parse($date)->format('d M') }}</th>
                                @endforeach
                            </tr>
                        </thead> --}}
                        {{-- <tbody>
                            @foreach ($lessonSchedules->groupBy('timeSlot.start_time') as $timeSlot => $schedules)
                                <tr>
                                    <th rowspan="2" style="border-right: 2px solid #dee2e6">{{ \Carbon\Carbon::parse($timeSlot)->format('H:i') }}</th>

                                    Row pertama untuk Coach
                                    @foreach ($processedDates as $date)
                                        @php
                                            $scheduleForDate = $schedules->firstWhere('date', $date);
                                        @endphp

                                        <td class="table-danger">
                                            <strong>{{ $scheduleForDate ? $scheduleForDate->coach->name : 'TBA / No class' }}</strong>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    Row kedua untuk Participant
                                    @foreach ($processedDates as $date)
                                        @php
                                            $scheduleForDate = $schedules->firstWhere('date', $date);
                                        @endphp

                                        <td class="align-middle">
                                            @if ($scheduleForDate && $scheduleForDate->bookings->isNotEmpty())
                                                <table class="table" style="margin: 0;">
                                                    <tbody>
                                                        @foreach ($scheduleForDate->bookings as $booking)
                                                            <tr>
                                                                <td>{{ $booking->user ? $booking->user->name : $booking->booked_by_name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span>No participants / No class</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
