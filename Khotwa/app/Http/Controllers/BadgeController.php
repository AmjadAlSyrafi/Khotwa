<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use App\Models\Badge;
use App\Helpers\ApiResponse;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BadgeResource;


class BadgeController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Volunteer: List my badges
     */
    public function myBadges(): JsonResponse
    {
        $user = Auth::user();
        $volunteer = $user->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access this endpoint.', 403);
        }

        $badges = $volunteer->badges()->get();

        return ApiResponse::success(BadgeResource::collection($badges), 'Badges fetched successfully.');
    }

    /**
     * Supervisor: List badges for a specific volunteer
     */
    public function volunteerBadges(int $volunteerId): JsonResponse
    {
        $volunteer = Volunteer::find($volunteerId);

        if (!$volunteer) {
            return ApiResponse::error('Volunteer not found.', 404);
        }

        $badges = $volunteer->badges()->with('category')->get();

        return ApiResponse::success(BadgeResource::collection($badges), 'Volunteer badges fetched successfully.');
    }

    /**
     * Admin: Force re-sync all badge families for a volunteer
     */
    public function syncAllForVolunteer(int $volunteerId): JsonResponse
    {
        $volunteer = Volunteer::find($volunteerId);

        if (!$volunteer) {
            return ApiResponse::error('Volunteer not found.', 404);
        }

        $this->badgeService->syncParticipationBadges($volunteer->id);
        $this->badgeService->syncPerformanceBadges($volunteer->id);
        $this->badgeService->syncCreativityBadges($volunteer->id);

        return ApiResponse::success([], 'Badges re-synced successfully.');
    }

    /**
     * Admin or Supervisor: List all badges in the system
     */
    public function allBadges(): JsonResponse
    {
        $badges = Badge::get();
        return ApiResponse::success(BadgeResource::collection($badges), 'All badges fetched successfully.');
    }
}
