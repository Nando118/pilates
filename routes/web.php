<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\HomeDashboardController;
use App\Http\Controllers\Dashboard\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get("/", [\App\Http\Controllers\Auth\AuthController::class, "login"])->name("login");
    Route::get("/login", [\App\Http\Controllers\Auth\AuthController::class, "login"])->name("login");
    Route::post("/login/submit", [\App\Http\Controllers\Auth\AuthController::class, "loginPost"])->name("login.post");

    Route::get("/register/form", [\App\Http\Controllers\Auth\AuthController::class, "register"])->name("register");
    Route::post("/register/form/submit", [\App\Http\Controllers\Auth\AuthController::class, "registerPost"])->name("register.submit");
    Route::get("/register/complete-registration", [\App\Http\Controllers\Auth\AuthController::class, "completeRegistration"])->name("complete-registration");
    Route::post("/register/complete-registration/submit", [\App\Http\Controllers\Auth\AuthController::class, "completeRegistrationPost"])->name("complete-registration.submit");

    Route::get("/auth/{provider}", [\App\Http\Controllers\Auth\AuthController::class, "redirectToProvider"])->name("redirectToProvider");
    Route::get("/auth/{provider}/callback", [\App\Http\Controllers\Auth\AuthController::class, "handleProvideCallback"])->name("handleProvideCallback");

    Route::get("/forgot-password", [\App\Http\Controllers\Auth\AuthController::class, "forgotPassword"])->name("password.request");
    Route::post("/forgot-password/send-link", [\App\Http\Controllers\Auth\AuthController::class, "forgotPasswordEmail"])->name("password.email");
    Route::get("/reset-password/{token}", [\App\Http\Controllers\Auth\AuthController::class, "resetPassword"])->name("password.reset");
    Route::post("/reset-password/submit", [\App\Http\Controllers\Auth\AuthController::class, "resetPasswordUpdate"])->name("password.update");
});

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [\App\Http\Controllers\Auth\AuthController::class, "emailNotice"])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\AuthController::class, "emailVerify"])->middleware('signed', 'throttle:6,1')->name('verification.verify');
    Route::post('/email/resend', [\App\Http\Controllers\Auth\AuthController::class, "emailResend"])->middleware('throttle:6,1')->name('verification.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/logout', function () {
        abort(405); // Atau tanggapan lain seperti abort(403) jika Anda ingin menampilkan Forbidden
    });
    Route::post("/logout", [\App\Http\Controllers\Auth\AuthController::class, "logout"])->name("logout");

    Route::get("/home", [Controller::class, "home"])->name("home");

    Route::get("/dashboard", [HomeDashboardController::class, "index"])->name("dashboard");

    Route::get("/dashboard/users", [UserController::class, "index"])->name("users.index");
    Route::get("/dashboard/users/data", [UserController::class, "getUsersData"])->name("users.data");
    Route::get("/dashboard/users/add-new-user", [UserController::class, "create"])->name("users.create");
    Route::post("/dashboard/users/add-new-user/store", [UserController::class, "store"])->name("users.store");
    Route::get("/dashboard/users/profile/{id}", [UserController::class, "view"])->name("users.view");
});
