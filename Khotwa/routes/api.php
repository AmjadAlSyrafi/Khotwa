<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
Route::post('/register', [RegisterController::class, 'register']);

// تسجيل الدخول
Route::post('/login', [LoginController::class, 'login']);

// تسجيل الخروج (بدو توكن)
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);

// إرسال رابط التفعيل من جديد
Route::middleware(['auth:sanctum'])->post('/email/verify/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'the email is on acctully '], 400);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => ' done send link verify to your email ']);
});

// تأكيد التحقق بعد الكبس على الرابط
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => ' the email is on succesfully ']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// لاستعادة كلمةة المرور
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

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


