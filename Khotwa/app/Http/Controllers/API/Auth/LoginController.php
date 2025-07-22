<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OtpService;

class LoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Incorrect credentials'], 401);
        }

        $user = Auth::user();

        // Check if email is verified
        if (!$user->email_verified) {
            // Generate and send OTP
            $otp = $this->otpService->generateOtp($user);
            $this->otpService->sendOtpEmail($user, $otp);
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status'  => 'unverified_email',
                'message' => 'Please verify your email. OTP has been sent.',
                'email'   => $user->email,
                'token'   => $token,
            ], 403);
        }

        // Check if password has been changed
        if (!$user->password_verified) {
            return response()->json([
                'status'  => 'unverified_password',
                'message' => 'Please change your default password before continuing.',
            ], 403);
        }

        // All good → issue token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }
}
