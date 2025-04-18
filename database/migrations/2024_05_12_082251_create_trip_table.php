<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('TeamId')->constrained('teams');
            $table->string('Title');
            $table->date('StartDate');
            $table->date('EndDate');
            $table->date('StartBooking');
            $table->date('EndBooking');
            $table->string('Location');
            $table->enum('Type' , ['Tour' , 'Adventure' , 'Cultural' , 'Excursions' , 'Leisure']);
            $table->enum('Level' , ['Hard' , 'Medium' , 'Easy']);
            $table->integer('SubLimit');
            $table->float('Cost');
            $table->string('Description');
            $table->enum('Retrieve', ['true', 'false']);
            $table->string('Requirements');
            $table->double('Rate')->default(0);;
            $table->string('TripPhoto')->nullable();
            $table->enum('Status' , ['Opened' , 'Done' , 'Cancelled']);
            $table->timestamps();
           // $table->date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip');
    }
};
