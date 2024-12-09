@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">User Details</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Details</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-dark text-white">
                <div class="d-flex align-items-center">
                    @if(isset($userData->profile->profile_picture))
                        <img class="rounded-circle mr-3" src="{{ asset('storage/' . $userData->profile->profile_picture) }}" alt="User Avatar" width="50" height="50">
                    @else
                        <img class="rounded-circle mr-3" src="{{ asset('storage/images/profile_default/profile_default.jpg') }}" alt="User Avatar" width="50" height="50">
                    @endif
                <div>
                    <h3 class="card-title mb-0">{{ $userData->name }}</h3>
                    <h6 class="card-subtitle">{{ $roleName }}</h6>
                </div>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-venus-mars mr-2"></i>{{ ucfirst($userData->profile->gender) }}
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-phone mr-2"></i>{{ ucfirst($userData->profile->phone) }}
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-home mr-2"></i>{{ ucfirst($userData->profile->address) }}
                    </li>
                    @if ($roleName != "Coach")                        
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-coins mr-2"></i>{{ ucfirst($userData->credit_balance) }} credits
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        
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
                                <th>Lesson Time</th>
                                <th>Lesson Code</th>                          
                                <th>Booking at</th>
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
            var userId = {{ $userData->id }};

            // Initialize DataTable
            var table = $('#tbl_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('users.view.data-bookings', ['user' => 'USER_ID']) }}'.replace('USER_ID', userId),
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
                    { data: 'lesson_time', name: 'lesson_time', className: 'align-middle'},                     
                    { data: 'lesson_code', name: 'lesson_code', className: 'align-middle'},
                    { data: 'booked_at', name: 'booked_at', className: 'align-middle'},                     
                ],
                order: [
                    [1, 'desc']
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
                $('#tbl_list').DataTable().ajax.reload(); // Reload tabel dengan filter baru
            });
        });
    </script>
@endpush