<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// تجريبي للقاعدة البيانات
use Illuminate\Support\Facades\DB;
Route::get('/check-db', function () {
    return DB::connection()->getDatabaseName();
});

// تجريبي مشان الايميل
use Illuminate\Support\Facades\Mail;
Route::get('/test-mail', function () {
    Mail::raw('Laravel', function ($message) {
        $message->to('your_other_email@example.com')
                ->subject('test email Laravel');
    });
    return 'done send !';
});

// // الروابط للمشروع المهمة
// use App\Http\Controllers\Auth\RegisterController;
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
// Route::post('/register', [RegisterController::class, 'register'])->name('register');

// // ادخال تسجيل الدخول
// use App\Http\Controllers\Auth\LoginController;
// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
// Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use Illuminate\Support\Facades\Auth;
// //  مسار عرض تنبيه التفعيل
// Route::get('/email/verify', function () {
//     return view('auth.verify-email');
// })->middleware('auth')->name('verification.notice');

// //  عند الضغط على رابط التفعيل في البريد
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
//     return redirect('/dashboard');
// })->middleware(['auth', 'signed'])->name('verification.verify');

// //  لإعادة إرسال رابط التفعيل
// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();
//     return back()->with('message', ' done send link to your email ');
// })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// // لعرض نسيان كلمة المرور ولارسال رابط لاعادة التعين وادخال كلمة مرور جديدوة
// use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Auth\ResetPasswordController;
// Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// // من اجل تسجيل المستخدك من قبل الادمن
// use App\Http\Controllers\Admin\UserManagementController;
// Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
//     Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
//     Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
// });

// //تجريبي مشان تسجيل الميديل بالرول
// Route::middleware(['auth', 'role:Admin'])->group(function () {
//     Route::get('/admin/dashboard', fn () => ' Admin control pannel  ');
// });

// //  للمسؤول  (Admin) إدارة المستخدمين، المشاريع، إلخ
// Route::middleware(['auth' , 'role:Admin'])->prefix('admin')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('admin.dashboard');
//     });

//     Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
// });

// //  للمشرف  (Supervisor) التقييمات، الملاحظات، المتطوعين، إلخ
// Route::middleware(['auth', 'role:Supervisor'])->prefix('supervisor')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('supervisor.dashboard');
//     });

// });

// //  للمتطوع  (Volunteer) الفعاليات، المشاركات، ملفه الشخصي والخ
// Route::middleware(['auth', 'role:Volunteer'])->prefix('volunteer')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('volunteer.dashboard');
//     });

// });
