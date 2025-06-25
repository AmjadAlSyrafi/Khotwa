<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // التحقق من تفعيل البريد
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors(['email' => 'يرجى تفعيل البريد الإلكتروني أولاً.']);
            }

            // التحقق من الحالة
            if ($user->status != 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'تم تعطيل الحساب.']);
            }

            return redirect('/'); // وجه المستخدم بعد تسجيل الدخول
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }

    protected function redirectTo()
    {
    $role = auth()->user()->role->name;
    return match ($role) {
        'Admin'      => '/admin/dashboard',
        'Supervisor' => '/supervisor/dashboard',
        'Volunteer'  => '/volunteer/dashboard',
        default      => '/',
    };
    }

    public function role()
    {
    return $this->belongsTo(Role::class);
    }

}

