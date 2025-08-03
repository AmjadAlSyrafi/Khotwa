<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Volunteer;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    // البحث عن المتطوعين (للمشرف فقط)
    public function searchVolunteers(Request $request)
    {
        if (Auth::user()->role_id !== 'supervisor') {
            return ApiResponse::error('Unauthorized to search volunteers', 403);
        }

        $query = $request->input('query');
        $results = Volunteer::search($query)->get();
        return ApiResponse::success($results, 'Volunteers search results');
    }


    // البحث عن الفعاليات(للكل )
    public function searchEvents(Request $request)
    {
        $query = $request->input('query');
        $results = Event::search($query)->get();
        return ApiResponse::success($results, 'Events search results');
    }

    // البحث عن المشاريع ( للكل)
    public function searchProjects(Request $request)
    {
        $query = $request->input('query');
        $results = Project::search($query)->get();
        return ApiResponse::success($results, 'Projects search results');
    }
}
