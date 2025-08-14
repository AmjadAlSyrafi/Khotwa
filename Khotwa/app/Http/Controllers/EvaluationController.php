<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\Evaluation;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Helpers\ApiResponse;
use App\Services\BadgeService;

class EvaluationController extends Controller
{

    public function __construct(private BadgeService $badgeService) {}

    /**
     * Supervisor creates an evaluation for a volunteer after an event.
     * Preconditions:
     * - Auth user is Supervisor
     * - Event is completed
     * - Volunteer attended (has Attendance record)
     * - No duplicate evaluation by the same supervisor for this (volunteer,event)
     */
    /**
     * Supervisor creates an evaluation for a volunteer after an event.
     */
    public function store(StoreEvaluationRequest $request)
    {
        $data = $request->validated();

        $event = Event::find($data['event_id']);
        if (!$event) {
            return ApiResponse::error('Event not found.', 404);
        }

        if ($event->status !== 'completed') {
            return ApiResponse::error('Event must be completed before evaluation.', 400);
        }

        $attended = Attendance::where('event_id', $event->id)
            ->where('volunteer_id', $data['volunteer_id'])
            ->exists();

        if (!$attended) {
            return ApiResponse::error('Volunteer did not attend this event.', 422);
        }

        $exists = Evaluation::where('volunteer_id', $data['volunteer_id'])
            ->where('event_id', $event->id)
            ->where('supervisor_id', Auth::id())
            ->exists();
        if ($exists) {
            return ApiResponse::error('Evaluation already exists for this volunteer and event.', 409);
        }

        // Calculate the average rating
        $avg = collect([
            $data['punctuality'],
            $data['work_quality'],
            $data['teamwork'],
            $data['initiative'],
            $data['discipline'],
        ])->avg();

        try {
            $evaluation = Evaluation::create([
                'volunteer_id'  => $data['volunteer_id'],
                'event_id'      => $event->id,
                'supervisor_id' => Auth::id(),

                'punctuality'   => $data['punctuality'],
                'work_quality'  => $data['work_quality'],
                'teamwork'      => $data['teamwork'],
                'initiative'    => $data['initiative'],
                'discipline'    => $data['discipline'],
                'average_rating'=> round($avg, 2),

                'notes'         => $data['notes'] ?? null,

                // Creative booleans
                'initiated' => $data['initiated'] ?? false,
                'mentored' => $data['mentored'] ?? false,
                'creative_contribution' => $data['creative_contribution'] ?? false,
                'impactful' => $data['impactful'] ?? false,
                'inspirational' => $data['inspirational'] ?? false,
            ]);

            // Sync creativity badges with thresholds
            $this->badgeService->syncAfterEvaluation($evaluation->volunteer_id);

            return ApiResponse::success($evaluation, 'Evaluation created successfully.', 201);
        } catch (QueryException $e) {
            return ApiResponse::error('Could not create evaluation.', 500);
        }
    }


    /**
     * Supervisor updates an evaluation they created.
     */
    public function update(UpdateEvaluationRequest $request, $id)
    {
        $evaluation = Evaluation::find($id);
        if (!$evaluation) {
            return ApiResponse::error('Evaluation not found.', 404);
        }

        if ($evaluation->supervisor_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized.', 403);
        }

        $data = $request->validated();
        $evaluation->fill($data);

        $dirtyCriteria = collect(['punctuality','work_quality','teamwork','initiative','discipline'])
            ->some(fn($key) => array_key_exists($key, $data));

        if ($dirtyCriteria) {
            $avg = collect([
                $evaluation->punctuality,
                $evaluation->work_quality,
                $evaluation->teamwork,
                $evaluation->initiative,
                $evaluation->discipline,
            ])->avg();
            $evaluation->average_rating = round($avg, 2);
        }

        $evaluation->save();

        return ApiResponse::success($evaluation, 'Evaluation updated successfully.');
    }

    /**
     * List evaluations for an event (Supervisor).
     */
    public function indexForEvent($eventId)
    {
        $evaluations = Evaluation::with(['volunteer'])
            ->where('event_id', $eventId)
            ->get();

        return ApiResponse::success($evaluations, 'Event evaluations fetched successfully.');
    }

    /**
     * Show one evaluation (Supervisor or Volunteer who owns it).
     */
    public function show($id)
    {
        $evaluation = Evaluation::with(['volunteer','event','supervisor'])->find($id);
        if (!$evaluation) {
            return ApiResponse::error('Evaluation not found.', 404);
        }

        $user = Auth::user();
        $isSupervisor = optional($user->role)->name === 'Supervisor' && $evaluation->supervisor_id === $user->id;
        $isOwnerVolunteer = $user->volunteer && $evaluation->volunteer_id === $user->volunteer->id;

        if (!$isSupervisor && !$isOwnerVolunteer && optional($user->role)->name !== 'Admin') {
            return ApiResponse::error('Unauthorized.', 403);
        }

        return ApiResponse::success($evaluation, 'Evaluation fetched successfully.');
    }

    /**
     * Delete an evaluation (only the supervisor who created it or Admin).
     */
    public function destroy($id)
    {
        $evaluation = Evaluation::find($id);
        if (!$evaluation) {
            return ApiResponse::error('Evaluation not found.', 404);
        }

        $user = Auth::user();
        $isSupervisor = optional($user->role)->name === 'Supervisor' && $evaluation->supervisor_id === $user->id;
        $isAdmin = optional($user->role)->name === 'Admin';

        if (!$isSupervisor && !$isAdmin) {
            return ApiResponse::error('Unauthorized.', 403);
        }

        $evaluation->delete();
        return ApiResponse::success(null, 'Evaluation deleted successfully.');
    }

    /**
     * Volunteer: list my evaluations.
     */
    public function myEvaluations()
    {
        $volunteer = optional(Auth::user())->volunteer;
        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access this resource.', 403);
        }

        $evaluations = Evaluation::with(['event','supervisor'])
            ->where('volunteer_id', $volunteer->id)
            ->orderBy('created_at','desc')
            ->get();

        return ApiResponse::success($evaluations, 'My evaluations fetched successfully.');
    }
}
