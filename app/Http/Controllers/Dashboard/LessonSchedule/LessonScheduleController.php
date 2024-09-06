<?php

namespace App\Http\Controllers\Dashboard\LessonSchedule;

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
}
