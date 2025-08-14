<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\Event;
use App\Http\Requests\StoreEventRegistrationRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Services\NotificationService;

class EventRegistrationController extends Controller
{
    /**
     * Register a volunteer for an event.
     */
    public function register(StoreEventRegistrationRequest $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can register for events.', 403);
        }

        $event = Event::find($request->event_id);

        if ($event->status !== 'open') {
            return ApiResponse::error('Event is not open for registration.', 400);
        }

        if ($event->current_volunteers >= $event->required_volunteers) {
            return ApiResponse::error('Event has reached the maximum number of volunteers.', 400);
        }

        $existing = EventRegistration::where('volunteer_id', $volunteer->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            return ApiResponse::error('You have already registered for this event.', 409);
        }

        $registration = EventRegistration::create([
            'volunteer_id' => $volunteer->id,
            'event_id' => $event->id,
            'status' => 'pending',
            'joined_at' => now(),
        ]);

        // Increment current volunteers count
        $event->increment('current_volunteers');

        return ApiResponse::success($registration, 'Registration successful.');

        // عند قبول التسجيل في الفعالية (التنفيذ من قبل المشرف)
       NotificationService::notifyVolunteerEventAccepted($volunteer, $event);

       return ApiResponse::success($registration, 'Registration successful.');
    }

    /**
     * Withdraw a volunteer from an event.
     */
    public function withdraw(StoreEventRegistrationRequest $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $volunteer = Auth::user()->volunteer;

        if (!$volunteer) {
            return ApiResponse::error('Only volunteers can withdraw from events.', 403);
        }

        $registration = EventRegistration::where('volunteer_id', $volunteer->id)
            ->where('event_id', $request->event_id)
            ->first();

        if (!$registration) {
            return ApiResponse::error('You are not registered for this event.', 404);
        }

        if ($registration->status === 'withdrawn') {
            return ApiResponse::error('You have already withdrawn from this event.', 409);
        }

        $registration->status = 'withdrawn';
        $registration->save();

        // Decrement current volunteers count
        $event = Event::find($request->event_id);
        if ($event && $event->current_volunteers > 0) {
            $event->decrement('current_volunteers');
        }

        return ApiResponse::success($registration, 'You have successfully withdrawn from the event.');
    }
}
