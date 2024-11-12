<?php

namespace App\Http\Controllers\Dashboard\TimeSlot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TimeSlots\CreateTimeSlotRequest;
use App\Http\Requests\Dashboard\TimeSlots\UpdateTimeSlotRequest;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TimeSlotController extends Controller
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
        $title = "Delete Time Slot!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.time-slots.index", [
            "title_page" => "Ohana Pilates | Time Slots"
        ]);
    }

    public function getData()
    {
        $timeSlotDatas = TimeSlot::query()->get();

        return DataTables::of($timeSlotDatas)
            ->addColumn("start_time", function ($timeSlotData) {
                $startTime = $timeSlotData->start_time ?? "N/A";

                return date("H:i", strtotime($startTime));
            })
            ->addColumn("end_time", function ($timeSlotData) {
                $endTime = $timeSlotData->end_time ?? "N/A";

                return date("H:i", strtotime($endTime));
            })
            ->addColumn("duration", function ($timeSlotData) {
                $duration = $timeSlotData->duration ?? "N/A";

                return $duration . " Minute";
            })
            ->addColumn("action", function ($timeSlot) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="' . route("time-slots.edit", ["timeSlot" => $timeSlot->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("time-slots.delete", ["timeSlot" => $timeSlot->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("time-slots.store");

        return view("dashboard.time-slots.form.form", [
            "title_page" => "Ohana Pilates | Add New Time Slot",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateTimeSlotRequest $request)
    {
        try {
            $validated = $request->validated();

            $newStartTime = Carbon::createFromFormat("H:i", $validated["start_time"]);
            $newEndTime = Carbon::createFromFormat("H:i", $validated["end_time"]);
            $duration = $validated["duration"];

            $availableDuration = $newStartTime->diffInMinutes($newEndTime);

            if ($duration > $availableDuration) {
                alert()->error("Oppss...", "The duration cannot be more than the time available between Start Time and End Time.");
                return redirect()->back()->withInput();
            }

            // Cek apakah time slot bentrok dengan yang sudah ada di database
            $isConflict = TimeSlot::where(function ($query) use ($newStartTime, $newEndTime) {
                $query->where("start_time", "<", $newEndTime)
                    ->where("end_time", ">", $newStartTime);
            })->exists();

            if ($isConflict) {
                alert()->error("Oppss...", "The created time already exists or conflicts with an existing time. Please enter another time to avoid schedule conflicts.");
                return redirect()->back()->withInput();
            }

            DB::beginTransaction();

            // Jika tidak ada bentrok, simpan time slot baru
            TimeSlot::create([
                "start_time" => $newStartTime,
                "end_time" => $newEndTime,
                "duration" => $duration,
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new time slot data.");
            return redirect()->route("time-slots.index");
        } catch (\Exception $e) {
            Log::error("Error adding time slot in TimeSlotController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new time slot data, please try again.");
            return redirect()->back();
        }
    }

    public function edit(TimeSlot $timeSlot)
    {
        $action = route("time-slots.update", ["timeSlot" => $timeSlot->id]);

        return view("dashboard.time-slots.form.form", compact("timeSlot", "action"))
        ->with([
            "title_page" => "Ohana Pilates | Update Time Slot",
            "method" => "POST"
        ]);
    }

    public function update(TimeSlot $timeSlot, UpdateTimeSlotRequest $request)
    {
        try {
            $validated = $request->validated();

            $newStartTime = Carbon::createFromFormat("H:i", $validated["start_time"]);
            $newEndTime = Carbon::createFromFormat("H:i", $validated["end_time"]);
            $duration = $validated["duration"];

            $availableDuration = $newStartTime->diffInMinutes($newEndTime);

            if ($duration > $availableDuration) {
                alert()->error("Oppss...", "The duration cannot be more than the time available between Start Time and End Time.");
                return redirect()->back()->withInput();
            }

            // Ambil data time slot dari database
            $timeSlot = TimeSlot::findOrFail($timeSlot->id);

            // Konversi waktu dari database ke format Carbon untuk membandingkan secara tepat
            $dbStartTime = Carbon::createFromFormat("H:i:s", $timeSlot->start_time);
            $dbEndTime = Carbon::createFromFormat("H:i:s", $timeSlot->end_time);

            // Hanya lakukan validasi isConflict jika start_time atau end_time berubah
            if (!$newStartTime->equalTo($dbStartTime) || !$newEndTime->equalTo($dbEndTime)) {
                // Cek apakah time slot bentrok dengan yang sudah ada di database
                $isConflict = TimeSlot::where(function ($query) use ($newStartTime, $newEndTime) {
                    $query->where("start_time", "<", $newEndTime)
                        ->where("end_time", ">", $newStartTime);
                })->exists();

                if ($isConflict) {
                    alert()->error("Oppss...", "The created time clashes with an existing time. Please enter another time to avoid a schedule clash.");
                    return redirect()->back()->withInput();
                }
            }

            DB::beginTransaction();

            $timeSlot->start_time = $validated["start_time"];
            $timeSlot->end_time = $validated["end_time"];
            $timeSlot->duration = $validated["duration"];
            $timeSlot->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated new time slot data.");
            return redirect()->route("time-slots.index");
        } catch (\Exception $e) {
            Log::error("Error updated time slot in TimeSlotController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updated a new time slot data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(TimeSlot $timeSlot)
    {
        try {
            DB::beginTransaction();

            $lesson = TimeSlot::findOrFail($timeSlot->id);

            $lesson->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted time slot data.");
            return redirect()->route("time-slots.index");
        } catch (\Exception $e) {
            Log::error("Error deleted time slot in TimeSlotController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted time slot data, please try again.");
            return redirect()->back();
        }
    }
}
