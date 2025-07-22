<?php

namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate and save an OTP for the given user.
     *
     * @param User $user
     * @param string|null $for
     * @return int
     */
    public function generateOtp(User $user): int
    {
        $otp = random_int(10000, 99999);

        Otp::create([
            'user_id' => $user->id,
            'code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        return $otp;
    }

    /**
     * Send the OTP to the user's email.
     *
     * @param User $user
     * @param int $otp
     * @param string $subject
     * @return void
     */
    public function sendOtpEmail(User $user, int $otp, string $subject = 'Your OTP Code'): void
    {
        Mail::raw("Your OTP is: {$otp}", function ($message) use ($user, $subject) {
            $message->to($user->email)
                    ->subject($subject);
        });
    }

    /**
     * Clear all OTP records for the user (optional).
     *
     * @param User $user
     * @return void
     */
    public function clearOtpData(User $user): void
    {
        Otp::where('user_id', $user->id)->delete();
    }

    /**
     * Check if the given OTP is valid for the user.
     *
     * @param User $user
     * @param string $otp
     * @return bool
     */
    public function isValidOtp(User $user, string $otp): bool
    {
        $otpRecord = Otp::where('user_id', $user->id)
                        ->where('code', $otp)
                        ->where('expires_at', '>=', now())
                        ->latest()
                        ->first();

        return !is_null($otpRecord);
    }
}
