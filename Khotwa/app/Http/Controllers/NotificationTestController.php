<?php

namespace App\Http\Controllers;
use App\Jobs\SendPushNotification;

use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
    //
    public function test(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = ['title' => 'Test Notification', 'body' => 'This is a test message from the server'];

        $data = ['type' => 'test', 'note' => 'backend-test'];

        // ضع في queue
        SendPushNotification::dispatch($user->id, $notification, $data);

        return response()->json(['message' => 'Test notification queued']);
    }
}

