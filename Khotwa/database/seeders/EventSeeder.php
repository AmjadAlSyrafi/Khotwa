<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('events')->insert([
            [
                'title'               => 'Cleanup Campaign - Park',
                'description'         => 'Cleaning the main park.',
                'date'                => now()->addDays(7),
                'time'                => '10:00:00',
                'duration_hours'      => 4,
                'location'            => 'Central Park',
                'status'              => 'Upcoming',
                'project_id'          => 1,
                'required_volunteers' => 20,
                'current_volunteers'  => 5,
                'registered_count'    => 5,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'title'               => 'School Painting',
                'description'         => 'Painting classrooms at the local school.',
                'date'                => now()->addDays(14),
                'time'                => '09:00:00',
                'duration_hours'      => 6,
                'location'            => 'Al-Sham School',
                'status'              => 'open',
                'project_id'          => 2,
                'required_volunteers' => 15,
                'current_volunteers'  => 3,
                'registered_count'    => 3,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}
