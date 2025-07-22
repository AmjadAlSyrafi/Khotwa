<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens;

    protected $fillable =
     [
        'username',
        'email',
        'password',
        'role_id',
        'volunteer_id',
        'email_verified',
        'password_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',

    ];

        // مشان تقييد وصول المستخدم حسب النوع
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function volunteer()
    {
        return $this->hasOne(Volunteer::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

}

