<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies('access-dashboard')) {
                abort(403); // Akses ditolak
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view("dashboard.home.home", [
            "title_page" => "Pilates | Dashboard",
            "user" => Auth::user()
        ]);
    }
}
