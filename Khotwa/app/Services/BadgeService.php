<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Attendance;
use App\Models\Evaluation;
use App\Models\VolunteerBadge;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    // Slugs used in badges table (make sure seeder used the same)
    public const PARTICIPATION = [
        'newcomer', 'regular', 'active', 'gold-participant', 'diamond-member'
    ];

    public const PERFORMANCE = [
        'reliable', 'outstanding', 'role-model', 'quality-ambassador'
    ];

    public const CREATIVITY = [
        'initiator', 'mentor', 'creative-contributor', 'change-maker', 'team-inspirer'
    ];

    /**
     * Thresholds for creativity badges: [slug => times required]
     */
    protected array $creativityThresholds = [
        'initiator'             => 2, // يحتاج يبادر مرتين
        'mentor'                => 3, // يحتاج يدرب 3 مرات
        'creative-contributor'  => 2, // يساهم بإبداع مرتين
        'change-maker'          => 2, // يحدث أثر واضح مرتين
        'team-inspirer'         => 4, // يلهم الفريق 4 مرات
    ];

    /** Award a badge by slug if not already awarded */
    public function awardIfEligible(int $volunteerId, string $badgeSlug): bool
    {
        $badge = Badge::where('slug', $badgeSlug)->first();
        if (!$badge) return false;

        $already = VolunteerBadge::where('volunteer_id', $volunteerId)
            ->where('badge_id', $badge->id)
            ->exists();

        if ($already) return false;

        VolunteerBadge::create([
            'volunteer_id' => $volunteerId,
            'badge_id'     => $badge->id,
            'awarded_at'   => Carbon::now(),
        ]);

        return true;
    }

    /** Sync participation badges based on attended events count */
    public function syncParticipationBadges(int $volunteerId): void
    {
        $attendedCount = Attendance::where('volunteer_id', $volunteerId)
            ->whereNotNull('checkin_time')
            ->count();

        if ($attendedCount >= 1)  $this->awardIfEligible($volunteerId, 'new-participant');
        if ($attendedCount >= 3)  $this->awardIfEligible($volunteerId, 'regular-participant');
        if ($attendedCount >= 7)  $this->awardIfEligible($volunteerId, 'active');
        if ($attendedCount >= 15) $this->awardIfEligible($volunteerId, 'gold-participant');
        if ($attendedCount >= 30) $this->awardIfEligible($volunteerId, 'diamond-member');
    }

    /** Sync performance badges based on evaluations (rating 1..5) */
    public function syncPerformanceBadges(int $volunteerId): void
    {
        $evaluations = Evaluation::where('volunteer_id', $volunteerId);

        $count = $evaluations->count();
        $avg   = $evaluations->avg('average_rating');

        if ($count >= 3 && $avg >= 4.0) {
            $this->awardIfEligible($volunteerId, 'reliable');
        }

        if ($count >= 7 && $avg >= 4.5) {
            $this->awardIfEligible($volunteerId, 'outstanding');
        }

        if ($count >= 15 && (float)$avg === 5.0) {
            $this->awardIfEligible($volunteerId, 'role-model');
        }

        $last5 = Evaluation::where('volunteer_id', $volunteerId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->pluck('average_rating');

        if ($last5->count() === 5 && $last5->every(fn($r) => (float)$r === 5.0)) {
            $this->awardIfEligible($volunteerId, 'quality-ambassador');
        }
    }

    /** Sync creativity badges based on evaluation flags and thresholds */
    public function syncCreativityBadges(int $volunteerId): void
    {
        foreach ($this->creativityThresholds as $slug => $threshold) {
            $field = $this->mapSlugToEvaluationField($slug);

            if (!$field) continue;

            $count = Evaluation::where('volunteer_id', $volunteerId)
                ->where($field, true)
                ->count();

            if ($count >= $threshold) {
                $this->awardIfEligible($volunteerId, $slug);
            }
        }
    }

    /**
     * Map badge slug to evaluation table boolean field
     */
    protected function mapSlugToEvaluationField(string $slug): ?string
    {
        return match ($slug) {
            'initiator'     => 'initiated',
            'mentor'        => 'mentored',
            'creative'      => 'creative_contribution',
            'change-maker'  => 'impactful',
            'team-inspirer' => 'inspirational',
            default => null,
        };
    }

    /** Run all syncs after a new evaluation is created */
    public function syncAfterEvaluation(int $volunteerId): void
    {
        DB::transaction(function () use ($volunteerId) {
            $this->syncPerformanceBadges($volunteerId);
            $this->syncCreativityBadges($volunteerId);
        });
    }

    /** Run participation sync after attendance check-in/out */
    public function syncAfterAttendance(int $volunteerId): void
    {
        DB::transaction(function () use ($volunteerId) {
            $this->syncParticipationBadges($volunteerId);
        });
    }
}
