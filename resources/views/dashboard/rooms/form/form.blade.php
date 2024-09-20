@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        @if (isset($room))
            Update Room
        @else
            Add New Room
        @endif
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Rooms</a></li>
                @if (isset($room))
                    <li class="breadcrumb-item active" aria-current="page">Update Room</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Add New Room</li>
                @endif
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($room))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $room->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($room) ? $room->name : "") }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        @if (isset($room))
                            Update
                        @else
                            Create
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
