<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $table = "trips";
    public function reserveTrips()
{
    return $this->hasMany(ReserveTrip::class, 'TripId');
}


    public function PhotoAlbumForTrip(){
        return $this->hasMany(PhotoAlbumForTrip::class, 'PhotoAlbumForTripId');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'TeamId'); // Specify the foreign key name
    }

    public function retrieve() {
        return $this->hasOne(Retrieve::class, 'TripId');
    }
}
