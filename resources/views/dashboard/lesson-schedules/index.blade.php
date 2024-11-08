@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Lesson Schedules</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lesson Schedules</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>
                                <th>Lesson Code</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lesson</th>                                
                                <th>Quota</th>
                                <th>Credit Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('lesson-schedules.create') }}" class="btn btn-success">Add Lesson Schedule</a>
            </div>
        </div>
    </div>

    <!-- Modal HTML -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Add Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" method="POST">
                        @csrf
                        <div id="nameFields">
                            <!-- Nama fields akan ditambahkan di sini dengan JavaScript -->
                        </div>
                        <input type="hidden" name="lesson_schedule_id" id="lessonScheduleId">
                        <button type="button" class="btn btn-secondary" id="addNameField">Add Participant Field</button>
                        <span id="fieldCountMessage" class="ml-2"></span>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitBooking">Save Booking</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push("scripts")
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lesson-schedules.data') }}',
                language: {
                    zeroRecords: "There is no lesson schedule data yet",
                },
                columns: [
                    {
                        data: null,
                        name: 'no',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            // Menampilkan nomor index (incremented by 1) pada setiap baris
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        className: 'align-middle'
                    },
                    { data: 'lesson_code', name: 'lesson_code', className: 'align-middle'},
                    { data: 'date', name: 'date', render: DataTable.render.date(), className: 'align-middle'},                  
                    { data: 'time', name: 'time', className: 'align-middle'},
                    { data: 'lesson', name: 'lesson', className: 'align-middle'},                    
                    { data: 'quota', name: 'quota', className: 'align-middle'},
                    { data: 'credit_price', name: 'credit_price', className: 'align-middle'},
                    { data: 'status', name: 'status', className: 'align-middle'},
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                order: [
                    [2, 'desc'],                  
                    [3, 'desc']                    
                ],
            });

            // Event handler untuk tombol Add Booking
            // Inisialisasi variabel untuk menghitung jumlah field yang ditambahkan
            let addedFieldsCount = 0;
            let maxQuota;

            $(document).on("click", ".add-booking-btn", function (e) {
                e.preventDefault();
                
                var lessonScheduleId = $(this).data("id");
                maxQuota = $(this).data("quota");

                $("#nameFields").empty();
                $("#lessonScheduleId").val(lessonScheduleId);
                addedFieldsCount = 0; // Reset jumlah field saat modal dibuka

                $("#bookingModal").modal("show");
                updateFieldCountMessage();
            });

            $("#addNameField").on("click", function () {
                if (addedFieldsCount < maxQuota) {
                    addedFieldsCount++;
                    $("#nameFields").append(`
                        <div class="form-group">
                            <label for="name${addedFieldsCount}">Participant ${addedFieldsCount}</label>
                            <input type="text" class="form-control" id="name${addedFieldsCount}" name="names[]" required>
                        </div>
                    `);
                    updateFieldCountMessage();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum quota reached!',
                        text: 'You cannot add more fields than the maximum quota.',
                    });
                }
            });

            $("#submitBooking").on("click", function () {
                $.ajax({
                    url: '/bookings/store',
                    method: "POST",
                    data: $("#bookingForm").serialize(),
                    success: function (response) {
                        $("#bookingModal").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking saved successfully!',
                        });
                        $('#tbl_list').DataTable().ajax.reload(); // Reload data table after success
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while saving the booking.',
                        });
                    }
                });
            });

            function updateFieldCountMessage() {
                $("#fieldCountMessage").text(`Fields added: ${addedFieldsCount}/${maxQuota}`);
            }
        });
    </script>
@endpush
