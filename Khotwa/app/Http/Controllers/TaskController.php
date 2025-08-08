<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    // ---------------- Supervisor Actions ---------------- //

    public function createTask(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'volunteer_id' => 'required|exists:volunteers,id',
            'start_time'   => 'required|date'
        ]);

        $data['supervisor_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['completion_state'] = 'active';

        $task = Task::create($data);

        return ApiResponse::success($task, 'Task created successfully', 201);
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return ApiResponse::error('Task not found', 404);
        }

        if ($task->supervisor_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_time'  => 'sometimes|date'
        ]);

        $task->update($data);

        return ApiResponse::success($task, 'Task updated successfully');
    }

    public function deleteTask($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return ApiResponse::error('Task not found', 404);
        }

        if ($task->supervisor_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $task->delete();

        return ApiResponse::success(null, 'Task deleted successfully');
    }

    public function supervisorTasks(Request $request)
    {
        $query = Task::where('supervisor_id', Auth::id());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

        return ApiResponse::success($tasks, 'Supervisor tasks retrieved successfully');
    }

    public function supervisorShow($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return ApiResponse::error('Task not found', 404);
        }

        if ($task->supervisor_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        return ApiResponse::success($task, 'Task details retrieved successfully');
    }

    // ---------------- Volunteer Actions ---------------- //

    public function volunteerTasks(Request $request)
    {
        $volunteer = Auth::user()->volunteer;
        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access this endpoint.', 403);
        }

        $query = Task::where('volunteer_id', $volunteer->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

        return ApiResponse::success($tasks, 'Volunteer tasks retrieved successfully');
    }

    public function updateTaskStatus($id, Request $request)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        if (!$this->isVolunteerAuthorized($task)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $data = $request->validate([
            'action' => 'required|in:accept,reject,withdraw'
        ]);

        if ($data['action'] === 'withdraw') {
            if ($task->status !== 'accepted') {
                return ApiResponse::error('Task cannot be withdrawn', 400);
            }

            if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($task->start_time))) {
                return ApiResponse::error('Cannot withdraw after start time', 400);
            }

            $task->update(['status' => 'withdrawn']);
        }
        elseif (in_array($data['action'], ['accept', 'reject'])) {
            if ($task->status !== 'pending') {
                return ApiResponse::error('Task cannot be updated for this action', 400);
            }

            $task->update(['status' => $data['action'] === 'accept' ? 'accepted' : 'rejected']);
        }

        return ApiResponse::success($task, "Task {$data['action']} successfully");
    }

    public function updateCompletionState(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        if (!$this->isVolunteerAuthorized($task)) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $data = $request->validate([
            'completion_state' => 'required|in:active,completed'
        ]);

        $task->update(['completion_state' => $data['completion_state']]);

        return ApiResponse::success($task, 'Task completion state updated successfully');
    }

    /* ---------------- Private Helpers ---------------- */

    private function isVolunteerAuthorized(Task $task): bool
    {
        $volunteer = Auth::user()->volunteer;
        return $volunteer && $task->volunteer_id === $volunteer->id;
    }
}
