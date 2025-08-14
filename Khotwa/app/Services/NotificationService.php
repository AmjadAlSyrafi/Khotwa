<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\Task;
use App\Models\Event;

class NotificationService
{

     // basic notification

    public static function sendNotification(User $user, string $title, string $message, string $type, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'read_at' => null
        ]);


        return $notification;
    }


     // Notify volunteer about a new assigned task

    public static function notifyVolunteerTaskAssigned(Volunteer $volunteer, Task $task): Notification
    {
        return self::sendNotification(
            $volunteer->user,
            'New Task Assigned',
            'You have been assigned a new task: ' . $task->title,
            'task_assigned',
            ['task_id' => $task->id]
        );
    }


     // Notify volunteer about task status change

    public static function notifyVolunteerTaskStatusChanged(Volunteer $volunteer, Task $task, string $status): Notification
    {
        return self::sendNotification(
            $volunteer->user,
            'Task Status Updated',
            'Task "' . $task->title . '" status changed to: ' . $status,
            'task_status_changed',
            [
                'task_id' => $task->id,
                'status' => $status
            ]
        );
    }


     // Notify volunteer about event registration approval

    public static function notifyVolunteerEventAccepted(Volunteer $volunteer, Event $event): Notification
    {
        return self::sendNotification(
            $volunteer->user,
            'Event Registration Approved',
            'Your registration for event: ' . $event->title . ' has been approved',
            'event_accepted',
            ['event_id' => $event->id]
        );
    }


    //  Notify supervisor about volunteer withdrawal

 public static function notifySupervisorVolunteerWithdrawn($supervisor, $volunteer, $task)
    {
        self::sendNotification(
            $supervisor,
            'Volunteer Withdrawal',
            'volunteer'. $volunteer->full_name .'has withdrawn from task:'. $task->title,
            'volunteer_withdrawn',
            ['task_id' => $task->id, 'volunteer_id' => $volunteer->id]
        );
    }
}

