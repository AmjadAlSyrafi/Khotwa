<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class DocumentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'file_name' => $this->file_name,

            'file_url' => URL::temporarySignedRoute('documents.download', now()
            ->addMinutes(30),['id' => $this->id]),

            'uploaded_by' => $this->uploader?->name,
            'volunteer'   => $this->volunteer?->full_name,
            'event'       => $this->event?->title,
            'project'     => $this->project?->title,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}

