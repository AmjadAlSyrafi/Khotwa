<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\{SendResetOtpRequest, ChangeDefaultPasswordRequest, ResetPasswordRequest};
use App\Services\{OtpService, PasswordService};
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    protected $otpService;
    protected $userService;

    public function __construct(OtpService $otpService, PasswordService $userService)
    {
        $this->otpService = $otpService;
        $this->userService = $userService;
    }

    public function sendResetOtp(SendResetOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No user found with this email.'], 404);
        }

        try {
            $otp = $this->otpService->generateOtp($user);
            $this->otpService->sendOtpEmail($user, $otp);

            return response()->json(['message' => 'OTP sent successfully to email.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeDefaultPassword(ChangeDefaultPasswordRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->password_verified) {
            return response()->json(['message' => 'Password already changed.'], 400);
        }

        $this->userService->updatePassword($user, $request->new_password, true);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$this->otpService->isValidOtp($user, $request->otp)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $this->userService->updatePassword($user, $request->password);
        $this->otpService->clearOtpData($user);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
