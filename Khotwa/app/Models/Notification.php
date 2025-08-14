<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'read_at',
        'data' ,
        'event_id',
        'task_id'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع لمهام بحال كان الاشعارات للمهام
    public function task()
    {
        return $this->belongsTo(Task::class);
    }


    // علاقة مع الفعاليات اذا الاشعارات للفعاليات
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}
