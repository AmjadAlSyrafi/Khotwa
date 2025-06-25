<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    /** @use HasFactory<\Database\Factories\VolunteerFactory> */
    use HasFactory;

    protected $fillable = [
        'full_name', 'gender', 'birth_date', 'phone', 'email',
        'city', 'education_level', 'university', 'registration_date', 'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

