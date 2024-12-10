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
use App\Http\Controllers\Home\Home\HomeUserController;
use App\Http\Controllers\Home\LessonSchedule\UserLessonScheduleController;
use App\Http\Controllers\Home\MyLesson\MyLessonController;
use App\Http\Controllers\Home\MySchedule\MyScheduleController;
use App\Http\Controllers\Home\Profile\ProfileController;
use App\Http\Controllers\Dashboard\CoachCertification\CoachCertificationController;
use App\Http\Controllers\Dashboard\CreditTransaction\CreditTransactionController;
use App\Http\Controllers\Dashboard\Report\ReportController;
use App\Http\Controllers\Dashboard\UserCredit\UserCreditController;
use App\Http\Controllers\Home\Coach\CoachController;
use App\Http\Controllers\Home\MyTransaction\MyTransactionController;
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
    Route::get("/", function () {
        return redirect()->route("login");
    });
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

Route::middleware(['auth', 'verified', 'onlyCoachClient'])->group(function () {
    // CLIENT & COACH PAGES
    // START HOME ROUTE - CLIENT & COACH PAGES
    Route::get("/home/upcoming-lessons", [HomeUserController::class, "upcomingLessons"])->name("home");
    Route::get("/home/past-lessons", [HomeUserController::class, "pastLessons"])->name("pastLessons");
    // END HOME ROUTE - CLIENT & COACH PAGES

    // START LESSON SCHEDULES ROUTE - CLIENT & COACH PAGES
    Route::get("/home/lesson-schedules", [UserLessonScheduleController::class, "index"])->name("user-lesson-schedules.index");
    Route::get("/home/lesson-schedules/bookings/{bookings}/create", [UserLessonScheduleController::class, "create"])->name("user-lesson-schedules.create");
    Route::post("/home/lesson-schedules/bookings/{bookings}/create/store", [UserLessonScheduleController::class, "store"])->name("user-lesson-schedules.store");
    // END LESSON SCHEDULES ROUTE - CLIENT & COACH PAGES

    // START MY LESSONS ROUTE - CLIENT & COACH PAGES
    Route::get("/home/my-lesson-schedules", [MyLessonController::class, "index"])->name("my-lesson-schedules.index");
    // END MY LESSONS ROUTE - CLIENT & COACH PAGES

    // START PROFILE ROUTE - CLIENT & COACH PAGES
    Route::get("/home/my-profile", [ProfileController::class, "index"])->name("my-profile.index");
    Route::get("/home/my-profile/edit", [ProfileController::class, "edit"])->name("my-profile.edit");
    Route::put("/home/my-profile/edit/update", [ProfileController::class, "update"])->name("my-profile.update");
    // END PROFILE ROUTE - CLIENT & COACH PAGES

    // START MY SCHEDULES ROUTE - CLIENT & COACH PAGES
    Route::get("/home/my-schedules", [MyScheduleController::class, "index"])->name("my-schedules.index");
    Route::get("/home/my-schedules/{lessonSchedule}/participants", [MyScheduleController::class, "view"])->name("my-schedules.view");
    // END MY SCHEDULES ROUTE - CLIENT & COACH PAGES

    // START MY TRANSACTIONS ROUTE - CLIENT PAGES
    Route::get("/home/my-transactions", [MyTransactionController::class, "index"])->name("my-transactions.index");
    // END MY TRANSACTIONS ROUTE - CLIENT PAGES

    // START COACHES ROUTE - CLIENT & COACH PAGES
    Route::get("/home/coaches", [CoachController::class, "index"])->name("coaches.index");
    // END COACHES ROUTE - CLIENT & COACH PAGES
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/logout', function () {
        return redirect()->back(); // Atau tanggapan lain seperti abort(403) jika Anda ingin menampilkan Forbidden
    });
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
});

Route::middleware(['auth', 'verified', 'onlyAdmin'])->group(function () {
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
    Route::get("/dashboard/users/profile/{user}/data-bookings", [UserController::class, "getDataBookings"])->name("users.view.data-bookings");
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
    Route::get("/dashboard/lesson-schedules/getAvailableTimeSlots", [LessonScheduleController::class, "getAvailableTimeSlots"])->name("lesson-schedules.getAvailableTimeSlots");
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

    // START COACH CERTIFICATION ROUTE - ADMIN PAGE
    Route::get("/dashboard/coach-certifications", [CoachCertificationController::class, "index"])->name("coach-certifications.index");
    Route::get("/dashboard/coach-certifications/data", [CoachCertificationController::class, "getData"])->name("coach-certifications.data");
    Route::get("/dashboard/coach-certifications/add-coach-certifications", [CoachCertificationController::class, "create"])->name("coach-certifications.create");
    Route::post("/dashboard/coach-certifications/add-coach-certifications/store", [CoachCertificationController::class, "store"])->name("coach-certifications.store");
    Route::get("/dashboard/coach-certifications/edit/{coachCertification}", [CoachCertificationController::class, "edit"])->name("coach-certifications.edit");
    Route::put("/dashboard/coach-certifications/edit/{coachCertification}/update", [CoachCertificationController::class, "update"])->name("coach-certifications.update");
    Route::delete("/dashboard/coach-certifications/{coachCertification}/delete", [CoachCertificationController::class, "destroy"])->name("coach-certifications.delete");
    // END COACH CERTIFICATION ROUTE - ADMIN PAGE

    // START USER CREDITS ROUTE - SUPER ADMIN PAGE
    Route::get("/dashboard/user-credits", [UserCreditController::class, "index"])->name("user-credits.index");
    Route::get("/dashboard/user-credits/data", [UserCreditController::class, "getData"])->name("user-credits.data");
    Route::get("/dashboard/user-credits/edit/{user}", [UserCreditController::class, "edit"])->name("user-credits.edit");
    Route::put("/dashboard/user-credits/edit/{user}/update", [UserCreditController::class, "update"])->name("user-credits.update");
    // END USER CREDITS ROUTE - SUPER ADMIN PAGE

    // START USER CREDITS HISTORY ROUTE - SUPER ADMIN PAGE
    Route::get("/dashboard/credit-transactions", [CreditTransactionController::class, "index"])->name("credit-transactions.index");
    Route::get("/dashboard/credit-transactions/data", [CreditTransactionController::class, "getData"])->name("credit-transactions.data");
    // END USER CREDITS HISTORY ROUTE - SUPER ADMIN PAGE

    // START REPORTS ROUTE - SUPER ADMIN PAGE
    Route::get('/dashboard/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/dashboard/reports/generate', [ReportController::class, 'generateReport'])->name('reports.generate');
    Route::get('/dashboard/reports/export-weekly', [ReportController::class, 'exportWeeklyReport'])->name('reports.export.weekly');
    Route::get('/dashboard/reports/export-monthly', [ReportController::class, 'exportMonthlyReport'])->name('reports.export.monthly');   
    // END REPORTS ROUTE - SUPER ADMIN PAGE
});
