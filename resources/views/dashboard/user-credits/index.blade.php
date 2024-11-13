@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">User Credits</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Credits</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_list" class="table table-striped" width="100%">
                        <thead>
                            <tr >
                                <th>No</th>                                
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Credits Balance</th>                                
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
                ajax: '{{ route('user-credits.data') }}',
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
                    { data: 'name', name: 'name'},
                    { data: 'email', name: 'email'},
                    { data: 'phone', name: 'phone'},
                    { data: 'credit_balance', name: 'credit_balance'},                    
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [1, 'desc'],                
            });
        });
    </script>
@endpush
