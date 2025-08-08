<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFormRequest;

class UpdateAttendanceRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return True;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'volunteer_ids' => ['required', 'array', 'min:1'],
            'volunteer_ids.*' => ['integer', 'exists:volunteers,id'],
            'action' => ['required', Rule::in(['checkin', 'checkout'])],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'Event ID is required.',
            'event_id.exists' => 'Selected event does not exist.',
            'volunteer_ids.required' => 'You must select at least one volunteer.',
            'volunteer_ids.array' => 'Volunteer IDs must be in an array format.',
            'volunteer_ids.*.exists' => 'One or more selected volunteers do not exist.',
            'action.required' => 'Action type is required.',
            'action.in' => 'Action must be either checkin or checkout.',
        ];
    }
}
