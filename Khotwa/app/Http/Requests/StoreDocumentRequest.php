<?php

namespace App\Http\Requests;

class StoreDocumentRequest extends BaseFormRequest
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
            'type'         => 'required|in:event,donation,project,volunteer,general',
            'file'         => 'required|file|max:5120', // 5MB
            'volunteer_id' => 'nullable|exists:volunteers,id',
            'event_id'     => 'nullable|exists:events,id',
            'project_id'   => 'nullable|exists:projects,id',
        ];
    }
}
