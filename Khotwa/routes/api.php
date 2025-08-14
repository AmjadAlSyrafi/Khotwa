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
    AttendanceController
};

// Admin-specific controllers
use App\Http\Controllers\Admin\{
    VolunteerApplicationController,
    VolunteerAdminController,
};
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;
use App\Models\Volunteer;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public & Utility Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible and do not require authentication.
|
*/

//  Check database connection
Route::get('/check-db', function () {
    try {
        return response()->json([
            'message' => 'Connection to DB is successful.',
            'database' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'DB connection failed.',
            'error' => $e->getMessage()
        ], 500);
    }
});

//  Send a test email
Route::post('/test-mail', function (Request $request) {
    $email = $request->input('email');
    if (!$email) {
        return response()->json(['message' => 'Please enter email.'], 422);
    }
    try {
        Mail::raw('Laravel API Test Email', function ($message) use ($email) {
            $message->to($email)->subject('Test Email API');
        });
        return response()->json(['message' => 'Email sent to: ' . $email]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to send email.', 'error' => $e->getMessage()], 500);
    }
});

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

    // 🔹 Volunteer Management
    Route::apiResource('/volunteers', VolunteerController::class);

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

    // notification about volunteer & supervisor
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});

});

