<?php

namespace App\Http\Controllers\Dashboard\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Lessons\CreateLessonRequest;
use App\Http\Requests\Dashboard\Lessons\UpdateLessonRequest;
use App\Models\Lesson;
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
            "title_page" => "Ohana Pilates | Lessons"
        ]);
    }

    public function getData()
    {
        $lessonDatas = Lesson::get();

        return DataTables::of($lessonDatas)
            ->addColumn("action", function ($lesson) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="' . route("lessons.edit", ["lesson" => $lesson->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
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
            "title_page" => "Ohana Pilates | Add Lesson",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateLessonRequest $request)
    {
        try {
            $validated = $request->validated();

            $existingLesson = Lesson::where("name", $validated["name"])
                ->whereNull("deleted_at") // Cek hanya yang belum dihapus (soft delete)
                ->first();

            if ($existingLesson) {
                alert()->error("Oppss...", "Lesson name already exists. Please use a different name.");
                return redirect()->back()->withInput();
            }

            DB::beginTransaction();

            Lesson::create([
                "name" => $validated["name"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new lesson data.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error("Error adding lesson in LessonController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new lesson data, please try again.");
            return redirect()->back();
        }
    }

    public function edit(Lesson $lesson)
    {
        $action = route("lessons.update", ["lesson" => $lesson->id]);

        return view("dashboard.lessons.form.form", compact("lesson", "action"))
            ->with([
                "title_page" => "Ohana Pilates | Update Lesson",
                "method" => "POST"
            ]);
    }

    public function update(Lesson $lesson, UpdateLessonRequest $request)
    {
        try {
            $validated = $request->validated();

            $existingLesson = Lesson::where("name", $validated["name"])
                ->whereNull("deleted_at") // Cek hanya yang belum dihapus (soft delete)
                ->first();

            if ($existingLesson) {
                alert()->error("Oppss...", "Lesson name already exists. Please use a different name.");
                return redirect()->back()->withInput();
            }

            DB::beginTransaction();

            $lesson = Lesson::findOrFail($lesson->id);
            $lesson->name = $validated["name"];
            $lesson->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated lesson data.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error("Error updating lesson in LessonController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updated a lesson data, please try again.");
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
