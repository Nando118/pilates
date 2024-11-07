@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Dashboard</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header font-weight-bold">
                Dashboard
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-footer alert alert-primary text-center">
                                <i class="fa-solid fa-ticket fa-xl"></i>
                                <span class="font-weight-bold ml-2">Total Clients</span>
                            </div>
                            <div class="card-body text-center">
                                <p class="card-text font-weight-bolder">{{ $clientCount }}</p>
                                <a href="{{ route('users.index') }}" class="btn btn-primary">Check</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-footer alert alert-danger text-center">
                                <i class="fa-solid fa-ticket fa-xl"></i>
                                <span class="font-weight-bold ml-2">Total Coaches</span>
                            </div>
                            <div class="card-body text-center">
                                <p class="card-text font-weight-bolder">{{ $coachCount }}</p>
                                <a href="{{ route('users.index') }}" class="btn btn-primary">Check</a>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
@endsection
