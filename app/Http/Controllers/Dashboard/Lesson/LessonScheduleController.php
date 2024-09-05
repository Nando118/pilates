<?php

namespace App\Http\Controllers\Dashboard\Lesson;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class LessonScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies('access-dashboard')) {
                return abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $title = 'Delete Lesson!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.lesson-schedule.index", [
            "title_page" => "Pilates | Lesson Schedule"
        ]);
    }

    public function getLessonSchedulesData()
    {
        $lessonScheduleDatas = LessonSchedule::query()->with(['lesson', 'coach'])->get();

        return DataTables::of($lessonScheduleDatas)
            ->addColumn('lessonName', function ($lessonScheduleData) {
                return ucfirst($lessonScheduleData->lesson->name) ?? 'N/A';
            })
            ->addColumn('lessonType', function ($lessonScheduleData) {
                return ucfirst($lessonScheduleData->lesson->type) ?? 'N/A';
            })
            ->addColumn('coachName', function ($lessonScheduleData) {
                return ucfirst($lessonScheduleData->coach->name) ?? 'N/A';
            })
            ->addColumn('action', function ($lessonScheduleData) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="#" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="#" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }
}
