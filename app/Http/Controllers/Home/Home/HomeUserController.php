<?php

namespace App\Http\Controllers\Home\Home;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LessonSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeUserController extends Controller
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

    public function upcomingLessons()
    {
        $user = Auth::user();

        // Waktu saat ini
        $currentDate = Carbon::now()->translatedFormat("l, d F Y");
        $currentDateTime = Carbon::now();

        // Ambil awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Cek apakah user adalah coach
        $isCoach = $user->roles->contains("name", "coach"); // Pastikan role check sesuai dengan implementasi Anda

        if ($isCoach) {
            // Jika user adalah coach, ambil lessons yang dia ajarkan dalam bulan ini
            $myLessons = LessonSchedule::where("user_id", $user->id)
                ->whereNull("lesson_schedules.deleted_at") // Hanya ambil lesson yang belum dihapus
                ->whereBetween("date", [$startOfMonth, $endOfMonth]) // Ambil pelajaran bulan ini
                ->with([
                    "lesson",
                    "lessonType",
                    "timeSlot"
                ]) // Eager load relasi yang diperlukan
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Melakukan join dengan time_slots
                ->where(function ($query) use ($currentDateTime) {
                    $query->where("lesson_schedules.date", ">", $currentDateTime->toDateString()) // Tanggal lebih dari hari ini
                        ->orWhere(function ($query) use ($currentDateTime) {
                            $query->where("lesson_schedules.date", "=", $currentDateTime->toDateString())
                                ->where("time_slots.end_time", ">", $currentDateTime->toTimeString()); // Waktu berakhir di masa mendatang
                        });
                })
                ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
                ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
                ->select("lesson_schedules.*") // Pilih kolom dari lesson_schedules
                ->get();
        } else {
            // Jika user adalah client, ambil lessons yang sudah dibooking bulan ini
            $myLessons = Booking::where("bookings.user_id", $user->id)
                ->whereHas("lessonSchedule", function ($query) use ($startOfMonth, $endOfMonth, $currentDateTime) {
                    $query->whereBetween("date", [$startOfMonth, $endOfMonth]) // Filter berdasarkan tanggal di lessonSchedule
                        ->where(function ($query) use ($currentDateTime) {
                            $query->where("date", ">", $currentDateTime->toDateString()) // Tanggal lebih dari hari ini
                                ->orWhere(function ($query) use ($currentDateTime) {
                                    $query->where("date", "=", $currentDateTime->toDateString())
                                        ->whereHas("timeSlot", function ($query) use ($currentDateTime) {
                                            $query->where("end_time", ">", $currentDateTime->toTimeString()); // Waktu berakhir di masa mendatang
                                        });
                                });
                        });
                })
                ->with([
                    "lessonSchedule.lesson",
                    "lessonSchedule.lessonType",
                    "lessonSchedule.timeSlot",
                    "lessonSchedule.user"
                ]) // Eager load relasi yang diperlukan
                ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id") // Join dengan lesson_schedules
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
                ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
                ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
                ->select("bookings.*", "lesson_schedules.date") // Pilih kolom dari bookings dan date dari lesson_schedules
                ->get();
        }

        return view("home.homes.upcoming-lessons", [
            "title_page" => "Ohana Pilates | Home",
            "user" => $user,
            "currentDate" => $currentDate,
            "myLessons" => $myLessons, // Sesuaikan variable agar bisa digunakan di view
            "isCoach" => $isCoach // Kirim variabel isCoach untuk pengecekan di view
        ]);
    }

    public function pastLessons()
    {
        $user = Auth::user();

        // Waktu saat ini
        $currentDate = Carbon::now()->translatedFormat("l, d F Y");
        $currentDateTime = Carbon::now();

        // Ambil awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Cek apakah user adalah coach
        $isCoach = $user->roles->contains("name", "coach");

        if ($isCoach) {
            // Jika user adalah coach, ambil lessons yang dia ajarkan yang sudah lewat dalam bulan ini
            $myLessons = LessonSchedule::where("user_id", $user->id)
                ->whereBetween("date", [$startOfMonth, $endOfMonth]) // Hanya untuk bulan ini
                ->with(["lesson", "lessonType", "timeSlot"])
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
                ->where(function ($query) use ($currentDateTime) {
                    $query->where("lesson_schedules.date", "<", $currentDateTime->toDateString()) // Tanggal kurang dari hari ini
                        ->orWhere(function ($query) use ($currentDateTime) {
                            $query->where("lesson_schedules.date", "=", $currentDateTime->toDateString())
                                ->where("time_slots.end_time", "<=", $currentDateTime->toTimeString()); // Waktu berakhir di masa lalu
                        });
                })
                ->orderBy("lesson_schedules.date", 'desc') // Urutkan berdasarkan tanggal terbaru
                ->orderBy("time_slots.start_time", 'desc') // Urutkan berdasarkan waktu mulai terbaru
                ->select("lesson_schedules.*")
                ->get();
        } else {
            // Jika user adalah client, ambil lessons yang sudah dibooking yang sudah lewat dalam bulan ini
            $myLessons = Booking::where("bookings.user_id", $user->id)
                ->whereHas("lessonSchedule", function ($query) use ($startOfMonth, $endOfMonth, $currentDateTime) {
                    $query->whereBetween("date", [$startOfMonth, $endOfMonth])
                        ->where(function ($query) use ($currentDateTime) {
                            $query->where("date", "<", $currentDateTime->toDateString()) // Tanggal kurang dari hari ini
                                ->orWhere(function ($query) use ($currentDateTime) {
                                    $query->where("date", "=", $currentDateTime->toDateString())
                                        ->whereHas("timeSlot", function ($query) use ($currentDateTime) {
                                            $query->where("end_time", "<=", $currentDateTime->toTimeString()); // Waktu berakhir di masa lalu
                                        });
                                });
                        });
                })
                ->with([
                    "lessonSchedule.lesson",
                    "lessonSchedule.lessonType",
                    "lessonSchedule.timeSlot",
                    "lessonSchedule.user"
                ])
                ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id")
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
                ->orderBy("lesson_schedules.date", 'desc') // Urutkan berdasarkan tanggal terbaru
                ->orderBy("time_slots.start_time", 'desc') // Urutkan berdasarkan waktu mulai terbaru
                ->select("bookings.*", "lesson_schedules.date")
                ->get();
        }

        return view("home.homes.past-lessons", [
            "title_page" => "Ohana Pilates | Home",
            "user" => $user,
            "currentDate" => $currentDate,
            "myLessons" => $myLessons,
            "isCoach" => $isCoach
        ]);
    }
}
