<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retrieve extends Model
{
    use HasFactory;

    protected $table = 'retrieve_for_trips';
    protected $fillable = [
        'TripId',
        'EndDate',
        'Percent',
    ];


    public function trip() {
    return $this->belongsTo(Trip::class, 'TripId');
}
}
