<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVolunteerApplicationRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:volunteer_applications,email',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'date_of_birth' => 'required|date',
            'study' => 'nullable|string|max:255',
            'career' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'interests' => 'required|array',
            'interests.*' => 'string|max:255',
            'availability' => 'required|array',
            'availability.*' => 'string|max:255',
            'preferred_time' => ['required', Rule::in([
                '1-2 hours per week',
                '3-5 hours per week',
                '6-10 hours per week',
                'more than 10 hours per week'
            ])],
            'volunteering_years' => 'nullable|integer|min:0',
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:255',
            'motivation' => 'nullable|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => ['required', Rule::in(['Parent', 'Spouse', 'Friend'])],
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
            'email.unique' => 'This Email had been Taken',
        ];
    }
}
