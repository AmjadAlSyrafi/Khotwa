<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerBadge extends Model
{
    protected $table = 'volunteer_badges';
    public $timestamps = true;
    protected $fillable = [
        'volunteer_id',
        'badge_id',
        'awarded_at',
    ];

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
