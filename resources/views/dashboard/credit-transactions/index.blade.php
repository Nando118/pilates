@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Credit Transactions</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Credit Transactions</li>
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
                                <th>Transaction Code</th>
                                <th>Name</th>                        
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
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
                ajax: '{{ route('credit-transactions.data') }}',
                language: {
                    zeroRecords: "There is no transaction data yet",
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
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data, type, row) {
                            // Format tanggal menjadi DD-MM-YYYY
                            return moment(data).format('DD-MM-YYYY');
                        }
                    },
                    { data: 'transaction_code', name: 'transaction_code'},
                    { data: 'name', name: 'name'},
                    { data: 'type', name: 'type'},
                    { data: 'amount', name: 'amount'},
                    { data: 'description', name: 'description'}
                ],
                order: [1, 'desc'],                
            });
        });
    </script>
@endpush
