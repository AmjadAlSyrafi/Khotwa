<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Helpers\ApiResponse;


class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return ApiResponse::success($projects, 'Projects fetched successfully');
    }

    public function show($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return ApiResponse::error('Project not found', 404);
        }
        return ApiResponse::success($project, 'Project details fetched successfully');
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->all());

        return ApiResponse::success($project, 'Project created successfully', 201);
    }

    public function update(StoreProjectRequest $request, $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return ApiResponse::error('Project not found', 404);
        }

        $project->update($request->all());

        return ApiResponse::success($project, 'Project updated successfully');
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return ApiResponse::error('Project not found', 404);
        }

        $project->delete();

        return ApiResponse::success(null, 'Project deleted successfully');
    }

public function top()
{
    $projects = Project::with([
            'donations',
            'events.registrations'
        ])
        ->get()
        ->map(function ($project) {

            $paid = $project->donations->sum('amount');
            $participants = $project->events->sum(function ($event) {
                return $event->registrations->where('status', 'accepted')->count();
            });

            $activityScore = $paid + ($participants * 10) + ($project->events->count() * 5);
            // وزن تقريبي: التبرعات + المشاركين + عدد الفعاليات

            return [
                'id' => $project->id,
                'name' => $project->name,
                'organization' => $project->organization ?? 'Charity Org',
                'paid' => $paid,
                'participants' => $participants,
                'events_count' => $project->events->count(),
                'activity_score' => $activityScore,
            ];
        })
        ->sortByDesc('activity_score')
        ->values();

    return ApiResponse::success($projects, 'Top projects fetched successfully.');
}


}

