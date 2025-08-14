<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = Auth::user();
        $notifications = $user->notifications()
                            ->orderBy('created_at', 'desc')
                            ->get();

        return ApiResponse::success($notifications, 'Notifications retrieved successfully');
    }

    // وضع علامة مقروءة للااشعار 
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return ApiResponse::error('Notification not found', 404);
        }

        $notification->markAsRead();

        return ApiResponse::success(null, 'Notification marked as read');
    }

    // وضع علامة على كل الإشعارات كمقروءة

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return ApiResponse::success(null, 'All notifications marked as read');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return ApiResponse::error('Notification not found', 404);
        }

        $notification->delete();

        return ApiResponse::success(null, 'Notification deleted successfully');
    }
}
