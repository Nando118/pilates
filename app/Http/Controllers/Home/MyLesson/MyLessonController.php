<?php

namespace App\Http\Controllers\Home\MyLesson;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LessonSchedule;
use Illuminate\Http\Request;
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
        $myBookings = Booking::where("user_id", $user_id)
            ->with(["lessonSchedule.lesson", "lessonSchedule.lessonType", "lessonSchedule.user", "lessonSchedule.timeSlot", "user.profile"]) // Eager load relasi yang diperlukan
            ->get();
        
        return view("home.my-lesson-schedules.index", [
            "title_page" => "Pilates | My Lesson Schedules",
            "myBookings" => $myBookings
        ]);
    }

    public function destroy(Booking $bookings)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Ambil data booking berdasarkan id dari request
            $bookings = Booking::findOrFail($bookings->id); // Di sini gunakan $id yang dikirimkan ke function

            // Ambil lesson schedule terkait dari booking yang akan dihapus
            $lessonSchedule = LessonSchedule::where("id", "=", $bookings->lesson_schedule_id)->first();

            if ($lessonSchedule) {
                // Kembalikan kuota yang sudah terpakai
                $lessonSchedule->quota += 1;

                // Simpan perubahan kuota
                $lessonSchedule->save();
            } else {
                // Jika lesson schedule tidak ditemukan
                throw new \Exception('Lesson schedule not found.');
            }

            // Soft delete booking
            $bookings->delete();

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
