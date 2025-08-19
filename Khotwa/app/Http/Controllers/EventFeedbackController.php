<?php

namespace App\Http\Controllers;

use App\Models\EventFeedback;
use App\Http\Requests\StoreEventFeedbackRequest;
use App\Http\Resources\EventFeedbackResource;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

class EventFeedbackController extends Controller
{
    /**
     * Volunteer submits feedback after attending an event.
     */
    public function store(StoreEventFeedbackRequest $request)
    {
        $data = $request->validated();
        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can submit feedback.', 403);
        }

        // Ensure the volunteer has attended
        $hasAttended = $volunteer->attendances()
            ->where('event_id', $data['event_id'])
            ->exists();

        if (!$hasAttended) {
            return ApiResponse::error('You did not attend this event.', 403);
        }

        // Ensure only one feedback per volunteer per event
        if (EventFeedback::where('event_id', $data['event_id'])
            ->where('volunteer_id', $volunteer->id)
            ->exists()) {
            return ApiResponse::error('Feedback already submitted.', 409);
        }

        $feedback = EventFeedback::create([
            'event_id'     => $data['event_id'],
            'volunteer_id' => $volunteer->id,
            'rating'       => $data['rating'],
            'comment'      => $data['comment'] ?? null,
        ]);

        return ApiResponse::success(new EventFeedbackResource($feedback), 'Feedback submitted successfully!', 201);
    }

    /**
     * Admin/Supervisor: view all feedback for an event.
     */
    public function indexByEvent($eventId)
    {
        $feedback = EventFeedback::with(['volunteer','event'])
            ->where('event_id', $eventId)
            ->get();

        return ApiResponse::success(EventFeedbackResource::collection($feedback), 'Event feedback fetched successfully.');
    }

    /**
     * Volunteer: view my own feedback for a specific event.
     */
    public function myEventFeedback($eventId)
    {
        $volunteer = optional(Auth::user())->volunteer;
        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can access this.', 403);
        }

        $feedback = EventFeedback::with('event')
            ->where('event_id', $eventId)
            ->where('volunteer_id', $volunteer->id)
            ->first();

        if (!$feedback) {
            return ApiResponse::error('Feedback not found for this event.', 404);
        }

        return ApiResponse::success(new EventFeedbackResource($feedback), 'My feedback fetched successfully.');
    }

    /**
     * Admin/Supervisor: view feedback given by a specific volunteer.
     */
    public function feedbackForVolunteer($volunteerId)
    {
        $feedback = EventFeedback::with('event')
            ->where('volunteer_id', $volunteerId)
            ->orderBy('created_at','desc')
            ->get();

        return ApiResponse::success(EventFeedbackResource::collection($feedback), 'Volunteer feedback fetched successfully.');
    }
}
