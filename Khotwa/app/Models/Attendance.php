<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;


    protected $table = 'attendance';

    protected $fillable = [
        'volunteer_id',
        'event_id',
        'checkin_time',
        'checkin_method',
        'supervisor_id',
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
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
