@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <div class="card">
            <h5 class="card-header font-weight-bold">Users</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>
                                <th>Created At</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Branch</th>
                                <th>Gender</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('users.create') }}" class="btn btn-success">Add New User</a>
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
                ajax: '{{ route('users.data') }}',
                language: {
                    zeroRecords: "There is no users data yet",
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
                    { data: 'created_at', name: 'created_at', render: DataTable.render.date(), },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'branch', name: 'branch' },
                    { data: 'gender', name: 'gender' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },               
                ],
                order: [1, 'desc'],
            });
        });
    </script>
@endpush