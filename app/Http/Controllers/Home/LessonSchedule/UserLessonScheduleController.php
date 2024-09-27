<?php

namespace App\Http\Controllers\Home\LessonSchedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\Bookings\UserCreateBookingsRequest;
use App\Models\Booking;
use App\Models\LessonSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UserLessonScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-user-home")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        $lessonScheduleDatas = LessonSchedule::with(['timeSlot', 'lesson', 'lessonType', 'user', 'room'])->get();

        return view("home.lesson-schedules.index", [
            "title_page" => "Pilates | Lesson Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas
        ]);
    }

    public function create(LessonSchedule $bookings)
    {
        $action = route("user-lesson-schedules.store", ["bookings" => $bookings->id]);
        $clientUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "client");
        })->get();

        // Hitung kuota yang tersisa
        $remainingQuota = $bookings->quota - $bookings->bookings()->count(); // Misalkan ada relasi bookings() pada LessonSchedule

        return view("home.lesson-schedules.form.form", [
            "title_page" => "Pilates | Booking Lesson",
            "action" => $action,
            "method" => "POST",
            "lessonDetails" => $bookings,
            "clientUsers" => $clientUsers,
            "remainingQuota" => $remainingQuota, // Tambahkan kuota yang tersisa
        ]);
    }

    public function store(UserCreateBookingsRequest $request)
    {
        try {
            $validated = $request->validated();

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::beginTransaction();

            // Dapatkan lesson_schedule dengan locking
            $lessonSchedule = LessonSchedule::where("id", $validated["id"])->lockForUpdate()->first();

            // Cek apakah tanggal dan waktu sudah lewat
            $currentDateTime = now(); // Waktu saat ini
            $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . ' ' . $lessonSchedule->timeSlot->start_time);

            if ($currentDateTime->greaterThanOrEqualTo($lessonStartTime)) {
                // Jika sudah lewat, rollback dan kirim alert
                alert()->warning("Warning", "Cannot book this lesson because it has already started.");
                return redirect()->back();
            }

            // Cek apakah kuota masih tersedia
            if ($lessonSchedule->quota <= 0) {
                // Jika kuota sudah habis, rollback dan kirim alert
                alert()->warning("Warning", "This lesson schedule is fully booked.");
                return redirect()->back();
            }

            // Hitung jumlah nama yang diinput
            $names = $validated["name"];
            $totalNames = count($names);

            // Validasi: Pastikan jumlah nama tidak melebihi sisa kuota
            if ($totalNames > $lessonSchedule->quota) {
                // Batasi jumlah nama sesuai kuota yang tersedia
                $names = array_slice($names, 0, $lessonSchedule->quota);
            }

            // Simpan setiap nama ke tabel bookings
            foreach ($names as $name) {
                // Cek apakah nama ini adalah user yang terdaftar
                $user = User::where("id", $name)->first(); // Update: cek user berdasarkan id

                // Cek apakah user sudah terdaftar di lesson ini
                $existingBooking = Booking::where("lesson_schedule_id", $lessonSchedule->id)
                    ->where("user_id", $user ? $user->id : null)
                    ->first();
                if ($existingBooking) {
                    // Jika user sudah terdaftar, rollback dan kirim alert
                    alert()->warning("Warning", "You or one of the Names you entered is already registered for this lesson schedule.");
                    return redirect()->back();
                }

                // Simpan booking baru
                Booking::create([
                    "lesson_schedule_id" => $lessonSchedule->id,
                    "booked_by_name" => $user ? $user->name : $name, // Update: simpan nama dari database atau nama input
                    "user_id" => $user ? $user->id : null, // Update: simpan user_id jika terdaftar, null jika tidak
                ]);
                // Kurangi kuota
                $lessonSchedule->quota -= 1;
            }

            // Update status jika kuota habis
            if ($lessonSchedule->quota <= 0) {
                $lessonSchedule->status = "Full Booked";
            }

            // Simpan perubahan pada lesson_schedules
            $lessonSchedule->save();

            // Commit transaksi jika semua berhasil
            DB::commit();

            alert()->success("Yeay!", "Successfully booked a lesson.");
            return redirect()->route("user-lesson-schedules.index");
        } catch (\Exception $e) {
            Log::error("Error adding booking data in UserLessonScheduleController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while making a booking, please try again.");
            return redirect()->back();
        }
    }

}
