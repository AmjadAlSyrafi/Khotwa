<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $targetDonation = $this->target_donation ?? 0;
        $donatedAmount = $this->donations->sum('amount');
        $remainingAmount = max($targetDonation - $donatedAmount, 0);

        $totalDonations = $this->donations->count();
        $totalEvents = $this->events->count();
        $totalVolunteers = $this->events->sum(function ($event) {
            return $event->registrations->where('status', 'pending')->count();
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,

            'target_donation' => $targetDonation,
            'donated_amount' => $donatedAmount,
            'remaining_amount' => $remainingAmount,

            'total_donations' => $totalDonations,
            'total_volunteers' => $totalVolunteers,
            'total_events' => $totalEvents,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
