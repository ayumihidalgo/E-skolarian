<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;


use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /*Seed the application's database.*/
  public function run(): void
  {
        User::create([
            'username' => 'SuperAdminUser',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('@Eskolarian12345'),
            'role' => 'super admin',
            'organization_acronym' => 'PUPSRC',
            'profile_pic' => 'images/profiles/student.png', // Example value,
            'role_name' => 'Super Admin',
        ]);

            User::create([
            'username' => 'Leny Salmingo',
            'email' => 'lenysalmingo@gmail.com',
            'password' => Hash::make('@Eskolarian12345'),
            'role' => 'admin',
            'organization_acronym' => 'PUPSRC',
            'profile_pic' => 'images/profiles/student.png', // Example value,
            'role_name' => 'Admin',
        ]);

            User::create([
            'username' => 'TAPNOTCH',
            'email' => 'tapnotch@gmail.com',
            'password' => Hash::make('@Tapnotch12345'),
            'role' => 'student',
            'organization_acronym' => 'TAPNOTCH',
            'profile_pic' => 'images/profiles/student.png', // Example value,
            'role_name' => 'Student',
        ]);
    }
}
