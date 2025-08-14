<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    /** @use HasFactory<\Database\Factories\BadgeFactory> */
    use HasFactory;

    protected $table = 'badges';
    public function volunteers() {
    return $this->belongsToMany(Volunteer::class, 'volunteer_badges')
        ->withPivot(['awarded_at'])
        ->withTimestamps();
}

}
