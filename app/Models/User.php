<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public function UserTeam(){
        return $this->hasMany(UserTeam::class, 'UserTeamId');
    }

    public function UserSite(){
        return $this->hasMany(UserSite::class, 'UserSiteId');
    }

    public function ReserveTrip(){
        return $this->hasMany(ReserveTrip::class, 'ReserveTripId');
    }
    

    protected $fillable = [
        'FirstName',
        'LastName',
        'Email',
        'Password',
        'Number',
        'Age'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
