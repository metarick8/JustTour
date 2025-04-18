<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Team extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public function Trips(){
        return $this->hasMany(Trip::class, 'TeamId');
    }


     public function UserTeam(){
        return $this->hasMany(UserTeam::class, 'TeamId'); // Correct foreign key
    }


    public function TeamPhotos(){
        return $this->hasMany(TeamPhotos::class, 'TeamPhotosId');
    }

    protected $fillable = [
        'TeamName',
        'Email',
        'Password',
        'Description',
        'ContactInfo',
        'ProfilePhoto'
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
