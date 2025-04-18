<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetrievePayment extends Model
{
    use HasFactory;

    public function ReserveTrip(){
        return $this->hasOne(ReserveTrip::class, 'ReserveTripId');
    }
}
