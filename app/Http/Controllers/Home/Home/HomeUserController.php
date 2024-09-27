<?php

namespace App\Http\Controllers\Home\Home;

use App\Http\Controllers\Controller;
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

        return view("home.homes.index", [
            "title_page" => "Pilates | Home",
            "user" => $user,
            "randomQuote" => $randomQuote, // Kirimkan quote ke view
            "currentDate" => $currentDate
        ]);
    }
}
