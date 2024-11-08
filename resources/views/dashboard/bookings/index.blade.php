@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Bookings</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bookings</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                
                <div class="row mb-4">
                    <div class="col d-flex justify-content-between align-items-center">
                        <div>
                            <form method="GET" id="filter-form">
                                <div class="form-row align-items-center justify-content-center">
                                    <div class="col-auto">
                                        <label class="mr-sm-2">Date:</label>                                        
                                        <div class="input-group date" data-provide="datepicker">
                                            <input type="text" class="form-control" id="date" name="date" autocomplete="off" placeholder="{{ now()->format('Y/m/d') }}">
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-th"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <label class="mr-sm-2">Time:</label>
                                        <select class="form-control" id="time" name="time_slot_id"> <!-- Ubah name menjadi time_slot_id -->
                                            <option value="">All Times</option> <!-- Tambahkan opsi untuk semua waktu -->
                                            @foreach ($timeSlots as $timeSlot)                                            
                                                <option value="{{ $timeSlot->id }}">{{ date("H:i", strtotime($timeSlot->start_time)) }}</option>
                                            @endforeach
                                        </select>                                        
                                    </div>
                                    <div class="col-auto btn-list align-self-end">
                                        <button type="submit" id="cari" class="btn btn-info"><i class="fa fa-search mr-1"></i> Filter</button>                                        
                                    </div>
                                </div>
                            </form>
                        </div>                        
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>                                
                                <th>Name</th>
                                <th>Phone</th>                                
                                <th>Lesson Code</th>                              
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#tbl_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('bookings.data') }}',
                    data: function (d) {
                        d.date = $('#date').val(); // Kirim nilai filter date ke server
                        d.time_slot_id = $('#time').val(); // Kirim nilai filter time_slot_id ke server
                    }
                },
                language: {
                    zeroRecords: "There is no booking data yet",
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
                    { data: 'booked_by_name', name: 'booked_by_name', className: 'align-middle'},
                    { data: 'phone', name: 'phone', className: 'align-middle'},                    
                    { data: 'lesson_code', name: 'lesson_code', className: 'align-middle'},                    
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                order: [
                    [3, 'desc']
                ],
            });

            // Initialize datepicker
            $('.date').datepicker({
                format: 'yyyy/mm/dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });

            // Handle filter form submit
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload(); // Reload tabel dengan filter baru
            });
        });
    </script>
@endpush
