@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        @if (isset($timeSlot))
            Update Time Slot
        @else
            Add New Time Slot
        @endif
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('time-slots.index') }}">Time Slots</a></li>
                @if (isset($timeSlot))
                    <li class="breadcrumb-item active" aria-current="page">Update Time Slot</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Add New Time Slot</li>
                @endif
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($timeSlot))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $timeSlot->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="start_time">Time Start<span style="color: red;">*</span></label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') ? old('start_time') : (isset($timeSlot) ? date('H:i', strtotime($timeSlot->start_time)) : '') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_time">Time End<span style="color: red;">*</span></label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') ? old('end_time') : (isset($timeSlot) ? date('H:i', strtotime($timeSlot->end_time)) : '') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration<span style="color: red;">*</span></label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" autocomplete="off" value="{{ old('duration') ?? (isset($timeSlot) ? $timeSlot->duration : "") }}" required>
                        <div class="form-text" id="basic-addon4">Minimum duration entered is 20 minutes.</div>
                        @error('duration')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        @if (isset($timeSlot))
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
