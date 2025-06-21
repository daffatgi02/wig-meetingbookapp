<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DefaultSettingsSeeder::class,
            AdminUserSeeder::class,
            RoomSeeder::class,
        ]);
    }
}