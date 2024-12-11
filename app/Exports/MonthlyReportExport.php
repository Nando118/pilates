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
        // Ambil data jadwal pelajaran, time slot, dan coach
        $lessonSchedules = LessonSchedule::with([
            'timeSlot', // Relasi dengan timeSlot
            'coach',    // Relasi dengan coach
            'bookings', // Relasi dengan bookings (peserta)
        ])
            ->whereMonth("date", "=", date("m", strtotime($this->startDate)))
            ->whereYear("date", "=", date("Y", strtotime($this->startDate)))
            ->orderBy('lesson_schedules.date') // Mengurutkan berdasarkan tanggal
            ->get(); // Ambil data

        // Menghitung jumlah peserta untuk setiap lessonSchedule
        foreach ($lessonSchedules as $schedule) {
            $schedule->participants_count = $schedule->bookings->count(); // Hitung jumlah bookings untuk setiap schedule
        }

        // Urutkan berdasarkan tanggal terlebih dahulu, kemudian waktu mulai
        $lessonSchedules = $lessonSchedules->sortBy(function ($schedule) {
            return $schedule->date . $schedule->timeSlot->start_time; // Gabungkan date dan start_time untuk urutkan dengan benar
        });

        return $lessonSchedules->map(function ($schedule) {
            // Ambil peserta (user) dari relasi bookings
            $participants = $schedule->bookings->map(function ($booking) {
                return $booking->user ? $booking->user->name : $booking->booked_by_name;
            });

            // Hitung jumlah peserta
            $participantCount = $schedule->participants_count; // Dapatkan jumlah peserta dari hasil penghitungan sebelumnya

            return [
                'Lesson Code' => $schedule->lesson_code,
                'Date' => Carbon::parse($schedule->date)->format('D, d M Y'),
                'Time' => date("H:i", strtotime($schedule->timeSlot->start_time)) . ' - ' . date("H:i", strtotime($schedule->timeSlot->end_time)),
                'Coach' => $schedule->coach ? $schedule->coach->name : 'N/A',
                'Participants' => $participantCount > 0 ? $participants->implode(', ') : 'No Participants', // Menampilkan peserta
                'Participants Count' => $participantCount, // Menampilkan jumlah peserta
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
            'Participants',
            'Participants Count',
        ];
    }

    // Menambahkan style pada sheet
    public function styles(Worksheet $sheet)
    {
        // Menambahkan style untuk header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // Header bold
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Header center
        $sheet->getStyle('A1:F1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN); // Border pada header

        // Menambahkan border pada seluruh data
        $sheet->getStyle('A2:F' . (count($this->collection()) + 1))
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
                $sheet->getStyle('A1:F' . (count($this->collection()) + 1))
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Membuat header menjadi bold dan di-center
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
