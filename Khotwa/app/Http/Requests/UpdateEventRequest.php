<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'           => ['sometimes', 'string', 'max:100'],
            'description'     => ['sometimes', 'nullable', 'string'],
            'date'            => ['sometimes', 'date'],
            'time'            => ['sometimes'],
            'duration_hours'  => ['sometimes', 'integer', 'min:1'],
            'location'        => ['sometimes', 'string'],
            'lat'             => ['sometimes', 'nullable', 'numeric'],
            'lng'             => ['sometimes', 'nullable', 'numeric'],
            'status'          => ['sometimes', 'in:open,closed,completed'],
            'project_id'      => ['sometimes', 'exists:projects,id'],
            'required_volunteers' => ['sometimes', 'integer', 'min:1'],
            'registered_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}

