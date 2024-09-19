@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        @if (isset($lessonType))
            Update Lesson Schedule
        @else
            Add New Lesson Schedule
        @endif
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lesson-schedules.index') }}">Lesson Schedule</a></li>
                @if (isset($lesson))
                    <li class="breadcrumb-item active" aria-current="page">Update Lesson Schedule</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Add Lesson Schedule</li>
                @endif
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($lesson))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $lesson->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Date<span style="color: red;">*</span></label>
                        <input type="date" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($lesson) ? $lesson->name : "") }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="time_slot" class="form-label">Time<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="time_slot" name="time_slot" required>
                            <option value="" disabled selected>Select time</option>
                            @foreach ($timeSlots as $timeSlot)
                                <option value="{{ $timeSlot->id }}">
                                    {{ $timeSlot->start_time }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lesson" class="form-label">Lesson<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="lesson" name="lesson" required>
                            <option value="" disabled selected>Select lesson</option>
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}">
                                    {{ $lesson->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lesson_type" class="form-label">Lesson Type<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="lesson_type" name="lesson_type" required>
                            <option value="" disabled selected>Select lesson type</option>
                            @foreach ($lessonTypes as $lessonType)
                                <option value="{{ $lessonType->id }}" data-quota="{{ $lessonType->quota }}">
                                    {{ $lessonType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="coach_user" class="form-label">Coach<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="coach_user" name="coach_user" required>
                            <option value="" disabled selected>Select coach</option>
                            @foreach ($coachUsers as $coachUser)
                                <option value="{{ $coachUser->id }}">
                                    {{ $coachUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="room" class="form-label">Room<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="room" name="room" required>
                            <option value="" disabled selected>Select room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quota" class="form-label">Quota<span style="color: red;">*</span></label>
                        <input type="number" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" autocomplete="off" value="{{ old('quota') ?? (isset($lessonType) ? $lessonType->quota : "") }}" required>
                        @error('quota')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        @if (isset($lesson))
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

@push("scripts")
    <script>
        $(document).ready(function() {
            $('.dropdown-form-select').select2();
        });
    </script>
@endpush