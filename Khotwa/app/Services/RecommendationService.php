<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Volunteer;

class RecommendationService
{
    public function getRecommendedEvents(Volunteer $volunteer, int $limit = 10)
    {
        $interests       = $volunteer->interests ?? [];
        $availability    = $volunteer->availability ?? [];
        $preferredTime   = $volunteer->preferred_time ?? null;

        $attendedEventIds = $volunteer->attendances()->pluck('event_id')->toArray();

        $goodEventCategories = $volunteer->evaluations()
            ->where('average_rating', '>=', 4)
            ->with('event')
            ->get()
            ->pluck('event.category')
            ->filter()
            ->unique()
            ->toArray();

        // ---------------------------------
        // Step 1: Advanced Scoring Logic
        // ---------------------------------
        $events = Event::where('status', 'upcoming')
            ->withCount('registrations')
            ->get()
            ->map(function ($event) use (
                $interests, $availability, $preferredTime,
                $attendedEventIds, $goodEventCategories
            ) {
                $score = 0;

                // 1. Interests
                if (in_array($event->category, $interests)) {
                    $score += 3;
                }

                // 2. Availability (Day & Time)
                if ($event->date) {
                    $eventDay = strtolower($event->date->format('l')); // monday, tuesday...
                    if (in_array($eventDay, $availability)) {
                        $score += 2;
                    }
                }
                if ($preferredTime && $event->time_slot === $preferredTime) {
                    $score += 1;
                }

                // 3. Penalize if already attended
                if (in_array($event->id, $attendedEventIds)) {
                    $score -= 2;
                }

                // 4. Boost for categories with good past evaluations
                if (in_array($event->category, $goodEventCategories)) {
                    $score += 2;
                }

                $event->recommendation_score = $score;
                return $event;
            })
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();

        // ---------------------------------
        // Step 2: Fallback if not enough events
        // ---------------------------------
        if ($events->count() < $limit) {
            $needed = $limit - $events->count();

            $fallback = Event::where('status', 'open')
                ->whereNotIn('id', $events->pluck('id'))
                ->orderBy('date', 'asc')
                ->take($needed)
                ->get()
                ->map(function ($event) {
                    $event->recommendation_score = 0; // default for fallback
                    return $event;
                });

            $events = $events->merge($fallback)->take($limit);
        }

        return $events;
    }
}
