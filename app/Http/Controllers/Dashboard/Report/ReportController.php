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
        // Ambil data jadwal pelajaran, coach, dan peserta
        $lessonSchedules = LessonSchedule::with(["timeSlot", "coach", "bookings.user"])
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
            ->whereBetween("date", [$startDate, $endDate])
            ->orderBy("date") // Mengurutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Mengurutkan berdasarkan waktu mulai
            ->get();

        return view("dashboard.reports.weekly", compact("lessonSchedules", "startDate", "endDate"))->with("title_page", "Weekly Report");
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
        $lessonSchedules = LessonSchedule::with(["timeSlot", "coach", "bookings.user"])
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
            ->whereMonth("date", "=", date("m", strtotime($startDate)))
            ->whereYear('date', "=", date("Y", strtotime($startDate)))
            ->orderBy("date") // Mengurutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Mengurutkan berdasarkan waktu mulai
            ->get();

        return view("dashboard.reports.monthly", compact("lessonSchedules", "startDate", "endDate"))->with("title_page", "Monthly Report");
    }

    public function exportMonthlyReport(Request $request)
    {
        $startDate = $request->get("start_date");
        $endDate = $request->get("end_date");

        // Export Monthly Report as Excel
        return Excel::download(new MonthlyReportExport($startDate, $endDate), "Monthly_Report_{$startDate}_to_{$endDate}.xlsx");
    }
}
