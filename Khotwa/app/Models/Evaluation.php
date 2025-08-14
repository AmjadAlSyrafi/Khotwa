<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'volunteer_id', 'event_id', 'supervisor_id',
        'punctuality', 'work_quality', 'teamwork', 'initiative', 'discipline',
        'average_rating', 'notes',
        'initiated', 'mentored', 'creative_contribution', 'impactful', 'inspirational'
    ];

    public $timestamps = true; // created_at & updated_at

    // Relations
    public function volunteer()
    {
        return $this->belongsTo(\App\Models\Volunteer::class);
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'supervisor_id');
    }
}
