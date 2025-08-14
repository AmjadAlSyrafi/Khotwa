<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('badges')->insert([
            // 🏆 Participation Path (Blue)
            [
                'name' => 'New Participant',
                'slug' => 'new-participant',
                'category' => 'Participation',
                'level' => 1,
                'icon_path' => asset('images/badges/new_participant.png'),
                'description' => 'Joined their first event.',
            ],
            [
                'name' => 'Regular Participant',
                'slug' => 'regular-participant',
                'category' => 'Participation',
                'level' => 2,
                'icon_path' => 'images/badges/regular_participant.png',
                'description' => 'Attended 3 events.',
            ],
            [
                'name' => 'Active',
                'slug' => 'active',
                'category' => 'Participation',
                'level' => 3,
                'icon_path' => 'images/badges/active.png',
                'description' => 'Attended 7 events.',
            ],
            [
                'name' => 'Golden Participant',
                'slug' => 'golden-participant',
                'category' => 'Participation',
                'level' => 4,
                'icon_path' => 'images/badges/golden_participant.png',
                'description' => 'Attended 15 events.',
            ],
            [
                'name' => 'Diamond Member',
                'slug' => 'diamond-member',
                'category' => 'Participation',
                'level' => 5,
                'icon_path' => 'images/badges/diamond_member.png',
                'description' => 'Attended 30 or more events.',
            ],

            // 💼 Performance Path (Gold)
            [
                'name' => 'Reliable',
                'slug' => 'reliable',
                'category' => 'Performance',
                'level' => 1,
                'icon_path' => 'images/badges/reliable.png',
                'description' => 'Average rating ≥ 4 in 3 events.',
            ],
            [
                'name' => 'Outstanding',
                'slug' => 'outstanding',
                'category' => 'Performance',
                'level' => 2,
                'icon_path' => 'images/badges/outstanding.png',
                'description' => 'Average rating ≥ 4.5 in 7 events.',
            ],
            [
                'name' => 'Role Model',
                'slug' => 'role-model',
                'category' => 'Performance',
                'level' => 3,
                'icon_path' => 'images/badges/role_model.png',
                'description' => 'Average rating 5/5 in 15 events.',
            ],
            [
                'name' => 'Quality Ambassador',
                'slug' => 'quality-ambassador',
                'category' => 'Performance',
                'level' => 4,
                'icon_path' => 'images/badges/quality_ambassador.png',
                'description' => 'Perfect rating in last 5 consecutive events.',
            ],

            // 💡 Creativity Path (Purple)
            [
                'name' => 'Initiator',
                'slug' => 'initiator',
                'category' => 'Creativity',
                'level' => 1,
                'icon_path' => 'images/badges/initiator.png',
                'description' => 'Proposed a new idea or helped improve an event.',
            ],
            [
                'name' => 'Supporter',
                'slug' => 'supporter',
                'category' => 'Creativity',
                'level' => 2,
                'icon_path' => 'images/badges/supporter.png',
                'description' => 'Helped onboard or train new volunteers.',
            ],
            [
                'name' => 'Creative',
                'slug' => 'creative',
                'category' => 'Creativity',
                'level' => 3,
                'icon_path' => 'images/badges/creative.png',
                'description' => 'Contributed a design, idea, or creative content.',
            ],
            [
                'name' => 'Change Maker',
                'slug' => 'change-maker',
                'category' => 'Creativity',
                'level' => 4,
                'icon_path' => 'images/badges/change_maker.png',
                'description' => 'Played a role in a project/event with visible community impact.',
            ],
            [
                'name' => 'Team Inspirer',
                'slug' => 'team-inspirer',
                'category' => 'Creativity',
                'level' => 5,
                'icon_path' => 'images/badges/team_inspirer.png',
                'description' => 'Inspired and motivated others in multiple events.',
            ],
        ]);
    }
}
