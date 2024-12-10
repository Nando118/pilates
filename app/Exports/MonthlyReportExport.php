<?php

namespace App\Exports;

use App\Models\LessonSchedule;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyReportExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return LessonSchedule::with(["timeSlot", "coach", "bookings.user"])
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
            ->whereMonth("date", "=", date("m", strtotime($this->startDate)))
            ->whereYear("date", "=", date("Y", strtotime($this->startDate)))
            ->orderBy("date") // Mengurutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Mengurutkan berdasarkan waktu mulai
            ->get()
            ->map(function ($schedule) {
                return [
                    'Lesson Code' => $schedule->lesson_code,
                    'Date' => Carbon::parse($schedule->date)->format('D, d M Y'),
                    'Time' => date("H:i", strtotime($schedule->timeSlot->start_time)) . ' - ' . date("H:i", strtotime($schedule->timeSlot->end_time)),
                    'Coach' => $schedule->coach ? $schedule->coach->name : 'N/A',
                    'Participants' => $schedule->bookings->isEmpty() ? 'No Participants' : $schedule->bookings->map(function ($booking) {
                        return $booking->user ? $booking->user->name : $booking->booked_by_name;
                    })->implode(', ')
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Lesson Code',
            'Date',
            'Time',
            'Coach',
            'Participants'
        ];
    }

    // Menambahkan style pada sheet
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true); // Header bold
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Header center
        $sheet->getStyle('A1:E1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Border pada header

        $sheet->getStyle('A2:E' . (count($this->collection()) + 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Border pada data

        return [];
    }

    // Event untuk setelah sheet diproses
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Menambahkan border ke seluruh sel
                $sheet->getStyle('A1:E' . (count($this->collection()) + 1))
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Membuat header menjadi bold
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
