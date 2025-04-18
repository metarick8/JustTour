<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ReserveTripId')->constrained('reserve_trips');
            $table->double('Value');
            $table->string('Review');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('rate_trip');
    }
};
