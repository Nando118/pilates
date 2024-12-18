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
    <link rel="stylesheet" href="{{ asset("libs/dataTables/css/dataTables-bootstrap4-min.css") }}" />
    <link rel="stylesheet" href="{{ asset("libs/dataTables/css/buttons-bootstrap4-min.css") }}">
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2.css") }}"/>
    <link rel="stylesheet" href="{{ asset("/libs/select2/css/select2-bootstrap4.min.css") }}"/>
    <link rel="stylesheet" href="{{ asset("/libs/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css") }}"/>
@stop

@section('js')
    @stack('scripts')
    @include('sweetalert::alert')
    <script src="{{ asset("libs/dataTables/js/jquery-dataTables-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/dataTables-bootstrap4-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/dataTables-buttons-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/buttons-bootstrap4-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/jszip-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/buttons-html5-min.js") }}"></script>
    <script src="{{ asset("libs/dataTables/js/buttons-print-min.js") }}"></script>

    <script src="{{ asset("/js/script.js") }}"></script>
    <script src="{{ asset("/libs/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js") }}"></script>
    <script src="{{ asset("/libs/moment/moment.js") }}"></script>
@stop
