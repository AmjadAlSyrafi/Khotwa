<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventFeedbackResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'event'        => [
                'id'    => $this->event->id ?? null,
                'title' => $this->event->title ?? null,
            ],
            'volunteer'    => [
                'id'   => $this->volunteer->id ?? null,
                'name' => $this->volunteer->full_name ?? null,
            ],
            'rating'       => $this->rating,
            'comment'      => $this->comment,
            'created_at'   => $this->created_at->toDateTimeString(),
        ];
    }
}
