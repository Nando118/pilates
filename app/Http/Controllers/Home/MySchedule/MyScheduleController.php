<?php

namespace App\Http\Controllers\Home\MySchedule;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function index(Request $request)
    {
        $coach_id = Auth::id(); // ID pengguna yang sedang login (coach)

        // Ambil semua id lesson_schedule yang sudah dipesan oleh coach
        // (Jika perlu, Anda bisa menyesuaikan logika ini untuk mengambil booking oleh coach)
        $userBookings = Booking::where("user_id", $coach_id)
            ->pluck("lesson_schedule_id")
            ->toArray();

        // Ambil tanggal dari request dan konversi ke Asia/Jakarta
        $dateFilter = $request->input('date', date('Y-m-d'));  // Default tanggal hari ini
        $dateFilter = Carbon::createFromFormat('Y-m-d', $dateFilter)->setTimezone('Asia/Jakarta')->toDateString();

        $groupFilter = $request->input('group', 'All');

        // Query untuk mengambil data lesson schedule yang diajar oleh coach
        $query = LessonSchedule::where("lesson_schedules.user_id", $coach_id) // Filter berdasarkan coach yang sedang login
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
            ->with(["timeSlot", "lesson", "lessonType", "user"]) // Eager load relasi yang diperlukan
            ->select("lesson_schedules.*")
            ->whereDate("lesson_schedules.date", $dateFilter); // Filter berdasarkan tanggal

        // Tambahkan filter group jika dipilih
        if ($groupFilter !== 'All') {
            $query->whereHas('lessonType', function ($q) use ($groupFilter) {
                $q->where('name', $groupFilter);
            });
        }

        // Urutkan berdasarkan date dan start_time
        $lessonScheduleDatas = $query->orderBy("lesson_schedules.date")
        ->orderBy("time_slots.start_time")
        ->get();

        // Ambil semua lesson types untuk dropdown filter
        $lessonTypes = LessonType::all();

        // Cek apakah permintaan berasal dari AJAX
        if ($request->ajax()) {
            return view("home.my-schedules.partials.schedule-table", [
                "lessonScheduleDatas" => $lessonScheduleDatas,
                "lessonTypes" => $lessonTypes,
                "userBookings" => $userBookings // Kirim data booking pengguna
            ])->render();
        }

        // Tampilkan tampilan utama
        return view("home.my-schedules.index", [
            "title_page" => "Ohana Pilates | My Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas,
            "lessonTypes" => $lessonTypes, // Kirim data lesson types untuk filter
            "userBookings" => $userBookings // Kirim data booking pengguna
        ]);
    }

    public function view(LessonSchedule $lessonSchedule)
    {
        $coach_id = Auth::id();

        // Ambil data lesson schedules berdasarkan user_id (coach)
        $lessonScheduleDatas = LessonSchedule::where("user_id", $coach_id)
        ->with(["timeSlot", "lesson", "lessonType", "user"]) // relasi yang dibutuhkan
        ->get();

        // Ambil data bookings berdasarkan lesson_schedule_id
        $bookings = Booking::where("lesson_schedule_id", $lessonSchedule->id)
        ->with("user") // pastikan mengambil data user dari relasi booking
        ->get();

        return view("home.my-schedules.participants.index", [
            "title_page" => "Ohana Pilates | Participants",
            "lessonSchedule" => $lessonSchedule, // Kirimkan data lesson schedule
            "bookings" => $bookings // Kirimkan data bookings ke view
        ]);
    }
}
