<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    //
    public function register(Request $request)
    {

        $request->validate([
            'username' => 'required|string|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        // الكود من 5 ارقام
        $otp = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->otp_verified_for = 'register';
        $user->save();

        // إرسال الكود بالبريد
        Mail::raw("your code is : $otp", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('  the code vrify account ');
        });

        return response()->json([
            'message' => 'done create account succesfully.. check your email for on account',
            'user_id' => $user->id
        ], 201);
    
    }
}
