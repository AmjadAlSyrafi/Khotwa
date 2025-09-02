<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericDatabaseNotification extends Notification
{
    use Queueable;

    private array $payload;

    /**
     * @param array $payload => ['title'=>'..','body'=>'..','data'=>[]]
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return ['database']; // يخزن في جدول notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->payload['title'] ?? null,
            'body' => $this->payload['body'] ?? null,
            'data' => $this->payload['data'] ?? []
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
