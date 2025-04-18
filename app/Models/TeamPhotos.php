<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamPhotos extends Model
{
    use HasFactory;

    public function Team(){
        return $this->hasOne(Team::class, 'TeamId');
    }
}
