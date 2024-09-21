<?php

namespace App\Http\Controllers\Dashboard\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Bookings\CreateBookingsRequest;
use App\Models\Booking;
use App\Models\LessonSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
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
        $title = "Delete Booking!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.bookings.index", [
            "title_page" => "Pilates | Bookings"
        ]);
    }

    public function getData()
    {
        $bookings = Booking::get();

        return DataTables::of($bookings)
            ->addColumn("lesson", function ($booking) {
                $lessonName = ucfirst($booking->lessonSchedule->lesson->name ?? "N/A");
                $lessonType = ucfirst($booking->lessonSchedule->lessonType->name ?? "N/A");
                $coachName = ucfirst($booking->lessonSchedule->user->name ?? "N/A");

                return "<strong>" . $lessonName . " / " . $lessonType . "</strong>" . "<br>" . $coachName;
            })
            ->addColumn("date", function ($booking) {
                $date = $booking->lessonSchedule->date ?? "N/A";
                $time = $booking->lessonSchedule->timeSlot->start_time ?? "N/A";

                return "<strong>" . $date . "</strong>" . "<br>" . $time;
            })
            ->addColumn("username", function ($booking) {
                $username = $booking->user->profile->username ?? "-";

                return $username;
            })
            ->addColumn("action", function ($booking) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="' . route("bookings.delete", ["bookings" => $booking->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(["lesson", "date", "action"])
            ->make(true);
    }

    public function create(LessonSchedule $bookings)
    {
        $action = route("bookings.store", ["bookings" => $bookings->id]);
        $clientUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "client");
        })->get();

        // Hitung kuota yang tersisa
        $remainingQuota = $bookings->quota - $bookings->bookings()->count(); // Misalkan ada relasi bookings() pada LessonSchedule

        return view("dashboard.bookings.form.form", [
            "title_page" => "Pilates | Booking Lesson",
            "action" => $action,
            "method" => "POST",
            "lessonDetails" => $bookings,
            "clientUsers" => $clientUsers,
            "remainingQuota" => $remainingQuota, // Tambahkan kuota yang tersisa
        ]);
    }

    public function store(CreateBookingsRequest $request) {
        try {
            $validated = $request->validated();

            // Gunakan transaksi untuk memastikan konsistensi data
            DB::beginTransaction();

            // Dapatkan lesson_schedule dengan locking
            $lessonSchedule = LessonSchedule::where("id", $validated["id"])->lockForUpdate()->first();

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
            return redirect()->route("bookings.index");
        } catch (\Exception $e) {
            Log::error("Error adding booking data in BookingController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while making a booking, please try again.");
            return redirect()->back();
        }
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
            return redirect()->route("bookings.index");
        } catch (\Exception $e) {
            // Menyimpan log error jika terjadi kesalahan
            Log::error("Error deleting booking in BookingController@destroy: " . $e->getMessage());

            // Rollback transaksi jika ada error
            DB::rollBack();

            // Menampilkan pesan error menggunakan SweetAlert2
            alert()->error("Oppss...", "An error occurred while canceling the lesson schedule booking for this user, please try again.");
            return redirect()->back();
        }

    }
}
