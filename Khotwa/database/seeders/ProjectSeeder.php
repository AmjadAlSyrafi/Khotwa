<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('projects')->insert([
            [
                'name' => 'Community Cleanup',
                'description' => 'Organizing a cleanup campaign in the city.',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'status' => 'Active',
                'target_donation' => 5000,
                'donated_amount' => 1200,
                'remaining_amount' => 3800,
                'total_volunteers' => 15,
                'total_events' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'School Renovation',
                'description' => 'Helping renovate schools and classrooms.',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'status' => 'completed',
                'target_donation' => 10000,
                'donated_amount' => 2500,
                'remaining_amount' => 7500,
                'total_volunteers' => 20,
                'total_events' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'School Renovation',
                'description' => ' renovate schools and classrooms.',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'status' => 'postponed',
                'target_donation' => 15000,
                'donated_amount' => 5500,
                'remaining_amount' => 7500,
                'total_volunteers' => 10,
                'total_events' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
