<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFeedback extends Model
{
    /** @use HasFactory<\Database\Factories\EventFeedbackFactory> */
    use HasFactory;
    protected $table = 'event_feedback';

    protected $fillable = [
        'event_id', 'volunteer_id', 'rating', 'comment'
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}
