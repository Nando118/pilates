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
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>
                                <th>Lesson</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Created At</th>
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
            $('#tbl_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('bookings.data') }}',
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
                    { data: 'lesson', name: 'lesson', className: 'align-middle'},
                    { data: 'date', name: 'date', className: 'align-middle'},
                    { data: 'booked_by_name', name: 'booked_by_name', className: 'align-middle'},
                    { data: 'username', name: 'username', className: 'align-middle'},
                    { data: 'created_at', name: 'created_at', className: 'align-middle', render: DataTable.render.date()},
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                order: [
                    [2, 'desc'],
                    [5, 'desc']
                ],
            });
        });
    </script>
@endpush