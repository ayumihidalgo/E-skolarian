<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;


use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**Seed the application's database.*/
    public function run(): void
    {
        $users = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'role_name' => 'Super Admin',
                'organization_acronym' => 'PUPSRC',
                'password' => Hash::make('password123'),
                'active' => true,
                'password_changed_at' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'super_admin',
                'profile_pic' => 'superadmin.png'
            ],
            [
                'username' => 'adminuser',
                'email' => 'admin@example.com',
                'role_name' => 'Admin',
                'organization_acronym' => 'PUPSRC',
                'password' => Hash::make('password123'),
                'active' => true,
                'password_changed_at' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin',
                'profile_pic' => 'admin.png'
            ],
            [
                'username' => 'studentuser',
                'email' => 'student@example.com',
                'role_name' => 'Student',
                'organization_acronym' => 'PUPSRC',
                'password' => Hash::make('password123'),
                'active' => true,
                'password_changed_at' => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'student',
                'profile_pic' => 'student.png'
            ]
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }


}