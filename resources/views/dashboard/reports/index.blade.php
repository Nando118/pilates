@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Reports</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.generate') }}" method="GET">
                    @csrf
                    <div class="form-group">
                        <label for="start_date">Start Date:</label><span style="color: red;">*</span>
                        <div class="input-group date" id="start_date_picker" data-provide="datepicker">
                            <input type="text" class="form-control" id="start_date" name="start_date" autocomplete="off" placeholder="{{ now()->format('Y-m-d') }}" required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label><span style="color: red;">*</span>
                        <div class="input-group date" id="end_date_picker" data-provide="datepicker">
                            <input type="text" class="form-control" id="end_date" name="end_date" autocomplete="off" placeholder="{{ now()->format('Y-m-d') }}" required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="frequency">Frequency:</label>
                        <select id="frequency" name="frequency" class="form-control" required>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <small class="form-text text-muted">Frequency Note:</small>                        
                        <small class="form-text text-muted">Weekly - Reports will be created based on Start Date to End Date.</small>
                        <small class="form-text text-muted">Monthly - Report will be created for one full month based on the month from Start Date.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>        
    </div>
@endsection

@push("scripts")
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize datepickers
            $('#start_date_picker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });

            $('#end_date_picker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });
        });
    </script>
@endpush
