<?php

namespace App\Services;

use App\Models\Leaderboard;
use Carbon\Carbon;

class LeaderboardService
{
    /**
     * Increment volunteer score.
     */
    public function addScore(int $volunteerId, int $points): Leaderboard
    {
        $record = Leaderboard::firstOrCreate(
            ['volunteer_id' => $volunteerId],
            ['score' => 0, 'last_updated' => Carbon::now()]
        );

        $record->score += $points;
        $record->last_updated = Carbon::now();
        $record->save();

        return $record;
    }

    /**
     * Set volunteer score (overwrite).
     */
    public function setScore(int $volunteerId, int $points): Leaderboard
    {
        $record = Leaderboard::updateOrCreate(
            ['volunteer_id' => $volunteerId],
            ['score' => $points, 'last_updated' => Carbon::now()]
        );

        return $record;
    }

    /**
     * Get leaderboard sorted (top N).
     */
    public function top(int $limit = 5)
    {
        return Leaderboard::with('volunteer')
            ->orderByDesc('score')
            ->take($limit)
            ->get();
    }

    /**
     * Get rank of a volunteer.
     */
    public function rank(int $volunteerId): ?int
    {
        $ordered = Leaderboard::orderByDesc('score')->pluck('volunteer_id')->toArray();
        $pos = array_search($volunteerId, $ordered);

        return $pos !== false ? $pos + 1 : null;
    }
}
