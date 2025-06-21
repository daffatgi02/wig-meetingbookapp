<?php
// database/factories/RoomFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => 'Meeting Room ' . $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'location' => 'Lantai ' . $this->faker->numberBetween(1, 5),
            'capacity' => $this->faker->randomElement([6, 10, 15, 20, 30, 50]),
            'facilities' => $this->faker->randomElements(
                ['proyektor', 'AC', 'whiteboard', 'wifi', 'sound_system', 'microphone', 'flipchart'],
                $this->faker->numberBetween(3, 6)
            ),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}