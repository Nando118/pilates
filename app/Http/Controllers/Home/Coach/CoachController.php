<?php

namespace App\Http\Controllers\Home\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CoachController extends Controller
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
        $coaches = User::with(["profile", "coachCertifications"])
            ->whereHas("roles", function($query) {
                $query->where("name", "coach");
            })
            ->get();

        return view("home.coaches.index", [
            "title_page" => "Ohana Pilates | Coaches",
            "coaches" => $coaches
        ]);
    }
}
