<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    //
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'No user found with this email.'], 404);
        }

        $otp = random_int(10000, 99999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::raw("Your OTP to reset password is: {$otp}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset OTP');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'OTP sent successfully to email.'
        ]);
    }
}
