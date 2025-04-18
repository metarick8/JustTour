<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripRate extends Model
{
    use HasFactory;

    public function ReserveTrip()
    {
        return $this->belongsTo(ReserveTrip::class, 'ReserveTripId');
    }

    protected $fillable = [
        'ReserveTripId',
        'Value',
        'Review',
    ];

    // protected $attributes = [
    //     'Value',
    //     'Review',
    // ];
}
