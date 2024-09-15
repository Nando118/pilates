@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Lessons</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lessons</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>
                                <th>Created At</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('lessons.create') }}" class="btn btn-success">Add Lesson</a>
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
                ajax: '{{ route('lessons.data') }}',
                language: {
                    zeroRecords: "There is no lesson data yet",
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
                        }
                    },
                    { data: 'created_at', name: 'created_at', render: DataTable.render.date()},
                    { data: 'name', name: 'name'},
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [1, 'desc'],
            });
        });
    </script>
@endpush
