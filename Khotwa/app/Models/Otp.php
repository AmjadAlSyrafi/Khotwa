<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'for',
    ];

    protected $dates = ['expires_at'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
