<?php

namespace App\Http\Controllers\Dashboard\Booking;

use App\Helpers\TransactionCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Bookings\CreateBookingsRequest;
use App\Models\Booking;
use App\Models\CreditTransaction;
use App\Models\LessonSchedule;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $text = "Are you sure you want to cancel the Lesson booking for this user?";
        confirmDelete($title, $text);

        $timeSlots = TimeSlot::get();

        return view("dashboard.bookings.index", [
            "title_page" => "Ohana Pilates | Bookings",
            "timeSlots" => $timeSlots
        ]);
    }

    public function getData(Request $request)
    {
        $query = Booking::query()->with(['lessonSchedule', 'lessonSchedule.timeSlot', 'user.profile']);

        // Filter berdasarkan tanggal jika ada, atau gunakan tanggal hari ini sebagai default
        if ($request->has("date") && $request->date) {
            $filterDate = Carbon::parse($request->date)->format('Y-m-d');
        } else {
            $filterDate = Carbon::today()->format('Y-m-d');
        }
        $query->whereHas('lessonSchedule', function ($q) use ($filterDate) {
            $q->whereDate('date', '=', $filterDate);
        });

        // Filter berdasarkan jam jika ada
        if ($request->has("time_slot_id") && $request->time_slot_id) {
            $query->whereHas('lessonSchedule', function ($q) use ($request) {
                $q->where('time_slot_id', '=', $request->time_slot_id);
            });
        }

        return DataTables::of($query)
            ->addColumn("phone", function ($booking) {
                return $booking->user->profile->phone ?? "N/A";
            })
            ->addColumn("lesson_code", function ($booking) {
                return $booking->lessonSchedule->lesson_code;
            })
            ->addColumn("lesson_time", function ($booking) {
                $scheduleDate = Carbon::parse($booking->lessonSchedule->date)->format('d-m-Y');
                $scheduleTime = date("H:i", strtotime($booking->lessonSchedule->timeSlot->start_time));
                return "<strong>" . $scheduleDate . "</strong><br>" . $scheduleTime;
            })
            ->addColumn("action", function ($booking) {
                $currentDateTime = Carbon::now();
                $scheduleDateTime = Carbon::parse($booking->lessonSchedule->date . ' ' . $booking->lessonSchedule->timeSlot->start_time);

                // Cek apakah jadwal masih belum lewat
                $isCancellable = $scheduleDateTime->isFuture();

                if ($isCancellable) {
                    $btn = '<div class="btn-group mr-1">';
                    $btn .= '<a href="' . route("bookings.delete", ["bookings" => $booking->id]) . '" class="btn btn-danger btn-sm" title="Cancel Booking" data-confirm-delete="true"><i class="fas fa-fw fa-ban"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                }

                // Jika jadwal sudah lewat, tombol dinonaktifkan
                return
                    '<a href="' . route("bookings.delete", ["bookings" => $booking->id]) . '" class="btn btn-danger btn-sm disabled" title="Cancel Booking" data-confirm-delete="true"><i class="fas fa-fw fa-ban"></i></a>';
            })
            ->rawColumns(["action", "lesson_time"])
            ->make(true);
    }


    public function create(LessonSchedule $bookings)
    {
        $action = route("bookings.store", ["bookings" => $bookings->id]);

        // Ambil user dengan role client yang belum melakukan booking pada lesson_schedule saat ini dan memiliki kredit lebih dari 0
        $clientUsers = User::with("profile")->whereHas("roles", function ($query) {
            $query->where("name", "client");
        })->whereDoesntHave("bookings", function ($query) use ($bookings) {
            $query->where("lesson_schedule_id", $bookings->id);
        })->where('credit_balance', '>', 0) // Filter pengguna dengan kredit lebih dari 0
        ->get();

        // Hitung kuota yang tersisa
        $remainingQuota = $bookings->quota - $bookings->bookings()->count();

        return view("dashboard.bookings.form.form", [
            "title_page" => "Ohana Pilates | Booking Lesson",
            "action" => $action,
            "method" => "POST",
            "lessonDetails" => $bookings,
            "clientUsers" => $clientUsers,
            "remainingQuota" => $remainingQuota, // Tambahkan kuota yang tersisa
        ]);
    }

    public function store(CreateBookingsRequest $request)
    {
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

            // Ambil data start_time berdasarkan start_time_id
            $timeSlot = TimeSlot::find($lessonSchedule->time_slot_id);
            if (!$timeSlot) {
                // Jika tidak ditemukan, throw error
                throw new \Exception('Time slot not found');
            }

            // Format tanggal dan waktu untuk deskripsi transaksi
            $formattedDate = \Carbon\Carbon::parse($lessonSchedule->date)->format('d-m-Y');
            $formattedTime = \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i');

            // Simpan setiap nama ke tabel bookings
            foreach ($names as $name) {
                // Cek apakah nama ini adalah user yang terdaftar
                $user = User::where("id", $name)->first(); // Update: cek user berdasarkan id

                // Jika terdaftar, akan dilakukan pengecekan credit_balance
                if ($user) {
                    // Cek saldo user apakah cukup untuk booking lesson atau tidak
                    if ($user->credit_balance < $lessonSchedule->credit_price) {
                        alert()->warning("Warning", "User {$user->name} does not have enough credit balance to book this lesson.");
                        return redirect()->back();
                    }

                    // Jika cukup, kurangi credit balance
                    $user->credit_balance -= $lessonSchedule->credit_price;
                    $user->save();

                    CreditTransaction::query()->create([
                        "user_id" => $user->id,
                        "type" => "deduct",
                        "amount" => $lessonSchedule->credit_price,
                        "transaction_code" => TransactionCodeHelper::generateTransactionCode(),
                        "description" => $lessonSchedule->credit_price . ' credit has been deducted from the account '. $user->email .' , to make a booking for the lesson on '. $formattedDate .' - '. $formattedTime .'.'
                    ]);
                }

                // Simpan booking baru
                Booking::create([
                    "lesson_schedule_id" => $lessonSchedule->id,
                    "paid_credit" => $lessonSchedule->credit_price,
                    "booked_by_name" => $user ? $user->name : $name, // Update: simpan nama dari database atau nama input
                    "user_id" => $user ? $user->id : null, // Update: simpan user_id jika terdaftar, null jika tidak
                ]);

                // Kurangi kuota
                $lessonSchedule->quota -= 1;
            }

            // Simpan perubahan pada lesson_schedules
            $lessonSchedule->save();
            // Commit transaksi jika semua berhasil
            DB::commit();

            alert()->success("Yeay!", "Successfully booked a lesson.");
            return redirect()->route("lesson-schedules.index");
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

            // Ambil data booking berdasarkan id
            $booking = Booking::findOrFail($bookings->id);

            // Ambil lesson schedule terkait dari booking yang akan dihapus
            $lessonSchedule = LessonSchedule::where("id", $booking->lesson_schedule_id)->first();

            if (!$lessonSchedule) {
                alert()->error("Oppss...", "Lesson schedule not found.");
                return redirect()->route("bookings.index");
            }

            // Kembalikan kuota yang sudah terpakai
            $lessonSchedule->quota += 1;
            $lessonSchedule->save();

            // Periksa apakah booking terkait memiliki user_id
            if ($booking->user_id) {
                // Ambil data user terkait
                $user = User::find($booking->user_id);

                // Jika ketemu, return credit balance user tersebut
                if ($user) {
                    // Tambahkan kembali credit balance untuk pengguna yang melakukan booking
                    $user->credit_balance += $booking->paid_credit; // Gunakan nilai paid_credit yang disimpan
                    $user->save();

                    CreditTransaction::query()->create([
                        "user_id" => $user->id,
                        "type" => "return",
                        "amount" => $booking->paid_credit,
                        "transaction_code" => TransactionCodeHelper::generateTransactionCode(),
                        "description" => $booking->paid_credit . ' credit has been returned to the account ' . $user->email . ', because the booking for the lesson code has been cancelled ' . $lessonSchedule->lesson_code . '.'
                    ]);
                }
            }

            // Soft delete booking
            $booking->delete();

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
