<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'cover_image',

    ];

    public function toSearchableArray()
    {
    return [
        'id' => $this->id,
        'name' => $this->name,
        'description' => $this->description,
        'start_date' => $this->start_date,
        'end_date' => $this->end_date
    ];
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

        public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function expenses() {
        return $this->hasMany(Expense::class);
    }

   protected $appends = ['cover_image_url'];

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return Storage::url('images/defaults/project.png');
        }
        return Storage::url($this->cover_image);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $project) {
            if ($project->cover_image && Storage::disk('public')->exists($project->cover_image)) {
                Storage::disk('public')->delete($project->cover_image);
            }
        });
    }

}

