<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveTrip extends Model
{
    use HasFactory;
    protected $table = 'reserve_trips';
    protected $fillable = [
        'UserId',
        'TripId',
        'Count',        
    ];
    public function RetrievePayment(){
        return $this->hasMany(RetrievePayment::class, 'RetrievePaymentId');
    }

    public function TripRate(){
        return $this->hasMany(TripRate::class, 'TripRateId');
    }

    public function Trip(){
        return $this->belongsTo(Trip::class, 'TripId');
    }

}
