<?php
// database/migrations/xxxx_xx_xx_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Topik kegiatan
            $table->text('description')->nullable(); // Deskripsi tambahan
            $table->string('purpose'); // Tujuan pemesanan
            $table->integer('participant_count'); // Jumlah peserta
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'draft', 
                'pending', 
                'approved', 
                'rejected', 
                'ongoing', 
                'completed', 
                'cancelled'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->boolean('requires_reapproval')->default(false);
            $table->foreignId('created_by_admin')->nullable()->constrained('users'); // Jika dibuat oleh admin
            $table->timestamps();

            // Index untuk optimasi query
            $table->index(['room_id', 'booking_date', 'start_time', 'end_time']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};