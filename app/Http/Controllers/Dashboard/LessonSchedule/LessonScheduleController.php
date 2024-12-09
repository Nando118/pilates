<?php

namespace App\Http\Controllers\Dashboard\LessonSchedule;

use App\Helpers\LessonCodeHelper;
use App\Helpers\TransactionCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LessonSchedules\CreateLessonScheduleRequest;
use App\Http\Requests\Dashboard\LessonSchedules\UpdateLessonScheduleRequest;
use App\Models\Booking;
use App\Models\CreditTransaction;
use App\Models\Lesson;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $timeSlots = TimeSlot::get();

        return view("dashboard.lesson-schedules.index", [
            "title_page" => "Ohana Pilates | Lesson Schedules",
            "timeSlots" => $timeSlots
        ]);
    }

    public function getData(Request $request)
    {
        // Ambil parameter filter dari request
        $filterDate = $request->input('date', now()->format('Y-m-d')); // Default ke hari ini
        $filterTimeSlotId = $request->input('time_slot_id', null); // Default ke null (all)

        // Query awal dengan join ke time_slots
        $query = LessonSchedule::join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
        ->select("lesson_schedules.*", "time_slots.start_time", "time_slots.duration");

        // Filter berdasarkan tanggal jika parameter 'date' diberikan
        if (!empty($filterDate) && $filterDate !== now()->format('Y-m-d')) {
            $query->whereDate('lesson_schedules.date', $filterDate);
        }

        // Filter berdasarkan time_slot_id jika ada
        if ($filterTimeSlotId) {
            $query->where('lesson_schedules.time_slot_id', $filterTimeSlotId);
        }

        // Urutkan data berdasarkan tanggal dan waktu terbaru
        $lessonScheduleDatas = $query
            ->orderBy("lesson_schedules.date", "desc")
            ->orderBy("time_slots.start_time", "desc")
            ->get();

        // DataTables processing
        return DataTables::of($lessonScheduleDatas)
            ->addColumn("date", function ($lessonSchedule) {
                return Carbon::parse($lessonSchedule->date)->format('d/m/Y');
            })
            ->addColumn("time", function ($lessonSchedule) {
                $startTime = $lessonSchedule->start_time ?? "N/A";
                $duration = $lessonSchedule->duration ?? 0;
                return "<strong>" . date("H:i", strtotime($startTime)) . "</strong><br>" . $duration . " Minute";
            })
            ->addColumn("lesson", function ($lessonSchedule) {
                $lessonName = ucfirst($lessonSchedule->lesson->name ?? "N/A");
                $lessonType = ucfirst($lessonSchedule->lessonType->name ?? "N/A");
                $coachName = ucfirst($lessonSchedule->user->name ?? "N/A");
                return "<strong>" . $lessonName . " / " . $lessonType . "</strong><br>" . $coachName;
            })
            ->addColumn("status", function ($lessonSchedule) {
                if ($lessonSchedule->deleted_at) {
                    return "<span class='text-danger'>Cancelled</span>";
                }
                return $lessonSchedule->quota <= 0 ? "Full Booking" : "Available";
            })
            ->addColumn("action", function ($lessonSchedule) {
                $currentDateTime = Carbon::now();
                $scheduleDateTime = Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->start_time);
                $disabledAttribute = $lessonSchedule->quota <= 0 ? "disabled" : "";

                if ($scheduleDateTime->greaterThanOrEqualTo($currentDateTime) && !$lessonSchedule->deleted_at) {
                    $btn = '<div class="btn-group mr-1">';
                    $btn .= '<a href="' . route("bookings.create", ["bookings" => $lessonSchedule->id]) . '" class="btn btn-primary btn-sm ' . $disabledAttribute . '" title="Booking"><i class="fas fa-fw fa-user-plus"></i></a> ';
                    $btn .= '<a href="' . route("lesson-schedules.edit", ["lessonSchedule" => $lessonSchedule->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                    $btn .= '<a href="' . route("lesson-schedules.delete", ["lessonSchedule" => $lessonSchedule->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></a> ';
                    $btn .= '</div>';
                    return $btn;
                }

                return $lessonSchedule->deleted_at ? '<span class="text-muted">Deleted</span>' : '<span class="text-muted">Not Available to edit</span>';
            })
            ->rawColumns(["time", "lesson", "status", "action"])
            ->make(true);
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $selectedDate = $request->input("date"); // Ambil input tanggal dari request

        // Ambil semua time slot yang ada
        $availableTimeSlots = TimeSlot::whereDoesntHave("schedules", function ($query) use ($selectedDate) {
            $query->where("date", $selectedDate); // Cari lesson schedules pada tanggal yang dipilih
        })->get(); // Hanya ambil time slot yang belum ada lesson schedule

        return response()->json($availableTimeSlots);
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

        return view("dashboard.lesson-schedules.form.form-add", [
            "title_page" => "Ohana Pilates | Add Lesson Schedules",
            "timeSlots" => $timeSlots,
            "lessons" => $lessons,
            "lessonTypes" => $lessonTypes,
            "coachUsers" => $coachUsers,
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateLessonScheduleRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $startDate = Carbon::parse($validated["date"]);
            $timeSlotId = $validated["time_slot"];
            $coachId = $validated["coach_user"];
            $frequency = $validated["frequency"];

            // Tentukan jumlah pengulangan berdasarkan frekuensi
            $dates = [$startDate];
            if ($frequency == "weekly") {
                for ($i = 1; $i <= 3; $i++) { // Misalnya 3 minggu ke depan
                    $dates[] = $startDate->copy()->addWeeks($i);
                }
            } elseif ($frequency == "monthly") {
                for ($i = 1; $i <= 2; $i++) { // Misalnya 2 bulan ke depan
                    $dates[] = $startDate->copy()->addMonths($i);
                }
            }

            foreach ($dates as $date) {
                // Periksa apakah coach tersedia di waktu tersebut
                $isCoachAvailable = LessonSchedule::where("user_id", $coachId)
                    ->where("date", $date)
                    ->whereHas("timeSlot", function ($query) use ($timeSlotId) {
                        $timeSlot = TimeSlot::find($timeSlotId);
                        $query->where("start_time", "<", $timeSlot->end_time)
                            ->where("end_time", ">", $timeSlot->start_time);
                    })
                    ->exists();

                if ($isCoachAvailable) {
                    DB::rollBack();
                    alert()->error("Oops...", "Coach is already scheduled at the selected time on $date, please select a different time.");
                    return redirect()->back()->withInput();
                }

                // Generate kode lesson
                $lessonCode = LessonCodeHelper::generateLessonCode();

                // Buat jadwal baru
                LessonSchedule::create([
                    "date" => $date,
                    "lesson_code" => $lessonCode,
                    "time_slot_id" => $validated["time_slot"],
                    "lesson_id" => $validated["lesson"],
                    "lesson_type_id" => $validated["lesson_type"],
                    "user_id" => $validated["coach_user"],
                    "quota" => $validated["quota"],
                    "credit_price" => $validated["credit_price"]
                ]);
            }

            DB::commit();

            alert()->success("Success", "Lesson schedules created successfully.");
            return redirect()->route("lesson-schedules.index");
        } catch (\Exception $e) {
            Log::error("Error adding lesson schedule in LessonScheduleController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oops...", "An error occurred while adding lesson schedules. Please try again.");
            return redirect()->back();
        }
    }

    public function edit(LessonSchedule $lessonSchedule)
    {
        $action = route("lesson-schedules.update", ["lessonSchedule" => $lessonSchedule->id]);
        $lessons = Lesson::get();
        $lessonTypes = LessonType::get();
        $coachUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "coach");
        })->get();

        // Dapatkan tanggal dari lessonSchedule yang akan diedit
        $selectedDate = $lessonSchedule->date;

        // Query time slots yang tersedia berdasarkan tanggal
        $timeSlots = TimeSlot::whereDoesntHave("schedules", function ($query) use ($selectedDate, $lessonSchedule) {
            // Filter time slots yang bentrok pada tanggal dan ruangan yang sama
            $query->where("date", $selectedDate)
                ->where("id", "!=", $lessonSchedule->id);  // Jangan ambil lessonSchedule yang sedang diedit
        })->get();

        return view("dashboard.lesson-schedules.form.form-edit", compact("lessonSchedule", "action", "timeSlots", "lessons", "lessonTypes", "coachUsers"))
            ->with([
                "title_page" => "Ohana Pilates | Update Lesson Schedule",
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
            $coachId = $validated["coach_user"];

            // Ambil lesson schedule yang akan diupdate
            $lessonSchedule = LessonSchedule::findOrFail($lessonSchedule->id);

            if ($coachId != $lessonSchedule->user_id) {
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
            }

            // Cek sisa Quota kelas terakhir dari Database
            $old_quota = $lessonSchedule->quota;

            // Cek apakah value Quota diubah atau tidak
            if($old_quota != $validated["quota"]) {
                $new_quota = $old_quota + intval($validated["quota"]);

                $lessonSchedule->update([
                    "quota" => $new_quota
                ]);
            }

            // 3. Jika semua validasi lolos, update jadwal
            $lessonSchedule->update([
                "date" => $validated["date"],
                "time_slot_id" => $validated["time_slot"],
                "lesson_id" => $validated["lesson"],
                "lesson_type_id" => $validated["lesson_type"],
                "user_id" => $validated["coach_user"],
                "credit_price" => $validated["credit_price"]
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

            // Pastikan lesson schedule ditemukan
            $lessonSchedule = LessonSchedule::findOrFail($lessonSchedule->id);

            // Ambil semua bookings terkait dengan lesson schedule yang akan dihapus
            $bookings = Booking::where('lesson_schedule_id', $lessonSchedule->id)->get();

            foreach ($bookings as $booking) {
                // Periksa apakah booking terkait memiliki user_id
                if ($booking->user_id) {
                    // Ambil data user terkait
                    $user = User::find($booking->user_id);

                    // Jika user ditemukan, kembalikan credit balance sesuai dengan `paid_credit` booking
                    if ($user) {
                        $user->credit_balance += $booking->paid_credit;
                        $user->save();

                        // Buat entri transaksi kredit sebagai pengembalian dana
                        CreditTransaction::create([
                            'user_id' => $user->id,
                            'type' => 'return',
                            'amount' => $booking->paid_credit,
                            'transaction_code' => TransactionCodeHelper::generateTransactionCode(),
                            'description' => $booking->paid_credit . ' credit has been returned to the account ' . $user->email . ' because the lesson schedule with code ' . $lessonSchedule->lesson_code . ' was cancelled.',
                        ]);
                    }
                }

                // Soft delete setiap booking setelah kredit dikembalikan
                $booking->delete();
            }

            // Setelah semua booking diproses, hapus lesson schedule
            $lessonSchedule->delete();

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            alert()->success("Yeay!", "Successfully deleted lesson schedule and refunded users' credits.");
            return redirect()->route("lesson-schedules.index");
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error("Error deleting lesson schedule in LessonScheduleController@destroy: " . $e->getMessage());

            // Rollback transaksi jika ada error
            DB::rollBack();

            alert()->error("Oppss...", "An error occurred while deleting lesson schedule data, please try again.");
            return redirect()->back();
        }
    }
}
