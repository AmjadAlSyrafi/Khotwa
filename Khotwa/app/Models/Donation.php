<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    /** @use HasFactory<\Database\Factories\DonationFactory> */
    use HasFactory;

    protected $fillable = [
        'type','amount','description',
        'donor_name','donor_email',
        'method','transaction_id','payment_status',
        'project_id','event_id','donated_at',
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
