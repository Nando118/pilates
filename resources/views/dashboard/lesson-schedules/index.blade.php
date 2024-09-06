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
                                <th>Created At</th>
                                <th>Lesson Name</th>
                                <th>Type</th>
                                <th>Coach</th>
                                <th>Start</th>
                                <th>Time</th>
                                <th>Quota</th>                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="#" class="btn btn-success">Add New Schedule</a>
            </div>
        </div>
    </div>
        
@endsection