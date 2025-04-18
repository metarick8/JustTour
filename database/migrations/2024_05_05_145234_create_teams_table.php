<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('TeamName');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->string('Description')->nullable();
            $table->string('ContactInfo')->nullable();
            $table->bigInteger('Wallet')->default(0);
            $table->double('Rate')->default(0);
            $table->string('ProfilePhoto')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
