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
                <span class="input-group-text" id="date-filter" style="width: 40px; display: flex; justify-content: center; align-items: center;"><i class="fas fa-calendar"></i></span>
                <input type="text" id="datePicker" class="form-control" placeholder="Select Date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" aria-describedby="date-filter">
            </div>

            <!-- Group Dropdown -->
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1" style="width: 40px; display: flex; justify-content: center; align-items: center;"><i class="fas fa-users"></i></span>
                <select id="groupFilter" class="form-control" aria-describedby="basic-addon1">
                    <option value="All">All Groups</option>
                    @foreach($lessonTypes as $lessonType)
                        <option value="{{ $lessonType->name }}">{{ $lessonType->name }}</option>
                    @endforeach
                </select>
            </div>
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
                                            {{ $lessonSchedule->status ?? 'N/A' }}
                                        @else
                                            Quota {{ $lessonSchedule->quota ?? 'N/A' }}
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

                                        @if(in_array($lessonSchedule->id, $userBookings))
                                            {{-- Tombol disabled jika sudah booking --}}
                                            <button class="btn btn-primary btn-sm" title="Already Booked" disabled>
                                                <i class="fas fa-fw fa-user-check"></i>
                                            </button>
                                        @elseif($currentDateTime->greaterThanOrEqualTo($lessonStartTime))
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
            // Inisialisasi DatePicker dengan default tanggal hari ini
            $('#datePicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,  // Agar hari ini di highlight
                startDate: Date(),
            }).on('changeDate', function(e) {
                filterTable(); // Panggil filter saat tanggal berubah
            });

            // Event Listener untuk perubahan pada Group Filter
            $('#groupFilter').on('change', function() {
                filterTable(); // Panggil filter saat group berubah
            });

            // Panggil filter saat halaman pertama kali dimuat dengan nilai default
            filterTable();

            function filterTable() {
                const selectedDate = $('#datePicker').datepicker('getFormattedDate'); // Ambil tanggal yang dipilih
                const selectedGroup = $('#groupFilter').val(); // Ambil nilai group yang dipilih dari dropdown

                let visibleRows = 0; // Counter untuk baris yang terlihat

                // Loop melalui setiap baris <tr> di tabel
                $('#tbl_list tbody tr').each(function() {
                    const rowDate = $(this).data('date'); // Ambil nilai data-date dari <tr>
                    const rowGroup = $(this).data('group'); // Ambil nilai data-group dari <tr>

                    // Cek apakah ini placeholder "Tidak ada lesson"
                    if ($(this).attr('id') === 'noLessonPlaceholder') {
                        return; // Skip baris placeholder
                    }

                    // Check apakah baris cocok dengan tanggal yang dipilih dan group yang dipilih
                    const dateMatch = selectedDate === '' || rowDate === selectedDate;
                    const groupMatch = selectedGroup === 'All' || rowGroup === selectedGroup;

                    // Jika cocok, tampilkan baris, jika tidak, sembunyikan
                    if (dateMatch && groupMatch) {
                        $(this).show();
                        visibleRows++; // Tambahkan counter untuk baris yang terlihat
                    } else {
                        $(this).hide();
                    }
                });

                // Jika tidak ada baris yang cocok, tampilkan placeholder
                if (visibleRows === 0) {
                    $('#noLessonPlaceholder').show(); // Tampilkan placeholder
                } else {
                    $('#noLessonPlaceholder').hide(); // Sembunyikan placeholder jika ada data yang cocok
                }
            }
        });
    </script>
@endpush
