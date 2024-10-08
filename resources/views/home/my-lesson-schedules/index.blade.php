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
        <!-- Filter Date and Group -->
        <div class="mb-3 px-3 pt-3">
            <!-- Datepicker -->
            <div class="input-group mb-3">
                <span class="input-group-text" id="date-filter" style="width: 40px; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-calendar"></i>
                </span>
                <input type="text" id="datePicker" class="form-control" placeholder="Select Date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" aria-describedby="date-filter">
            </div>

            <!-- Group Dropdown -->
            <div class="input-group mb-3">
                <span class="input-group-text" id="group-filter" style="width: 40px; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-users"></i>
                </span>
                <select id="groupFilter" class="form-control" aria-describedby="group-filter">
                    <option value="All">All Groups</option>
                    @foreach($lessonTypes as $lessonType)
                        <option value="{{ $lessonType->name }}">{{ $lessonType->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Scrollable Content Section -->
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <p class="fs-5"><strong>My Schedules</strong></p>
                @if($myBookings->isEmpty())
                    <p>You have no bookings yet.</p>
                @else
                    <table id="tbl_list" width="100%" style="font-size: 0.8rem">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Lesson</th>
                                <th>Booking At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Placeholder jika data tidak ditemukan -->
                            <tr id="noLessonPlaceholder" style="display: none;">
                                <td colspan="4" class="text-center pt-2">There are no bookings matching the filter.</td>
                            </tr>

                            @foreach($myBookings as $booking)
                                @php
                                    // Mendapatkan tanggal pelajaran dan kategori dari booking
                                    $lessonDate = $booking->lessonSchedule->date ?? 'N/A';
                                    $group = ucfirst($booking->lessonSchedule->lessonType->name ?? 'N/A'); // Ambil nama group
                                @endphp
                                <!-- Sesuaikan data-date dan data-group untuk filter -->
                                <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                                    <td>
                                        <strong>{{ $lessonDate }}</strong><br>
                                        {{ date('H:i', strtotime($booking->lessonSchedule->timeSlot->start_time ?? 'N/A')) }}
                                    </td>
                                    <td>
                                        <strong>{{ ucfirst($booking->lessonSchedule->lesson->name ?? 'N/A') }} / {{ ucfirst($booking->lessonSchedule->lessonType->name ?? 'N/A') }}</strong><br>
                                        {{ ucfirst($booking->lessonSchedule->user->name ?? 'N/A') }}
                                    </td>
                                    <td>
                                        <strong>{{ $booking->created_at ? $booking->created_at->format('d-m-Y') : 'N/A' }}</strong><br>
                                        {{ $booking->created_at ? $booking->created_at->format('H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        @php
                                            // Mendapatkan waktu mulai pelajaran
                                            $lessonStartTime = \Carbon\Carbon::parse($booking->lessonSchedule->date . ' ' . $booking->lessonSchedule->timeSlot->start_time);
                                            $currentDateTime = now();
                                        @endphp

                                        @if($currentDateTime->greaterThanOrEqualTo($lessonStartTime))
                                            <button class="btn btn-danger btn-sm" title="Cannot Delete" disabled>
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('my-lesson-schedules.delete', ["bookings" => $booking->id]) }}" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DatePicker
            $('#datePicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                startDate: Date(),
            }).on('changeDate', function() {
                filterTable(); // Panggil filter saat tanggal berubah
            });

            // Event Listener untuk Group Filter
            $('#groupFilter').on('change', function() {
                filterTable(); // Panggil filter saat group berubah
            });

            // Panggil filter saat halaman pertama kali dimuat
            filterTable();

            function filterTable() {
                const selectedDate = $('#datePicker').datepicker('getFormattedDate');
                const selectedGroup = $('#groupFilter').val();
                let visibleRows = 0;

                // Loop melalui setiap baris <tr> di tabel
                $('#tbl_list tbody tr').each(function() {
                    const rowDate = $(this).data('date');
                    const rowGroup = $(this).data('group');

                    // Cek apakah ini placeholder "Tidak ada lesson"
                    if ($(this).attr('id') === 'noLessonPlaceholder') {
                        return; // Skip baris placeholder
                    }

                    // Cek kecocokan tanggal dan group
                    const dateMatch = selectedDate === '' || rowDate === selectedDate;
                    const groupMatch = selectedGroup === 'All' || rowGroup === selectedGroup;

                    // Jika cocok, tampilkan baris
                    if (dateMatch && groupMatch) {
                        $(this).show();
                        visibleRows++;
                    } else {
                        $(this).hide();
                    }
                });

                // Jika tidak ada baris yang cocok, tampilkan placeholder
                if (visibleRows === 0) {
                    $('#noLessonPlaceholder').show();
                } else {
                    $('#noLessonPlaceholder').hide();
                }
            }
        });
    </script>
@endpush
