<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\Event;
use App\Http\Requests\StoreEventRegistrationRequest;
use App\Http\Requests\UpdateEventRegistrationRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

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

        return ApiResponse::success($registration, 'You have successfully withdrawn from the event.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRegistrationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventRegistration $eventRegistration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventRegistration $eventRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRegistrationRequest $request, EventRegistration $eventRegistration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventRegistration $eventRegistration)
    {
        //
    }
}
