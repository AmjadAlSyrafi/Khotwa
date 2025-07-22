<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Update the user's password.
     *
     * @param User $user
     * @param string $newPassword
     * @param bool $markVerified
     * @return void
     */
    public function updatePassword(User $user, string $newPassword, bool $markVerified = false): void
    {
        $user->password = Hash::make($newPassword);

        if ($markVerified) {
            $user->password_verified = true;
        }

        $user->save();
    }
}
