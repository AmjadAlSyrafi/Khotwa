<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Teamwork',
            'Communication',
            'Leadership',
            'Problem Solving',
            'Creativity',
            'Event Planning',
            'First Aid',
            'Technical Support'
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->insertOrIgnore([
                'name' => $skill,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
