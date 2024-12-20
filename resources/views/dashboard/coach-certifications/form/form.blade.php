@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Add New Coach Certification</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach-certifications.index') }}">Coach Certification</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Coach Certification</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($coachCertification))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $coachCertification->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Coach Name<span style="color: red;">*</span></label>
                        <select class="form-control select2" name="name" required>
                            <option value="" disabled {{ !isset($coachCertification) ? 'selected' : '' }}>Select coach name</option>
                            @foreach ($coaches as $coach)
                                <option value="{{ $coach->id }}" {{ old('name') == $coach->id ? 'selected' : (isset($coachCertification) && $coachCertification->user_id == $coach->id ? 'selected' : '') }}>
                                    {{ $coach->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="certification_names" class="form-label">Certification Names<span style="color: red;">*</span></label>
                        <div id="certifications-container">
                            <!-- Field pertama -->
                            <input type="text" class="form-control mb-2 certification-name @error('certification_names.0') is-invalid @enderror" name="certification_names[]" autocomplete="off" value="{{ old('certification_names.0') ?? '' }}" required>
                            @error('certification_names.0')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>                    

                    <button id="btn_submit" class="btn btn-success" type="submit">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for the existing elements
            $('.select2').select2({
                theme: 'bootstrap4',
            });

            // Function to add new certification input
            function addCertificationInput() {
                const certificationInput = '<input type="text" class="form-control mb-2 certification-name" name="certification_names[]" autocomplete="off">';
                $('#certifications-container').append(certificationInput);
            }

            // Event delegation to handle dynamically added elements
            $('#certifications-container').on('input', '.certification-name', function() {
                const lastInput = $('#certifications-container input').last();

                // Check if the last input field has some value
                if (lastInput.val() !== '') {
                    // Add new certification input if the last one is not empty
                    addCertificationInput();
                }
            });
        });
    </script>
@endpush
