<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterWithNormalFormRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login.login", [
            "title_page" => "Pilates | Sign In"
        ]);
    }

    public function register()
    {
        return view("auth.register.register", [
            "title_page" => "Pilates | Sign Up"
        ]);
    }

    public function registerWithNormalForm(RegisterWithNormalFormRequest $request) {
        try {
            $validated = $request->validated();

            if ($validated) {
                DB::beginTransaction();

                $user = User::query()->create([
                    "email" => $request['email'],
                    "password" => Hash::make($request['password']),
                    "registration_type" => "form"
                ]);

                $userId = $user->id;

                $userProfile = UserProfile::query()->create([
                    "user_id" => $userId,
                    "branch" => $request['branch'],
                    "name" => $request['name'],
                    "username" => $request['username'],
                    "gender" => $request['gender'],
                    "phone" => $request['phone'],
                    "address" => $request['address'],
                    "profile_picture" => null
                ]);

                $role = Role::where('name', 'client')->first();

                // Menetapkan role ke pengguna
                if ($user && $role) {
                    $user->roles()->attach($role->id);
                }

                DB::commit();

                return redirect()->route("login");
            }else{
                DB::rollBack();
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back();
        }
    }
}
