<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leaderboard extends Model
{
    use HasFactory;
    protected $table = 'leaderboard';
    protected $fillable = [
        'volunteer_id',
        'score',
        'last_updated',
    ];

    public $timestamps = false;

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
