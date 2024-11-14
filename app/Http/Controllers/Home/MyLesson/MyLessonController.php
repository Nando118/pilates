<?php

namespace App\Http\Controllers\Home\MyLesson;

use App\Helpers\TransactionCodeHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CreditTransaction;
use App\Models\LessonSchedule;
use App\Models\LessonType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MyLessonController extends Controller
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
        $title = "Delete Booking!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $user_id = Auth::id();
        $myBookings = Booking::where("bookings.user_id", $user_id)
            ->join("lesson_schedules", "bookings.lesson_schedule_id", "=", "lesson_schedules.id") // Join dengan lesson_schedules
            ->join("time_slots", "lesson_schedules.time_slot_id", "=", "time_slots.id") // Join dengan time_slots
            ->with(["lessonSchedule.lesson", "lessonSchedule.lessonType", "lessonSchedule.user", "lessonSchedule.timeSlot", "user.profile"]) // Eager load relasi yang diperlukan
            ->select("bookings.*") // Pilih kolom dari bookings
            ->orderBy("lesson_schedules.date") // Urutkan berdasarkan tanggal
            ->orderBy("time_slots.start_time") // Urutkan berdasarkan start_time
            ->get();

        $lessonTypes = LessonType::get();

        return view("home.my-lesson-schedules.index", [
            "title_page" => "Ohana Pilates | My Lesson Schedules",
            "myBookings" => $myBookings,
            "lessonTypes" => $lessonTypes
        ]);
    }

    public function destroy(Booking $bookings)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Ambil data booking berdasarkan id
            $booking = Booking::findOrFail($bookings->id);

            // Ambil lesson schedule terkait dari booking yang akan dihapus
            $lessonSchedule = LessonSchedule::where("id", $booking->lesson_schedule_id)->first();

            if (!$lessonSchedule) {
                alert()->error("Oppss...", "Lesson schedule not found.");
                return redirect()->route("bookings.index");
            }

            // Periksa apakah booking terkait memiliki user_id
            if ($booking->user_id) {
                // Ambil data user terkait
                $user = User::find($booking->user_id);

                if ($user) {
                    // Tambahkan kembali credit balance untuk pengguna
                    $user->credit_balance += $lessonSchedule->credit_price;
                    $user->save();

                    // Catat transaksi pengembalian kredit
                    CreditTransaction::query()->create([
                        "user_id" => $user->id,
                        "type" => "return",
                        "amount" => $lessonSchedule->credit_price,
                        "transaction_code" => TransactionCodeHelper::generateTransactionCode(),
                        "description" => $lessonSchedule->credit_price . ' credit has been returned to the account ' . $user->email . ' , because the booking for the lesson code has been cancelled ' . $lessonSchedule->lesson_code . '.'
                    ]);
                }
            }

            // Kembalikan kuota yang sudah terpakai
            $lessonSchedule->quota += 1;
            $lessonSchedule->save();

            // Soft delete booking
            $booking->delete();

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            // Menampilkan pesan sukses menggunakan SweetAlert2
            alert()->success("Yeay!", "Successfully canceled the lesson schedule booking for this user.");
            return redirect()->route("my-lesson-schedules.index");
        } catch (\Exception $e) {
            // Menyimpan log error jika terjadi kesalahan
            Log::error("Error deleting booking in MyLessonController@destroy: " . $e->getMessage());

            // Rollback transaksi jika ada error
            DB::rollBack();

            // Menampilkan pesan error menggunakan SweetAlert2
            alert()->error("Oppss...", "An error occurred while canceling the lesson schedule booking for this user, please try again.");
            return redirect()->back();
        }
    }

}
