<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class OtpController extends Controller
{
    //
    public function verify(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'otp_code' => 'required|digits:5'
        ]);

        $user = User::find($request->user_id);

        if (!$user || $user->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'رمز التحقق غير صحيح'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'انتهت صلاحية الرمز'], 400);
        }

        if ($user->otp_verified_for === 'register') {
            $user->email_verified_at = now();
        }

        // إزالة البيانات للكود بعد التحقق
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->otp_verified_for = null;
        $user->save();

        return response()->json(['message' => 'تم التحقق من الرمز بنجاح.']);
    }
}
