<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskController extends Controller
{
    /////////////////////////////////////////////////
    // كلشي خاص بالمشرف.....

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

        $task = Task::create($data);

        return ApiResponse::success($task, 'Task created successfully', 201);
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return ApiResponse::error('Task not found', 404);
        }

        $this->authorizeSupervisor($task);

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

        $this->authorizeSupervisor($task);

        $task->delete();

        return ApiResponse::success(null, 'Task deleted successfully');
    }

    // عرض كل مهام المشرف
    public function supervisorTasks()
    {
        $tasks = Task::where('supervisor_id', Auth::id())->get();
        return ApiResponse::success($tasks, 'Supervisor tasks retrieved successfully');
    }

    // عرض مهمة معينة يعني خاصة للمشرف
    public function supervisorShow($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return ApiResponse::error('Task not found', 404);
        }

        $this->authorizeSupervisor($task);

        return ApiResponse::success($task, 'Task details retrieved successfully');
    }

    /////////////////////////////////////////////////
    // كلشي للمتطوع
    public function volunteerTasks()
    {
        $tasks = Task::where('volunteer_id', Auth::id())->get();
        return ApiResponse::success($tasks, 'Volunteer tasks retrieved successfully');
    }

    public function acceptTask($id)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        $this->authorizeVolunteer($task);

        if ($task->status !== 'pending') {
            return ApiResponse::error('Task cannot be accepted', 400);
        }

        $task->update(['status' => 'accepted']);

        return ApiResponse::success($task, 'Task accepted successfully');
    }

    public function rejectTask($id)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        $this->authorizeVolunteer($task);

        if ($task->status !== 'pending') {
            return ApiResponse::error('Task cannot be rejected', 400);
        }

        $task->update(['status' => 'rejected']);

        return ApiResponse::success($task, 'Task rejected successfully');
    }

    public function withdrawTask($id)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        $this->authorizeVolunteer($task);

        if ($task->status !== 'accepted') {
            return ApiResponse::error('Task cannot be withdrawn', 400);
        }

        if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($task->start_time))) {
            return ApiResponse::error('Cannot withdraw after start time', 400);
        }

        $task->update(['status' => 'withdrawn']);

        return ApiResponse::success($task, 'Task withdrawn successfully');
    }

    //  تحديث الحالة للتاسك (active/completed)
    public function updateCompletionState(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) return ApiResponse::error('Task not found', 404);

        $this->authorizeVolunteer($task);

        $data = $request->validate([
            'completion_state' => 'required|in:active,completed'
        ]);

        $task->update(['completion_state' => $data['completion_state']]);

        return ApiResponse::success($task, 'Task completion state updated successfully');
    }

/* ---------------- Private Helpers ---------------- */

    private function authorizeSupervisor(Task $task)
    {
        if ($task->supervisor_id !== Auth::id()) {
            abort(ApiResponse::error('Unauthorized', 403));
        }
    }

    private function authorizeVolunteer(Task $task)
    {
        if ($task->volunteer_id !== Auth::id()) {
            abort(ApiResponse::error('Unauthorized', 403));
        }
    }
}
