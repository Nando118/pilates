<?php

namespace App\Http\Controllers\Dashboard\Report;

use App\Exports\MonthlyReportExport;
use App\Exports\WeeklyReportExport;
use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-dashboard")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view("dashboard.reports.index", [
            "title_page" => "Ohana Pilates | Reports"
        ]);
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get("start_date");
        $endDate = $request->get("end_date");
        $frequency = $request->get("frequency");

        // Check for selected frequency and generate report accordingly
        if ($frequency == "weekly") {
            return $this->weeklyReport($startDate, $endDate);
        } elseif ($frequency == "monthly") {
            return $this->monthlyReport($startDate, $endDate);
        }
    }

    public function weeklyReport($startDate, $endDate)
    {
        // Ambil data jadwal pelajaran, time slot, dan coach
        $lessonSchedules = LessonSchedule::with([
            'timeSlot', // Relasi dengan timeSlot
            'coach',    // Relasi dengan coach
            'bookings', // Relasi dengan bookings (peserta)
        ])
            ->whereBetween('lesson_schedules.date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
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

        // Kirim data ke view
        return view('dashboard.reports.weekly', compact('lessonSchedules', 'startDate', 'endDate'))
        ->with('title_page', 'Weekly Report');
    }


    public function exportWeeklyReport(Request $request)
    {
        $startDate = $request->get("start_date");
        $endDate = $request->get("end_date");

        // Export Weekly Report as Excel
        return Excel::download(new WeeklyReportExport($startDate, $endDate), "Weekly_Report_{$startDate}_to_{$endDate}.xlsx");
    }

    public function monthlyReport($startDate, $endDate)
    {
        // Ambil data jadwal pelajaran, coach, dan peserta
        $lessonSchedules = LessonSchedule::with([
            'timeSlot', // Relasi dengan timeSlot
            'coach',    // Relasi dengan coach
            'bookings', // Relasi dengan bookings (peserta)
        ])
        ->whereMonth('lesson_schedules.date', '=', date('m', strtotime($startDate))) // Filter berdasarkan bulan
        ->whereYear('lesson_schedules.date', '=', date('Y', strtotime($startDate))) // Filter berdasarkan tahun
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

        // Kirim data ke view
        return view('dashboard.reports.monthly', compact('lessonSchedules', 'startDate', 'endDate'))
        ->with('title_page', 'Monthly Report');
    }

    public function exportMonthlyReport(Request $request)
    {
        $startDate = $request->get("start_date");
        $endDate = $request->get("end_date");

        // Export Monthly Report as Excel
        return Excel::download(new MonthlyReportExport($startDate, $endDate), "Monthly_Report_{$startDate}_to_{$endDate}.xlsx");
    }
}
