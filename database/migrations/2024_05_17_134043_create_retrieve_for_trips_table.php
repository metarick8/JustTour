<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('retrieve_for_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('TripId')->constrained('trips');
            $table->date('EndDate');
            $table->smallInteger('Percent');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE retrieve_for_trips ADD CONSTRAINT chk_percent CHECK (Percent BETWEEN 0 AND 100)");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE retrieve_for_trips DROP CONSTRAINT chk_percent");

        Schema::dropIfExists('retrieve_for_trips');
    }
};
