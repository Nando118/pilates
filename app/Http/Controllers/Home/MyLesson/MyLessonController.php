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

    public function index(Request $request)
    {
        $user_id = Auth::id(); // ID pengguna yang sedang login

        // Ambil semua id lesson_schedule yang sudah dipesan pengguna
        $userBookings = Booking::where("user_id", $user_id)
            ->pluck("lesson_schedule_id")
            ->toArray();

        // Ambil tanggal dari request atau gunakan tanggal hari ini sebagai default
        $dateFilter = $request->input('date', date('Y-m-d'));
        $dateFilter = Carbon::createFromFormat('Y-m-d', $dateFilter)->setTimezone('Asia/Jakarta')->toDateString(); // Menyesuaikan zona waktu

        $groupFilter = $request->input('group', 'All');

        // Query untuk mengambil data booking berdasarkan filter
        $query = Booking::where("bookings.user_id", $user_id)
            ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id") // Join dengan lesson_schedules
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
            ->with(["lessonSchedule.lesson", "lessonSchedule.lessonType", "lessonSchedule.user", "lessonSchedule.timeSlot", "user.profile"]) // Eager load relasi yang diperlukan
            ->select("bookings.*")
            ->whereDate("lesson_schedules.date", $dateFilter); // Filter berdasarkan tanggal

        // Tambahkan filter group jika dipilih
        if ($groupFilter !== 'All') {
            $query->whereHas('lessonSchedule.lessonType', function ($q) use ($groupFilter) {
                $q->where('name', $groupFilter);
            });
        }

        // Urutkan berdasarkan date dan start_time
        $myBookings = $query->orderBy("lesson_schedules.date")
            ->orderBy("time_slots.start_time")
            ->get();

        // Ambil semua lesson types untuk dropdown filter
        $lessonTypes = LessonType::all();

        // Cek apakah permintaan berasal dari AJAX
        if ($request->ajax()) {
            return view("home.my-lesson-schedules.partials.schedule-table", [
                "myBookings" => $myBookings,
                "userBookings" => $userBookings,
                "lessonTypes" => $lessonTypes
            ])->render();
        }

        return view("home.my-lesson-schedules.index", [
            "title_page" => "Ohana Pilates | My Booked Lessons",
            "myBookings" => $myBookings,
            "lessonTypes" => $lessonTypes,
            "userBookings" => $userBookings // Kirim data booking pengguna
        ]);
    }
}
