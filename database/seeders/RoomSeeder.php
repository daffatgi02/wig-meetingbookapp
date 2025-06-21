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
                'name' => 'Ruang Dealing',
                'location' => 'Lantai 2',
                'capacity' => 15,
                'facilities' => ['proyektor BENQ', 'AC'],
                'description' => '-',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Meeting 2',
                'location' => 'Lantai 2',
                'capacity' => 8,
                'facilities' => ['TV Xiaomi', 'AC', 'whiteboard'],
                'description' => '-',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Komunal',
                'location' => 'Lantai 2',
                'capacity' => 10,
                'facilities' => ['TV Cocoa', 'Non AC', 'whiteboard'],
                'description' => '-',
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