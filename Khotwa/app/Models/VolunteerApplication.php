<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    /** @use HasFactory<\Database\Factories\VolunteerApplicationFactory> */
        use HasFactory;

    protected $table = 'volunteer_applications';

    protected $casts = [
        'interests' => 'array',
        'availability' => 'array',
        'skills' => 'array',
        'date_of_birth' => 'date',
    ];

    protected $attributes = [
    'status' => 'pending',
];

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'study',
        'career',
        'city',
        'address',
        'interests',
        'availability',
        'preferred_time',
        'volunteering_years',
        'skills',
        'motivation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'status',
    ];

}
