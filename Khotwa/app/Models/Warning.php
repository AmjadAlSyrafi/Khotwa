<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    /** @use HasFactory<\Database\Factories\WarningFactory> */
    use HasFactory;

        protected $fillable = [
        'volunteer_id',
        'event_id',
        'supervisor_id',
        'reason',
        'status',
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    
}
