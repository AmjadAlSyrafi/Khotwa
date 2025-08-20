<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

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

}

