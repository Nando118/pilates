<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
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
    Route::get("/home", [Controller::class, "home"])->name("home");
    Route::get("/dashboard", [Controller::class, "dashboard"])->name("dashboard");
});
