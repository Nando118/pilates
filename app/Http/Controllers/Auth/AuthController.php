<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\RegisterWithNormalFormRequest;
use App\Http\Requests\RegisterWithProviderRequest;
use App\Models\Role;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

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
        return view("auth.register.form", [
            "title_page" => "Pilates | Sign Up"
        ]);
    }

    public function registerPost(RegisterWithNormalFormRequest $request)
    {
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
            } else {
                DB::rollBack();
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->back();
        }
    }

    // Register or Login With Social Media Account
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProvideCallback($provider)
    {
        try {
            $providerData = Socialite::driver($provider)->user();

            $user = User::query()->where("email", "=", $providerData->getEmail())->first();

            if ($user) {
                $userProfile = UserProfile::query()->where("user_id", "=", $user->id)->first();

                if (empty($userProfile->branch) || empty($userProfile->username) || empty($userProfile->phone) || empty($userProfile->address)) {
                    session(["PROVIDER_ID" => $providerData->getId()]);

                    return redirect()->route("complete-registration");
                }

                Auth::login($user);

                return redirect()->intended(route("home"));
            } else {
                DB::beginTransaction();

                $user = User::query()->updateOrCreate([
                    "email" => $providerData->email,
                ], [
                    "registration_type" => "social"
                ]);

                $userProfile = UserProfile::query()->updateOrCreate([
                    "user_id" => $user->id
                ], [
                    "branch" => "",
                    "name" => $providerData->name,
                    "username" => "",
                    "gender" => "other",
                    "phone" => "",
                    "address" => "",
                    "profile_picture" => null
                ]);

                $role = Role::where('name', 'client')->first();

                // Menetapkan role ke pengguna
                if ($user && $role) {
                    $user->roles()->attach($role->id);
                }

                $socialAccount = SocialAccount::query()->updateOrCreate([
                    "user_id" => $user->id
                ], [
                    "provider" => "Google",
                    "provider_id" => $providerData->id,
                    "access_token" => $providerData->token
                ]);

                DB::commit();

                session(["PROVIDER_ID" => $providerData->getId()]);

                return redirect()->route("complete-registration");
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->route("login");
        }
    }

    public function completeRegistration() {
        if (!session()->has("PROVIDER_ID")) {
            return redirect()->route("login");
        }

        return view("auth.complete-registration.register", [
            "title_page" => "Pilates | Complete Registration"
        ]);
    }

    public function completeRegistrationPost(RegisterWithProviderRequest $request) {
        try {
            $validated = $request->validated();

            if ($validated) {
                DB::beginTransaction();

                $providerId = session()->get("PROVIDER_ID");

                $socialAccount = SocialAccount::query()->where("provider_id", "=", $providerId)->first();
                $userProfile = UserProfile::query()->where("user_id", "=", $socialAccount->user_id)->first();
                $user = User::query()->where("id", "=", $socialAccount->user_id)->first();

                $dataProfile = [
                    "branch" => $validated['branch'],
                    "username" => $validated['username'],
                    "gender" => $validated['gender'],
                    "phone" => $validated['phone'],
                    "address" => $validated['address']
                ];

                $userProfile->update($dataProfile);

                DB::commit();

                session()->forget('PROVIDER_ID');

                Auth::login($user);

                return redirect()->intended(route("home"));
            } else {
                DB::rollBack();
                return redirect()->back();
            }
        }catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()->route("login");
        }
    }
}
