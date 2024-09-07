<?php

namespace App\Http\Controllers\Dashboard\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Lessons\CreateLessonRequest;
use App\Http\Requests\Dashboard\Lessons\UpdateLessonRequest;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class LessonController extends Controller
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

        return view("dashboard.lessons.index", [
            "title_page" => "Pilates | Lessons"
        ]);
    }

    public function getData()
    {
        $lessonDatas = Lesson::query()->get();

        foreach ($lessonDatas as $lesson) {
            $lesson->name = ucfirst($lesson->name);
            $lesson->type = ucfirst($lesson->type);
        }

        return DataTables::of($lessonDatas)
            ->addColumn("action", function ($lesson) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. route("lessons.edit", ["lesson" => $lesson->id]) .'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("lessons.delete", ["lesson" => $lesson->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("lessons.store");

        return view("dashboard.lessons.form.form", [
            "title_page" => "Pilates | Add New Lesson",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateLessonRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            Lesson::create([
                "name" => $validated["name"],
                "type" => $validated["type"],
                "quota" => $validated["quota"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new lesson.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error("Error adding lesson in LessonController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new lesson, please try again.");
            return redirect()->back();
        }
    }

    public function edit(Lesson $lesson)
    {
        $action = route("lessons.update", $lesson->id);

        return view("dashboard.lessons.form.form", compact("lesson", "action"))
        ->with([
            "title_page" => "Pilates | Update Lesson",
            "method" => "POST"
        ]);
    }

    public function update(Lesson $lesson, UpdateLessonRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = Lesson::findOrFail($lesson->id);
            $user->name = $validated["name"];
            $user->type = $validated["type"];
            $user->quota = $validated["quota"];
            $user->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated lesson data.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error("Error updating lesson in LessonController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating lesson data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(Lesson $lesson)
    {
        try {
            DB::beginTransaction();

            $lesson = Lesson::findOrFail($lesson->id);

            $lesson->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted lesson data.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error("Error deleting lesson in LessonController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted lesson data, please try again.");
            return redirect()->back();
        }
    }
}
