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

        <!-- Scrollable Content Section with Cards -->
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <p class="fs-5"><strong>Lesson Schedules</strong></p>
                <table id="tbl_list" class="" width="100%">
                    <tbody style="font-size: 0.8rem">
                        <!-- Placeholder jika data tidak ditemukan -->
                        <tr id="noLessonPlaceholder" style="display: none;">
                            <td colspan="4" class="text-center">There are no lessons scheduled at this time.</td>
                        </tr>

                        @foreach ($lessonScheduleDatas as $lessonSchedule)
                            @php
                                // Mengambil nilai date dari model lessonSchedule
                                $lessonDate = $lessonSchedule->date ?? 'N/A';
                                $group = ucfirst(optional($lessonSchedule->lessonType)->name ?? 'N/A'); // Ambil nama group
                            @endphp
                            <!-- Sesuaikan data-date untuk filter -->
                            <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                                <td>
                                    <strong>{{ date("H:i", strtotime(optional($lessonSchedule->timeSlot)->start_time ?? 'N/A')) }}</strong><br>{{ optional($lessonSchedule->timeSlot)->duration ?? 0 }} Min
                                </td>
                                <td>
                                    <strong>
                                        {{ ucfirst(optional($lessonSchedule->lesson)->name ?? 'N/A') }} / {{ ucfirst(optional($lessonSchedule->lessonType)->name ?? 'N/A') }}
                                    </strong>
                                    <br>{{ ucfirst(optional($lessonSchedule->user)->name ?? 'N/A') }}
                                    <br>{{ $lessonSchedule->lesson_code ?? 'N/A' }}
                                </td>
                                <td>
                                    <strong>
                                        @if ($lessonSchedule->quota <= 0)
                                            Full Booking
                                        @else
                                            Quota {{ $lessonSchedule->quota }}
                                        @endif
                                    </strong>
                                    <br>
                                    Credit Price {{ $lessonSchedule->credit_price ?? 'N/A' }}
                                </td>
                                @can('access-client-menu')
                                    <td class="text-center">
                                        @php
                                            // Mendapatkan waktu mulai pelajaran
                                            $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->timeSlot->start_time);
                                            $currentDateTime = now();
                                        @endphp

                                        @if ($lessonSchedule->deleted_at)
                                            {{-- Tampilkan keterangan jika lesson sudah dihapus --}}
                                            <span class="badge bg-danger">Canceled</span>
                                        @elseif (in_array($lessonSchedule->id, $userBookings))
                                            {{-- Tombol disabled jika sudah booking --}}
                                            <button class="btn btn-primary btn-sm" title="Already Booked" disabled>
                                                <i class="fas fa-fw fa-user-check"></i>
                                            </button>
                                        @elseif ($currentDateTime->greaterThanOrEqualTo($lessonStartTime))
                                            {{-- Tombol disabled jika waktu pelajaran sudah mulai --}}
                                            <button class="btn btn-primary btn-sm" title="Cannot Book" disabled>
                                                <i class="fas fa-fw fa-user-plus"></i>
                                            </button>
                                        @else
                                            {{-- Tombol aktif jika belum booking dan waktu belum lewat --}}
                                            <a href="{{ route('user-lesson-schedules.create', ['bookings' => $lessonSchedule->id]) }}" class="btn btn-primary btn-sm" title="Booking">
                                                <i class="fas fa-fw fa-user-plus"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endcan
                                @can('access-coach-menu')
                                    <td class="text-center">
                                        @if ($lessonSchedule->deleted_at)
                                            {{-- Tampilkan keterangan jika lesson sudah dihapus --}}
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            @php
                                                // Mendapatkan waktu mulai pelajaran
                                                $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->timeSlot->start_time);
                                                $currentDateTime = now();
                                            @endphp

                                            @if ($currentDateTime->greaterThanOrEqualTo($lessonStartTime))
                                                {{-- Jika waktu sudah lewat, tampilkan "Not Available" --}}
                                                <span class="badge bg-secondary">Not Available</span>
                                            @elseif ($lessonSchedule->quota <= 0)
                                                {{-- Jika kuota habis, tampilkan "Full Booking" --}}
                                                <span class="badge bg-info">Full Booking</span>
                                            @else
                                                {{-- Jika kuota masih tersedia dan waktu belum lewat --}}
                                                <span class="badge bg-success">Available</span>
                                            @endif
                                        @endif
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
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
