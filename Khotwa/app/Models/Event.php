<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'duration_hours',
        'location',
        'lat',
        'lng',
        'status',
        'project_id',
        'required_volunteers',
        'registered_count',
    ];

    public function toSearchableArray()
    {
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'location' => $this->location,
        'date' => $this->date,
        'time' => $this->time
    ];
    }
    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    
        public function donations() {
        return $this->hasMany(Donation::class);
    }

    public function expenses() {
        return $this->hasMany(Expense::class);
    }
}
