<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;
use App\Models\User;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skill::inRandomOrder()->take(2)->pluck('id')->toArray();

        $userId = User::updateOrCreate(
            ['email' => 'ali@example.com'],
            [
                'username' => 'ali_volunteer',
                'password' => bcrypt('password123'),
                'role_id'  => 3, // Volunteer role
            ]
        )->id;

        DB::table('volunteers')->insert([
            [
                'full_name'  => 'Ali Ahmad',
                'gender'     => 'Male',
                'birth_date' => '1998-05-10',
                'phone'      => '0999999999',
                'email'      => 'alaa@example.com',
                'city'       => 'Damascus',
                'address'    => 'Mezzeh Street',
                'interests'  => json_encode(['Education', 'Community Service']),
                'availability' => json_encode(['Weekends']),
                'preferred_time' => '3-5 hours per week',
                'volunteering_years' => 2,
                'motivation' => 'I want to contribute to my community.',
                'emergency_contact_name' => 'Omar Ahmad',
                'emergency_contact_phone' => '0988888888',
                'emergency_contact_relationship' => 'parent',
                'education_level' => 'Bachelor',
                'university' => 'Damascus University',
                'registration_date' => now(),
                'status'     => 'Active',
                'availability_days' => json_encode(['Saturday', 'Sunday']),
                'user_id'    => $userId,
                'total_volunteer_hours' => 0,
                'profile_image' => 'images/volunteers/default.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $volunteerId = DB::getPdo()->lastInsertId();
        foreach ($skills as $skillId) {
            DB::table('volunteer_skills')->insert([
                'volunteer_id' => $volunteerId,
                'skill_id'     => $skillId,
            ]);
        }
    }
}
