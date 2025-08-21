<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Volunteer;
use App\Models\Attendance;
use App\Models\Badge;
use App\Models\VolunteerBadge;
use App\Models\Evaluation;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\DB;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('leaderboard')->truncate();

        $volunteers = Volunteer::all();

        foreach ($volunteers as $volunteer) {
            $score = 0;

            // Attendance points
            $attendanceCount = Attendance::where('volunteer_id', $volunteer->id)->count();
            $score += $attendanceCount * 5;

            // Badge points
            $badges = VolunteerBadge::where('volunteer_id', $volunteer->id)->get();
            foreach ($badges as $vb) {
                $badge = Badge::find($vb->badge_id);
                if (!$badge) continue;

                $score += match ($badge->category) {
                    'Participation' => 5,
                    'Performance'   => 15,
                    'Creativity'    => 20,
                    default         => 10,
                };
            }

            // Evaluation points
            $evaluations = Evaluation::where('volunteer_id', $volunteer->id)->get();
            foreach ($evaluations as $evaluation) {
                if ($evaluation->average_rating >= 4.5) {
                    $score += 10;
                }

                // Optional: inspirational feedback
                if (!empty($evaluation->inspirational) && $evaluation->inspirational === true) {
                    $score += 5;
                }
            }

            // Insert into leaderboard
            Leaderboard::updateOrCreate(
                ['volunteer_id' => $volunteer->id],
                ['score' => $score, 'last_updated' => now()]
            );
        }
    }
}
