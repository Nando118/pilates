@extends('adminlte::page')

@section('title', $title_page)

@section('content_header')
    @yield('content_header')
@stop

@section('content')
    @yield('content')
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2-bootstrap4.min.css") }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset("/libs/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css") }}" rel="stylesheet" />
@stop

@section('js')
    @stack('scripts')
    @include('sweetalert::alert')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset("/js/script.js") }}"></script>
    <script src="{{ asset("/libs/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js") }}"></script>
@stop
