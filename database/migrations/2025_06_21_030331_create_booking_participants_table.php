<?php
// database/migrations/xxxx_xx_xx_create_booking_participants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained(); // Jika peserta adalah user terdaftar
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_participants');
    }
};