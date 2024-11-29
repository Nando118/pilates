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

        .input-group {
            margin-bottom: 15px; /* Tambahkan jarak di bawah elemen input group */
        }

        #dateButtonsContainer {
            display: flex;
            overflow-x: auto; /* Untuk mengaktifkan scroll horizontal */
            white-space: nowrap; /* Mencegah tombol wrap ke baris baru */
            -webkit-overflow-scrolling: touch; /* Mendukung scrolling yang halus di perangkat sentuh */
            scrollbar-width: none; /* Menghilangkan scrollbar di Firefox */
            margin-bottom: 15px; /* Tambahkan jarak di bawah container tombol */
        }

        #dateButtonsContainer::-webkit-scrollbar {
            display: none; /* Menghilangkan scrollbar di WebKit (Chrome, Safari) */
        }

        .date-button {
            flex: 0 0 auto; /* Pastikan tombol tidak mengecil */
            width: 60px; /* Ukuran yang sama untuk setiap tombol */
            height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Rata tengah horizontal */
            justify-content: center; /* Rata tengah vertikal */
            text-align: center; /* Pusatkan teks dalam tombol */
            line-height: 1.2; /* Jarak antar baris teks dalam tombol */
            font-size: 0.9rem; /* Sesuaikan ukuran font */
            margin: 5px; /* Margin antar tombol */
            padding: 0; /* Hapus padding default */
        }
    </style>
@endpush

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <!-- Filter Date and Group -->
        <div class="mb-3 px-3 pt-3">
            <p class="fs-5"><strong>Filter By:</strong></p>
            <!-- Group Dropdown -->
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1" style="width: 40px; display: flex; justify-content: center; align-items: center;"><i class="fas fa-users"></i></span>
                <select id="groupFilter" class="form-control" aria-describedby="basic-addon1">
                    <option value="All">All Groups</option>
                    @foreach($lessonTypes as $lessonType)
                        <option value="{{ $lessonType->name }}">{{ $lessonType->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Buttons --> 
            <div class="d-flex overflow-auto" id="dateButtonsContainer"></div>            
        </div>

        <!-- Scrollable Content Section -->
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <p class="fs-5"><strong>My Schedules</strong></p>
                @if($lessonScheduleDatas->isEmpty())
                    <p>You have no schedules yet.</p>
                @else
                    <table id="tbl_list" width="100%" style="font-size: 0.8rem">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Lesson</th>                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Placeholder jika data tidak ditemukan -->
                            <tr id="noLessonPlaceholder" style="display: none;">
                                <td colspan="4" class="text-center pt-2">There are no bookings matching the filter.</td>
                            </tr>

                            @foreach($lessonScheduleDatas as $lessonSchedule)
                                @php
                                    // Mendapatkan tanggal pelajaran dan kategori dari booking
                                    $lessonDate = $lessonSchedule->date ?? 'N/A';
                                    $group = ucfirst($lessonSchedule->lessonType->name ?? 'N/A'); // Ambil nama group
                                @endphp
                                <!-- Sesuaikan data-date dan data-group untuk filter -->
                                <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                                    <td>
                                        <strong>{{ $lessonDate }}</strong><br>
                                        {{ date('H:i', strtotime($lessonSchedule->timeSlot->start_time ?? 'N/A')) }}
                                    </td>
                                    <td>
                                        <strong>{{ ucfirst($lessonSchedule->lesson->name ?? 'N/A') }} / {{ ucfirst($lessonSchedule->lessonType->name ?? 'N/A') }}</strong><br>
                                        {{ ucfirst($lessonSchedule->user->name ?? 'N/A') }} <br>
                                        {{ ucfirst($lessonSchedule->lesson_code ?? 'N/A') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('my-schedules.view', ["lessonSchedule" => $lessonSchedule->id]) }}" class="btn btn-primary btn-sm" title="View Participants">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
            // Generate Date Buttons
            function generateDateButtons() {
                const container = $('#dateButtonsContainer');
                const startDate = new Date(); // Hari ini

                for (let i = 0; i < 30; i++) {
                    const date = new Date();
                    date.setDate(startDate.getDate() + i);

                    // Mengambil tanggal dan hari
                    const day = date.getDate();
                    const weekday = date.toLocaleDateString('en-EN', { weekday: 'short' });

                    // Membuat tombol dengan format yang diinginkan
                    const button = `
                        <button class="btn btn-outline-dark m-1 date-button" data-date="${date.toISOString().split('T')[0]}">
                            <strong>
                                <div>${day}</div>
                                <div>${weekday}</div>
                            </strong>
                        </button>`;
                    
                    container.append(button);
                }
            }

            // Highlight Selected Button
            function highlightSelectedButton(selectedDate) {
                $('.date-button').removeClass('active');
                $(`.date-button[data-date="${selectedDate}"]`).addClass('active');
            }

            // Filter Table
            function filterTable() {
                const selectedDate = $('.date-button.active').data('date');
                const selectedGroup = $('#groupFilter').val();

                let visibleRows = 0;

                $('#tbl_list tbody tr').each(function() {
                    const rowDate = $(this).data('date');
                    const rowGroup = $(this).data('group');

                    if ($(this).attr('id') === 'noLessonPlaceholder') {
                        return;
                    }

                    const dateMatch = selectedDate === undefined || rowDate === selectedDate;
                    const groupMatch = selectedGroup === 'All' || rowGroup === selectedGroup;

                    if (dateMatch && groupMatch) {
                        $(this).show();
                        visibleRows++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleRows === 0) {
                    $('#noLessonPlaceholder').show();
                } else {
                    $('#noLessonPlaceholder').hide(); 
                }
            }

            // Initial Setup
            generateDateButtons();
            $('.date-button').first().addClass('active'); // Set the first button as active
            filterTable();

            // Event Listeners
            $(document).on('click', '.date-button', function() {
                highlightSelectedButton($(this).data('date'));
                filterTable();
            });

            $('#groupFilter').on('change', function() {
                filterTable();
            });
        });
    </script>
@endpush
