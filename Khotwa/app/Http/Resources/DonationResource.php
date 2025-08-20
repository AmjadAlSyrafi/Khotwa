<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'amount'         => $this->amount,
            'description'    => $this->description,
            'donor_name'     => $this->donor_name,
            'donor_email'    => $this->donor_email,
            'method'         => $this->method,
            'payment_status' => $this->payment_status,
            'transaction_id' => $this->transaction_id,
            'project'        => optional($this->project)->name,
            'event'          => optional($this->event)->title,
            'donated_at'     => $this->donated_at,
        ];
    }
}
