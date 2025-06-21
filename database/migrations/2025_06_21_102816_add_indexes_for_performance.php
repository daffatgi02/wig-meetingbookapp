<?php
// database/migrations/xxxx_xx_xx_add_indexes_for_performance.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['status', 'booking_date']);
            $table->index(['user_id', 'booking_date']);
            $table->index(['room_id', 'status']);
            $table->index(['booking_date', 'start_time', 'end_time']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['created_at']);
            $table->index(['model_type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status', 'booking_date']);
            $table->dropIndex(['user_id', 'booking_date']);
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['booking_date', 'start_time', 'end_time']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['type', 'created_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['model_type', 'created_at']);
        });
    }
};