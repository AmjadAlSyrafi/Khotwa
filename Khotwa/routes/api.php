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
    ResetPasswordController,
    VerifyOtpController,
};

use App\Http\Controllers\{
    VolunteerController
};

use App\Http\Controllers\Admin\{
    VolunteerApplicationController,
};

use App\Http\Controllers\Admin\UserManagementController;

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
    Route::post('/change-default-password', [ForgotPasswordController::class, 'changeDefaultPassword']);

});

//
//  Admin Routes
//
Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::get('/users/{id}', [UserManagementController::class, 'show']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    Route::get('/join-requests', [VolunteerApplicationController::class, 'index']);
    Route::post('/volunteers', [VolunteerController::class, 'store']);
    Route::post('/applications/approve', [VolunteerApplicationController::class, 'approve']);
});

//
//  Supervisor Dashboard
//
Route::middleware(['auth:sanctum', 'role:Supervisor'])->prefix('supervisor')->group(function () {

});

//
// ✅ Volunteer Dashboard
//
Route::middleware(['auth:sanctum', 'role:Volunteer'])->prefix('volunteer')->group(function () {

});
