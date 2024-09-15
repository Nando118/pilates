<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\CreateUserRequest;
use App\Http\Requests\Dashboard\Users\UpdateUserRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
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
        $title = "Delete User!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.users.index", [
            "title_page" => "Pilates | Users"
        ]);
    }

    public function getData()
    {
        $users = User::with("profile")->whereDoesntHave("roles", function ($query) {
            $query->where("name", "admin");
        })->get();

        return DataTables::of($users)
//            ->addColumn("branch", function ($user) {
//                return ucfirst($user->profile->branch) ?? "N/A";
//            })
            ->addColumn("gender", function ($user) {
                return ucfirst($user->profile->gender) ?? "N/A";
            })
            ->addColumn("role", function ($user) {
                return ucfirst($user->roles->pluck("name")->first() ?? "N/A");
            })
            ->addColumn("action", function ($user) {
                $btn = '<div class="btn-group mr-1">';
                $btn .= '<a href="'. route("users.edit", ["user" => $user->id]) .'" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
                $btn .= '<a href="' . route("users.view", ["user" => $user->id]) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-fw fa-eye"></i></a> ';
                $btn .= '<a href="'. route("users.delete", ["user" => $user->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></button> ';
                $btn .= '</div>';
                return $btn;
            })
            ->make(true);
    }

    public function create()
    {
        $action = route("users.store");

        return view("dashboard.users.form.form", [
            "title_page" => "Pilates | Add New User",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
                "registration_type" => "form",
                "email_verified_at" => Carbon::now()
            ]);

            $userId = $user->id;

            // Menangani upload gambar
            if ($request->hasFile("profile_picture")) {
                $imageName = uniqid() . "." . $request->profile_picture->extension();
                // Simpan gambar di storage (folder storage/app/public/images/profile)
                $path = $request->file("profile_picture")->storeAs("images/profile", $imageName, "public");

                // Jika ingin menyimpan path untuk disimpan ke database
                $imageName = $path; // Simpan path yang bisa diakses via 'storage/images/profile/uniqueimagename.extension'
            } else {
                $imageName = null; // Atau set ke default image
            }

            UserProfile::create([
                "user_id" => $userId,
//                "branch" => $validated["branch"],
                "username" => $validated["username"],
                "gender" => $validated["gender"],
                "phone" => $validated["phone"],
                "address" => $validated["address"],
                "profile_picture" => $imageName
            ]);

            $user->roles()->attach($validated["role"]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new user.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error adding user in UserController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new user, please try again.");
            return redirect()->back();
        }
    }

    public function view(User $user)
    {
        // Eager load relasi profile dan roles, gunakan instance $user secara langsung
        $userData = $user->load(["profile", "roles"]);

        // Ambil role pertama (jika user memiliki lebih dari satu role, sesuaikan logikanya)
        $roleName = ucfirst($userData->roles->pluck("name")->first());

        // Kirim data ke view menggunakan compact
        return view("dashboard.users.profile.profile", compact("userData", "roleName"))
        ->with("title_page", "Pilates | User Profile");
    }

    public function edit(User $user)
    {
        // Eager load relasi profile dan roles
        $user->load(["profile", "roles"]);

        // Ambil role pertama dari roles yang dimiliki user (jika ada)
        $roleId = optional($user->roles->first())->id;

        // Gunakan compact untuk merangkum variabel ke view
        $action = route("users.update", $user->id);

        return view("dashboard.users.form.form", compact("user", "action", "roleId"))
        ->with([
            "title_page" => "Pilates | Update User Profile",
            "method" => "POST"
        ]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Update data di tabel 'users'
            $user = User::findOrFail($user->id);
            $user->name = $validated["name"];
            $user->save();

            // Update data di tabel 'profiles'
            $profile = UserProfile::where("user_id", $user->id)->firstOrFail();

            // Menangani upload gambar
            if ($request->hasFile("profile_picture")) {
                // Hapus gambar lama jika ada
                if ($profile->profile_picture) {
                    // Menghapus file lama dari storage
                    Storage::disk("public")->delete($profile->profile_picture);
                }

                // Menyimpan gambar baru di storage
                $imageName = uniqid() . "." . $request->profile_picture->extension();

                // Simpan gambar di folder 'images/profile' di storage
                $path = $request->file("profile_picture")->storeAs("images/profile", $imageName, "public");

                // Simpan path gambar baru di database
                $profile->profile_picture = $path; // Simpan path seperti 'images/profile/imagename.extension'
            }

//            $profile->branch = $validated["branch"];
            $profile->gender = $validated["gender"];
            $profile->phone = $validated["phone"];
            $profile->address = $validated["address"];
            $profile->save();

            // Update data di tabel 'user_roles'
            UserRole::where("user_id", $user->id)->update([
                "role_id" => $validated["role"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully updated user data.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error updating user in UserController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating user data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($user->id);

            $user->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted user data.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error deleted user in UserController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted user data, please try again.");
            return redirect()->back();
        }
    }
}
