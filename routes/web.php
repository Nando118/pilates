<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\Booking\BookingController;
use App\Http\Controllers\Dashboard\Home\HomeController;
use App\Http\Controllers\Dashboard\Lesson\LessonController;
use App\Http\Controllers\Dashboard\LessonSchedule\LessonScheduleController;
use App\Http\Controllers\Dashboard\Room\RoomController;
use App\Http\Controllers\Dashboard\TimeSlot\TimeSlotController;
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


    // CLIENT & COACH PAGES
    Route::get("/home", [Controller::class, "home"])->name("home");


    // ADMIN PAGES
    // START HOME ROUTE - ADMIN PAGE
    Route::get("/dashboard", [HomeController::class, "index"])->name("dashboard");
    // END HOME ROUTE - ADMIN PAGE

    // START USERS ROUTE - ADMIN PAGE
    Route::get("/dashboard/users", [UserController::class, "index"])->name("users.index");
    Route::get("/dashboard/users/data", [UserController::class, "getData"])->name("users.data");
    Route::get("/dashboard/users/add-new-user", [UserController::class, "create"])->name("users.create");
    Route::post("/dashboard/users/add-new-user/store", [UserController::class, "store"])->name("users.store");
    Route::get("/dashboard/users/profile/{user}", [UserController::class, "view"])->name("users.view");
    Route::get("/dashboard/users/profile/edit/{user}", [UserController::class, "edit"])->name("users.edit");
    Route::put("/dashboard/users/profile/edit/{user}/update", [UserController::class, "update"])->name("users.update");
    Route::delete("/dashboard/users/profile/{user}/delete", [UserController::class, "destroy"])->name("users.delete");
    // END USERS ROUTE - ADMIN PAGE

    // START TIME SLOTS ROUTE - ADMIN PAGE
    Route::get("/dashboard/time-slots", [TimeSlotController::class, "index"])->name("time-slots.index");
    Route::get("/dashboard/time-slots/data", [TimeSlotController::class, "getData"])->name("time-slots.data");
    Route::get("/dashboard/time-slots/add-new-time-slot", [TimeSlotController::class, "create"])->name("time-slots.create");
    Route::post("/dashboard/time-slots/add-new-time-slot/store", [TimeSlotController::class, "store"])->name("time-slots.store");
    Route::get("/dashboard/time-slots/edit/{timeSlot}", [TimeSlotController::class, "edit"])->name("time-slots.edit");
    Route::put("/dashboard/time-slots/edit/{timeSlot}/update", [TimeSlotController::class, "update"])->name("time-slots.update");
    Route::delete("/dashboard/time-slots/{timeSlot}/delete", [TimeSlotController::class, "destroy"])->name("time-slots.delete");
    // END TIME SLOTS ROUTE - ADMIN PAGE

    // START ROOMS ROUTE - ADMIN PAGE
    Route::get("/dashboard/rooms", [RoomController::class, "index"])->name("rooms.index");
    Route::get("/dashboard/rooms/data", [RoomController::class, "getData"])->name("rooms.data");
    Route::get("/dashboard/rooms/create", [RoomController::class, "create"])->name("rooms.create");
    Route::post("/dashboard/rooms/create/store", [RoomController::class, "store"])->name("rooms.store");
    Route::get("/dashboard/rooms/edit/{room}", [RoomController::class, "edit"])->name("rooms.edit");
    Route::put("/dashboard/rooms/edit/{room}/update", [RoomController::class, "update"])->name("rooms.update");
    Route::delete("/dashboard/rooms/{room}/delete", [RoomController::class, "destroy"])->name("rooms.delete");
    // END ROOMS ROUTE - ADMIN PAGE

    // START LESSONS ROUTE - ADMIN PAGE
    Route::get("/dashboard/lessons", [LessonController::class, "index"])->name("lessons.index");
    Route::get("/dashboard/lessons/data", [LessonController::class, "getData"])->name("lessons.data");
    Route::get("/dashboard/lessons/add-new-lesson", [LessonController::class, "create"])->name("lessons.create");
    Route::post("/dashboard/lessons/add-new-lesson/store", [LessonController::class, "store"])->name("lessons.store");
    Route::get("/dashboard/lessons/edit/{lesson}", [LessonController::class, "edit"])->name("lessons.edit");
    Route::put("/dashboard/lessons/edit/{lesson}/update", [LessonController::class, "update"])->name("lessons.update");
    Route::delete("/dashboard/lessons/{lesson}/delete", [LessonController::class, "destroy"])->name("lessons.delete");
    // END LESSONS ROUTE - ADMIN PAGE

    // START LESSON SCHEDULES ROUTE - ADMIN PAGE
    Route::get("/dashboard/lesson-schedules", [LessonScheduleController::class, "index"])->name("lesson-schedules.index");
    Route::get("/dashboard/lesson-schedules/data", [LessonScheduleController::class, "getLessonSchedulesData"])->name("lesson-schedules.data");
    // END LESSON SCHEDULES ROUTE - ADMIN PAGE

    // START BOOKINGS SCHEDULES ROUTE - ADMIN PAGE
    Route::get("/dashboard/bookings", [BookingController::class, "index"])->name("bookings.index");
    // END BOOKINGS SCHEDULES ROUTE - ADMIN PAGE
});
