<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\LeaderboardResource;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboardService) {}

    /**
     * Admin / Public - View top leaderboard.
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 5);
        $leaders = $this->leaderboardService->top($limit);

        return ApiResponse::success(LeaderboardResource::collection($leaders));
    }

    /**
     * Volunteer - View my rank.
     */
    public function myRank()
    {
        $volunteer = optional(Auth::user())->volunteer;
        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access rank.', 403);
        }

        $rank = $this->leaderboardService->rank($volunteer->id);
        return ApiResponse::success([
            'volunteer_id' => $volunteer->id,
            'rank' => $rank,
        ]);
    }
}
