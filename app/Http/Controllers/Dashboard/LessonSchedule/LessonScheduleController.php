<?php

namespace App\Http\Controllers\Dashboard\LessonSchedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LessonSchedules\CreateLessonScheduleRequest;
use App\Http\Requests\Dashboard\LessonSchedules\UpdateLessonScheduleRequest;
use App\Models\Lesson;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
        $title = "Delete Lesson Schedule!";
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
                $btn .= '<button class="btn btn-primary btn-sm add-booking-btn" data-id="' . $lessonSchedule->id . '" data-quota="' . $lessonSchedule->quota . '" title="Add Booking"><i class="fas fa-user-plus"></i></button> ';
                $btn .= '<a href="' . route("lesson-schedules.edit", ["lessonSchedule" => $lessonSchedule->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("lesson-schedules.delete", ["lessonSchedule" => $lessonSchedule->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
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

    public function store(CreateLessonScheduleRequest $request)
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();

            $date = $validated["date"];
            $timeSlotId = $validated["time_slot"]; // Assumed time slot contains start_time and end_time
            $roomId = $validated["room"];
            $coachId = $validated["coach_user"];

            // 1. Cek apakah ruangan tersedia untuk waktu yang sama
            $isRoomAvailable = LessonSchedule::isTimeSlotAvailable($date, $timeSlotId, $roomId);

            if (!$isRoomAvailable) {
                // Jika ruangan tidak tersedia, rollback transaksi dan kirim pesan error
                DB::rollBack();
                alert()->error("Oppss...", "Room is not available for the selected time, please select a different room or time.");
                return redirect()->back()->withInput();
            }

            // 2. Cek apakah coach sudah terjadwal dalam rentang waktu yang sama
            $isCoachAvailable = LessonSchedule::where("user_id", $coachId)
            ->where("date",
                $date
            )
            ->whereHas("timeSlot", function ($query) use ($timeSlotId) {
                $timeSlot = TimeSlot::find($timeSlotId);
                $query->where("start_time", "<", $timeSlot->end_time)
                ->where("end_time", ">", $timeSlot->start_time);
            })
            ->exists();

            if ($isCoachAvailable) {
                // Jika coach sudah dijadwalkan pada waktu yang sama, rollback transaksi dan kirim pesan error
                DB::rollBack();
                alert()->error("Oppss...", "Room is not available for the selected time, please select a different room or time.");
                return redirect()->back()->withInput();
            }

            // 3. Jika semua validasi lolos, buat jadwal baru
            LessonSchedule::create([
                "date" => $validated["date"],
                "time_slot_id" => $validated["time_slot"],
                "lesson_id" => $validated["lesson"],
                "lesson_type_id" => $validated["lesson_type"],
                "user_id" => $validated["coach_user"],
                "room_id" => $validated["room"],
                "quota" => $validated["quota"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new lesson schedule data.");
            return redirect()->route("lesson-schedules.index");
        } catch (\Exception $e) {
            Log::error("Error adding lesson schedule in LessonScheduleController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new lesson schedule data, please try again.");
            return redirect()->back();
        }
    }

    public function edit(LessonSchedule $lessonSchedule)
    {
        $action = route("lesson-schedules.update", ["lessonSchedule" => $lessonSchedule->id]);
        $timeSlots = TimeSlot::get();
        $lessons = Lesson::get();
        $lessonTypes = LessonType::get();
        $coachUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "coach");
        })->get();
        $rooms = Room::get();

        return view("dashboard.lesson-schedules.form.form", compact("lessonSchedule", "action", "timeSlots", "lessons", "lessonTypes", "coachUsers", "rooms"))
        ->with([
            "title_page" => "Pilates | Update Lesson Schedule",
            "method" => "POST"
        ]);
    }

    public function update(LessonSchedule $lessonSchedule, UpdateLessonScheduleRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $date = $validated["date"];
            $timeSlotId = $validated["time_slot"];
            $roomId = $validated["room"];
            $coachId = $validated["coach_user"];

            // Ambil lesson schedule yang akan diupdate
            $lessonSchedule = LessonSchedule::findOrFail($lessonSchedule->id);

            // 1. Cek apakah ruangan tersedia untuk waktu yang sama, kecuali ruangan yang sama yang sedang diupdate
            $isRoomAvailable = LessonSchedule::isTimeSlotAvailable($date, $timeSlotId, $roomId, $lessonSchedule->id);

            if (!$isRoomAvailable) {
                DB::rollBack();
                alert()->error("Oppss...", "Room is not available for the selected time, please select a different room or time.");
                return redirect()->back()->withInput();
            }

            // 2. Cek apakah coach sudah terjadwal dalam rentang waktu yang sama, kecuali coach yang sama yang sedang diupdate
            $isCoachAvailable = LessonSchedule::where("user_id", $coachId)
                ->where("date", $date)
                ->where("id", '!=', $lessonSchedule->id) // Pastikan tidak memeriksa jadwal yang sama
                ->whereHas("timeSlot", function ($query) use ($timeSlotId) {
                    $timeSlot = TimeSlot::find($timeSlotId);
                    $query->where("start_time", "<", $timeSlot->end_time)
                        ->where("end_time", ">", $timeSlot->start_time);
                })
                ->exists();

            if ($isCoachAvailable) {
                DB::rollBack();
                alert()->error("Oppss...", "Coach is already scheduled at the selected time, please select a different time.");
                return redirect()->back()->withInput();
            }

            // 3. Jika semua validasi lolos, update jadwal
            $lessonSchedule->update([
                "date" => $validated["date"],
                "time_slot_id" => $validated["time_slot"],
                "lesson_id" => $validated["lesson"],
                "lesson_type_id" => $validated["lesson_type"],
                "user_id" => $validated["coach_user"],
                "room_id" => $validated["room"],
                "quota" => $validated["quota"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully updated lesson schedule data.");
            return redirect()->route("lesson-schedules.index");
        } catch (\Exception $e) {
            Log::error("Error updating lesson schedule in LessonScheduleController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating the lesson schedule data, please try again.");
            return redirect()->back();
        }

    }

    public function destroy(LessonSchedule $lessonSchedule)
    {
        try {
            DB::beginTransaction();

            $lessonSchedule = LessonSchedule::findOrFail($lessonSchedule->id);

            $lessonSchedule->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted lesson schedule data.");
            return redirect()->route("lesson-schedules.index");
        } catch (\Exception $e) {
            Log::error("Error deleting lesson schedule in LessonScheduleController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted lesson schedule data, please try again.");
            return redirect()->back();
        }
    }
}
