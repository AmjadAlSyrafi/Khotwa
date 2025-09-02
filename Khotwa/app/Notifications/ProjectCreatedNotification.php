<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectCreatedNotification extends Notification
{
    use Queueable;

    protected $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function via($notifiable)
    {
        return ['database']; // لاحقاً ممكن نضيف fcm/email
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'تمت إضافة مشروع جديد',
            'body' => "تمت إضافة مشروع جديد: {$this->project->name}",
            'project_id' => $this->project->id,
            'type' => 'project_created',
        ];
    }
}
