<?php

namespace App\Http\Controllers\Dashboard\UserCredit;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class UserCreditController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-dashboard") || Gate::denies("super-admin")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {        
        return view("dashboard.user-credits.index", [
            "title_page" => "Pilates | User Credits"
        ]);
    }

    public function getData()
    {
        $users = User::with("profile")->whereDoesntHave("roles", function ($query) {
            $query->whereIn("name", ["super_admin", "admin", "coach"]);
        })->get();

        return DataTables::of($users)
            ->addColumn("phone", function ($user) {
                return ucfirst($user->profile->phone) ?? "N/A";
            })                   
            ->addColumn("gender", function ($user) {
                return ucfirst($user->profile->gender) ?? "N/A";
            })                   
            ->addColumn("action", function ($user) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="#" class="btn btn-success btn-sm" title="Add Credits to User"><i class="fas fa-fw fa-coins"></i></a> ';                
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }
}
