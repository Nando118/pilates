<?php

namespace App\Http\Controllers\Home\MyLesson;

use App\Helpers\TransactionCodeHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CreditTransaction;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MyLessonController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-user-home")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user_id = Auth::id();
        $myBookings = Booking::where("bookings.user_id", $user_id)
            ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id") // Join dengan lesson_schedules
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
            ->with(["lessonSchedule.lesson", "lessonSchedule.lessonType", "lessonSchedule.user", "lessonSchedule.timeSlot", "user.profile"]) // Eager load relasi yang diperlukan
            ->select("bookings.*") // Pilih kolom dari bookings
            ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
            ->get();

        $lessonTypes = LessonType::get();

        return view("home.my-lesson-schedules.index", [
            "title_page" => "Ohana Pilates | My Lesson Schedules",
            "myBookings" => $myBookings,
            "lessonTypes" => $lessonTypes
        ]);
    }
}
