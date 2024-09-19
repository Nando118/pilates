<?php

namespace App\Http\Controllers\Dashboard\LessonSchedule;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class LessonScheduleController extends Controller
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
        $title = "Delete Lesson!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.lesson-schedules.index", [
            "title_page" => "Pilates | Lesson Schedules"
        ]);
    }

    public function getData()
    {
        $lessonScheduleDatas = LessonSchedule::get();

        return DataTables::of($lessonScheduleDatas)
            ->addColumn("time", function ($lessonSchedule) {
                $startTime = $lessonSchedule->timeSlot->start_time ?? "N/A";
                $duration = $lessonSchedule->timeSlot->duration ?? 0; // Pastikan Anda memiliki kolom duration di timeSlot

                // Format waktu dan durasi
                return "<strong>" . $startTime . "</strong>" . "<br>" . $duration . " Menit";
            })
            ->addColumn("lesson", function ($lessonSchedule) {
                $lessonName = ucfirst($lessonSchedule->lesson->name ?? "N/A");
                $lessonType = ucfirst($lessonSchedule->lessonType->name ?? "N/A");
                $coachName = ucfirst($lessonSchedule->user->name ?? "N/A");

                return "<strong>" . $lessonName . " / " . $lessonType . "</strong>" . "<br>" . $coachName;
            })
            ->addColumn("room", function ($lessonSchedule) {
                return ucfirst($lessonSchedule->room->name ?? "N/A");
            })
            ->addColumn("action", function ($lessonSchedule) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="#" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="#" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(["time", "lesson", "action"])
            ->make(true);
    }

    public function create()
    {
        $action = route("lesson-schedules.store");

        $timeSlots = TimeSlot::get();
        $lessons = Lesson::get();
        $lessonTypes = LessonType::get();
        $coachUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "coach");
        })->get();
        $rooms = Room::get();

        return view("dashboard.lesson-schedules.form.form", [
            "title_page" => "Pilates | Add Lesson Schedules",
            "timeSlots" => $timeSlots,
            "lessons" => $lessons,
            "lessonTypes" => $lessonTypes,
            "coachUsers" => $coachUsers,
            "rooms" => $rooms,
            "action" => $action,
            "method" => "POST"
        ]);
    }
}
