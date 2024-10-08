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

    public function index()
    {
        $user = Auth::user();

        // Array kutipan Pilates
        $quotes = [
            "Pilates is complete coordination of body, mind, and spirit.",
            "In 10 sessions you’ll feel the difference, in 20 you’ll see the difference, and in 30 you’ll have a whole new body.",
            "You will feel better in ten sessions, look better in twenty sessions, and have a completely new body in thirty sessions.",
            "Physical fitness is the first requisite of happiness.",
            "The mind, when housed within a healthful body, possesses a glorious sense of power.",
            "Breathing is the first act of life and the last. Our very life depends on it.",
            "Pilates is complete coordination of body, mind and spirit.",
            "A man is as young as his spinal column.",
            "Change happens through movement and movement heals.",
            "Contrology is not a system of haphazard exercises designed to produce only bulging muscles.",
            "It’s the mind itself which builds the body.",
            "The Pilates method of body conditioning is gaining the mastery of your mind over the complete control of your body.",
            "Every moment of our life can be the beginning of great things.",
            "The mind, when housed within a healthful body, possesses a glorious sense of power.",
            "Before you can do something, you must first be something.",
            "Our bodies are our gardens to which our wills are gardeners."
        ];

        // Pilih kutipan secara acak
        $randomQuote = $quotes[array_rand($quotes)];

        // Waktu saat ini
        $currentDate = Carbon::now()->translatedFormat("l, d F Y");

        // Ambil awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Cek apakah user adalah coach
        $isCoach = $user->roles->contains("name", "coach"); // Pastikan role check sesuai dengan implementasi Anda

        if ($isCoach) {
            // Jika user adalah coach, ambil lessons yang dia ajarkan dalam bulan ini
            $myLessons = LessonSchedule::where("user_id", $user->id)
                ->whereBetween("date", [$startOfMonth, $endOfMonth]) // Ambil pelajaran bulan ini
                ->with([
                    "lesson",
                    "lessonType",
                    "timeSlot",
                    "room"
                ]) // Eager load relasi yang diperlukan
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Melakukan join dengan time_slots
                ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
                ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
                ->select("lesson_schedules.*") // Pilih kolom dari lesson_schedules
                ->get();
        } else {
            // Jika user adalah client, ambil lessons yang sudah dibooking bulan ini
            $myLessons = Booking::where("bookings.user_id", $user->id)
            ->whereHas("lessonSchedule", function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween("date", [$startOfMonth, $endOfMonth]); // Filter berdasarkan tanggal di lessonSchedule
            })
            ->with([
                "lessonSchedule.lesson",
                "lessonSchedule.lessonType",
                "lessonSchedule.timeSlot",
                "lessonSchedule.room",
                "lessonSchedule.user"
            ]) // Eager load relasi yang diperlukan
                ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id") // Join dengan lesson_schedules
                ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
                ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
                ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
                ->select("bookings.*", "lesson_schedules.date") // Pilih kolom dari bookings dan date dari lesson_schedules
                ->get();
        }

        return view("home.homes.index", [
            "title_page" => "Pilates | Home",
            "user" => $user,
            "randomQuote" => $randomQuote, // Kirimkan quote ke view
            "currentDate" => $currentDate,
            "myLessons" => $myLessons, // Sesuaikan variable agar bisa digunakan di view
            "isCoach" => $isCoach // Kirim variabel isCoach untuk pengecekan di view
        ]);
    }
}
