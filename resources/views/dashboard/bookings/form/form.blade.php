@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Booking Lesson</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lesson-schedules.index') }}">Lesson Schedules</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Booking Lesson</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    <input type="hidden" name="id" value="{{ $lessonDetails->id }}">

                    <div class="card">
                        <div class="card-header">
                            Booking lessons <strong>{{ $lessonDetails->lesson->name }}</strong> with coach <strong>{{ $lessonDetails->user->name }}</strong>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Lesson Type: {{ $lessonDetails->lessonType->name }}</p>
                            <p class="card-text">Date: {{ date('d-m-Y', strtotime($lessonDetails->date)) }}</p>
                            <p class="card-text">Time:  {{ date('H:i', strtotime($lessonDetails->timeSlot->start_time)) }} - {{ date('H:i', strtotime($lessonDetails->timeSlot->end_time)) }}</p>
                            <p class="card-text">Duration: {{ $lessonDetails->timeSlot->duration }} Minute</p>                            
                            <p class="card-text">Available Quota: {{ $lessonDetails->quota }} Person</p>
                            <p class="card-text">Credit Price: {{ $lessonDetails->credit_price }}</p>

                            <small id="emailHelp" class="form-text text-muted">Make sure the user has sufficient credit to book a lesson.</small>
                        </div>
                    </div>                    

                    <div id="participants-container">
                        <div class="mb-3 participant">
                            <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                            <select class="form-control select2" name="name[]" required>
                                <option value="" disabled selected>Select or add name</option>
                                @foreach ($clientUsers as $clientUser)
                                    <option value="{{ $clientUser->id }}">
                                        {{ $clientUser->name . " - " . $clientUser->email . " - Remaining credit" . " " . $clientUser->credit_balance }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="remaining-quota" value="{{ $remainingQuota }}">

                    <button id="btn_submit" class="btn btn-success" type="submit">Booking</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $participantsContainer = $('#participants-container');
            let remainingQuota = {{ $remainingQuota }}; // Set remaining quota dari backend
            let selectedNames = []; // Menyimpan nama yang dipilih

            $participantsContainer.on('change', 'select[name="name[]"]', function() {
                const selectedValue = $(this).val();
                const $this = $(this);

                // Cek apakah nama sudah dipilih sebelumnya
                if (selectedNames.includes(selectedValue)) {
                    alert("This name has already been selected. Please choose another one.");
                    $this.val(''); // Reset pilihan
                    return;
                }

                // Tambahkan nama ke array jika valid
                selectedNames.push(selectedValue);

                // Hitung jumlah peserta saat ini
                const participantCount = selectedNames.length; // Hitung berdasarkan nama yang dipilih

                // Cek apakah masih ada kuota yang tersedia
                if (selectedValue && (participantCount <= remainingQuota)) {
                    // Tambahkan field baru hanya jika kuota masih ada
                    const newParticipantDiv = $(`
                        <div class="mb-3 participant">
                            <label for="name" class="form-label">Name</label>
                            <select class="form-control select2" name="name[]">
                                <option value="" disabled selected>Select or add name</option>
                                @foreach ($clientUsers as $clientUser)
                                    <option value="{{ $clientUser->id }}" class="client-option">
                                        {{ $clientUser->name . " - " . $clientUser->email . " - Remaining credit" . " " . $clientUser->credit_balance }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    `);
                    $participantsContainer.append(newParticipantDiv);

                    // Hapus option yang sudah dipilih dari dropdown baru
                    selectedNames.forEach(name => {
                        newParticipantDiv.find(`option[value="${name}"]`).remove();
                    });

                    // Inisialisasi Select2 pada elemen baru
                    $('.select2').select2({
                        theme: 'bootstrap4',
                        tags: true
                    });

                    // Update kuota yang tersisa
                    remainingQuota -= 1; // Kurangi kuota setelah memilih nama
                }
            });

            // Inisialisasi Select2 untuk elemen yang sudah ada saat halaman dimuat
            $('.select2').select2({
                theme: 'bootstrap4',
                tags: true
            });
        });
    </script>
@endpush

