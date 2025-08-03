<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\API\Auth\{
    RegisterController,
    LoginController,
    LogoutController,
    ForgotPasswordController,
    OtpController,
};

use App\Http\Controllers\API\Volunteer\{
    ProfileController,
};

use App\Http\Controllers\{
    VolunteerController,
    ProjectController,
    EventController,
    EventRegistrationController

};

use App\Http\Controllers\Admin\{
    VolunteerApplicationController,
    VolunteerAdminController,
};

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;

//
//  Public Utility Routes
//
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

//Add volunteerApplication
Route::post('/join-request', [VolunteerApplicationController::class, 'store']);


//
//  Authenticated User Info
//
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//
//  Authentication Routes Group
//
Route::prefix('auth')->group(function () {

    // 🔹 Registration
    Route::post('/register', [RegisterController::class, 'register']);

    // 🔹 Login & Logout
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);

    // 🔹 OTP Verification
    Route::post('/verify-otp', [OtpController::class, 'verify']);

    // 🔹 Forgot & Reset Password
    Route::post('/forget-password', [ForgotPasswordController::class, 'sendResetOtp']);
    Route::post('/confirm-reset-password', [ForgotPasswordController::class, 'reset']);

});

//
//  Admin Routes
//
Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    Route::apiResource('users', UserManagementController::class);
    Route::get('/join-requests', [VolunteerApplicationController::class, 'index']);
    Route::post('/applications/approve', [VolunteerApplicationController::class, 'approve']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('/events', EventController::class);
    Route::get('/volunteers', [VolunteerAdminController::class, 'index']);
    Route::post('/volunteers', [VolunteerAdminController::class, 'store']);
    Route::get('/volunteers/{id}', [VolunteerAdminController::class, 'show']);
    Route::put('/volunteers/{id}', [VolunteerAdminController::class, 'update']);
    Route::delete('/volunteers/{id}', [VolunteerAdminController::class, 'destroy']);
});

//  Supervisor Dashboard
//
Route::middleware(['auth:sanctum', 'role:Supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/tasks', [TaskController::class, 'supervisorTasks']);
    Route::get('/tasks/{id}', [TaskController::class, 'supervisorShow']);
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'updateTask']);
    Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
});

//
// ✅ Volunteer Dashboard
//
Route::middleware(['auth:sanctum', 'role:Volunteer'])->prefix('volunteer')->group(function () {
    Route::post('/change-default-password', [ForgotPasswordController::class, 'changeDefaultPassword']);
    Route::post('/event-register', [EventRegistrationController::class, 'register']);
    Route::post('/event-withdraw', [EventRegistrationController::class, 'withdraw']);
    Route::get('/volunteer/profile', [ProfileController::class, 'show']);
    Route::put('/volunteer/profile', [ProfileController::class, 'update']);
    Route::get('/tasks', [TaskController::class, 'volunteerTasks']); 
    Route::post('/tasks/{id}/accept', [TaskController::class, 'acceptTask']);
    Route::post('/tasks/{id}/reject', [TaskController::class, 'rejectTask']);
    Route::post('/tasks/{id}/withdraw', [TaskController::class, 'withdrawTask']);
    Route::post('/tasks/{id}/status', [TaskController::class, 'updateCompletionState']);
});

// راوتات البحث(المتطوع خاص للمشرف والباقي للكل )
Route::prefix('search')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/volunteers', [SearchController::class, 'searchVolunteers'])->middleware('role:supervisor');
    Route::get('/events', [SearchController::class, 'searchEvents']);
    Route::get('/projects', [SearchController::class, 'searchProjects']);
});

