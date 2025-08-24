<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'gender','birth_date','phone','email','address',
        'city_id','education_level','university','registration_date',
        'volunteering_years','motivation','availability','preferred_time',
        'interests','emergency_contact_name','emergency_contact_phone',
        'emergency_contact_relationship','user_id','total_volunteer_hours', 'profile_image',
    ];

    /** searchable fields **/
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'city_id'   => $this->city_id,
            'university'=> $this->university,
            'education_level' => $this->education_level
        ];
    }

    /** Relations **/
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

    public function badges()
    {
        return $this->belongsToMany(Badge::class,'volunteer_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    protected $casts = [
        'birth_date'        => 'date',
        'registration_date' => 'date',
        'availability'      => 'array',
        'interests'         => 'array',
    ];

    protected $appends = ['profile_image_url'];
        public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) {
            //Default image
            return Storage::url('images/defaults/volunteer.png');
        }
        return Storage::url($this->profile_image);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $volunteer) {
            if ($volunteer->profile_image && Storage::disk('public')->exists($volunteer->profile_image)) {
                Storage::disk('public')->delete($volunteer->profile_image);
            }
        });
    }

}
