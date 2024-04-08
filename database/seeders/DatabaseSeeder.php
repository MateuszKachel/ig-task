<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create(
            [
                'name' => 'Mateusz Kachel',
                'email' => 'mateuszkachel@gmail.com',
                'password' => bcrypt('StrongPassword!444'),
            ]
        );

        Airline::create(['name' => 'DTR']);
    }
}
