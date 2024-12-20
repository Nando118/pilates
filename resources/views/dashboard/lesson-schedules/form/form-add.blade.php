@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        Add New Lesson Schedule
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lesson-schedules.index') }}">Lesson Schedule</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Lesson Schedule</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    <div class="mb-3">
                        <label for="frequency" class="form-label">Frequency<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="frequency" name="frequency" required>
                            <option value="" selected disabled>Select frequency</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <small class="form-text text-muted">Frequency Note:</small>
                        <small class="form-text text-muted">Daily - Lesson Schedule will be created for one day, which is the selected date.</small>
                        <small class="form-text text-muted">Weekly - Lesson Schedule will be created every week on the same day, starting from the selected date, for the next 3 weeks.</small>
                        <small class="form-text text-muted">Monthly - Lesson Schedule will be created every month on the same date, starting from the selected date, for the next 2 months.</small>
                    </div>

                    <!-- Step 1: Date -->
                    <div class="mb-3">
                        <label for="date" class="form-label">Date<span style="color: red;">*</span></label>
                        <div class="input-group date" data-provide="datepicker">
                            <input type="text" class="form-control @error('date') is-invalid @enderror" id="date" name="date" autocomplete="off" value="{{ old('date') }}" required>
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
                    
                    <!-- Step 2: Coach -->
                    <div class="mb-3">
                        <label for="coach_user" class="form-label">Coach<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="coach_user" name="coach_user" required>
                            <option value="" selected disabled>Select coach</option>
                            @foreach ($coachUsers as $coachUser)
                                <option value="{{ $coachUser->id }}" {{ old('coach_user') == $coachUser->id ? 'selected' : '' }}>
                                    {{ $coachUser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 3: Time (dynamic) -->
                    <div class="mb-3" id="time_slot_container" style="display: none;">
                        <label for="time_slot" class="form-label">Time<span style="color: red;">*</span></label>
                        <select class="form-control dropdown-form-select" id="time_slot" name="time_slot" required>                            
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>

                    <!-- Step 4: Lesson, Lesson Type, Quota -->
                    <div id="lesson_details_container" style="display: none;">
                        <div class="mb-3">
                            <label for="lesson" class="form-label">Lesson<span style="color: red;">*</span></label>
                            <select class="form-control dropdown-form-select" id="lesson" name="lesson" required>
                                <option value="" selected disabled>Select lesson</option>
                                @foreach ($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('lesson') == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="lesson_type" class="form-label">Lesson Type<span style="color: red;">*</span></label>
                            <select class="form-control dropdown-form-select" id="lesson_type" name="lesson_type" required>
                                <option value="" selected disabled>Select lesson type</option>
                                @foreach ($lessonTypes as $lessonType)
                                    <option value="{{ $lessonType->id }}" data-quota="{{ $lessonType->quota }}" {{ old('lesson_type') == $lessonType->id ? 'selected' : '' }}>
                                        {{ $lessonType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quota" class="form-label">Quota<span style="color: red;">*</span></label>
                            <input type="number" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" autocomplete="off" value="{{ old('quota') }}" required>
                            @error('quota')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="credit_price" class="form-label">Credit Price<span style="color: red;">*</span></label>
                            <input type="number" class="form-control @error('credit_price') is-invalid @enderror" id="credit_price" name="credit_price" autocomplete="off" value="{{ old('credit_price') }}" required>
                            @error('credit_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        Create
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('.date').datepicker({
                format: 'yyyy/mm/dd',
                autoclose: true,
                todayHighlight: true,
                startDate: Date(),
                orientation: 'bottom'
            });

            // Initialize select2
            $('.dropdown-form-select').select2({
                theme: 'bootstrap4',
            });

            // Trigger when Date changes
            $('#date').change(function() {
                var selectedDate = $('#date').val();

                if (selectedDate) {
                    // Fetch available time slots for the selected date
                    $.ajax({
                        url: '{{ route('lesson-schedules.getAvailableTimeSlots') }}',
                        type: 'GET',
                        data: {
                            date: selectedDate
                        },
                        success: function(response) {
                            // Clear existing time slots
                            $('#time_slot').empty();

                            // Populate available time slots
                            if (response.length > 0) {
                                $('#time_slot_container').show();
                                $('#time_slot').append('<option value="" disabled selected>Select time</option>');
                                $.each(response, function(index, timeSlot) {
                                    $('#time_slot').append('<option value="' + timeSlot.id + '">' + timeSlot.start_time + ' - ' + timeSlot.end_time + '</option>');
                                });
                            } else {
                                $('#time_slot_container').hide();
                            }
                        }
                    });
                }
            });

            // Show lesson details after time slot is selected
            $('#time_slot').change(function() {
                $('#lesson_details_container').show();
            });

            // Automatically fill in the quota based on selected lesson type
            $('#lesson_type').change(function() {
                var selectedQuota = $(this).find(':selected').data('quota');
                $('#quota').val(selectedQuota);
            });
        });
    </script>
@endpush
