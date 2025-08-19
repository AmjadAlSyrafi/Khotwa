<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return Auth::check() ;
    }

    public function rules(): array
    {
        return [
            'volunteer_id' => ['required', 'exists:volunteers,id'],
            'event_id'     => ['required', 'exists:events,id'],

            'initiated' => ['sometimes', 'boolean'],
            'mentored' => ['sometimes', 'boolean'],
            'creative_contribution' => ['sometimes', 'boolean'],
            'impactful' => ['sometimes', 'boolean'],
            'inspirational' => ['sometimes', 'boolean'],


            'punctuality'  => ['required', 'integer', 'between:1,5'],
            'work_quality' => ['required', 'integer', 'between:1,5'],
            'teamwork'     => ['required', 'integer', 'between:1,5'],
            'initiative'   => ['required', 'integer', 'between:1,5'],
            'discipline'   => ['required', 'integer', 'between:1,5'],

            'notes'        => ['nullable', 'string'],

            'warning_reason'        => ['sometimes', 'string'],


        ];
    }
}
