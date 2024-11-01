<?php

namespace App\Http\Controllers\Dashboard\LessonType;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LessonTypes\CreateLessonTypeRequest;
use App\Http\Requests\Dashboard\LessonTypes\UpdateLessonTypeRequest;
use App\Models\LessonType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class LessonTypeController extends Controller
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
        $title = "Delete Lesson Type!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.lesson-types.index", [
            "title_page" => "Pilates | Lesson Types"
        ]);
    }

    public function getData()
    {
        $lessonTypeDatas = LessonType::get();

        return DataTables::of($lessonTypeDatas)
            ->addColumn("action", function ($lessonType) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="' . route("lesson-types.edit", ["lessonType" => $lessonType->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("lesson-types.delete", ["lessonType" => $lessonType->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("lesson-types.store");

        return view("dashboard.lesson-types.form.form", [
            "title_page" => "Pilates | Add Lesson Type",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateLessonTypeRequest $request)
    {
        try {
            $validated = $request->validated();

            $existingLessonType = LessonType::where("name", $validated["name"])
                ->whereNull("deleted_at") // Cek hanya yang belum dihapus (soft delete)
                ->first();

            if ($existingLessonType) {
                alert()->error("Oppss...", "Lesson type name already exists. Please use a different name.");
                return redirect()->back()->withInput();
            }

            DB::beginTransaction();

            LessonType::create([
                "name" => $validated["name"],
                "quota" => $validated["quota"],
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new lesson type data.");
            return redirect()->route("lesson-types.index");
        } catch (\Exception $e) {
            Log::error("Error adding lesson type in LessonTypeController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new lesson type data, please try again.");
            return redirect()->back();
        }
    }

    public function edit(LessonType $lessonType)
    {
        $action = route("lesson-types.update", ["lessonType" => $lessonType->id]);

        return view("dashboard.lesson-types.form.form", compact("lessonType", "action"))
            ->with([
                "title_page" => "Pilates | Update Lesson Type",
                "method" => "POST"
            ]);
    }

    public function update(LessonType $lessonType, UpdateLessonTypeRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $lessonType = LessonType::findOrFail($lessonType->id);
            $lessonType->name = $validated["name"];
            $lessonType->quota = $validated["quota"];
            $lessonType->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated lesson type data.");
            return redirect()->route("lesson-types.index");
        } catch (\Exception $e) {
            Log::error("Error updating lesson type in LessonTypeController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updated a lesson type data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(LessonType $lessonType)
    {
        try {
            DB::beginTransaction();

            $lessonType = LessonType::findOrFail($lessonType->id);

            $lessonType->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted lesson type data.");
            return redirect()->route("lesson-types.index");
        } catch (\Exception $e) {
            Log::error("Error deleting lesson type in LessonTypeController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted lesson type data, please try again.");
            return redirect()->back();
        }
    }
}
