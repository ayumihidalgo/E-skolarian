<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Sender',
            'email' => 'sender@example.com',
            'password' => Hash::make('sender123'),
        ]);

        User::create([
            'name' => 'Receiver',
            'email' => 'receiver@example.com',
            'password' => Hash::make('receiver123'),
        ]);

    }
}
