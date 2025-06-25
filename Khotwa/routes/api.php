<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\ResetPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// تسجيل مستخدم جديد
Route::post('/register', [RegisterController::class, 'register']);

// تسجيل الدخول
Route::post('/login', [LoginController::class, 'login']);

// تسجيل الخروج (بدو توكن)
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);

// إرسال رابط التفعيل من جديد
Route::middleware(['auth:sanctum'])->post('/email/verify/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'البريد مفعل بالفعل'], 400);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'تم إرسال رابط التفعيل إلى بريدك']);
});

// تأكيد التحقق بعد الكبس على الرابط
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'تم تفعيل البريد بنجاح']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// لاستعادة كلمةة المرور 
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
