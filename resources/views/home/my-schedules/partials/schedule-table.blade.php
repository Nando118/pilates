<tbody style="font-size: 0.8rem;">
    @if($lessonScheduleDatas->isEmpty())
        <tr id="noLessonPlaceholder">
            <td colspan="4" class=""><strong>You have no schedules yet.</strong></td>
        </tr>
    @else
        @foreach($lessonScheduleDatas as $lessonSchedule)
            @php
                // Mendapatkan tanggal pelajaran dan kategori dari booking
                $lessonDate = $lessonSchedule->date ?? 'N/A';
                $group = ucfirst($lessonSchedule->lessonType->name ?? 'N/A'); // Ambil nama group
            @endphp
            <!-- Sesuaikan data-date dan data-group untuk filter -->
            <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                <td>
                    <strong>{{ date('H:i', strtotime($lessonSchedule->timeSlot->start_time ?? 'N/A')) }}</strong><br>
                    {{ $lessonSchedule->timeSlot->duration ?? 0 }} Min
                </td>
                <td>
                    <strong>{{ ucfirst($lessonSchedule->lesson->name ?? 'N/A') }} / {{ ucfirst($lessonSchedule->lessonType->name ?? 'N/A') }}</strong><br>
                    {{ ucfirst($lessonSchedule->user->name ?? 'N/A') }}
                </td>
                <td>
                    <a href="{{ route('my-schedules.view', ["lessonSchedule" => $lessonSchedule->id]) }}" class="btn btn-primary btn-sm" title="View Participants">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
