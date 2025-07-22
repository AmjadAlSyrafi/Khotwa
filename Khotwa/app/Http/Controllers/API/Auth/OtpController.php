<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\VerifyOtpRequest;
use App\Services\OtpService;
use App\Models\User;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function verify(VerifyOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !$this->otpService->isValidOtp($user, $request->otp)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $this->otpService->clearOtpData($user);

        if (!$user->email_verified) {
            $user->email_verified = true;
            $user->save();
        }

        return response()->json(['message' => 'OTP verified successfully.']);
    }
}
