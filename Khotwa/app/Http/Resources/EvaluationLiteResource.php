<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Warning;

class EvaluationLiteResource extends JsonResource
{
    public function toArray($request)
    {

        $warning = Warning::where('volunteer_id', $this->volunteer_id)
            ->where('event_id', $this->event_id)->where('status','approved')
            ->first();

        return [
            'event_title'     => $this->event->title ?? null,
            'average_rating'  => $this->average_rating,
            'notes'           => $this->notes,

            'warning'         => $warning ? [
                'reason' => $warning->reason,
                'status' => $warning->status,
                'created_at' => $warning->created_at->toDateTimeString(),
            ] : null,

            'created_at'      => $this->created_at->toDateTimeString()
        ];
    }
}
