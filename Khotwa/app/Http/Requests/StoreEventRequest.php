<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
            'date'            => ['required', 'date'],
            'time'            => ['required'],
            'duration_hours'  => ['required', 'integer', 'min:1'],
            'location'        => ['required', 'string'],
            'lat'             => ['nullable', 'numeric'],
            'lng'             => ['nullable', 'numeric'],
            'status'          => ['required', 'in:open,closed,completed'],
            'project_id'      => ['required', 'exists:projects,id'],
            'required_volunteers' => ['required', 'integer', 'min:1'],
            'registered_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
