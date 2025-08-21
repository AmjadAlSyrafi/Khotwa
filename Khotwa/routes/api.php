<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// Controllers for authentication and volunteer-related actions
use App\Http\Controllers\API\Auth\{
    RegisterController,
    LoginController,
    LogoutController,
    ForgotPasswordController,
    OtpController,
};
use App\Http\Controllers\API\Volunteer\ProfileController;

// General controllers
use App\Http\Controllers\{
    VolunteerController,
    ProjectController,
    EventController,
    EventRegistrationController,
    AttendanceController,
    EvaluationController,
    BadgeController,
    WarningController,
    EventFeedbackController,
    DonationController,
    ExpenseController,
    FinanceController,
    DocumentController,
    LeaderboardController
};

// Admin-specific controllers
use App\Http\Controllers\Admin\{
    VolunteerApplicationController,
    VolunteerAdminController,
};
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Public & Utility Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible and do not require authentication.
|
*/

//  Submit a new volunteer application
Route::post('/join-request', [VolunteerApplicationController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated User Info
|--------------------------------------------------------------------------
|
| A route to get information about the currently authenticated user.
|
*/

//  Get authenticated user data
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Includes routes for registration, login, logout, and password reset.
|
*/

Route::prefix('auth')->group(function () {
    // 🔹 Registration
    Route::post('/register', [RegisterController::class, 'register']);

    // 🔹 Login & Logout
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);

    // 🔹 OTP Verification
    Route::post('/verify-otp', [OtpController::class, 'verify']);

    // 🔹 Password Reset
    Route::post('/forget-password', [ForgotPasswordController::class, 'sendResetOtp']);
    Route::post('/confirm-reset-password', [ForgotPasswordController::class, 'reset']);

});

/*
|--------------------------------------------------------------------------
| Donatios Routes
|--------------------------------------------------------------------------
|
| These routes require the user to be authenticated and have the Donor.
|
*/
Route::middleware(['auth:sanctum'])->prefix('donatios')->group(function () {
    //Donations
    // Step 1: Initialize a donation record and get back a donation_id
    Route::post('/donate/init', [DonationController::class, 'init'])->name('donations.init');

    // Step 2: Confirm the donation status after payment processing
    Route::post('/donate/confirm', [DonationController::class, 'confirm'])->name('donations.confirm');

    // Authenticated User Endpoint
    Route::get('/my-donations', [DonationController::class, 'myDonations'])->name('donations.mine');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes require the user to be authenticated and have the "Admin" role.
|
*/

Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    // 🔹 User Management
    Route::apiResource('users', UserManagementController::class);

    // 🔹 Volunteer Join Requests
    Route::get('/join-requests', [VolunteerApplicationController::class, 'index']);
    Route::post('/applications/approve', [VolunteerApplicationController::class, 'approve']);

    // 🔹 Project Management
    Route::apiResource('/projects', ProjectController::class);

    // 🔹 Event Management
    Route::apiResource('/events', EventController::class);
    Route::get('/events/{eventId}/evaluations', [EvaluationController::class, 'indexForEvent']);

    // Badges
    Route::get('/badges/all', [BadgeController::class, 'allBadges']);
    Route::post('/badges/sync/{volunteerId}', [BadgeController::class, 'syncAllForVolunteer']);
    Route::get('/badges/{volunteerId}', [BadgeController::class, 'volunteerBadges']);

    // 🔹 Volunteer Management
    Route::apiResource('/volunteers', VolunteerController::class);

    Route::get('/warnings', [WarningController::class,'index']);
    Route::post('/warnings/{id}/approve', [WarningController::class,'approve']);
    Route::post('/warnings/{id}/reject', [WarningController::class,'reject']);


    Route::post('/volunteers/{id}/promote', [VolunteerAdminController::class,'promote']);
    Route::post('/volunteers/{id}/demote',  [VolunteerAdminController::class,'demote']);

    //Voulnteer Feadback
    Route::get('/events/{eventId}/feedback', [EventFeedbackController::class,'indexByEvent']);

    Route::get('/feedback/volunteer/{volunteerId}', [EventFeedbackController::class,'feedbackForVolunteer']);

    Route::apiResource('/donations', DonationController::class);

    // Donations
    // Step 1: Initialize a donation record and get back a donation_id
    Route::post('/donate/init', [DonationController::class, 'init'])->name('donations.init');

    // Step 2: Confirm the donation status after payment processing
    Route::post('/donate/confirm', [DonationController::class, 'confirm'])->name('donations.confirm');

    // list donations
    Route::get('/projects/{project}/donations', [DonationController::class, 'listByProject'])->name('projects.donations');
    Route::get('/events/{event}/donations', [DonationController::class, 'listByEvent'])->name('events.donations');

    // Admin Statistics
    Route::get('/donations-statistics', [DonationController::class, 'statistics'])->name('donations.stats');

    Route::apiResource('expenses', ExpenseController::class);

    Route::apiResource('donations', DonationController::class);

    Route::get('/financial-statistics', [FinanceController::class, 'statistics'])->name('donations.stats');

    // File Uploader
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{id}', [DocumentController::class, 'show']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    //Download file
    Route::get('documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    

   // Route::get('/search/volunteers', [SearchController::class, 'searchVolunteers']);
});

/*
|--------------------------------------------------------------------------
| Supervisor Routes
|--------------------------------------------------------------------------
|
| These routes require the user to be authenticated and have the "Supervisor" role.
|
*/

Route::middleware(['auth:sanctum', 'role:Supervisor'])->prefix('supervisor')->group(function () {

    /**
     * Volunteer Management
     */
    Route::get('/search/volunteers', [SearchController::class, 'searchVolunteers']);
    Route::get('/volunteers', [VolunteerAdminController::class, 'index']);

    /**
     * Events Management
     */
    Route::get('/events', [EventController::class, 'supervisorEvents']);
    Route::get('/events/log', [EventController::class, 'supervisorEventLog']);
    Route::get('/events/{eventId}/qr', [EventController::class, 'generateQrCode']);

    /**
     * Attendance Management
     */
    // View attendance list for a specific event
    Route::get('/attendance/event/{eventId}', [AttendanceController::class, 'eventAttendance']);

    // Get registered volunteers for a specific event (for manual check-in/out or feedback)
    Route::get('/attendance/event/{eventId}/registrations', [AttendanceController::class, 'eventRegistrations']);

    // Manually check-in or check-out volunteers
    Route::post('/attendance/manual', [AttendanceController::class, 'manualAttendance']);

    /**
     * Tasks Management
     */
    Route::get('/tasks', [TaskController::class, 'supervisorTasks']);
    Route::get('/tasks/{id}', [TaskController::class, 'supervisorShow']);
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'updateTask']);
    Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask']);

    /**
     * Evaluation Management
     */
    Route::post('/evaluations', [EvaluationController::class, 'store']);
    Route::put('/evaluations/{id}', [EvaluationController::class, 'update']);
    Route::delete('/evaluations/{id}', [EvaluationController::class, 'destroy']);
    Route::get('/events/{eventId}/evaluations', [EvaluationController::class, 'indexForEvent']);
    Route::get('/evaluations/{id}', [EvaluationController::class, 'show']);
    //list badges for Volunteer
    Route::get('/badges/all', [BadgeController::class, 'allBadges']);
    Route::get('/badges/{volunteerId}', [BadgeController::class, 'volunteerBadges']);

    //Voulnteer Feadback
    Route::get('/events/{eventId}/feedback', [EventFeedbackController::class,'indexByEvent']);

    Route::get('/feedback/volunteer/{volunteerId}', [EventFeedbackController::class,'feedbackForVolunteer']);

});


