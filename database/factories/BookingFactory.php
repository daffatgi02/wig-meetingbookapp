<?php
// database/factories/BookingFactory.php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition()
    {
        $startTime = $this->faker->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00']);
        $endTime = Carbon::parse($startTime)->addHours($this->faker->numberBetween(1, 3))->format('H:i');
        
        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'purpose' => $this->faker->randomElement(['Meeting', 'Training', 'Presentation', 'Workshop', 'Conference']),
            'participant_count' => $this->faker->numberBetween(2, 15),
            'booking_date' => $this->faker->dateTimeBetween('today', '+30 days'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement(['pending', 'approved', 'completed']),
        ];
    }

    public function pending()
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved()
    {
        return $this->state([
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }
}