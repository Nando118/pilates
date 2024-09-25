<?php

namespace App\Http\Controllers\Home\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\MyProfiles\UserUpdateMyProfileRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function edit()
    {
        $user = User::findOrFail(Auth::id());

        // Eager load relasi profile dan roles, gunakan instance $user secara langsung
        $userData = $user->load(["profile", "roles"]);

        // Ambil role pertama dari roles yang dimiliki user (jika ada)
        $roleId = optional($userData->roles->first())->id;

        // Gunakan compact untuk merangkum variabel ke view
        $action = route("my-profile.update", $user->id);

        return view("home.profiles.form.form", compact("userData", "action", "roleId"))
        ->with([
            "title_page" => "Pilates | Update User Profile",
            "method" => "POST"
        ]);
    }

    public function update(UserUpdateMyProfileRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = User::findOrFail(Auth::id());

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
            
            $profile->gender = $validated["gender"];
            $profile->phone = $validated["phone"];
            $profile->address = $validated["address"];
            $profile->save();

            DB::commit();

            alert()->success("Yeay!", "Successfully updated profile.");
            return redirect()->route("my-profile.index");
        } catch (\Exception $e) {
            Log::error("Error updating profile in ProfileController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating profile, please try again.");
            return redirect()->back();
        }
    }
}
