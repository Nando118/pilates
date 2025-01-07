<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RedirectByRoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPassword\CheckEmailRequest;
use App\Http\Requests\Auth\ForgotPassword\PasswordResetRequest;
use App\Http\Requests\Auth\Login\LoginRequest;
use App\Http\Requests\Auth\Register\RegisterRequest;
use App\Http\Requests\Auth\Register\RegisterWithProviderRequest;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\RegisterWithNormalFormRequest;
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
            "title_page" => "Ohana Pilates | Sign In"
        ]);
    }

    public function loginPost(LoginRequest $request)
    {
        $validated = $request->validated();
        $credentials = [
            "email" => $validated["email"],
            "password" => $validated["password"]
        ];
        $remember = $request->has("remember");

        $userCheck = User::where("email", $credentials["email"])->first();

        // Jika user tidak ditemukan
        if (!$userCheck) {
            alert()->error("Oppss...", "Account not found or credentials are invalid.");
            return redirect()->back();
        }

        // Jika email terdaftar sebagai email sosmed
        if ($userCheck->registration_type === "social") {
            alert()->info("Hei", "This email is already registered. Try using another email or login with an existing account.");
            return redirect()->back();
        }

        // Autentikasi user
        if (Auth::attempt($credentials, $remember)) {
            return RedirectByRoleHelper::redirectBasedOnRole($request->user());
        }

        // Jika kredensial tidak cocok
        alert()->error("Oppss...", "Account not found or credentials are invalid.");
        return redirect()->back();
    }

  /*   public function register()
    {
        return view("auth.register.form", [
            "title_page" => "Ohana Pilates | Sign Up"
        ]);
    }

    public function registerPost(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
                "registration_type" => "form"
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

            $userProfile = UserProfile::create([
                "user_id" => $userId,                
                "gender" => $validated["gender"],
                "phone" => $validated["phone"],
                "address" => isset($validated["address"]) && !empty($validated["address"]) ? $validated["address"] : null,
                "profile_picture" => $imageName
            ]);

            $role = Role::where("name", "client")->first();

            // Menetapkan role ke pengguna
            if ($user && $role) {
                $user->roles()->attach($role->id);
            }

            event(new Registered($user));

            Auth::login($user);

            DB::commit();

            return redirect()->route("verification.notice");
        } catch (\Exception $e) {
            Log::error("Error adding new user in AuthController@registerPost: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->back();
        }
    }

    public function emailNotice(Request $request)
    {
        try {
            $user = $request->user();

            // Jika sudah terverifikasi, langsung redirect berdasarkan role
            if ($user->hasVerifiedEmail()) {
                return RedirectByRoleHelper::redirectBasedOnRole($user);
            }

            // Jika belum terverifikasi, tampilkan form verifikasi email
            return view("auth.email-verify.form", [
                "title_page" => "Ohana Pilates | Sign Up"
            ]);
        } catch (\Exception $e) {
            Log::error("Error adding new user in AuthController@emailNotice: " . $e->getMessage());
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->route("login");
        }
    }

    public function emailVerify(EmailVerificationRequest $request)
    {
        try {
            $request->fulfill();
            return RedirectByRoleHelper::redirectBasedOnRole($request->user());
        } catch (\Exception $e) {
            Log::error("Error adding new user in AuthController@emailVerify: " . $e->getMessage());
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->route("login");
        }
    }

    public function emailResend(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();

            // Menambahkan pesan notifikasi
            alert()->success("Success", "Verification email has been resent.");

            return back();
        } catch (\Exception $e) {
            Log::error("Error adding new user in AuthController@emailResend: " . $e->getMessage());
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->route("login");
        }
    }

    // Register or Login With Social Media Account
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProvideCallback($provider)
    {
        // Cek apakah ada kesalahan dalam permintaan (parameter 'code' hilang)
        if (!request()->has("code")) {
            alert()->info("Info", "Authentication was successfully canceled. Please login or try again.");
            return redirect()->route("login");
        }

        try {
            $providerData = Socialite::driver($provider)->user();
            $user = User::where("email", $providerData->getEmail())->first();

            if ($user) {
                // Cek jika email terdaftar via form
                if ($user->registration_type === "form") {
                    alert()->info("Hei", "This email is already registered. Try using another email or login with an existing account.");
                    return redirect()->route("login");
                }

                // Cek apakah profil user lengkap
                $userProfile = UserProfile::where("user_id", $user->id)->first();
                if ($this->isUserProfileIncomplete($userProfile)) {
                    session(["PROVIDER_ID" => $providerData->getId()]);
                    alert()->info("Hei", "Please complete this form first to complete the registration process.");
                    return redirect()->route("complete-registration");
                }

                Auth::login($user);

                // Jika email belum diverifikasi
                if (!$user->hasVerifiedEmail()) {
                    alert()->info("Hei", "Please verify your email first to complete the registration process.");
                    return redirect()->route('verification.notice');
                }

                return RedirectByRoleHelper::redirectBasedOnRole($user);
            }

            // Jika user tidak ditemukan, buat user baru
            DB::beginTransaction();

            $user = User::updateOrCreate(
                ["email" => $providerData->email],
                [
                    "name" => $providerData->name,
                    "registration_type" => "social"
                ]
            );

            $userProfile = UserProfile::updateOrCreate(
                ["user_id" => $user->id],
                [                    
                    "gender" => "other",
                    "phone" => "",
                    "address" => ""
                ]
            );

            // Ambil role client dan tetapkan
            $role = Role::where("name", "client")->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            SocialAccount::updateOrCreate(
                ["user_id" => $user->id],
                [
                    "provider" => $provider,
                    "provider_id" => $providerData->id,
                    "access_token" => $providerData->token
                ]
            );

            DB::commit();

            session(["PROVIDER_ID" => $providerData->getId()]);
            return redirect()->route("complete-registration");
        } catch (\Exception $e) {
            Log::error("Error adding new social user in AuthController@handleProvideCallback: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->route("login");
        }
    }

    public function completeRegistration()
    {
        // Jika session "PROVIDER_ID" tidak ada, redirect ke login
        if (!session()->has("PROVIDER_ID")) {
            alert()->error("Oppss...", "An error occurred. Please log in to complete your registration.");
            return redirect()->route("login");
        }

        // Tampilkan form complete registration jika session ada
        return view("auth.complete-registration.form", [
            "title_page" => "Ohana Pilates | Complete Registration"
        ]);
    }

    public function completeRegistrationPost(RegisterWithProviderRequest $request)
    {
        try {
            $validated = $request->validated();
            $providerId = session()->get("PROVIDER_ID");

            // Early return jika session PROVIDER_ID tidak ada
            if (!$providerId) {
                alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
                return redirect()->route("login");
            }

            DB::beginTransaction();

            // Mengambil semua data yang diperlukan sekaligus
            $socialAccount = SocialAccount::query()->where("provider_id", $providerId)->firstOrFail();
            $user = $socialAccount->user;
            $userProfile = $user->profile;

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

            // Update profile
            $userProfile->update([                
                "gender" => $validated["gender"],
                "phone" => $validated["phone"],
                "address" => isset($validated["address"]) && !empty($validated["address"]) ? $validated["address"] : null,
                "profile_picture" => $imageName
            ]);

            event(new Registered($user));
            Auth::login($user);

            DB::commit();

            session()->forget("PROVIDER_ID");

            return redirect()->route("verification.notice");
        } catch (\Exception $e) {
            Log::error("Error adding new social user in AuthController@completeRegistrationPost: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred during the registration process, please try again.");
            return redirect()->route("login");
        }
    }

    private function isUserProfileIncomplete($userProfile)
    {
        return empty($userProfile->gender) || empty($userProfile->phone);
    }
 */

    // Forgot Password
    public function forgotPassword()
    {
        return view("auth.forgot-password.form", [
            "title_page" => "Ohana Pilates | Forgot Password"
        ]);
    }

    public function forgotPasswordEmail(CheckEmailRequest $request)
    {
        try {
            // Validasi dan ambil hasil validasi
            $validated = $request->validated();

            // Cek apakah email ada dalam database
            $user = User::where("email", $validated["email"])->first();

            // Early return jika pengguna tidak ditemukan
            if (!$user) {
                alert()->error("Oppss...", "An error occurred while resetting the password or the credentials are invalid.");
                return redirect()->route("login");
            }

            // Cek jenis pendaftaran
            if ($user->registration_type === "social") {
                alert()->info("Hei", "This email is already registered. Try using another email or login with an existing account.");
                return redirect()->route("login");
            }

            // Kirim link reset password
            $status = Password::sendResetLink($request->only("email"));
            alert()->success("Success", "A password reset link has been sent to your email address.");

            return back();
        } catch (\Exception $e) {
            Log::error("Error resseting password user in AuthController@forgotPasswordEmail: " . $e->getMessage());
            alert()->error("Oppss...", "An error occurred while resetting the password.");
            return redirect()->back();
        }
    }

    public function resetPassword(string $token)
    {
        // Early return jika token tidak ada
        if (!$token) {
            alert()->error("Oppss...", "An error occurred while resetting the password.");
            return redirect()->route("password.request");
        }

        // Kembalikan view dengan data yang diperlukan
        return view("auth.forgot-password.reset-password.form", [
            "title_page" => "Ohana Pilates | Reset Password",
            "token" => $token,
            "email" => request("email") // Mengambil email dari request
        ]);
    }

    public function resetPasswordUpdate(PasswordResetRequest $request)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction(); // Memulai transaksi

            // Lakukan reset password
            $status = Password::reset(
                $request->only("email", "password", "token"),
                function (User $user, string $password) {
                    $user->forceFill([
                        "password" => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                }
            );

            // Mengembalikan redirect berdasarkan status
            if ($status === Password::PASSWORD_RESET) {
                DB::commit(); // Commit jika berhasil
                alert()->success("Success", "Password has been reset successfully."); // Pesan sukses
                return redirect()->route("login");
            } else {
                DB::rollBack(); // Rollback jika gagal
                alert()->error("Oppss...", "Failed to reset password."); // Pesan kesalahan
                return back();
            }
        } catch (\Exception $e) {
            Log::error("Error resseting password user in AuthController@resetPasswordUpdate: " . $e->getMessage());
            DB::rollBack(); // Rollback jika terjadi kesalahan
            alert()->error("Oppss...", "An error occurred while resetting the password. Please try again.");
            return redirect()->route("password.request");
        }
    }

    public function logout(Request $request)
    {
        // Cek apakah ada autentikasi user yang sedang berlangsung
        if (!auth()->check()) {
            return redirect(route("login")); // Kembali ke login jika tidak ada user yang terautentikasi
        }

        // Logout user
        Auth::logout();

        // Hapus sesi dan regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembali ke halaman login dengan SweetAlert2
        alert()->success("Success", "You have been logged out successfully."); // Pesan sukses

        return redirect(route("login")); // Kembali ke halaman login
    }
}
