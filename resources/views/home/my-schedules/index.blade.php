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

        .table td,
        .table th {
            padding: 10px; /* Tambahkan jarak padding */
            vertical-align: middle; /* Posisi vertikal rata tengah */
            text-align: left; /* Teks rata kiri */
            white-space: nowrap; /* Hindari teks membungkus */
            background-color: transparent; /* Pastikan latar belakang sel transparan */
        }

        .table tr {
            border: none; /* Garis bawah antar baris */
            background-color: transparent; /* Pastikan latar belakang baris transparan */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: transparent; /* Menghapus latar belakang baris ganjil */
        }

        .table {
            margin-bottom: 20px; /* Jarak di bawah tabel */
        }
    </style>
@endpush

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <!-- Filter Date and Group -->
        <div class="mb-3 px-3 pt-3">
            <p class="fs-5"><strong>Filter By:</strong></p>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1" style="width: 40px; display: flex; justify-content: center; align-items: center;"><i class="fas fa-users"></i></span>
                <select id="groupFilter" class="form-control" aria-describedby="basic-addon1">
                    <option value="All">All Groups</option>
                    @foreach($lessonTypes as $lessonType)
                        <option value="{{ $lessonType->name }}">{{ $lessonType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex overflow-auto" id="dateButtonsContainer"></div>
        </div>
        
        <!-- Scrollable Content Section -->
        <div class="scrollable-content pb-3">
            <div class="container-fluid">
                <p class="fs-5"><strong>My Schedules</strong></p>
                <table id="tbl_list" class="table table-striped" width="100%">
                    @include('home.my-schedules.partials.schedule-table', [
                        "lessonScheduleDatas" => $lessonScheduleDatas,
                        "lessonTypes" => $lessonTypes
                    ])
                </table>
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
                const startDate = moment.tz("Asia/Jakarta");  // Menggunakan zona waktu Jakarta

                for (let i = 0; i < 30; i++) {
                    const date = startDate.clone().add(i, 'days');
                    const day = date.date();
                    const weekday = date.format('ddd');  // Menggunakan format singkat untuk hari

                    const button = `
                        <button class="btn btn-outline-dark m-1 date-button" data-date="${date.format('YYYY-MM-DD')}">
                            <strong>
                                <div>${day}</div>
                                <div>${weekday}</div>
                            </strong>
                        </button>`;

                    container.append(button);
                }
            }

            function highlightSelectedButton(selectedDate) {
                $('.date-button').removeClass('active');
                $(`.date-button[data-date="${selectedDate}"]`).addClass('active');
            }

            function filterTable() {
                const selectedDate = $('.date-button.active').data('date');
                const selectedGroup = $('#groupFilter').val();

                $.ajax({
                    url: "{{ route('user-lesson-schedules.index') }}",
                    method: "GET",
                    data: {
                        date: selectedDate,  // Kirim tanggal dengan format yang sudah dikonversi
                        group: selectedGroup
                    },
                    success: function(data) {
                        $('#tbl_list tbody').html(data);
                    },
                    error: function() {
                        console.error("Error fetching data.");
                    }
                });
            }

            // Initialize filters and buttons
            generateDateButtons();
            $('.date-button').first().addClass('active');
            filterTable();

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
