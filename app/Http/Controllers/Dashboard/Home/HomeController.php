<?php

namespace App\Http\Controllers\Dashboard\Home;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
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
        // Menghitung jumlah pengguna dengan role coach
        $coachCount = Role::where('name', 'coach')
            ->withCount('users')
            ->first()
            ->users_count;

        // Menghitung jumlah pengguna dengan role client
        $clientCount = Role::where('name', 'client')
            ->withCount('users')
            ->first()
            ->users_count;        

        return view("dashboard.homes.index", [
            "title_page" => "Ohana Pilates | Dashboard",
            "user" => Auth::user(),
            "coachCount" => $coachCount,
            "clientCount" => $clientCount
        ]);
    }
}
