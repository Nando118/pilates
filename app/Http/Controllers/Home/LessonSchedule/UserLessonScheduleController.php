<?php

namespace App\Http\Controllers\Home\LessonSchedule;

use App\Helpers\TransactionCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\Bookings\UserCreateBookingsRequest;
use App\Models\Booking;
use App\Models\CreditTransaction;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $userId = Auth::id(); // ID pengguna yang sedang login

        // Ambil semua id lesson_schedule yang sudah dipesan pengguna
        $userBookings = Booking::where("user_id", $userId)
        ->pluck("lesson_schedule_id")
        ->toArray();

        // Ambil data lesson schedule dengan eager loading dan urutkan berdasarkan date dan start_time
        $lessonScheduleDatas = LessonSchedule::withTrashed()
        ->with(["timeSlot", "lesson", "lessonType", "user"])
        ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id")
        ->select("lesson_schedules.*")
        ->orderBy("lesson_schedules.date")
        ->orderBy("time_slots.start_time")
        ->get();

        $lessonTypes = LessonType::all();

        return view("home.lesson-schedules.index", [
            "title_page" => "Ohana Pilates | Lesson Schedules",
            "lessonScheduleDatas" => $lessonScheduleDatas,
            "lessonTypes" => $lessonTypes,
            "userBookings" => $userBookings // Kirim data booking pengguna
        ]);
    }

    public function create(LessonSchedule $bookings)
    {
        $action = route("user-lesson-schedules.store", ["bookings" => $bookings->id]);
        $clientUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "client");
        })->get();

        return view("home.lesson-schedules.form.form", [
            "title_page" => "Ohana Pilates | Booking Lesson",
            "action" => $action,
            "method" => "POST",
            "lessonDetails" => $bookings,
            "clientUsers" => $clientUsers
        ]);
    }

    public function store(UserCreateBookingsRequest $request)
    {
        try {
            $validated = $request->validated();

            // Dapatkan user yang sedang login
            $userId = Auth::id();
            $currentUser = User::where("id", $userId)->first();

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::beginTransaction();

            // Dapatkan lesson_schedule dengan locking
            $lessonSchedule = LessonSchedule::where("id", $validated["id"])->lockForUpdate()->first();

            // Cek apakah tanggal dan waktu sudah lewat
            $currentDateTime = now();
            $lessonStartTime = \Carbon\Carbon::parse($lessonSchedule->date . " " . $lessonSchedule->timeSlot->start_time);

            if ($currentDateTime->greaterThanOrEqualTo($lessonStartTime)) {
                alert()->warning("Warning", "Cannot book this lesson because it has already started.");
                return redirect()->back();
            }

            // Cek apakah kuota masih tersedia
            if ($lessonSchedule->quota <= 0) {
                alert()->warning("Warning", "This lesson schedule is fully booked.");
                return redirect()->back();
            }

            // Cek apakah user sudah terdaftar di lesson ini
            $existingBooking = Booking::where("lesson_schedule_id", $lessonSchedule->id)
                ->where("user_id", $currentUser->id)
                ->first();

            if ($existingBooking) {
                alert()->warning("Warning", "You are already registered for this lesson schedule.");
                return redirect()->back();
            }

            // Cek saldo user apakah cukup untuk booking lesson atau tidak
            if ($currentUser->credit_balance < $lessonSchedule->credit_price) {
                alert()->warning("Warning", "You do not have enough credits to book this lesson.");
                return redirect()->back();
            }

            // Jika cukup, kurangi credit balance
            $currentUser->credit_balance -= $lessonSchedule->credit_price;
            $currentUser->save();

            // Catat transaksi kredit
            CreditTransaction::create([
                "user_id" => $currentUser->id,
                "type" => "deduct",
                "amount" => $lessonSchedule->credit_price,
                "transaction_code" => TransactionCodeHelper::generateTransactionCode(),
                "description" => $lessonSchedule->credit_price . ' credits have been deducted from the account ' . $currentUser->email . ' for booking the lesson code ' . $lessonSchedule->lesson_code . '.'
            ]);

            // Simpan booking baru
            Booking::create([
                "lesson_schedule_id" => $lessonSchedule->id,
                "paid_credit" => $lessonSchedule->credit_price,
                "booked_by_name" => $currentUser->name,
                "user_id" => $currentUser->id,
            ]);

            // Kurangi kuota
            $lessonSchedule->quota -= 1;

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
