<?php

namespace App\Http\Controllers\Home\Home;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeUserController extends Controller
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
        $user = Auth::user();

        // Array kutipan Pilates
        $quotes = [
            "Pilates is complete coordination of body, mind, and spirit.",
            "In 10 sessions you’ll feel the difference, in 20 you’ll see the difference, and in 30 you’ll have a whole new body.",
            "You will feel better in ten sessions, look better in twenty sessions, and have a completely new body in thirty sessions.",
            "Physical fitness is the first requisite of happiness.",
            "The mind, when housed within a healthful body, possesses a glorious sense of power."
        ];

        // Pilih kutipan secara acak
        $randomQuote = $quotes[array_rand($quotes)];

        // Waktu saat ini
        $currentDate = Carbon::now()->translatedFormat('l, d F Y');

        // Ambil tanggal hari ini
        $today = Carbon::now()->toDateString();

        // Ambil data booking berdasarkan user_id dan tanggal saat ini di lessonSchedule
        $myBookings = Booking::where("user_id", $user->id)
        ->whereHas('lessonSchedule', function ($query) use ($today) {
            $query->whereDate('date', $today); // Filter berdasarkan tanggal di lessonSchedule
        })
        ->with([
            "lessonSchedule.lesson",
            "lessonSchedule.lessonType",
            "lessonSchedule.user",
            "lessonSchedule.timeSlot",
            "lessonSchedule.room",
            "user.profile"
        ]) // Eager load relasi yang diperlukan
        ->get();

        return view("home.homes.index", [
            "title_page" => "Pilates | Home",
            "user" => $user,
            "randomQuote" => $randomQuote, // Kirimkan quote ke view
            "currentDate" => $currentDate,
            "myBookings" => $myBookings
        ]);
    }
}
