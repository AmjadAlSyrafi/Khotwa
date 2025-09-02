<?php

namespace App\Http\Controllers\API;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Jobs\SendPushNotification;
use App\Notifications\GenericDatabaseNotification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendFcmNotification;
use Illuminate\Support\Collection;


class NotificationController extends Controller
{
    /**
     * إرسال إشعار DB + Push لمستخدم/مجموعة.
     */
    private function sendTo(User|Collection $recipients, string $title, string $body, array $data = []): void
    {
        $recipients = $recipients instanceof Collection ? $recipients : collect([$recipients]);

        foreach ($recipients as $user) {
            if (!$user) continue;

            $user->notify(new GenericDatabaseNotification([
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
            ]));

            SendPushNotification::dispatch(
                $user->id,
                ['title' => $title, 'body' => $body],
                $data
            );
        }
    }

    /* ----------------- المتطوع ----------------- */

    public function notifyVolunteerTaskAssigned(User $volunteer, $task): void
    {
        $this->sendTo(
            $volunteer,
            'New Task',
            "You have been added to the task: {$task->title}",
            ['type' => 'task_assigned', 'task_id' => $task->id]
        );
    }

    public function notifyVolunteerTaskStatusChanged(User $volunteer, $task, string $old, string $new): void
    {
        $this->sendTo(
            $volunteer,
            'Task Status Updated',
            "Task '{$task->title}' status changed from {$old} to {$new}",
            ['type' => 'task_status_changed', 'task_id' => $task->id, 'old' => $old, 'new' => $new]
        );
    }

    public function notifyVolunteerEventAccepted(User $volunteer, $event): void
    {
        $this->sendTo(
            $volunteer,
            'Registration Accepted',
            "You have been accepted to the event: {$event->title}",
            ['type' => 'event_registration_accepted', 'event_id' => $event->id]
        );
    }

    public function notifyVolunteerEventEnded(User $volunteer, $event): void
    {
        $this->sendTo(
            $volunteer,
            'Event Ended',
            "Event '{$event->title}' has ended.",
            ['type' => 'event_ended', 'event_id' => $event->id]
        );
    }

    public function notifyVolunteerProjectEnded(User $volunteer, $project): void
    {
        $this->sendTo(
            $volunteer,
            'Project Ended',
            "Project '{$project->title}' has ended.",
            ['type' => 'project_ended', 'project_id' => $project->id]
        );
    }

    public function notifyAllVolunteersNewEvent($event): void
    {
        $volunteers = User::whereHas('volunteer')->get();
        $this->sendTo(
            $volunteers,
            'New Event',
            "A new event '{$event->title}' has been added.",
            ['type' => 'event_new', 'event_id' => $event->id]
        );
    }

    public function notifyAllVolunteersNewProject($project): void
    {
        $volunteers = User::whereHas('volunteer')->get();
        $this->sendTo(
            $volunteers,
            'New Project',
            "A new project '{$project->title}' has been added.",
            ['type' => 'project_new', 'project_id' => $project->id]
        );
    }

    public function notifyVolunteerWarningIssued(User $volunteer, $warning): void
    {
        $this->sendTo(
            $volunteer,
            'Warning Issued',
            $warning->reason ?? 'You have received a warning.',
            ['type' => 'warning', 'warning_id' => $warning->id ?? null]
        );
    }

    public function notifyVolunteerRatedInEvent(User $volunteer, $event, $rating): void
    {
        $score = $rating->score ?? ($rating->value ?? 'N/A');
        $this->sendTo(
            $volunteer,
            'Event Rating',
            "You were rated {$score} in event '{$event->title}'.",
            ['type' => 'event_rating', 'event_id' => $event->id, 'rating_id' => $rating->id ?? null]
        );
    }

    /** إشعار منتهي الفعالية لجميع المسجلين المقبولين */
    public function notifyRegisteredVolunteersEventEnded($event): void
    {
        $users = User::whereHas('volunteer.registrations', function ($q) use ($event) {
            $q->where('event_id', $event->id)->whereIn('status', ['accepted', 'attended']);
        })->get();

        $this->sendTo(
            $users,
            'Event Ended',
            "Event '{$event->title}' has ended.",
            ['type' => 'event_ended', 'event_id' => $event->id]
        );
    }

    /** إشعار منتهي المشروع لكل المتطوعين المنخرطين فيه (تبع تصميمك لعلاقات المشروع) */
    public function notifyProjectVolunteersEnded($project): void
    {
        $users = User::whereHas('volunteer.projectMemberships', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })->get();

