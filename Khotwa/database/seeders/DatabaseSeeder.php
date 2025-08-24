<?php

namespace Database\Seeders;

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
        $this->call([
            RoleSeeder::class,
            BadgeSeeder::class,
            SkillSeeder::class,
            UserSeeder::class,
            VolunteerApplicationSeeder::class,
            VolunteerSeeder::class,
            ProjectSeeder::class,
            EventSeeder::class,
            DonationSeeder::class,
            ExpenseSeeder::class,
            WarningSeeder::class,
            LeaderboardSeeder::class,
            AuditLogSeeder::class,
        ]);

    }
}
