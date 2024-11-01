@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        Update Lesson Schedule
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lesson-schedules.index') }}">Lesson Schedule</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Lesson Schedule</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($lessonSchedule))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $lessonSchedule->id }}">                        
                    @endif

                    <div class="mb-3">
                        <label for="date" class="form-label">Date<span style="color: red;">*</span></label>
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="form-control @error('date') is-invalid @enderror" id="date" name="date" autocomplete="off" value="{{ old('date') ?? (isset($lessonSchedule) ? date("Y/m/d", strtotime($lessonSchedule->date)) : "") }}" required>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                        @error('date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="room" class="form-label">Room<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="room" name="room" required>
                            <option value="" disabled {{ !isset($lessonSchedule) ? 'selected' : '' }}>Select room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room') == $room->id ? 'selected' : (isset($lessonSchedule) && $lessonSchedule->room_id == $room->id ? 'selected' : '') }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="coach_user" class="form-label">Coach<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="coach_user" name="coach_user" required>
                            <option value="" disabled {{ !isset($lessonSchedule) ? 'selected' : '' }}>Select coach</option>
                            @foreach ($coachUsers as $coachUser)
                                <option value="{{ $coachUser->id }}" {{ old('coach_user') == $coachUser->id ? 'selected' : (isset($lessonSchedule) && $lessonSchedule->user_id == $coachUser->id ? 'selected' : '') }}>
                                    {{ $coachUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>  

                    <div class="mb-3">
                        <label for="time_slot" class="form-label">Time<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="time_slot" name="time_slot" required>
                            <option value="" disabled {{ !isset($lessonSchedule) ? 'selected' : '' }}>Select time</option>
                            @foreach ($timeSlots as $timeSlot)
                                <option value="{{ $timeSlot->id }}" {{ old('time_slot') == $timeSlot->id ? 'selected' : (isset($lessonSchedule) && $lessonSchedule->time_slot_id == $timeSlot->id ? 'selected' : '') }}>
                                    {{ $timeSlot->start_time . " - " . $timeSlot->end_time }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lesson" class="form-label">Lesson<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="lesson" name="lesson" required>
                            <option value="" disabled {{ !isset($lessonSchedule) ? 'selected' : '' }}>Select lesson</option>
                            @foreach ($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson') == $lesson->id ? 'selected' : (isset($lessonSchedule) && $lessonSchedule->lesson_id == $lesson->id ? 'selected' : '') }}>
                                    {{ $lesson->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lesson_type" class="form-label">Lesson Type<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="lesson_type" name="lesson_type" required>
                            <option value="" disabled {{ !isset($lessonSchedule) ? 'selected' : '' }}>Select lesson type</option>
                            @foreach ($lessonTypes as $lessonType)
                                <option value="{{ $lessonType->id }}" data-quota="{{ $lessonType->quota }}" {{ old('lesson_type') == $lessonType->id ? 'selected' : (isset($lessonSchedule) && $lessonSchedule->lesson_type_id == $lessonType->id ? 'selected' : '') }}>
                                    {{ $lessonType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quota" class="form-label">Quota<span style="color: red;">*</span></label>
                        <input type="number" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" autocomplete="off" value="{{ old('quota') ?? (isset($lessonSchedule) ? $lessonSchedule->quota : "") }}" required>
                        @error('quota')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        @if (isset($lessonSchedule))
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
        $(document).ready(function(){
            $('.date').datepicker({
                format: 'yyyy/mm/dd', // Format tanggal
                autoclose: true,      // Otomatis menutup setelah tanggal dipilih
                todayHighlight: true,  // Sorot hari ini
                startDate: new Date(), // Set agar minimal hari ini
                orientation: 'bottom'
            });
        });

        $(document).ready(function() {
            $('.dropdown-form-select').select2({
                theme: 'bootstrap4',
            });
        });

        $(document).ready(function(){
            $('#lesson_type').change(function() {
                // Ambil data-quota dari opsi yang dipilih
                var selectedQuota = $(this).find(':selected').data('quota');
                // Set nilai quota ke input field
                $('#quota').val(selectedQuota);
            });
        });
    </script>
@endpush
