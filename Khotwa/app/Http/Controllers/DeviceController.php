<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|in:web,android,ios'
        ]);

        $user = $request->user();

        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $user->id, 'token' => $request->token],
            [
                'platform' => $request->platform ?? 'web',
                'last_used_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['message' => 'Device registered']);
    }

    public function unregister(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $user = $request->user();
        DB::table('device_tokens')->where([
            'user_id' => $user->id,
            'token' => $request->token,
        ])->delete();

        return response()->json(['message' => 'Device unregistered']);
    }
}

