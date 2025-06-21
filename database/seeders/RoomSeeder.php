<?php
// database/seeders/RoomSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'name' => 'Meeting Room A',
                'location' => 'Lantai 1',
                'capacity' => 10,
                'facilities' => ['proyektor', 'AC', 'whiteboard', 'wifi'],
                'description' => 'Ruang meeting kecil untuk diskusi tim',
                'is_active' => true,
            ],
            [
                'name' => 'Meeting Room B',
                'location' => 'Lantai 2',
                'capacity' => 20,
                'facilities' => ['proyektor', 'AC', 'whiteboard', 'wifi', 'sound_system'],
                'description' => 'Ruang meeting sedang untuk presentasi',
                'is_active' => true,
            ],
            [
                'name' => 'Conference Room',
                'location' => 'Lantai 3',
                'capacity' => 50,
                'facilities' => ['proyektor', 'AC', 'whiteboard', 'wifi', 'sound_system', 'microphone'],
                'description' => 'Ruang konferensi besar untuk acara resmi',
                'is_active' => true,
            ],
            [
                'name' => 'Training Room',
                'location' => 'Lantai 2',
                'capacity' => 30,
                'facilities' => ['proyektor', 'AC', 'whiteboard', 'wifi', 'flipchart'],
                'description' => 'Ruang training untuk pelatihan karyawan',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }
    }
}