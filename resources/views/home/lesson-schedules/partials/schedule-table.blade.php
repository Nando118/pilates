<tbody style="font-size: 0.8rem">
    @if($lessonScheduleDatas->isEmpty())
        <tr id="noLessonPlaceholder">
            <td colspan="4" class=""><strong>There are no lessons scheduled at this time.</strong></td>
        </tr>
    @else
        @foreach ($lessonScheduleDatas as $lessonSchedule)
            @php
                $lessonDate = $lessonSchedule->date ?? 'N/A';
                $group = ucfirst(optional($lessonSchedule->lessonType)->name ?? 'N/A');
            @endphp
            <tr data-date="{{ $lessonDate }}" data-group="{{ $group }}">
                <td>
                    <strong>{{ date("H:i", strtotime(optional($lessonSchedule->timeSlot)->start_time ?? 'N/A')) }}</strong>
                    <br>{{ optional($lessonSchedule->timeSlot)->duration ?? 0 }} Min
                </td>
                <td>
                    <strong>
                        {{ ucfirst(optional($lessonSchedule->lesson)->name ?? 'N/A') }} /
                        {{ ucfirst(optional($lessonSchedule->lessonType)->name ?? 'N/A') }}
                    </strong>
                    <br>{{ ucfirst(optional($lessonSchedule->user)->name ?? 'N/A') }}
                </td>
                <td>
                    <strong>
                        @if ($lessonSchedule->quota <= 0)
                            <span class="text-danger">FULLY BOOKED</span>
                        @else
                            Quota {{ $lessonSchedule->quota }}
                        @endif
                    </strong>
                    <br>Credit Price {{ $lessonSchedule->credit_price ?? 'N/A' }}
                </td>
                @can('access-client-menu')
                    <td class="text-center">
                        @php
                            $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->timeSlot->start_time);
                            $currentDateTime = now();
                        @endphp

                        @if ($lessonSchedule->deleted_at)
                            <span class="badge bg-danger">Canceled</span>
                        @elseif (in_array($lessonSchedule->id, $userBookings))
                            <button class="btn btn-primary btn-sm" title="Already Booked" disabled>
                                <i class="fas fa-fw fa-user-check"></i>
                            </button>
                        @elseif ($lessonSchedule->quota <= 0 || $currentDateTime->greaterThanOrEqualTo($lessonStartTime))
                            <button class="btn btn-primary btn-sm" title="Cannot Book" disabled>
                                <i class="fas fa-fw fa-user-plus"></i>
                            </button>
                        @else
                            <a href="{{ route('user-lesson-schedules.create', ['bookings' => $lessonSchedule->id]) }}"
                               class="btn btn-primary btn-sm" title="Booking">
                                <i class="fas fa-fw fa-user-plus"></i>
                            </a>
                        @endif
                    </td>
                @endcan
                @can('access-coach-menu')
                    <td class="text-center">
                        @if ($lessonSchedule->deleted_at)
                            <span class="badge bg-danger">Canceled</span>
                        @else
                            @php
                                $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->timeSlot->start_time);
                                $currentDateTime = now();
                            @endphp

                            @if ($currentDateTime->greaterThanOrEqualTo($lessonStartTime))
                                <span class="badge bg-secondary">Not Available</span>
                            @elseif ($lessonSchedule->quota <= 0)
                                <span class="badge bg-info">Full Booking</span>
                            @else
                                <span class="badge bg-success">Available</span>
                            @endif
                        @endif
                    </td>
                @endcan
            </tr>
        @endforeach
    @endif
</tbody>
