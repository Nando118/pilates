<?php

namespace App\Http\Controllers\Home\MySchedule;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LessonSchedule;
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
            ->with(["timeSlot", "lesson", "lessonType", "room", "user"])
            ->get();

        return view("home.my-schedules.index", [
            "title_page" => "Pilates | My Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas
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
