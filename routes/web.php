<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\Booking\BookingController;
use App\Http\Controllers\Dashboard\Home\HomeController;
use App\Http\Controllers\Dashboard\Lesson\LessonController;
use App\Http\Controllers\Dashboard\Room\RoomController;
use App\Http\Controllers\Dashboard\TimeSlot\TimeSlotController;
use App\Http\Controllers\Dashboard\User\UserController;
use App\Http\Controllers\Dashboard\LessonType\LessonTypeController;
use App\Http\Controllers\Dashboard\LessonSchedule\LessonScheduleController;
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
    // START LOGIN ROUTE - GUEST PAGE
    Route::get("/", [AuthController::class, "login"])->name("login");
    Route::get("/login", [AuthController::class, "login"])->name("login");
    Route::post("/login/submit", [AuthController::class, "loginPost"])->name("login.post");
    // END LOGIN ROUTE - GUEST PAGE

    // START REGISTER ROUTE - GUEST PAGE
    Route::get("/register/form", [AuthController::class, "register"])->name("register");
    Route::post("/register/form/submit", [AuthController::class, "registerPost"])->name("register.submit");
    Route::get("/register/complete-registration", [AuthController::class, "completeRegistration"])->name("complete-registration");
    Route::post("/register/complete-registration/submit", [AuthController::class, "completeRegistrationPost"])->name("complete-registration.submit");
    // END REGISTER ROUTE - GUEST PAGE

    // START REDIRECT PROVIDER ROUTE - GUEST PAGE
    Route::get("/auth/{provider}", [AuthController::class, "redirectToProvider"])->name("redirectToProvider");
    Route::get("/auth/{provider}/callback", [AuthController::class, "handleProvideCallback"])->name("handleProvideCallback");
    // END REDIRECT PROVIDER ROUTE - GUEST PAGE

    // START FORGOT PASSWORD ROUTE - GUEST PAGE
    Route::get("/forgot-password", [AuthController::class, "forgotPassword"])->name("password.request");
    Route::post("/forgot-password/send-link", [AuthController::class, "forgotPasswordEmail"])->name("password.email");
    Route::get("/reset-password/{token}", [AuthController::class, "resetPassword"])->name("password.reset");
    Route::post("/reset-password/submit", [AuthController::class, "resetPasswordUpdate"])->name("password.update");
    // END FORGOT PASSWORD ROUTE - GUEST PAGE
});

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [AuthController::class, "emailNotice"])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, "emailVerify"])->middleware('signed', 'throttle:6,1')->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, "emailResend"])->middleware('throttle:6,1')->name('verification.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // CLIENT & COACH PAGES
    Route::get("/home", [Controller::class, "home"])->name("home");
});

Route::middleware(['auth', 'verified', 'onlyAdmin'])->group(function () {
    Route::get('/logout', function () {
        return redirect()->back(); // Atau tanggapan lain seperti abort(403) jika Anda ingin menampilkan Forbidden
    });
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");

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

    // START LESSON TYPES ROUTE - ADMIN PAGE
    Route::get("/dashboard/lesson-types", [LessonTypeController::class, "index"])->name("lesson-types.index");
    Route::get("/dashboard/lesson-types/data", [LessonTypeController::class, "getData"])->name("lesson-types.data");
    Route::get("/dashboard/lesson-types/add-new-lesson-type", [LessonTypeController::class, "create"])->name("lesson-types.create");
    Route::post("/dashboard/lesson-types/add-new-lesson-type/store", [LessonTypeController::class, "store"])->name("lesson-types.store");
    Route::get("/dashboard/lesson-types/edit/{lessonType}", [LessonTypeController::class, "edit"])->name("lesson-types.edit");
    Route::put("/dashboard/lesson-types/edit/{lessonType}/update", [LessonTypeController::class, "update"])->name("lesson-types.update");
    Route::delete("/dashboard/lesson-types/{lessonType}/delete", [LessonTypeController::class, "destroy"])->name("lesson-types.delete");
    // END LESSON TYPES ROUTE - ADMIN PAGE

    // START ROOMS ROUTE - ADMIN PAGE
    Route::get("/dashboard/rooms", [RoomController::class, "index"])->name("rooms.index");
    Route::get("/dashboard/rooms/data", [RoomController::class, "getData"])->name("rooms.data");
    Route::get("/dashboard/rooms/create", [RoomController::class, "create"])->name("rooms.create");
    Route::post("/dashboard/rooms/create/store", [RoomController::class, "store"])->name("rooms.store");
    Route::get("/dashboard/rooms/edit/{room}", [RoomController::class, "edit"])->name("rooms.edit");
    Route::put("/dashboard/rooms/edit/{room}/update", [RoomController::class, "update"])->name("rooms.update");
    Route::delete("/dashboard/rooms/{room}/delete", [RoomController::class, "destroy"])->name("rooms.delete");
    // END ROOMS ROUTE - ADMIN PAGE

    // START LESSON ROUTE - ADMIN PAGE
    Route::get("/dashboard/lessons", [LessonController::class, "index"])->name("lessons.index");
    Route::get("/dashboard/lessons/data", [LessonController::class, "getData"])->name("lessons.data");
    Route::get("/dashboard/lessons/create", [LessonController::class, "create"])->name("lessons.create");
    Route::post("/dashboard/lessons/create/store", [LessonController::class, "store"])->name("lessons.store");
    Route::get("/dashboard/lessons/edit/{lesson}", [LessonController::class, "edit"])->name("lessons.edit");
    Route::put("/dashboard/lessons/edit/{lesson}/update", [LessonController::class, "update"])->name("lessons.update");
    Route::delete("/dashboard/lessons/{lesson}/delete", [LessonController::class, "destroy"])->name("lessons.delete");
    // END LESSON ROUTE - ADMIN PAGE

    // START LESSON SCHEDULES ROUTE - ADMIN PAGE
    Route::get("/dashboard/lesson-schedules", [LessonScheduleController::class, "index"])->name("lesson-schedules.index");
    Route::get("/dashboard/lesson-schedules/data", [LessonScheduleController::class, "getData"])->name("lesson-schedules.data");
    Route::get("/dashboard/lesson-schedules/create", [LessonScheduleController::class, "create"])->name("lesson-schedules.create");
    Route::post("/dashboard/lesson-schedules/create/store", [LessonScheduleController::class, "store"])->name("lesson-schedules.store");
    Route::get("/dashboard/lesson-schedules/edit/{lessonSchedule}", [LessonScheduleController::class, "edit"])->name("lesson-schedules.edit");
    Route::put("/dashboard/lesson-schedules/edit/{lessonSchedule}/update", [LessonScheduleController::class, "update"])->name("lesson-schedules.update");
    Route::delete("/dashboard/lesson-schedules/{lessonSchedule}/delete", [LessonScheduleController::class, "destroy"])->name("lesson-schedules.delete");
    // END LESSON SCHEDULES ROUTE - ADMIN PAGE

    // START BOOKINGS ROUTE - ADMIN PAGE
    Route::get("/dashboard/bookings", [BookingController::class, "index"])->name("bookings.index");
    Route::get("/dashboard/bookings/data", [BookingController::class, "getData"])->name("bookings.data");
    Route::get("/dashboard/bookings/{bookings}/create", [BookingController::class, "create"])->name("bookings.create");
    Route::post("/dashboard/bookings/{bookings}/create/store", [BookingController::class, "store"])->name("bookings.store");
    Route::delete("/dashboard/bookings/{bookings}/delete", [BookingController::class, "destroy"])->name("bookings.delete");
    // END BOOKINGS ROUTE - ADMIN PAGE
});
