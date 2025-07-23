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
}