/*
|--------------------------------------------------------------------------
| Volunteer Dashboard Routes
|--------------------------------------------------------------------------
|
| These routes require the user to be authenticated and have the "Volunteer" role.
|
*/

Route::middleware(['auth:sanctum', 'role:Volunteer'])->prefix('volunteer')->group(function () {
    // 🔹 Change default password
    Route::post('/change-default-password', [ForgotPasswordController::class, 'changeDefaultPassword']);

    // 🔹 Event registration and withdrawal
    Route::post('/event-register', [EventRegistrationController::class, 'register']);
    Route::post('/event-withdraw', [EventRegistrationController::class, 'withdraw']);

    Route::get('/events', [EventController::class, 'volunteerEvents']);
    Route::get('/events/log', [EventController::class, 'volunteerEventLog']);

    // 🔹 Get recommended events and top projects
    Route::get('/events/recommended', [EventController::class, 'recommended']);
    Route::get('/projects/top', [ProjectController::class, 'top']);

    // 🔹 Volunteer profile management
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/tasks', [TaskController::class, 'volunteerTasks']);
    Route::post('/tasks/{id}/status', [TaskController::class, 'updateTaskStatus']);
    Route::post('/tasks/{id}/completion', [TaskController::class, 'updateCompletionState']);
    // 🔹 Search events and projects
 //   Route::get('search/events', [SearchController::class, 'searchEvents']);
   // Route::get('search/projects', [SearchController::class, 'searchProjects']);

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/volunteer/log', [AttendanceController::class, 'volunteerAttendanceLog']);

    Route::get('/evaluations', [EvaluationController::class, 'myEvaluations']);
    //Badges
    Route::get('/badges', [BadgeController::class, 'myBadges']);

    //Feadback
    Route::post('/feedback', [EventFeedbackController::class,'store']);
    Route::get('/feedback/event/{eventId}', [EventFeedbackController::class,'myEventFeedback']);
});

