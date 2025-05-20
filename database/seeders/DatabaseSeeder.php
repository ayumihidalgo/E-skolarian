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
            'email' => 'superadmin@example.com',
            'password' => Hash::make('superadminpass'),
            'role' => 'super admin',
            'profile_pic' => 'images/profiles/student.png', // Example value

        ]);

        User::create([
            'username' => 'AdminUser',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpass'),
            'role' => 'admin',
            'profile_pic' => 'images/profiles/student.png', // Example value
        ]);

        User::create([
            'username' => 'StudentUser',
            'email' => 'student@example.com',
            'password' => Hash::make('studentpass'),
            'role' => 'student',
            'profile_pic' => 'images/profiles/student.png', // Example value
        ]);
    }

}