<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterWithNormalFormRequest;
use App\Http\Requests\RegisterWithProviderRequest;
use App\Http\Requests\UserEmailCheckRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Models\Role;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login.form", [
            "title_page" => "Pilates | Sign In"
        ]);
    }

    public function loginPost(LoginFormRequest $request)
    {
        $validate = $request->validated();

        if ($validate) {

            $credentials = $request->only(["email", "password"]);
            $remember = $request->has("remember");

            $userCheck = User::query()->where("email", "=", $request["email"])->first();

            if ($userCheck) {
                if ($userCheck->registration_type === "social") {
                    return redirect()->back();
                }

                if (Auth::attempt($credentials, $remember)) {
                    return redirect()->intended(route("home"));
                } else {
                    return redirect()->back();
                }
            }else{
                return redirect()->back();
            }
        } else {
            return redirect()->back();
        }
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

                event(new Registered($user));

                Auth::login($user);

                DB::commit();

                return redirect()->route("verification.notice");
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

    public function emailNotice(Request $request){
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route("home") : view('auth.email-verify.form', [
                "title_page" => "Pilates | Sign Up"
            ]);
    }

    public function emailVerify(EmailVerificationRequest $request){
        $request->fulfill();
        return redirect()->route("home");
    }

    public function emailResend(Request $request){
        $request->user()->sendEmailVerificationNotification();
        return back();
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

                if ($user->registration_type === "form") {
                    return  redirect()->route("login");
                }

                $userProfile = UserProfile::query()->where("user_id", "=", $user->id)->first();

                if (empty($userProfile->branch) || empty($userProfile->username) || empty($userProfile->phone) || empty($userProfile->address)) {
                    session(["PROVIDER_ID" => $providerData->getId()]);

                    return redirect()->route("complete-registration");
                }

                Auth::login($user);

                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

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

        return view("auth.complete-registration.form", [
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

                event(new Registered($user));

                Auth::login($user);

                DB::commit();

                session()->forget('PROVIDER_ID');

                return redirect()->route("verification.notice");
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

    // Forgot Password
    public function forgotPassword() {
        return view("auth.forgot-password.form", [
            "title_page" => "Pilates | Forgot Password"
        ]);
    }

    public function forgotPasswordEmail(UserEmailCheckRequest $request) {
        try {
            $request->validated();

            $validated = User::query()->where("email", "=", $request['email'])->first();

            if ($validated) {
                if ($validated->registration_type === "social") {
                    return redirect()->route("login");
                }

                $status = Password::sendResetLink($request->only("email"));
                return $status === Password::RESET_LINK_SENT ? back() : back();
            } else {
                return redirect()->route("login");
            }
        }catch (\Exception $e){
            Log::error($e);
            return redirect()->back();
        }
    }

    public function resetPassword(string $token) {
        if ($token) {
            return view("auth.forgot-password.reset-password.form", [
                "title_page" => "Support Ticket System | Reset Password",
                "token" => $token,
                "email" => \request("email")
            ]);
        } else {
            return redirect()->route("home");
        }
    }

    public function resetPasswordUpdate(UserPasswordRequest $request) {
        $validated = $request->validated();

        if ($validated) {
            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                }
            );

            return $status === Password::PASSWORD_RESET ? redirect()->route('login') : back();
        } else {
            return redirect()->route("password.request");
        }
    }
}