        $this->sendTo(
            $users,
            'Project Ended',
            "Project '{$project->title}' has ended.",
            ['type' => 'project_ended', 'project_id' => $project->id]
        );
    }

    /* ----------------- المشرف ----------------- */

    public function notifyAdminsVolunteerWithdrawn($task, User $volunteerUser): void
    {
        $supervisors = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();
        $this->sendTo(
            $supervisors,
            'Volunteer Withdrawal',
            "Volunteer {$volunteerUser->name} has withdrawn from task '{$task->title}'.",
            ['type' => 'volunteer_withdrawn', 'task_id' => $task->id, 'volunteer_id' => $volunteerUser->id]
        );
    }

    public function notifySupervisorsVolunteerCompletedTask($task, User $volunteerUser): void
    {
        // مشرف المهمة نفسها أولاً (إن وجد)
        $supervisor = User::find($task->supervisor_id);
        $targets = collect([$supervisor])->filter();

        // (اختياري) لو بدك كل المشرفين:
        // $targets = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();

        $this->sendTo(
            $targets,
            'Task Completed',
            "Volunteer {$volunteerUser->name} completed task '{$task->title}'.",
            ['type' => 'task_completed', 'task_id' => $task->id, 'volunteer_id' => $volunteerUser->id]
        );
    }

    public function notifySupervisorsNewEvent($event): void
    {
        $supervisors = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();
        $this->sendTo(
            $supervisors,
            'New Event Created',
            "Event '{$event->title}' has been created.",
            ['type' => 'event_new_admin', 'event_id' => $event->id]
        );
    }

    public function notifySupervisorsNewProject($project): void
    {
        $supervisors = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();
        $this->sendTo(
            $supervisors,
            'New Project Created',
            "Project '{$project->title}' has been created.",
            ['type' => 'project_new_admin', 'project_id' => $project->id]
        );
    }

    public function notifySupervisorsEventEnded($event): void
    {
        $supervisors = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();
        $this->sendTo(
            $supervisors,
            'Event Ended',
            "Event '{$event->title}' has ended.",
            ['type' => 'event_ended_admin', 'event_id' => $event->id]
        );
    }

    public function notifySupervisorsProjectEnded($project): void
    {
        $supervisors = User::whereHas('role', fn ($q) => $q->where('name', 'Supervisor'))->get();
        $this->sendTo(
            $supervisors,
            'Project Ended',
            "Project '{$project->title}' has ended.",
            ['type' => 'project_ended_admin', 'project_id' => $project->id]
        );
    }

    /* ----------------- الادمن ----------------- */

    /**
    * Admin sends notification: volunteer promoted to supervisor
    */
    public function notifyVolunteerPromotedToSupervisor($volunteer)
    {
    $this->sendTo(
        $volunteer->user,
        "You have been promoted",
        "You have been promoted to become a system supervisor",
        [
            "type" => "promotion",
            "volunteer_id" => $volunteer->id,
        ]
    );
    }

    /**
    * Admin sends notification: new event added
    */
    public function notifyAdminsEventCreated($event)
    {
    $admins = User::where('role', 'Admin')->get();
    $this->sendTo(
        $admins,
        " event added",
        "done event added : {$event->name}",
        [
            "type" => "event_created",
            "event_id" => $event->id,
        ]
    );
    }

    /**
    * Admin sends notification: volunteer warning accepted
    */
    public function notifyVolunteerWarningAccepted($warning)
    {
    $this->sendTo(
        $warning->volunteer->user,
        "warning accepte",
        "The warning directed to you has been accepted",
        [
            "type" => "warning_accepted",
            "warning_id" => $warning->id,
        ]
    );
    }

    /**
    * Admin receives notification: event ended
    */
    public function notifyAdminEventEnded($event)
    {
    $admins = User::where('role', 'Admin')->get();
    $this->sendTo(
        $admins,
        " event end",
        " event ended: {$event->name}",
        [
            "type" => "event_ended",
            "event_id" => $event->id,
        ]
    );
    }

    /**
    * Admin receives notification: event capacity reached
    */
    public function notifyAdminEventCapacityReached($event)
    {
    $admins = User::where('role', 'Admin')->get();
    $this->sendTo(
        $admins,
        "capacity reached",
        "The number of volunteers has reached the required limit for the event: {$event->name}",
        [
            "type" => "capacity_reached",
            "event_id" => $event->id,
        ]
    );
    }


    /**
    * Admin receives notification: warning processed (accepted/rejected)
    */
    public function notifyAdminWarningProcessed($warning)
    {
    $admins = User::where('role', 'Admin')->get();
    $status = $warning->status == 'accepted' ? ' warring accepte ' : ' warring reject';
    $this->sendTo(
        $admins,
        " warring prcessing ",
        "{$status} for volunteer {$warning->volunteer->name}",
        [
            "type" => "warning_processed",
            "warning_id" => $warning->id,
            "status" => $warning->status,
        ]
    );
    }

    public function test() {
    return response()->json(['message' => 'Notifications test works']);
}

}
