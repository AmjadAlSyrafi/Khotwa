<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@khotwa.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role_id'  => 1, // Admin
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@khotwa.com'],
            [
                'username' => 'supervisor',
                'password' => Hash::make('password123'),
                'role_id'  => 2, // Supervisor
            ]
        );

        User::updateOrCreate(
            ['email' => 'volunteer@khotwa.com'],
            [
                'username' => 'volunteer',
                'password' => Hash::make('password123'),
                'role_id'  => 3, // Volunteer
            ]
        );
    }
}
