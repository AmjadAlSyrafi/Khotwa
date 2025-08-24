<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;

class VolunteerApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skill::inRandomOrder()->take(2)->pluck('id')->toArray();

        DB::table('volunteer_applications')->insert([
            [
                'full_name'  => 'Ali Ahmad',
                'phone'      => '0999999999',
                'email'      => 'alaa@example.com',
                'gender'     => 'Male',
                'date_of_birth' => '1998-05-10',
                'study'      => 'Computer Science',
                'career'     => 'Software Developer',
                'city'       => 'Damascus',
                'address'    => 'Mezzeh Street',
                'interests'  => json_encode(['Education', 'Community Service']),
                'availability' => json_encode(['Weekends']),
                'preferred_time' => '3-5 hours per week',
                'volunteering_years' => 2,
                'skills'     => json_encode($skills),
                'motivation' => 'I want to contribute to my community.',
                'emergency_contact_name' => 'Omar Ahmad',
                'emergency_contact_phone' => '0988888888',
                'emergency_contact_relationship' => 'parent',
                'status'     => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
