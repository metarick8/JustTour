<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoAlbumForTrip extends Model
{
    use HasFactory;

    public function Trip(){
        return $this->hasOne(Trip::class, 'TripId');
    }
}
