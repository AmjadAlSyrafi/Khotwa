<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount'      => 'required|numeric|min:1',
            'project_id'  => 'nullable|exists:projects,id',
            'event_id'    => 'nullable|exists:events,id',
            'donor_name'  => 'nullable|string|max:100',
            'donor_email' => 'nullable|email|max:100',
            'type'        => 'required|string|in:cash,in-kind',
        ];
    }
}
