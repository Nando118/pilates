@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Booking</h1>    
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Booking</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora, libero!.</p>
            </div>
        </div>
    </div>
@endsection