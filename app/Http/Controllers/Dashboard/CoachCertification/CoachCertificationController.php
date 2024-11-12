<?php

namespace App\Http\Controllers\Dashboard\CoachCertification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CoachCertifications\CreateCoachCertificationRequest;
use App\Http\Requests\Dashboard\CoachCertifications\UpdateCoachCertificationRequest;
use App\Models\CoachCertification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CoachCertificationController extends Controller
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
        $title = "Delete Data!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.coach-certifications.index", [
            "title_page" => "Ohana Pilates | Coach Certifications"
        ]);
    }

    public function getData()
    {
        $coachCertificationDatas = CoachCertification::with("user")->get();

        return DataTables::of($coachCertificationDatas)
            ->addColumn("coach", function ($coachCertification) {
                // Ambil nama coach dari relasi user
                $coachName = ucfirst($coachCertification->user->name ?? "N/A");

                return "<strong>" . $coachName . "</strong>";
            })
            ->addColumn("action", function ($coachCertification) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. route("coach-certifications.edit", ["coachCertification" => $coachCertification->id]) .'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="'. route("coach-certifications.delete", ["coachCertification" => $coachCertification->id]) .'" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(["coach", "action"])
            ->make(true);
    }

    public function create()
    {
        $action = route("coach-certifications.store");

        $coach = User::whereHas("roles", function($query) {
            $query->where("name", "coach");
        })->get();

        return view("dashboard.coach-certifications.form.form", [
            "title_page" => "Ohana Pilates | Add New Coach Certification",
            "action" => $action,
            "coaches" => $coach,
            "method" => "POST"
        ]);
    }

    public function store(CreateCoachCertificationRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = CoachCertification::create([
                "user_id" => $validated["name"],
                "certification_name" => $validated["certification_name"],
                "date_received" => $validated["date"],
                "issuing_organization" => $validated["organization_name"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new coach certification.");
            return redirect()->route("coach-certifications.index");
        } catch (\Exception $e) {
            Log::error("Error adding coach certification in CoachCertificationController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new coach certification, please try again.");
            return redirect()->back();
        }
    }

    public function edit(CoachCertification $coachCertification)
    {
        // Eager load relasi profile dan roles
        $coachCertification->load(["user"]);

        // Gunakan compact untuk merangkum variabel ke view
        $action = route("coach-certifications.update", $coachCertification->id);

        $coach = User::whereHas("roles", function($query) {
            $query->where("name", "coach");
        })->get();

        return view("dashboard.coach-certifications.form.form", [
            "title_page" => "Ohana Pilates | Update Coach Certification",
            "action" => $action,
            "method" => "POST",
            "coaches" => $coach,
            "coachCertification" => $coachCertification
        ]);
    }

    public function update(CoachCertification $coachCertification, UpdateCoachCertificationRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $coachCertification->user_id = $validated["name"];
            $coachCertification->certification_name = $validated["certification_name"];
            $coachCertification->date_received = $validated["date"];
            $coachCertification->issuing_organization = $validated["organization_name"];
            $coachCertification->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated coach certification data.");
            return redirect()->route("coach-certifications.index");
        } catch (\Exception $e) {
            Log::error("Error updating coach certification in CoachCertificationController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating coach certification data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(CoachCertification $coachCertification)
    {
        try {
            DB::beginTransaction();

            $certification = CoachCertification::findOrFail($coachCertification->id);

            $certification->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted coach certification data.");
            return redirect()->route("coach-certifications.index");
        } catch (\Exception $e) {
            Log::error("Error deleted coach certification in CoachCertificationController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted coach certification data, please try again.");
            return redirect()->back();
        }
    }
}
