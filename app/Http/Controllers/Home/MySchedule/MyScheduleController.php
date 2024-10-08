<?php

namespace App\Http\Controllers\Home\MySchedule;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MyScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-coach-menu")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Dapatkan user_id dari coach yang sedang login
        $coach_id = Auth::id();

        // Ambil data lesson schedules berdasarkan user_id coach
        $lessonScheduleDatas = LessonSchedule::where("user_id", $coach_id)
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
            ->with(["timeSlot", "lesson", "lessonType", "room", "user"]) // Eager load relasi yang diperlukan
            ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
            ->select("lesson_schedules.*") // Pastikan hanya memilih kolom dari lesson_schedules
            ->get();

        $lessonTypes = LessonType::get();

        return view("home.my-schedules.index", [
            "title_page" => "Pilates | My Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas,
            "lessonTypes" => $lessonTypes
        ]);        
    }

    public function view(LessonSchedule $lessonSchedule)
    {
        $coach_id = Auth::id();

        // Ambil data lesson schedules berdasarkan user_id (coach)
        $lessonScheduleDatas = LessonSchedule::where("user_id", $coach_id)
        ->with(["timeSlot", "lesson", "lessonType", "room", "user"]) // relasi yang dibutuhkan
        ->get();

        // Ambil data bookings berdasarkan lesson_schedule_id
        $bookings = Booking::where("lesson_schedule_id", $lessonSchedule->id)
        ->with("user") // pastikan mengambil data user dari relasi booking
        ->get();

        return view("home.my-schedules.participants.index", [
            "title_page" => "Pilates | Participants",
            "lessonSchedule" => $lessonSchedule, // Kirimkan data lesson schedule
            "bookings" => $bookings // Kirimkan data bookings ke view
        ]);
    }
}
