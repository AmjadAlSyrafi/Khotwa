<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\VolunteerStatusHistoryFactory> */
    use HasFactory;
    protected $table = 'volunteer_status_history';

    protected $fillable = [
        'volunteer_id',
        'status',
        'changed_at',
        'changed_by',
    ];

        protected $casts = [
        'changed_at' => 'datetime',
    ];

        /**
     * Get the volunteer that the status history belongs to.
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

}
