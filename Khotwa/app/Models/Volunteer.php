<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    /** @use HasFactory<\Database\Factories\VolunteerFactory> */
    use HasFactory;

protected $fillable = [
    'full_name',
    'gender',
    'birth_date',
    'phone',
    'email',
    'address',
    'city_id',
    'education_level',
    'university',
    'registration_date',
    'volunteering_years',
    'motivation',
    'availability',
    'preferred_time',
    'interests',
    'emergency_contact_name',
    'emergency_contact_phone',
    'emergency_contact_relationship',
    'user_id',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'volunteer_skills');
    }

    public function statusHistory()
    {
        return $this->hasMany(VolunteerStatusHistory::class);
    }

    protected $casts = [
    'birth_date' => 'date',
    'registration_date' => 'date',
    'availability' => 'array',
    'interests' => 'array',
];

}

