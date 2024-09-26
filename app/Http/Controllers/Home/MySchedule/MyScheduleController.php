<?php

namespace App\Http\Controllers\Home\MySchedule;

use App\Http\Controllers\Controller;
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
        $lessonScheduleDatas = LessonSchedule::where('user_id', $coach_id)
            ->with(['timeSlot', 'lesson', 'lessonType', 'room', 'user'])
            ->get();

        return view("home.my-schedules.index", [
            "title_page" => "Pilates | My Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas
        ]);
    }

    public function getData($id)
    {
        // Cari lesson schedule berdasarkan ID
        $lessonSchedule = LessonSchedule::with("bookings")->find($id);

        // Jika lesson schedule tidak ditemukan, return 404
        if (!$lessonSchedule) {
            alert()->error("Oppss...", "Lesson schedule not found.");
        }

        // Dapatkan data peserta
        $participants = $lessonSchedule->bookings()->with("user")->get();

        // Return data peserta dalam bentuk JSON
        return response()->json(["participants" => $participants]);
    }

}
