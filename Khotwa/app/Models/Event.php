<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Storage;

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
        'cover_image',
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
    protected $casts = [
        'date' => 'datetime',
    ];
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

        protected $appends = ['cover_image_url'];

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return Storage::url('images/defaults/event.png');
        }
        return Storage::url($this->cover_image);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $event) {
            if ($event->cover_image && Storage::disk('public')->exists($event->cover_image)) {
                Storage::disk('public')->delete($event->cover_image);
            }
        });
    }

}
