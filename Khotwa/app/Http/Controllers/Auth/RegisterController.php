<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    //
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

     $role = Role::where('name', 'Volunteer')->first();

            if (!$role) {
    return redirect()->back()->withErrors(['role' => 'دور Volunteer غير موجود'])->withInput();
            }

        $user = User::create([
               'username' => $request->username,
               'email' => $request->email,
               'password' => Hash::make($request->password),
               'role_id' => $role->id,
]);

        // إرسال رابط تفعيل البريد
        event(new Registered($user));
        return redirect('/login')->with('status', 'تم التسجيل.. يرجى التحقق من بريدك الإلكتروني.');
    }
}

