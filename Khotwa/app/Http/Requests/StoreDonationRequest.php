<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends BaseFormRequest
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
            'type'        => 'required|in:cash,in-kind',
            'amount'      => 'nullable|numeric|min:1',
            'description' => 'nullable|string',
            'donor_name'  => 'nullable|string|max:100',
            'donor_email' => 'nullable|email',
            'method'      => 'required|in:manual,stripe',
            'project_id'  => 'nullable|exists:projects,id',
            'event_id'    => 'nullable|exists:events,id',
        ];
    }
}
