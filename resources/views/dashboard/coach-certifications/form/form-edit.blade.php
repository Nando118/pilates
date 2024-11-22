@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Update Coach Certification</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('coach-certifications.index') }}">Coach Certification</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Coach Certification</li>
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
                        <label for="certification_name" class="form-label">Certification Name<span style="color: red;">*</span></label>
                        <input type="text" class="form-control @error('certification_name') is-invalid @enderror" id="certification_name" name="certification_name" autocomplete="off" value="{{ old('certification_name') ?? (isset($coachCertification) ? $coachCertification->certification_name : "") }}" required>
                        @error('certification_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>                    

                    <button id="btn_submit" class="btn btn-success" type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : 'Choose file';
                const label = input.nextElementSibling;
                label.textContent = fileName;
            }

            // Inisialisasi Select2 untuk elemen yang sudah ada saat halaman dimuat
            $('.select2').select2({
                theme: 'bootstrap4',
            });

            $('.date').datepicker({
                format: 'yyyy/mm/dd', // Mengatur format menjadi 2020/12/23
                autoclose: true,      // Agar otomatis menutup setelah tanggal dipilih
                todayHighlight: true,  // Agar hari ini di highlight
                endDate: Date()
            });
        });
    </script>
@endpush