<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
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
        return view("dashboard.user.index", [
            "title_page" => "Pilates | User"
        ]);
    }

    public function getUsersData()
    {
        $users = User::query()->with("profile")->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        return DataTables::of($users)
            ->addColumn('branch', function ($user) {
                return ucfirst($user->profile->branch) ?? 'N/A';
            })
            ->addColumn('gender', function ($user) {
                return ucfirst($user->profile->gender) ?? 'N/A';
            })
            ->addColumn('action', function ($user) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. $user->id .'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("users.view", ["id" => $user->id]) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-fw fa-eye"></i></a> ';
                $btn .= '<button type="button" class="delete btn btn-danger btn-sm" title="Delete" data-url="#"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        return view("dashboard.user.form.form", [
            "title_page" => "Pilates | Add New User"
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($validated) {
                DB::beginTransaction();

                $user = User::query()->create([
                    "name" => $request['name'],
                    "email" => $request['email'],
                    "password" => Hash::make($request['password']),
                    "registration_type" => "form",
                    "email_verified_at" => Carbon::now()
                ]);

                $userId = $user->id;

                $userProfile = UserProfile::query()->create([
                    "user_id" => $userId,
                    "branch" => $request['branch'],
                    "username" => $request['username'],
                    "gender" => $request['gender'],
                    "phone" => $request['phone'],
                    "address" => $request['address'],
                    "profile_picture" => null
                ]);

                $user->roles()->attach($request['role']);

                DB::commit();

                alert()->success("Yeay!", "Successfully added new user.");
                return redirect()->route("users.index");
            } else {
                DB::rollBack();
                alert()->error("Oppss...", "An error occurred while adding a new user, please try again.");
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new user, please try again.");
            return redirect()->back();
        }
    }

    public function view($userId)
    {
        $userData = User::query()->with('profile')->where("id", "=", $userId)->firstOrFail();
        $roleNames = $userData->roles->pluck('name');
        
        foreach ($roleNames as $roleName) {
            $roleName;
        }

        return view("dashboard.user.profile.profile", [
            "title_page" => "Pilates | User Profile",
            "user_data" => $userData,
            "user_role" => ucfirst($roleName)
        ]);
    }
}
