<?php

namespace App\Http\Controllers\Home\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
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
        $user = User::findOrFail(Auth::id());

        // Eager load relasi profile dan roles, gunakan instance $user secara langsung
        $userData = $user->load(["profile", "roles"]);

        // Ambil role pertama (jika user memiliki lebih dari satu role, sesuaikan logikanya)
        $roleName = ucfirst($userData->roles->pluck("name")->first());

        return view("home.profiles.index", [
            "title_page" => "Pilates | Home",
            "userData" => $userData,
            "roleName" => $roleName
        ]);
    }
}
