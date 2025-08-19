<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Warning;

class EvaluationResource extends JsonResource
{
    public function toArray($request)
    {
        // Get latest warning related to this volunteer + event (if exists)
        $warning = Warning::where('volunteer_id', $this->volunteer_id)
            ->where('event_id', $this->event_id)
            ->first();

        return [
            'id'              => $this->id,
            'volunteer'       => [
                'id'   => $this->volunteer->id ?? null,
                'name' => $this->volunteer->full_name ?? null,
            ],
            'event'           => [
                'id'    => $this->event->id ?? null,
                'title' => $this->event->title ?? null,
            ],
            'supervisor'      => [
                'id'   => $this->supervisor->id ?? null,
                'name' => $this->supervisor->username ?? null,
            ],
            'punctuality'     => $this->punctuality,
            'work_quality'    => $this->work_quality,
            'teamwork'        => $this->teamwork,
            'initiative'      => $this->initiative,
            'discipline'      => $this->discipline,
            'average_rating'  => $this->average_rating,
            'notes'           => $this->notes,
            'warning'         => $warning ? [
                'reason' => $warning->reason,
                'status' => $warning->status,
                'created_at' => $warning->created_at->toDateTimeString(),
            ] : null,
            'created_at'      => $this->created_at->toDateTimeString(),
            'updated_at'      => $this->updated_at->toDateTimeString(),
        ];
    }
}
