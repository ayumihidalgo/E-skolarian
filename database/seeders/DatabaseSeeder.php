<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;


use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username' => 'SuperAdmintUser',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('@Eskolarian12345'),
            'role' => 'super admin',
            'profile_pic' => 'images/profiles/student.png', // Example value

        ]);
    }

}
