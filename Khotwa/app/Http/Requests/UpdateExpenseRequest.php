<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends BaseFormRequest
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
            'title'       => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'amount'      => 'sometimes|numeric|min:0',
            'date'        => 'sometimes|date',
            'project_id'  => 'sometimes|exists:projects,id',
            'event_id'    => 'sometimes|exists:events,id',
        ];
    }
}
