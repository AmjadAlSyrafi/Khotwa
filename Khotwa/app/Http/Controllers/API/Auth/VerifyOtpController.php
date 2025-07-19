<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;

class VerifyOtpController extends Controller
{
    //التحقق من الرمز المرسل للبريد
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:5',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (
            $user->otp_code !== $request->otp ||
            Carbon::now()->gt($user->otp_expires_at)
        ) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Account verified successfully.']);
    }

     // إعادة إرسال رمز التحقق
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $otp = random_int(10000, 99999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Mail::raw("Your new verification code is: {$otp}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Resend OTP Code');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'OTP resent successfully.']);
    }
}
