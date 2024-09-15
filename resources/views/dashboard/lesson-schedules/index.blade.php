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
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lesson</th>
                                <th>Room</th>
                                <th>Quota</th>
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
                <a href="#" class="btn btn-success">Add Lesson Schedule</a>
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
                    { data: 'date', name: 'date', render: DataTable.render.date(), className: 'align-middle'},
                    { data: 'time', name: 'time', className: 'align-middle'},
                    { data: 'lesson', name: 'lesson', className: 'align-middle'},
                    { data: 'room', name: 'room', className: 'align-middle'},
                    { data: 'quota', name: 'quota', className: 'align-middle'},
                    { data: 'status', name: 'status', className: 'align-middle'},
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                order: [1, 'desc'],
            });
        });
    </script>
@endpush
