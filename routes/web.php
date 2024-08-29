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

    Route::get("/register", [\App\Http\Controllers\Auth\AuthController::class, "register"])->name("register");
    Route::post("/register/submit", [\App\Http\Controllers\Auth\AuthController::class, "registerPost"])->name("register.submit");

    Route::get("/auth/redirect", [\App\Http\Controllers\Auth\AuthController::class, "googleOAuthRedirect"])->name("googleOAuthRedirect");
    Route::get("/auth/callback", [\App\Http\Controllers\Auth\AuthController::class, "googleOAuthCallback"])->name("googleOAuthCallback");
});

Route::middleware(['auth'])->group(function () {
    Route::get("/user", [Controller::class, "user"])->name("home");
    Route::get("/dashboard", [Controller::class, "dashboard"])->name("dashboard");
});
