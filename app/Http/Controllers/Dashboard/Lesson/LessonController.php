<?php

namespace App\Http\Controllers\Dashboard\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonCreateRequest;
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

        return view("dashboard.lesson.index", [
            "title_page" => "Pilates | Lesson"
        ]);
    }

    public function getLessonsData()
    {
        $lessonDatas = Lesson::query()->get();

        foreach ($lessonDatas as $lesson) {
            $lesson->name = ucfirst($lesson->name);
            $lesson->type = ucfirst($lesson->type);
        }

        return DataTables::of($lessonDatas)
            ->addColumn('action', function ($lesson) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. route("lessons.edit", ["id" => $lesson->id]) .'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("lessons.delete", ["id" => $lesson->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("lessons.store");

        return view("dashboard.lesson.form.form", [
            "title_page" => "Pilates | Add New Lesson",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(LessonCreateRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($validated) {
                DB::beginTransaction();

                $lesson = Lesson::query()->create([
                    "name" => $request['name'],
                    "type" => $request['type'],
                    "quota" => $request['quota']
                ]);

                DB::commit();

                alert()->success("Yeay!", "Successfully added new lesson.");
                return redirect()->route("lessons.index");
            } else {
                DB::rollBack();
                alert()->error("Oppss...", "An error occurred while adding a new lesson, please try again.");
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new lesson, please try again.");
            return redirect()->back();
        }
    }

    public function edit($lessonId)
    {
        $action = route("lessons.update", ["id" => $lessonId]);

        $lessonData = Lesson::query()->where("id", "=", $lessonId)->firstOrFail();

        return view("dashboard.lesson.form.form", [
            "title_page" => "Pilates | Update Lesson",
            "action" => $action,
            "method" => "POST",
            "lesson_data" => $lessonData
        ]);
    }

    public function update($lessonId, LessonCreateRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($validated) {
                DB::beginTransaction();

                $user = Lesson::findOrFail($lessonId);
                $user->name = $validated['name'];
                $user->type = $validated['type'];
                $user->quota = $validated['quota'];
                $user->save();

                DB::commit();

                alert()->success("Yeay!", "Successfully updated lesson data.");
                return redirect()->route("lessons.index");
            } else {
                DB::rollBack();
                alert()->error("Oppss...", "An error occurred while updating lesson data, please try again.");
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating lesson data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $lesson = Lesson::findOrFail($id);

            $lesson->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted lesson data.");
            return redirect()->route("lessons.index");
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted lesson data, please try again.");
            return redirect()->back();
        }
    }
}
