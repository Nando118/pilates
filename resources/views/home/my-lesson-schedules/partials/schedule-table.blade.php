<tbody style="font-size: 0.8rem;">
    @if($myBookings->isEmpty())
        <tr id="noLessonPlaceholder">
            <td colspan="4" class=""><strong>You have no bookings yet.</strong></td>
        </tr>
    @else
        @foreach($myBookings as $booking)
            @php
                // Mendapatkan tanggal pelajaran dan kategori dari booking
                $lessonDate = $booking->lessonSchedule->date ?? 'N/A';
                $group = ucfirst($booking->lessonSchedule->lessonType->name ?? 'N/A'); // Ambil nama group
                $lessonSchedule = $booking->lessonSchedule; // Akses lessonSchedule untuk kebutuhan lainnya
            @endphp
            <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                <td>
                    <strong>{{ date('H:i', strtotime($lessonSchedule->timeSlot->start_time ?? 'N/A')) }}</strong>
                    <br>
                    {{ $lessonSchedule->timeSlot->duration ?? 0 }} Min
                </td>
                <td>
                    <strong>{{ ucfirst($lessonSchedule->lesson->name ?? 'N/A') }} / {{ ucfirst($lessonSchedule->lessonType->name ?? 'N/A') }}</strong><br>
                    {{ ucfirst($lessonSchedule->user->name ?? 'N/A') }}<br>                    
                </td>
                <td>
                    Booking at:
                    <br>
                    <strong>{{ $booking->created_at ? $booking->created_at->format('d-m-Y') : 'N/A' }}</strong><br>
                    {{ $booking->created_at ? $booking->created_at->format('H:i') : 'N/A' }}
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
