<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Helpers\ApiResponse;
use App\Notifications\ProjectCreatedNotification;
use App\Notifications\ProjectClosedNotification;
use App\Models\User;

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

    // إرسال إشعار للمشرفين والمتطوعين
    $supervisors = User::where('role', 'Supervisor')->get();
    $volunteers = User::where('role', 'Volunteer')->get();

    foreach ($supervisors as $supervisor) {
        $supervisor->notify(new ProjectCreatedNotification($project));
    }

    foreach ($volunteers as $volunteer) {
        $volunteer->notify(new ProjectCreatedNotification($project));
    }

        return ApiResponse::success($project, 'Project created successfully', 201);
    }

    public function update(StoreProjectRequest $request, $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return ApiResponse::error('Project not found', 404);
        }

        $oldStatus = $project->status;
        $project->update($request->all());

        // إذا تغيرت الحالة إلى finished إشعار انتهاء
        if ($oldStatus !== 'closed' && $project->status === 'closed') {
        $supervisors = User::where('role', 'Supervisor')->get();
        $volunteers = User::where('role', 'Volunteer')->get();

        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new ProjectClosedNotification($project));
        }
        foreach ($volunteers as $volunteer) {
            $volunteer->notify(new ProjectClosedNotification($project));
        }
    }

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

