<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Auth\VerifyOtpController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

//  المستخدم الحالي (مع توكن)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// اختبار قاعدة البيانتات
Route::get('/check-db', function () {
    try {
        return response()->json([
            'message' => ' connection on DB are succes ',
            'database' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'fail connection on DB',
            'error' => $e->getMessage()
        ], 500);
    }
});

// اختبار إرسال بريد
Route::post('/test-mail', function (Request $request) {
    $email = $request->input('email');

    if (!$email) {
        return response()->json(['message' => 'please enter email.'], 422);
    }

    try {
        Mail::raw('Laravel API', function ($message) use ($email) {// رسالة تجريبية من لارافيل
            $message->to($email)
                    ->subject(' test send email API');
        });

        return response()->json(['message' => ' done send the email to: ' . $email]);
    } catch (\Exception $e) {
        return response()->json(['message' => '  faild send the email :  ', 'error' => $e->getMessage()], 500);
    }
});

// تسجيل مستخدم جديد
Route::post('/register', [RegisterController::class, 'register']);// on

//  تحقق من OTP بعد التسجيل
Route::post('/verify-otp', [VerifyOtpController::class, 'verify']); // on

//  إعادة إرسال OTP للتحقق
Route::post('/resend-otp', [VerifyOtpController::class, 'resend']);// on

// تسجيل الدخول
Route::post('/login', [LoginController::class, 'login']); // on

// تسجيل الخروج (بدو توكن)
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);

// نسيان كلمة المرور=> إرسال OTP
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp']);// on

//  إعادة تعيين كلمة المرور: إدخال OTP + كلمة جديدة
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);//on

// من اجل ادارة المستخدمين من قبل الادمن
Route::middleware(['auth:sanctum', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::get('/users/{id}', [UserManagementController::class, 'show']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
});

// لوحة المشرف مثل تحكم للمشرف
Route::middleware(['auth:sanctum', 'role:Supervisor'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => ' supervisoration pannel']);
    });
});

// لوحة المتطوع
Route::middleware(['auth:sanctum', 'role:Volunteer'])->prefix('volunteer')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => ' volunteer pannel ']);
    });
});

