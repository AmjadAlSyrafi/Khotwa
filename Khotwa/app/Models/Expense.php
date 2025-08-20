<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'date',
        'project_id',
        'event_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
