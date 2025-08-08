<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checkin_method' => ['required', 'string', Rule::in(['QR', 'Manual'])],

            'event_id' => [
                'nullable',
                'required_if:checkin_method,Manual',
                'exists:events,id',
            ],

            'qr_token' => [
                'nullable',
                'required_if:checkin_method,QR',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'checkin_method.required' => 'Check-in method is required.',
            'checkin_method.in' => 'Check-in method must be QR or Manual.',

            'event_id.required_if' => 'Event ID is required for manual check-in.',
            'event_id.exists' => 'The selected event does not exist.',

            'qr_token.required_if' => 'QR token is required for QR check-in.',
            'qr_token.string' => 'QR token must be a string.',
            'qr_token.max' => 'QR token may not be greater than :max characters.',
        ];
    }
}
